<?php
namespace Home\Controller;

use Think\Controller;
use \Org\Util\WeixinJssdk;
class IndexController extends CommonController
{
    public function index()
    {
        $user_device = D('UserDevice');
        $device = D('Devices');
        $where['status'] = 1;//session('device.did');
        $where['uid'] = session('user.id');
        $res = $user_device->where($where)->find();
        if(empty($res['did'])) $this->redirect('Home/Device/index');
        $deviceInfo = $device
            ->alias('d')
            ->where('d.id='.$res['did'])
            ->join('__DEVICES_STATU__ ds ON d.device_code=ds.DeviceID', 'LEFT')
            ->find();
        $assign = [
            'device' => $deviceInfo,
        ];
        $this->assign($assign);
    	$this->display();
    }

    /**
     * [filledwater 水满]
     * @return [type] [description]
     */
    public function filledwater()
    {

    	$this->display();
    }

    /**
     * [queshui 缺水]
     * @return [type] [description]
     */
    public function queshui()
    {

    	$this->display();
    }

    /**
     * [filledwater 修检]
     * @return [type] [description]
     */
    public function overhaul()
    {

    	$this->display();
    }

    /**
     * [makewater 制水]
     * @return [type] [description]
     */
    public function makewater()
    {

    	$this->display();
    }

    /**
     * [makewater 冲洗]
     * @return [type] [description]
     */
    public function chongxi()
    {

    	$this->display();
    }
}


