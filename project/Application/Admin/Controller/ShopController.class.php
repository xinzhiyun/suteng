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
        $goodsList = $goods->select();
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
        // try {
        //     $goods = D('Goods');
        //     $data = I('post.');
        //     if(!$goods->create()) E($goods->getError(),203);
        //
        //     $res = $goods->add();
        //     if($res){
        //         E('添加成功',200);
        //     } else {
        //         E('添加失败',203);
        //     }
        // } catch (Exception $e) {
        //     $err = [
        //         'code' => $e->getCode(),
        //         'msg' => $e->getMessage(),
        //     ];
        //     return $this->ajaxReturn($err);
        // }
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

    // 商品添加处理
    public function goodsAction()
    {
        $data = I('post.');
        dump($_POST);die;
        // 确认分类
        // if($data['thirdcate'] != '--'){
        //     $goods['cid'] = $data['thirdcate'];
        // } elseif ($data['seccate'] != '--'){
        //     $goods['cid'] = $data['seccate'];
        // } elseif ($data['firscate'] != '--'){
        //     $goods['cid'] = $data['firscate'];
        // } else {
        //     E('请选择分类',205);
        // }

        // 处理属性
        foreach ($data['attr'] as $key => $value) {
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
        $this->display();
    }
}
