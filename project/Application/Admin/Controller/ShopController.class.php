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
        $map = '';
        if (!empty(I('get.key')) && !empty(I('get.keywords'))) {
            $map[I('get.key')] = array('like',"%".trim(I('get.keywords'))."%");
        }
        $data = D('Category')->getTreeData('tree','id, name',$name='name',$child='id',$parent='pid',$map);
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
        // $map = '';
        if (!empty(I('get.key')) && !empty(I('get.keywords'))) {
            $map[I('get.key')] = array('like',"%".I('get.keywords')."%");
        }
        $cate = D('Category');
        $cateInfo = $cate->getAllCate();
        $goods = D('Goods');
        $map['g.status'] = array('neq',2);
        $goodsList = $goods->getGoodsList($map);
        $assign = [
            'data' => $goodsList['goodsData'],
            'cateInfo'=>$cateInfo,
            // 'show' => $goodsList['show'],
        ];

//         dump($assign);
        $this->assign($assign);
        $this->display();
    }

    //加载商品详情
    public function good_detail()
    {
        if(IS_AJAX){
            $id = I('post.id');
            $res = M('goods_detail')->where('gid='.$id)->getField('desc');
            return $this->ajaxReturn($res);
        }

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

        /* 添加快递公司选择 */
        $courier = M('courier');
        $courierList = $courier->where('status=1')->field('id,name')->select();
        /* 结束 */

        $assign = [
            'cateInfo' => $cateInfo,
            'attrInfo' => $attrInfo,
            'courierList' => $courierList,
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
        // dump($_POST);die;
       
        try{
            $goods_add = D('Goods');
            $attr_val = D('AttrVal');
            $attr = D('Attr');
            $goods_detail = D('GoodsDetail');
            $cate = D('Category');
            $data = I('post.');
            $price = $data['price'];
            $goods['cid'] = $cate->sureCate();
            if(!$goods['cid']) E('请选择分类', 605);
            $goods['name'] = $data['name'];
            // 事务开启
            $goods_add->startTrans();
            if(!$goods_add->create($goods)) {
                E($goods_add->getError(),406);
            }
            // 商品添加
            $goods_status = $goods_add->add();
            $goods_add->where(['id'=>$goods_status])->save(['vid'=>$_SESSION['adminInfo']['id']]);
            $goodsDetail['gid'] = $goods_status;
            $goodsDetail['cost'] = $data['cost'];
            // $goodsDetail['stock'] = $data['stock'];
            if($data['is_install'] == 'on'){
                $goodsDetail['is_install'] = 1;
            } else {
                $goodsDetail['is_install'] = 0;
            }
            if($data['is_hire'] == 'on'){
                $goodsDetail['is_hire'] = 1;
            } else {
                $goodsDetail['is_hire'] = 0;
            }
            if(!$goods_detail->create($goodsDetail)) E($goods_detail->getError(),408);
            // 商品详情添加
            $goodsDetail_status = $goods_detail->add($goodsDetail);
            // $attrVal['val'] = $data['attr_val'];

            //商品属性添加
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


            //属性值添加完成后，将属性名字也写入库
            foreach ($data['attrName'] as $key => $value) {
                $gid = $goods_status;
                $aid = $key;
                $ainfo['aname'] = $value;
                //进行添加
                M('AttrVal')->where('gid='.$gid .' AND aid='.$aid)->save($ainfo);   
            }

            /* 商品快递费添加 */
            foreach ($data['courier'] as $key => $value) {
                $data['gid'] = $goods_status;
                $data['cid'] = $key;
                $data['cprice'] = $value;

                //插入数据前先查询同一个商品是否有同一个快递
                $info = M('GoodsCourier')->where('gid='.$data['gid'].' AND cid='.$data['cid'])->select();

                if ($info) {
                    E('此商品已经有设置了这个快递的价格，请前往更改',203);
                } else {
                    //进行添加
                    $list = M('GoodsCourier')->add($data);

                    if (!$list) {
                        E('快递价格设置失败，请刷新页面', 506);
                    } 
                }
            }

            //商品快递费添加完成后，将快递公司名字也写入库
            foreach ($data['courierName'] as $key => $value) {
                $gid = $goods_status;
                $cid = $key;
                $info['cname'] = $value;
                //进行添加
                M('GoodsCourier')->where('gid='.$gid .' AND cid='.$cid)->save($info);   
            }


            /* 商品快递费添加 */
            


            // 商品单价添加
            foreach ($price as $key => $value) {
                $p['price'] = $value;
                $p['grade'] = $key;
                $p['gid'] = $goods_status;
                $price_status = M('Price')->add($p);
            }
            if($goods_status && $attr_val_status && $goodsDetail_status && price_status){
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
            $res = $pic->where('gid='.$data['gid'])->find();
            if($res){
                $status_res = $pic->where('gid='.$data['gid'])->save($data);
                if($status_res){
                    E('更新成功',200);
                } else {
                    E('更新失败',604);
                }
            } else {
                $status_res = $pic->add($data);
                if($status_res){
                    E('添加成功',200);
                } else {
                    E('添加失败',604);
                }
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

    // 修改商品
    public function goodEdit()
    {   

        //修改商品之前先查询出所有的快递公司
        $courierList = M('courier')->where('status = 1')->select();

        //查询出所有分类
        $categoryList = M('category')->field('id, name')->select();

        //查询出所有属性
        $arrtList = M('attr')->select();
        
        //获取商品id
        $id = $_GET['gid'];
        //实例化商品对象
        $goods = M('goods');

        $goodsInfo=D('goods')->getGoodInfo($id);

        // dump($goodsInfo);
        $price = $goodsInfo['price'];
        $attrVal = $goodsInfo['attr_val'];
        $goodsCourier = $goodsInfo['goods_courier'];
        $goodsDetail = $goodsInfo['goods_detail'];

        //处理属性所属
        foreach ($attrVal as $value) {
            $attrValArr[]=$value['aid'];
        }

        foreach ($arrtList as &$value) {
            if(in_array($value['id'], $attrValArr)){
                $value['check']='checked';
            }else{
                $value['check']='';
            }
        }

        // dump($attrVal);
        //处理商品对应属性的属性值
        if (!empty($attrVal)) {
            foreach ($attrVal as $key => $val) {
                $newattrVal[$val['aid']] = $val['val'];
            }
        } else {
            $newattrVal = '';
        }
        

        //处理快递所属
        foreach ($goodsCourier as $v) {
            $goodsDetailArr[]=$v['cid'];
        }

        foreach ($courierList as &$v) {
            if(in_array($v['id'], $goodsDetailArr)){
                $v['check']='checked';
            }else{
                $v['check']='';
            }
        }

        //处理商品对应的快递费
        if (!empty($goodsCourier)) {
            foreach ($goodsCourier as $key => $cval) {
                $gcVal[$cval['cid']] = $cval['cprice'];
            }
        } else {
            $gcVal = '';
        }
        

        $this->assign('courierList', $courierList);
        $this->assign('categoryList', $categoryList);
        $this->assign('arrtList', $arrtList);
        $this->assign('goodsInfo', $goodsInfo);
        $this->assign('price', $price);
        $this->assign('newattrVal', $newattrVal);
        $this->assign('gcVal', $gcVal);
        $this->assign('goodsDetail', $goodsDetail);
        $this->display(); 
    }

    // 商品编辑处理
    public function goodsEditAction()
    {


        // dump($_POST);

        // die;
        try{

            //接收要修改的商品的id
            $gid = $_POST['gid'];

            //1.修改商品分类及商品名
            $goods = M('goods');

            //接受要修改分类的id
            $data['cid'] = $_POST['firscate'];
            $data['name'] = $_POST['name'];
            $data['updatetime'] = time();
            $goodsBool = $goods->where('id='.$gid)->save($data);


            //2.修改商品的属性
            /* 在修改属性之前，先将之前商品的所有属性删除 */
            $av = D('attr_val');
            //先查询是否存在，存在则删除
            if($av->where('gid='.$gid)->select()) {
                $av->where('gid='.$gid)->delete();
                //进行属性添加
                //接受属性值
                $attrValList = $_POST['attr'];

                if (empty($attrValList)) {
                    E('属性不能为空！',407);

                } else {
                    foreach ($attrValList as $key => $value) {
                        $attrValDate['gid'] = $gid;
                        $attrValDate['aid'] = $key;
                        $attrValDate['updatetime'] = time();
                        $attrValDate['val'] = $value;
                        $avBool = $av->add($attrValDate);

                    }
                }
                
            } else {
                //进行属性添加
                //接受属性值
                $attrValList = $_POST['attr'];
                if (empty($attrValList)) {
                    E('属性不能为空！',407);

                } else {
                    foreach ($attrValList as $key => $value) {
                        $attrValDate['gid'] = $gid;
                        $attrValDate['aid'] = $key;
                        $attrValDate['updatetime'] = time();
                        $attrValDate['val'] = $value;
                        $avBool = $av->add($attrValDate);

                    }
                }
                
            }

            //添加完后，将属性名也写入数据库
            //接受属性名
            if ($avBool) {
                $attrNameList = $_POST['attrName'];
                foreach ($attrNameList as $akey => $avalue) {
                    $attrNameDate['aname'] = $avalue;
                    $attrBool = $av->where('aid='.$key)->save($attrNameDate);
                }
            }
           
            //3.修改商品的快递运费
            /* 在修改快递运费之前，先将之前商品的所有快递运费删除 */
            $gc = D('goods_courier');
            if ($gc->where('gid='.$gid)->select()) {
                $gc->where('gid='.$gid)->delete();

                //接受快递费
                $courierList = $_POST['courier'];

                if (empty($courierList)) {
                    E('快递费不能为空！',407);
                    
                } else {
                    foreach ($courierList as $k => $v) {
                        $courierValDate['gid'] = $gid;
                        $courierValDate['cid'] = $k;
                        $courierValDate['cprice'] = $v;
                        $courierValDate['updatetime'] = time();
                        $gcBool = $gc->add($courierValDate);

                    }
                }
                
            } else {
                //进行快递费添加
                //接受快递费
                $courierList = $_POST['courier'];
                if (empty($courierList)) {
                    E('快递费不能为空！',407);
                } else {
                    foreach ($courierList as $k => $v) {
                        $courierValDate['gid'] = $gid;
                        $courierValDate['cid'] = $k;
                        $courierValDate['cprice'] = $v;
                        $courierValDate['updatetime'] = time();
                        $gcBool = $gc->add($courierValDate);

                    }
                }
                
            }

            //添加完后，将快递名也写入数据库
            //接收快递名
            if ($gcBool) {
                $courierNameList = $_POST['courierName'];
                foreach ($courierNameList as $ckey => $cvalue) {
                    $courierNameDate['cname'] = $cvalue;
                    $courierBool = $gc->where('cid='.$ckey)->save($courierNameDate);
                }
            }
            

            //4.商品价格修改
            //添加价格之前先删除之前的价格
            $price = D('price');

            if ($price->where('gid='.$gid)->select()) {
                $price->where('gid='.$gid)->delete();
                //接受价格数据
                $priceList = $_POST['price'];

                if (empty($priceList)) {
                     E('商品价格不能为空！',407);
                } else {
                    foreach ($priceList as $keys => $values) {
                        $priceValDate['gid'] = $gid;
                        $priceValDate['price'] = $values;
                        $priceValDate['grade'] = $keys;
                        $priceValDate['updatetime'] = time();
                        $priceBool = $price->add($priceValDate);
                    }
                }
                
            } else {
                //进行商品价格添加
                //接受价格数据
                $priceList = $_POST['price'];
                if (empty($priceList)) {
                    E('商品价格不能为空！',407);
                    
                } else {
                    foreach ($priceList as $keys => $values) {
                        $priceValDate['gid'] = $gid;
                        $priceValDate['price'] = $values;
                        $priceValDate['grade'] = $keys;
                        $priceValDate['updatetime'] = time();
                        $priceBool = $price->add($priceValDate);
                    }
                }
            }
            
            
            //5.成本价，是否安装，是否租赁修改
            $goodDetail = D('goods_detail');
            $detailDate['cost'] = $_POST['cost'];
            $detailDate['updatetime'] = time();
            $detailDate['is_install'] = $_POST['is_install'];
            $detailDate['is_hire'] = $_POST['is_hire'];

            if($detailDate['is_install'] == 'on'){
                $detailDate['is_install'] = 1;
            } else {
                $detailDate['is_install'] = 0;
            }
            if($detailDate['is_hire'] == 'on'){
                $detailDate['is_hire'] = 1;
            } else {
                $detailDate['is_hire'] = 0;
            }

            $bool = $goodDetail->where('gid='.$gid)->save($detailDate);

          
            if($bool && $priceBool && $courierBool && $attrBool && $goodsBool){
                E('商品编辑成功！',200);
            } else {
                E('商品编辑失败',407);
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

    // 删除产品属性
    public function attrDel()
    {
        try {
            $attrVal = D('AttrVal');
            $attr = D('Attr');
            $id = I('post.aid');
            $where = ['aid' => $id];
            $res = $attrVal->where($where)->select();
            if ($res) {
                E('该属性下有商品，不能删除',204);
            }else{
                // 去做删除
                // 重新定义条件
                $where = ['id' => $id];                
                $info = $attr->where($where)->delete();
                if ($info) {
                    E('删除成功',$info);
                }else{
                    E('删除失败',203);
                }
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
        $map = '';
        if (!empty(I('get.key')) && !empty(I('get.keywords'))) {
            $map[I('get.key')] = array('like',"%".I('get.keywords')."%");
        }
        $order = D('ShopOrder');
        $data = $order->getOrders($map);
        $assign = [
            'data' => $data['data'],
            'show' => $data['show'],
        ];
        $where['key'] = $_GET['key'];
        $where['keywords'] = $_GET['keywords'];

        $this->assign($assign);
        $this->assign('where',$where);
        $this->display();
    }

    /**
     * 为订单发货
     * @return [json] [description]
     */
    public function ship()
    {
        if (IS_AJAX) {
            $id = I('post.orderid');
            $data['express'] = I('post.express');
            $data['status'] = 2;
            $order = D('ShopOrder');
            $res = $order->where('order_id='.$id)->save($data);
            if ($res && D('Orders')->where(['order_id'=>$id])->save(['is_ship'=>1])) {
                return $this->ajaxReturn(['code'=>200,'msg'=>'发货成功']);
            } else {
                return $this->ajaxReturn(['code'=>500,'msg'=>'发货失败']);
            }
        }             
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

    // 查看当前商品单价
    public function price()
    {
        try {
            $gid = I('post.gid');
            $data = M('Price')->where('gid='.$gid)->select();
            $this->ajaxReturn($data);
        } catch (\Exception $e) {
            $err = [
                'code' => $e->getCode(),
                'msg' => $e->getMessage(),
            ];
            $this->ajaxReturn($err);
        }
    }

    // 添加商品描述
    public function addDesc()
    {
        $map['id'] = I('get.gid');
        $goodsName = M('Goods')->where($map)->find();
        $content = M('GoodsDetail')->where('gid='.$map['id'])->find();
        $assign = [
            'goodsName' => $goodsName['name'],
            'content' => $content['desc'],
            'gid' => $map['id'],
        ];
        $this->assign($assign);
        $this->display();
    }

    // 商品描述数据处理
    public function addDescAction()
    {
        try {
            $desc = I('post.desc');
            $data['desc'] = $desc;
            $gid = I('post.gid');
            $res = M('GoodsDetail')->where('gid='.$gid)->save($data);
            if($res){
                E('OK',200);
            } else {
                E('请重新编辑',603);
            }
        } catch (\Exception $e) {
            $err = [
                'code' => $e->getCode(),
                'msg' => $e->getMessage(),
            ];
            $this->ajaxReturn($err);
        }
    }


    /* 新增功能 -- 库存单独管理 */

    /**
     * [inventory 库存列表]
     * @return [type] [description]
     */
    public function inventory()
    {
        //查询出所有商品的库存量
        // $map = '';
        if (!empty(I('get.keywords'))) {
            $map['g.name'] = array('like',"%".I('get.keywords')."%");
        }

        $cate = D('Category');
        $cateInfo = $cate->getAllCate();
        $goods = D('Goods');
        $map['g.status'] = array('neq',2);
        $goodsList = $goods->getGoodsList($map);
        $assign = [
            'data' => $goodsList['goodsData'],
            'cateInfo'=>$cateInfo,
            'show' => $goodsList['show'],
        ];

        // dump($assign);
        $this->assign($assign);
        $this->display();

    }

    /**
     * [inventoryAdd 库存添加处理]
     * @return [type] [description]
     */
    public function inventoryAdd()
    {
        try {
                
            //接受POST数据
            $data['allnum'] = I('post.allnum');
            $data['gid'] = I('post.gid');

            // dump($data);die;
            $inventory = M('inventory');

            //添加库存前先判断该商品是否已经存在于库存表
            if ($inventory->where('gid='.$data['gid'])->find()) {

                E('该商品已存在于库存表，请前往更改页面更改','203');

            } else {
                //添加商品库存数据
                $info = $inventory->add($data);

                if ($info) {
                        E('添加成功',$info);
                    }else{
                        E('添加失败',203);
                }      
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
     * [inventoryAddList 库存添加页面显示]
     * @return [type] [description]
     */
    public function inventoryAddList()
    {
        //查询所有商品
        $glist = D('goods')->field('id,name')->select();;
        // dump($glist);
        
        $this->assign('glist',$glist);
        $this->display('inventory_add');

    }

    /**
     * [inventoryEdid 异常库存编辑]
     * @param  [type] $gid [description]
     * @return [type]      [description]
     */
    public function inventoryEdid()
    {

        try {
                
            //接受POST数据
            $data['abnormalnum'] = I('post.abnormalnum');
            $id = $_POST['id'];

            $inventory = M('inventory');
            //修改库存数据
            $info = $inventory->where('gid='.$id)->save($data);

            if ($info) {
                    E('修改成功',$info);
                }else{
                    E('修改失败',203);
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
     * [inventoryEdidList 异常库存编辑页面]
     * @return [type] [description]
     */
    public function inventoryEdidList()
    {
        // echo $gid;
        //查询商品
        $ginfo = D('goods')->field('id,name')->find($_GET['gid']);
        // dump($ginfo);

        $this->assign('ginfo', $ginfo);
        $this->display('inventory_edit');
    }

    /**
     * [ainventoryEdid 总库存编辑]
     * @param  [type] $gid [description]
     * @return [type]      [description]
     */
    public function ainventoryEdid()
    {

        try {
                
            //接受POST数据
            $data['allnum'] = I('post.allnum');
            $id = $_POST['id'];

            $inventory = M('inventory');
            //修改库存数据
            $info = $inventory->where('gid='.$id)->save($data);

            if ($info) {
                    E('修改成功',$info);
                }else{
                    E('修改失败',203);
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
     * [ainventoryEdidList 总库存编辑页面]
     * @return [type] [description]
     */
    public function ainventoryEdidList()
    {
        // echo $gid;
        //查询商品
        $ginfo = D('goods')->field('id,name')->find($_GET['gid']);
        // dump($ginfo);

        $this->assign('ginfo', $ginfo);
        $this->display('ainventory_edit');
    }




    /* 快递公司模块添加 */

    /**
     * [courier 快递公司显示]
     * @return [type] [description]
     */
    public function courier()
    {   
        //接受处理搜索条件
        if (!empty(I('get.keywords'))) {
            $map['name'] = array('like',"%".I('get.keywords')."%");
        }

        $courier = D('courier');
        $clist = $courier->where($map)->select();

        $this->assign('clist', $clist);
        $this->display();
    }   

    /**
     * [courierAdd 执行快递公司添加]
     * @return [type] [description]
     */
    public function courierAdd()
    {
        try {      
            //接受POST数据
            $data['name'] = I('post.name');
          
            // dump($data);die;
            $courier = M('courier');

            //添加快递公司前先判断快递公司是否已经存在于表中
            if ($courier->where("name = '{$data['name']}'")->find()) {

                E('该快递公司已存在，如需改名请前往更改页面更改','203');

            } else {
                //添加快递公司
                $info = $courier->add($data);

                if ($info) {
                        E('添加成功',$info);
                    }else{
                        E('添加失败',203);
                }      
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
     * [courierAddList 加载快递公司添加页面]
     * @return [type] [description]
     */
    public function courierAddList()
    {
        $this->display('courier_add');
    }

    /**
     * [courierEdit 执行修改快递公司]
     * @return [type] [description]
     */
    public function courierEdit()
    {
        try {
                
            //接受POST数据
            $data['name'] = $_POST['name'];
            $id = $_POST['id'];
            $data['status'] = $_POST['status'];

            $courier = M('courier');
            //修改库存数据
            $info = $courier->where('id='.$id)->save($data);

            if ($info) {
                    E('修改成功',$info);
                }else{
                    E('修改失败',203);
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
     * [courierEditList 快递公司编辑页面加载]
     * @return [type] [description]
     */
    public function courierEditList($id)
    {
        $cinfo = M('courier')->find($id);

        $this->assign('cinfo', $cinfo);
        $this->display('courier_edit');
    }

    /**
     * [courierDel 快递公司删除]
     * @return [type] [description]
     */
    public function courierDel()
    {
        try {
                
            //接收要删除数据的id
            $id = $_POST['id'];

            $courier = M('courier');
            //删除快递表信息
            $info = $courier->where('id='.$id)->delete();
            
            if ($info) {
                //查看关联表中是否存在这个快递公司
                if (M('GoodsCourier')->where('cid='.$id)->select()) {
                    //存在
                    //删除快递公司的同时也要删除关联表的信息
                    $gc = M('GoodsCourier')->where('cid='.$id)->delete();
                }

                E('删除成功',$info);
            } else {
                    E('删除失败',203);
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
     * [goodsCourier 修改商品对应的快递价格]
     * @return [type] [description]
     */
    public function goodsCourierEdit($gid)
    {
        echo $gid;
    }

    /**
     * [goodsCourierList 查询商品对应的运费]
     * @return [type] [description]
     */
    public function goodsCourierList()
    {
        try {
            $gid = I('post.gid');
            $gc = M('GoodsCourier');
            $data = $gc->alias('gc')->where('gc.gid='.$gid)->join('__COURIER__ c ON gc.cid = c.id')->select();

            $this->ajaxReturn($data);
        } catch (\Exception $e) {
            $err = [
                'code' => $e->getCode(),
                'msg' => $e->getMessage(),
            ];
            $this->ajaxReturn($err);
        }
    }
    

    /**
     * 商品评论列表
     */
    public function comments()
    {       
        $comment = D('comment');
        strlen(I('get.grade')) ? $map['grade'] = (int)I('get.grade'):'';
        strlen(I('get.status')) ? $map['status'] = (int)I('get.status'):'';   
        // ---- 解决非第一页搜索条件$_GET['p']不等于1的情况【start】
        if(I('sou')){
            $_GET['p'] = 1;
            unset($_GET['sou']);
        }
        // ---- 【end】             
        if($map !== NULL && count($map) > 0){
           $comment->where($map);          
        }
        $comment2 = clone $comment;
        $total = $comment->count();
        $page  = new \Think\Page($total,10);
        $pageButton =$page->show();
        $data = $comment2->relation(['user','good','pics'])->limit($page->firstRow.','.$page->listRows)->select();
  
        $this->assign('list',$data);
        $this->assign('page',bootstrap_page_style($pageButton));
        $this->display();
    }

    /**
     * 商品评论修改显示状态
     */
    public function comment_edit()
    {
        if(IS_AJAX) {
            $id = I('get.id');
            $status = I('get.status');
            if(!is_numeric($id) || !is_numeric($status)){
                return $this->ajaxReturn(['code'=>401,'msg'=>'修改失败','errMsg'=>'参数错误']);
            }
            $res = D('comment')->where(['id'=>$id])->data(['status'=>$status])->save();
            if($res){
                return $this->ajaxReturn(['code'=>200,'msg'=>'修改成功']);
            } else {
                return $this->ajaxReturn(['code'=>401,'msg'=>'修改失败']);
            }
        }
    }
}
