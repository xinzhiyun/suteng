<?php
namespace Admin\Model;
use Think\Model;

/**
 * 分类
 */
class CategoryModel extends BaseModel
{
    // 自动验证
    protected $_validate = array(
        array('pid', 'require', '上级分类不能为空'),
        array('vid', 'require', '刷新页面确认是否登陆'),
        array('name', 'require', '分类名不能为空'),
        array('name','/^[a-zA-Z0-9\x{4e00}-\x{9fa5}]{1,660}$/u', '不可包含特殊字符','regex')
    );

    // 自动完成
    protected $_auto = array(

    );
}
