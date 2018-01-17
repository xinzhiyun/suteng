<?php
namespace Home\Controller;

use Think\Controller;
use Org\Util\Validate;
use \Org\Util\WeixinJssdk;

/**
 * 经销商注册控制器
 */
class RegisteredVendorController extends Controller
{
    public function index()
    {

            if(IS_POST){
                dump($_POST);die;

            }else{
                // 如果是分销商注册
                // 实例化微信JSSDK
                $weixin = new WeixinJssdk;
                // 获取用户open_id
                if(empty($_SESSION['open_id'])){
                    // 如果不存在则，跳转获取open_id,并缓存
                    $_SESSION['open_id'] = $weixin->GetOpenid();
                }
                // 获取用户open_id
                $showData['open_id'] = $_SESSION['open_id'];
                // 查询微信信息表
                $wechat = M('wechat')->where($showData)->find();
                // 接收类型
                $type   = $wechat["type"];
                // 接收操作
                $action = $wechat["action"];
                // 接收邀请人二维码票据
                $ticket = $wechat["invitation_ticket"];

                if($type){
                    switch ($action) {
                        case '0':
                            // 显示模板
                            $this->display();
                            break;
                        case '1':
                            // 已经注册过，跳转到分销商登录页
                            $this->redirect('分销商登录页面');
                            break;
                        default:
                            # 不存在这个类型的操作
                            $this->error('系统暂不支持该类型分销注册，请加盟联系客服！');
                            break;
                    }
                }else{
                    // 非法访问
                    $this->error('分销商只支持邀请注册，请加盟联系客服！');
                }
            }

   
    	// if(IS_POST){


    	// 	// 匹配注册类型
    	// 	switch ($reType) {
    	// 		case '3':
    	// 			// echo '分公司邀请注册分销商:';
    	// 			// 匹配分销商级别
    	// 			switch ($leavel) {
    	// 				case '0':
    	// 					// echo '一级';
    	// 					break;
    	// 				case '2':
    	// 					// echo '二级';
    	// 					break;
    	// 				case '2':
    	// 					// echo '三级';
    	// 					break;
    	// 				default:
	    // 					// 非法访问
	    // 					$this->error('系统暂不支持该该级别的分销商注册，请联系后台管理员');
    	// 					break;
    	// 			}
    	// 			break;
    	// 		case '4':
    	// 			// echo '分销商邀请注册分销商';
    	// 			// 匹配分销商级别
    	// 			switch ($leavel) {
    	// 				case '0':
    	// 					// echo '一级';
    	// 					break;
    	// 				case '2':
    	// 					// echo '二级';
    	// 					break;
    	// 				case '2':
    	// 					// echo '三级';
    	// 					break;
    	// 				default:
	    // 					// 分销商级别错误
	    // 					$this->error('系统暂不支持该该级别的分销商注册，请联系后台管理员');
    	// 					break;
    	// 			}
    	// 			break;
    	// 		default:
    	// 				// 注册类型错误
    	// 				$this->error('系统暂不支持该类型分销邀请注册，请联系后台管理员');
    	// 			break;
    	// 	}

    	// }else{
    	// 	// 如果是分销商注册
    	// 	if($type){
    	// 		switch ($action) {
    	// 			case '0':
    	// 				// 显示模板
    	// 				$this->display();
    	// 				break;
    	// 			case '1':
    	// 				// 已经注册过，跳转到分销商登录页
    	// 				$this->redirect('分销商登录页面');
    	// 				break;
    	// 			default:
    	// 				# 不存在这个类型的操作
    	// 				$this->error('系统暂不支持该类型分销注册，请联系后台管理员');
    	// 				break;
    	// 		}
    	// 	}else{
    	// 		// 非法访问
    	// 		$this->error('非法入境,请退出边境线！');
    	// 	}
    	// }
    }
}

// array(7) {
//   ["id"] => string(1) "1"
//   ["open_id"] => string(28) "oQktJwL8ioR4DoxSQmikdzekbUyU"
//   ["invitation_ticket"] => string(96) "gQGa8DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAya05pU2toMmJkOTAxMDAwMDAwM0IAAgQUVV1aAwQAAAAA"
//   ["type"] => string(1) "1"
//   ["action"] => string(1) "0"
//   ["addtime"] => string(10) "1516071015"
//   ["updatetime"] => string(10) "1516071015"
// }