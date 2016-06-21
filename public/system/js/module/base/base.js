/**
 * 页面框架
 */
(function ($, owner) {
    owner.frame = function () {
        //处理绑定组件
        $("[data-mango]").each(function () {
            var data = $(this).data(), name = data['mango'], names = name.split('-', 2);
            window[names[0]][names[1]](this, data);
        });

    };
    owner.menu = function ($el, config) {
        $($el).click(function () {
            $('body').toggleClass('dux-mobile-menu');
        });
    };
}(jQuery, window.base = {}));


/**
 * 表单操作
 */
(function ($, owner) {
    /**
     * 绑定AJAX提交
     */
    owner.bind = function ($el, config) {
        Do('form', 'dialog', function () {
            var options = {
                dataType: 'json',
                beforeSubmit: function () {
                    $($el).find("button[type=submit]").prepend('<i class="am-icon-circle-o-notch am-icon-spin"></i> ');
                    $($el).find("button").attr("disabled", true);
                },
                uploadProgress: function (event, position, total, percentComplete) {
                },
                complete: function () {
                    $($el).find("button").attr("disabled", false);
                    $($el).find("button[type=submit]").find('i:first-child').remove();
                },
                success: function (json) {
                    if (json.status) {
                        //成功回调
                        if (typeof config.callback === 'function') {
                            config.callback(json.info, json.url);
                            return;
                        }
                        if (typeof config.callback === 'string') {
                            window[config.callback](json.info, json.url);
                            return;
                        }
                        //执行弹窗
                        if (json.url) {
                            layer.confirm(json.info, {
                                icon: 1,
                                btn: ['返回', '继续']
                            }, function () {
                                window.location.href = json.url;
                            }, function () {
                                location.reload();
                            });
                        } else {
                            notify.success({
                                content: json.info
                            });
                        }
                    } else {
                        notify.error({
                            content: json.info
                        });
                    }
                },
                error: function (data) {
                    notify.error({
                        content: '提交失败,失败状态:' + data.status
                    });
                }
            };
            $($el).validator({
                H5validation: false,
                validateOnSubmit: true,
                onValid: function (validity) {
                    $(validity.field).closest('.am-form-group').find('.am-alert').hide();
                },
                markValid: function (validity) {
                    var options = this.options;
                    var $field = $(validity.field);
                    var $parent = $field.closest('.am-form-group');
                    $field.removeClass(options.inValidClass);
                    $parent.removeClass('am-form-error');
                    options.onValid.call(this, validity);
                },
                onInValid: function (validity) {
                    var $field = $(validity.field);
                    var $group = $field.parent();
                    if ($group.hasClass('am-input-group')) {
                        $group = $field.parent().parent();
                    }
                    var $alert = $group.find('.am-alert');
                    var msg = $field.data('validationMessage') || this.getValidationMessage(validity);
                    if (!$alert.length) {
                        $alert = $('<div class="am-alert am-alert-danger"></div>').hide().appendTo($group);
                    }
                    $alert.html(msg).show();
                },
                submit: function () {
                    var formValidity = this.isFormValid();
                    if (formValidity) {
                        $($el).ajaxSubmit(options);
                    }
                    return false;
                }
            });
            $($el).find("button").attr("disabled", false);
        });
    };

    /**
     * 时间日期
     * @param $el
     * @param config
     */
    owner.date = function ($el, config) {
        var defaultConfig = {
            language: 'zh-CN',
            format: 'yyyy-mm-dd',
            autoclose: true
        };
        Do('date', function () {
            config = $.extend(defaultConfig, config);
            $($el).datetimepicker(config);

        });
    };

    /**
     * 地区选择
     * @param $el
     * @param config
     */
    owner.location = function ($el, config) {
        var defaultConfig = {};
        config = $.extend(defaultConfig, config);
        Do('distpicker', function () {
            $($el).distpicker(config);
        });
    };

    /**
     * 编辑器
     * @param $el
     * @param config
     */
    owner.editor = function ($el, config) {
        var toolbar = [
            'source',
            '|',
            'bold',
            'underline',
            'italic',
            'strikethrough',
            'eraser',
            'forecolor',
            'bgcolor',
            '|',
            'quote',
            'fontfamily',
            'fontsize',
            'head',
            'unorderlist',
            'orderlist',
            'alignleft',
            'aligncenter',
            'alignright',
            '|',
            'link',
            'unlink',
            'table',
            'emotion',
            '|',
            'img',
            'files',
            'indent',
            'video',
            'location',
            'insertcode',
            '|',
            'undo',
            'redo',
            'fullscreen'
        ];
        var defaultConfig = {
            textarea: $($el),
            toolbar : toolbar,
            uploadUrl: rootUrl + '/' + roleName +'/system/Upload/index'
        };
        config = $.extend(defaultConfig, config, $(this).data());
        Do('editor', function () {
            var editor = new wangEditor(config.textarea);
            editor.config.menus = config.toolbar;
            editor.config.height= 500;
            //editor.config.uploadImgUrl = rootUrl + '/' + roleName +'/system/Upload/editor';
            editor.config.uploadUrl = config.uploadUrl;
            editor.config.emotions = {
                'default': {
                    title: '默认',
                    data: rootUrl + '/' + roleName +'/system/Editor/emotions'
                }
            };
            editor.config.customUpload = true;
            editor.config.customUploadInit = editorUploadInit;
            editor.create();
            var name = $($el).attr("name") + 'Editor';
            window[name] = editor;
            if(config.load === false) {
                editor.destroy();
            }
        });
    };

    /**
     * 显示切换组件
     * @param $el
     * @param config
     */
    owner.change = function($el, config) {
        var defaultConfig = {};
        config = $.extend(defaultConfig, config);
        $($el).on('change', 'input:radio', function() {
            $('.' + config.class).hide();
            $('.' + config.class + '-' + $(this).val()).show();
        });
    };

    /**
     * tag输入组件
     */
    owner.tags = function($el, config) {
        var defaultConfig = {};
        config = $.extend(defaultConfig, config);
        Do('tags', function () {
            $($el).tagsinput();
        });
    };

    /**
     * 下拉输入
     */
    owner.chosen = function($el, config) {
        var defaultConfig = {};
        config = $.extend(defaultConfig, config);
        Do('chosen', function () {
            $($el).chosen(config);
        });
    };

    /**
     * 地图组件
     * @param $el
     * @param config
     */
    owner.map = function ($el, config) {
        var defaultConfig = {};
        config = $.extend(defaultConfig, config);
        Do('dialog', function () {
            var id = $($el).data('id');
            $($el).on('click', function() {
                layer.open({
                    title : '地图选择',
                    type: 2,
                    id : 'dialog-' + id,
                    area: ['500px', '400px'],
                    fix: false,
                    btn: ['确定', '取消'],
                    content: rootUrl + '/' + roleName +'/site/FormField/map?id='+id,
                    yes: function(index, layero) {
                        var iframe = $(layero).find("iframe");
                        iframe[0].contentWindow.getMap(id);
                        layer.close(index);
                    }
                });
            });

        });
    };

    /**
     * 上传
     * @param $el
     * @param config
     */
    owner.upload = function ($el, config) {
        var defaultConfig = {
            url: rootUrl + '/' + roleName +'/system/Upload/index',
            type: '*',
            size: 0,
            multi: true,
            resize: {},
            target: '',
            callback: ''
        };
        config = $.extend(defaultConfig, config);
        Do('upload', function () {
            var uploader = new plupload.Uploader({
                runtimes: 'html5,html4',
                browse_button: $($el).get(0),
                url: config.url,
                filters: {
                    mime_types: [
                        {title: "指定文件", extensions: config.type}
                    ]
                },
                max_file_size: config.size,
                multipart: config.multi,
                resize: config.resize,
                init: {
                    PostInit: function () {
                        //初始化
                    },
                    FilesAdded: function (up, files) {
                        //添加文件
                        $($el).attr('disabled', true).append(' <span class="prs">[<strong>0%</strong>]</span>');
                        uploader.start();
                    },
                    UploadProgress: function (up, file) {
                        //上传进度
                        $($el).find('span').text(file.percent + '%');
                    },
                    FileUploaded: function (up, file, response) {
                        //文件上传完毕
                        var data = JSON.parse(response.response);
                        if (!data.status) {
                            notify.error({
                                content : data.info
                            });
                            return;
                        }
                        //赋值地址
                        if (config.target) {
                            $(config.target).val(data.data.url);
                        }
                        //设置回调
                        if (typeof config.callback === 'function') {
                            config.callback(data.data);
                        }
                        if (typeof config.callback === 'string' && config.callback) {
                            window[config.callback](data.data);
                        }
                    },
                    Error: function (up, err) {
                        //错误信息
                        $($el).attr('disabled', false).find('span').remove();
                        notify.error({
                            content : err.message
                        });
                    },
                    UploadComplete: function (up, num) {
                        //队列上传完毕
                        $($el).attr('disabled', false).find('span').remove();
                    }
                }
            });
            uploader.init();
        });
    };
    /**
     * 组图
     * @param $el
     * @param config
     */
    owner.images = function ($el, config) {
        var defaultConfig = {
            imgWarp: '',
            imgName: '',
            imgList: {}
        };
        config = $.extend(defaultConfig, config);
        Do('sortable', 'tpl', function () {
            var tpl = '<li>' +
                '<img src="{{ d.data.url }}">' +
                '<div class="info">' +
                '<span class="title">{{ d.data.title }}</span>' +
                '<a class="del">删除</a>' +
                '</div>' +
                '<input type="hidden" name="{{ d.name }}[url][]" value="{{ d.data.url }}">' +
                '<input type="hidden" name="{{ d.name }}[title][]" value="{{ d.data.title }}">' +
                '</li>';
            $(config.imgWarp).on('click', '.del', function () {
                $(this).parents('li').remove();
            });
            owner.upload($el, $.extend(config, {
                callback: function (data) {
                    laytpl(tpl).render({name: config.imgName, data: data}, function (html) {
                        $(config.imgWarp).append(html);
                    });
                    $(config.imgWarp).sortable();
                }
            }));
            if (config.imgList) {
                $.each(config.imgList, function (index, item) {
                    laytpl(tpl).render({name: config.imgName, data: item}, function (html) {
                        $(config.imgWarp).append(html);
                    });
                });
                $(config.imgWarp).sortable();
            }

        });
    };

    /**
     * 多文件
     * @param $el
     * @param config
     */
    owner.files = function ($el, config) {
        var defaultConfig = {
            fileWarp: '',
            fileName: '',
            fileList: {}
        };
        config = $.extend(defaultConfig, config);
        Do('sortable', 'tpl', function () {
            var tpl = '<li>' +
                '<span class="title"><input type="text" name="{{ d.name }}[title][]" value="{{ d.data.title }}">.{{ d.data.ext }} ({{ (d.data.size/1024).toFixed(2) }}kb)</span> ' +
                '<a class="del">删除</a>' +
                '<input type="hidden" name="{{ d.name }}[url][]" value="{{ d.data.url }}">' +
                '<input type="hidden" name="{{ d.name }}[ext][]" value="{{ d.data.ext }}">' +
                '<input type="hidden" name="{{ d.name }}[size][]" value="{{ d.data.size }}">' +
                '</li>';
            $(config.fileWarp).on('click', '.del', function () {
                $(this).parents('li').remove();
            });
            owner.upload($el, $.extend(config, {
                callback: function (data) {
                    laytpl(tpl).render({name: config.fileName, data: data}, function (html) {
                        $(config.fileWarp).append(html);
                    });
                    $(config.fileWarp).sortable();
                }
            }));
            if (config.fileList) {
                $.each(config.fileList, function (index, item) {
                    laytpl(tpl).render({name: config.fileName, data: item}, function (html) {
                        $(config.fileWarp).append(html);
                    });
                });
                $(config.fileWarp).sortable();
            }

        });
    };


}(jQuery, window.form = {}));


/**
 * 通知组件
 */
(function ($, owner) {
    owner.success = function (config) {
        var defaultConfig = {
            content: "处理成功",
            time: 6
        };
        config = $.extend(defaultConfig, config, {status: 'success'});
        owner.show(config);
    };
    owner.warning = function (config) {
        var defaultConfig = {
            content: "处理中断",
            time: 6
        };
        config = $.extend(defaultConfig, config, {status: 'warning'});
        owner.show(config);
    };
    owner.error = function (config) {
        var defaultConfig = {
            content: "处理失败",
            time: 6
        };
        config = $.extend(defaultConfig, config, {status: 'error'});
        owner.show(config);
    };
    owner.show = function (config) {
        Do('notify', function () {
            var status = {
                success: ['ok', '#27ae60'],
                warning: ['warning', '#e0690c'],
                error: ['error', '#dd514c']
            };
            console.log(status[config.status][1]);
            $.amaran({
                theme: 'default ' + status[config.status][0],
                delay: config.time * 1000,
                content: {
                    message: config.content,
                    color: status[config.status][1]
                }
            });
        });
    }
}(jQuery, window.notify = {}));


/**
 * 表格组件
 */
(function ($, owner) {
    owner.bind = function ($el, config) {
        Do('dialog', function () {
        var defaultConfig = {}, config = $.extend(defaultConfig, config);
        var $table = $($el).find('[data-table]'), $del = $table.find('[data-del]');
        //更改状态
        $table.on('click', '[data-status]', function () {
            var data = $(this).data(), $obj = this;
            if (data.status == 1) {
                var status = 0;
                var css = 'am-text-danger';
            } else {
                var status = 1;
                var css = 'am-text-success';
            }
            app.ajax({
                type: 'post',
                url: data.url,
                data: {
                    id: data.id,
                    name: data.name,
                    status: status
                },
                success: function (info) {
                    notify.success({
                        content : info
                    });
                    $($obj).removeClass('am-text-success am-text-danger').addClass(css).data('status', status);
                }
            });

        });
        //全选
        $table.find('[data-all]').click(function () {
            if (!$(this).is(':checked')) {
                $table.find('input[type=checkbox]').prop("checked", false);
            } else {
                $table.find('input[type=checkbox]').prop("checked", true);
            }
        });
        //删除
        $del.click(function () {
            var data = $(this).data(), $tr = $(this).parents('tr');
            layer.confirm('是否确认删除?', {
                icon: 3,
                btn: ['确认', '取消']
            }, function (index) {
                app.ajax({
                    type: 'post',
                    url: data.url,
                    data: {id: data.id},
                    success: function (info) {
                        notify.success({
                            content : info
                        });
                        $tr.remove();
                        layer.close(index);
                    },
                    error: function () {
                        layer.close(index);
                    }
                });
            });
        });
        //批量操作
        var $batch = $($el).find('[data-batch]');
        $batch.submit(function () {
            event.stopPropagation();
            var data = {}, ids = [];
            $.each($batch.serializeArray(), function (index, vo) {
                data[vo.name] = vo.value;
            });
            $table.find('input[type=checkbox]:checked').each(function () {
                ids.push($(this).val());
            });
            data['ids'] = ids.join(',');
            app.ajax({
                url: $batch.attr('action'),
                data: data,
                type : 'post',
                success: function (info) {
                    layer.alert(info, function() {
                        location.reload();
                    });
                },
                error: function (info) {
                    layer.alert(info, function () {
                        location.reload();
                    });
                }
            });
            return false;
        });
        //分页跳转
        var $pages = $($el).find('[data-pages]');
        $pages.submit(function (event) {
            event.stopPropagation();
            var page = $pages.find('input[name="page"]').val();
            var href = location.href;
            if (/page=\d+/.test(href)) {
                href = href.replace(/page=\d+/, "page=" + page);
            } else if (href.indexOf('?') == -1) {
                href = href + "?page=" + page;
            } else {
                href = href + "&page=" + page;
            }
            window.location.href = href;
            return false;
        });

        });
    };
}(jQuery, window.table = {}));

/**
 * 常用方法
 */
(function ($, owner) {
    /**
     * 调试方法
     * @param msg
     */
    owner.debug = function (msg) {
        if (typeof(console) != 'undefined') {
            console.log(msg);
        }
    };
    /**
     * AJAX请求
     * @param config
     */
    owner.ajax = function (config) {
        $.ajax({
            url: config.url,
            type: config.type,
            data: config.data,
            dataType: 'json',
            success: function (json) {
                if (json.status) {
                    if (typeof config.success == 'function') {
                        config.success(json.info);
                    }
                } else {
                    notify.error({
                        content : json.info
                    });
                    if (typeof config.error == 'function') {
                        config.error(json.info);
                    }
                }
            },
            error: function () {
                notify.error({
                    content : '数据请求失败，请刷新后再试！'
                });
            }
        });
    };

    /**
     * 错误提示
     * @param msg
     * @returns {boolean}
     */
    owner.error = function (msg) {
        alert(msg);
        return false;
    };

    /**
     * 移动端检测
     * @returns {boolean}
     */
    owner.mobile = function() {
        var check = false;
        (function(a){if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test(a)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0,4)))check = true})(navigator.userAgent||navigator.vendor||window.opera);
        return check; }

}(jQuery, window.app = {}));