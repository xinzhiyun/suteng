<?php
namespace Home\Controller;
use Common\Tool\File;
use Common\Tool\Weixin;
use \Org\Util\WeixinJssdk;
use Common\Tool\Work;
/**
 * 服务站
 */
class ServiceController extends ServiceCommonController
{
    // 主页
    public function index()
    {
        $sid = $_SESSION['serviceInfo']['id'];
        $service = M('service')->find($sid);

        $work['name'] = $service['company'];

        $map['sid'] = $sid;
        $map['is_examine']=1;

        $workModel = M('work');

        $map['result'] = 0;
        $work[1] = $workModel->where($map)->count();

        $map['result'] = 2;
        $work[2] = $workModel->where($map)->count();

        $this->assign('work',$work);
        $this->display();
    }
    
    //任务列表
    public function list()
    {
        $this->display();
    }
    // 获取列表
    public function getList()
    {
        $p = I('p',1);
        $_GET['p']=$p;

        $map['sid'] = $_SESSION['serviceInfo']['id'];

        if($_GET['result']==4){  //服务站人员
            $total = M('service_users')
                ->where($map)
                ->count();
            if(empty($total)){
                $this->toJson(['data'=>[]],'获取成功');
            }
            $page  = new \Think\Page($total,10);
            $list = M('service_users')
                ->where($map)
                ->limit($page->firstRow.','.$page->listRows)
                ->select();
            $this->toJson(['data'=>$list],'获取成功');
        }

        $map['is_examine'] = 1;
        if(!empty($_GET['word'])){
            $map['number'] = ['like','%'.$_GET['word'].'%'];
        }

        //处理结果 (0：待处理(服务站) 1处理中(工作人员) 2已完成(工作人员) 3(完成) 9 工单关闭 )
        if(isset($_GET['result']) && strlen($_GET['result'])){
            $map['result'] = $_GET['result'];
        }

        $total = M('work')
            ->where($map)
            ->count();
        if(empty($total)){
            $this->toJson(['data'=>[]],'获取成功');
        }

        $page  = new \Think\Page($total,10);
        $list = M('work')
            ->where($map)
            ->limit($page->firstRow.','.$page->listRows)
            ->select();
        $this->toJson(['data'=>$list],'获取成功');
    }

    // 工单详情
    public function detail()
    {
        $this->display();
    }
    // 详情页
    public function getDetail()
    {
        try {
            $number = I('number');
            if(empty($number)){
                E('工单号错误',400001);
            }
            $map['sid'] =$_SESSION['serviceInfo']['id'];

            $list = M('service_users')->where($map)->select();   // 服务人员


            $info = M('work')->where('number='.$number)->find(); //工单信息

//            if($info['type'] == 0){
//                $info['device_info'] = M('shop_order_device_install')
//                    ->alias('i')
//                    ->where('i.id='.$info['install_id'])
//                    ->field('i.gname')
//                    ->find();
//            }else{
//                $info['device_info'] = M('devices')
//                    ->alias('d')
//                    ->where('device_code='.$info['device_code'])
//                    ->join('__TYPE__ t ON d.type_id=t.id', 'LEFT')
//                    ->field('t.typename gname')
//                    ->find();
//            }

            $this->toJson(['data'=>$info,'people'=>$list],'获取成功');
        } catch (\Exception $e) {
            $this->toJson($e);
        }

    }


    // 工单派工
    public function packwork()
    {
        try {
            $post = I('post.');
            if( empty($post['number']) ){
                E('工单信息错误',400022);
            }

            $map['number'] = $post['number'];
            $info = M('work')->where($map)->find();
            if( empty($info) ){
                E('工单不存在!',400022);
            }

            $uinfo = M('service_users')->where('id='.$post['dw_uid'])->find();
            if( empty($uinfo) ){
                E('服务人员错误!',400023);
            }


            if ( empty($post['name']) || empty($post['phone']) || empty($post['dw_uid']) ) {
                E('请确认服务人员的信息',400022);
            }

            if (empty($post['anry_time']) || empty($post['anry_period'])) {
                E('请确认预约时间',400022);
            }

            unset($post['number']);

            $post['sn']=$uinfo['sn'];

            $post['update_at']=time();
            $post['anry_at']=time();
            $post['refuse'] = 0;
            $post['result'] = 1;
            $res = M('work')->where($map)->save($post);

            if ($res) {
                Work::add($info['id'], 5); //派遣
                E('派工成功!',200);
            }else{
                E('派工成功,请重试!',400001);
            }
        } catch (\Exception $e) {
            $this->toJson($e);
        }
    }

    // 服务站验收工单
    public function checkPass()
    {
        try {
            $post = I('post.');
            if( empty($post['number']) ){
                E('工单信息错误',400022);
            }

            $map['number'] = $post['number'];
            $info = M('work')->where($map)->find();
            if( empty($info) ){
                E('工单不存在',400022);
            }

            if ( $info['result'] != 2) {
                E('工单未完成请联系工作人员!',400022);
            }

            $res = M('work')->where($map)->save(['result'=>3,'update_at'=>time(),'check_at'=>time()]);

            if ($res) {
                Work::add($info['id'], 8); //验收
                E('验收成功!',200);
            }else{
                E('验收失败,请重试!',400001);
            }
        } catch (\Exception $e) {
            $this->toJson($e);
        }
    }

    // 服务站人员
    public function installInfo()
    {
        $this->wx_info();
        $uid = I('uid');
        if($uid){
            $info = M('service_users')->where('id='.$uid)->find();
            $this->assign('info',$info);
        }
        $this->display();
    }

    // 添加安装人员
    public function postPeople()
    {
        try {
            $post = I('post.');

            if(empty($post['action'])){
                E('参数错误',40001);
            }

            if(!empty($post['uImg'])){
                $addData['uImg'] = File::downloadPic('service',$post['uImg']);
            }

            $addData['sn'] = $post['sn'];
            $addData['idCard'] = $post['idCard'];
            $addData['name'] = $post['name'];
            $addData['phone'] = $post['phone'];

            $addData['updatetime'] = time();

            if($post['action']==1){//添加
                $addData['sid'] = $_SESSION['serviceInfo']['id'];
                $addData['addtime'] = time();
                $addData['password'] = md5($post['password']);
                $addData['star'] = 4;
                $res = M('service_users')->add($addData);
                if($res){
                    E('添加成功!',200);
                }
            }elseif ($post['action']==2){//编辑
                if(!empty($post['password'])){
                    $addData['password'] = md5($post['password']);
                }
                if(empty($post["uid"])){
                    E('参数错误:uid',40001);
                }
                $res = M('service_users')->where('id='.$post["uid"])->save($addData);
                if($res){
                    E('编辑成功!',200);
                }
            }elseif($post['action']==3){//删除
                if(empty($post["uid"])){
                    E('参数错误:uid',40001);
                }
                $res = M('service_users')->where('id='.$post["uid"])->delete();
                if($res){ E('删除成功!',200); }
            }else{
                E('参数错误',40001);
            }

            E('操作失败,请重试!',202);

        } catch (\Exception $e) {
            $this->toJson($e);
        }

    }

    // 微信 解绑
    public function removeWeixin()
    {
        try {
            $saveData = [
                'open_id'=>'',
                'wxname'=>'',
                'updatetime'=>time()
            ];
            $res = M('admin_user')->where(['id'=>$_SESSION['serviceInfo']['auid']])->save($saveData);

            if ($res) {
                E('解绑成功!',200);
            }else{
                E('解绑失败,请重试!',400001);
            }
        } catch (\Exception $e) {
            $this->toJson($e);
        }
    }

    // 微信 绑定
    public function bindingWeixin()
    {
        $data = Weixin::getWeiXinUserInfo($_SESSION['open_id']);
        if(!empty($data['open_id'])){
            $saveData = [
                'open_id'=> $data['open_id'],
                'wxname'=>  $data['nickname'],
                'updatetime'=>time()
            ];
            $res = M('admin_user')->where(['id'=>$_SESSION['serviceInfo']['auid']])->save($saveData);
            if($res){
                notice('绑定成功!','index');
            }
        }
        notice('绑定失败,请重试!','index');
    }

}
