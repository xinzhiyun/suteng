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
        // 获取用户微信唯一标识
        $showUser['open_id'] = $_SESSION['user']['open_id'];
        // 查询用户信息
        $userData = M('users')->where($showUser)->find();
        //调用微信JS-SDK类获取签名需要用到的数据
        $weixin = new WeixinJssdk;
        $signPackage = $weixin->getSignPackage();
        // 查询用户微信中的openid
        $openId = $weixin->GetOpenid();

        //分配数据        
        $this->assign('info',$signPackage);
        $this->assign('openId',$openId);
        $this->assign('user',$userData);
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
        $devices['d.id'] = session('device.did');
        $res = $filter->getFilters($devices);
        $assign = [
            'data' => json_encode($res),
        ];
        $this->assign($assign);
        $this->display();
    }

    // 信息确认并生成订单
    public function information()
    {
       
        try {
            $fliter = D('Fliters');
            $orders = D('ShopOrder');
            $order_detail = D('OrderDetail');
            $data = json_decode($_POST['data'],'true');
            $orders->startTrans();
            $order['uid'] = session('user.id');
            $order['order_id'] = gerOrderId();
            $order['addtime'] = time();
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


