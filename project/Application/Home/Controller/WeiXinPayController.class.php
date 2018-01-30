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
        //file_put_contents('./wx_pay.txt',$xml."\r\n", FILE_APPEND);die;
        //echo 1;die;
//         $xml = '<xml><appid><![CDATA[wx0bab2f4b5b7ec3b5]]></appid>
// <attach><![CDATA[3]]></attach>
// <bank_type><![CDATA[CFT]]></bank_type>
// <cash_fee><![CDATA[1]]></cash_fee>
// <fee_type><![CDATA[CNY]]></fee_type>
// <is_subscribe><![CDATA[Y]]></is_subscribe>
// <mch_id><![CDATA[1490274062]]></mch_id>
// <nonce_str><![CDATA[kbdzxh20tp4o0ox36kve28bzc5n2cj1i]]></nonce_str>
// <openid><![CDATA[oQktJwL8ioR4DoxSQmikdzekbUyU]]></openid>
// <out_trade_no><![CDATA[728016708453580]]></out_trade_no>
// <result_code><![CDATA[SUCCESS]]></result_code>
// <return_code><![CDATA[SUCCESS]]></return_code>
// <sign><![CDATA[93AC5BF364A6843CC87EB3B1755B0659]]></sign>
// <time_end><![CDATA[20180130174703]]></time_end>
// <total_fee>1</total_fee>
// <trade_type><![CDATA[JSAPI]]></trade_type>
// <transaction_id><![CDATA[4200000056201801303706399965]]></transaction_id>
// </xml>';
        
        if($xml){
            //解析微信返回数据数组格式
            $result = $this->notifyData($xml);

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

}


