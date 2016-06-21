<?php

namespace dux;

class Engine {

    protected static $classes = array();

    public function __construct() {
        $this->init();
    }

    /**
     * 初始化
     */
    public function init() {
        $this->autoload();
        $this->handleErrors();
        $this->route();
    }

    /**
     * 注册类
     */
    public function autoload() {
        spl_autoload_register(array(__CLASS__, 'loadClass'));
    }

    /**
     * 加载类文件
     * @param $class
     * @return bool
     */
    public function loadClass($class) {
        $classFile = str_replace(array('\\', '_'), '/', $class) . '.php';
        $file = ROOT_PATH . $classFile;
        if (!isset(self::$classes[$file])) {
            if (!file_exists($file)) {
                return false;
            }
            self::$classes[$classFile] = $classFile;
            require_once $classFile;
        }
        return true;
    }

    /**
     * 结果异常错误
     */
    public function handleErrors() {
        set_error_handler(array($this, 'handleError'));
        set_exception_handler(array($this, 'handleException'));
    }

    /**
     * 错误接管
     * @param $errno
     * @param $errstr
     * @param $errfile
     * @param $errline
     * @throws \ErrorException
     */
    public function handleError($errno, $errstr, $errfile, $errline) {
        if ($errno & error_reporting()) {
            throw new \ErrorException($errstr, $errno, 0, $errfile, $errline);
        }
    }

    /**
     * 异常接管
     * @param $e
     */
    public function handleException($e) {
        if (\Config::get('dux.log')) {
            error_log("{$e->getMessage()} line {$e->getLine()} in file {$e->getFile()}");
        }
        $html = "<title>Dux System Engine</title>";
        if (!\Config::get('dux.debug')) {
            \Dux::notFound();
        } else {
            $html .= "<h1>{$e->getMessage()}</h1>";
            $html .= "<code>line {$e->getLine()} in file {$e->getFile()}</code>";
            $html .= "<p>";
            foreach ($e->getTrace() as $value) {
                $html .= "{$value['file']} line {$value['line']} <br>";
            }
            $html .= "</p>";
        }
        $html .= "<p> run time " . \Dux::runTime() . "s</p>";

        \Dux::header(500, function($html) {
            header("Content-Type: text/html; charset=UTF-8");
            echo $html;
        }, $html);

    }

    /**
     * 解析路由
     */
    public function route() {
        $url = str_replace(ROOT_SCRIPT, '', $_SERVER['REQUEST_URI']);
        $url = trim($url, '/');
        $urlParse = parse_url($url);
        $urlPath = explode('.', $urlParse['path'], 2);
        $urlArray = explode("/", $urlPath[0], 5);
        $moduleConfig = \Config::get('dux.module');
        $moduleRule = array_flip($moduleConfig);
        $roleName = null;
        if(in_array($urlArray[0], $moduleConfig)) {
            $roleName = $urlArray[0];
            $layer = $moduleRule[$urlArray[0]];
            $appName = $urlArray[1];
            $modelName =  $urlArray[2];
            $actionName = $urlArray[3];
            $params = $urlArray[4];
        }else{
            foreach($moduleRule as $key => $vo) {
                if(empty($key)) {
                    $layer = $vo;
                    continue;
                }
            }
            $appName = $urlArray[0];
            $modelName = $urlArray[1];
            $actionName = $urlArray[2];
            $params = $urlArray[3] . '/' . $urlArray[4];
        }
        $layer = empty($layer) ? 'controller' : $layer;
        $appName = empty($appName) ? 'index' : $appName;
        $modelName = empty($modelName) ? 'Index' : $modelName;
        $actionName = empty($actionName) ? 'index' : $actionName;
        if (!defined('ROLE_NAME')) define('ROLE_NAME', $roleName);
        if (!defined('LAYER_NAME')) define('LAYER_NAME', $layer);
        if (!defined('APP_NAME')) define('APP_NAME', strtolower($appName));
        if (!defined('MODULE_NAME')) define('MODULE_NAME', ucfirst($modelName));
        if (!defined('ACTION_NAME')) define('ACTION_NAME', $actionName);
        $paramArray = explode("/", $params);
        $paramArray = array_filter($paramArray);
        if (empty($paramArray)) return;
        foreach ($paramArray as $key => $value) {
            $list = explode('-', $value, 2);
            if (count($list) == 2) {
                $_GET[$list[0]] = $list[1];
            }
        }
    }

    /**
     * 运行框架
     * @throws \Exception
     */
    public function run() {
        $class = '\app\\' . APP_NAME . '\\'. LAYER_NAME .'\\' . MODULE_NAME . ucfirst(LAYER_NAME);

        $action = ACTION_NAME;
        if (!class_exists($class)) {
            throw new \Exception("Class not found: {$class}.");
        }
        $obj = new $class();
        if (!method_exists($class, $action) && !method_exists($class, '__call')) {
            throw new \Exception("Action not found: {$class} -> {$action}.");
        }
        $obj->$action();
    }
}