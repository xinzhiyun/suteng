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

}
