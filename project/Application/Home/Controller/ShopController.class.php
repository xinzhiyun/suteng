<?php
namespace Home\Controller;
use Think\Controller;
use \Org\Util\WeixinJssdk;
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

        if (IS_POST) {
            $goods = D('Goods');
            $cartInfo = M('Cart')->where('uid='.session('user.id'))->count();
            $cate = M('Category')->where('pid=0')->limit(7)->select();
            $banner = M('banner')->field('url,pic')->where('status=1')->select();

            $map['pr.grade'] = session('user.grade');
            $map['g.status'] = array('eq', 1);

            $GoodsBlock = M('GoodsBlock')->field('id,bname')->where('status=1')->select();

            foreach ($GoodsBlock as $key => $value) {
                $blist[] = M('goods_relation_block')->where('bid='.$value['id'])->select();
            
            }
            foreach ($blist as $k => $v) {
               
                for ($i=0; $i < count($v); $i++) { 
                    $str .= $v[$i]['gid'].',';
                }
                $arr[] = $str; 
                $str = '';
            }
            
            foreach ($arr as $keys => $values) {
                    $map['g.id'] = array('in',$values);
                    $GoodsBlock[$keys][] = M('goods')
                        ->alias('g')
                        ->where($map)
                        ->join('__GOODS_PRICE__ pr ON g.id=pr.gid', 'LEFT')
                        // ->join('__GOODS_RELATION_BLOCK__ grb ON g.id=grb.gid', 'LEFT')
                        ->field('g.id,g.name,g.gpic,g.price prices,pr.price')
                        ->select();
                }

            $assign = [
                'cate' => $cate,
                'cartInfo' => $cartInfo,
                'goods' => $GoodsBlock,
                'banner'=> $banner
            ];

            // dump($assign);die;
            return $this->ajaxReturn($assign);
        } else {
            $category = M('category')->where('pid=0')->field('id,name')->select();

            $this->assign('category',json_encode($category,JSON_UNESCAPED_UNICODE)  );
            $this->display();
        }
            
        
    }

    public function getCategory()
    {
        try {
            $post = I('post.');
            if (empty($post['id'])) {
                E('数据不完整', 201);
            } else {
                $map['pid'] = $post['id'];
            }

            $category = M('category')->where($map)->order('sort')->field('id,name,pic')->select();
            $adv = M('category')->where('id='.$post['id'])->getField('adv');
            $adv = json_decode(htmlspecialchars_decode($adv));

            if(empty($adv)){
                $adv=[];
            }
            $this->ajaxReturn(array(
                'status'=>200,
                'data'=>$category,
                'adv'=>$adv,
                'msg'=>'获取成功',
            ),'JSON',JSON_UNESCAPED_UNICODE);
        } catch (\Exception $e) {
            $this->toJson($e);
        }
    }

    /**
     * [getGoodsList 商品列表页接口]
     * @return [type] [description]
     */
    public function getGoodsList()
    {
        try {
            $post = I('post.');
            if (!empty($post['cid'])) {
                $map['cid'] = $post['cid'];
            }

            if(!empty($post['search'])){
                $map['g.name'] = ['like',"%".$post['search']."%"];
            }
            $count = M('goods')->alias('g')->where($map)->count();

            $GoodsMap = M('goods')->alias('g')->where($map);
            
            // 会员价格模式
            if(empty(0)){
                $GoodsMap = $GoodsMap->field('g.id,g.name,g.gpic,g.price');
            }else{
                $grade = " and gp.grade=".'1';
                $GoodsMap = $GoodsMap->join('__GOODS_PRICE__ gp ON g.id=gp.gid '.$grade, 'LEFT');
                $GoodsMap = $GoodsMap->field('g.id,g.name,g.gpic,gp.price');
            }

            $mode = (string)$post['sort'];
            $order_modes = [' desc',' asc'];//0 降序 1升序 默认降序
            $order_modes = $order_modes[$post['sortmode']]?:'';

            //排序模式
            switch ($mode){
                case '1':// 时间
                    $GoodsMap = $GoodsMap->order('g.updatetime'.$order_modes);
                    break;
                case '2':// 人气
                    $GoodsMap = $GoodsMap->order('g.hits'.$order_modes);
                    break;
                case '3':// 价格
                    $orderPrciefield = empty(0)?'g.price':'gp.price'; // 会员价格模式
                    $GoodsMap = $GoodsMap->order($orderPrciefield.$order_modes);
                    break;
                case '4':// 销量
                    $GoodsMap = $GoodsMap->order('g.sales'.$order_modes);
                    break;
            }

            // 分页 兼容
            $_GET['p'] = $post['p'];
            if(!empty($post['sou'])){
                $_GET['p'] = 1;
            }

            $Page       = new \Think\Page($count,5);
            $GoodsMap = $GoodsMap->limit($Page->firstRow.','.$Page->listRows);

            $goodsList = $GoodsMap->select();

            $this->ajaxReturn(array(
                'status'=>200,
                'data'=>$goodsList,
                'msg'=>'获取成功',
            ),'JSON',JSON_UNESCAPED_UNICODE);
        } catch (\Exception $e) {
            $this->toJson($e);
        }
    }

    /**
     * 订单管理
     */
    public function orders()

    {
        $this->display();
    }

    /**
     * [goodsDetail 商品详情]
     * @return [type] [description]
     */
    public function goodsDetail()
    {
        try {
            $post = I('post.');
            if (empty($post['id'])) {
                E('数据错误!', 201);
            } else {
                $map['id'] = $post['id'];
            }

            $goodsInfo = M('goods')->where($map)->field('id,name,price as prices')->find();

            $goodsDetail = M('goodsDetail')->where('gid='.$post['id'])->field('pic,desc,specs,saleservice')->find();
            $goodsDetail['pic'] = explode('|', $goodsDetail['pic']);


            $goodsInfo = array_merge($goodsInfo, $goodsDetail);

            $goodsAttr = M('goodsSku')->where('gid='.$post['id'])->select();

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


            // 其他附加数据
            $where['grade'] = session('user.grade');
            $where['gid'] = $post['id'];

            //商品会员价格
            $goodsInfo['price'] = M('goodsPrice')->field('price')->where($where)->find()['price'];

            $goodsInfo['cartNum'] = M('Cart')->where('uid='.session('user.id'))->count();


            // //商品对应的快递
            // $goodsInfo['goodsCourier'] = M('goods_courier')->where('gid='.$post['id'])->field('cid,cname,cprice')->select();;

            $goodsInfo['attr'] = $attrRes;

            $this->ajaxReturn(array(
                'status'=>200,
                'data'=>$goodsInfo,
                'msg'=>'获取成功',
            ),'JSON',JSON_UNESCAPED_UNICODE);
        } catch (\Exception $e) {
            $this->toJson($e);
        }
    }
    

    // 商品详情页面
    public function goods_detail()
    {
        $map['g.id'] = I('get.id');
        $goods = D('Goods');

        $map['pr.grade'] = session('user.grade');
        $map['gd.status'] = 0;
        $map['g.status'] = array('neq', 2);
        $arr = [];
        $goodsDetail = $goods->getGoodsList($map);
    	foreach($goodsDetail as $key => $val){
            // $goodsDetail[$key]['pics'] = D('Pic')->field('path')->where(['gid'=>$val['gid']])->select();
    		$key = $val['gid'];
    		if(isset($arr[$key])) {
    			$arr[$key]['attr'] .= $val['attr'].':'.$val['val'].'|';
    		} else {
                $arr[$key] = $val;
    			$arr[$key]['attr'] = $val['attr'].':'.$val['val'].'|';
    		}
    	}

        // dump($goodsDetail);
    	// $goodsDetail = array_values($arr);
        // dump($goodsDetail);

        //查询商品对应的快递运费信息
        // $goodsCourier = M('goods_courier')->where('gid='.$_GET['id'])->field('cid,cname,cprice')->select();
        // dump($goodsCourier);


        //获取评论数据
        $data = [
            'goodsDetail' => $goodsDetail,
            // 'goodsCourier' => $goodsCourier,
        ];

        // dump($data);
        $this->ajaxReturn($data);
    }

    public function chooseMeal()
    {  
        $meal = D('setmeal')->select();

        //调用微信JS-SDK类获取签名需要用到的数据
        $weixin = new WeixinJssdk;
        $signPackage = $weixin->getSignPackage();
        // 查询用户微信中的openid
        // $openId = $weixin->GetOpenid();
        $openId = $_SESSION['open_id'];
        //分配数据        
        $this->assign('info',$signPackage);
        $this->assign('openId',$openId);

        $this->assign('list',json_encode($meal));
        $this->display();
    }
    
    // 购物前 库存检查
    public function checkGoodsStock()
    {
        try {
            $post= I('post.');
            // dump($post);
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

            if($res){
                $err = [
                    'code'=> 200,
                    'data'=>$res,
                    'msg' => '获取成功',
                ];
                $this->ajaxReturn($err);
            } else {
                E('该商品类型无库存!',603);
            }
        } catch (\Exception $e) {
            $err = [
                'code' => $e->getCode(),
                'msg' => $e->getMessage(),
            ];
            $this->ajaxReturn($err);
        }
    }

    // 信息确认并生成订单
    public function information()
    {
        try {
            $fliter = D('Filters');
            $orders = D('shop_order');
            $order_detail = D('ShopOrderDetail');
            $data = json_decode($_POST['data'],'true');
            $orders->startTrans();
            $order['uid'] = session('user.id');
            $order['order_id'] = gerOrderId();
            $order['addtime'] = time();
            $order['g_type'] = 2;
            $order['g_cost'] = "";
            $detail = [];
            foreach($data as $key => $value){
                $where['id'] = $value['gid'];
                $order['g_price'] += $value['money'];
                $order['g_num'] += $value['num'];
                $arr = $fliter->alias('g')->where($where)->field('price,cost')->find();
                $order['g_cost'] += $value['num']*$arr['cost'];
                $detail['order_id'] = $order['order_id'];
                $detail['gid'] = $value['gid'];
                $detail['num'] = $value['num'];
                $detail['cost'] = $arr['cost'];
                $detail['price'] = $arr['price'];
                $detail['addtime'] = time();
                $detail_statut = $order_detail->add($detail);
                if(!$detail_statut) E('请重新结算',603);
            }

            $res = $orders->add($order);

            if($res){
                $msg = M('Cart')->where('uid='.session('user.id'))->delete();
                
                $orders->commit();
                $this->ajaxReturn($order['order_id']);
            } else {
                $orders->rollback();
                E('请重新购买',603);
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
     * [paytofailed 订单支付]
     * @return [type] [description]
     */
    public function orderPay()
    {
        if(IS_POST){
            // echo 1;
            // // 准备订单查询数据
            $showWhere['order_id'] = I('post.order');
 
            // // 查询订单表
            $orderData = D('OrderSetmeal')->where($showWhere)->find();

            // 判断订单未支付
            if($orderData['status']==0){
                // 订单金额
                $money = $orderData['goods_price'];
                // 订单号码
                $order_id = $orderData['order_id'];
                // 订单描述
                $content = '速腾商城商品购买';
                $this->uniformOrderTow($money,$order_id,$content);
            }else{
                // 订单不存在
                echo -1;
            }
            // $this->uniformOrder(100,123456,'content');
        }
    }

    /**
     * 统一下单充值并返回数据
     * @return string json格式的数据，可以直接用于js支付接口的调用
     * @param  [type] $money    [订单金额]
     * @param  [type] $order_id [订单号码]
     * @param  [type] $content  [订单详情]
     */
    public function uniformOrder($money,$flow_id,$content,$type)
    {
        // dump($_SESSION);die;
        // $content = substr($content,0,80);
        // 将金额强转换整数
        $money = $money * 100;
        // 冲值测试额1分钱
        $money = 1;
        // 用户在公众号的唯一ID
        $openId = $_SESSION['open_id'];

        //微信examle的WxPay.JsApiPay.php
        vendor('WxPay.jsapi.WxPay#JsApiPay');

        $tools = new \JsApiPay();

        //②、统一下单
        vendor('WxPay.jsapi.WxPay#JsApiPay');
        $input = new \WxPayUnifiedOrder();
        // 傳用戶ID
        // $input->SetDetail($type);
        // 产品内容
        $input->SetBody($content);
        // 唯一订单ID
        $input->SetAttach($flow_id.','.$type);
        // 设置商户系统内部的订单号,32个字符内、可包含字母, 其他说明见商户订单号
        $input->SetOut_trade_no(getOrderId());
        // 产品金额单位为分
        // $input->SetTotal_fee($money);
        // 调试用1分钱
        $input->SetTotal_fee($money);
        // 设置订单生成时间
        // $input->SetTime_start(date("YmdHis"));
        // 设置订单失效时间
        // $input->SetTime_expire(date("YmdHis", time() + 300));
        //$input->SetGoods_tag($uid);
        // 支付成功的回调地址
        // 微信充值回调地址
        $input->SetNotify_url('http://'.$_SERVER['SERVER_NAME'].U('Home/WeiXinPay/congzhiNotify'));
        // 支付方式 JS-SDK 类型是：JSAPI
        $input->SetTrade_type("JSAPI");
        // 用户在公众号的唯一标识
        $input->SetOpenid($openId);
        // print_r($input);die;
        // 统一下单
        $order = \WxPayApi::unifiedOrder($input);
        // print_r($order);die;
        // 返回支付需要的对象JSON格式数据
        $jsApiParameters = $tools->GetJsApiParameters($order);

        echo $jsApiParameters;
        exit;
    }

    // 
    public function shoppingdetail()
    {
        
        $sold_num = D('shop_order_detail')->field('count(num) as sum')->where(['gid'=>I('gid')])->select();
        $cartInfo = M('Cart')->where('uid='.session('user.id'))->count();
        $this->assign('cartInfo',$cartInfo);
        $this->assign('sold_num',$sold_num[0]['sum']);
        // $this->assign('postage',8.99);
        $this->display();
    }

}
