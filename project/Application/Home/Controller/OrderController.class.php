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
     * [waitpaylist 查询订单列表]
     * @return [type] [description]
     */
    public function order_list()
    {
        if(IS_POST){
            $status = I('post.status');
            // echo $status;die;
            // 查询用户
            $showData['uid']     = $_SESSION['user']['id'];
            // 订单状态-未支付
            $showData['status'] = $status;
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

                // 订单详情表
                $dWhere = array('d.order_id'=>$value['order_id']);
                // 订单详情
                $waitpaylist[$i]['productList'] = M('order_detail')
                                                    ->alias('d')
                                                    ->where($dWhere)
                                                    ->join('__GOODS__ g ON g.id = d.gid','LEFT')
                                                    ->join('__GOODS_DETAIL__ g_d ON g.id = g_d.gid','LEFT')
                                                    ->join('__PIC__ p ON g.id = p.gid','LEFT')
                                                    ->field(array('p.path'=>'orderimg','g.name'=>'productname','g.desc'=>'productbrief','d.price'=>'price','d.num'=>'productnumber'))
                                                    ->select();
                $i++;
            }

            // 返回数据
            if(empty($waitpaylist)){
                $message    = ['code' => 403, 'message' => '暂无订单!'];    
            }else{
                $message    = ['code' => 200, 'message' => '订单查询成功!','data'=>$waitpaylist];
            }
            // echo '<pre>';                         
            // print_r($showData);
            // 返回JSON格式数据
            $this->ajaxReturn($message); 
        }


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
    
    /**
     * 耗材订单
     * @return [type] [description]
     */
    public function consumable()
    {
        $this->display();
    }
}


