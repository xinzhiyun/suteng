<?php
namespace Pc\Controller;

use Think\Controller;

class ShopController extends Controller
{
	// 商城首页
    public function index()
    {
        // 商城导航
        // 查询PC首页导航
        $map['type'] = 1;
        $navs = M('pcnav')->where($map)->order('`order`')->select();
        // 商城轮播
        $banner = M('pcbanner')->where($map)->select();
        // 分类
        $catgory = D('Category')->select();


        // 轮播
        $this->assign('banner',$banner);
        // 导航
        $this->assign('navs',$navs);
        // 分类
        $this->assign('catgory',$catgory);


        // 显示
        $this->display('index');

    }

    // 商品列表页
    public function goodsList()
    {
        $this->display('goodsList');

    }

    // 商品详情页
    public function goodsdetail()
    {
        $this->display('goodsdetail');

    }
}