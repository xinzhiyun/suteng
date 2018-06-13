<?php
namespace Home\Controller;
use \Org\Util\WeixinJssdk;
use Common\Tool\Work;
/**
 * 服务站 工作人员
 */

class ServicePeopleController extends ServiceCommonController
{
    // 任务列表
    public function servicePersonal()
    {
        $this->display();
    }
    // 任务列表
    public function list()
    {
        $this->display('service/list');
    }

    // 获取列表
    public function getList()
    {
        $p = I('p',1);
        $_GET['p']=$p;

        $map['sid'] = $_SESSION['serviceInfo']['id'];

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

    // 工单完成
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
                E('工单不存在',400022);
            }

            $data['update_at']=time();
            $data['result'] = 2;
            $res = M('work')->where($map)->save($data);

            if ($res) {
                Work::add($info['id'], 5); //完成
                E('提交成功!',200);
            }else{
                E('提交失败,请重试!',400001);
            }
        } catch (\Exception $e) {
            $this->toJson($e);
        }
    }



}
