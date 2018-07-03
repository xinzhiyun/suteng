<?php
namespace Admin\Controller;
use Think\Controller;

/**
 * 配置管理控制器
 * 
 * @author 潘宏钢 <619328391@qq.com>
 */

class ConfigController extends CommonController 
{
    /**
     * 微信配置方法
     *
     * @author 潘宏钢 <619328391@qq.com>
     */
    public function wx_config()
    {
        $auid = $_SESSION['adminInfo']['id'];
        $system_config = M('system_config');

        if (IS_POST) {

            $res = $system_config->where('auid='.$auid)->save($_POST);
            if ($res) {
                $this->toJson([],'修改成功',200);
            } else {
                $this->toJson([],'修改失败',40002);
            }
            
        }else{

            $info = $system_config->where('auid='.$auid)->find();

            $this->assign('data',$info);
            $this->display();
        }

    }

}