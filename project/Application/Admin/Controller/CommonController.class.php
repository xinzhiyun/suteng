<?php
namespace Admin\Controller;
use Think\Controller;
use Think\Auth;

/**
 * 公共控制器
 * 后台控制器除login外必须继承我
 * @author 潘宏钢 <619328391@qq.com>
 */

class CommonController extends Controller 
{
    protected $leavel = null;

	/**
     * 初始化
     * @author 潘宏钢 <619328391@qq.com>
     */
    public function _initialize()
    {	
    	// 登录检测
    	if(empty($_SESSION['adminInfo'])) $this->redirect('Login/index');
        $vid = session('adminInfo.id');
        $bool = $this->rule_check($vid);
        if(!$bool){
            $this->error('权限不足');
        }
        
        if(!S('st_vendor_leavel_'.$vid)){
            $this->getLevel();
        } else {
            $this->leavel = S('st_vendor_leavel_'.$vid);
        }

        // 分配菜单权限
        $nav_data=D('AdminMenu')->getTreeData('level','order_number,id');
        $assign=array(
            'nav_data'=>$nav_data
            );
        $this->assign($assign);
    }

    /**
     * 检测当前用户是否为经销商
     */
    public function check_vendors()
    {
        if(M('vendors')->where('id='.session('adminInfo.id'))->getField('leavel') >0 ){
            return true;
        }
        return false;
    }

    public function rule_check($uid)
    {
        $auth = new \Think\Auth();

        if( session('adminInfo.user') == 'admin' ){
            return true;
        }

        $name = MODULE_NAME."/".CONTROLLER_NAME."/".ACTION_NAME;
        // echo $name;die;
        return $auth->check($name, $uid);
    }

    private function getLevel(){
        $vid = session('adminInfo.id');
        $leavel = D('vendors')->where(['id'=>$vid])->field('leavel')->find();
        if($leavel){
            S('st_vendor_leavel_'.$vid,$leavel['leavel'],300);
            $this->leavel = S('st_vendor_leavel_'.$vid);
        }      
    }
}