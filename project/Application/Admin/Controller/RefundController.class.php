<?php
namespace Admin\Controller;
use Think\Controller;
/**
 * [退款及退款退货]
 */
class RefundController extends CommonController
{
    public function index(){
        $data = D('Refund')->getPage(D('Refund'),'');
        // print_r($data);
        $this->assign('data',$data);
        $this->display();
    }

    public function showDetails()
    {
        $data = D('Refund')->relation(['logistics','goods'])->find(1);
        // dump($data);
        $this->assign('data',$data);
        $this->display();
    }

    public function editStatus($id,$status)
    {
        if (is_numeric($id) && is_numeric($status)){
            $res = D('Refund')->where('id='.$id)->setField('status',$status);
            if ($res) {
                return $this->ajaxReturn(['code'=>200,'msg'=>'修改成功']);
            } else{
                return $this->ajaxReturn(['code'=>400,'msg'=>'修改失败']);
            }
            
        } else{
            return $this->ajaxReturn(['code'=>400,'msg'=>'id和状态必须为数字']);
        }
        
    }
}