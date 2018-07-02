<?php
namespace Home\Controller;
use \Org\Util\WeixinJssdk;

class PaymentSystemController extends CommonController
{
    /**
     * [exchange 金币兑换银币]
     * @return [type] [description]
     */
    public function exchange()
    {
        // 获取兑换套餐ID
        $showData['id'] = I('post.id');
        // 查询兑换套餐
        $silverData = M('gold_silver')->where($showData)->find();
        // 判断兑换套餐是否存在
        if($silverData){
            // 如果兑换套餐存在，查询用户金币
            $showUser['id'] = session('user.id');
            $userDB = M('users');
            $user = $userDB->where($showUser)->find();
            if($user){
                // 判断用户是否有足够金币兑换
                if($user['gold_num'] > $silverData['gold']){
                    // 进行金币兑换操作
                    // 用户金币等于当前金币-兑换金额
                    $saveData['gold_num'] = $user['gold_num'] - $silverData['gold'];
                    // 用户当前银币等于当前银币+兑换的银币
                    $saveData['silver'] = $user['silver'] + $silverData['silver_num'];
                    $userDB->startTrans();
                    $res = $userDB->where($showUser)->save($saveData);

                    // 写记录
                    $addData['user_id']     = session('user.id');
                    $addData['gold_num']    = $silverData['gold'];       
                    $addData['silver_num']  = $silverData['silver_num']; 
                    $addData['content']     = $silverData['content'];
                    $addData['status']      = 0;
                    $addData['updatetime']  = $addData['addtime'] = time();
                    
                    $exchangeRes = M('exchange_record')->add($addData);

                    if($res && $exchangeRes){
                        $userDB->commit();
                        $message    = ['code' => 200, 'message' => '银币兑换兑换成功!'];
                    }else{
                        $userDB->rollback();
                        $message    = ['code' => 403, 'message' => '银币兑换失败请重试!'];
                    }

                }else{
                    // 用户用于兑换的金币余额不足
                    $message    = ['code' => 403, 'message' => '用户用于兑换的金币余额不足!'];
                }
            }else{
                // 用户信息查询失败
                $message    = ['code' => 403, 'message' => '用户信息查询失败!'];
            }
            
        }else{
            // 如果兑换套餐不存在
            $message    = ['code' => 403, 'message' => '没有这个兑换套餐!'];
        }
        
        // 返回JSON格式数据
        $this->ajaxReturn($message);
    }


    /**
     * [payConfirm 确认支付]  已迁移
     * @return [type] [description]
     */
    public function payConfirm()
    {
        echo '已停用,迁移至pay';exit;
//        $address = D('Address');
//        $where['uid'] = session('user.id');
//        $where['status'] = 0;
//        $data = $address->where($where)->find();
//        $assign = [
//            'data' => json_encode($data),
//        ];
//
//        //调用微信JS-SDK类获取签名需要用到的数据
//        $weixin = new WeixinJssdk;
//        $signPackage = $weixin->getSignPackage();
//        // 查询用户微信中的openid
//        // $openId = $weixin->GetOpenid();
//        $openId = $_SESSION['open_id'];
//        //分配数据
//        $this->assign('info',$signPackage);
//        $this->assign('openId',$openId);
//
//
//        $postage = 8.99;
//        $this->assign('postage',$postage);
//        $this->assign($assign);
//        $this->display();
    }

    // 信息确认并生成订单
    public function information()
    {   
        try {
            $post= I('post.');
            dump($post);
            die;
            //检查库存
            if(empty($post['skuattr']) || empty($post['gid'])){
                E('数据错误',40001);
            }

            $sku = $post['skuattr'];
            // dump($sku);die;
            $skuattr = array_column($sku,'id');
            sort($skuattr);
            $map['skuattr'] = implode('_', $skuattr);//属性值id组合

            // dump($map);
            $goodsSku = M('goodsSku');

            $map['gid'] = $post['gid'];
            $res = $goodsSku->where($map)->getField('skustock');

            if(!$res){
                E('该商品类型无库存!',603);
            }
        
            

            $goods = D('Goods');
            $orders = D('ShopOrder');
            $order_detail = D('ShopOrderDetail');
            
            $orders->startTrans();
            $order['uid'] = session('user.id');
            $order['order_id'] = gerOrderId();
            $order['addtime'] = time();
            $order['g_cost'] = "";
            $detail = [];
            foreach($data as $key => $value){
                $where['pr.gid'] = $value['gid'];
                $where['pr.grade'] = session('user.grade');
                $order['g_price'] += $value['money'];
                $order['g_num'] += $value['num'];
                $order['gid']  =  $value['gid'];

                $arr = $goods
                    ->alias('g')
                    ->where($where)
                    ->join('__GOODS_DETAIL__ gd ON g.id=gd.gid', 'LEFT')
                    ->join('__GOODS_PRICE__ pr ON g.id=pr.gid','LEFT')
                    ->join('__PIC__ p ON g.id=p.gid','LEFT')
                    ->field('pr.price,gd.cost,g.name,gd.desc,p.path,gd.is_install')
                    ->find();

                $order['g_cost'] += $value['num']*$arr['cost'];
                $detail['order_id'] = $order['order_id'];
                $detail['gid'] = $value['gid'];
                $detail['num'] = $value['num'];
                $detail['cost'] = $arr['cost'];
                $detail['price'] = $arr['price'];
                $detail['gname'] = $arr['name'];
                $detail['gdes'] = $arr['desc'];
                $detail['gpic'] = $arr['path'];
                $detail['addtime'] = time();
                $detail['is_install'] = $arr['is_install'];
                $detail_statut = $order_detail->add($detail);

                if(!$detail_statut) E('请重新结算',603);
            }
            
            $res = $orders->add($order);
            
            if($res){
                $path = \parse_url($_SERVER['HTTP_REFERER']);
                $file = pathinfo($path['path'])['basename'];
                if($file !== 'shoppingdetail.html'){
                    $ids = implode(',',array_column($data,'gid'));
                    M('Cart')->where(['uid'=>session('user.id'),'gid'=>array('in',$ids)])->delete();
                }
                $orders->commit();
                $this->ajaxReturn($order['order_id']);
            } else {
                $orders->rollback();
                E('请重新购买',603);
            }
        } catch (\Exception $e) {
            $err = [
                'code' => $e->getCode(),
                'msg' => $e->getMessage(),
            ];
            $this->ajaxReturn($err);
        }
    }

    /**
     * [paytosuccess 支付成功]
     * @return [type] [description]
     */
    public function paytosuccess()
    {
        if(IS_AJAX){
            $order_id = I('post.order_id');
            $uid = session('user.id');

            $shopOrder = D('ShopOrder');
            $orderDetail = D('ShopOrderDetail');
            $inventory = D('inventory');
            
            $shopOrder->startTrans();
            $rs = $shopOrder->where(['order_id'=>$order_id,'uid'=>$uid])->setField(['status'=>9]);
            $order_goods = $order_detail->field('gid,num')->where(['order_id'=>$order_id])->select();
            // $goods = \array_column($order_goods,'gid');
            foreach($order_goods as $good){
                // $inventory->where(['gid'=>$good['gid']])->setDec('allnum',$good['num']);
                $display_order[$good['gid']] = $good['num'];
            }
            
            // $display_order = array( 
            //     1 => 4, 
            //     2 => 1, 
            //     3 => 2, 
            //     4 => 3, 
            //     5 => 9, 
            //     6 => 5, 
            //     7 => 8, 
            //     8 => 9 
            // ); 
            $ids = implode(',', array_keys($display_order)); 
            $sql = "UPDATE st_inventory SET allnum = CASE gid "; 
            foreach ($display_order as $id => $ordinal) { 
                $sql .= sprintf("WHEN %d THEN allnum - %d ", $id, $ordinal); 
            } 
            $sql .= "END WHERE id IN ($goods)"; 
            // echo $sql;
            $Model = new \Think\Model(); // 实例化一个model对象 没有对应任何数据表
            $res = $Model->execute($sql);
            if($rs && $res){
                $shopOrder->commit();
                $this->ajaxReturn(['code'=>200,'msg'=>'操作成功','state'=>true]);
            } else {
                $shopOrder->rollback();
                $this->ajaxReturn(['code'=>400,'msg'=>'操作失败','state'=>false]);
            }

        } else {
            $this->display();
        }  	
    }

    /**
     * [paytosuccess 支付成功]
     * @return [type] [description]
     */
    public function paytosuccess_cz()
    {
        $this->display();
    }


    /**
     * [paytofailed 支付失败]
     * @return [type] [description]
     */
    public function paytofailed()
    {
        $this->display();
    }

    /**
     * [paytofailed 订单支付]
     * @return [type] [description]
     */
    public function orderPay()
    {
        if(IS_POST){
            // // 准备订单查询数据
            $showWhere['order_id'] = I('post.order');
                
            // // 查询订单表
            $orderData = M('shop_order')->where($showWhere)->find();
            // print_r($orderData);
            // 判断订单未支付
            if($orderData['status']==8){
                // 订单金额
                $money = $orderData['g_price'];
                // 订单号码
                $order_id = $orderData['order_id'];



                // 订单描述
                $content = '速腾商城商品购买';
                $openId = $_SESSION['open_id'];
                $url = 'http://'.$_SERVER['SERVER_NAME'].U('Home/WeiXinPay/onotify');
                A('Pay')->uniformOrder($openId,$money,$order_id,$content,$url);

//                $this->uniformOrderTow($money,$order_id,$content);
            }else{
                // 订单不存在
                echo -1;
            }
            // $this->uniformOrder(100,123456,'content');
        }
    }

  // 金币支付订单
  public function orderPayByGold(){
    if(IS_POST){
        // echo 1;
        // // 准备订单查询数据
        $showWhere['order_id'] = I('post.order');
            
        // // 查询订单表
        $orderData = M('shop_order')->where($showWhere)->find();
        $orders = M('ShopOrderDetail')->where($showWhere)->field('cprice,num')->select();
        $yunfei = 0;
        foreach($orders as $key => $val){
            $yunfei += $val['cprice'] * $val['num'];
        }
        
        // p($orderData);
        // 判断订单未支付
        if($orderData['status']==8){
            // 订单金额
            $money = $orderData['g_price'];
            $true_money = $money + $yunfei;
            // 订单号码
            $order_id = $orderData['order_id'];

            // 1. 剩余金币检测，支付订单（减相应的金币）
                // 1.1 剩余金币检测
                $showUser['open_id'] = $_SESSION['open_id'];
                $user = M('users');
                $gold_num = $user->where($showUser)->field('gold_num')->find();

                // 1.2 查询相应金币费率
                $gold_rate = D('website')->field('gold_rate')->find();
                // 1.3 订单金额与金币
                $gold_num_true = (int)$gold_num['gold_num'] * (float)$gold_rate['gold_rate'];
                if((float)$true_money > $gold_num_true){
                    $this->ajaxReturn(['code'=>1001,'msg'=>'金币不足']);
                }

                // 4.1 库存检测
            
            
                // 1.4 支付订单
            M()->startTrans();
            try{
                $gold_money = $true_money / (float)$gold_rate['gold_rate']; 
                $pay_res = $user->where($showUser)->setDec('gold_num',$gold_money);
                if(!$pay_res){
                    E('支付失败',1002);
                }
            // 2. 更改订单状态，支付类型更新
                $uid = $orderData['uid'];

                $shopOrder = D('ShopOrder');
                $orderDetail = D('ShopOrderDetail');
                $inventory = D('inventory');

                $rs = $shopOrder->where(['order_id'=>$order_id,'uid'=>$uid])->setField(['status'=>9,'mode'=>3]);
                \Think\Log::write('地址'.json_encode($rs));
            // 3. 表票金额录入
                $order_goods = $orderDetail->field('gid,num')->where(['order_id'=>$order_id])->select();
                // p($order_goods);die;
                D('invoice')->where(['oid'=>$order_id])->save(['oprice'=>$orderData['g_price']]);
                foreach($order_goods as $good){
                    $display_order[$good['gid']] = $good['num'];
                }
            // 4. 库存检测，减库存
                $ids = implode(',', array_keys($display_order));

                $sql = "UPDATE st_inventory SET allnum = CASE gid ";
                foreach ($display_order as $id => $ordinal) {
                    $sql .= sprintf("WHEN %d THEN allnum - %d ", $id, $ordinal);
                }
                $sql .= "END WHERE gid IN ($ids)";
                // file_put_contents('./wxcallback.txt',$sql."\r\n\r\n", FILE_APPEND);
                $Model = new \Think\Model(); // 实例化一个model对象 没有对应任何数据表
                $res = $Model->execute($sql);
            // 5.地址写入
                    $saveOrder['order_id']  = $orderData['order_id'];
                    // 用户ID
                    $showAddres['uid']      = $orderData['uid'];
                    // 默认的地址
                    $showAddres['status']   = 0;
                    // 快递地址ID
                    $saveOrderDara['address_id'] = M('address')->where($showAddres)->find()['id'];
                    // 准备更新数据                 
                    M('shop_order')->where($saveOrder)->save($saveOrderDara);
            // 6. 订单支付完成返回信息
                M()->commit(); 
                $this->ajaxReturn(['code'=>200,'msg'=>'支付成功']);
            }catch(\Exception $e){
                M()->rollback();
                $err = [
                    'code' => $e->getCode(),
                    'msg' => $e->getMessage()
                ];
                $this->ajaxReturn($err);
            }
        }else{
            // 订单不存在
            $this->ajaxReturn(['code'=>-1,'msg'=>'已支付']);
        }
        // $this->uniformOrder(100,123456,'content');
    }
}

 // 银币支付订单
  public function orderPayBySilver(){
    if(IS_POST){
        // echo 1;
        // // 准备订单查询数据
        $showWhere['order_id'] = I('post.order');
            
        // // 查询订单表
        $orderData = M('shop_order')->where($showWhere)->find();
        $orders = M('ShopOrderDetail')->where($showWhere)->field('cprice,num')->select();
        $yunfei = 0;
        foreach($orders as $key => $val){
            $yunfei += $val['cprice'] * $val['num'];
        }
        
        // p($orderData);
        // 判断订单未支付
        if($orderData['status']==8){
            // 订单金额
            $money = $orderData['g_price'];
            $true_money = $money + $yunfei;
            // 订单号码
            $order_id = $orderData['order_id'];

            // 1. 剩余金币检测，支付订单（减相应的金币）
                // 1.1 剩余金币检测
                $showUser['open_id'] = $_SESSION['open_id'];
                $user = M('users');
                $silver = $user->where($showUser)->field('silver')->find();

                // 1.2 查询相应金币费率
                $silver_rate = D('website')->field('silver_rate')->find();
                // 1.3 订单金额与金币
                $silver_true = (float)$silver['silver'] * (float)$silver_rate['silver_rate'];
                if((float)$true_money > $silver_true){
                    $this->ajaxReturn(['code'=>1001,'msg'=>'银币不足']);
                }
                // 4.1 库存检测
            
            $silver_money = $true_money / (float)$silver_rate['silver_rate'];

                // 1.4 支付订单
            M()->startTrans();
            try{
                
                $pay_res = $user->where($showUser)->setDec('silver',$silver_money);
                if(!$pay_res){
                    E('支付失败',1002);
                }
            // 2. 更改订单状态，支付类型更新
                $uid = $orderData['uid'];

                $shopOrder = D('ShopOrder');
                $orderDetail = D('ShopOrderDetail');
                $inventory = D('inventory');

                $rs = $shopOrder->where(['order_id'=>$order_id,'uid'=>$uid])->setField(['status'=>9,'mode'=>4]);
               
            // 3. 表票金额录入
                $order_goods = $orderDetail->field('gid,num')->where(['order_id'=>$order_id])->select();
                // p($order_goods);die;
                D('invoice')->where(['oid'=>$order_id])->save(['oprice'=>$orderData['g_price']]);
                foreach($order_goods as $good){
                    $display_order[$good['gid']] = $good['num'];
                }
            // 4. 库存检测，减库存
                $ids = implode(',', array_keys($display_order));

                $sql = "UPDATE st_inventory SET allnum = CASE gid ";
                foreach ($display_order as $id => $ordinal) {
                    $sql .= sprintf("WHEN %d THEN allnum - %d ", $id, $ordinal);
                }
                $sql .= "END WHERE gid IN ($ids)";
                // file_put_contents('./wxcallback.txt',$sql."\r\n\r\n", FILE_APPEND);
                $Model = new \Think\Model(); // 实例化一个model对象 没有对应任何数据表
                $res = $Model->execute($sql);
            // 5.地址写入
                $saveOrder['order_id']  = $orderData['order_id'];
                // 用户ID
                $showAddres['uid']      = $orderData['uid'];
                // 默认的地址
                $showAddres['status']   = 0;
                // 快递地址ID
                $saveOrderDara['address_id'] = M('address')->where($showAddres)->find()['id'];
                // 准备更新数据
                M('shop_order')->where($saveOrder)->save($saveOrderDara);
            // 6. 订单支付完成返回信息
                M()->commit(); 
                $this->ajaxReturn(['code'=>200,'msg'=>'支付成功']);
            }catch(\Exception $e){
                M()->rollback();
                $err = [
                    'code' => $e->getCode(),
                    'msg' => $e->getMessage()
                ];
                $this->ajaxReturn($err);
            }
        }else{
            // 订单不存在
            $this->ajaxReturn(['code'=>-1,'msg'=>'已支付']);
        }
        // $this->uniformOrder(100,123456,'content');
    }
}
    // 套餐支付
    public function choosePay()
    {
        $showWhere['order_id'] = I('post.order');

        // // 查询订单表
        $orderData = M('order_setmeal')->where($showWhere)->find();

        // print_r($orderData);
        // 判断订单未支付
        if($orderData['status']==0){
            // 订单金额
            $money = $orderData['goods_price'];
            // 订单号码
            $order_id = $orderData['order_id'];
            // 订单描述
            $content = '速腾商城套餐购买';
            $this->uniformOrderSetmeal($money,$order_id,$content);
        }else{
            // 订单不存在
            echo -1;
        }
    }
// 套餐银币支付
    public function choosePayBySilver(){
        $showWhere['order_id'] = I('post.order');
        // p(I(''));die;
        // // 查询订单表
        $orderData = M('order_setmeal')->where($showWhere)->find();

        // 判断订单未支付
        if($orderData['status']==0){
            // 订单金额
            $money = $orderData['goods_price'];
            $true_money = $money;
            // 订单号码
            $order_id = $orderData['order_id'];

            // 1. 剩余金币检测，支付订单（减相应的金币）
                // 1.1 剩余金币检测
                $showUser['open_id'] = $_SESSION['open_id'];
                $user = M('users');
                $silver = $user->where($showUser)->field('silver')->find();

                // 1.2 查询相应金币费率
                $silver_rate = D('website')->field('silver_rate')->find();
                // 1.3 订单金额与金币
                $silver_true = (float)$silver['silver'] * (float)$silver_rate['silver_rate'];
                if((float)$true_money > $silver_true){
                    $this->ajaxReturn(['code'=>1001,'msg'=>'银币不足']);
                }

                // 4.1 库存检测
            
            
                // 1.4 支付订单
            M()->startTrans();
            try{
                $silver_money = (float)$true_money / (float)$silver_rate['silver_rate'];
                $pay_res = $user->where($showUser)->setDec('silver',$silver_money);
                if(!$pay_res){
                    E('支付失败',1002);
                }
            // 2. 更改订单状态，支付类型更新
               //$uid = M('Users')->where("open_id='{$result['']}'")->find()['id'];
            //file_put_contents('./wx_pay1.txt',$xml."\r\n", FILE_APPEND);
            // 如果订单号不为空

                if($order_id){
                //                $did =  $result['out_trade_no'];
                
                                $did = $order_id;
                                //file_put_contents('./wx_pay1.txt',$result['out_trade_no']."\r\n", FILE_APPEND);
                                //file_put_contents('./wx_pay1.txt',$uid."\r\n", FILE_APPEND);
                                // 获取传回来的订单号
                                $data['order_id'] = $did;
                
                
                
                
                                // 查询订单是否已处理
                                // 查询订单是否已处理
                                $orderData = M('orders')->where($data)->field('is_pay,order_id,total_price,device_id')->find();
                
                                // 1分钱测试数据
                                // $orderData['total_price'] = 1;
                
                                // dump($data);die;
                                // 如果订单未处理，订单支付金额等于订单实际金额
                                $result['total_fee'] = $true_money;
                                if(empty($orderData['is_pay']) && $orderData['total_price'] == $result['total_fee']){

                                    //file_put_contents('./wx_pay121.txt',$xml."\r\n", FILE_APPEND);
                                    //                    dump($result);
                                    // 处理订单
                                    // 实例化订单对象
                                    $orders = M('orders');
                                    // 实例化订单滤芯对象
                                    //                    $orderFilter = M('order_filter');
                                    // 实例化订单套餐对象
                                    $orderSetmeal = M('order_setmeal');
                                    // 实例化设备详细信息对象
                                    $devicesStatu = M('devices_statu');
                                    // 实例化设备对象
                                    $device = M('Devices');
                                    // 实例化充值流水对象
                                    $flowObj = M('Flow');
                
                //
                
                                    // 修改订单状态为已付款
                                    $isPay['is_pay'] = 1;
                
                
                                    //show($isPayRes);die;
                                    // 查询订单包含的全部套餐
                                    $orderSetmealData = $orderSetmeal->where($data)->select();
                
                
                                    if($orderSetmealData){
                                        $isPay['is_recharge'] = 1;
                                    }
                
                                    $isPayRes = $orders->where($data)->save($isPay);
                                    $isStatus = $orderSetmeal->where($data)->save(['status'=>1]);
                //                         $isPayRes = $orders->where($data)->find();
                //                     $isStatus = $orderSetmeal->where($data)->find();
                
                                    // dump($orderSetmealData);die;
                                    // 充值状态
                                    $status = 0;
                
                
                                    if($orderSetmealData){
                
                
                                        //查询当前用户
                                        $user_info = M('users')->field('id,invitation_code,open_id')->where(['open_id'=> $_SESSION['open_id']])->find();
                
                //                        file_put_contents('./222222.txt',M('users')->getLastSql() ."\r\n", FILE_APPEND);
                                        //show($orderSetmealData);die;
                                        // 统计未处理套餐数量
                                        $countNun = count($orderSetmealData);
                
                                        // 定义计数器
                                        $num     = 0;
                                        $flownum = 0;
                                        //file_put_contents('./wx_pay1uid.txt',$result['out_trade_no']."\r\n", FILE_APPEND);
                                        // 查询当前设备编号
                                        //                        $deviceId['id'] = $did;
                                        $deviceId['id'] = $orderData['device_id'];
                
                                        //file_put_contents('./wx_pay2uid.txt',$uid."\r\n", FILE_APPEND);
                                        $deviceCode['DeviceID'] = $device->where($deviceId)->find()['device_code'];
                
                
                                        foreach ($orderSetmealData as $value) {
                
                                            // 查询设备当前剩余流量
                                            $devicesStatus = $devicesStatu->where($deviceCode)->find();
                ////查找对应的租金总价
                                            $setmeal_money = M('setmeal')->field('money,flow,tid,describe')->where(['id'=>$orderSetmealData[0]['setmeal_id']])->find();
                                            //查找对应的滤芯成本
                                            $setmeal = M('type')->where(['id'=>$setmeal_money['tid']])->find();
                                            //计算出利润
                //                               dump($setmeal_money['money']-($setmeal['money']+$setmeal['cost']*$setmeal_money['flow']));exit;
                                            if ($orderData['total_price'] < $setmeal['money']) {
                                                $money = 0;
                                            } else {
                                                $money =      $orderData['total_price']-($setmeal['money']+$setmeal['cost']*$setmeal_money['flow']);
                                            }
                                            // $devicesStatuReFlow = $devicesStatu->where($deviceCode)->find()['reflow']-0;
                                            $devicesStatuReFlow = $devicesStatus['reflow'];
                                            $devicesStatuReDay = $devicesStatus['reday'];
                                            // file_put_contents('套餐模式',var_export($value['remodel'], true),FILE_APPEND);
                                            //                             if ($value['remodel'] == 1) {
                                            //                                 // 充值后流量应剩余天数
                                            //                                 $Flow['ReDay'] = $devicesStatuReDay + ($value['flow']*$value['goods_num']);
                                            //                             } else {
                                            //                                 // 充值后流量应剩余流量
                                            //                                 $Flow['ReFlow'] = $devicesStatuReFlow + ($value['flow']*$value['goods_num']);
                                            //                             }
                                            switch ($value['remodel']) {
                                                case '0'://流量
                                                    $Flow['ReFlow'] = $devicesStatuReFlow + ($value['flow']*$value['goods_num']);
                                                    break;
                                                case '1'://时长
                                                    $Flow['ReDay'] =$devicesStatuReDay  + ($value['flow']*$value['goods_num']);
                                                    break;
                                                default:
                                                    # code...
                                                    break;
                                            }
                                            $Flow['data_statu']=1;
                
                                            \Think\Log::write(json_encode($Flow), '更新devicesStatu');
                
                                            // 修改设备剩余流量
                                            $FlowRes = $devicesStatu->where($deviceCode)->save($Flow);
                                            // echo $devicesStatu->getlastsql();
                
                
                                            // file_put_contents('jfdsk',var_export($devicesStatu->_sql(), true),FILE_APPEND);
                                            // 准备发送指令
                                            // if(empty($Flow['ReDay'])){
                                            //     $msg = [
                                            //         'DeviceID'=>$deviceCode['DeviceID'],
                                            //         'PackType'=>'SetData',
                                            //         'Vison'=>'0',
                                            //         'ReFlow'=>$Flow['ReFlow'],
                                            //     ];
                                            // } else {
                                            //     $msg = [
                                            //         'DeviceID'=>$deviceCode['DeviceID'],
                                            //         'PackType'=>'SetData',
                                            //         'Vison'=>'0',
                                            //         'ReDay'=>$Flow['ReDay'],
                                            //     ];
                                            // }
                                            // dump($msg);die;
                
                                            // 写充值流水
                                            // 订单编号
                                            $flowData['order_id']       = $value['order_id'];
                                            // 用户ID
                                            $flowData['user_id']            = $user_info['id'];
                                            // 充值金额
                                            //
                                            $flowData['money']          = $value['money'];
                                            // 充值方式
                                            $flowData['mode']           = 1;
                                            // 充值流量
                                            $flowData['flow']           = $value['flow'];
                                            // 套餐数量
                                            $flowData['num']            = $value['goods_num'];
                                            // 套餐描述
                                            $flowData['describe']       = $value['describe'];
                                            // 当前流量
                                            $flowData['currentflow']    = $Flow['ReFlow'];
                                            // 充值时间
                                            $flowData['addtime']           = time();
                                            //show($flowData);die;
                                            // 创建充值流水
                                            $flowObjRes = $flowObj->add($flowData);
                
                                            // dump($orderData);
                                            // dump($flowObjRes);die;
                
                                            // 判断流水是否创建成果
                                            if($flowObjRes){
                                                // 定时器++
                                                $flownum++;
                                            }
                                            //                            dump($FlowRes);
                
                                            // 判断修改结果
                                            if($FlowRes){
                                                // 计数器++
                                                $num++;
                                            }
                
                                        }
                
                
                                        //                         die;
                                        // 全部套餐充值完成
                                        if($countNun == $num && $countNun == $flownum){
                
                                            //查找对应的租金总价
                                            $setmeal_money = M('setmeal')->field('money,flow,tid,describe')->where(['id'=>$orderSetmealData[0]['setmeal_id']])->find();
                                            //查找对应的滤芯成本
                                            $setmeal = M('type')->where(['id'=>$setmeal_money['tid']])->find();
                                            //计算出利润
                //                               dump($setmeal_money['money']-($setmeal['money']+$setmeal['cost']*$setmeal_money['flow']));exit;
                                            if ($setmeal_money['money'] < $setmeal['money']) {
                                                $money = 0;
                                            } else {
                                                $money =      $setmeal_money['money']-($setmeal['money']+$setmeal['cost']*$setmeal_money['flow']);
                                            }
                
                                            //查询分配比例
                                            $butros = M('butros')->find();
                                            //销售奖(定义 卖商品的经销商)
                                            $com_c = $money*($butros['com_c']/ 100);
                                            //市场推广奖
                                            $com_d = $money*(($butros['com_b']/ 100)*(50/ 100));
                                            //市场培育将
                                            $com_p = $money*(($butros['com_b']/100)*((50/100)));
                                            //团队管理奖 B级加盟商
                                            $com_t =  $money*(($butros['com_a']/ 100)*(50/ 100));
                                            //团队管理奖 A级加盟商
                                            $com_ta =  $money*(($butros['com_a']/100)*((100-50)/100));
                //                                dump($com_c);
                //                                dump($com_d);
                //                                dump($com_p);
                //                                dump($com_t);
                //                                dump($com_ta);
                //                                dump($money);exit;
                                            //查出当前推荐商人
                
                                            $c_info = M('vendors')->where(['code'=>$user_info['invitation_code']])->find();
                                            // echo M('vendors')->getLastSql();
                
                                            // file_put_contents('./wx3333333_payFee.txt',M('vendors')->getLastSql()."\r\n", FILE_APPEND);
                                            //查出推荐人的分公司
                                            $f_info = M('vendors')->where(['code'=>$c_info['office_code']])->find();
                                            //销售奖(卖商品的经销商 分公司)
                                            if ($f_info) {
                                                $earnings_comc = M('vendors')->where(['id'=>$f_info['id']])->setInc('abonus',$com_c);
                
                                                file_put_contents('./wx2_payFee.txt', M('vendors')->getLastSql()."\r\n", FILE_APPEND);
                
                                                //销售奖收益记录
                                                if ($earnings_comc) {
                
                                                    M('earnings')->add(['orderid'=>$orderData['order_id'],'type'=>2,'open_id'=>$f_info['open_id'],'vid'=>$f_info['id'],'abonus'=>$com_c,'create_time'=>date('Y-m-d
                                                    H:i:s')]);
                                                    // file_put_contents('./wx2_payFee.txt', M('earnings')->getLastSql()."\r\n", FILE_APPEND);
                
                                                }
                                            }
                
                                            //市场推广奖(定义 卖商品的经销商推荐人) 只查询存在的
                                            if ($c_info){
                                                $earnings_comd = M('vendors')->where(['id'=>$c_info['id']])->setInc('abonus',$com_d);
                                                //销售奖收益记录
                                                if ($earnings_comd) {
                                                    M('earnings')->add(['orderid'=>$orderData['order_id'],'type'=>2,'open_id'=>$c_info['open_id'],'vid'=>$c_info['id'],'abonus'=>$com_d,'create_time'=>date('Y-m-d H:i:s')]);
                                                }
                                            }
                                            //查找直系推荐关系中的最近B级加盟商(包括自己)
                                            //                    $my_level_info = M('vendors')->field('id,leavel,code,path,updatetime')->where(['id'=>$list['ccid']])->find();
                                            //如果自己是B级 利润给自己 不是的话 给最近的加盟商呢( 市场培育将)
                                            if ($c_info['leavel'] == 3) {
                
                                                //                         //市场培育奖励
                                                //                    M('vendors')->where(['id'=>$my_level_info['id']])->save(['updatetime'=>time()]);
                                                $earnings_comp = M('vendors')->where(['id'=>$c_info['id']])->setInc('abonus',$com_p);
                                                //市场培育收益记录
                                                if ($earnings_comp) {
                                                    M('earnings')->add(['orderid'=>$orderData['order_id'],'type'=>2,'open_id'=>$c_info['open_id'],'vid'=>$c_info['id'],'abonus'=>$com_p,'create_time'=>date('Y-m-d H:i:s')]);
                                                }
                
                                            } else {
                                                if ($c_info['path'] != null) {
                
                                                    //查找直系推荐关系中的最近B级经销商(包括自己)
                                                    $path = explode('-',$c_info['path']);
                                                    $in_B['id']  = array('in',$path);
                                                    $in_B['leavel'] = 3;
                                                    //查找最近的B级经销商
                                                    $my_level_info = M('vendors')->field('id,leavel,code,path,updatetime,open_id')->order('id desc')->where($in_B)->find();
                                                    if ($my_level_info) {
                                                        //                            M('vendors')->where(['id'=>$my_level_info['id']])->save(['updatetime'=>time()]);
                                                        M('vendors')->where(['id'=>$my_level_info['id']])->setInc('abonus',$com_p);
                                                        //
                                                        if ($earnings_comc) {
                                                            M('earnings')->add(['orderid'=>$orderData['order_id'],'type'=>2,'open_id'=>$my_level_info['open_id'],'vid'=>$my_level_info['id'],'abonus'=>$com_p,'create_time'=>date('Y-m-d H:i:s')]);
                                                        }
                                                    }
                                                }
                                            }
                                            //查找团队管理奖收益人
                                            if ($c_info['path'] != null) {
                
                                                //查找直系推荐关系中的最近B级经销商(包括自己)
                                                $path = explode('-',$c_info['path']);
                                                $in_B['id']  = array('in',$path);
                                                $in_B['leavel'] = 3;
                                                $in_B['updatetime'] = array('lt',$c_info['updatetime']);
                                                $in_info = M('vendors')->where($in_B)->find();
                                                $earnings_ta = M('vendors')->where(['id'=>$in_info['id']])->setInc('abonus',$com_t);
                                                //市场培育收益记录
                                                if ($earnings_ta) {
                                                    M('earnings')->add(['orderid'=>$orderData['order_id'],'type'=>2,'open_id'=>$in_info['open_id'],'vid'=>$in_info['id'],'abonus'=>$com_t,'create_time'=>date('Y-m-d H:i:s')]);
                                                }
                                            }
                                            //B级加盟商受益人
                                            //如果自己是A级 利润给自己 不是的话 给最近的加盟商呢
                                            if ($c_info['leavel'] == 2) {
                                                //团队管理奖 A级加盟商
                                                M('vendors')->where(['id'=>$c_info['id']])->save(['updatetime'=>time()]);
                                                $earnings_ta = M('vendors')->where(['id'=>$c_info['id']])->setInc('abonus',$com_ta);
                
                                                //市场培育收益记录
                                                if ($earnings_ta) {
                                                    M('earnings')->add(['orderid'=>$orderData['order_id'],'type'=>2,'open_id'=>$c_info['open_id'],'vid'=>$c_info['id'],'abonus'=>$com_ta,'create_time'=>date('Y-m-d H:i:s')]);
                                                }
                
                                            } else {
                                                if ($c_info['path'] != null) {
                
                                                    //查找直系推荐关系中的最近B级加盟商(包括自己)
                                                    $path = explode('-',$c_info['path']);
                                                    $in_A['id']  = array('in',$path);
                                                    $in_A['leavel'] = 2;
                                                    $path_info_A = M('vendors')->field('id,leavel,code,path,open_id')->order('id desc')->where($in_A)->find();
                
                                                    if ($path_info_A) {
                //                            M('vendors')->where(['id'=>$path_info_A['id']])->save(['updatetime'=>time()]);
                                                        $earnings_ta = M('vendors')->where(['id'=>$path_info_A['id']])->setInc('abonus',$com_ta);
                                                        if ($earnings_ta) {
                                                            M('earnings')->add(['orderid'=>$orderData['order_id'],'type'=>2,'open_id'=>$path_info_A['open_id'],'vid'=>$path_info_A['id'],'abonus'=>$com_ta,'create_time'=>date('Y-m-d H:i:s')]);
                                                        }
                                                    }
                                                }
                                            }
                                            // 充值和流水完成，状态设为1
                                            $status = 1;
                                        }
                                        // echo 12;
                                    }else{
                                        // 没有套餐默认值，状态设为1
                                        $status = 1;
                                    }
                
                                    // show($msg);die;
                                    // file_put_contents('saaa',$isPayRes .'jfdslajfds'. $status);
                                    if($isPayRes && $status && $isStatus){
                
                

                                        file_put_contents('./wx22223_payFee.txt',$b."\r\n", FILE_APPEND);
                                        $sc=A("Api/Action");
                
                                        $sc->sysnc($deviceCode['DeviceID']);
                                        //我觉得应该按订单的总价为标准
                
                
                
                
                                    }else{
                                        // file_put_contents('./wx_notifyEeor.txt','订单号：'.$result['attach']."充值失败 \r\n", FILE_APPEND);
                                    }
                                }else{
                                    // 充值金额不匹配
                                    // if($orderData['total_price'] != $result['total_fee']){
                                    //    file_put_contents('./wx_notifymoney.txt','订单号：'.$result['attach']."充值失败,金额不匹配。订单金额：{$orderData['total_price']} ，充值金额：{$result['total_fee']} \r\n", FILE_APPEND);
                                    // }
                                }
                            }
            // 5. 订单支付完成返回信息

                M()->commit(); 
                $this->ajaxReturn(['code'=>200,'msg'=>'支付成功']);
            }catch(\Exception $e){
                M()->rollback();
                $err = [
                    'code' => $e->getCode(),
                    'msg' => $e->getMessage()
                ];
                $this->ajaxReturn($err);
            }
        }else{
            // 订单不存在
            $this->ajaxReturn(['code'=>-1,'msg'=>'已支付']);
        }
    }

    // 套餐金币支付
    public function choosePayByGold(){
        $showWhere['order_id'] = I('post.order');
        // p(I(''));die;
        // // 查询订单表
        $orderData = M('order_setmeal')->where($showWhere)->find();

        // 判断订单未支付
        if($orderData['status']==0){
            // 订单金额
            $money = $orderData['goods_price'];
            $true_money = $money;
            // 订单号码
            $order_id = $orderData['order_id'];

            // 1. 剩余金币检测，支付订单（减相应的金币）
                // 1.1 剩余金币检测
                $showUser['open_id'] = $_SESSION['open_id'];
                $user = M('users');
                $gold_num = $user->where($showUser)->field('gold_num')->find();

                // 1.2 查询相应金币费率
                $gold_rate = D('website')->field('gold_rate')->find();
                // 1.3 订单金额与金币
                $gold_true = (float)$gold_num['gold_num'] * (float)$gold_rate['gold_rate'];
                if((float)$true_money > $gold_true){
                    $this->ajaxReturn(['code'=>1001,'msg'=>'金币不足']);
                }
                // 4.1 库存检测
            // \Think\Log::write('111');die;
            
                // 1.4 支付订单
            M()->startTrans();
            try{
                $gold_money = (float)$true_money / (float)$gold_rate['gold_rate'];
                $pay_res = $user->where($showUser)->setDec('gold_num',$gold_money);
                if(!$pay_res){
                    E('支付失败',1002);
                }
            // 2. 更改订单状态，支付类型更新
               //$uid = M('Users')->where("open_id='{$result['']}'")->find()['id'];
            //file_put_contents('./wx_pay1.txt',$xml."\r\n", FILE_APPEND);
            // 如果订单号不为空
                if($order_id){
                //                $did =  $result['out_trade_no'];
                
                                $did = $order_id;
                                //file_put_contents('./wx_pay1.txt',$result['out_trade_no']."\r\n", FILE_APPEND);
                                //file_put_contents('./wx_pay1.txt',$uid."\r\n", FILE_APPEND);
                                // 获取传回来的订单号
                                $data['order_id'] = $did;
                
                
                
                
                                // 查询订单是否已处理
                                // 查询订单是否已处理
                                $orderData = M('orders')->where($data)->field('is_pay,order_id,total_price,device_id')->find();
                
                                // 1分钱测试数据
                                // $orderData['total_price'] = 1;
                
                
                                // dump($data);die;
                                // 如果订单未处理，订单支付金额等于订单实际金额
                                $result['total_fee'] = $true_money;
                                if(empty($orderData['is_pay']) && $orderData['total_price'] == $result['total_fee']){

                                    //file_put_contents('./wx_pay121.txt',$xml."\r\n", FILE_APPEND);
                                    //                    dump($result);
                                    // 处理订单
                                    // 实例化订单对象
                                    $orders = M('orders');
                                    // 实例化订单滤芯对象
                                    //                    $orderFilter = M('order_filter');
                                    // 实例化订单套餐对象
                                    $orderSetmeal = M('order_setmeal');
                                    // 实例化设备详细信息对象
                                    $devicesStatu = M('devices_statu');
                                    // 实例化设备对象
                                    $device = M('Devices');
                                    // 实例化充值流水对象
                                    $flowObj = M('Flow');
                
                //
                
                                    // 修改订单状态为已付款
                                    $isPay['is_pay'] = 1;
                
                
                                    //show($isPayRes);die;
                                    // 查询订单包含的全部套餐
                                    $orderSetmealData = $orderSetmeal->where($data)->select();
                
                
                                    if($orderSetmealData){
                                        $isPay['is_recharge'] = 1;
                                    }
                
                                    $isPayRes = $orders->where($data)->save($isPay);
                                    $isStatus = $orderSetmeal->where($data)->save(['status'=>1]);
                //                         $isPayRes = $orders->where($data)->find();
                //                     $isStatus = $orderSetmeal->where($data)->find();
                
                                    // dump($orderSetmealData);die;
                                    // 充值状态
                                    $status = 0;
                
                
                                    if($orderSetmealData){
                
                
                                        //查询当前用户
                                        $user_info = M('users')->field('id,invitation_code,open_id')->where(['open_id'=> $_SESSION['open_id']])->find();
                
                
                //                        file_put_contents('./222222.txt',M('users')->getLastSql() ."\r\n", FILE_APPEND);
                                        //show($orderSetmealData);die;
                                        // 统计未处理套餐数量
                                        $countNun = count($orderSetmealData);
                
                                        // 定义计数器
                                        $num     = 0;
                                        $flownum = 0;
                                        //file_put_contents('./wx_pay1uid.txt',$result['out_trade_no']."\r\n", FILE_APPEND);
                                        // 查询当前设备编号
                                        //                        $deviceId['id'] = $did;
                                        $deviceId['id'] = $orderData['device_id'];
                
                                        //file_put_contents('./wx_pay2uid.txt',$uid."\r\n", FILE_APPEND);
                                        $deviceCode['DeviceID'] = $device->where($deviceId)->find()['device_code'];
                
                
                                        foreach ($orderSetmealData as $value) {
                
                                            // 查询设备当前剩余流量
                                            $devicesStatus = $devicesStatu->where($deviceCode)->find();
                ////查找对应的租金总价
                                            $setmeal_money = M('setmeal')->field('money,flow,tid,describe')->where(['id'=>$orderSetmealData[0]['setmeal_id']])->find();
                                            //查找对应的滤芯成本
                                            $setmeal = M('type')->where(['id'=>$setmeal_money['tid']])->find();
                                            //计算出利润
                //                               dump($setmeal_money['money']-($setmeal['money']+$setmeal['cost']*$setmeal_money['flow']));exit;
                                            if ($orderData['total_price'] < $setmeal['money']) {
                                                $money = 0;
                                            } else {
                                                $money =      $orderData['total_price']-($setmeal['money']+$setmeal['cost']*$setmeal_money['flow']);
                                            }
                                            // $devicesStatuReFlow = $devicesStatu->where($deviceCode)->find()['reflow']-0;
                                            $devicesStatuReFlow = $devicesStatus['reflow'];
                                            $devicesStatuReDay = $devicesStatus['reday'];
                                            // file_put_contents('套餐模式',var_export($value['remodel'], true),FILE_APPEND);
                                            //                             if ($value['remodel'] == 1) {
                                            //                                 // 充值后流量应剩余天数
                                            //                                 $Flow['ReDay'] = $devicesStatuReDay + ($value['flow']*$value['goods_num']);
                                            //                             } else {
                                            //                                 // 充值后流量应剩余流量
                                            //                                 $Flow['ReFlow'] = $devicesStatuReFlow + ($value['flow']*$value['goods_num']);
                                            //                             }
                                            switch ($value['remodel']) {
                                                case '0'://流量
                                                    $Flow['ReFlow'] = $devicesStatuReFlow + ($value['flow']*$value['goods_num']);
                                                    break;
                                                case '1'://时长
                                                    $Flow['ReDay'] =$devicesStatuReDay  + ($value['flow']*$value['goods_num']);
                                                    break;
                                                default:
                                                    # code...
                                                    break;
                                            }
                                            $Flow['data_statu']=1;
                
                                            // Log::write(json_encode($Flow), '更新devicesStatu');
                
                                            // 修改设备剩余流量
                                            $FlowRes = $devicesStatu->where($deviceCode)->save($Flow);
                                            // echo $devicesStatu->getlastsql();
                
                
                                            // file_put_contents('jfdsk',var_export($devicesStatu->_sql(), true),FILE_APPEND);
                                            // 准备发送指令
                                            // if(empty($Flow['ReDay'])){
                                            //     $msg = [
                                            //         'DeviceID'=>$deviceCode['DeviceID'],
                                            //         'PackType'=>'SetData',
                                            //         'Vison'=>'0',
                                            //         'ReFlow'=>$Flow['ReFlow'],
                                            //     ];
                                            // } else {
                                            //     $msg = [
                                            //         'DeviceID'=>$deviceCode['DeviceID'],
                                            //         'PackType'=>'SetData',
                                            //         'Vison'=>'0',
                                            //         'ReDay'=>$Flow['ReDay'],
                                            //     ];
                                            // }
                                            // dump($msg);die;
                
                                            // 写充值流水
                                            // 订单编号
                                            $flowData['order_id']       = $value['order_id'];
                                            // 用户ID
                                            $flowData['user_id']            = $user_info['id'];
                                            // 充值金额
                                            //
                                            $flowData['money']          = $value['money'];
                                            // 充值方式
                                            $flowData['mode']           = 0;
                                            // 充值流量
                                            $flowData['flow']           = $value['flow'];
                                            // 套餐数量
                                            $flowData['num']            = $value['goods_num'];
                                            // 套餐描述
                                            $flowData['describe']       = $value['describe'];
                                            // 当前流量
                                            $flowData['currentflow']    = $Flow['ReFlow'];
                                            // 充值时间
                                            $flowData['addtime']           = time();
                                            //show($flowData);die;
                                            // 创建充值流水
                                            $flowObjRes = $flowObj->add($flowData);
                
                                            // dump($orderData);
                                            // dump($flowObjRes);die;
                
                                            // 判断流水是否创建成果
                                            if($flowObjRes){
                                                // 定时器++
                                                $flownum++;
                                            }
                                            //                            dump($FlowRes);
                
                                            // 判断修改结果
                                            if($FlowRes){
                                                // 计数器++
                                                $num++;
                                            }
                
                                        }
                
                
                                        //                         die;
                                        // 全部套餐充值完成
                                        if($countNun == $num && $countNun == $flownum){
                
                                            //查找对应的租金总价
                                            $setmeal_money = M('setmeal')->field('money,flow,tid,describe')->where(['id'=>$orderSetmealData[0]['setmeal_id']])->find();
                                            //查找对应的滤芯成本
                                            $setmeal = M('type')->where(['id'=>$setmeal_money['tid']])->find();
                                            //计算出利润
                //                               dump($setmeal_money['money']-($setmeal['money']+$setmeal['cost']*$setmeal_money['flow']));exit;
                                            if ($setmeal_money['money'] < $setmeal['money']) {
                                                $money = 0;
                                            } else {
                                                $money =      $setmeal_money['money']-($setmeal['money']+$setmeal['cost']*$setmeal_money['flow']);
                                            }
                
                                            //查询分配比例
                                            $butros = M('butros')->find();
                                            //销售奖(定义 卖商品的经销商)
                                            $com_c = $money*($butros['com_c']/ 100);
                                            //市场推广奖
                                            $com_d = $money*(($butros['com_b']/ 100)*(50/ 100));
                                            //市场培育将
                                            $com_p = $money*(($butros['com_b']/100)*((50/100)));
                                            //团队管理奖 B级加盟商
                                            $com_t =  $money*(($butros['com_a']/ 100)*(50/ 100));
                                            //团队管理奖 A级加盟商
                                            $com_ta =  $money*(($butros['com_a']/100)*((100-50)/100));
                //                                dump($com_c);
                //                                dump($com_d);
                //                                dump($com_p);
                //                                dump($com_t);
                //                                dump($com_ta);
                //                                dump($money);exit;
                                            //查出当前推荐商人
                
                                            $c_info = M('vendors')->where(['code'=>$user_info['invitation_code']])->find();
                                            // echo M('vendors')->getLastSql();
                
                                            // file_put_contents('./wx3333333_payFee.txt',M('vendors')->getLastSql()."\r\n", FILE_APPEND);
                                            //查出推荐人的分公司
                                            $f_info = M('vendors')->where(['code'=>$c_info['office_code']])->find();
                                            //销售奖(卖商品的经销商 分公司)
                                            if ($f_info) {
                                                $earnings_comc = M('vendors')->where(['id'=>$f_info['id']])->setInc('abonus',$com_c);
                
                                                file_put_contents('./wx2_payFee.txt', M('vendors')->getLastSql()."\r\n", FILE_APPEND);
                
                                                //销售奖收益记录
                                                if ($earnings_comc) {
                
                                                    M('earnings')->add(['orderid'=>$orderData['order_id'],'type'=>2,'open_id'=>$f_info['open_id'],'vid'=>$f_info['id'],'abonus'=>$com_c,'create_time'=>date('Y-m-d
                                                    H:i:s')]);
                                                    // file_put_contents('./wx2_payFee.txt', M('earnings')->getLastSql()."\r\n", FILE_APPEND);
                
                                                }
                                            }
                
                                            //市场推广奖(定义 卖商品的经销商推荐人) 只查询存在的
                                            if ($c_info){
                                                $earnings_comd = M('vendors')->where(['id'=>$c_info['id']])->setInc('abonus',$com_d);
                                                //销售奖收益记录
                                                if ($earnings_comd) {
                                                    M('earnings')->add(['orderid'=>$orderData['order_id'],'type'=>2,'open_id'=>$c_info['open_id'],'vid'=>$c_info['id'],'abonus'=>$com_d,'create_time'=>date('Y-m-d H:i:s')]);
                                                }
                                            }
                                            //查找直系推荐关系中的最近B级加盟商(包括自己)
                                            //                    $my_level_info = M('vendors')->field('id,leavel,code,path,updatetime')->where(['id'=>$list['ccid']])->find();
                                            //如果自己是B级 利润给自己 不是的话 给最近的加盟商呢( 市场培育将)
                                            if ($c_info['leavel'] == 3) {
                
                                                //                         //市场培育奖励
                                                //                    M('vendors')->where(['id'=>$my_level_info['id']])->save(['updatetime'=>time()]);
                                                $earnings_comp = M('vendors')->where(['id'=>$c_info['id']])->setInc('abonus',$com_p);
                                                //市场培育收益记录
                                                if ($earnings_comp) {
                                                    M('earnings')->add(['orderid'=>$orderData['order_id'],'type'=>2,'open_id'=>$c_info['open_id'],'vid'=>$c_info['id'],'abonus'=>$com_p,'create_time'=>date('Y-m-d H:i:s')]);
                                                }
                
                                            } else {
                                                if ($c_info['path'] != null) {
                
                                                    //查找直系推荐关系中的最近B级经销商(包括自己)
                                                    $path = explode('-',$c_info['path']);
                                                    $in_B['id']  = array('in',$path);
                                                    $in_B['leavel'] = 3;
                                                    //查找最近的B级经销商
                                                    $my_level_info = M('vendors')->field('id,leavel,code,path,updatetime,open_id')->order('id desc')->where($in_B)->find();
                                                    if ($my_level_info) {
                                                        //                            M('vendors')->where(['id'=>$my_level_info['id']])->save(['updatetime'=>time()]);
                                                        M('vendors')->where(['id'=>$my_level_info['id']])->setInc('abonus',$com_p);
                                                        //
                                                        if ($earnings_comc) {
                                                            M('earnings')->add(['orderid'=>$orderData['order_id'],'type'=>2,'open_id'=>$my_level_info['open_id'],'vid'=>$my_level_info['id'],'abonus'=>$com_p,'create_time'=>date('Y-m-d H:i:s')]);
                                                        }
                                                    }
                                                }
                                            }
                                            //查找团队管理奖收益人
                                            if ($c_info['path'] != null) {
                
                                                //查找直系推荐关系中的最近B级经销商(包括自己)
                                                $path = explode('-',$c_info['path']);
                                                $in_B['id']  = array('in',$path);
                                                $in_B['leavel'] = 3;
                                                $in_B['updatetime'] = array('lt',$c_info['updatetime']);
                                                $in_info = M('vendors')->where($in_B)->find();
                                                $earnings_ta = M('vendors')->where(['id'=>$in_info['id']])->setInc('abonus',$com_t);
                                                //市场培育收益记录
                                                if ($earnings_ta) {
                                                    M('earnings')->add(['orderid'=>$orderData['order_id'],'type'=>2,'open_id'=>$in_info['open_id'],'vid'=>$in_info['id'],'abonus'=>$com_t,'create_time'=>date('Y-m-d H:i:s')]);
                                                }
                                            }
                                            //B级加盟商受益人
                                            //如果自己是A级 利润给自己 不是的话 给最近的加盟商呢
                                            if ($c_info['leavel'] == 2) {
                                                //团队管理奖 A级加盟商
                                                M('vendors')->where(['id'=>$c_info['id']])->save(['updatetime'=>time()]);
                                                $earnings_ta = M('vendors')->where(['id'=>$c_info['id']])->setInc('abonus',$com_ta);
                
                                                //市场培育收益记录
                                                if ($earnings_ta) {
                                                    M('earnings')->add(['orderid'=>$orderData['order_id'],'type'=>2,'open_id'=>$c_info['open_id'],'vid'=>$c_info['id'],'abonus'=>$com_ta,'create_time'=>date('Y-m-d H:i:s')]);
                                                }
                
                                            } else {
                                                if ($c_info['path'] != null) {
                
                                                    //查找直系推荐关系中的最近B级加盟商(包括自己)
                                                    $path = explode('-',$c_info['path']);
                                                    $in_A['id']  = array('in',$path);
                                                    $in_A['leavel'] = 2;
                                                    $path_info_A = M('vendors')->field('id,leavel,code,path,open_id')->order('id desc')->where($in_A)->find();
                
                                                    if ($path_info_A) {
                //                            M('vendors')->where(['id'=>$path_info_A['id']])->save(['updatetime'=>time()]);
                                                        $earnings_ta = M('vendors')->where(['id'=>$path_info_A['id']])->setInc('abonus',$com_ta);
                                                        if ($earnings_ta) {
                                                            M('earnings')->add(['orderid'=>$orderData['order_id'],'type'=>2,'open_id'=>$path_info_A['open_id'],'vid'=>$path_info_A['id'],'abonus'=>$com_ta,'create_time'=>date('Y-m-d H:i:s')]);
                                                        }
                                                    }
                                                }
                                            }
                                            // 充值和流水完成，状态设为1
                                            $status = 1;
                                        }
                                        // echo 12;
                                    }else{
                                        // 没有套餐默认值，状态设为1
                                        $status = 1;
                                    }
                
                                    // show($msg);die;
                                    // file_put_contents('saaa',$isPayRes .'jfdslajfds'. $status);
                                    if($isPayRes && $status && $isStatus){
                
                

                                        file_put_contents('./wx22223_payFee.txt',$b."\r\n", FILE_APPEND);
                                        $sc=A("Api/Action");
                
                                        $sc->sysnc($deviceCode['DeviceID']);
                                        //我觉得应该按订单的总价为标准
                
                
                
                
                                    }else{
                                        // file_put_contents('./wx_notifyEeor.txt','订单号：'.$result['attach']."充值失败 \r\n", FILE_APPEND);
                                    }
                                }else{
                                    // 充值金额不匹配
                                    // if($orderData['total_price'] != $result['total_fee']){
                                    //    file_put_contents('./wx_notifymoney.txt','订单号：'.$result['attach']."充值失败,金额不匹配。订单金额：{$orderData['total_price']} ，充值金额：{$result['total_fee']} \r\n", FILE_APPEND);
                                    // }
                                }
                            }
            // 5. 订单支付完成返回信息

                M()->commit(); 
                $this->ajaxReturn(['code'=>200,'msg'=>'支付成功']);
            }catch(\Exception $e){
                M()->rollback();
                $err = [
                    'code' => $e->getCode(),
                    'msg' => $e->getMessage()
                ];
                $this->ajaxReturn($err);
            }
        }else{
            // 订单不存在
            $this->ajaxReturn(['code'=>-1,'msg'=>'已支付']);
        }
    }



    /**
     * [chongzhi 微信充值接口]
     * @return [type] [description]
     */
    public function wxchongzhi()
    {
        if(IS_POST){
            // 接收充值数据
            $name = I('post.name');

            switch ($name) {
                case 'gold':
                    // 金币充值
                    $showData['id'] = I('post.id');
                    // 查询金币
                    $gold = M('gold')->where($showData)->find();
   
                    // 请求支付
                    $this->uniformOrder($gold['money'],$gold['id'],$gold['content'],'gold');

                    break;
                case 'silver':
                    // 银币充值
                    $showData['id'] = I('post.id');
                    // 查询金币
                    $silver = M('silver')->where($showData)->find();
   
                    // 请求支付
                    $this->uniformOrder($silver['money'],$silver['id'],$silver['content'],'silver');
                    break;
                default:
                    # code...
                    break;
            }
             
        }
    }
   
    /**
     * 统一下单充值并返回数据
     * @return string json格式的数据，可以直接用于js支付接口的调用
     * @param  [type] $money    [订单金额]
     * @param  [type] $order_id [订单号码]
     * @param  [type] $content  [订单详情]
     */
    public function uniformOrder($money,$flow_id,$content,$type)
    {
        // dump($_SESSION);die;
        // $content = substr($content,0,80);
        // 将金额强转换整数
        $money = $money * 100;
        // 冲值测试额1分钱
        $money = 1;
        // 用户在公众号的唯一ID
        $openId = $_SESSION['open_id'];

        //微信examle的WxPay.JsApiPay.php
        vendor('WxPay.jsapi.WxPay#JsApiPay');

        $tools = new \JsApiPay();

        //②、统一下单
        vendor('WxPay.jsapi.WxPay#JsApiPay');
        $input = new \WxPayUnifiedOrder();
        // 傳用戶ID
        // $input->SetDetail($type);
        // 产品内容
        $input->SetBody($content);
        // 唯一订单ID
        $input->SetAttach($flow_id.','.$type);
        // 设置商户系统内部的订单号,32个字符内、可包含字母, 其他说明见商户订单号
        $input->SetOut_trade_no(getOrderId());
        // 产品金额单位为分
        // $input->SetTotal_fee($money);
        // 调试用1分钱
        $input->SetTotal_fee($money);
        // 设置订单生成时间
        // $input->SetTime_start(date("YmdHis"));
        // 设置订单失效时间
        // $input->SetTime_expire(date("YmdHis", time() + 300));
        //$input->SetGoods_tag($uid);
        // 支付成功的回调地址
        // 微信充值回调地址
        $input->SetNotify_url('http://'.$_SERVER['SERVER_NAME'].U('Home/WeiXinPay/congzhiNotify'));
        // 支付方式 JS-SDK 类型是：JSAPI
        $input->SetTrade_type("JSAPI");
        // 用户在公众号的唯一标识
        $input->SetOpenid($openId);
        // print_r($input);die;
        // 统一下单
        $order = \WxPayApi::unifiedOrder($input);
        // print_r($order);die;
        // 返回支付需要的对象JSON格式数据
        $jsApiParameters = $tools->GetJsApiParameters($order);

        echo $jsApiParameters;
        exit;
    }

    /**
     * 统一下单订单支付并返回数据
     * @return string json格式的数据，可以直接用于js支付接口的调用
     * @param  [type] $money    [订单金额]
     * @param  [type] $order_id [订单号码]
     * @param  [type] $content  [订单详情]
     */
    public function uniformOrderTow($money,$order_id,$content)
    {
        // dump($_SESSION);die;
        // $content = substr($content,0,80);
        // 将金额强转换整数
        $money = $money * 100;
        // 冲值测试额1分钱
        $money = 1;
        // 用户在公众号的唯一ID
        $openId = $_SESSION['open_id'];


        //微信examle的WxPay.JsApiPay.php
        vendor('WxPay.jsapi.WxPay#JsApiPay');

        $tools = new \JsApiPay();

        //②、统一下单
        vendor('WxPay.jsapi.WxPay#JsApiPay');
        $input = new \WxPayUnifiedOrder();
        // 傳用戶ID
        //$input->SetDetail($uid);
        // 产品内容
        $input->SetBody($content);
        // 唯一订单ID
        $input->SetAttach($order_id);
        // 设置商户系统内部的订单号,32个字符内、可包含字母, 其他说明见商户订单号
        $input->SetOut_trade_no(gerOrderId());
        // 产品金额单位为分
        // $input->SetTotal_fee($money);
        // 调试用1分钱
        $input->SetTotal_fee($money);
        // 设置订单生成时间
        // $input->SetTime_start(date("YmdHis"));
        // 设置订单失效时间
        // $input->SetTime_expire(date("YmdHis", time() + 300));
        //$input->SetGoods_tag($uid);
        // 支付成功的回调地址
        // 微信充值回调地址
        $input->SetNotify_url('http://'.$_SERVER['SERVER_NAME'].U('Home/WeiXinPay/notify'));
        // 支付方式 JS-SDK 类型是：JSAPI
        $input->SetTrade_type("JSAPI");
        // 用户在公众号的唯一标识
        $input->SetOpenid($openId);
        // 统一下单
        $order = \WxPayApi::unifiedOrder($input);

        // 返回支付需要的对象JSON格式数据
        $jsApiParameters = $tools->GetJsApiParameters($order);

        echo $jsApiParameters;
        exit;
    }

    /**
     * 统一下单订单套餐充值并返回数据
     * @return string json格式的数据，可以直接用于js支付接口的调用
     * @param  [type] $money    [订单金额]
     * @param  [type] $order_id [订单号码]
     * @param  [type] $content  [订单详情]
     */
    public function uniformOrderSetmeal($money,$order_id,$content)
    {
        // dump($_SESSION);die;
        // $content = substr($content,0,80);
        // 将金额强转换整数
        $money = $money * 100;
        // 冲值测试额1分钱
        $money = 1;
        // 用户在公众号的唯一ID
        $openId = $_SESSION['open_id'];


        //微信examle的WxPay.JsApiPay.php
        vendor('WxPay.jsapi.WxPay#JsApiPay');

        $tools = new \JsApiPay();

        //②、统一下单
        vendor('WxPay.jsapi.WxPay#JsApiPay');
        $input = new \WxPayUnifiedOrder();
        // 傳用戶ID
        //$input->SetDetail($uid);
        // 产品内容
        $input->SetBody($content);
        // 唯一订单ID
        $input->SetAttach($order_id);
        // 设置商户系统内部的订单号,32个字符内、可包含字母, 其他说明见商户订单号
        $input->SetOut_trade_no(gerOrderId());
        // 产品金额单位为分
        // $input->SetTotal_fee($money);
        // 调试用1分钱
        $input->SetTotal_fee($money);
        // 设置订单生成时间
        // $input->SetTime_start(date("YmdHis"));
        // 设置订单失效时间
        // $input->SetTime_expire(date("YmdHis", time() + 300));
        //$input->SetGoods_tag($uid);
        // 支付成功的回调地址
        // 微信充值回调地址
        $input->SetNotify_url('http://'.$_SERVER['SERVER_NAME'].U('Home/WeiXinPay/setmealNotify'));
        // 支付方式 JS-SDK 类型是：JSAPI
        $input->SetTrade_type("JSAPI");
        // 用户在公众号的唯一标识
        $input->SetOpenid($openId);
        // 统一下单
        $order = \WxPayApi::unifiedOrder($input);

        // 返回支付需要的对象JSON格式数据
        $jsApiParameters = $tools->GetJsApiParameters($order);

        echo $jsApiParameters;
        exit;
    }



}
