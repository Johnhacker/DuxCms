(function () {
    // 获取 wangEditor 构造函数和 jquery
    var E = window.wangEditor;
    var $ = window.jQuery;

    E.createMenu(function (check) {
        var menuId = 'files';
        if (!check(menuId)) {
            return;
        }
        var editor = this;

        // 创建 menu 对象
        var menu = new E.Menu({
            editor: editor,
            id: menuId,
            title: '附件',
            $domNormal: $('<a href="#" tabindex="-1"><i class="wangeditor-menu-img-upload"></i></a>'),
            $domSelected: $('<a href="#" tabindex="-1" class="selected"><i class="wangeditor-menu-img-upload"></i></a>')
        });

        // 创建 panel content
        var $panelContent = $('<div class="panel-tab"></div>');
        var $tabContainer = $('<div class="tab-container"></div>');
        var $contentContainer = $('<div class="content-container"></div>');
        $panelContent.append($tabContainer).append($contentContainer);

        // tab
        var $uploadTab = $('<a href="#">上传附件</a>');
        var $linkTab = $('<a href="#">网络附件</a>');
        $tabContainer.append($uploadTab).append($linkTab);

        // 上传图片 content
        var $uploadFile = $('<div class="content"></div>');
        $contentContainer.append($uploadFile);

        // 网络图片 content
        var $linkContent = $('<div class="content"></div>');
        $contentContainer.append($linkContent);
        linkContentHandler(editor, menu, $linkContent);

        // 添加panel
        menu.dropPanel = new E.DropPanel(editor, menu, {
            $content: $panelContent,
            width: 400,
            onRender: function () {
                var init = uploadInit;
                init && init.call(editor);
            }
        });

        // 增加到editor对象中
        editor.menus[menuId] = menu;

        function uploadInit() {
            var editor = this;
            var btnId = 'upload' + E.random();

            var $uploadFile = editor.$uploadFile;
            var uploadUrl = editor.config.uploadUrl;

            var $uploadIcon = $('<div class="upload-icon-container" id="' + btnId + '"><i class="wangeditor-menu-img-upload"></i></div>');
            $uploadFile.append($uploadIcon);

            Do('upload', function () {
                var uploader = new plupload.Uploader({
                    runtimes: 'html5,html4',
                    browse_button: btnId,
                    url: uploadUrl,
                    multipart: true,
                    resize: {},
                    init: {
                        PostInit: function () {
                            //初始化
                        },
                        FilesAdded: function (up, files) {
                            //添加文件
                            uploader.start();
                        },
                        UploadProgress: function (up, file) {
                            //上传进度
                            editor.showUploadProgress(file.percent);
                        },
                        FileUploaded: function (up, file, response) {
                            //文件上传完毕
                            var data = JSON.parse(response.response);
                            if (!data.status) {
                                alert(data.info);
                                return;
                            }
                            editor.command(null, 'insertHtml', '<a href="' + data.data.url + '" target="_blank">' + data.data.title + '.'+data.data.ext+' ('+(data.data.size/1024).toFixed(2)+'kb)</a><br>');
                        },
                        Error: function (up, err) {
                            //错误信息
                            alert(err.message);
                        },
                        UploadComplete: function (up, num) {
                            //队列上传完毕
                            editor.hideUploadProgress();
                        }
                    }
                });
                uploader.init();
            });
        }

        // tab 切换事件
        function tabToggle() {
            $uploadTab.click(function (e) {
                $tabContainer.children().removeClass('selected');
                $contentContainer.children().removeClass('selected');
                $uploadFile.addClass('selected');
                $uploadTab.addClass('selected');
                e.preventDefault();
            });
            $linkTab.click(function (e) {
                $tabContainer.children().removeClass('selected');
                $contentContainer.children().removeClass('selected');
                $linkContent.addClass('selected');
                $linkTab.addClass('selected');
                e.preventDefault();

                // focus input
                if (E.placeholder) {
                    $linkContent.find('input[type=text]').focus();
                }
            });

            $uploadTab.click();
        }

        // 隐藏上传图片
        function hideUpload() {
            $tabContainer.remove();
            $uploadFile.remove();
            $linkContent.addClass('selected');
        }

        // 判断用户是否配置了上传图片
        editor.ready(function () {
            var editor = this;
            var config = editor.config;
            var uploadUrl = config.uploadUrl;
            var customUpload = config.customUpload;
            var $uploadImgPanel;

            if (uploadUrl || customUpload) {
                // 第一，暴露出 $uploadFile 以便用户自定义 ！！！重要
                editor.$uploadFile = $uploadFile;

                // 第二，绑定tab切换事件
                tabToggle();
            } else {
                // 未配置上传图片功能
                hideUpload();
            }

            // 点击 $uploadFile 立即隐藏 dropPanel
            // 为了兼容IE8、9的上传，因为IE8、9使用 modal 上传
            // 这里使用异步，为了不妨碍高级浏览器通过点击 $uploadFile 选择文件
            function hidePanel() {
                menu.dropPanel.hide();
            }

            $uploadFile.click(function () {
                setTimeout(hidePanel);
            });
        });

        function linkContentHandler(editor, menu, $linkContent) {
            var $urlContainer = $('<div style="margin:20px 10px 10px 10px;"></div>');
            var $titleInput = $('<input type="text" class="block" placeholder="文件名称"/>');
            var $urlInput = $('<input type="text" class="block" placeholder="http://"/>');
            $urlContainer.append($titleInput);
            $urlContainer.append($urlInput);
            var $btnSubmit = $('<button class="right">提交</button>');
            var $btnCancel = $('<button class="right gray">取消</button>');

            $linkContent.append($urlContainer).append($btnSubmit).append($btnCancel);

            // 取消
            $btnCancel.click(function (e) {
                e.preventDefault();
                menu.dropPanel.hide();
            });

            // callback
            function callback() {
                $urlInput.val('');
                $titleInput.val('');
            }

            // 确定
            $btnSubmit.click(function (e) {
                e.preventDefault();
                var url = $.trim($urlInput.val());
                var title = $.trim($titleInput.val());
                if (!url) {
                    $urlInput.focus();
                    return;
                }
                if (!title) {
                    $titleInput.focus();
                    return;
                }
                var html = '<a href="' + url + '" target="_blank">' + title + '</a>';
                editor.command(e, 'insertHtml', html, callback);
            });
        }
    });

    window.editorUploadInit = function () {
        var editor = this;
        var btnId = editor.customUploadBtnId;
        Do('upload', function () {
            var uploader = new plupload.Uploader({
                runtimes: 'html5,html4',
                browse_button: btnId,
                url: editor.config.uploadUrl,
                multipart: true,
                resize: {},
                init: {
                    PostInit: function () {
                        //初始化
                    },
                    FilesAdded: function (up, files) {
                        //添加文件
                        uploader.start();
                    },
                    UploadProgress: function (up, file) {
                        //上传进度
                        editor.showUploadProgress(file.percent);
                    },
                    FileUploaded: function (up, file, response) {
                        //文件上传完毕
                        var data = JSON.parse(response.response);
                        if (!data.status) {
                            alert(data.info);
                            return;
                        }
                        editor.command(null, 'insertHtml', '<img src="' + data.data.url  + '" style="max-width:100%;"/>');
                    },
                    Error: function (up, err) {
                        //错误信息
                        alert(err.message);
                    },
                    UploadComplete: function (up, num) {
                        //队列上传完毕
                        editor.hideUploadProgress();
                    }
                }
            });
            uploader.init();
        });
    }

})();