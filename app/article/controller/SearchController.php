<?php

/**
 * 文章搜索
 */

namespace app\article\controller;

class SearchController extends \app\index\controller\SiteController {

    public function index() {
        $name = request('', 'keyword');
        $name = str_len(html_clear(urldecode($name)), 10, false);
        if(empty($name)) {
            $this->error('请输入搜索关键词!');
        }

        $crumb = [
            [
                'name' => $name,
                'url' => url('article/Search/index', ['keyword' => $name])
            ],
        ];
        $this->setCrumb($crumb);
        $this->setMeta($name . ' - 站内搜索');
        
        $pageParams = [];
        $pageParams['keyword'] = $name;

        $where = [];
        $where['A.status'] = 1;
        $where['_sql'] = 'A.title like "%'.$name.'%"';
        $pageLimit = 15;
        $model = target('article/Article');
        $count = $model->countList($where);
        $pageData = $this->pageData($count, $pageLimit, $pageParams);
        $list = $model->loadList($where, $pageData['limit']);

        $tpl = $this->siteConfig['tpl_search'] . '_article';

        $this->assign('name' ,$name);
        $this->assign('page', $pageData['html']);
        $this->assign('pageList', $list);
        $this->assign('pageParams', $pageParams);
        $this->siteDisplay($tpl);
    }

}