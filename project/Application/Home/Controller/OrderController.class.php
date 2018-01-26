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


