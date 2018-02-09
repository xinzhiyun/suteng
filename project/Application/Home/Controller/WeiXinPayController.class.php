<?php
namespace Home\Controller;
use \Org\Util\WeixinJssdk;
use Think\Controller;

class WeiXinPayController extends Controller
{

    /**
     * 分销商交加盟费接口
     */
    public function vendor_fee(){
        // 准备查询条件
        $showData['id'] = 1;
        // 查询分销商加盟费
        $vendor_fee = M('vendor_fee')->where($showData)->field('vendor_a,vendor_b,vendor_c')->find();

        // 匹配分销商级别
        switch ($_SESSION['vendorInfo']['leavel']) {
            case '2':
                // A级分销商
                $money = $vendor_fee['vendor_a'];
                break;
            case '3':
                // B级分销商
                $money = $vendor_fee['vendor_b'];
            case '4':
                // C级分销商
                $money = $vendor_fee['vendor_c'];
                break;
            default:
                # code...
                break;
        }

        // 描述
        $content = '速腾分销商加盟费支付';
        // 分销商标识
        $code = $money;
        // 下单
        $this->uniformOrderVendor($money,$code,$content);
    }
    
    /**
     * 统一收分销商加盟费并返回数据
     * @return string json格式的数据，可以直接用于js支付接口的调用
     * @param  [type] $money    [订单金额]
     * @param  [type] $order_id [订单号码]
     * @param  [type] $content  [订单详情]
     */
    public function uniformOrderVendor($money,$code,$content)
    {
        // dump($_SESSION);die;
        // $content = substr($content,0,80);
        // 将金额强转换整数
        $money = $money * 100;
        // 冲值测试额1分钱
        $money = 1;
        // 用户在公众号的唯一ID
        $openId = $_SESSION['vendorInfo']['open_id'];


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
        $input->SetAttach($code);
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
        $input->SetNotify_url('http://'.$_SERVER['SERVER_NAME'].U('Home/WeiXinPay/vendorNotify'));
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
     * 处理订单写入数据
     * @return array 返回数组格式的notify数据
     */
    public function notify()
    {
        // 获取微信服务器返回的xml文档
        $xml=file_get_contents('php://input', 'r');       
//         $xml = '<xml><appid><![CDATA[wx0bab2f4b5b7ec3b5]]></appid>
// <attach><![CDATA[561131965313806]]></attach>
// <bank_type><![CDATA[CFT]]></bank_type>
// <cash_fee><![CDATA[1]]></cash_fee>
// <fee_type><![CDATA[CNY]]></fee_type>
// <is_subscribe><![CDATA[Y]]></is_subscribe>
// <mch_id><![CDATA[1490274062]]></mch_id>
// <nonce_str><![CDATA[2332uho7x16aiz10cg13ffkz5fwdrayg]]></nonce_str>
// <openid><![CDATA[oQktJwL8ioR4DoxSQmikdzekbUyU]]></openid>
// <out_trade_no><![CDATA[478056852556374]]></out_trade_no>
// <result_code><![CDATA[SUCCESS]]></result_code>
// <return_code><![CDATA[SUCCESS]]></return_code>
// <sign><![CDATA[A684A3F06136116FDD304A338A606E58]]></sign>
// <time_end><![CDATA[20180201023601]]></time_end>
// <total_fee>1</total_fee>
// <trade_type><![CDATA[JSAPI]]></trade_type>
// <transaction_id><![CDATA[4200000071201802014605968829]]></transaction_id>
// </xml>';
// 
// 
// UR体会与人体热敷的供热的高
        // file_put_contents('./wx_dddpay.txt',$xml."\r\n", FILE_APPEND);die;
        if($xml){
            //解析微信返回数据数组格式
            $result = $this->notifyData($xml);
            // dump($result);die;
            // 实例化订单模型
            $order = M('shop_order');
            // 准备查询订单的条件
            $showOrder['order_id'] = $result['attach'];
            // 查询订单表
            $orderData = $order->where($showOrder)->find();
            
            // 如果订单未支付
            if($orderData['status']==8){
                // 开启事务
                // $order->startTrans();
                // dump();die;
                $saveOrder['order_id']  = $orderData['order_id'];
                // 用户ID
                $showAddres['uid']      = $orderData['uid'];
                // 默认的地址
                $showAddres['status']   = 0;
                // 快递地址ID
                $saveOrderDara['address_id'] = M('address')->where($showAddres)->find()['id'];
                // 准备更新数据
                $saveOrderDara['status'] = 9;
                // 支付类型
                $saveOrderDara['mode'] = 0;
                // 执行更新操作
                $order->where($saveOrder)->save($saveOrderDara);
                // 准备佣金数据
                // 微信用户标识
                $open_id = $result['openid'];
                // 订单编号
                $order_id = $result['attach'];

                // 总成本
                $cost = $orderData['g_cost'];
                // 总售价
                $price = $orderData['g_price'];
                // 总利润
                $profit = $price - $cost;
                // 佣金比例
                $yjbl = 0.03;
                // 金币比例
                $jbbl = 0.01;
                // 银币比例
                $ybbl = 0.02;

                // 佣金
                $yj = ($profit*$yjbl)>0?($profit*$yjbl):0;
                // 金币
                $jb = ($profit*$jbbl)>0?($profit*$jbbl):0;
                // 银币
                $yb = ($profit*$ybbl)>0?($profit*$ybbl):0;

                // echo $open_id.'-'.$yj.'-'.$jb.'-'.$yb;die;
                // 分配佣金
                $this->branch_commission($open_id,$order_id,$yj,$jb,$yb);
            }
        }
    }

    /**
     * [vendorNotify 分销商加盟费回调]
     * @return [type] [description]
     */
    public function vendorNotify(){
        // 接收微信支付回调
        $xml=file_get_contents('php://input', 'r');
        // 写文件测试
        // file_put_contents('./wx_payFee.txt',$xml."\r\n", FILE_APPEND);die;

//         $xml = '<xml><appid><![CDATA[wx0bab2f4b5b7ec3b5]]></appid>
// <attach><![CDATA[10000.00]]></attach>
// <bank_type><![CDATA[CFT]]></bank_type>
// <cash_fee><![CDATA[1]]></cash_fee>
// <fee_type><![CDATA[CNY]]></fee_type>
// <is_subscribe><![CDATA[Y]]></is_subscribe>
// <mch_id><![CDATA[1490274062]]></mch_id>
// <nonce_str><![CDATA[7zdvpsqu62x2t0gdf4u5g6mvcjpa651l]]></nonce_str>
// <openid><![CDATA[oQktJwL8ioR4DoxSQmikdzekbUyU]]></openid>
// <out_trade_no><![CDATA[389282621064792]]></out_trade_no>
// <result_code><![CDATA[SUCCESS]]></result_code>
// <return_code><![CDATA[SUCCESS]]></return_code>
// <sign><![CDATA[5A964113D173D6C037E4685ABA9E53C8]]></sign>
// <time_end><![CDATA[20180206142758]]></time_end>
// <total_fee>1</total_fee>
// <trade_type><![CDATA[JSAPI]]></trade_type>
// <transaction_id><![CDATA[4200000073201802068049959066]]></transaction_id>
// </xml>';

        if($xml){
            //解析微信返回数据数组格式
            $result = $this->notifyData($xml);
            // 分销商openid
            $showData['open_id']      = $result['openid'];
            // 加盟费
            $saveData['fee']          = $result['attach'];
            // 状态
            $saveData['status']       = 3;
            // 更新时间
            $saveData['updatetime  '] = time();
            // 执行更新操作
            $saveRes = M('vendors')->where($showData)->save($saveData);

            if($saveRes){
                // 分红
                // dump($result);die;
            }
 
  
        }
    }



    /**
     * 处理充值写入数据
     * @return array 返回数组格式的notify数据
     */
    public function congzhiNotify()
    {
        // echo 'http://'.$_SERVER['SERVER_NAME'].U('Home/WeiXinPay/congzhiNotify');
        // 实例化微信JSSDK类对象
        //$wxJSSDK = new \Org\Util\WeixinJssdk;
        // 獲取微信微信ID
        //$openId = $wxJSSDK->GetOpenid();
        // 查询用户ID
        //$uid = M('Users')->where("open_id='{$openId}'")->find()['id'];

        // 获取微信服务器返回的xml文档
        $xml=file_get_contents('php://input', 'r');
        // file_put_contents('./wx_pay1.txt',$xml."\r\n", FILE_APPEND);die;
        //echo 1;die;
// 金币模拟数据
//         $xml = '<xml><appid><![CDATA[wx0bab2f4b5b7ec3b5]]></appid>
// <attach><![CDATA[17,gold]]></attach>
// <bank_type><![CDATA[CFT]]></bank_type>
// <cash_fee><![CDATA[1]]></cash_fee>
// <fee_type><![CDATA[CNY]]></fee_type>
// <is_subscribe><![CDATA[Y]]></is_subscribe>
// <mch_id><![CDATA[1490274062]]></mch_id>
// <nonce_str><![CDATA[lfrkw9gmvr9lh35r39kserjfq0gk0qvp]]></nonce_str>
// <openid><![CDATA[oQktJwL8ioR4DoxSQmikdzekbUyU]]></openid>
// <out_trade_no><![CDATA[274960238018351]]></out_trade_no>
// <result_code><![CDATA[SUCCESS]]></result_code>
// <return_code><![CDATA[SUCCESS]]></return_code>
// <sign><![CDATA[F5A2A01E2FFD4223E073137AF62E8DA4]]></sign>
// <time_end><![CDATA[20180205191926]]></time_end>
// <total_fee>1</total_fee>
// <trade_type><![CDATA[JSAPI]]></trade_type>
// <transaction_id><![CDATA[4200000071201802057648810477]]></transaction_id>
// </xml>';
        

//             // [detail] => gold
//             // [body] => 100元充值100个金币
//             // [attach] => 16
//             // [out_trade_no] => 685361645248128
//             // [total_fee] => 1
//             // [notify_url] => http://test.dianqiukj.com/index.php/Home/WeiXinPay/congzhiNotify.html
//             // [trade_type] => JSAPI
//             // [openid] => oQktJwL8ioR4DoxSQmikdzekbUyU
        if($xml){
            //解析微信返回数据数组格式
            $result = $this->notifyData($xml);
            // dump($result);die;
            $showFlow['order_id'] = $result['out_trade_no'];
            $flowData = M('flow')->where($showFlow)->find();

            if(empty($flowData)){
                // 匹配充值套餐类型
                $wxData = explode(",",$result['attach']);
                // 准备查询条件
                $showData['id'] = $wxData[0];
                // 获取查询的表
                $table = $wxData[1];
                // 执行查询
                $flowData = M($table)->where($showData)->find();

                // 查询用户
                $showUser['open_id'] = $result['openid'];
                // 查询用户
                $user = M('users')->where($showUser)->find();
                // 微信标识
                $addData['order_id'] = $result['openid'];
                // 关联用户ID
                $addData['user_id'] = $user['id'];
                // 订单编号
                $addData['order_id'] = $result['out_trade_no'];
                // 订单金额
                $addData['money']   = $flowData['money'];

                switch ($table) {
                    case 'gold':
                        // 金币数量 
                        $addData['gold_num'] = $flowData['gold_num'];
                        // 当前金币
                        $addData['current_gold_num'] = $saveData['gold_num'] = $flowData['gold_num'] + $user['gold_num'];
                        // 当前银币
                        $addData['current_silver'] = $user['silver'];
                        break;
                    case 'silver':
                        // 银币数量 
                        $addData['silver_num'] = $flowData['silver_num'];
                        // 当前银币
                        $addData['current_silver'] = $saveData['silver'] = $flowData['silver_num'] + $user['silver'];
                        // 当前金币
                        $addData['current_gold_num'] = $user['gold_num'];
                        break;
                    default:
                        # code...
                        break;
                }

                // 累计充值金额消费的金额
                $saveData['total_money'] = (($user['total_money']*100) + ($flowData['money']*100))/100;
                // 如果用户是标准类型
                if($user['grade']== 3){
                    // 查询用户升级条件
                    $user_upgrade =  M('config')->where('id=1')->field('user_upgrade')->find()['user_upgrade'];
                    if($saveData['total_money']>=$user_upgrade){
                        // 升级钻石会员
                        $saveData['grade'] = 4;
                    }
                }

                // 支付类型
                $addData['mode']    = 2;
                // 支付描述
                $addData['describe'] = $flowData['content'];
                // 创建时间和更新时间
                $addData['addtime'] = $addData['updatetime'] =  time();
                // 写充值流水
                $addRes = M('flow')->add($addData);
                // 更新用户账户
                $saveRes = M('users')->where($showUser)->save($saveData);
            }
        }
    }

    /**
     * 验证服务器返回支付成功订单
     * @return array 返回数组格式的notify数据
     */
    public function notifyData($xml)
    {
        // 获取微信服务器返回的xml文档
        // $xml=file_get_contents('php://input', 'r');
        // file_put_contents('./wx_notify.txt',$xml, FILE_APPEND);

        // 转成php数组
        $data=$this->toArray($xml);
        // return 1;
        // file_put_contents('./wx_notify1.txt','data:'.$data, FILE_APPEND);    
        // file_put_contents('./wx_notify2.txt','123:'.$data['out_trade_no'], FILE_APPEND);
        // file_put_contents('./wx_notify3.txt','456:'.$data['sign'], FILE_APPEND); 

        // 保存原sign
        $dataSign=$data['sign'];

        // sign不参与签名
        unset($data['sign']);

        // 生成签名
        $sign=$this->makeSign($data);
        // file_put_contents('./wx_notify.txt','原签: '.$dataSign.'现签：'.$sign, FILE_APPEND);  
        // 判断签名是否正确  判断支付状态
        if ($sign==$dataSign && $data['return_code']=='SUCCESS') {

            // 返回状态给微信服务器
            echo '<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>';

            // 返回数据给回调函数进行插入操作
            return $data;
        }else{
            // 签名错误 或 支付未成功 
            $result=false;
        }

        
    }

    /**
     * 将xml转为array
     * @param  string $xml xml字符串
     * @return array       转换得到的数组
     */
    public function toArray($xml){   
        //禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        $result= json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);        
        return $result;
    }

    /**
     * 生成签名
     * @return 签名，本函数不覆盖sign成员变量，如要设置签名需要调用SetSign方法赋值
     */
    public function makeSign($data){
        // 去空
        $data=array_filter($data);
        //签名步骤一：按字典序排序参数
        ksort($data);
        $string_a=http_build_query($data);
        $string_a=urldecode($string_a);
        //签名步骤二：在string后加入KEY
        $config=$this->config;
        $string_sign_temp=$string_a."&key=".C('KEY');
        //签名步骤三：MD5加密
        $sign = md5($string_sign_temp);
        // 签名步骤四：所有字符转为大写
        $result=strtoupper($sign);
        return $result;
    }


    /**
     * [branch_commission description]
     * @param  [type] $open_id  [用户微信唯一标识]
     * @param  [type] $order_id [订单号]
     * @param  [type] $yj       [佣金总额]
     * @param  [type] $jb       [金币总额]
     * @param  [type] $yb       [银币总额]
     */
    public function branch_commission($open_id,$order_id,$yj,$jb,$yb)
    {
        // 准备查询条件
        $showData['id'] = 1;
        // 查询佣金分配比例
        $commission = M('commission')->where($showData)->find();
        // 准备用户查询条件
        $showUserData['open_id'] = $open_id;
        // 查询用户信息
        $user = M('users')->where($showUserData)->find();

        // 会员邀请人
        $invitation_code    =  $user['invitation_code'];
        // A级分销商
        $vendora_code       =  $user['vendora_code'];
        // B级分销商
        $vendorb_code       =  $user['vendorb_code'];
        // C级分销商
        $vendorc_code       =  $user['vendorc_code'];
        // 分销商邀请人
        $vendori_code       =  $user['vendori_code'];
        // 邀请人类型
        $invite             =  $user['invite'];

        switch ($invite) {
            case '0':
                // 分公司邀请的会员
                // 会员获得金币和银币
                $this->user_commission($user,$commission,$order_id,$jb,$yb);
                break;
            case '1':
                // A级分销商邀请的会员
                // 会员获得金币和银币
                $this->user_commission($user,$commission,$order_id,$jb,$yb);
                // 准备A级分销商查询条件
                $vendoraWhere['code'] = $user['vendora_code'];
                // 查询A级分销商
                $vendora = M('vendors')->where($vendoraWhere)->find();

                // 佣金比例
                $ratioa = 100;
                // 获得分红的分销商关系人
                $nexus_vendor = $vendora['code'];

                // 如果有分销商邀请人按分成给邀请人分部分佣金
                if(!empty($vendora['invitation_code'])){
                    // 准备分销商邀请人查询条件
                    $vendoriWhere['code'] = $vendora['invitation_code'];
                    // 查询分销商邀请人
                    $vendori = M('vendors')->where($vendoriWhere)->find();
                    // 分销商邀请人佣金比例
                    $ratioi  = $commission['vendor_i'];
                    // 获取佣金的类型[同级邀请的分销商下的会员购买产品]
                    $typei = 3;
                    // 修改A级分销佣金比例
                    $ratioa = $ratioa - $ratioi;
                    // 分配分销商邀请人佣金
                    $this->vendor_commission($vendori,$typei,$vendora,$user,$ratioi,$order_id,$yj);
                }
                // A级分销商获得分红的类型
                $typea = 0;
                // A级分销商获得其余全部佣金
                $this->vendor_commission($vendora,$typea,$vendora,$user,$ratioa,$order_id,$yj);
                break;
            case '2':
                // B级分销商邀请的会员
                // 会员获得金币和银币
                $this->user_commission($user,$commission,$order_id,$jb,$yb);
                // 准备A级分销商查询条件
                $vendorbWhere['code'] = $user['vendorb_code'];
                // 查询A级分销商
                $vendorb = M('vendors')->where($vendorbWhere)->find();

                // 佣金比例
                $ratiob = 100;
                // 获得分红的分销商关系人
                $nexus_vendor = $vendorb['code'];

                // 如果有分销商邀请人按分成给邀请人分部分佣金
                if(!empty($vendorb['invitation_code'])){
                    // 准备分销商邀请人查询条件
                    $vendoriWhere['code'] = $vendorb['invitation_code'];
                    // 查询分销商邀请人
                    $vendori = M('vendors')->where($vendoriWhere)->find();

                    // 分销商邀请人佣金比例
                    $ratioi  = $commission['vendor_i'];
                    // 获取佣金的类型[同级邀请的分销商下的会员购买产品]
                    $typei = 3;
                    // 修改A级分销佣金比例
                    $ratiob = $ratiob - $ratioi;

                    // 分配分销商邀请人佣金
                    $this->vendor_commission($vendori,$typei,$vendorb,$user,$ratioi,$order_id,$yj);
                }

                // 如果有上面分销商A级分销商
                if(!empty($vendorb['superior_code'])){
                    // 准备分销商邀请人查询条件
                    $vendoraWhere['code'] = $vendorb['superior_code'];
                    // 查询分销商邀请人
                    $vendora = M('vendors')->where($vendoraWhere)->find();
                    // 分销商邀请人佣金比例
                    $ratioa  = $commission['vendor_a'];
                    // 获取佣金的类型[下分销商下的会员购买产品]
                    $typea = 1;
                    // 修改A级分销佣金比例
                    $ratiob = $ratiob - $ratioa;
                    // 分配分销商邀请人佣金
                    $this->vendor_commission($vendora,$typea,$vendorb,$user,$ratioa,$order_id,$yj);
                    // dump($ratioa);die;
                }

                // B级分销商获得分红的类型
                $typeb = 0;
                // B级分销商获得其余全部佣金
                $this->vendor_commission($vendorb,$typeb,$vendorb,$user,$ratiob,$order_id,$yj);

                break;
            case '3':
                // C级分销商邀请的会员
                // 会员获得金币和银币
                $this->user_commission($user,$commission,$order_id,$jb,$yb);
                // 准备A级分销商查询条件
                $vendorcWhere['code'] = $user['vendorc_code'];
                // 查询A级分销商
                $vendorc = M('vendors')->where($vendorcWhere)->find();

                // 佣金比例
                $ratioc = 100;
                // 获得分红的分销商关系人
                $nexus_vendor = $vendorc['code'];

                // 如果有分销商邀请人按分成给邀请人分部分佣金
                if(!empty($vendorc['invitation_code'])){
                    // 准备分销商邀请人查询条件
                    $vendoriWhere['code'] = $vendorc['invitation_code'];
                    // 查询分销商邀请人
                    $vendori = M('vendors')->where($vendoriWhere)->find();

                    // 分销商邀请人佣金比例
                    $ratioi  = $commission['vendor_i'];
                    // 获取佣金的类型[同级邀请的分销商下的会员购买产品]
                    $typei = 3;
                    // 修改C级分销佣金比例
                    $ratioc = $ratioc - $ratioi;

                    // 分配分销商邀请人佣金
                    $this->vendor_commission($vendori,$typei,$vendorc,$user,$ratioi,$order_id,$yj);
                }

                // 如果有上面分销商A级分销商
                if(!empty($vendorc['superiors_code'])){
                    // 准备分销商邀请人查询条件
                    $vendoraWhere['code'] = $vendorc['superiors_code'];
                    // 查询分销商邀请人
                    $vendora = M('vendors')->where($vendoraWhere)->find();
                    // 分销商邀请人佣金比例
                    $ratioa  = $commission['vendor_a'];
                    // 获取佣金的类型[下下分销商下的会员购买产品]
                    $typea = 2;
                    // 修改A级分销佣金比例
                    $ratioc = $ratioc - $ratioa;
                    // 分配分销商邀请人佣金
                    $this->vendor_commission($vendora,$typea,$vendorc,$user,$ratioa,$order_id,$yj);
                    // dump($ratioa);die;
                }

                // 如果有上面分销商B级分销商
                if(!empty($vendorc['superior_code'])){
                    // 准备分销商邀请人查询条件
                    $vendorbWhere['code'] = $vendorc['superior_code'];
                    // 查询分销商邀请人
                    $vendorb = M('vendors')->where($vendorbWhere)->find();
                    // 分销商邀请人佣金比例
                    $ratiob  = $commission['vendor_b'];
                    // 获取佣金的类型[下分销商下的会员购买产品]
                    $typea = 1;
                    // 修改A级分销佣金比例
                    $ratioc = $ratioc - $ratiob;
                    // 分配分销商邀请人佣金
                    $this->vendor_commission($vendorb,$typea,$vendorc,$user,$ratiob,$order_id,$yj);
                    // dump($ratioa);die;
                }
                // C级分销商获得分红的类型
                $typec = 0;
                // C级分销商获得其余全部佣金
                $this->vendor_commission($vendorc,$typec,$vendorc,$user,$ratioc,$order_id,$yj);
                break;
            case '4':
                // 会员邀请的会员
                // 会员获得金币和银币
                $this->user_commission($user,$commission,$order_id,$jb,$yb);
                // 准备查询会员邀请的人查询条件
                $showinvitationData['code'] = $user['invitation_code'];
                // 查询用户信息
                $invitation = M('users')->where($showinvitationData)->find();
                // 会员邀请人获得金币和银币
                $this->invitation_commission($invitation,$user,$commission,$order_id,$jb,$yb);
                // 获取邀请人的邀请人级别
                $invitationInvite = $invitation['invite'];
                // 匹配分配商佣金分配
                switch ($invitationInvite) {
                    case '1':
                        // A级分销商下的会员邀请会员
                        // 准备A级分销商查询条件
                        $vendoraWhere['code'] = $user['vendora_code'];
                        // 查询A级分销商
                        $vendora = M('vendors')->where($vendoraWhere)->find();

                        // 佣金比例
                        $ratioa = 100;
                        // 获得分红的分销商关系人
                        $nexus_vendor = $vendora['code'];

                        // 如果有分销商邀请人按分成给邀请人分部分佣金
                        if(!empty($vendora['invitation_code'])){
                            // 准备分销商邀请人查询条件
                            $vendoriWhere['code'] = $vendora['invitation_code'];
                            // 查询分销商邀请人
                            $vendori = M('vendors')->where($vendoriWhere)->find();
                            // 分销商邀请人佣金比例
                            $ratioi  = $commission['vendor_i'];
                            // 获取佣金的类型[同级邀请的分销商下的会员购买产品]
                            $typei = 3;
                            // 修改A级分销佣金比例
                            $ratioa = $ratioa - $ratioi;
                            // 分配分销商邀请人佣金
                            $this->vendor_commission($vendori,$typei,$vendora,$user,$ratioi,$order_id,$yj);
                        }
                        // A级分销商获得分红的类型
                        $typea = 0;
                        // A级分销商获得其余全部佣金
                        $this->vendor_commission($vendora,$typea,$vendora,$user,$ratioa,$order_id,$yj);
                        break;
                    case '2':
                        // B级分销商下的会员邀请会员
                        // 准备B级分销商查询条件
                        $vendorbWhere['code'] = $user['vendorb_code'];
                        // 查询A级分销商
                        $vendorb = M('vendors')->where($vendorbWhere)->find();

                        // 佣金比例
                        $ratiob = 100;
                        // 获得分红的分销商关系人
                        $nexus_vendor = $vendorb['code'];

                        // 如果有分销商邀请人按分成给邀请人分部分佣金
                        if(!empty($vendorb['invitation_code'])){
                            // 准备分销商邀请人查询条件
                            $vendoriWhere['code'] = $vendorb['invitation_code'];
                            // 查询分销商邀请人
                            $vendori = M('vendors')->where($vendoriWhere)->find();

                            // 分销商邀请人佣金比例
                            $ratioi  = $commission['vendor_i'];
                            // 获取佣金的类型[同级邀请的分销商下的会员购买产品]
                            $typei = 3;
                            // 修改A级分销佣金比例
                            $ratiob = $ratiob - $ratioi;

                            // 分配分销商邀请人佣金
                            $this->vendor_commission($vendori,$typei,$vendorb,$user,$ratioi,$order_id,$yj);
                        }

                        // 如果有上面分销商A级分销商
                        if(!empty($vendorb['superior_code'])){
                            // 准备分销商邀请人查询条件
                            $vendoraWhere['code'] = $vendorb['superior_code'];
                            // 查询分销商邀请人
                            $vendora = M('vendors')->where($vendoraWhere)->find();
                            // 分销商邀请人佣金比例
                            $ratioa  = $commission['vendor_a'];
                            // 获取佣金的类型[下分销商下的会员购买产品]
                            $typea = 1;
                            // 修改A级分销佣金比例
                            $ratiob = $ratiob - $ratioa;
                            // 分配分销商邀请人佣金
                            $this->vendor_commission($vendora,$typea,$vendorb,$user,$ratioa,$order_id,$yj);
                            // dump($ratioa);die;
                        }

                        // B级分销商获得分红的类型
                        $typeb = 0;
                        // B级分销商获得其余全部佣金
                        $this->vendor_commission($vendorb,$typeb,$vendorb,$user,$ratiob,$order_id,$yj);
                        break;
                    case '3':
                        // C级分销商下的会员邀请会员
                        $vendorcWhere['code'] = $user['vendorc_code'];
                        // 查询A级分销商
                        $vendorc = M('vendors')->where($vendorcWhere)->find();

                        // 佣金比例
                        $ratioc = 100;
                        // 获得分红的分销商关系人
                        $nexus_vendor = $vendorc['code'];

                        // 如果有分销商邀请人按分成给邀请人分部分佣金
                        if(!empty($vendorc['invitation_code'])){
                            // 准备分销商邀请人查询条件
                            $vendoriWhere['code'] = $vendorc['invitation_code'];
                            // 查询分销商邀请人
                            $vendori = M('vendors')->where($vendoriWhere)->find();

                            // 分销商邀请人佣金比例
                            $ratioi  = $commission['vendor_i'];
                            // 获取佣金的类型[同级邀请的分销商下的会员购买产品]
                            $typei = 3;
                            // 修改C级分销佣金比例
                            $ratioc = $ratioc - $ratioi;

                            // 分配分销商邀请人佣金
                            $this->vendor_commission($vendori,$typei,$vendorc,$user,$ratioi,$order_id,$yj);
                        }

                        // 如果有上面分销商A级分销商
                        if(!empty($vendorc['superiors_code'])){
                            // 准备分销商邀请人查询条件
                            $vendoraWhere['code'] = $vendorc['superiors_code'];
                            // 查询分销商邀请人
                            $vendora = M('vendors')->where($vendoraWhere)->find();
                            // 分销商邀请人佣金比例
                            $ratioa  = $commission['vendor_a'];
                            // 获取佣金的类型[下下分销商下的会员购买产品]
                            $typea = 2;
                            // 修改A级分销佣金比例
                            $ratioc = $ratioc - $ratioa;
                            // 分配分销商邀请人佣金
                            $this->vendor_commission($vendora,$typea,$vendorc,$user,$ratioa,$order_id,$yj);
                            // dump($ratioa);die;
                        }

                        // 如果有上面分销商B级分销商
                        if(!empty($vendorc['superior_code'])){
                            // 准备分销商邀请人查询条件
                            $vendorbWhere['code'] = $vendorc['superior_code'];
                            // 查询分销商邀请人
                            $vendorb = M('vendors')->where($vendorbWhere)->find();
                            // 分销商邀请人佣金比例
                            $ratiob  = $commission['vendor_b'];
                            // 获取佣金的类型[下分销商下的会员购买产品]
                            $typea = 1;
                            // 修改A级分销佣金比例
                            $ratioc = $ratioc - $ratiob;
                            // 分配分销商邀请人佣金
                            $this->vendor_commission($vendorb,$typea,$vendorc,$user,$ratiob,$order_id,$yj);
                            // dump($ratioa);die;
                        }
                        // C级分销商获得分红的类型
                        $typec = 0;
                        // C级分销商获得其余全部佣金
                        $this->vendor_commission($vendorc,$typec,$vendorc,$user,$ratioc,$order_id,$yj);
                        break;
                    default:
                        # code...
                        break;
                }

                break;
            case '5':

                // 会员扫普通二维码注册
                // 会员获得金币和银币
                $this->user_commission($user,$commission,$order_id,$jb,$yb);
                break;
            default:
                break;
        }
    }

    /**
     * [user_commission 会员邀请人金币银币分配]
     * @param  [type] $user       [会员]
     * @param  [type] $invitation [会员邀请人]
     * @param  [type] $commission [分成比例]
     * @param  [type] $order_id   [订单号码]
     * @param  [type] $yb         [银币]
     * @param  [type] $jb         [金币]
     * @return [type]             [布尔值]
     */
    public function invitation_commission($invitation,$user,$commission,$order_id,$jb,$yb)
    {
        // 实例化用户佣金模型
        $usersCommission = M('usersCommission');
        // 开启事务
        $usersCommission->startTrans();
        // 会员邀请人
        $saveData['user_code']  = $invitation['code'];
        // 产生分红的会员
        $saveData['nexus_user'] = $user['code'];
        // 分成的订单号
        $saveData['order_id']   = $order_id;
        // 会员获得银币
        $saveData['silver']     = $commission['inviter_y'] * $yb / 100;
        // 会员获得金币
        $saveData['gold_num']   = $commission['inviter_j'] * $jb / 100;
        // 会员当前银币
        $saveData['current_silver']     = $invitation['silver'] + $saveData['silver'];
        // 会员当前金币
        $saveData['current_gold_num']   = $invitation['gold_num'] + $saveData['gold_num'];
        // 获得类型
        $saveData['type']   = 1;
        // 描述
        $saveData['describe']   = '邀请会员'.$user['nickname'].'购买，单号为['.$order_id.']的商品获得金币奖励:'.$saveData['gold_num'].'个,获得银币奖励：'.$saveData['silver'].'个';
        // 创建时间
        $saveData['addtime']    = time(); 
        // 更新时间
        $saveData['updatetime']     = $saveData['addtime']; 


        // dump($saveData);die;
        // 写用户佣金记录
        $userRes = $usersCommission->add($saveData);
        // 准备更新条件
        $userWhere['id'] = $invitation['id'];
        // 准备更新数据
        // 会员银币
        $saveUserData['silver']     = $saveData['current_silver'];
        // 会员金币
        $saveUserData['gold_num']   = $saveData['current_gold_num'];
        // 更新时间
        $saveUserData['updatetime'] = $saveData['addtime'];
        // 更新用户账号
        $usersSaveRes = M('users')->where($userWhere)->save($saveUserData);

        // 模拟看
        // echo $saveData['describe'].'<br/>';

        // 会员金银币记录流水填写成功并更新会员表当前金银币成功
        if($userRes && $usersSaveRes){
            // 执行事务
            $usersCommission->commit();
            return true;
        }else{
            // 事务回滚
            $usersCommission->rollback();
            return false;
        }
    }

    /**
     * [user_commission 会员金币银币分配]
     * @param  [type] $user       [会员]
     * @param  [type] $commission [分成比例]
     * @param  [type] $order_id   [订单号码]
     * @param  [type] $yb         [银币]
     * @param  [type] $jb         [金币]
     * @return [type]             [布尔值]
     */
    public function user_commission($user,$commission,$order_id,$jb,$yb)
    {
        // 实例化用户佣金模型
        $usersCommission = M('usersCommission');
        // 开启事务
        $usersCommission->startTrans();
        // 会员自己
        $saveData['user_code']  = $user['code'];
        // 产生分红的会员
        $saveData['nexus_user']     = $user['code'];
        // 分成的订单号
        $saveData['order_id']   = $order_id;
        // 会员获得银币
        $saveData['silver']     = $commission['user_y'] * $yb / 100;
        // 会员获得金币
        $saveData['gold_num']   = $commission['user_j'] * $jb / 100;
        // 会员当前银币
        $saveData['current_silver']     = $user['silver'] + $saveData['silver'];
        // 会员当前金币
        $saveData['current_gold_num']   = $user['gold_num'] + $saveData['gold_num'];
        // 获得类型
        $saveData['type']   = 0;
        // 描述
        $saveData['describe']   = '自己购买，单号为['.$order_id.']的商品获得金币奖励:'.$saveData['gold_num'].'个,获得银币奖励：'.$saveData['silver'].'个';
        // 创建时间
        $saveData['addtime']    = time(); 
        // 更新时间
        $saveData['updatetime']     = $saveData['addtime']; 


        
        // 写用户佣金记录
        $userRes = $usersCommission->add($saveData);
        // 准备更新条件
        $userWhere['id'] = $user['id'];
        // 准备更新数据
        // 会员银币
        $saveUserData['silver']     = $saveData['current_silver'];
        // 会员金币
        $saveUserData['gold_num']   = $saveData['current_gold_num'];
        // 更新时间
        $saveUserData['updatetime'] = $saveData['addtime'];
        // 更新用户账号
        $usersSaveRes = M('users')->where($userWhere)->save($saveUserData);

        // 模拟看
        // echo $saveData['describe'].'<br/>';;

        // 会员金银币记录流水填写成功并更新会员表当前金银币成功
        if($userRes && $usersSaveRes){
            // 执行事务
            $usersCommission->commit();
            return true;
        }else{
            // 事务回滚
            $usersCommission->rollback();
            return false;
        }

    }


    /**
     * [vendor_commission 分销商佣金分配]
     * @param  [type] $vendor   [分销商]
     * @param  [type] $type     [获得佣金类型]
     * @param  [type] $nexus_vendor     [产生分红的分销商] 
     * @param  [type] $ratio    [分成比例]
     * @param  [type] $order_id [获得分成的订单号]
     * @param  [type] $yj       [佣金总额]
     * @return [type]           [布尔值]
     */
    public function vendor_commission($vendor,$type,$nexus_vendor,$nexus_user,$ratio,$order_id,$yj){
        // 关联分销商唯一标识
        $addData['vendor_code'] = $vendor['code'];
        // 产生分成的关联分销商
        $addData['nexus_vendor'] = $nexus_vendor['code'];
        // 产生分成的关联会员
        $addData['nexus_user'] = $nexus_user['code'];
        // 产生分成的订单号码
        $addData['order_id'] = $order_id;
        // 获得分红
        $addData['abonus'] = $yj*$ratio/100;
        // 当前分红
        $addData['current_abonus'] = (($addData['abonus']*100)+($vendor['abonus']*100))/100;
        // 获得分红的类型
        $addData['type'] = $type;
        // 创建时间
        $addData['addtime']     = time(); 
        // 更新时间
        $addData['updatetime']  = $addData['addtime']; 
        // 获得分红的描述
        switch ($type) {
            case '0':
                // 分销商直接会员
                $addData['describe'] = '分销商下的会员'.$nexus_user['nickname'].'购买单号：'.$order_id.'的商品获得佣金奖励:'.$addData['abonus'].'元';
                break;
            case '1':
                // 分销商下级分销商的会员
                $addData['describe'] = '下级分销商'.$nexus_vendor['name'].'下的会员'.$nexus_user['nickname'].'购买单号：'.$order_id.'的商品获得佣金奖励:'.$addData['abonus'].'元';
                break;
            case '2':
                // 分销商下下级分销商的会员
                $addData['describe'] = '下下级分销商'.$nexus_vendor['name'].'下的会员'.$nexus_user['nickname'].'购买单号：'.$order_id.'的商品获得佣金奖励:'.$addData['abonus'].'元';
                break;
            case '3':
                // 分销商邀请的同级分销商的会员
                $addData['describe'] = '邀请的分销商'.$nexus_vendor['name'].'下的会员'.$nexus_user['nickname'].'购买单号：'.$order_id.'的商品获得佣金奖励:'.$addData['abonus'].'元';
                break;
            default:
                # code...
                break;
        }
        // 实例化用户佣金模型
        $vendorCommission = M('vendorsCommission');
        // 开启事务
        $vendorCommission->startTrans();
        // 写佣金记录
        $vendoriAddRes = $vendorCommission->add($addData);
        // 更新条件
        $saveWhere['code'] = $vendor['code'];
        // 更新数据
        $saveData['abonus'] = $addData['current_abonus'];
        // 执行更新
        $saveData['updatetime']     = $addData['updatetime'];
        $vendorisaveRes = M('vendors')->where($saveWhere)->save($saveData);

        // 模拟看
        // echo $addData['describe'].'<br/>';;

        // 如果分销商分红记录和分销商分红更新成功
        if($vendoriAddRes && $vendorisaveRes){
            // 执行事务
            $vendorCommission->commit();
            return true;
        }else{
            // 事务回滚
            $vendorCommission->rollback();
            return false;
        }
    }


}


