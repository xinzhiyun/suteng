<?php
namespace Admin\Controller;
use Think\Controller;
use Think\Exception;
use Think\Log;
/**
 * 商城
 */
class ShopController extends CommonController
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
        $data = D('Category')->getTreeData('tree','id, name');
        $assign = [
            'data' => $data,
        ];
        $this->assign($assign);
        $this->display();
    }
    // 添加分类
    public function cateGoryAdd()
    {
        try {
            $cate = D('Category');
            $data = I('post.');
            if(!$cate->create($post)) E($cate->getError(),203);
            $res = $cate->add($data);
            if(!$res) E('添加失败了', 202);
            E('添加成功', 200);
        } catch (\Exception $e) {
            $err = [
                'code' => $e->getCode(),
                'msg' => $e->getMessage(),
            ];
            return $this->ajaxReturn($err);
        }

    }
    // 修改分类
    public function cateGoryEdit()
    {
        try {
            $cate = D('Category');
            $post = I('post.');
            $where['id'] = $post['id'];
            if(!$cate->create($post)) E($cate->getError(),203);
            $res = $cate->where($where)->save($post);
            if(!$res) E('修改失败了', 202);
            E('修改成功', 200);
        } catch (\Exception $e) {
            $err = [
                'code' => $e->getCode(),
                'msg' => $e->getMessage(),
            ];
            return $this->ajaxReturn($err);
        }

    }
    // 删除分类
    public function cateGoryDel()
    {
        try {
            $id = I('post.id');
            $cate = M('Category');
            $res = $cate->where('pid='.$id)->find();
            if(!empty($res)){
                E('该分类存在子类，不可删除',204);
            } else {
                $res = $cate->where('id='.$id)->delete();
                if($res) E('分类删除成功', 200);
            }
        } catch (\Exception $e) {
            $err = [
                'code' => $e->getCode(),
                'msg' => $e->getMessage(),
            ];
            return $this->ajaxReturn($err);
        }

    }

    /**
     * 商品管理
     */
    public function goods()
    {
        $cate = D('Category');
        $cateInfo = $cate->getAllCate();
        $goods = D('Goods');
        $map['g.status'] = array('neq',2);
        $goodsList = $goods->getGoodsList($map);
        // echo $goods->_sql();die;
        foreach($goodsList as $val){
            $key = $val['gid'];
            if(isset($arr[$key])) {
                $arr[$key]['attr'] .= $val['attr'].':'.$val['val'].'|';
            } else {
                $arr[$key] = $val;
                $arr[$key]['attr'] = $val['attr'].':'.$val['val'].'|';
            }
        }
        $goodsList = array_values($arr);
        // dump($goodsList);
        $assign = [
            'data' => $goodsList,
            'cateInfo'=>$cateInfo,
        ];
        $this->assign($assign);
        $this->display();
    }

    // 添加商品
    public function goodsAdd()
    {
        $cate = D('Category');
        $cateInfo = $cate->where('pid=0')->select();
        $goods = D('Goods');
        $attr = D('Attr');
        $attrInfo = $attr->select();
        $goodsList = $goods->select();  //暂时无用
        $assign = [
            'cateInfo' => $cateInfo,
            'attrInfo' => $attrInfo,
        ];
        $this->assign($assign);
        $this->display();
    }

    // 商品管理首页删除商品
    public function goodsDel()
    {
        try {
            $goods = D('Goods');
            $id = I('post.id');
            $where = ['id' => $id];
            $data = ['status'=>2];
            $del = $goods->where($where)->save($data);
            // echo $goods->_sql();
            // dump($del);die;
            if($del){
                E('删除成功', 200);
            } else {
                E('删除失败', 606);
            }
        } catch (\Exception $e) {
            $err = [
                'code' => $e->getCode(),
                'msg' => $e->getMessage(),
            ];
            $this->ajaxReturn($err);
        }
    }

    // 商品添加处理
    public function goodsAction()
    {
        try{
            $goods_add = D('Goods');
            $attr_val = D('AttrVal');
            $attr = D('Attr');
            $goods_detail = D('GoodsDetail');
            $cate = D('Category');
            $data = I('post.');
            $goods['cid'] = $cate->sureCate();
            if(!$goods['cid']) E('请选择分类', 605);
            $goods['name'] = $data['name'];
            // $goods_check = $goods_add->create($goods);
            // dump($goods);die;
            // 事务开启
            $goods_add->startTrans();
            if(!$goods_add->create($goods)) {
                E($goods_add->getError(),406);
            }
            // 商品添加
            $goods_status = $goods_add->add();
            $goodsDetail['gid'] = $goods_status;
            $goodsDetail['price'] = $data['price'];
            $goodsDetail['cost'] = $data['cost'];
            $goodsDetail['stock'] = $data['stock'];
            if(!$goods_detail->create($goodsDetail)) E($goods_detail->getError(),408);
            // 商品详情添加
            $goodsDetail_status = $goods_detail->add($goodsDetail);
            // $attrVal['val'] = $data['attr_val'];

            $attrVal['gid'] = $goods_status;
            foreach ($data['attr'] as $key => $val) {
                if($attr->where('id='.$key)->field('id')){

                    $attrVal['aid'] = $key;
                    $attrVal['val'] = $val;
                    if(!$attr_val->create($attrVal)) E($attr_val->getError(),407);
                    // 属性值添加
                    $attr_val_status = $attr_val->add($attrVal);
                } else {
                    E('属性缺失，请刷新页面', 506);
                }
            }
            if($goods_status && $attr_val_status && $goodsDetail_status){
                $goods_add->commit();
                E('商品添加成功，可继续添加',200);
            } else {
                $goods_add->rollback();
                E('商品添加失败，请重新添加',407);
            }
        } catch (\Exception $e) {
            $err = [
                'code' => $e->getCode(),
                'msg' => $e->getMessage(),
            ];
            $this->ajaxReturn($err);
        }
    }

    // 添加商品图片
    public function goodsAddPic()
    {
        try {
            $pic = D('Pic');
            $data['gid'] = I('get.id');
            $upload = new \Think\Upload();// 实例化上传类
            $upload->maxSize   =     3145728 ;// 设置附件上传大小
            $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
            $upload->rootPath  =     './Uploads/'; // 设置附件上传根目录
            $upload->savePath  =     ''; // 设置附件上传（子）目录
            // 上传文件 
            $info   =   $upload->upload();
            if(!$info) E($upload->getError(),603);
            $data['picname'] = $info['pic']['savename'];
            $data['path'] = $info['pic']['savepath'].$info['pic']['savename'];
            $res = $pic->add($data);
            if($res){
                E('添加成功',200);
            } else {
                E('添加失败',604);
            }
        } catch (\Exception $e) {
            $err = [
                'code' => $e->getCode(),
                'msg' => $e->getMessage(),
            ];
            $this->ajaxReturn($err);
        }
    }

    // 商品上下架状态修改
    public function edidStatus(){
        try {
            $goods = D('GoodsDetail');
            $where['gid'] = I('post.id');
            $data['status'] = I('post.status');
            $res = $goods->where($where)->save($data);
            // echo $goods->_sql();
            // dump($res);die;
            if($res){
                E('状态已发生改变',200);
            } else {
                E('状态修改失败',603);
            }
        } catch (\Exception $e) {
            $err = [
                    'code' => $e->getCode(),
                    'msg' => $e->getMessage(),
                ];
            $this->ajaxReturn($err);
        }
    }

    // 根据pid获取分类
    public function getCate()
    {
        try {
            $cate = D('Category');
            $pid['pid'] = I('post.pid');
            $cateInfo = $cate->where($pid)->select();
            $this->ajaxReturn($cateInfo);
        } catch (\Exception $e) {
            $err = [
                'code' => $e->getCode(),
                'msg' => $e->getMessage(),
            ];
            $this->ajaxReturn($err);
        }

    }

    // 产品属性
    public function setAttr()
    {
        $cate = D('Category');
        $attr = D('attr');
        $cateInfo = $cate->getAllCate();
        $attrInfo = $attr->select();
        $assign = [
            'cateInfo'=>$cateInfo,
            'attrInfo'=>$attrInfo,
        ];
        $this->assign($assign);
        $this->display();
    }

    // 添加属性
    public function attrAdd()
    {
        try {
            $attr = D('Attr');
            $data = I('post.');
            if(!$attr->create()) {
                E($attr->getError(),204);
            }
            $res = $attr->add();
            if($res){
                E('添加完成',$res);
            } else {
                E('添加失败',203);
            }
        } catch (\Exception $e) {
            $err = [
                'code' => $e->getCode(),
                'msg' => $e->getMessage(),
            ];
            $this->ajaxReturn($err);
        }

    }

    /**
     * 订单管理
     */
    public function orders()
    {
        $order = D('ShopOrder');
        $data = $order->getOrders();
        // dump($data);
        $assign = [
            'data' => $data,
        ];
        $this->assign($assign);
        $this->display();
    }

    // 查看订单详情
    public function selectOrder()
    {
        $orderDetail = D('OrderDetail');
        $orders = D("ShopOrder");
        $order['order_id'] = I('get.id');
        $userInfo = $orders->getUserInfo($order);
        $map['od.order_id'] = $order['order_id'];
        $data = $orderDetail->getInfo($map);
        $assign = [
            'userInfo' => $userInfo,
            'data'     => $data,
        ];
        $this->assign($assign);
        $this->display('orderDetail');
    }

}
