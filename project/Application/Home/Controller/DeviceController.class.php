<?php
namespace Home\Controller;
use \Org\Util\WeixinJssdk;

class DeviceController extends CommonController
{
    /**
     * [index 设备主页]
     * @return [type] [description]
     */
    public function index()
    {
        //调用微信JS-SDK类获取签名需要用到的数据
        $weixin = new WeixinJssdk;
        $signPackage = $weixin->getSignPackage();
        // 查询用户微信中的openid
        //$openId = $weixin->GetOpenid();
        // 调试用默认用户
        $openId = $_SESSION['homeuser']['open_id'];
        // 查询绑定设备
        $user_device = D('UserDevice');
        $map['ud.uid'] = session('user.id');

        $bind_device = $user_device->getBindInof($map); //where('uid='.session('user.id'))->select();
        // 将当前设备的ID存到SESSION中
        $where['status'] = 1;
        $where['uid'] = session('user.id');
        $cur_device = $user_device->where($where)->find();
        session('device.did',$cur_device['did']);
        //分配数据        
        $this->assign('info',$signPackage);
        $this->assign('bindInfo',$bind_device);
        $this->assign('openId',$openId);
    	$this->display();
    }
    
    // 设备绑定
    public function bind()
    {
        try {
            $device = D('Devices');
            $user_device = D('UserDevice');
            $uid = session('user.id');
            $code = I('post.device_code');
            $device->startTrans();
            $res = $device->where('device_code='.$code)->find();
            if(!$res) E('设备不存在',603);
            $did = $res['id'];
            $type = $res['type_id'];
            $res_find = $user_device->where('did='.$did)->find();
            if($res_find != null) E('设备已绑定',604);
            $data['did']        = $did;
            $data['uid']        = $uid;
            $data['status']     = 1;
            $data['addtime']    = time();
            $data['updatetime'] = time();
            $user_device->where('uid='.$uid)->save(['status'=>0]);
            $res_save = $user_device->add($data);

            // 查询设备类型
            $device_type = M('Type')->where('id='.$type)->field('type')->find();
            if($device_type['type']){
                // 商务
                $leavel = 3;
            } else {
                // 家用
                $leavel = 2;
            }

            // 查询当前会员最高等级
            $cur_device = $user_device->where('id='.$uid)->field('did')->select();
            $arr = [];
            foreach ($cur_device as $key => $value) {
                $arr[$key] = $value;
            }
            $arr = max($arr);

            // 根据会员级别更新等级
            if($arr < $leavel){
                // 更新用户表会员等级
                M('Users')->where('id='.$uid)->save(['grade'=>$leavel]);
                // 更新缓存会员等级
                $_SESSION['user']['grade'] = $leavel;
            }


            // dump($type);die;
            if($res_save){
                $_SESSION['device']['did'] = $did;
                $device->commit();
                
                E('设备绑定成功',200);
            } else {
                $device->rollback();
                E('绑定失败',605);
            }
        } catch (\Exception $e) {
            $err = [
                'code' => $e->getCode(),
                'msg' => $e->getMessage(),
            ];
            $this->ajaxReturn($err);
        }        
    }

    // 设置默认
    public function setDefault()
    {
        try {
            $user_device = D('UserDevice');
            $where['device_code'] = I('post.code');
            $map['uid'] = session('user.id');

            $user_device->startTrans();
            // 修改当前用户的所有状态
            $res = $user_device->where($map)->save(['status'=>0]);
            if(!$res) E('无法更新',603);

            // 查询当前设备ID
            $map['did'] = M('Devices')->where($where)->find()['id'];
            // 设置当前设备状态
            $user_status = $user_device->where($map)->save(['status'=>1]);;
            $_SESSION['device']['did'] = $map['did'];
            if($user_status){
                $user_device->commit();
                E('更新成功',200);
            } else {
                $user_device->rollback();
                E('更新失败',604);
            }
        } catch (\Exception $e) {
            $err = [
                'code' => $e->getCode(),
                'msg' => $e->getMessage(),
            ];
            $this->ajaxReturn($err);
        }
    }
}


