<?php
namespace Home\Controller;

class OrderController extends CommonController
{
    /**
     * [index 订单首页]
     * @return [type] [description]
     */
    public function index()
    {   
    	$this->display();
    }

    /**
     * [waitpaylist 查询用户未支付订单列表]
     * @return [type] [description]
     */
    public function waitpaylist()
    {
        // 查询用户
        $showData['uid']     = $_SESSION['user']['id'];
        // 订单状态
        $showData['status'] = 8;
        // 查询订单表
        $arrList = M('shop_order')->where($showData)->select();
        // 未支付订单
        $waitpaylist = [];
        $i = 0;
        foreach ($arrList as $key => $value) {
            // 订单编号
            $waitpaylist[$i]['orderid']     = $value['order_id'];
            // 订单时间
            $waitpaylist[$i]['ordertime']   = date("Y-m-d",$value['addtime']);
            // 订单总数量
            $waitpaylist[$i]['totalnumber'] = $value['g_num'];
            // 订单总金额
            $waitpaylist[$i]['totalprice']  = $value['g_price'];
            // 订单快递费
            $waitpaylist[$i]['postage']     = mt_rand(5,22);
            // 订单详情
            $waitpaylist[$i]['productList'] = M('order_detail')
                                                ->where("st_order_detail.order_id='{$value['order_id']}'")
                                                ->join('st_goods ON st_order_detail.gid = st_goods.id')
                                                ->join('st_goods_detail ON st_goods_detail.gid = st_goods.id')
                                                ->select();
            $i++;
        }

        // print_r($waitpaylist);
    }



    /**
     * [paysuccBtn 提交成功]
     * @return [type] [description]
     */
    public function paysuccBtn()
    {
        $this->display();
    }

    /**
     * [payfailed 提交失败]
     * @return [type] [description]
     */
    public function payfailed()
    {
        $this->display();
    }

    /**
     * [reward 会员没有订单]
     * @return [type] [description]
     */
    public function nomemberOrder()
    {
        $this->display();
    }

    /**
     * [reward 会员订单]
     * @return [type] [description]
     */
    public function memberOrder()
    {
        $this->display();
    }

    /**
     * [applicRefund 退款商品]
     * @return [type] [description]
     */
    public function applicRefund()
    {
        $this->display();
    }

    /**
     * [choiceRefund 选择退款商品]
     * @return [type] [description]
     */
    public function choiceRefund()
    {
        $this->display();
    }
}


