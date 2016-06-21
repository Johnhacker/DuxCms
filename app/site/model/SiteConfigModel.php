<?php

/**
 * 站点设置
 */
namespace app\site\model;

use app\system\model\SystemModel;

class SiteConfigModel extends SystemModel {

    protected $infoModel = [
        'pri' => 'config_id',
        'into' => '',
        'out' => '',
    ];

    public function getConfig() {
        $list = $this->loadList();
        $data = array();
        foreach($list as $vo) {
            $data[$vo['name']] = $vo['content'];
        }
        return $data;
    }

    public function saveInfo() {
        $post = request('post');
        foreach ($post as $key => $value) {
            $where = array();
            $where['name'] = $key;
            $data = array();
            $data['content'] = html_in($value);
            if(!$this->data($data)->where($where)->update()){
                return false;
            }
        }
        return true;
    }


}