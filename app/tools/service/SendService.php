<?php
namespace app\tools\service;
/**
 * 发送接口
 */
class SendService {

    /**
     * 获取推送结构
     */
    public function getTypeSend() {
        return array(
            'email' => array(
                'name' => '邮件',
                'target' => 'tools/Email',
                'configRule' => array(
                    'smtp_host' => '发信地址',
                    'smtp_port' => '发信端口',
                    'smtp_ssl' => '安全链接',
                    'smtp_username' => '发信用户',
                    'smtp_password' => '发信密码',
                    'smtp_from_to' => '发信邮箱',
                    'smtp_from_name' => '发件人',
                )
            ),
            'yunpian' => array(
                'name' => '云片短信',
                'target' => 'tools/YunPian',
                'configRule' => array(
                    'apikey' => 'API秘钥',
                )
            ),
        );
    }
}

