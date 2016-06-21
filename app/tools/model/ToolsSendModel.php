<?php

/**
 * 推送队列
 */
namespace app\tools\model;

use app\system\model\SystemModel;

class ToolsSendModel extends SystemModel {

    protected $infoModel = [
        'pri' => 'send_id'
    ];

    public function loadList($where = array(), $limit = 0, $order = '') {
        $typeList = target('tools/ToolsSendConfig')->typeList();
        $list = $this->where($where)->limit($limit)->order('send_id desc')->select();
        foreach ($list as $key => $vo) {
            $list[$key]['type_name'] = $typeList[$vo['type']]['name'];
        }
        return $list;
    }


}