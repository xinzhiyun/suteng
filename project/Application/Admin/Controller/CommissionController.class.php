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
            if($commission->create()){
               // // 上传数据
               // if($commission->add()){
               //      $message = ['code' => 200, 'message' => '佣金比例添加成功'];
               // }else{
               //      $message = ['code' => 403, 'message' => '佣金比例添加失败'];
               // }
            }else{
                $this->error($commission->getError());
                // 表单验证错误
                $message = ['code' => 403, 'message' => $commission->getError()];
            }
            // 返回JSON格式数据
            $this->ajaxReturn($message);        
        }

    }

}
