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
     * [vendor_fee 分销商加盟费]
     * @return [type] [description]
     */
    public function vendor_fee()
    {
        if(IS_POST){
            $data = I('post.');

            // 准备查询条件
            $showData['id'] = 1;
            // 查询佣金分配比例
            $vendor_fee = M('vendor_fee')->where($showData)->field('vendor_a,vendor_b,vendor_c')->find();

            if($data==$vendor_fee){
                $message = ['code' => 403, 'message' =>'您没有修改分销商加盟费！'];
            }else{
                // 实例化验证类
                $validate   = new \Org\Util\Validate;
                // 判断A级分销商加盟费是否修改
                if($data['vendor_a'] != $vendor_fee['vendor_a']){
                    // 验证是加盟费格式
                    if($validate->original('/^[\d]{1,18}[\.][\d]{2}$/',$data['vendor_a'])){
                        $saveData['vendor_a'] = $data['vendor_a'];
                    }else{
                        $message = ['code' => 403, 'message' =>'A级分销商加盟费格式试不正确，请检测！'];
                    }
                }

                // 判断B级分销商加盟费是否修改
                if($data['vendor_b'] != $vendor_fee['vendor_b']){
                    // 验证是加盟费格式
                    if($validate->original('/^[\d]{1,18}[\.][\d]{2}$/',$data['vendor_b'])){
                        $saveData['vendor_b'] = $data['vendor_b'];
                    }else{
                        $message = ['code' => 403, 'message' =>'B级分销商加盟费格式试不正确，请检测！'];
                    }
                }

                // 判断C级分销商加盟费是否修改
                if($data['vendor_c'] != $vendor_fee['vendor_c']){
                    // 验证是加盟费格式
                    if($validate->original('/^[\d]{1,18}[\.][\d]{2}$/',$data['vendor_c'])){
                        $saveData['vendor_c'] = $data['vendor_c'];
                    }else{
                        $message = ['code' => 403, 'message' =>'C级分销商加盟费格式试不正确，请检测！'];
                    }
                }

                // 最后一次更新责任人
                $name = $saveData['name'] = $_SESSION['adminInfo']['user'];
                // 更新时间
                $saveData['updatetime'] = time();

                // 执行更新
                $saveRes = M('vendor_fee')->where($showData)->save($saveData);

                if($saveRes){
                    $message = ['code' => 200, 'message' =>'加盟费更新成功！','name'=>$name];
                }else{
                    $message = ['code' => 603, 'message' =>'加盟费更新失败，请刷新浏览后重试！'];
                }
                
            }
            
            // 返回JSON格式数据
            $this->ajaxReturn($message);  
        }else{
            // 准备查询条件
            $showData['id'] = 1;
            // 查询加盟费
            $vendor_fee = M('vendor_fee')->where($showData)->find();
            // 分配数据
            $this->assign('data',$vendor_fee);
            // 显示模板
            $this->display(); 
        }


    }

    /**
     * [company_add 分公司添加]
     * @return [type] [description]
     */
    public function company_add(){
        if(IS_POST){
            // dump(I('post.'));die;
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
                     $this->error($vendors->getError());
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
        $map = '';
        if (!empty(I('get.key')) && !empty(I('get.keywords'))) {
            $map[I('get.key')] = array('like',"%".trim(I('get.keywords'))."%");
        }

        $data = D('vendors')->companyListPage($map);
        $assign = [
            'data' => $data['list'],
            'page' => bootstrap_page_style($data['page'])
        ];
        $this->assign($assign);
        $this->display();
    }

    /**F
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
        if( $office['status'] == 8 ) E('禁用的分公司不能进行交接', 606);
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
            $showLeavel['status'] = ['neq',8];
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
        $map = '';
        if (!empty(I('get.key')) && !empty(I('get.keywords'))) {
            $map[I('get.key')] = array('like',"%".trim(I('get.keywords'))."%");
        }

        strlen(I('get.status')) ? $map['status'] = I('get.status') : '';
        strlen(I('get.leavel')) ? $map['leavel'] = I('get.leavel') : '';
        // ---- 解决非第一页搜索条件$_GET['p']不等于1的情况【start】
        if(I('sou')){
            $_GET['p'] = 1;
            unset($_GET['sou']);
        }
        // ---- 【end】
        $data = D('vendors')->vendorList($map);

        $assign = [
            'data' => $data['list'],
        ];
        $where['key'] = I('get.key');
        $where['keywords'] = I('get.keywords');
        $where['status'] = I('get.status');
        $where['leavel'] = I('get.leavel');

        //echo D('vendors')->getLastSql();
        $this->assign($assign);
        $this->assign('page',bootstrap_page_style($data['page']));
        $this->assign('where',$where);
        $this->display();
    }


    /**
     * [vendor_reviewed 分销商审核列表]
     * @return [type] [description]
     */
    public function vendor_reviewed()
    {
        $map = '';
        if (!empty(I('get.key')) && !empty(I('get.keywords'))) {
            $map[I('get.key')] = array('like',"%".trim(I('get.keywords'))."%");
        }
        $data = D('vendors')->vendorReviewed($map);


        // dump($data);die;
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
//            $data['updatetime'] = time();
//            $res = D('vendors')->where($saveData)->find($data);

            $flag = true;
            $argv = 1;

            // 判断信息是否修改成功
            if($res){

                if ($data['reviewed'] == 3) {

                    $where['id'] = I('post.id');

                    $info = M('vendors')->field('invitation_code,id')->where($where)->find();
                    //它的上级
                    $path_code = M('vendors')->field('path,id,leavel,invitation_code,name')->where(['code'=>$info['invitation_code']])->find();


                    if ($path_code['path']==null) {
                        //当他推荐人是最大的时候
                        $path = $path_code['id'];

                    } else{
                        //当他推荐人还有推荐人的时候
                        $path = $path_code['path'].'-'.$path_code['id'];
                    }

                    $buros_info = M('butros')->field('trader_a,trader_b')->find();

                    //多少个儿子
                    $count = M('vendors')->where(['invitation_code'=>$info['invitation_code']])->count();


                    $save_path = M('vendors')->where($where)->save(['path'=>$path,'updatetime'=>time()]);

                    if ($save_path) {

                        $cc_map['leavel'] =  array(array('eq',3),array('eq',4),array('eq',2), 'or');
                        $cc_map['invitation_code'] = $info['invitation_code'];
    //                    $cc_map['status'] = 7;
                        //查找多少个他下面多少个C b级
                        $count_c = M('vendors')->where($cc_map)->count();


    //                    //查看他下面有多少个B级
    //                    $map['path']=array('like',$info['id']);
    //                    $map['leavel'] = 3;
    //                    $count_bb  =  M('vendors')->where($map)->count();
    //                    $count_c = intval($count_bb)+intval($count_c);

                        if ($count_c >= $buros_info['trader_a']) {
    //                        if ($path_code['leavel'] == 3) {
    //                            exit;
    //                        }


                            if ($path_code['leavel'] >= 3) {

                                $day = M('vendors')->where(['code'=>$info['invitation_code']])->save(['leavel'=>3,'updatetime'=>time()]);

                                $tmp[] = '-'.$path_code['id'].'-';

                                if ($day) {
                                    $code = $path_code['invitation_code'];

                                    do{
                                        if (empty($path_code['invitation_code'])) {
                                            $flag = false;
                                        }

                                        if ($flag) {

                                            $tion = M('vendors')->where(['code'=>$code])->find();

//                                            $tmp[] = '-'.$tion['id'].'-';
                                            $path = json_decode($tion['records_b'],true);

                                            foreach ($tmp as $item) {

                                                if(in_array($item,$path)){ // 在升级历史中

                                                    $flag = false;
                                                    break;
                                                }
                                            }

                                            if($flag){ //通过升级检测

                                                if (empty($path)) {
                                                    $path = [];
                                                }

                                                $newpath = array_merge($path,$tmp);//新的历史路径

                                                $nwe_class_a = $tion['class_b']+1;
                                                //                                                $nwe_class_b =  $buros_info['trader_b']+$nwe_class_a;
                                                $newData = ['class_b'=>$nwe_class_a,'records_b'=>json_encode($newpath)];
                                                if ($nwe_class_a >= $buros_info['trader_b']) {
                                                    $newData['leavel'] = 2;
                                                }


                                                M('vendors')->where('id='.$tion['id'])->save($newData);

                                                $code = $tion['code'];
                                            }
                                            // if ($tion['leavel'] >3) {

                                            //     $flag = false;
                                            // } else {
//                                            $tmp[] = '-'.$tion['id'].'-';
//                                            dump($tmp);
                                            //     $path = json_decode($tion['records_b'],true);
                                            //     foreach ($tmp as $item) {
                                            //         if(in_array($item,$path)){ // 在升级历史中
                                            //             $flag = false;
                                            //             break;
                                            //         }
                                            //     }
                                            //     if($flag){ //通过升级检测
                                            //         if (empty($path)) {
                                            //             $path = [];
                                            //         }
                                            //         $newpath = array_merge($path,$tmp);//新的历史路径
                                            //         $nwe_class_a = $tion['class_b']+1;
                                            //         // $nwe_class_b =  $buros_info['trader_b']+$nwe_class_a;
                                            //         $newData = ['class_b'=>$nwe_class_a,'records_b'=>json_encode($newpath)];
                                            //         if ($nwe_class_a >= $buros_info['trader_b']) {
                                            //             $newData['leavel'] = 2;
                                            //         }



                                            //         M('vendors')->where('id='.$tion['id'])->save($newData);

                                            //         $code = $tion['code'];
                                            //     }
                                            // }
                                        }


                                    }while($flag);
    //                                $vv_map['path'] = array('like', "%".$path_code['id']."%");
    //                                $a = M('vendors')->where($vv_map)->order('id desc')->find();
    //
    //                                //直线网络B级个数
    //                                $b_path = explode('-',$path_code['path']);
    //                                $b_map['id']  = array('in',$b_path);
    //                                $b_map['records_b']=array('notlike',$path_code['id']);
    //                                $b_select = M('vendors')->where($b_map)->select();

                                    //找他上级关系下面有多少个 B C级
    //                                $c_map['id']  = array('in',$b_path);
    ////                                $c_map['leavel'] =       array(array('eq',3),array('eq',4), 'or') ;
    //
    //                                $a_count = M('vendors')->where($c_map)->select();
    //
    //                                foreach ($a_count as $v) {
    //                                    $d_map['leavel'] =       array(array('eq',3),array('eq',4), 'or') ;
    //                                    $d_map['path']=array('like', "%".$v['id']."%");
    //                                    $a_count = M('vendors')->where($d_map)->count();
    //
    //                                }





                                    //查看他下面有多少个B级
                                    //添加直接网络直接任务个数与关系
    //                                foreach ($b_select as $v) {
    //
    ////                                    dump($ccc_info);
    ////
    //                                    if ($v['records_b'] == 0) {
    //                                        $bb_map['id'] = $v['id'];
    //                                        $b_data['records_b'] = $path_code['id'].'-';
    //                                        $b_data['class_b'] = ++$v['class_b'];
    //                                        $b_data['updatetime'] = time();
    //                                    } else {
    //                                        $bb_map['id'] = $v['id'];
    //                                        $b_data['class_b'] = ++$v['class_b'];
    //                                        $b_data['records_b'] = $v['records_b'].'-'.$path_code['id'];
    //                                        $b_data['updatetime'] = time();
    //                                    }
    //                                    $succ = M('vendors')->where($bb_map)->save($b_data);
    //                                    if ($succ) {
    ////                                        $count_bb  =  M('vendors')->where($map_a)->count();
    //                                        if ($v['class_b'] >=$buros_info['trader_b']) {
    //                                            M('vendors')->where(['id'=>$v['id']])->save(['leavel'=>2]);
    //                                        }
    //                                    }
    //                                }

                                }
                            } else {
                                $day = M('vendors')->where(['code'=>$info['invitation_code']])->find();
                            }


    //                        if ($day) {
    //                            $count_a = M('vendors')->where(['invitation_code'=>$path_code['invitation_code'],'leavel'=>3])->count();
    //                            $map['path']=array('like',$path_code['id']);
    //                            $map['leavel'] = 2;
    //                            $count_aa  =  M('vendors')->where($map)->count();
    //                            if ($count_aa >=$buros_info['trader_b'] ) {
    //
    //                                M('vendors')->where(['code'=>$path_code['invitation_code']])->save(['leavel'=>2]);
    //
    //                            }
    //                        }
                        }

    //                    if ($path_code['leavel'] == 3) {
    //
    //                        $count_b = M('vendors')->where(['invitation_code'=>$info['invitation_code'],'leavel'=>3])->count();
    //                        // 查找父亲
    //                        $grandpa =  M('vendors')->where(['invitation_code'=>$path_code['invitation_code'],'leavel'=>3])->count();
    //
    //                        if ($count_b >=$buros_info['trader_b'] ) {
    //
    //
    //                            M('vendors')->where(['code'=>$info['invitation_code']])->save(['leavel'=>2]);
    //                        }
    //                    }

                    }
                }
                exit;
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

    /**
     * 修改经销商密码
     */
    public function updateMima($id)
    {
        if (IS_POST) {
            //密码加密
            $data['password'] = md5($_POST['password']);

            //判断两次密码是否一致
            if ($_POST['password'] == $_POST['repassword']) {

                $info = D("Vendors")->where('id='.$_POST['id'])->save($data);

                if ($info) {
                    $this->success('密码修改成功','vendor_list');
                } else {
                    $this->error('密码修改失败');
                }
            } else {
                $this->error('两次输入的密码不一致！');
            }

        } else {
            $vlist = D("Vendors")->field('id,name')->find($id);
            $this->assign('vlist',$vlist);
            $this->display();
        }
        
    }

     // 设置服务站后台登录账号
     public function addAccount()
     {
         $vendor_id = I('post.vendor_id');
         $data['user'] = I('post.user');
         $data['password'] = I('post.password');
         $data['repassword'] = I('post.repassword');
         $rules = array(
             array('user','','帐号名称已经存在！',0,'unique',1), // 在新增的时候验证name字段是否唯一
             array('user','require','帐号名称必须！'), //默认情况下用正则进行验证
             array('user', '3,150', '帐号名称必须长于3位', '0', 'length'),
             array('repassword','password','确认密码不正确',0,'confirm'), // 验证确认密码是否和密码一致
             array('password','require','密码必须！'), //默认情况下用正则进行验证
             array('password','checkPwd','密码格式不正确',0,'function'), // 自定义函数验证密码格式
             array('password', '5,50', '密码必须长于5位', '0', 'length'),
        );
        $User = M("adminUser"); // 实例化User对象
        if (!$User->validate($rules)->create($data)){
             // 如果创建失败 表示验证没有通过 输出错误提示信息
             $this->ajaxReturn(['code'=>10001,'msg'=>$User->getError()]);
        }else{
             // 验证通过 可以进行其他数据操作
        }
         // 判断账号是否注册过
         $auid = D('vendors')->find($service_id);
         if($auid['auid']){
             $this->ajaxReturn(['code'=>10002,'msg'=>'该经销商已经注册过后台账号']);
         }
         // 注册账号
         $uid = $User->add([
             'user'=>I('post.user'),
             'password'=>md5(I('password')),
             'type'=>2,
             'addtime'=>time(),
             'updatetime'=>time()
         ]);
         $res = D('vendors')->where(['id'=>$vendor_id])->setField(['auid'=>$uid]);
         if($res){
             $this->ajaxReturn(['code'=>200,'msg'=>'用户添加成功','data'=>$data]);
         }else{
             $this->ajaxReturn(['code'=>400,'msg'=>'系统出现错误,请稍后重试......']); 
         }
     }
    
     // 修改服务站后台账号密码
    public function editpassword()
    {
        $id = I('post.admin_user_id');
        $data['oldpassword'] = I('post.oldpassword');
        $data['password'] = I('post.password');
        $data['repassword'] = I('post.repassword');
   
       $rules = array(
            array('oldpassword','require','旧密码必须！'), //默认情况下用正则进行验证
            array('repassword','password','确认密码不正确',0,'confirm'), // 验证确认密码是否和密码一致
            array('password','require','密码必须！'), //默认情况下用正则进行验证
            array('password','checkPwd','密码格式不正确',0,'function'), // 自定义函数验证密码格式
            array('password', '5,50', '密码必须长于5位', '0', 'length'),
        );
       $User = M("adminUser"); // 实例化User对象
       if (!$User->validate($rules)->create($data)){
            // 如果创建失败 表示验证没有通过 输出错误提示信息
            $this->ajaxReturn(['code'=>10001,'msg'=>$User->getError()]);
       }else{
            // 验证通过 可以进行其他数据操作
       }
        // 判断旧密码是否正确
        $userinfo = $User->find($id);
        if($userinfo['password'] != md5($data['oldpassword'])){
            $this->ajaxReturn(['code'=>10002,'msg'=>'旧密码不正确']);
        }
        // 注册账号
        $uid = $User->where(['id'=>$id])->save([
            'password'=>md5($data['password']),
            'updatetime'=>time()
        ]);
        if($uid){
            $this->ajaxReturn(['code'=>200,'msg'=>'修改密码成功','data'=>$data]);
        }else{
            $this->ajaxReturn(['code'=>400,'msg'=>'系统出现错误,请稍后重试......']); 
        }
    }
}
