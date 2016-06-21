<?php

/**
 * 图像处理类
 */

namespace dux\lib;

class Image {

    /**
     * 图像文件
     * @var string
     */
    protected $img;

    /**
     * 图像驱动
     * @var string
     */
    protected $driver;

    /**
     * 驱动对象
     * @var array
     */
    protected static $objArr = array();

    /**
     * 构建函数
     * @param string $img 图片路径
     * @param string $driver 图片驱动
     */
    public function __construct($img, $driver = 'gd') {
        $this->img = $img;
        $this->driver = $driver;
    }

    /**
     * 回调驱动
     * @param $method
     * @param $args
     * @return mixed
     * @throws \Exception
     */
    public function __call($method, $args){
        if( !isset(self::$objArr[$this->img]) ){
            $imageDriver = __NAMESPACE__.'\image\\' . ucfirst( $this->driver ).'Driver';
            if( !class_exists($imageDriver) ) {
                throw new \Exception("Image Driver '{$imageDriver}' not found'", 500);
            }
            self::$objArr[$this->img] = new $imageDriver( $this->img );
        }
        return call_user_func_array(array(self::$objArr[$this->img], $method), $args);
    }

    /**
     * 析构函数
     */
    public function __destruct() {
        self::$objArr[$this->img]->__destruct();
    }

}