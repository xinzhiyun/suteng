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
        // 获取用户open_id
        if(empty($_SESSION['vendorInfo']['open_id'])){
        	// 请先登录
        	$this->redirect('Login/index');
        	exit;
        }

        // 获取用户open_id
        $showData['open_id'] = $_SESSION['vendorInfo']['open_id'];
        // 查询分销商状态
        $vendor = M('vendors')->where($showData)->find();

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
	        		echo '等待审批';
	        		break;
	        	case '4':
	        		// 身份证审批失败
	        		echo '身份证审批失败';
	        		// 身份证信息填写
                	$this->identity_refillings();
	        		break;
	        	case '5':
	        		// 公司信息审批失败
	        		echo '公司信息审批失败';
	        		$this->company_refillings();
	        		break;
	        	case '6':
	        		// 协议审批失败
	        		echo '重新上传协议';
	        		$this->display('protocol_refillings');
	        		break;
	        	case '7':
	        		// 审批成功
	        		echo '审批成功';
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
            	$this->success('合同信息提交成功', U('Home/Vendors/index'));
            }else{
            	$this->error('合同信息提交失败，请重新上传！');
            }
    	}else{
    		$this->error('合同上传失败，请重新上传！');
    	}

    	// dump($info);
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



}
