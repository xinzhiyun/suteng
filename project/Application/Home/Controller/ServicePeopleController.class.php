<?php
namespace Home\Controller;
use \Org\Util\WeixinJssdk;
use Think\Controller;
use Common\Tool\Work;
use Common\Tool\Weixin;
/**
 * 服务站 工作人员
 */

class ServicePeopleController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        // 检查微信
        if(empty($_SESSION['open_id'])){
            $openid = Weixin::GetOpenid();
            $_SESSION['open_id']=$openid;
        }

        // 自动登录
        if(empty($_SESSION['servicepeople'])){
            $info = M('service_users')->where("open_id='{$_SESSION['open_id']}'")->find();
            if($info) {
                unset($info['password']);
                $_SESSION['servicepeople'] = $info;
            }else{
                $this->redirect("Home/ServiceLogin/peopleIndex");
            }
        }
    }

    public function index(){
        $_SESSION['servicepeople']['company'] = M('service')
            ->cache('cache_company_name',60)
            ->where('id='.$_SESSION['servicepeople']['sid'])
            ->getField('company');

        $work_model = M('work');

        $map = [
            'sid'=>$_SESSION['servicepeople']['sid'],
            'dw_uid'=>$_SESSION['servicepeople']['id'],
            'result'=>1
        ];
        $work['wait_install']  = $work_model->where($map)->where(['type'=>0])->count();
        $work['wait_repair']   = $work_model->where($map)->where(['type'=>1])->count();
        $work['wait_maintain'] = $work_model->where($map)->where(['type'=>2])->count();
        $this->assign('work',$work);
        $this->display();
    }

    // 获取服务(任务)列表
    public function getList()
    {
        $p = I('p',1);
        $_GET['p']=$p;

        $map['sid'] = $_SESSION['servicepeople']['sid'];

        strlen(I('type'))?$map['type'] = I('type'):'';
        $total = M('work')
            ->where($map)
            ->count('id');
        if(empty($total)){
            $this->toJson(['data'=>[]],'获取成功');
        }

        $page  = new \Think\Page($total,10);
        $list = M('work')
            ->where($map)
            ->limit($page->firstRow.','.$page->listRows)
//            ->field('id,kname,kphone,create_at')
            ->select();
        $this->toJson(['data'=>$list],'获取成功');
    }

    // 获取工单详情
    public function getDetail()
    {
        try {
            $post = I('post.');
            if( empty($post['wid']) ){
                E('工单信息错误',400022);
            }

            $map['id'] = $post['wid'];
            $info = M('work')->where($map)->find();
            if( empty($info) ){
                E('工单不存在',400022);
            }
            $this->toJson(['data'=>$info,200]);
        } catch (\Exception $e) {
            $this->toJson($e);
        }
    }

    // 工单完成
    public function passWork()
    {
        try {
            $post = I('post.');
            if( empty($post['wid']) ){
                E('工单信息错误',400022);
            }

            $map['id'] = $post['wid'];
            $info = M('work')->where($map)->find();
            if( empty($info) ){
                E('工单不存在',400022);
            }

            $data['update_at']=time();
            $data['result'] = 2;
            $res = M('work')->where($map)->save($data);

            if ($res) {
                Work::add($info['id'], 7); //完成
                E('提交成功!',200);
            }else{
                E('提交失败,请重试!',400001);
            }
        } catch (\Exception $e) {
            $this->toJson($e);
        }
    }

    // 用户信息
    public function servicePersonal()
    {
        $info = M('service_users')->where(['id'=>$_SESSION['servicepeople']['id']])->find();
        $this->assign('info',$info);
        $this->display();
    }
    
    //用户信息编辑
    public function setInfo()
    {
        try {
            $post = I('post.');
            if(!empty($post['opassword'])){
                if(empty($post['password'])){
                    E('密码不能为空!',40002);
                }

                $info = M('service_users')->where('id='.$_SESSION['servicepeople']['id'])->find();
                if (md5($post['opassword']) != $info['password']) {
                    E('旧密码不对,请重试!',40003);
                }

                $saveData['password'] = md5($post['password']);
            }

            if(!empty($post['name'])){
                $saveData['name'] = $post['name'];
            }
            if(!empty($post['phone'])){
                $saveData['phone'] = $post['phone'];
            }

            if (empty($saveData))E('无信息提交!',40001);

            $saveData['updatetime']=time();

            $res = M('service_users')->where('id='.$_SESSION['servicepeople']['id'])->save($saveData);
            if ($res) {
                E('修改成功!',200);
            }else{
                E('修改失败,请重试!',40001);
            }
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
            $res = M('service_users')->where(['id'=>$_SESSION['servicepeople']['id']])->save($saveData);

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
            $res = M('service_users')->where(['id'=>$_SESSION['servicepeople']['id']])->save($saveData);
            if($res){
                notice('绑定成功!','servicePersonal');
            }
        }
        notice('绑定失败,请重试!','servicePersonal');
    }
}
