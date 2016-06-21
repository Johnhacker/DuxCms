<?php
namespace app\site\service;

/**
 * 标签接口
 */
class LabelService {

    /**
     * 导航列表
     * @param $data
     * @return mixed
     */
	public function nav($data){
        $where = array();
        if(!empty($data['group_id'])){
            $where['group_id'] = $data['group_id'];
        }else {
            $where['group_id'] = 1;
        }
        //上级栏目
        $parentId = 0;
        if(isset($data['parent_id'])){
            $parentId = $data['parent_id'];
        }
        //其他条件
        if(!empty($data['where'])){
            $where['_sql'] = $data['where'];
        }
        $limit = 10;
        if(!empty($data['limit'])){
            $limit = $data['limit'];
        }
        $list = target('site/SiteNav')->loadList($where, $limit);
        if($list){
            $list = target('site/SiteNav')->getTree($list, $parentId, ['parent_id', 'nav_id']);
        }
        return $list;
	}

    /**
     * 碎片内容
     * @param $data
     * @return mixed
     */
    public function fragment($data) {
        $where = array();
        $where['fragment_id'] = $data['id'];

        if(!empty($data['where'])){
            $where['_sql'] = $data['where'];
        }

        $info = target('site/SiteFragment')->getInfo($data['id']);
        return html_out($info['content']);
    }

    /**
     * 表单内容
     * @param $data
     * @return mixed
     */
    public function form($data) {
        $formId = intval($data['form_id']);
        if(empty($formId)) {
            return [];
        }
        $formInfo = target('site/SiteForm')->getInfo($formId);
        if (empty($formInfo)) {
            return [];
        }
        $where = [];
        if(!empty($data['where'])){
            $where['_sql'] = $data['where'];
        }
        $limit = 10;
        if(!empty($data['limit'])){
            $limit = $data['limit'];
        }
        if (empty($data['order'])) {
            $data['order'] = 'data_id desc';
        }
        return target('site/SiteFormData')->table('form_' . $formInfo['label'])->loadList($where, $limit, 'data_id desc');

    }

}
