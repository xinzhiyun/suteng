<?php
namespace Admin\Controller;
use Admin\Controller\CommonController;
use Think\Controller;

class MailController extends CommonController
{
	/**
	 * [index 站内信首页]
	 * @return [type] [description]
	 */
    public function index()
    {
        $this->display();
    }
}