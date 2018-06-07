<?php
namespace Home\Controller;
use \Org\Util\WeixinJssdk;
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
            $info = M('work')->where('number='.$number)->find();
            $this->toJson(['data'=>$info],'获取成功');
        } catch (\Exception $e) {
            $this->toJson($e);
        }

    }


}
