/*
Navicat MySQL Data Transfer

Source Server         : wuzhibin.cn
Source Server Version : 50173
Source Host           : sdm359768304.my3w.com:3306
Source Database       : sdm359768304_db

Target Server Type    : MYSQL
Target Server Version : 50173
File Encoding         : 65001

Date: 2018-01-25 11:07:33
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
  `user` int(11) unsigned NOT NULL DEFAULT '0',
  `inviter` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '会员邀请人分配占比',
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
INSERT INTO `st_commission` VALUES ('1', '60', '40', '40', '30', '20', '10', '0', '0');

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
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8;

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
INSERT INTO `st_dimension_num` VALUES ('1', '41', '1');

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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

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
  `abonus` int(11) unsigned NOT NULL DEFAULT '0',
  `auditing` varchar(30) DEFAULT NULL COMMENT '审核-责任人',
  `add_liable` varchar(30) DEFAULT NULL COMMENT '添加-责任人',
  `status` tinyint(1) unsigned DEFAULT '0' COMMENT '状态{0:身份填写 1:公司信息填写 2:签协议 3:待审批 4:身份证审批失败 5:公司信息审批失败 6:协议审批失败  7：审批成功  8：禁用分销商}',
  `addtime` int(11) unsigned NOT NULL COMMENT '添加时间',
  `updatetime` int(11) unsigned NOT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of st_vendors
-- ----------------------------
INSERT INTO `st_vendors` VALUES ('1', null, 'admin', '202cb962ac59075b964b07152d234b70', '13425492760', '超级管理员', null, null, null, null, null, null, null, null, null, '0', null, null, null, null, null, null, null, '', null, '0', null, null, '7', '1513872000', '1513872000');
INSERT INTO `st_vendors` VALUES ('6', null, 'wuzhibin03', 'e10adc3949ba59abbe56e057f20f883e', '13425492760', '吴智彬', '440882198812045532', '/Vendors/2018-01-20/5a62f96d7e718.png', '/Vendors/2018-01-20/5a62f96d7f254.png', '/Vendors/2018-01-20/5a62f96d7fe79.png', '点球电子科技', '020-5656256-123', '公司地址很长很长', '/Vendors/2018-01-20/5a62f96d807d4.png', '/Vendors/2018-01-20/5a62f96d81105.png', '1', '5A6704', null, null, 'gQGK8DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAySml2TmtHMmJkOTAxMDAwMHcwM04AAgRU8F5aAwQAAAAA', 'gQF78DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyV3h0VmxYMmJkOTAxMDAwMDAwM3AAAgRU8F5aAwQAAAAA', 'gQGW8DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyWFlZeWxJMmJkOTAxMDAwMGcwMzUAAgRf815aAwQAAAAA', 'gQG28DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyUk10WmswMmJkOTAxMDAwMHcwMzUAAgRf815aAwQAAAAA', null, null, '0', null, null, '5', '1516435821', '1516435821');
INSERT INTO `st_vendors` VALUES ('7', null, 'dew123dwer', 'e10adc3949ba59abbe56e057f20f883e', '13425492760', '分公司123', '440882198812045532', '/Vendors/2018-01-22/5a654a5c6632b.png', '/Vendors/2018-01-22/5a654a5c6edf2.png', '/Vendors/2018-01-22/5a654a5c6f9f7.png', '点球电子科技', '020-5656256-123', '公司地址很长很长', '/Vendors/2018-01-22/5a654a5c702dd.png', '/Vendors/2018-01-22/5a654a5c70bcf.png', '1', '273345', null, null, 'gQHu8DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyRl8ySmxCMmJkOTAxMDAwMDAwM2wAAgRw815aAwQAAAAA', 'gQEZ8DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAya2tEWGxzMmJkOTAxMDAwMHcwM3cAAgRx815aAwQAAAAA', 'gQE28TwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyNXNldGw4MmJkOTAxMDAwMHcwM0YAAgSx815aAwQAAAAA', 'gQGL8TwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyOU1NNGtBMmJkOTAxMDAwMHcwM2MAAgSx815aAwQAAAAA', null, null, '0', null, 'admin', '7', '1516587612', '1516587612');
INSERT INTO `st_vendors` VALUES ('8', null, 'wuzhibin0311', 'e10adc3949ba59abbe56e057f20f883e', '13425492760', '分公司1231', '440882198812045532', '/Vendors/2018-01-22/5a65823c0ff07.png', '/Vendors/2018-01-22/5a65823c107e5.png', '/Vendors/2018-01-22/5a65823c11309.png', '点球电子科技', '020-5656256-123', '公司地址很长很长', '/Vendors/2018-01-22/5a65823c11be0.png', '/Vendors/2018-01-22/5a65823c17e55.docx', '1', '27B3B0', null, null, 'gQFA8TwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyVU9aa2swMmJkOTAxMDAwME0wMzUAAgQC9F5aAwQAAAAA', 'gQHL8DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyR2l1Y2t0MmJkOTAxMDAwMDAwM2QAAgQC9F5aAwQAAAAA', 'gQFS8DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyU21rZWw2MmJkOTAxMDAwMGcwM3MAAgRq9F5aAwQAAAAA', 'gQEZ8DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyemNUZWx4MmJkOTAxMDAwMHcwM1QAAgRq9F5aAwQAAAAA', null, null, '0', null, 'admin', '7', '1516601916', '1516601916');

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
) ENGINE=InnoDB AUTO_INCREMENT=322 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of st_wechat
-- ----------------------------
INSERT INTO `st_wechat` VALUES ('50', 'oQktJwFa11iPVrtwYriQWwNy5fg8', null, '0', null, '0', '1516434407', '1516434407', '0');
INSERT INTO `st_wechat` VALUES ('51', 'oQktJwDXdYF68zhcEgHmJWYMmK7g', null, '0', null, '0', '1516434719', '1516434719', '0');
INSERT INTO `st_wechat` VALUES ('52', 'oQktJwHJ6GrHINObEujPn7vWPX-c', null, '0', null, '0', '1516434779', '1516434779', '0');
INSERT INTO `st_wechat` VALUES ('53', 'oQktJwHzuqE97TtwPKFpelKBvH8U', null, '0', null, '0', '1516435231', '1516435231', '0');
INSERT INTO `st_wechat` VALUES ('54', 'oQktJwPh18NVZ-gqaLN3PRU4876w', null, '0', null, '0', '1516435524', '1516435524', '0');
INSERT INTO `st_wechat` VALUES ('55', 'oQktJwErm7JPy1vrTuRYu-zikpBo', null, '0', null, '0', '1516436849', '1516436849', '0');
INSERT INTO `st_wechat` VALUES ('56', 'oQktJwCONjgx-NB6psmz_JMZlykQ', null, '0', null, '0', '1516437257', '1516437257', '0');
INSERT INTO `st_wechat` VALUES ('57', 'oQktJwAn_7eRKlUVclBU3YsdaSGE', null, '0', null, '0', '1516438277', '1516438277', '0');
INSERT INTO `st_wechat` VALUES ('58', 'oQktJwPohfFkSOZxvtJvMF23LWEc', null, '0', null, '0', '1516438360', '1516438360', '0');
INSERT INTO `st_wechat` VALUES ('59', 'oQktJwKvskS57eI9tmE4n4-18s8Y', null, '0', null, '0', '1516438426', '1516438426', '0');
INSERT INTO `st_wechat` VALUES ('60', 'oQktJwEIrm8k4tY08nxw9YksuchU', null, '0', null, '0', '1516438584', '1516438584', '0');
INSERT INTO `st_wechat` VALUES ('61', 'oQktJwAyS6-NPGGmUNQZeNjANH0o', null, '0', null, '0', '1516438672', '1516438672', '0');
INSERT INTO `st_wechat` VALUES ('62', 'oQktJwK78JzrMECJUoGuXnhfw7TE', null, '0', null, '0', '1516439735', '1516439735', '0');
INSERT INTO `st_wechat` VALUES ('63', 'oQktJwIabVaCH5fF8NDq2HCMNFIU', null, '0', null, '0', '1516439828', '1516439828', '0');
INSERT INTO `st_wechat` VALUES ('64', 'oQktJwJ8lDJxbbTO98kxYvUmkNK0', null, '0', null, '0', '1516439957', '1516439957', '0');
INSERT INTO `st_wechat` VALUES ('65', 'oQktJwNBdfy08QKOLvfJZMjjnroY', null, '0', null, '0', '1516439975', '1516439975', '0');
INSERT INTO `st_wechat` VALUES ('66', 'oQktJwJQCqMjD2JgpACAqyKTen7g', null, '0', null, '0', '1516440074', '1516440074', '0');
INSERT INTO `st_wechat` VALUES ('67', 'oQktJwM1n0oyVmARY3IjPjzGrILw', null, '0', null, '0', '1516440181', '1516440181', '0');
INSERT INTO `st_wechat` VALUES ('68', 'oQktJwPBU_yTKFUD4ZX6UXBQJNGg', null, '0', null, '0', '1516440331', '1516440331', '0');
INSERT INTO `st_wechat` VALUES ('69', 'oQktJwB7sD8_J0sXT4ZvHa09GpUw', null, '0', null, '0', '1516440413', '1516440413', '0');
INSERT INTO `st_wechat` VALUES ('70', 'oQktJwNwsroL9e77G8dD_L4OijsM', null, '0', null, '0', '1516440553', '1516440553', '0');
INSERT INTO `st_wechat` VALUES ('71', 'oQktJwMz_zPnlDCC3hq-a2k34gVc', null, '0', null, '0', '1516440685', '1516440685', '0');
INSERT INTO `st_wechat` VALUES ('72', 'oQktJwDg-i5LA7xN5Qh2KErEv4SU', null, '0', null, '0', '1516440777', '1516440777', '0');
INSERT INTO `st_wechat` VALUES ('73', 'oQktJwE5fq9Ed8KcvbmEx0hBjxYM', null, '0', null, '0', '1516440977', '1516440977', '0');
INSERT INTO `st_wechat` VALUES ('74', 'oQktJwOzScipkQDLoxZuCpFAJrdI', null, '0', null, '0', '1516441128', '1516441128', '0');
INSERT INTO `st_wechat` VALUES ('75', 'oQktJwC4i1rvteaT9CdAqTtanU9s', null, '0', null, '0', '1516441309', '1516441309', '0');
INSERT INTO `st_wechat` VALUES ('76', 'oQktJwGYFFa7svuj5PFpPIuqNdXA', null, '0', null, '0', '1516441858', '1516441858', '0');
INSERT INTO `st_wechat` VALUES ('77', 'oQktJwCkNiv_TrZq-T5znd8E7WEs', null, '0', null, '0', '1516441994', '1516441994', '0');
INSERT INTO `st_wechat` VALUES ('78', 'oQktJwA-w2326x-2IdQCdTsbR4ds', null, '0', null, '0', '1516442018', '1516442018', '0');
INSERT INTO `st_wechat` VALUES ('79', 'oQktJwJ3A4QZ7_3kbn0OdNLkPjok', null, '0', null, '0', '1516442117', '1516442117', '0');
INSERT INTO `st_wechat` VALUES ('80', 'oQktJwGo4TzDRKxZI1_Sg-p8n1Aw', null, '0', null, '0', '1516442562', '1516442562', '0');
INSERT INTO `st_wechat` VALUES ('81', 'oQktJwFkoko2HtOnJRQb7ddXeDos', null, '0', null, '0', '1516442956', '1516442956', '0');
INSERT INTO `st_wechat` VALUES ('82', 'oQktJwJAC-mz4xYhtFKmSYlsySho', null, '0', null, '0', '1516443939', '1516443939', '0');
INSERT INTO `st_wechat` VALUES ('83', 'oQktJwKF7_besbawY3xqItSrNjOU', null, '0', null, '0', '1516445267', '1516445267', '0');
INSERT INTO `st_wechat` VALUES ('84', 'oQktJwHMsxQxUPxV1M-E_mfZ63zg', null, '0', null, '0', '1516445346', '1516445346', '0');
INSERT INTO `st_wechat` VALUES ('85', 'oQktJwCpGsjWAtbkD7zYlklVn9d4', null, '0', null, '0', '1516445386', '1516445386', '0');
INSERT INTO `st_wechat` VALUES ('86', 'oQktJwJRektUGb-soV5rx2WjqJlY', null, '0', null, '0', '1516445491', '1516445491', '0');
INSERT INTO `st_wechat` VALUES ('87', 'oQktJwFh2ZIuLEBvBn9eouzAv5nc', null, '0', null, '0', '1516445649', '1516445649', '0');
INSERT INTO `st_wechat` VALUES ('88', 'oQktJwAmzbdKYoiaAfUVRiu2VK2U', null, '0', null, '0', '1516446488', '1516446488', '0');
INSERT INTO `st_wechat` VALUES ('89', 'oQktJwAVosp3HzpJ7X3UhQYMQSmc', null, '0', null, '0', '1516446991', '1516446991', '0');
INSERT INTO `st_wechat` VALUES ('90', 'oQktJwI88FWKU7nbZhlAWMWg-n4I', null, '0', null, '0', '1516447110', '1516447110', '0');
INSERT INTO `st_wechat` VALUES ('91', 'oQktJwNdR_3LNM2f1KMlV7wSaJcE', null, '0', null, '0', '1516447172', '1516447172', '0');
INSERT INTO `st_wechat` VALUES ('92', 'oQktJwB9DERVgbD2hKCzFK0z8oa8', null, '0', null, '0', '1516448560', '1516448560', '0');
INSERT INTO `st_wechat` VALUES ('93', 'oQktJwOxWqoDQstLOE5axcwffX4U', null, '0', null, '0', '1516448999', '1516448999', '0');
INSERT INTO `st_wechat` VALUES ('94', 'oQktJwBevwRfgiB7HlmjlzTBrNC4', null, '0', null, '0', '1516449591', '1516449591', '0');
INSERT INTO `st_wechat` VALUES ('95', 'oQktJwJI5vrgtto4Ybg1xISXBw9M', null, '0', null, '0', '1516449784', '1516449784', '0');
INSERT INTO `st_wechat` VALUES ('96', 'oQktJwEtaohcPe7ndak3OxzI9C7M', null, '0', null, '0', '1516449808', '1516449808', '0');
INSERT INTO `st_wechat` VALUES ('97', 'oQktJwAi7gihm2I1Hi7c3RcCehII', null, '0', null, '0', '1516449938', '1516449938', '0');
INSERT INTO `st_wechat` VALUES ('98', 'oQktJwHoKlpBgEv2ElOrmiZWlSi4', null, '0', null, '0', '1516451148', '1516451148', '0');
INSERT INTO `st_wechat` VALUES ('99', 'oQktJwMGQUzzat4pNVqDD9auaw9U', null, '0', null, '0', '1516451413', '1516451413', '0');
INSERT INTO `st_wechat` VALUES ('100', 'oQktJwJ544PJDZOEG9RFONKlhXBE', null, '0', null, '0', '1516452728', '1516452728', '0');
INSERT INTO `st_wechat` VALUES ('101', 'oQktJwC4B1ire5CEBOGlAf-8h6YM', null, '0', null, '0', '1516453430', '1516453430', '0');
INSERT INTO `st_wechat` VALUES ('102', 'oQktJwITcP-zC-qH-5CIYCnsU21k', null, '0', null, '0', '1516453936', '1516453936', '0');
INSERT INTO `st_wechat` VALUES ('103', 'oQktJwFlcgHm8XlgPqRioztXGDQc', null, '0', null, '0', '1516454276', '1516454276', '0');
INSERT INTO `st_wechat` VALUES ('104', 'oQktJwDZbaAhBGLOG67SZcDAzdmM', null, '0', null, '0', '1516455168', '1516455168', '0');
INSERT INTO `st_wechat` VALUES ('105', 'oQktJwOM8xNgRNTuD_utl_P1rpmQ', null, '0', null, '0', '1516456212', '1516456212', '0');
INSERT INTO `st_wechat` VALUES ('106', 'oQktJwCzN7f8QHYXdc-N3sB_gLYw', null, '0', null, '0', '1516456577', '1516456577', '0');
INSERT INTO `st_wechat` VALUES ('107', 'oQktJwIzHlfi4HLICXdqaX_LT4Ok', null, '0', null, '0', '1516458325', '1516458325', '0');
INSERT INTO `st_wechat` VALUES ('108', 'oQktJwBZRR39KxHug3h2KMWDRRNw', null, '0', null, '0', '1516458511', '1516458511', '0');
INSERT INTO `st_wechat` VALUES ('109', 'oQktJwNKdnb46r5cu-y0PStIscBc', null, '0', null, '0', '1516459730', '1516459730', '0');
INSERT INTO `st_wechat` VALUES ('110', 'oQktJwAaF0BcR9R3sw2kQlOBT57Q', null, '0', null, '0', '1516487046', '1516487046', '0');
INSERT INTO `st_wechat` VALUES ('111', 'oQktJwAoaWm8ds42cPjAn9XkZ1uk', null, '0', null, '0', '1516491409', '1516491409', '0');
INSERT INTO `st_wechat` VALUES ('112', 'oQktJwOiLH04gxCv5u09u7tbUu8I', null, '0', null, '0', '1516493528', '1516493528', '0');
INSERT INTO `st_wechat` VALUES ('113', 'oQktJwJ3YzUl05XXvAkh4koxee9s', null, '0', null, '0', '1516493790', '1516493790', '0');
INSERT INTO `st_wechat` VALUES ('114', 'oQktJwCr2dV7EEUBP2X7Y1qY2-3M', null, '0', null, '0', '1516495541', '1516495541', '0');
INSERT INTO `st_wechat` VALUES ('115', 'oQktJwH6Ut11UBSwFvzt6eyfMDwo', null, '0', null, '0', '1516497569', '1516497569', '0');
INSERT INTO `st_wechat` VALUES ('116', 'oQktJwJyZ5DKW_JNCwdoULw4x1vo', null, '0', null, '0', '1516498728', '1516498728', '0');
INSERT INTO `st_wechat` VALUES ('117', 'oQktJwNgkRefUFlEf3FDqMIu-zCg', null, '0', null, '0', '1516500312', '1516500312', '0');
INSERT INTO `st_wechat` VALUES ('118', 'oQktJwAyhNcM73LYAsNrmKFxmgyg', null, '0', null, '0', '1516500624', '1516500624', '0');
INSERT INTO `st_wechat` VALUES ('119', 'oQktJwAeSrs0LJaSt7nxMDwlsbI8', null, '0', null, '0', '1516500780', '1516500780', '0');
INSERT INTO `st_wechat` VALUES ('120', 'oQktJwBzdC284b-I2-Up6pkOYFzk', null, '0', null, '0', '1516501895', '1516501895', '0');
INSERT INTO `st_wechat` VALUES ('121', 'oQktJwPR0BWmvXldrdZHale4ZT1k', null, '0', null, '0', '1516502500', '1516502500', '0');
INSERT INTO `st_wechat` VALUES ('122', 'oQktJwOpCjKlxPW1Irdxwf0ICEiE', null, '0', null, '0', '1516502678', '1516502678', '0');
INSERT INTO `st_wechat` VALUES ('123', 'oQktJwFWk5oVnnVBgsOuU0tvYIGU', null, '0', null, '0', '1516503010', '1516503010', '0');
INSERT INTO `st_wechat` VALUES ('124', 'oQktJwFgqD0GgHf1XeVpCeHo-mwg', null, '0', null, '0', '1516503041', '1516503041', '0');
INSERT INTO `st_wechat` VALUES ('125', 'oQktJwHDbfNgM1BJIiByXIaMib-U', null, '0', null, '0', '1516503321', '1516503321', '0');
INSERT INTO `st_wechat` VALUES ('126', 'oQktJwLjS4Cm-A_JqB89rxlnAAZ8', null, '0', null, '0', '1516503669', '1516503669', '0');
INSERT INTO `st_wechat` VALUES ('127', 'oQktJwFFtQ3iPK3cUIY2lqLA_OkM', null, '0', null, '0', '1516504031', '1516504031', '0');
INSERT INTO `st_wechat` VALUES ('128', 'oQktJwC42l-Ll-oqJPcs-ndJLBus', null, '0', null, '0', '1516504487', '1516504487', '0');
INSERT INTO `st_wechat` VALUES ('129', 'oQktJwCv_bf4RxreBIA6U6CKH28c', null, '0', null, '0', '1516504515', '1516504515', '0');
INSERT INTO `st_wechat` VALUES ('130', 'oQktJwHF_4LhitpZDotpviLYvXJk', null, '0', null, '0', '1516505216', '1516505216', '0');
INSERT INTO `st_wechat` VALUES ('131', 'oQktJwGwkvyJKTNlGixxIh_9XzdI', null, '0', null, '0', '1516505227', '1516505227', '0');
INSERT INTO `st_wechat` VALUES ('132', 'oQktJwJsX20p_0yqIWsjk3er-2v0', null, '0', null, '0', '1516505234', '1516505234', '0');
INSERT INTO `st_wechat` VALUES ('133', 'oQktJwMJAAdDArSlDPc4uwaYvp80', null, '0', null, '0', '1516505277', '1516505277', '0');
INSERT INTO `st_wechat` VALUES ('134', 'oQktJwDbW_J_Q0XUDegf6Ii9STjs', null, '0', null, '0', '1516505623', '1516505623', '0');
INSERT INTO `st_wechat` VALUES ('135', 'oQktJwIsy1iC0V98FDvXognhPJtg', null, '0', null, '0', '1516505840', '1516505840', '0');
INSERT INTO `st_wechat` VALUES ('136', 'oQktJwJ4j4c9QA3MrZKhIKxFstH0', null, '0', null, '0', '1516505918', '1516505918', '0');
INSERT INTO `st_wechat` VALUES ('137', 'oQktJwHQ_RdtMWMEspsMtHHzOaME', null, '0', null, '0', '1516506279', '1516506279', '0');
INSERT INTO `st_wechat` VALUES ('138', 'oQktJwI9PuMPuV53_XVgewRbQ9AA', null, '0', null, '0', '1516506753', '1516506753', '0');
INSERT INTO `st_wechat` VALUES ('139', 'oQktJwO1MA2NybRQO9jIJFS88DNE', null, '0', null, '0', '1516506882', '1516506882', '0');
INSERT INTO `st_wechat` VALUES ('140', 'oQktJwJONbwp0VN5M-NbLHThQbSE', null, '0', null, '0', '1516507018', '1516507018', '0');
INSERT INTO `st_wechat` VALUES ('141', 'oQktJwHn2bmlZPQmFSPyUmGWDlmQ', null, '0', null, '0', '1516508331', '1516508331', '0');
INSERT INTO `st_wechat` VALUES ('142', 'oQktJwDZ2zBTdtshM6Tu0qPJpVaQ', null, '0', null, '0', '1516508425', '1516508425', '0');
INSERT INTO `st_wechat` VALUES ('143', 'oQktJwAKgqsqCKUrvhcBnArke9kE', null, '0', null, '0', '1516508473', '1516508473', '0');
INSERT INTO `st_wechat` VALUES ('144', 'oQktJwFeOIZ00H9O0Q46K_g3ZFk8', null, '0', null, '0', '1516509123', '1516509123', '0');
INSERT INTO `st_wechat` VALUES ('145', 'oQktJwNnL43HiTUPr3Mv3HFFZEZQ', null, '0', null, '0', '1516509681', '1516509681', '0');
INSERT INTO `st_wechat` VALUES ('146', 'oQktJwJoHEffnhH-1M7-g8LdqKzg', null, '0', null, '0', '1516510164', '1516510164', '0');
INSERT INTO `st_wechat` VALUES ('147', 'oQktJwJN8NuIDCPgIMJNQ_WCz7RQ', null, '0', null, '0', '1516510450', '1516510450', '0');
INSERT INTO `st_wechat` VALUES ('148', 'oQktJwGPTX3Z26y8vdu0lpPmT9K8', null, '0', null, '0', '1516512473', '1516512473', '0');
INSERT INTO `st_wechat` VALUES ('149', 'oQktJwIWxK3okHydqde9Uueouqb4', null, '0', null, '0', '1516516738', '1516516738', '0');
INSERT INTO `st_wechat` VALUES ('150', 'oQktJwFNprWCe6pGhJYiQupwXONw', null, '0', null, '0', '1516516749', '1516516749', '0');
INSERT INTO `st_wechat` VALUES ('151', 'oQktJwBO-IM9Vv7ZBGI91ZyqoBdQ', null, '0', null, '0', '1516516822', '1516516822', '0');
INSERT INTO `st_wechat` VALUES ('152', 'oQktJwI1i7yZqSdj3MfMQT3KzYNU', null, '0', null, '0', '1516516910', '1516516910', '0');
INSERT INTO `st_wechat` VALUES ('153', 'oQktJwM76JcS6CWPDSwhRggpcwhM', null, '0', null, '0', '1516517108', '1516517108', '0');
INSERT INTO `st_wechat` VALUES ('154', 'oQktJwHfVd2AyVtDZRudgVLXW04I', null, '0', null, '0', '1516518066', '1516518066', '0');
INSERT INTO `st_wechat` VALUES ('155', 'oQktJwJeCErlshoRR1XjVCzJMXbE', null, '0', null, '0', '1516518976', '1516518976', '0');
INSERT INTO `st_wechat` VALUES ('156', 'oQktJwNK5O3M3IEx9eG-Llvawzxo', null, '0', null, '0', '1516519666', '1516519666', '0');
INSERT INTO `st_wechat` VALUES ('157', 'oQktJwDpvzhI_4sWVL_NKQEuiiuo', null, '0', null, '0', '1516519723', '1516519723', '0');
INSERT INTO `st_wechat` VALUES ('158', 'oQktJwKF5zwx9vrpnk8ywCS7RRws', null, '0', null, '0', '1516519735', '1516519735', '0');
INSERT INTO `st_wechat` VALUES ('159', 'oQktJwOhYg5Su6eqTwVgDmRm_Y7w', null, '0', null, '0', '1516519737', '1516519737', '0');
INSERT INTO `st_wechat` VALUES ('160', 'oQktJwLkIyZwEBIac_T8HzK52E2k', null, '0', null, '0', '1516519956', '1516519956', '0');
INSERT INTO `st_wechat` VALUES ('161', 'oQktJwCdjjJxHqmoe7rUUt12iryI', null, '0', null, '0', '1516520991', '1516520991', '0');
INSERT INTO `st_wechat` VALUES ('162', 'oQktJwCpUe-1uBnQ9HOMZImQN_pw', null, '0', null, '0', '1516521204', '1516521204', '0');
INSERT INTO `st_wechat` VALUES ('163', 'oQktJwGkF2dsMIMKLyqXbXiLfy2Y', null, '0', null, '0', '1516522080', '1516522080', '0');
INSERT INTO `st_wechat` VALUES ('164', 'oQktJwBW0yMaBSclYWkLpflX9bvM', null, '0', null, '0', '1516522970', '1516522970', '0');
INSERT INTO `st_wechat` VALUES ('165', 'oQktJwM5YL568vuJ-69ReLXxteAo', null, '0', null, '0', '1516522992', '1516522992', '0');
INSERT INTO `st_wechat` VALUES ('166', 'oQktJwGTdm6Vd5M8t4mtsvxlp1Oo', null, '0', null, '0', '1516523004', '1516523004', '0');
INSERT INTO `st_wechat` VALUES ('167', 'oQktJwJtM_vWzq_m3dG9ONF0J7JE', null, '0', null, '0', '1516523018', '1516523018', '0');
INSERT INTO `st_wechat` VALUES ('168', 'oQktJwNqE317_VMcVRazGgvNsp74', null, '0', null, '0', '1516523097', '1516523097', '0');
INSERT INTO `st_wechat` VALUES ('169', 'oQktJwDLL4-WzK9sVnlYc9XOxwpY', null, '0', null, '0', '1516523801', '1516523801', '0');
INSERT INTO `st_wechat` VALUES ('170', 'oQktJwGzUdKe_Enntv27-Mq-orNQ', null, '0', null, '0', '1516524210', '1516524210', '0');
INSERT INTO `st_wechat` VALUES ('171', 'oQktJwHUhcNq48IdTn2rrzIntpZM', null, '0', null, '0', '1516524267', '1516524267', '0');
INSERT INTO `st_wechat` VALUES ('172', 'oQktJwFhsibKHaUfKIx1kOBDdM2M', null, '0', null, '0', '1516524822', '1516524822', '0');
INSERT INTO `st_wechat` VALUES ('173', 'oQktJwLVPVr9axGSfigv01M-XpgM', null, '0', null, '0', '1516526607', '1516526607', '0');
INSERT INTO `st_wechat` VALUES ('174', 'oQktJwFCA4K2xhNE_4nQQ-02mVnA', null, '0', null, '0', '1516527009', '1516527009', '0');
INSERT INTO `st_wechat` VALUES ('175', 'oQktJwPK9KJmDIeq5f3KX2EKUbiw', null, '0', null, '0', '1516527765', '1516527765', '0');
INSERT INTO `st_wechat` VALUES ('176', 'oQktJwCmOMqE4kwDssa5L2aVx0T4', null, '0', null, '0', '1516529795', '1516529795', '0');
INSERT INTO `st_wechat` VALUES ('177', 'oQktJwMwETZLz-OKSbw5h2CzuYMM', null, '0', null, '0', '1516529807', '1516529807', '0');
INSERT INTO `st_wechat` VALUES ('178', 'oQktJwENOZhuteiONzF4jg441FLs', null, '0', null, '0', '1516529839', '1516529839', '0');
INSERT INTO `st_wechat` VALUES ('179', 'oQktJwENOZhuteiONzF4jg441FLs', null, '0', null, '0', '1516530075', '1516530075', '0');
INSERT INTO `st_wechat` VALUES ('180', 'oQktJwG9ZBAoxsibELJ3r-usqxr0', null, '0', null, '0', '1516530110', '1516530110', '0');
INSERT INTO `st_wechat` VALUES ('181', 'oQktJwAn5J1Ayw8Cy-WlYcKpFO8U', null, '0', null, '0', '1516530121', '1516530121', '0');
INSERT INTO `st_wechat` VALUES ('182', 'oQktJwNb1s8PbEQ43ExyDu0yUdCI', null, '0', null, '0', '1516531025', '1516531025', '0');
INSERT INTO `st_wechat` VALUES ('183', 'oQktJwFTLT6Zif_4jh61RTDDICqk', null, '0', null, '0', '1516531193', '1516531193', '0');
INSERT INTO `st_wechat` VALUES ('184', 'oQktJwB5S9IkxFagu8gzkItbqpzM', null, '0', null, '0', '1516531654', '1516531654', '0');
INSERT INTO `st_wechat` VALUES ('185', 'oQktJwNNCW4II-1hvNg49Y9T3zeM', null, '0', null, '0', '1516531706', '1516531706', '0');
INSERT INTO `st_wechat` VALUES ('186', 'oQktJwOo2yvJKxgDeFGq2B8XWt-0', null, '0', null, '0', '1516531984', '1516531984', '0');
INSERT INTO `st_wechat` VALUES ('187', 'oQktJwGWvUeuHZ0HIobWmJ-0cVSw', null, '0', null, '0', '1516533193', '1516533193', '0');
INSERT INTO `st_wechat` VALUES ('188', 'oQktJwLjyRk1htotvKs3bD9T1CXE', null, '0', null, '0', '1516534105', '1516534105', '0');
INSERT INTO `st_wechat` VALUES ('189', 'oQktJwC_6HeZ_2iD1NsOssefVrJU', null, '0', null, '0', '1516535069', '1516535069', '0');
INSERT INTO `st_wechat` VALUES ('190', 'oQktJwPFa9vv6oUuzMzJFppPUIYk', null, '0', null, '0', '1516535623', '1516535623', '0');
INSERT INTO `st_wechat` VALUES ('191', 'oQktJwGskXJg2k0K4zUBpXEtO89I', null, '0', null, '0', '1516535740', '1516535740', '0');
INSERT INTO `st_wechat` VALUES ('192', 'oQktJwO2RYXBNR4WV3RWiT5ofcas', null, '0', null, '0', '1516536468', '1516536468', '0');
INSERT INTO `st_wechat` VALUES ('193', 'oQktJwM0AiGajIHqozdfYlQsAAdg', null, '0', null, '0', '1516537048', '1516537048', '0');
INSERT INTO `st_wechat` VALUES ('194', 'oQktJwN9AxhGjJYI1DBSIatXrkCc', null, '0', null, '0', '1516537106', '1516537106', '0');
INSERT INTO `st_wechat` VALUES ('195', 'oQktJwJ-Mkxl_IDWPTLOgRJJVsdE', null, '0', null, '0', '1516537134', '1516537134', '0');
INSERT INTO `st_wechat` VALUES ('196', 'oQktJwBpD79zdUT5PNu4VAa5w-vA', null, '0', null, '0', '1516537423', '1516537423', '0');
INSERT INTO `st_wechat` VALUES ('197', 'oQktJwDHx-MWrXlqo6ed64YQy4Dw', null, '0', null, '0', '1516538347', '1516538347', '0');
INSERT INTO `st_wechat` VALUES ('198', 'oQktJwAx6bTCw95ZWfBU_3pu7kSQ', null, '0', null, '0', '1516538428', '1516538428', '0');
INSERT INTO `st_wechat` VALUES ('199', 'oQktJwG4bHdGwh1_kJMO7J1dvlOU', null, '0', null, '0', '1516538457', '1516538457', '0');
INSERT INTO `st_wechat` VALUES ('200', 'oQktJwMxXXdY4Y8QxIjpLKS8dtiI', null, '0', null, '0', '1516538573', '1516538573', '0');
INSERT INTO `st_wechat` VALUES ('201', 'oQktJwE1p9qJg06DDhog91KE-42U', null, '0', null, '0', '1516538660', '1516538660', '0');
INSERT INTO `st_wechat` VALUES ('202', 'oQktJwHiwIOygl42H81vRw1VbyP4', null, '0', null, '0', '1516539899', '1516539899', '0');
INSERT INTO `st_wechat` VALUES ('203', 'oQktJwCnBPz1qeEcgbGaVs_kv0ZY', null, '0', null, '0', '1516540326', '1516540326', '0');
INSERT INTO `st_wechat` VALUES ('204', 'oQktJwOfWmfCLjGKxKx1Xe9rruFM', null, '0', null, '0', '1516541277', '1516541277', '0');
INSERT INTO `st_wechat` VALUES ('205', 'oQktJwHLlBVhJIqc2X4jAa8J68Eo', null, '0', null, '0', '1516543760', '1516543760', '0');
INSERT INTO `st_wechat` VALUES ('206', 'oQktJwBo6E6kXi5YYOK8GGMdN8u8', null, '0', null, '0', '1516544654', '1516544654', '0');
INSERT INTO `st_wechat` VALUES ('207', 'oQktJwONEnL_jTQqjLtY9YvQzjYM', null, '0', null, '0', '1516544846', '1516544846', '0');
INSERT INTO `st_wechat` VALUES ('208', 'oQktJwEs1qtozw7wVwvUzWTsyJZQ', null, '0', null, '0', '1516548338', '1516548338', '0');
INSERT INTO `st_wechat` VALUES ('209', 'oQktJwPQDFSBthUJ0LcSsCuYtqyM', null, '0', null, '0', '1516574142', '1516574142', '0');
INSERT INTO `st_wechat` VALUES ('210', 'oQktJwDU8k8EZo7kMxv3LabdcjZM', null, '0', null, '0', '1516577418', '1516577418', '0');
INSERT INTO `st_wechat` VALUES ('211', 'oQktJwFYdGY--qKe6dR_gWoyOkBU', null, '0', null, '0', '1516580927', '1516580927', '0');
INSERT INTO `st_wechat` VALUES ('212', 'oQktJwNNjLW3tp2JkqckHu7JTPpo', null, '0', null, '0', '1516581569', '1516581569', '0');
INSERT INTO `st_wechat` VALUES ('213', 'oQktJwN5tYfHU16VorTj5eFKJrdM', null, '0', null, '0', '1516583799', '1516583799', '0');
INSERT INTO `st_wechat` VALUES ('214', 'oQktJwDWcFHovMi-415kinbGrXD4', null, '0', null, '0', '1516585146', '1516585146', '0');
INSERT INTO `st_wechat` VALUES ('215', 'oQktJwKg8IPBS1Fqoyl1saBRJm5c', null, '0', null, '0', '1516585422', '1516585422', '0');
INSERT INTO `st_wechat` VALUES ('216', 'oQktJwN95swNr9xORb3qJK4TR320', null, '0', null, '0', '1516585486', '1516585486', '0');
INSERT INTO `st_wechat` VALUES ('217', 'oQktJwLLvmC8gH_1Xg4fJ1_daqzM', null, '0', null, '0', '1516585617', '1516585617', '0');
INSERT INTO `st_wechat` VALUES ('218', 'oQktJwMJF4hwENgnr0_mhV05mYXw', null, '0', null, '0', '1516585966', '1516585966', '0');
INSERT INTO `st_wechat` VALUES ('219', 'oQktJwHl6ZRXAIyinkvDw9kw2WfU', null, '0', null, '0', '1516586390', '1516586390', '0');
INSERT INTO `st_wechat` VALUES ('220', 'oQktJwMFsrH_0ZLqkI4IIzJ3myxU', null, '0', null, '0', '1516586890', '1516586890', '0');
INSERT INTO `st_wechat` VALUES ('221', 'oQktJwCRmFShMyTM1XPlFsw99q9E', null, '0', null, '0', '1516587812', '1516587812', '0');
INSERT INTO `st_wechat` VALUES ('222', 'oQktJwHO-qou6xc1TS2bUpvuFlss', null, '0', null, '0', '1516590032', '1516590032', '0');
INSERT INTO `st_wechat` VALUES ('223', 'oQktJwGFFPfg0Lq4zSTW2dYRNcCQ', null, '0', null, '0', '1516590172', '1516590172', '0');
INSERT INTO `st_wechat` VALUES ('224', 'oQktJwA2jBt5qb7yJ1k_SrpSxoMI', null, '0', null, '0', '1516590429', '1516590429', '0');
INSERT INTO `st_wechat` VALUES ('225', 'oQktJwD5wdt1xtSeG9e8xhDgP5hA', null, '0', null, '0', '1516590504', '1516590504', '0');
INSERT INTO `st_wechat` VALUES ('226', 'oQktJwLbpEKmcVd7nlKfVS19YqJM', null, '0', null, '0', '1516590994', '1516590994', '0');
INSERT INTO `st_wechat` VALUES ('227', 'oQktJwEzKqqh3DXB9r4AcRSv4Cww', null, '0', null, '0', '1516591335', '1516591335', '0');
INSERT INTO `st_wechat` VALUES ('228', 'oQktJwHtVQ2PUjBIngBwk8detWMg', null, '0', null, '0', '1516591521', '1516591521', '0');
INSERT INTO `st_wechat` VALUES ('229', 'oQktJwKFpGpvlXIzEiWfH3kkDkbI', null, '0', null, '0', '1516591525', '1516591525', '0');
INSERT INTO `st_wechat` VALUES ('230', 'oQktJwLUR6WnU0xBt6gTEq4RCf_s', null, '0', null, '0', '1516591549', '1516591549', '0');
INSERT INTO `st_wechat` VALUES ('231', 'oQktJwJPKz6l2zglfN26lSIT9i6A', null, '0', null, '0', '1516592013', '1516592013', '0');
INSERT INTO `st_wechat` VALUES ('232', 'oQktJwOJyhV96x2W_v3j1zQGs4VA', null, '0', null, '0', '1516592177', '1516592177', '0');
INSERT INTO `st_wechat` VALUES ('233', 'oQktJwLztpe9Kl6BSmZbitFVzxLU', null, '0', null, '0', '1516592465', '1516592465', '0');
INSERT INTO `st_wechat` VALUES ('234', 'oQktJwOjHIwYNvskYJi5CLUyUnzc', null, '0', null, '0', '1516593415', '1516593415', '0');
INSERT INTO `st_wechat` VALUES ('235', 'oQktJwMjeQpCTqhqy6jUoMmpNveU', null, '0', null, '0', '1516594232', '1516594232', '0');
INSERT INTO `st_wechat` VALUES ('236', 'oQktJwC5wibZYgMv_1-85uOmAs18', null, '0', null, '0', '1516594243', '1516594243', '0');
INSERT INTO `st_wechat` VALUES ('237', 'oQktJwCUM0REH6BUv-97nd2Oj3dI', null, '0', null, '0', '1516594249', '1516594249', '0');
INSERT INTO `st_wechat` VALUES ('238', 'oQktJwJQDY84vj4D66LHdmzaL5ko', null, '0', null, '0', '1516594646', '1516594646', '0');
INSERT INTO `st_wechat` VALUES ('239', 'oQktJwLARJNaLmQIcINdXdd4xFzk', null, '0', null, '0', '1516595182', '1516595182', '0');
INSERT INTO `st_wechat` VALUES ('240', 'oQktJwD0cNyYZtpuosG_OhJe1lCc', null, '0', null, '0', '1516595521', '1516595521', '0');
INSERT INTO `st_wechat` VALUES ('241', 'oQktJwLWmZRvoKVcnbS8lqLOZvH0', null, '0', null, '0', '1516596632', '1516596632', '0');
INSERT INTO `st_wechat` VALUES ('242', 'oQktJwAjArLYfd0K5xCeRTTWWqRs', null, '0', null, '0', '1516596868', '1516596868', '0');
INSERT INTO `st_wechat` VALUES ('243', 'oQktJwAzGym74B_dDu3uSzpJvus0', null, '0', null, '0', '1516596870', '1516596870', '0');
INSERT INTO `st_wechat` VALUES ('244', 'oQktJwO5QxEgrlXfO0n1T0Nb_xco', null, '0', null, '0', '1516597229', '1516597229', '0');
INSERT INTO `st_wechat` VALUES ('245', 'oQktJwAGzBxJT4_GbV4qONZ0QAv8', null, '0', null, '0', '1516597773', '1516597773', '0');
INSERT INTO `st_wechat` VALUES ('246', 'oQktJwLgWEpQ3pKFz6c2DmQDZ8kk', null, '0', null, '0', '1516597836', '1516597836', '0');
INSERT INTO `st_wechat` VALUES ('247', 'oQktJwKf8r8w_hp_FHmOzY2cpFX8', null, '0', null, '0', '1516597909', '1516597909', '0');
INSERT INTO `st_wechat` VALUES ('248', 'oQktJwDv23ZZoz6qBqhSNvLiz2Sw', null, '0', null, '0', '1516598322', '1516598322', '0');
INSERT INTO `st_wechat` VALUES ('249', 'oQktJwArZO7Y9aRpU2zNtLav7SB4', null, '0', null, '0', '1516598328', '1516598328', '0');
INSERT INTO `st_wechat` VALUES ('250', 'oQktJwGmoczAaJpGwexkn9OzV6II', null, '0', null, '0', '1516599229', '1516599229', '0');
INSERT INTO `st_wechat` VALUES ('251', 'oQktJwNnGW8-4lXe3BiCSbF20MZc', null, '0', null, '0', '1516599428', '1516599428', '0');
INSERT INTO `st_wechat` VALUES ('252', 'oQktJwC7NMUQPlxW_y7-UT2z-9mw', null, '0', null, '0', '1516599448', '1516599448', '0');
INSERT INTO `st_wechat` VALUES ('253', 'oQktJwL-A3UJDvhF2pcluSkozTQs', null, '0', null, '0', '1516599649', '1516599649', '0');
INSERT INTO `st_wechat` VALUES ('254', 'oQktJwKM5xZNHpEF05vtK8ntkWQ8', null, '0', null, '0', '1516599684', '1516599684', '0');
INSERT INTO `st_wechat` VALUES ('255', 'oQktJwOcFB91CDMYbqS_UCNtGSnk', null, '0', null, '0', '1516599731', '1516599731', '0');
INSERT INTO `st_wechat` VALUES ('256', 'oQktJwLAEyJa0DLzpQPnpwkgf9TM', null, '0', null, '0', '1516601307', '1516601307', '0');
INSERT INTO `st_wechat` VALUES ('257', 'oQktJwEOmKeWY9zhDtfaHMTMUScU', null, '0', null, '0', '1516601498', '1516601498', '0');
INSERT INTO `st_wechat` VALUES ('258', 'oQktJwP43oW3hGNk8RCRwD7JOh94', null, '0', null, '0', '1516601688', '1516601688', '0');
INSERT INTO `st_wechat` VALUES ('259', 'oQktJwImCjVxAWPOFt49Y_-bAIn8', null, '0', null, '0', '1516602165', '1516602165', '0');
INSERT INTO `st_wechat` VALUES ('260', 'oQktJwDRDQjEt6V6MVTc0G1MRQ54', null, '0', null, '0', '1516602431', '1516602431', '0');
INSERT INTO `st_wechat` VALUES ('261', 'oQktJwB-r-DpdXV7D48vauSEy2Mo', null, '0', null, '0', '1516602532', '1516602532', '0');
INSERT INTO `st_wechat` VALUES ('262', 'oQktJwOKQFoBppL-t6XpEZhCliUE', null, '0', null, '0', '1516602586', '1516602586', '0');
INSERT INTO `st_wechat` VALUES ('263', 'oQktJwNKKkDerhHtnoceIoHVdR0Y', null, '0', null, '0', '1516603253', '1516603253', '0');
INSERT INTO `st_wechat` VALUES ('264', 'oQktJwBBOl4tLspxvDtWOpD30fk8', null, '0', null, '0', '1516604147', '1516604147', '0');
INSERT INTO `st_wechat` VALUES ('265', 'oQktJwMbXENyLebUfUXbO5nU0P5s', null, '0', null, '0', '1516605559', '1516605559', '0');
INSERT INTO `st_wechat` VALUES ('266', 'oQktJwLVPNOuh_iI7iwcsQS75i_4', null, '0', null, '0', '1516606736', '1516606736', '0');
INSERT INTO `st_wechat` VALUES ('267', 'oQktJwPyVXLUhtS3sZoNRZeHDePc', null, '0', null, '0', '1516608107', '1516608107', '0');
INSERT INTO `st_wechat` VALUES ('268', 'oQktJwPOCn9QUWfu33vMCGHvFc_Y', null, '0', null, '0', '1516608519', '1516608519', '0');
INSERT INTO `st_wechat` VALUES ('269', 'oQktJwMYgCuT3W1iK8XGcptZ_5QQ', null, '0', null, '0', '1516608546', '1516608546', '0');
INSERT INTO `st_wechat` VALUES ('270', 'oQktJwCSlgcmzWTev5AJmoDg49zc', null, '0', null, '0', '1516609030', '1516609030', '0');
INSERT INTO `st_wechat` VALUES ('271', 'oQktJwNsoKabLt6s4288yQksuERw', null, '0', null, '0', '1516609668', '1516609668', '0');
INSERT INTO `st_wechat` VALUES ('272', 'oQktJwPr1m0vOmiLa5cBSHxvflsI', null, '0', null, '0', '1516609714', '1516609714', '0');
INSERT INTO `st_wechat` VALUES ('273', 'oQktJwB1CPlVS9Rlpy9e8yxay0T8', null, '0', null, '0', '1516610303', '1516610303', '0');
INSERT INTO `st_wechat` VALUES ('274', 'oQktJwMTHydIvohf5mYp7am0RTew', null, '0', null, '0', '1516784322', '1516784322', '0');
INSERT INTO `st_wechat` VALUES ('275', 'oQktJwLKxFzol_N6wnjTpyCG1_Fo', null, '0', null, '0', '1516784603', '1516784603', '0');
INSERT INTO `st_wechat` VALUES ('276', 'oQktJwJWUeehvobbcane1bU85izA', null, '0', null, '0', '1516784710', '1516784710', '0');
INSERT INTO `st_wechat` VALUES ('277', 'oQktJwD_W56pRaRkO0fAfgYRDPyY', null, '0', null, '0', '1516785799', '1516785799', '0');
INSERT INTO `st_wechat` VALUES ('278', 'oQktJwMXwuveOVcvl4JONaRuCSMk', null, '0', null, '0', '1516786314', '1516786314', '0');
INSERT INTO `st_wechat` VALUES ('279', 'oQktJwDlh0I0xXIi8AosgnUlX6c8', null, '0', null, '0', '1516786620', '1516786620', '0');
INSERT INTO `st_wechat` VALUES ('280', 'oQktJwKI1fVtYTtu8amP7fzJosQc', null, '0', null, '0', '1516788110', '1516788110', '0');
INSERT INTO `st_wechat` VALUES ('281', 'oQktJwKKYeOAlJVHpfcBKtPFfYYE', null, '0', null, '0', '1516789718', '1516789718', '0');
INSERT INTO `st_wechat` VALUES ('282', 'oQktJwBlNPRUxjVxamx-kkIOUFAc', null, '0', null, '0', '1516794215', '1516794215', '0');
INSERT INTO `st_wechat` VALUES ('283', 'oQktJwNQWRpQMwrg10CvK-9VOpq4', null, '0', null, '0', '1516794887', '1516794887', '0');
INSERT INTO `st_wechat` VALUES ('284', 'oQktJwD_02FbSekpvJJqNlYHipf0', null, '0', null, '0', '1516794970', '1516794970', '0');
INSERT INTO `st_wechat` VALUES ('285', 'oQktJwOiqyu24Qfl07gYG2raNJ_M', null, '0', null, '0', '1516795060', '1516795060', '0');
INSERT INTO `st_wechat` VALUES ('286', 'oQktJwKLs2ITlYQrA_-aqj2fmzh0', null, '0', null, '0', '1516796069', '1516796069', '0');
INSERT INTO `st_wechat` VALUES ('287', 'oQktJwFa-kHGyj8fBiXK0BeHwXrE', null, '0', null, '0', '1516797007', '1516797007', '0');
INSERT INTO `st_wechat` VALUES ('288', 'oQktJwC6Fw0-fejW1FRUXTycGufU', null, '0', null, '0', '1516802287', '1516802287', '0');
INSERT INTO `st_wechat` VALUES ('289', 'oQktJwMAZfTwAIFDNcYfijW7VzDk', null, '0', null, '0', '1516802506', '1516802506', '0');
INSERT INTO `st_wechat` VALUES ('290', 'oQktJwENhwmFvCBgmenDbQHnycBk', null, '0', null, '0', '1516803605', '1516803605', '0');
INSERT INTO `st_wechat` VALUES ('291', 'oQktJwOKiExIYeNhMFr-Jcq-M5kg', null, '0', null, '0', '1516806055', '1516806055', '0');
INSERT INTO `st_wechat` VALUES ('292', 'oQktJwGAKPif8VJ93IwFU6QB1dRQ', null, '0', null, '0', '1516806763', '1516806763', '0');
INSERT INTO `st_wechat` VALUES ('293', 'oQktJwFe6t79wOaBjuncpvyraw3o', null, '0', null, '0', '1516809869', '1516809869', '0');
INSERT INTO `st_wechat` VALUES ('294', 'oQktJwN2JG-8OwR4I9zSukFuo6Wg', null, '0', null, '0', '1516811834', '1516811834', '0');
INSERT INTO `st_wechat` VALUES ('295', 'oQktJwM85XvHbN4wxxfZPf6qi3zo', null, '0', null, '0', '1516829829', '1516829829', '0');
INSERT INTO `st_wechat` VALUES ('296', 'oQktJwDK_lpeGXuEZK_4rBAG7Yjo', null, '0', null, '0', '1516837161', '1516837161', '0');
INSERT INTO `st_wechat` VALUES ('297', 'oQktJwDlqYnHPCGdHs-XCvGABUlY', null, '0', null, '0', '1516840901', '1516840901', '0');
INSERT INTO `st_wechat` VALUES ('298', 'oQktJwLmb2Eb8lk1_BfaGQp2SfRE', null, '0', null, '0', '1516841036', '1516841036', '0');
INSERT INTO `st_wechat` VALUES ('299', 'oQktJwPwd-HyCx-0PVX-ZP5ddnC0', null, '0', null, '0', '1516843394', '1516843394', '0');
INSERT INTO `st_wechat` VALUES ('300', 'oQktJwLnIVyVO3O86UTPT-cUC8Vo', null, '0', null, '0', '1516843756', '1516843756', '0');
INSERT INTO `st_wechat` VALUES ('301', 'oQktJwAGbfpf_vIZjGeM7R9EMX_M', null, '0', null, '0', '1516843877', '1516843877', '0');
INSERT INTO `st_wechat` VALUES ('302', 'oQktJwPInlW5QD5GM5QVdhA-keIM', null, '0', null, '0', '1516844413', '1516844413', '0');
INSERT INTO `st_wechat` VALUES ('303', 'oQktJwLAsBeEKk3GA9XLT9EDXPPk', null, '0', null, '0', '1516844511', '1516844511', '0');
INSERT INTO `st_wechat` VALUES ('304', 'oQktJwKOlFJUPdlRuuf1nNH5grVE', null, '0', null, '0', '1516845306', '1516845306', '0');
INSERT INTO `st_wechat` VALUES ('305', 'oQktJwKdwmTivGy3MdjD5i8Dnngk', null, '0', null, '0', '1516846682', '1516846682', '0');
INSERT INTO `st_wechat` VALUES ('306', 'oQktJwKxGv5mTQTfBimQKFlbpX3c', null, '0', null, '0', '1516846731', '1516846731', '0');
INSERT INTO `st_wechat` VALUES ('307', 'oQktJwDYehr-GcsoawlpQUWeCYaA', null, '0', null, '0', '1516846793', '1516846793', '0');
INSERT INTO `st_wechat` VALUES ('308', 'oQktJwLmLJ1X6nEvkztjNrl3xbrY', null, '0', null, '0', '1516847048', '1516847048', '0');
INSERT INTO `st_wechat` VALUES ('309', 'oQktJwGo1CKxMrzep6jZNVq8cK4o', null, '0', null, '0', '1516847276', '1516847276', '0');
INSERT INTO `st_wechat` VALUES ('310', 'oQktJwLXvtOCd4cNLVbM04GUlJIM', null, '0', null, '0', '1516847320', '1516847320', '0');
INSERT INTO `st_wechat` VALUES ('311', 'oQktJwEjlNrA0VzV_eskLXN28nK8', null, '0', null, '0', '1516847357', '1516847357', '0');
INSERT INTO `st_wechat` VALUES ('312', 'oQktJwOT2k4UA5-L2B6CtWsQFz-k', null, '0', null, '0', '1516847486', '1516847486', '0');
INSERT INTO `st_wechat` VALUES ('313', 'oQktJwDu7hS7TF5CQXcT1VgTXbZk', null, '0', null, '0', '1516847770', '1516847770', '0');
INSERT INTO `st_wechat` VALUES ('314', 'oQktJwPkAqOpR_8WpnpzHOTEntOU', null, '0', null, '0', '1516847928', '1516847928', '0');
INSERT INTO `st_wechat` VALUES ('315', 'oQktJwKhsRhiJGD38nWP2Y4_Tn14', null, '0', null, '0', '1516848023', '1516848023', '0');
INSERT INTO `st_wechat` VALUES ('316', 'oQktJwM_xGDNh33L-K0ZJcKX3jeM', null, '0', null, '0', '1516848493', '1516848493', '0');
INSERT INTO `st_wechat` VALUES ('317', 'oQktJwDfT1k5uMoeCrOP_WIEGtXY', null, '0', null, '0', '1516848819', '1516848819', '0');
INSERT INTO `st_wechat` VALUES ('318', 'oQktJwHC1I-5zEDA-AM6pJAzR7nI', null, '0', null, '0', '1516848936', '1516848936', '0');
INSERT INTO `st_wechat` VALUES ('319', 'oQktJwBhxUcRePfzj1y-X9uEjSvM', null, '0', null, '0', '1516849157', '1516849157', '0');
INSERT INTO `st_wechat` VALUES ('320', 'oQktJwEwNZDy-_XQEBNdM3FQL06s', null, '0', null, '0', '1516849263', '1516849263', '0');
INSERT INTO `st_wechat` VALUES ('321', 'oQktJwHwSoFsrvswiJzClkzY2fU0', null, '0', null, '0', '1516849639', '1516849639', '0');

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
