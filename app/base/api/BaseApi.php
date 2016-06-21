<?php

/**
 * 基础API
 */

namespace app\base\api;

class BaseApi extends \dux\kernel\Api {

    protected $sysInfo;
    protected $sysConfig;

    public function __construct() {
        parent::__construct();
        $this->sysInfo = \Config::get('dux.info');
        $this->sysConfig = \Config::get('dux.use');
        $this->checkLink();
        target('system/Statistics', 'service')->incStats('api');
    }

    /**
     * 检查链接码
     */
    private function checkLink() {
        if($this->sysConfig['com_key'] <> $this->data['auth_link']) {
            $this->error('Link Error code', 403);
        }
    }

}