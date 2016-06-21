<?php
namespace app\system\admin;

/**
 * 系统上传
 */
class UploadAdmin extends \app\system\admin\SystemAdmin {

    /**
     * AJAX上传文件
     */
    public function index() {
        $return = array('status' => 1, 'info' => '上传成功', 'data' => '');
        $file = target('system/SystemFile');
        $info = $file->uploadData();
        $info = current($info);
        if ($info) {
            $return['data'] = $info;
        } else {
            $return['status'] = 0;
            $return['info'] = $file->getError();
        }
        $this->json($return);
    }

    /**
     * 编辑器上传
     */
    public function editor() {
        $return = array('success' => true, 'msg' => '上传成功', 'file_path' => '');
        $file = target('system/SystemFile');
        $info = $file->uploadData();
        $info = current($info);
        if ($info) {
            $return['file_path'] = $info['url'];
        } else {
            $return['success'] = 0;
            $return['msg'] = $file->getError();
        }
        $this->json($return);
    }

}

