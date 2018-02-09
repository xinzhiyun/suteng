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
        $devices['d.id'] =169;// session('device.did');
        $res = $filter->getFilters($devices);
        $data = $device
            ->alias('d')
            ->where($devices)
            ->join('__DEVICES_STATU__ ds ON d.device_code=ds.DeviceID', 'LEFT')
            ->find();
        // dump($data);
        // dump($res);
        $assign = [
            'res' => json_encode($res),
            'data' => json_encode($data),
        ];

        // dump($assign);die;
        $this->assign('data',$assign);
        $this->display();
    } 

    // 信息确认并生成订单
    public function information()
    {
       
        try {
            $fliter = D('Filters');
            $orders = D('ShopOrder');
            $order_detail = D('OrderDetail');
            $data = json_decode($_POST['data'],'true');
            $orders->startTrans();
            $order['uid'] = session('user.id');
            $order['order_id'] = gerOrderId();
            $order['addtime'] = time();
            $order['g_type'] = 2;
            $order['g_cost'] = "";
            $detail = [];
            foreach($data as $key => $value){
                $where['id'] = $value['gid'];
                $order['g_price'] += $value['money'];
                $order['g_num'] += $value['num'];
                $arr = $fliter->alias('g')->where($where)->field('price,cost')->find();
                $order['g_cost'] += $value['num']*$arr['cost'];
                $detail['order_id'] = $order['order_id'];
                $detail['gid'] = $value['gid'];
                $detail['num'] = $value['num'];
                $detail['cost'] = $arr['cost'];
                $detail['price'] = $arr['price'];
                $detail['addtime'] = time();
                $detail_statut = $order_detail->add($detail);
                if(!$detail_statut) E('请重新结算',603);
            }

            $res = $orders->add($order);

            if($res){
                $msg = M('Cart')->where('uid='.session('user.id'))->delete();
                
                $orders->commit();
                $this->ajaxReturn($order['order_id']);
            } else {
                $orders->rollback();
                E('请重新购买',603);
            }
        } catch (\Exception $e) {
            $err = [
                'code' => $e->getCode(),
                'msg' => $e->getMessage(),
            ];
            $this->ajaxReturn($err);
        }
    }
}


