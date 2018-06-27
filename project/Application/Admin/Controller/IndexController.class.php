<?php
namespace Admin\Controller;
use Admin\Controller\CommonController;
use Think\Controller;

class IndexController extends CommonController
{
    public function index()
    {
        $this->display();
    }

    public function welcome()
    {
    	if (IS_AJAX) {
    		// 充值数额统计数量 （本月列表显示）
	    	$flows = D('Flow')->getTotalByEveryDay();

	    	$devices = D('Devices')->getTotalByEveryDay();
	    	// 订单数量统计
	    	$orders = D('ShopOrder')->field('count(distinct(order_id)) as total')->select();
	    	// 保修数量统计->保修列表 
	    	$repairs['total'] = D('Repair')->count();	    	

	    	// 总用户量统计->用户列表
	    	$feeds['total'] = D('Users')->count();

	    	//倒数7天添加的会员数
	    	$users = $this->getUserCount();

	    	//当天时间段充值套餐的用户数
	    	$flowUsers = $this->getFlowUsers();
	
	    	$data = [
	    		'flows' => $flows,
	    		'devices'=> $devices,
	    		'orders' => $orders[0],
	    		'repairs' => $repairs,
	    		'feeds' => $feeds,
	    		'users' => $users,
	    		'flowUsers' => $flowUsers
	    	];
	    	// print_r($data);
	    	$this->ajaxReturn($data);
    	} else {
    		$this->display();
    	}       
    }

    /**
     * [getUserCount 获取倒数七天的用户新增数量]
     * @return [type] [description]
     */
    public function getUserCount()
    {
    	//当天凌晨0点0时0分的时间戳
    	$todaystart7 = strtotime(date("Y-m-d"),time());
    	//当天结束时间 
    	$todayend7 = $todaystart7 + 24*60*60;
    	$map['addtime'] = array('between',array($todaystart7,$todayend7));
    	$users[7]['count'] = M('Users')->where($map)->count();
    	$users[7]['today'] = date('Y-m-d',time());

    	$todaystart6 = strtotime(date("Y-m-d"),time()) - 1*24*60*60; 
    	$todayend6 = $todaystart7;
    	$map['addtime'] = array('between',array($todaystart6,$todayend6));
    	$users[6]['count'] = M('Users')->where($map)->count();
    	$users[6]['today'] = date('Y-m-d',time()-1*24*60*60);

    	
    	$todaystart5 = strtotime(date("Y-m-d"),time()) - 2*24*60*60; 
    	$todayend5 = $todaystart6;
    	$map['addtime'] = array('between',array($todaystart5,$todaystart5));
    	$users[5]['count'] = M('Users')->where($map)->count();
    	$users[5]['today'] = date('Y-m-d',time()-2*24*60*60);

    	
    	$todaystart4 = strtotime(date("Y-m-d"),time()) - 3*24*60*60; 
    	$todayend4 = $todaystart5;
    	$map['addtime'] = array('between',array($todaystart4,$todayend4));
    	$users[4]['count'] = M('Users')->where($map)->count();
    	$users[4]['today'] = date('Y-m-d',time()-3*24*60*60);

    	
    	$todaystart3 = strtotime(date("Y-m-d"),time()) - 4*24*60*60; 
    	$todayend3 = $todaystart4;
    	$map['addtime'] = array('between',array($todaystart3,$todaystart3));
    	$users[3]['count'] = M('Users')->where($map)->count();
    	$users[3]['today'] = date('Y-m-d',time()-4*24*60*60);

    	
    	$todaystart2 = strtotime(date("Y-m-d"),time()) - 5*24*60*60; 
    	$todayend2 = $todaystart3;
    	$map['addtime'] = array('between',array($todaystart2,$todayend2));
    	$users[2]['count'] = M('Users')->where($map)->count();
    	$users[2]['today'] = date('Y-m-d',time()-5*24*60*60);
    	
    	$todaystart1 = strtotime(date("Y-m-d"),time()) - 6*24*60*60; 
    	$todayend1 = $todaystart2;
    	$map['addtime'] = array('between',array($todaystart1,$todayend1));
    	$users[1]['count'] = M('Users')->where($map)->count();
    	$users[1]['today'] = date('Y-m-d',time()-6*24*60*60);

    	return $users;
    }

    /**
     * [getFlowUsers 当天时间段充值的用户数量]
     * @return [type] [description]
     */
    public function getFlowUsers()
    {
    	//当天凌晨0点0时0分的时间戳
    	$times = strtotime(date("Y-m-d"),time());

    	//8:00-12:00
    	$starttime1 = $times + 8*60*60;
    	$endtime1 = $times + 12*60*60;
    	$map['addtime'] = array('between',array($starttime1,$endtime1));
	    $flowUsers[1] = M("flow")->where($map)->field("count(*) as count,describe")->group("`describe`")->select();

	    //12:00-16:00
    	$starttime2 = $times + 12*60*60;
    	$endtime2 = $times + 16*60*60;
    	$map['addtime'] = array('between',array($starttime2,$endtime2));
	    $flowUsers[2] = M("flow")->where($map)->field("count(*) as count,describe")->group("`describe`")->select();

	    //16:00-20:00
    	$starttime3 = $times + 16*60*60;
    	$endtime3 = $times + 20*60*60;
    	$map['addtime'] = array('between',array($starttime3,$endtime3));
	    $flowUsers[3] = M("flow")->where($map)->field("count(*) as count,describe")->group("`describe`")->select();

	    //20:00-24:00
    	$starttime4 = $times + 20*60*60;
    	$endtime4 = $times + 24*60*60;
    	$map['addtime'] = array('between',array($starttime4,$endtime4));
	    $flowUsers[4] = M("flow")->where($map)->field("count(*) as count,describe")->group("`describe`")->select();

	    return $flowUsers;
    }
}