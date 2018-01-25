<?php
namespace Home\Controller;

use Think\Controller;
use \Org\Util\WeixinJssdk;
class IndexController extends CommonController
{
    public function index()
    {
       // 100元佣金
       // 100金币
       // 100银币
       //dump($_SESSION['user']);
       	$yj = 100;
       	$jb = 100;
       	$yb = 100;
       	$user = $_SESSION['user'];

    	echo $this->branch_commission($user,$yj,$jb,$yb);


    }
// array(23) {
//   ["id"] => string(1) "2"
//   ["open_id"] => string(28) "oQktJwL8ioR4DoxSQmikdzekbUyU"
//   ["office_code"] => string(6) "53AB0F"
//   ["vendora_code"] => string(6) "428777"
//   ["vendorb_code"] => string(6) "428777"
//   ["vendorc_code"] => string(6) "428777"
//   ["vendori_code"] => NULL
//   ["invitation_code"] => string(6) "428777"
//   ["code"] => string(11) "81375E7BC3E"
//   ["invite"] => string(1) "1"
//   ["ticket"] => string(96) "gQF47zwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyTWpaQ2x3MmJkOTAxSndUZzFxMVgAAgQwcmlaAwQwhScA"
//   ["parameter"] => string(1) "1"
//   ["ticket_time"] => string(10) "1519449953"
//   ["nickname"] => string(21) "吴智彬-13425492760"
//   ["head"] => string(140) "http://wx.qlogo.cn/mmopen/Q3auHgzwzM4IUw4vejmKA64ib5YEGDyFzrOkCVqa7EasmHUxvGDb5dA9nfjQ7x0zffsF5hCiaEvZgnt7WHjpeEPjAB7ibq9nZu0Ep2fcqtaH84/132"
//   ["sex"] => string(1) "1"
//   ["area"] => string(6) "广东"
//   ["address"] => string(20) "中国 广东 广州"
//   ["gold_num"] => string(1) "0"
//   ["silver"] => string(1) "0"
//   ["balance"] => string(4) "0.00"
//   ["addtime"] => string(10) "1516859952"
//   ["updatetime"] => string(10) "1516859952"
// }
    public function branch_commission($user,$yj,$jb,$yb)
    {
       // 准备查询条件
       $showData['id'] = 1;
       // 查询佣金分配比例
       $data = M('commission')->where($showData)->find();

    	// 邀请人级别
    	$invite = $user['invite'];

    	switch ($invite) {
    		case '0':
    			// 分公司邀请的会员
    			// 会员自己
    			$uid = $user['id'];

    			break;
    		case '1':
    			// A级分销商邀请的会员
    			// 会员自己
    			$uid = $user['id'];
    			// A级分销商
    			$vendora = $user['vendora_code'];
    			// A级分销商邀请人
    			$vendora = $user['vendori_code'];

    			// 会员获得银币
    			$user_y = $data['user_y'] * $yb/100;
    			// 会员获得金币
    			$user_j = $data['user_j'] * $jb/100;

    			// 分销商邀请人获得提成
    			$vendor_i = $data['vendor_i'] * $yj/100;

    			// A级分销商获得提成
    			$vendor_a = $yj - $vendor_i;

    			return '会员获得银币:'.$user_y.'会员获得金币'.$user_j.'分销商邀请人获得提成'.$vendor_i.'A级分销商获得提成'.$vendor_a;

    			break;
    		case '2':
    			// B级分销商邀请的会员
    			break;
    		case '3':
    			// C级分销商邀请的会员
    			break;
    		case '4':
    			// 会员邀请的会员
    			break;
    		case '5':
    			// 会员普通二维码注册的
    			break;
    		default:
    			# code...
    			break;
    	}
    }
}
