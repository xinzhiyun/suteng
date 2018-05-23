<?php
namespace Pc\Controller;

use Think\Controller;

class OrderController extends Controller
{
	// 订单首页
    public function index()
    {
        $this->display('index');

    }
}