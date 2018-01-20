<?php
namespace Home\Controller;

use Think\Controller;
use \Org\Util\WeixinJssdk;
/**
 * 经销商注册控制器
 * 吴智彬
 */
class RegisteredVendorController extends Controller
{
    public function index()
    {
        if(IS_POST){
            //  获取邀请入二维码票据
            $ticket  = $_SESSION['ticket'];
            // 分销商级别
            $leavel  = $_SESSION['leavel'];
            // 用户微信唯一标识
            $open_id = $_SESSION['open_id'];
            // 邀请注册类型
            $recommend = $_SESSION['recommend'];

            // 匹配注册的分销商级别
            switch ($leavel) {
                case '0':
                        // A级分销商注册
                        $retype = 'a';
                        $leavelNum = 2;
                        $showFather['vendor_a'] = $ticket;
                    break;
                case '1':
                        // B级分销商注册
                        $retype = 'b';
                        $leavelNum = 3;
                        $showFather['vendor_b'] = $ticket;
                    break;
                case '2':
                        // C级分销商注册
                        $retype = 'c';
                        $showFather['vendor_c'] = $ticket;
                        $leavelNum = 4;
                    break;
                default:
                        // 异常处理
                        $this->error('分销注册类型错误，请加盟联系客服！');
                    break;
            }

            // 实例化分销商类型
            $father     = D('vendors');
            // 查询邀请人信息表
            $fatherData = $father->where($showFather)->find();
            //dump($fatherData);die;
            $vendorLeavel = $fatherData['leavel'];
            // 匹配邀请类型
            switch ($recommend) {
                case '4':
                    // 分公司邀请分销商注册
                    $superiors['office_code'] = $fatherData['code'];
                    break;
                case '5':
                    // 分销商邀请分销商注册
                    // 匹配注册的分销商级别
                    switch ($leavel) {
                        case '0':
                            // 分公司唯一标识
                            $superiors['office_code'] = $fatherData['office_code'];
                            // A级分销商邀请人唯一标识
                            $superiors['invitation_code'] = $fatherData['code'];
                            break;
                        case '1':
                            // 分销商级别匹配
                            switch ($vendorLeavel) {
                                case '2':
                                    // A级邀请B级注册 superior_code
                                    // 分公司唯一标识
                                    $superiors['office_code'] = $fatherData['office_code'];
                                    // B级分销商上级唯一标识
                                    $superiors['superior_code'] = $fatherData['code'];
                                    // B级分销商邀请人唯一标识
                                    $superiors['invitation_code'] = $fatherData['code'];
                                    break;
                                case '3':
                                    // B级邀请B级注册
                                    // 分公司唯一标识
                                    $superiors['office_code'] = $fatherData['office_code'];
                                    // B级分销商上级唯一标识
                                    $superiors['superior_code'] = $fatherData['superior_code'];
                                    // B级分销商邀请人唯一标识
                                    $superiors['invitation_code'] = $fatherData['code'];
                                    break;
                                default:
                                    # code...
                                    break;
                            }
                            break;
                        case '2':
                            // 分销商级别匹配
                            switch ($vendorLeavel) {
                                case '2':
                                    // A级邀请C级注册 superior_code
                                    // 分公司唯一标识
                                    $superiors['office_code'] = $fatherData['office_code'];
                                    // C级分销商上上级唯一标识
                                    $superiors['superiors_code'] = $fatherData['code'];
                                    // C级分销商上级唯一标识
                                    $superiors['superior_code'] = $fatherData['code'];
                                    // C级分销商邀请人唯一标识
                                    $superiors['invitation_code'] = $fatherData['code'];
                                    break;
                                case '3':
                                    // B级邀请C级注册 superior_code
                                    // 分公司唯一标识
                                    $superiors['office_code'] = $fatherData['office_code'];
                                    // C级分销商上上级唯一标识
                                    $superiors['superiors_code'] = $fatherData['superior_code'];
                                    // C级分销商上级唯一标识
                                    $superiors['superior_code'] = $fatherData['code'];
                                    // C级分销商邀请人唯一标识
                                    $superiors['invitation_code'] = $fatherData['code'];
                                    break;
                                case '4':
                                    // C级邀请C级注册
                                    // 分公司唯一标识
                                    $superiors['office_code'] = $fatherData['office_code'];
                                    // C级分销商上上级唯一标识
                                    $superiors['superiors_code'] = $fatherData['superiors_code'];
                                    // C级分销商上级唯一标识
                                    $superiors['superior_code'] = $fatherData['superior_code'];
                                    // C级分销商邀请人唯一标识
                                    $superiors['invitation_code'] = $fatherData['code'];
                                    break;
                                default:
                                    # code...
                                    break;
                            }
                            break;
                        default:
                                // 异常处理
                                $this->error('分销注册类型错误，请加盟联系客服！');
                            break;
                    }
                    // 分销商邀请分销商注册
                    break;
                default:
                    # code...
                    break;
            }
            // dump($fatherData);die;
            //dump($newData);die;
            // 分销商模型，开启事务
            $father->startTrans();

            // 创建数据对象
            $data = $father->create();

            if($data){
                // 数据对象创建成功,实例化二维码信息类
                $dimensionClass         = new DimensionController;
                // 获取分销商二维码
                $ticket                 = $dimensionClass->vendor($retype);
                // 将分销商票据合并入数组
                $newData                = array_merge($data,$ticket);
                // 将分A级分销商唯一标识加人数组中
                $newData['code']        = $this->vendor_code(); 
                // 分销商级别
                $newData['leavel']      = $leavelNum;
                // 状态：身份验证
                $newData['status']      = 0;
                // 用户微信唯一标识
                $newData['open_id']     = $open_id;
                // 将上级分销商编号合并进数组
                $newData                = array_merge($newData,$superiors);


                // dump($newData);die;
                // 更新条件
                $vendorWhere['open_id'] = $open_id;
                // 更新数据
                $vendorData['action']   = 1;
                // 执行更新操作，将微信分销商操作该为登录状态
                $vendorRes = M('wechat')->where($vendorWhere)->save($vendorData);

                // 验证通过，写入新增数据。并且微信信息表更新成功
                if($vendorRes && $father->add($newData)){
                    // 执行事务
                    $father->commit();
                    $newData['password'] = MD5($newData['password']);
                    $_SESSION['vendorInfo'] = $newData;
                    $this->success('注册成功，请尽快完成认证！');
                    return false;
                }else{
                    // 事务回滚
                    $father->rollback();
                    $this->error('注册失败了，去重新申请！');
                    return false;
                }
            }else{
                // 如果创建失败 表示验证没有通过 输出错误提示信息
                $this->success($father->getError());
                return false;
            }
        }else{
            // 如果是分销商注册
            // 实例化微信JSSDK
            $weixin = new WeixinJssdk;
            // 获取用户open_id
            if(empty($_SESSION['open_id'])){
                // 如果不存在则，跳转获取open_id,并缓存
                $_SESSION['open_id'] = $weixin->GetOpenid();
                //$_SESSION['open_id'] = 'oQktJwL8ioR4DoxSQmikdzekbUyU';

            }
            // 获取用户open_id
            $showData['open_id'] = $_SESSION['open_id'];
            // 查询微信信息表
            $wechat = M('wechat')->where($showData)->find();
            // 接收类型
            $type   = $wechat["type"];
            // 接收操作
            $action = $wechat["action"];
            // 接收邀请人二维码票据
            $_SESSION['ticket'] = $wechat["invitation_ticket"];
            // 分销商级别
            $_SESSION['leavel'] = $wechat["leavel"];
            // 邀请类型
            $_SESSION['recommend'] = $wechat['recommend'];

            if($type){
                switch ($action) {
                    case '0':
                        // 显示模板
                        $this->display();
                        break;
                    case '1':
                        // 已经注册过，跳转到分销商登录页
                        $this->redirect('Login/index');
                        break;
                    default:
                        # 不存在这个类型的操作
                        $this->error('系统暂不支持该类型分销注册，请加盟联系客服！');
                        break;
                }
            }else{
                // 非法访问
                $this->error('分销商只支持邀请注册，请加盟联系客服！');
            }
        }

    }

    /**
     * [upload 图片上传]
     * @return [type] [description]
     */
    public function upload()
    {
        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize   =     3145728;// 设置附件上传大小3MB
        $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
        $upload->rootPath  =     './Public/'; // 设置附件上传根目录
        $upload->savePath  =     '/Vendors/'; // 设置附件上传（子）目录
        // 上传文件 
        $info   =   $upload->upload();
        if(!$info) {// 上传错误提示错误信息
            return false;
            // $this->error($upload->getError());
        }else{
            // 上传成功
            foreach ($info as $key=>$file) {
                // dump($info);die;
                $pic[$key] = $file['savepath'].$file['savename'];
            }
            // $this->success('上传成功！');
            return $pic;
        }
    }

    /**
     * 获取分销商唯一标识
     */
    public function vendor_code()
    {        
        do {
          // 生成分销商唯一标识
          $code = strtoupper(substr(md5(mt_rand(100000, 999999)), mt_rand(1,9), 6));
          // 查询销商标识是否存在
          $oid = M('vendors')->where("`code`='{$code}'")->field('id')->find();
          // 分销商唯一标识已存在再重新获取一次
        } while ($oid);

        // 绝对唯一的6位code
        return $code;
    }
}