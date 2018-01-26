<?php
namespace Admin\Model;
use Think\Model;

/**
 * 分类
 */
class GoodsDetailModel extends Model
{
    // 自动验证
    protected $_validate = array(
        array('price', 'require', '请填写销售价'),
        array('price','/^[\d]+[\.][\d]{2}$/', '请输入正确的销售价格','regex'),
        array('cost', 'require', '请填写成本价'),
        array('cost','/^[\d]+[\.][\d]{2}$/', '请输入正确的成本价格','regex'),
        array('stock', 'require', '请输入库存量'),
        array('stock','/^[1-9][\d]{0,10}$/', '库存量为整数','regex'),
    );

}
