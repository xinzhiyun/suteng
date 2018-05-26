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
        // echo json_decode($_POST['data']);
        $data = json_decode($_POST['data'],'true');
        // 库存检测
        $result=[];
        foreach($data as $val){
            $result[] =  [
                    'status'=> D('inventory')->where(['gid'=>$val['gid'],'allnum'=>['LT',$val['num']]])->select()?'pass':'fail',
                    'gid'=>$val['gid']
            ];
        }
        if(in_array('fail',\array_column($result,'status'))){
            $this->ajaxReturn(['code'=>604,'msg'=>'商品库存不足','data'=>$result]);
        }

        try {
            $goods = D('Goods');
            $orders = D('ShopOrder');
            $order_detail = D('OrderDetail');
            
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
                    ->join('__PRICE__ pr ON g.id=pr.gid','LEFT')
                    ->field('pr.price,gd.cost')
                    ->find();
                $order['g_cost'] += $value['num']*$arr['cost'];
                $detail['order_id'] = $order['order_id'];
                $detail['gid'] = $value['gid'];
                $detail['num'] = $value['num'];
                $detail['cost'] = $arr['cost'];
                $detail['price'] = $arr['price'];
                $detail['addtime'] = time();
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
            $orderDetail = D('OrderDetail');
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
            // echo 1;
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
