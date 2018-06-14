<?php
namespace Home\Controller;
use \Org\Util\WeixinJssdk;
use Think\Controller;
use Common\Tool\Work;
/**
 * 服务站 工作人员
 */

class ServicePeopleController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        if(empty($_SESSION['servicepeople'])){
            $this->redirect("Home/ServiceLogin/peopleIndex");
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

        $map['sid'] = $_SESSION['serviceInfo']['id'];

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
            ->field('id,kname,kphone,create_at')
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


    // 代缴费 搜索
    



}
