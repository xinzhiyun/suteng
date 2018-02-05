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
        $bind_device = $user_device->getBindInof(session('user.id')); //where('uid='.session('user.id'))->select();
        // dump($bind_device);die;
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
            $res_find = $user_device->where('did='.$did)->find();
            if($res_find != null) E('设备已绑定',604);
            $data['did']        = $did;
            $data['uid']        = $uid;
            $data['status']     = 1;
            $data['addtime']    = time();
            $data['updatetime'] = time();
            $user_device->where('uid='.$uid)->save(['status'=>0]);
            $res_save = $user_device->add($data);
            if($res_save){
                // session('device.did',$did);
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
}


