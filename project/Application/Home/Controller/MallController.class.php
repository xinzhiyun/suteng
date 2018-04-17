<?php
namespace Home\Controller;
use \Org\Util\WeixinJssdk;

class MallController extends CommonController
{

//    /**
//     * [chongzhi 充值]
//     * @return [type] [description]
//     */
//    public function chongzhi()
//    {
//        // 查询条件
//        $showData['status'] = 1;
//        // 金币套餐
//        $data['gold'] = M('gold')->where($showData)->select();
//        // 银币套餐
//        $data['silver'] = M('silver')->where($showData)->select();
//        // 银币兑换套餐
//        $data['gold_silver'] = M('gold_silver')->where($showData)->select();
//        // 获取用户微信唯一标识
//        // $showUser['open_id'] = $_SESSION['user']['open_id'];
//        // // 查询用户信息
//        // $userData = M('users')->where($showUser)->find();
//        //调用微信JS-SDK类获取签名需要用到的数据
//        $weixin = new WeixinJssdk;
//        $signPackage = $weixin->getSignPackage();
//        // 查询用户微信中的openid
//        // $openId = $weixin->GetOpenid();
//        $openId = $_SESSION['open_id'];
//        // dump($data);die;
//        //分配数据
//        $this->assign('info',$signPackage);
//        $sss=A('Pay')->wx_info();
//        $this->assign('openId',$openId);
//        $this->assign('data',$data);
//        // dump($userData);die;
//        $this->display();
//    }




    /**
     * [lvxin 滤芯]
     * @return [type] [description]
     */
    public function lvxin()
    {

        $filter = D('Filters');
        $device = D('Devices');
        $devices['d.id'] = session('device.did');
        $res = $filter->getFilters($devices);
        $data = $device
            ->alias('d')
            ->where($devices)
            ->join('__DEVICES_STATU__ ds ON d.device_code=ds.DeviceID', 'LEFT')
            ->find();

        $assign = [
            'res' => json_encode($res),
            'data' => json_encode($data),
        ];
        $this->assign('data',$assign);
        $this->display();
    }

    /**
     * 设备充值--信息确认并生成订单
     */
    public function information()
    {
        // 获取用户uid
//        $uid = $_SESSION['open_id'];
        $user_device = D('UserDevice');
        $map['ud.uid'] = session('user.id');
        $bind_device = $user_device->getBindInof($map);

        $bind_device_info = M('devices')->where(['device_code'=>$bind_device[0]['device_code']])->find();

        // 实例化订单模型
        $orders = M('orders');
        // 实例化订单套餐模型
        $orderSetmeal = M('order_setmeal');
//        处理订单信息
//        $mealId     = I('post.id');     // 商品（套餐）ID
        $mealId     = 57;     // 商品（套餐）ID
        $num        = I('post.num');    // 套餐数量
        $money      = I('post.money');    // 套餐金额
        $mealInfo   = D('setmeal')->find($mealId);

        $orderId    = $this->getOrderId();

        // 开启事务
        $orders->startTrans();

        // 准备数据
        // 唯一订单ID号
        $order['order_id']      = $orderId;

        // 用户ID号
        $order['user_id']       = session('user.id');
        // 关联的设备ID号
        $order['device_id']     =$bind_device_info['id'];

//        $order['device_id']     = M('currentDevices')->where("`uid`={$uid}")->getField('did');
        // 商品的购买总数量
        $order['total_num']     = $num;
        // 商品的购买总金额
        $order['total_price']   = $money;
        // 订单创建时间
        $order['created_at']   = time();

//
        // print_r($mealInfo);
        // 写入订单表
        $dataDS = [
            'order_id'      =>  $orderId,
            'uid'           =>  session('user.id'),
            'device_id'    =>  $bind_device_info['id'],
            'setmeal_id'    =>  $mealId,
            'type_id'       =>  $mealInfo['tid'],
            'remodel'       =>  $mealInfo['remodel'],
            'money'         =>  $money,
            'flow'          =>  $mealInfo['flow'],
            'describe'      =>  $mealInfo['describe'],
            'goods_num'     =>  $num,
            'goods_price'   =>  $money,
            'status'        =>  0,
            'created_at'    =>  time()
        ];
        $orderSetmeal = D('OrderSetmeal');
        $insertId = $orderSetmeal->data($dataDS)->add();

        // 创建订单
        $ordersRes = $orders->add($order);


        if($ordersRes && $orderSetmeal){
            // 执行事务
            $orders->commit();
            // 准备订单数据
            // 充值金额
            $money = $dataDS['goods_price']-0;
            // 订单号码
            $order_id = $order['order_id'];
            // 订单描述
            $contentstr = $dataDS['describe'];

            // 描述超长处理
            $content = substr($contentstr, 0, 100);
            // 订单创建成功，跳转到支付页面
            $this->ajaxReturn($orderId);
        }else{
            // 事务回滚
            $orders->rollback();
            $this->error('订单创建失败');
        }
//        if (IS_AJAX) {
//            // 处理订单信息
//            $mealId     = I('post.id');     // 商品（套餐）ID
//            $num        = I('post.num');    // 套餐数量
//            $money      = I('post.money');    // 套餐金额
//            $mealInfo   = D('setmeal')->find($mealId);
//            $orderId    = $this->getOrderId();
//            // print_r($mealInfo);
//            // 写入订单表
//            $dataDS = [
//                'order_id'      =>  $orderId,
//                'uid'           =>  session('user.id'),
//                'device_id'    => M('currentDevices')->where("`uid`={$uid}")->getField('did'),
//                'setmeal_id'    =>  $mealId,
//                'type_id'       =>  $mealInfo['tid'],
//                'remodel'       =>  $mealInfo['remodel'],
//                'money'         =>  $money,
//                'flow'          =>  $mealInfo['flow'],
//                'describe'      =>  $mealInfo['describe'],
//                'goods_num'     =>  $num,
//                'goods_price'   =>  $money,
//                'status'        =>  0,
//                'created_at'    =>  time()
//            ];
//            $orderSetmeal = D('OrderSetmeal');
//            $insertId = $orderSetmeal->data($dataDS)->add();
//            // 订单详情表(商品遍历)
//            // $order = $orderSetmeal->where('id='.$insertId)->field('order_id,created_at')->find();
//            // $dataOD = [
//            //     'order_id'      =>  $order['order_id'],
//            //     'gid'           =>  $mealId,
//            //     'num'           =>  $num,
//            //     'price'         =>  $money,
//            //     'status'        =>  0,
//            //     'addtime'       =>  $order['created_at'],
//            // ];
//            // D('OrderDetail')->data($dataOD)->add();
//            $this->ajaxReturn($orderId);
//        }
    }


    /*  已迁移至 Pay 控制器 李振东
    public function chooseMeal()
    {
        
        $map=[
            'ud.status'=>1,
            'ud.uid'=>$_SESSION['user']['id'],
        ];
        $type=D('UserDevice')->getBindType($map);

        $meal = M('setmeal')->where($type)->select();

        //调用微信JS-SDK类获取签名需要用到的数据
        $weixin = new WeixinJssdk;
        $signPackage = $weixin->getSignPackage();
        // 查询用户微信中的openid
        // $openId = $weixin->GetOpenid();
        $openId = $_SESSION['open_id'];
        //分配数据        
        $this->assign('info',$signPackage);
        $this->assign('openId',$openId);

        // dump($signPackage);die;
        $this->assign('list',json_encode($meal));
        $this->display();
    }
    */

//    /**
//     * 订单ID
//     * @return string   绝对唯一的32位订单ID号
//     */
//    function getOrderId()
//    {
//        do {
//            $orderId = onlyOrderId();
//            //查询订单号是否存在
//            $oid = M('shop_order')->where("`order_id`='{$orderId}'")->field('id')->find();
//            $osid = D('OrderSetmeal')->where("`order_id`='{$orderId}'")->field('id')->find();
//            if ($oid || $osid) {
//                $res = true;
//            } else {
//                $res = false;
//            }
//            // 如果订单号已存在再重新获取一次
//        } while ($res);
//
//        return $orderId;
//    }
}


