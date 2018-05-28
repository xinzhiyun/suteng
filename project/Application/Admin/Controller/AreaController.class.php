<?php
namespace Admin\Controller;


class AreaController extends CommonController
{
    /**
     * 地址库管理
     */
    public function index()
    {
        $data = M('area')->where('parentid=0')->select();
        $this->assign('data',$data);
        $this->display();
    }

    public function getNextArea()
    {
        $parentid = I('parentid');
        $data = M('area')->where('parentid='.$parentid)->select();

        $this->toJson(['data'=>$data],'获取成功');
    }

}