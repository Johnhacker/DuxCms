<?php

/**
 * 推送设置
 */
namespace app\tools\model;

use app\system\model\SystemModel;

class ToolsSendConfigModel extends SystemModel {

    protected $infoModel = [
        'pri' => 'config_id',
        'validate' => [
            'type' => [
                'required' => ['', '类型参数获取不正确!', 'must', 'all'],
            ],
        ],
    ];

    /**
     * 获取配置
     * @param $type
     * @return mixed
     */
    public function getConfig($type) {
        $where = array();
        $where['type'] = $type;
        $info = $this->getWhereInfo($where);
        return unserialize($info['setting']);
    }

    /**
     * 获取服务接口
     */
    public function typeList() {
        $list = hook('service', 'Send', 'Type');
        $data = array();
        foreach ($list as $value) {
            $data = array_merge_recursive((array)$data, (array)$value);
        }
        return $data;
    }


}