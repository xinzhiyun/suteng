<?php
namespace Pc\Controller;

use Think\Controller;

class IndexController extends Controller
{
    public function index()
    {
        // 查询PC首页导航
        $map['type'] = 0;
        $navs = M('pcnav')->where($map)->order('`order`')->select();
        // 商城轮播
        $banner = M('pcbanner')->where($map)->select();
        // 轮播
        $this->assign('banner',$banner);
        // 导航
        $this->assign('navs',$navs);
        // 商城轮播
        $banner = M('pcbanner')->where($map)->select();
        $this->display('index');

    }
}