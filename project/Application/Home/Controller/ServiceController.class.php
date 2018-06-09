<?php
namespace Home\Controller;
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
        $map['sid'] = $sid;
        $map['is_examine']=1;
        M('work')->where($map)->select();

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
        $map['is_examine'] = 1;
        if(!empty($_GET['word'])){
            $map['number'] = ['like','%'.$_GET['word'].'%'];
        }

        //处理结果 (0：待处理(服务站) 1处理中(工作人员) 2已完成(工作人员) 3(完成) 9 工单关闭 )
        if(isset($_GET['result'])){
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

            if($info['type'] == 0){
                $info['device_info'] = M('shop_order_device_install')
                    ->alias('i')
                    ->where('i.id='.$info['install_id'])
                    ->field('i.gname')
                    ->find();
            }else{
                $info['device_info'] = M('devices')
                    ->alias('d')
                    ->where('device_code='.$info['device_code'])
                    ->join('__TYPE__ t ON d.type_id=t.id', 'LEFT')
                    ->field('t.typename gname')
                    ->find();
            }

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
                E('工单不存在',400022);
            }

            if ( empty($post['name']) || empty($post['phone']) || empty($post['dw_uid']) ) {
                E('请确认服务人员的信息',400022);
            }

            if (empty($post['anry_time']) || empty($post['anry_period'])) {
                E('请确认预约时间',400022);
            }

            unset($post['number']);

            $post['update_at']=time();
            $post['anry_at']=time();
            $post['result'] = 1;
            $res = M('work')->where($map)->save($post);

            if ($res) {
                Work::add($info['id'], 4); //派遣
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
                Work::add($info['id'], 6); //验收
                E('派工成功!',200);
            }else{
                E('派工成功,请重试!',400001);
            }
        } catch (\Exception $e) {
            $this->toJson($e);
        }
    }

}
