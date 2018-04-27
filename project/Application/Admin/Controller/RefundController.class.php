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
        $refund = D('Refund');
        $data = $refund->getPage($refund,'');
        // dump($data);
        $this->assign('data',$data);
        $this->display();
    }

    /**
     * 退货退款查看详情页
     * @return [type] [description]
     */
    public function showDetails() 
    {
        $id = I('get.id');
        $data = D('Refund')->relation(['logistics','goods'])->find($id);
        foreach ($data['goods'] as $key => $value) {
            $goods[] = $value['gid'];
        }
        $d = D('goods')->field('id,name,pic')->where('id',['in',$goods])->select();
        dump($goods);
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
        
        if (!is_numeric($id) || !is_numeric($status)){ 
            return $this->ajaxReturn(['code'=>400,'msg'=>'id和状态必须为数字']);
        }
        try {
            $refund = D('Refund');
            $refund->startTrans();
            $res = $refund->where('id='.$id)->setField('status',$status);
            $d = D('Refund_goods')->field('distinct(oid)')->where('rf_id='.$id)->select();
            // foreach ($d as $key => $value) {
            //     $ids[] = $value['oid'];
            // }
            if ($status == 1 || $status == 3) {
                $statu = 4;
            } elseif ($status == 5) {
                $statu = 5;
            }
            
            $orderid = $d[0]['oid'];

            D('order_detail')->where(['order_id'=>$orderid])->setField('status',$statu);
            // 装B的代码开始了
            
            $status = D('shop_order')->alias('so')->where(['so.order_id'=>$orderid])->join('st_order_detail od ON so.order_id = od.order_id','RIGHT')->field('od.status')->select();
            foreach($status as $v){
                if($v['status'] !== 4 && $v['status'] !== 5){
                    $rs = D('shop_order')->where(['order_id'=>$orderid])->setField(['status'=>5]);
                    if($rs) {
                        $refund->commit();
                        return $this->ajaxReturn(['code'=>200,'msg'=>'修改成功2']);
                    }
                    break;
                }
            }


            if ($res) {
                $refund->commit();
                return $this->ajaxReturn(['code'=>200,'msg'=>'修改成功']);
            } else{
                $refund->rollback();
                return $this->ajaxReturn(['code'=>400,'msg'=>'修改失败']);
            }
        } catch (\Exception $e) {
            $err = [
                'code' => $e->getCode(),
                'msg' => $e->getMessage(),
            ];
            return $this->ajaxReturn($err);
        }         
    }

    public function install()
    {
        $map = '';
        // if (!empty(I('get.key')) && !empty(I('get.keywords'))) {
        //     $map[I('get.key')] = array('like',"%".I('get.keywords')."%");
        // }
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