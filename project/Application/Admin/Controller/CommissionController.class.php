<?php
namespace Admin\Controller;
use Think\Controller;

class CommissionController extends CommonController
{
    public function index()
    {
        $this->display();
    }

    /**
     * [commission 佣金设置]
     * @return [type] [description]
     */
    public function commission_set()
    {
        // 准备查询条件
        $showData['id'] = 1;
        // 查询佣金分配比例
        $data = M('commission')->where($showData)->find();
        // 分配数据
        $this->assign('data',$data);
        // 显示模板
        $this->display();
    }

    /**
     * [commission 佣金按比例更新]
     * @return [type] [description]
     */
    public function commission_proportion()
    {
        if(IS_POST){
            // 实例化佣金模型
            $commission = D('commission');
            // 创建数据
            $data = $commission->create();
            // 判断数据是否创建成功
            if($data){
                $user_y         = $data['user_y'];  
                $inviter_y      = $data['inviter_y'];
                $user_j         = $data['user_j'];  
                $inviter_j      = $data['inviter_j'];
                $vendor_a       = $data['vendor_a'];
                $vendor_b       = $data['vendor_b'];
                $vendor_c       = $data['vendor_c'];
                $vendor_i       = $data['vendor_i'];

                // 设置状态
                $status = true;

                // 准备查询条件
                $showData['id'] = 1;
                // 查询佣金分配比例
                $showDommissionData = $commission->where($showData)->field('user_y,inviter_y,user_j,inviter_j,vendor_a,vendor_b,vendor_c,vendor_i')->find();

                if($data==$showDommissionData){
                    $message = ['code' => 403, 'message' =>'您没有修改佣金分配比例！'];
                }else{
                    // 佣金比例和网站币比例
                    $abonus = $glob_y = $glob_j = 100;
                    // 银币比例
                    $inputGlob_y = $user_y+$inviter_y;
                    // 判断银币比例设置
                    if($glob_y != $inputGlob_y){
                        $message = ['code' => 403, 'message' =>'会员和会员邀请人，银币分配比例不正确'];
                        // 修改状态
                        $status = false;
                    }

                    // 金币比例
                    $inputGlob_j = $user_j+$inviter_j;
                    // 判断金币比例设置
                    if($glob_j != $inputGlob_j){
                        $message = ['code' => 403, 'message' =>'会员和会员邀请人，金币分配比例不正确'];
                        // 修改状态
                        $status = false;
                    }

                    // 佣金比例
                    $inputAbonus = $vendor_a+$vendor_b+$vendor_c+$vendor_i;
                    // 判断佣金比例设置
                    if($abonus != $inputAbonus){
                        $message = ['code' => 403, 'message' =>'分销商和分销商邀请人，佣金分配比例不正确'];
                        // 修改状态
                        $status = false;
                    }

                    if($status){
                        // 准备修改条件
                        $saveData['id'] = 1;
                        // 加入更新时间
                        $data['updatetime'] = time();
                        // 执行修改
                        $res = $commission->where($saveData)->save($data);
                        // 判断是否修改成功
                        if($res){
                            $message = ['code' => 200, 'message' => '佣金比例添加成功'];
                        }else{
                            $message = ['code' => 403, 'message' => '佣金比例添加失败'];
                        }
                    }
                }

            }else{
                // 表单验证错误
                $message = ['code' => 403, 'message' => $commission->getError()];
            }
            // 返回JSON格式数据
            $this->ajaxReturn($message);        
        }

    }


    /**
     * [branch_commission description]
     * @param  [type] $open_id  [用户微信唯一标识]
     * @param  [type] $order_id [订单号]
     * @param  [type] $yj       [佣金总额]
     * @param  [type] $jb       [金币总额]
     * @param  [type] $yb       [银币总额]
     */
    public function branch_commission($open_id,$order_id,$yj,$jb,$yb)
    {
        // 准备查询条件
        $showData['id'] = 1;
        // 查询佣金分配比例
        $commission = M('commission')->where($showData)->find();
        // 准备用户查询条件
        $showUserData['open_id'] = $open_id;
        // 查询用户信息
        $user = M('users')->where($showUserData)->find();

        // 会员邀请人
        $invitation_code    =  $user['invitation_code'];
        // A级分销商
        $vendora_code       =  $user['vendora_code'];
        // B级分销商
        $vendorb_code       =  $user['vendorb_code'];
        // C级分销商
        $vendorc_code       =  $user['vendorc_code'];
        // 分销商邀请人
        $vendori_code       =  $user['vendori_code'];
        // 邀请人类型
        $invite             =  $user['invite'];

        switch ($invite) {
            case '0':
                // 分公司邀请的会员
                // 会员获得金币和银币
                $this->user_commission($user,$commission,$order_id,$jb,$yb);
                break;
            case '1':
                // A级分销商邀请的会员
                // 会员获得金币和银币
                $this->user_commission($user,$commission,$order_id,$jb,$yb);
                // 准备A级分销商查询条件
                $vendoraWhere['code'] = $user['vendora_code'];
                // 查询A级分销商
                $vendora = M('vendors')->where($vendoraWhere)->find();

                // 佣金比例
                $ratioa = 100;
                // 获得分红的分销商关系人
                $nexus_vendor = $vendora['code'];

                // 如果有分销商邀请人按分成给邀请人分部分佣金
                if(!empty($vendora['invitation_code'])){
                    // 准备分销商邀请人查询条件
                    $vendoriWhere['code'] = $vendora['invitation_code'];
                    // 查询分销商邀请人
                    $vendori = M('vendors')->where($vendoriWhere)->find();
                    // 分销商邀请人佣金比例
                    $ratioi  = $commission['vendor_i'];
                    // 获取佣金的类型[同级邀请的分销商下的会员购买产品]
                    $typei = 3;
                    // 修改A级分销佣金比例
                    $ratioa = $ratioa - $ratioi;
                    // 分配分销商邀请人佣金
                    $this->vendor_commission($vendori,$typei,$vendora,$user,$ratioi,$order_id,$yj);
                }
                // A级分销商获得分红的类型
                $typea = 0;
                // A级分销商获得其余全部佣金
                $this->vendor_commission($vendora,$typea,$vendora,$user,$ratioa,$order_id,$yj);
                break;
            case '2':
                // B级分销商邀请的会员
                // 会员获得金币和银币
                $this->user_commission($user,$commission,$order_id,$jb,$yb);
                // 准备A级分销商查询条件
                $vendorbWhere['code'] = $user['vendorb_code'];
                // 查询A级分销商
                $vendorb = M('vendors')->where($vendorbWhere)->find();

                // 佣金比例
                $ratiob = 100;
                // 获得分红的分销商关系人
                $nexus_vendor = $vendorb['code'];

                // 如果有分销商邀请人按分成给邀请人分部分佣金
                if(!empty($vendorb['invitation_code'])){
                    // 准备分销商邀请人查询条件
                    $vendoriWhere['code'] = $vendorb['invitation_code'];
                    // 查询分销商邀请人
                    $vendori = M('vendors')->where($vendoriWhere)->find();

                    // 分销商邀请人佣金比例
                    $ratioi  = $commission['vendor_i'];
                    // 获取佣金的类型[同级邀请的分销商下的会员购买产品]
                    $typei = 3;
                    // 修改A级分销佣金比例
                    $ratiob = $ratiob - $ratioi;

                    // 分配分销商邀请人佣金
                    $this->vendor_commission($vendori,$typei,$vendorb,$user,$ratioi,$order_id,$yj);
                }

                // 如果有上面分销商A级分销商
                if(!empty($vendorb['superior_code'])){
                    // 准备分销商邀请人查询条件
                    $vendoraWhere['code'] = $vendorb['superior_code'];
                    // 查询分销商邀请人
                    $vendora = M('vendors')->where($vendoraWhere)->find();
                    // 分销商邀请人佣金比例
                    $ratioa  = $commission['vendor_a'];
                    // 获取佣金的类型[下分销商下的会员购买产品]
                    $typea = 1;
                    // 修改A级分销佣金比例
                    $ratiob = $ratiob - $ratioa;
                    // 分配分销商邀请人佣金
                    $this->vendor_commission($vendora,$typea,$vendorb,$user,$ratioa,$order_id,$yj);
                    // dump($ratioa);die;
                }

                // B级分销商获得分红的类型
                $typeb = 0;
                // B级分销商获得其余全部佣金
                $this->vendor_commission($vendorb,$typeb,$vendorb,$user,$ratiob,$order_id,$yj);

                break;
            case '3':
                // C级分销商邀请的会员
                // 会员获得金币和银币
                $this->user_commission($user,$commission,$order_id,$jb,$yb);
                // 准备A级分销商查询条件
                $vendorcWhere['code'] = $user['vendorc_code'];
                // 查询A级分销商
                $vendorc = M('vendors')->where($vendorcWhere)->find();

                // 佣金比例
                $ratioc = 100;
                // 获得分红的分销商关系人
                $nexus_vendor = $vendorc['code'];

                // 如果有分销商邀请人按分成给邀请人分部分佣金
                if(!empty($vendorc['invitation_code'])){
                    // 准备分销商邀请人查询条件
                    $vendoriWhere['code'] = $vendorc['invitation_code'];
                    // 查询分销商邀请人
                    $vendori = M('vendors')->where($vendoriWhere)->find();

                    // 分销商邀请人佣金比例
                    $ratioi  = $commission['vendor_i'];
                    // 获取佣金的类型[同级邀请的分销商下的会员购买产品]
                    $typei = 3;
                    // 修改C级分销佣金比例
                    $ratioc = $ratioc - $ratioi;

                    // 分配分销商邀请人佣金
                    $this->vendor_commission($vendori,$typei,$vendorc,$user,$ratioi,$order_id,$yj);
                }

                // 如果有上面分销商A级分销商
                if(!empty($vendorc['superiors_code'])){
                    // 准备分销商邀请人查询条件
                    $vendoraWhere['code'] = $vendorc['superiors_code'];
                    // 查询分销商邀请人
                    $vendora = M('vendors')->where($vendoraWhere)->find();
                    // 分销商邀请人佣金比例
                    $ratioa  = $commission['vendor_a'];
                    // 获取佣金的类型[下下分销商下的会员购买产品]
                    $typea = 2;
                    // 修改A级分销佣金比例
                    $ratioc = $ratioc - $ratioa;
                    // 分配分销商邀请人佣金
                    $this->vendor_commission($vendora,$typea,$vendorc,$user,$ratioa,$order_id,$yj);
                    // dump($ratioa);die;
                }

                // 如果有上面分销商B级分销商
                if(!empty($vendorc['superior_code'])){
                    // 准备分销商邀请人查询条件
                    $vendorbWhere['code'] = $vendorc['superior_code'];
                    // 查询分销商邀请人
                    $vendorb = M('vendors')->where($vendorbWhere)->find();
                    // 分销商邀请人佣金比例
                    $ratiob  = $commission['vendor_b'];
                    // 获取佣金的类型[下分销商下的会员购买产品]
                    $typea = 1;
                    // 修改A级分销佣金比例
                    $ratioc = $ratioc - $ratiob;
                    // 分配分销商邀请人佣金
                    $this->vendor_commission($vendorb,$typea,$vendorc,$user,$ratiob,$order_id,$yj);
                    // dump($ratioa);die;
                }
                // C级分销商获得分红的类型
                $typec = 0;
                // C级分销商获得其余全部佣金
                $this->vendor_commission($vendorc,$typec,$vendorc,$user,$ratioc,$order_id,$yj);
                break;
            case '4':
                // 会员邀请的会员
                // 会员获得金币和银币
                $this->user_commission($user,$commission,$order_id,$jb,$yb);
                // 准备查询会员邀请的人查询条件
                $showinvitationData['code'] = $user['invitation_code'];
                // 查询用户信息
                $invitation = M('users')->where($showinvitationData)->find();
                // 会员邀请人获得金币和银币
                $this->invitation_commission($invitation,$user,$commission,$order_id,$jb,$yb);
                // 获取邀请人的邀请人级别
                $invitationInvite = $invitation['invite'];
                // 匹配分配商佣金分配
                switch ($invitationInvite) {
                    case '1':
                        // A级分销商下的会员邀请会员
                        // 准备A级分销商查询条件
                        $vendoraWhere['code'] = $user['vendora_code'];
                        // 查询A级分销商
                        $vendora = M('vendors')->where($vendoraWhere)->find();

                        // 佣金比例
                        $ratioa = 100;
                        // 获得分红的分销商关系人
                        $nexus_vendor = $vendora['code'];

                        // 如果有分销商邀请人按分成给邀请人分部分佣金
                        if(!empty($vendora['invitation_code'])){
                            // 准备分销商邀请人查询条件
                            $vendoriWhere['code'] = $vendora['invitation_code'];
                            // 查询分销商邀请人
                            $vendori = M('vendors')->where($vendoriWhere)->find();
                            // 分销商邀请人佣金比例
                            $ratioi  = $commission['vendor_i'];
                            // 获取佣金的类型[同级邀请的分销商下的会员购买产品]
                            $typei = 3;
                            // 修改A级分销佣金比例
                            $ratioa = $ratioa - $ratioi;
                            // 分配分销商邀请人佣金
                            $this->vendor_commission($vendori,$typei,$vendora,$user,$ratioi,$order_id,$yj);
                        }
                        // A级分销商获得分红的类型
                        $typea = 0;
                        // A级分销商获得其余全部佣金
                        $this->vendor_commission($vendora,$typea,$vendora,$user,$ratioa,$order_id,$yj);
                        break;
                    case '2':
                        // B级分销商下的会员邀请会员
                        // 准备B级分销商查询条件
                        $vendorbWhere['code'] = $user['vendorb_code'];
                        // 查询A级分销商
                        $vendorb = M('vendors')->where($vendorbWhere)->find();

                        // 佣金比例
                        $ratiob = 100;
                        // 获得分红的分销商关系人
                        $nexus_vendor = $vendorb['code'];

                        // 如果有分销商邀请人按分成给邀请人分部分佣金
                        if(!empty($vendorb['invitation_code'])){
                            // 准备分销商邀请人查询条件
                            $vendoriWhere['code'] = $vendorb['invitation_code'];
                            // 查询分销商邀请人
                            $vendori = M('vendors')->where($vendoriWhere)->find();

                            // 分销商邀请人佣金比例
                            $ratioi  = $commission['vendor_i'];
                            // 获取佣金的类型[同级邀请的分销商下的会员购买产品]
                            $typei = 3;
                            // 修改A级分销佣金比例
                            $ratiob = $ratiob - $ratioi;

                            // 分配分销商邀请人佣金
                            $this->vendor_commission($vendori,$typei,$vendorb,$user,$ratioi,$order_id,$yj);
                        }

                        // 如果有上面分销商A级分销商
                        if(!empty($vendorb['superior_code'])){
                            // 准备分销商邀请人查询条件
                            $vendoraWhere['code'] = $vendorb['superior_code'];
                            // 查询分销商邀请人
                            $vendora = M('vendors')->where($vendoraWhere)->find();
                            // 分销商邀请人佣金比例
                            $ratioa  = $commission['vendor_a'];
                            // 获取佣金的类型[下分销商下的会员购买产品]
                            $typea = 1;
                            // 修改A级分销佣金比例
                            $ratiob = $ratiob - $ratioa;
                            // 分配分销商邀请人佣金
                            $this->vendor_commission($vendora,$typea,$vendorb,$user,$ratioa,$order_id,$yj);
                            // dump($ratioa);die;
                        }

                        // B级分销商获得分红的类型
                        $typeb = 0;
                        // B级分销商获得其余全部佣金
                        $this->vendor_commission($vendorb,$typeb,$vendorb,$user,$ratiob,$order_id,$yj);
                        break;
                    case '3':
                        // C级分销商下的会员邀请会员
                        $vendorcWhere['code'] = $user['vendorc_code'];
                        // 查询A级分销商
                        $vendorc = M('vendors')->where($vendorcWhere)->find();

                        // 佣金比例
                        $ratioc = 100;
                        // 获得分红的分销商关系人
                        $nexus_vendor = $vendorc['code'];

                        // 如果有分销商邀请人按分成给邀请人分部分佣金
                        if(!empty($vendorc['invitation_code'])){
                            // 准备分销商邀请人查询条件
                            $vendoriWhere['code'] = $vendorc['invitation_code'];
                            // 查询分销商邀请人
                            $vendori = M('vendors')->where($vendoriWhere)->find();

                            // 分销商邀请人佣金比例
                            $ratioi  = $commission['vendor_i'];
                            // 获取佣金的类型[同级邀请的分销商下的会员购买产品]
                            $typei = 3;
                            // 修改C级分销佣金比例
                            $ratioc = $ratioc - $ratioi;

                            // 分配分销商邀请人佣金
                            $this->vendor_commission($vendori,$typei,$vendorc,$user,$ratioi,$order_id,$yj);
                        }

                        // 如果有上面分销商A级分销商
                        if(!empty($vendorc['superiors_code'])){
                            // 准备分销商邀请人查询条件
                            $vendoraWhere['code'] = $vendorc['superiors_code'];
                            // 查询分销商邀请人
                            $vendora = M('vendors')->where($vendoraWhere)->find();
                            // 分销商邀请人佣金比例
                            $ratioa  = $commission['vendor_a'];
                            // 获取佣金的类型[下下分销商下的会员购买产品]
                            $typea = 2;
                            // 修改A级分销佣金比例
                            $ratioc = $ratioc - $ratioa;
                            // 分配分销商邀请人佣金
                            $this->vendor_commission($vendora,$typea,$vendorc,$user,$ratioa,$order_id,$yj);
                            // dump($ratioa);die;
                        }

                        // 如果有上面分销商B级分销商
                        if(!empty($vendorc['superior_code'])){
                            // 准备分销商邀请人查询条件
                            $vendorbWhere['code'] = $vendorc['superior_code'];
                            // 查询分销商邀请人
                            $vendorb = M('vendors')->where($vendorbWhere)->find();
                            // 分销商邀请人佣金比例
                            $ratiob  = $commission['vendor_b'];
                            // 获取佣金的类型[下分销商下的会员购买产品]
                            $typea = 1;
                            // 修改A级分销佣金比例
                            $ratioc = $ratioc - $ratiob;
                            // 分配分销商邀请人佣金
                            $this->vendor_commission($vendorb,$typea,$vendorc,$user,$ratiob,$order_id,$yj);
                            // dump($ratioa);die;
                        }
                        // C级分销商获得分红的类型
                        $typec = 0;
                        // C级分销商获得其余全部佣金
                        $this->vendor_commission($vendorc,$typec,$vendorc,$user,$ratioc,$order_id,$yj);
                        break;
                    default:
                        # code...
                        break;
                }

                break;
            case '5':
                // 会员扫普通二维码注册
                // 会员获得金币和银币
                $this->user_commission($user,$commission,$order_id,$jb,$yb);
                break;
            default:
                break;
        }
    }

    /**
     * [user_commission 会员邀请人金币银币分配]
     * @param  [type] $user       [会员]
     * @param  [type] $invitation [会员邀请人]
     * @param  [type] $commission [分成比例]
     * @param  [type] $order_id   [订单号码]
     * @param  [type] $yb         [银币]
     * @param  [type] $jb         [金币]
     * @return [type]             [布尔值]
     */
    public function invitation_commission($invitation,$user,$commission,$order_id,$jb,$yb)
    {
        // 实例化用户佣金模型
        $usersCommission = M('usersCommission');
        // 开启事务
        $usersCommission->startTrans();
        // 会员邀请人
        $saveData['user_code']  = $invitation['code'];
        // 产生分红的会员
        $saveData['nexus_user'] = $user['code'];
        // 分成的订单号
        $saveData['order_id']   = $order_id;
        // 会员获得银币
        $saveData['silver']     = $commission['inviter_y'] * $yb / 100;
        // 会员获得金币
        $saveData['gold_num']   = $commission['inviter_j'] * $jb / 100;
        // 会员当前银币
        $saveData['current_silver']     = $invitation['silver'] + $saveData['silver'];
        // 会员当前金币
        $saveData['current_gold_num']   = $invitation['gold_num'] + $saveData['gold_num'];
        // 获得类型
        $saveData['type']   = 1;
        // 描述
        $saveData['describe']   = '邀请会员'.$user['nickname'].'购买，单号为['.$order_id.']的商品获得金币奖励:'.$saveData['gold_num'].'个,获得银币奖励：'.$saveData['silver'].'个';
        // 创建时间
        $saveData['addtime']    = time(); 
        // 更新时间
        $saveData['updatetime']     = $saveData['addtime']; 
        // dump($saveData);die;
        // 写用户佣金记录
        $userRes = $usersCommission->add($saveData);
        // 准备更新条件
        $userWhere['id'] = $invitation['id'];
        // 准备更新数据
        // 会员银币
        $saveUserData['silver']     = $saveData['current_silver'];
        // 会员金币
        $saveUserData['gold_num']   = $saveData['current_gold_num'];
        // 更新时间
        $saveUserData['updatetime'] = $saveData['addtime'];
        // 更新用户账号
        $usersSaveRes = M('users')->where($userWhere)->save($saveUserData);

        // 模拟看
        echo $saveData['describe'].'<br/>';

        // 会员金银币记录流水填写成功并更新会员表当前金银币成功
        if($userRes && $usersSaveRes){
            // 执行事务
            $usersCommission->commit();
            return true;
        }else{
            // 事务回滚
            $usersCommission->rollback();
            return false;
        }
    }

    /**
     * [user_commission 会员金币银币分配]
     * @param  [type] $user       [会员]
     * @param  [type] $commission [分成比例]
     * @param  [type] $order_id   [订单号码]
     * @param  [type] $yb         [银币]
     * @param  [type] $jb         [金币]
     * @return [type]             [布尔值]
     */
    public function user_commission($user,$commission,$order_id,$jb,$yb)
    {
        // 实例化用户佣金模型
        $usersCommission = M('usersCommission');
        // 开启事务
        $usersCommission->startTrans();
        // 会员自己
        $saveData['user_code']  = $user['code'];
        // 产生分红的会员
        $saveData['nexus_user']     = $user['code'];
        // 分成的订单号
        $saveData['order_id']   = $order_id;
        // 会员获得银币
        $saveData['silver']     = $commission['user_y'] * $yb / 100;
        // 会员获得金币
        $saveData['gold_num']   = $commission['user_j'] * $jb / 100;
        // 会员当前银币
        $saveData['current_silver']     = $user['silver'] + $saveData['silver'];
        // 会员当前金币
        $saveData['current_gold_num']   = $user['gold_num'] + $saveData['gold_num'];
        // 获得类型
        $saveData['type']   = 0;
        // 描述
        $saveData['describe']   = '自己购买，单号为['.$order_id.']的商品获得金币奖励:'.$saveData['gold_num'].'个,获得银币奖励：'.$saveData['silver'].'个';
        // 创建时间
        $saveData['addtime']    = time(); 
        // 更新时间
        $saveData['updatetime']     = $saveData['addtime']; 
        // dump($saveData);die;
        // 写用户佣金记录
        $userRes = $usersCommission->add($saveData);
        // 准备更新条件
        $userWhere['id'] = $user['id'];
        // 准备更新数据
        // 会员银币
        $saveUserData['silver']     = $saveData['current_silver'];
        // 会员金币
        $saveUserData['gold_num']   = $saveData['current_gold_num'];
        // 更新时间
        $saveUserData['updatetime'] = $saveData['addtime'];
        // 更新用户账号
        $usersSaveRes = M('users')->where($userWhere)->save($saveUserData);

        // 模拟看
        echo $saveData['describe'].'<br/>';;

        // 会员金银币记录流水填写成功并更新会员表当前金银币成功
        if($userRes && $usersSaveRes){
            // 执行事务
            $usersCommission->commit();
            return true;
        }else{
            // 事务回滚
            $usersCommission->rollback();
            return false;
        }

    }


    /**
     * [vendor_commission 分销商佣金分配]
     * @param  [type] $vendor   [分销商]
     * @param  [type] $type     [获得佣金类型]
     * @param  [type] $nexus_vendor     [产生分红的分销商] 
     * @param  [type] $ratio    [分成比例]
     * @param  [type] $order_id [获得分成的订单号]
     * @param  [type] $yj       [佣金总额]
     * @return [type]           [布尔值]
     */
    public function vendor_commission($vendor,$type,$nexus_vendor,$nexus_user,$ratio,$order_id,$yj){
        // 关联分销商唯一标识
        $addData['vendor_code'] = $vendor['code'];
        // 产生分成的关联分销商
        $addData['nexus_vendor'] = $nexus_vendor['code'];
        // 产生分成的关联会员
        $addData['nexus_user'] = $nexus_user['code'];
        // 产生分成的订单号码
        $addData['order_id'] = $order_id;
        // 获得分红
        $addData['abonus'] = $yj*$ratio/100;
        // 当前分红
        $addData['current_abonus'] = (($addData['abonus']*100)+($vendor['abonus']*100))/100;
        // 获得分红的类型
        $addData['type'] = $type;
        // 创建时间
        $addData['addtime']     = time(); 
        // 更新时间
        $addData['updatetime']  = $addData['addtime']; 
        // 获得分红的描述
        switch ($type) {
            case '0':
                // 分销商直接会员
                $addData['describe'] = '分销商下的会员'.$nexus_user['nickname'].'购买单号：'.$order_id.'的商品获得佣金奖励:'.$addData['abonus'].'元';
                break;
            case '1':
                // 分销商下级分销商的会员
                $addData['describe'] = '下级分销商'.$nexus_vendor['name'].'下的会员'.$nexus_user['nickname'].'购买单号：'.$order_id.'的商品获得佣金奖励:'.$addData['abonus'].'元';
                break;
            case '2':
                // 分销商下下级分销商的会员
                $addData['describe'] = '下下级分销商'.$nexus_vendor['name'].'下的会员'.$nexus_user['nickname'].'购买单号：'.$order_id.'的商品获得佣金奖励:'.$addData['abonus'].'元';
                break;
            case '3':
                // 分销商邀请的同级分销商的会员
                $addData['describe'] = '邀请的分销商'.$nexus_vendor['name'].'下的会员'.$nexus_user['nickname'].'购买单号：'.$order_id.'的商品获得佣金奖励:'.$addData['abonus'].'元';
                break;
            default:
                # code...
                break;
        }
        // 实例化用户佣金模型
        $vendorCommission = M('vendorsCommission');
        // 开启事务
        $vendorCommission->startTrans();
        // 写佣金记录
        $vendoriAddRes = $vendorCommission->add($addData);
        // 更新条件
        $saveWhere['code'] = $vendor['code'];
        // 更新数据
        $saveData['abonus'] = $addData['current_abonus'];
        // 执行更新
        $saveData['updatetime']     = $addData['updatetime'];
        $vendorisaveRes = M('vendors')->where($saveWhere)->save($saveData);

        // 模拟看
        echo $addData['describe'].'<br/>';;

        // 如果分销商分红记录和分销商分红更新成功
        if($vendoriAddRes && $vendorisaveRes){
            // 执行事务
            $vendorCommission->commit();
            return true;
        }else{
            // 事务回滚
            $vendorCommission->rollback();
            return false;
        }
    }

}
