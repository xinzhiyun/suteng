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
        }



        $area = M('area')->where('parentid=0')->select();

        $count = M('service')->where($map)->count();
        $Page       = new \Think\Page($count,15);
        $data = M('service')->where($map)
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

    public function addAccount()
    {
        $service_id = I('post.service_id');
        $data['user'] = I('post.user');
        $data['password'] = I('post.password');
        $data['repassword'] = I('post.repassword');
        $rules = array(
            array('user','','帐号名称已经存在！',0,'unique',1), // 在新增的时候验证name字段是否唯一
            array('repassword','password','确认密码不正确',0,'confirm'), // 验证确认密码是否和密码一致
            array('password','checkPwd','密码格式不正确',0,'function'), // 自定义函数验证密码格式
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

}