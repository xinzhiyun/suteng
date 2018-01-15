/*
Navicat MySQL Data Transfer

Source Server         : wuzhibin.cn
Source Server Version : 50173
Source Host           : sdm359768304.my3w.com:3306
Source Database       : sdm359768304_db

Target Server Type    : MYSQL
Target Server Version : 50173
File Encoding         : 65001

Date: 2018-01-15 09:06:59
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for st_admin_menu
-- ----------------------------
DROP TABLE IF EXISTS `st_admin_menu`;
CREATE TABLE `st_admin_menu` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '菜单表',
  `pid` int(11) unsigned DEFAULT '0' COMMENT '所属菜单',
  `name` varchar(15) DEFAULT '' COMMENT '菜单名称',
  `mca` varchar(255) DEFAULT '' COMMENT '模块、控制器、方法',
  `ico` varchar(50) DEFAULT '' COMMENT 'font-awesome图标',
  `order_number` int(11) unsigned DEFAULT NULL COMMENT '排序',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of st_admin_menu
-- ----------------------------
INSERT INTO `st_admin_menu` VALUES ('1', '0', '分销商管理', 'Admin/Rule', 'fa-hand-o-right', null);
INSERT INTO `st_admin_menu` VALUES ('2', '1', '分销商列表', 'Admin/Vendors/vendor_list', '', null);
INSERT INTO `st_admin_menu` VALUES ('3', '1', '分销商审核', 'Admin/Vendors/vendor_reviewed', '', null);
INSERT INTO `st_admin_menu` VALUES ('4', '0', '权限管理', 'Admin/Rule/', 'fa-hand-o-right', null);
INSERT INTO `st_admin_menu` VALUES ('5', '4', '权限组', 'Admin/Rule/group', null, null);
INSERT INTO `st_admin_menu` VALUES ('6', '5', '权限分配', 'Admin/Rule/rule_group', null, null);
INSERT INTO `st_admin_menu` VALUES ('7', '5', '成员列表', 'Admin/Rule/group_list', null, null);
INSERT INTO `st_admin_menu` VALUES ('8', '4', '权限列表', 'Admin/Rule/index', null, null);
INSERT INTO `st_admin_menu` VALUES ('9', '0', '菜单管理', 'Admin/Menu/', 'fa-hand-o-right', null);
INSERT INTO `st_admin_menu` VALUES ('10', '9', '菜单编辑', 'Admin/Menu/index', '', null);
INSERT INTO `st_admin_menu` VALUES ('13', '1', '添加分销商', 'Admin/Vendors/vendor_add', '', null);

-- ----------------------------
-- Table structure for st_auth_group
-- ----------------------------
DROP TABLE IF EXISTS `st_auth_group`;
CREATE TABLE `st_auth_group` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` char(100) NOT NULL DEFAULT '',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `rules` text COMMENT '规则id',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='用户组表';

-- ----------------------------
-- Records of st_auth_group
-- ----------------------------
INSERT INTO `st_auth_group` VALUES ('1', '超级管理员', '1', '1,2,4,3,5,6,7,8,9,10,12,13,11');
INSERT INTO `st_auth_group` VALUES ('2', '一级经销商', '1', '1,2,4,3');
INSERT INTO `st_auth_group` VALUES ('3', '二级经销商', '1', '1,2,4,3');
INSERT INTO `st_auth_group` VALUES ('4', '三级经销商', '1', '1,2,3');

-- ----------------------------
-- Table structure for st_auth_group_access
-- ----------------------------
DROP TABLE IF EXISTS `st_auth_group_access`;
CREATE TABLE `st_auth_group_access` (
  `uid` int(11) unsigned NOT NULL COMMENT '用户id',
  `group_id` int(11) unsigned NOT NULL COMMENT '用户组id',
  UNIQUE KEY `uid_group_id` (`uid`,`group_id`),
  KEY `uid` (`uid`),
  KEY `group_id` (`group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='用户组明细表';

-- ----------------------------
-- Records of st_auth_group_access
-- ----------------------------
INSERT INTO `st_auth_group_access` VALUES ('1', '1');
INSERT INTO `st_auth_group_access` VALUES ('1', '4');

-- ----------------------------
-- Table structure for st_auth_rule
-- ----------------------------
DROP TABLE IF EXISTS `st_auth_rule`;
CREATE TABLE `st_auth_rule` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `pid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '父级id',
  `name` char(80) NOT NULL DEFAULT '' COMMENT '规则唯一标识',
  `title` char(20) NOT NULL DEFAULT '' COMMENT '规则中文名称',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态：为1正常，为0禁用',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `condition` char(100) NOT NULL DEFAULT '' COMMENT '规则表达式，为空表示存在就验证，不为空表示按照条件验证',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COMMENT='规则表';

-- ----------------------------
-- Records of st_auth_rule
-- ----------------------------
INSERT INTO `st_auth_rule` VALUES ('1', '0', 'Admin/Rule', '经销商管理', '1', '1', '');
INSERT INTO `st_auth_rule` VALUES ('2', '1', 'Admin/Vendors/vendor_list', '经销商列表', '1', '1', '');
INSERT INTO `st_auth_rule` VALUES ('3', '1', 'Admin/Vendors/vendor_reviewed', '经销商审核', '1', '1', '');
INSERT INTO `st_auth_rule` VALUES ('4', '2', 'Admin/Vendors/vendor_add', '经销商添加', '1', '1', '');
INSERT INTO `st_auth_rule` VALUES ('5', '0', 'Admin/Menu/', '菜单管理', '1', '1', '');
INSERT INTO `st_auth_rule` VALUES ('6', '5', 'Admin/Menu/index', '菜单编辑', '1', '1', '');
INSERT INTO `st_auth_rule` VALUES ('7', '0', 'Admin/Rule/', '权限管理', '1', '1', '');
INSERT INTO `st_auth_rule` VALUES ('8', '7', 'Admin/Rule/group', '权限组', '1', '1', '');
INSERT INTO `st_auth_rule` VALUES ('9', '7', 'Admin/Rule/index', '权限列表', '1', '1', '');
INSERT INTO `st_auth_rule` VALUES ('10', '8', 'Admin/Rule/rule_group', '权限分配', '1', '1', '');
INSERT INTO `st_auth_rule` VALUES ('11', '8', 'Admin/Rule/group_list', '成员列表', '1', '1', '');

-- ----------------------------
-- Table structure for st_dimension
-- ----------------------------
DROP TABLE IF EXISTS `st_dimension`;
CREATE TABLE `st_dimension` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `ticket` varchar(255) NOT NULL COMMENT '注册类型{0:会员直接注册 1:会员推荐会员 2:分销商推荐会员 3:分公司邀请分销商 4:分销商邀请分销商}',
  `recommend` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '注册类型{0:会员直接注册 1:会员推荐会员 2:分销商推荐会员 3:分公司邀请分销商 4:分销商邀请分销商}',
  `leavel` tinyint(1) unsigned DEFAULT NULL COMMENT '分销商级别{0:A级分销商 1:B级分销商 2:C级分销商}',
  `effective` tinyint(1) unsigned DEFAULT '0' COMMENT '有效期{0:永久 1:临时（30天）}',
  `addtime` int(11) unsigned NOT NULL COMMENT '创建时间',
  `updatetime` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of st_dimension
-- ----------------------------

-- ----------------------------
-- Table structure for st_dimension_num
-- ----------------------------
DROP TABLE IF EXISTS `st_dimension_num`;
CREATE TABLE `st_dimension_num` (
  `users_num` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '临时二维码数量',
  `vendors_num` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '永久二维码数量'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of st_dimension_num
-- ----------------------------

-- ----------------------------
-- Table structure for st_users
-- ----------------------------
DROP TABLE IF EXISTS `st_users`;
CREATE TABLE `st_users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `open_id` varchar(50) NOT NULL COMMENT '微信的ID号',
  `code` int(11) unsigned NOT NULL COMMENT '用户唯一标识',
  `invitation_code` int(11) unsigned DEFAULT NULL COMMENT '邀请人唯一标识',
  `ticket` varchar(255) DEFAULT NULL COMMENT '二维码票据',
  `nickname` varchar(50) DEFAULT NULL COMMENT '微信昵称',
  `head` varchar(255) DEFAULT NULL COMMENT '头像',
  `sex` tinyint(1) unsigned DEFAULT '2' COMMENT '性别{0:女 1:男 2:保密}',
  `area` varchar(20) DEFAULT NULL COMMENT '地区',
  `address` varchar(255) DEFAULT NULL COMMENT '地址',
  `addtime` int(11) unsigned NOT NULL COMMENT '创建时间',
  `updatetime` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of st_users
-- ----------------------------

-- ----------------------------
-- Table structure for st_vendors
-- ----------------------------
DROP TABLE IF EXISTS `st_vendors`;
CREATE TABLE `st_vendors` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `open_id` varchar(50) DEFAULT NULL COMMENT '微信的ID号',
  `user` varchar(30) NOT NULL COMMENT '分销商帐号',
  `password` varchar(32) NOT NULL COMMENT '分销商密码',
  `phone` int(11) unsigned DEFAULT NULL COMMENT '手机号码',
  `name` varchar(32) DEFAULT NULL COMMENT '真实姓名',
  `identity` varchar(18) DEFAULT NULL COMMENT '身份证号码',
  `positive` varchar(255) DEFAULT NULL COMMENT '身份证正面图片',
  `opposite` varchar(255) DEFAULT NULL COMMENT '身份证反面',
  `handheld` varchar(255) DEFAULT NULL COMMENT '手持身份证正面',
  `company` varchar(50) DEFAULT NULL COMMENT '公司名称',
  `telephone` varchar(20) DEFAULT NULL COMMENT '座机号码',
  `address` varchar(255) DEFAULT NULL COMMENT '地址',
  `licence` varchar(255) DEFAULT NULL COMMENT '营业执照',
  `protocol` varchar(255) DEFAULT NULL COMMENT '协议文件',
  `leavel` tinyint(1) unsigned DEFAULT '0' COMMENT '{0:超级管理员 1:分公司 2:A级分销商 3:B级分销商 4:C级分销商}',
  `code` int(6) DEFAULT NULL COMMENT '分销商唯一验证码',
  `superior_code` int(6) DEFAULT NULL COMMENT '上级分销商唯一验证码',
  `superiors_code` int(6) DEFAULT NULL COMMENT '上上级分销商唯一验证码',
  `office_code` int(6) DEFAULT NULL COMMENT '分公司唯一ID',
  `status` tinyint(1) unsigned DEFAULT '0' COMMENT '状态{0:身份填写 1:公司信息填写 3:签协议 2:等待审批 3:身份证审批失败 4:公司信息审批失败 5:协议审批失败  6：审批成功  7：禁用分销商}',
  `addtime` int(11) unsigned DEFAULT NULL COMMENT '添加时间',
  `updatetime` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of st_vendors
-- ----------------------------
INSERT INTO `st_vendors` VALUES ('1', null, 'admin', '202cb962ac59075b964b07152d234b70', '2147483647', '超级管理员', null, null, null, null, null, '020-21526352', null, null, null, '0', '15634', '0', null, null, '1', '1513872000', '1513872000');
INSERT INTO `st_vendors` VALUES ('2', null, 'wuzhibin', 'fengkuan123', '4294967295', '吴智彬', '440882198812045532', 'zm.jpg', 'fm.jpg', 'sssfz.jpg', '点球电子科技有限公司', '020-83225332-1', '广州市番禺区大夫山北门123号3楼201室', 'yzzz.jpg', 'xywj.jpg', '0', '0', null, null, null, '0', '0', null);
INSERT INTO `st_vendors` VALUES ('3', null, 'wzb3030', 'fengkuan12', '0', '', '', '', '', '', '', '', '', '', '', '0', '12536', null, null, null, '0', '0', null);
INSERT INTO `st_vendors` VALUES ('4', null, 'dew324$#@', 'deqwdew', '0', '', '', '', '', '', '', '', '', '', '', '0', '0', null, null, null, '0', '0', null);
INSERT INTO `st_vendors` VALUES ('5', null, 'dew324$#@', 'deqwdew', '0', '', '', '', '', '', '', '', '', '', '', '0', '0', null, null, null, '0', '0', null);
INSERT INTO `st_vendors` VALUES ('6', null, 'q3ed', 'qdwq', '0', '', '', '', '', '', '', '', '', '', '', '0', '0', null, null, null, '0', '0', null);
INSERT INTO `st_vendors` VALUES ('7', null, 'q3ed', 'qdwq', '0', '', '', '', '', '', '', '', '', '', '', '0', '0', null, null, null, '0', '0', null);
INSERT INTO `st_vendors` VALUES ('8', null, 'wuzhibin1212', 'e10adc3949ba59abbe56e057f20f883e', '4294967295', '', '', '', '', '', '', '', '', '', '', '0', null, null, null, null, '0', '1515923551', null);
INSERT INTO `st_vendors` VALUES ('9', null, 'efwfwewe', '5dd5870283c637cad2b8a8f40d2cdf4f', '0', '', '', '', '', '', '', '', '', '', '', '0', null, null, null, null, '0', '1515923923', null);
INSERT INTO `st_vendors` VALUES ('10', null, 'wrfwefwefv', 'e10adc3949ba59abbe56e057f20f883e', '0', '', '', '', '', '', '', '', '', '', '', '0', null, null, null, null, '0', '1515924353', null);
INSERT INTO `st_vendors` VALUES ('11', null, 'ewfeefw', '202cb962ac59075b964b07152d234b70', '0', '', '', '', '', '', '', '', '', '', '', '0', null, null, null, null, '0', '1515924432', null);

-- ----------------------------
-- Table structure for st_wechat
-- ----------------------------
DROP TABLE IF EXISTS `st_wechat`;
CREATE TABLE `st_wechat` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `open_id` varchar(50) NOT NULL COMMENT '微信的ID号',
  `invitation_ticket` varchar(255) NOT NULL COMMENT '受邀请二维码票据',
  `nickname` varchar(50) DEFAULT NULL COMMENT '微信昵称',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '用户类型{0:会员 1:分销商}',
  `action` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '操作{0:注册 1:登录}',
  `addtime` int(11) unsigned NOT NULL COMMENT '创建时间',
  `updatetime` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of st_wechat
-- ----------------------------
