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


        // $orderDetail = D('orderDetail');
        // foreach ($data['goods'] as $key => $value) {
        //     $orderDetail->union('select num,price,st_goods.name,st_pic.path from st_order_detail LEFT JOIN st_goods ON st_order_detail.gid = st_goods.id LEFT JOIN st_pic ON st_order_detail.gid = st_pic.gid where order_id = '.$value['oid'].' AND st_order_detail.gid ='.$value['gid']);
        // } 
        //     $orderDetail->field('num,price,st_goods.name,st_pic.path');
        //     $orderDetail->join('st_goods ON st_order_detail.gid = st_goods.id','LEFT');
        //     $orderDetail->join('st_pic ON st_order_detail.gid = st_pic.gid','LEFT');
        //     $orderDetail->where('st_order_detail.id < 0');
        // $goods = $orderDetail->select();
        

        $orderDetail = D('orderDetail');
        foreach ($data['goods'] as $key => $value) {
            $orderDetail->union('select num,price,st_goods.name,st_order_detail.gpic from st_order_detail LEFT JOIN st_goods ON st_order_detail.gid = st_goods.id  where order_id = '.$value['oid'].' AND st_order_detail.gid ='.$value['gid']);
        } 
            $orderDetail->field('num,price,st_goods.name,st_order_detail.gpic');
            $orderDetail->join('st_goods ON st_order_detail.gid = st_goods.id','LEFT');
            // $orderDetail->join('st_pic ON st_order_detail.gid = st_pic.gid','LEFT');
            $orderDetail->where('st_order_detail.id < 0');
        $goods = $orderDetail->select();
        // dump($goods);
        $this->assign('goods',$goods);
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
        // （0：未处理 1：同意退款 2：不同意退款 3：同意退货 4：不同意退货 5：已退款 6：正在退货 7：已收货 ）
        // 0：已下单，1：已取消，2：已发货，3：已收货，4：退货处理中，5：已退货，6：申诉中，7：订单完成，8：未支付，9：已支付',

        if (!is_numeric($id) || !is_numeric($status)){ 
            return $this->ajaxReturn(['code'=>400,'msg'=>'id和状态必须为数字']);
        }
        try {
            //先查询该订单的支付类型
            $order_id = M('refund_goods')->where('rf_id='.$id)->find()['oid'];

            //根据订单号去查询订单支付类型
            $orderData = M('shop_order')->where("order_id={$order_id}")->find();
            // dump($payStatus);
            $payStatus = $orderData['mode'];
            switch ($payStatus) {
                case '0':
                    //微信
                    //微信退款
                    //微信examle的WxPay.JsApiPay.php
                    vendor('WxPay.WxPay#Api');
                    // vendor('WxPay.WxPay#Config');
                    // include VENDOR_PATH."/Wxpay/WxPay.Api.php";  
                    //查询订单,根据订单里边的数据进行退款  
                    $order = M('refund')->where(array('id'=>$id,'status'=>0))->find(); 


                    $orders = M('refund_goods')->where(array('rf_id'=>$id))->find()['oid'];

                    $transaction_id = $orderData['transaction_id']; 

                    $merchid = \WxPayConfig::MCHID;  
                        
                    if(!$order) return false;
                    //生成随机退款单号  
                    $refund_no = time();

                    $input = new \WxPayRefund();  
                    // $input->SetOut_trade_no($orders);         //自己的订单号  
                    $input->SetTransaction_id($transaction_id);     //微信官方生成的订单流水 号，在支付成功中有返回  
                    $input->SetOut_refund_no($refund_no);         //退款单号  
                    // $input->SetTotal_fee($order['total_amount']);         //订单标价金额，单位为分  
                    $input->SetTotal_fee(1);         //订单标价金额，单位为分  
                    // $input->SetRefund_fee($order['total_amount']);            //退款总金额，订单总金额，单位为分，只能为整数  
                    $input->SetRefund_fee(1);            //退款总金额，订单总金额，单位为分，只能为整数  
                    $input->SetOp_user_id($merchid);  
                      
                    $result = \WxPayApi::refund($input); //退款操作  
                    dump($result);
                    // 这句file_put_contents是用来查看服务器返回的退款结果 测试完可以删除了  
                    //file_put_contents(APP_ROOT.'/Api/wxpay/logs/log4.txt',arrayToXml($result),FILE_APPEND);  
                    if(($result['return_code']=='SUCCESS') && ($result['result_code']=='SUCCESS')){  
                        //退款成功  
                        echo 1;
                    }else if(($result['return_code']=='FAIL') || ($result['result_code']=='FAIL')){  
                        //退款失败  
                        //原因  
                        echo 2;
                        $reason = (empty($result['err_code_des'])?$result['return_msg']:$result['err_code_des']);  
                    }else{  
                        //失败 
                        echo 3;
                    } 
                    break;

                case '1':
                    //支付宝
                    
                    break;

                case '2':
                    //银联

                    break;

                case '3':
                    //金币
                    echo '金币';
                    break;

                case '4':
                    //银币

                    break;
            }
            

            die;
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

            $order = D('shopOrder')->where(['order_id'=>$orderid])->field('g_type')->find();
            switch ($order['g_type']) {
                case 0:
                    # code...
                    break;
                
                case 1:
                        $subsql = D('refund_goods')->where(['oid'=>$orderid])->field('gid')->select(false);
                        $goods_count = M('order_detail')                        
                        ->alias('d')
                        // ->where(['d.order_id'=>$orderid,'so.uid'=>$_SESSION['user']['id'],'d.gid'=>['NEQ','rg.gid'],'rg.oid'=>['NEQ',$orderid]])
                        ->where(['d.order_id'=>$orderid,'g.id'=>['exp',"NOT IN ($subsql)"]])
                        ->join('st_shop_order so ON d.order_id = so.order_id','LEFT')
                        ->join('__GOODS__ g ON g.id = d.gid','LEFT')
                        ->join('__GOODS_DETAIL__ g_d ON g.id = g_d.gid','LEFT')
                        ->join('__PIC__ p ON g.id = p.gid','LEFT')
                        // // ->table($refund.' a')
                        ->field(array('p.path'=>'orderimg','g.name'=>'productname','g.desc'=>'productbrief','d.gid','d.price'=>'price','d.num'=>'productnumber','g_d.is_install'=>'is_install','g_d.is_hire'=>'is_hire'))
                        ->count();
                    break;

                case 2:
                        $subsql = D('refund_goods')->where(['oid'=>$orderid])->field('gid')->select(false);
                        $goods_count = M('order_detail')                        
                        ->alias('d')
                        // ->where(['d.order_id'=>$orderid,'so.uid'=>$_SESSION['user']['id'],'d.gid'=>['NEQ','rg.gid'],'rg.oid'=>['NEQ',$orderid]])
                        ->where(['d.order_id'=>$orderid,'f.id'=>['exp',"NOT IN ($subsql)"]])
                        ->join('st_shop_order so ON d.order_id = so.order_id','LEFT')
                        ->join('__FILTERS__ f ON f.id = d.gid','LEFT')
                        // // ->table($refund.' a')
                        ->field(array('f.picpath'=>'orderimg','f.filtername'=>'productname','f.introduce'=>'productbrief','d.gid','d.price'=>'price','d.num'=>'productnumber'))
                        ->count();
                    break;

                case 3:
                    # code...
                    break;
                default:
                    # code...
                    break;
            }
            // p($goods_count);
            // p($d);die;
            if($goods_count < 1){
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