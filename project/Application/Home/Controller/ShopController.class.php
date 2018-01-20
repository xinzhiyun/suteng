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
     * 分类管理
     */
    public function category()
    {
        $this->display();
    }
    
    /**
     * 商品管理
     */
    public function goods()
    {
        $this->display();
    }
    
    /**
     * 订单管理
     */
    public function orders()
    {
        $this->display();
    }
}
