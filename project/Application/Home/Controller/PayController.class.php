<?php
namespace Home\Controller;
use Think\Controller;

/**
 * 统一支付加载控制
 * @package Home\Controller
 */
class PayController extends Controller
{

    /**
     * 金币银币 充值
     * @return [type] [description]
     */
    public function chongzhi()
    {
        // 查询条件
        $showData['status'] = 1;
        // 金币套餐
        $data['gold'] = M('gold')->where($showData)->select();
        // 银币套餐
        $data['silver'] = M('silver')->where($showData)->select();
        // 银币兑换套餐
        $data['gold_silver'] = M('gold_silver')->where($showData)->select();

        $this->wx_info();

        //分配数据
        $this->assign('data',$data);
        // dump($userData);die;
        $this->display();
    }

    /**
     * 设备充值
     */
    public function chooseMeal()
    {
        $map=[
            'ud.status'=>1,
            'ud.uid'=>$_SESSION['user']['id'],
        ];
        $type = D('UserDevice')->getBindType($map);

        $meal = M('setmeal')->where($type)->select();

        $this->wx_info();
        $this->assign('list',json_encode($meal));
        $this->display();
    }

    /**
     * 滤芯 确认支付
     * @return [type] [description]
     */
    public function payConfirm()
    {
        $address = D('Address');
        $where['uid'] = session('user.id');
        // dump(session());
        $where['status'] = 0;
        $data = $address->where($where)->find();

        //查询商品对应的快递运费信息
        foreach ($_SESSION['goodsid'] as $key => $value) {
            // echo $value."<br>";
            $goodsCourier[$value] = M('goods_courier')->where('gid='.$value)->field('gid,cid,cname,cprice')->select();
        }
        unset($_SESSION['goodsid']);
        $assign = [
            'data' => json_encode($data),
            'goodsCourier' => json_encode($goodsCourier),
        ];
        $this->wx_info();

        $this->assign($assign);
        $this->display();
    }

    /**
     * [updateOrder 支付前修改订单的快递运费信息及是否开发票信息]
     * @return [type] [description]
     */
    public function updateOrder()
    {
        //接收前端传过来的订单号进行修改订单信息
        $orderid = $_POST[''];


    }

    /**
     * [invoiceAdd 将发票信息写入订单]
     * @return [type] [description]
     */
    public function invoiceAdd()
    {
        //发票相关信息
        
        //订单id
    }

    /**
     * 设备充值-套餐支付
     */
    public function choosePay()
    {
        $showWhere['setmeal_id'] = I('post.order');



        //查询订单表
        $orderData = M('order_setmeal')->where($showWhere)->find();
        // 判断订单未支付
        if($orderData['status']==0){
            // 订单金额
            $money = $orderData['goods_price'];
            // 订单号码
            $order_id = $orderData['order_id'];
            // 订单描述
            $content = '速腾商城套餐购买';

            $openId = $_SESSION['open_id'];

            $url = 'http://'.$_SERVER['SERVER_NAME'].U('Home/WeiXinPay/setmealNotify');

            $this->uniformOrder($openId,$money,$order_id,$content,$url);
        }else{
            // 订单不存在
            echo -1;
        }
    }
    /*
   * 充值年费
   */
    public function  gradeSelect() {

        $this->wx_info();
        //查出当前会员等级
        $map['id'] = $_SESSION['user']['id'];
        $grade = M('users')->where($map)->field('id,grade')->find();

        switch ($grade['grade']) {
            case '1':
                $grade['grade'] = '3';
                break;
            case '2':
                $grade['grade'] = '2';
                break;
            case '3':
                $grade['grade'] = '1';
                break;
            case '4':
                $grade['grade'] = '0';
                break;
        }

        $annual = M('annual')->find();

        $this->assign('grade',$grade);
        $this->assign('annual',$annual);
        $this->display();
    }
    /*
     * 会员升级充值
     */
    public function annualPay() {
        $userWhere['order_id'] = I('post.order');
        $orderData = M('users_order')->where($userWhere)->find();
        if ($orderData['is_pay'] == 0) {
            //订单号码
            $money = $orderData['price'];
            //订单号
            $order_id = $orderData['order_id'];
            //订单描述
            $content = '速腾'.$orderData['name'].'购买';
            $openId = $_SESSION['open_id'];
            $url = 'http://'.$_SERVER['SERVER_NAME'].U('Home/WeiXinPay/annualNotify');
            $this->uniformOrder($openId,$money,$order_id,$content,$url);
        } else {
            //订单不存在
            echo -1;
        }
    }

    /**
     * [lvxin 滤芯]
     * @return [type] [description]
     */
    public function lvxin()
    {
        $where['status'] = 1;//session('device.did');
        $where['uid'] = session('user.id');
        $devices['d.id'] = M('UserDevice')->where($where)->getField('did');

        $filter = D('Filters');
        $device = D('Devices');

        $res = $filter->getFilters($devices);
        $data = $device
            ->alias('d')
            ->where($devices)
            ->join('__DEVICES_STATU__ ds ON d.device_code=ds.DeviceID', 'LEFT')
            ->find();

        $assign = [
            'res' => json_encode($res),
            'data' => json_encode($data),
        ];
        $this->assign('data',$assign);
        $this->display();
    }





    /**
     * 微信充值接口
     * @return [type] [description]
     */
    public function weixin()
    {
        if(IS_POST){
            // 接收充值数据
            $name = I('post.name');

            switch ($name) {
                case 'gold':   // 金币充值
                    $showData['id'] = I('post.id');
                    // 查询金币
                    $gold = M('gold')->where($showData)->find();

                    // 请求支付
                    $this->uniformOrder($gold['money'],$gold['id'],$gold['content'],'gold');

                    break;
                case 'silver':  // 银币充值

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
     * 加载微信配置信息
     */
    public function wx_info()
    {
        $weixin = new \Org\Util\WeixinJssdk();
        $signPackage = $weixin->getSignPackage();
        $this->assign('wxinfo',$signPackage);

        //$openId = $weixin->GetOpenid();
        $openId = $_SESSION['open_id'];
        $this->assign('openId',$openId);
    }

    /**
     * 统一下单订单支付并返回数据 JsApi
     * @return string json格式的数据，可以直接用于js支付接口的调用
     * @param  [type] $openId    用户openid
     * @param  [type] $money     订单金额(原金额 未乘100的)
     * @param  [type] $order_id  订单id
     * @param  [type] $content    订单详情
     * @param  [type] $notify_url 回调地址
     */
    public function uniformOrder($openId,$money,$order_id,$content,$notify_url)
    {
        $content = substr($content,0,80);
        $money = $money * 100;                          // 将金额强转换整数

        $money = 1;                                     // 冲值测试额1分钱 上线取消此行

        vendor('WxPay.jsapi.WxPay#JsApiPay');
        $tools = new \JsApiPay();

        vendor('WxPay.jsapi.WxPay#JsApiPay');
        $input = new \WxPayUnifiedOrder();
        //$input->SetDetail($uid);

        $input->SetBody($content);                      // 产品内容

        $input->SetAttach($order_id);                   // 唯一订单ID

        $input->SetOut_trade_no(gerOrderId());          // 设置商户系统内部的订单号,32个字符内、可包含字母, 其他说明见商户订单号
        $input->SetTotal_fee($money);                   // 产品金额单位为分

        //$input->SetTime_start(date("YmdHis"));        // 设置订单生成时间
        //$input->SetTime_expire(date("YmdHis", time() + 300));// 设置订单失效时间
        //$input->SetGoods_tag($uid);

        $input->SetNotify_url($notify_url);             // 微信充值回调地址
        $input->SetTrade_type("JSAPI");           // 支付方式 JS-SDK 类型是：JSAPI
        // 用户在公众号的唯一标识
        $input->SetOpenid($openId);

        $order = \WxPayApi::unifiedOrder($input);       // 统一下单

        // 返回支付需要的对象JSON格式数据
        $jsApiParameters = $tools->GetJsApiParameters($order);

        echo $jsApiParameters;
        exit;
    }




}