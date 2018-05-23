<?php
namespace Pc\Controller;

use Think\Controller;

class CartController extends Controller
{
	// 购物车主页
    public function collection()
    {
        $this->display('collection');

    }
}