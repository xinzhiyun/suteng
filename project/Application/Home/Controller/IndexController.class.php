<?php
namespace Home\Controller;

use Think\Controller;
use \Org\Util\WeixinJssdk;
class IndexController extends CommonController
{
    public function index()
    {
    	// // 佣金
     //   	$yj = 1000;
     //   	// 金币
     //   	$jb = 2000;
     //   	// 银币
     //   	$yb = 3000;
     //   	// 单号
     //   	$order_id = '123456';
     //   	// 用户open_id
     //   	$open_id = $_SESSION['user']['open_id'];
       	
    	// echo $this->branch_commission($open_id,$order_id,$yj,$jb,$yb);
    	$this->display();
    }

    /**
     * [filledwater 水满]
     * @return [type] [description]
     */
    public function filledwater()
    {

    	$this->display();
    }

    /**
     * [queshui 缺水]
     * @return [type] [description]
     */
    public function queshui()
    {

    	$this->display();
    }

    /**
     * [filledwater 修检]
     * @return [type] [description]
     */
    public function overhaul()
    {

    	$this->display();
    }

    /**
     * [makewater 制水]
     * @return [type] [description]
     */
    public function makewater()
    {

    	$this->display();
    }

    /**
     * [makewater 冲洗]
     * @return [type] [description]
     */
    public function chongxi()
    {

    	$this->display();
    }
}


