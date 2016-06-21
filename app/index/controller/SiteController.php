<?php
namespace app\index\controller;

/**
 * 站点控制器
 */

class SiteController extends \app\base\controller\BaseController {

    protected $siteConfig = [];

    protected $pageInfo = [
        'title' => '',
        'keyword' => '',
        'description' => '',
        'crumb' => []
    ];

    public function __construct() {
        parent::__construct();
        target('system/Statistics', 'service')->incStats('web');
        $this->siteConfig = target('site/SiteConfig')->getConfig();
    }

    protected function siteDisplay($tpl = 'index') {
        $this->assign('site', $this->siteConfig);
        $this->assign('pageInfo', $this->pageInfo);

        $detect = new \dux\vendor\MobileDetect();
        if ($detect->isMobile()) {
            $this->siteConfig['tpl_name'] = $this->siteConfig['tpl_name'].'_mobile';
        }else {
            $this->siteConfig['tpl_name'];
        }
        $tpl = 'theme/' . $this->siteConfig['tpl_name'] . '/' . strtolower($tpl);
        $this->_getView()->addTag(function () {
            return [
                '/<!--#include\s*file=[\"|\'](.*)\.(html|htm)[\"|\']-->/' => "<?php \$__Template->render(\"" . 'theme/' . $this->siteConfig['tpl_name'] . "/$1\"); ?>",
                '/<(.*?)(src=|href=|value=|background=)[\"|\'](images\/|img\/|css\/|js\/|style\/)(.*?)[\"|\'](.*?)>/' => [$this, 'parseLoad'],
                '/__TPL__/' => ROOT_URL . '/theme/' . $this->siteConfig['tpl_name']
            ];
        });
        $this->display($tpl);
        exit;
    }

    protected function setMeta($title = '', $keyword = '', $description = '') {
        $title = $title ? $title . ' - ' : '';
        $this->pageInfo['title'] = $title . $this->siteConfig['info_title'];
        $this->pageInfo['keyword'] = $keyword ? $keyword : $this->siteConfig['info_keyword'];
        $this->pageInfo['description'] = $description ? $description : $this->siteConfig['info_desc'];
    }

    protected function setCrumb($data) {
        $this->pageInfo['crumb'] = $data;
    }

    protected function pageData($sumLimit, $pageLimit, $params = []) {
        $pageObj = new \dux\lib\Pagination($sumLimit, request('get', 'page', 1), $pageLimit);
        $pageData = $pageObj->build();
        $limit = [$pageData['offset'], $pageLimit];
        $pageData['prevUrl'] = $this->createPageUrl($pageData['prev'], $params);
        $pageData['nextUrl'] = $this->createPageUrl($pageData['next'], $params);
        $html = '<div class="dux-pages"><a href="{prevUrl}"> <  Prev</a>';
        foreach ($pageData['pageList'] as $vo) {
            if ($vo == $pageData['current']) {
                $html .= '<span class="current">' . $vo . '</span>';
            } else {
                $html .= '<a href="' . $this->createPageUrl($vo, $params) . '">' . $vo . '</a>';
            }
        }
        $html .= '<a href="{nextUrl}">Next  > </a></div>';
        foreach ($pageData as $key => $vo) {
            $html = str_replace('{' . $key . '}', $vo, $html);
        }
        return [
            'html' => $html,
            'limit' => $limit,
        ];
    }

    protected function createPageUrl($page = 1, $params = []) {
        return $url = url(APP_NAME . '/' . MODULE_NAME . '/' . ACTION_NAME, array_merge($params, ['page' => $page]));
    }

    public function parseLoad($var) {
        $file = $var[3] . $var[4];
        $url = 'theme' . '/' . $this->siteConfig['tpl_name'];
        if (substr($url, 0, 1) == '.') {
            $url = substr($url, 1);
        }
        $url = str_replace('\\', '/', $url);
        $url = ROOT_URL . '/' . $url . '/' . $file;
        $html = '<' . $var[1] . $var[2] . '"' . $url . '"' . $var[5] . '>';
        return $html;
    }

}