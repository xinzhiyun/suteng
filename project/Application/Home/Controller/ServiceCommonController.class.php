<?php
namespace Home\Controller;
use Common\Tool\Weixin;
use Think\Controller;
use \Org\Util\WeixinJssdk;
/**
 * 前共控制器 (服务站)
 * 前台控制器除login外必须继承我
 */
class ServiceCommonController extends Controller
{

    public function _initialize()
    {
        // 检查微信
        if(empty($_SESSION['open_id'])){
            $openid = Weixin::GetOpenid();
            $_SESSION['open_id']=$openid;
        }

        // 自动登录
        if(empty($_SESSION['serviceInfo'])){
            $info = M('admin_user')
                ->alias('au')
                ->where("open_id='{$_SESSION['open_id']}'")
                ->join('__SERVICE__ s ON s.auid=au.id', 'LEFT')
                ->field('au.password,au.user,s.id,s.company,s.auid')
                ->find();
            if($info) {
                unset($info['password']);
                $_SESSION['serviceInfo'] = $info;
            }else{
                $this->redirect("Home/ServiceLogin/index");
            }
        }
    }

    /**
     * 加载微信配置信息
     */
    public function wx_info()
    {
        $weixin = new \Org\Util\WeixinJssdk();
        $signPackage = $weixin->getSignPackage();
        $this->assign('wxinfo',$signPackage);
    }



}
