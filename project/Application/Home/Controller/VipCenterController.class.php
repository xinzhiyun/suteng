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
        $code = session('user.code');
        // 获取用户唯一标识
        $uWhere['c.user_code'] = array('EQ',$code);
        $uWhere['c.nexus_user'] = array('NEQ',$code);
    
        $data = M('users_commission')
            ->alias('c')
            ->where($uWhere)
            ->join('__USERS__ u ON u.code = c.nexus_user','LEFT')
            ->join('__SHOP_ORDER__ o ON o.order_id = c.order_id','LEFT')
            ->field('u.nickname,o.g_price,c.gold_num,c.silver,c.addtime')
            ->select();

        $reData['tatal_num']    = 0;
        $reData['tatal_gold']   = 0;
        $reData['tatal_silver'] = 0;

        if($data){
            // 统计会员订单总数
            $reData['tatal_num'] = count($data);

            foreach ($data as $key => $value) {
                $reData['tatal_gold'] += $value['gold_num'];
                $reData['tatal_silver'] += $value['silver'];
            }
            $reData['data'] = $data;

            $message    = ['code' => 200, 'message' => '会员订单数据查询成功！!', 'redata' => $reData];
        }else{
            // 没有会员订单
            $message    = ['code' => 403, 'message' => '暂无会员订单，赶紧去邀请吧!', 'redata' => $reData];
        }

        // echo '<pre>';
        // print_r($message);
        // 返回JSON格式数据
        $this->ajaxReturn($message);

    }
}
// Array
// (
//     [0] => Array
//         (
//             [nickname] => 快乐翱翔
//             [g_price] => 500.00
//             [gold_num] => 0
//             [silver] => 1
//             [addtime] => 1519442351
//         )

//     [1] => Array
//         (
//             [nickname] => 快乐翱翔
//             [g_price] => 1000.00
//             [gold_num] => 1
//             [silver] => 2
//             [addtime] => 1518259208
//         )

// )

// Array
// (
//     [0] => Array
//         (
//             [id] => 255
//             [nexus_user] => 96B1944669C
//             [user_code] => 5BBED4D4B5F
//             [order_id] => 815782408320460
//             [gold_num] => 50000450
//             [silver] => 903
//             [current_gold_num] => 407
//             [current_silver] => 1817
//             [describe] => 自己购买，单号为[815782408320460]的商品获得金币奖励:0.72个,获得银币奖励：1.44个
//             [type] => 0
//             [status] => 9
//             [addtime] => 1518242765
//             [updatetime] => 1519608241
//             [open_id] => fgs
//             [office_code] => 653A61
//             [vendora_code] => 
//             [vendorb_code] => 
//             [vendorc_code] => 
//             [vendori_code] => 
//             [invitation_code] => 67037D
//             [code] => 96B1944669C
//             [invite] => 0
//             [ticket] => gQGy7zwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyZVVUamtFMmJkOTAxRXpxQXhxMXgAAgTzFH1aAwQwhScA
//             [parameter] => 27
//             [ticket_time] => 1520736803
//             [nickname] => 快乐翱翔
//             [head] => http://thirdwx.qlogo.cn/mmopen/8RP9wLClmUicB78UOvFu61ibbk8DRhmMnRK79oAKfibo9ficu1aVSzaFEjHoYD7oYHrRlw5K4S9zEnkL0TsfpvvibGf5cwURvpKVJ/132
//             [sex] => 0
//             [area] => 广东
//             [address] => 中国 广东 广州
//             [balance] => 0.00
//             [original_grade] => 0
//             [total_money] => 100.00
//             [grade] => 3
//             [uid] => 35
//             [address_id] => 52
//             [gid] => 
//             [g_cost] => 380.00
//             [g_price] => 500.00
//             [g_num] => 1
//             [g_type] => 2
//             [mode] => 0
//             [express] => 
//         )

//     [1] => Array
//         (
//             [id] => 279
//             [nexus_user] => 96B1944669C
//             [user_code] => 5BBED4D4B5F
//             [order_id] => 764154273195985
//             [gold_num] => 50000450
//             [silver] => 903
//             [current_gold_num] => 1301
//             [current_silver] => 3
//             [describe] => 自己购买，单号为[764154273195985]的商品获得金币奖励:1.44个,获得银币奖励：2.88个
//             [type] => 0
//             [status] => 9
//             [addtime] => 1518259196
//             [updatetime] => 1519608241
//             [open_id] => fgs
//             [office_code] => 653A61
//             [vendora_code] => 
//             [vendorb_code] => 
//             [vendorc_code] => 
//             [vendori_code] => 
//             [invitation_code] => 67037D
//             [code] => 96B1944669C
//             [invite] => 0
//             [ticket] => gQGy7zwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyZVVUamtFMmJkOTAxRXpxQXhxMXgAAgTzFH1aAwQwhScA
//             [parameter] => 27
//             [ticket_time] => 1520736803
//             [nickname] => 快乐翱翔
//             [head] => http://thirdwx.qlogo.cn/mmopen/8RP9wLClmUicB78UOvFu61ibbk8DRhmMnRK79oAKfibo9ficu1aVSzaFEjHoYD7oYHrRlw5K4S9zEnkL0TsfpvvibGf5cwURvpKVJ/132
//             [sex] => 0
//             [area] => 广东
//             [address] => 中国 广东 广州
//             [balance] => 0.00
//             [original_grade] => 0
//             [total_money] => 100.00
//             [grade] => 3
//             [uid] => 35
//             [address_id] => 52
//             [gid] => 
//             [g_cost] => 760.00
//             [g_price] => 1000.00
//             [g_num] => 2
//             [g_type] => 2
//             [mode] => 0
//             [express] => 
//         )

// )