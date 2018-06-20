<?php
namespace Admin\Controller;


use Common\Tool\File;

class ServiceController extends CommonController
{
    /**
     * 服务站管理
     */
    public function index()
    {
        $map = [];

        if(I('sou')){
            $_GET['p'] = 1;
            unset($_GET['sou']);
        }
        if(!empty($_GET['province_id'])){
            $map['st_service.province_id'] = $_GET['province_id'];
            $city = M('area')->where('parentid='.$_GET['province_id'])->select();
        }
        if(!empty($_GET['city_id'])){
            $map['st_service.city_id'] = $_GET['city_id'];
            $district = M('area')->where('parentid='.$_GET['city_id'])->select();
        }
        if(!empty($_GET['district_id'])){
            $map['st_service.district_id'] = $_GET['district_id'];
        }
        if(!empty($_GET['status'])){
            $map['st_service.status'] = $_GET['status'];
        }
        if(!empty($_GET['keywords'])){
            $map['st_service.'.$_GET['key']] = array('like',"%".$_GET['keywords']."%");
        }
        if(isset($_GET['status']) && $_GET['status'] != '' ){
            $map['st_service.status'] = $_GET['status'];
        }
        
        $area = M('area')->where('parentid=0')->select();

        $count = M('service')->where($map)->count();
        $Page       = new \Think\Page($count,15);
        $data = M('service')->where($map)
            ->join('__ADMIN_USER__ ad ON st_service.auid=ad.id', 'LEFT')
            ->limit($Page->firstRow.','.$Page->listRows)
            ->field('st_service.*,ad.user auser')
            ->select();
        page_config($Page);
        $show       = $Page->show();

        $assign = [
            'area' => $area,
            'city'=>$city,
            'district'=>$district,
            'data' => $data,
            'page'=> bootstrap_page_style($show),
        ];
        $this->assign($assign);
        $this->display();
    }

    //服务站详情
    public function getServiceInfo()
    {
        try {
            $post = I('post.');
            if(empty($post['id']) ){
                E('数据错误',40001);
            }

            $res = M('service')->where('id='.$post['id'])->find();
            if(empty($res)) {E('无数据',200);}
            $res['admin_user']=M('admin_user')->find($res['auid']);
            $this->toJson(['data'=>$res],'获取成功',200);

        } catch (\Exception $e) {
            $this->toJson($e);
        }
    }

    /**
     * 服务站申请
     */
    public function apply()
    {
        $map = [];

        if(I('sou')){
            $_GET['p'] = 1;
            unset($_GET['sou']);
        }
        if(!empty($_GET['province_id'])){
            $map['province_id'] = $_GET['province_id'];
            $city = M('area')->where('parentid='.$_GET['province_id'])->select();
        }
        if(!empty($_GET['city_id'])){
            $map['city_id'] = $_GET['city_id'];
            $district = M('area')->where('parentid='.$_GET['city_id'])->select();
        }
        if(!empty($_GET['district_id'])){
            $map['district_id'] = $_GET['district_id'];
        }
        if(!empty($_GET['status'])){
            $map['status'] = $_GET['status'];
        }
        if(!empty($_GET['keywords'])){
            $map[$_GET['key']] = array('like',"%".$_GET['keywords']."%");
        }
        if(isset($_GET['status']) && $_GET['status'] != '' ){
            $map['status'] = $_GET['status'];
        }else{
            $map['status'] = ['gt',0];
        }

        $count = M('service_apply')->where($map)->count();
        $Page = new \Think\Page($count,15);
        $data = M('service_apply')->where($map)
            ->limit($Page->firstRow.','.$Page->listRows)
            ->select();
        page_config($Page);
        $show       = $Page->show();

        $assign = [
            'area' => $area,
            'city'=>$city,
            'district'=>$district,
            'data' => $data,
            'page'=> bootstrap_page_style($show),
        ];
        $this->assign($assign);
        $this->display();
    }

    // 服务站的申请详情
    public function getApplyInfo()
    {
        try {
            $post = I('post.');
            if(empty($post['id']) ){
                E('数据错误',40001);
            }

            $res = M('service_apply')->where('id='.$post['id'])->find();
            if(empty($res)) {E('无数据',200);}
            $this->toJson(['data'=>$res],'获取成功',200);

        } catch (\Exception $e) {
            $this->toJson($e);
        }
    }

    // 服务站申请 - 审核操作
    public function applyExamine()
    {
        try {
            $post = I('post.');
            if(empty($post['id']) || empty($post['status'])){
                E('数据错误',40001);
            }

            $saveData=[];
            if ($post['action'] == '1') { // 基础信息审核
                if ($post['status'] == 1 ) {
                    $saveData['status'] = 3;
                } else {
                    $saveData['status'] = 2;
                }

            } elseif ($post['action'] == '2') { //支付审核 (开通)
                if ($post['status'] == 1 ) {
                    // 开通服务站
                    $info = M('service_apply')->where('id='.$post['id'])->find();
                    if(empty($info['sid'])){
                        E('服务站参数为空',200);
                    }
                    if(empty($this->openApplyService($info['sid'], $info))){
                        E('开通失败!',40002);
                    }
                    $saveData['status'] = 5;
                } else {
                    $saveData['status'] = 3;
                }
            } else {
                E('数据错误',40001);
            }

            $saveData['updatetime'] = time();

            $res = M('service_apply')->where('id='.$post['id'])->save($saveData);
            if ($res) {
                E('开通成功',200);
            }
            E('添加失败,请重试!',201);

        } catch (\Exception $e) {
            $this->toJson($e);
        }
    }
    
    

    /**
     * 添加服务站
     */
    public function addService()
    {
        try {
            if(!empty($_FILES)){
                $picinfo =  File::upload('service');
                $_POST['pic'] = is_array($picinfo)?$picinfo[0]:$picinfo;
            }

            $_POST['addtime'] = time();
            $_POST['updatetime'] = time();

            $_POST['addressinfo'] = $_POST['province'].$_POST['city'].$_POST['district'].$_POST['address'];
            $_POST['status'] = 1;

            $res = M('service')->add($_POST);
            if ($res) {
                E('开通成功',200);
            }
            E('添加失败,请重试!',201);

        } catch (\Exception $e) {
            $this->toJson($e);
        }
    }

    /**
     * 开通服务站(申请模式)
     */
    public function openApplyService($sid,$data)
    {
        try {

            $old = M('service')->find($sid);
            if(empty($old))E('服务站不存在',201);

            $saveData['addtime'] = time();
            $saveData['updatetime'] = time();

            $saveData['addressinfo'] = $data['province'].$data['city'].$data['district'].$data['addressinfo'];
            $saveData['address']   = $data['addressinfo'];
            $saveData['telephone'] = $data['telephone'];
            $saveData['phone'] = $data['phone'];
            $saveData['name']  = $data['name'];
            $saveData['auid']  = $data['auid'];
            $saveData['status'] = 1;

            // 开通账号

            M('admin_user')->where('id='.$data['auid'])->save(['status'=>1, 'updatetime'=>time()]);

            return M('service')->where('id='.$sid)->save($saveData);
        } catch (\Exception $e) {
            $this->toJson($e);
        }
    }

    /**
     * 开通服务站
     */
    public function openService()
    {
        try {
            if(empty($_POST['id']))E('数据错误',201);

            if(empty($_POST['address']) || empty($_POST['phone']) || empty($_POST['name']) || empty($_POST['company']) || empty($_POST['telephone'])){
                E('参数错误',40002);
            }

            $old = M('service')->find($_POST['id']);
            if(empty($old))E('服务站不存在',201);

            if(!empty($_FILES)){
                $picinfo =  File::upload('service');
                $savedata['pic'] = is_array($picinfo)?$picinfo[0]:$picinfo;
            }

            $_POST['addtime'] = time();
            $_POST['updatetime'] = time();

            $savedata['addressinfo'] = $old['province'].$old['city'].$old['district'].$_POST['address'];
            $savedata['address'] = $_POST['address'];
            $savedata['status'] = 1;

            M('service')->where('id='.$_POST['id'])->save($savedata);

            E('开通成功',200);

        } catch (\Exception $e) {
            $this->toJson($e);
        }
    }

    /**
     * 设置代管
     */
    public function setAgent()
    {
        try {
            $data = I('post.');

            if(empty($data['id']) || empty($data['t_id']) || empty($data['mode']) || empty($data['t_company'])){
                E('数据错误',40002);
            }
            $map['id'] = $data['id'];
            $savetata['t_id']  = $data['t_id'];
            $savetata['t_company'] = $data['t_company'];


            if($data['mode']==1){
                $savetata['status']  = 2;
            }else{
                $savetata['status']  = 3;
            }
            $res = M('service')->where($map)->save($savetata);
            if($res) {
                E('修改成功',200);
            } else {
                E('修改失败,请重试!',201);
            }



        } catch (\Exception $e) {
            $this->toJson($e);
        }
    }
    /**
     * 代管加载
     * mode 1 服务站 2 第三方
     */
    public function getAgent()
    {
        try {
            $data = I('post.');

            $map_list =array('province_id'=>'','city_id'=>'','district_id'=>'');
            $map = array_intersect_key($data, $map_list);
            $map['status'] = 1;

            // 删除数组中为空的值
            $map = array_filter($map, function ($v) {
                if ($v != "") {
                    return true;
                }
                return false;
            });

            if(I('mode',1)==1){
                $list = M('service')->where($map)->select();
            }else{
                $list = M('service_other')->where($map)->select();
            }

            $this->toJson(['list'=>$list],'获取成功');

        } catch (\Exception $e) {
            $this->toJson($e);
        }
    }

    /**
     * 服务站详情
     */
    public function detail()
    {
        $this->display();
    }

    /**
     * 第三方机构
     */
    public function other()
    {
        $area = M('area')->where('parentid=0')->select();


        $count = M('service_other')->where($map)->count();
        $Page       = new \Think\Page($count,15);
        $data = M('service_other')->where($map)
            ->limit($Page->firstRow.','.$Page->listRows)
            ->select();
        page_config($Page);
        $show       = $Page->show();

        $assign = [
            'area' => $area,
            'data' => $data,
            'page'=> bootstrap_page_style($show),
        ];
        $this->assign($assign);
        $this->display();
    }

    public function othrtAdd()
    {
        try {
            if(!empty($_FILES)){
                $picinfo =  File::upload('service');
                $_POST['pic'] = is_array($picinfo)?$picinfo[0]:$picinfo;
            }
            $_POST['addtime'] = time();
            $_POST['updatetime'] = time();
            $_POST['addressinfo'] = $_POST['province'].$_POST['city'].$_POST['district'].$_POST['address'];
            $_POST['status'] = 1;

            M('service_other')->add($_POST);

            E('创建成功',200);

        } catch (\Exception $e) {
            $this->toJson($e);
        }
    }



    /**
     * 设置预设服务站
     */
    public function preset()
    {
        try {
            $area = M('area')->where('parentid=0')->select();
            $i = 0;
            foreach ($area as $item) {
                $area_item = M('area')->where('parentid='.$item['id'])->select();

                foreach ($area_item as $area_item_list){

                    $item_list = M('area')->where('parentid='.$area_item_list['id'])->select();
                    foreach ($item_list as $item_list_value) {
                        $map = [];
                        $map['province_id'] = $item['id'];
                        $map['city_id']     = $area_item_list['id'];
                        $map['district_id'] = $item_list_value['id'];
                        $service_item = M('service')->where($map)->find();
                        if(empty($service_item)) {
                            $map['company'] = $item_list_value['areaname'].'服务站';
                            $map['status']  = 0;
                            $map['addtime'] = time();
                            $map['province'] = $item['areaname'];
                            $map['city']     = $area_item_list['areaname'];
                            $map['district'] = $item_list_value['areaname'];
                            $map['addressinfo'] = $item['areaname'].$area_item_list['areaname'].$item_list_value['areaname'];

                            $res=M('service')->add($map);
                            if($res){
                                $i++;
                            }
                        }
                    }
                }
            }

            $this->toJson(array(
                'num'=>$i,
            ),'创建成功');

        } catch (\Exception $e) {
            $this->toJson($e);
        }
    }

    /**
     * 关闭
     */
    public function setClose()
    {
        try {
            $data = I('post.');
            if(empty($data['id'])){
                E('数据错误',40001);
            }
            $res = M('service')->where('id='.$data['id'])->save(['status'=>4,'updatetime'=>time()]);
            if($res){
                E('关闭成功',200);
            }else{
                E('关闭失败',40002);
            }

        } catch (\Exception $e) {
            $this->toJson($e);
        }
    }

    // 设置服务站后台登录账号
    public function addAccount()
    {
        $service_id = I('post.service_id');
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
        $auid = D('service')->find($service_id);    
        if($auid['auid']){
            $this->ajaxReturn(['code'=>10002,'msg'=>'该服务站已经注册过后台账号']);
        }
        // 注册账号
        $uid = $User->add([
            'user'=>I('post.user'),
            'password'=>md5(I('password')),
            'type'=>3,
            'addtime'=>time(),
            'updatetime'=>time()
        ]);
        $res = D('service')->where(['id'=>$service_id])->setField(['auid'=>$uid]);
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

    // 服务站人员管理
    public function people()
    {
        $area = M('area')->where('parentid=0')->select();
        $map = [];
        if(!empty($_GET['keywords'])){
            if($_GET['key']=='company'){
                $map['s.company']=['like',"%".$_GET['keywords']."%"];
            }
        }

        if(strlen($_GET['status'])){
            $map['st_service_users.status']=$_GET['status'];
        }

        $count = M('service_users')->where($map)->join('__SERVICE__ s ON st_service_users.sid=s.id', 'LEFT')->count();
        $Page = new \Think\Page($count,15);
        $data = M('service_users')->where($map)
            ->join('__SERVICE__ s ON st_service_users.sid=s.id', 'LEFT')
            ->limit($Page->firstRow.','.$Page->listRows)
            ->field('st_service_users.*,s.company company')

            ->select();
        page_config($Page);
        $show       = $Page->show();

        $assign = [
            'area' => $area,
            'city'=>$city,
            'district'=>$district,
            'data' => $data,
            'page'=> bootstrap_page_style($show),
        ];
        $this->assign($assign);
        $this->display();
    }

    //编辑服务人员
    public function setPeople()
    {
        try {
            $data = I('post.');
            if(empty($data['id'])){
                E('数据错误',40001);
            }
            if(!empty($data['password'])){
                $saveData['password'] = md5($data['password']);
            }
            $saveData['sn'] = $data['sn'];
            $saveData['name'] = $data['name'];
            $saveData['phone'] = $data['phone'];

            $res = M('service_users')->where('id='.$data['id'])->save($_POST);
            if($res){
                E('修改成功',200);
            }else{
                E('修改失败',40002);
            }

        } catch (\Exception $e) {
            $this->toJson($e);
        }
    }

    // 添加人员
    public function addServicePeople()
    {
        try {
            $post = I('post.');
            if (empty($post['sid']) ||
                empty($post['name']) ||
                empty($post['password']) ||
                empty($post['repassword']) ||
                empty($post['phone']) ||
                empty($post['sn']) )
            {
                E('数据不完整', 201);
            }

            if($post['password'] != $post['repassword']) E('两次的输入的密码不同,请重试!',400001);

            $re = M('service_users')->where('phone='.$post['phone'])->find();

            if(!empty($re)) E('手机号已被注册,请重试',400002);

            $post['password'] = md5($post['password']);
            $post['addtime'] = time();
            $post['updatetime'] = time();


            $res = M('service_users')->add($post);
            if($res) {
                E('添加成功',200);
            }else{
                E('添加失败,请重试',40009);

            }

        } catch (\Exception $e) {
            $this->toJson($e);
        }
    }

    // 设置人员状态
    public function setStatus()
    {
        try {
            $post = I('post.');
            if (empty($post['id']) || !isset($post['status']) ) {
                E('数据不完整', 201);
            }

            $res = M('service_users')->save($post);
            if($res) {
                E('修改成功',200);
            }else{
                E('修改失败,请重试',40009);

            }

        } catch (\Exception $e) {
            $this->toJson($e);
        }
    }

    public function getServiceUser()
    {
        try {
            $post = I('post.');
            if (empty($post['sid']) ) {
                E('数据不完整', 201);
            }
            $list = M('service_users')->where('sid='.$post['sid'])->select();
            $this->toJson(['data'=>$list],'获取成功');

        } catch (\Exception $e) {
            $this->toJson($e);
        }
    }

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
            $map['status'] = 1;
            $count = M('service')->where($map)->count();
            if(empty($count)){
                $this->toJson(['data'=>[]],'无数据,请重试!',40001);
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



    // 服务站参数设置
    public function setService(){
        $service_model = M('service_seting');

        if(IS_POST){

            $saveData['joinsost'] = (int)$_POST['joinsost']*100;
            $saveData['kfphone'] = $_POST['kfphone'];
            $saveData['updatetime'] = time();

            $res = $service_model->where('1')->save($saveData);
            if($res){
                $this->toJson([],'修改成功',200);
            }else{
                $this->toJson([],'修改失败',40002);
            }

        }else{
            $data = $service_model->find();
            $data['joinsost'] = number_format(intval(trim($data['joinsost']), 10)/100,2,'.','');

            $this->assign('data',$data);
            $this->display();
        }
    }

    // 服务评价设置
    public function evaluate()
    {
        $data = M('service_evaluate')->where('pid=0')->select();
        $this->assign('data',$data);

        $this->display();
    }

    /**
     * 加载下一级的评价项目
     */
    public function getNextEval()
    {
        $parentid = I('pid',0);

        $data = M('service_evaluate')->where('pid='.$parentid)
            ->order('sort')
            ->select();

        $this->toJson(['data'=>$data],'获取成功');
    }

    // 添加评价项
    public function evalAdd()
    {

        $res=M('service_evaluate')->add($_POST);

        if(empty($res)){
            $this->error('添加失败......');
        }else{
            $this->success('添加成功......');
        }
    }

    // 编辑评价项
    public function evalEdit()
    {
        $res=M('service_evaluate')->save($_POST);

        if(empty($res)){
            $this->error('编辑失败......');
        }else{
            $this->success('编辑成功......');
        }
    }
    // 编辑评价项
    public function evalDel($id)
    {
        $re=M('service_evaluate')->where('pid='.$id)->find();
        if($re){
            $this->error('有子评价项不能删除......');die;
        }

        $res=M('service_evaluate')->delete($id);

        if(empty($res)){
            $this->error('删除失败......');
        }else{
            $this->success('删除成功......');
        }
    }




}