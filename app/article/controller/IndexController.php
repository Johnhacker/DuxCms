<?php

/**
 * 文章栏目
 */

namespace app\article\controller;

class IndexController extends \app\index\controller\SiteController {

    public function index() {
        $classId = request('get', 'id');
        if (empty($classId)) {
            $this->error404();
        }
        $classInfo = target('article/ArticleClass')->getInfo($classId);
        if (empty($classInfo)) {
            $this->error404();
        }
        if ($classInfo['url']) {
            $this->redirect($classInfo['url']);
        }
        $this->setMeta($classInfo['name']);

        $crumb = target('article/ArticleClass')->loadCrumbList($classId);
        $this->setCrumb($crumb);

        $pageParams = [];
        $pageParams['id'] = $classId;

        $where = [];
        $where['A.status'] = 1;

        $classIds = target('article/ArticleClass')->getSubClassId($classId);
        $where['_sql'] = 'B.class_id in (' . $classIds . ')';

        $attrList = [];
        if($classInfo['filter_id']) {
            $filter = target('site/SiteFilter')->getFilter('article/ArticleClass', $classIds, ['id' => $classId]);
            if ($filter['status']) {
                $where['_sql'] = 'A.content_id in (' . $filter['ids'] . ')';
            }
            $attrList = $filter['attrList'];
        }

        $pageLimit = 15;
        $model = target('article/Article');
        $count = $model->countList($where);
        $pageData = $this->pageData($count, $pageLimit, $pageParams);
        $list = $model->loadList($where, $pageData['limit'], '', $classInfo['model_id']);

        $tpl = $this->siteConfig['tpl_class'];
        if ($classInfo['tpl_class']) {
            $tpl = $classInfo['tpl_class'];
        }
        $parentClassInfo = array_slice($crumb, -2, 1);
        if (empty($parentClassInfo)) {
            $parentClassInfo = $crumb[0];
        } else {
            $parentClassInfo = $parentClassInfo[0];
        }
        $topClassInfo = $crumb[0];

        $this->assign('attrList', $attrList);
        $this->assign('classInfo', $classInfo);
        $this->assign('parentClassInfo', $parentClassInfo);
        $this->assign('topClassInfo', $topClassInfo);
        $this->assign('page', $pageData['html']);
        $this->assign('pageList', $list);
        $this->assign('pageParams', $pageParams);
        $this->siteDisplay($tpl);
    }


}