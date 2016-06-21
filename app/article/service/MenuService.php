<?php
namespace app\article\service;
/**
 * 系统菜单接口
 */
class MenuService {
    /**
     * 获取菜单结构
     */
    public function getSystemMenu() {
        return array(
            'content' => array(
                'menu' => array(
                    array(
                        'name' => '内容管理',
                        'icon' => 'build',
                        'order' => 0,
                        'menu' => array(
                            array(
                                'name' => '文章管理',
                                'icon' => 'bars',
                                'url' => url('article/Content/index'),
                                'order' => 0
                            ),
                            array(
                                'name' => '文章分类',
                                'icon' => 'code-fork',
                                'url' => url('article/Class/index'),
                                'order' => 1
                            ),
                        )
                    ),
                ),
            ),
        );
    }
}

