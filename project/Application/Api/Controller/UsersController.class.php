<?php
namespace Api\Controller;
use Think\Controller;

use Aliyun\Core\Config; 
use Aliyun\Core\Profile\DefaultProfile; 
use Aliyun\Core\DefaultAcsClient; 
use Aliyun\Api\Sms\Request\V20170525\SendSmsRequest; 

/**
 * PC端用户接口类
 * author:梁康伦
 * 时间：2018-05-03
 */
class UsersController extends Controller
{
    /**
     * [register 用户手机注册验证]
     * @return [type] [description]
     */
    public function register()
    {
        if (IS_POST) {
            //接收前端的手机号码
            $phone = $_POST['phone'];

            $info = D('users')->where('phone='.$phone)->find();

            if ($info) {
                //存在返回接口信息
                $this->ajaxReturn(array('msg'=>'该手机号已被注册','code'=>'201'));
            } else {
                //不存在
                $this->ajaxReturn(array('msg'=>'该手机号可使用','code'=>'200'));
                
            }
        } else {
            $this->ajaxReturn(array('msg'=>'请求方式有误','code'=>'201'));

        }

    }
    
    /**
     * [sendMsg 发送短信]
     * @return [type] [description]
     */
    public function sendMsg()
    {
        $mobile = $_POST['phone'];
        require_once LIB_PATH.'Org/Util/Aliyun/vendor/autoload.php';    //此处为你放置API的路径
        Config::load();             //加载区域结点配置
        $accessKeyId = 'LTAIlQ8P0pthE4ik';
        $accessKeySecret = 'ejXfcVd0T2N0Au4VrMo9IM5kHcVzaR';
        $templateCode = '82565004';   //短信模板ID
        //短信API产品名（短信产品名固定，无需修改）
        $product = "Dysmsapi";
        //短信API产品域名（接口地址固定，无需修改）
        $domain = "dysmsapi.aliyuncs.com";
        //暂时不支持多Region（目前仅支持cn-hangzhou请勿修改）
        $region = "cn-hangzhou";
        // 初始化用户Profile实例
        $profile = DefaultProfile::getProfile($region, $accessKeyId, $accessKeySecret);
        // 增加服务结点
        DefaultProfile::addEndpoint("cn-hangzhou", "cn-hangzhou", $product, $domain);
        // 初始化AcsClient用于发起请求
        $acsClient = new DefaultAcsClient($profile);
        // 初始化SendSmsRequest实例用于设置发送短信的参数
        $request = new SendSmsRequest();
        // 必填，设置短信接收号码
        $request->setPhoneNumbers($mobile);    //$moblie是我前台传入的电话
        // 必填，设置签名名称
        $request->setSignName("家惠联");      //此处需要填写你在阿里上创建的签名
        // 必填，设置模板CODE
        $request->setTemplateCode("SMS_133978913");    //短信模板编号

        $code = $this->rand_string();

        $smsData = array('code'=>$code);    //所使用的模板若有变量 在这里填入变量的值  我的变量名为username此处也为username
        $request->setTemplateParam(json_encode($smsData));    
        //发起访问请求
        $acsResponse = $acsClient->getAcsResponse($request);
        //返回请求结果
        $result = json_decode(json_encode($acsResponse), true);

        // dump($result);
        if ($result['Message'] == 'OK') {
            $this->set('code', $code);
            // dump($_SESSION);
            $this->ajaxReturn(array('msg'=>'短信已发送到您手机，请注意查收','code'=>'200'));
        } else {
            $this->ajaxReturn(array('msg'=>'短信请求次数超过当天请求次数，请改天再来','code'=>'201'));
        }
    }

    /**
     * [confirmMsg 接受手机验证码确认]
     * @return [type] [description]
     */
    public function  confirmMsg() 
    {
        $msgCode = $_POST['code'];
        $sessCode = $this->get('code');

        // dump($sessCode);die;
        
        if (!empty($sessCode)) {
            if ($msgCode == $sessCode) {
                //验证码通过后将该用户手机号写入数据库
                $data['phone'] = $_POST['phone'];
                if (M('users')->add($data)) {
                    $_SESSION['user']['phone'] = $_POST['phone'];
                    $this->ajaxReturn(array('msg'=>'注册成功','code'=>'200'));
                } else {
                    $this->ajaxReturn(array('msg'=>'注册失败','code'=>'201'));
                }
         
            } else {
                $this->ajaxReturn(array('msg'=>'验证码错误，请重新输入','code'=>'201'));
            }
        } else {
            $this->ajaxReturn(array('msg'=>'验证码已过期','code'=>'201'));
        }
        

    }

    /**
     * [userUpdate 注册成功后完善信息]
     * @return [type] [description]
     */
    public function userUpdate()
    {
        
        $data['password'] = md5($_POST['password']);
        $data['pcname'] = $_POST['pcname'];
        $repass = md5($_POST['repassword']);

        if ($data['password'] == $repass) {
            if (M('users')->where('phone='.$_SESSION['user']['phone'])->save($data)) {
                $info = M('users')->where("phone=".$_SESSION['user']['phone'])->find();
                $_SESSION['user'] = $info;
                $this->ajaxReturn(array('msg'=>'信息完善完成','code'=>'200'));
            } else {
                $this->ajaxReturn(array('msg'=>'信息完善失败','code'=>'201'));
            }
        }else {
            $this->ajaxReturn(array('msg'=>'两次密码不一样','code'=>'201'));

        }
    }

    /**
     * [checkPcname 验证用户名是否存在]
     * @return [type] [description]
     */
    public function checkPcname()
    {
        $pcname = $_POST['pcname'];

        $info = M('users')->where("pcname='{$pcname}'")->find();

        if ($info) {
            $data[] = $pcname.$this->rand_string(3);
            $data[] = $pcname.$this->rand_string(3);
            $data[] = $pcname.$this->rand_string(3);
            $this->ajaxReturn(array('msg'=>$data,'code'=>'201'));
        } else {
            $this->ajaxReturn(array('msg'=>'该用户名可用','code'=>'200'));
        }
    }

    /**
     * [login 用户登录]
     * @return [type] [description]
     */
    public function login()
    {
        //接受前端用户手机号或者用户名
        $name = $_POST['name'];

        $password = md5($_POST['password']);

        $user = M('users');

        $info = $user->where("phone='{$name}' or pcname='{$name}'")->find();

        if ($info) {
            if ($info['password'] == $password) {
                $_SESSION['user'] = $info;
                $this->ajaxReturn(array('msg'=>'登录成功','code'=>'200'));

            } else {

                $this->ajaxReturn(array('msg'=>'密码错误','code'=>'201'));

            }
        } else {
            $this->ajaxReturn(array('msg'=>'该账号未注册','code'=>'201'));
        }


    }

    /**
     * [logout 退出登录]
     * @return [type] [description]
     */
    public function logout()
    {
        unset($_SESSION['user']);
        unset($_SESSION['weixin']);
        $this->ajaxReturn(array('msg'=>'退出成功','code'=>'200'));

    }


    /**
     * [userInfo 会员中心信息]
     * @return [type] [description]
     */
    public function userInfo()
    {
        // 准备查询条件
        $showUser['open_id'] = $_SESSION['open_id'];
        // // 执行查询
        $user = M('users')->where($showUser)->find();

        // 查询会员收益合计
        $showCommission['user_code'] = $user['code'];
        $commission = M('users_commission')->where($showCommission)->select();
        // dump($commission);die;

        $comData['gold_num'] = 0;
        $comData['silver'] = 0;
        foreach ($commission as $key => $value) {
            $comData['gold_num'] += $value['gold_num'];
            $comData['silver'] += $value['silver'];
        }

        $code = session('user.code');
        // 获取用户唯一标识
        $uWhere['c.user_code'] = array('EQ',$code);
        $uWhere['c.nexus_user'] = array('NEQ',$code);
    
        $orderNum = 0;
        $orderData = M('users_commission')
            ->alias('c')
            ->where($uWhere)->field('c.id')
            ->select();
        $orderNum = count($orderData);
        // 分配数据

        $assign = array(
            array('user',$user),
            array('comData',$comData),
            array('orderNum',$orderNum),
        );
        $this->ajaxReturn($assign);
        

    }

    /***************************************************辅助方法*************************************************/
    //设置session有效期
    public function set($name, $data, $expire=600){  
            $session_data = array();  
            $session_data['data'] = $data;  
            $session_data['expire'] = time()+$expire;  
            $_SESSION[$name] = $session_data;  
        }  
      
    /** 
     * 读取session 
     * @param  String $name  session name 
     * @return Mixed 
     */  
    public function get($name){  
        if(isset($_SESSION[$name])){  
            if($_SESSION[$name]['expire']>time()){  
                return $_SESSION[$name]['data'];  
            }else{  
                $this->clear($name);  
            }  
        }  
        return false;  
    }

    /** 
     * 清除session 
     * @param  String  $name  session name 
     */  
    public function clear($name){  
        unset($_SESSION[$name]);  
    } 


    /**
     * 获取随机位数数字，用于生成短信验证码
     * @param  integer $len 长度
     * @return string
     */
    protected  function rand_string($len = 6){
        $chars = str_repeat('0123456789', $len);
        $chars = str_shuffle($chars);
        $str   = substr($chars, 0, $len);
        return $str;
    }   


}