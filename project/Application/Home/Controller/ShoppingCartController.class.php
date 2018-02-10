<?php
namespace Home\Controller;

class ShoppingCartController extends CommonController
{

    /**
     * [index 购物车主页]
     * @return [type] [description]
     */
    public function index()
    {
        $this->display();
    }

    public function cart()
    {
        try {
            $cart = D('Cart');
            $where['c.uid'] = session('user.id');
            $data = $cart->getCart($where);
            // dump($data);die;
            foreach($data as $val){
        		$key = $val['gid'];
        		if(isset($arr[$key])) {
        			$arr[$key]['attr'] .= $val['attr'].':'.$val['val'].'|';
        		} else {
                    $arr[$key] = $val;
        			$arr[$key]['attr'] = $val['attr'].':'.$val['val'].'|';
        		}
        	}
        	$data = array_values($arr);
            $this->ajaxReturn($data);
        } catch (\Exception $e) {
            $err = [
                'code' => $e->getCode(),
                'msg' => $e->getMessage(),
            ];
            $this->ajaxReturn($err);
        }

    }

    // 加入购物车
    public function shopAdd()
    {
        try {
            $cart = D('Cart');
            $data['num'] = I('post.num');
            $data['gid'] = I('post.gid');
            $data['uid'] = session('user.id');
            $data['addtime'] = time();
            $res = $cart->create();
            if(!$res) E($cart->getError(),603);
            // 查询购物车中是否存在这个商品
            $num = $cart->where('gid='.$data['gid'])->field('id,num')->find();
            if($num){
                $data['num'] = $data['num']+$num['num'];
                $res = $cart->where('id='.$num['id'])->save($data);
            } else {
                $res = $cart->add($data);
            }
            $cartInfo = M('Cart')->where('uid='.session('user.id'))->count();
            if($res){
                // E('加入购物车', 200);
            } else {
                E('无法加入购物车', 603);
            }
        } catch (\Exception $e) {
            $err = [
                'code' => $e->getCode(),
                'msg' => $e->getMessage(),
            ];
            $this->ajaxReturn($err);
        }
        $this->ajaxReturn($cartInfo);
    }

    // 购物车删除
    public function cartDel()
    {
        try {
            $cart = D('Cart');
            $data['id'] = I('post.id');
            $res = $cart->where($data)->delete();
            if($res){
                E('删除',200);
            } else {
                E('失败', 603);
            }
        } catch (\Exception $e) {
            $err = [
                'code' => $e->getCode(),
                'msg' => $e->getMessage(),
            ];
            $this->ajaxReturn($err);
        }
    }
}
