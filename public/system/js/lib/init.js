/**
 * 初始化类库
 */
(function (win, doc) {
    /**
     * 设置包路径
     */
    var jsSelf = (function () {
        var files = doc.getElementsByTagName('script');
        return files[files.length - 1];
    })();
    window.packagePath = jsSelf.getAttribute('data-path');
    window.rootUrl = jsSelf.getAttribute('data-root');
    window.roleName = jsSelf.getAttribute('data-role');

    /**
     * 核心模块
     */
    Do.add('base', {
        path: packagePath + 'module/base/base.js',
        type: 'js'
    });

    /**
     * 通知
     */
    Do.add('notifyCss', {
        path: packagePath + 'module/notify/amaran.min.css',
        type: 'css'
    });
    Do.add('notify', {
        path: packagePath + 'module/notify/jquery.amaran.min.js',
        requires: ['notifyCss']
    });

    /**
     * 图表
     */
    Do.add('chartCss', {
        path: packagePath + 'module/chart/chartist.min.css',
        type: 'css'
    });
    Do.add('chart', {
        path: packagePath + 'module/chart/chartist.min.js',
        type: 'js',
        requires: ['chartCss']
    });

    /**
     * 表单
     */
    Do.add('form', {
        path: packagePath + 'module/form/jquery.form.js',
        type: 'js'
    });

    /**
     * 上传
     */
    Do.add('uploadSrc', {
        path: packagePath + 'module/upload/plupload.full.min.js'
    });
    Do.add('upload', {
        path: packagePath + 'module/upload/zh_CN.js',
        requires: ['uploadSrc']
    });

    /**
     * 模板引擎
     */
    Do.add('tpl', {
        path: packagePath + 'module/tpl/laytpl.js',
        type: 'js'
    });

    /**
     * 拖动排序
     */
    Do.add('sortable', {
        path: packagePath + 'module/sortable/jquery.sortable.min.js',
        type: 'js'
    });

    /**
     * 取色器
     */
    Do.add('colorCss', {
        path: packagePath + 'module/color/iColor-min.css',
        type: 'css'
    });
    Do.add('color', {
        path: packagePath + 'module/color/iColor-min.js',
        requires: ['colorCss']
    });

    /**
     * 弹窗插件
     */
    Do.add('dialog', {
        path: packagePath + 'module/dialog/layer.js'
    });

    /**
     * 编辑器
     */
    Do.add('editorCss', {
        path: packagePath + 'module/editor/css/wangEditor.min.css',
        type: 'css'
    });
    Do.add('editorSrc', {
        path: packagePath + 'module/editor/js/wangEditor.min.js'
    });
    Do.add('editor', {
        path: packagePath + 'module/editor/js/plugin.js',
        requires: ['editorCss', 'editorSrc']
    });

    /**
     * 日期选择
     */
    Do.add('dateCss', {
        path: packagePath + 'module/date/css/amazeui.datetimepicker.css',
        type: 'css'
    });
    Do.add('date', {
        path: packagePath + 'module/date/js/amazeui.datetimepicker.min.js',
        requires: ['dateCss']
    });

    /**
     * 地区选择
     */
    Do.add('distpickerSrc', {
        path: packagePath + 'module/distpicker/distpicker.data.min.js'
    });
    Do.add('distpicker', {
        path: packagePath + 'module/distpicker/distpicker.min.js',
        requires: ['distpickerSrc']
    });

    /**
     * 拖动列表
     */
    Do.add('nestable', {
        path: packagePath + 'module/nestable/jquery.nestable.js'
    });

    /**
     * TAG输入
     */
    Do.add('tagsCss', {
        path: packagePath + 'module/tags/amazeui.tagsinput.css',
        type: 'css'
    });
    Do.add('tags', {
        path: packagePath + 'module/tags/amazeui.tagsinput.min.js',
        requires: ['tagsCss']
    });

    /**
     * 下拉增强
     */
    Do.add('chosenCss', {
        path: packagePath + 'module/chosen/amazeui.chosen.css',
        type: 'css'
    });
    Do.add('chosen', {
        path: packagePath + 'module/chosen/amazeui.chosen.min.js',
        requires: ['chosenCss']
    });

})(window, document);