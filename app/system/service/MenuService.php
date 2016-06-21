<?php
namespace app\system\service;
/**
 * 系统菜单接口
 */
class MenuService {
    /**
     * 获取菜单结构
     */
    public function getSystemMenu() {
        return array(
            'index' => array(
                'name' => '首页',
                'icon' => 'home',
                'order' => 0,
                'menu' => array(
                    array(
                        'name' => '系统信息',
                        'icon' => 'build',
                        'order' => 0,
                        'menu' => array(
                            array(
                                'name' => '系统概况',
                                'icon' => 'dashboard',
                                'url' => url('system/Index/index'),
                                'order' => 0
                            ),
                            array(
                                'name' => '系统通知',
                                'icon' => 'bell',
                                'url' => url('system/Notice/index'),
                                'order' => 1
                            ),
                        )
                    ),
                ),
            ),
            'system' => array(
                'name' => '系统',
                'icon' => 'build',
                'order' => 99,
                'menu' => array(
                    array(
                        'name' => '系统设置',
                        'icon' => 'build',
                        'order' => 10,
                        'menu' => array(
                            array(
                                'name' => '基本设置',
                                'icon' => 'cog',
                                'url' => url('system/Config/index'),
                                'order' => 1
                            ),
                            array(
                                'name' => '配置管理',
                                'icon' => 'bars',
                                'url' => url('system/ConfigManage/index'),
                                'order' => 5
                            )
                        )
                    ),
                    array(
                        'name' => '用户管理',
                        'icon' => 'person',
                        'order' => 11,
                        'menu' => array(
                            array(
                                'name' => '用户管理',
                                'icon' => 'user',
                                'url' => url('system/User/index'),
                                'order' => 1
                            ),
                            array(
                                'name' => '角色管理',
                                'icon' => 'group',
                                'url' => url('system/Role/index'),
                                'order' => 5
                            )
                        )
                    ),
                    array(
                        'name' => '应用管理',
                        'icon' => 'build',
                        'order' => 12,
                        'menu' => array(
                            array(
                                'name' => '应用管理',
                                'icon' => 'bars',
                                'url' => url('system/Application/index'),
                                'order' => 1
                            ),
                        )
                    ),

                ),
            ),
        );
    }
}

