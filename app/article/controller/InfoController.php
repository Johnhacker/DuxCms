<?php

/**
 * 文章内容
 */

namespace app\article\controller;

class InfoController extends \app\index\controller\SiteController {

    public function index() {

        $id = request('get', 'id');
        if(empty($id)){
            $this->error404();
        }

        $info = target('article/Article')->getInfo($id);
        if(empty($info) || !$info['status']) {
            $this->error404();
        }

        if($info['url']) {
            $this->redirect($info['url']);
        }

        $classInfo = target('article/ArticleClass')->getInfo($info['class_id']);
        if(empty($classInfo)) {
            $this->error404();
        }

        $info = target('article/Article')->getInfo($id, $classInfo['model_id']);

        $this->setMeta($info['title'] . ' - ' . $classInfo['name']);

        $crumb = target('article/ArticleClass')->loadCrumbList($info['class_id']);
        $this->setCrumb($crumb);

        $filter = [];
        if($info['filter_id']) {
            $filter = target('site/SiteFilter')->getFilterContent($info['filter_id'], $info['content_id']);
        }

        $pageParams = [];
        $pageParams['id'] = $id;

        $tpl = $this->siteConfig['tpl_content'];
        if($classInfo['tpl_content']) {
            $tpl = $classInfo['tpl_content'];
        }
        if($info['tpl']) {
            $tpl = $info['tpl'];
        }

        $parentClassInfo =  array_slice($crumb, -2, 1);
        if(empty($parentClassInfo)){
            $parentClassInfo = $crumb[0];
        }else {
            $parentClassInfo = $parentClassInfo[0];
        }
        $topClassInfo = $crumb[0];

        target('site/SiteContent')->where(['content_id' => $info['content_id']])->setInc('view');

        $where = [];
        $where['A.status'] = 1;
        $where['_sql'] = 'A.create_time < ' . $info['create_time'];
        $where['B.class_id'] = $info['class_id'];
        $nextInfo = target('article/Article')->loadList($where, 1);

        $where = [];
        $where['A.status'] = 1;
        $where['_sql'] = 'A.create_time > ' . $info['create_time'];
        $where['B.class_id'] = $info['class_id'];
        $prevInfo = target('article/Article')->loadList($where, 1);

        $tagList = [];
        if($info['tags_id']) {
            $tagList = target('site/SiteTags')->contentTags('article', $info['tags_id']);
        }

        $this->assign('info', $info);
        $this->assign('prevInfo', $prevInfo[0]);
        $this->assign('nextInfo', $nextInfo[0]);
        $this->assign('filterInfo', $filter);
        $this->assign('classInfo', $classInfo);
        $this->assign('parentClassInfo', $parentClassInfo);
        $this->assign('topClassInfo', $topClassInfo);
        $this->assign('tagList' , $tagList);
        $this->assign('pageParams', $pageParams);
        $this->siteDisplay($tpl);
    }

}