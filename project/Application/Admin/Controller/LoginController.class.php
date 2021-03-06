<?php
namespace Admin\Controller;
use Think\Controller;

class LoginController extends Controller
{   
    // 登录方法
    public function index()
    {
        if (IS_POST) {
            // 验证验证码是否OK
            $Verify = new \Think\Verify();
            $res = $Verify->check($_POST['code']);
            if(!$res) $this->ajaxReturn(array('msg'=>'验证码不对','code'=>'201'));

            $password = md5($_POST['password']);
            $info = M('adminUser')->where("user='{$_POST['name']}'")->find();

            if($info){
                if ($info['password'] == $password) {
                    // 万事大吉
                    $_SESSION['adminInfo'] = $info;

                    $data['logintime'] = time();
                    M('adminUser')->where("user='{$_POST['name']}'")->save($data);
                    $this->ajaxReturn(array('msg'=>'登录成功','code'=>'200'));
                    // $this->redirect('Index/index');
                }else{
                    $this->ajaxReturn(array('msg'=>'账号或密码错误！','code'=>'201'));
                }
            }else{
                $this->ajaxReturn(array('msg'=>'您输入的用户名不存在！','code'=>'201'));
            }

        }else{
            if($_SESSION['adminInfo']){$this->redirect('Index/index');}
            $this->display();
        }
    }

    // 验证码方法
    public function yzm()
    {
        $config = array(
            'font-Size' =>   30, //验证码大学
            'length'    =>   4,  //验证码个数
            'useNoise'  =>   false, //关闭验证码杂点
        );
        $Verify = new \Think\Verify($config);
        $Verify->entry();
    }


    public function logout()
    {
        unset($_SESSION['adminInfo']);
        $this->success('正在退出登录......',U('Admin/login/index'));
    }

}
