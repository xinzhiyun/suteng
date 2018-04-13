<?php
namespace Admin\Controller;
use Think\Controller;

class ButrosController extends CommonController
{
    /*
         * 新版分销策略设置
         */
    public function strategy()
    {
        $info = M('butros')->field('id,trader_a,trader_b,com_a,com_b,com_c,com_d')->find();

        if (IS_POST) {
            $data = I('post.');

            // 实例化佣金模型
            $butors = D('butros');

            $num = $data['com_a']+$data['com_b']+$data['com_c'];
            if ($num < 100) {
                $message = ['code' => 403, 'message' =>'分配比例不能小于100'];
            }
             //创建数据
            $create= $butors->create();
            if ($create ) {
                if ($data['id'] == 0) {
                    $data['create_time'] = date('Y-m-d H:i:s');
                    $info= $butors->add($data);
                    if ($info) {
                        $message = ['code' => 200, 'message' =>'分配成功'];
                    } else {
                        $message = ['code' => 403, 'message' =>'分配失败'];
                    }
                }
                if ($data['id'] == 1) {

                    if ($data == $info) {
                        $message = ['code' => 403, 'message' =>'无需更新'];
                    }
                    $info = $butors->where(['id'=>1])->save($data);
                    if ($info) {
                        $message = ['code' => 200, 'message' =>'分配成功'];
                    } else {
                        $message = ['code' => 403, 'message' =>'无需更新'];
                    }
                }

            } else {

                // 表单验证错误
                $message = ['code' => 403, 'message' => $butors->getError()];
            }
            // 返回JSON格式数据
            $this->ajaxReturn($message);
        }
        $this->assign('data',$info);
        $this->display();
    }
     /*
      * 商品利润分配
      */
     public function profits () {

         //商品销售佣金
        $list = M('ShopOrder as a')->field('a.id,a.order_id,a.gid,a.g_cost,a.g_price,a.g_num,b.vid,c.id ccid,c.code,c.invitation_code,c.superiors_code,c.superior_code,c.abonus')->join('st_goods b on a.gid = b.id')->join('st_vendors c on b.vid = c.id')->where(['a
         .g_type'=>1,'a.status'=>7])->select();

         //查询分配比例
        $butros = M('butros')->find();

        foreach ($list as $k=>$v) {
             //这句判断是防止销售总额低于成本
            if ($v['g_price'] < $v['g_cost']) {
                $money = 0;
            } else {
                $money = $v['g_price']-$v['g_cost'];
            }
             //销售奖(定义 卖商品的经销商)
            $com_c = $money*($butros['com_c']/ 100);
             //市场推广奖
            $com_d = $money*(($butros['com_b']/ 100)*(50/ 100));
             //市场培育将
            $com_p = $money*(($butros['com_b']/100)*((50/100)));
//                     //团队管理奖 B级加盟商
            $com_t =  $money*(($butros['com_a']/ 100)*($butros['com_d']/ 100));
             //团队管理奖 A级加盟商
            $com_ta =  $money*(($butros['com_a']/100)*((50/100)));

             //销售奖(卖商品的经销商)
            M('vendors')->where(['id'=>$v['ccid']])->setInc('abonus',$com_c);

            //市场推广奖(定义 卖商品的经销商推荐人) 只查询存在的
            if ($v['invitation_code']!=null){
                $info = M('vendors')->where(['code'=>$v['invitation_code']])->setInc('abonus',$com_d);
            }

            //查找直系推荐关系中的最近B级加盟商(包括自己)
            $my_level_info = M('vendors')->field('id,leavel,code,path')->where(['id'=>$v['ccid']])->find();
            //如果自己是B级 利润给自己 不是的话 给最近的加盟商呢( 市场培育将)
            if ($my_level_info['leavel'] == 3) {
//                         //市场培育奖励
                M('vendors')->where(['id'=>$my_level_info['id']])->save(['updatetime'=>time()]);
                M('vendors')->where(['id'=>$my_level_info['id']])->setInc('abonus',$com_p);

            } else {
                if ($my_level_info['path'] != null) {

                    //查找直系推荐关系中的最近B级经销商(包括自己)
                    $path = explode('-',$my_level_info['path']);
                    $in_B['id']  = array('in',$path);
                    $in_B['leavel'] = 3;
                    //查找最近的B级经销商
                    $path_info_B = M('vendors')->field('id,leavel,code,path')->order('id desc')->where($in_B)->find();
                    if ($path_info_B) {
                        M('vendors')->where(['id'=>$path_info_B['id']])->save(['updatetime'=>time()]);
                        M('vendors')->where(['id'=>$path_info_B['id']])->setInc('abonus',$com_p);
//                        dump($my_level_info['id']);
                    }
                }
            }
            //如果自己是A级 利润给自己 不是的话 给最近的加盟商呢
            if ($my_level_info['leavel'] == 2) {
                //团队管理奖 A级加盟商
                M('vendors')->where(['id'=>$my_level_info['id']])->save(['updatetime'=>time()]);
                M('vendors')->where(['id'=>$my_level_info['id']])->setInc('abonus',$com_ta);

            } else {
                if ($my_level_info['path'] != null) {
                    //查找直系推荐关系中的最近B级加盟商(包括自己)
                    $path = explode('-',$my_level_info['path']);
                    $in_A['id']  = array('in',$path);
                    $in_A['leavel'] = 2;
                    $path_info_A = M('vendors')->field('id,leavel,code,path')->order('id desc')->where($in_A)->find();

                    if ($path_info_A) {
                        M('vendors')->where(['id'=>$path_info_A['id']])->save(['updatetime'=>time()]);
                        M('vendors')->where(['id'=>$path_info_A['id']])->setInc('abonus',$com_ta);
//                        dump($com_ta);
                        dump($my_level_info['id']);
                    }
                }
            }
         }
     }
    //租金利润分配  微信成功支付后 回调 拿到订单号
    public function rent() {
        $showWhere['order_id'] =  989074887972580;
        $showWhere['status'] = 1;
        // // 查询订单表
        $orderData = M('order_setmeal')->where($showWhere)->find();


        if ($orderData) {
            //查询套餐表的这条数据
            $setmeal = M('setmeal')->where(['id'=>$orderData['setmeal_id']])->find();

            if ($setmeal) {
//               $money = 0;//租金成本
//               $time_money = 0;//单位时间成本
                //租金计算公式 租金总价-（滤芯+单位时间*时间）分配的总佣金
                $money = $orderData['goods_price']-($setmeal['ren_money']+$setmeal['time_out']*$setmeal['flow']);
                if ($money > 0 ) {
                    //查询推荐人 也就是所谓的经销商
                   $user_info = M('users')->field('invitation_code,id,invite')->where(['id'=>$orderData['uid']])->find();

                   if ($user_info['invitation_code'] != null && $user_info['invite'] < 4) {
                       //查询分配比例
                       $butros = M('butros')->find();
                       $my_level_info = M('vendors')->field('id,leavel,code,path,invitation_code')->where(['code'=>$user_info['invitation_code']])->find();

                       //销售奖(定义 卖商品的经销商)
                       $com_c = $money*($butros['com_c']/ 100);
                       //市场推广奖
                       $com_d = $money*(($butros['com_b']/ 100)*(50/ 100));
                       //市场培育将
                       $com_p = $money*(($butros['com_b']/100)*((50/100)));
//                     //团队管理奖 B级加盟商
                       $com_t =  $money*(($butros['com_a']/ 100)*($butros['com_d']/ 100));
                       //团队管理奖 A级加盟商
                       $com_ta =  $money*(($butros['com_a']/100)*((100-$butros['com_d'])/100));
//                       dump($com_c);   dump($com_d);   dump($com_p);   dump($com_t);   dump($com_ta);exit;
                        //销售奖(给用户的推荐人)
                       M('vendors')->where(['code'=>$user_info['invitation_code']])->setInc('abonus',$com_c);
                       if ($my_level_info['invitation_code'] != null) {

                           //市场推广奖(给用户的推荐人的推荐人)
                           M('vendors')->where(['code'=>$my_level_info['invitation_code']])->save(['updatetime'=>time()]);
                           M('vendors')->where(['code'=>$my_level_info['invitation_code']])->setInc('abonus',$com_d);
                       }
                       //如果自己是B级 利润给自己 不是的话 给最近的加盟商呢( 市场培育将)
                       if ($my_level_info['leavel'] == 3) {
//                         //市场培育奖励
                           M('vendors')->where(['id'=>$my_level_info['id']])->save(['updatetime'=>time()]);
                           M('vendors')->where(['id'=>$my_level_info['id']])->setInc('abonus',$com_p);
                       } else {
                           if ($my_level_info['path'] != null) {

                               //查找直系推荐关系中的最近B级经销商(包括自己)
                               $path = explode('-',$my_level_info['path']);
                               $in_B['id']  = array('in',$path);
                               $in_B['leavel'] = 3;
                               //查找最近的B级经销商
                               $path_info_B = M('vendors')->field('id,leavel,code,path')->order('id desc')->where($in_B)->find();
                               dump($path);
                               echo M('vendors')->getLastSql();
                               dump($path_info_B);
                               if ($path_info_B) {
                                   M('vendors')->where(['id'=>$path_info_B['id']])->save(['updatetime'=>time()]);
                                   M('vendors')->where(['id'=>$path_info_B['id']])->setInc('abonus',$com_p);
                               }
                           }
                       }
                       //如果自己是A级 利润给自己 不是的话 给最近的加盟商呢
                       if ($my_level_info['leavel'] == 2) {
                           //团队管理奖 A级加盟商
                           M('vendors')->where(['id'=>$my_level_info['id']])->save(['updatetime'=>time()]);
                           M('vendors')->where(['id'=>$my_level_info['id']])->setInc('abonus',$com_ta);
                       } else {
                           if ($my_level_info['path'] != null) {
                               //查找直系推荐关系中的最近B级加盟商(包括自己)
                               $path = explode('-',$my_level_info['path']);
                               $in_A['id']  = array('in',$path);
                               $in_A['leavel'] = 2;
                               $path_info_A = M('vendors')->field('id,leavel,code,path')->order('id desc')->where($in_A)->find();
                               if ($path_info_A) {
                                   M('vendors')->where(['id'=>$path_info_A['id']])->save(['updatetime'=>time()]);
                                   M('vendors')->where(['id'=>$path_info_A['id']])->setInc('abonus',$com_ta);
                               }
                           }
                       }
                   }

                }
            }
        }

    }
    // 年费佣金分配
    public function annual_money() {

         //查询用户的推荐人
        $where['id'] = 50;
        $user_info = M('users')->field('invitation_code,id,invite')->where($where)->find();
        if ($user_info) {
           $annual_info =  M('annual')->find();
           $money = $annual_info['annual_money'] - $annual_info['cost_money'];
            if ($money > 0 ) {
                //查询推荐人 也就是所谓的经销商


                if ($user_info['invitation_code'] != null && $user_info['invite'] < 4) {

                    //查询分配比例
                    $butros = M('butros')->find();
                    $my_level_info = M('vendors')->field('id,leavel,code,path,invitation_code')->where(['code'=>$user_info['invitation_code']])->find();

                    //销售奖(定义 卖商品的经销商)
                    $com_c = $money*($butros['com_c']/ 100);
                    //市场推广奖
                    $com_d = $money*(($butros['com_b']/ 100)*(50/ 100));
                    //市场培育将
                    $com_p = $money*(($butros['com_b']/100)*((50/100)));
//                     //团队管理奖 B级加盟商
                    $com_t =  $money*(($butros['com_a']/ 100)*($butros['com_d']/ 100));
                    //团队管理奖 A级加盟商
                    $com_ta =  $money*(($butros['com_a']/100)*((100-$butros['com_d'])/100));
//                       dump($com_c);   dump($com_d);   dump($com_p);   dump($com_t);   dump($com_ta);exit;
                    //销售奖(给用户的推荐人)
                    M('vendors')->where(['code'=>$user_info['invitation_code']])->setInc('abonus',$com_c);
                    if ($my_level_info['invitation_code'] != null) {

                        //市场推广奖(给用户的推荐人的推荐人)
                        M('vendors')->where(['code'=>$my_level_info['invitation_code']])->save(['updatetime'=>time()]);
                        M('vendors')->where(['code'=>$my_level_info['invitation_code']])->setInc('abonus',$com_d);
                    }
                    //如果自己是B级 利润给自己 不是的话 给最近的加盟商呢( 市场培育将)
                    if ($my_level_info['leavel'] == 3) {
//                         //市场培育奖励
                        M('vendors')->where(['id'=>$my_level_info['id']])->save(['updatetime'=>time()]);
                        M('vendors')->where(['id'=>$my_level_info['id']])->setInc('abonus',$com_p);
                    } else {
                        if ($my_level_info['path'] != null) {

                            //查找直系推荐关系中的最近B级经销商(包括自己)
                            $path = explode('-',$my_level_info['path']);
                            $in_B['id']  = array('in',$path);
                            $in_B['leavel'] = 3;
                            //查找最近的B级经销商
                            $path_info_B = M('vendors')->field('id,leavel,code,path')->order('id desc')->where($in_B)->find();
                            dump($path);
                            echo M('vendors')->getLastSql();
                            dump($path_info_B);
                            if ($path_info_B) {
                                M('vendors')->where(['id'=>$path_info_B['id']])->save(['updatetime'=>time()]);
                                M('vendors')->where(['id'=>$path_info_B['id']])->setInc('abonus',$com_p);
                            }
                        }
                    }
                    //如果自己是A级 利润给自己 不是的话 给最近的加盟商呢
                    if ($my_level_info['leavel'] == 2) {
                        //团队管理奖 A级加盟商
                        M('vendors')->where(['id'=>$my_level_info['id']])->save(['updatetime'=>time()]);
                        M('vendors')->where(['id'=>$my_level_info['id']])->setInc('abonus',$com_ta);
                    } else {
                        if ($my_level_info['path'] != null) {
                            //查找直系推荐关系中的最近B级加盟商(包括自己)
                            $path = explode('-',$my_level_info['path']);
                            $in_A['id']  = array('in',$path);
                            $in_A['leavel'] = 2;
                            $path_info_A = M('vendors')->field('id,leavel,code,path')->order('id desc')->where($in_A)->find();
                            if ($path_info_A) {
                                M('vendors')->where(['id'=>$path_info_A['id']])->save(['updatetime'=>time()]);
                                M('vendors')->where(['id'=>$path_info_A['id']])->setInc('abonus',$com_ta);
                            }
                        }
                    }
                }

            }
        }
    }
     //年费设置
    public function annual()
    {

        $info = M('annual')->find();

        if (IS_POST) {
            header("Content-type: text/html; charset=utf-8");
            $data = I('post.');
            $data['create_time'] = date('Y-m-d H:i:s');
           dump(urldecode($data['per_describe']));exit;
//            dump( urldecode(iconv("gb2312", "UTF-8", $data['per_describe'])));exit;
            if (intval($data['annual_money']) < intval($data['cost_money'])) {
                $message = ['code' => 403, 'message' =>'年费不能小于成本价'];
            }
            if ($data['id'] == '') {
                $info =  M('annual')->add($data);//
                if ($info) {
                    $message = ['code' => 200, 'message' =>'设置成功'];
                } else {
                    $message = ['code' => 403, 'message' =>'设置失败'];
                }
            }
            if ($data['id'] == 1) {
                $info =  M('annual')->where(['id'=>$data['id']])->save($data);//
                if ($info) {
                    $message = ['code' => 200, 'message' =>'修改成功'];
                } else {
                    $message = ['code' => 403, 'message' =>'修改失败'];
                }
            }

            $this->ajaxReturn($message);
        } else {
            $this->assign('data',$info);
            $this->display();
        }

    }
}