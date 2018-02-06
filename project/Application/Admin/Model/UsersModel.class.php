<?php
namespace Admin\Model;

use Think\Model;

/**
 * Class 用户数据处理
 * @package Admin\Model
 * @author 潘宏钢 <619328391@qq.com>
 */
class UsersModel extends Model
{   
    // 自动验证
    protected $_validate = array(
        array('name','require','账户名不能为空'),
        array('name','','该账户已存在，请换一个试试',0,'unique',1),
        array('repassword','password','两次密码不相同',0,'confirm'), //验证确认密码是否和密码一致
        array('phone','/^1[34578]\d{9}$/','电话号码格式不对',1,'regex'),
        array('email','/([a-z0-9]*[-_\.]?[a-z0-9]+)*@([a-z0-9]*[-_]?[a-z0-9]+)+[\.][a-z]{2,3}([\.][a-z]{2})?/i','邮箱格式不对',1,'regex'),
        array('company','require','公司不能为空'),
        array('address','require','地址不能为空'),
        array('idcard','/^(\d{15}$|^\d{18}$|^\d{17}(\d|X|x))$/','身份证格式不对',1,'regex')
    );


    // 自动完成
    protected $_auto = array ( 
        array('addtime','time',3,'function'), // 对addtime字段在新增和编辑的时候写入当前时间戳 
        array('password','md5',3,'function') , // 对password字段在新增和编辑的时候使md5函数处理
        
    );

    // 查询用户绑定的信息
    public function getBindInfo($where=array())
    {
        $data = $this
            ->alias('u')
            ->where($where)
            ->join('__USER_DEVICE__ ud ON u.id=ud.uid')
            ->join('__DEVICES__ d ON ud.did=d.id')
            ->join('__DEVICES_STATU__ ds ON d.device_code=ds.DeviceID')
            ->join('__TYPE__ t ON d.type_id=t.id')
            ->field('d.device_code,t.typename,ud.name,ud.phone,ud.address,ud.status,ud.addtime')
            ->select();
        if(empty($data)) $data = null;
        return $data;
    }

    // 设备解除绑定
    public function unBind($where=array(),$uid=array())
    {
        $use_device = M('UserDevice');
        $this->startTrans();
        $id = M('devices')->where($where)->find();
        if(empty($id)) E("请重试", 10061);
        $uid['did'] = $id['id'];
        $data = $use_device->where('uid='.$uid['uid'])->select();
        if(count($data) == 1){
            $f_status = $use_device->where($uid)->delete();
        } else {
            $arr = [];
            $f_status = $use_device->where($uid)->delete();
            foreach ($data as $key => $value) {
                $arr[$key] = $value['did'];
                if($value['did'] == $uid['did']){
                    unset($arr[$key]);
                }
            }
            $res = $use_device->where(['uid='.$uid['uid'],['status'=>1]])->find();
            if(!$res){
                $uid['did'] = $arr[array_rand($arr)];
                $res = $use_device->where($uid)->save(['status'=>1]);
            }
        }
        if($res && $f_status){
            $this->commit();
            return true;
        } else {
            $this->rollback();
            return false;
        }
    }
}