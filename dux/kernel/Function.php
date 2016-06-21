<?php

/**
 * 判断AJAX
 */
function isAjax() {
    if ((isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') && !isset($_GET['ajax'])) {
        return true;
    } else {
        return false;
    }
}

/**
 * 判断GET
 */
function isGet() {
    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        return true;
    } else {
        return false;
    }
}

/**
 * 判断POST
 */
function isPost() {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        return true;
    } else {
        return false;
    }
}

/**
 * 获取钩子类
 */
function hook($layer, $name, $method, $vars = array()) {
    if (empty($name)) return null;
    $apiPath = APP_PATH . '*/' . $layer . '/' . ucfirst($name) . ucwords($layer) . '.php';

    $apiList = glob($apiPath);

    if (empty($apiList)) {
        return;
    }
    $appPathStr = strlen(APP_PATH);
    $method = 'get' . $method . ucwords($name);

    $data = array();
    foreach ($apiList as $value) {
        $path = substr($value, $appPathStr, -4);
        $path = str_replace('\\', '/', $path);
        $appName = explode('/', $path);
        $appName = $appName[0];
        $config = load_config('app/' . $appName . '/config/config', false);
        if (!$config['app.system'] && (!$config['app.state'] || !$config['app.install'])) {
            continue;
        }
        $class = '\\app\\' . $appName . '\\' . $layer . '\\' . ucfirst($name) . ucwords($layer);
        if (!class_exists($class)) {
            return null;
        }
        $class = target($appName . '/' . $name, $layer);
        if (method_exists($class, $method)) {
            $data[$appName] = call_user_func_array(array($class, $method), $vars);
        }
    }
    return $data;
}

/**
 * 获取请求参数
 */
function request($method = '', $key = '', $default = '', $function = '') {
    return \Dux::request($method, $key, $default, $function);
}

/**
 * 简化URL方法
 */
function url($str = '', $params = []) {
    return \Dux::url($str, $params);
}

/**
 * 简化类调用
 */
function target($class, $layer = 'model') {
    return \Dux::target($class, $layer);
}

/**
 * 简化类配置加载
 */
function load_config($file, $enforce = true) {
    return \Dux::loadConfig($file, $enforce);
}

/**
 * 配置保存
 * @param $file
 * @param $config
 * @return array|bool
 */
function save_config($file, $config) {
    return \Dux::saveConfig($file, $config);
}

/**
 * 二维数组排序
 * @param $data
 * @param $key
 * @param string $type
 * @return mixed
 */
function array_sort($data, $key, $type = 'asc') {
    if (empty($data)) {
        return $data;
    }
    $keys = array();
    foreach ($data as $k => $v) {
        $keys[] = $v[$key];
    }
    if ($type == 'asc') {
        $sort = SORT_ASC;
    } else {
        $sort = SORT_DESC;
    }
    array_multisort($keys, $sort, $data);
    return $data;
}


/**
 * 数据签名认证
 * @param  array $data 被认证的数据
 * @return string       签名
 */
function data_auth_sign($data) {
    //数据类型检测
    if (!is_array($data)) {
        $data = (array)$data;
    }
    ksort($data); //排序
    $code = http_build_query($data);
    $config = \Config::get('dux.use');
    return sha1($code . $config['safe_key']);
}


/**
 * 遍历所有文件和目录
 * @param $dir
 * @return array
 */
function listDir($dir) {
    $dir .= substr($dir, -1) == '/' ? '' : '/';
    $dirInfo = array();
    foreach (glob($dir . '*') as $v) {
        $dirInfo[] = $v;
        if (is_dir($v)) {
            $dirInfo = array_merge($dirInfo, listDir($v));
        }
    }
    return $dirInfo;
}

/**
 * 时间格式化
 * @param $time
 * @return string
 */
function date_tran($time) {
    $date = date("m-d H:i", $time);
    $time = time() - $time;

    if ($time < 60) {
        $str = $time . '秒前';
    } elseif ($time < 3600) {
        $min = floor($time / 60);
        $str = $min . '分钟前';
    } elseif ($time < 86400) {
        $h = floor($time / 3600);
        $str = $h . '小时前';
    } elseif ($time < 172800) {
        $d = floor($time / 86400);
        if ($d == 1)
            $str = '昨天';
        else
            $str = '前天';
    } else {
        $str = $date;
    }
    return $str;
}

/**
 * HTML转义
 * @param $html
 * @return string
 */
function html_in($html) {
    return \dux\lib\Str::htmlIn($html);

}

/**
 * HTML反转义
 * @param $str
 * @return string
 */
function html_out($str) {
    return \dux\lib\Str::htmlOut($str);
}

/**
 * 清理HTML代码
 * @param $str
 * @return string
 */
function html_clear($str) {
    return \dux\lib\Str::htmlClear($str);
}

/**
 * 字符串截取
 * @param $str
 * @param int $len
 * @param bool $suffix
 * @return string
 */
function str_len($str, $len = 20, $suffix = true) {
    return \dux\lib\Str::strLen($str, $len, $suffix);
}