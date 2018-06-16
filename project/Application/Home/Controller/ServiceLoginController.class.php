<?php
namespace Home\Controller;
use Common\Tool\File;
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

    // 服务站注册
    public function register()
    {
        if( empty($_SESSION['open_id']) ){
            $_SESSION['open_id'] = Weixin::GetOpenid();
        }
        $weixin = new \Org\Util\WeixinJssdk();
        $signPackage = $weixin->getSignPackage();
        $phone = M('service_seting')->cache('service_kfphone',60)->where(1)->getField('kfphone');

        $info = M('service_apply')->where(['open_id'=>$_SESSION['open_id'], 'status'=>0])->find();

//        if( !empty($info['business']) && is_json($info['business'])) {
//            $info['business'] = json_decode($info['business']);
//        }
//        if( !empty($info['agreement']) && is_json($info['agreement'])) {
//            $info['agreement'] = json_decode($info['agreement']);
//        }

        $this->assign('info',$info);
        $this->assign('kfphone',$phone);
        $this->assign('wxinfo',$signPackage);
        $this->display();
    }

    // 地址加载
    public function getNextArea()
    {
        $parentid = I('parentid',0);
        $data = M('area')->where('parentid='.$parentid)->select();

        $this->toJson(['data'=>$data],'获取成功');
    }

    // 服务站申请-提交
    public function apply()
    {
        try {
            $post = I('post.');
            if(!empty($post['type'])){ ///提交审核
                if(empty($post['sid'])){
                    E('请选择服务站',40001);
                }

                $service_status = M('service')->where('id='.$post['sid'])->getField('status');
                if($service_status==1){
                    E('该服务站已经被开通',40002);
                }

                if( empty($post['name']) || empty($post['phone']) || empty($post['sex']) || empty($post['idcard']) ){
                    E('请确认身份信息!',40003);
                }

                if( empty($post['province']) || empty($post['city']) || empty($post['district']) || empty($post['addressinfo']) ){
                    E('请确认地址信息!',40004);
                }

                if( empty($post['account']) || empty($post['password'])  || empty($post['repassword'])){
                    E('请确认账号信息!',40005);
                }

                if( $post['password'] != $post['repassword'] ){
                    E('请确认账号信息!',40006);
                }

                if( empty($post['telephone']) ){
                    E('请确认客服电话!',40007);
                }

                if( empty($post['company']) || empty($post['legal']) || empty($post['business']) || empty($post['agreement']) ){
                    E('请确认申请资质信息!',40008);
                }
                $post['password'] = md5($post['password']);

                $post['open_id'] = $_SESSION['open_id'];
                $post['status'] = 0;

                if( !empty($post['business']) && is_array($post['business'])) {
                    $post['business'] = json_encode($post['business']);
                }
                if( !empty($post['agreement']) && is_array($post['agreement'])) {
                    $post['agreement'] = json_encode($post['agreement']);
                }


                $res = M('service_apply')->add($post);
                if($res){
                    E('您的申请已提交,请等待工作人员审核',200);
                }else{
                    E('提交失败,请联系工作人员!',40010);
                }
            }


            if( !empty($post['business']) && is_array($post['business'])) {
                $post['business'] = json_encode($post['business']);
            }
            if( !empty($post['agreement']) && is_array($post['agreement'])) {
                $post['agreement'] = json_encode($post['agreement']);
            }
            $post['updatetime'] = time();


            $info = M('service_apply')->where(['open_id'=>$_SESSION['open_id'], 'status'=>0])->find();
            if(empty($info)){
                $res = M('service_apply')->add($post);
            }else{
                $res = M('service_apply')->where('id='.$info['id'])->save($post);
            }
            if($res){
                E('您的申请已提交,请等待工作人员审核',200);
            }else{
                E('提交失败,请联系工作人员!',40010);
            }



        } catch (\Exception $e) {
            $this->toJson($e);
        }
    }

    // 支付押金
    public function registerPay()
    {
        if( empty($_SESSION['open_id']) ){
//            $_SESSION['open_id'] = Weixin::GetOpenid();
        }

        $map=[
            'open_id'=>$_SESSION['open_id'],
            'status'=>1,
        ];
        $res = M('service_apply')->where($map)->getField('id,servicename,name,company,legal,phone');
        if(empty($res)){
            notice('请等待审核!','finalTip');
        }

//        $weixin = new \Org\Util\WeixinJssdk();
//        $signPackage = $weixin->getSignPackage();
        $joinsost = M('service_seting')->where(1)->getField('joinsost');
        $joinsost = number_format(intval(trim($joinsost), 10)/100,0,'.','');

        $this->assign('serviceInfo',$res);
        $this->assign('joinsost',$joinsost);
        $this->assign('wxinfo',$signPackage);
        $this->display();
    }

    // 缴费
    public function registerPayOrder()
    {
        try {
            $post = I('post.');
            if(empty($post['paytype'])){
                E('参数错误',40001);
            }
            $money = M('service_seting')->where(1)->getField('joinsost');
            $orderId = getOrderId();
            $content = '速腾服务站加盟费';
            $url = 'http://'.$_SERVER['SERVER_NAME'].U('Home/WeiXinPay/setmealNotify');

            Weixin::uniformOrder($_SESSION['open_id'],$money,$orderId,$content,$url);

        }catch (\Exception $e) {
            $this->toJson($e);
        }
    }


    /**
     * 服务站申请 -获取未开通的服务站
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

            $map['status'] = ['neq',1];
            $count = M('service')->where($map)->count();
            if(empty($count)){
                $this->toJson(['data'=>[]],'无数据,请重试!',200);
            }
            $Page       = new \Think\Page($count,15);
            $data = M('service')->where($map)
                ->limit($Page->firstRow.','.$Page->listRows)
                ->select();

            $this->toJson(['data'=>$data],'获取成功!',200);

        } catch (\Exception $e) {
            $this->toJson($e);
        }
    }


    // 图片上传
    public function picUpload()
    {
        $pic = I('pic');
        if(!empty($pic)){
            if(is_string($pic)){
                $pic = explode(',',$pic);
            }
            if( is_array($pic) ){
                foreach ($pic as $p) {
                    $pics[] = File::downloadPic('service_apply',$p);
                }
            }
            $data['pic'] = json_encode($pics);
            $this->toJson(['data'=>$data],'上传成功!',200);
        }else{
            $this->toJson(['data'=>[]],'无图片上传!',400001);
        }
    }



}
