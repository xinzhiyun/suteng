<?php
namespace Home\Controller;
use \Org\Util\WeixinJssdk;

class MallController extends CommonController
{

    /**
     * [chongzhi 充值]
     * @return [type] [description]
     */
    public function chongzhi()
    {
        // 获取用户微信唯一标识
        $showUser['open_id'] = $_SESSION['user']['open_id'];
        // 查询用户信息
        $userData = M('users')->where($showUser)->find();
        //调用微信JS-SDK类获取签名需要用到的数据
        $weixin = new WeixinJssdk;
        $signPackage = $weixin->getSignPackage();
        // 查询用户微信中的openid
        $openId = $weixin->GetOpenid();

        //分配数据        
        $this->assign('info',$signPackage);
        $this->assign('openId',$openId);
        $this->assign('user',$userData);
        // dump($userData);die;
        $this->display();
    }

    /**
     * [lvxin 滤芯]
     * @return [type] [description]
     */
    public function lvxin()
    {

        $filter = D('Filters');
        $devices['d.id'] = session('device.did');
        $res = $filter->getFilters($devices);
        $assign = [
            'data' => json_encode($res),
        ];
        $this->assign($assign);
        $this->display();
    }
}


