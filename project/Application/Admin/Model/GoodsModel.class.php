<?php
namespace Admin\Model;
use Think\Model;

/**
 * 分类
 */
class GoodsModel extends BaseModel
{
    // 自动验证
    protected $_validate = array(
        array('cid','/^[\d]{0,10}$/', '请选择分类','regex'),
        array('name', 'require', '商品名不能为空'),
        // array('name','/^[\(\)（）【】\[\]—_\-a-zA-Z0-9\x{4e00}-\x{9fa5}]{1,660}$/u', '商品名称格式不正确','regex'),
        array('price','/^[\d]+[\.][\d]{2}$/', '请输入正确的价格','regex'),
        array('stock','/^[1-9][\d]{0,10}$/', '请输入正确的库存','regex'),
    );

    // 自动完成
    protected $_auto = array ( 
        array('addtime','time',3,'function'),
        array('updatetime','time',3,'function'),
    );

}
