/*
Navicat MySQL Data Transfer

Source Server         : 120.27.12.1
Source Server Version : 50636
Source Host           : localhost:3306
Source Database       : newsuteng

Target Server Type    : MYSQL
Target Server Version : 50636
File Encoding         : 65001

Date: 2018-01-31 17:50:15
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for st_address
-- ----------------------------
DROP TABLE IF EXISTS `st_address`;
CREATE TABLE `st_address` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL COMMENT '关联用户ID',
  `name` varchar(12) NOT NULL COMMENT '收货人姓名',
  `phone` varchar(12) NOT NULL COMMENT '收货人电话',
  `address` varchar(255) NOT NULL COMMENT '收货人地址',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '地址状态，默认为0，设为默认地址，否则为1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of st_address
-- ----------------------------
INSERT INTO `st_address` VALUES ('1', '2', ' aa', '13800138000', ' 广州', '0');

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
) ENGINE=MyISAM AUTO_INCREMENT=34 DEFAULT CHARSET=utf8;

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
INSERT INTO `st_admin_menu` VALUES ('30', '13', '设备绑定', 'Admin/Devices/bind', '', null);
INSERT INTO `st_admin_menu` VALUES ('31', '13', '滤芯列表', 'Admin/Devices/filterList', '', null);
INSERT INTO `st_admin_menu` VALUES ('32', '13', '产品类型', 'Admin/Devices/product', '', null);
INSERT INTO `st_admin_menu` VALUES ('33', '0', '首页', 'Admin/Test/welcome', '', '1');

-- ----------------------------
-- Table structure for st_attr
-- ----------------------------
DROP TABLE IF EXISTS `st_attr`;
CREATE TABLE `st_attr` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `attr` varchar(20) NOT NULL COMMENT '属性名称',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of st_attr
-- ----------------------------
INSERT INTO `st_attr` VALUES ('1', '内存');
INSERT INTO `st_attr` VALUES ('2', '运行内存');
INSERT INTO `st_attr` VALUES ('3', '颜色');
INSERT INTO `st_attr` VALUES ('4', '储存');

-- ----------------------------
-- Table structure for st_attr_val
-- ----------------------------
DROP TABLE IF EXISTS `st_attr_val`;
CREATE TABLE `st_attr_val` (
  `gid` int(11) NOT NULL COMMENT '商品ID',
  `aid` int(11) NOT NULL COMMENT '属性ID',
  `val` varchar(20) NOT NULL COMMENT '属性值'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of st_attr_val
-- ----------------------------
INSERT INTO `st_attr_val` VALUES ('1', '1', '4G');
INSERT INTO `st_attr_val` VALUES ('1', '2', '4G');
INSERT INTO `st_attr_val` VALUES ('4', '1', '64G');
INSERT INTO `st_attr_val` VALUES ('5', '2', '3G');
INSERT INTO `st_attr_val` VALUES ('6', '1', '64G');
INSERT INTO `st_attr_val` VALUES ('7', '2', '20');
INSERT INTO `st_attr_val` VALUES ('8', '1', '20');
INSERT INTO `st_attr_val` VALUES ('9', '1', '20');
INSERT INTO `st_attr_val` VALUES ('10', '1', '2');
INSERT INTO `st_attr_val` VALUES ('11', '1', '2');
INSERT INTO `st_attr_val` VALUES ('12', '2', '2.3');
INSERT INTO `st_attr_val` VALUES ('13', '2', '230');
INSERT INTO `st_attr_val` VALUES ('14', '2', '230');
INSERT INTO `st_attr_val` VALUES ('15', '1', '23');
INSERT INTO `st_attr_val` VALUES ('16', '1', '23');
INSERT INTO `st_attr_val` VALUES ('17', '1', '320');
INSERT INTO `st_attr_val` VALUES ('18', '1', '320');
INSERT INTO `st_attr_val` VALUES ('19', '1', '320');
INSERT INTO `st_attr_val` VALUES ('20', '1', '320');
INSERT INTO `st_attr_val` VALUES ('22', '1', '64G');
INSERT INTO `st_attr_val` VALUES ('22', '2', '4G');
INSERT INTO `st_attr_val` VALUES ('22', '3', '红色');
INSERT INTO `st_attr_val` VALUES ('23', '0', '4');
INSERT INTO `st_attr_val` VALUES ('24', '4', '1852122013得分都是');
INSERT INTO `st_attr_val` VALUES ('25', '4', '214156122452');
INSERT INTO `st_attr_val` VALUES ('38', '2', '速度发生发生');
INSERT INTO `st_attr_val` VALUES ('39', '4', '是放松放松');
INSERT INTO `st_attr_val` VALUES ('41', '2', 'sd#$$$  $%$%%');

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
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=utf8 COMMENT='用户组表';

-- ----------------------------
-- Records of st_auth_group
-- ----------------------------
INSERT INTO `st_auth_group` VALUES ('1', '超级管理员', '1', '1,2,28,5,6,7,8,10,11,9,14,15,32,33,34,16,17,18,19,20,21,22,23,24,25,26,30,31,{$v[\'id\']}');
INSERT INTO `st_auth_group` VALUES ('3', '二级经销商', '1', '1,2,4,3');
INSERT INTO `st_auth_group` VALUES ('4', '三级经销商', '1', '1,2,3');
INSERT INTO `st_auth_group` VALUES ('14', '超级管理员', '1', '1,2,28,5,6,7,8,10,11,9,14,15,16,17,18,19,20,21,22,23,24,25,26,30,31');
INSERT INTO `st_auth_group` VALUES ('7', '合作商（PC后台管理）', '1', '20,21,22,23,{$v[\'id\']}');
INSERT INTO `st_auth_group` VALUES ('6', '会员（手机商城会员水机）', '1', '14,15,32,33,34,16,17,20,21,22,23');
INSERT INTO `st_auth_group` VALUES ('5', '服务站（PC后台管理）', '1', '14,15,32,33,34,16,17,20,21,22,23,30,31');
INSERT INTO `st_auth_group` VALUES ('8', '经销商（PC后台管理）', '1', null);
INSERT INTO `st_auth_group` VALUES ('9', '售后管理（PC后台管理）', '1', '');
INSERT INTO `st_auth_group` VALUES ('10', '管理人员（PC后台管理）', '1', null);
INSERT INTO `st_auth_group` VALUES ('11', '生产和采购（PC后台管理）', '1', null);
INSERT INTO `st_auth_group` VALUES ('12', '仓储（PC后台管理）', '1', '1,2,28,14,15,32,33,34,18,19,20,21,22,23,{$v[\'id\']}');
INSERT INTO `st_auth_group` VALUES ('16', '一级经销商', '1', null);

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
INSERT INTO `st_auth_group_access` VALUES ('22', '1');
INSERT INTO `st_auth_group_access` VALUES ('22', '5');
INSERT INTO `st_auth_group_access` VALUES ('23', '14');
INSERT INTO `st_auth_group_access` VALUES ('24', '12');
INSERT INTO `st_auth_group_access` VALUES ('28', '1');

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
) ENGINE=MyISAM AUTO_INCREMENT=35 DEFAULT CHARSET=utf8 COMMENT='规则表';

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
INSERT INTO `st_auth_rule` VALUES ('32', '14', 'Admin/Devices/bind', '设备绑定', '1', '1', '');
INSERT INTO `st_auth_rule` VALUES ('33', '14', 'Admin/Devices/filterList', '滤芯列表', '1', '1', '');
INSERT INTO `st_auth_rule` VALUES ('34', '14', 'Admin/Devices/product', '产品类型', '1', '1', '');

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
  `uid` int(11) NOT NULL COMMENT '用户ID',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '购物车状态0未结算，1已结算',
  `addtime` varchar(12) NOT NULL COMMENT '添加购物时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;

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
  `leavel` int(11) NOT NULL COMMENT '分类级别',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of st_category
-- ----------------------------
INSERT INTO `st_category` VALUES ('1', '0', '滤芯', '1');
INSERT INTO `st_category` VALUES ('2', '0', '食品区', '1');
INSERT INTO `st_category` VALUES ('3', '0', '生活用品', '1');
INSERT INTO `st_category` VALUES ('4', '2', '坚果', '2');
INSERT INTO `st_category` VALUES ('5', '0', '生活区', '1');
INSERT INTO `st_category` VALUES ('6', '2', '干果', '2');
INSERT INTO `st_category` VALUES ('7', '3', '洗头水', '2');
INSERT INTO `st_category` VALUES ('8', '3', '沐浴露', '2');
INSERT INTO `st_category` VALUES ('16', '6', '饼干', '3');
INSERT INTO `st_category` VALUES ('17', '6', '面包', '3');

-- ----------------------------
-- Table structure for st_com_pic
-- ----------------------------
DROP TABLE IF EXISTS `st_com_pic`;
CREATE TABLE `st_com_pic` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cid` int(11) NOT NULL COMMENT '评论ID',
  `path` varchar(255) CHARACTER SET utf8 NOT NULL COMMENT '图片地址',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of st_com_pic
-- ----------------------------
INSERT INTO `st_com_pic` VALUES ('1', '3', '2018-01-29/5a6f0ac6c12d9.jpg');
INSERT INTO `st_com_pic` VALUES ('2', '4', '');
INSERT INTO `st_com_pic` VALUES ('3', '10', '2018-01-30/5a700923bda78.jpg|2018-01-30/5a700923bde07.jpg|');
INSERT INTO `st_com_pic` VALUES ('4', '11', '2018-01-30/5a7009436b4e5.jpg|2018-01-30/5a7009436b7f9.jpg|');
INSERT INTO `st_com_pic` VALUES ('5', '12', '2018-01-30/5a700a6749ba0.jpg|2018-01-30/5a700a6749e1a.jpg|');
INSERT INTO `st_com_pic` VALUES ('6', '13', '2018-01-30/5a700a7821745.jpg|2018-01-30/5a700a78219e8.jpg|');
INSERT INTO `st_com_pic` VALUES ('7', '14', '2018-01-30/5a700a8d8f539.jpg|2018-01-30/5a700a8d8f9e7.jpg|2018-01-30/5a700a8d8fdc3.jpg|');

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
  `status` tinyint(1) NOT NULL COMMENT '1好评2中评3差评',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of st_comment
-- ----------------------------
INSERT INTO `st_comment` VALUES ('1', '2', '22', '132', null, null, null, '', '0');
INSERT INTO `st_comment` VALUES ('3', '2', '22', 'fdsafsa', null, null, null, '', '0');
INSERT INTO `st_comment` VALUES ('4', '2', '22', '', null, null, null, '1517291267', '1');
INSERT INTO `st_comment` VALUES ('10', '2', '22', '', null, null, null, '1517291811', '1');
INSERT INTO `st_comment` VALUES ('11', '2', '22', '', null, null, null, '1517291843', '1');
INSERT INTO `st_comment` VALUES ('12', '2', '22', '', null, null, null, '1517292135', '1');
INSERT INTO `st_comment` VALUES ('13', '2', '22', '', null, null, null, '1517292152', '1');
INSERT INTO `st_comment` VALUES ('14', '2', '22', '', null, null, null, '1517292173', '1');

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
INSERT INTO `st_commission` VALUES ('1', '30', '40', '70', '60', '40', '30', '20', '10', '0', '0');

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
  `bind_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '绑定状态 0未绑定1绑定',
  `vid` int(11) DEFAULT NULL COMMENT '绑定的经销商',
  `addtime` int(11) NOT NULL COMMENT '添加时间',
  `type_id` int(11) NOT NULL COMMENT '产品类型ID',
  PRIMARY KEY (`id`),
  KEY `id` (`id`,`device_code`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of st_devices
-- ----------------------------
INSERT INTO `st_devices` VALUES ('1', '123456123456123', '1', '1', '9', '1517230209', '0');

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
) ENGINE=InnoDB AUTO_INCREMENT=195 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of st_dimension
-- ----------------------------

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
INSERT INTO `st_dimension_num` VALUES ('1', '1', '1');

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
-- Table structure for st_filters
-- ----------------------------
DROP TABLE IF EXISTS `st_filters`;
CREATE TABLE `st_filters` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `filtername` varchar(30) NOT NULL COMMENT '滤芯名称',
  `alias` varchar(30) DEFAULT '暂无别名' COMMENT '滤芯别名',
  `picpath` varchar(255) NOT NULL COMMENT '滤芯图片',
  `price` decimal(15,2) NOT NULL COMMENT '滤芯单价',
  `timelife` int(11) unsigned NOT NULL COMMENT '时间寿命（小时）',
  `flowlife` int(11) unsigned NOT NULL COMMENT '流量寿命（升）',
  `balancatime` int(11) unsigned DEFAULT NULL COMMENT '时间寿命使能',
  `balancaflow` int(11) unsigned DEFAULT NULL COMMENT '流量寿命使能',
  `introduce` varchar(255) DEFAULT '暂无简介' COMMENT '滤芯简介',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '滤芯状态（0使用中1禁用中)',
  `url` varchar(255) DEFAULT NULL COMMENT '滤芯购买网址',
  `addtime` int(11) NOT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`),
  KEY `device_id` (`filtername`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of st_filters
-- ----------------------------
INSERT INTO `st_filters` VALUES ('13', 'fdsa', 'fsa', '', '2.33', '300', '3000', null, null, '', '0', '', '1517279226');
INSERT INTO `st_filters` VALUES ('14', '土木工程', '123', '', '3.22', '320', '32510', null, null, '', '0', '', '1517281868');

-- ----------------------------
-- Table structure for st_flow
-- ----------------------------
DROP TABLE IF EXISTS `st_flow`;
CREATE TABLE `st_flow` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '充值流水ID',
  `user_id` int(11) unsigned NOT NULL COMMENT '用户ID',
  `order_id` varchar(32) NOT NULL COMMENT '订单编号',
  `money` decimal(25,0) unsigned DEFAULT NULL COMMENT '充值金额',
  `mode` tinyint(1) unsigned DEFAULT NULL COMMENT '充值方式(0：金币 1：银币  2：微信 3：支付宝 4：银联)',
  `gold_num` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '金币',
  `silver` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '银币',
  `describe` varchar(255) DEFAULT NULL COMMENT '充值描述',
  `current_gold_num` varchar(255) DEFAULT '0' COMMENT '当前金币',
  `current_silver` int(11) unsigned DEFAULT '0' COMMENT '当前银币',
  `addtime` int(11) NOT NULL COMMENT '充值时间',
  `updatetime` int(11) unsigned NOT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=40 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of st_flow
-- ----------------------------
INSERT INTO `st_flow` VALUES ('39', '15', '733959877288654', '400', '2', '0', '1000', '400元1000个银币', '200', '1000', '1517391973', '1517391973');
INSERT INTO `st_flow` VALUES ('38', '15', '458872302164080', '200', '2', '200', '0', '200元200个金币', '200', '0', '1517391894', '1517391894');

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
  `cost` double(6,2) DEFAULT NULL COMMENT '商品成本',
  `price` double(6,2) DEFAULT NULL COMMENT '商品单价',
  `stock` int(11) DEFAULT '0' COMMENT '商品库存',
  `status` tinyint(1) DEFAULT '0' COMMENT '商品状态，默认0上架，1为下架',
  `addtime` int(12) NOT NULL COMMENT '商品添加时间',
  `updatetime` int(12) NOT NULL COMMENT '商品更新时间',
  PRIMARY KEY (`id`),
  KEY `id` (`id`,`cid`)
) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of st_goods
-- ----------------------------
INSERT INTO `st_goods` VALUES ('1', '8', null, '小米', null, null, null, null, '0', '0', '1516970804', '1516970804');
INSERT INTO `st_goods` VALUES ('4', '8', null, '三星', null, null, null, null, '0', '0', '1516970804', '1516971060');
INSERT INTO `st_goods` VALUES ('5', '21', null, '三星', null, null, null, null, '0', '0', '1516970804', '1516971093');
INSERT INTO `st_goods` VALUES ('6', '22', null, '苹果', null, null, null, null, '0', '2', '1516970804', '1516971093');
INSERT INTO `st_goods` VALUES ('22', '10', null, '手机', null, null, null, null, '0', '0', '1517190942', '1517190942');
INSERT INTO `st_goods` VALUES ('23', '10', null, '食品', null, null, null, null, '0', '0', '1517298526', '1517298526');
INSERT INTO `st_goods` VALUES ('24', '7', null, '111', null, null, null, null, '0', '2', '1517306607', '1517306607');
INSERT INTO `st_goods` VALUES ('25', '1', null, 'RO膜', null, null, null, null, '0', '0', '1517311414', '1517311414');
INSERT INTO `st_goods` VALUES ('38', '21', null, '$%%    2334', null, null, null, null, '0', '2', '1517316178', '1517316178');
INSERT INTO `st_goods` VALUES ('39', '21', null, '是否单身的', null, null, null, null, '0', '2', '1517316214', '1517316214');
INSERT INTO `st_goods` VALUES ('41', '7', null, '$%    ^^^', null, null, null, null, '0', '2', '1517316557', '1517316557');

-- ----------------------------
-- Table structure for st_goods_detail
-- ----------------------------
DROP TABLE IF EXISTS `st_goods_detail`;
CREATE TABLE `st_goods_detail` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gid` int(11) NOT NULL COMMENT '关联商品ID',
  `price` double(6,2) NOT NULL COMMENT '商品单价',
  `cost` double(6,2) NOT NULL COMMENT '商品成本',
  `stock` int(11) NOT NULL COMMENT '库存',
  `desc` text CHARACTER SET utf8 COMMENT '商品描述',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '商品状态，0下架，默认1为上架',
  PRIMARY KEY (`id`),
  KEY `id` (`id`,`gid`,`status`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of st_goods_detail
-- ----------------------------
INSERT INTO `st_goods_detail` VALUES ('1', '1', '1988.00', '1500.00', '325', null, '1');
INSERT INTO `st_goods_detail` VALUES ('3', '4', '3.00', '2.00', '12', null, '1');
INSERT INTO `st_goods_detail` VALUES ('4', '5', '30.25', '20.00', '215', null, '1');
INSERT INTO `st_goods_detail` VALUES ('5', '6', '300.00', '20.00', '302', null, '1');
INSERT INTO `st_goods_detail` VALUES ('6', '7', '2.00', '2.00', '10', null, '1');
INSERT INTO `st_goods_detail` VALUES ('7', '8', '3.02', '2.00', '362', null, '1');
INSERT INTO `st_goods_detail` VALUES ('8', '9', '3.02', '2.00', '362', null, '1');
INSERT INTO `st_goods_detail` VALUES ('9', '10', '3.00', '2.00', '265', null, '1');
INSERT INTO `st_goods_detail` VALUES ('10', '11', '3.00', '2.00', '265', null, '1');
INSERT INTO `st_goods_detail` VALUES ('11', '12', '3.20', '2.00', '12', null, '1');
INSERT INTO `st_goods_detail` VALUES ('12', '13', '3.20', '15.01', '1562', null, '1');
INSERT INTO `st_goods_detail` VALUES ('13', '14', '3.20', '15.01', '1562', null, '1');
INSERT INTO `st_goods_detail` VALUES ('14', '15', '3.20', '2.00', '123', null, '1');
INSERT INTO `st_goods_detail` VALUES ('15', '16', '3.20', '2.00', '123', null, '1');
INSERT INTO `st_goods_detail` VALUES ('16', '17', '2.02', '23.02', '123', null, '1');
INSERT INTO `st_goods_detail` VALUES ('17', '18', '2.02', '23.02', '123', null, '1');
INSERT INTO `st_goods_detail` VALUES ('18', '19', '2.02', '23.02', '123', null, '1');
INSERT INTO `st_goods_detail` VALUES ('19', '20', '2.02', '23.02', '123', null, '1');
INSERT INTO `st_goods_detail` VALUES ('20', '22', '306.00', '201.00', '165', null, '1');
INSERT INTO `st_goods_detail` VALUES ('21', '26', '500.00', '300.00', '123', null, '1');
INSERT INTO `st_goods_detail` VALUES ('22', '23', '15.00', '10.00', '60', null, '1');
INSERT INTO `st_goods_detail` VALUES ('23', '24', '52.00', '25.00', '50', null, '1');
INSERT INTO `st_goods_detail` VALUES ('24', '25', '25.00', '15.00', '20', null, '1');
INSERT INTO `st_goods_detail` VALUES ('28', '38', '1.08', '1.08', '8', null, '1');
INSERT INTO `st_goods_detail` VALUES ('29', '39', '1.80', '1.80', '85', null, '1');
INSERT INTO `st_goods_detail` VALUES ('30', '41', '100.00', '100.00', '80', null, '1');

-- ----------------------------
-- Table structure for st_order_detail
-- ----------------------------
DROP TABLE IF EXISTS `st_order_detail`;
CREATE TABLE `st_order_detail` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` varchar(16) CHARACTER SET utf8 NOT NULL COMMENT '订单号码',
  `gid` int(11) NOT NULL COMMENT '商品ID',
  `num` int(11) NOT NULL COMMENT '商品数量',
  `cost` double(6,2) NOT NULL,
  `price` double(6,2) NOT NULL COMMENT '商品单价',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `addtime` int(11) NOT NULL COMMENT '订单生成时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of st_order_detail
-- ----------------------------
INSERT INTO `st_order_detail` VALUES ('7', '119954568251015', '22', '3', '20.00', '306.00', '0', '1517321363');
INSERT INTO `st_order_detail` VALUES ('8', '119954568251015', '1', '4', '10.00', '1988.00', '0', '1517321363');
INSERT INTO `st_order_detail` VALUES ('13', '263573882426098', '22', '3', '20.00', '306.00', '0', '1517321440');
INSERT INTO `st_order_detail` VALUES ('14', '263573882426098', '1', '4', '10.00', '1988.00', '0', '1517321440');
INSERT INTO `st_order_detail` VALUES ('15', '644871529179373', '22', '3', '20.00', '306.00', '0', '1517321472');
INSERT INTO `st_order_detail` VALUES ('16', '644871529179373', '1', '2', '10.00', '1988.00', '0', '1517321472');
INSERT INTO `st_order_detail` VALUES ('17', '436106100658239', '22', '1', '20.00', '306.00', '0', '1517321521');
INSERT INTO `st_order_detail` VALUES ('18', '436106100658239', '1', '2', '10.00', '1988.00', '0', '1517321521');
INSERT INTO `st_order_detail` VALUES ('19', '538464792029082', '22', '10', '20.00', '306.00', '0', '1517321550');
INSERT INTO `st_order_detail` VALUES ('20', '538464792029082', '1', '8', '10.00', '1988.00', '0', '1517321550');

-- ----------------------------
-- Table structure for st_orders
-- ----------------------------
DROP TABLE IF EXISTS `st_orders`;
CREATE TABLE `st_orders` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `order_id` varchar(32) NOT NULL COMMENT '订单编号',
  `device_id` int(11) unsigned NOT NULL COMMENT '关联的设备ID号',
  `user_id` int(11) unsigned NOT NULL COMMENT '关联的用户ID号',
  `express_id` int(11) unsigned DEFAULT NULL COMMENT '关联快递信息ID号',
  `total_num` int(11) unsigned NOT NULL COMMENT '商品的购买总数量',
  `total_price` decimal(15,2) unsigned NOT NULL COMMENT '商品的购买总金额',
  `created_at` int(11) DEFAULT NULL COMMENT '订单创建时间',
  `updated_at` int(11) DEFAULT NULL COMMENT '订单修改时间',
  `is_pay` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '付款状态(0：未付款1：已付款 2：已取消)',
  `is_receipt` tinyint(1) unsigned DEFAULT '0' COMMENT '发货状态(0：未发货1：已发货)',
  `is_ship` tinyint(1) unsigned DEFAULT '0' COMMENT '收货状态(0：未收货1：已收货)',
  `is_recharge` tinyint(1) unsigned DEFAULT '0' COMMENT '充值状态(0：未充值1：已充值)',
  `express` varchar(32) DEFAULT NULL COMMENT '快递名称',
  `mca` varchar(32) DEFAULT NULL COMMENT '快递订单号',
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`,`device_id`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of st_orders
-- ----------------------------

-- ----------------------------
-- Table structure for st_pic
-- ----------------------------
DROP TABLE IF EXISTS `st_pic`;
CREATE TABLE `st_pic` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '图片ID',
  `gid` int(11) NOT NULL COMMENT '关联商品ID',
  `picname` varchar(100) CHARACTER SET utf8 NOT NULL COMMENT '图片名称',
  `path` varchar(255) CHARACTER SET utf8 NOT NULL COMMENT '图片地址',
  PRIMARY KEY (`id`),
  KEY `id` (`id`,`gid`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of st_pic
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
  `order_id` varchar(20) NOT NULL COMMENT '订单号码',
  `uid` int(11) NOT NULL COMMENT '关联用户ID',
  `address_id` int(11) DEFAULT NULL COMMENT '地址ID',
  `gid` int(11) DEFAULT NULL COMMENT '商品ID',
  `g_cost` double(12,2) NOT NULL COMMENT '商品总成本',
  `g_price` double(12,2) NOT NULL COMMENT '商品总金额',
  `g_num` int(11) NOT NULL COMMENT '商品总数量',
  `addtime` varchar(12) NOT NULL COMMENT '订单添加时间',
  `status` tinyint(1) NOT NULL DEFAULT '8' COMMENT '0已下单，1处理中，2已发货，3已收货，4退货处理中，5已退货，6申诉中，7订单完成，8未支付',
  `mode` tinyint(1) DEFAULT NULL COMMENT '支付方式(0-微信、1-支付宝、2-银联、3-金币、4-银币)',
  `express` varchar(32) DEFAULT NULL COMMENT '快递单号',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of st_shop_order
-- ----------------------------

-- ----------------------------
-- Table structure for st_type
-- ----------------------------
DROP TABLE IF EXISTS `st_type`;
CREATE TABLE `st_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `typename` varchar(255) NOT NULL COMMENT '类型名称',
  `filter1` varchar(30) DEFAULT NULL COMMENT '一级滤芯',
  `filter2` varchar(30) DEFAULT NULL,
  `filter3` varchar(30) DEFAULT NULL,
  `filter4` varchar(30) DEFAULT NULL,
  `filter5` varchar(30) DEFAULT NULL,
  `filter6` varchar(30) DEFAULT NULL,
  `filter7` varchar(30) DEFAULT NULL,
  `filter8` varchar(30) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态0使用中1禁用中',
  `addtime` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=37 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of st_type
-- ----------------------------
INSERT INTO `st_type` VALUES ('32', '魂牵梦萦', '13', null, null, null, null, null, null, null, '0', null);
INSERT INTO `st_type` VALUES ('33', '圾', '13', '14', null, null, null, null, null, null, '0', '1517284707');
INSERT INTO `st_type` VALUES ('34', '脸扔', '13', '14', null, null, null, null, null, null, '0', '1517284766');
INSERT INTO `st_type` VALUES ('35', 'dfsf', null, null, null, null, null, null, null, null, '0', '1517312612');
INSERT INTO `st_type` VALUES ('36', 'LVR', '13', '14', null, null, null, null, null, null, '0', '1517312646');

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
  `gold_num` int(11) NOT NULL DEFAULT '0' COMMENT '金币数量 默认为0',
  `silver` int(11) NOT NULL DEFAULT '0' COMMENT '金币数量 默认为0',
  `balance` double(6,2) NOT NULL DEFAULT '0.00' COMMENT '用户余额，默认0.00',
  `addtime` int(11) unsigned NOT NULL COMMENT '创建时间',
  `updatetime` int(11) unsigned NOT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of st_users
-- ----------------------------

-- ----------------------------
-- Table structure for st_users_commission
-- ----------------------------
DROP TABLE IF EXISTS `st_users_commission`;
CREATE TABLE `st_users_commission` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增',
  `user_id` int(11) unsigned NOT NULL COMMENT '关联用户表ID',
  `order_id` varchar(12) DEFAULT NULL COMMENT '订单号码',
  `gold_num` int(11) unsigned DEFAULT '0' COMMENT '金币数量 默认为0',
  `silver` int(11) unsigned DEFAULT '0' COMMENT '银币数量 默认为0',
  `current_gold_num` int(11) unsigned DEFAULT '0' COMMENT '当前金币数量 默认为0',
  `current_silver` int(11) unsigned DEFAULT '0' COMMENT '当前银币数量 默认为0',
  `describe` varchar(255) DEFAULT NULL COMMENT '佣金获得描述',
  `type` tinyint(1) unsigned DEFAULT '0' COMMENT '分配类型{0：按比例分配，1：固定金额分配}',
  `status` tinyint(1) unsigned DEFAULT '0' COMMENT '状态{0：启用，1：禁用}',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=gbk;

-- ----------------------------
-- Records of st_users_commission
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
  `abonus` double(6,2) unsigned NOT NULL DEFAULT '0.00',
  `auditing` varchar(30) DEFAULT NULL COMMENT '审核-责任人',
  `add_liable` varchar(30) DEFAULT NULL COMMENT '添加-责任人',
  `reviewed_describe` varchar(255) DEFAULT NULL COMMENT '审核描述',
  `reviewed` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '审核{0:省份审核,1:公司审核,2:协议审核 3，审核完成}',
  `status` tinyint(1) unsigned DEFAULT '0' COMMENT '状态{0:身份填写 1:公司信息填写 2:签协议 3:待审批 4:身份证审批失败 5:公司信息审批失败 6:协议审批失败  7：审批成功  8：禁用分销商}',
  `addtime` int(11) unsigned NOT NULL COMMENT '添加时间',
  `updatetime` int(11) unsigned NOT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of st_vendors
-- ----------------------------
INSERT INTO `st_vendors` VALUES ('1', null, 'admin', '202cb962ac59075b964b07152d234b70', '13425492760', '超级管理员', null, null, null, null, null, null, null, null, null, '0', null, null, null, null, null, null, null, '', null, '0.00', null, null, null, '0', '7', '1513872000', '1513872000');
INSERT INTO `st_vendors` VALUES ('18', null, 'diyijia', 'e10adc3949ba59abbe56e057f20f883e', '13580029652', '第一分公司', '425132199802054256', '/Vendors/2018-01-31/5a7191758ea12.png', '/Vendors/2018-01-31/5a7191758ef3b.png', '/Vendors/2018-01-31/5a7191758f069.jpg', '冈茉优有限公司', '020-56239856', '广州市海珠区海珠广场', '/Vendors/2018-01-31/5a7191758f31c.jpg', '/Vendors/2018-01-31/5a7191758fa9b.jpg', '1', 'BD4BE2', null, null, 'gQE38DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyTlBFd2tHMmJkOTAxMDAwMGcwM00AAgRVdlhaAwQAAAAA', 'gQHx8DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyQThuOWszMmJkOTAxMDAwMHcwM3AAAgS2dlhaAwQAAAAA', 'gQEm8TwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyOEZwamx3MmJkOTAxMDAwMGcwM2kAAgTmp1xaAwQAAAAA', 'gQE_8TwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyem5MZGw0MmJkOTAxMDAwME0wM2wAAgTop1xaAwQAAAAA', null, null, '0.00', null, 'admin', null, '3', '7', '1517392245', '1517392245');

-- ----------------------------
-- Table structure for st_vendors_commission
-- ----------------------------
DROP TABLE IF EXISTS `st_vendors_commission`;
CREATE TABLE `st_vendors_commission` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增',
  `vendor_id` int(11) unsigned NOT NULL COMMENT '关联分销商表ID',
  `order_id` varchar(12) DEFAULT NULL COMMENT '订单号码',
  `abonus` double(6,2) unsigned DEFAULT '0.00' COMMENT '分红 默认为0.00',
  `current_abonus` double(6,2) unsigned DEFAULT '0.00' COMMENT '当前分红 默认为0.00',
  `describe` varchar(255) DEFAULT NULL COMMENT '佣金获得描述',
  `type` tinyint(1) unsigned DEFAULT '0' COMMENT '分配类型{0：按比例分配，1：固定金额分配}',
  `status` tinyint(1) unsigned DEFAULT '0' COMMENT '状态{0：启用，1：禁用}',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=gbk;

-- ----------------------------
-- Records of st_vendors_commission
-- ----------------------------

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
) ENGINE=InnoDB AUTO_INCREMENT=373 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of st_wechat
-- ----------------------------

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
) ENGINE=MyISAM AUTO_INCREMENT=24124 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of st_work
-- ----------------------------
INSERT INTO `st_work` VALUES ('24112', '1', null, '陈', 'hgR20180120114596815', null, '', '0', '显示器在城工珠援助佣兵 显示器在城工珠援助佣兵物城 显示器在城工珠援助佣兵物城 显示器在城工珠援助佣兵物城 显示器在城工珠援助佣兵物城 显示器在城工珠援助佣兵物城 显示器在城工珠援助佣兵物城 显示器在城工珠援助佣兵物城 显示器在城工珠援助佣兵物城 显示器在城工珠援助佣兵物城 显示器在城工珠援助佣兵物城 显示器在城工珠援助佣兵物城 显示器在城工珠援助佣兵物城 显示器在城工珠援助佣兵物城物城', '北京市北京市市辖区东城区', '2', '', '1516419920', '');
INSERT INTO `st_work` VALUES ('24113', '1', '1', '01安装', 'TEG20180129202585427', '*&amp;&amp;*', '13526530256', '0', '安装', '黑龙江省鹤岗市兴山区', '2', '1517231593', '1517228723', '');
INSERT INTO `st_work` VALUES ('24114', '1', '1', 'aaaaaa', 'oet20180129202523662', '*&amp;*&amp;*gldgkgs', '14785203698', '1', '维修', '辽宁省抚顺市新宾满族自治县', '2', '1517231635', '1517228759', '');
INSERT INTO `st_work` VALUES ('24115', '1', '1', 'weixiu', 'xya20180130103239526', '010', '18402036523', '1', '维修', '浙江省金华市兰溪市', '2', '1517279726', '1517279566', '');
INSERT INTO `st_work` VALUES ('24116', '1', '1', '01', 'bly20180130165773824', '01', '14702589632', '0', '01', '0', '2', '1517302749', '1517302637', '');
INSERT INTO `st_work` VALUES ('24117', '1', '1', '01', 'wNv20180130165781345', '01', '17402588520', '0', '01', '辽宁省沈阳市和平区', '2', '1517307251', '1517302673', '');
INSERT INTO `st_work` VALUES ('24118', '1', '1', '广州番禺区', 'mdC20180130193353285', '小小', '14014714714', '0', '广州番禺区钟村安装净水器', '广东省广州市番禺区', '2', '1517312089', '1517312028', '');
INSERT INTO `st_work` VALUES ('24119', '1', '1', '番禺广场', 'Rwn20180130193926813', 'ihLHK   **', '13452525252', '1', '维修', '广东省广州市番禺区', '2', '1517314510', '1517312351', '');
INSERT INTO `st_work` VALUES ('24120', '1', '1', 'KK*  ^ **', 'vso20180130201502494', '34$%    %^^', '14925893265', '0', 'weixiu', '内蒙古自治区通辽市科尔沁区', '1', '1517315785', '1517314555', '');
INSERT INTO `st_work` VALUES ('24121', '1', '1', 'fh  $%^ %', 'jyP20180130201564953', '34535$%^^%#$', '17000235623', '0', '安装', '北京市北京市市辖区东城区', '1', '1517314791', '1517314556', '');
INSERT INTO `st_work` VALUES ('24122', '1', '1', '飞', 'rCk20180130201806971', 'dgdfg drtte$%%^T^', '17702589632', '0', '沙发上￥%%%￥v     $$ %', '北京市北京市市辖区东城区', '2', '1517365977', '1517314690', '');
INSERT INTO `st_work` VALUES ('24123', '1', null, '￥%……    %#……    %&amp;……￥', 'MQe20180131113333774', null, '', '1', '￥%……%￥   ￥#￥#    ￥%%', '北京市北京市市辖区东城区', '0', '', '1517369592', '');
