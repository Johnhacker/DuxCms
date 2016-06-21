# ************************************************************
# Sequel Pro SQL dump
# Version 4541
#
# http://www.sequelpro.com/
# https://github.com/sequelpro/sequelpro
#
# Host: localhost (MySQL 5.5.42)
# Database: duxcms3
# Generation Time: 2016-06-11 06:28:36 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table dux_article
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dux_article`;

CREATE TABLE `dux_article` (
  `article_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `class_id` int(10) NOT NULL DEFAULT '0' COMMENT '栏目ID',
  `content_id` int(10) NOT NULL DEFAULT '0' COMMENT '内容ID',
  `content` text COMMENT '内容',
  PRIMARY KEY (`article_id`),
  KEY `class_id` (`class_id`),
  KEY `content_id` (`content_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table dux_article_class
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dux_article_class`;

CREATE TABLE `dux_article_class` (
  `class_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(10) NOT NULL DEFAULT '0' COMMENT '上级栏目',
  `category_id` int(10) NOT NULL DEFAULT '0',
  `tpl_class` varchar(250) DEFAULT '' COMMENT '栏目模板',
  `tpl_content` varchar(250) DEFAULT '' COMMENT '内容模板',
  PRIMARY KEY (`class_id`),
  KEY `category_id` (`category_id`),
  KEY `parent_id` (`parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table dux_site_class
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dux_site_class`;

CREATE TABLE `dux_site_class` (
  `category_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `model_id` int(10) NOT NULL DEFAULT '0' COMMENT '模型ID',
  `name` varchar(250) NOT NULL DEFAULT '' COMMENT '名称',
  `subname` varchar(250) DEFAULT '' COMMENT '副名称',
  `sort` int(10) NOT NULL DEFAULT '0' COMMENT '排序',
  `url` varchar(259) DEFAULT '' COMMENT '外部链接',
  `image` varchar(250) DEFAULT '' COMMENT '形象图',
  `keyword` varchar(250) DEFAULT '' COMMENT '关键词',
  `description` varchar(250) DEFAULT '' COMMENT '描述',
  `filter_id` int(10) DEFAULT '0' COMMENT '筛选ID',
  PRIMARY KEY (`category_id`),
  KEY `model_id` (`model_id`),
  KEY `filter_id` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table dux_site_config
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dux_site_config`;

CREATE TABLE `dux_site_config` (
  `config_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(250) NOT NULL DEFAULT '',
  `content` varchar(250) NOT NULL DEFAULT '',
  `description` varchar(250) NOT NULL DEFAULT '',
  PRIMARY KEY (`config_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `dux_site_config` WRITE;
/*!40000 ALTER TABLE `dux_site_config` DISABLE KEYS */;

INSERT INTO `dux_site_config` (`config_id`, `name`, `content`, `description`)
VALUES
	(1,'tpl_index','index','默认首页模板'),
	(2,'tpl_class','list','默认栏目模板'),
	(3,'tpl_content','content','默认内容模板'),
	(6,'info_title','DuxCms内容管理系统','站点标题'),
	(7,'info_keyword','','站点关键词'),
	(8,'info_desc','','站点描述'),
	(9,'info_copyright','Copyright@2013-2016 duxcms.com All Rights Reserved.','版权信息'),
	(10,'info_email','','站点邮箱'),
	(11,'info_tel','','站点电话'),
	(12,'tpl_name','default','模板目录'),
	(13,'tpl_tags','tag','标签模板'),
	(15,'tpl_search','search','搜索模板');

/*!40000 ALTER TABLE `dux_site_config` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table dux_site_content
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dux_site_content`;

CREATE TABLE `dux_site_content` (
  `content_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pos_id` varchar(250) DEFAULT '' COMMENT '推荐位',
  `title` varchar(250) NOT NULL DEFAULT '' COMMENT '标题',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态',
  `subtitle` varchar(250) DEFAULT '' COMMENT '副标题',
  `image` varchar(250) DEFAULT '' COMMENT '形象图',
  `url` varchar(250) DEFAULT '' COMMENT '外部链接',
  `keyword` varchar(250) DEFAULT '' COMMENT '关键词',
  `description` varchar(250) DEFAULT '' COMMENT '描述',
  `tpl` varchar(50) DEFAULT '' COMMENT '模板名',
  `sort` int(10) NOT NULL DEFAULT '0' COMMENT '排序',
  `create_time` varchar(10) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `view` int(10) NOT NULL DEFAULT '0' COMMENT '浏览量',
  `source` varchar(250) DEFAULT '' COMMENT '来源',
  `auth` varchar(250) DEFAULT '' COMMENT '作者',
  `editor` varchar(250) DEFAULT '' COMMENT '编辑',
  `tags_id` varchar(250) DEFAULT '' COMMENT 'tags',
  PRIMARY KEY (`content_id`),
  KEY `pos_id` (`pos_id`),
  KEY `title` (`title`),
  KEY `status` (`status`),
  KEY `sort` (`sort`),
  KEY `create_time` (`create_time`),
  KEY `view` (`view`),
  KEY `tags_id` (`tags_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table dux_site_content_attr
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dux_site_content_attr`;

CREATE TABLE `dux_site_content_attr` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `content_id` int(10) NOT NULL COMMENT '内容ID',
  `attr_id` int(10) NOT NULL COMMENT '属性ID',
  `value` varchar(250) NOT NULL DEFAULT '' COMMENT '属性值',
  PRIMARY KEY (`id`),
  KEY `content_id` (`content_id`),
  KEY `attr_id` (`attr_id`),
  KEY `value` (`value`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table dux_site_filter
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dux_site_filter`;

CREATE TABLE `dux_site_filter` (
  `filter_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(250) NOT NULL DEFAULT '',
  PRIMARY KEY (`filter_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table dux_site_filter_attr
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dux_site_filter_attr`;

CREATE TABLE `dux_site_filter_attr` (
  `attr_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '属性ID',
  `filter_id` int(10) NOT NULL COMMENT '筛选ID',
  `type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '控件类型',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '名称',
  `value` text COMMENT '属性值',
  PRIMARY KEY (`attr_id`),
  KEY `filter_id` (`filter_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table dux_site_form
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dux_site_form`;

CREATE TABLE `dux_site_form` (
  `form_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '表单名称',
  `description` varchar(250) DEFAULT '' COMMENT '表单描述',
  `label` varchar(50) DEFAULT '' COMMENT '表单标识',
  `tpl_list` varchar(50) DEFAULT '' COMMENT '列表模板',
  `tpl_info` varchar(50) DEFAULT '' COMMENT '内容模板',
  `status_list` tinyint(1) DEFAULT '0',
  `status_info` tinyint(1) DEFAULT '0',
  `submit` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`form_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table dux_site_form_field
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dux_site_form_field`;

CREATE TABLE `dux_site_form_field` (
  `field_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `form_id` int(11) NOT NULL DEFAULT '0',
  `name` varchar(50) DEFAULT '' COMMENT '字段名称',
  `label` varchar(50) DEFAULT '' COMMENT '字段标识',
  `type` varchar(50) DEFAULT '' COMMENT '字段类型',
  `tip` varchar(250) DEFAULT '' COMMENT '字段提示',
  `must` tinyint(1) DEFAULT '0' COMMENT '必须字段',
  `default` varchar(250) DEFAULT '' COMMENT '默认值',
  `sort` int(10) DEFAULT '0' COMMENT '字段顺序',
  `config` text COMMENT '字段配置',
  `show` tinyint(1) DEFAULT '0' COMMENT '列表显示',
  `submit` tinyint(1) DEFAULT '0' COMMENT '前台提交',
  PRIMARY KEY (`field_id`),
  KEY `form_id` (`form_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table dux_site_fragment
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dux_site_fragment`;

CREATE TABLE `dux_site_fragment` (
  `fragment_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(250) NOT NULL DEFAULT '' COMMENT '描述',
  `content` text COMMENT '内容',
  `editor` tinyint(1) NOT NULL DEFAULT '0' COMMENT '编辑器',
  PRIMARY KEY (`fragment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table dux_site_model
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dux_site_model`;

CREATE TABLE `dux_site_model` (
  `model_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '模型名称',
  `description` varchar(250) DEFAULT '' COMMENT '模型描述',
  `label` varchar(50) DEFAULT '' COMMENT '模型标识',
  PRIMARY KEY (`model_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table dux_site_model_field
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dux_site_model_field`;

CREATE TABLE `dux_site_model_field` (
  `field_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `model_id` int(11) NOT NULL DEFAULT '0',
  `name` varchar(50) DEFAULT '' COMMENT '字段名称',
  `label` varchar(50) DEFAULT '' COMMENT '字段标识',
  `type` varchar(50) DEFAULT '' COMMENT '字段类型',
  `tip` varchar(250) DEFAULT '' COMMENT '字段提示',
  `must` tinyint(1) DEFAULT '0' COMMENT '必须字段',
  `default` varchar(250) DEFAULT '' COMMENT '默认值',
  `sort` int(10) DEFAULT '0' COMMENT '字段顺序',
  `config` text COMMENT '字段配置',
  PRIMARY KEY (`field_id`),
  KEY `model_id` (`model_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table dux_site_nav
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dux_site_nav`;

CREATE TABLE `dux_site_nav` (
  `nav_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(10) DEFAULT '0' COMMENT '上级ID',
  `group_id` int(10) NOT NULL COMMENT '分组ID',
  `name` varchar(250) NOT NULL DEFAULT '' COMMENT '导航名称',
  `url` varchar(250) DEFAULT '' COMMENT '外链地址',
  `subname` varchar(10) DEFAULT '' COMMENT '导航副名称',
  `image` varchar(250) DEFAULT '' COMMENT '导航封面图',
  `keyword` varchar(250) DEFAULT '' COMMENT '导航关键词',
  `description` varchar(250) DEFAULT '' COMMENT '导航描述',
  `sort` int(10) DEFAULT '0' COMMENT '排序',
  PRIMARY KEY (`nav_id`),
  KEY `parent_id` (`parent_id`),
  KEY `group_id` (`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table dux_site_nav_group
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dux_site_nav_group`;

CREATE TABLE `dux_site_nav_group` (
  `group_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(250) NOT NULL DEFAULT '' COMMENT '分组名称',
  `description` varchar(250) NOT NULL DEFAULT '' COMMENT '分组描述',
  PRIMARY KEY (`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


LOCK TABLES `dux_site_nav_group` WRITE;
/*!40000 ALTER TABLE `dux_site_nav_group` DISABLE KEYS */;

INSERT INTO `dux_site_nav_group` (`group_id`, `name`, `description`)
VALUES
	(1,'默认分组','系统默认导航');

/*!40000 ALTER TABLE `dux_site_nav_group` ENABLE KEYS */;
UNLOCK TABLES;



# Dump of table dux_site_position
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dux_site_position`;

CREATE TABLE `dux_site_position` (
  `pos_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(250) NOT NULL DEFAULT '',
  `sort` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`pos_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `dux_site_position` WRITE;
/*!40000 ALTER TABLE `dux_site_position` DISABLE KEYS */;

INSERT INTO `dux_site_position` (`pos_id`, `name`, `sort`)
VALUES
	(1,'默认推荐',0);

/*!40000 ALTER TABLE `dux_site_position` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table dux_site_tags
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dux_site_tags`;

CREATE TABLE `dux_site_tags` (
  `tag_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(250) NOT NULL DEFAULT '',
  `quote` int(10) NOT NULL DEFAULT '1',
  `view` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`tag_id`),
  KEY `name` (`name`),
  KEY `quote` (`quote`),
  KEY `view` (`view`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table dux_system_file
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dux_system_file`;

CREATE TABLE `dux_system_file` (
  `file_id` int(10) NOT NULL AUTO_INCREMENT,
  `url` varchar(250) DEFAULT NULL,
  `original` varchar(250) DEFAULT NULL,
  `title` varchar(250) DEFAULT NULL,
  `ext` varchar(20) DEFAULT NULL,
  `size` int(10) DEFAULT NULL,
  `time` int(10) DEFAULT NULL,
  `user_id` int(10) DEFAULT NULL,
  PRIMARY KEY (`file_id`),
  KEY `ext` (`ext`),
  KEY `time` (`time`) USING BTREE,
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='上传文件';



# Dump of table dux_system_info
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dux_system_info`;

CREATE TABLE `dux_system_info` (
  `info_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(50) NOT NULL DEFAULT '' COMMENT '键名',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '配置名称',
  `value` text NOT NULL COMMENT '配置值',
  `description` varchar(250) NOT NULL DEFAULT '' COMMENT '配置描述',
  `reserve` tinyint(1) NOT NULL DEFAULT '0' COMMENT '内置',
  PRIMARY KEY (`info_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table dux_system_notice
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dux_system_notice`;

CREATE TABLE `dux_system_notice` (
  `notice_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `icon` varchar(20) DEFAULT '',
  `content` varchar(250) DEFAULT '',
  `url` varchar(250) DEFAULT '',
  `time` int(10) NOT NULL DEFAULT '0',
  `type` varchar(20) NOT NULL DEFAULT 'primary',
  PRIMARY KEY (`notice_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `dux_system_notice` WRITE;
/*!40000 ALTER TABLE `dux_system_notice` DISABLE KEYS */;

INSERT INTO `dux_system_notice` (`notice_id`, `icon`, `content`, `url`, `time`, `type`)
VALUES
	(1,'bars','欢迎使用DuxCms3内容管理系统','',1457247723,'primary');

/*!40000 ALTER TABLE `dux_system_notice` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table dux_system_role
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dux_system_role`;

CREATE TABLE `dux_system_role` (
  `role_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL DEFAULT '',
  `description` varchar(250) DEFAULT '',
  `purview` text,
  PRIMARY KEY (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `dux_system_role` WRITE;
/*!40000 ALTER TABLE `dux_system_role` DISABLE KEYS */;

INSERT INTO `dux_system_role` (`role_id`, `name`, `description`, `purview`)
VALUES
	(1,'管理员','系统后台管理员','a:93:{i:0;s:21:\"article.Content.index\";i:1;s:19:\"article.Content.add\";i:2;s:20:\"article.Content.edit\";i:3;s:22:\"article.Content.status\";i:4;s:19:\"article.Content.del\";i:5;s:19:\"article.Class.index\";i:6;s:17:\"article.Class.add\";i:7;s:18:\"article.Class.edit\";i:8;s:20:\"article.Class.status\";i:9;s:17:\"article.Class.del\";i:10;s:17:\"site.Config.index\";i:11;s:15:\"site.Config.tpl\";i:12;s:21:\"site.FormManage.index\";i:13;s:19:\"site.FormManage.add\";i:14;s:20:\"site.FormManage.edit\";i:15;s:19:\"site.FormManage.del\";i:16;s:20:\"site.FormField.index\";i:17;s:18:\"site.FormField.add\";i:18;s:19:\"site.FormField.edit\";i:19;s:18:\"site.FormField.del\";i:20;s:22:\"site.ModelManage.index\";i:21;s:20:\"site.ModelManage.add\";i:22;s:21:\"site.ModelManage.edit\";i:23;s:20:\"site.ModelManage.del\";i:24;s:21:\"site.ModelField.index\";i:25;s:19:\"site.ModelField.add\";i:26;s:20:\"site.ModelField.edit\";i:27;s:19:\"site.ModelField.del\";i:28;s:14:\"site.Nav.index\";i:29;s:12:\"site.Nav.add\";i:30;s:13:\"site.Nav.edit\";i:31;s:15:\"site.Nav.status\";i:32;s:12:\"site.Nav.del\";i:33;s:19:\"site.NavGroup.index\";i:34;s:17:\"site.NavGroup.add\";i:35;s:18:\"site.NavGroup.edit\";i:36;s:20:\"site.NavGroup.status\";i:37;s:17:\"site.NavGroup.del\";i:38;s:19:\"site.Fragment.index\";i:39;s:17:\"site.Fragment.add\";i:40;s:18:\"site.Fragment.edit\";i:41;s:20:\"site.Fragment.status\";i:42;s:17:\"site.Fragment.del\";i:43;s:19:\"site.Position.index\";i:44;s:17:\"site.Position.add\";i:45;s:18:\"site.Position.edit\";i:46;s:20:\"site.Position.status\";i:47;s:17:\"site.Position.del\";i:48;s:17:\"site.Filter.index\";i:49;s:15:\"site.Filter.add\";i:50;s:16:\"site.Filter.edit\";i:51;s:18:\"site.Filter.status\";i:52;s:15:\"site.Filter.del\";i:53;s:18:\"system.Index.index\";i:54;s:21:\"system.Index.userData\";i:55;s:19:\"system.Notice.index\";i:56;s:17:\"system.Notice.del\";i:57;s:23:\"system.Statistics.index\";i:58;s:26:\"system.Statistics.computer\";i:59;s:24:\"system.Statistics.mobile\";i:60;s:21:\"system.Statistics.api\";i:61;s:19:\"system.Config.index\";i:62;s:18:\"system.Config.user\";i:63;s:18:\"system.Config.info\";i:64;s:20:\"system.Config.upload\";i:65;s:25:\"system.ConfigManage.index\";i:66;s:23:\"system.ConfigManage.add\";i:67;s:24:\"system.ConfigManage.edit\";i:68;s:26:\"system.ConfigManage.status\";i:69;s:23:\"system.ConfigManage.del\";i:70;s:17:\"system.User.index\";i:71;s:15:\"system.User.add\";i:72;s:16:\"system.User.edit\";i:73;s:18:\"system.User.status\";i:74;s:15:\"system.User.del\";i:75;s:17:\"system.Role.index\";i:76;s:15:\"system.Role.add\";i:77;s:16:\"system.Role.edit\";i:78;s:15:\"system.Role.del\";i:79;s:24:\"system.Application.index\";i:80;s:22:\"system.Application.add\";i:81;s:23:\"system.Application.edit\";i:82;s:22:\"system.Application.del\";i:83;s:16:\"tools.Send.index\";i:84;s:14:\"tools.Send.add\";i:85;s:15:\"tools.Send.info\";i:86;s:20:\"tools.SendConf.index\";i:87;s:22:\"tools.SendConf.setting\";i:88;s:19:\"tools.SendTpl.index\";i:89;s:17:\"tools.SendTpl.add\";i:90;s:18:\"tools.SendTpl.edit\";i:91;s:17:\"tools.SendTpl.del\";i:92;s:17:\"tools.Label.index\";}');

/*!40000 ALTER TABLE `dux_system_role` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table dux_system_statistics
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dux_system_statistics`;

CREATE TABLE `dux_system_statistics` (
  `stat_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `date` varchar(8) DEFAULT '',
  `web` int(10) DEFAULT '0',
  `api` int(10) DEFAULT '0',
  `mobile` int(10) DEFAULT '0',
  PRIMARY KEY (`stat_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table dux_system_user
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dux_system_user`;

CREATE TABLE `dux_system_user` (
  `user_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `role_id` int(10) NOT NULL,
  `nickname` varchar(20) NOT NULL,
  `username` varchar(20) NOT NULL DEFAULT '',
  `password` varchar(128) NOT NULL DEFAULT '',
  `avatar` varchar(250) DEFAULT NULL,
  `status` tinyint(1) DEFAULT '0',
  `reg_time` int(10) DEFAULT '0',
  `login_time` int(10) DEFAULT '0',
  `login_ip` varchar(50) DEFAULT '',
  `role_ext` varchar(250) DEFAULT '',
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `dux_system_user` WRITE;
/*!40000 ALTER TABLE `dux_system_user` DISABLE KEYS */;

INSERT INTO `dux_system_user` (`user_id`, `role_id`, `nickname`, `username`, `password`, `avatar`, `status`, `reg_time`, `login_time`, `login_ip`, `role_ext`)
VALUES
	(1,1,'Dux','admin','21232f297a57a5a743894a0e4a801fc3','',1,0,1465626473,'::1','');

/*!40000 ALTER TABLE `dux_system_user` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table dux_tools_send
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dux_tools_send`;

CREATE TABLE `dux_tools_send` (
  `send_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `receive` varchar(250) NOT NULL DEFAULT '' COMMENT '接收账号',
  `title` varchar(250) NOT NULL DEFAULT '' COMMENT '发送标题',
  `content` text NOT NULL COMMENT '发送内容',
  `param` text COMMENT '附加参数',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '发送状态',
  `type` varchar(50) NOT NULL DEFAULT '' COMMENT '发送类型',
  `start_time` int(10) NOT NULL DEFAULT '0' COMMENT '添加时间',
  `stop_time` int(10) NOT NULL DEFAULT '0' COMMENT '结束时间',
  `remark` varchar(250) NOT NULL COMMENT '备注',
  PRIMARY KEY (`send_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;



# Dump of table dux_tools_send_config
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dux_tools_send_config`;

CREATE TABLE `dux_tools_send_config` (
  `config_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `type` varchar(250) NOT NULL DEFAULT '' COMMENT '类型名',
  `setting` text NOT NULL COMMENT '配置内容',
  PRIMARY KEY (`config_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;



# Dump of table dux_tools_send_tpl
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dux_tools_send_tpl`;

CREATE TABLE `dux_tools_send_tpl` (
  `tpl_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(250) NOT NULL DEFAULT '' COMMENT '模板标题',
  `content` text NOT NULL COMMENT '模板内容',
  `time` int(10) NOT NULL COMMENT '时间',
  PRIMARY KEY (`tpl_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;




/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
