<?php
namespace Home\Controller;
use \Org\Util\WeixinJssdk;
use Think\Controller;

class NotifyController extends Controller
{
    //所有的支付回调

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
//        $string_sign_temp=$string_a."&key=".C('KEY');
        $string_sign_temp=$string_a."&key=c5fb60622163f8341b970dfad4031edc";
        //签名步骤三：MD5加密
        $sign = md5($string_sign_temp);
        // 签名步骤四：所有字符转为大写
        $result=strtoupper($sign);
        return $result;
    }

}


