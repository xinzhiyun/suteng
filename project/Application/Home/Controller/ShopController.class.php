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
            'cate' => json_encode($cate),
            'goods' => json_encode($goodsList),
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
        $arr = [];
        $goodsDetail = $goods->getGoodsList($id);
    	foreach($goodsDetail as $val){
    		$key = $val['gid'];
    		if(isset($arr[$key])) {
    			$arr[$key]['attr'] .= $val['attr'].':'.$val['val'].'|';
    		} else {
                $arr[$key] = $val;
    			$arr[$key]['attr'] = $val['attr'].':'.$val['val'].'|';
    		}
    	}
    	$goodsDetail = array_values($arr);
        $commentInfo = $goods->getComment($id);
        $data = [
            'goodsDetail' => $goodsDetail,
            'commentInfo' => $commentInfo,
        ];
        $this->ajaxReturn($data);
    }
}
