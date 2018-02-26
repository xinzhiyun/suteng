<?php
namespace Home\Controller;

class BankCardController extends CommonController
{
	/**
	 * [index 银行卡管理首页]
	 * @return [type] [description]
	 */
    public function index()
    {
    	$this->display();
    }

	/**
	 * [addmanageCard 去绑定银行卡]
	 * @return [type] [description]
	 */
    public function addmanageCard()
    {
    	$this->display();
    }

	/**
	 * [addcardClass 选择银行卡类型]
	 * @return [type] [description]
	 */
    public function addcardClass()
    {
    	$this->display();
    }

	/**
	 * [addcardBand 银行卡绑定]
	 * @return [type] [description]
	 */
    public function addcardBand()
    {
    	$this->display();
    }

	/**
	 * [identifyingCode 银行卡绑定验证码]
	 * @return [type] [description]
	 */
    public function identifyingCode()
    {
    	$this->display();
    }

	/**
	 * [bandOk 绑定成功]
	 * @return [type] [description]
	 */
    public function bandOk()
    {
    	$this->display();
    }

	/**
	 * [bandNo 绑定失败]
	 * @return [type] [description]
	 */
    public function bandNo()
    {
    	$this->display();
    }
}


