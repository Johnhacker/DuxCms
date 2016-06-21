<?php
namespace app\base\model;
/**
 * 上传模块
 */
class UploadModel {

    private $error = '';

    /**
     * 上传数据
     * @param array $config 上传配置信息可选
     * @return array 文件信息
     */
    public function upload($config = array()) {
        $baseConfig = \Config::get('dux.use_upload');
        $config = array_merge((array)$baseConfig, (array)$config);
        if (empty($config['dir_name'])) {
            $config['dir_name'] = date('Y-m-d');
        }
        $upConfig = array(
            'maxSize' => intval($config['upload_size']) * 1024 * 1024,
            'allowExts' => explode(',', $config['upload_exts']),
            'rootPath' => ROOT_PATH . 'upload/',
            'savePath' => $config['dir_name'] . '/',
            'saveRule' => 'md5_file',
            'driver' => 'Local',
            'driverConfig' => array(),
        );
        $path = 'upload/' . $config['dir_name'] . '/';
        //上传
        $upload = new \dux\lib\Upload($upConfig);
        if (!$upload->upload()) {
            $this->error = $upload->getError();
            return false;
        }
        //上传信息
        $list = $upload->getUploadFileInfo();

        if(empty($list)) {
            $this->error = '上传文件不存在!';
            return false;
        }

        $returnData = [];
        foreach($list as $key => $info) {
            //设置基本信息
            $file = $path . $info['savename'];
            $fileUrl = ROOT_URL . '/' . $file;
            $filePath = pathinfo($info['savename']);
            $fileName = $filePath['filename'];
            $fileTitle = pathinfo($info['name']);
            $fileTitle = $fileTitle['filename'];
            $fileExt = $info['extension'];
            $saveName = $info['savename'];
            //处理图片数据
            $imgType = array('jpg', 'jpeg', 'png', 'gif', 'bmp');
            if (in_array(strtolower($fileExt), $imgType)) {
                //设置缩图
                if ($config['thumb_status']) {
                    $image = new \dux\lib\Image(ROOT_PATH . $file);
                    $thumbFile = $path . 'thumb_' . $saveName;
                    $status = $image->thumb($config['thumb_width'], $config['thumb_height'], $config['thumb_type'])->output(ROOT_PATH . $thumbFile);
                    if ($status) {
                        $file = $thumbFile;
                    }
                }
                //设置水印
                if ($config['water_status']) {
                    $image = new \dux\lib\Image(ROOT_PATH . $file);
                    $wateFile = $path . 'wate_' . $saveName;
                    $status = $image->water(ROOT_PATH . 'public/watermark/' . $config['water_image'], $config['water_position'])->output(ROOT_PATH . $wateFile);
                    if ($status) {
                        $file = $wateFile;
                    }
                }
            }
            //录入文件信息
            $data = array();
            $data['url'] = ROOT_URL . '/' . $file;
            $data['original'] = $fileUrl;
            $data['title'] = $fileTitle;
            $data['ext'] = $fileExt;
            $data['size'] = $info['size'];
            $data['time'] = time();

            $returnData[$key] = $data;
        }
        return $returnData;
    }

    /**
     * 获取错误信息
     */
    public function getError() {
        return $this->error;
    }

}
