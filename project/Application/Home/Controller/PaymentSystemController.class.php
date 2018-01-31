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
            $order['time'] = time();
            $order['cost'] = "";
            $detail = [];
            foreach($data as $key => $value){
                $where['gid'] = $value['gid'];
                $order['g_price'] += $value['money'];
                $order['num'] += $value['num'];
                $arr = $goods->alias('g')->where($where)->join('__GOODS_DETAIL__ gd ON g.id=gd.gid', 'LEFT')->field('gd.price,gd.cost')->find();
                $order['cost'] += $value['num']*$arr['cost'];
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
            // dump($order['order']);die;
            if($res){
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
     * [paytofailed 支付失败]
     * @return [type] [description]
     */
    public function paytofailed()
    {
        $this->display();
    }

    /**
     * [chongzhi 微信充值接口]
     * @return [type] [description]
     */
    public function wxchongzhi()
    {
        $id = I('post.id')-1;
        $jeArr =   [
                    100,
                    200,
                    300,
                    400,
                    500,
                    100,
                    200,
                    300,
                    400,
                    500,
                ];
        $contentArr =   [
                    '100元100个金币',
                    '200元200个金币',
                    '300元300个金币',
                    '400元400个金币',
                    '500元500个金币',
                   ' 100元200个银币',
                    '200元400个银币',
                    '300元600个银币',
                    '400元1000个银币',
                    '500元1200个银币',
                ];
        $je = $jeArr[$id];
        //
        //$openId = $_SESSION['user']['open_id'];

        //dump($openId);die;
        // echo $je;
        $this->uniformOrder($je,$id,'速腾商城充值');
    }

    /**
     * 统一下单并返回数据
     * @return string json格式的数据，可以直接用于js支付接口的调用
     * @param  [type] $money    [订单金额]
     * @param  [type] $order_id [订单号码]
     * @param  [type] $content  [订单详情]
     */
    public function uniformOrder($money,$order_id,$content)
    {
        //dump($_SESSION);die;
        // $content = substr($content,0,80);
        // 将金额强转换整数
        $money = $money * 100;
        // 冲值测试额1分钱
        $money = 1;
        // 用户在公众号的唯一ID
        $openId = $_SESSION['user']['open_id'];


        //微信examle的WxPay.JsApiPay.php
        vendor('WxPay.jsapi.WxPay#JsApiPay');

        $tools = new \JsApiPay();

        //②、统一下单
        vendor('WxPay.jsapi.WxPay#JsApiPay');
        $input = new \WxPayUnifiedOrder();
        // 傳用戶ID
        //$input->SetDetail($uid);
        // 产品内容
        $input->SetBody('点球科技商品购买');
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
        $input->SetNotify_url('http://'.$_SERVER['SERVER_NAME'].U('Home/WeiXinPay/congzhiNotify'));
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
