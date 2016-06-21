<?php

/**
 * 筛选管理
 */
namespace app\site\model;

use app\system\model\SystemModel;

class SiteFilterModel extends SystemModel {

    private $urlParam = [];
    private $filterParam = [];
    private $filterStatus = false;
    private $classIds = [];
    private $attrList = [];
    private $ids = [];

    protected $infoModel = [
        'pri' => 'filter_id',
        'validate' => [
            'name' => [
                'len' => ['1, 20', '名称输入不正确!', 'must', 'all'],
            ],
        ],
        'format' => [
            'name' => [
                'function' => ['html_in', 'all'],
            ],
        ],
        'into' => '',
        'out' => '',
    ];

    public function _saveBefore($data) {
        $this->beginTransaction();
        return $data;
    }

    public function _saveAfter($type, $data) {
        $list = $_POST['attr'];
        $attrData = [];
        foreach ($list['name'] as $key => $vo) {
            $attrData[] = [
                'filter_id' => $data['filter_id'],
                'attr_id' => $list['attr_id'][$key],
                'name' => $vo,
                'type' => $list['type'][$key],
                'value' => $list['value'][$key]
            ];
        }
        if (empty($attrData)) {
            $this->error = '请添加属性!';
            $this->rollBack();
            return false;
        }
        $attrIds = [];
        foreach ($attrData as $v) {
            if (empty($v['name']) || empty($v['value'])) {
                $this->error('请完善属性信息!');
                return false;
            }
            if ($v['attr_id']) {
                $attrIds[] = $v['attr_id'];
                $status = target('site/SiteFilterAttr')->edit($v);
            } else {
                $status = target('site/SiteFilterAttr')->add($v);
                $attrIds[] = $status;
            }
            if (!$status) {
                $this->error = '处理失败,请重试!';
                $this->rollBack();
                return false;
            }
        }

        $list = target('site/SiteFilterAttr')->loadList(['filter_id' => $data['filter_id'], '_sql' => 'attr_id NOT IN ('. implode(',', $attrIds) .')']);
        if($list) {
            foreach($list as $vo) {
                if(!target('site/SiteContentAttr')->where(['attr_id' => $vo['attr_id']])->delete()) {
                    $this->error = '处理失败,请重试!';
                    $this->rollBack();
                    return false;
                }
                if(!target('site/SiteFilterAttr')->del($vo['attr_id'])) {
                    $this->error = '处理失败,请重试!';
                    $this->rollBack();
                    return false;
                }
            }
        }

        $this->commit();
        return true;
    }

    public function delData($id) {
        $attrIds = [];
        $attrList = target('site/SiteFilterAttr')->loadList(['filter_id' => $id]);
        if($attrList) {
            foreach($attrList as $vo) {
                $attrIds[] = $vo['attr_id'];
            }
        }
        $this->beginTransaction();
        $where = array();
        $where['filter_id'] = $id;
        if (!$this->where($where)->delete()) {
            $this->rollBack();
            return false;
        }
        if (!target('site/SiteFilterAttr')->where(['filter_id' => $id])->delete()) {
            $this->rollBack();
            return false;
        }
        if($attrIds) {
            if (!target('site/SiteContentAttr')->where(['_sql' => 'attr_id IN ('.implode(',', $attrIds).')'])->delete()){
                $this->rollBack();
                return false;
            }
        }
        $this->commit();
        return true;
    }

    public function getHtml($id, $contentId = 0) {
        $info = $this->getInfo($id);
        $list = target('site/SiteFilterAttr')->loadList(['filter_id' => $id]);
        if (empty($list)) {
            return '';
        }
        $contentInfo = [];
        if (!empty($contentId)) {
            $contentAttr = target('site/siteContentAttr')->loadList(['content_id' => $contentId]);
            $attrInfo = [];
            if (!empty($contentAttr)) {
                foreach ($contentAttr as $vo) {
                    $attrInfo[$vo['attr_id']] = explode(',', $vo['value']);
                }
            }
            $contentInfo = $attrInfo;
        }
        $html = '';
        foreach ($list as $attr) {
            $cHtml = [];
            $cHtml[] = '<select name="attr_data['.$attr['attr_id'].'][]"';
            if($attr['type']) {
                $cHtml[] = 'multiple';
            }
            $cHtml[] = 'data-am-selected="{btnWidth: \'100%\'}">';
            $valueList = explode(',', $attr['value']);
            foreach($valueList as $v) {
                $cHtml[] = '<option value="'.$v.'"';
                if(in_array($v, (array)$contentInfo[$attr['attr_id']])) {
                    $cHtml[] = 'selected';
                }
                $cHtml[] = '>'.$v.'</option>';
            }
            $cHtml[] = '</select>';
            $cHtml = implode(' ', $cHtml);
            $html .= target('site/SiteFormHtml')->layerWrap($attr['name'], $cHtml);
        }
        return $html;
    }

    public function getFilterContent($filterId, $contentId) {
        $attrData = target('site/siteFilterAttr')->loadList(['filter_id' => $filterId], 0, 'attr_id asc');
        $contentData = target('site/SiteContentAttr')->loadList(['content_id' => $contentId]);
        if(empty($attrData) || empty($contentData)) {
            return [];
        }

        $attrList = [];
        foreach ($attrData as $key => $vo) {
            $attrList[$vo['attr_id']] = $vo;
        }

        $attrData = [];
        foreach($contentData as $key => $vo) {
            $val = explode(',', $vo['value']);
            $attr = $attrList[$vo['attr_id']];
            if(empty($attr)) {
                continue;
            }
            $attrData[] = [
                'name' => $attr['name'],
                'val' => $val
            ];
        }
        return $attrData;

    }

    public function getFilter($target, $classIds, $urlParam = []) {
        $this->classIds = $classIds;
        $this->getFilterParam($urlParam);
        $this->getFilterData($target);
        return [
            'status' => $this->filterStatus,
            'attrList' => $this->attrList,
            'ids' => $this->ids,
            'filterParam' => $this->filterParam
        ];
    }


    private function getFilterParam($urlParam = []) {
        $getParam = request('get');
        $attrArray = [];
        foreach ($getParam as $key => $vo) {
            if (stristr($key, 'attr_', 0) !== false) {
                $vo = urldecode($vo);
                $attrArray[substr($key, 5)] = $vo;
                $urlParam[$key] = $vo;
            }
        }
        $this->filterParam = $attrArray;
        $this->urlParam = $urlParam;
    }

    private function getFilterData($target) {
        $filterParam = $this->filterParam;
        $attrCond = [];
        $attrList = [];
        foreach ($filterParam as $key => $val) {
            if ($key && $val) {
                $attrCond[] = ' attr_id = ' . intval($key) . ' and FIND_IN_SET("' . $val . '",value)';
            }
        }
        $ids = [];
        if ($attrCond) {
            $this->filterStatus = true;
            $tempArray = [];
            foreach ($attrCond as $key => $cond) {
                $tempArray[] = '(' . $cond . ')';
            }
            $childSql = join(' or ', $tempArray);
            $goodsIdArray = target('site/SiteContentAttr')->query('SELECT content_id FROM {pre}site_content_attr WHERE ' . $childSql . '  GROUP BY content_id HAVING count(content_id) >= ' . count($attrCond));
            if ($goodsIdArray) {
                foreach ($goodsIdArray as $key => $val) {
                    $ids[] = $val['content_id'];
                }
                $ids = array_unique($ids);
            }
        }
        $classList = target($target)->loadList([
            '_sql' => 'class_id in (' . $this->classIds . ')'
        ]);
        $filterIds = [];
        if (!empty($classList)) {
            foreach ($classList as $vo) {
                if ($vo['filter_id']) {
                    $filterIds[] = $vo['filter_id'];
                }
            }
        }
        $filterIds = empty($filterIds) ? [] : array_unique($filterIds);
        $filterIds = implode(',', $filterIds);

        if ($filterIds) {
            $getData = request('get');
            $attrData = target('site/siteFilterAttr')->loadList(['_sql' => 'filter_id in (' . $filterIds . ')'], 0, 'attr_id asc');
            foreach ($attrData as $key => $val) {
                $attrArray = explode(",", $val['value']);
                $getVal = urldecode($getData['attr_' . $val['attr_id']]);
                $resList = [];
                foreach ($attrArray as $k => $v) {
                    $resList[] = [
                        'name' => $v,
                        'url' => $this->filterUrl(['attr_' . $val['attr_id'] => $v]),
                        'cur' => ($v == $getVal) ? true : false,
                    ];
                }
                $attrList[] = [
                    'attr_id' => $val['attr_id'],
                    'name' => $val['name'],
                    'value' => array_merge([[
                        'name' => '不限',
                        'url' => $this->filterUrl(['attr_' . $val['attr_id'] => '']),
                        'cur' => !request('get', 'attr_' . $val['attr_id']) ? true : false,
                    ]], $resList),
                ];
            }
        }
        $this->attrList = $attrList;
        if($ids) {
            $this->ids = implode(',', $ids);
        }else {
            $this->ids = 0;
        }
    }

    private function filterUrl($param) {
        $urlParam = array_filter(array_merge($this->urlParam, $param));
        return url('index', $urlParam);
    }


}