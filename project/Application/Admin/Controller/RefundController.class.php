<?php
namespace Admin\Controller;
use Think\Controller;
/**
 * [退款及退款退货]
 */
class RefundController extends CommonController
{
    /**
     * 退货退款显示列表
     * @return [type] 模板渲染
     */
    public function index(){
        $data = D('Refund')->getPage(D('Refund'),'');
        // print_r($data);
        $this->assign('data',$data);
        $this->display();
    }

    /**
     * 退货退款查看详情页
     * @return [type] [description]
     */
    public function showDetails() 
    {
        $data = D('Refund')->relation(['logistics','goods'])->find(1);
        // dump($data);
        $this->assign('data',$data);
        $this->display();
    }

    /**
     * 修改售后状态
     * @param  int $id     退货退款id
     * @param  int $status 状态
     * @return [type]         [description]
     */
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

    public function install()
    {
        $map = '';
        if (!empty(I('get.key')) && !empty(I('get.keywords'))) {
            $map[I('get.key')] = array('like',"%".I('get.keywords')."%");
        }
        $data = D('shop_order')->alias('so')
                            ->where(['gd.is_install'=>1])
                            ->where($map)
                            ->join('st_order_detail od ON od.order_id = so.order_id','LEFT')
                            ->join('st_pic pic ON od.gid = pic.gid','LEFT')
                            ->join('st_goods g ON od.gid = g.id','LEFT')
                            ->join('st_users u ON so.uid = u.id','LEFT')
                            ->join('st_goods_detail gd ON od.gid = gd.gid','LEFT')
                            ->field(array('od.id'=>'id','so.uid','u.nickname','so.order_id','od.gid','od.num','od.price','g.name'=>'goodname','pic.path'=>'picpath','so.addtime','od.is_installed'))
                            ->select();
        // print_r($data);
        $this->assign('data',$data);
        $this->display();
    }

    /**
     * 更改报装状态
     * @param  int $id     order_detail的id
     * @param  int $status is_installed
     * @return json         [description]
     */
    public function updateInstalled($id,$status)
    {
        if (is_numeric($id) && is_numeric($status)){
            $res = D('order_detail')->where('id='.$id)->setField('is_installed',$status);
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