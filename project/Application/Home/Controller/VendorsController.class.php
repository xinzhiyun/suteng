<?php
namespace Home\Controller;

use Think\Controller;
use \Org\Util\WeixinJssdk;

/**
 * 分销商前台控制器 
 * 吴智彬
 */
class VendorsController extends Controller
{
	/**
	 * [index 分销商主页]
	 * @return [type] [description]
	 */
    public function index()
    {
        // dump($_SESSION['vendorInfo']);die;
        // 获取用户open_id
        if(empty($_SESSION['vendorInfo']['open_id'])){
        	// 请先登录
        	$this->redirect('Login/index');
        	exit;
        }

        // 获取用户open_id
        $showData['open_id'] = $_SESSION['vendorInfo']['open_id'];
        // 查询分销商
        $vendor = M('vendors')->where($showData)->find();
        // 分销商级别
        $vendor_leavel = $vendor['leavel'];
        // 昨日时间区间
        $Yesterday = array(array('gt',strtotime(date("Y-m-d 00:00:00",strtotime("-1 day")))),array('lt',strtotime(date("Y-m-d 23:59:59",strtotime("-1 day")))));
        // 本月时间区间
        $month = array(array('gt',strtotime(date("Y-m-1 00:00:00"))),array('lt',strtotime(date("Y-m-d 23:59:59"))));

        // 根据分销商级别匹配查询条件
        switch ($vendor_leavel) {
            case '2':
                    // A级分销商
                    $vendor_code = 'vendora_code';
                break;
            case '3':
                    // B级分销商
                    $vendor_code = 'vendorb_code';
                break;
            case '4':
                    // C级分销商
                    $vendor_code = 'vendorc_code';
                break;
            default:
                # code...
                break;
        }
        // dump($Yesterday);die;
        // 昨天统计
        $YesterdayData = $this->showTimeData($vendor_code,$vendor['code'],$Yesterday);
        // 本月统计
        $monthData = $this->showTimeData($vendor_code,$vendor['code'],$month); 
        // 查询下属用户总数量
        $userNum = $this->showUserData($vendor_code,$vendor['code']);
        // 查询下属分销商总数量
        $verdorNum = $this->showVerdorData($vendor['code']);

        $this->assign('YesterdayData',$YesterdayData);
        $this->assign('monthData',$monthData);
        $this->assign('userNum',$userNum);
        $this->assign('verdorNum',$verdorNum);
        $this->assign('vendor',$vendor);
        if($vendor){
        	// 获取分销商状态码
	        $status = $vendor['status'];
	        // 匹配分销商状态，安排后续操作
	        switch ($status) {
	        	case '0':
	        		// 身份证信息填写
                	$this->identity();
	        		break;
	        	case '1':
	        		// 公司信息填写
	        		$this->company();
	        		break;
	        	case '2':
	        		// 签协议
	        		$this->display('protocol');
	        		break;
	        	case '3':
	        		// 待审批
	        		// echo '等待审批';
                    $this->display('vendor_wait');
	        		break;
	        	case '4':
	        		// 身份证审批失败
                	$this->identity_refillings();
	        		break;
	        	case '5':
	        		// 公司信息审批失败
	        		$this->company_refillings();
	        		break;
	        	case '6':
	        		// 协议审批失败
	        		$this->display('protocol_refillings');
	        		break;
                case '9':
                    // 交加盟费，传入分销商级别
                    // $this->protocol_fee();
                    $this->redirect('Home/vendors/protocol_fee');
                    break;
	        	case '7':
                    $leavel = $vendor['vendor'];
                    switch ($leavel) {
                        case '2':
                            // A级分销商
                            $showA['invitation_code'] =  $vendor['code'];
                            $showA['leavel'] =  2;
                            // 查询同级邀请的A级分销商
                            $data['vendor_a'] = M('vendors')->where($showA)->select();
                            // B级分销商
                            $showB['superior_code'] =  $vendor['code'];
                            $showB['leavel'] =  3;
                            $data['vendor_b'] = M('vendors')->where($showB)->select();
                            // C级分销商
                            $showC['superiors_code'] =  $vendor['code'];
                            $showC['leavel'] =  4;
                            $data['vendor_c'] = M('vendors')->where($showC)->select();
                            // 会员
                            $showUser['vendora_code'] =  $vendor['code'];
                            $data['vendor_user'] = M('users')->where($showUser)->select();
                            break;
                        case '3':
                            // B级分销商
                            $showB['invitation_code'] =  $vendor['code'];
                            $showB['leavel'] =  3;
                            // 查询同级邀请的A级分销商
                            $data['vendor_b'] = M('vendors')->where($showB)->select();
                            // C级分销商
                            $showC['superior_code'] =  $vendor['code'];
                            $showC['leavel'] =  4;
                            $data['vendor_c'] = M('vendors')->where($showC)->select();
                            // 会员
                            $showUser['vendorb_code'] =  $vendor['code'];
                            $data['vendor_user'] = M('users')->where($showUser)->select();
                        case '4':
                            // C级分销商
                            $showC['invitation_code'] =  $vendor['code'];
                            $showC['leavel'] =  4;
                            // 查询同级邀请的A级分销商
                            $data['vendor_b'] = M('vendors')->where($showC)->select();
                            // 会员
                            $showUser['vendorc_code'] =  $vendor['code'];
                            $data['vendor_user'] = M('users')->where($showUser)->select();
                            break;
                        default:
                            # code...
                            break;
                    }
                    $this->assign('data',$data);
                    
	        		// 审批成功
	        		$this->display('index');
	        		break;
	        	case '8':
	        		// 禁用分销商
	        		echo '禁用分销商';
	        		break;
	        	default:
	        		// 异常处理
            		$this->error('分销商状态码错误，请加盟联系客服！');
	        		break;
	        }
        }else{
            // 异常处理
            $this->error('分销不存在，请加盟联系客服！');
        }

    }

    // 身份证信息填写
    public function identity()
    {
    	if(IS_POST){
    		// 获取缓存用户微信标识
    		$open_id = $_SESSION['vendorInfo']['open_id'];

    		// 接收表单信息
    		$name 		= I('post.name');
  			$phone 		= I('post.phone');
  			$identity 	= I('post.identity');

    		// 实例化验证类
    		$validate 	= new \Org\Util\Validate;

    		// 验证是否合法用户名：字母或中文开头，数字字母下划线中文,2-30位
    		if($validate->isName($name)){
    			$data['name'] = $name;
    		}else{
    			$this->error('请输入真实性名！');
    		}

    		// 手机号码验证
    		if($validate->isMobilePhone($phone)){
    			$data['phone'] = $phone;
    		}else{
    			 $this->error('手机号码格式不正确！');
    		}

    		// 身份证号码验证
    		if($validate->isIdentity($identity)){
    			$data['identity'] = $identity;
    		}else{
    			 $this->error('请输入真实的身份证号码！');
    		}

            // 没有上传图片报错信息组
            $emptyError = array(
                'positive' =>  '身份证正面图片必须上传',
                'opposite' =>  '身份证反面图片必须上传',
                'handheld' =>  '手持身份证正面图片必须上传',
            );

            // 图片超出上传文件大小报错组
            $sizeError = array(
                'positive' =>  '身份证正面图片超出允许上传文件大小3M',
                'opposite' =>  '身份证反面图片超出允许上传文件大小3M',
                'handheld' =>  '手持身份证正面图片超出允许上传文件大小3M',
            );

            // 图片超出上传文件大小报错组
            $typeError = array(
                'positive' =>  '身份证正面图片类型错误,允许jpg, gif, png, jpeg',
                'opposite' =>  '身份证反面图片类型错误,允许jpg, gif, png, jpeg',
                'handheld' =>  '手持身份证正面图片类型错误,允许jpg, gif, png, jpeg',
            );

            // 允许的图片类型
            $type = array(
                "image/jpg",
                "image/png",
                "image/gif",
                "image/jpeg",
            );

            // 遍历上传图片信息
            foreach ($_FILES as $key => $value) {
                // 不上传图片检测
                if($_FILES[$key]['error'] == 4){
                    // 反馈XX图片必须上传
                    $this->error($emptyError[$key]);
                    return false;
                }

                // 图片大小检测
                if($_FILES[$key]['size'] > 3145728){
                    // 反馈XX图片大小错误
                    $this->error($sizeError[$key]);
                    return false;
                }

                // 图片类型检测
                if(!in_array($_FILES[$key]['type'], $type)){
                    // 反馈XX图片类型错误
                    $this->error($typeError[$key]);
                    return false;
                }
            }

            // 处理图片
            $info = $this->upload();

            if($info){
            	// 将图片和表单数据合并
            	$newData = array_merge($data,$info);
            	// 分销商状态
            	$newData['status'] = 1;
            	// 更新条件
            	$saveData['open_id'] = $open_id;
            	// 更新分销商信息
            	$vandorRes = M('vendors')->where($saveData)->save($newData);

            	// 如果分销商数据更新成功
            	if($vandorRes){
            		$this->success('身份证信息提交成功', U('Home/Vendors/index'));
            	}else{
            		$this->error('身份证信息提交失败，请重新认证！');
            	}

            }else{
            	$this->error('图片上传失败，请重新上传！');
            }

    	}else{
    		$this->display('identity');
    	}
    }

    // 身份证信息重填写
    public function identity_refillings()
    {
    	if(IS_POST){
    		// 获取缓存用户微信标识
    		$open_id = $_SESSION['vendorInfo']['open_id'];
    		// 接收表单信息
    		$name 		= I('post.name');
  			$phone 		= I('post.phone');
  			$identity 	= I('post.identity');

    		// 实例化验证类
    		$validate 	= new \Org\Util\Validate;

    		// 验证是否合法用户名：字母或中文开头，数字字母下划线中文,2-30位
    		if($validate->isName($name)){
    			$data['name'] = $name;
    		}else{
    			$this->error('请输入真实性名！');
    		}

    		// 手机号码验证
    		if($validate->isMobilePhone($phone)){
    			$data['phone'] = $phone;
    		}else{
    			 $this->error('手机号码格式不正确！');
    		}

    		// 身份证号码验证
    		if($validate->isIdentity($identity)){
    			$data['identity'] = $identity;
    		}else{
    			 $this->error('请输入真实的身份证号码！');
    		}

            // 没有上传图片报错信息组
            $emptyError = array(
                'positive' =>  '身份证正面图片必须上传',
                'opposite' =>  '身份证反面图片必须上传',
                'handheld' =>  '手持身份证正面图片必须上传',
            );

            // 图片超出上传文件大小报错组
            $sizeError = array(
                'positive' =>  '身份证正面图片超出允许上传文件大小3M',
                'opposite' =>  '身份证反面图片超出允许上传文件大小3M',
                'handheld' =>  '手持身份证正面图片超出允许上传文件大小3M',
            );

            // 图片超出上传文件大小报错组
            $typeError = array(
                'positive' =>  '身份证正面图片类型错误,允许jpg, gif, png, jpeg',
                'opposite' =>  '身份证反面图片类型错误,允许jpg, gif, png, jpeg',
                'handheld' =>  '手持身份证正面图片类型错误,允许jpg, gif, png, jpeg',
            );

            // 允许的图片类型
            $type = array(
                "image/jpg",
                "image/png",
                "image/gif",
                "image/jpeg",
            );

            // 遍历上传图片信息
            foreach ($_FILES as $key => $value) {
                // 不上传图片检测
                if($_FILES[$key]['error'] == 4){
                    // 反馈XX图片必须上传
                    $this->error($emptyError[$key]);
                    return false;
                }

                // 图片大小检测
                if($_FILES[$key]['size'] > 3145728){
                    // 反馈XX图片大小错误
                    $this->error($sizeError[$key]);
                    return false;
                }

                // 图片类型检测
                if(!in_array($_FILES[$key]['type'], $type)){
                    // 反馈XX图片类型错误
                    $this->error($typeError[$key]);
                    return false;
                }
            }

            // 处理图片
            $info = $this->upload();

            if($info){
            	// 将图片和表单数据合并
            	$newData = array_merge($data,$info);
            	// 分销商状态
            	$newData['status'] = 3;
            	// 更新条件
            	$saveData['open_id'] = $open_id;
            	// 更新分销商信息
            	$vandorRes = M('vendors')->where($saveData)->save($newData);

            	// 如果分销商数据更新成功
            	if($vandorRes){
            		$this->success('身份证信息提交成功', U('Home/Vendors/index'));
            	}else{
            		$this->error('身份证信息提交失败，请重新认证！');
            	}

            }else{
            	$this->error('图片上传失败，请重新上传！');
            }

    	}else{
    		$this->display('identity_refillings');
    	}
    }

    /**
     * 公司信息填写
     */
    public function company()
    {
    	if(IS_POST){
    		// 获取缓存用户微信标识
    		$open_id = $_SESSION['vendorInfo']['open_id'];
    		// 接收表单信息
    		$company 		= I('post.company');
  			$telephone 		= I('post.telephone');
  			$address 	 	= I('post.address');

    		// 实例化验证类
    		$validate 	= new \Org\Util\Validate;

    		// 验证是否合法公司名称：字母或中文开头，数字字母下划线中文,2-30位
    		if($validate->isName($company)){
    			$data['company'] = $company;
    		}else{
    			 $this->error('公司名称格式不正确，请重新输入！');
    		}

    		// 座机号码验证
    		if($validate->isPhone($telephone)){
    			$data['telephone'] = $telephone;
    		}else{
    			 $this->error('坐机号码格式不正确！');
    		}

    		// 公司地址验证
    		if($validate->isAddress($address)){
    			$data['address'] = $address;
    		}else{
    			 $this->error('请输入完整的公司地址！');
    		}

            // 没有上传图片报错信息组
            $emptyError = array(
                'licence'  =>  '营业执照图片必须上传',
                'protocol' =>  '协调文件图片必须上传',
            );

            // 图片超出上传文件大小报错组
            $sizeError = array(
                'licence'  =>  '营业执照图片超出允许上传文件大小3M',
                'protocol' =>  '协调文件图片超出允许上传文件大小3M',
            );

            // 图片超出上传文件大小报错组
            $typeError = array(
                'licence'  =>  '营业执照图片类型错误,允许jpg, gif, png, jpeg',
                'protocol' =>  '协调文件图片类型错误,允许jpg, gif, png, jpeg',
            );

            // 允许的图片类型
            $type = array(
                "image/jpg",
                "image/png",
                "image/gif",
                "image/jpeg",
            );

            // 遍历上传图片信息
            foreach ($_FILES as $key => $value) {
                // 不上传图片检测
                if($_FILES[$key]['error'] == 4){
                    // 反馈XX图片必须上传
                    $this->error($emptyError[$key]);
                    return false;
                }

                // 图片大小检测
                if($_FILES[$key]['size'] > 3145728){
                    // 反馈XX图片大小错误
                    $this->error($sizeError[$key]);
                    return false;
                }

                // 图片类型检测
                if(!in_array($_FILES[$key]['type'], $type)){
                    // 反馈XX图片类型错误
                    $this->error($typeError[$key]);
                    return false;
                }
            }

            // 处理图片
            $info = $this->upload();

            if($info){
            	// 将图片和表单数据合并
            	$newData = array_merge($data,$info);
            	// 分销商状态
            	$newData['status'] = 2;
            	// 更新条件
            	$saveData['open_id'] = $open_id;
            	// 更新分销商信息
            	$vandorRes = M('vendors')->where($saveData)->save($newData);

            	// 如果分销商数据更新成功
            	if($vandorRes){
            		$this->success('公司信息提交成功', U('Home/Vendors/index'));
            	}else{
            		$this->error('身份证信息提交失败，请重新认证！');
            	}

            }else{
            	$this->error('图片上传失败，请重新上传！');
            }

    	}else{
    		$this->display('company');
    	}
    }

    /**
     * 公司信息重填写
     */
    public function company_refillings()
    {
    	if(IS_POST){
    		// 获取缓存用户微信标识
    		$open_id = $_SESSION['vendorInfo']['open_id'];
    		// 接收表单信息
    		$company 		= I('post.company');
  			$telephone 		= I('post.telephone');
  			$address 	 	= I('post.address');

    		// 实例化验证类
    		$validate 	= new \Org\Util\Validate;

    		// 验证是否合法公司名称：字母或中文开头，数字字母下划线中文,2-30位
    		if($validate->isName($company)){
    			$data['company'] = $company;
    		}else{
    			 $this->error('公司名称格式不正确，请重新输入！');
    		}

    		// 座机号码验证
    		if($validate->isPhone($telephone)){
    			$data['telephone'] = $telephone;
    		}else{
    			 $this->error('坐机号码格式不正确！');
    		}

    		// 公司地址验证
    		if($validate->isAddress($address)){
    			$data['address'] = $address;
    		}else{
    			 $this->error('请输入完整的公司地址！');
    		}

            // 没有上传图片报错信息组
            $emptyError = array(
                'licence'  =>  '营业执照图片必须上传',
                'protocol' =>  '协调文件图片必须上传',
            );

            // 图片超出上传文件大小报错组
            $sizeError = array(
                'licence'  =>  '营业执照图片超出允许上传文件大小3M',
                'protocol' =>  '协调文件图片超出允许上传文件大小3M',
            );

            // 图片超出上传文件大小报错组
            $typeError = array(
                'licence'  =>  '营业执照图片类型错误,允许jpg, gif, png, jpeg',
                'protocol' =>  '协调文件图片类型错误,允许jpg, gif, png, jpeg',
            );

            // 允许的图片类型
            $type = array(
                "image/jpg",
                "image/png",
                "image/gif",
                "image/jpeg",
            );

            // 遍历上传图片信息
            foreach ($_FILES as $key => $value) {
                // 不上传图片检测
                if($_FILES[$key]['error'] == 4){
                    // 反馈XX图片必须上传
                    $this->error($emptyError[$key]);
                    return false;
                }

                // 图片大小检测
                if($_FILES[$key]['size'] > 3145728){
                    // 反馈XX图片大小错误
                    $this->error($sizeError[$key]);
                    return false;
                }

                // 图片类型检测
                if(!in_array($_FILES[$key]['type'], $type)){
                    // 反馈XX图片类型错误
                    $this->error($typeError[$key]);
                    return false;
                }
            }

            // 处理图片
            $info = $this->upload();

            if($info){
            	// 将图片和表单数据合并
            	$newData = array_merge($data,$info);
            	// 分销商状态
            	$newData['status'] = 3;
            	// 更新条件
            	$saveData['open_id'] = $open_id;
            	// 更新分销商信息
            	$vandorRes = M('vendors')->where($saveData)->save($newData);

            	// 如果分销商数据更新成功
            	if($vandorRes){
            		$this->success('公司信息提交成功', U('Home/Vendors/index'));
            	}else{
            		$this->error('身份证信息提交失败，请重新认证！');
            	}

            }else{
            	$this->error('图片上传失败，请重新上传！');
            }

    	}else{
    		$this->display('company_refillings');
    	}
    }

    /**
     * [protocol 签协议]
     * @return [type] [description]
     */
    public function protocol()
    {	
        if (IS_AJAX) {
            // 获取微信用户唯一标识
            $open_id = $_SESSION['vendorInfo']['open_id'];

            // 上传合同文件
            $info = $this->upload();

            if($info){
                $info['status'] = 9;
                // 更新条件
                $saveData['open_id'] = $open_id;
                // 更新分销商信息
                $vandorRes = M('vendors')->where($saveData)->save($info);
                if($vandorRes){
                    // $this->success('合同信息提交成功', U('Home/Vendors/index'));
                    $message['code'] = 200;
                    $message['res']  = '合同信息提交成功';
                }else{
                    // $this->error('合同信息提交失败，请重新上传！');
                    $message['code'] = 605;
                    $message['res']  = '合同信息提交失败，请重新上传！';
                }
            }else{
                $message['code'] = 605;
                $message['res']  = '合同信息提交失败，请重新上传！';
            }

            $this->ajaxReturn($message); 
        } else {
            $this->display();
        }
    	
    }

    /**
     * [protocol 签协议]
     * @return [type] [description]
     */
    public function protocol_re()
    {   
        // 获取微信用户唯一标识
        $open_id = $_SESSION['vendorInfo']['open_id'];

        // 上传合同文件
        $info = $this->upload();

        if($info){
            $info['status'] = 3;
            // 更新条件
            $saveData['open_id'] = $open_id;
            // 更新分销商信息
            $vandorRes = M('vendors')->where($saveData)->save($info);
            if($vandorRes){
                // $this->success('合同信息提交成功', U('Home/Vendors/index'));
                $message['code'] = 200;
                $message['res']  = '合同信息提交成功';
            }else{
                // $this->error('合同信息提交失败，请重新上传！');
                $message['code'] = 605;
                $message['res']  = '合同信息提交失败，请重新上传！';
            }
        }else{
            $message['code'] = 605;
            $message['res']  = '合同信息提交失败，请重新上传！';
        }

        $this->ajaxReturn($message); 
    }
    

    // 收加盟费
    public function protocol_fee()
    {
        // 获取微信用户唯一标识
        $open_id = $_SESSION['vendorInfo']['open_id'];

        // 准备查询条件
        $showData['id'] = 1;
        // 查询分销商加盟费
        $vendor_fee = M('vendor_fee')->where($showData)->field('vendor_a,vendor_b,vendor_c')->find();

        // 匹配分销商级别
        switch ($_SESSION['vendorInfo']['leavel']) {
            case '2':
                // A级分销商
                $money = $vendor_fee['vendor_a'];
                break;
            case '3':
                // B级分销商
                $money = $vendor_fee['vendor_b'];
            case '4':
                // C级分销商
                $money = $vendor_fee['vendor_c'];
                break;
            default:
                # code...
                break;
        }

        // 微信支付配置
        $weixin = new WeixinJssdk;
        $signPackage = $weixin->getSignPackage();
        // 查询用户微信中的openid
        // $openId = $weixin->GetOpenid();
        $openId = $_SESSION['vendorInfo']['open_id'];
        // 获取用户open_id
        $showDataa['open_id'] = $openId;
        // 查询分销商状态
        $vendor = M('vendors')->where($showDataa)->find();
        $this->assign('vendor',$vendor);

        //分配数据        
        $this->assign('info',$signPackage);
        $this->assign('openId',$openId);
        $this->assign('money',$money);
        // 显示模板
        $this->display('protocol_fee');
    }

    // 收益明细
    public function profit_details()
    {
        // 获取分销商标识
        $showData['vendor_code'] = $_SESSION['vendorInfo']['code'];
        // 查询分销商收益明细
        $data = M('vendors_commission')->where($showData)->select();
        $total = 0;
        foreach ($data as $key => $value) {
            // 避免小数运算
            $total += $value['abonus']*100;
        }
        // 单位换算
        $total = $total/100;

        // 分配数据
        $this->assign('data',$data);
        $this->assign('total',$total);
        // 显示模板
        $this->display();
    }

    // 分销商团队
    public function vendor_team()
    {
        $this->display();
    }

    // 收入详情
    public function income_detailed()
    {
        $map['order_id'] = I('order_id');
        $order_detail = D('OrderDetail');
        $data = $order_detail->getGoodsDetail($map);
        dump($data);

        $this->display();
    }

    /**
     * [upload 图片上传]
     * @return [type] [description]
     */
    public function upload()
    {
        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize   =     3145728;// 设置附件上传大小3MB
        $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg','docx','doc');// 设置附件上传类型
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
     * [showTimeData 按时间区间统计分销商]
     * @param  [type] $vendor [分销商级别]
     * @param  [type] $code   [分销商标识]
     * @param  [type] $time   [时间区间]
     * @return [type]         [区间新增加会员，区间新增加订单，区间订单总额]
     */
    public function showTimeData($vendor,$code,$time)
    {

        // A级分销商
        $showYesterdayUser = array($vendor=>$code);

        // 昨日时间区间
        $showYesterdayUser['addtime'] = $time;

        // 昨日新增会员
        $YesterdayUserData =  M('users')->where($showYesterdayUser)->field('id')->select();
        
        // 1.统计昨日新增会员数量
        $YesterdayUserNum = 0;
        if(!empty($YesterdayUserData)){
            $YesterdayUserNum = count($YesterdayUserData);
        } 
        
        // 昨日订单时间区间
        $showYesterdayUserOrder['o.addtime'] = $time;
        $showYesterdayUserOrder['u.'.$vendor] = $code;

        // 昨日会员订单
        $YesterdayUserOrderData = M('shop_order')
                                    ->alias('o')
                                    ->where($showYesterdayUserOrder)
                                    ->join('__USERS__ u ON o.uid = u.id','LEFT')
                                    ->field('o.id,o.g_price')
                                    ->select();

        // 2.昨日会员订单数量
        $YesterdayUserOrderNum = 0;
        
        // 3.昨日会员订单总金额
        $YesterdayUserOrderPrice = 0;

        // 有订单数据就汇总金额
        if(!empty($YesterdayUserOrderData)){
            $YesterdayUserOrderNum = count($YesterdayUserOrderData);
            // 统计订单金额
            foreach ($YesterdayUserOrderData as $key => $value) {
                // 单位转换
                $YesterdayUserOrderPrice += $value['g_price'] * 100;
            }
            // 单位转换
            $YesterdayUserOrderPrice = $YesterdayUserOrderPrice / 100;
        }

        // 新增加会员数量
        $data['user_num'] = $YesterdayUserNum;
        // 会员订单数量
        $data['order_num'] = $YesterdayUserOrderNum;
        // 会员订单金额
        $data['order_price'] = $YesterdayUserOrderPrice;

        return $data;
    }

    /**
     * [showTimeData 分销商下用户总数量]
     * @param  [type] $vendor [分销商级别]
     * @param  [type] $code   [分销商标识]
     * @return [type]         [用户总数量]
     */
    public function showUserData($vendor,$code)
    {
        // A级分销商
        $showYesterdayUser[$vendor] = $code;

        // 昨日新增会员
        $YesterdayUserData =  M('users')->where($showYesterdayUser)->field('id')->select();
        
        // 1.统计昨日新增会员数量
        $YesterdayUserNum = 0;
        if(!empty($YesterdayUserData)){
            $YesterdayUserNum = count($YesterdayUserData);
        } 
 
        return $YesterdayUserNum;
    }

    /**
     * [showTimeData 按时间区间统计分销商]
     * @param  [type] $vendor [分销商级别]
     * @param  [type] $code   [分销商标识]
     * @param  [type] $time   [时间区间]
     * @return [type]         [区间新增加会员，区间新增加订单，区间订单总额]
     */
    public function showVerdorData($code)
    {
        // 上级分销商
        $showYesterdayUser['superior_code']   = $code;
        // 上上级分销商
        $showYesterdayUser['superiors_code']  = $code;
        // 同级邀请分销商
        // $showYesterdayUser['invitation_code'] = $code;
        $showYesterdayUser['_logic'] = 'OR';

        // 昨日新增会员
        $YesterdayUserData =  M('vendors')->where($showYesterdayUser)->field('id')->select();
        
        // 1.统计昨日新增会员数量
        $YesterdayUserNum = 0;
        if(!empty($YesterdayUserData)){
            $YesterdayUserNum = count($YesterdayUserData);
        } 
 
        return $YesterdayUserNum;
    }

    // 查询分销商团队
    public function get_vendor_team()
    {
        // 用户级别2:A级分销商 3:B级分销商 4:C级分销商
        $leavel = $_SESSION['vendorInfo']['leavel'];
        // 分销商标识
        $code = $_SESSION['vendorInfo']['code'];
        // 接收获取数据类型
        $type = I('post.type');
        // 匹配分销商级别
        switch ($leavel) {
            case '2':
            // A级分销商区间
                switch ($type) {
                    case 'A':
                        // A级分销商
                        $showDataA['invitation_code'] = $code;
                        $showDataA['leavel'] = 2;
                        $vendor_a = M('vendors')->where($showDataA)->select();

                        if($vendor_a){
                            $message    = ['code' => 200, 'message' => '分销商数据查询成功!', 'data' => $vendor_a , 'num' => count($vendor_a)];
                        }else{
                            $message    = ['code' => 403, 'message' => '暂无该级别分销商，赶紧去邀请吧!'];
                        }
                        break;
                    case 'B':
                        // B级分销商
                        $showDataB['superior_code'] = $code;
                        $showDataB['leavel'] = 3;
                        $vendor_b = M('vendors')->where($showDataB)->select();

                        if($vendor_b){
                            $message    = ['code' => 200, 'message' => '分销商数据查询成功!', 'data' => $vendor_b , 'num' => count($vendor_b)];
                        }else{
                            $message    = ['code' => 403, 'message' => '暂无该级别分销商，赶紧去邀请吧!'];
                        }
                        break;
                    case 'C':
                        // C级分销商
                        $showDataC['superiors_code'] = $code;
                        $showDataC['leavel'] = 4;
                        $vendor_c = M('vendors')->where($showDataC)->select();

                        if($vendor_c){
                            $message    = ['code' => 200, 'message' => '分销商数据查询成功!', 'data' => $vendor_c , 'num' => count($vendor_c)];
                        }else{
                            $message    = ['code' => 403, 'message' => '暂无该级别分销商，赶紧去邀请吧!'];
                        }
                        break;
                    case 'U':
                        // 分销商会员
                        $showDataU['vendora_code'] = $code;
                        $vendor_u =  M('users')->where($showDataU)->select();

                        if($vendor_u){
                            $message    = ['code' => 200, 'message' => '会员数据查询成功!', 'data' => $vendor_u , 'num' => count($vendor_u)];
                        }else{
                            $message    = ['code' => 403, 'message' => '暂无会员员，赶紧去邀请吧!'];
                        }
                        break;
                }

                break;
            case '3':
                // B级分销商
                switch ($type) {
                    case 'A':
                        // A级分销商
                        $message    = ['code' => 403, 'message' => 'B级分销商下没有A级分销商'];
                        break;
                    case 'B':
                        // B级分销商
                        $showDataB['invitation_code'] = $code;
                        $showDataB['leavel'] = 3;
                        $vendor_b = M('vendors')->where($showDataB)->select();

                        if($vendor_b){
                            $message    = ['code' => 200, 'message' => '分销商数据查询成功!', 'data' => $vendor_b , 'num' => count($vendor_b)];
                        }else{
                            $message    = ['code' => 403, 'message' => '暂无该级别分销商，赶紧去邀请吧!'];
                        }
                        break;
                    case 'C':
                        // C级分销商
                        $showDataC['superior_code'] = $code;
                        $showDataC['leavel'] = 4;
                        $vendor_c = M('vendors')->where($showDataC)->select();

                        if($vendor_c){
                            $message    = ['code' => 200, 'message' => '分销商数据查询成功!', 'data' => $vendor_c ,'num' => count($vendor_c)];
                        }else{
                            $message    = ['code' => 403, 'message' => '暂无该级别分销商，赶紧去邀请吧!'];
                        }
                        break;
                    case 'U':
                        // 分销商会员
                        $showDataU['vendora_code'] = $code;
                        $vendor_u =  M('users')->where($showDataU)->select();

                        if($vendor_u){
                            $message    = ['code' => 200, 'message' => '会员数据查询成功!', 'data' => $vendor_u, 'num' => count($vendor_u)];
                        }else{
                            $message    = ['code' => 403, 'message' => '暂无会员员，赶紧去邀请吧!'];
                        }
                        break;
                }
                break;
            case '4':
                // C级分销商
                switch ($type) {
                    case 'A':
                        // A级分销商
                        $message    = ['code' => 403, 'message' => 'C级分销商下没有B级分销商'];
                        break;
                    case 'B':
                        // B级分销商
                        $message    = ['code' => 403, 'message' => 'C级分销商下没有B级分销商'];
                        break;
                    case 'C':
                        // C级分销商
                        $showDataC['invitation_code'] = $code;
                        $showDataC['leavel'] = 4;
                        $vendor_c = M('vendors')->where($showDataC)->select();

                        if($vendor_c){
                            $message    = ['code' => 200, 'message' => '分销商数据查询成功!', 'data' => $vendor_c ,'num' => count($vendor_c)];
                        }else{
                            $message    = ['code' => 403, 'message' => '暂无该级别分销商，赶紧去邀请吧!'];
                        }
                        break;
                    case 'U':
                        // 分销商会员
                        $showDataU['vendora_code'] = $code;
                        $vendor_u =  M('users')->where($showDataU)->select();

                        if($vendor_u){
                            $message    = ['code' => 200, 'message' => '会员数据查询成功!', 'data' => $vendor_u,'num' => count($vendor_u)];
                        }else{
                            $message    = ['code' => 403, 'message' => '暂无会员员，赶紧去邀请吧!'];
                        }
                        break;
                }
                break;
        }
        // 返回JSON格式数据
        $this->ajaxReturn($message);
    }

    /**
     * 获取当前分销商级别
     */
    public function get_leavel()
    {
        // 用户级别2:A级分销商 3:B级分销商 4:C级分销商
        $leavel = $_SESSION['vendorInfo']['leavel']; 

        switch ($leavel) {
            case '2':
                $data = 'A级分销商';
                break;
            case '3':
                $data = 'B级分销商';
                break;
            case '4':
                $data = 'C级分销商';
                break;
        }

        if($leavel){
            $message    = ['code' => 200, 'message' => '分销商级别获取成功!', 'leavel' => $data];
        }else{
            $message    = ['code' => 403, 'message' => '分销商级别获取失败!'];
        }

        // 返回JSON格式数据
        $this->ajaxReturn($message);
    }

}
