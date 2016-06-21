<?php

/**
 * 表单
 */
namespace app\site\controller;

class FormController extends \app\index\controller\SiteController {

    public function index() {
        $formId = request('get', 'id');
        if (empty($formId)) {
            $this->error404();
        }
        $formInfo = target('site/SiteForm')->getInfo($formId);
        if (empty($formInfo)) {
            $this->error404();
        }
        if (!$formInfo['status_list']) {
            $this->error404();
        }

        $this->setMeta($formInfo['name']);

        $this->setCrumb([[
            'name' => $formInfo['name'],
            'url' => url('index', ['id' => $formId])
        ]]);

        $pageParams = [];
        $pageParams['id'] = $formId;

        $pageLimit = 15;

        $where = [];
        $model = target('site/SiteFormData');

        $count = $model->table('form_' . $formInfo['label'])->countList($where);
        $pageData = $this->pageData($count, $pageLimit, $pageParams);
        $list = $model->table('form_' . $formInfo['label'])->loadList($where, $pageData['limit'], 'data_id desc');
        if ($list) {
            $formFields = target('site/SiteFormField')->loadList(['form_id' => $formId]);
            foreach ($formFields as $field) {
                foreach ($list as $key => $vo) {
                    $list[$key][$field['label']] = call_user_func_array([target('site/SiteFormDataShow'), $field['type']], [$vo[$field['label']], $field['config']]);
                }
            }
        }

        $tpl = $formInfo['tpl_list'];

        $this->assign('formInfo', $formInfo);
        $this->assign('page', $pageData['html']);
        $this->assign('pageList', $list);
        $this->assign('pageParams', $pageParams);
        $this->siteDisplay($tpl);
    }

    public function info() {
        $formId = request('get', 'form_id');
        $id = request('get', 'id');
        if (empty($formId) || empty($id)) {
            $this->error404();
        }
        $formInfo = target('site/SiteForm')->getInfo($formId);
        if (empty($formInfo)) {
            $this->error404();
        }
        if (!$formInfo['status_info']) {
            $this->error404();
        }
        $info = target('site/SiteFormData')->table('form_' . $formInfo['label'])->getInfo($id);
        if (empty($info)) {
            $this->error404();
        }
        $formFields = target('site/SiteFormField')->loadList(['form_id' => $formId]);
        foreach ($formFields as $field) {
            $info[$field['label']] = call_user_func_array([target('site/SiteFormDataShow'), $field['type']], [$info[$field['label']], $field['config']]);
        }
        $this->setMeta('详情 - ' . $formInfo['name']);
        $this->setCrumb([
            [
                'name' => $formInfo['name'],
                'url' => url('index', ['id' => $formId])
            ],
            [
                'name' => '详情',
                'url' => url('index', ['form_id' => $formId, 'id' => $id])
            ]
        ]);
        $tpl = $formInfo['tpl_info'];
        $this->assign('formInfo', $formInfo);
        $this->assign('info', $info);
        $this->siteDisplay($tpl);
    }

    public function submit() {
        $formId = request('get', 'id');
        if (empty($formId)) {
            $this->error('表单ID不正确!');
        }
        $formInfo = target('site/SiteForm')->getInfo($formId);
        if (empty($formInfo)) {
            $this->error404();
        }
        if (!$formInfo['submit']) {
            $this->error404();
        }
        $post = request('post');
        $data = [];
        $formFields = target('site/SiteFormField')->loadList(['form_id' => $formId]);
        foreach ($formFields as $field) {
            if (!$field['submit']) {
                continue;
            }
            if (method_exists(target('site/SiteFormFieldMediate'), $field['type'])) {
                $post[$field['label']] = call_user_func_array([target('site/SiteFormFieldMediate'), $field['type']], [$field['label'], $field['config']]);
            }
            $data[$field['label']] = call_user_func_array([target('site/SiteFormFieldFormat'), $field['type']], [$post[$field['label']], $field['config']]);
            if ($field['must']) {
                $validate = call_user_func_array([target('site/SiteFormFieldValidate'), $field['type']], [$data[$field['label']], $field['config']]);
                if (!$validate) {
                    $this->error($field['name'] . '输入不正确!');
                }
            }
        }
        if (empty($data)) {
            $this->error('暂无提交数据!');
        }
        $id = target('site/SiteFormData')->table('form_' . $formInfo['label'])->add($data);
        if (empty($id)) {
            $this->error($formInfo['name'] . '提交失败,请稍后再试!');
        }
        $this->success($formInfo['name'] . '提交成功!');
    }

}