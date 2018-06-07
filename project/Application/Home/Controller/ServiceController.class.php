<?php
namespace Home\Controller;
use \Org\Util\WeixinJssdk;
/**
 * 服务站
 */

class ServiceController extends ServiceCommonController
{
    // 主页
    public function index()
    {
        $sid = $_SESSION['serviceInfo']['id'];



        $this->display();
    }



}
