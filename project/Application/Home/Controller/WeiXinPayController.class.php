<?php
namespace Home\Controller;
use \Org\Util\WeixinJssdk;

class WeiXinPayController extends CommonController
{
    /**
     * 处理订单写入数据
     * @return array 返回数组格式的notify数据
     */
    public function notify()
    {
        //dump($_SESSION);die;
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
//         $xml = '<xml><appid><![CDATA[wxae48f3bbcda86ab1]]></appid>
// <attach><![CDATA[316904214969787641]]></attach>
// <bank_type><![CDATA[CFT]]></bank_type>
// <cash_fee><![CDATA[1]]></cash_fee>
// <fee_type><![CDATA[CNY]]></fee_type>
// <is_subscribe><![CDATA[Y]]></is_subscribe>
// <mch_id><![CDATA[1394894802]]></mch_id>
// <nonce_str><![CDATA[s5qfmwv8zk7es4r8iz65v98klr4ijgc1]]></nonce_str>
// <openid><![CDATA[oXwY4t-9clttAFWXjCcNRJrvch3w]]></openid>
// <out_trade_no><![CDATA[614841407334299817]]></out_trade_no>
// <result_code><![CDATA[SUCCESS]]></result_code>
// <return_code><![CDATA[SUCCESS]]></return_code>
// <sign><![CDATA[79A94BC290092A928F851539742BFA3A]]></sign>
// <time_end><![CDATA[20180112102104]]></time_end>
// <total_fee>1</total_fee>
// <trade_type><![CDATA[JSAPI]]></trade_type>
// <transaction_id><![CDATA[4200000052201801122671865383]]></transaction_id>
// </xml>';
        
        if($xml){
            //解析微信返回数据数组格式
            $result = $this->notifyData($xml);
            //show($result);die;
            //$uid = M('Users')->where("open_id='{$result['']}'")->find()['id'];
            //file_put_contents('./wx_pay1.txt',$xml."\r\n", FILE_APPEND);
            // 如果订单号不为空
            if(!empty($result['out_trade_no'])){
                $did = substr($result['out_trade_no'],15);
                //file_put_contents('./wx_pay1.txt',$result['out_trade_no']."\r\n", FILE_APPEND);
                //file_put_contents('./wx_pay1.txt',$uid."\r\n", FILE_APPEND);
                // 获取传回来的订单号
                $data['order_id'] = $result['attach'];

                // 查询订单是否已处理
                $orderData = M('Orders')->where($data)->field('is_pay,total_price')->find();

                // 1分钱测试数据
                $orderData['total_price'] = 1;
                // 如果订单未处理，订单支付金额等于订单实际金额
                if(empty($orderData['is_pay']) && $orderData['total_price'] == $result['total_fee']){
                    //file_put_contents('./wx_pay121.txt',$xml."\r\n", FILE_APPEND);
                    //dump($result);
                    // 处理订单
                    // 实例化订单对象
                    $orders = M('Orders');  
                    // 实例化订单滤芯对象
                    $orderFilter = M('OrderFilter');
                    // 实例化订单套餐对象
                    $orderSetmeal = M('OrderSetmeal');
                    // 实例化设备详细信息对象
                    $devicesStatu = M('devicesStatu');
                    // 实例化设备对象
                    $device = M('Devices');
                    // 实例化充值流水对象
                    $flowObj = M('Flow');
                    
                    // 开启事务
                     $orders->startTrans();

                    // 修改订单状态为已付款
                    $isPay['is_pay'] = 1;
                    
                    
                    //show($isPayRes);die;
                    // 查询订单包含的全部套餐
                    $orderSetmealData = $orderSetmeal->where($data)->select();


                    if($orderSetmealData){
                        $isPay['is_recharge'] = 1;
                    }

                    $isPayRes = $orders->where($data)->save($isPay);
                    
                    // 充值状态
                    $status = 0;
                    if($orderSetmealData){
                        //show($orderSetmealData);die;
                        // 统计未处理套餐数量
                        $countNun = count($orderSetmealData);
                        // 定义计数器
                        $num     = 0;
                        $flownum = 0;
                        //file_put_contents('./wx_pay1uid.txt',$result['out_trade_no']."\r\n", FILE_APPEND);
                        // 查询当前设备编号
                        $deviceId['id'] = $did;
                        //file_put_contents('./wx_pay2uid.txt',$uid."\r\n", FILE_APPEND);
                        $deviceCode['DeviceID'] = $device->where($deviceId)->find()['device_code'];
                        
                        foreach ($orderSetmealData as $value) {
                            //show($value);die;
                            // 查询设备当前剩余流量
                            $devicesStatuReFlow = $devicesStatu->where($deviceCode)->find()['reflow']-0;

                            // 充值后流量应剩余流量
                            $Flow['ReFlow'] = $devicesStatuReFlow + ($value['flow']*$value['goods_num']);

                            // 修改设备剩余流量
                            $FlowRes = $devicesStatu->where($deviceCode)->save($Flow);

                            // 写充值流水
                            // 订单编号
                            $flowData['order_id']       = $value['order_id'];
                            // 用户ID
                            $flowData['did']            = $did;
                            // 充值金额
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


                            // 判断流水是否创建成果
                            if($flowObjRes){
                                // 定时器++
                                $flownum++;
                            }

                            // 判断修改结果
                            if($FlowRes){
                                // 计数器++
                                $num++;  
                            }
                            
                        }

                        // 全部套餐充值完成
                        if($countNun == $num && $countNun == $flownum){
                            // 充值和流水完成，状态设为1
                            $status = 1;
                        }

                    }else{
                        // 没有套餐默认值，状态设为1
                        $status = 1;
                    }
                    
                    //show($status);die;
                    //file_put_contents('saaa',$isPayRes .'jfdslajfds'. $status);
                    if($isPayRes && $status){
                        
                        // 执行事务
                        $orders->commit();
                        file_put_contents('./wx_notifyYes.txt','订单号：'.$result['attach']."充值完成 \r\n", FILE_APPEND);
                    }else{
                        // 事务回滚
                        $orders->rollback();
                        file_put_contents('./wx_notifyEeor.txt','订单号：'.$result['attach']."充值失败 \r\n", FILE_APPEND);
                    }
                }else{
                    // 充值金额不匹配
                    // if($orderData['total_price'] != $result['total_fee']){
                    //    file_put_contents('./wx_notifymoney.txt','订单号：'.$result['attach']."充值失败,金额不匹配。订单金额：{$orderData['total_price']} ，充值金额：{$result['total_fee']} \r\n", FILE_APPEND); 
                    // }  
                }
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
        if ($sign==$dataSign && $data['return_code']=='SUCCESS' && $data['result_code']=='SUCCESS') {

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
        $string_sign_temp=$string_a."&key=CAA5EAE2CE5AC44A3F8930E6F127B423";
        //签名步骤三：MD5加密
        $sign = md5($string_sign_temp);
        // 签名步骤四：所有字符转为大写
        $result=strtoupper($sign);
        return $result;
    }

}


