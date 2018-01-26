<?php
namespace Admin\Model;
use Think\Model;

/**
 * 分类
 */
class AttrValModel extends Model
{
    // 自动验证
    protected $_validate = array(
        array('val', 'require', '属性值不能为空'),
    );

}
