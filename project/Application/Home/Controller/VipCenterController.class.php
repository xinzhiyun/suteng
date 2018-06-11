<?php
namespace Home\Controller;

class VipCenterController extends CommonController
{
    public function _initialize()
    {
        parent::_initialize();
        // 准备查询条件
        $showUser['open_id'] = $_SESSION['open_id'];
        $vendors = M('vendors')->field('status,reviewed')->where(['open_id'=>$showUser['open_id']])->find();

        if ($vendors) {
            //审核完毕 更改钻石会员经销商
            if ($vendors['status'] == 7 && $vendors['reviewed']==3) {
                M('users')->where($showUser)->save(['grade'=>4]);
            }
        }
    }

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

        //如果是分销商，则从分销商出获取会员分享码
        if ($user['ticket'] == '0') {
            $vInfo = M('vendors')->where($showUser)->find();

            $user['ticket'] = $vInfo['vendor_user'];
        }


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

        $code = session('user.code');
        // 获取用户唯一标识
        $uWhere['c.user_code'] = array('EQ',$code);
        $uWhere['c.nexus_user'] = array('NEQ',$code);
    
        $orderNum = 0;
        $orderData = M('users_commission')
            ->alias('c')
            ->where($uWhere)->field('c.id')
            ->select();
        $orderNum = count($orderData);
        // 分配数据

        // dump($user);

        $this->assign('user',$user);
        $this->assign('comData',$comData);
        $this->assign('orderNum',$orderNum);
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
        if(!empty($_GET['machineNo'])){
            $device = M('devices')->where('device_code='.$_GET['machineNo'])->find();
            if(empty($device))$this->error('改设备不存在,请联系经销商.');

        }
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
        $did = session('user.did');
        
        if( empty($did) ){
            $did = M('user_device')->where(['uid'=> session('user.id'),'status'=>1])->getField('did');
            session('user.did',$did);
        }
        $map['dcode'] = M('devices')->where('id='.$did)->getField('device_code');
        $map['date'] = date("Ym", time());
        $data = M('Tds')->where($map)->select();
        // dump($data);
        $this->assign('data', $data);
        $this->display();
    }

    // 查询净水记录
    public function getTds()
    {
        try {
            $where['id'] = session('device.did');

            $map['dcode'] = M('devices')->where($where)->getField('device_code');
            $map['date'] = I('post.month');
            $data = M('Tds')->where($map)->select();
            $this->ajaxReturn($data);
        } catch (\Exception $e) {
            $err = [
                'code' => $e->getCode(),
                'msg' => $e->getMessage(),
            ];
            $this->ajaxReturn($err);
        }
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
    /*
     * 充值年费
     */
    public function  gradeSelect() {
        $this->wx_info();
        $annual = M('annual')->find();
        $this->assign('annual',$annual);
        $this->display();
    }
    /*
     * 生成充值年费订单
     */
    public function Usersorder() {


        $mall =A("Home/Mall");
//        处理订单信息
        $data   = I('post.');
        $data['order_id'] = $mall->getOrderId();
        $data['user_id']= session('user.id');
        $data['create_time'] = date('Y-m-d H:i:s');
        $data['is_pay'] = 0;

        // 实例化订单模型
        $users_order = M('users_order');
        $annual_find = M('annual')->find();
        switch ($data['annual_status']) {
            case '1':
                $data['price'] = $annual_find['annual_money'];
                $data['mon_ey'] = $annual_find['annual_money']/365;
                $data['name'] = '钻石年费充值';
                break;
            case '2':
                $data['price'] = $annual_find['medal_money'];
                $data['mon_ey'] = $annual_find['medal_money']/365;
                $data['name'] = '黄金年费充值';
                break;
            case '3':
                $data['price'] = $annual_find['personal_money'];
                $data['mon_ey'] = $annual_find['personal_money']/365;
                $data['name'] = '个人年费充值';
                break;
        }
        //查询当前用户
        $user_info = M('users')->field('id,invitation_code,open_id,grade,start_time,end_time')->where(['id'=> $data['user_id']])->find();
        //会员升级金钱
        if ($user_info['grade']  > 1) {
            switch ($user_info['grade']) {
                case '4':
                    //每天的会员费
                    $data['mo_ey'] = $annual_find['annual_money'] / 365;
                    break;
                case '3':
                    $data['mo_ey'] = $annual_find['medal_money'] / 365;
                    break;
                case '2':
                    $data['mo_ey'] = $annual_find['personal_money'] / 365;
                    break;
            }
            $time = intval(($user_info['end_time']-time())/86400);
            $data['price']= (round($data['mon_ey'], 2)-round($data['mo_ey'], 2)) * $time ;
        }



        // 开启事务
        $users_order->startTrans();
        // 创建订单
        $ordersRes = $users_order->add($data);


        if($ordersRes){
            // 执行事务
            $users_order->commit();
            // 准备订单数据
            // 充值金额
            $money =   $data['price']-0;
            // 订单号码
            $order_id = $ordersRes['order_id'];
            // 订单描述
            $contentstr = $data['name'];

            // 描述超长处理
            $content = substr($contentstr, 0, 100);

            // 订单创建成功，跳转到支付页面
            $this->ajaxReturn($data['order_id']);
        }else{
            // 事务回滚
            $users_order->rollback();
            $this->error('订单创建失败');
        }

    }
    // 会员订单
    public function user_order()
    {
        $uid = session('user.id');
        $code = D('users')->where(['id'=>$uid])->find();
        $code = $code['code'];

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

            $message    = ['code' => 200, 'message' => '会员订单数据查询成功！!', 'redata' => $reData,'data'=>$data];
        }else{
            // 没有会员订单
            $message    = ['code' => 403, 'message' => '暂无会员订单，赶紧去邀请吧!', 'redata' => $reData,'data'=>$data];
        }

        // echo '<pre>';
        // print_r($message);
        // 返回JSON格式数据
        $this->ajaxReturn($message);

    }
    
    
    //会员 服务记录
    public function service_record()
    {
        $this->display();
    }
    public function get_service_record()
    {
        $p = I('p',1);
        $_GET['p']=$p;

        $map['uid'] =session('user.id');

        $total =  M('work')->where($map)->count();
        if(empty($total)){
            $this->toJson(['data'=>[]],'获取成功!');
        }
        $page  = new \Think\Page($total,10);
        $work_data =  M('work')->where($map)
            ->limit($page->firstRow.','.$page->listRows)
            ->select();
        
        $this->toJson(['data'=>$work_data],'获取成功!');
    }

    //会员 服务记录
    public function record_detail()
    {
        $this->display();
    }

    // 工单信息(时间线)
    public function showWorkTimeInfo()
    {
        try {
            $number = I('number');
            if( empty($number ) ) {
                E('请确认工单号',400022);
            }
            $info = M('work')->where('number='.$number)->find();
            if( empty($info ) ) {
                E('工单信息不存在',400022);
            }
            $list = M('work_note')->where('wid='.$info['id'])->order('id desc')->select();
            $evaluaction = 0;
            if($info['result'] ==3){
                $evaluaction = 1;
            }
            $res = [
                'data'=>$list,
                'evaluaction'=> $evaluaction
            ];
            $this->toJson([],'获取成功!');

        } catch (\Exception $e) {
            $this->toJson($e);
        }
    }


}