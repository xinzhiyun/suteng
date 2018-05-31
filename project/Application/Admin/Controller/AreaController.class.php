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

    /**
     * 加载下一级的地址库
     */
    public function getNextArea()
    {
        $parentid = I('parentid',0);

        $data = M('area')->where('parentid='.$parentid)->select();

        $this->toJson(['data'=>$data],'获取成功');
    }

}