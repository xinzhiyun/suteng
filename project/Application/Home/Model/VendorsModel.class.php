<?php
namespace Home\Model;
use Think\Model;
/**
 * Class 分销商管理
 * @package Admin\Model\
 * @author 吴智彬 <519002008@qq.com>
 */
class VendorsModel extends Model
{   
    /**
     * [$_validate 自动验证]
     * @var array
     */
    protected $_validate = array(
        array('user','','账号已经存在！',0,'unique',1), // 在新增的时候验证user字段是否唯一
        array('user','/^[a-zA-Z][\w]{1,29}$/','账号格式不对！',1,'regex'), // 字母开头，数字字母下划线，总长度 2-30位
        array('repassword','require','密码必须填写',1),
        array('repassword','password','确认密码不正确',0,'confirm'), // 验证确认密码是否和密码一致
    );

    /**
     * [$_auto 自动完成]
     * @var array
     */
    protected $_auto = array ( 
        array('password','md5',3,'function') ,    // 对password字段在新增和编辑的时候使md5函数处理
        array('addtime','time',3,'function'),     // 对addtime字段在新增和编辑的时候写入当前时间戳
        array('updatetime','time',3,'function'),  // 对update_time字段在更新的时候写入当前时间戳
    );
}