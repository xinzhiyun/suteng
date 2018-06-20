<?php
/**
 * Created by PhpStorm.
 * User: 李振东
 * Time: 2018/3/29 下午2:37
 */
namespace Common\Tool;
use \Org\Util\WeixinJssdk;
use Think\Log;

class Weixin
{
    public static $_wx;

    public static $templates=[  // 信息模板ID
        'apply_pass'=>'',   // 服务站申请-审核通过
        'apply_fall'=>'',   // 服务站申请-审核不通过
    ];

    public static function wx_sdk(){
        if(!(self::$_wx instanceof WeixinJssdk)){
            self::$_wx = new WeixinJssdk;
        }
        return self::$_wx;
    }

    /**
     * 模板消息通知
     *
     * $data = ['keyword1'=>["value"=>"巧克力","color"=>"#173177"], ....  ]
     */
    public function sendTplNotice($openid, $template_id, $data, $url='')
    {

        $api = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=".self::getAccessToken();

        $sendData=[
            'touser'=>$openid,
            'template_id'=>$template_id,
            'data'=>$data
        ];

        if(!empty($url)){
            $sendData['url']=$url;
        }

        return self::httpPost($api,json_encode($sendData));

    }
    

    /**
     * 获取 Openid
     * @return
     */
    public static function GetOpenid()
    {
        return  self::wx_sdk()->GetOpenid();
    }
    
    /**
     * 获取用户信息
     * @param $openid
     * @return mixed
     */
    public static function getInfo($openid)
    {
        $accessToken = self::getAccessToken();
        $url = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$accessToken.'&openid='.$openid.'&lang=zh_CN';
        // 发送请求获取用户信息
        $userInfo = self::httpGet($url);
        // 把 JSON 格式的字符串转换为PHP数组
        return json_decode($userInfo, true);
    }

    /**
     * get请求
     * @param $url
     * @return mixed
     */
    public static function httpGet($url)
    {
        return  self::wx_sdk()->httpGet($url);
    }

    /**
     * POST请求
     * @param $url
     * @return mixed
     */
    public static function httpPost($url,$data)
    {
        return  self::wx_sdk()->httpPost($url, $data);
    }


    /**
     * 获取 SignPackage
     * @return array
     */
    public static function getSignPackage()
    {
        return  self::wx_sdk()->getSignPackage();
    }

    /**
     * 获取 AccessToken
     * @return mixed
     */
    public static function getAccessToken()
    {
        return  self::wx_sdk()->getAccessToken();
    }

    // 获取微信信息
    public static function getWeiXinUserInfo($openid)
    {
        // 实例化微信JSSDK类对象
        $wxJSSDK = new WeixinJssdk;
        // 调用获取公众号的全局唯一接口调用凭据
        $accessToken = $wxJSSDK->getAccessToken();
        //show($accessToken);die;
        // 请求返回简体中文版用户信息数据
        $url = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$accessToken.'&openid='.$openid.'&lang=zh_CN';

        // 发送请求获取用户信息
        $userInfo = $wxJSSDK->httpGet($url);

        // 把 JSON 格式的字符串转换为PHP数组
        $userInfo = json_decode($userInfo, true);

        // 准备微信信息表数据
        // 微信的ID号
        $data['open_id'] = $userInfo['openid'];
        $data['nickname'] = $userInfo['nickname'];
        $data['head'] = $userInfo['headimgurl'];
        // 性别{0:女, 1:男, 2:保密}
        $data['sex'] = $userInfo['sex'];
        switch ($userInfo['sex']) {
            case '0':
                $data['sex'] = 2;
                break;
            case '1':
                $data['sex'] = 1;
                break;
            case '2':
                $data['sex'] = 0;
                break;
            default:
                # code...
                break;
        }
        // 地区 省份
        $data['area'] = $userInfo['province'];
        // 国家 省份 市区
        $data['address'] = $userInfo['country'].' '.$userInfo['province'].' '.$userInfo['city'];

        return $data;
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
    public static function uniformOrder($openId,$money,$order_id,$content,$notify_url)
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
        return $tools->GetJsApiParameters($order);


    }


}