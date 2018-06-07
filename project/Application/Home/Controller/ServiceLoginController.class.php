<?php
namespace Home\Controller;
use Think\Controller;

class ServiceLoginController extends Controller
{   
    // 登录方法
    public function index()
    {
        if (IS_POST) {

            $password = md5($_POST['password']);
            $info = M('admin_user')
                ->alias('au')
                ->where("user='{$_POST['name']}'")
                ->join('__SERVICE__ s ON s.auid=au.id', 'LEFT')
                ->field('s.id,s.company')
                ->find();

            if($info){
                if ($info['password'] == $password) {
                    unset($info['password']);

                    // 万事大吉
                    $_SESSION['serviceInfo'] = $info;
                    // 主页
                    $this->redirect('Service/index');
                }else{
                    $this->error('您的密码输入错误！');
                }
            }else{
                $this->error('您输入的用户名不存在！');
            }

        }else{

            // 如果用户已经登录
            if(!empty($_SESSION['serviceInfo']['open_id'])  && $_SESSION['vendorInfo']['status']==7){
                // 主页
                //$this->redirect('Service/index');
            }else{
                $this->display();
            }
        }
    }

    // 验证码方法
    public function yzm()
    {
        $config = array(
            'font-Size' =>   30, //验证码大小
            'length'    =>   4,  //验证码个数
            'useNoise'  =>   false, //关闭验证码杂点
        );
        $Verify = new \Think\Verify($config);
        $Verify->entry();
    }


    public function logout()
    {
        unset($_SESSION['vendorInfo']);
        $this->redirect('ServiceLogin/index');
    }

}
