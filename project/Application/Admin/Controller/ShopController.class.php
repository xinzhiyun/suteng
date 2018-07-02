<?php
namespace Admin\Controller;
use Common\Tool\File;
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
        $map = [];
        if (!empty(I('get.key')) && !empty(I('get.keywords'))) {
            $map[I('get.key')] = array('like',"%".trim(I('get.keywords'))."%");
        }
        $data = D('Category')->getTreeData('tree','id, name',$name='name',$child='id',$parent='pid',$map);
        $assign = [
            'data' => $data['data']
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

            if(!$cate->create($data)) E($cate->getError(),203);
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

    public function appendChildCategory(){
        $pid = I('post.pid');

        $pic = '';
        if(!empty($_FILES)){
            $pic = File::upload('Category');
            if($pic){
                $pic = '/Public/upload'.$pic[0];
            }
        }

        $leavel = D('Category')->where('id='.$pid)->getField('leavel')+1;

        $cateName = I('post.catename');
        if(!$pid || strlen($cateName) < 1){
            $this->ajaxReturn(['code'=>'-1','msg'=>'参数错误']);
        }
        $res = D('Category')->data(['name'=>trim($cateName), 'pid'=>$pid, 'leavel'=>$leavel, 'pic'=>$pic])->add();
        if($res) {
            $this->ajaxReturn(['code'=>'200','msg'=>'添加成功','data'=>I('')]);
        } else {
            $this->ajaxReturn(['code'=>'-1','msg'=>'添加失败']);
        }
    }

    // 修改分类
    public function cateGoryEdit()
    {
        try {
            $cate = D('Category');
            $post = I('post.');

            if(!empty($_FILES)){
                $pic = File::upload('Category');
                if($pic){
                    $post['pic'] = '/Public/upload'.$pic[0];
                }
            }

            $where['id'] = $post['id'];
            unset($post['id']);
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

    // 广告编辑
    public function cateGoryAdvEdit()
    {
        try {
            $cate = D('Category');
            $post = I('post.');
            $where['id'] = $post['id'];
            unset($post['id']);
            $adv = json_decode(htmlspecialchars_decode($post['adv']),true);

            $adv = array_sort($adv,'sort');

            $post['adv'] = json_encode($adv,JSON_UNESCAPED_UNICODE);

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

    // 广告图片
    public function cateGoryAdvPic()
    {

        if(!empty($_FILES)){
            $picInfo = File::upload('CategoryAdv');
            if($picInfo){
                $pic = '/Public/upload'.$picInfo[0];
            }
        }
        $this->ajaxReturn(['code'=>200,'pic'=>$pic]);
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
            }
            // 判断该分类下的商品
            $count = D('goods')->where(['cid'=>$id,'status'=>['NEQ',2]])->count();
            if($count > 0){
                E('该分类下存在商品，不可删除',204);
            }
            
            $res = $cate->where('id='.$id)->delete();
            if($res) E('分类删除成功', 200);
            
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
            'show' => $goodsList['show'],
        ];

        // dump($goodsList);
        $this->assign($assign);
        $this->display();
    }

    //加载商品详情
    public function goodsDetail($id)
    {
        // dump($id);
        $map['g.id'] = $id;
        $goodsInfo = D('Goods')
                ->alias('g')
                ->where($map)
                ->join('__GOODS_DETAIL__ gd ON g.id=gd.gid', 'LEFT')
                ->field('gd.desc,gd.specs,gd.saleservice,g.name')
                ->find();

        // dump($goodsInfo);
        $this->assign('goodsInfo', $goodsInfo);
        $this->display();

    }

    /**
     * [goodsHuishou 回收站商品信息]
     * @return [type] [description]
     */
    public function goodsHuishou()
    {
        $map['g.status'] = array('eq',2);
        $count = D('Goods')
            ->where($map)
            ->alias('g')
            ->join('__GOODS_DETAIL__ gd ON g.id=gd.gid', 'LEFT')
            ->join('__CATEGORY__ c ON g.cid=c.id', 'LEFT')
            ->join('__INVENTORY__ i on i.gid=g.id' , 'LEFT')
            ->field('c.name cname,g.*')
            ->limit($Page->firstRow.','.$Page->listRows)
            ->count();
            $Page  = new \Think\Page($count,10);
            $pageButton =$Page->show();
        $goodsData = D('Goods')
            ->where($map)
            ->alias('g')
            ->join('__GOODS_DETAIL__ gd ON g.id=gd.gid', 'LEFT')
            ->join('__CATEGORY__ c ON g.cid=c.id', 'LEFT')
            ->join('__INVENTORY__ i on i.gid=g.id' , 'LEFT')
            ->field('c.name cname,g.*')
            ->order(' addtime desc')
            ->limit($Page->firstRow.','.$Page->listRows)
            ->select();
        $assign = [
            'data' => $goodsData,
            'show' => $pageButton,
        ];

        $this->assign($assign);
        $this->display();
    }

    // 添加商品
    public function goodsAdd()
    {
        $cate = D('Category');
        $cateInfo = $cate->where('pid=0')->select();

        $blockInfo = M('goods_block')->field('id,bname')->select();


        /* 添加快递公司选择 */
        $courier = M('courier');
        $courierList = $courier->where('status=1')->field('id,name')->select();
        /* 结束 */

        $assign = [
            'cateInfo' => $cateInfo,
            'attrInfo' => $attrInfo,
            'courierList' => $courierList,
            'blockInfo' => $blockInfo,
        ];
        $this->assign($assign);
        $this->display();
    }

    /**
     * [getAttr 通过分类id获取该分类下的所有属性]
     * @return [type] [description]
     */
    public function getAttr()
    {
        $attr = D('attr');
        $map['cid'] = $_POST['cid'];

        $attr = M('attr')->field('id,attr')->where($map)->select();

        foreach ($attr as $key => &$value) {

            $value['data'] = M('attr_val')->field('id,val')->where('aid='.$value['id'])->select();

            if (empty($value['data'])) unset($attr[$key]);

        }

        // dump($attr);die;
        $a = json_encode(array_values($attr));
        
        $this->ajaxReturn($a);
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
                E('已放到回收站', 200);
            } else {
                E('放到回收站失败', 606);
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
     * 二进制流图片上传
     * @param  [type] $image     二进制图片
     * @param  [type] $imageSize 图片大小
     * @return mix            图片路径，false
     */
    public function upload($image,$imageSize)
    {
        $key = fopen($image, "r");
        $file = fread($key, $imageSize); //二进制数据流
        fclose ( $key );

        do{
             //设置目录+图片完整路径
            $save_name = md5 (time().mt_rand(100000,9999999)).'.png';
            $save_dir = './Uploads/goods';
            ! is_dir ( $save_dir ) && mkdir ( $save_dir,0777,1 );
            $save_fullpath = $save_dir . '/' . $save_name;
        }while (file_exists());

        @$fp = fopen ( $save_fullpath, 'w+' );
        
        if (fwrite ( $fp, $file ) != false) {
            return $save_fullpath;
        } else {
            return false;
        }
        fclose ( $fp );        
    }

    /**
     * [删除文件]
     * @param  Array $files 文件路径数组
     * @return Boolean        
     */
    public function rmfiles($files)
    {
        foreach ($files as $key => $value) {
            return unlink($value);
        }
    }


    // 商品添加处理
    public function goodsAction()
    {
        try{
            //商品表
            $goods_add = D('Goods');
            //商品详情
            $goods_detail = D('GoodsDetail');
            //商品sku库存
            $GoodsSku = D('GoodsSku');
            //商品与主题的关系
            $GoodsRelationBlock = D('GoodsRelationBlock');
            //商品快递
            $GoodsCourier = D('GoodsCourier');
            //商品价格
            $GoodsPrice = D('GoodsPrice');
        


            //商品分类id
            $goods['cid'] = $_POST['seccate'];
            // 分类路径
            $catepath = [];
            if($_POST['firscate'] && $_POST['firscate'] !='--')$catepath[]   = $_POST['firscate'];
            if($_POST['seccate'] && $_POST['seccate'] !='--')$catepath[]    = $_POST['seccate'];
            if($_POST['thirdcate'] && $_POST['thirdcate'] !='--')$catepath[]  = $_POST['thirdcate'];

            $goods['catepath'] = implode(',', $catepath);

            //添加该商品的商户
            $goods['vid'] = $_SESSION['adminInfo']['id'];
            //商品名
            $goods['name'] = $_POST['name'];
            //商品状态，默认是下线
            $goods['status'] = 0;
            //个人会员价格
            $goods['price'] = $_POST['price'][1];
            //成本价
            $goods['cost'] = $_POST['cost'];
            //商品总库存
            $goods['stock'] = $_POST['allnum'];
            //是否安装
            if($_POST['is_install'] == 'on'){
                $goods['is_install'] = 1;
            } else {
                $goods['is_install'] = 0;
            }
            //是否租赁
            if($_POST['is_hire'] == 'on'){
                $goods['is_hire'] = 1;
            } else {
                $goods['is_hire'] = 0;
            }
            //添加时间
            $goods['addtime'] = time();
            //更新时间
            $goods['updatetime'] = time();

            //图片上传         
            // 二进制文件上传简单处理
            if (!empty($_FILES["UploadForm"])) {
                foreach ($_FILES["UploadForm"]["tmp_name"] as $key => $value) {
                    $image = $_FILES["UploadForm"]["tmp_name"][$key];
                    $imageSize = $_FILES["UploadForm"]["size"][$key];
                    $info[] = $this->upload($image,$imageSize);                        
                }
            }else{
                E('没有文件上传', 602);
            }   
            
            if(!$info) {// 上传错误提示错误信息
                E('上传错误',606);
            }
            if(!(count($info) <= 5)){
                E('只能添加五张张图片',604);
            }

            foreach ($info as $k => $val) {
                $path .= ltrim($val,'.').'|';
                $goods['gpic'] = ltrim($val,'.');
            }

            // 事务开启
            $goods_add->startTrans();
            $goods_detail->startTrans();
            $GoodsSku->startTrans();
            $GoodsRelationBlock->startTrans();
            $GoodsCourier->startTrans();
            $GoodsPrice->startTrans();

            if(!$goods_add->create($goods)) {
                E($goods_add->getError(),406);
            }
            // 商品添加
            $goodsid = $goods_add->add();

            //商品详情id
            $goodsDetail['gid'] = $goodsid;
            //商品详情多图
            $goodsDetail['pic'] = $path;
            //商品详情描述
            $goodsDetail['desc'] = $_POST['desc'];
            //商品详情规格
            $goodsDetail['specs'] = $_POST['specs'];
            //商品详情售后服务
            $goodsDetail['saleservice'] = $_POST['saleservice'];
            //商品详情添加时间
            $goodsDetail['addtime'] = time();
            //商品详情更新时间
            $goodsDetail['updatetime'] = time();    
            
            if(!$goods_detail->create($goodsDetail)) E($goods_detail->getError(),408);
            // 商品详情添加
            $goodsDetail_status = $goods_detail->add($goodsDetail);
            
            
            //sku库存数据写入
            $skuattrs = json_decode($_POST['skuattr'],true);

            
            foreach ($skuattrs as $key => $value) {
                $sku = array();

                $sku['gid'] = $goodsid; //sku商品id
                //属性组合库存
                $skuattrArr=array_column($value['list'],'id');
                sort($skuattrArr);
                $sku['skuattr'] = implode('_', $skuattrArr);//属性值id组合

                $sku['skustock'] = $value['stock'];//属性组合库存

                $sku['addtime'] = time();
                $sku['updatetime'] = time();

                if(!$GoodsSku->create($sku)) E($GoodsSku->getError(),408);
                // 商品sku添加
                $skustatus = $GoodsSku->add($sku);
                
            }

            //商品与活动主题的关系
            $goodsBlock['gid'] = $goodsid;
            $goodsBlock['bid'] = $_POST['bid'];
            $goodsBlock['addtime'] = time();
            $goodsBlock['updatetime'] = time();
            if(!$GoodsRelationBlock->create($goodsBlock)) E($GoodsRelationBlock->getError(),408);
            // 商品与主题活动的关系写入
            $GoodsRelationBlockstatus = $GoodsRelationBlock->add($goodsBlock);

           
            /* 商品快递费添加 */
            foreach ($_POST['courier'] as $key => $value) {
                $data['gid'] = $goodsid;
                $data['cid'] = $key;
                $data['cprice'] = $value;
                $data['addtime'] = time();
                $data['updatetime'] = time();


                //进行添加
                $GoodsCourierStatus = $GoodsCourier->add($data);
    
            }

            //商品快递费添加完成后，将快递公司名字也写入库
            foreach ($_POST['courierName'] as $key => $value) {
                $gid = $goodsid;
                $cid = $key;
                $infos['cname'] = $value;
                $infos['updatetime'] = time();
                //进行添加
                M('GoodsCourier')->where('gid='.$gid .' AND cid='.$cid)->save($infos);   
            }


            /* 商品快递费添加 */

            // 商品单价添加
            foreach ($_POST['price'] as $key => $value) {
                $p['price'] = $value;
                $p['grade'] = $key;
                $p['gid'] = $goodsid;
                $p['addtime'] = time();
                $p['updatetime'] = time();
                $price_status = $GoodsPrice->add($p);
            }
            if($goodsid && $goodsDetail_status && $skustatus && $GoodsRelationBlockstatus && $GoodsCourierStatus && $price_status){
                $goods_add->commit();
                $goods_detail->commit();
                $GoodsSku->commit();
                $GoodsRelationBlock->commit();
                $GoodsCourier->commit();
                $GoodsPrice->commit();
                // dump(1);
                // die;
                E('商品添加成功，可继续添加',200);
            } else {
                $goods_add->rollback();
                $goods_detail->rollback();
                $sku->rollback();
                $GoodsRelationBlock->rollback();
                $GoodsCourier->rollback();
                $GoodsPrice->rollback();
                //失败删除图片
                $this->rmfiles($info);
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

    // 商品上下架状态修改
    public function edidStatus(){
        try {
            // dump($_POST);die;
            $goods = D('Goods');
            $where['id'] = I('post.id');
            $data['status'] = I('post.status');
            $res = $goods->where($where)->save($data);
            // echo $goods->_sql();
            // p(I(''));die;
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
        $gid = $_GET['id'];
        $goods_mode = D('goods');
        $goodsInfo = $goods_mode->find($gid);

        $goodsDetail = M('goodsDetail')->where('gid='.$gid)->field('pic,desc,specs,saleservice')->find();
        $goodsDetail['pic'] = explode('|', $goodsDetail['pic']);
        $goodsInfo = array_merge($goodsInfo, $goodsDetail);

        $goodsAttr = M('goodsSku')->where('gid='.$gid)->select();

        $attrValArr = [];//值
        $attrArr = [];  //属性
        $attrValRel = [];  //属性和值的关系

        foreach ($goodsAttr as $item){
            $attrs =  explode('_',$item['skuattr']);
            foreach ($attrs as $attrid){
                if(empty($attrValArr[$attrid])){
                    $tmpAttr = M('attrVal')->find($attrid);
                    $attrValArr[$attrid] = $tmpAttr['val'];
                    $attrValRel[$tmpAttr['aid']][] = $attrid;
                    if(empty($attrArr[$tmpAttr['aid']])){
                        $tmpAttrs = M('attr')->find($tmpAttr['aid']);
                        $attrArr[$tmpAttr['aid']] = $tmpAttrs['attr'];
                    }
                }
            }
        }
        $attrRes = [];
        foreach ($attrArr as $atrrid =>$attrVal){
            $tmpRes = [];
            $tmpRes['name']=$attrVal;
            $tmpAttr = [];
            foreach ($attrValRel[$atrrid] as $attrItem){
                $tmpAttr['pname'] = $attrArr[$atrrid];
                $tmpAttr['name']  = $attrValArr[$attrItem];
                $tmpAttr['id']    = $attrItem ;
                $tmpRes['list'][]=$tmpAttr;
            }
            $attrRes[] = $tmpRes;
        }

        $where['gid'] = $gid;

        //商品会员价格
        $goodsInfo['price'] = M('goodsPrice')->field('price')->where($where)->find();
        $goodsInfo['goodsCourier'] = M('goods_courier')->where('gid='.$gid)->field('cid,cname,cprice')->select();;

        $this->assign('goodsDetail', $goodsInfo);
        $this->display();
    }

    /*
    // 修改商品
    public function goodEdit()
    {





        // //修改商品之前先查询出所有的快递公司
        // $courierList = M('courier')->where('status = 1')->select();

        // //查询出所有分类
         $categoryList = M('category')->field('id, name')->select();

        // //查询出所有属性
//         $arrtList = M('attr')->select();
        
        // //获取商品id
        // $id = $_GET['gid'];
        // //实例化商品对象
        // $goods = M('goods');

        // $goodsInfo=D('goods')->getGoodInfo($id);

        // // dump($goodsInfo);
        // $price = $goodsInfo['price'];
        // $attrVal = $goodsInfo['attr_val'];
        // $goodsCourier = $goodsInfo['goods_courier'];
        // $goodsDetail = $goodsInfo['goods_detail'];

        // $goodsPics = D('pic')->where(['gid'=>$id])->select();
     
        // //处理属性所属
        // foreach ($attrVal as $value) {
        //     $attrValArr[]=$value['aid'];
        // }

        // foreach ($arrtList as &$value) {
        //     if(in_array($value['id'], $attrValArr)){
        //         $value['check']='checked';
        //     }else{
        //         $value['check']='';
        //     }
        // }

        // // dump($attrVal);
        // //处理商品对应属性的属性值
        // if (!empty($attrVal)) {
        //     foreach ($attrVal as $key => $val) {
        //         $newattrVal[$val['aid']] = $val['val'];
        //     }
        // } else {
        //     $newattrVal = '';
        // }
        

        // //处理快递所属
        // foreach ($goodsCourier as $v) {
        //     $goodsDetailArr[]=$v['cid'];
        // }

        // foreach ($courierList as &$v) {
        //     if(in_array($v['id'], $goodsDetailArr)){
        //         $v['check']='checked';
        //     }else{
        //         $v['check']='';
        //     }
        // }

        // //处理商品对应的快递费
        // if (!empty($goodsCourier)) {
        //     foreach ($goodsCourier as $key => $cval) {
        //         $gcVal[$cval['cid']] = $cval['cprice'];
        //     }
        // } else {
        //     $gcVal = '';
        // }
        

        // $this->assign('courierList', $courierList);
        // $this->assign('categoryList', $categoryList);
        // $this->assign('arrtList', $arrtList);
        // $this->assign('goodsInfo', $goodsInfo);
        // $this->assign('price', $price);
        // $this->assign('newattrVal', $newattrVal);
        // $this->assign('gcVal', $gcVal);
        // $this->assign('goodsDetail', $goodsDetail);
        $this->display(); 
    }
    */

    // 商品编辑处理
    public function goodsEditAction()
    {

        if(!empty($_FILES)){
            $upload = new \Think\Upload();// 实例化上传类
            $upload->maxSize   =     3145728 ;// 设置附件上传大小
            $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
            $upload->rootPath  =     './Uploads/'; // 设置附件上传根目录
            $upload->savePath  =     ''; // 设置附件上传（子）目录
            // 上传文件 
            $info   =   $upload->upload();
            if(!$info) {// 上传错误提示错误信息
                // $this->error($upload->getError());
            }else{// 上传成功
                // $this->success('上传成功！');
            }
            // p($info);
        }
        // p($_FILES);die;

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
        $_GET['keywords'] = \urldecode($_GET['keywords']); 
        $map = [];
        if (!empty(I('get.key')) && !empty(I('get.keywords'))) {
            $map[I('get.key')] = array('like',"%".I('get.keywords')."%");
        }
        if(\strlen(I('status'))){
            $map['o.status'] = I('status');
        }
        $order = D('ShopOrder');
        $data = $order->getOrders($map);

        foreach ($data['data'] as $key => $value) {
            $data['data'][$key]['cprice'] = '订单详情查看';
        }

        // dump($data);

        // $data = $order
        //             ->alias('o')
        //             ->join('st_users u ON o.uid = u.id','LEFT')
        //             ->join('st_address ad ON o.address_id = ad.id','LEFT')
        //             ->field('o.*,u.nickname,ad.name as adname,ad.phone as adphone')
        //             ->select();
        // dump($data);
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
    
    public function getinvoice($id){
        $data = D('Invoice')->find($id);
        $this->ajaxReturn(['code'=>200,'msg'=>'success','data'=>$data]);
    }

    /**
     * 为订单发货
     * @return [json] [description]
     */
    public function ship()
    {
        if (IS_AJAX) {
            try{
                // $id = I('post.orderid');
                // $data['express_name'] = I('post.express_name');
                // $data['express'] = I('post.express');
                // $data['status'] = 2;
                // $order = D('ShopOrder');
                // // print_r(I(''));die;
                // $res = $order->where('order_id='.$id)->save($data);

                $postData = I('post.');
                $orderDetail = D('ShopOrderDetail');
                $orderDetail->startTrans();
                $order = D('shopOrder')->where(['order_id'=>$postData['orderid']])->field('g_type')->find();

                switch ($order['g_type']) {
                    case 0:
                        # code...
                        break;
                    
                    case 1:
                            // dump($postData);die;
                            foreach($postData['express'] as $val){
                                $orderDetail
                                        ->data(['express_name'=>$val['express_name'],'express'=>$val['express_value']])
                                        ->where(['order_id'=>$postData['orderid'],'cid'=>$val['cid'],'gid'=>$val['gid']])
                                        ->save();
                            }
                        break;
    
                    case 2:
                           $res = $orderDetail
                            ->data(['cid'=>$postData['express'][0]['cid'],'express_name'=>$postData['express'][0]['express_name'],'express'=>$postData['express'][0]['express_value']])
                            ->where(['order_id'=>$postData['orderid']])
                            ->save();                          
                        break;
    
                    case 3:
                        # code...
                        break;
                    default:
                        # code...
                        break;
                }
                $res2 = D('shopOrder')->where(['order_id'=>$postData['orderid']])->setField('status',2);
                $orderDetail->commit();
                return $this->ajaxReturn(['code'=>200,'msg'=>'发货成功']);

            } catch (\Exception $e){
                $orderDetail->rollback();
                return $this->ajaxReturn(['code'=>501,'msg'=>'发货失败']);
            }
                
        }             
    }

    // 查看订单详情
    public function selectOrder()
    {
        $orderDetail = D('ShopOrderDetail');
        $orders = D('ShopOrder');
        $order['order_id'] = I('get.id');
        $userInfo = $orders->getUserInfo($order);
        $map['od.order_id'] = $order['order_id'];
        if($userInfo['g_type'] == 2){
            $data = $orderDetail->getLvxinInfo($map);
        } else {
            $data = $orderDetail->getInfo($map);
        }



        $rids = array();
        $refund_goods =  M('refund_goods')->where('oid='."'{$order['order_id']}'")->select();

        //查找退货的商品id
        foreach ($refund_goods as $rd => $rdvalue) {
            $rids[] = $rdvalue['gid'];
        }

        //将订单商品总价加上商品的快递费 及判断该订单是否有退货商品
        foreach ($data as $key => $value) {
           
            if (in_array($value['gid'],$rids)) {
                    
                $data[$key]['tuihuo'] = '退货';     
                
            } else {
                $data[$key]['tuihuo'] = '正常';
            }

            
            $userInfo['g_price'] += $value['cprice']*$value['num'];
        }

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
            $data = M('GoodsPrice')->where('gid='.$gid)->select();
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
            //比较异常库存与总库存
            $allnum = $inventory->where(['gid'=>$id])->find();
            if($data['abnormalnum'] > $allnum['allnum']){
                E('超过总库存了',203);
            }
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
        $data = $comment2->relation(['user','good','pics'])->limit($page->firstRow.','.$page->listRows)->order('addtime desc')->select();
  
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

    /**
     * [block 主题活动显示]
     * @return [type] [description]
     */
    public function block()
    {
        //接受处理搜索条件
        if (!empty(I('get.keywords'))) {
            $map['bname'] = array('like',"%".I('get.keywords')."%");
        }

        $goodsblock = D('goods_block');
        $blist = $goodsblock->where($map)->select();

        $this->assign('blist', $blist);
        $this->display();
    }

    /**
     * [blockAddList 加载主题活动编辑页面]
     * @return [type] [description]
     */
    public function blockAddList()
    {
        $this->display('block_add');
    }

    /**
     * [uploads description]
     * @return [type] [description]
     */
    public function uploads(){
        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize   =     3145728 ;// 设置附件上传大小
        $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
        $upload->rootPath  =     './Uploads/'; // 设置附件上传根目录
        $upload->savePath  =     ''; // 设置附件上传（子）目录
        $upload->autoSub = true;
        // 上传文件 
        $info   =   $upload->upload();

        if(!$info) {
            // 上传错误提示错误信息
            return 0;
        }else{
            // 上传成功
            return $info;
        }
    }
     /**
     * [courierAdd 执行活动添加]
     * @return [type] [description]
     */
    public function blockAdd()
    {
        try {      
            //接受POST数据
            $data['bname'] = $_POST['name'];
            $data['status'] = $_POST['status'];
            $data['addtime'] = time();
            $data['updatetime'] = time();

            //图片上传
            $info = $this->uploads();
            if ($info == 0) E('图片上传失败',203);

            $data['pic'] = $info['pic']['savepath'].$info['pic']['savename'];
            $path = './Uploads/'.$info['pic']['savepath'].$info['pic']['savename'];
            
            $goodsblock = M('goods_block');

            //添加活动前先判断活动是否已经存在于表中
            if ($goodsblock->where("bname = '{$data['bname']}'")->find()) {
                unlink($path);
                E('该活动名已存在，如需改名请前往更改页面更改','203');

            } else {
                //添加主题活动
                $infos = $goodsblock->add($data);

                if ($infos) {
                        E('添加成功',200);
                    }else{
                        //
                        unlink($path);
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
     * [courierEditList 活动编辑页面加载]
     * @return [type] [description]
     */
    public function blockEditList($id)
    {
        if (empty($bid)) {
            $bid = $id;
        }

        // dump($bid);
        $binfo = M('goods_block')->find($bid);

        $this->assign('binfo', $binfo);
        $this->display('block_edit');
    }

    /**
     * [courierEdit 执行修改活动]
     * @return [type] [description]
     */
    public function blockEdit()
    {
        try {
                
            //接受POST数据
            $data['bname'] = $_POST['name'];
            $id = $_POST['id'];
            $data['status'] = $_POST['status'];
            $data['updatetime'] = time();

            // dump($_POST);die;
            //如果有文件上传的话，取新文件
            if ($_FILES['pic']['error'] == 0) {
                //图片上传
                $info = $this->uploads();
                if ($info == 0) E('图片上传失败',203);
                $data['pic'] = $info['pic']['savepath'].$info['pic']['savename'];
                $path = './Uploads/'.$info['pic']['savepath'].$info['pic']['savename'];

                //原图
                $path1 = './Uploads/'.M('goods_block')->field('pic')->find($id)['pic'];
            } 
            
            $goods_block = M('goods_block');
            //修改库存数据
            $info = $goods_block->where('id='.$id)->save($data);

            if ($info) {

                    //修改成功就删除原图
                    unlink($path1);
                    E('修改成功',$info);
                }else{

                    //失败删除新图
                    unlink($path);
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
     * [courierDel 主题活动删除]
     * @return [type] [description]
     */
    public function blockDel()
    {
        try {
                
            //接收要删除数据的id
            $id = $_GET['id'];

            //图片路径
            $path = './Uploads/'.M('goods_block')->field('pic')->find($id)['pic'];

            $goods_block = M('goods_block');
            //删除主题活动之前先判断该活动下是否有商品存在，存在则先要清空商品
            $glList = M('goods_relation_block')->where('bid='.$id)->select();

            if (!empty($glList)) E('改专题下还有商品，请先清除商品再删除',203);

            
            $info = $goods_block->where('id='.$id)->delete();
            
            
            if ($info) {
                unlink($path);
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
     * [attr 属性显示]
     * @return [type] [description]
     */
    public function attr()
    {
        // dump($_SESSION);
        $map = [];
        if (!empty(I('get.key')) && !empty(I('get.keywords'))) {
            $map[I('get.key')] = array('like',"%".trim(I('get.keywords'))."%");
        }
        $data = D('attr')->order('cid desc')->select();

        // dump($data);
        $category = D('Category')->getTreeData('tree','id, name',$name='name',$child='id',$parent='pid');
        $assign = [
            'data' => $data
        ];

        // dump($category);
        $this->assign('category', $category['data']);
        $this->assign($assign);
        $this->display();
    }


    /**
     * [attrAdd 添加属性名]
     * @return [type] [description]
     */
    public function attrAdd()
    {
        try {
            $attr = D('Attr');
            $_POST['addtime'] = time();
            $_POST['updatetime'] = time();
            $data = I('post.');

            if(!$attr->create()) {
                E($attr->getError(),204);
            }
            $res = $attr->add();
            if($res){
                E('添加成功', 200);
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
     * [attrEdit 属性修改]
     * @return [type] [description]
     */
    public function attrEdit()
    {
        try {
            $attr = D('attr');
            $post = I('post.');

            $where['id'] = $post['id'];
            unset($post['id']);
            if(!$attr->create($post)) E($cate->getError(),203);
            $res = $attr->where($where)->save($post);
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


    // 加载属性值
    public function getAttrVal()
    {
        try {

            $post = I('post.');

            if(empty($post['pid'])) {
                E('数据错误',204);
            }
            $attrval = D('attrVal');

            $res = $attrval->where('aid='.$post['pid'])->select();

            $this->ajaxReturn([
                'code'=>200,
                'data'=>$res,
                'msg'=>'获取成功',
            ]);


        } catch (\Exception $e) {
            $err = [
                'code' => $e->getCode(),
                'msg' => $e->getMessage(),
            ];
            $this->ajaxReturn($err);
        }
    }
    // 属性值添加
    public function attrValAdd()
    {
        try {
            $post = I('post.');
            $attrval = D('attrVal');

            //$aids = $attrval->where('aid='.$post['aid'])->field('id,val')->select();

            $vals = json_decode(htmlspecialchars_decode($post['vals']),true);

            $time = time();
            $add = [];
            foreach ($vals as $val){
                if(empty($val['id'])){
                    $val['aid'] = $post['aid'];
                    $val['addtime'] = $time;
                    $val['updatetime'] = $time;
                    unset($val['id']);
                    $add[] = $val;
                }else{
                    $attrval->where('id='.$val['id'])->save(['val'=>$val['val']]);
                }
            }

            $res = $attrval->addAll($add);

            if($res){
                E('修改成功', 200);
            } else {
                E('添加属性值失败',203);
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
     * [indexPage banner轮播]
     * @return [type] [description]
     */
    public function indexPage()
    {
        //接受处理搜索条件
        if (!empty(I('get.keywords'))) {
            $map['name'] = array('like',"%".I('get.keywords')."%");
        }

        $goodsblock = D('banner');
        $bannerlist = $goodsblock->where($map)->select();

        $this->assign('blist', $bannerlist);
        $this->display();
    }
    
    

    /**
     * [blockAddList 加载banner编辑页面]
     * @return [type] [description]
     */
    public function bannerAddList()
    {
        $this->display('banner_add');
    }

    
     /**
     * [courierAdd 执行banner添加]
     * @return [type] [description]
     */
    public function bannerAdd()
    {
        try {      
            //接受POST数据
            $data['name'] = $_POST['name'];
            $data['status'] = $_POST['status'];
            $data['url'] = $_POST['url'];
            $data['addtime'] = time();
            $data['updatetime'] = time();

            //图片上传
            $info = $this->uploads();
            if ($info == 0) E('图片上传失败',203);

            $data['pic'] = $info['pic']['savepath'].$info['pic']['savename'];
            $path = './Uploads/'.$info['pic']['savepath'].$info['pic']['savename'];
            
            $banner = M('banner');

            //添加先判断banner活动是否已经存在于表中
            if ($banner->where("name = '{$data['name']}'")->find()) {
                unlink($path);
                E('该banner名已存在，如需改名请前往更改页面更改','203');

            } else {
                //添加主题活动
                $infos = $banner->add($data);

                if ($infos) {
                        E('添加成功',200);
                    }else{
                        //
                        unlink($path);
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
     * [courierEditList banner编辑页面加载]
     * @return [type] [description]
     */
    public function bannerEditList($id)
    {
        if (empty($bid)) {
            $bid = $id;
        }

        // dump($bid);
        $bnnerInfo = M('banner')->find($bid);

        $this->assign('binfo', $bnnerInfo);
        $this->display('banner_edit');
    }

    /**
     * [courierEdit 执行修改banner]
     * @return [type] [description]
     */
    public function bannerEdit()
    {
        try {
                
            //接受POST数据
            $data['name'] = $_POST['name'];
            $id = $_POST['id'];
            $data['status'] = $_POST['status'];
            $data['url'] = $_POST['url'];
            $data['updatetime'] = time();

            // dump($_POST);die;
            //如果有文件上传的话，取新文件
            if ($_FILES['pic']['error'] == 0) {
                //图片上传
                $info = $this->uploads();
                if ($info == 0) E('图片上传失败',203);
                $data['pic'] = $info['pic']['savepath'].$info['pic']['savename'];
                $path = './Uploads/'.$info['pic']['savepath'].$info['pic']['savename'];

                //原图
                $path1 = './Uploads/'.M('banner')->field('pic')->find($id)['pic'];
            } 
            
            $banner = M('banner');
            //修改库存数据
            $info = $banner->where('id='.$id)->save($data);

            if ($info) {

                    //修改成功就删除原图
                    unlink($path1);
                    E('修改成功',$info);
                }else{

                    //失败删除新图
                    unlink($path);
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
     * [courierDel banner删除]
     * @return [type] [description]
     */
    public function bannerDel()
    {
        try {
                
            //接收要删除数据的id
            $id = $_GET['id'];

            //图片路径
            $path = './Uploads/'.M('banner')->field('pic')->find($id)['pic'];

            $banner = M('banner');
            //删除主题活动之前先判断该活动下是否有商品存在，存在则先要清空商品
            $glList = M('goods_relation_block')->where('bid='.$id)->select();

            if (!empty($glList)) E('改专题下还有商品，请先清除商品再删除',203);

            
            $info = $banner->where('id='.$id)->delete();
            
            
            if ($info) {
                unlink($path);
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
    
}
