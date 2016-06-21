<?php
$useConfig = array();
//读取其他配置
$dir = dirname(__FILE__);
$files = glob($dir . '/use/*.php');
foreach ($files as $file) {
    if(is_file($file)){
        $array = include $file;
        $useConfig = array_merge($useConfig, (array)$array);
    }
}

//全局配置
$config = [
    //路由配置
    'dux.routes' => [

    ],
    //模块转发配置
    'dux.module' => [
        'controller' => '',
        'api' => 'a',
        'admin' => 's',
        'mobile' => 'm'
    ],
    //错误处理
    'dux.debug' => $useConfig['dux.use']['debug'],
    'dux.log' => $useConfig['dux.use']['log'],

    //缓存配置
    'dux.cache' => $useConfig['dux.use_cache'],

    //模板设置
    'dux.tpl' => [
        'path' => ROOT_PATH,
        'cache' => $useConfig['dux.use']['tpl_cache']
    ],

    //数据库配置
    'dux.database' => [
        'default' => $useConfig['dux.use_data'],
    ],

    //COOKIE
    'dux.cookie' => [
        'default' => [
            'pre' => $useConfig['dux.use']['cookie_pre'],
        ]
    ],

    //SESSION
    'dux.session' => [
        'default' => [
            'pre' => $useConfig['dux.use']['cookie_pre'],
            'time' => 0,
            'cache' => $useConfig['dux.use']['session_cache']
        ]
    ]

];

return array_merge($config, $useConfig);