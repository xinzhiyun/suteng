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
        $map['ud.uid'] = session('user.id');
        $map['ud.status'] = 1;
        // // 执行查询
        $user = M('users')->where($showUser)->find();
        $device = D('UserDevice')->getBindInof($map)[0];
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

    // AJAX请求评论数据
    public function ajaxMy()
    {
        try {
            $com = D('Comment');
            $map['c.uid'] = session('user.id');
            $map['so.status'] = 3;
            $data = $com->getInfo($map);
            dump($data);die;
            $this->ajaxReturn($data);
        } catch (\Exception $e) {
            $err = [
                'code' => $e->getCode(),
                'msg' => $e->getMessage(),
            ];
            $this->ajaxReturn();
        }
    }



    /**
     * [reward 个人信息]
     * @return [type] [description]
     */
    public function personalInformation()
    {
        $map['uid'] = session('user.id');
        $map['status'] = 1;
        $user_device = M('user_device')->where($map)->find();
        $this->assign('user_device',$user_device);
        $this->display();
    }

    public function infomationAction()
    {
        try {
            $user_device = D('UserDevice');
            $data = I('post.');
            $map['uid'] = session('user.id');
            $map['status'] = 1;
            $res = $user_device->where($map)->field('name,sex,birth,phone,address')->find();

            if($res && ($res != $data)){
                // 实例化验证类
                $validate   = new \Org\Util\Validate;
                if($data['name'] != $res['name']){
                    if($validate->isName($data['name'])){
                        $saveData['name'] = $data['name'];
                    }else{
                         E('用户名格式不正确',200);
                    }
                }

                if($data['sex'] != $res['sex']){
                    if($validate->original('/^[0,1]{1}$/',$data['sex'])){
                        $saveData['sex'] = $data['sex'];
                    }else{
                         E('性别格式不正确',200);
                    }
                }

                if($data['birth'] != $res['birth']){
                        $saveData['birth'] = $data['birth'];
                }

                if($data['phone'] != $res['phone']){
                    // if($validate->isPhone($data['phone'])){
                        $saveData['phone'] = $data['phone'];
                    // }else{
                    //      E('手机号码格式不正确',200);
                    // }
                }

                if($data['address'] != $res['address']){
                    // if($validate->isAddress($data['address'])){
                        $saveData['address'] = $data['address'];
                    // }else{
                    //      E('地址格式格式不正确',200);
                    // }
                }

                $user_device->startTrans();
                $data['updatetime'] = time();
                $save_status = $user_device->where($map)->save($saveData);
                if($res){
                    $user_device->commit();
                    E('OK',200);
                } else {
                    $user_device->rollback();
                    E('添加失败',603);
                }
            }else{
                E('您没有修改！',200);
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

    // 我的奖励金接口
    public function get_reward()
    {
        // 获取用户唯一标识
        $uWhere['u.id'] = session('user.id');

        $data = M('users')
            ->alias('u')
            ->where($uWhere)
            ->join('__USERS_COMMISSION__ c ON c.user_code = u.code','LEFT')
            ->select(); 

        // 返回数据
        if(empty($data)){
            $message    = ['code' => 403, 'message' => '暂无奖励金!'];    
        }else{
            $total_gold_num = 0;
            $total_silver = 0;

            foreach ($data as $key => $value) {
                $total_gold_num += $value['gold_num'];
                $total_silver += $value['silver'];
            }
            $message    = ['code' => 200, 'message' => '奖励金数据查询成功!','total_gold_num'=>$total_gold_num,'total_silver'=>$total_silver,'data'=>$data];
        }
        // echo '<pre>';
        // print_r($message);

        // 返回JSON格式数据
        $this->ajaxReturn($message);
    }
    
    // 会员订单
    public function user_order()
    {
        // 获取用户唯一标识
        $uWhere['c.user_code'] = session('user.code');
       $data = M('users_commission')
            ->alias('c')
            ->where($uWhere)
            ->select();

        echo '<pre>';
        print_r($data);
    }
}


