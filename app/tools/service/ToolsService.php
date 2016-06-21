<?php
namespace app\tools\service;
/**
 * 模块接口
 */
class ToolsService extends \app\base\service\BaseService {

    /**
     * 添加队列数据
     */
    public function addQuene($data) {
        $data = [
            'receive' => $data['receive'],
            'type' => html_in($data['type']),
            'title' => html_clear($data['title']),
            'content' => html_in($data['content']),
            'param' => serialize($data['param']),
        ];
        if(empty($data['type']) || empty($data['title']) || empty($data['content']) || empty($data['receive'])) {
            $this->error('队列参数不正确!');
        }
        //检查接口格式
        $typelist = target('tools/ToolsSendConfig')->typeList();
        $typeInfo = $typelist[$data['type']];
        if(empty($typeInfo)){
            return $this->error('未发现相关接口!');
        }
        if(!target($typeInfo['target'], 'send')->check($data)){
            return $this->error(target($typeInfo['target'], 'send')->getError());
        }
        $saveData = array();
        $saveData['type'] = $data['type'];
        $saveData['title'] = $data['title'];
        $saveData['content'] = $data['content'];
        $saveData['param'] = $data['param'];
        $saveData['receive'] = $data['receive'];
        $saveData['start_time'] = time();

        if(!target('tools/ToolsSend')->add($saveData)){
            return $this->error('队列添加失败!');
        }
        return $this->success();
    }
}

