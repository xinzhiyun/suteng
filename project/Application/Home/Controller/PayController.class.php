<?php
namespace Home\Controller;
use Common\Tool\Weixin;
use Think\Controller;

/**
 * 统一支付加载控制
 * @package Home\Controller
 */
class PayController extends Controller
{
    // 服务人员 代缴费
    public function servicePeoplePay(){
        $weixin = new \Org\Util\WeixinJssdk();
        $signPackage = $weixin->getSignPackage();
        $this->assign('wxinfo',$signPackage);
        $this->display();
    }

    // 个人中心 - 设备 租赁转个人 支付
    public function deviceIndex()
    {
        $this->wx_info();   //加载微信信息

        if(empty($_SESSION['open_id'])){
            $openId =  Weixin::GetOpenid();
            $_SESSION['open_id'] = $openId;
        }

        // 查询绑定设备
        $user_device = D('UserDevice');
        $map['ud.uid'] = session('user.id');

        $bind_device = $user_device->getBindInof($map); //where('uid='.session('user.id'))->select();
        // 将当前设备的ID存到SESSION中
        $where['status'] = 1;
        $where['uid'] = session('user.id');
        $cur_device = $user_device->where($where)->find();
        session('device.did',$cur_device['did']);
        session('user.did',$cur_device['did']);
        //分配数据
        $this->assign('bindInfo',$bind_device);
        $this->assign('openId',$openId);
        $this->display();
    }

    // 服务站申请 支付押金
    public function registerPay()
    {
        if( empty($_SESSION['open_id']) ){
            $_SESSION['open_id'] = Weixin::GetOpenid();
        }

        $map=[
            'open_id'=>$_SESSION['open_id'],
            'status'=>3,
        ];
        $res = M('service_apply')->where($map)->field('id,servicename,name,company,legal,phone')->find();
        if(empty($res)){
            notice('请等待审核!','finalTip','ServiceLogin');
        }

        $weixin = new \Org\Util\WeixinJssdk();
        $signPackage = $weixin->getSignPackage();
        $joinsost = M('service_seting')->where(1)->getField('joinsost');
        $joinsost = number_format(intval(trim($joinsost), 10)/100,0,'.','');

        $this->assign('serviceInfo',$res);
        $this->assign('joinsost',$joinsost);
        $this->assign('wxinfo',$signPackage);
        $this->display('registerPay');
    }

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

        $rate = D('website')->field('gold_rate,silver_rate')->find();
        $showUser['open_id'] = $_SESSION['open_id'];
        $user = M('users')->where($showUser)->find();
        $assign = [
            'user' => json_encode($user),
            'rate' => json_encode($rate)
        ];
        $this->wx_info();
        $this->assign('list',json_encode($meal));
        $this->assign('infos', $assign);
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

        //根据订单id查询出该订单下有多少商品
        
        if (!empty($_GET['order_id'])) {
            $_SESSION['order']['orderid'] = $_GET['order_id'];
        }
        

        $orderid = $_SESSION['order']['orderid'];

        $OrderDetail = M('ShopOrderDetail')
                        ->alias('od')
                        ->where('order_id='.$orderid)
                        ->join('st_goods g ON od.gid = g.id','LEFT')
                        ->join('st_goods_detail gd ON od.gid = gd.gid','LEFT')
                        ->field('od.*,g.name,gd.desc')
                        ->select();
        $order = D('shop_order')->where(['order_id'=>$orderid])->find();
        $where['status'] = 0;
        $data = $address->where($where)->find();

        if(!empty($_SESSION['order']['orderid'])){
            if(!empty($data)){
                $addressWhere = ['order_id' => $_SESSION['order']['orderid']];
                $addressInfo = [
                    'addressinfo'=>json_encode($data),
                    'address_id'=>$data['id'],
                    'address'=>$data['province'].$data['city'].$data['area'].$data['address']
                ];
                M('ShopOrder')->where($addressWhere)->save($addressInfo);
            }
        }
        //查询商品对应的快递运费信息
        foreach ($OrderDetail as $key => $value) {
            // echo $value."<br>";
            // $OrderDetail[$key]['path'] = D('pic')->where(['gid'=>$value['gid']])->find()['path'];
            $goodsCourier[$value['gid']] = M('goods_courier')->where('gid='.$value['gid'])->field('gid,cid,cname,cprice')->select();
        }
        // p($OrderDetail);
        $rate = D('website')->field('gold_rate,silver_rate')->find();
        $showUser['open_id'] = $_SESSION['open_id'];
        // // 执行查询
        $user = M('users')->where($showUser)->find();
        $assign = [
            'data' => json_encode($data),
            'goodsCourier' => json_encode($goodsCourier),
            'gtype' => json_encode($order['g_type']),
            'user' => json_encode($user),
            'rate' => json_encode($rate)
        ];

        // dump($assign);
        $this->wx_info();
        $this->assign($assign);
        $this->display();
    }

    /**
     * [getGoodsByOrder 订单详情信息]
     * @return [type] [description]
     */
    public function getGoodsByOrder(){

        $where['uid'] = session('user.id');

        //根据订单id查询出该订单下有多少商品
        
        if (!empty($_GET['order_id'])) {
            $_SESSION['order']['orderid'] = $_GET['order_id'];
        }
        

        $orderid = $_SESSION['order']['orderid'];

        $OrderDetail = M('ShopOrderDetail')
                        ->alias('od')
                        ->where('order_id='.$orderid)
                        ->join('st_goods g ON od.gid = g.id','LEFT')
                        ->join('st_goods_detail gd ON od.gid = gd.gid','LEFT')
                        ->field('g.name,g.gpic,gd.*,od.gsku,od.num,od.price')
                        ->select();
        // $order = D('shop_order')->where(['order_id'=>$orderid])->find();

        //查询商品对应的快递运费信息
        foreach ($OrderDetail as $key => $value) {
            // echo $value."<br>";
            // $OrderDetail[$key]['path'] = D('pic')->where(['gid'=>$value['gid']])->find()['path'];
            $OrderDetail[$key]['Courier'] = M('goods_courier')->where('gid='.$value['gid'])->field('gid,cid,cname,cprice')->select();
        }
        // p($OrderDetail);
         return $this->ajaxReturn(array('code'=>200,'msg'=>$OrderDetail));
    }

        /**
     * 滤芯 确认支付
     * @return [type] [description]
     */
    public function lvxinPay()
    {
        $address = D('Address');
        $where['uid'] = session('user.id');

        //根据订单id查询出该订单下有多少商品
        
        if (!empty($_GET['order_id'])) {
            $_SESSION['order']['orderid'] = $_GET['order_id'];
        }
        

        $orderid = $_SESSION['order']['orderid'];

        $gids = M('ShopOrderDetail')->where('order_id='.$orderid)->field('gid')->select();

        $where['status'] = 0;
        $data = $address->where($where)->find();
        $rate = D('website')->field('gold_rate,silver_rate')->find();
        $showUser['open_id'] = $_SESSION['open_id'];
        // // 执行查询
        $user = M('users')->where($showUser)->find(); 
        $assign = [
            'data' => json_encode($data),
            'user' => json_encode($user),
            'rate' => json_encode($rate)
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

        $orderId = $_POST['postage']['order_id'];

        // echo $orderId;die;
        $gid = $_POST['postage']['gid'];
        $data['cid'] = $_POST['postage']['cid'];
        $data['cprice'] = $_POST['postage']['cprice'];
        $data['cname'] = $_POST['postage']['cname'];
        
        //更改订单快递信息
        $so = M('ShopOrderDetail');

        $info = $so->where('order_id='.$orderId.' AND gid='.$gid)->save($data);

        if ($info) {
            $this->ajaxReturn('快递方式选择成功');
        } else {
            $this->ajaxReturn('快递方式选择失败');

        }


    }

    /**
     * [invoiceAdd 将发票信息写入订单]
     * @return [type] [description]
     */
    public function invoiceAdd()
    {
        //发票相关信息
        $data['oid'] = $_POST['order_id'];
        $data['voicehead'] = $_POST['voiceArr']['voicehead'];
        $data['associated'] = $_POST['voiceArr']['associated'];
        $data['secpAddr'] = $_POST['voiceArr']['secpAddr'];
        $data['phone'] = $_POST['voiceArr']['phone'];
        $data['email'] = $_POST['voiceArr']['mail'];

        //发票是个人还是公司的
        switch ($_POST['voiceArr']['info']) {
            case '公司':
                $data['info'] = 2;
                break;
            case '个人':
                $data['info'] = 1;
                break;
        }

        //电子发票还是纸质发票
        switch ($_POST['voiceArr']['type']) {
            case '电子发票':
                $data['is_electronic'] = 2;
                break;
            case '纸质发票':
                $data['is_electronic'] = 3;
                break;
            
            default:
                $data['is_electronic'] = 1;
                break;
        }

        //将订单发票信息写入数据库
        $invoice = M('invoice');
        // dump($data);die;
        //写入之前先判断是否存在该订单下是否有发票信息
        $info = $invoice->where('oid='.$_POST['order_id'])->find();
       

        if ($info) {
            //已存在则修改信息
            if ($invoice->where('oid='.$_POST['order_id'])->save($data)) {
                $this->ajaxReturn('发票信息修改成功');
            } else {
                $this->ajaxReturn('发票信息修改成功');
            }

        } else {
            //不存在则写入
            if ($invoice->add($data)) {
                $this->ajaxReturn('发票信息写入成功');
            } else {
                $this->ajaxReturn('发票信息写入失败');
            }

        }
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
        $grade = M('users')->where($map)->field('id,grade,end_time')->find();


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
            default:
                $grade['grade'] = '3';
        }
        //查找最后一条用户充值会员的记录
        $orderData = M('users_order')->where(['user_id'=>$grade['id'],'is_pay'=>1])->field('id,is_pay,order_id,user_id,annual_status,name')->order('id desc')->find();

        if ($orderData) {

            $annual_money = M('annual')->find();

            //证明是续费
//            $type_info = M('users_order')->where(['user_id'=>$grade['id'],'is_pay'=>1])->save(['type'=>2]);
            $type_info = M('users_order')->where(['user_id'=>$grade['id'],'is_pay'=>1])->save(['type'=>2,'update_time'=>time()]);

            // dump($type_info);exit;
            if ($type_info) {

                switch ($orderData['annual_status']) {
                    case '1':
                        $pp = [
                            'status'=>1
                        ];
                        break;
                    case '2':

                        $data['annual_ey'] = $annual_money['annual_money']/365;
                        $data['medal_ey'] = $annual_money['medal_money']/365;
                        //计算出当前会员剩余天数
                        $time = intval(($grade['end_time']-time())/86400);
                        //钻石会员剩下的钱
                        $annual_money= (round($data['annual_ey'], 2)-round($data['medal_ey'], 2)) * $time;
                        $pp = [
                            'annual_money'=>$annual_money,
                            'dia_describe' => $annual_money['dia_describe'],
                            'status' => 2
                        ];

                        break;
                    case '3':

                        //个人会员继续省钱 所补交的费用
                        $data['annual_ey'] = $annual_money['annual_money']/365;
                        $data['medal_ey'] = $annual_money['medal_money']/365;
                        $data['mo_ey'] = $annual_money['personal_money']/365;

                        //计算出当前会员剩余天数
                        $time = intval(($grade['end_time']-time())/86400);


                        //钻石会员剩下的钱
                        $annual_money= (round($data['annual_ey'], 2)-round($data['mo_ey'], 2)) * $time;

                        //黄金会员剩下的钱
                        $medal_money= (round($data['medal_ey'], 2)-round($data['mo_ey'], 2)) * $time;
                        $pp = [
                            'annual_money'=>$annual_money,
                            'medal_money'=>$medal_money,
                            'dia_describe' => $annual_money['dia_describe'],
                            'gold_describe'=>$annual_money['gold_describe'],
                            'status' => 3
                        ];
                        break;
                }

                $this->assign('grade',$grade);
                $this->assign('annual',$pp);
                $this->display();
            } else {
                $annual = M('annual')->find();
                $annual['status'] = 0;
                $this->assign('grade',$grade);
                $this->assign('annual',$annual);
                $this->display();
            }



        } else {

            $annual = M('annual')->find();
            $annual['status'] = 0;
            $this->assign('grade',$grade);
            $this->assign('annual',$annual);
            $this->display();

        }

//        if ($grade['grade'] > 1) {
//            $annual_money = M('annual')->find();
//            $orderData = M('users_order')->where(['user_id'=>$grade['id'],'annual_status'=>$grade['grade']])->field('is_pay,order_id,user_id,annual_status,name')->find();
//
//            switch ($orderData['annual_status']) {
//                case '1':
//                    $data['price'] = $annual_money['annual_money']-$annual_money['cost_money'];
//                    //每天的会员费
//                    $data['mon_ey'] = $annual_money['annual_money']/365;
//                    $data['grade'] = 4;
//                    break;
//                case '2':
//                    $data['price'] = $annual_money['medal_money']-$annual_money['gold_money'];
//                    $data['mon_ey'] = $annual_money['medal_money']/365;
//                    $data['grade'] = 3;
//                    break;
//                case '3':
//                    $data['price'] = $annual_money['personal_money']-$annual_money['per_money'];
//                    $data['mon_ey'] = $annual_money['personal_money']/365;
//                    $data['grade'] = 2;
//                    break;
//            }
//            switch ($grade['grade']) {
//                case '4':
//                    //每天的会员费
//                    $data['mo_ey'] = $annual_money['annual_money']/365;
//                    break;
//                case '3':
//                    $data['mo_ey'] = $annual_money['medal_money']/365;
//                    break;
//                case '2':
//                    $data['mo_ey'] = $annual_money['personal_money']/365;
//                    break;
//            }
//            //计算出当前会员剩余天数
//            $time = intval(($grade['end_time']-time())/86400);
//            $money= (round($data['mon_ey'], 2)-round($data['mo_ey'], 2)) * $time ;
//            dump($money);
//        }
//        $annual = M('annual')->find();
//
//        $this->assign('grade',$grade);
//        $this->assign('annual',$annual);
//        $this->display();
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
        $this->assign('res',$res);
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