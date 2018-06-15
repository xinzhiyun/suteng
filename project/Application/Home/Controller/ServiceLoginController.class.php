<?php
namespace Home\Controller;
use Common\Tool\Weixin;
use Think\Controller;

class ServiceLoginController extends Controller
{   
    // 登录方法
    public function index()
    {
        if (IS_POST) {
            try{
                if(empty($_POST['code']))E('验证码不能为空',40001);
                $Verify = new \Think\Verify();
                $res = $Verify->check($_POST['code']);
                if(!$res) E('验证码不对',40002);

                $password = md5($_POST['password']);
                $info = M('admin_user')
                    ->alias('au')
                    ->where("user='{$_POST['name']}'")
                    ->join('__SERVICE__ s ON s.auid=au.id', 'LEFT')
                    ->field('au.password,au.user,s.id,s.company,s.auid')
                    ->find();

                if($info){
                    if ($info['password'] == $password) {
                        unset($info['password']);
                        // 万事大吉
                        $_SESSION['serviceInfo'] = $info;
                        E('登录成功',200);// 主页
                    }else{
                        E('您的密码输入错误!',40002);
                    }
                }else{
                    E('您输入的用户名不存在!',40002);
                }

            } catch (\Exception $e) {
                $this->toJson($e);
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

    // 安装人员登录
    public function peopleIndex()
    {
        $this->display();
    }
    public function peopleLogin()
    {
        try{
            if(empty($_POST['code']))E('验证码不能为空',40001);
            $Verify = new \Think\Verify();
            $res = $Verify->check($_POST['code']);
            if(!$res) E('验证码不对',40002);

            $password = md5($_POST['password']);
            $info = M('service_users')
                ->where("phone='{$_POST['name']}'")
                ->find();

            if($info){
                if ($info['password'] == $password) {
                    unset($info['password']);
                    // 万事大吉
                    $_SESSION['servicepeople'] = $info;
                    E('登录成功',200);// 主页
                }else{
                    E('您的密码输入错误!',40002);
                }
            }else{
                E('您输入的用户名不存在!',40002);
            }

        } catch (\Exception $e) {
            $this->toJson($e);
        }
    }

    public function logout()
    {
        unset($_SESSION['vendorInfo']);
        $this->redirect('ServiceLogin/index');
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



//    // 服务站申请
//    public function apply()
//    {
//
//    }

    // 服务站申请 -获取未开通的服务站
    /**
     * 获取服务站
     */
    public function getService()
    {
        try {
            $data = I('post.');
            if(!empty($data['province_id'])){
                $map['province_id'] = $data['province_id'];
            }
            if(!empty($data['city_id'])){
                $map['city_id'] = $data['city_id'];
            }
            if(!empty($data['district_id'])){
                $map['district_id'] = $data['district_id'];
            }

            $phone = '13532643349';


            $map['status'] = ['neq',1];
            $count = M('service')->where($map)->count();
            if(empty($count)){
                $this->toJson(['data'=>[],'phone'=>$phone],'无数据,请重试!',40001);
            }
            $Page       = new \Think\Page($count,15);
            $data = M('service')->where($map)
                ->limit($Page->firstRow.','.$Page->listRows)
                ->select();

            $this->toJson(['data'=>$data,'phone'=>$phone],'获取成功!',200);

        } catch (\Exception $e) {
            $this->toJson($e);
        }
    }






}
