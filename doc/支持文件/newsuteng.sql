/*
Navicat MySQL Data Transfer

Source Server         : 120.27.12.1
Source Server Version : 50636
Source Host           : localhost:3306
Source Database       : newsuteng

Target Server Type    : MYSQL
Target Server Version : 50636
File Encoding         : 65001

Date: 2018-01-31 15:23:10
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
INSERT INTO `st_category` VALUES ('3', '0', 'aaa1', '1');
INSERT INTO `st_category` VALUES ('4', '2', '坚果', '2');
INSERT INTO `st_category` VALUES ('5', '0', 'aaa2', '1');
INSERT INTO `st_category` VALUES ('6', '2', 'bbb2', '2');
INSERT INTO `st_category` VALUES ('7', '3', 'aaa1a', '2');
INSERT INTO `st_category` VALUES ('8', '3', 'aaa2a', '2');
INSERT INTO `st_category` VALUES ('10', '4', 'bbb2b', '3');
INSERT INTO `st_category` VALUES ('12', '0', '发财了', '1');
INSERT INTO `st_category` VALUES ('14', '5', '哈哈哈', '2');
INSERT INTO `st_category` VALUES ('15', '14', '大厦', '3');
INSERT INTO `st_category` VALUES ('16', '6', '止ddddd', '3');
INSERT INTO `st_category` VALUES ('17', '6', '123', '3');
INSERT INTO `st_category` VALUES ('21', '5', '添加成功', '2');

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
) ENGINE=InnoDB AUTO_INCREMENT=171 DEFAULT CHARSET=utf8;

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
INSERT INTO `st_dimension` VALUES ('50', 'gQE38DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyTlBFd2tHMmJkOTAxMDAwMGcwM00AAgRVdlhaAwQAAAAA', '4', '0', '0', '1517222260', '1517222260');
INSERT INTO `st_dimension` VALUES ('51', 'gQHx8DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyQThuOWszMmJkOTAxMDAwMHcwM3AAAgS2dlhaAwQAAAAA', '4', '1', '0', '1517222260', '1517222260');
INSERT INTO `st_dimension` VALUES ('52', 'gQEm8TwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyOEZwamx3MmJkOTAxMDAwMGcwM2kAAgTmp1xaAwQAAAAA', '4', '2', '0', '1517222260', '1517222260');
INSERT INTO `st_dimension` VALUES ('53', 'gQE_8TwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyem5MZGw0MmJkOTAxMDAwME0wM2wAAgTop1xaAwQAAAAA', '3', null, '0', '1517222260', '1517222260');
INSERT INTO `st_dimension` VALUES ('54', 'gQFb8DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAya2FvNmx3MmJkOTAxSFIybTFxMXYAAgTF-W5aAwQwhScA', '1', null, '1', '1517223367', '1517223367');
INSERT INTO `st_dimension` VALUES ('55', 'gQHH8TwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyQjVhM2wwMmJkOTAxMDAwMGcwM1oAAgTop1xaAwQAAAAA', '5', '1', '0', '1517223397', '1517223397');
INSERT INTO `st_dimension` VALUES ('56', 'gQGf8TwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyNlVNTGtmMmJkOTAxMDAwME0wM04AAgTop1xaAwQAAAAA', '5', '2', '0', '1517223397', '1517223397');
INSERT INTO `st_dimension` VALUES ('57', 'gQGa8DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAya05pU2toMmJkOTAxMDAwMDAwM0IAAgQUVV1aAwQAAAAA', '2', null, '0', '1517223397', '1517223397');
INSERT INTO `st_dimension` VALUES ('58', 'gQHb7zwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAySWNhN2xnMmJkOTAxRnc1bXhxMVcAAgQwAG9aAwQwhScA', '1', null, '1', '1517223986', '1517223986');
INSERT INTO `st_dimension` VALUES ('59', 'gQHH8DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyOWwwZ2stMmJkOTAxMDAwME0wM2wAAgQUVV1aAwQAAAAA', '5', '1', '0', '1517224096', '1517224096');
INSERT INTO `st_dimension` VALUES ('60', 'gQFb8DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyaFh1VGtMMmJkOTAxMDAwMGcwM1IAAgQUVV1aAwQAAAAA', '5', '2', '0', '1517224096', '1517224096');
INSERT INTO `st_dimension` VALUES ('61', 'gQGP8TwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyLUdTZGw2MmJkOTAxMDAwME0wM1EAAgTr115aAwQAAAAA', '2', null, '0', '1517224096', '1517224096');
INSERT INTO `st_dimension` VALUES ('62', 'gQGK8DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyUWYtQWtBMmJkOTAxRU82bXhxMUMAAgQCAW9aAwQwhScA', '1', null, '1', '1517224195', '1517224195');
INSERT INTO `st_dimension` VALUES ('63', 'gQHx8DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyLUJtMWtxMmJkOTAxR1I2bU5xMWYAAgSFAW9aAwQwhScA', '1', null, '1', '1517224326', '1517224326');
INSERT INTO `st_dimension` VALUES ('64', 'gQEo8TwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyMnlKWWxPMmJkOTAxMDAwMGcwM2IAAgTr115aAwQAAAAA', '4', '0', '0', '1517224436', '1517224436');
INSERT INTO `st_dimension` VALUES ('65', 'gQHv8DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAybHpfZmtzMmJkOTAxMDAwME0wMzUAAgTr115aAwQAAAAA', '4', '1', '0', '1517224436', '1517224436');
INSERT INTO `st_dimension` VALUES ('66', 'gQFN8TwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAya0dzMGxfMmJkOTAxMDAwME0wM0MAAgTX6F5aAwQAAAAA', '4', '2', '0', '1517224436', '1517224436');
INSERT INTO `st_dimension` VALUES ('67', 'gQEq8TwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyd2hPaWtTMmJkOTAxMDAwME0wMzEAAgTX6F5aAwQAAAAA', '3', null, '0', '1517224436', '1517224436');
INSERT INTO `st_dimension` VALUES ('68', 'gQFC8DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyTEw3OWtGMmJkOTAxR0o4bU5xMVEAAgR9A29aAwQwhScA', '1', null, '1', '1517224831', '1517224831');
INSERT INTO `st_dimension` VALUES ('69', 'gQGZ8DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyNVJSbWxOMmJkOTAxR1E5bXhxMXkAAgSEBG9aAwQwhScA', '1', null, '1', '1517225094', '1517225094');
INSERT INTO `st_dimension` VALUES ('70', 'gQF58DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyek9tWmtQMmJkOTAxMDAwMGcwMzgAAgRs7V5aAwQAAAAA', '5', '2', '0', '1517225106', '1517225106');
INSERT INTO `st_dimension` VALUES ('71', 'gQF08DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAydkFWamxqMmJkOTAxMDAwMDAwM3EAAgRt7V5aAwQAAAAA', '2', null, '0', '1517225106', '1517225106');
INSERT INTO `st_dimension` VALUES ('72', 'gQFk8DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyOTExcWwxMmJkOTAxRzhhbWhxMTIAAgRXBW9aAwQwhScA', '1', null, '1', '1517225305', '1517225305');
INSERT INTO `st_dimension` VALUES ('73', 'gQFR8TwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAycU9EX2xyMmJkOTAxMDAwMDAwM3QAAgSZ7V5aAwQAAAAA', '5', '1', '0', '1517226229', '1517226229');
INSERT INTO `st_dimension` VALUES ('74', 'gQFJ8TwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyRXVEcWstMmJkOTAxMDAwMHcwM28AAgSZ7V5aAwQAAAAA', '5', '2', '0', '1517226229', '1517226229');
INSERT INTO `st_dimension` VALUES ('75', 'gQEd8TwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyOXdrcGsyMmJkOTAxMDAwMGcwM0sAAgTY7V5aAwQAAAAA', '2', null, '0', '1517226229', '1517226229');
INSERT INTO `st_dimension` VALUES ('76', 'gQEY8TwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAySk1TWGxzMmJkOTAxMDAwME0wM3EAAgTY7V5aAwQAAAAA', '4', '0', '0', '1517227327', '1517227327');
INSERT INTO `st_dimension` VALUES ('77', 'gQHj8DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyTXpsbGtVMmJkOTAxMDAwMDAwM04AAgQO7l5aAwQAAAAA', '4', '1', '0', '1517227327', '1517227327');
INSERT INTO `st_dimension` VALUES ('78', 'gQGc8DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAydV9seWtZMmJkOTAxMDAwME0wM1oAAgQO7l5aAwQAAAAA', '4', '2', '0', '1517227327', '1517227327');
INSERT INTO `st_dimension` VALUES ('79', 'gQHw8DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAydDZoSWtHMmJkOTAxMDAwMGcwM0cAAgQj7l5aAwQAAAAA', '3', null, '0', '1517227327', '1517227327');
INSERT INTO `st_dimension` VALUES ('80', 'gQEq8TwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyZjdGMGtZMmJkOTAxMDAwMHcwMzIAAgQk7l5aAwQAAAAA', '4', '0', '0', '1517228477', '1517228477');
INSERT INTO `st_dimension` VALUES ('81', 'gQE-8TwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyVVJuSGtDMmJkOTAxMDAwME0wM18AAgTY7l5aAwQAAAAA', '4', '1', '0', '1517228477', '1517228477');
INSERT INTO `st_dimension` VALUES ('82', 'gQEY8TwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAybHZSTWwtMmJkOTAxMDAwME0wM3cAAgTY7l5aAwQAAAAA', '4', '2', '0', '1517228477', '1517228477');
INSERT INTO `st_dimension` VALUES ('83', 'gQHX8DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAydTJiYWtHMmJkOTAxMDAwMGcwMzUAAgQ98F5aAwQAAAAA', '3', null, '0', '1517228477', '1517228477');
INSERT INTO `st_dimension` VALUES ('84', 'gQGa8DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyQW5ScGwwMmJkOTAxMDAwMHcwM3AAAgQ_8F5aAwQAAAAA', '4', '0', '0', '1517228579', '1517228579');
INSERT INTO `st_dimension` VALUES ('85', 'gQGK8DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAySml2TmtHMmJkOTAxMDAwMHcwM04AAgRU8F5aAwQAAAAA', '4', '1', '0', '1517228579', '1517228579');
INSERT INTO `st_dimension` VALUES ('86', 'gQF78DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyV3h0VmxYMmJkOTAxMDAwMDAwM3AAAgRU8F5aAwQAAAAA', '4', '2', '0', '1517228579', '1517228579');
INSERT INTO `st_dimension` VALUES ('87', 'gQGW8DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyWFlZeWxJMmJkOTAxMDAwMGcwMzUAAgRf815aAwQAAAAA', '3', null, '0', '1517228579', '1517228579');
INSERT INTO `st_dimension` VALUES ('88', 'gQE-7zwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyNGlpZWtxMmJkOTAxR3ZvbXhxMTcAAgRvE29aAwQwhScA', '1', null, '1', '1517228912', '1517228912');
INSERT INTO `st_dimension` VALUES ('89', 'gQG28DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyUk10WmswMmJkOTAxMDAwMHcwMzUAAgRf815aAwQAAAAA', '5', '0', '0', '1517229025', '1517229025');
INSERT INTO `st_dimension` VALUES ('90', 'gQHu8DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyRl8ySmxCMmJkOTAxMDAwMDAwM2wAAgRw815aAwQAAAAA', '5', '1', '0', '1517229025', '1517229025');
INSERT INTO `st_dimension` VALUES ('91', 'gQEZ8DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAya2tEWGxzMmJkOTAxMDAwMHcwM3cAAgRx815aAwQAAAAA', '5', '2', '0', '1517229025', '1517229025');
INSERT INTO `st_dimension` VALUES ('92', 'gQE28TwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyNXNldGw4MmJkOTAxMDAwMHcwM0YAAgSx815aAwQAAAAA', '2', null, '0', '1517229025', '1517229025');
INSERT INTO `st_dimension` VALUES ('93', 'gQGL8TwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyOU1NNGtBMmJkOTAxMDAwMHcwM2MAAgSx815aAwQAAAAA', '5', '0', '0', '1517229616', '1517229616');
INSERT INTO `st_dimension` VALUES ('94', 'gQFA8TwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyVU9aa2swMmJkOTAxMDAwME0wMzUAAgQC9F5aAwQAAAAA', '5', '1', '0', '1517229616', '1517229616');
INSERT INTO `st_dimension` VALUES ('95', 'gQHL8DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyR2l1Y2t0MmJkOTAxMDAwMDAwM2QAAgQC9F5aAwQAAAAA', '5', '2', '0', '1517229616', '1517229616');
INSERT INTO `st_dimension` VALUES ('96', 'gQFS8DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyU21rZWw2MmJkOTAxMDAwMGcwM3MAAgRq9F5aAwQAAAAA', '2', null, '0', '1517229616', '1517229616');
INSERT INTO `st_dimension` VALUES ('97', 'gQGh8DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyU180RWtoMmJkOTAxSGpzbTFxMTQAAgSjF29aAwQwhScA', '1', null, '1', '1517229989', '1517229989');
INSERT INTO `st_dimension` VALUES ('98', 'gQGi7zwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyNEpFaWx6MmJkOTAxRm93bWhxMVgAAgQoG29aAwQwhScA', '1', null, '1', '1517230889', '1517230889');
INSERT INTO `st_dimension` VALUES ('99', 'gQF88DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAydXJFM2tfMmJkOTAxSEZJbTFxMVYAAgS5J29aAwQwhScA', '1', null, '1', '1517234107', '1517234107');
INSERT INTO `st_dimension` VALUES ('100', 'gQFx7zwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyWkpZemtaMmJkOTAxRmtSbWhxMXcAAgQkMG9aAwQwhScA', '1', null, '1', '1517236262', '1517236262');
INSERT INTO `st_dimension` VALUES ('101', 'gQFj8DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyckRCNGstMmJkOTAxeHlSbjFxMWUAAgQysG9aAwQwhScA', '1', null, '1', '1517269042', '1517269042');
INSERT INTO `st_dimension` VALUES ('102', 'gQFa8DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyaUdFM2xsMmJkOTAxemxfbnhxMWsAAgSmum9aAwQwhScA', '1', null, '1', '1517271718', '1517271718');
INSERT INTO `st_dimension` VALUES ('103', 'gQFW8DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAybmZxUGxFMmJkOTAxenFfbjFxMWcAAgSqum9aAwQwhScA', '1', null, '1', '1517271723', '1517271723');
INSERT INTO `st_dimension` VALUES ('104', 'gQFo8DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyRXhKUmxOMmJkOTAxenJfbk5xMWkAAgSrum9aAwQwhScA', '1', null, '1', '1517271723', '1517271723');
INSERT INTO `st_dimension` VALUES ('105', 'gQFG8DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyUmZXSGtfMmJkOTAxQnIwbk5xMWMAAgQru29aAwQwhScA', '1', null, '1', '1517271852', '1517271852');
INSERT INTO `st_dimension` VALUES ('106', 'gQE68DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyWXpnaWs5MmJkOTAxQ0U5bjFxMTMAAgR4xG9aAwQwhScA', '1', null, '1', '1517274233', '1517274233');
INSERT INTO `st_dimension` VALUES ('107', 'gQEZ8DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyemNUZWx4MmJkOTAxMDAwMHcwM1QAAgRq9F5aAwQAAAAA', '4', '0', '0', '1517275412', '1517275412');
INSERT INTO `st_dimension` VALUES ('108', 'gQFp8TwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAydmFWRms2MmJkOTAxMDAwME0wM2sAAgST9F5aAwQAAAAA', '4', '1', '0', '1517275412', '1517275412');
INSERT INTO `st_dimension` VALUES ('109', 'gQFI8TwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyZ2ROU2tPMmJkOTAxMDAwME0wM3oAAgST9F5aAwQAAAAA', '4', '2', '0', '1517275412', '1517275412');
INSERT INTO `st_dimension` VALUES ('110', 'gQE18TwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAySHN4ZGxfMmJkOTAxMDAwMDAwM2EAAgSf9V5aAwQAAAAA', '3', null, '0', '1517275412', '1517275412');
INSERT INTO `st_dimension` VALUES ('111', 'gQFC8TwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyM0tfb2t1MmJkOTAxMDAwMGcwM1MAAgSf9V5aAwQAAAAA', '4', '0', '0', '1517275503', '1517275503');
INSERT INTO `st_dimension` VALUES ('112', 'gQEe8TwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAySWpOdGw4MmJkOTAxMDAwMDAwMzgAAgQf-l5aAwQAAAAA', '4', '1', '0', '1517275503', '1517275503');
INSERT INTO `st_dimension` VALUES ('113', 'gQG-8DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAySmlVQWxVMmJkOTAxMDAwMHcwM18AAgQf-l5aAwQAAAAA', '4', '2', '0', '1517275503', '1517275503');
INSERT INTO `st_dimension` VALUES ('114', 'gQFz8TwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyUWNHN2xMMmJkOTAxMDAwMGcwM1MAAgSXDGBaAwQAAAAA', '3', null, '0', '1517275503', '1517275503');
INSERT INTO `st_dimension` VALUES ('115', 'gQF38DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyNWw4TGxZMmJkOTAxQXdmbnhxMTIAAgTwyW9aAwQwhScA', '1', null, '1', '1517275632', '1517275632');
INSERT INTO `st_dimension` VALUES ('116', 'gQHC8TwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyQ2NFUmtLMmJkOTAxMDAwMDAwMzIAAgSXDGBaAwQAAAAA', '5', '0', '0', '1517275925', '1517275925');
INSERT INTO `st_dimension` VALUES ('117', 'gQFq8TwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAySkh1bGtRMmJkOTAxMDAwMDAwM0oAAgSaDWBaAwQAAAAA', '5', '1', '0', '1517275925', '1517275925');
INSERT INTO `st_dimension` VALUES ('118', 'gQHz8DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAydVNxUmttMmJkOTAxMDAwMGcwM1YAAgSaDWBaAwQAAAAA', '5', '2', '0', '1517275925', '1517275925');
INSERT INTO `st_dimension` VALUES ('119', 'gQEk8TwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyazV0X2xyMmJkOTAxMDAwMDAwM1oAAgSuDWBaAwQAAAAA', '2', null, '0', '1517275925', '1517275925');
INSERT INTO `st_dimension` VALUES ('120', 'gQH28DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyMlV3Z2xpMmJkOTAxMDAwMGcwM2gAAgSuDWBaAwQAAAAA', '5', '0', '0', '1517276319', '1517276319');
INSERT INTO `st_dimension` VALUES ('121', 'gQGP8DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyYjRpYmxxMmJkOTAxMDAwMDAwM3oAAgQ1DmBaAwQAAAAA', '5', '1', '0', '1517276319', '1517276319');
INSERT INTO `st_dimension` VALUES ('122', 'gQHT8DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyQkQ0T2tmMmJkOTAxMDAwMHcwM1IAAgQ2DmBaAwQAAAAA', '5', '2', '0', '1517276319', '1517276319');
INSERT INTO `st_dimension` VALUES ('123', 'gQG58DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAya1NSLWtZMmJkOTAxMDAwMHcwMzMAAgRIDmBaAwQAAAAA', '2', null, '0', '1517276319', '1517276319');
INSERT INTO `st_dimension` VALUES ('124', 'gQFB8DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyZHl2bGt5MmJkOTAxMDAwME0wMzMAAgRIDmBaAwQAAAAA', '5', '1', '0', '1517277169', '1517277169');
INSERT INTO `st_dimension` VALUES ('125', 'gQHc8TwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyUllHZ2xVMmJkOTAxMDAwMDAwMzcAAgSNymZaAwQAAAAA', '5', '2', '0', '1517277169', '1517277169');
INSERT INTO `st_dimension` VALUES ('126', 'gQHe8TwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyQjY1UGtOMmJkOTAxMDAwMHcwM1EAAgSOymZaAwQAAAAA', '2', null, '0', '1517277169', '1517277169');
INSERT INTO `st_dimension` VALUES ('127', 'gQEL8TwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAybHVfcGtrMmJkOTAxMDAwMGcwM2QAAgSOymZaAwQAAAAA', '5', '2', '0', '1517277393', '1517277393');
INSERT INTO `st_dimension` VALUES ('128', 'gQE_8TwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyV2xWZmtuMmJkOTAxMDAwMHcwM08AAgSOymZaAwQAAAAA', '2', null, '0', '1517277393', '1517277393');
INSERT INTO `st_dimension` VALUES ('129', 'gQF67zwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyb2VibWxzMmJkOTAxQ2ptbjFxMWcAAgRj0W9aAwQwhScA', '1', null, '1', '1517277540', '1517277540');
INSERT INTO `st_dimension` VALUES ('130', 'gQFb8DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAycXAzU2tmMmJkOTAxRFdxbk5xMVYAAgTK1W9aAwQwhScA', '1', null, '1', '1517278666', '1517278666');
INSERT INTO `st_dimension` VALUES ('131', 'gQEE8TwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyQXo2TWtFMmJkOTAxMDAwMDAwM3gAAgRPy2ZaAwQAAAAA', '5', '2', '0', '1517278695', '1517278695');
INSERT INTO `st_dimension` VALUES ('132', 'gQEK8TwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyMEFLVmxyMmJkOTAxMDAwMGcwMzgAAgRQy2ZaAwQAAAAA', '2', null, '0', '1517278695', '1517278695');
INSERT INTO `st_dimension` VALUES ('133', 'gQHQ8DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAySXcyUWtDMmJkOTAxRE9ybjFxMVMAAgTC1m9aAwQwhScA', '1', null, '1', '1517278915', '1517278915');
INSERT INTO `st_dimension` VALUES ('134', 'gQFE8DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyNXUwM2syMmJkOTAxQlRzbnhxMVUAAgRH129aAwQwhScA', '1', null, '1', '1517279048', '1517279048');
INSERT INTO `st_dimension` VALUES ('135', 'gQEc8DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyYVNCdWtxMmJkOTAxQXd3bjFxMVUAAgTw2m9aAwQwhScA', '1', null, '1', '1517279984', '1517279984');
INSERT INTO `st_dimension` VALUES ('136', 'gQEN8DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyamhYZGxOMmJkOTAxQlBBbjFxMWcAAgRD329aAwQwhScA', '1', null, '1', '1517281092', '1517281092');
INSERT INTO `st_dimension` VALUES ('137', 'gQGn8DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyWG5JRGxaMmJkOTAxRGRQbjFxMW4AAgSd7m9aAwQwhScA', '1', null, '1', '1517285021', '1517285021');
INSERT INTO `st_dimension` VALUES ('138', 'gQFl8DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyZGVpT2wtMmJkOTAxRExSbnhxMVoAAgS-8G9aAwQwhScA', '1', null, '1', '1517285567', '1517285567');
INSERT INTO `st_dimension` VALUES ('139', 'gQEp8DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAySzVjSmtpMmJkOTAxQkVTbk5xMWcAAgQ48W9aAwQwhScA', '1', null, '1', '1517285688', '1517285688');
INSERT INTO `st_dimension` VALUES ('140', 'gQFb8DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyS1RiZGxKMmJkOTAxQjdVbk5xMUYAAgQX829aAwQwhScA', '1', null, '1', '1517286168', '1517286168');
INSERT INTO `st_dimension` VALUES ('141', 'gQGB8DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAySlFIYmszMmJkOTAxQzVVbjFxMTgAAgRV829aAwQwhScA', '1', null, '1', '1517286230', '1517286230');
INSERT INTO `st_dimension` VALUES ('142', 'gQEz8DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyeGNCZ2s4MmJkOTAxQVotbmhxMVIAAgQN_W9aAwQwhScA', '1', null, '1', '1517287694', '1517287694');
INSERT INTO `st_dimension` VALUES ('143', 'gQFH8DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyTzQ5dGxQMmJkOTAxRjkybnhxMXAAAgQZ-W9aAwQwhScA', '1', null, '1', '1517288730', '1517288730');
INSERT INTO `st_dimension` VALUES ('144', 'gQGx8DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyMG1zbGxXMmJkOTAxSDYybmhxMUcAAgSW-W9aAwQwhScA', '1', null, '1', '1517288855', '1517288855');
INSERT INTO `st_dimension` VALUES ('145', 'gQHd8DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAySUlTb2tWMmJkOTAxSDY5bmhxMTYAAgSWBHBaAwQwhScA', '1', null, '1', '1517290647', '1517290647');
INSERT INTO `st_dimension` VALUES ('146', 'gQFd7zwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyTmM4WGtxMmJkOTAxR3lhbnhxMVcAAgRyBXBaAwQwhScA', '1', null, '1', '1517290867', '1517290867');
INSERT INTO `st_dimension` VALUES ('147', 'gQG28DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAycFlNQ2xJMmJkOTAxR1Zhbk5xMUMAAgSJBXBaAwQwhScA', '1', null, '1', '1517290890', '1517290890');
INSERT INTO `st_dimension` VALUES ('148', 'gQHL7zwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAybERCUGs5MmJkOTAxR2hmbmhxMUgAAgRhCnBaAwQwhScA', '1', null, '1', '1517292130', '1517292130');
INSERT INTO `st_dimension` VALUES ('149', 'gQFt7zwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAycmVsc2xuMmJkOTAxRVZpbmhxMWIAAgQJDXBaAwQwhScA', '1', null, '1', '1517292810', '1517292810');
INSERT INTO `st_dimension` VALUES ('150', 'gQFq7zwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAya2t1MGxtMmJkOTAxRnNsbnhxMUoAAgQsEHBaAwQwhScA', '1', null, '1', '1517293613', '1517293613');
INSERT INTO `st_dimension` VALUES ('151', 'gQE68DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyTHIyeWxhMmJkOTAxTGVObzFxMTUAAgSebHFaAwQwhScA', '1', null, '1', '1517382814', '1517382814');
INSERT INTO `st_dimension` VALUES ('152', 'gQEC8DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAybHdQUGwyMmJkOTAxTGZOb2hxMUkAAgSfbHFaAwQwhScA', '1', null, '1', '1517382815', '1517382815');
INSERT INTO `st_dimension` VALUES ('153', 'gQFk8DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyblE5T2s0MmJkOTAxTGZObzFxMVkAAgSfbHFaAwQwhScA', '1', null, '1', '1517382816', '1517382816');
INSERT INTO `st_dimension` VALUES ('154', 'gQHd7zwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAydldXaWx4MmJkOTAxTGdOb05xMTYAAgSgbHFaAwQwhScA', '1', null, '1', '1517382816', '1517382816');
INSERT INTO `st_dimension` VALUES ('155', 'gQGG7zwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAybHpmemttMmJkOTAxTGhOb2hxMUQAAgShbHFaAwQwhScA', '1', null, '1', '1517382817', '1517382817');
INSERT INTO `st_dimension` VALUES ('156', 'gQFQ8DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyM20wWmxJMmJkOTAxTGhOb05xMVMAAgShbHFaAwQwhScA', '1', null, '1', '1517382818', '1517382818');
INSERT INTO `st_dimension` VALUES ('157', 'gQGz7zwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyeUVXdWxnMmJkOTAxTGlOb2hxMVYAAgSibHFaAwQwhScA', '1', null, '1', '1517382818', '1517382818');
INSERT INTO `st_dimension` VALUES ('158', 'gQHY7zwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyRVZtcmttMmJkOTAxTGpObzFxMXEAAgSjbHFaAwQwhScA', '1', null, '1', '1517382819', '1517382819');
INSERT INTO `st_dimension` VALUES ('159', 'gQHu7zwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyZFdWNmtJMmJkOTAxTGtOb3hxMWkAAgSkbHFaAwQwhScA', '1', null, '1', '1517382820', '1517382820');
INSERT INTO `st_dimension` VALUES ('160', 'gQFP8DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyRU9EYWw1MmJkOTAxTEROb2hxMU0AAgS3bHFaAwQwhScA', '1', null, '1', '1517382839', '1517382839');
INSERT INTO `st_dimension` VALUES ('161', 'gQH_7zwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyR1RHV2tkMmJkOTAxTEVOb2hxMW4AAgS4bHFaAwQwhScA', '1', null, '1', '1517382840', '1517382840');
INSERT INTO `st_dimension` VALUES ('162', 'gQHW7zwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyRnc1c2wtMmJkOTAxSnVPbzFxMTEAAgQubXFaAwQwhScA', '1', null, '1', '1517382958', '1517382958');
INSERT INTO `st_dimension` VALUES ('163', 'gQFq8TwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAySkh1bGtRMmJkOTAxMDAwMDAwM0oAAgSaDWBaAwQAAAAA', '4', '0', '0', '1517382997', '1517382997');
INSERT INTO `st_dimension` VALUES ('164', 'gQHz8DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAydVNxUmttMmJkOTAxMDAwMGcwM1YAAgSaDWBaAwQAAAAA', '4', '1', '0', '1517382997', '1517382997');
INSERT INTO `st_dimension` VALUES ('165', 'gQEk8TwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyazV0X2xyMmJkOTAxMDAwMDAwM1oAAgSuDWBaAwQAAAAA', '4', '2', '0', '1517382997', '1517382997');
INSERT INTO `st_dimension` VALUES ('166', 'gQH28DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyMlV3Z2xpMmJkOTAxMDAwMGcwM2gAAgSuDWBaAwQAAAAA', '3', null, '0', '1517382997', '1517382997');
INSERT INTO `st_dimension` VALUES ('167', 'gQGP8DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyYjRpYmxxMmJkOTAxMDAwMDAwM3oAAgQ1DmBaAwQAAAAA', '4', '0', '0', '1517383366', '1517383366');
INSERT INTO `st_dimension` VALUES ('168', 'gQHT8DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyQkQ0T2tmMmJkOTAxMDAwMHcwM1IAAgQ2DmBaAwQAAAAA', '4', '1', '0', '1517383366', '1517383366');
INSERT INTO `st_dimension` VALUES ('169', 'gQG58DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAya1NSLWtZMmJkOTAxMDAwMHcwMzMAAgRIDmBaAwQAAAAA', '4', '2', '0', '1517383366', '1517383366');
INSERT INTO `st_dimension` VALUES ('170', 'gQFB8DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyZHl2bGt5MmJkOTAxMDAwME0wMzMAAgRIDmBaAwQAAAAA', '3', null, '0', '1517383366', '1517383366');

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
INSERT INTO `st_dimension_num` VALUES ('14', '57', '1');

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
  `gold_num` int(11) unsigned NOT NULL COMMENT '金币',
  `silver` int(11) unsigned NOT NULL COMMENT '银币',
  `describe` varchar(255) DEFAULT NULL COMMENT '充值描述',
  `current_gold_num` varchar(255) DEFAULT NULL COMMENT '当前金币',
  `current_silver` int(11) unsigned DEFAULT NULL COMMENT '当前银币',
  `addtime` int(11) NOT NULL COMMENT '充值时间',
  `updatetime` int(11) unsigned NOT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=37 DEFAULT CHARSET=utf8;

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
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of st_users
-- ----------------------------
INSERT INTO `st_users` VALUES ('11', 'oQktJwL8ioR4DoxSQmikdzekbUyU', null, null, null, null, null, null, '58D5FCCF291', '5', 'gQHW7zwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyRnc1c2wtMmJkOTAxSnVPbzFxMTEAAgQubXFaAwQwhScA', '13', '1519972958', '吴智彬-13425492760', 'http://wx.qlogo.cn/mmopen/Q3auHgzwzM4IUw4vejmKA64ib5YEGDyFzrOkCVqa7EasmHUxvGDb5dA9nfjQ7x0zffsF5hCiaEvZgnt7WHjpeEPjAB7ibq9nZu0Ep2fcqtaH84/132', '1', '广东', '中国 广东 广州', '0', '0', '0.00', '1517382957', '1517382957');

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
INSERT INTO `st_users_commission` VALUES ('1', '0', '123456', '800', '900', '800', '900', '自己购买，单号为[123456]的商品获得金币奖励:800个,获得银币奖励：900个', '0', '0');
INSERT INTO `st_users_commission` VALUES ('2', '0', '123456', '800', '900', '1600', '1800', '自己购买，单号为[123456]的商品获得金币奖励:800个,获得银币奖励：900个', '0', '0');
INSERT INTO `st_users_commission` VALUES ('3', '0', '123456', '800', '900', '2400', '2700', '自己购买，单号为[123456]的商品获得金币奖励:800个,获得银币奖励：900个', '0', '0');

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
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of st_vendors
-- ----------------------------
INSERT INTO `st_vendors` VALUES ('1', null, 'admin', '202cb962ac59075b964b07152d234b70', '13425492760', '超级管理员', null, null, null, null, null, null, null, null, null, '0', null, null, null, null, null, null, null, '', null, '0.00', null, null, null, '0', '7', '1513872000', '1513872000');
INSERT INTO `st_vendors` VALUES ('11', null, 'diyijia', 'e10adc3949ba59abbe56e057f20f883e', '15820250362', '第一分公司', '425136198902056325', '/Vendors/2018-01-31/5a716d547e665.png', '/Vendors/2018-01-31/5a716d547ec6a.png', '/Vendors/2018-01-31/5a716d547edd3.jpg', '广厚有限公司', '020-58960253', '广州市天河区天河路', '/Vendors/2018-01-31/5a716d547f0d6.jpg', '/Vendors/2018-01-31/5a716d547f879.jpg', '1', 'F373EC', null, null, 'gQFq8TwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAySkh1bGtRMmJkOTAxMDAwMDAwM0oAAgSaDWBaAwQAAAAA', 'gQHz8DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAydVNxUmttMmJkOTAxMDAwMGcwM1YAAgSaDWBaAwQAAAAA', 'gQEk8TwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyazV0X2xyMmJkOTAxMDAwMDAwM1oAAgSuDWBaAwQAAAAA', 'gQH28DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyMlV3Z2xpMmJkOTAxMDAwMGcwM2gAAgSuDWBaAwQAAAAA', null, null, '0.00', null, 'admin', null, '3', '7', '1517382996', '1517382996');
INSERT INTO `st_vendors` VALUES ('12', null, 'dierjia', 'e10adc3949ba59abbe56e057f20f883e', '18952025323', '第二分公司', '425136198902054252', '/Vendors/2018-01-31/5a716ec604a7c.png', '/Vendors/2018-01-31/5a716ec604fb8.png', '/Vendors/2018-01-31/5a716ec605102.jpg', '明星公司', '020-89525896', '广州市番禺区钟村', '/Vendors/2018-01-31/5a716ec6053e7.jpg', '/Vendors/2018-01-31/5a716ec605b71.jpg', '1', 'B3C7A5', null, null, 'gQGP8DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyYjRpYmxxMmJkOTAxMDAwMDAwM3oAAgQ1DmBaAwQAAAAA', 'gQHT8DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyQkQ0T2tmMmJkOTAxMDAwMHcwM1IAAgQ2DmBaAwQAAAAA', 'gQG58DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAya1NSLWtZMmJkOTAxMDAwMHcwMzMAAgRIDmBaAwQAAAAA', 'gQFB8DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyZHl2bGt5MmJkOTAxMDAwME0wMzMAAgRIDmBaAwQAAAAA', null, null, '0.00', null, 'admin', null, '3', '7', '1517383366', '1517383366');

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
INSERT INTO `st_vendors_commission` VALUES ('1', '0', '123456', '1000.00', '1000.00', '分销商下的会员吴智彬-13425492760购买单号：123456的商品获得佣金奖励:1000元', '0', '0');
INSERT INTO `st_vendors_commission` VALUES ('2', '0', '123456', '1000.00', '2000.00', '分销商下的会员吴智彬-13425492760购买单号：123456的商品获得佣金奖励:1000元', '0', '0');
INSERT INTO `st_vendors_commission` VALUES ('3', '0', '123456', '1000.00', '3000.00', '分销商下的会员吴智彬-13425492760购买单号：123456的商品获得佣金奖励:1000元', '0', '0');

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
) ENGINE=InnoDB AUTO_INCREMENT=360 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of st_wechat
-- ----------------------------
INSERT INTO `st_wechat` VALUES ('358', 'oQktJwL8ioR4DoxSQmikdzekbUyU', null, '0', null, '1', '1517382008', '1517382008', '0');
INSERT INTO `st_wechat` VALUES ('359', 'oQktJwAGFgfYYdpYhBgD-54fKJTg', null, '0', null, '0', '1517382521', '1517382521', '0');

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
