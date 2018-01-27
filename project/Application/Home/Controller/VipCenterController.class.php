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
 		$this->display();
    }

    /**
     * [vipCenter 会员中心列表页]
     * @return [type] [description]
     */
    public function vipCenter()
    {
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


