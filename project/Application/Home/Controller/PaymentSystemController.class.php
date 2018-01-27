<?php
namespace Home\Controller;

class PaymentSystemController extends CommonController
{
    /**
     * [payConfirm 确认支付]
     * @return [type] [description]
     */
    public function payConfirm()
    {
        $this->display();
    }
    
    /**
     * [paytosuccess 支付成功]
     * @return [type] [description]
     */
    public function paytosuccess()
    {
    	$this->display();
    }

    /**
     * [paytofailed 支付失败]
     * @return [type] [description]
     */
    public function paytofailed()
    {
        $this->display();
    }
}


