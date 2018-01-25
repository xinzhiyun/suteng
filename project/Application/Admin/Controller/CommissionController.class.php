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
        $this->display();
    }

    /**
     * [commission 佣金按比例分配]
     * @return [type] [description]
     */
    public function commission_proportion()
    {
        if(IS_POST){
            // dump(I());die;
            // 实例化佣金模型
            $commission = D('commission');

            // 创建数据
            if($commission->create()){
               // 上传数据
               if($commission->add()){
                    $message = ['code' => 200, 'message' => '佣金比例添加成功'];
               }else{
                    $message = ['code' => 403, 'message' => '佣金比例添加失败'];
               }
            }else{
                $this->error($commission->getError());
                // 表单验证错误
                $message = ['code' => 403, 'message' => $commission->getError()];
            }
            // 返回JSON格式数据
            $this->ajaxReturn($message);
                     
        }else{
            $this->display();  
        }
        
    }

}
