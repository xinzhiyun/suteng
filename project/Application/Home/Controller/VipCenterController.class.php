<?php
namespace Home\Controller;

class VipCenterController extends CommonController
{
	/**
	 * [index 会员中心首页]
	 * @return [type] [description]
	 */
    public function index()
    {
        // 准备查询条件
        // $showUser['open_id'] = $_SESSION['open_id'];
        // // 执行查询
        // $user = M('users')->where($showUser)->find();

        // // 查询会员收益合计
        // $showCommission['user_code'] = $user['code'];
        // $commission = M('users_commission')->where($showCommission)->select();
        
        // // $comData = 
        // // foreach ($commission as $key => $value) {
        // //     $value
        // // }
        // // 分配数据
        // $this->assign('user',$user);
        // dump($user);
        // 显示模板
 		$this->display();
    }

    /**
     * [vipCenter 会员中心列表页]
     * @return [type] [description]
     */
    public function vipCenter()
    {
        // 准备查询条件
        $showUser['open_id'] = $_SESSION['open_id'];
        // 执行查询
        $user = M('users')->where($showUser)->find();
        // 分配数据
        $this->assign('user',$user);
        // dump($user);
 		$this->display();
    }

	/**
	 * [mine 钱包]
	 * @return [type] [description]
	 */
    public function minepack()
    {
 		$this->display();
    }

	/**
	 * [reward 我的金银币]
	 * @return [type] [description]
	 */
    public function reward()
    {
 		$this->display();
    }

    /**
     * [reward 没有金银币]
     * @return [type] [description]
     */
    public function noReward()
    {
        $this->display();
    }



    /**
     * [reward 商品评价]
     * @return [type] [description]
     */
    public function mycomment()
    {
        $this->display();
    }



    /**
     * [reward 个人信息]
     * @return [type] [description]
     */
    public function personalInformation()
    {
        $this->display();
    }
    

}


