<?php
namespace Home\Controller;
use \Org\Util\WeixinJssdk;
use Think\Controller;

class WeiXinPayController extends Controller
{
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
                $saveOrder['order_id'] = $orderData['order_id'];

                // 准备更新数据
                $saveOrderDara['status'] = 9;
                // 执行更新操作
                //$order->where($saveOrder)->save($saveOrderDara);
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
                $yjbl = 0.3;
                // 金币比例
                $jbbl = 0.1;
                // 银币比例
                $ybbl = 0.2;

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
        // file_put_contents('./wx_pay.txt',$xml."\r\n", FILE_APPEND);die;
        //echo 1;die;
//         $xml = '<xml><appid><![CDATA[wx0bab2f4b5b7ec3b5]]></appid>
// <attach><![CDATA[2]]></attach>
// <bank_type><![CDATA[CFT]]></bank_type>
// <cash_fee><![CDATA[1]]></cash_fee>
// <fee_type><![CDATA[CNY]]></fee_type>
// <is_subscribe><![CDATA[Y]]></is_subscribe>
// <mch_id><![CDATA[1490274062]]></mch_id>
// <nonce_str><![CDATA[usxnuq63f6kepips79estlyual4ikdht]]></nonce_str>
// <openid><![CDATA[oQktJwL8ioR4DoxSQmikdzekbUyU]]></openid>
// <out_trade_no><![CDATA[668073778377321]]></out_trade_no>
// <result_code><![CDATA[SUCCESS]]></result_code>
// <return_code><![CDATA[SUCCESS]]></return_code>
// <sign><![CDATA[01A682464432D90E7829BA09EAFFE83C]]></sign>
// <time_end><![CDATA[20180131155144]]></time_end>
// <total_fee>1</total_fee>
// <trade_type><![CDATA[JSAPI]]></trade_type>
// <transaction_id><![CDATA[4200000056201801314352585826]]></transaction_id>
// </xml>';
        
        if($xml){
            //解析微信返回数据数组格式
            $result = $this->notifyData($xml);
            // dump($result);die;
            $showFlow['order_id'] = $result['out_trade_no'];
            $flowData = M('flow')->where($showFlow)->find();

            if(empty($flowData)){
                // 充值套餐ID
                $id = $result['attach'];
                // 充值金额
                $je = [100,200,300,400,500,100,200,300,400,500];
                $jyb = [100,200,300,500,600,200,400,600,1000,1200];
                $contentArr =   [
                        '100元100个金币',
                        '200元200个金币',
                        '300元300个金币',
                        '400元500个金币',
                        '500元600个金币',
                       ' 100元200个银币',
                        '200元400个银币',
                        '300元600个银币',
                        '400元1000个银币',
                        '500元1200个银币',
                    ];
                // 查询用户
                $showUser['open_id'] = $result['openid'];


                $user = M('users')->where($showUser)->find();

                $addData['order_id'] = $result['openid'];
                $addData['user_id'] = $user['id'];
                $addData['order_id'] = $result['out_trade_no'];
                $addData['money']   = $je[$id];
                if($id<5){
                    $addData['gold_num'] = $jyb[$id];
                }else{
                    $addData['silver'] = $jyb[$id];
                }
                $addData['mode']    = 2;
                $addData['describe'] = $contentArr[$id];


                if($id<5){
                    $addData['current_gold_num'] = $saveData['gold_num'] = $jyb[$id] + $user['gold_num'];
                    $addData['current_silver'] = $user['silver'];
                }else{
                    $addData['current_silver'] = $saveData['silver'] = $jyb[$id] + $user['silver'];
                    $addData['current_gold_num'] = $user['gold_num'];
                }
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


