<?php
namespace Admin\Behaviors;
class CheckAuthBehavior extends \Think\Behavior{
    //行为执行入口
    public function run(&$param){
        // 只对Admin模块起作用
        if('Admin' == MODULE_NAME){        
            $path = MODULE_NAME."/".CONTROLLER_NAME."/".ACTION_NAME;
            $this->check_vender_reviewed($path);    
        }       
    }

    /**
     * 检测经销商是否通过审核，未通过审核不允许登录
     */
    protected function check_vender_reviewed($path)
    {
        // 过滤通用的请求路由方法
        $except = array(
            'admin/login/index',
            'admin/login/yzm',
            'admin/login/logout'
        );
        $path = strtolower($path);

        // 过滤通用路由
        if(!in_array($path,$except)){
            // 判断经销商等级不是超级管理员，并且经销商状态为通过审核
            if((int)$_SESSION['adminInfo']['leavel'] !== 0 && (int)$_SESSION['adminInfo']['status'] !== 7) {
                // var_dump(session());die;
                // 没通过审核退出登录
                redirect(U('Admin/Login/logout'),3,'您还没通过审核！！！');
            }
        }
    }
}