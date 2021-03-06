<?php
namespace Admin\Controller;
use Think\Controller;

/**
 * 订单控制器
 * 
 * @author 潘宏钢 <619328391@qq.com>
 */

class OrdersController extends CommonController 
{
	/**
     * 订单列表
     * @author 潘宏钢 <619328391@qq.com>
     */
    public function index()
    {	
        // 根据用户昵称进行搜索
        $map = '';
    	if(!empty($_GET['order_id'])) $map['order_id'] = array('like',"%{$_GET['order_id']}%");
        $map['is_pay'] = '1'; 

        $order = M('orders');

        $total = $order->where($map)
                      ->join('st_devices ON st_orders.device_id = st_devices.id')
                      ->join('st_users ON st_orders.user_id = st_users.id')
                      ->join('st_express_information ON st_orders.express_id = st_express_information.id')
                      ->join('st_wechat ON st_users.open_id = st_wechat.open_id')
                      ->field('st_orders.*,st_wechat.nickname,st_express_information.name,st_express_information.phone,st_express_information.addres')
                        ->count();
        $page  = new \Think\Page($total,8);
        $pageButton =$page->show();

        $list = $order->where($map)
                      ->join('st_devices ON st_orders.device_id = st_devices.id')
                      ->join('st_users ON st_orders.user_id = st_users.id')
                      ->join('st_express_information ON st_orders.express_id = st_express_information.id')
                      ->join('st_wechat ON st_users.open_id = st_wechat.open_id')
                      ->field('st_orders.*,st_wechat.nickname,st_express_information.name,st_express_information.phone,st_express_information.addres')
                      ->limit($page->firstRow.','.$page->listRows)
                      ->select();
        // dump($list);

        $this->assign('list',$list);
        $this->assign('button',$pageButton);
        $this->display();
    }

    

    /**
     * 更改状态
     * @author 潘宏钢 <619328391@qq.com>
     */
    public function edit($order_id,$is_receipt)
    {
        if ($_POST['express'] && $_POST['mca']) {
            $work = M("orders");
            $order_id = $_GET['order_id'];
            $data['is_receipt'] = $is_receipt;
            $data['express'] = $_POST['express'];
            $data['mca'] = $_POST['mca'];
            // dump($data);die;
            $res = $work->where('order_id='.$order_id)->save($data); 
            if ($res) {
                $this->success('发货成功！',U('Orders/index'));        
            } else {
                $this->error('修改失败啦！');
            }
        }else{
            $this->error('请将快递信息输入完整！');
        }
        
    }

    /**
     * 订单详情
     * @author 潘宏钢 <619328391@qq.com>
     */
    public function detail($order_id)
    {

        $orders = D("orders");
         $order = $orders->where('st_orders.order_id='.$order_id)->select();
        $filter = $orders->where('st_orders.order_id='.$order_id)->join('LEFT JOIN st_order_filter ON st_orders.order_id = st_order_filter.order_id')
                      ->field('st_order_filter.*')
                      ->select();
        $setmeal = $orders->where('st_orders.order_id='.$order_id)->join('LEFT JOIN st_order_setmeal ON st_orders.order_id = st_order_setmeal.order_id')
                      ->field('st_order_setmeal.*')
                      ->select();              
       
        $info['order'] = $order; 
        $info['filter'] = $filter; 
        $info['setmeal'] = $setmeal; 
        
        // $list = $orders->where('st_orders.order_id='.$order_id)->getAll();
        // dump($info);
        $this->ajaxReturn($info,'JSON');

    }
 
    public function getCourierByOrder($order_id = '')
    {
        if(!IS_AJAX){ return $this->ajaxReturn(['code'=>-1,'msg'=>'非法请求']); }
        if(empty($order_id)){
            return $this->ajaxReturn(['code'=>-1,'msg'=>'参数错误']);
        }



        //先判断售后审核通过审核再过滤数据
        $rid = M('refund_goods')->where('oid='.$order_id)->find()['rf_id'];



        if(empty($rid)) {
          //1.先查询订单中的商品
            $od = M('shop_order_detail')->where("order_id='{$order_id}'")->select();

            //订单中所有商品的id
            $ids = array();
            foreach ($od as $key => $value) {
              $ids[] = $value['gid'];
            }
            
          
            $map['order_id'] = $order_id;
            $map['gid'] = array('in',$ids);

            // dump($map);die;

            $couriers = D('ShopOrderDetail')->field('gid,gname,cid,cname')->where($map)->select();

        } else {
          

          $rinfo = M('refund')->where('id='.$rid)->find();

          if ($rinfo['status'] == '0') {
            //如果订单中有退货的商品未处理，先提示处理退货，在继续发货
            $this->ajaxReturn(array('code'=>'201','data'=>'该订单还有退货商品未处理，请先处理再发货'));
          
            
          } else {
            
            //已同意退货
            //逻辑修改
            //1.先查询订单中的商品
            $od = M('shop_order_detail')->where("order_id='{$order_id}'")->select();

            //订单中所有商品的id
            $ids = array();
            foreach ($od as $key => $value) {
              $ids[] = $value['gid'];
            }
            
            //2.查询退货订单的商品
            $rg = M('refund_goods')->where("oid='{$order_id}'")->select();

            $rids = array();
            foreach ($rg as $key => $value) {
              $rids[] = $value['gid'];
            }

            //退货商品的快递的信息就不需要查询了
            $jid = array_diff($ids,$rids);
          
            $map['order_id'] = $order_id;
            $map['gid'] = array('in',$jid);

            // dump($map);die;

            $couriers = D('ShopOrderDetail')->field('gid,gname,cid,cname')->where($map)->select();

          }
        }

        //在这里先处理改订单是否有退货商品，如果有退货商品，先过滤退货的商品，再发货
        // dump($couriers);die;
        return $this->ajaxReturn(['code'=>200,'mag'=>'','data'=>$couriers]);
    }
    
    public function getCouriers()
    {
        if(!IS_AJAX){ return $this->ajaxReturn(['code'=>-1,'msg'=>'非法请求']); }
        $couriers = D('courier')->field('id,name')->select();
        $this->ajaxReturn(['code'=>200,'msg'=>'','data'=>$couriers]);
    }
}
