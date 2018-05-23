<?php
namespace Pc\Controller;

use Think\Controller;

class VipCenterController extends Controller
{
	// 会员中心首页
    public function index()
    {
        $this->display('index');
    }

    // 收藏
    public function collection()
    {
        $this->display('collection');

    }
    
    // 我的积分
    public function integral()
    {
        $this->display('integral');

    }
    

}