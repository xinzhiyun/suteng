<?php
namespace Home\Controller;
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
        $_SESSION['open_id'] = 'onLe70UOYRAS057fUzuCbyTYKRFo'; // 前端调试通道

        // 获取用户open_id
        if (empty($_SESSION['open_id'])) {
            $weixin = new WeixinJssdk;
            $_SESSION['open_id'] = $weixin->GetOpenid();
        }

        // 后续添加 绑定微信进行自动登录


        if(empty($_SESSION['serviceInfo'])){
//            $this->redirect("Home/SericeLogin/index");
        }
    }



}
