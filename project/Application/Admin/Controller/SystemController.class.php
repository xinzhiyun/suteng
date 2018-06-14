<?php
namespace Admin\Controller;

class SystemController extends CommonController
{



    public function setAgreement()
    {
        try {
            $data = I('post.');

            $map['status'] = 1;

            // 删除数组中为空的值
            $map = array_filter($map, function ($v) {
                if ($v != "") {
                    return true;
                }
                return false;
            });

            if(I('mode',1)==1){
                $list = M('service')->where($map)->select();
            }else{
                $list = M('service_other')->where($map)->select();
            }

            $this->toJson(['list'=>$list],'获取成功');

        } catch (\Exception $e) {
            $this->toJson($e);
        }
    }

}