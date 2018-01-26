/*
Navicat MySQL Data Transfer

Source Server         : wuzhibin.cn
Source Server Version : 50173
Source Host           : sdm359768304.my3w.com:3306
Source Database       : sdm359768304_db

Target Server Type    : MYSQL
Target Server Version : 50173
File Encoding         : 65001

Date: 2018-01-26 18:11:00
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for st_address
-- ----------------------------
DROP TABLE IF EXISTS `st_address`;
CREATE TABLE `st_address` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(12) NOT NULL COMMENT '收货人姓名',
  `phone` varchar(12) NOT NULL COMMENT '收货人电话',
  `address` varchar(255) NOT NULL COMMENT '收货人地址',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '地址状态，默认为0，设为默认地址，否则为1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of st_address
-- ----------------------------

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
) ENGINE=MyISAM AUTO_INCREMENT=30 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of st_admin_menu
-- ----------------------------
INSERT INTO `st_admin_menu` VALUES ('1', '0', '经销商管理', 'Admin/Vendors/', 'fa-hand-o-right', null);
INSERT INTO `st_admin_menu` VALUES ('2', '1', '经销商列表', 'Admin/Vendors/vendor_list', '', null);
INSERT INTO `st_admin_menu` VALUES ('3', '1', '经销商审核', 'Admin/Vendors/vendor_reviewed', '', null);
INSERT INTO `st_admin_menu` VALUES ('4', '0', '权限管理', 'Admin/Rule/', 'fa-hand-o-right', null);
INSERT INTO `st_admin_menu` VALUES ('5', '4', '权限组', 'Admin/Rule/group', null, null);
INSERT INTO `st_admin_menu` VALUES ('6', '5', '权限分配', 'Admin/Rule/rule_group', null, null);
INSERT INTO `st_admin_menu` VALUES ('7', '5', '成员列表', 'Admin/Rule/group_list', null, null);
INSERT INTO `st_admin_menu` VALUES ('8', '4', '权限列表', 'Admin/Rule/index', null, null);
INSERT INTO `st_admin_menu` VALUES ('9', '0', '菜单管理', 'Admin/Menu/', 'fa-hand-o-right', null);
INSERT INTO `st_admin_menu` VALUES ('10', '9', '菜单编辑', 'Admin/Menu/index', '', null);
INSERT INTO `st_admin_menu` VALUES ('13', '0', '设备管理', 'Admin/Devices/', 'fa-hand-o-right', null);
INSERT INTO `st_admin_menu` VALUES ('14', '13', '设备列表', 'Admin/Devices/index', null, null);
INSERT INTO `st_admin_menu` VALUES ('15', '0', '工单管理', 'Admin/Work', 'fa-hand-o-right', null);
INSERT INTO `st_admin_menu` VALUES ('16', '15', '工单列表', 'Admin/Work/index', '', null);
INSERT INTO `st_admin_menu` VALUES ('17', '0', '净水器管理', 'Admin/Water', 'fa-hand-o-right', null);
INSERT INTO `st_admin_menu` VALUES ('18', '17', '水机列表', 'Admin/Water/index', '', null);
INSERT INTO `st_admin_menu` VALUES ('19', '0', '商城管理', 'Admin/Shop/', 'fa-hand-o-right', null);
INSERT INTO `st_admin_menu` VALUES ('20', '19', '分类管理', 'Admin/Shop/category', '', null);
INSERT INTO `st_admin_menu` VALUES ('21', '19', '商品管理', 'Admin/Shop/goods', '', null);
INSERT INTO `st_admin_menu` VALUES ('22', '19', '订单管理', 'Admin/Shop/orders', '', null);
INSERT INTO `st_admin_menu` VALUES ('23', '0', '分公司管理', 'Admin/Vendor', '', null);
INSERT INTO `st_admin_menu` VALUES ('24', '23', '添加分公司', 'Admin/Vendors/company_add', '', null);
INSERT INTO `st_admin_menu` VALUES ('25', '23', '分公司列表', 'Admin/Vendors/company_list', '', null);
INSERT INTO `st_admin_menu` VALUES ('28', '0', '佣金管理', 'Admin/Commission', '', null);
INSERT INTO `st_admin_menu` VALUES ('29', '28', '佣金设置', 'Admin/Commission/commission_set', '', null);

-- ----------------------------
-- Table structure for st_attr
-- ----------------------------
DROP TABLE IF EXISTS `st_attr`;
CREATE TABLE `st_attr` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL COMMENT '属性名称',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of st_attr
-- ----------------------------

-- ----------------------------
-- Table structure for st_attr_val
-- ----------------------------
DROP TABLE IF EXISTS `st_attr_val`;
CREATE TABLE `st_attr_val` (
  `gid` int(11) NOT NULL COMMENT '商品ID',
  `aid` int(11) DEFAULT NULL COMMENT '属性ID',
  ` val` varchar(20) NOT NULL COMMENT '属性值'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of st_attr_val
-- ----------------------------

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
INSERT INTO `st_auth_group` VALUES ('1', '超级管理员', '1', '1,2,28,5,6,7,8,10,11,9,14,15,16,17,18,19,20,21,22,23,24,25,26,30,31');
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
) ENGINE=MyISAM AUTO_INCREMENT=32 DEFAULT CHARSET=utf8 COMMENT='规则表';

-- ----------------------------
-- Records of st_auth_rule
-- ----------------------------
INSERT INTO `st_auth_rule` VALUES ('1', '0', 'Admin/Vendors/', '经销商管理', '1', '1', '');
INSERT INTO `st_auth_rule` VALUES ('2', '1', 'Admin/Vendors/vendor_list', '经销商列表', '1', '1', '');
INSERT INTO `st_auth_rule` VALUES ('5', '0', 'Admin/Menu/', '菜单管理', '1', '1', '');
INSERT INTO `st_auth_rule` VALUES ('6', '5', 'Admin/Menu/index', '菜单编辑', '1', '1', '');
INSERT INTO `st_auth_rule` VALUES ('7', '0', 'Admin/Rule/', '权限管理', '1', '1', '');
INSERT INTO `st_auth_rule` VALUES ('8', '7', 'Admin/Rule/group', '权限组', '1', '1', '');
INSERT INTO `st_auth_rule` VALUES ('9', '7', 'Admin/Rule/index', '权限列表', '1', '1', '');
INSERT INTO `st_auth_rule` VALUES ('10', '8', 'Admin/Rule/rule_group', '权限分配', '1', '1', '');
INSERT INTO `st_auth_rule` VALUES ('11', '8', 'Admin/Rule/group_list', '成员列表', '1', '1', '');
INSERT INTO `st_auth_rule` VALUES ('14', '0', 'Admin/Devices/', '设备管理', '1', '1', '');
INSERT INTO `st_auth_rule` VALUES ('15', '14', 'Admin/Devices/index', '设备列表', '1', '1', '');
INSERT INTO `st_auth_rule` VALUES ('16', '0', 'Admin/Work', '工单管理', '1', '1', '');
INSERT INTO `st_auth_rule` VALUES ('17', '16', 'Admin/Work/index', '工单列表', '1', '1', '');
INSERT INTO `st_auth_rule` VALUES ('18', '0', 'Admin/Water', '净水器管理', '1', '1', '');
INSERT INTO `st_auth_rule` VALUES ('19', '18', 'Admin/Water/index', '水机列表', '1', '1', '');
INSERT INTO `st_auth_rule` VALUES ('20', '0', 'Admin/Shop/', '商城系统', '1', '1', '');
INSERT INTO `st_auth_rule` VALUES ('21', '20', 'Admin/Shop/category', '分类管理', '1', '1', '');
INSERT INTO `st_auth_rule` VALUES ('22', '20', 'Admin/Shop/goods', '商品管理', '1', '1', '');
INSERT INTO `st_auth_rule` VALUES ('23', '20', 'Admin/Shop/orders', '订单管理', '1', '1', '');
INSERT INTO `st_auth_rule` VALUES ('24', '0', 'Admin/Vendor', '分公司管理', '1', '1', '');
INSERT INTO `st_auth_rule` VALUES ('25', '24', 'Admin/Vendors/company_add', '添加分公司', '1', '1', '');
INSERT INTO `st_auth_rule` VALUES ('26', '24', 'Admin/Vendors/company_list', '分公司列表', '1', '1', '');
INSERT INTO `st_auth_rule` VALUES ('28', '1', 'Admin/Vendors/vendor_reviewed', '分销商审核', '1', '1', '');
INSERT INTO `st_auth_rule` VALUES ('30', '0', 'Admin/Commission', '佣金管理', '1', '1', '');
INSERT INTO `st_auth_rule` VALUES ('31', '30', 'Admin/Commission/commission_set', '佣金设置', '1', '1', '');

-- ----------------------------
-- Table structure for st_binding
-- ----------------------------
DROP TABLE IF EXISTS `st_binding`;
CREATE TABLE `st_binding` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `vid` int(11) NOT NULL COMMENT '经销商ID',
  `did` int(11) NOT NULL COMMENT '设备ID',
  `operator` varchar(255) NOT NULL COMMENT '操作人是谁',
  `addtime` int(11) NOT NULL COMMENT '操作时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=26 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of st_binding
-- ----------------------------

-- ----------------------------
-- Table structure for st_blank
-- ----------------------------
DROP TABLE IF EXISTS `st_blank`;
CREATE TABLE `st_blank` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(12) NOT NULL COMMENT '持卡人姓名',
  `card` varchar(32) NOT NULL COMMENT '银行卡号',
  `phone` varchar(12) NOT NULL COMMENT '手机号码',
  `code` varchar(12) DEFAULT NULL COMMENT '验证码',
  `code_status` tinyint(1) DEFAULT NULL COMMENT '验证码状态，0为未使用，1为已经使用',
  `uid` int(11) NOT NULL COMMENT '关联用户',
  `addtime` varchar(12) NOT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of st_blank
-- ----------------------------

-- ----------------------------
-- Table structure for st_cart
-- ----------------------------
DROP TABLE IF EXISTS `st_cart`;
CREATE TABLE `st_cart` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gid` int(11) NOT NULL COMMENT '商品ID',
  `num` int(11) NOT NULL COMMENT '商品数量',
  `price` double(6,2) NOT NULL COMMENT '商品单价',
  `addtime` varchar(12) NOT NULL COMMENT '添加购物时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of st_cart
-- ----------------------------

-- ----------------------------
-- Table structure for st_category
-- ----------------------------
DROP TABLE IF EXISTS `st_category`;
CREATE TABLE `st_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL COMMENT '父ID',
  `name` varchar(12) NOT NULL COMMENT '分类名',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of st_category
-- ----------------------------
INSERT INTO `st_category` VALUES ('1', '0', 'aaa');
INSERT INTO `st_category` VALUES ('2', '0', 'bbb');
INSERT INTO `st_category` VALUES ('3', '0', 'aaa1');
INSERT INTO `st_category` VALUES ('4', '2', 'bbb1ddd');
INSERT INTO `st_category` VALUES ('5', '0', 'aaa2');
INSERT INTO `st_category` VALUES ('6', '2', 'bbb2');
INSERT INTO `st_category` VALUES ('7', '3', 'aaa1a');
INSERT INTO `st_category` VALUES ('8', '3', 'aaa2a');
INSERT INTO `st_category` VALUES ('10', '4', 'bbb2b');
INSERT INTO `st_category` VALUES ('11', '0', '厉害');
INSERT INTO `st_category` VALUES ('12', '0', '发财了');
INSERT INTO `st_category` VALUES ('14', '5', '哈哈哈');
INSERT INTO `st_category` VALUES ('15', '14', '大厦');
INSERT INTO `st_category` VALUES ('16', '6', '止ddddd');
INSERT INTO `st_category` VALUES ('17', '6', '123');
INSERT INTO `st_category` VALUES ('21', '5', '添加成功');
INSERT INTO `st_category` VALUES ('22', '7', '老子开心');

-- ----------------------------
-- Table structure for st_comment
-- ----------------------------
DROP TABLE IF EXISTS `st_comment`;
CREATE TABLE `st_comment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL COMMENT '关联用户',
  `gid` int(11) NOT NULL COMMENT '关联商品',
  `content` varchar(255) NOT NULL COMMENT '评论内容',
  `installer_graded` int(11) DEFAULT NULL COMMENT '安装人员评分',
  `attitude` int(11) DEFAULT NULL COMMENT '服务态度评分',
  `dressing` int(11) DEFAULT NULL COMMENT '着装评分',
  `addtime` varchar(12) NOT NULL COMMENT '评论时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of st_comment
-- ----------------------------

-- ----------------------------
-- Table structure for st_commission
-- ----------------------------
DROP TABLE IF EXISTS `st_commission`;
CREATE TABLE `st_commission` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `user_y` int(11) unsigned NOT NULL DEFAULT '0',
  `user_j` int(11) unsigned NOT NULL DEFAULT '0',
  `inviter_y` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '会员邀请人分配占比',
  `inviter_j` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '会员邀请人分配占比',
  `vendor_a` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'A级分销商分配占比',
  `vendor_b` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'B级分销商分配占比',
  `vendor_c` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'C级分销商分配占比',
  `vendor_i` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '分销商邀请人分配占比',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '分配类型{0：按比例分配，1：固定金额分配}',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '状态{0：启用，1：禁用}',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of st_commission
-- ----------------------------
INSERT INTO `st_commission` VALUES ('1', '40', '50', '60', '50', '40', '30', '10', '20', '0', '0');

-- ----------------------------
-- Table structure for st_convert
-- ----------------------------
DROP TABLE IF EXISTS `st_convert`;
CREATE TABLE `st_convert` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gold` int(11) NOT NULL COMMENT '金币数量',
  `silver` int(11) NOT NULL COMMENT '银币数量',
  `uid` int(11) NOT NULL COMMENT '兑换人',
  `addtime` varchar(12) NOT NULL COMMENT '兑换时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of st_convert
-- ----------------------------

-- ----------------------------
-- Table structure for st_current_devices
-- ----------------------------
DROP TABLE IF EXISTS `st_current_devices`;
CREATE TABLE `st_current_devices` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `uid` int(11) unsigned NOT NULL COMMENT '用户',
  `did` int(11) unsigned NOT NULL COMMENT '当前设备',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=gbk;

-- ----------------------------
-- Records of st_current_devices
-- ----------------------------

-- ----------------------------
-- Table structure for st_devices
-- ----------------------------
DROP TABLE IF EXISTS `st_devices`;
CREATE TABLE `st_devices` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `device_code` varchar(20) NOT NULL COMMENT '设备编码',
  `device_statu` tinyint(1) unsigned NOT NULL COMMENT '设备状态：1已入库，2待激活，3已激活',
  `addtime` int(11) NOT NULL COMMENT '添加时间',
  `type_id` int(11) NOT NULL COMMENT '产品类型ID',
  PRIMARY KEY (`id`),
  KEY `id` (`id`,`device_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of st_devices
-- ----------------------------

-- ----------------------------
-- Table structure for st_devices_statu
-- ----------------------------
DROP TABLE IF EXISTS `st_devices_statu`;
CREATE TABLE `st_devices_statu` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `DeviceID` varchar(20) NOT NULL COMMENT '设备码',
  `DeviceStause` tinyint(2) DEFAULT '11' COMMENT '设备状态 0:制水 1:冲洗 2:水满 3:缺水 4漏水 5:检修 6:欠费停机 7:关机 8:开机(仅命令)',
  `ReFlow` int(10) DEFAULT NULL COMMENT '剩余流量 租赁用',
  `Reday` int(10) DEFAULT NULL COMMENT '剩余天数 租赁用',
  `RawTDS` int(10) DEFAULT NULL COMMENT '纯水',
  `PureTDS` int(10) DEFAULT NULL COMMENT '原水',
  `Temperature` int(10) DEFAULT NULL COMMENT '温度',
  `ReFlowFilter1` int(10) DEFAULT NULL COMMENT '滤芯1剩余流量',
  `ReDayFilter1` int(10) DEFAULT NULL COMMENT '滤芯1剩余天数',
  `ReFlowFilter2` int(10) DEFAULT NULL COMMENT '滤芯2剩余流量',
  `ReDayFilter2` int(10) DEFAULT NULL COMMENT '滤芯2剩余天数',
  `ReFlowFilter3` int(10) DEFAULT NULL COMMENT '滤芯3剩余流量',
  `ReDayFilter3` int(10) DEFAULT NULL COMMENT '滤芯3剩余天数',
  `ReFlowFilter4` int(10) DEFAULT NULL COMMENT '滤芯4剩余流量',
  `ReDayFilter4` int(10) DEFAULT NULL COMMENT '滤芯4剩余天数',
  `ReFlowFilter5` int(10) DEFAULT NULL COMMENT '滤芯5剩余流量',
  `ReDayFilter5` int(10) DEFAULT NULL COMMENT '滤芯5剩余天数',
  `ReFlowFilter6` int(10) DEFAULT NULL COMMENT '滤芯6剩余流量',
  `ReDayFilter6` int(10) DEFAULT NULL COMMENT '滤芯6剩余天数',
  `ReFlowFilter7` int(10) DEFAULT NULL COMMENT '滤芯7剩余流量',
  `ReDayFilter7` int(10) DEFAULT NULL COMMENT '滤芯7剩余天数',
  `ReFlowFilter8` int(10) DEFAULT NULL COMMENT '滤芯8剩余流量',
  `ReDayFilter8` int(10) DEFAULT NULL COMMENT '滤芯8剩余天数',
  `LeasingMode` tinyint(1) DEFAULT NULL COMMENT '租赁模式  0:零售型 1:按流量计费 2:按时间计费 3:时长和流量套餐',
  `AliveStause` tinyint(1) DEFAULT NULL,
  `SumFlow` int(10) DEFAULT NULL COMMENT '累计流量',
  `SumDay` int(10) DEFAULT NULL COMMENT '累计天数',
  `FilterMode` tinyint(1) DEFAULT NULL COMMENT '滤芯模式',
  `Device` varchar(10) DEFAULT NULL,
  `ICCID` varchar(20) DEFAULT NULL,
  `CSQ` int(3) DEFAULT NULL,
  `Loaction` varchar(200) DEFAULT NULL,
  `addtime` varchar(11) DEFAULT NULL,
  `updatetime` varchar(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`,`DeviceID`,`DeviceStause`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of st_devices_statu
-- ----------------------------

-- ----------------------------
-- Table structure for st_dimension
-- ----------------------------
DROP TABLE IF EXISTS `st_dimension`;
CREATE TABLE `st_dimension` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `ticket` varchar(255) NOT NULL COMMENT '注册类型{0:会员直接注册 1:会员推荐会员 2:分销商推荐会员 3:分公司邀请分销商 4:分销商邀请分销商}',
  `recommend` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '注册类型{0:会员直接注册 1:会员推荐会员 2：分销商推荐会员 3：分公司推荐会员 4:分公司邀请分销商 5:分销商邀请分销商}',
  `leavel` tinyint(1) unsigned DEFAULT NULL COMMENT '分销商级别{0:A级分销商 1:B级分销商 2:C级分销商}',
  `effective` tinyint(1) unsigned DEFAULT '0' COMMENT '有效期{0:永久 1:临时（30天）}',
  `addtime` int(11) unsigned NOT NULL COMMENT '创建时间',
  `updatetime` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=78 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of st_dimension
-- ----------------------------
INSERT INTO `st_dimension` VALUES ('29', 'gQGK8DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAySml2TmtHMmJkOTAxMDAwMHcwM04AAgRU8F5aAwQAAAAA', '4', '0', '0', '1516435822', '1516435822');
INSERT INTO `st_dimension` VALUES ('30', 'gQF78DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyV3h0VmxYMmJkOTAxMDAwMDAwM3AAAgRU8F5aAwQAAAAA', '4', '1', '0', '1516435822', '1516435822');
INSERT INTO `st_dimension` VALUES ('31', 'gQGW8DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyWFlZeWxJMmJkOTAxMDAwMGcwMzUAAgRf815aAwQAAAAA', '4', '2', '0', '1516435822', '1516435822');
INSERT INTO `st_dimension` VALUES ('32', 'gQG28DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyUk10WmswMmJkOTAxMDAwMHcwMzUAAgRf815aAwQAAAAA', '3', null, '0', '1516435822', '1516435822');
INSERT INTO `st_dimension` VALUES ('33', 'gQHu8DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyRl8ySmxCMmJkOTAxMDAwMDAwM2wAAgRw815aAwQAAAAA', '4', '0', '0', '1516587613', '1516587613');
INSERT INTO `st_dimension` VALUES ('34', 'gQEZ8DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAya2tEWGxzMmJkOTAxMDAwMHcwM3cAAgRx815aAwQAAAAA', '4', '1', '0', '1516587613', '1516587613');
INSERT INTO `st_dimension` VALUES ('35', 'gQE28TwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyNXNldGw4MmJkOTAxMDAwMHcwM0YAAgSx815aAwQAAAAA', '4', '2', '0', '1516587613', '1516587613');
INSERT INTO `st_dimension` VALUES ('36', 'gQGL8TwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyOU1NNGtBMmJkOTAxMDAwMHcwM2MAAgSx815aAwQAAAAA', '3', null, '0', '1516587613', '1516587613');
INSERT INTO `st_dimension` VALUES ('37', 'gQFA8TwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyVU9aa2swMmJkOTAxMDAwME0wMzUAAgQC9F5aAwQAAAAA', '4', '0', '0', '1516601917', '1516601917');
INSERT INTO `st_dimension` VALUES ('38', 'gQHL8DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyR2l1Y2t0MmJkOTAxMDAwMDAwM2QAAgQC9F5aAwQAAAAA', '4', '1', '0', '1516601917', '1516601917');
INSERT INTO `st_dimension` VALUES ('39', 'gQFS8DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyU21rZWw2MmJkOTAxMDAwMGcwM3MAAgRq9F5aAwQAAAAA', '4', '2', '0', '1516601917', '1516601917');
INSERT INTO `st_dimension` VALUES ('40', 'gQEZ8DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyemNUZWx4MmJkOTAxMDAwMHcwM1QAAgRq9F5aAwQAAAAA', '3', null, '0', '1516601917', '1516601917');
INSERT INTO `st_dimension` VALUES ('41', 'gQFp8TwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAydmFWRms2MmJkOTAxMDAwME0wM2sAAgST9F5aAwQAAAAA', '4', '0', '0', '1516858546', '1516858546');
INSERT INTO `st_dimension` VALUES ('42', 'gQFI8TwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyZ2ROU2tPMmJkOTAxMDAwME0wM3oAAgST9F5aAwQAAAAA', '4', '1', '0', '1516858546', '1516858546');
INSERT INTO `st_dimension` VALUES ('43', 'gQE18TwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAySHN4ZGxfMmJkOTAxMDAwMDAwM2EAAgSf9V5aAwQAAAAA', '4', '2', '0', '1516858546', '1516858546');
INSERT INTO `st_dimension` VALUES ('44', 'gQFC8TwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyM0tfb2t1MmJkOTAxMDAwMGcwM1MAAgSf9V5aAwQAAAAA', '3', null, '0', '1516858546', '1516858546');
INSERT INTO `st_dimension` VALUES ('45', 'gQEe8TwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAySWpOdGw4MmJkOTAxMDAwMDAwMzgAAgQf-l5aAwQAAAAA', '5', '0', '0', '1516858662', '1516858662');
INSERT INTO `st_dimension` VALUES ('46', 'gQG-8DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAySmlVQWxVMmJkOTAxMDAwMHcwM18AAgQf-l5aAwQAAAAA', '5', '1', '0', '1516858662', '1516858662');
INSERT INTO `st_dimension` VALUES ('47', 'gQFz8TwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyUWNHN2xMMmJkOTAxMDAwMGcwM1MAAgSXDGBaAwQAAAAA', '5', '2', '0', '1516858662', '1516858662');
INSERT INTO `st_dimension` VALUES ('48', 'gQHC8TwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyQ2NFUmtLMmJkOTAxMDAwMDAwMzIAAgSXDGBaAwQAAAAA', '2', null, '0', '1516858662', '1516858662');
INSERT INTO `st_dimension` VALUES ('49', 'gQF47zwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyTWpaQ2x3MmJkOTAxSndUZzFxMVgAAgQwcmlaAwQwhScA', '1', null, '1', '1516859953', '1516859953');
INSERT INTO `st_dimension` VALUES ('50', 'gQFq8TwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAySkh1bGtRMmJkOTAxMDAwMDAwM0oAAgSaDWBaAwQAAAAA', '5', '0', '0', '1516942196', '1516942196');
INSERT INTO `st_dimension` VALUES ('51', 'gQHz8DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAydVNxUmttMmJkOTAxMDAwMGcwM1YAAgSaDWBaAwQAAAAA', '5', '1', '0', '1516942196', '1516942196');
INSERT INTO `st_dimension` VALUES ('52', 'gQEk8TwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyazV0X2xyMmJkOTAxMDAwMDAwM1oAAgSuDWBaAwQAAAAA', '5', '2', '0', '1516942196', '1516942196');
INSERT INTO `st_dimension` VALUES ('53', 'gQH28DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyMlV3Z2xpMmJkOTAxMDAwMGcwM2gAAgSuDWBaAwQAAAAA', '2', null, '0', '1516942196', '1516942196');
INSERT INTO `st_dimension` VALUES ('54', 'gQGb8DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyMTJFUWxJMmJkOTAxeEdWaTFxMW8AAgQ6tGpaAwQwhScA', '1', null, '1', '1516942394', '1516942394');
INSERT INTO `st_dimension` VALUES ('55', 'gQGR8DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyMXBRamtsMmJkOTAxemdXaTFxMWoAAgSgtWpaAwQwhScA', '1', null, '1', '1516942752', '1516942752');
INSERT INTO `st_dimension` VALUES ('56', 'gQHU8DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAya0RTWmtFMmJkOTAxQVQyaTFxMVAAAgQHvWpaAwQwhScA', '1', null, '1', '1516944648', '1516944648');
INSERT INTO `st_dimension` VALUES ('57', 'gQEN8DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAySHptQ2xwMmJkOTAxQnc0aWhxMU0AAgQwv2paAwQwhScA', '1', null, '1', '1516945201', '1516945201');
INSERT INTO `st_dimension` VALUES ('58', 'gQGP8DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyYjRpYmxxMmJkOTAxMDAwMDAwM3oAAgQ1DmBaAwQAAAAA', '5', '1', '0', '1516945361', '1516945361');
INSERT INTO `st_dimension` VALUES ('59', 'gQHT8DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyQkQ0T2tmMmJkOTAxMDAwMHcwM1IAAgQ2DmBaAwQAAAAA', '5', '2', '0', '1516945361', '1516945361');
INSERT INTO `st_dimension` VALUES ('60', 'gQG58DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAya1NSLWtZMmJkOTAxMDAwMHcwMzMAAgRIDmBaAwQAAAAA', '2', null, '0', '1516945361', '1516945361');
INSERT INTO `st_dimension` VALUES ('61', 'gQF-8DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyTmk0Y2xBMmJkOTAxQzY1aXhxMTYAAgRWwGpaAwQwhScA', '1', null, '1', '1516945495', '1516945495');
INSERT INTO `st_dimension` VALUES ('62', 'gQFB8DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyZHl2bGt5MmJkOTAxMDAwME0wMzMAAgRIDmBaAwQAAAAA', '5', '2', '0', '1516947144', '1516947144');
INSERT INTO `st_dimension` VALUES ('63', 'gQHc8TwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyUllHZ2xVMmJkOTAxMDAwMDAwMzcAAgSNymZaAwQAAAAA', '2', null, '0', '1516947144', '1516947144');
INSERT INTO `st_dimension` VALUES ('64', 'gQGW8DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyZTJGNmtjMmJkOTAxQzNjaTFxMVEAAgRTx2paAwQwhScA', '1', null, '1', '1516947283', '1516947283');
INSERT INTO `st_dimension` VALUES ('65', 'gQHe8TwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyQjY1UGtOMmJkOTAxMDAwMHcwM1EAAgSOymZaAwQAAAAA', '5', '2', '0', '1516947909', '1516947909');
INSERT INTO `st_dimension` VALUES ('66', 'gQEL8TwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAybHVfcGtrMmJkOTAxMDAwMGcwM2QAAgSOymZaAwQAAAAA', '2', null, '0', '1516947909', '1516947909');
INSERT INTO `st_dimension` VALUES ('67', 'gQHw7zwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyVmpPNWtGMmJkOTAxQ3hmaTFxMXoAAgRxympaAwQwhScA', '1', null, '1', '1516948082', '1516948082');
INSERT INTO `st_dimension` VALUES ('68', 'gQFh8DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAySHhzQ2xiMmJkOTAxRHJqaXhxMTYAAgSrzmpaAwQwhScA', '1', null, '1', '1516949163', '1516949163');
INSERT INTO `st_dimension` VALUES ('69', 'gQHE7zwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyR180c2xxMmJkOTAxQnZFaXhxMXcAAgQv42paAwQwhScA', '1', null, '1', '1516954416', '1516954416');
INSERT INTO `st_dimension` VALUES ('70', 'gQFl8DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyTC00aWxoMmJkOTAxQmdLaTFxMW8AAgQg6WpaAwQwhScA', '1', null, '1', '1516955936', '1516955936');
INSERT INTO `st_dimension` VALUES ('71', 'gQEL8DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyU2NDdGtsMmJkOTAxQzFLaU5xMVYAAgRR6WpaAwQwhScA', '1', null, '1', '1516955986', '1516955986');
INSERT INTO `st_dimension` VALUES ('72', 'gQHp7zwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyQmJPYmtwMmJkOTAxQzVOaWhxMWQAAgRV7GpaAwQwhScA', '1', null, '1', '1516956758', '1516956758');
INSERT INTO `st_dimension` VALUES ('73', 'gQEW8TwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyOTU1TmtaMmJkOTAxRE9RaTFxMVoAAgTC72paAwQwhScA', '1', null, '1', '1516957634', '1516957634');
INSERT INTO `st_dimension` VALUES ('74', 'gQGp7zwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyZkNoa2tnMmJkOTAxQ0FSaXhxMUMAAgR08GpaAwQwhScA', '1', null, '1', '1516957813', '1516957813');
INSERT INTO `st_dimension` VALUES ('75', 'gQFi8DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyYmFxMmtzMmJkOTAxQTNTaWhxMVQAAgTT8GpaAwQwhScA', '1', null, '1', '1516957907', '1516957907');
INSERT INTO `st_dimension` VALUES ('76', 'gQER8DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyd3ZaRWx3MmJkOTAxQWpWaU5xMUcAAgTj82paAwQwhScA', '1', null, '1', '1516958692', '1516958692');
INSERT INTO `st_dimension` VALUES ('77', 'gQEt8DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyWmZ3aGxOMmJkOTAxQXRWaTFxMUoAAgTt82paAwQwhScA', '1', null, '1', '1516958702', '1516958702');

-- ----------------------------
-- Table structure for st_dimension_num
-- ----------------------------
DROP TABLE IF EXISTS `st_dimension_num`;
CREATE TABLE `st_dimension_num` (
  `users_num` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '临时二维码数量',
  `vendors_num` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '永久二维码数量',
  `id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of st_dimension_num
-- ----------------------------
INSERT INTO `st_dimension_num` VALUES ('19', '60', '1');

-- ----------------------------
-- Table structure for st_feeds
-- ----------------------------
DROP TABLE IF EXISTS `st_feeds`;
CREATE TABLE `st_feeds` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content` text NOT NULL COMMENT '反馈内容',
  `uid` int(11) NOT NULL COMMENT '用户ID',
  `addtime` int(11) NOT NULL COMMENT '反馈时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of st_feeds
-- ----------------------------

-- ----------------------------
-- Table structure for st_flow
-- ----------------------------
DROP TABLE IF EXISTS `st_flow`;
CREATE TABLE `st_flow` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '充值流水ID',
  `uid` int(11) unsigned NOT NULL COMMENT '用户ID',
  `order_id` varchar(32) NOT NULL COMMENT '订单编号',
  `money` decimal(25,0) unsigned DEFAULT NULL COMMENT '充值金额',
  `mode` tinyint(1) unsigned DEFAULT NULL COMMENT '充值方式(0：系统赠送 1：微信 2：支付宝)',
  `flow` int(11) unsigned NOT NULL COMMENT '流量',
  `num` int(11) unsigned NOT NULL COMMENT '套餐数量',
  `describe` varchar(255) DEFAULT NULL COMMENT '套餐描述',
  `currentflow` int(11) unsigned DEFAULT NULL COMMENT '当前流量',
  `addtime` int(11) NOT NULL COMMENT '充值时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=29 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of st_flow
-- ----------------------------

-- ----------------------------
-- Table structure for st_goods
-- ----------------------------
DROP TABLE IF EXISTS `st_goods`;
CREATE TABLE `st_goods` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cid` int(11) NOT NULL COMMENT '商品分类',
  `vid` int(11) DEFAULT NULL COMMENT '关联卖家',
  `name` varchar(64) NOT NULL COMMENT '商品名称',
  `pic` varchar(255) DEFAULT NULL,
  `desc` text COMMENT '商品描述',
  `cost` double(6,2) NOT NULL COMMENT '商品成本',
  `price` double(6,2) NOT NULL COMMENT '商品单价',
  `stock` int(11) NOT NULL DEFAULT '0' COMMENT '商品库存',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '商品状态，默认0上架，1为下架',
  `addtime` varchar(12) NOT NULL COMMENT '商品添加时间',
  `updatetime` varchar(12) NOT NULL COMMENT '商品更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of st_goods
-- ----------------------------

-- ----------------------------
-- Table structure for st_repair
-- ----------------------------
DROP TABLE IF EXISTS `st_repair`;
CREATE TABLE `st_repair` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `device_code` varchar(255) NOT NULL COMMENT '设备编码',
  `content` text NOT NULL COMMENT '问题描述',
  `picpath` varchar(255) NOT NULL COMMENT '图片路径',
  `uid` int(11) NOT NULL COMMENT '报修人ID 用户ID',
  `address` varchar(255) NOT NULL COMMENT '维修地址',
  `addtime` int(11) NOT NULL COMMENT '报修时间',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态0：未处理 1：已处理',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of st_repair
-- ----------------------------

-- ----------------------------
-- Table structure for st_shop_order
-- ----------------------------
DROP TABLE IF EXISTS `st_shop_order`;
CREATE TABLE `st_shop_order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` varchar(12) NOT NULL COMMENT '订单号码',
  `gid` int(11) NOT NULL COMMENT '商品ID',
  `g_cost` double(6,2) NOT NULL,
  `g_price` double(6,2) NOT NULL COMMENT '商品单价',
  `g_num` int(11) NOT NULL COMMENT '商品数量',
  `addtime` varchar(12) NOT NULL COMMENT '订单添加时间',
  `status` tinyint(1) NOT NULL COMMENT '0已下单，1处理中，2已发货，3已收货，4退货处理中，5已退货，6申诉中，7订单完成，8未支付',
  `express` varchar(32) DEFAULT NULL COMMENT '快递单号',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of st_shop_order
-- ----------------------------

-- ----------------------------
-- Table structure for st_users
-- ----------------------------
DROP TABLE IF EXISTS `st_users`;
CREATE TABLE `st_users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `open_id` varchar(50) NOT NULL COMMENT '微信的ID号',
  `office_code` varchar(6) DEFAULT NULL COMMENT '分公司唯一标识',
  `vendora_code` varchar(6) DEFAULT NULL COMMENT 'A级分销商唯一标识',
  `vendorb_code` varchar(6) DEFAULT NULL COMMENT 'B级分销商唯一标识',
  `vendorc_code` varchar(6) DEFAULT NULL COMMENT 'C级分销商唯一标识',
  `vendori_code` varchar(6) DEFAULT NULL COMMENT '分销商邀请人',
  `invitation_code` varchar(11) DEFAULT NULL COMMENT '邀请人唯一标识',
  `code` varchar(11) NOT NULL COMMENT '用户唯一标识',
  `invite` tinyint(1) NOT NULL COMMENT '邀请人类型:{0：分公司，1：A级分销，2：B级分销，3：C级分销，4：会员 5：普通二维码}',
  `ticket` varchar(255) NOT NULL COMMENT '二维码票据',
  `parameter` int(11) unsigned NOT NULL COMMENT '请求二维码的参数',
  `ticket_time` int(11) unsigned NOT NULL COMMENT '二维码有效时间',
  `nickname` varchar(50) DEFAULT NULL COMMENT '微信昵称',
  `head` varchar(255) DEFAULT NULL COMMENT '头像',
  `sex` tinyint(1) unsigned DEFAULT '2' COMMENT '性别{0:女 1:男 2:保密}',
  `area` varchar(20) DEFAULT NULL COMMENT '地区',
  `address` varchar(255) DEFAULT NULL COMMENT '地址',
  `gold_num` int(11) NOT NULL COMMENT '金币数量 默认为0',
  `silver` int(11) NOT NULL COMMENT '金币数量 默认为0',
  `balance` double(6,2) NOT NULL DEFAULT '0.00' COMMENT '用户余额，默认0.00',
  `addtime` int(11) unsigned NOT NULL COMMENT '创建时间',
  `updatetime` int(11) unsigned NOT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of st_users
-- ----------------------------
INSERT INTO `st_users` VALUES ('13', 'oQktJwL8ioR4DoxSQmikdzekbABCu', '53AB0F', '428777', '1CE019', '7352CF', '1CE019', '7352CF', '06DCFD672A7', '3', 'gQHw7zwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyVmpPNWtGMmJkOTAxQ3hmaTFxMXoAAgRxympaAwQwhScA', '8', '1519538082', '吴智彬-13425492760', 'http://wx.qlogo.cn/mmopen/Q3auHgzwzM4IUw4vejmKA64ib5YEGDyFzrOkCVqa7EasmHUxvGDb5dA9nfjQ7x0zffsF5hCiaEvZgnt7WHjpeEPjAB7ibq9nZu0Ep2fcqtaH84/132', '1', '广东', '中国 广东 广州', '14800', '24660', '0.00', '1516948080', '1516959607');
INSERT INTO `st_users` VALUES ('14', 'oQktJwL8ioR4DoxSQmikdzekbUyU', '53AB0F', '428777', '1CE019', '7352CF', '1CE019', '06DCFD672A7', 'E8EDB67CF5F', '4', 'gQFh8DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAySHhzQ2xiMmJkOTAxRHJqaXhxMTYAAgSrzmpaAwQwhScA', '9', '1519539163', '吴智彬-13425492760', 'http://wx.qlogo.cn/mmopen/Q3auHgzwzM4IUw4vejmKA64ib5YEGDyFzrOkCVqa7EasmHUxvGDb5dA9nfjQ7x0zffsF5hCiaEvZgnt7WHjpeEPjAB7ibq9nZu0Ep2fcqtaH84/132', '1', '广东', '中国 广东 广州', '14160', '16110', '0.00', '1516949162', '1516959607');
INSERT INTO `st_users` VALUES ('15', 'oQktJwH4pvWGmyzQW5Pw2QlsgoMA', null, null, null, null, null, null, '4B4CE0F8477', '5', 'gQHE7zwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyR180c2xxMmJkOTAxQnZFaXhxMXcAAgQv42paAwQwhScA', '10', '1519544416', '艳阳天', 'http://wx.qlogo.cn/mmopen/8RP9wLClmUicRxrG1wwicpcnpREmJiakXfuMKaqVyKIYqWIf4vB74NRNDzJDWdkocvFicwXIKHlksGl3q6Wwam45DHgxrLxpM8Nu/132', '0', '堪培拉', '澳大利亚 堪培拉 ', '0', '0', '0.00', '1516954415', '1516954415');
INSERT INTO `st_users` VALUES ('16', 'oQktJwIWnouiVZV7ks9OqtDWg_kI', null, null, null, null, null, null, '2442F6394A2', '5', 'gQFl8DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyTC00aWxoMmJkOTAxQmdLaTFxMW8AAgQg6WpaAwQwhScA', '11', '1519545936', '宝松', 'http://wx.qlogo.cn/mmopen/PCxWjnicpibLPrVPt1nibibI8YA52IrlohXVhVR1NIA4jibyTAj1OgmicGF1ITYBp4TwqrQ2LlqJpszTRPgfmYZ104yHuEoXyCeXxb/132', '1', '北区', '中国香港 北区 ', '0', '0', '0.00', '1516955935', '1516955935');
INSERT INTO `st_users` VALUES ('17', 'oQktJwOg4MgSWV2vvkeYXWLRhPLE', null, null, null, null, null, null, '568A6CC8281', '5', 'gQEL8DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyU2NDdGtsMmJkOTAxQzFLaU5xMVYAAgRR6WpaAwQwhScA', '12', '1519545986', 'dyf', 'http://wx.qlogo.cn/mmopen/PCxWjnicpibLPvBkEAoIzHcCltJGicycMNrJoOv7GxT7ibv7hNHPwAYQmoKZD928SBhbibpjZgFHLyiaxbB3G1WRfTyOgIVictNVkicib/132', '2', '', '  ', '0', '0', '0.00', '1516955985', '1516955985');
INSERT INTO `st_users` VALUES ('18', 'oQktJwDcehCKazprwaD6cCBi_gMQ', null, null, null, null, null, null, '79C543ACFD4', '5', 'gQHp7zwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyQmJPYmtwMmJkOTAxQzVOaWhxMWQAAgRV7GpaAwQwhScA', '13', '1519546758', '人心难测', 'http://wx.qlogo.cn/mmopen/tW8icv6f6mGOAdZXcK6W4ic7skeRicHKcHD24zk5avRDoRamBVbpWgWxN6NfXIU9uEHPUthHmkzIE4ib5xJHTF76e7GqcMicvDkibP/132', '1', '辽宁', '中国 辽宁 朝阳', '1000', '1200', '0.00', '1516956757', '1516956930');
INSERT INTO `st_users` VALUES ('19', 'oQktJwFlFLD07FF0E5qsKEnnljgY', null, null, null, null, null, null, '171DF1BF3E5', '5', 'gQEW8TwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyOTU1TmtaMmJkOTAxRE9RaTFxMVoAAgTC72paAwQwhScA', '14', '1519547634', '乐施林视力康复18088833688', 'http://wx.qlogo.cn/mmopen/uFDO94xgj3E5CyZj7FEvbb4GgaeGso4TCibsduQHSSe5ibqu1ErJgrXHTiafvgYqd9qSVDGk8EPWvW5uxeQng5qZfcPlzup4MJ7/132', '1', '广东', '中国 广东 佛山', '0', '0', '0.00', '1516957633', '1516957633');
INSERT INTO `st_users` VALUES ('20', 'oQktJwKn6We-LT7EVXYmFjcPA6Lg', null, null, null, null, null, null, '5017D5BDDAF', '5', 'gQGp7zwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyZkNoa2tnMmJkOTAxQ0FSaXhxMUMAAgR08GpaAwQwhScA', '15', '1519547813', '芦妈', 'http://wx.qlogo.cn/mmopen/GB58cfpy0hJyv1ic5FKXibE2UF1iazT5SoicOITsNibyAySVR2YShpIIvRc2ToMzPKxegEQDRgCibfSNRUIurFrJUKO2PEaPORB9Vw/132', '2', '', '  ', '0', '0', '0.00', '1516957812', '1516957812');
INSERT INTO `st_users` VALUES ('21', 'oQktJwM_1gtIGWFOr2J9IWNGTbOE', null, null, null, null, null, null, 'A375F99A52F', '5', 'gQFi8DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyYmFxMmtzMmJkOTAxQTNTaWhxMVQAAgTT8GpaAwQwhScA', '16', '1519547907', '凌艺', 'http://wx.qlogo.cn/mmopen/KQas9Orx7ZuqYibMGMDvXJdNEt6zfRuTEuPxlPhNNnJ8YLoht0RXXKuCgM2jGDibDEriafVI4CyBrPGRAxga8aW7yqJnDXFsoBe/132', '2', '', '  ', '0', '0', '0.00', '1516957906', '1516957906');
INSERT INTO `st_users` VALUES ('22', 'oQktJwAxpRl83nGeNg5eFQlmb2t4', null, null, null, null, null, null, '935F68985DD', '5', 'gQER8DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyd3ZaRWx3MmJkOTAxQWpWaU5xMUcAAgTj82paAwQwhScA', '17', '1519548692', '随遇而安', 'http://wx.qlogo.cn/mmopen/8RP9wLClmU9hmtqEUSkEScenlic1fbBnZ9icevLB36xD2ibEr8P6Xicp6XclCicsiaB8uhTsKkOf2INwWpLqpKBpTzib7kiaKZiahU0SY/132', '0', '辽宁', '中国 辽宁 朝阳', '1000', '1200', '0.00', '1516958691', '1516959110');
INSERT INTO `st_users` VALUES ('23', 'oQktJwJNaHdSZDpd5vZELuZWvRlA', null, null, null, null, null, null, '1C3B2C374E3', '5', 'gQEt8DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyWmZ3aGxOMmJkOTAxQXRWaTFxMUoAAgTt82paAwQwhScA', '18', '1519548702', '是福', 'http://wx.qlogo.cn/mmopen/8RP9wLClmU9icRwTlWjLdB5zMwdpM63JjYL4ibNicoGWwia3ymXE22rZM3picTcPYBRwmozvEjDfp2UtARamQk6QwfkiaLrX0MWJibg/132', '1', '内蒙古', '中国 内蒙古 赤峰', '0', '0', '0.00', '1516958701', '1516958701');

-- ----------------------------
-- Table structure for st_users_commission
-- ----------------------------
DROP TABLE IF EXISTS `st_users_commission`;
CREATE TABLE `st_users_commission` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增',
  `nexus_user` varchar(11) NOT NULL COMMENT '产生分红的会员',
  `user_code` varchar(11) NOT NULL COMMENT '关联用户表ID',
  `order_id` varchar(12) DEFAULT NULL COMMENT '订单号码',
  `gold_num` int(11) unsigned DEFAULT '0' COMMENT '金币数量 默认为0',
  `silver` int(11) unsigned DEFAULT '0' COMMENT '银币数量 默认为0',
  `current_gold_num` int(11) unsigned DEFAULT '0' COMMENT '当前金币数量 默认为0',
  `current_silver` int(11) unsigned DEFAULT '0' COMMENT '当前银币数量 默认为0',
  `describe` varchar(255) DEFAULT NULL COMMENT '佣金获得描述',
  `type` tinyint(1) unsigned DEFAULT '0' COMMENT '分配类型{0：自己购买，1：邀请的会员购买}',
  `status` tinyint(1) unsigned DEFAULT '0' COMMENT '状态{0：启用，1：禁用}',
  `addtime` int(11) unsigned DEFAULT NULL COMMENT '创建时间',
  `updatetime` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=95 DEFAULT CHARSET=gbk;

-- ----------------------------
-- Records of st_users_commission
-- ----------------------------
INSERT INTO `st_users_commission` VALUES ('43', 'E8EDB67CF5F', 'E8EDB67CF5F', '123456', '40', '30', '40', '30', '自己购买，单号为[123456]的商品获得金币奖励:40个,获得银币奖励：30个', '0', '0', '1516951755', '1516951755');
INSERT INTO `st_users_commission` VALUES ('44', 'E8EDB67CF5F', '06DCFD672A7', '123456', '60', '70', '60', '70', '邀请会员吴智彬-13425492760购买，单号为[123456]的商品获得金币奖励:60个,获得银币奖励：70个', '1', '0', '1516951755', '1516951755');
INSERT INTO `st_users_commission` VALUES ('45', 'E8EDB67CF5F', 'E8EDB67CF5F', '123456', '40', '30', '80', '60', '自己购买，单号为[123456]的商品获得金币奖励:40个,获得银币奖励：30个', '0', '0', '1516951895', '1516951895');
INSERT INTO `st_users_commission` VALUES ('46', 'E8EDB67CF5F', '06DCFD672A7', '123456', '60', '70', '120', '140', '邀请会员吴智彬-13425492760购买，单号为[123456]的商品获得金币奖励:60个,获得银币奖励：70个', '1', '0', '1516951895', '1516951895');
INSERT INTO `st_users_commission` VALUES ('47', 'E8EDB67CF5F', 'E8EDB67CF5F', '123456', '40', '30', '120', '90', '自己购买，单号为[123456]的商品获得金币奖励:40个,获得银币奖励：30个', '0', '0', '1516952022', '1516952022');
INSERT INTO `st_users_commission` VALUES ('48', 'E8EDB67CF5F', '06DCFD672A7', '123456', '60', '70', '180', '210', '邀请会员吴智彬-13425492760购买，单号为[123456]的商品获得金币奖励:60个,获得银币奖励：70个', '1', '0', '1516952022', '1516952022');
INSERT INTO `st_users_commission` VALUES ('49', 'E8EDB67CF5F', 'E8EDB67CF5F', '123456', '40', '30', '160', '120', '自己购买，单号为[123456]的商品获得金币奖励:40个,获得银币奖励：30个', '0', '0', '1516952063', '1516952063');
INSERT INTO `st_users_commission` VALUES ('50', 'E8EDB67CF5F', 'E8EDB67CF5F', '123456', '40', '30', '160', '120', '自己购买，单号为[123456]的商品获得金币奖励:40个,获得银币奖励：30个', '0', '0', '1516952063', '1516952063');
INSERT INTO `st_users_commission` VALUES ('51', 'E8EDB67CF5F', 'E8EDB67CF5F', '123456', '40', '30', '160', '120', '自己购买，单号为[123456]的商品获得金币奖励:40个,获得银币奖励：30个', '0', '0', '1516952063', '1516952063');
INSERT INTO `st_users_commission` VALUES ('52', 'E8EDB67CF5F', '06DCFD672A7', '123456', '60', '70', '240', '280', '邀请会员吴智彬-13425492760购买，单号为[123456]的商品获得金币奖励:60个,获得银币奖励：70个', '1', '0', '1516952063', '1516952063');
INSERT INTO `st_users_commission` VALUES ('53', 'E8EDB67CF5F', '06DCFD672A7', '123456', '60', '70', '240', '280', '邀请会员吴智彬-13425492760购买，单号为[123456]的商品获得金币奖励:60个,获得银币奖励：70个', '1', '0', '1516952063', '1516952063');
INSERT INTO `st_users_commission` VALUES ('54', 'E8EDB67CF5F', '06DCFD672A7', '123456', '60', '70', '300', '350', '邀请会员吴智彬-13425492760购买，单号为[123456]的商品获得金币奖励:60个,获得银币奖励：70个', '1', '0', '1516952063', '1516952063');
INSERT INTO `st_users_commission` VALUES ('55', 'E8EDB67CF5F', 'E8EDB67CF5F', '123456', '40', '30', '200', '150', '自己购买，单号为[123456]的商品获得金币奖励:40个,获得银币奖励：30个', '0', '0', '1516952081', '1516952081');
INSERT INTO `st_users_commission` VALUES ('56', 'E8EDB67CF5F', '06DCFD672A7', '123456', '60', '70', '360', '420', '邀请会员吴智彬-13425492760购买，单号为[123456]的商品获得金币奖励:60个,获得银币奖励：70个', '1', '0', '1516952081', '1516952081');
INSERT INTO `st_users_commission` VALUES ('57', 'E8EDB67CF5F', 'E8EDB67CF5F', '123456', '40', '30', '240', '180', '自己购买，单号为[123456]的商品获得金币奖励:40个,获得银币奖励：30个', '0', '0', '1516952082', '1516952082');
INSERT INTO `st_users_commission` VALUES ('58', 'E8EDB67CF5F', 'E8EDB67CF5F', '123456', '40', '30', '280', '210', '自己购买，单号为[123456]的商品获得金币奖励:40个,获得银币奖励：30个', '0', '0', '1516952082', '1516952082');
INSERT INTO `st_users_commission` VALUES ('59', 'E8EDB67CF5F', '06DCFD672A7', '123456', '60', '70', '420', '490', '邀请会员吴智彬-13425492760购买，单号为[123456]的商品获得金币奖励:60个,获得银币奖励：70个', '1', '0', '1516952082', '1516952082');
INSERT INTO `st_users_commission` VALUES ('60', 'E8EDB67CF5F', '06DCFD672A7', '123456', '60', '70', '480', '560', '邀请会员吴智彬-13425492760购买，单号为[123456]的商品获得金币奖励:60个,获得银币奖励：70个', '1', '0', '1516952082', '1516952082');
INSERT INTO `st_users_commission` VALUES ('61', 'E8EDB67CF5F', 'E8EDB67CF5F', '123456', '40', '30', '320', '240', '自己购买，单号为[123456]的商品获得金币奖励:40个,获得银币奖励：30个', '0', '0', '1516952082', '1516952082');
INSERT INTO `st_users_commission` VALUES ('62', 'E8EDB67CF5F', '06DCFD672A7', '123456', '60', '70', '540', '630', '邀请会员吴智彬-13425492760购买，单号为[123456]的商品获得金币奖励:60个,获得银币奖励：70个', '1', '0', '1516952082', '1516952082');
INSERT INTO `st_users_commission` VALUES ('63', 'E8EDB67CF5F', 'E8EDB67CF5F', '123456', '40', '30', '360', '270', '自己购买，单号为[123456]的商品获得金币奖励:40个,获得银币奖励：30个', '0', '0', '1516952117', '1516952117');
INSERT INTO `st_users_commission` VALUES ('64', 'E8EDB67CF5F', '06DCFD672A7', '123456', '60', '70', '600', '700', '邀请会员吴智彬-13425492760购买，单号为[123456]的商品获得金币奖励:60个,获得银币奖励：70个', '1', '0', '1516952117', '1516952117');
INSERT INTO `st_users_commission` VALUES ('65', 'E8EDB67CF5F', 'E8EDB67CF5F', '123456', '800', '240', '1160', '510', '自己购买，单号为[123456]的商品获得金币奖励:800个,获得银币奖励：240个', '0', '0', '1516952206', '1516952206');
INSERT INTO `st_users_commission` VALUES ('66', 'E8EDB67CF5F', '06DCFD672A7', '123456', '1200', '560', '1800', '1260', '邀请会员吴智彬-13425492760购买，单号为[123456]的商品获得金币奖励:1200个,获得银币奖励：560个', '1', '0', '1516952206', '1516952206');
INSERT INTO `st_users_commission` VALUES ('67', 'E8EDB67CF5F', 'E8EDB67CF5F', '123456', '1000', '1200', '2160', '1710', '自己购买，单号为[123456]的商品获得金币奖励:1000个,获得银币奖励：1200个', '0', '0', '1516952902', '1516952902');
INSERT INTO `st_users_commission` VALUES ('68', 'E8EDB67CF5F', '06DCFD672A7', '123456', '1000', '1800', '2800', '3060', '邀请会员吴智彬-13425492760购买，单号为[123456]的商品获得金币奖励:1000个,获得银币奖励：1800个', '1', '0', '1516952902', '1516952902');
INSERT INTO `st_users_commission` VALUES ('69', 'E8EDB67CF5F', 'E8EDB67CF5F', '123456', '1000', '1200', '3160', '2910', '自己购买，单号为[123456]的商品获得金币奖励:1000个,获得银币奖励：1200个', '0', '0', '1516953004', '1516953004');
INSERT INTO `st_users_commission` VALUES ('70', 'E8EDB67CF5F', '06DCFD672A7', '123456', '1000', '1800', '3800', '4860', '邀请会员吴智彬-13425492760购买，单号为[123456]的商品获得金币奖励:1000个,获得银币奖励：1800个', '1', '0', '1516953004', '1516953004');
INSERT INTO `st_users_commission` VALUES ('71', 'E8EDB67CF5F', 'E8EDB67CF5F', '123456', '1000', '1200', '4160', '4110', '自己购买，单号为[123456]的商品获得金币奖励:1000个,获得银币奖励：1200个', '0', '0', '1516953037', '1516953037');
INSERT INTO `st_users_commission` VALUES ('72', 'E8EDB67CF5F', '06DCFD672A7', '123456', '1000', '1800', '4800', '6660', '邀请会员吴智彬-13425492760购买，单号为[123456]的商品获得金币奖励:1000个,获得银币奖励：1800个', '1', '0', '1516953037', '1516953037');
INSERT INTO `st_users_commission` VALUES ('73', 'E8EDB67CF5F', 'E8EDB67CF5F', '123456', '1000', '1200', '5160', '5310', '自己购买，单号为[123456]的商品获得金币奖励:1000个,获得银币奖励：1200个', '0', '0', '1516953082', '1516953082');
INSERT INTO `st_users_commission` VALUES ('74', 'E8EDB67CF5F', '06DCFD672A7', '123456', '1000', '1800', '5800', '8460', '邀请会员吴智彬-13425492760购买，单号为[123456]的商品获得金币奖励:1000个,获得银币奖励：1800个', '1', '0', '1516953082', '1516953082');
INSERT INTO `st_users_commission` VALUES ('75', 'E8EDB67CF5F', 'E8EDB67CF5F', '123456', '1000', '1200', '6160', '6510', '自己购买，单号为[123456]的商品获得金币奖励:1000个,获得银币奖励：1200个', '0', '0', '1516953236', '1516953236');
INSERT INTO `st_users_commission` VALUES ('76', 'E8EDB67CF5F', '06DCFD672A7', '123456', '1000', '1800', '6800', '10260', '邀请会员吴智彬-13425492760购买，单号为[123456]的商品获得金币奖励:1000个,获得银币奖励：1800个', '1', '0', '1516953236', '1516953236');
INSERT INTO `st_users_commission` VALUES ('77', 'E8EDB67CF5F', 'E8EDB67CF5F', '123456', '1000', '1200', '7160', '7710', '自己购买，单号为[123456]的商品获得金币奖励:1000个,获得银币奖励：1200个', '0', '0', '1516953747', '1516953747');
INSERT INTO `st_users_commission` VALUES ('78', 'E8EDB67CF5F', '06DCFD672A7', '123456', '1000', '1800', '7800', '12060', '邀请会员吴智彬-13425492760购买，单号为[123456]的商品获得金币奖励:1000个,获得银币奖励：1800个', '1', '0', '1516953747', '1516953747');
INSERT INTO `st_users_commission` VALUES ('79', 'E8EDB67CF5F', 'E8EDB67CF5F', '123456', '1000', '1200', '8160', '8910', '自己购买，单号为[123456]的商品获得金币奖励:1000个,获得银币奖励：1200个', '0', '0', '1516953786', '1516953786');
INSERT INTO `st_users_commission` VALUES ('80', 'E8EDB67CF5F', '06DCFD672A7', '123456', '1000', '1800', '8800', '13860', '邀请会员吴智彬-13425492760购买，单号为[123456]的商品获得金币奖励:1000个,获得银币奖励：1800个', '1', '0', '1516953786', '1516953786');
INSERT INTO `st_users_commission` VALUES ('81', 'E8EDB67CF5F', 'E8EDB67CF5F', '123456', '1000', '1200', '9160', '10110', '自己购买，单号为[123456]的商品获得金币奖励:1000个,获得银币奖励：1200个', '0', '0', '1516953905', '1516953905');
INSERT INTO `st_users_commission` VALUES ('82', 'E8EDB67CF5F', '06DCFD672A7', '123456', '1000', '1800', '9800', '15660', '邀请会员吴智彬-13425492760购买，单号为[123456]的商品获得金币奖励:1000个,获得银币奖励：1800个', '1', '0', '1516953905', '1516953905');
INSERT INTO `st_users_commission` VALUES ('83', 'E8EDB67CF5F', 'E8EDB67CF5F', '123456', '1000', '1200', '10160', '11310', '自己购买，单号为[123456]的商品获得金币奖励:1000个,获得银币奖励：1200个', '0', '0', '1516953912', '1516953912');
INSERT INTO `st_users_commission` VALUES ('84', 'E8EDB67CF5F', '06DCFD672A7', '123456', '1000', '1800', '10800', '17460', '邀请会员吴智彬-13425492760购买，单号为[123456]的商品获得金币奖励:1000个,获得银币奖励：1800个', '1', '0', '1516953912', '1516953912');
INSERT INTO `st_users_commission` VALUES ('85', 'E8EDB67CF5F', 'E8EDB67CF5F', '123456', '1000', '1200', '11160', '12510', '自己购买，单号为[123456]的商品获得金币奖励:1000个,获得银币奖励：1200个', '0', '0', '1516953923', '1516953923');
INSERT INTO `st_users_commission` VALUES ('86', 'E8EDB67CF5F', '06DCFD672A7', '123456', '1000', '1800', '11800', '19260', '邀请会员吴智彬-13425492760购买，单号为[123456]的商品获得金币奖励:1000个,获得银币奖励：1800个', '1', '0', '1516953923', '1516953923');
INSERT INTO `st_users_commission` VALUES ('87', 'E8EDB67CF5F', 'E8EDB67CF5F', '123456', '1000', '1200', '12160', '13710', '自己购买，单号为[123456]的商品获得金币奖励:1000个,获得银币奖励：1200个', '0', '0', '1516953934', '1516953934');
INSERT INTO `st_users_commission` VALUES ('88', 'E8EDB67CF5F', '06DCFD672A7', '123456', '1000', '1800', '12800', '21060', '邀请会员吴智彬-13425492760购买，单号为[123456]的商品获得金币奖励:1000个,获得银币奖励：1800个', '1', '0', '1516953934', '1516953934');
INSERT INTO `st_users_commission` VALUES ('89', 'E8EDB67CF5F', 'E8EDB67CF5F', '123456', '1000', '1200', '13160', '14910', '自己购买，单号为[123456]的商品获得金币奖励:1000个,获得银币奖励：1200个', '0', '0', '1516955083', '1516955083');
INSERT INTO `st_users_commission` VALUES ('90', 'E8EDB67CF5F', '06DCFD672A7', '123456', '1000', '1800', '13800', '22860', '邀请会员吴智彬-13425492760购买，单号为[123456]的商品获得金币奖励:1000个,获得银币奖励：1800个', '1', '0', '1516955084', '1516955084');
INSERT INTO `st_users_commission` VALUES ('91', '79C543ACFD4', '79C543ACFD4', '123456', '1000', '1200', '1000', '1200', '自己购买，单号为[123456]的商品获得金币奖励:1000个,获得银币奖励：1200个', '0', '0', '1516956930', '1516956930');
INSERT INTO `st_users_commission` VALUES ('92', '935F68985DD', '935F68985DD', '123456', '1000', '1200', '1000', '1200', '自己购买，单号为[123456]的商品获得金币奖励:1000个,获得银币奖励：1200个', '0', '0', '1516959110', '1516959110');
INSERT INTO `st_users_commission` VALUES ('93', 'E8EDB67CF5F', 'E8EDB67CF5F', '123456', '1000', '1200', '14160', '16110', '自己购买，单号为[123456]的商品获得金币奖励:1000个,获得银币奖励：1200个', '0', '0', '1516959607', '1516959607');
INSERT INTO `st_users_commission` VALUES ('94', 'E8EDB67CF5F', '06DCFD672A7', '123456', '1000', '1800', '14800', '24660', '邀请会员吴智彬-13425492760购买，单号为[123456]的商品获得金币奖励:1000个,获得银币奖励：1800个', '1', '0', '1516959607', '1516959607');

-- ----------------------------
-- Table structure for st_vendors
-- ----------------------------
DROP TABLE IF EXISTS `st_vendors`;
CREATE TABLE `st_vendors` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `open_id` varchar(50) DEFAULT NULL COMMENT '微信的ID号',
  `user` varchar(30) NOT NULL COMMENT '分销商帐号',
  `password` varchar(32) NOT NULL COMMENT '分销商密码',
  `phone` varchar(11) DEFAULT NULL COMMENT '手机号码',
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
  `code` varchar(6) DEFAULT NULL COMMENT '分销商唯一验证码',
  `superior_code` varchar(6) DEFAULT NULL COMMENT '上级分销商唯一验证码',
  `superiors_code` varchar(6) DEFAULT NULL COMMENT '上上级分销商唯一验证码',
  `vendor_a` varchar(255) DEFAULT NULL COMMENT 'A级分销商邀请码',
  `vendor_b` varchar(255) DEFAULT NULL COMMENT 'B级分销商邀请码',
  `vendor_c` varchar(255) DEFAULT NULL COMMENT 'C级分销商邀请码',
  `vendor_user` varchar(255) DEFAULT NULL COMMENT '分销商邀请会员',
  `invitation_code` varchar(255) DEFAULT NULL COMMENT '分销商推荐人',
  `office_code` varchar(6) DEFAULT NULL COMMENT '分公司唯一ID',
  `abonus` double(6,2) unsigned NOT NULL DEFAULT '0.00',
  `auditing` varchar(30) DEFAULT NULL COMMENT '审核-责任人',
  `add_liable` varchar(30) DEFAULT NULL COMMENT '添加-责任人',
  `reviewed_describe` varchar(255) DEFAULT NULL COMMENT '审核描述',
  `reviewed` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '审核{0:省份审核,1:公司审核,2:协议审核 3，审核完成}',
  `status` tinyint(1) unsigned DEFAULT '0' COMMENT '状态{0:身份填写 1:公司信息填写 2:签协议 3:待审批 4:身份证审批失败 5:公司信息审批失败 6:协议审批失败  7：审批成功  8：禁用分销商}',
  `addtime` int(11) unsigned NOT NULL COMMENT '添加时间',
  `updatetime` int(11) unsigned NOT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of st_vendors
-- ----------------------------
INSERT INTO `st_vendors` VALUES ('1', null, 'admin', '202cb962ac59075b964b07152d234b70', '13425492760', '超级管理员', null, null, null, null, null, null, null, null, null, '0', null, null, null, null, null, null, null, '', null, '0.00', null, null, null, '0', '7', '1513872000', '1513872000');
INSERT INTO `st_vendors` VALUES ('9', null, 'wuzhibin', 'e10adc3949ba59abbe56e057f20f883e', '13425492760', '分公司', '440882198812045532', '/Vendors/2018-01-25/5a696cb0e9927.png', '/Vendors/2018-01-25/5a696cb0f09a4.png', '/Vendors/2018-01-25/5a696cb0f10c7.png', '点球电子科技', '020-5656256-123', '公司地址很长很长', '/Vendors/2018-01-25/5a696cb0f145d.png', '/Vendors/2018-01-25/5a696cb0f178b.png', '1', '53AB0F', null, null, 'gQFp8TwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAydmFWRms2MmJkOTAxMDAwME0wM2sAAgST9F5aAwQAAAAA', 'gQFI8TwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyZ2ROU2tPMmJkOTAxMDAwME0wM3oAAgST9F5aAwQAAAAA', 'gQE18TwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAySHN4ZGxfMmJkOTAxMDAwMDAwM2EAAgSf9V5aAwQAAAAA', 'gQFC8TwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyM0tfb2t1MmJkOTAxMDAwMGcwM1MAAgSf9V5aAwQAAAAA', null, null, '0.00', null, 'admin', null, '0', '7', '1516858544', '1516858544');
INSERT INTO `st_vendors` VALUES ('10', 'oQktJwL8ioR4DoxSQmikdzekbUyA', 'wuzhibinA', 'e10adc3949ba59abbe56e057f20f883e', '13425492760', '分公司下A级分销', '440882198812045535', '/Vendors/2018-01-25/5a696d6e883ad.png', '/Vendors/2018-01-25/5a696d6e889b2.png', '/Vendors/2018-01-25/5a696d6e88fea.png', '分公司A级分销', '020-8685856-12', '访问的时间段', '/Vendors/2018-01-25/5a696d89cc469.png', '/Vendors/2018-01-25/5a696d98633e7.docx', '2', '428777', null, null, 'gQEe8TwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAySWpOdGw4MmJkOTAxMDAwMDAwMzgAAgQf-l5aAwQAAAAA', 'gQG-8DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAySmlVQWxVMmJkOTAxMDAwMHcwM18AAgQf-l5aAwQAAAAA', 'gQFz8TwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyUWNHN2xMMmJkOTAxMDAwMGcwM1MAAgSXDGBaAwQAAAAA', 'gQHC8TwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyQ2NFUmtLMmJkOTAxMDAwMDAwMzIAAgSXDGBaAwQAAAAA', null, '53AB0F', '6000.00', 'admin', null, null, '3', '7', '1516858661', '1516959607');
INSERT INTO `st_vendors` VALUES ('11', 'oQktJwL8ioR4DoxSQmikdzekbUAA', 'WZBa_a', 'e10adc3949ba59abbe56e057f20f883e', '13425492760', 'A_A', '440882198812045532', '/Vendors/2018-01-26/5a6ab38f4fc69.png', '/Vendors/2018-01-26/5a6ab38f57004.png', '/Vendors/2018-01-26/5a6ab38f57653.png', '点球电子科技a_a', '020-5656256-123', '公司地址很长很长', '/Vendors/2018-01-26/5a6ab39b5ec99.png', '/Vendors/2018-01-26/5a6ab3a121c8f.png', '2', 'C571CE', null, null, 'gQFq8TwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAySkh1bGtRMmJkOTAxMDAwMDAwM0oAAgSaDWBaAwQAAAAA', 'gQHz8DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAydVNxUmttMmJkOTAxMDAwMGcwM1YAAgSaDWBaAwQAAAAA', 'gQEk8TwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyazV0X2xyMmJkOTAxMDAwMDAwM1oAAgSuDWBaAwQAAAAA', 'gQH28DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyMlV3Z2xpMmJkOTAxMDAwMGcwM2gAAgSuDWBaAwQAAAAA', '428777', '53AB0F', '0.00', 'admin', null, null, '3', '7', '1516942194', '1516945201');
INSERT INTO `st_vendors` VALUES ('12', 'oQktJwL8ioR4DoxSQmikdzekbUAB', 'w123wzA_B', 'e10adc3949ba59abbe56e057f20f883e', '13425492760', '分公司A_B', '440882198812045532', '/Vendors/2018-01-26/5a6abfee2d9a4.png', '/Vendors/2018-01-26/5a6abfee3106f.png', '/Vendors/2018-01-26/5a6abfee316e9.png', '点球电子科技', '020-5656256-123', '公司地址很长很长', '/Vendors/2018-01-26/5a6abff8ddfa8.png', '/Vendors/2018-01-26/5a6abffca6be1.png', '3', '1CE019', '428777', null, null, 'gQGP8DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyYjRpYmxxMmJkOTAxMDAwMDAwM3oAAgQ1DmBaAwQAAAAA', 'gQHT8DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyQkQ0T2tmMmJkOTAxMDAwMHcwM1IAAgQ2DmBaAwQAAAAA', 'gQG58DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAya1NSLWtZMmJkOTAxMDAwMHcwMzMAAgRIDmBaAwQAAAAA', '428777', '53AB0F', '7500.00', 'admin', null, null, '3', '7', '1516945360', '1516959607');
INSERT INTO `st_vendors` VALUES ('13', 'oQktJwL8ioR4DoxSQmikdzekbABC', 'wuzhibinA_B_C', 'e10adc3949ba59abbe56e057f20f883e', '13425492760', '吴智彬A_B_C', '440882198812045532', '/Vendors/2018-01-26/5a6ac6d73fb6e.png', '/Vendors/2018-01-26/5a6ac6d740181.png', '/Vendors/2018-01-26/5a6ac6d7407f9.png', '点球电子科技', '020-5656256-123', '公司地址很长很长', '/Vendors/2018-01-26/5a6ac6e0c510f.png', '/Vendors/2018-01-26/5a6ac6e914a21.png', '4', 'F552EA', '428777', '428777', null, null, 'gQFB8DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyZHl2bGt5MmJkOTAxMDAwME0wMzMAAgRIDmBaAwQAAAAA', 'gQHc8TwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyUllHZ2xVMmJkOTAxMDAwMDAwMzcAAgSNymZaAwQAAAAA', '428777', '53AB0F', '0.00', 'admin', null, null, '3', '7', '1516947143', '1516947311');
INSERT INTO `st_vendors` VALUES ('14', 'oQktJwL8ioR4DoxSQmikdzekbABCC', 'wuA_B_C', 'e10adc3949ba59abbe56e057f20f883e', '13425492760', '吴智彬A_B_CC', '440882198812045532', '/Vendors/2018-01-26/5a6ac9d65e8f3.png', '/Vendors/2018-01-26/5a6ac9d65ef70.png', '/Vendors/2018-01-26/5a6ac9d65f570.png', '点球abc', '020-5656256-123', '公司地址很长很长', '/Vendors/2018-01-26/5a6ac9e398798.png', '/Vendors/2018-01-26/5a6ac9e790db1.png', '4', '7352CF', '1CE019', '428777', null, null, 'gQHe8TwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyQjY1UGtOMmJkOTAxMDAwMHcwM1EAAgSOymZaAwQAAAAA', 'gQEL8TwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAybHVfcGtrMmJkOTAxMDAwMGcwM2QAAgSOymZaAwQAAAAA', '1CE019', '53AB0F', '1480.00', 'admin', null, null, '3', '7', '1516947909', '1516959607');

-- ----------------------------
-- Table structure for st_vendors_commission
-- ----------------------------
DROP TABLE IF EXISTS `st_vendors_commission`;
CREATE TABLE `st_vendors_commission` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增',
  `nexus_vendor` varchar(6) NOT NULL COMMENT '产生分成的关联分销商',
  `vendor_code` varchar(6) NOT NULL COMMENT '关联分销商唯一标识',
  `order_id` varchar(12) DEFAULT NULL COMMENT '订单号码',
  `abonus` double(6,2) unsigned DEFAULT '0.00' COMMENT '分红 默认为0.00',
  `current_abonus` double(6,2) unsigned DEFAULT '0.00' COMMENT '当前分红 默认为0.00',
  `describe` varchar(255) DEFAULT NULL COMMENT '佣金获得描述',
  `type` tinyint(1) unsigned DEFAULT '0' COMMENT '分配类型{0：直接会员，1：下级的会员，2：下下级的会员，3:同级下的会员}',
  `status` tinyint(1) unsigned DEFAULT '0' COMMENT '状态{0：启用，1：禁用}',
  `addtime` int(11) unsigned DEFAULT NULL COMMENT '创建时间',
  `updatetime` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `nexus_user` varchar(11) NOT NULL COMMENT '产生分成的关联分销商',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=342 DEFAULT CHARSET=gbk;

-- ----------------------------
-- Records of st_vendors_commission
-- ----------------------------
INSERT INTO `st_vendors_commission` VALUES ('242', '7352CF', '1CE019', '123456', '20.00', '20.00', '邀请的分销商吴智彬A_B_CC下的会员吴智彬-13425492760购买单号：123456的商品获得佣金奖励:20元', '3', '0', '1516951755', '1516951755', 'E8EDB67CF5F');
INSERT INTO `st_vendors_commission` VALUES ('243', '7352CF', '428777', '123456', '40.00', '40.00', '下下级分销商吴智彬A_B_CC下的会员吴智彬-13425492760购买单号：123456的商品获得佣金奖励:40元', '2', '0', '1516951755', '1516951755', 'E8EDB67CF5F');
INSERT INTO `st_vendors_commission` VALUES ('244', '7352CF', '1CE019', '123456', '30.00', '50.00', '下级分销商吴智彬A_B_CC下的会员吴智彬-13425492760购买单号：123456的商品获得佣金奖励:30元', '1', '0', '1516951755', '1516951755', 'E8EDB67CF5F');
INSERT INTO `st_vendors_commission` VALUES ('245', '7352CF', '7352CF', '123456', '10.00', '10.00', '分销商下的会员吴智彬-13425492760购买单号：123456的商品获得佣金奖励:10元', '0', '0', '1516951755', '1516951755', 'E8EDB67CF5F');
INSERT INTO `st_vendors_commission` VALUES ('246', '7352CF', '1CE019', '123456', '20.00', '70.00', '邀请的分销商吴智彬A_B_CC下的会员吴智彬-13425492760购买单号：123456的商品获得佣金奖励:20元', '3', '0', '1516951895', '1516951895', 'E8EDB67CF5F');
INSERT INTO `st_vendors_commission` VALUES ('247', '7352CF', '428777', '123456', '40.00', '80.00', '下下级分销商吴智彬A_B_CC下的会员吴智彬-13425492760购买单号：123456的商品获得佣金奖励:40元', '2', '0', '1516951895', '1516951895', 'E8EDB67CF5F');
INSERT INTO `st_vendors_commission` VALUES ('248', '7352CF', '1CE019', '123456', '30.00', '100.00', '下级分销商吴智彬A_B_CC下的会员吴智彬-13425492760购买单号：123456的商品获得佣金奖励:30元', '1', '0', '1516951895', '1516951895', 'E8EDB67CF5F');
INSERT INTO `st_vendors_commission` VALUES ('249', '7352CF', '7352CF', '123456', '10.00', '20.00', '分销商下的会员吴智彬-13425492760购买单号：123456的商品获得佣金奖励:10元', '0', '0', '1516951895', '1516951895', 'E8EDB67CF5F');
INSERT INTO `st_vendors_commission` VALUES ('250', '7352CF', '1CE019', '123456', '20.00', '120.00', '邀请的分销商吴智彬A_B_CC下的会员吴智彬-13425492760购买单号：123456的商品获得佣金奖励:20元', '3', '0', '1516952022', '1516952022', 'E8EDB67CF5F');
INSERT INTO `st_vendors_commission` VALUES ('251', '7352CF', '428777', '123456', '40.00', '120.00', '下下级分销商吴智彬A_B_CC下的会员吴智彬-13425492760购买单号：123456的商品获得佣金奖励:40元', '2', '0', '1516952022', '1516952022', 'E8EDB67CF5F');
INSERT INTO `st_vendors_commission` VALUES ('252', '7352CF', '1CE019', '123456', '30.00', '150.00', '下级分销商吴智彬A_B_CC下的会员吴智彬-13425492760购买单号：123456的商品获得佣金奖励:30元', '1', '0', '1516952022', '1516952022', 'E8EDB67CF5F');
INSERT INTO `st_vendors_commission` VALUES ('253', '7352CF', '7352CF', '123456', '10.00', '30.00', '分销商下的会员吴智彬-13425492760购买单号：123456的商品获得佣金奖励:10元', '0', '0', '1516952022', '1516952022', 'E8EDB67CF5F');
INSERT INTO `st_vendors_commission` VALUES ('254', '7352CF', '1CE019', '123456', '20.00', '170.00', '邀请的分销商吴智彬A_B_CC下的会员吴智彬-13425492760购买单号：123456的商品获得佣金奖励:20元', '3', '0', '1516952063', '1516952063', 'E8EDB67CF5F');
INSERT INTO `st_vendors_commission` VALUES ('255', '7352CF', '1CE019', '123456', '20.00', '170.00', '邀请的分销商吴智彬A_B_CC下的会员吴智彬-13425492760购买单号：123456的商品获得佣金奖励:20元', '3', '0', '1516952063', '1516952063', 'E8EDB67CF5F');
INSERT INTO `st_vendors_commission` VALUES ('256', '7352CF', '428777', '123456', '40.00', '160.00', '下下级分销商吴智彬A_B_CC下的会员吴智彬-13425492760购买单号：123456的商品获得佣金奖励:40元', '2', '0', '1516952063', '1516952063', 'E8EDB67CF5F');
INSERT INTO `st_vendors_commission` VALUES ('257', '7352CF', '1CE019', '123456', '20.00', '190.00', '邀请的分销商吴智彬A_B_CC下的会员吴智彬-13425492760购买单号：123456的商品获得佣金奖励:20元', '3', '0', '1516952063', '1516952063', 'E8EDB67CF5F');
INSERT INTO `st_vendors_commission` VALUES ('258', '7352CF', '428777', '123456', '40.00', '200.00', '下下级分销商吴智彬A_B_CC下的会员吴智彬-13425492760购买单号：123456的商品获得佣金奖励:40元', '2', '0', '1516952063', '1516952063', 'E8EDB67CF5F');
INSERT INTO `st_vendors_commission` VALUES ('259', '7352CF', '1CE019', '123456', '30.00', '220.00', '下级分销商吴智彬A_B_CC下的会员吴智彬-13425492760购买单号：123456的商品获得佣金奖励:30元', '1', '0', '1516952063', '1516952063', 'E8EDB67CF5F');
INSERT INTO `st_vendors_commission` VALUES ('260', '7352CF', '428777', '123456', '40.00', '200.00', '下下级分销商吴智彬A_B_CC下的会员吴智彬-13425492760购买单号：123456的商品获得佣金奖励:40元', '2', '0', '1516952063', '1516952063', 'E8EDB67CF5F');
INSERT INTO `st_vendors_commission` VALUES ('261', '7352CF', '1CE019', '123456', '30.00', '250.00', '下级分销商吴智彬A_B_CC下的会员吴智彬-13425492760购买单号：123456的商品获得佣金奖励:30元', '1', '0', '1516952063', '1516952063', 'E8EDB67CF5F');
INSERT INTO `st_vendors_commission` VALUES ('262', '7352CF', '7352CF', '123456', '10.00', '40.00', '分销商下的会员吴智彬-13425492760购买单号：123456的商品获得佣金奖励:10元', '0', '0', '1516952063', '1516952063', 'E8EDB67CF5F');
INSERT INTO `st_vendors_commission` VALUES ('263', '7352CF', '1CE019', '123456', '30.00', '250.00', '下级分销商吴智彬A_B_CC下的会员吴智彬-13425492760购买单号：123456的商品获得佣金奖励:30元', '1', '0', '1516952063', '1516952063', 'E8EDB67CF5F');
INSERT INTO `st_vendors_commission` VALUES ('264', '7352CF', '7352CF', '123456', '10.00', '40.00', '分销商下的会员吴智彬-13425492760购买单号：123456的商品获得佣金奖励:10元', '0', '0', '1516952063', '1516952063', 'E8EDB67CF5F');
INSERT INTO `st_vendors_commission` VALUES ('265', '7352CF', '7352CF', '123456', '10.00', '40.00', '分销商下的会员吴智彬-13425492760购买单号：123456的商品获得佣金奖励:10元', '0', '0', '1516952063', '1516952063', 'E8EDB67CF5F');
INSERT INTO `st_vendors_commission` VALUES ('266', '7352CF', '1CE019', '123456', '20.00', '270.00', '邀请的分销商吴智彬A_B_CC下的会员吴智彬-13425492760购买单号：123456的商品获得佣金奖励:20元', '3', '0', '1516952081', '1516952081', 'E8EDB67CF5F');
INSERT INTO `st_vendors_commission` VALUES ('267', '7352CF', '428777', '123456', '40.00', '240.00', '下下级分销商吴智彬A_B_CC下的会员吴智彬-13425492760购买单号：123456的商品获得佣金奖励:40元', '2', '0', '1516952081', '1516952081', 'E8EDB67CF5F');
INSERT INTO `st_vendors_commission` VALUES ('268', '7352CF', '1CE019', '123456', '30.00', '300.00', '下级分销商吴智彬A_B_CC下的会员吴智彬-13425492760购买单号：123456的商品获得佣金奖励:30元', '1', '0', '1516952082', '1516952082', 'E8EDB67CF5F');
INSERT INTO `st_vendors_commission` VALUES ('269', '7352CF', '7352CF', '123456', '10.00', '50.00', '分销商下的会员吴智彬-13425492760购买单号：123456的商品获得佣金奖励:10元', '0', '0', '1516952082', '1516952082', 'E8EDB67CF5F');
INSERT INTO `st_vendors_commission` VALUES ('270', '7352CF', '1CE019', '123456', '20.00', '320.00', '邀请的分销商吴智彬A_B_CC下的会员吴智彬-13425492760购买单号：123456的商品获得佣金奖励:20元', '3', '0', '1516952082', '1516952082', 'E8EDB67CF5F');
INSERT INTO `st_vendors_commission` VALUES ('271', '7352CF', '428777', '123456', '40.00', '280.00', '下下级分销商吴智彬A_B_CC下的会员吴智彬-13425492760购买单号：123456的商品获得佣金奖励:40元', '2', '0', '1516952082', '1516952082', 'E8EDB67CF5F');
INSERT INTO `st_vendors_commission` VALUES ('272', '7352CF', '1CE019', '123456', '20.00', '340.00', '邀请的分销商吴智彬A_B_CC下的会员吴智彬-13425492760购买单号：123456的商品获得佣金奖励:20元', '3', '0', '1516952082', '1516952082', 'E8EDB67CF5F');
INSERT INTO `st_vendors_commission` VALUES ('273', '7352CF', '1CE019', '123456', '30.00', '370.00', '下级分销商吴智彬A_B_CC下的会员吴智彬-13425492760购买单号：123456的商品获得佣金奖励:30元', '1', '0', '1516952082', '1516952082', 'E8EDB67CF5F');
INSERT INTO `st_vendors_commission` VALUES ('274', '7352CF', '428777', '123456', '40.00', '320.00', '下下级分销商吴智彬A_B_CC下的会员吴智彬-13425492760购买单号：123456的商品获得佣金奖励:40元', '2', '0', '1516952082', '1516952082', 'E8EDB67CF5F');
INSERT INTO `st_vendors_commission` VALUES ('275', '7352CF', '7352CF', '123456', '10.00', '60.00', '分销商下的会员吴智彬-13425492760购买单号：123456的商品获得佣金奖励:10元', '0', '0', '1516952082', '1516952082', 'E8EDB67CF5F');
INSERT INTO `st_vendors_commission` VALUES ('276', '7352CF', '1CE019', '123456', '30.00', '400.00', '下级分销商吴智彬A_B_CC下的会员吴智彬-13425492760购买单号：123456的商品获得佣金奖励:30元', '1', '0', '1516952082', '1516952082', 'E8EDB67CF5F');
INSERT INTO `st_vendors_commission` VALUES ('277', '7352CF', '7352CF', '123456', '10.00', '60.00', '分销商下的会员吴智彬-13425492760购买单号：123456的商品获得佣金奖励:10元', '0', '0', '1516952082', '1516952082', 'E8EDB67CF5F');
INSERT INTO `st_vendors_commission` VALUES ('278', '7352CF', '1CE019', '123456', '20.00', '420.00', '邀请的分销商吴智彬A_B_CC下的会员吴智彬-13425492760购买单号：123456的商品获得佣金奖励:20元', '3', '0', '1516952082', '1516952082', 'E8EDB67CF5F');
INSERT INTO `st_vendors_commission` VALUES ('279', '7352CF', '428777', '123456', '40.00', '360.00', '下下级分销商吴智彬A_B_CC下的会员吴智彬-13425492760购买单号：123456的商品获得佣金奖励:40元', '2', '0', '1516952082', '1516952082', 'E8EDB67CF5F');
INSERT INTO `st_vendors_commission` VALUES ('280', '7352CF', '1CE019', '123456', '30.00', '450.00', '下级分销商吴智彬A_B_CC下的会员吴智彬-13425492760购买单号：123456的商品获得佣金奖励:30元', '1', '0', '1516952082', '1516952082', 'E8EDB67CF5F');
INSERT INTO `st_vendors_commission` VALUES ('281', '7352CF', '7352CF', '123456', '10.00', '70.00', '分销商下的会员吴智彬-13425492760购买单号：123456的商品获得佣金奖励:10元', '0', '0', '1516952082', '1516952082', 'E8EDB67CF5F');
INSERT INTO `st_vendors_commission` VALUES ('282', '7352CF', '1CE019', '123456', '20.00', '470.00', '邀请的分销商吴智彬A_B_CC下的会员吴智彬-13425492760购买单号：123456的商品获得佣金奖励:20元', '3', '0', '1516952117', '1516952117', 'E8EDB67CF5F');
INSERT INTO `st_vendors_commission` VALUES ('283', '7352CF', '428777', '123456', '40.00', '400.00', '下下级分销商吴智彬A_B_CC下的会员吴智彬-13425492760购买单号：123456的商品获得佣金奖励:40元', '2', '0', '1516952117', '1516952117', 'E8EDB67CF5F');
INSERT INTO `st_vendors_commission` VALUES ('284', '7352CF', '1CE019', '123456', '30.00', '500.00', '下级分销商吴智彬A_B_CC下的会员吴智彬-13425492760购买单号：123456的商品获得佣金奖励:30元', '1', '0', '1516952117', '1516952117', 'E8EDB67CF5F');
INSERT INTO `st_vendors_commission` VALUES ('285', '7352CF', '7352CF', '123456', '10.00', '80.00', '分销商下的会员吴智彬-13425492760购买单号：123456的商品获得佣金奖励:10元', '0', '0', '1516952117', '1516952117', 'E8EDB67CF5F');
INSERT INTO `st_vendors_commission` VALUES ('286', '7352CF', '1CE019', '123456', '200.00', '700.00', '邀请的分销商吴智彬A_B_CC下的会员吴智彬-13425492760购买单号：123456的商品获得佣金奖励:200元', '3', '0', '1516952206', '1516952206', 'E8EDB67CF5F');
INSERT INTO `st_vendors_commission` VALUES ('287', '7352CF', '428777', '123456', '400.00', '800.00', '下下级分销商吴智彬A_B_CC下的会员吴智彬-13425492760购买单号：123456的商品获得佣金奖励:400元', '2', '0', '1516952206', '1516952206', 'E8EDB67CF5F');
INSERT INTO `st_vendors_commission` VALUES ('288', '7352CF', '1CE019', '123456', '300.00', '1000.00', '下级分销商吴智彬A_B_CC下的会员吴智彬-13425492760购买单号：123456的商品获得佣金奖励:300元', '1', '0', '1516952206', '1516952206', 'E8EDB67CF5F');
INSERT INTO `st_vendors_commission` VALUES ('289', '7352CF', '7352CF', '123456', '100.00', '180.00', '分销商下的会员吴智彬-13425492760购买单号：123456的商品获得佣金奖励:100元', '0', '0', '1516952206', '1516952206', 'E8EDB67CF5F');
INSERT INTO `st_vendors_commission` VALUES ('290', '7352CF', '1CE019', '123456', '200.00', '1200.00', '邀请的分销商吴智彬A_B_CC下的会员吴智彬-13425492760购买单号：123456的商品获得佣金奖励:200元', '3', '0', '1516952902', '1516952902', 'E8EDB67CF5F');
INSERT INTO `st_vendors_commission` VALUES ('291', '7352CF', '428777', '123456', '400.00', '1200.00', '下下级分销商吴智彬A_B_CC下的会员吴智彬-13425492760购买单号：123456的商品获得佣金奖励:400元', '2', '0', '1516952902', '1516952902', 'E8EDB67CF5F');
INSERT INTO `st_vendors_commission` VALUES ('292', '7352CF', '1CE019', '123456', '300.00', '1500.00', '下级分销商吴智彬A_B_CC下的会员吴智彬-13425492760购买单号：123456的商品获得佣金奖励:300元', '1', '0', '1516952903', '1516952903', 'E8EDB67CF5F');
INSERT INTO `st_vendors_commission` VALUES ('293', '7352CF', '7352CF', '123456', '100.00', '280.00', '分销商下的会员吴智彬-13425492760购买单号：123456的商品获得佣金奖励:100元', '0', '0', '1516952903', '1516952903', 'E8EDB67CF5F');
INSERT INTO `st_vendors_commission` VALUES ('294', '7352CF', '1CE019', '123456', '200.00', '1700.00', '邀请的分销商吴智彬A_B_CC下的会员吴智彬-13425492760购买单号：123456的商品获得佣金奖励:200元', '3', '0', '1516953004', '1516953004', 'E8EDB67CF5F');
INSERT INTO `st_vendors_commission` VALUES ('295', '7352CF', '428777', '123456', '400.00', '1600.00', '下下级分销商吴智彬A_B_CC下的会员吴智彬-13425492760购买单号：123456的商品获得佣金奖励:400元', '2', '0', '1516953004', '1516953004', 'E8EDB67CF5F');
INSERT INTO `st_vendors_commission` VALUES ('296', '7352CF', '1CE019', '123456', '300.00', '2000.00', '下级分销商吴智彬A_B_CC下的会员吴智彬-13425492760购买单号：123456的商品获得佣金奖励:300元', '1', '0', '1516953004', '1516953004', 'E8EDB67CF5F');
INSERT INTO `st_vendors_commission` VALUES ('297', '7352CF', '7352CF', '123456', '100.00', '380.00', '分销商下的会员吴智彬-13425492760购买单号：123456的商品获得佣金奖励:100元', '0', '0', '1516953004', '1516953004', 'E8EDB67CF5F');
INSERT INTO `st_vendors_commission` VALUES ('298', '7352CF', '1CE019', '123456', '200.00', '2200.00', '邀请的分销商吴智彬A_B_CC下的会员吴智彬-13425492760购买单号：123456的商品获得佣金奖励:200元', '3', '0', '1516953037', '1516953037', 'E8EDB67CF5F');
INSERT INTO `st_vendors_commission` VALUES ('299', '7352CF', '428777', '123456', '400.00', '2000.00', '下下级分销商吴智彬A_B_CC下的会员吴智彬-13425492760购买单号：123456的商品获得佣金奖励:400元', '2', '0', '1516953037', '1516953037', 'E8EDB67CF5F');
INSERT INTO `st_vendors_commission` VALUES ('300', '7352CF', '1CE019', '123456', '300.00', '2500.00', '下级分销商吴智彬A_B_CC下的会员吴智彬-13425492760购买单号：123456的商品获得佣金奖励:300元', '1', '0', '1516953037', '1516953037', 'E8EDB67CF5F');
INSERT INTO `st_vendors_commission` VALUES ('301', '7352CF', '7352CF', '123456', '100.00', '480.00', '分销商下的会员吴智彬-13425492760购买单号：123456的商品获得佣金奖励:100元', '0', '0', '1516953037', '1516953037', 'E8EDB67CF5F');
INSERT INTO `st_vendors_commission` VALUES ('302', '7352CF', '1CE019', '123456', '200.00', '2700.00', '邀请的分销商吴智彬A_B_CC下的会员吴智彬-13425492760购买单号：123456的商品获得佣金奖励:200元', '3', '0', '1516953082', '1516953082', 'E8EDB67CF5F');
INSERT INTO `st_vendors_commission` VALUES ('303', '7352CF', '428777', '123456', '400.00', '2400.00', '下下级分销商吴智彬A_B_CC下的会员吴智彬-13425492760购买单号：123456的商品获得佣金奖励:400元', '2', '0', '1516953082', '1516953082', 'E8EDB67CF5F');
INSERT INTO `st_vendors_commission` VALUES ('304', '7352CF', '1CE019', '123456', '300.00', '3000.00', '下级分销商吴智彬A_B_CC下的会员吴智彬-13425492760购买单号：123456的商品获得佣金奖励:300元', '1', '0', '1516953082', '1516953082', 'E8EDB67CF5F');
INSERT INTO `st_vendors_commission` VALUES ('305', '7352CF', '7352CF', '123456', '100.00', '580.00', '分销商下的会员吴智彬-13425492760购买单号：123456的商品获得佣金奖励:100元', '0', '0', '1516953082', '1516953082', 'E8EDB67CF5F');
INSERT INTO `st_vendors_commission` VALUES ('306', '7352CF', '1CE019', '123456', '200.00', '3200.00', '邀请的分销商吴智彬A_B_CC下的会员吴智彬-13425492760购买单号：123456的商品获得佣金奖励:200元', '3', '0', '1516953236', '1516953236', 'E8EDB67CF5F');
INSERT INTO `st_vendors_commission` VALUES ('307', '7352CF', '428777', '123456', '400.00', '2800.00', '下下级分销商吴智彬A_B_CC下的会员吴智彬-13425492760购买单号：123456的商品获得佣金奖励:400元', '2', '0', '1516953236', '1516953236', 'E8EDB67CF5F');
INSERT INTO `st_vendors_commission` VALUES ('308', '7352CF', '1CE019', '123456', '300.00', '3500.00', '下级分销商吴智彬A_B_CC下的会员吴智彬-13425492760购买单号：123456的商品获得佣金奖励:300元', '1', '0', '1516953236', '1516953236', 'E8EDB67CF5F');
INSERT INTO `st_vendors_commission` VALUES ('309', '7352CF', '7352CF', '123456', '100.00', '680.00', '分销商下的会员吴智彬-13425492760购买单号：123456的商品获得佣金奖励:100元', '0', '0', '1516953236', '1516953236', 'E8EDB67CF5F');
INSERT INTO `st_vendors_commission` VALUES ('310', '7352CF', '1CE019', '123456', '200.00', '3700.00', '邀请的分销商吴智彬A_B_CC下的会员吴智彬-13425492760购买单号：123456的商品获得佣金奖励:200元', '3', '0', '1516953747', '1516953747', 'E8EDB67CF5F');
INSERT INTO `st_vendors_commission` VALUES ('311', '7352CF', '428777', '123456', '400.00', '3200.00', '下下级分销商吴智彬A_B_CC下的会员吴智彬-13425492760购买单号：123456的商品获得佣金奖励:400元', '2', '0', '1516953747', '1516953747', 'E8EDB67CF5F');
INSERT INTO `st_vendors_commission` VALUES ('312', '7352CF', '1CE019', '123456', '300.00', '4000.00', '下级分销商吴智彬A_B_CC下的会员吴智彬-13425492760购买单号：123456的商品获得佣金奖励:300元', '1', '0', '1516953747', '1516953747', 'E8EDB67CF5F');
INSERT INTO `st_vendors_commission` VALUES ('313', '7352CF', '7352CF', '123456', '100.00', '780.00', '分销商下的会员吴智彬-13425492760购买单号：123456的商品获得佣金奖励:100元', '0', '0', '1516953747', '1516953747', 'E8EDB67CF5F');
INSERT INTO `st_vendors_commission` VALUES ('314', '7352CF', '1CE019', '123456', '200.00', '4200.00', '邀请的分销商吴智彬A_B_CC下的会员吴智彬-13425492760购买单号：123456的商品获得佣金奖励:200元', '3', '0', '1516953786', '1516953786', 'E8EDB67CF5F');
INSERT INTO `st_vendors_commission` VALUES ('315', '7352CF', '428777', '123456', '400.00', '3600.00', '下下级分销商吴智彬A_B_CC下的会员吴智彬-13425492760购买单号：123456的商品获得佣金奖励:400元', '2', '0', '1516953786', '1516953786', 'E8EDB67CF5F');
INSERT INTO `st_vendors_commission` VALUES ('316', '7352CF', '1CE019', '123456', '300.00', '4500.00', '下级分销商吴智彬A_B_CC下的会员吴智彬-13425492760购买单号：123456的商品获得佣金奖励:300元', '1', '0', '1516953786', '1516953786', 'E8EDB67CF5F');
INSERT INTO `st_vendors_commission` VALUES ('317', '7352CF', '7352CF', '123456', '100.00', '880.00', '分销商下的会员吴智彬-13425492760购买单号：123456的商品获得佣金奖励:100元', '0', '0', '1516953786', '1516953786', 'E8EDB67CF5F');
INSERT INTO `st_vendors_commission` VALUES ('318', '7352CF', '1CE019', '123456', '200.00', '4700.00', '邀请的分销商吴智彬A_B_CC下的会员吴智彬-13425492760购买单号：123456的商品获得佣金奖励:200元', '3', '0', '1516953905', '1516953905', 'E8EDB67CF5F');
INSERT INTO `st_vendors_commission` VALUES ('319', '7352CF', '428777', '123456', '400.00', '4000.00', '下下级分销商吴智彬A_B_CC下的会员吴智彬-13425492760购买单号：123456的商品获得佣金奖励:400元', '2', '0', '1516953905', '1516953905', 'E8EDB67CF5F');
INSERT INTO `st_vendors_commission` VALUES ('320', '7352CF', '1CE019', '123456', '300.00', '5000.00', '下级分销商吴智彬A_B_CC下的会员吴智彬-13425492760购买单号：123456的商品获得佣金奖励:300元', '1', '0', '1516953905', '1516953905', 'E8EDB67CF5F');
INSERT INTO `st_vendors_commission` VALUES ('321', '7352CF', '7352CF', '123456', '100.00', '980.00', '分销商下的会员吴智彬-13425492760购买单号：123456的商品获得佣金奖励:100元', '0', '0', '1516953905', '1516953905', 'E8EDB67CF5F');
INSERT INTO `st_vendors_commission` VALUES ('322', '7352CF', '1CE019', '123456', '200.00', '5200.00', '邀请的分销商吴智彬A_B_CC下的会员吴智彬-13425492760购买单号：123456的商品获得佣金奖励:200元', '3', '0', '1516953912', '1516953912', 'E8EDB67CF5F');
INSERT INTO `st_vendors_commission` VALUES ('323', '7352CF', '428777', '123456', '400.00', '4400.00', '下下级分销商吴智彬A_B_CC下的会员吴智彬-13425492760购买单号：123456的商品获得佣金奖励:400元', '2', '0', '1516953912', '1516953912', 'E8EDB67CF5F');
INSERT INTO `st_vendors_commission` VALUES ('324', '7352CF', '1CE019', '123456', '300.00', '5500.00', '下级分销商吴智彬A_B_CC下的会员吴智彬-13425492760购买单号：123456的商品获得佣金奖励:300元', '1', '0', '1516953912', '1516953912', 'E8EDB67CF5F');
INSERT INTO `st_vendors_commission` VALUES ('325', '7352CF', '7352CF', '123456', '100.00', '1080.00', '分销商下的会员吴智彬-13425492760购买单号：123456的商品获得佣金奖励:100元', '0', '0', '1516953912', '1516953912', 'E8EDB67CF5F');
INSERT INTO `st_vendors_commission` VALUES ('326', '7352CF', '1CE019', '123456', '200.00', '5700.00', '邀请的分销商吴智彬A_B_CC下的会员吴智彬-13425492760购买单号：123456的商品获得佣金奖励:200元', '3', '0', '1516953923', '1516953923', 'E8EDB67CF5F');
INSERT INTO `st_vendors_commission` VALUES ('327', '7352CF', '428777', '123456', '400.00', '4800.00', '下下级分销商吴智彬A_B_CC下的会员吴智彬-13425492760购买单号：123456的商品获得佣金奖励:400元', '2', '0', '1516953923', '1516953923', 'E8EDB67CF5F');
INSERT INTO `st_vendors_commission` VALUES ('328', '7352CF', '1CE019', '123456', '300.00', '6000.00', '下级分销商吴智彬A_B_CC下的会员吴智彬-13425492760购买单号：123456的商品获得佣金奖励:300元', '1', '0', '1516953923', '1516953923', 'E8EDB67CF5F');
INSERT INTO `st_vendors_commission` VALUES ('329', '7352CF', '7352CF', '123456', '100.00', '1180.00', '分销商下的会员吴智彬-13425492760购买单号：123456的商品获得佣金奖励:100元', '0', '0', '1516953923', '1516953923', 'E8EDB67CF5F');
INSERT INTO `st_vendors_commission` VALUES ('330', '7352CF', '1CE019', '123456', '200.00', '6200.00', '邀请的分销商吴智彬A_B_CC下的会员吴智彬-13425492760购买单号：123456的商品获得佣金奖励:200元', '3', '0', '1516953934', '1516953934', 'E8EDB67CF5F');
INSERT INTO `st_vendors_commission` VALUES ('331', '7352CF', '428777', '123456', '400.00', '5200.00', '下下级分销商吴智彬A_B_CC下的会员吴智彬-13425492760购买单号：123456的商品获得佣金奖励:400元', '2', '0', '1516953934', '1516953934', 'E8EDB67CF5F');
INSERT INTO `st_vendors_commission` VALUES ('332', '7352CF', '1CE019', '123456', '300.00', '6500.00', '下级分销商吴智彬A_B_CC下的会员吴智彬-13425492760购买单号：123456的商品获得佣金奖励:300元', '1', '0', '1516953934', '1516953934', 'E8EDB67CF5F');
INSERT INTO `st_vendors_commission` VALUES ('333', '7352CF', '7352CF', '123456', '100.00', '1280.00', '分销商下的会员吴智彬-13425492760购买单号：123456的商品获得佣金奖励:100元', '0', '0', '1516953934', '1516953934', 'E8EDB67CF5F');
INSERT INTO `st_vendors_commission` VALUES ('334', '7352CF', '1CE019', '123456', '200.00', '6700.00', '邀请的分销商吴智彬A_B_CC下的会员吴智彬-13425492760购买单号：123456的商品获得佣金奖励:200元', '3', '0', '1516955084', '1516955084', 'E8EDB67CF5F');
INSERT INTO `st_vendors_commission` VALUES ('335', '7352CF', '428777', '123456', '400.00', '5600.00', '下下级分销商吴智彬A_B_CC下的会员吴智彬-13425492760购买单号：123456的商品获得佣金奖励:400元', '2', '0', '1516955084', '1516955084', 'E8EDB67CF5F');
INSERT INTO `st_vendors_commission` VALUES ('336', '7352CF', '1CE019', '123456', '300.00', '7000.00', '下级分销商吴智彬A_B_CC下的会员吴智彬-13425492760购买单号：123456的商品获得佣金奖励:300元', '1', '0', '1516955084', '1516955084', 'E8EDB67CF5F');
INSERT INTO `st_vendors_commission` VALUES ('337', '7352CF', '7352CF', '123456', '100.00', '1380.00', '分销商下的会员吴智彬-13425492760购买单号：123456的商品获得佣金奖励:100元', '0', '0', '1516955084', '1516955084', 'E8EDB67CF5F');
INSERT INTO `st_vendors_commission` VALUES ('338', '7352CF', '1CE019', '123456', '200.00', '7200.00', '邀请的分销商吴智彬A_B_CC下的会员吴智彬-13425492760购买单号：123456的商品获得佣金奖励:200元', '3', '0', '1516959607', '1516959607', 'E8EDB67CF5F');
INSERT INTO `st_vendors_commission` VALUES ('339', '7352CF', '428777', '123456', '400.00', '6000.00', '下下级分销商吴智彬A_B_CC下的会员吴智彬-13425492760购买单号：123456的商品获得佣金奖励:400元', '2', '0', '1516959607', '1516959607', 'E8EDB67CF5F');
INSERT INTO `st_vendors_commission` VALUES ('340', '7352CF', '1CE019', '123456', '300.00', '7500.00', '下级分销商吴智彬A_B_CC下的会员吴智彬-13425492760购买单号：123456的商品获得佣金奖励:300元', '1', '0', '1516959607', '1516959607', 'E8EDB67CF5F');
INSERT INTO `st_vendors_commission` VALUES ('341', '7352CF', '7352CF', '123456', '100.00', '1480.00', '分销商下的会员吴智彬-13425492760购买单号：123456的商品获得佣金奖励:100元', '0', '0', '1516959607', '1516959607', 'E8EDB67CF5F');

-- ----------------------------
-- Table structure for st_wechat
-- ----------------------------
DROP TABLE IF EXISTS `st_wechat`;
CREATE TABLE `st_wechat` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `open_id` varchar(50) NOT NULL COMMENT '微信的ID号',
  `invitation_ticket` varchar(255) DEFAULT NULL COMMENT '受邀请二维码票据',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '用户类型{0:会员 1:分销商}',
  `leavel` tinyint(1) unsigned DEFAULT NULL COMMENT '分销商级别{0:A级分销商 1:B级分销商 2:C级分销商}',
  `action` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '操作{0:注册 1:登录}',
  `addtime` int(11) unsigned NOT NULL COMMENT '创建时间',
  `updatetime` int(11) unsigned NOT NULL COMMENT '更新时间',
  `recommend` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '注册类型{0:会员直接注册 1:会员推荐会员 2：分销商推荐会员 3：分公司推荐会员 4:分公司邀请分销商 5:分销商邀请分销商}',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=547 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of st_wechat
-- ----------------------------
INSERT INTO `st_wechat` VALUES ('516', 'oQktJwI8cu9_dU7_kenwyhhYduwA', 'gQHw7zwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyVmpPNWtGMmJkOTAxQ3hmaTFxMXoAAgRxympaAwQwhScA', '0', null, '0', '1516953901', '1516953901', '1');
INSERT INTO `st_wechat` VALUES ('517', 'oQktJwLCJ3YzYnaz-Z5FIcPvllzo', null, '0', null, '0', '1516953938', '1516953938', '0');
INSERT INTO `st_wechat` VALUES ('518', 'oQktJwH4pvWGmyzQW5Pw2QlsgoMA', null, '0', null, '1', '1516954176', '1516954176', '0');
INSERT INTO `st_wechat` VALUES ('519', 'oQktJwJnzFES9JDZ_mdEb5YcPke4', null, '0', null, '0', '1516954205', '1516954205', '0');
INSERT INTO `st_wechat` VALUES ('520', 'oQktJwB9nb6JvNXwl5zWp8VhBEV4', null, '0', null, '0', '1516954385', '1516954385', '0');
INSERT INTO `st_wechat` VALUES ('521', 'oQktJwD_CG9l5LGbDU7Wf0RuEbg0', null, '0', null, '0', '1516954388', '1516954388', '0');
INSERT INTO `st_wechat` VALUES ('522', 'oQktJwP9lcJA_z5hNK3oYzoHlFLE', null, '0', null, '0', '1516954465', '1516954465', '0');
INSERT INTO `st_wechat` VALUES ('523', 'oQktJwMN2XEgnVPPLoLX4v5ogMB8', null, '0', null, '0', '1516954674', '1516954674', '0');
INSERT INTO `st_wechat` VALUES ('524', 'oQktJwEQta10FtyC9TejH8gLPzrI', null, '0', null, '0', '1516955784', '1516955784', '0');
INSERT INTO `st_wechat` VALUES ('525', 'oQktJwIWnouiVZV7ks9OqtDWg_kI', null, '0', null, '1', '1516955912', '1516955912', '0');
INSERT INTO `st_wechat` VALUES ('526', 'oQktJwOg4MgSWV2vvkeYXWLRhPLE', null, '0', null, '1', '1516955976', '1516955976', '0');
INSERT INTO `st_wechat` VALUES ('527', 'oQktJwEdqGd1nycK3BCVMm2y_5lI', null, '0', null, '0', '1516956091', '1516956091', '0');
INSERT INTO `st_wechat` VALUES ('528', 'oQktJwCnohpog1iVGHuX4GsQnn5I', null, '0', null, '0', '1516956192', '1516956192', '0');
INSERT INTO `st_wechat` VALUES ('529', 'oQktJwHnLzw1q7lAoMg5Pis89cZI', null, '0', null, '0', '1516956646', '1516956646', '0');
INSERT INTO `st_wechat` VALUES ('530', 'oQktJwDcehCKazprwaD6cCBi_gMQ', null, '0', null, '1', '1516956749', '1516956749', '0');
INSERT INTO `st_wechat` VALUES ('531', 'oQktJwKuKTuoVLGX46RA1wG9Q0dk', null, '0', null, '0', '1516956933', '1516956933', '0');
INSERT INTO `st_wechat` VALUES ('532', 'oQktJwEAyaS7KadNV0KYSStQN7Go', null, '0', null, '0', '1516957292', '1516957292', '0');
INSERT INTO `st_wechat` VALUES ('533', 'oQktJwFlFLD07FF0E5qsKEnnljgY', null, '0', null, '1', '1516957620', '1516957620', '0');
INSERT INTO `st_wechat` VALUES ('534', 'oQktJwKn6We-LT7EVXYmFjcPA6Lg', null, '0', null, '1', '1516957797', '1516957797', '0');
INSERT INTO `st_wechat` VALUES ('535', 'oQktJwM_1gtIGWFOr2J9IWNGTbOE', null, '0', null, '1', '1516957900', '1516957900', '0');
INSERT INTO `st_wechat` VALUES ('536', 'oQktJwDE_7cCU9rns6vxSFSTz-6s', null, '0', null, '0', '1516958589', '1516958589', '0');
INSERT INTO `st_wechat` VALUES ('537', 'oQktJwAxpRl83nGeNg5eFQlmb2t4', null, '0', null, '1', '1516958681', '1516958681', '0');
INSERT INTO `st_wechat` VALUES ('538', 'oQktJwJNaHdSZDpd5vZELuZWvRlA', null, '0', null, '1', '1516958690', '1516958690', '0');
INSERT INTO `st_wechat` VALUES ('539', 'oQktJwDcehCKazprwaD6cCBi_gMQ', null, '0', null, '0', '1516959251', '1516959251', '0');
INSERT INTO `st_wechat` VALUES ('540', 'oQktJwFhla2OPZsqMAqZNewMhAr4', null, '0', null, '0', '1516959793', '1516959793', '0');
INSERT INTO `st_wechat` VALUES ('541', 'oQktJwANOmLv2R0cjuqMvgTN1VDU', null, '0', null, '0', '1516960144', '1516960144', '0');
INSERT INTO `st_wechat` VALUES ('542', 'oQktJwGFr7CF9pGrNyXSsIZcfn_0', null, '0', null, '0', '1516960901', '1516960901', '0');
INSERT INTO `st_wechat` VALUES ('543', 'oQktJwBwqrUJtl8Zl-cmZAqZtux8', null, '0', null, '0', '1516961328', '1516961328', '0');
INSERT INTO `st_wechat` VALUES ('544', 'oQktJwCnns-UplUc-YgdzXYohyb8', null, '0', null, '0', '1516961425', '1516961425', '0');
INSERT INTO `st_wechat` VALUES ('545', 'oQktJwPZVJBAOD_sd6WOsnzU_rrs', null, '0', null, '0', '1516961572', '1516961572', '0');
INSERT INTO `st_wechat` VALUES ('546', 'oQktJwLcyHkc3xwRdgaHN7Dpjp70', null, '0', null, '0', '1516961623', '1516961623', '0');

-- ----------------------------
-- Table structure for st_work
-- ----------------------------
DROP TABLE IF EXISTS `st_work`;
CREATE TABLE `st_work` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '用户id',
  `uid` int(11) NOT NULL COMMENT '用户id',
  `dw_uid` int(11) DEFAULT NULL COMMENT '处理人',
  `title` varchar(30) NOT NULL COMMENT '工单标题',
  `number` varchar(50) NOT NULL COMMENT '工单编号',
  `name` varchar(50) DEFAULT NULL COMMENT '处理人',
  `phone` varchar(11) DEFAULT '' COMMENT '处理人电话',
  `type` tinyint(1) NOT NULL COMMENT '工单类型(0：安装 1：维修 2：维护)',
  `content` text NOT NULL COMMENT '维护内容',
  `address` varchar(50) NOT NULL COMMENT '地址',
  `result` tinyint(1) DEFAULT '0' COMMENT '处理结果(0：未处理 1：正在处理 2：已处理)',
  `time` varchar(30) DEFAULT '' COMMENT '处理时间',
  `create_at` varchar(30) NOT NULL,
  `update_at` varchar(30) DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `dw_uid` (`dw_uid`)
) ENGINE=MyISAM AUTO_INCREMENT=24113 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of st_work
-- ----------------------------
INSERT INTO `st_work` VALUES ('24112', '1', null, '陈', 'hgR20180120114596815', null, '', '0', '显示器在城工珠援助佣兵 显示器在城工珠援助佣兵物城 显示器在城工珠援助佣兵物城 显示器在城工珠援助佣兵物城 显示器在城工珠援助佣兵物城 显示器在城工珠援助佣兵物城 显示器在城工珠援助佣兵物城 显示器在城工珠援助佣兵物城 显示器在城工珠援助佣兵物城 显示器在城工珠援助佣兵物城 显示器在城工珠援助佣兵物城 显示器在城工珠援助佣兵物城 显示器在城工珠援助佣兵物城 显示器在城工珠援助佣兵物城物城', '北京市北京市市辖区东城区', '2', '', '1516419920', '');
