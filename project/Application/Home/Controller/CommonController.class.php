<?php
namespace Home\Controller;
use Think\Controller;
use \Org\Util\WeixinJssdk;
use Home\Controller\WechatController;
/**
 * 前共控制器
 * 前台控制器除login外必须继承我
 * @author 吴智彬 <519002008@qq.com>
 */

class CommonController extends Controller
{
    /**
     * 初始化
     * @author 吴智彬 <519002008@qq.com>
     */
    public function _initialize()
    {
        // $_SESSION = null;
        // 实例化微信JSSDK
        $weixin = new WeixinJssdk;

        // 前端调试通道
        // $_SESSION['open_id'] = C('open_id');
//        $_SESSION['open_id'] = 'onLe70SfHSwyjUrqtIgt4MGN7mI8';
        // dump($_SESSION);exit;
        // 获取用户open_id
        if(empty($_SESSION['open_id'])){

            // 如果不存在则，跳转获取open_id,并缓存
            $_SESSION['open_id'] = $weixin->GetOpenid();
        }

        // 获取用户open_id
        $showData['open_id'] = $_SESSION['open_id'];


        // 查询微信信息表
        $wechat = M('wechat')->where($showData)->find();

        // dump($wechat);die;
        // 接收类型
        $type   = $wechat["type"];
        // 接收操作
        $action = $wechat["action"];
        // 查询用户信息
        $info = M('users')->where(['open_id'=>$showData['open_id']])->find();


        // 判断用户是否存在
        if($info){

            // 用户当前设备
            $info['did'] = M('user_device')->where(['uid'=>$info['id'],'status'=>1])->getField('did');
            session('user',$info);
        }


        switch ($type) {
            case '0':
                // 会员通道
                switch ($action) {
                    case '0':
                        // 会员资料填写注册类型{0:会员直接注册 1:会员推荐会员 2：分销商推荐会员 3：分公司推荐会员}
                        // 邀请人类型:{0：分公司，1：A级分销，2：B级分销，3：C级分销，4：会员 5：普通二维码}
                        // 会员等级{0：非企业会员，1：普通会员，2：VIP会员，3：标准会员，4：钻石会员}
                        switch ($wechat['recommend']) {
                            case '0':
                                // 会员扫不带参数二维码注册（公众号普通二维码）
                                // 邀请人类型
                                $newData['invite']          = 5;
                                // 获取用户唯一标识
                                $newData['code']            = $this->user_code();
                                // 添加时间
                                $newData['addtime']         = time();
                                // 修改时间
                                $newData['updatetime']      = $newData['addtime'];
                                // 获取微信用户基本信息
                                $weixinData = $this->getWeiXinUserInfo($_SESSION['open_id']);
                                // 获取用户推荐二维码
                                // 数据对象创建成功,实例化二维码信息类
                                $dimensionClass         = new DimensionController;
                                // 获取分销商二维码
                                $ticket                 = $dimensionClass->user();
                                // dump($ticket);die;
                                // 将微信用户信息和二维码票据合并人新数组
                                $newData    = array_merge($newData,$weixinData,$ticket);

                                // 添加会员级别
                                $newData['grade'] = 0;
                                $newData['original_grade'] = 0;
                                // 创建微信用户信息
                                $userRes = M('users')->add($newData);

                                if($userRes){
                                    // 更新条件
                                    $userWhere['open_id'] = $_SESSION['open_id'];
                                    // 更新数据
                                    $userData['action']   = 1;
                                    // 执行更新操作，将微信分销商操作该为登录状态
                                    $wechatRes = M('wechat')->where($userWhere)->save($userData);
                                }
                                break;
                            case '1':
                                // 会员扫会员推荐二维码注册
                                // 准备查询用户条件
                                $showUserData['ticket'] = $wechat['invitation_ticket'];
                                // 查询用户
                                $userdata = M('users')->where($showUserData)->find();
                                // 准备用户表数据
                                $newData['office_code']     = $userdata['office_code'];
                                // 邀请人
                                $newData['invitation_code'] = $userdata['code'];
                                // A级分销商
                                $newData['vendora_code']    = $userdata['vendora_code'];
                                // B级分销商
                                $newData['vendorb_code']    = $userdata['vendorb_code'];
                                // C级分销商
                                $newData['vendorc_code']    = $userdata['vendorc_code'];
                                // 邀请人类型
                                $newData['invite']          = 4;
                                // 分销商邀请人
                                $newData['vendori_code']    = $userdata['vendori_code'];
                                // 获取用户唯一标识
                                $newData['code']            = $this->user_code();
                                // 添加时间
                                $newData['addtime']         = time();
                                // 修改时间
                                $newData['updatetime']      = $newData['addtime'];
                                //类别
                                $newData['type'] = 2;
                                if ($userdata['path']) {

                                    $newData['path']      = $userdata['path'].'-'.$userdata['id'];
                                } else {

                                    $newData['path']      = $userdata['id'];
                                }

                                // 获取微信用户基本信息
                                $weixinData = $this->getWeiXinUserInfo($_SESSION['open_id']);
                                // 获取用户推荐二维码
                                // 数据对象创建成功,实例化二维码信息类
                                $dimensionClass         = new DimensionController;
                                // 获取分销商二维码
                                $ticket                 = $dimensionClass->user();
                                // 将微信用户信息和二维码票据合并人新数组
                                $newData    = array_merge($newData,$weixinData,$ticket);
                                // 添加会员级别
                                $newData['grade'] = 1;
                                $newData['original_grade'] = 1;
                                //dump($newData);die;
                                // 创建微信用户信息
                                $userRes = M('users')->add($newData);

                                if($userRes){
                                    // 更新条件
                                    $userWhere['open_id'] = $_SESSION['open_id'];
                                    // 更新数据
                                    $userData['action']   = 1;
                                    // 执行更新操作，将微信分销商操作该为登录状态
                                    $wechatRes = M('wechat')->where($userWhere)->save($userData);
                                }


                                break;
                            case '2':
                                // 会员扫分销商推荐二维码注册
                                // 准备分销商查询条件
                                $showoffice['vendor_user'] = $wechat['invitation_ticket'];
                                // 查询分销商
                                $vendor = M('vendors')->where($showoffice)->find();
                                $id =  M('users')->field('id,path')->where(['open_id'=>$vendor['open_id']])->find();

                                // 获取分销商级别
                                $leavel = $vendor['leavel'];
                                //dump($leavel);die;
                                switch ($leavel) {
                                    case '2':
                                        // A级分销商邀请会员
                                        // 准备用户表数据
                                        $newData['office_code']     = $vendor['office_code'];
                                        // 邀请人
                                        $newData['invitation_code'] = $vendor['code'];
                                        // A级分销商
                                        $newData['vendora_code']    = $vendor['code'];
                                        // B级分销商
                                        $newData['vendorb_code']    = $vendor['code'];
                                        // C级分销商
                                        $newData['vendorc_code']    = $vendor['code'];
                                        // 邀请人类型
                                        $newData['invite']          = 1;
                                        // 分销商邀请人
                                        $newData['vendori_code']    = $vendor['invitation_code'];
                                        if ($id['path']) {
                                            $newData['path']      = $id['path'].'-'.$id['id'];
                                        } else {
                                            $newData['path']      = $id['id'];
                                        }
                                        //类别
                                        $newData['type'] = 2;

                                        break;
                                    case '3':
                                        // B级分销商邀请会员
                                        // 准备用户表数据
                                        $newData['office_code']     = $vendor['office_code'];
                                        // 邀请人
                                        $newData['invitation_code'] = $vendor['code'];
                                        // A级分销商
                                        $newData['vendora_code']    = $vendor['superior_code'];
                                        // B级分销商
                                        $newData['vendorb_code']    = $vendor['code'];
                                        // C级分销商
                                        $newData['vendorc_code']    = $vendor['code'];
                                        // 邀请人类型
                                        $newData['invite']          = 2;
                                        if ($id['path']) {
                                            $newData['path']      = $id['path'].'-'.$id['id'];
                                        } else {
                                            $newData['path']      = $id['id'];
                                        }
                                        //类别
                                        $newData['type'] = 2;
                                        // 分销商邀请人
                                        $newData['vendori_code']    = $vendor['invitation_code'];
                                        break;
                                    case '4':
                                        // c级分销商邀请会员
                                        // 准备用户表数据
                                        $newData['office_code']     = $vendor['office_code'];
                                        // 邀请人
                                        $newData['invitation_code'] = $vendor['code'];
                                        // A级分销商
                                        $newData['vendora_code']    = $vendor['superiors_code'];
                                        // B级分销商
                                        $newData['vendorb_code']    = $vendor['superior_code'];
                                        // C级分销商
                                        $newData['vendorc_code']    = $vendor['code'];
                                        // 邀请人类型
                                        $newData['invite']          = 3;
                                        // 分销商邀请人
                                        $newData['vendori_code']    = $vendor['invitation_code'];
                                        if ($id['path']) {
                                            $newData['path']      = $id['path'].'-'.$id['id'];
                                        } else {
                                            $newData['path']      = $id['id'];
                                        }
                                        //类别
                                        $newData['type'] = 2;
                                        break;
                                    default:
                                        # code...
                                        break;
                                }


                                // 获取用户唯一标识
                                $newData['code']            = $this->user_code();
                                // 添加时间
                                $newData['addtime']         = time();
                                // 修改时间
                                $newData['updatetime']      = $newData['addtime'];
                                // 获取微信用户基本信息
                                $weixinData = $this->getWeiXinUserInfo($_SESSION['open_id']);
                                // 获取用户推荐二维码
                                // 数据对象创建成功,实例化二维码信息类
                                $dimensionClass         = new DimensionController;
                                // 获取分销商二维码
                                $ticket                 = $dimensionClass->user();
                                // 将微信用户信息和二维码票据合并人新数组
                                $newData    = array_merge($newData,$weixinData,$ticket);
                                // 添加会员级别
                                $newData['grade'] = 1;
                                //dump($newData);die;
                                // 创建微信用户信息
                                $userRes = M('users')->add($newData);

                                if($userRes){

                                    // 更新条件
                                    $userWhere['open_id'] = $_SESSION['open_id'];
                                    // 更新数据
                                    $userData['action']   = 1;
                                    // 执行更新操作，将微信分销商操作该为登录状态
                                    $wechatRes = M('wechat')->where($userWhere)->save($userData);
                                }
                                break;
                            case '3':
                                // 会员扫分公司推荐二维码注册
                                // 准备分公司查询条件
                                $showoffice['vendor_user'] = $wechat['invitation_ticket'];
                                // 查询分公司信息
                                $office = M('vendors')->where($showoffice)->find();
                                // 准备用户表数据
                                $newData['office_code']     = $office['code'];
                                // 邀请人
                                $newData['invitation_code'] = $office['code'];
                                // 邀请人类型
                                $newData['invite']          = 0;
                                // 获取用户唯一标识
                                $newData['code']            = $this->user_code();
                                // 添加时间
                                $newData['addtime']         = time();
                                // 修改时间
                                $newData['updatetime']      = $newData['addtime'];
                                // 获取微信用户基本信息
                                $weixinData = $this->getWeiXinUserInfo($_SESSION['open_id']);
                                // 获取用户推荐二维码
                                // 数据对象创建成功,实例化二维码信息类
                                $dimensionClass         = new DimensionController;
                                // 获取分销商二维码
                                $ticket                 = $dimensionClass->user();
                                // 将微信用户信息和二维码票据合并人新数组
                                $newData    = array_merge($newData,$weixinData,$ticket);
                                // 添加会员级别
                                $newData['grade'] = 0;
                                $newData['original_grade'] = 0;
                                // 创建微信用户信息
                                $userRes = M('users')->add($newData);

                                if($userRes){

                                    // 更新条件
                                    $userWhere['open_id'] = $_SESSION['open_id'];
                                    // 更新数据
                                    $userData['action']   = 1;
                                    // 执行更新操作，将微信分销商操作该为登录状态
                                    $wechatRes = M('wechat')->where($userWhere)->save($userData);
                                }
                                break;
                            default:
                                # code...
                                break;
                        }
                        // dump($wechat);
                        break;
                    case '1':
                        // 会员登录
                        if(empty($_SESSION['user'])){
                            // 更新条件
                            $userWhere['open_id'] = $_SESSION['open_id'];
                            // 查询用户信息,并缓存
                            $_SESSION['user'] = M('users')->where($userWhere)->find();

                            if(empty(session('device.did'))){
                                $this->redirect("Device/index");
                            }
                        }

                        break;
                    default:
                        break;
                }
                break;
            case '1':
                // dump($_SESSION['open_id']);die;
                // 分销通道
                // $this->redirect('RegisteredVendor/index');
                // if(empty($_SESSION['user'])){
                // 更新条件
                $userWhere['open_id'] = $_SESSION['open_id'];
                // 查询用户表
                $user = M('users')->where($userWhere)->find();

                if($user){
                    $_SESSION['user'] = $user;
                }else{
                    // 查询用户信息,并缓存
                    $vendor = M('vendors')->where($userWhere)->find();
                    $userdata = M('users')->where(['code'=>$vendor['invitation_code']])->find();
                    // 创建分销商前台用户
                    $addData['open_id'] = $_SESSION['open_id'];
                    // 分公司
                    $addData['office_code']     = $vendor['office_code'];
                    //
                    $addData['invitation_code'] = $vendor['invitation_code'];
                    // A级分销商
                    $addData['vendora_code']    = 0;
                    // B级分销商
                    $addData['vendora_code']    = 0;
                    // C级分销商
                    $addData['vendora_code']    = 0;
                    // 分销商邀请人
                    $addData['vendori_code']    = 0;
                    // 分销商唯一标识
                    $addData['code']            = $vendor['code'];
                    // 邀请人类型
                    $addData['invite']          = 0;
                    // 分销商会员邀请码
                    $addData['ticket']          = 0;
                    // 请求二维码的参数
                    $addData['parameter']       = 0;
                    // 二维码有效时间
                    $addData['ticket_time']     = 0;
                    // 分销商名称
                    $addData['nickname']        = $vendor['name'];
                    // 分销商头像
                    $addData['head']            = $vendor['head'];
                    // 更新时间
                    $addData['addtime'] = $addData['updatetime'] = time();
                    $addData['type'] = 1;
                    if ($userdata['path']) {

                        $addData['path']      = $userdata['path'].'-'.$userdata['id'];
                    } else {

                        $addData['path']      = $userdata['id'];
                    }
                    // 分销商会员级别
                    if ($vendor['status'] == 7) {
                        $addData['grade']            = 4;
                        $addData['original_grade'] = 4;
                    } else {
                        $addData['grade']            = 1;
                        $addData['original_grade'] = 1;
                    }


                    // 创建用户
                    $addRes = M('users')->add($addData);

                    // 如果添加成功
                    if($addRes){
                        // 去主页
                        $this->redirect("Home/Index/index");
                    }
                }

                // dump($_SESSION);die;

                // if(empty(session('device.id'))){
                //     $this->redirect("Device/index");
                // }
                // }
                break;
            default:

                // 未关注
                // $this->follow();
                $this->redirect('Home/Wechat/follow');
                break;
        }

    }

    // 获取微信信息
    public function getWeiXinUserInfo($openid)
    {
        // 实例化微信JSSDK类对象
        $wxJSSDK = new \Org\Util\WeixinJssdk;
        // 调用获取公众号的全局唯一接口调用凭据
        $accessToken = $wxJSSDK->getAccessToken();
        //show($accessToken);die;
        // 请求返回简体中文版用户信息数据
        $url = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$accessToken.'&openid='.$openid.'&lang=zh_CN';

        // 发送请求获取用户信息
        $userInfo = $wxJSSDK->httpGet($url);

        // 把 JSON 格式的字符串转换为PHP数组
        $userInfo = json_decode($userInfo, true);

        // 准备微信信息表数据
        // 微信的ID号
        $data['open_id'] = $userInfo['openid'];
        $data['nickname'] = $userInfo['nickname'];
        $data['head'] = $userInfo['headimgurl'];
        // 性别{0:女, 1:男, 2:保密}
        $data['sex'] = $userInfo['sex'];
        switch ($userInfo['sex']) {
            case '0':
                $data['sex'] = 2;
                break;
            case '1':
                $data['sex'] = 1;
                break;
            case '2':
                $data['sex'] = 0;
                break;
            default:
                # code...
                break;
        }
        // 地区 省份
        $data['area'] = $userInfo['province'];
        // 国家 省份 市区
        $data['address'] = $userInfo['country'].' '.$userInfo['province'].' '.$userInfo['city'];

        return $data;
    }

    /**
     * 加载微信配置信息
     */
    public function wx_info()
    {
        $weixin = new \Org\Util\WeixinJssdk();
        $signPackage = $weixin->getSignPackage();
        $this->assign('wxinfo',$signPackage);
    }

    /**
     * 获取用户唯一标识
     */
    public function user_code()
    {
        do {
            // 生成用户唯一标识
            $code = strtoupper(substr(md5(mt_rand(100000, 999999)), mt_rand(1,9), 11));
            // 查询用户标识是否存在
            $oid = M('vendors')->where("`code`='{$code}'")->field('id')->find();
            // 用户唯一标识已存在再重新获取一次
        } while ($oid);

        // 绝对唯一的11位code
        return $code;
    }


}
