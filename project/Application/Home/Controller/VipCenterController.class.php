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
        $showUser['open_id'] = $_SESSION['open_id'];
        // // 执行查询
        $user = M('users')->where($showUser)->find();

        // 查询会员收益合计
        $showCommission['user_code'] = $user['code'];
        $commission = M('users_commission')->where($showCommission)->select();
        // dump($commission);die;

        $comData['gold_num'] = 0;
        $comData['silver'] = 0;
        foreach ($commission as $key => $value) {
            $comData['gold_num'] += $value['gold_num'];
            $comData['silver'] += $value['silver'];
        }
        // 分配数据
        $this->assign('user',$user);
        $this->assign('comData',$comData);
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
        // 准备查询条件
        $showUser['open_id'] = $_SESSION['open_id'];
        // // 执行查询
        $user = M('users')->where($showUser)->find();
        $device = D('UserDevice')->getBindInof(session('user.id'))[0];
        // 分配数据
        $this->assign('user',$user);
        $this->assign('device',$device);
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

    public function infomationAction()
    {
        try {
            $user_device = D('UserDevice');
            $data = I('post.');
            $res = $user_device->add();
            if($res){
                E('OK',200);
            } else {
                E('false',603);
            }
        } catch (\Exception $e) {
            $err = [
                'code' => $e->getCode(),
                'msg' => $e->getMessage(),
            ];
            $this->ajaxReturn($err);
        }
    }

    // 净水记录
    public function purificationNotes()
    {
        $this->display();
    }

    // 用水记录
    public function usedNotes()
    {
        $this->display();
    }
    

}


