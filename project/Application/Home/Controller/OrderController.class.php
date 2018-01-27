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
     * [reward 商品评论成功]
     * @return [type] [description]
     */
    public function commentsucceed()
    {
        $this->display();
    }

    /**
     * [reward 发表评论]
     * @return [type] [description]
     */
    public function comment()
    {
        $this->display();
    }

    /**
     * [reward 邀请的下级会员没有消费]
     * @return [type] [description]
     */
    public function nomemberOrder()
    {
        $this->display();
    }

    /**
     * [reward 邀请的下级会员消费产生的金银币奖励]
     * @return [type] [description]
     */
    public function memberOrder()
    {
        $this->display();
    }
}


