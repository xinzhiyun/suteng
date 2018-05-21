<?php
namespace Api\Controller;
use Think\Controller;

/**
 * PC端商城接口类
 * author:梁康伦
 * 时间：2018-05-03
 */

class GoodsController extends Controller
{
	/**
	 * [index descr商城首页数据iption]
	 * @return [type] [description]
	 */
	public function index()
	{
        if (IS_POST) {
            $goods = D('Goods');

            if (!empty($_SESSION['user'])) {
                $cartInfo = M('Cart')->where('uid='.session('user.id'))->count();
            }
            $cate = M('Category')->select();

            if (!empty($_SESSION['user'])) {
                $map['pr.grade'] = session('user.grade');

            } else {
                $map['pr.grade'] = 1;

            }
            $map['gd.status'] = 0;
            $map['g.status'] = array('neq', 2);
            $goodsList = $goods->getGoodsList($map);
            foreach($goodsList as $val){
                $key = $val['gid'];
                if(isset($arr[$key])) {
                    $arr[$key]['attr'] .= $val['attr'].':'.$val['val'].'|';
                } else {
                    $arr[$key] = $val;
                    $arr[$key]['attr'] = $val['attr'].':'.$val['val'].'|';
                }
            }

            //轮播图产品
            $banner = D('goods')->page(1,5)->order('addtime desc')->field('id,pic')->select();
            // dump($banner);
            $goodsList = array_values($arr);
            $assign = [
                'cate' => $cate,
                'cartInfo' => $cartInfo,
                'goods' => $goodsList,
                'banner' => $banner
            ];

            $this->ajaxReturn(array('msg'=>$assign,'code'=>'200'));
        } else {
            $this->ajaxReturn(array('msg'=>'请求方式有误','code'=>'201'));
        }
               
	}

    /**
     * [goodsDetail 商品详情]
     * @return [type] [description]
     */
    public function goodsDetail()
    {
        $map['g.id'] = I('get.id');

        if (!empty($map['g.id'])) {
            $goods = D('Goods');

            if (!empty($_SESSION['user'])) {
                $map['pr.grade'] = session('user.grade');

            } else {
                $map['pr.grade'] = 1;

            }
            $map['gd.status'] = 0;
            $map['g.status'] = array('neq', 2);
            $arr = [];
            $goodsDetail = $goods->getGoodsList($map);
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

            //获取评论数据
            $commentInfo = $goods->getComment($map['g.id']);
            $data = [
                'goodsDetail' => $goodsDetail,
                'commentInfo' => $commentInfo,
                // 'goodsCourier' => $goodsCourier,
            ];

            $this->ajaxReturn(array('msg'=>$data,'code'=>'200'));
        } else {
            $this->ajaxReturn(array('msg'=>'非法id','code'=>'201'));

        }
    }




    

}