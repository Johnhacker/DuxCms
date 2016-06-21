<?php

/**
 * Tag标签
 */
namespace app\article\controller;

class TagsController extends \app\index\controller\SiteController {

    public function index() {
        $name = request('get', 'name');
        $name = str_len(html_clear(urldecode($name)), 10, false);
        if(empty($name)) {
            $this->error404();
        }
        $tagInfo = target('site/SiteTags')->getWhereInfo([
            'name' => $name
        ]);
        if (empty($tagInfo)) {
            $this->error404();
        }

        $this->setMeta($tagInfo['name']);
        $this->setCrumb([
            [
                'name' => $name,
                'url' => url('index', ['name' => $name])
            ]
        ]);

        $pageParams = [];
        $pageParams['name'] = $name;

        $where = [];
        $where['_sql'] = 'FIND_IN_SET("' . $tagInfo['tag_id'] . '", A.tags_id)';

        $order = 'A.create_time desc, B.article_id desc';
        $tpl = $this->siteConfig['tpl_tags'] . '_article';

        $model = target('article/Article');
        $pageLimit = 15;
        $count = $model->countList($where);
        $pageData = $this->pageData($count, $pageLimit, $pageParams);
        $list = $model->loadList($where, $pageData['limit'], $order);

        $this->assign('tagInfo', $tagInfo);
        $this->assign('page', $pageData['html']);
        $this->assign('pageList', $list);
        $this->assign('pageParams', $pageParams);
        $this->siteDisplay($tpl);
    }

}