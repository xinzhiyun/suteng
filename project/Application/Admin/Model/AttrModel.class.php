<?php
namespace Admin\Model;
use Think\Model;

/**
 * 分类
 */
class AttrModel extends Model
{
    // 自动验证
    protected $_validate = array(
        array('attr', 'require', '属性名不能为空'),
        array('attr','','不可重复添加',0,'unique',1),
    );

}
