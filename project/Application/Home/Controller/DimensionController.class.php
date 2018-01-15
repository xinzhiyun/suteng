<?php
namespace Home\Controller;

use Think\Controller;

class DimensionController extends Controller
{
	/**
	 * [vedor 吴智彬]
	 * @param  [number]		$num    	[二维码数量]
	 * @param  [boolean]   	$type   	[永久或临时，默认临时]
	 * @return [string]     $jsoninfo   [带参数二维码票据]
	 * 临时票据有效时间：2590000秒 (既 29.97685185185天) 或 再次使用同样的参请求，生成新的票据时
	 * 永久票据有效时间：长期有效
	 * 使用票据：<img src="https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket='.$jsoninfo.'" alt="二维码票据">
	 */
    public function vedor($num,$type=false)
    {
    	// 实例化微信JSSDK类对象
        $wxJSSDK = new \Org\Util\WeixinJssdk;
        // 调用获取公众号的全局唯一接口调用凭据
        $accessToken = $wxJSSDK->getAccessToken();
        // 请求微信带参数二维码
        $url = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token={$accessToken}";
        // 临时二维码请求数据格式
        $lscode = '{"expire_seconds": 2590000, "action_name": "QR_SCENE", "action_info": {"scene": {"scene_id": '.$num.'}}}';
        // 永久二维码请求数据格式
        $yjcode = '{"action_name":"QR_LIMIT_SCENE","action_info":{"scene":{"scene_id":'.$num.'}}';
        // 请求二维码数据格式
        $data = $type?$yjcode:$lscode;
        // 发送请求
        $result = $this->httpPost($url,$data);
        // 解析JSON格式数据
        $jsoninfo = json_decode($result,true)['ticket'];
        // 返回获取二维码票据
        return $jsoninfo;
    }

    /**
     * [httpPost 吴智彬]
     * @param  [string] $url 		[请求地址]
     * @param  [json] 	$data 		[请求参数]
     * @return [json]   $tmpInfo    [返回票据]
     */
	public function httpPost($url, $data){
	    $ch = curl_init();
	    $header = "Accept-Charset: utf-8";
	    curl_setopt($ch, CURLOPT_URL, $url);
	    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
	    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
	    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
	    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	    curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
	    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    $tmpInfo = curl_exec($ch);
	    if (curl_errno($ch)) {
	        curl_close( $ch );
	        return $ch;
	    }else{
	        curl_close( $ch );
	        return $tmpInfo;
	    }
	}
}
