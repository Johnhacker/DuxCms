<?php

/**
 * 公共API
 */

namespace dux\kernel;

class Api {

    protected $data;

    /**
     * Api constructor.
     */
    public function __construct() {
        $this->data = request();
    }

    /**
     * 返回成功数据
     * @param array $data
     */
    public function success($msg = '', $data = []) {
        if(empty($msg)) {
            $msg = \Dux::$codes[200];
        }
        $data = [
            'code' => 200,
            'message' => $msg,
            'result' => $data
        ];
        \Dux::header(200, function($array){
            $this->returnData($array);
        }, $data);
    }

    /**
     * 返回错误数据
     * @param int $code
     * @param string $msg
     */
    public function error($msg = '', $code = 500) {
        if(empty($msg)) {
            $msg = \Dux::$codes[$code];
        }
        $data = [
            'code' => $code,
            'message' => $msg,
        ];
        \Dux::header($code, function($array) {
            $this->returnData($array);
        }, $data);
    }

    /**
     * 返回数据
     * @param $data
     * @param string $type
     */
    public function returnData($data, $type = 'json') {
        $format = request('', 'format');
        if (empty($format)) {
            $format = $type;
        }
        $callback = request('', 'callback');
        $format = strtolower($format);
        $charset = request('', 'charset');
        switch ($format) {
            case 'jsonp' :
                call_user_func_array([$this, 'return' . ucfirst($format)], [$data, $callback, $charset]);
                break;
            case 'json':
            default:
                call_user_func_array([$this, 'return' . ucfirst($format)], [$data, $charset]);
        }
    }

    /**
     * 返回JSON数据
     * @param array $data
     */
    public function returnJson($data = [], $charset="utf-8") {
        header("application/json; charset={$charset};");
        echo json_encode($data);
    }

    /**
     * 返回JSONP数据
     * @param array $data
     * @param string $callback
     */
    public function returnJsonp($data = [], $callback = 'q', $charset="utf-8") {
        header("application/javascript; charset={$charset};");
        echo $callback . '(' . json_encode($data) . ');';
    }

}