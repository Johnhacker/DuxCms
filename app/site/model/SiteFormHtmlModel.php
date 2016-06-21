<?php

/**
 * 表单HTML
 */
namespace app\site\model;

class SiteFormHtmlModel {

    public function text($label, $must, $tip, $data, $config) {
        $must = $must ? 'required' : '';
        $config = explode(',', $config, 2);
        $min = 0;
        $max = 0;
        if ($config[0]) {
            $min = intval($config[0]);
        }
        if ($config[1]) {
            $max = intval($config[1]);
        }
        $min = $min ? ' minlength="' . $min . '" ' : '';
        $max = $max ? ' maxlength="' . $max . '" ' : '';
        return '<input type="text" name="' . $label . '" value="' . $data . '" placeholder="' . $tip . '" ' . $must . $min . $max . '>';
    }

    public function number($label, $must, $tip, $data, $config) {
        $must = $must ? 'required' : '';
        $config = explode(',', $config, 2);
        $min = 0;
        $max = 0;
        if ($config[0]) {
            $min = intval($config[0]);
        }
        if ($config[1]) {
            $max = intval($config[1]);
        }
        $min = $min ? ' min="' . $min . '" ' : '';
        $max = $max ? ' max="' . $max . '" ' : '';
        return '<input type="number" name="' . $label . '" value="' . $data . '" placeholder="' . $tip . '" ' . $must . $min . $max . '>';
    }

    public function phone($label, $must, $tip, $data, $config) {
        $must = $must ? 'required' : '';
        if (empty($config)) {
            $reg = '(^1[3|4|5|7|8][0-9]{9}$)';
        } else {
            $config = explode(',', $config);
            $configReg = implode('|', $config);
            $reg = '(^[' . $configReg . '][0-9]{8}$)';
        }
        return '<input type="text" name="' . $label . '" value="' . $data . '" placeholder="' . $tip . '" ' . $must . ' pattern="' . $reg . '">';
    }

    public function tel($label, $must, $tip, $data, $config) {
        $must = $must ? 'required' : '';
        if (empty($config)) {
            $reg = '^([0-9]{3,4}-)?[0-9]{7,8}$';
        } else {
            $config = explode(',', $config);
            $configReg = implode('|', $config);
            $reg = '^([' . $configReg . ']-)?[0-9]{7,8}$';
        }
        return '<input type="text" name="' . $label . '" value="' . $data . '" placeholder="' . $tip . '" ' . $must . ' pattern="' . $reg . '">';
    }

    public function email($label, $must, $tip, $data, $config) {
        $must = $must ? 'required' : '';
        $reg = '';
        if (!empty($config)) {
            $config = explode(',', $config);
            $configReg = implode('|', $config);
            $reg = '\w+([-+.]\w+)*@[' . $configReg . ']\.\w+([-.]\w+)*';
        }
        return '<input type="email" name="' . $label . '" value="' . $data . '" placeholder="' . $tip . '" ' . $must . ' pattern="' . $reg . '">';
    }

    public function textarea($label, $must, $tip, $data, $config) {
        $must = $must ? 'required' : '';
        $config = explode(',', $config, 2);
        $min = 0;
        $max = 0;
        if ($config[0]) {
            $min = intval($config[0]);
        }
        if ($config[1]) {
            $max = intval($config[1]);
        }
        $min = $min ? ' minlength="' . $min . '" ' : '';
        $max = $max ? ' maxlength="' . $max . '" ' : '';
        return '<textarea name="' . $label . '" placeholder="' . $tip . '" ' . $must . $min . $max . '>' . $data . '</textarea>';
    }

    public function editor($label, $must, $tip, $data, $config) {
        $toolbar = '';
        if ($config) {
            $toolbar = ' data-toolbar="' . $config . '" ';
        }
        return '<textarea name="' . $label . '" placeholder="' . $tip . '" ' . $toolbar . ' data-mango="form-editor" style="height:250px">' . $data . '</textarea>';
    }

    public function date($label, $must, $tip, $data, $config) {
        $must = $must ? 'required' : '';
        $data = $data ? date('Y-m-d', $data) : '';
        return '<input type="date" name="' . $label . '" value="' . $data . '" placeholder="' . $tip . '" ' . $must . '>';
    }

    public function time($label, $must, $tip, $data, $config) {
        $must = $must ? 'required' : '';
        $data = $data ? $data : '';
        return '<input type="time" name="' . $label . '" value="' . $data . '" placeholder="' . $tip . '" ' . $must . '>';
    }

    public function datetime($label, $must, $tip, $data, $config) {
        $must = $must ? 'required' : '';
        $data = $data ? date('Y-m-d H:i', $data) : '';
        return '<input type="text" data-mango="form-date" data-format="yyyy-mm-dd hh:ii" name="' . $label . '" value="' . $data . '" placeholder="' . $tip . '" ' . $must . '>';
    }

    public function select($label, $must, $tip, $data, $config) {
        $must = $must ? 'required' : '';
        $html = '<select name="' . $label . '"  placeholder="' . $tip . '" data-am-selected ' . $must . '>';
        $list = explode("\n", $config);
        $list = array_filter($list);
        foreach ($list as $vo) {
            $info = explode(',', $vo);
            if ($info[0] == $data) {
                $html .= '<option value="' . $info[0] . '" selected>' . $info[1] . '</option>';
            } else {
                $html .= '<option value="' . $info[0] . '">' . $info[1] . '</option>';
            }
        }
        $html .= '</select>';
        return $html;
    }

    public function radio($label, $must, $tip, $data, $config) {
        $list = explode("\n", $config);
        $list = array_filter($list);
        $html = '<div class="am-g">';
        foreach ($list as $vo) {
            $info = explode(',', $vo);
            $html .= '<label class="am-radio-inline">';
            if ($info[0] == $data) {
                $html .= '<input name="' . $label . '" value="' . $info[0] . '" checked="checked" type="radio"> ';
            } else {
                $html .= '<input name="' . $label . '" value="' . $info[0] . '" type="radio"> ';
            }
            $html .= $info[1] . '</label>';
        }
        if ($tip) {
            $html .= '<div class="am-form-help">' . $tip . '</div>';
        }
        $html .= '</div>';
        return $html;
    }

    public function checkbox($label, $must, $tip, $data, $config) {
        $list = explode("\n", $config);
        $list = array_filter($list);
        $html = '<div class="am-g">';
        $data = $data ? explode(',', $data) : [];
        foreach ($list as $vo) {
            $info = explode(',', $vo);
            $html .= '<label class="am-checkbox-inline">';
            if (in_array($info[0], $data)) {
                $html .= '<input name="' . $label . '[]" value="' . $info[0] . '" checked="checked" type="checkbox"> ';
            } else {
                $html .= '<input name="' . $label . '[]" value="' . $info[0] . '" type="checkbox"> ';
            }
            $html .= $info[1] . '</label>';
        }
        if ($tip) {
            $html .= '<div class="am-form-help">' . $tip . '</div>';
        }
        $html .= '</div>';
        return $html;
    }

    public function image($label, $must, $tip, $data, $config) {
        $must = $must ? 'required' : '';
        $config = explode(',', $config, 2);
        $width = 0;
        $height = 0;
        if ($config[0]) {
            $width = intval($config[0]);
        }
        if ($config[1]) {
            $height = intval($config[1]);
        }
        $resize = $width && $height ? ' data-resize="{width : ' . $width . ', height: ' . $height . ', crop: true}" ' : '';
        $html = '<div class="am-input-group">
                    <input type="text" name="' . $label . '" id="' . $label . '" value="' . $data . '" placeholder="' . $tip . '" ' . $must . '>
                    <span class="am-input-group-btn">
                        <button class="am-btn am-btn-default" type="button" data-mango="form-upload" data-target="#' . $label . '" data-type="jpg,png,bmp" ' . $resize . '><i class="am-icon-upload"></i></button>
                    </span>
                 </div>';
        return $html;
    }

    public function images($label, $must, $tip, $data, $config) {
        $images = unserialize($data);
        $config = explode(',', $config);
        $width = 0;
        $height = 0;
        if ($config[0]) {
            $width = intval($config[0]);
        }
        if ($config[1]) {
            $height = intval($config[1]);
        }
        $images = json_encode($images);
        $resize = $width && $height ? ' data-resize="{width : ' . $width . ', height: ' . $height . ', crop: true}" ' : '';
        $html = '<button class="am-btn am-btn-default" type="button" data-mango="form-images" data-img-list=\'' . $images . '\' data-img-name="' . $label . '" data-img-warp="#dux-images-' . $label . '" data-type="jpg,png,bmp" ' . $resize . '><i class="am-icon-upload"></i> 上传组图</button>';
        $html .= '<div class="dux-images am-cf" id="dux-images-' . $label . '"></div>';
        if ($tip) {
            $html .= '<div class="am-form-help">' . $tip . '</div>';
        }
        return $html;
    }

    public function file($label, $must, $tip, $data, $config) {
        $must = $must ? 'required' : '';
        $config = $config ? ' data-type="' . $config . '" ' : '';
        $html = '<div class="am-input-group">
                    <input type="text" name="' . $label . '" id="' . $label . '" value="' . $data . '" placeholder="' . $tip . '" ' . $must . '>
                    <span class="am-input-group-btn">
                        <button class="am-btn am-btn-default" type="button" data-mango="form-upload" data-target="#' . $label . '" ' . $config . '><i class="am-icon-upload"></i></button>
                    </span>
                 </div>';
        return $html;
    }

    public function files($label, $must, $tip, $data, $config) {
        $files = unserialize($data);
        $files = json_encode($files);
        if(empty($config)) {
            $config = '*';
        }
        $html = '<button class="am-btn am-btn-default" type="button" data-mango="form-files" data-file-list=\'' . $files . '\' data-file-name="' . $label . '" data-file-warp="#dux-files-' . $label . '" data-type="'.$config.'"><i class="am-icon-upload"></i> 上传文件</button>';
        $html .= '<div class="dux-files" id="dux-files-' . $label . '"></div>';
        if ($tip) {
            $html .= '<div class="am-form-help">' . $tip . '</div>';
        }
        return $html;
    }

    public function price($label, $must, $tip, $data, $config) {
        if($data) {
            $data = number_format($data, 2);
        }
        $must = $must ? 'required' : '';
        $reg = '\d{1,10}(\.\d{1,2})?$';
        return '<input type="text" name="' . $label . '" value="' . $data . '" placeholder="' . $tip . '" ' . $must . ' pattern="' . $reg . '">';
    }

    public function color($label, $must, $tip, $data, $config) {
        $must = $must ? 'required' : '';
        return '<input type="color" name="' . $label . '" value="' . $data . '" placeholder="' . $tip . '" ' . $must . '>';
    }

    public function area($label, $must, $tip, $data, $config) {
        $must = $must ? 'required' : '';
        $area = explode(',', $data);
        $html = '<div class="dux-g" data-mango="form-location" ' . $must . '>
                    <div class="am-u-md-4">
                        <select name="' . $label . '[]" data-province="' . $area[0] . '"></select>
                    </div>
                    <div class="am-u-md-4">
                        <select name="' . $label . '[]" data-city="' . $area[1] . '"></select>
                    </div>
                    <div class="am-u-md-4">
                        <select name="' . $label . '[]" data-region="' . $area[2] . '"></select>
                    </div>
                 </div>';
        if ($tip) {
            $html .= '<div class="am-form-help">' . $tip . '</div>';
        }
        return $html;
    }

    public function baidumap($label, $must, $tip, $data, $config) {
        $must = $must ? 'required' : '';
        $html = '<div class="am-input-group">
                    <input type="text" name="' . $label . '" id="' . $label . '" value="' . $data . '" placeholder="' . $tip . '" ' . $must . '>
                    <span class="am-input-group-btn">
                        <button class="am-btn am-btn-default" type="button" data-mango="form-map" data-id="' . $label . '"><i class="am-icon-upload"></i></button>
                    </span>
                 </div>';
        return $html;
    }

    public function layer($name, $html) {
        return '<div class="am-form-group">
                        <label class="am-u-md-2 am-u-sm-12 am-form-label">' . $name . '</label>
                        <div class="am-u-md-10 am-u-sm-12">
                            ' . $html . '
                        </div>
                    </div>';

    }

    public function layerWrap($name, $html) {
        return '<div class="am-form-group">
                            <label class="am-form-label">' . $name . '</label>
                            <div>
                                ' . $html . '
                            </div>
                        </div>
        ';

    }
}