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

INSERT INTO `st_admin_menu` VALUES ('14', '13', '设备列表', 'Admin/Devices/devicesList', '', null);

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

INSERT INTO `st_attr` VALUES ('5', '内存');

INSERT INTO `st_attr` VALUES ('6', '颜色');

INSERT INTO `st_attr_val` VALUES ('42', '5', '4G');

INSERT INTO `st_attr_val` VALUES ('42', '6', '蓝色');

INSERT INTO `st_attr_val` VALUES ('43', '5', '3G');

INSERT INTO `st_attr_val` VALUES ('43', '6', '蓝色');

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

INSERT INTO `st_auth_group_access` VALUES ('1', '1');

INSERT INTO `st_auth_group_access` VALUES ('1', '4');

INSERT INTO `st_auth_group_access` VALUES ('22', '1');

INSERT INTO `st_auth_group_access` VALUES ('22', '5');

INSERT INTO `st_auth_group_access` VALUES ('23', '14');

INSERT INTO `st_auth_group_access` VALUES ('24', '12');

INSERT INTO `st_auth_group_access` VALUES ('28', '1');

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

INSERT INTO `st_auth_rule` VALUES ('15', '14', 'Admin/Devices/devicesList', '设备列表', '1', '1', '');

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

INSERT INTO `st_cart` VALUES ('15', '42', '3', '2', '0', '1517396746');

INSERT INTO `st_cart` VALUES ('16', '43', '2', '2', '0', '1517396812');

INSERT INTO `st_category` VALUES ('23', '0', '家电', '0');

INSERT INTO `st_category` VALUES ('24', '0', '服装', '0');

INSERT INTO `st_category` VALUES ('25', '0', '手机', '0');

INSERT INTO `st_category` VALUES ('26', '0', '图书', '0');

INSERT INTO `st_category` VALUES ('27', '0', '艺术', '0');

INSERT INTO `st_devices` VALUES ('1', '112233445566776', '1', '1', null, '0', '1');

INSERT INTO `st_devices` VALUES ('2', '112233445566778', '1', '1', '1', '1517410185', '37');

INSERT INTO `st_devices` VALUES ('3', '133466355844981', '1', '0', null, '1517412164', '37');

INSERT INTO `st_devices` VALUES ('4', '654123546852147', '1', '0', null, '1517413045', '1');

INSERT INTO `st_filters` VALUES ('1', 'RO膜', '第一', '', '0.00', '0', '0', null, null, '暂无简介', '0', null, '1517413045');

INSERT INTO `st_filters` VALUES ('16', 'A型', 'PP膜', '', '200.00', '3000', '6000', null, null, '', '0', '', '1517413649');

INSERT INTO `st_goods` VALUES ('42', '25', null, '华为', null, null, null, null, '0', '0', '1517396654', '1517396654');

INSERT INTO `st_goods` VALUES ('43', '25', null, '小米', null, null, null, null, '0', '0', '1517396689', '1517396689');

INSERT INTO `st_goods_detail` VALUES ('31', '42', '1999.00', '1500.00', '153', null, '0');

INSERT INTO `st_goods_detail` VALUES ('32', '43', '2599.00', '1300.00', '163', null, '0');

INSERT INTO `st_order_detail` VALUES ('21', '167928449789119', '42', '3', '1500.00', '1999.00', '0', '1517396751');

INSERT INTO `st_order_detail` VALUES ('22', '129593890191420', '42', '3', '1500.00', '1999.00', '0', '1517396792');

INSERT INTO `st_order_detail` VALUES ('23', '416126755371508', '43', '2', '1300.00', '2599.00', '0', '1517396819');

INSERT INTO `st_pic` VALUES ('2', '43', '5a71a3b5d27db.jpg', '2018-01-31/5a71a3b5d27db.jpg');

INSERT INTO `st_pic` VALUES ('3', '42', '5a71a3ba61b76.jpg', '2018-01-31/5a71a3ba61b76.jpg');

INSERT INTO `st_shop_order` VALUES ('9', '167928449789119', '2', null, null, '4500.00', '5997.00', '3', '1517396751', '8', null, null);

INSERT INTO `st_shop_order` VALUES ('10', '129593890191420', '2', null, null, '4500.00', '5997.00', '3', '1517396792', '8', null, null);

INSERT INTO `st_shop_order` VALUES ('11', '416126755371508', '2', null, null, '2600.00', '5198.00', '2', '1517396819', '8', null, null);

INSERT INTO `st_type` VALUES ('1', 'RO膜', 'RO膜-第一', 'RO膜-第一', null, null, null, null, null, null, '0', null);

INSERT INTO `st_type` VALUES ('37', '家用型', '1', null, null, null, null, null, null, null, '0', '1517410143');

INSERT INTO `st_type` VALUES ('38', '商用型', 'RO膜-第一', 'A型-PP膜', null, null, null, null, null, null, '0', '1517413679');

INSERT INTO `st_vendors` VALUES ('1', null, 'admin', '202cb962ac59075b964b07152d234b70', '13425492760', '超级管理员', null, null, null, null, null, null, null, null, null, '0', null, null, null, null, null, null, null, '', null, '0.00', null, null, null, '0', '7', '1513872000', '1513872000');
