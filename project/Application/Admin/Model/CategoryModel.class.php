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
    // 获取所有分类
    public function getAllCate()
    {
        $res = $this->getTreeData('tree','id, name');
        return $res;
    }

    // 确认分类
    public function sureCate()
    {
        if($_POST['thirdcate'] != '--'){
            $cid = $_POST['thirdcate'];
        } elseif ($_POST['seccate'] != '--'){
            $cid = $_POST['seccate'];
        } elseif ($_POST['firscate'] != '--'){
            $cid = $_POST['firscate'];
        } else {
            $cid = "";
        }

        $res = $this->where('id='.$cid)->field('id');
        if($res){
            return $cid;
        } else {
            E('分类不存在，请刷新页面重试',506);
        }
    }
}
