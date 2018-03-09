<?php
namespace Home\Controller;
use \Org\Util\WeixinJssdk;

class OrderController extends CommonController
{
    /**
     * [index 订单首页]
     * @return [type] [description]
     */
    public function index()
    {   
        $weixin = new WeixinJssdk;
        $signPackage = $weixin->getSignPackage();
        //分配数据        
        $this->assign('info',$signPackage);
        $this->assign('openId',$openId);

    	$this->display();
    }

    /**
     * [waitpaylist 查询订单列表]
     * @return [type] [description]
     */
    public function order_list()
    {
        if(IS_POST){
            // 接收请求数据
            // 订单类型：{0：租赁订单，1：商品订单，2：耗材订单，3：兑换订单}
            $g_type = I('post.g_type');
            // 8：未支付，9：待发货，2：待收货，7：待评论 4：退款处理中 5：已退货
            $status = I('post.status');
            
            // 查询用户
            $showData['uid']     = $_SESSION['user']['id'];
            // 订单类型
            $showData['g_type'] = $g_type;
            // 订单状态
            $showData['status'] = $status;
            // 查询订单表
            $arrList = M('shop_order')->where($showData)->select();
            // 未支付订单
            $waitpaylist = [];
            $i = 0;
            foreach ($arrList as $key => $value) {
                // 订单编号
                $waitpaylist[$i]['orderid']     = $value['order_id'];
                // 订单时间
                $waitpaylist[$i]['ordertime']   = date("Y-m-d",$value['addtime']);
                // 订单总数量
                $waitpaylist[$i]['totalnumber'] = $value['g_num'];
                // 订单总金额
                $waitpaylist[$i]['totalprice']  = $value['g_price'];
                // 订单快递费
                $waitpaylist[$i]['postage']     = mt_rand(5,22);
                // 快递名称
                $waitpaylist[$i]['express_name']     = $value['express_name'];
                // 快递名称
                $waitpaylist[$i]['express']     = $value['express'];


                // 订单详情表
                $dWhere = array('d.order_id'=>$value['order_id']);
                // 订单详情
                $waitpaylist[$i]['productList'] = M('order_detail')
                                                    ->alias('d')
                                                    ->where($dWhere)
                                                    ->join('__GOODS__ g ON g.id = d.gid','LEFT')
                                                    ->join('__GOODS_DETAIL__ g_d ON g.id = g_d.gid','LEFT')
                                                    ->join('__PIC__ p ON g.id = p.gid','LEFT')
                                                    ->field(array('p.path'=>'orderimg','g.name'=>'productname','g.desc'=>'productbrief','d.gid','d.price'=>'price','d.num'=>'productnumber','g_d.is_install'=>'is_install','g_d.is_hire'=>'is_hire'))
                                                    ->select();
                $i++;
            }

            // 返回数据
            if(empty($waitpaylist)){
                $message    = ['code' => 403, 'message' => '暂无订单!'];    
            }else{
                $message    = ['code' => 200, 'message' => '订单查询成功!','data'=>$waitpaylist];
            }
            // echo '<pre>';                         
            // print_r($showData);
            // 返回JSON格式数据
            $this->ajaxReturn($message); 
        }


    }

    public function exchange_record()
    {
        // 用户ID
        $showData['user_id'] = session('user.id');
        // 启用状态
        $showData['status']  = 0;
        // 查询用户兑换记录
        $data = M('exchange_record')->where($showData)->select();
    
        if($data){
            $message    = ['code' => 200, 'message' => '兑换记录查询成功!' , 'data' => $data];
        }else{
            $message    = ['code' => 403, 'message' => '暂无兑换记录!'];
        }

        // 返回JSON格式数据
        $this->ajaxReturn($message); 
    }

    /**
     * [cancellation_of_order 取消订单]
     * @return [type] [description]
     */
    public function cancellation_of_order()
    {
        if(IS_POST){
            // 要取消的订单编号
            $whrerData['order_id'] = I('post.order_id');
            // 修改订单状态
            $saveData['status'] = 1;
            // 执行修改
            $res = M('shop_order')->where($whrerData)->save($saveData);
            // 判断修改是否成功
            if($res){
                // 成功返回
                $message    = ['code' => 200, 'message' => '订单取消成功!'];
            }else{
                // 失败返回
                $message    = ['code' => 403, 'message' => '订单取消失败!']; 
            }

            // 返回JSON格式数据
            $this->ajaxReturn($message);
        }
    }

    public function editOrder()
    {
        try {
            $map = I('post.');
            $data['status'] = 3;
            M('shop_order')->where($map)->save($data);
        } catch (\Exception $e) {
            $err = [
                'code' => $e->getCode(),
                'msg' => $e->getMessage(),
            ];
            $this->ajaxReturn($err);
        }
    }

    /**
     * 我的订单售后报装列表
     * @return [type] [description]
     */
    public function install()
    {
        // 'select * from st_shop_order so LEFT join st_order_detail od ON od.order_id = so.order_id LEFT JOIN st_pic pic ON od.gid = pic.gid LEFT JOIN st_goods g ON od.gid = g.id LEFT JOIN st_goods_detail gd ON od.gid = gd.gid where so.uid = 32 AND gd.is_install = 1'
        // if (IS_AJAX) {
            $uid = $_SESSION['user']['id'];
            $data = D('shop_order')->alias('so')
                                ->where(['so.uid'=>$uid,'gd.is_install'=>1])
                                ->join('st_order_detail od ON od.order_id = so.order_id','LEFT')
                                ->join('st_pic pic ON od.gid = pic.gid','LEFT')
                                ->join('st_goods g ON od.gid = g.id','LEFT')
                                ->join('st_goods_detail gd ON od.gid = gd.gid','LEFT')
                                ->field(array('so.uid','so.order_id','od.gid','od.num','od.price','g.name'=>'goodname','pic.path'=>'picpath','so.addtime','od.is_installed'))
                                ->select();
            // 判断售后商品是否已经评论过了，然后给个标识状态
            $install_comment = D('install_comment');
            foreach ($data as $key => $value) {
                if ($install_comment->where(['gid'=>$value['gid'],'orderid'=>$value['order_id']])->find()) {
                    $data[$key]['is_comment'] = 1;
                } else {
                    $data[$key]['is_comment'] = 0;
                }
            }
            
            if ($data) {
                return $this->ajaxReturn(['code'=>200,'data'=>$data]);
            }
                return $this->ajaxReturn(['code'=>400,'msg'=>'没有数据','uid'=>$uid]);
        // }       
    }

    /**
     * 报装评论
     * @return [type] [description]
     */
    public function installComment()
    {
        if (empty(I('post.orderid')) && empty(I('post.gid'))) {
            return $this->ajaxReturn(['code'=>400,'msg'=>'参数错误']);
        }
        $data = [
            'orderid' => I('post.orderid'),
            'gid'     => I('post.gid'),
            'content' => I('post.appraise'),
            'installer_graded' => I('post.grade1'),
            'attitude' => I('post.grade2'),
            'dressing' => I('post.grade3'),
            'create_at' => time()
        ];
        $install_comment = D('install_comment');
         // 判断售后商品是否已经评论过了   
        if ($install_comment->where(['gid'=>I('post.gid'),'orderid'=>I('post.orderid')])->find()) {
            return $this->ajaxReturn(['code'=>400,'msg'=>'已经评论过了，不能再评论了']);
        }
        $res = $install_comment->data($data)->add();
        if ($res) {
            return $this->ajaxReturn(['code'=>200,'msg'=>'评论成功']);
        } else {
            return $this->ajaxReturn(['code'=>400,'msg'=>'评论失败']);
        }
    }

    /**
     * [paysuccBtn 提交成功]
     * @return [type] [description]
     */
    public function paysuccBtn()
    {
        $this->display();
    }

    /**
     * [payfailed 提交失败]
     * @return [type] [description]
     */
    public function payfailed()
    {
        $this->display();
    }

    /**
     * [reward 会员没有订单]
     * @return [type] [description]
     */
    public function nomemberOrder()
    {
        $this->display();
    }

    /**
     * [reward 会员订单]
     * @return [type] [description]
     */
    public function memberOrder()
    {
        $this->display();
    }

    /**
     * [applicRefund 退款商品]
     * @return [type] [description]
     */
    public function applicRefund()
    {
        $this->display();
    }

    /**
     * [choiceRefund 选择退款商品]
     * @return [type] [description]
     */
    public function choiceRefund()
    {
                if (IS_AJAX) {
            $orderid = I('orderid');
            // $refund = D('refund_goods')->field('oid,gid')->select(false);
            // echo $refund;die;
            // $data = M('shop_order')->alias('so')->where('order_id='.$orderid)->select();
            $data = M('order_detail')                        
                        ->alias('d')
                        ->where(['d.order_id'=>$orderid,'so.uid'=>$_SESSION['user']['id'],'d.gid'=>['NEQ','rg.gid'],'rg.oid'=>['NEQ',$orderid]])
                        ->join('st_shop_order so ON d.order_id = so.order_id','LEFT')
                        ->join('__GOODS__ g ON g.id = d.gid','LEFT')
                        ->join('__GOODS_DETAIL__ g_d ON g.id = g_d.gid','LEFT')
                        ->join('__PIC__ p ON g.id = p.gid','LEFT')
                        ->join('st_refund_goods rg ON rg.oid = d.order_id','LEFT')
                        // ->table($refund.' a')
                        ->field(array('p.path'=>'orderimg','g.name'=>'productname','g.desc'=>'productbrief','d.gid','d.price'=>'price','d.num'=>'productnumber','g_d.is_install'=>'is_install','g_d.is_hire'=>'is_hire'))
                        ->select();
            if ($data) {
                return $this->ajaxReturn(['code'=>200,'data'=>$data]);
            }
                return $this->ajaxReturn(['code'=>400,'msg'=>'没有数据']);
        } else {
            $this->display();
        }
    }
    
    /**
     * 耗材订单
     * @return [type] [description]
     */
    public function consumable()
    {
        $this->display();
    }
    /**
     * 租赁订单
     * @return [type] [description]
     */
    public function lease()
    {
        $this->display();
    }

    /**
     * 租赁订单
     * @return [type] [description]
     */
    public function exchangeNotes()
    {
        $this->display();
    }
    //填写物流单号页面
    public function expressOrder(){
        $this->display();
    }

    
}   


