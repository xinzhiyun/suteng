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
            $where['pr.grade'] = session('user.grade');
            $data = $cart->getCart($where);    
            $this->ajaxReturn(array('code'=>200,'msg'=>$data));
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
        $post = $_POST;

        // dump($post);
        // die;
        try {

            //检查库存
            if(empty($post['skuattr']) || empty($post['gid'])){
                E('数据错误',40001);
            }

            $sku = $post['skuattr'];
            // dump($sku);die;
            $skuattr = array_column($sku,'id');
            sort($skuattr);
            $map['skuattr'] = implode('_', $skuattr);//属性值id组合

            // dump($map);
            $goodsSku = M('goodsSku');

            $map['gid'] = $post['gid'];
            $res = $goodsSku->where($map)->getField('skustock');

            if(!$res){
                E('该商品类型无库存!',603);
            }
            // dump($res);
            $cart = D('Cart');
            $data['num'] = I('post.num');
            $data['gid'] = I('post.gid');
            $data['csku'] = json_encode(I('post.skuattr'));
            $data['uid'] = session('user.id');
            $data['addtime'] = time();
            $res = $cart->create();

            // dump($data);
            // die;
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
                E('加入购物车', 200);
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
        dump($_POST);
        die;
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

    public function deleteByGoodsID()
    {
        try {
            $ids = I('ids');
            if(!is_array($ids)) return(['code'=>603,'msg'=>'参数有误','data'=>$ids]);
            $cart = D('Cart');
            $ids = \implode(',',$ids);
            $cart->where(['gid'=>array('in',$ids)])->delete();
            E('删除',200);
        } catch (\Exception $e) {
            $err = [
                'code' => $e->getCode(),
                'msg' => $e->getMessage(),
            ];
            $this->ajaxReturn($err);
        }
    }
}
