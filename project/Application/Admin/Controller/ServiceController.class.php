<?php
namespace Admin\Controller;


class ServiceController extends CommonController
{
    /**
     * 服务站管理
     */
    public function index()
    {
        $map = [];
        $data = M('service')->where($map)->select();
        $this->assign('data',$data);
        $this->display();
    }


}