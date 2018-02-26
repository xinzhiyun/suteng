<?php
namespace Home\Model;
use Think\Model;
class RepairModel extends Model
{
    /**
     * [$_validate 自动验证]
     * @var array
     */
    protected $_validate = array(
        array('uid','require','uID不能为空'),
        array('device_code','require','设备编号不能为空'),
        array('date','require','日期不能为空'),
        array('picpath','require','图片路径不能为空'),
        array('reason','require','工单号不能为空'),
        array('truename','require','姓名不能为空'),
        array('truename',"/^[\w\x{4e00}-\x{9fa5}]{2,50}$/u",'姓名不能有特殊符号'),
        array('phone','/^1[34578]\d{9}$/','电话号码格式不对'),
        array('content','require','报修内容不能为空'),
        array('content',"/^[\w\x{4e00}-\x{9fa5}]{2,50}$/u",'报修内容不能有特殊符号'),
        array('address','require','地址不能为空'),
    );
}