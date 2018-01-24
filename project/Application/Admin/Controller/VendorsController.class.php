<?php
namespace Admin\Controller;
use Home\Controller\DimensionController;
use Think\Controller;

class VendorsController extends CommonController
{
    public function index()
    {
        $this->display();
    }

    /**
     * [company_add 分公司添加]
     * @return [type] [description]
     */
    public function company_add(){
        if(IS_POST){
            // 没有上传图片报错信息组
            $emptyError = array(
                'positive' =>  '身份证正面图片必须上传',
                'opposite' =>  '身份证反面图片必须上传',
                'handheld' =>  '手持身份证正面图片必须上传',
                'licence'  =>  '营业执照图片必须上传',
                'protocol' =>  '协调文件图片必须上传',
            );

            // 图片超出上传文件大小报错组
            $sizeError = array(
                'positive' =>  '身份证正面图片超出允许上传文件大小3M',
                'opposite' =>  '身份证反面图片超出允许上传文件大小3M',
                'handheld' =>  '手持身份证正面图片超出允许上传文件大小3M',
                'licence'  =>  '营业执照图片超出允许上传文件大小3M',
                'protocol' =>  '协调文件图片超出允许上传文件大小3M',
            );

            // 图片类型报错组
            $typeError = array(
                'positive' =>  '身份证正面图片类型错误,允许jpg, gif, png, jpeg',
                'opposite' =>  '身份证反面图片类型错误,允许jpg, gif, png, jpeg',
                'handheld' =>  '手持身份证正面图片类型错误,允许jpg, gif, png, jpeg',
                'licence'  =>  '营业执照图片类型错误,允许jpg, gif, png, jpeg',
                'protocol' =>  '协调文件图片类型错误,允许jpg, gif, png, jpeg,doc,docx',
            );

            // 允许的图片类型
            $type = array(
                "image/jpg",
                "image/png",
                "image/gif",
                "image/jpeg",
            );

            $typeDoc = array(
                "image/jpg",
                "image/png",
                "image/gif",
                "image/jpeg",
                'application/msword',
                'application/octet-stream',
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
            }

            // 接收上装文件
            $filesImg = $_FILES;
            // 从原数组中去除协议文件
            array_pop($filesImg);

            // 图片文件类型检查
            foreach ($filesImg as $key => $value) {
                // 图片类型检测
                if(!in_array($filesImg[$key]['type'], $type)){
                    // 反馈XX图片类型错误
                    $this->error($typeError[$key]);
                    return false;
                }
            }

            // 协议类型检测
            if(!in_array($_FILES['protocol']['type'], $typeDoc)){
                // 反馈XX图片类型错误
                $this->error($typeError['protocol']);
                return false;
            }
            
            // 开启事务
            $vendors = D("vendors");
            $vendors->startTrans();

            // 处理图片
            $info = $this->upload();

            // 检查图片是否上传成功
            if($info){
                // 图片上传成功，实例化vendors对象
                $data = $vendors->create();
                if (!$data){ // 创建数据对象
                     // 如果创建失败 表示验证没有通过 输出错误提示信息
                     $this->success($vendors->getError());
                     return false;
                }else{
                    // 实例化二维码信息类
                    $getCode            = new DimensionController;
                    // 获取分公司永久二维码
                    $ticket             = $getCode->office();
                    // 二维码获取成功
                    if($ticket){
                        // 将分公司唯一标识加人数组中
                        $data['code']    = $this->vendor_code();
                        // 设置级别为分公司
                        $data['leavel']     = 1;
                        // 设置状态为待审批
                        $data['status']     = 7;
                        // 设置审核状态
                        $data['reviewed']   = 3;
                        // 设置添加责任人
                        $data['add_liable'] = $_SESSION['adminInfo']['user'];
                        // 将图片合并入数据中
                        $newData            = array_merge($data,$info,$ticket); 
                        //dump($newData);die;
                        // 验证通过 写入新增数据
                        if($vendors->add($newData)){
                            // 执行事务
                            $vendors->commit();
                            $this->success('分公司添加成功，可以正常使用了！！！');
                            return false;
                        }else{
                            // 事务回滚
                            $vendors->rollback();
                            $this->error('资料提交失败了，请重新提交！');
                            return false;
                        }
                    }else{
                        $this->error('邀请二维码创建失败，请重新申请！');
                        return false;
                    }


                }
            }else{
                $this->error('图片上传失败，请重新上传！');
                return false;
            }
        }else{
            $this->display();
        }
    }

    /**
     * [company_list 查看分公司列表]
     * @return [type] [description]
     */
    public function company_list()
    {
        $data = D('vendors')->companyList();
        $assign = [
            'data' => $data,
        ];
        $this->assign($assign);
        $this->display();
    }

    /**
     * [vendor_data 删除分公司]
     * @return [type] [description]
     */
    public function company_del()
    {
        // 接收查询的ID号
        $showData['id'] = I('post.id');
        // 实例化分销商模型
        $vendors = M('vendors');
        // 查询分公司唯一标识
        $office = $vendors->where($showData)->find();
        // 准备查询条件
        $showOffice['office_code'] = $office['code'];
        // 查询分公司下是否有分销商
        $vendor = $vendors->where($showOffice)->field('id')->find()['id'];

        if($vendor){
            // 分公司名下有分销商不可删除
            $message     = ['code' => 403, 'message' => '分公司名下有分销商不可删除'];
        }else{
            // 查询分公司下是否有会员
            $user = M('users')->where($showOffice)->field('id')->find()['id'];
            if($user){
                // 分公司名下有会员不可以直接删除
                $message     = ['code' => 403, 'message' => '分公司名下有会员不可以直接删除'];
            }else{
                // 开启事务
                $vendors->startTrans();

                // 准备文件路径
                $positive   ='./Public'.$office['positive'];
                $opposite   ='./Public'.$office['opposite'];
                $handheld   ='./Public'.$office['handheld'];
                $licence    ='./Public'.$office['licence'];
                $protocol   ='./Public'.$office['protocol'];
                // 删除文件
                unlink($positive);
                unlink($opposite);
                unlink($handheld);
                unlink($licence);
                unlink($protocol);
                
                // 删除分公司信息
                $companyDelRes = $vendors->where($showData)->delete();

                // 如果图片删除成功并且信息删除成功
                if($companyDelRes){
                    // 执行事务
                    $vendors->commit();
                    // 分公司删除成功
                    $message     = ['code' => 200, 'message' => '分公司删除成功'];
                }else{
                    // 事务回滚
                    $vendors->rollback();
                    // 分公司删除失败
                    $message     = ['code' => 403, 'message' => '分公司删除失败'];
                }
            }
        }
        // 返回JSON格式数据
        $this->ajaxReturn($message);
    }

    /**
     * [take_over 查询是否需要交接分公司]
     * @return [type] [description]
     */
    public function take_over()
    {
        // 接收查询的ID号
        $showData['id'] = I('post.id');
        // 实例化分销商模型
        $vendors = M('vendors');
        // 查询分公司资料
        $office = $vendors->where($showData)->find();

        // 查询分公司下是否有分销商
        // 准备查询条件
        $showOffice['office_code'] = $office['code'];
        // 查询分公司下是否有分销商
        $vendor = $vendors->where($showOffice)->field('id')->find()['id'];
        // 查询分公司下是否有会员
        $user = M('users')->where($showOffice)->field('id')->find()['id'];

        if($vendor || $user){
            // 获取分公司级别
            $showLeavel['leavel'] = $office['leavel'];
            // 查询同级分公司
            $officeLeavel = $vendors->where($showLeavel)->select();
            // 统计同级分公司数量，包括自己
            $officeNum = count($officeLeavel);
            // 如果同级分公司不止一个
            if($officeNum>1){
                // 获取分公司ID
                $officeId = $office['id'];
                // 遍历可以交接的分公司
                foreach ($officeLeavel as $key => $value) {
                    // 从同级分公司中找出当前分公司
                    if($value['id']==$officeId){
                        // 将当前分公司从交接分公司数组中删除
                        unset($officeLeavel[$key]);
                    }
                }
                // 提示信息
                $message                    = ['code' => 200, 'message' => '请选择交接的分公司'];
                // 当前分公司信息
                $message['office']          = $office;
                // 同级分公司信息
                $message['officeLeavel']    = $officeLeavel;
            }else{
                // 请先添加一个分公司
                $message                    = ['code' => 403, 'message' => '无人可交接，请先添加一个公司'];
            }
        }else{
            // 不需要交接
            $message                        = ['code' => 403, 'message' => '分公司名下没有分销商和会员，不需要交接！'];
        }

        // 返回JSON格式数据
        $this->ajaxReturn($message); 
    }

    // 执行交接操作
    public function company_over()
    {
        // 获取原分公司ID
        $ycode = I('post.ycode');
        // 获取新分公司ID
        $xcode = I('post.xcode');

        // 准备修改条件
        $whereData['office_code'] = $ycode;
        // 准备修改数据
        $saveData['office_code']  = $xcode;

        // 实例化分销商模型
        $vendors = M('vendors');
        // 开启事务
        $vendors->startTrans();

        // 查询是否有分销商
        $is_vendor = $vendors->where($whereData)->find()['id'];
        // 查询是否有会员
        $is_userr = M('users')->where($whereData)->find()['id'];

        if($is_vendor){
            // 修改分销商上级分公司维新分公司
            $vendorsRes = $vendors->where($whereData)->save($saveData);
        }else{
            $vendorsRes = true;
        }

        if($is_userr){
            // 修改会员上级分公司
            $usersRes = M('users')->where($whereData)->save($saveData);
        }else{
            $usersRes = true;
        }

        // 如果分销商上级分公司和会员上级分公司修改成功
        if($vendorsRes && $usersRes){
            // 执行事务
            $vendors->commit();
            // 分公司交接成功
            $message     = ['code' => 200, 'message' => '分公司交接成功'];
        }else{
            // 事务回滚
            $vendors->rollback();
            // 分公司交接失败
            $message     = ['code' => 403, 'message' => '分公司交接失败'];
        }

        // 返回JSON格式数据
        $this->ajaxReturn($message);
    }

    /**
     * [company_forbidden 禁用分公司]
     * @return [type] [description]
     */
    public function company_forbidden()
    {   
        // 准备修改条件
        $whereData['id']    = I('post.id');
        // 准备修改数据
        $saveData['status'] = 8;
        // 执行修改操作
        $res = M('vendors')->where($whereData)->save($saveData);
        // 如果修改成功
        if($res){
            // 准备成功返回数据
            $message     = ['code' => 200, 'message' => '分公司禁用成功'];
        }else{
            // 准备失败返回数据
            $message     = ['code' => 403, 'message' => '分公司禁用失败'];
        }
        // 返回JSON格式数据
        $this->ajaxReturn($message);
    }

    /**
     * [company_forbidden 禁用分公司]
     * @return [type] [description]
     */
    public function company_start()
    {   
        // 准备修改条件
        $whereData['id']    = I('post.id');
        // 准备修改数据
        $saveData['status'] = 7;
        // 执行修改操作
        $res = M('vendors')->where($whereData)->save($saveData);
        // 如果修改成功
        if($res){
            // 准备成功返回数据
            $message     = ['code' => 200, 'message' => '分公司启用成功'];
        }else{
            // 准备失败返回数据
            $message     = ['code' => 403, 'message' => '分公司启用失败'];
        }
        // 返回JSON格式数据
        $this->ajaxReturn($message);
    }

    /**
     * [company_revise 修改分公司]
     * @return [type] [description]
     */
    public function company_revise(){
        // 接收数据，去除两边空白
        $name       = trim(I('post.name'));
        $phone      = trim(I('post.phone'));        
        $identity   = trim(I('post.identity'));        
        $companys   = trim(I('post.company'));        
        $telephone  = trim(I('post.telephone'));        
        $address    = trim(I('post.address'));        
        $password   = trim(I('post.password'));        
        $repassword = trim(I('post.repassword'));
        // 准备查询条件
        $showData['id']     = trim(I('post.number'));
        // 查询分公司原信息
        $company = M('vendors')->where($showData)->find();

        // 实例化验证类
        $validate   = new \Org\Util\Validate;

        // 判断姓名是否修改
        if($company['name'] != $name){
            // 验证是否合法用户名：字母或中文开头，数字字母下划线中文,2-30位
            if($validate->isName($name)){
                $data['name'] = $name;
            }else{
                $this->error('请输入真实性名！');
            }        
        }

        // 判断手机号码是否修改
        if($company['phone'] != $phone){
            // 验证是否合法用户名：字母或中文开头，数字字母下划线中文,2-30位
            if($validate->isMobilePhone($phone)){
                $data['phone'] = $phone;
            }else{
                $this->error('请输入正确的手机号码！');
            }        
        }

        // 判断身份证号码是否修改
        if($company['identity'] != $identity){
            // 验证是否合法用户名：字母或中文开头，数字字母下划线中文,2-30位
            if($validate->isIdentity($identity)){
                $data['identity'] = $identity;
            }else{
                $this->error('请输入正确的身份证号码！');
            }        
        }

        // 判断公司名称是否修改
        if($company['company'] != $companys){
            // 验证是否合法用户名：字母或中文开头，数字字母下划线中文,2-30位
            if($validate->isName($companys)){
                $data['company'] = $companys;
            }else{
                $this->error('公司名称格式不正确，请重新输入！');
            }        
        }

        // 判断座机号码是否修改
        if($company['telephone'] != $telephone){
            // 验证是否合法用户名：字母或中文开头，数字字母下划线中文,2-30位
            if($validate->isPhone($telephone)){
                $data['telephone'] = $telephone;
            }else{
                $this->error('座机号码格式不正确，请重新输入！');
            }        
        }

        // 判断公司地址是否修改
        if($company['address'] != $address){
            // 验证是否合法用户名：字母或中文开头，数字字母下划线中文,2-30位
            if($validate->isAddress($address)){
                $data['address'] = $address;
            }else{
                $this->error('请输入完整的公司地址');
            }        
        }

        // 判断密码是否为空
        if(!empty($password)){
            // 判断密码是否修改
            if($company['password'] != MD5($password)){
                // 判断两次输入密码是否一致
                if($password == $repassword){
                    $data['password'] = MD5($password);
                }else{
                    $this->error('两次输入的密码不一致！');
                }        
            }
        }

        // 处理图片
        $info = $this->upload();

        // 如果数据有修改，并且有上传图片
        if($data && $info){
            // 合并数据
            $newData = array_merge($data,$info);

        }elseif ($data) {
            // 如果只修改没有上传图片
            $newData = $data;
        }elseif($info){
            // 如果只上传图片
            $newData = $info;
        }else{
            // 没有修改数据
            $this->error('分公司您没有修改');
        }

        if(!empty($newData)){
            // 判断更新数据不为空
            // 执行更新
            $res = M('vendors')->where($showData)->save($newData);
            // 判断是否更新成功
            if($res){
                // 更新成功
                $this->error('分公司修改成功');
            }else{
                // 更新失败
                $this->error('分公司修改失败');
            }
        }
    }

    /**
     * [vendor_list 经销商列表]
     * @return [type] [description]
     */
    public function vendor_list()
    {
        $data = D('vendors')->vendorList();
        $assign = [
            'data' => $data,
        ];
        $this->assign($assign);
        $this->display();
    }


    /**
     * [vendor_reviewed 分销商审核列表]
     * @return [type] [description]
     */
    public function vendor_reviewed()
    {
        $data = D('vendors')->vendorReviewed();
        $assign = [
            'data' => $data,
        ];
        $this->assign($assign);
        $this->display();
    }

    /**
     * [reviewed 分销商审核失败]
     * @return [type] [description]
     */
    public function status()
    {
        // 更新条件
        $saveData['id'] = I('post.id');
        // 更新数据
        $data['status'] = I('post.status');
        // 审核-责任人
        $data['auditing'] = $_SESSION['adminInfo']['user'];
        // 执行更新
        $res = D('vendors')->where($saveData)->save($data);
        // 判断信息是否修改成功
        if($res){
            // 查询成功，设置返回前端的数据
            $message     = ['code' => 200, 'message' => '审核成功'];
        } else {
            // 查询失败，设置返回前端的数据
            $message     = ['code' => 403, 'message' => '审核失败'];
        }
        // 返回JSON格式数据
        $this->ajaxReturn($message);
    }

    /**
     * [reviewed 分销商审核成功]
     * @return [type] [description]
     */
    public function reviewed()
    {
        // 更新条件
        $saveData['id'] = I('post.id');
        // 更新数据
        $data['reviewed'] = I('post.reviewed');

        if($data['reviewed']==3){
            // 如果完成审核流程
            $data['status'] = 7;
        }
        // 审核-责任人
        $data['auditing'] = $_SESSION['adminInfo']['user'];
        // 执行更新
        $res = D('vendors')->where($saveData)->save($data);
        // 判断信息是否修改成功
        if($res){
            // 查询成功，设置返回前端的数据
            $message     = ['code' => 200, 'message' => '审核成功'];
        } else {
            // 查询失败，设置返回前端的数据
            $message     = ['code' => 403, 'message' => '审核失败'];
        }
        // 返回JSON格式数据
        $this->ajaxReturn($message);
    }

    /**
     * [vendor_data 分销商审核详情信息]
     * @return [type] [description]
     */
    public function reviewed_data()
    {
        // 接收查询的ID号
        $showData['id'] = I('post.id');
        // 根据ID查询分公司详细信息
        $data = M('vendors')->where($showData)->find();
        // 判断信息是否查询成功
        if($data){
            // 查询成功，设置返回前端的数据
            $message['res']     = ['code' => 200, 'message' => 'OK'];
            $message['data']    = $data;
        } else {
            // 查询失败，设置返回前端的数据
            $message['res']     = ['code' => 403, 'message' => '查询失败'];
            $message['data']    = null;
        }
        // 返回JSON格式数据
        $this->ajaxReturn($message);
    }

    /**
     * [upload 图片上传]
     * @return [type] [description]
     */
    public function upload()
    {
        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize   =     3145728;// 设置附件上传大小3MB
        $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg','doc','docx');// 设置附件上传类型
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
          //查询销商标识是否存在
          $oid = M('vendors')->where("`code`='{$code}'")->field('id')->find();
          // 如果订单号已存在再重新获取一次
        } while ($oid);

        // 绝对唯一的32位订单ID号
        return $code;
    }
}
