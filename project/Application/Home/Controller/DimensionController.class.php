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
    public function code($num,$type=false)
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
	 * [vedor 吴智彬]
	 * @param  [number]		$num    	[二维码数量]
	 * @param  [boolean]   	$type   	[永久或临时，默认临时]
	 * @return [string]     $jsoninfo   [带参数二维码票据]
	 * 临时票据有效时间：2590000秒 (既 29.97685185185天) 或 再次使用同样的参请求，生成新的票据时
	 * 永久票据有效时间：长期有效
	 * 使用票据：<img src="https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket='.$jsoninfo.'" alt="二维码票据">
	 */
    public function user()
    {
    	// 实例化微信JSSDK类对象
        $wxJSSDK = new \Org\Util\WeixinJssdk;
        // 调用获取公众号的全局唯一接口调用凭据
        $accessToken = $wxJSSDK->getAccessToken();
        // 请求微信带参数二维码
        $url = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token={$accessToken}";
        // 查询二维码编号
        $num = M('dimensionNum')->where('`id`=1')->find()['users_num'];
        // 临时二维码请求数据格式
        $lscode = '{"expire_seconds": 2590000, "action_name": "QR_SCENE", "action_info": {"scene": {"scene_id": '.$num.'}}}';

        // 发送请求
        $result = $this->httpPost($url,$lscode);
        // 解析JSON格式数据
        $jsoninfo['ticket'] 	 = json_decode($result,true)['ticket'];
        // 请求二维码的参数
        $jsoninfo['parameter']   = $num;
        // 二维码有效时间
        $jsoninfo['ticket_time'] = time()+2590000;
        // 填写二维码信息
		// 填写会员邀请二维码信息表注册类型
		$dimension = M('dimension');
		$user['ticket'] 		= $jsoninfo['ticket'];
		$user['recommend']		= 1;	
		$user['effective']		= 1;
		$user['addtime']		= time();
		$user['updatetime']	= $user['addtime'];
		//dump($user);die;
		$resuser = $dimension->add($user);

        if($jsoninfo['ticket']){
			// 修改分销商二维码参数
			$saveDate['users_num'] = $num+1;
			$resNum = M('dimensionNum')->where('id=1')->save($saveDate);
        }

        // 返回获取二维码票据
        return $jsoninfo;
    }

	/**vendorA
	 * [vedor 吴智彬]
	 * @return [string]     $jsoninfo   [带参数二维码票据]
	 * 永久票据有效时间：长期有效
	 * 使用票据：<img src="https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket='.$jsoninfo.'" alt="二维码票据">
	 */
    public function office()
    {
    	// 实例化微信JSSDK类对象
        $wxJSSDK = new \Org\Util\WeixinJssdk;
        // 调用获取公众号的全局唯一接口调用凭据
        $accessToken = $wxJSSDK->getAccessToken();
        // 请求微信带参数二维码
        $url = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token={$accessToken}";
        // 查询二维码编号
        $num = M('dimensionNum')->where('`id`=1')->find()['vendors_num'];

        // 分销商二维码下标定义
        $ticket = array('vendor_a','vendor_b','vendor_c','vendor_user');
        // 创建空数组准备装二维码
        $vedors = array();
        // 定义索引
        $j = 0;
        // 循环生成3个分销商邀请码
        for($i=$num;$i<$num+4;$i++){
	        // 永久二维码请求数据格式
	        $data = '{"action_name":"QR_LIMIT_SCENE","action_info":{"scene":{"scene_id":'.$i.'}}';
	        // 发送请求
	        $result = $this->httpPost($url,$data);
	        // 解析JSON格式数据
	        $jsoninfo = json_decode($result,true)['ticket'];
	        // 返回获取二维码票据
	        $vedors[$ticket[$j]] = $jsoninfo;
	        // 索引自增
	        $j++;
        }

        // 实例化二维码对象
        $dimension = M('dimension');
        // 开启事务
        $dimension->startTrans();

        // 填写A级分销商二维码信息表
		$vendor_a['ticket'] 	= $vedors['vendor_a'];
		$vendor_a['recommend']	= 4;
		$vendor_a['leavel']		= 0;
		$vendor_a['effective']	= 0;
		$vendor_a['addtime']	= time();
		$vendor_a['updatetime']	= $vendor_a['addtime'];
		$resa = $dimension->add($vendor_a);

		// 填写B级分销商二维码信息表
		$vendor_b['ticket'] 	= $vedors['vendor_b'];
		$vendor_b['recommend']	= 4;
		$vendor_b['leavel']		= 1;	
		$vendor_b['effective']	= 0;
		$vendor_b['addtime']	= time();
		$vendor_b['updatetime']	= $vendor_b['addtime'];
		$resb = $dimension->add($vendor_b);

		// 填写B级分销商二维码信息表
		$vendor_c['ticket'] 	= $vedors['vendor_c'];
		$vendor_c['recommend']	= 4;
		$vendor_c['leavel']		= 2;	
		$vendor_c['effective']	= 0;
		$vendor_c['addtime']	= time();
		$vendor_c['updatetime']	= $vendor_c['addtime'];
		$resc = $dimension->add($vendor_c);

		// 填写会员邀请二维码信息表注册类型
		$vendor_user['ticket'] 		= $vedors['vendor_user'];
		$vendor_user['recommend']	= 3;	
		$vendor_user['effective']	= 0;
		$vendor_user['addtime']		= time();
		$vendor_user['updatetime']	= $vendor_user['addtime'];
		$resuser = $dimension->add($vendor_user);

		// 修改分销商二维码参数
		$saveDate['vendors_num'] = $num+4;
		$resNum = M('dimensionNum')->where('id=1')->save($saveDate);
		//dump($resNum);die;
		// 如果二唯码全部创建成功，并修改参数成功
		if($resa && $resb && $resc && $resuser && $resNum){
			// 执行事务
			$dimension->commit();
	        // 返回获取二维码票据
	        return $vedors;
		}else{
			// 事务回滚
			$dimension->rollback();
			// 二维码创建失败
			return false;
		}
    }

	/**
	 * [vedor 吴智彬]
	 * @return [string]     $jsoninfo   [带参数二维码票据]
	 * 永久票据有效时间：长期有效
	 * 使用票据：<img src="https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket='.$jsoninfo.'" alt="二维码票据">
	 * {0:会员直接注册 1:会员推荐会员 2：分销商推荐会员 3：分公司推荐会员 4:分公司邀请分销商 5:分销商邀请分销商}
	 */
    public function vendor($leavel)
    {
    	// 实例化微信JSSDK类对象
        $wxJSSDK = new \Org\Util\WeixinJssdk;
        // 调用获取公众号的全局唯一接口调用凭据
        $accessToken = $wxJSSDK->getAccessToken();
        // 请求微信带参数二维码
        $url = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token={$accessToken}";
        // 查询二维码遍号
        $num = M('dimensionNum')->where('`id`=1')->find()['vendors_num'];

        // 分销商二维码下标定义
        switch ($leavel) {
        	case 'a':
        		// A级分销商获取二维码
        		$ticket = array('vendor_a','vendor_b','vendor_c','vendor_user');
        		// 获取二维码个数
        		$number = $num + 4;
        		break;
        	case 'b':
        		// B级分销商获取二维码
        		$ticket = array('vendor_b','vendor_c','vendor_user');
        		$number = $num + 3;
        		break;
        	case 'c':
        		// B级分销商获取二维码
        		$ticket = array('vendor_c','vendor_user');
        		$number = $num + 2;
        		break;
        	default:
        		// 异常处理
        		return false;
        		break;
        }

        // 创建空数组准备装二维码
        $vedors = array();
        // 定义索引
        $j = 0;

        // 循环生成3个分销商邀请码
        for($i=$num;$i<$number;$i++){
	        // 永久二维码请求数据格式
	        $data = '{"action_name":"QR_LIMIT_SCENE","action_info":{"scene":{"scene_id":'.$i.'}}';
	        // 发送请求
	        $result = $this->httpPost($url,$data);
	        // 解析JSON格式数据
	        $jsoninfo = json_decode($result,true)['ticket'];
	        // 返回获取二维码票据
	        $vedors[$ticket[$j]] = $jsoninfo;
	        // 索引自增
	        $j++;
        }

        // 实例化二维码对象
        $dimension = M('dimension');
        // 开启事务
        $dimension->startTrans();

        switch ($leavel) {
        	case 'a':
		        // 填写A级分销商二维码信息表
				$vendor_a['ticket'] 	= $vedors['vendor_a'];
				$vendor_a['recommend']	= 5;
				$vendor_a['leavel']		= 0;	
				$vendor_a['effective']	= 0;
				$vendor_a['addtime']	= time();
				$vendor_a['updatetime']	= $vendor_a['addtime'];
				$resa = $dimension->add($vendor_a);

				// 填写B级分销商二维码信息表
				$vendor_b['ticket'] 	= $vedors['vendor_b'];
				$vendor_b['recommend']	= 5;
				$vendor_b['leavel']		= 1;	
				$vendor_b['effective']	= 0;
				$vendor_b['addtime']	= time();
				$vendor_b['updatetime']	= $vendor_b['addtime'];
				$resb = $dimension->add($vendor_b);

				// 填写C级分销商二维码信息表
				$vendor_c['ticket'] 	= $vedors['vendor_c'];
				$vendor_c['recommend']	= 5;
				$vendor_c['leavel']		= 2;	
				$vendor_c['effective']	= 0;
				$vendor_c['addtime']	= time();
				$vendor_c['updatetime']	= $vendor_c['addtime'];
				$resc = $dimension->add($vendor_c);

				// 填写会员邀请二维码信息表注册类型
				$vendor_user['ticket'] 	= $vedors['vendor_user'];
				$vendor_user['recommend']	= 2;	
				$vendor_user['effective']	= 0;
				$vendor_user['addtime']	= time();
				$vendor_user['updatetime']	= $vendor_user['addtime'];
				$resuser = $dimension->add($vendor_user);
        		break;
        	case 'b':
				// 填写B级分销商二维码信息表
				$vendor_b['ticket'] 	= $vedors['vendor_b'];
				$vendor_b['recommend']	= 5;
				$vendor_b['leavel']		= 1;	
				$vendor_b['effective']	= 0;
				$vendor_b['addtime']	= time();
				$vendor_b['updatetime']	= $vendor_b['addtime'];
				$resb = $dimension->add($vendor_b);

				// 填写C级分销商二维码信息表
				$vendor_c['ticket'] 	= $vedors['vendor_c'];
				$vendor_c['recommend']	= 5;
				$vendor_c['leavel']		= 2;	
				$vendor_c['effective']	= 0;
				$vendor_c['addtime']	= time();
				$vendor_c['updatetime']	= $vendor_c['addtime'];
				$resc = $dimension->add($vendor_c);

				// 填写会员邀请二维码信息表注册类型
				$vendor_user['ticket'] 	= $vedors['vendor_user'];
				$vendor_user['recommend']	= 2;	
				$vendor_user['effective']	= 0;
				$vendor_user['addtime']	= time();
				$vendor_user['updatetime']	= $vendor_user['addtime'];
				$resuser = $dimension->add($vendor_user);
        		break;
        	case 'c':
				// 填写C级分销商二维码信息表
				$vendor_c['ticket'] 	= $vedors['vendor_c'];
				$vendor_c['recommend']	= 5;
				$vendor_c['leavel']		= 2;	
				$vendor_c['effective']	= 0;
				$vendor_c['addtime']	= time();
				$vendor_c['updatetime']	= $vendor_c['addtime'];
				$resc = $dimension->add($vendor_c);

				// 填写会员邀请二维码信息表注册类型
				$vendor_user['ticket'] 	= $vedors['vendor_user'];
				$vendor_user['recommend']	= 2;	
				$vendor_user['effective']	= 0;
				$vendor_user['addtime']	= time();
				$vendor_user['updatetime']	= $vendor_user['addtime'];
				$resuser = $dimension->add($vendor_user);
        		break;
        	default:
        		// 异常处理
        		return false;
        		break;
        }
		// 修改分销商二维码参数
		$saveDate['vendors_num'] = $number;
		$resNum = M('dimensionNum')->where('id=1')->save($saveDate);

        switch ($leavel) {
        	case 'a':
		        // 填写A级分销商二维码信息表
				// 如果二唯码全部创建成功，并修改参数成功
				if($resa && $resb && $resc && $resuser && $resNum){
					// 执行事务
					$dimension->commit();
			        // 返回获取二维码票据
			        return $vedors;
				}else{
					// 事务回滚
					$dimension->rollback();
					// 二维码创建失败
					return false;
				}
        		break;
        	case 'b':
				// 填写B级分销商二维码信息表
				// 如果二唯码全部创建成功，并修改参数成功
				if($resb && $resc && $resuser && $resNum){
					// 执行事务
					$dimension->commit();
			        // 返回获取二维码票据
			        return $vedors;
				}else{
					// 事务回滚
					$dimension->rollback();
					// 二维码创建失败
					return false;
				}

        		break;
        	case 'c':
				// 填写C级分销商二维码信息表
				// 如果二唯码全部创建成功，并修改参数成功
				if($resc && $resuser && $resNum){
					// 执行事务
					$dimension->commit();
			        // 返回获取二维码票据
			        return $vedors;
				}else{
					// 事务回滚
					$dimension->rollback();
					// 二维码创建失败
					return false;
				}
        		break;
        	default:
        		// 异常处理
        		return false;
        		break;
        }
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
