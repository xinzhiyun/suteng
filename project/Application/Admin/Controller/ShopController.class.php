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

        $gid = $_GET['gid'];
        $where['st_goods.id'] = $gid;
        $goods = D(Goods);
        $price = M(Price);
       
        $goodsInfo = $goods->where($where)
                  ->join('st_goods_detail ON st_goods.id = st_goods_detail.gid')
                  ->field('st_goods.id,st_goods.name,st_goods_detail.cost,st_goods_detail.stock,st_goods_detail.cost')
                  ->select();
        // 查价格
        // 重新定义条件
        $wheres['gid'] = $gid;
        $prices = $price->where($wheres)->select();

        $pre = array();
        foreach ($prices as $key => $value) {
            $pre[0][$value['grade']] = $value['price'];

        }
        // 分类
        $cate = D('Category');
        $cateInfo = $cate->where('pid=0')->select();
        // 商品属性
        $attr = D('Attr');
        $attrInfo = $attr->select();
        $assign = [
            'cateInfo'  => $cateInfo,
            'attrInfo'  => $attrInfo,
            'goodsInfo' => $goodsInfo,
            'pre'       => $pre,
        ];
        // dump($assign);
        $this->assign($assign);
        $this->display();
        
    }

    // 商品编辑处理
    public function goodsEditAction()
    {
        try{
            $goods_add = D('Goods');
            $attr_val = D('AttrVal');
            $attr = D('Attr');
            $goods_detail = D('GoodsDetail');
            $cate = D('Category');
            $data = I('post.');
            // 准备两个条件，给多个表更新用
            $where['id'] = $data['gid'];
            $wheres['gid'] = $data['gid'];

            $price = $data['price'];
            $goods['cid'] = $cate->sureCate();
            if(!$goods['cid']) E('请选择分类', 605);
            $goods['name'] = $data['name'];
            $goods['updatetime'] = time();
            // 事务开启
            $goods_add->startTrans();
            if(!$goods_add->create($goods)) {
                E($goods_add->getError(),406);
            }
            // 商品添加
            $goods_status = $goods_add->where($where)->save($goods);

            $goodsDetail['gid'] = $data['gid'];
            $goodsDetail['cost'] = $data['cost'];
            $goodsDetail['stock'] = $data['stock'];
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
            $goodsDetail_status = $goods_detail->where($wheres)->save($goodsDetail);

            $attrVal['gid'] = $data['gid'];
            // 添加之前先干掉原来的属性
            $attrdel = $attr_val->where($wheres)->delete();
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

            // 商品单价添加
            // dump($price);
            foreach ($price as $key => $value) {

                $p['price'] = $value;
                $wheres['grade'] = $key;
                $price_status = M('Price')->where($wheres)->save($p);
            }
          
            if($goods_status && $attr_val_status){
                $goods_add->commit();
                E('商品编辑成功！',200);
            } else {
                $goods_add->rollback();
                E('商品编辑失败，请添加属性后重新提交',407);
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
            if ($res) {
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


}
