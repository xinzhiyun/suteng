<?php
namespace Home\Controller;
use Think\Controller;

/**
 * 商城
 */
class ShopController extends CommonController
{
    /**
     * 商城首页
     */
    public function index()
    {
        $goods = D('Goods');
        $cartInfo = M('Cart')->where('uid='.session('user.id'))->count();
        $cate = M('Category')->select();
        $map['pr.grade'] = session('user.grade');
        $map['gd.status'] = 0;
        $map['g.status'] = array('neq', 2);
        $goodsList = $goods->getGoodsList($map);
        foreach($goodsList as $val){
    		$key = $val['gid'];
    		if(isset($arr[$key])) {
    			$arr[$key]['attr'] .= $val['attr'].':'.$val['val'].'|';
    		} else {
                $arr[$key] = $val;
    			$arr[$key]['attr'] = $val['attr'].':'.$val['val'].'|';
    		}
    	}
    	$goodsList = array_values($arr);
        $assign = [
            'cate' => json_encode($cate),
            'cartInfo' => json_encode($cartInfo),
            'goods' => json_encode($goodsList),
        ];
        $this->assign('goodsList',$goodsList);
        $this->assign($assign);
        $this->display();
    }

    /**
     * 订单管理
     */
    public function orders()

    {
        $this->display();
    }

    // 商品详情页面
    public function goods_detail()
    {
        $map['g.id'] = I('get.id');
        $goods = D('Goods');
        $map['pr.grade'] = session('user.grade');
        $map['gd.status'] = 0;
        $map['g.status'] = array('neq', 2);
        $arr = [];
        $goodsDetail = $goods->getGoodsList($map);
    	foreach($goodsDetail as $val){
    		$key = $val['gid'];
    		if(isset($arr[$key])) {
    			$arr[$key]['attr'] .= $val['attr'].':'.$val['val'].'|';
    		} else {
                $arr[$key] = $val;
    			$arr[$key]['attr'] = $val['attr'].':'.$val['val'].'|';
    		}
    	}
    	$goodsDetail = array_values($arr);
        $commentInfo = $goods->getComment($map['g.id']);
        $data = [
            'goodsDetail' => $goodsDetail,
            'commentInfo' => $commentInfo,
        ];
        $this->ajaxReturn($data);
    }

    public function chooseMeal()
    {
        $meal = D('setmeal')->select();
        // print_r($meal);
        $this->assign('list',json_encode($meal));
        $this->display();
    }

    // 信息确认并生成订单
    public function information()
    {
       
        try {
            $fliter = D('Filters');
            $orders = D('Order');
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

    /**
     * [paytofailed 订单支付]
     * @return [type] [description]
     */
    public function orderPay()
    {
        if(IS_POST){
            // echo 1;
            // // 准备订单查询数据
            $showWhere['order_id'] = I('post.order');
                
            // // 查询订单表
            $orderData = D('OrderSetmeal')->where($showWhere)->find();
            // print_r($orderData);
            // 判断订单未支付
            if($orderData['status']==8){
                // 订单金额
                $money = $orderData['goods_price'];
                // 订单号码
                $order_id = $orderData['order_id'];
                // 订单描述
                $content = '速腾商城商品购买';
                $this->uniformOrderTow($money,$order_id,$content);
            }else{
                // 订单不存在
                echo -1;
            }
            // $this->uniformOrder(100,123456,'content');
        }
    }
}
