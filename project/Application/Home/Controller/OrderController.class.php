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
        $openId = $_SESSION['open_id'];

        $this->wx_info();

        $this->assign('openId',$openId);

        // $this->display('mynoindent');
        $this->display('');
    }

    public function getOrders($page_index = 1, $page_size = 10, $condition = [], $order = '')
    {
        // IS_AJAX ? : $this->ajaxReturn(['code'=>-1,'msg'=>'非法请求']);
        $map = [];
        isset($condition['g_type']) ? $map['so.g_type'] = $condition['g_type']: '';
        isset($condition['status']) ? $map['so.status'] = array('in',\implode(',',$condition['status'])): '';
        $order = D('ShopOrderDetail');
        $order->alias('od');
        $order->join('st_shop_order so ON so.order_id = od.order_id','LEFT');
        $order->relation(['pics','product']);
        $order->field(array(
            'od.order_id','od.gid','od.num','od.price','od.cprice','so.status','od.addtime'
        ));
        $order->page($page_index,$page_size);
        $order->where($map);
        $order->order('od.addtime desc');
        $data = $order->select();
        $this->ajaxReturn(['code'=>200,'msg'=>empty($data) ? '暂无数据':'查询成功','data'=>$data,'g_type'=>$map]);
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
            $sta =implode(',',$status);
            // 查询用户

            $_SESSION['user']['id'];

            // $_SESSION['user']['id'] = 27;
 
            $uid  = $_SESSION['user']['id'];
            // 订单类型
            $showData['g_type'] = $g_type;
            // 订单状态
            $showData['status'] = $status;
            // 查询订单表
            $arrList = M('shop_order')->where(['uid'=>$uid,'g_type'=>$g_type,'status'=>array('in',$sta)])->select();

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
                switch($g_type){
                    case 0;
                        break;
                    case 1;
                        $waitpaylist[$i]['productList'] = D('shop_order_detail')
                        ->alias('d')
                        ->where($dWhere)
                        ->join('__GOODS__ g ON g.id = d.gid','LEFT')
                        ->join('__GOODS_DETAIL__ g_d ON g.id = g_d.gid','LEFT')
                        // ->join('__PIC__ p ON g.id = p.gid','LEFT')
                        ->field(array('g.name'=>'productname','g.desc'=>'productbrief','d.gid','d.price'=>'price','d.num'=>'productnumber','d.express_name','d.express','d.status','g_d.is_install'=>'is_install','g_d.is_hire'=>'is_hire','d.cprice','d.gid'=>'id','d.gpic'))
                        // ->relation('pics')
                        ->select();


                        break;
                    case 2;
                                            // 订单详情
                        $waitpaylist[$i]['productList'] = M('shop_order_detail')
                        ->alias('d')
                        ->where($dWhere)
                        ->join('__FILTERS__ f ON f.id = d.gid','LEFT')
                        ->field(array('f.picpath'=>'orderimg','f.filtername'=>'productname','f.introduce'=>'productbrief','d.gid','d.price'=>'price','d.num'=>'productnumber','d.cprice','d.express_name','d.express'))
                        ->select();
                        break;
                    case 3;
                        break;
                }

                $i++;
            }
            

            foreach ($waitpaylist as $key => $value) {
                //先处理数据再给前端分配数据
                
                foreach ($value['productList'] as $k => $v) {
        
                    //查找退货的商品id
                    $refund_goods =  M('refund_goods')->where('oid='."'{$value['orderid']}'")->select();
                    foreach ($refund_goods as $rd => $rdvalue) {
                        $rids[] = $rdvalue['gid'];
                    }
                    //id去重
                    $ids = array_unique($rids);

                    // dump($ids);
                    if (in_array($v['gid'],$ids)) {
                        // dump();
                        $waitpaylist[$key]['productList'][$k]['tuihuo']='1';
                    } 
                }

            }
            // dump($waitpaylist);

            // 返回数据
            if(empty($waitpaylist)){
                $message    = ['code' => 403, 'message' => '暂无订单!'];    
            }else{
                $message    = ['code' => 200, 'message' => '订单查询成功!','data'=>$waitpaylist];
            }
            // echo '<pre>';                         
            // print_r($message);
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

            $map['order_id'] = $map['order_id'];

//            $map['order_id'] = '661320342352019';
            $data['status'] = 3;
//            $data['addtime'] = '12';
            $info = M('shop_order')->where($map)->save($data);
//            $info = M('shop_order')->where($map)->find($data);




            if ($info) {
//                  $list = M('ShopOrder as a')->field('a.id,a.order_id,a.gid,a.g_cost,a.g_price,a.g_num,b.vid,c.id ccid,c.code,c.invitation_code,c.superiors_code,c.superior_code,c.abonus,b.name')->join
//                  ('st_goods b on a.gid = b.id')->join('st_vendors c on b.vid = c.id')->where(['a
//                .g_type'=>1,'a.status'=>3,'a.order_id'=>$map['order_id']])->find();
//                $list = M('ShopOrder as a')->field('a.id,a.order_id,a.gid,a.g_cost,a.g_price,a.g_num,b.name,d.invitation_code,d.id,d.open_id')
//                   ->join('st_users d on d.id = a.uid')->join
//                  ('st_goods b on a.gid = b.id')->where(['a
//                .g_type'=>1,'a.status'=>3,'a.order_id'=>$map['order_id']])->find();
                $list = M('ShopOrder as a')->field('a.id,a.order_id,a.gid,a.uid,a.g_cost,a.g_price,a.g_num,b.name,d.invitation_code,d.id,d.open_id')
                    ->join('st_users d on d.id = a.uid')->join
                    ('st_goods b on a.gid = b.id')->where(['a
                .g_type'=>1,'a.status'=>3,'a.order_id'=>$map['order_id']])->find();
//                dump($list);exit;

                //退商品总额
                $total_amount = 0.00;
                //退商品总成本价
                $cost_nu = 0.00;
                //多个商品
                if($list) {
                    $list['detail'] =  M('shop_order_detail')->where(['order_id'=>$list['order_id']])->select();
                    //查找是否有退款
                    $refund_goods = M('refund_goods')->where(['oid'=>$list['order_id']])->getField('rf_id');
                    if ($refund_goods) {
                        //寻找成本价
                        $gid = M('refund_goods')->field('gid')->where(['oid'=>$list['order_id']])->select();
                        foreach ($gid as $v) {
                            $shop_order_detail = M('shop_order_detail')->field('cost,num')->where(['order_id'=>$list['order_id'],'gid'=>$v['gid']])->find();
                            $cost_nu  += intval($shop_order_detail['cost']*$shop_order_detail['num']);

                        }


//                        if ($shop_order_detail) {
//                            foreach ($shop_order_detail as $v) {
//                                $cost_nu  += intval($v['cost']);
//                            }
//
//                        }
//                        if ($gid) {
//                            foreach ($gid as $v) {
//                                $goods_cost = M('goods')->field('cost')->where(['id'=>$v['gid']])->find();
//                                //退商品的总成本价
//                                $cost_nu  += intval($goods_cost['cost']);
//                                $a = $list['g_cost']-$cost_nu
//
//                            }
//
//                        }
                        //退款总金额
                       $total_amount = M('refund')->where(['id'=>$refund_goods,['status']=>5])->getField('total_amount');
                    }

                }

                if ($list['invitation_code'] != null) {

                    if ($list['g_price'] < $list['g_cost']) {

                        exit;
                    } else {
                        $money = $list['g_price']-$list['g_cost']-$total_amount+$cost_nu;
//                     dump( $list['g_price']);
//                     dump($list['g_cost']);
//                     dump($total_amount);
//                     dump($cost_nu);exit;
                    }
                    if ($money < 0) {

                        exit;
                    }
                    //查询分配比例
                    $butros = M('butros')->find();
                    //销售奖(定义 卖商品的经销商)
                    $com_c = $money*($butros['com_c']/ 100);
                    //市场推广奖
//                    $com_d = $money*(($butros['com_b']/ 100)*(50/ 100));
                    $com_d = $money*(($butros['com_b']/ 100)*($butros['com_b']/ 100));
                    //市场培育将
//                    $com_p = $money*(($butros['com_b']/100)*((50/100)));
                    $com_p = $money*(($butros['com_b']/100)*(((100-$butros['com_b'])/100)));
                    //团队管理奖 B级加盟商
//                    $com_t =  $money*(($butros['com_a']/ 100)*(50/ 100));
                    $com_t =  $money*(($butros['com_a']/ 100)*($butros['com_b']/ 100));
                    //团队管理奖 A级加盟商
//                    $com_ta =  $money*(($butros['com_a']/100)*((100-50)/100));
                    $com_ta =  $money*(($butros['com_a']/100)*(((100-$butros['com_b']))/100));
                    //查出当前推荐商人
                    $c_info = M('vendors')->where(['code'=>$list['invitation_code'],'status'=>7])->find();
                    //查找所有有直系关系的人
                   $us_path = M('vendors')->where(['open_id'=>$list['open_id']])->getField('path');

                   //查出推荐人的推荐人
//                    $f_info = M('vendors')->where(['code'=>$c_info['office_code']])->find();
//                    dump($com_c);
//                    dump($com_d);
//                    dump($com_p);
//                    dump($com_t);
//                    dump($com_ta);exit;

//                    dump($c_info);exit;
                    
                    //销售奖(卖商品的经销商)
                    if ($c_info) {
//                        $earnings_comc = M('vendors')->where(['id'=>$f_info['id']])->setInc('abonus',$com_c);
                        $earnings_comc = M('vendors')->where(['id'=>$c_info['id']])->setInc('abonus',$com_c);
                        //销售奖收益记录
                        if ($earnings_comc) {
//                            M('earnings')->add(['orderid'=>$list['order_id'],'type'=>1,'opoen_id'=>$list['open_id'],'vid'=>$f_info['id'],'abonus'=>$com_c,'create_time'=>date('Y-m-d H:i:s')]);
                            M('earnings')->add(['orderid'=>$list['order_id'],'type'=>1,'opoen_id'=>$list['open_id'],'vid'=>$c_info['id'],'abonus'=>$com_c,'create_time'=>date('Y-m-d H:i:s'),'content'=>'销售奖']);
                        }
                    }
                    //查找推荐人的推荐人
                    if ($c_info['invitation_code']) {
                        $f_info = M('vendors')->where(['code'=>$c_info['invitation_code'],'status'=>7])->find();

                        //市场推广奖
                        if ($f_info) {
                            $earnings_comd = M('vendors')->where(['id'=>$f_info['id']])->setInc('abonus',$com_d);
                        }
                        //销售奖收益记录
                        if ($earnings_comd) {
//                            M('earnings')->add(['orderid'=>$list['order_id'],'type'=>1,'opoen_id'=>$list['open_id'],'vid'=>$c_info['id'],'abonus'=>$com_d,'create_time'=>date('Y-m-d H:i:s')]);
                            M('earnings')->add(['orderid'=>$list['order_id'],'type'=>1,'opoen_id'=>$list['open_id'],'vid'=>$f_info['id'],'abonus'=>$com_d,'create_time'=>date('Y-m-d H:i:s'),'content'=>'市场推广奖']);
                        }
                    }

//                $earnings_comc = M('vendors')->where(['id'=>$list['ccid']])->setInc('abonus',$com_c);

                    //市场推广奖(定义 卖商品的经销商推荐人) 只查询存在的
//                    if ($c_info){
//
////                        $earnings_comd = M('vendors')->where(['id'=>$c_info['id']])->setInc('abonus',$com_d);
//                        $earnings_comd = M('vendors')->where(['id'=>$f_info['id']])->setInc('abonus',$com_d);
//
//                        //销售奖收益记录
//                        if ($earnings_comd) {
////                            M('earnings')->add(['orderid'=>$list['order_id'],'type'=>1,'opoen_id'=>$list['open_id'],'vid'=>$c_info['id'],'abonus'=>$com_d,'create_time'=>date('Y-m-d H:i:s')]);
//                            M('earnings')->add(['orderid'=>$list['order_id'],'type'=>1,'opoen_id'=>$list['open_id'],'vid'=>$f_info['id'],'abonus'=>$com_d,'create_time'=>date('Y-m-d H:i:s'),'content'=>'市场推广奖']);
//                        }
//                    }
                    //查找直系推荐关系中的最近B级加盟商(包括自己)
//                    $my_level_info = M('vendors')->field('id,leavel,code,path,updatetime')->where(['id'=>$list['ccid']])->find();
                    //如果自己是B级 利润给自己 不是的话 给最近的加盟商呢( 市场培育将)
                    if ($c_info['leavel'] == 3) {

//                         //市场培育奖励
//                    M('vendors')->where(['id'=>$my_level_info['id']])->save(['updatetime'=>time()]);
                        $earnings_comp = M('vendors')->where(['id'=>$c_info['id'],'status'=>7])->setInc('abonus',$com_p);
                        //市场培育收益记录
                        if ($earnings_comp) {
                            M('earnings')->add(['orderid'=>$list['order_id'],'type'=>1,'opoen_id'=>$list['open_id'],'vid'=>$c_info['id'],'abonus'=>$com_p,'create_time'=>date('Y-m-d H:i:s'),'content'=>'市场培育奖']);
                        }

                    } else {
                        if ($us_path != null) {

                            //查找直系推荐关系中的最近B级经销商(包括自己)
                            $path = explode('-',$us_path);
                            $in_B['id']  = array('in',$path);
                            $in_B['status'] = 7;
                            $in_B['leavel'] = 3;
                            //查找最近的经销
                            $my_level_info = M('vendors')->field('id,leavel,code,path,updatetime')->order('id desc')->where($in_B)->find();

                            if ($my_level_info) {
//                            M('vendors')->where(['id'=>$my_level_info['id']])->save(['updatetime'=>time()]);
                                M('vendors')->where(['id'=>$my_level_info['id']])->setInc('abonus',$com_p);
//
                                if ($earnings_comc) {
                                    M('earnings')->add(['orderid'=>$list['order_id'],'type'=>1,'opoen_id'=>$list['open_id'],'vid'=>$my_level_info['id'],'abonus'=>$com_p,'create_time'=>date('Y-m-d H:i:s'),'content'=>'市场培育奖']);
                                }
                            }
                        }
                    }

                    //查找团队管理奖收益人
                    if ($us_path != null) {

                        //查找直系推荐关系中的最近B级经销商(包括自己)
                        $path = explode('-',$us_path);
                        $in_B['id']  = array('in',$path);
                        $in_B['leavel'] = 3;
                        $in_B['status'] = 7;
                        //查找是否有分销有这个奖记录
                        $ea['type_cont'] = 4;
                        $ea['bid'] = $list['uid'];
                        //查找第一次这个用户产生的团队管理奖1是谁
                        $vid =  M('earnings')->where($ea)->order('id asc')->getField('vid');
                        if ($vid) {
                            $earnings_ta = M('vendors')->where(['id'=>$vid])->setInc('abonus',$com_t);
                            //市场培育收益记录
                            if ($earnings_ta) {
                                M('earnings')->add(['orderid'=>$list['order_id'],'bid'=>$list['uid'],'type'=>1,'opoen_id'=>$list['open_id'],'vid'=>$vid,'abonus'=>$com_t,'create_time'=>date
                                ('Y-m-d H:i:s')
                                    ,'content'=>'4']);
                            }
                        } else {
                            $in_info = M('vendors')->where($in_B)->select();
                            unset($in_info[0]);
                            if ($in_info) {
                                $earnings_ta = M('vendors')->where(['id'=>$in_info[1]['id']])->setInc('abonus',$com_t);
                                if ($earnings_ta) {
                                    M('earnings')->add(['orderid'=>$list['order_id'],'bid'=>$list['uid'],'type'=>1,'opoen_id'=>$list['open_id'],'vid'=>$in_info[1]['id'],'abonus'=>$com_t,'create_time'=>date
                                    ('Y-m-d H:i:s')
                                        ,'type_cont'=>'4']);
                                }
                            }
                        }
                    }
                    //B级加盟商受益人
                    //如果自己是A级 利润给自己 不是的话 给最近的加盟商呢
                    if ($c_info['leavel'] == 2) {
                        //团队管理奖 A级加盟商
                        M('vendors')->where(['id'=>$c_info['id']])->save(['updatetime'=>time()]);
                        $earnings_ta = M('vendors')->where(['id'=>$c_info['id']])->setInc('abonus',$com_ta);

                        //市场培育收益记录
                        if ($earnings_ta) {
                            M('earnings')->add(['orderid'=>$list['order_id'],'type'=>1,'opoen_id'=>$list['open_id'],'vid'=>$c_info['id'],'abonus'=>$com_ta,'create_time'=>date('Y-m-d H:i:s'),'content'=>'团队管理奖2']);
                        }

                    } else {
                        if ($us_path != null) {

                            //查找直系推荐关系中的最近B级加盟商(包括自己)
                            $path = explode('-',$us_path);
                            $in_A['id']  = array('in',$path);
                            $in_A['leavel'] = 2;
                            $in_A['status'] = 7;
                            $path_info_A = M('vendors')->field('id,leavel,code,path')->order('id desc')->where($in_A)->find();

                            if ($path_info_A) {
//                            M('vendors')->where(['id'=>$path_info_A['id']])->save(['updatetime'=>time()]);
                                $earnings_ta = M('vendors')->where(['id'=>$path_info_A['id']])->setInc('abonus',$com_ta);
                                if ($earnings_ta) {
                                    M('earnings')->add(['orderid'=>$list['order_id'],'type'=>1,'opoen_id'=>$list['open_id'],'vid'=>$path_info_A['id'],'abonus'=>$com_ta,'create_time'=>date('Y-m-d H:i:s'),'content'=>'团队管理奖2']);
                                }
                            }
                        }
                    }
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
                                ->join('st_shop_order_detail od ON od.order_id = so.order_id','LEFT')
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
            $order = D('shopOrder')->where(['order_id'=>$orderid])->field('g_type')->find();
            switch ($order['g_type']) {
                case 0:
                    # code...
                    break;
                
                case 1:
                        $subsql = D('refund_goods')->where(['oid'=>$orderid])->field('gid')->select(false);
                        $data = D('shop_order_detail')
                        ->alias('d')
                        // ->where(['d.order_id'=>$orderid,'so.uid'=>$_SESSION['user']['id'],'d.gid'=>['NEQ','rg.gid'],'rg.oid'=>['NEQ',$orderid]])
                        ->where(['d.order_id'=>$orderid,'so.uid'=>$_SESSION['user']['id'],'g.id'=>['exp',"NOT IN ($subsql)"]])
                        ->join('st_shop_order so ON d.order_id = so.order_id','LEFT')
                        ->join('__GOODS__ g ON g.id = d.gid','LEFT')
                        ->join('__GOODS_DETAIL__ g_d ON g.id = g_d.gid','LEFT')
                        // ->join('__PIC__ p ON g.id = p.gid','LEFT')
                        // ->relation(['pics'])
                        // // ->table($refund.' a')
                        ->field(array('g.name'=>'productname','g.desc'=>'productbrief','d.gid','d.price'=>'price','d.num'=>'productnumber','g_d.is_install'=>'is_install','g_d.is_hire'=>'is_hire','d.gpic'=>'gpic'))
                        ->select();
                    break;

                case 2:
                        $subsql = D('refund_goods')->where(['oid'=>$orderid])->field('gid')->select(false);
                        $data = M('shop_order_detail')
                        ->alias('d')
                        // ->where(['d.order_id'=>$orderid,'so.uid'=>$_SESSION['user']['id'],'d.gid'=>['NEQ','rg.gid'],'rg.oid'=>['NEQ',$orderid]])
                        ->where(['d.order_id'=>$orderid,'so.uid'=>$_SESSION['user']['id'],'f.id'=>['exp',"NOT IN ($subsql)"]])
                        ->join('st_shop_order so ON d.order_id = so.order_id','LEFT')
                        ->join('__FILTERS__ f ON f.id = d.gid','LEFT')
                        // // ->table($refund.' a')
                        ->field(array('f.picpath'=>'orderimg','f.filtername'=>'productname','f.introduce'=>'productbrief','d.gid','d.price'=>'price','d.num'=>'productnumber'))
                        ->select();
                    break;

                case 3:
                    # code...
                    break;
                default:
                    # code...
                    break;
            }

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
        $this->wx_info();
        $this->display();
    }
    /**
     * 租赁订单
     * @return [type] [description]
     */
    public function lease()
    {
        $this->wx_info();
        $this->display();
    }

    /**
     * 租赁订单
     * @return [type] [description]
     */
    public function exchangeNotes()
    {
        $this->wx_info();
        $this->display();
    }
    //填写物流单号页面
    public function expressOrder()
    {
        $this->wx_info();
        $this->display();
    }

    
}   


