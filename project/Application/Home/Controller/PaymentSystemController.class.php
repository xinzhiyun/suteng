<?php
namespace Home\Controller;
use \Org\Util\WeixinJssdk;

class PaymentSystemController extends CommonController
{
    /**
     * [payConfirm 确认支付]
     * @return [type] [description]
     */
    public function payConfirm()
    {
        $address = D('Address');
        $where['uid'] = session('user.id');
        $where['status'] = 0;
        $data = $address->where($where)->find();
        $assign = [
            'data' => json_encode($data),
        ];

        //调用微信JS-SDK类获取签名需要用到的数据
        $weixin = new WeixinJssdk;
        $signPackage = $weixin->getSignPackage();
        // 查询用户微信中的openid
        // $openId = $weixin->GetOpenid();
        $openId = $_SESSION['open_id'];
        //分配数据        
        $this->assign('info',$signPackage);
        $this->assign('openId',$openId);



        $this->assign($assign);
        $this->display();
    }

    // 信息确认并生成订单
    public function information()
    {
       
        try {
            $goods = D('Goods');
            $orders = D('ShopOrder');
            $order_detail = D('OrderDetail');
            $data = json_decode($_POST['data'],'true');
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
                M('Cart')->where('uid='.session('user.id'))->delete();
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
    	$this->display();
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
                $this->uniformOrderTow($money,$order_id,$content);
            }else{
                // 订单不存在
                echo -1;
            }
            // $this->uniformOrder(100,123456,'content');
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



}
