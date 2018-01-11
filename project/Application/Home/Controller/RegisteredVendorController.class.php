<?php
namespace Home\Controller;

use Think\Controller;
use Org\Util\Validate;

/**
 * 经销商注册控制器
 */
class RegisteredVendorController extends Controller
{
    public function index()
    {
    	$open_id = 'oXwY4t-9clttAFWXjCcNRJrvch3w';
    	// 查询微信用户表-检测是否注册分销商
    	$user = M('wechat')->where("open_id='{$open_id}'")->find();
    	// 注册类型[3:分公司邀请分销商 4:分销商邀请分销商]
    	$reType = $user['recommend'];
    	// 分销商级别[0:A级分销商 1:B级分销商 2:C级分销商]
    	$leavel = $user['leavel'];
    	// 操作[]
    	$action = $user['action'];

    	if(IS_POST){


    		// 匹配注册类型
    		switch ($reType) {
    			case '3':
    				// echo '分公司邀请注册分销商:';
    				// 匹配分销商级别
    				switch ($leavel) {
    					case '0':
    						// echo '一级';
    						break;
    					case '2':
    						// echo '二级';
    						break;
    					case '2':
    						// echo '三级';
    						break;
    					default:
	    					// 非法访问
	    					$this->error('系统暂不支持该该级别的分销商注册，请联系后台管理员');
    						break;
    				}
    				break;
    			case '4':
    				// echo '分销商邀请注册分销商';
    				// 匹配分销商级别
    				switch ($leavel) {
    					case '0':
    						// echo '一级';
    						break;
    					case '2':
    						// echo '二级';
    						break;
    					case '2':
    						// echo '三级';
    						break;
    					default:
	    					// 分销商级别错误
	    					$this->error('系统暂不支持该该级别的分销商注册，请联系后台管理员');
    						break;
    				}
    				break;
    			default:
    					// 注册类型错误
    					$this->error('系统暂不支持该类型分销邀请注册，请联系后台管理员');
    				break;
    		}
    	}else{


    		// 如果是分销商注册
    		if($reType>2){
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
    					$this->error('系统暂不支持该类型分销注册，请联系后台管理员');
    					break;
    			}
    		}else{
    			// 非法访问
    			$this->error('非法入境,请退出边境线！');
    		}
    	}
    }
}