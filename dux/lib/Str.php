<?php
namespace dux\lib;

/**
 * 字符串处理类
 *
 * @author Mr.L <349865361@qq.com>
 */
class Str {

    /**
     * 字符串截取
     * @param $str
     * @param $length
     * @param bool $suffix
     * @param string $charset
     * @return string
     */
    public static function strLen($str, $length, $suffix = true, $charset = "utf-8") {
        if ($charset != 'utf-8') {
            $str = mb_convert_encoding($str, 'utf8', $charset);
        }
        $osLen = mb_strlen($str);
        if ($osLen <= $length) {
            return $str;
        }
        $string = mb_substr($str, 0, $length, 'utf8');
        $sLen = mb_strlen($string, 'utf8');
        $bLen = strlen($string);
        $sCharCount = (3 * $sLen - $bLen) / 2;
        if ($osLen <= $sCharCount + $length) {
            $arr = preg_split('/(?<!^)(?!$)/u', mb_substr($str, $length + 1, $osLen, 'utf8')); //将中英混合字符串分割成数组（UTF8下有效）
        } else {
            $arr = preg_split('/(?<!^)(?!$)/u', mb_substr($str, $length + 1, $sCharCount, 'utf8'));
        }
        foreach ($arr as $value) {
            if (ord($value) < 128 && ord($value) > 0) {
                $sCharCount = $sCharCount - 1;
            } else {
                $sCharCount = $sCharCount - 2;
            }
            if ($sCharCount <= 0) {
                break;
            }
            $string .= $value;
        }
        if ($suffix) return $string . '…';
        return $string;
    }


    /**
     * 字符串转码
     * @param  string $str 字符串
     * @param  string $from 原始编码
     * @param  string $to 目标编码
     * @return string
     */
    public static function strCharset($str, $from = 'gbk', $to = 'utf-8') {
        $from = strtoupper($from) == 'UTF8' ? 'utf-8' : $from;
        $to = strtoupper($to) == 'UTF8' ? 'utf-8' : $to;
        if (strtoupper($from) === strtoupper($to) || empty($str) || (is_scalar($str) && !is_string($str))) {
            return $str;
        }
        if (is_string($str)) {
            if (function_exists('mb_convert_encoding')) {
                return mb_convert_encoding($str, $to, $from);
            } elseif (function_exists('iconv')) {
                return iconv($from, $to, $str);
            } else {
                return $str;
            }
        } elseif (is_array($str)) {
            foreach ($str as $key => $val) {
                $_key = self::strCharset($key, $from, $to);
                $str[$_key] = self::strCharset($val, $from, $to);
                if ($key != $_key)
                    unset($str[$key]);
            }
            return $str;
        } else {
            return $str;
        }
    }

    /**
     * 截取摘要
     * @param $data
     * @param int $cut
     * @param string $str
     * @return mixed|string
     */
    public static function strMake($data, $cut = 0, $str = "...") {
        $data = self::htmlOut($data);
        $data = strip_tags($data);
        $pattern = "/&[a-zA-Z]+;/";
        $data = preg_replace($pattern, '', $data);
        if (!is_numeric($cut)) {
            return $data;
        }
        if ($cut > 0) {
            $data = mb_strimwidth($data, 0, $cut, $str);
        }
        return $data;
    }

    /**
     * 判断UTF-8
     * @param  string $string 字符串
     * @return boolean
     */
    public static function isUtf8($string) {
        if (!empty($string)) {
            $ret = json_encode(array('code' => $string));
            if ($ret == '{"code":null}') {
                return false;
            }
        }
        return true;
    }

    /**
     * 转义html
     * @param $str
     * @return string
     */
    public static function htmlIn($str) {
        if (function_exists('htmlspecialchars')) {
            $str = htmlspecialchars($str);
        }else{
            $str = htmlentities($str);
        }
        $str = addslashes($str);
        return $str;
    }

    /**
     * html代码还原
     * @param $str
     * @return string
     */
    public static function htmlOut($str) {
        if (function_exists('htmlspecialchars_decode')) {
            $str = htmlspecialchars_decode($str);
        } else {
            $str = html_entity_decode($str);
        }
        $str = stripslashes($str);
        return $str;
    }

    /**
     * html代码清理
     * @param $str
     * @return string
     */
    public static function htmlClear($str) {
        $str = self::htmlOut($str);
        $xss = new \dux\vendor\HtmlCleaner();
        return $xss->remove($str);
    }

}