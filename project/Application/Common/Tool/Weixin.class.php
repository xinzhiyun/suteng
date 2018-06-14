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

    public static function wx_sdk(){
        if(!(self::$_wx instanceof WeixinJssdk)){
            self::$_wx = new WeixinJssdk;
        }
        return self::$_wx;
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


}