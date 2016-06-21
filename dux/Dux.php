<?php

/**
 * 注册框架方法
 */

class Dux {

    private static $objArr = array();

    private static $fileList = array();

    /**
     * 注册模板引擎类
     */
    public static function view($config = array()) {
        $sysConfig = \Config::get('dux.tpl');
        $config = array_merge((array)$sysConfig, (array)$config);
        return new \dux\kernel\View($config);
    }

    /**
     * 注册缓存类
     */
    public static function cache($configName = 'default') {
        return new \dux\lib\Cache($configName);
    }

    /**
     * 注册COOKIE类
     */
    public static function cookie($configName = 'default') {
        $key = 'cookie.' . $configName;
        if( !isset(self::$objArr[$key]) ){
            self::$objArr[$key] = new \dux\lib\Cookie($configName);
        }
        return self::$objArr[$key];
    }

    /**
     * 注册会话类
     */
    public static function session($configName = 'default') {
        $key = 'session.' . $configName;
        if( !isset(self::$objArr[$key]) ){
            self::$objArr[$key] = new \dux\lib\Session($configName);
        }
        return self::$objArr[$key];
    }


    /**
     * 获取请求数据
     */
    public static function request($method = '', $key = '', $default = '', $function = '') {
        $method = strtolower($method);
        switch($method) {
            case 'get':
                $data = $_GET;
                break;
            case 'post':
                $data = $_POST;
                break;
            default:
                $data = array_merge($_GET, $_POST);
        }
        if($key){
            $data = $data[$key];
            if($function) {
                $data = call_user_func($function, $data);
            }
            if(!empty($default) && empty($data)) {
                $data = $default;
            }
            return $data;
        }else {
            return $data;
        }
    }

    /**
     * URL生成方法
     */
    public static function url($str = '', $params = array()) {
        $param = explode('/', $str, 4);
        $param = array_filter($param);
        $paramCount = count($param);
        $module = \Config::get('dux.module');
        switch ($paramCount) {
            case 1:
                $layer = LAYER_NAME;
                $app = APP_NAME;
                $controller = MODULE_NAME;
                $action = $param[0];
                break;
            case 2:
                $layer = LAYER_NAME;
                $app = APP_NAME;
                $controller = $param[0];
                $action = $param[1];
                break;
            case 3:
                $layer = LAYER_NAME;
                $app = $param[0];
                $controller = $param[1];
                $action = $param[2];
                break;
            case 4:
                $layer = $param[0];
                $app = $param[1];
                $controller = $param[2];
                $action = $param[3];
                break;
            case 0:
            default:
                $layer = LAYER_NAME;
                $app = APP_NAME;
                $controller = MODULE_NAME;
                $action = ACTION_NAME;
                break;
        }
        $moduleUrl = $module[$layer];
        if($moduleUrl) {
            $url = $module[$layer] . '/' . $app . '/' . $controller . '/' . $action;
        }else{
            $url = $app . '/' . $controller . '/' . $action;
        }
        if(!empty($params)){
            foreach ($params as $key => $value) {
                $url .= '/' . $key . '-' . urlencode($value);
            }
        }
        return ROOT_URL . '/' . $url;
    }

    /**
     * 类调用方法
     */
    public static function target($class, $layer = 'model') {
        $param = explode('/', $class, 2);
        $paramCount = count($param);
        $app = '';
        $module = '';
        switch ($paramCount) {
            case 1:
                $app = APP_NAME;
                $module = $param[0];
                break;
            case 2:
                $app = $param[0];
                $module = $param[1];
                break;
        }
        $app = strtolower($app);
        $module = ucfirst($module);
        $class = "\\app\\{$app}\\{$layer}\\{$module}" . ucfirst($layer);
        if (isset(self::$objArr[$class])) {
            return self::$objArr[$class];
        }
        if (!class_exists($class)) {
            throw new \Exception("Class '{$class}' not found", 500);
        }
        $obj = new $class();
        self::$objArr[$class] = $obj;
        return $obj;
    }

    /**
     * 加载配置文件
     */
    public static function loadConfig($file, $enforce = true) {
        $file = ROOT_PATH . $file. '.php';
        if (!is_file($file)) {
            if ($enforce) {
                throw new \Exception("File '{$file}' not found", 500);
            }
            return array();
        }
        return require($file);
    }

    /**
     * 保存配置
     */
    public static function saveConfig($file, $config) {
        if (empty($config) || !is_array($config)) {
            return array();
        }
        $conf = load_config($file);
        $config = array_merge($conf, $config);
        $confString = var_export($config, true);
        $find = array("'true'", "'false'", "'1'", "'0'");
        $replace = array("true", "false", "1", "0");
        $confString = str_replace($find, $replace, $confString);
        $confString = "<?php \n return " . $confString . ';';
        if (file_put_contents(ROOT_PATH . $file . '.php', $confString)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 载入类库
     * @param string $file
     * @return bool
     */
    public static function import($file = '') {
        if(self::$fileList[$file]) {
            return true;
        }
        $dir = str_replace('\\', '/', $file);
        self::$fileList[$file] = $dir;
        require_once ROOT_PATH . $dir . '.php';
        return true;
    }

    public static $codes = array(
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',

        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status',
        208 => 'Already Reported',

        226 => 'IM Used',

        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => '(Unused)',
        307 => 'Temporary Redirect',
        308 => 'Permanent Redirect',

        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Payload Too Large',
        414 => 'URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Range Not Satisfiable',
        417 => 'Expectation Failed',

        422 => 'Unprocessable Entity',
        423 => 'Locked',
        424 => 'Failed Dependency',

        426 => 'Upgrade Required',

        428 => 'Precondition Required',
        429 => 'Too Many Requests',

        431 => 'Request Header Fields Too Large',

        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        506 => 'Variant Also Negotiates',
        507 => 'Insufficient Storage',
        508 => 'Loop Detected',

        510 => 'Not Extended',
        511 => 'Network Authentication Required'
    );

    /**
     * 发送HTTP头
     * @param $code
     * @param null $msg
     */
    public static function header($code, $content = null, $data = null) {
        header(implode(' ', [$_SERVER['SERVER_PROTOCOL'], $code, self::$codes[$code]]));
        call_user_func($content, $data);
        exit;
    }

    /**
     * 页面不存在
     */
    public static function notFound() {
        static::header(404, function($msg) {
            if(headers_sent()) {
                header("Content-Type: text/html; charset=UTF-8");
            }
            echo $msg;
        }, "<h1>404 Not Found</h1>");
    }

    /**
     * 运行时间获取
     * @return string
     */
    public static function runTime() {
        if(!defined("START_TIME")) {
            return "";
        }
        $stime = explode(" ", START_TIME);
        $etime = explode(" ", microtime());
        return sprintf("%0.4f", round($etime[0]+$etime[1]-$stime[0]-$stime[1], 4));
    }
}