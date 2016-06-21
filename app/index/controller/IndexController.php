<?php

/**
 * 系统首页
 */

namespace app\index\controller;

class IndexController extends \app\index\controller\SiteController {


    /**
     * 首页
     */
    public function index() {
        $this->setMeta('首页');
        $this->setCrumb([
            [
                'name' => '首页',
                'url' => ROOT_URL . '/'
            ]
        ]);
        $this->siteDisplay($this->siteConfig['tpl_index']);
    }

}