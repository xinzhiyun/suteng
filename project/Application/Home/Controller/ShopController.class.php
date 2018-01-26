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
        $goods = D('Goods');
        $cate = M('Category')->select();
        $goodsList = $goods->getGoodsList();
        $assign = [
            'cate' => $cate,
            'goods' => $goodsList,
        ];
        $this->assign($assign);
        $this->display();
    }

    /**
     * 订单管理
     */
    public function orders()
    {
        $this->display();
    }

    // 商品详情页面
    public function goods_detail()
    {
        $id['g.id'] = I('get.id');
        $goods = D('Goods');
        $goodsDetail = $goods->getGoodsList($id);
        dump($goodsDetail);
        $assign = [
            'goods' => $goodsDetail,
        ];
        $this->assign($assign);
        $this->display();
    }
}
