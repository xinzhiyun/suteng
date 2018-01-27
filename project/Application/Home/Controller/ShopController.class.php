<?php
namespace Home\Controller;
use Think\Controller;

/**
 * 商城
 */
class ShopController extends Controller
{
    /**
     * 商城首页
     */
    public function index()
    {
        $this->display();
    }
    
    /**
     * 商品详情
     */
    public function shoppingdetail()
    {
        $this->display();
    }
}
