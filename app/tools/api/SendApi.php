<?php
namespace app\tools\api;

/**
 * 自动推送
 */

//ignore_user_abort(true);
set_time_limit(0);

class SendApi {

    private $cache = null;
    private $lockName = 'send.status';

    public function __construct() {
        $config = \Config::get('dux.use');
        $this->cache = \Dux::cache($config['data_cache']);
    }

    /**
     * 执行计划任务
     */
    public function index() {
        if ($this->lockStatus()) {
            exit;
        }
        $where = array();
        $where['status'] = 0;
        $list = target('tools/ToolsSend')->where($where)->limit(20)->order('start_time asc')->select();
        if (empty($list)) {
            return;
        }
        $this->lock(true);
        $typeList = target('tools/ToolsSendConfig')->typeList();
        foreach ($list as $vo) {
            $typeInfo = $typeList[$vo['type']];
            if (empty($typeInfo)) {
                $this->complete($vo['send_id'], $vo['type'] . '推送类型不存在', false);
                continue;
            }
            if(!$this->send($vo, $typeInfo['target'])){
                continue;
            }
        }
        $this->lock(false);
    }

    /**
     * 执行发送
     */
    protected function send($info = array(), $target) {
        $data = array();
        $data['receive'] = $info['receive'];
        $data['title'] = $info['title'];
        $data['content'] = $info['content'];
        $data['param'] = unserialize($info['param']);
        if (!empty($data['param'])) {
            foreach ($data['param'] as $key => $vo) {
                $data['content'] = str_replace('{' . $key . '}', $vo, $data['content']);
            }
        }
        if(target($target, 'send')->send($data)){
            $this->complete($info['send_id'], '推送成功！', true);
            return true;
        }else{
            $this->complete($info['send_id'], target($target, 'send')->getError(), false);
            return false;
        }
    }

    /**
     * 完成状态
     */
    protected function complete($sendId, $remark = '未知', $status = true) {
        $data = array();
        $data['send_id'] = $sendId;
        if($status) {
            $data['status'] = 1;
        }else{
            $data['status'] = 2;
        }
        $data['remark'] = $remark;
        $data['stop_time'] = time();
        return target('tools/ToolsSend')->edit($data);
    }

    /**
     * 锁定状态
     * @param bool $status
     */
    private function lock($status = true) {
        if ($status) {
            $this->cache->set($this->lockName, time(), 300);
        } else {
            $this->cache->del($this->lockName);
        }
    }

    /**
     * 状态查询
     * @return bool
     */
    private function lockStatus() {
        if ($this->cache->get($this->lockName)) {
            return true;
        } else {
            return false;
        }
    }

}