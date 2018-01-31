<?php
namespace Home\Model;
use Think\Model;
class AddressModel extends BaseMode
{
    // 自动验证
    protected $_validate = array(
        array('name','require','请输入收货人姓名！'),
        array('name','/^[A-Za-z0-9_\x{4e00}-\x{9fa5}]+$/u','姓名格式不正确！', '3', 'regex'),
        array('phone', '/1(3|4|5|7|8|9)\d{9}$/', '手机号码格式不正确', '3', 'regex'),
        array('address','require','请确认地址合法性！'),
    );
}