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

        //退货的 需要将用户的快递信息显示出来
        if ($data['method'] == '2') {
            $rid = M('Refund')->find($id)['id'];
            $oid = M('RefundGoods')->where('rf_id='.$rid)->find()['oid'];

            $cinfo = M('RefundMessage')->where('orderid='.$oid)->find();

            $data['espress_name'] = $cinfo['espress_name'];
            $data['espress_num'] = $cinfo['espress_num'];
            $data['addtime'] = date('Y-m-d H:i:s',$cinfo['addtime']);
        }


        // $orderDetail = D('orderDetail');
        // foreach ($data['goods'] as $key => $value) {
        //     $orderDetail->union('select num,price,st_goods.name,st_pic.path from st_order_detail LEFT JOIN st_goods ON st_order_detail.gid = st_goods.id LEFT JOIN st_pic ON st_order_detail.gid = st_pic.gid where order_id = '.$value['oid'].' AND st_order_detail.gid ='.$value['gid']);
        // } 
        //     $orderDetail->field('num,price,st_goods.name,st_pic.path');
        //     $orderDetail->join('st_goods ON st_order_detail.gid = st_goods.id','LEFT');
        //     $orderDetail->join('st_pic ON st_order_detail.gid = st_pic.gid','LEFT');
        //     $orderDetail->where('st_order_detail.id < 0');
        // $goods = $orderDetail->select();
        

        $orderDetail = D('ShopOrderDetail');
        foreach ($data['goods'] as $key => $value) {
            $orderDetail->union('select num,price,st_goods.name,st_shop_order_detail.gpic from st_shop_order_detail LEFT JOIN st_goods ON st_shop_order_detail.gid = st_goods.id  where order_id = 
'.$value['oid'].' AND 
st_shop_order_detail.gid ='.$value['gid']);
        } 
            $orderDetail->field('num,price,st_goods.name,st_shop_order_detail.gpic');
            $orderDetail->join('st_goods ON st_shop_order_detail.gid = st_goods.id','LEFT');
            // $orderDetail->join('st_pic ON st_order_detail.gid = st_pic.gid','LEFT');
            $orderDetail->where('st_shop_order_detail.id < 0');
        $goods = $orderDetail->select();
        
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

            //先判断订单是待发货还是待收货的来写
            

            //先判断用户退货方式  1.仅退款  2.退货退款
            $refundList = M('refund')->where('id='.$id)->find();

            //订单id
            $oid = M('refund_goods')->where('rf_id='.$refundList['id'])->find()['oid'];

            //订单状态
            $ostatus = M('shop_order')->where("order_id='{$oid}'")->find()['status'];

            // dump($ostatus);die;
            // echo $ostatus;

            if ($ostatus=='11' or $ostatus=='12' or $ostatus=='13') {

                //待收货处退款  有退货退款跟仅退款
                if ($refundList['method'] == 2) {
                //退货退款的
                // dump($refundList);
                //根据退款订单状态进行
                switch ($refundList['status']) {
                    //同意退货退款，将status更改为  正在退货=>6
                    case '0':
                        $data['status'] = 3;
                        $info = M('refund')->where('id='.$id)->save($data);

                        if ($info) {
                            $this->ajaxReturn(array('code'=>'200','msg'=>'同意退货'));
                        } else {
                            $this->ajaxReturn(array('code'=>'400','msg'=>'同意退货失败'));
                        }
                        break;
                    
                    // //正在退货中，收到货后点击   前台修改状态为 6  同意退款
                    // case '3':
                    //     $data['status'] = 6;
                    //     $info = M('refund')->where('id='.$id)->save($data);

                    //     if ($info) {
                    //         $this->ajaxReturn(array('code'=>'200','msg'=>'已收到退货'));
                    //     } else {
                    //         $this->ajaxReturn(array('code'=>'400','msg'=>'收到退货失败'));
                    //     }
                    //     break;

                    //
                    case '6':
                        $data['status'] = 7;
                        $info = M('refund')->where('id='.$id)->save($data);

                        if ($info) {
                            $this->ajaxReturn(array('code'=>'200','msg'=>'已收到退货'));
                        } else {
                            $this->ajaxReturn(array('code'=>'400','msg'=>'收到退货失败'));
                        }
                        break;

                    //确认收货
                    case '7':

                        //先查询该订单的支付类型
                        $order_id = M('refund_goods')->where('rf_id='.$id)->find()['oid'];



                        // dump($order_id);die;
                        
                        //根据订单号去查询订单支付类型
                        $orderData = M('shop_order')->where("order_id={$order_id}")->find();
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
                                // dump($result);
                                // 这句file_put_contents是用来查看服务器返回的退款结果 测试完可以删除了  
                                //file_put_contents(APP_ROOT.'/Api/wxpay/logs/log4.txt',arrayToXml($result),FILE_APPEND);  
                                if(($result['return_code']=='SUCCESS') && ($result['result_code']=='SUCCESS')){  
                                    //退款成功
                                    M('refund')->startTrans();
                                    M('shop_order')->startTrans();

                                    //修改退款订单的状态
                                    $refund['status'] = 11;
                                    $bool2 = M('refund')->where('id='.$id)->save($refund);

                                    //修改订单状态前先判断该订单下是否还有未发货的商品
                                    //1.先查询订单中的商品
                                    $od = M('shop_order_detail')->where("order_id='{$order_id}'")->select();

                                    //订单中所有商品的id
                                    $ids = array();
                                    foreach ($od as $key => $value) {
                                      $ids[] = $value['gid'];
                                    }
                                    
                                    //2.查询退货订单的商品
                                    $rg = M('refund_goods')->where("oid='{$order_id}'")->select();

                                    $rids = array();
                                    foreach ($rg as $key => $value) {
                                      $rids[] = $value['gid'];
                                    }

                                    //退货商品的快递的信息就不需要查询了
                                    $jid = array_diff($ids,$rids);
                                  
                                    // $map['order_id'] = $order_id;
                                    $map['gid'] = array('in',$jid);

                                    // dump($map);die;

                                    // $couriers = D('ShopOrderDetail')->field('id')->where($map)->select();

                                    if (empty($map)) {
                                        // echo 1;
                                        $shop['status'] = 7;
                                        $shop['updatetime'] = time();
                                        $bool3 = M('shop_order')->where("order_id='{$order_id}'")->save($shop);
                                    } else {
                                        $shop['updatetime'] = time();
                                        $bool3 = M('shop_order')->where("order_id='{$order_id}'")->save($shop);

                                    }


                                    if ($bool2 && $bool3) {
                                        M('refund')->commit();
                                        M('shop_order')->commit();
                                        $this->ajaxReturn(array('code'=>'200','msg'=>'退款成功'));

                                    } else {
                                        M('refund')->rollback();
                                        M('shop_order')->rollback();
                                        $this->ajaxReturn(array('code'=>'400','msg'=>'退款失败'));
                                    }

                                } else{  
                                    //失败 
                                    $this->ajaxReturn(array('code'=>'400','msg'=>'退款失败'));
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
                                //先查询商品订单总价
                                
                                $odetail = M('shop_order_detail')->where("order_id='{$order_id}'")->select();

                                foreach ($odetail as $key => $value) {
                                    //订单总价
                                    (int)$totalPrice += $value['price'] * $value['num'];
                                }
                                
                                //查询要退款的金额
                                $refundInfo = M('refund')->where('id='.$id)->find();
                                $refundPrice = (int)$refundInfo['total_amount'];


                                //判断退款金额是否超过订单总额
                                if ($refundPrice > $totalPrice) {
                                    $this->ajaxReturn(array('code'=>'400','msg'=>'退款金额超过订单总额'));
                                } else {

                                    //先找出退款账号的openid
                                    $openid = M('users')->where('id='.$refundInfo['uid'])->find()['open_id'];

                                    M('Users')->startTrans();
                                    M('refund')->startTrans();
                                    M('shop_order')->startTrans();

                                    //修改用户表的金币数量
                                    //先查出该用户现有的金币总数
                                    $gold_num = M('users')->where("open_id='{$openid}'")->find()['gold_num'];
                                    $totlaNum = $gold_num + $refundPrice;

                                    // echo $totlaNum;die;
                                    $users['gold_num'] = $totlaNum;
                                    $bool1 = M('users')->where("open_id='{$openid}'")->save($users);

                                    //修改退款订单的状态
                                    $refund['status'] = 5;
                                    $bool2 = M('refund')->where('id='.$id)->save($refund);

                                    //修改订单的状态
                                    
                                    //修改订单状态前先判断该订单下是否还有未发货的商品
                                    //1.先查询订单中的商品
                                    $od = M('shop_order_detail')->where("order_id='{$order_id}'")->select();
                                    $status = M('shop_order')->where("order_id='{$order_id}'")->find()['status'];

                                    switch ($status) {
                                        //已发货的退货退款
                                        case '11':
                                            $shop['status'] = 7;
                                            $shop['updatetime'] = time();
                                            $bool3 = M('shop_order')->where("order_id='{$order_id}'")->save($shop);
                                            break;
                                        
                                        //已发货的仅退款
                                        case '12':
                                            $shop['status'] = 7;
                                            $shop['updatetime'] = time();
                                            $bool3 = M('shop_order')->where("order_id='{$order_id}'")->save($shop);
                                            break;


                                        default:
                                            # code...
                                            break;
                                    }
                                    
                                  
                                    if ($bool1 && $bool2 && $bool3) {
                                        M('Users')->commit();
                                        M('refund')->commit();
                                        M('shop_order')->commit();
                                        $this->ajaxReturn(array('code'=>'200','msg'=>'退款成功'));
                                    } else {
                                        M('Users')->rollback();
                                        M('refund')->rollback();
                                        M('shop_order')->rollback();
                                        $this->ajaxReturn(array('code'=>'400','msg'=>'退款失败'));
                                    }
                                }


                                break;

                            case '4':
                                //银币
                                //先查询商品订单总价
                                $odetail = M('shop_order_detail')->where("order_id='{$order_id}'")->select();

                                foreach ($odetail as $key => $value) {
                                    //订单总价
                                    (int)$totalPrice += $value['price']*$value['num'];
                                }
                                
                                //查询要退款的金额
                                $refundInfo = M('refund')->where('id='.$id)->find();
                                $refundPrice = (int)$refundInfo['total_amount'];

                                
                                //判断退款金额是否超过订单总额
                                if ($refundPrice > $totalPrice) {
                                    $this->ajaxReturn(array('code'=>'400','msg'=>'退款金额超过订单总额'));
                                } else {

                                    //先找出退款账号的openid
                                    $openid = M('users')->where('id='.$refundInfo['uid'])->find()['open_id'];

                                    M('Users')->startTrans();
                                    M('refund')->startTrans();
                                    M('shop_order')->startTrans();

                                    //修改用户表的金币数量
                                    //先查出该用户现有的金币总数
                                    $silver = M('users')->where("open_id='{$openid}'")->find()['silver'];
                                    $totlaNum = $silver + $refundPrice*2;

                                    // echo $totlaNum;die;
                                    $users['silver'] = $totlaNum;
                                    $bool1 = M('users')->where("open_id='{$openid}'")->save($users);

                                    //修改退款订单的状态
                                    $refund['status'] = 5;
                                    $bool2 = M('refund')->where('id='.$id)->save($refund);

                                    //修改订单状态前先判断该订单下是否还有未发货的商品
                                    //1.先查询订单中的商品
                                    $od = M('shop_order_detail')->where("order_id='{$order_id}'")->select();

                                    //订单中所有商品的id
                                    $ids = array();
                                    foreach ($od as $key => $value) {
                                      $ids[] = $value['gid'];
                                    }
                                    
                                    //2.查询退货订单的商品
                                    $rg = M('refund_goods')->where("oid='{$order_id}'")->select();

                                    $rids = array();
                                    foreach ($rg as $key => $value) {
                                      $rids[] = $value['gid'];
                                    }

                                    //退货商品的快递的信息就不需要查询了
                                    $jid = array_diff($ids,$rids);
                                  
                                    // $map['order_id'] = $order_id;
                                    $map['gid'] = array('in',$jid);

                                    // dump($map);die;

                                    // $couriers = D('ShopOrderDetail')->field('id')->where($map)->select();

                                    if (empty($map)) {
                                        // echo 1;
                                        $shop['status'] = 7;
                                        $shop['updatetime'] = time();
                                        $bool3 = M('shop_order')->where("order_id='{$order_id}'")->save($shop);
                                    } else {
                                        $shop['updatetime'] = time();
                                        $bool3 = M('shop_order')->where("order_id='{$order_id}'")->save($shop);

                                    }

                                    if ($bool1 && $bool2 && $bool3) {
                                        M('Users')->commit();
                                        M('refund')->commit();
                                        M('shop_order')->commit();
                                        $this->ajaxReturn(array('code'=>'200','msg'=>'退款成功'));
                                    } else {
                                        M('Users')->rollback();
                                        M('refund')->rollback();
                                        M('shop_order')->rollback();
                                        $this->ajaxReturn(array('code'=>'400','msg'=>'退款失败'));
                                    }
                                }

                                break;
                        }    
                                break;
                        }

                } else {
                    //仅退款
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
                            // dump($result);
                            // 这句file_put_contents是用来查看服务器返回的退款结果 测试完可以删除了  
                            //file_put_contents(APP_ROOT.'/Api/wxpay/logs/log4.txt',arrayToXml($result),FILE_APPEND);  
                            if(($result['return_code']=='SUCCESS') && ($result['result_code']=='SUCCESS')){  
                                //退款成功
                                M('refund')->startTrans();
                                M('shop_order')->startTrans();

                                //修改退款订单的状态
                                $refund['status'] = 5;
                                $bool2 = M('refund')->where('id='.$id)->save($refund);

                                //修改订单状态前先判断该订单下是否还有未发货的商品
                                //1.先查询订单中的商品
                                $od = M('shop_order_detail')->where("order_id='{$order_id}'")->select();

                                //订单中所有商品的id
                                $ids = array();
                                foreach ($od as $key => $value) {
                                  $ids[] = $value['gid'];
                                }
                                
                                //2.查询退货订单的商品
                                $rg = M('refund_goods')->where("oid='{$order_id}'")->select();

                                $rids = array();
                                foreach ($rg as $key => $value) {
                                  $rids[] = $value['gid'];
                                }

                                //退货商品的快递的信息就不需要查询了
                                $jid = array_diff($ids,$rids);
                              
                                // $map['order_id'] = $order_id;
                                $map['gid'] = array('in',$jid);

                                // dump($map);die;

                                // $couriers = D('ShopOrderDetail')->field('id')->where($map)->select();

                                if (empty($map)) {
                                    // echo 1;
                                    $shop['status'] = 7;
                                    $shop['updatetime'] = time();
                                    $bool3 = M('shop_order')->where("order_id='{$order_id}'")->save($shop);
                                } else {
                                    $shop['updatetime'] = time();
                                    $bool3 = M('shop_order')->where("order_id='{$order_id}'")->save($shop);

                                }


                                if ($bool2 && $bool3) {
                                    M('refund')->commit();
                                    M('shop_order')->commit();
                                    $this->ajaxReturn(array('code'=>'200','msg'=>'退款成功'));

                                } else {
                                    M('refund')->rollback();
                                    M('shop_order')->rollback();
                                    $this->ajaxReturn(array('code'=>'400','msg'=>'退款失败'));
                                }

                            } else{  
                                //失败 
                                $this->ajaxReturn(array('code'=>'400','msg'=>'退款失败'));
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
                            //先查询商品订单总价
                            $odetail = M('shop_order_detail')->where("order_id='{$order_id}'")->select();

                            // dump($odetail);

                            foreach ($odetail as $key => $value) {
                                //订单总价
                                (int)$totalPrice += ($value['price']+$value['cprice'])*$value['num'];
                            }
                            

                            //查询要退款的金额
                            $refundInfo = M('refund')->where('id='.$id)->find();
                            $refundPrice = (int)$refundInfo['total_amount'];

                            //要退款的总金币数
                            $totalNum = $refundPrice;

                            //判断退款金额是否超过订单总额
                            if ($totalNum > $totalPrice) {
                                $this->ajaxReturn(array('code'=>'400','msg'=>'退款金额超过订单总额'));
                            } else {

                                //先找出退款账号的openid
                                $openid = M('users')->where('id='.$refundInfo['uid'])->find()['open_id'];

                                M('Users')->startTrans();
                                M('refund')->startTrans();
                                M('shop_order')->startTrans();

                                //修改用户表的金币数量
                                //先查出该用户现有的金币总数
                                $gold_num = M('users')->where("open_id='{$openid}'")->find()['gold_num'];
                                $totlaNum = $gold_num + $totalNum;

                                // echo $totlaNum;die;
                                $users['gold_num'] = $totlaNum;
                                $bool1 = M('users')->where("open_id='{$openid}'")->save($users);

                                //修改退款订单的状态
                                $refund['status'] = 5;
                                $bool2 = M('refund')->where('id='.$id)->save($refund);

                                //修改订单的状态
                                
                                //修改订单状态前先判断该订单下是否还有未发货的商品
                                //1.先查询订单中的商品
                                $od = M('shop_order_detail')->where("order_id='{$order_id}'")->select();

                                //订单中所有商品的id
                                $ids = array();
                                foreach ($od as $key => $value) {
                                  $ids[] = $value['gid'];
                                }
                                
                                //2.查询退货订单的商品
                                $rg = M('refund_goods')->where("oid='{$order_id}'")->select();

                                $rids = array();
                                foreach ($rg as $key => $value) {
                                  $rids[] = $value['gid'];
                                }

                                //退货商品的快递的信息就不需要查询了
                                $jid = array_diff($ids,$rids);
                              
                                // $map['order_id'] = $order_id;
                                $map['gid'] = array('in',$jid);

                                // dump($map);die;

                                // $couriers = D('ShopOrderDetail')->field('id')->where($map)->select();

                                if (empty($map)) {
                                    // echo 1;
                                    $shop['status'] = 7;
                                    $shop['updatetime'] = time();
                                    $bool3 = M('shop_order')->where("order_id='{$order_id}'")->save($shop);
                                } else {
                                    $shop['updatetime'] = time();
                                    $bool3 = M('shop_order')->where("order_id='{$order_id}'")->save($shop);

                                }
                                

                                if ($bool1 && $bool2 && $bool3) {
                                    M('Users')->commit();
                                    M('refund')->commit();
                                    M('shop_order')->commit();
                                    $this->ajaxReturn(array('code'=>'200','msg'=>'退款成功'));
                                } else {
                                    M('Users')->rollback();
                                    M('refund')->rollback();
                                    M('shop_order')->rollback();
                                    $this->ajaxReturn(array('code'=>'400','msg'=>'退款失败'));
                                }
                            }


                            break;

                        case '4':
                            //银币
                            //先查询商品订单总价
                            $odetail = M('shop_order_detail')->where("order_id='{$order_id}'")->select();

                            foreach ($odetail as $key => $value) {
                                //订单总价
                                (int)$totalPrice += $value['price'] * $value['num'];
                            }
                            

                            //查询要退款的金额
                            $refundInfo = M('refund')->where('id='.$id)->find();
                            $refundPrice = (int)$refundInfo['total_amount'];

                    
                            //要退款的总银币数
                            $totalNum = $refundPrice;

                            //判断退款金额是否超过订单总额
                            if ($totalNum > $totalPrice) {
                                $this->ajaxReturn(array('code'=>'400','msg'=>'退款金额超过订单总额'));
                            } else {

                                //先找出退款账号的openid
                                $openid = M('users')->where('id='.$refundInfo['uid'])->find()['open_id'];

                                M('Users')->startTrans();
                                M('refund')->startTrans();
                                M('shop_order')->startTrans();

                                //修改用户表的金币数量
                                //先查出该用户现有的金币总数
                                $silver = M('users')->where("open_id='{$openid}'")->find()['silver'];
                                $totlaNum = $silver + $totalNum*2;

                                // echo $totlaNum;die;
                                $users['silver'] = $totlaNum;
                                $bool1 = M('users')->where("open_id='{$openid}'")->save($users);

                                //修改退款订单的状态
                                $refund['status'] = 5;
                                $bool2 = M('refund')->where('id='.$id)->save($refund);

                                //修改订单状态前先判断该订单下是否还有未发货的商品
                                //1.先查询订单中的商品
                                $od = M('shop_order_detail')->where("order_id='{$order_id}'")->select();

                                //订单中所有商品的id
                                $ids = array();
                                foreach ($od as $key => $value) {
                                  $ids[] = $value['gid'];
                                }
                                
                                //2.查询退货订单的商品
                                $rg = M('refund_goods')->where("oid='{$order_id}'")->select();

                                $rids = array();
                                foreach ($rg as $key => $value) {
                                  $rids[] = $value['gid'];
                                }

                                //退货商品的快递的信息就不需要查询了
                                $jid = array_diff($ids,$rids);
                              
                                // $map['order_id'] = $order_id;
                                $map['gid'] = array('in',$jid);

                                // dump($map);die;

                                // $couriers = D('ShopOrderDetail')->field('id')->where($map)->select();

                                if (empty($map)) {
                                    // echo 1;
                                    $shop['status'] = 7;
                                    $shop['updatetime'] = time();
                                    $bool3 = M('shop_order')->where("order_id='{$order_id}'")->save($shop);
                                } else {
                                    $shop['updatetime'] = time();
                                    $bool3 = M('shop_order')->where("order_id='{$order_id}'")->save($shop);

                                }

                                if ($bool1 && $bool2 && $bool3) {
                                    M('Users')->commit();
                                    M('refund')->commit();
                                    M('shop_order')->commit();
                                    $this->ajaxReturn(array('code'=>'200','msg'=>'退款成功'));
                                } else {
                                    M('Users')->rollback();
                                    M('refund')->rollback();
                                    M('shop_order')->rollback();
                                    $this->ajaxReturn(array('code'=>'400','msg'=>'退款失败'));
                                }
                            }

                            break;
                    }    
                }
            } elseif($ostatus=='6' or $ostatus=='10') {
                //待发货处退款   只有仅退款
                //需要退邮费
                //仅退款
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
                            // dump($result);
                            // 这句file_put_contents是用来查看服务器返回的退款结果 测试完可以删除了  
                            //file_put_contents(APP_ROOT.'/Api/wxpay/logs/log4.txt',arrayToXml($result),FILE_APPEND);  
                            if(($result['return_code']=='SUCCESS') && ($result['result_code']=='SUCCESS')){  
                                //退款成功
                                M('refund')->startTrans();
                                M('shop_order')->startTrans();

                                //修改退款订单的状态
                                $refund['status'] = 5;
                                $bool2 = M('refund')->where('id='.$id)->save($refund);

                                //修改订单状态前先判断该订单下是否还有未发货的商品
                                //1.先查询订单中的商品
                                $od = M('shop_order_detail')->where("order_id='{$order_id}'")->select();

                                //订单中所有商品的id
                                $ids = array();
                                foreach ($od as $key => $value) {
                                  $ids[] = $value['gid'];
                                }
                                
                                //2.查询退货订单的商品
                                $rg = M('refund_goods')->where("oid='{$order_id}'")->select();

                                $rids = array();
                                foreach ($rg as $key => $value) {
                                  $rids[] = $value['gid'];
                                }

                                //退货商品的快递的信息就不需要查询了
                                $jid = array_diff($ids,$rids);
                              
                                // $map['order_id'] = $order_id;
                                $map['gid'] = array('in',$jid);

                                // dump($map);die;

                                // $couriers = D('ShopOrderDetail')->field('id')->where($map)->select();

                                if (empty($map)) {
                                    // echo 1;
                                    $shop['status'] = 7;
                                    $shop['updatetime'] = time();
                                    $bool3 = M('shop_order')->where("order_id='{$order_id}'")->save($shop);
                                } else {
                                    $shop['updatetime'] = time();
                                    $bool3 = M('shop_order')->where("order_id='{$order_id}'")->save($shop);

                                }


                                if ($bool2 && $bool3) {
                                    M('refund')->commit();
                                    M('shop_order')->commit();
                                    $this->ajaxReturn(array('code'=>'200','msg'=>'退款成功'));

                                } else {
                                    M('refund')->rollback();
                                    M('shop_order')->rollback();
                                    $this->ajaxReturn(array('code'=>'400','msg'=>'退款失败'));
                                }

                            } else{  
                                //失败 
                                $this->ajaxReturn(array('code'=>'400','msg'=>'退款失败'));
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
                            //先查询商品订单总价
                            $odetail = M('shop_order_detail')->where("order_id='{$order_id}'")->select();

                            // dump($odetail);

                            foreach ($odetail as $key => $value) {
                                //订单总价
                                (int)$totalPrice += ($value['price']+$value['cprice'])*$value['num'];
                            }
                            

                            //查询要退款的金额
                            $refundInfo = M('refund')->where('id='.$id)->find();
                            $refundPrice = (int)$refundInfo['total_amount'];

                            //查找退款的商品id及订单
                            $gid = M('RefundGoods')->where('rf_id='.$id)->select();
                            foreach ($gid as $key => $value) {
                                $espress = M('shop_order_detail')->where('order_id='.$value['oid'].' and gid='.$value['gid'])->select();    
                            }

                            //商品快递总价
                            foreach ($espress as $key => $value) {
                                $totalEspressPrice += $value['cprice']*$value['num'];
                            }
                            // dump($totalEspressPrice);
                            // // dump($refundInfo);

                            // dump($refundPrice);die;

                            //要退款的总金币数
                            $totalNum = $refundPrice + $totalEspressPrice;

                            // echo $totalNum;die;

                            //判断退款金额是否超过订单总额
                            if ($totalNum > $totalPrice) {
                                $this->ajaxReturn(array('code'=>'400','msg'=>'退款金额超过订单总额'));
                            } else {

                                //先找出退款账号的openid
                                $openid = M('users')->where('id='.$refundInfo['uid'])->find()['open_id'];

                                M('Users')->startTrans();
                                M('refund')->startTrans();
                                M('shop_order')->startTrans();

                                //修改用户表的金币数量
                                //先查出该用户现有的金币总数
                                $gold_num = M('users')->where("open_id='{$openid}'")->find()['gold_num'];
                                $totlaNum = $gold_num + $totalNum;

                                // echo $totlaNum;die;
                                $users['gold_num'] = $totlaNum;
                                $bool1 = M('users')->where("open_id='{$openid}'")->save($users);

                                //修改退款订单的状态
                                $refund['status'] = 5;
                                $bool2 = M('refund')->where('id='.$id)->save($refund);

                                //修改订单的状态
                                
                                //修改订单状态前先判断该订单下是否还有未发货的商品
                                //1.先查询订单中的商品
                                $od = M('shop_order_detail')->where("order_id='{$order_id}'")->select();

                                //订单中所有商品的id
                                $ids = array();
                                foreach ($od as $key => $value) {
                                  $ids[] = $value['gid'];
                                }
                                
                                //2.查询退货订单的商品
                                $rg = M('refund_goods')->where("oid='{$order_id}'")->select();

                                $rids = array();
                                foreach ($rg as $key => $value) {
                                  $rids[] = $value['gid'];
                                }

                                //退货商品的快递的信息就不需要查询了
                                $jid = array_diff($ids,$rids);
                              
                                // $map['order_id'] = $order_id;
                                $map['gid'] = array('in',$jid);

                                // dump($map);die;

                                // $couriers = D('ShopOrderDetail')->field('id')->where($map)->select();

                                if (empty($map)) {
                                    // echo 1;
                                    $shop['status'] = 7;
                                    $shop['updatetime'] = time();
                                    $bool3 = M('shop_order')->where("order_id='{$order_id}'")->save($shop);
                                } else {
                                    $shop['updatetime'] = time();
                                    $bool3 = M('shop_order')->where("order_id='{$order_id}'")->save($shop);

                                }
                                

                                if ($bool1 && $bool2 && $bool3) {
                                    M('Users')->commit();
                                    M('refund')->commit();
                                    M('shop_order')->commit();
                                    $this->ajaxReturn(array('code'=>'200','msg'=>'退款成功'));
                                } else {
                                    M('Users')->rollback();
                                    M('refund')->rollback();
                                    M('shop_order')->rollback();
                                    $this->ajaxReturn(array('code'=>'400','msg'=>'退款失败'));
                                }
                            }


                            break;

                        case '4':
                            //银币
                            //先查询商品订单总价
                            $odetail = M('shop_order_detail')->where("order_id='{$order_id}'")->select();

                            foreach ($odetail as $key => $value) {
                                //订单总价
                                (int)$totalPrice += $value['price'] * $value['num'];
                            }
                            

                            //查询要退款的金额
                            $refundInfo = M('refund')->where('id='.$id)->find();
                            $refundPrice = (int)$refundInfo['total_amount'];

                            //查找退款的商品id及订单
                            $gid = M('RefundGoods')->where('rf_id='.$id)->select();
                            foreach ($gid as $key => $value) {
                                $espress = M('shop_order_detail')->where('order_id='.$value['oid'].' and gid='.$value['gid'])->select();    
                            }

                            //商品快递总价
                            foreach ($espress as $key => $value) {
                                $totalEspressPrice += $value['cprice']*$value['num'];
                            }
                            // dump($totalEspressPrice);
                            // // dump($refundInfo);

                            // dump($refundPrice);die;

                            //要退款的总银币数
                            $totalNum = $refundPrice + $totalEspressPrice;

                            //判断退款金额是否超过订单总额
                            if ($totalNum > $totalPrice) {
                                $this->ajaxReturn(array('code'=>'400','msg'=>'退款金额超过订单总额'));
                            } else {

                                //先找出退款账号的openid
                                $openid = M('users')->where('id='.$refundInfo['uid'])->find()['open_id'];

                                M('Users')->startTrans();
                                M('refund')->startTrans();
                                M('shop_order')->startTrans();

                                //修改用户表的金币数量
                                //先查出该用户现有的金币总数
                                $silver = M('users')->where("open_id='{$openid}'")->find()['silver'];
                                $totlaNum = $silver + $totalNum*2;

                                // echo $totlaNum;die;
                                $users['silver'] = $totlaNum;
                                $bool1 = M('users')->where("open_id='{$openid}'")->save($users);

                                //修改退款订单的状态
                                $refund['status'] = 5;
                                $bool2 = M('refund')->where('id='.$id)->save($refund);

                                //修改订单状态前先判断该订单下是否还有未发货的商品
                                //1.先查询订单中的商品
                                $od = M('shop_order_detail')->where("order_id='{$order_id}'")->select();

                                //订单中所有商品的id
                                $ids = array();
                                foreach ($od as $key => $value) {
                                  $ids[] = $value['gid'];
                                }
                                
                                //2.查询退货订单的商品
                                $rg = M('refund_goods')->where("oid='{$order_id}'")->select();

                                $rids = array();
                                foreach ($rg as $key => $value) {
                                  $rids[] = $value['gid'];
                                }

                                //退货商品的快递的信息就不需要查询了
                                $jid = array_diff($ids,$rids);
                              
                                // $map['order_id'] = $order_id;
                                $map['gid'] = array('in',$jid);

                                // dump($map);die;

                                // $couriers = D('ShopOrderDetail')->field('id')->where($map)->select();

                                if (empty($map)) {
                                    // echo 1;
                                    $shop['status'] = 7;
                                    $shop['updatetime'] = time();
                                    $bool3 = M('shop_order')->where("order_id='{$order_id}'")->save($shop);
                                } else {
                                    $shop['updatetime'] = time();
                                    $bool3 = M('shop_order')->where("order_id='{$order_id}'")->save($shop);

                                }

                                if ($bool1 && $bool2 && $bool3) {
                                    M('Users')->commit();
                                    M('refund')->commit();
                                    M('shop_order')->commit();
                                    $this->ajaxReturn(array('code'=>'200','msg'=>'退款成功'));
                                } else {
                                    M('Users')->rollback();
                                    M('refund')->rollback();
                                    M('shop_order')->rollback();
                                    $this->ajaxReturn(array('code'=>'400','msg'=>'退款失败'));
                                }
                            }

                            break;
                    }    
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
                            ->join('st_shop_order_detail od ON od.order_id = so.order_id','LEFT')
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
            $res = D('shop_order_detail')->where('id='.$id)->setField('is_installed',$status);
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