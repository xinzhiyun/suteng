<?php
namespace Home\Controller;
use \Org\Util\WeixinJssdk;

class MallController extends CommonController
{

    /**
     * [chongzhi 充值]
     * @return [type] [description]
     */
    public function chongzhi()
    {   
        // 查询条件
        $showData['status'] = 1;
        // 金币套餐
        $data['gold'] = M('gold')->where($showData)->select();
        // 银币套餐
        $data['silver'] = M('silver')->where($showData)->select();
        // 银币兑换套餐
        $data['gold_silver'] = M('gold_silver')->where($showData)->select();
        // 获取用户微信唯一标识
        // $showUser['open_id'] = $_SESSION['user']['open_id'];
        // // 查询用户信息
        // $userData = M('users')->where($showUser)->find();
        //调用微信JS-SDK类获取签名需要用到的数据
        $weixin = new WeixinJssdk;
        $signPackage = $weixin->getSignPackage();
        // 查询用户微信中的openid
        // $openId = $weixin->GetOpenid();
        $openId = $_SESSION['open_id'];
        // dump($data);die;
        //分配数据        
        $this->assign('info',$signPackage);
        $this->assign('openId',$openId);
        $this->assign('data',$data);
        // dump($userData);die;
        $this->display();
    }




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
        // echo '<pre>';
        // print_r($devices['d.id']);die;
        // print_r($data);
        // print_r($res);die;
        $assign = [
            'res' => json_encode($res),
            'data' => json_encode($data),
        ];
        $this->assign('data',$assign);
        $this->display();
    } 

    // 信息确认并生成订单
    public function information()
    {
        if (IS_AJAX) {                    
            // 处理订单信息
            $mealId     = I('post.id');     // 商品（套餐）ID
            $num        = I('post.num');    // 套餐数量
            $money      = I('post.money');    // 套餐金额
            $mealInfo   = D('setmeal')->find($mealId);
            $orderId    = $this->getOrderId();
            // print_r($mealInfo);
            // 写入订单表
            $dataDS = [
                'order_id'      =>  $orderId,
                'uid'           =>  session('user.id'),
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
            // 订单详情表(商品遍历)
            // $order = $orderSetmeal->where('id='.$insertId)->field('order_id,created_at')->find();
            // $dataOD = [
            //     'order_id'      =>  $order['order_id'],
            //     'gid'           =>  $mealId,
            //     'num'           =>  $num,
            //     'price'         =>  $money,
            //     'status'        =>  0,
            //     'addtime'       =>  $order['created_at'],
            // ];
            // D('OrderDetail')->data($dataOD)->add();
            $this->ajaxReturn($orderId);
        }
    }

    public function chooseMeal()
    {

        $meal = D('setmeal')->select();

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

    function getOrderId()
    {
      $orderId = onlyOrderId();

      do {
        //查询订单号是否存在
        $oid = M('shop_order')->where("`order_id`='{$orderId}'")->field('id')->find();
        $osid = D('OrderSetmeal')->where("`order_id`='{$orderId}'")->field('id')->find();
        if ($oid || $osid) {
            $res = true;
        } else {
            $res = false;
        }
        // 如果订单号已存在再重新获取一次
      } while ($oid);
      // 绝对唯一的32位订单ID号
      return $orderId;
    }
}


