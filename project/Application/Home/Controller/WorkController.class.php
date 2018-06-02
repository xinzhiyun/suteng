<?php
namespace Home\Controller;

use Zend\Serializer\Adapter\AdapterInterface;

class WorkController extends CommonController
{
    /**
     * 发起安装工单
     */
    public function workAdd()
    {
        try {
            $post = I('post.');
            if (empty($post['sid'])) {
                E('请选择服务站', 40103);
            }
            if (empty($post['type'])) {
                E('请选择工单', 40102);
            } else {
                $data['type'] = $post['type'];
            }
            if (empty($post['orderid'])) {
                E('订单号为空', 40101);
            } else {
                $data['orderid'] = $post['orderid'];
            }

            $orderMap=[
                'order_id'=>$post['orderid'],
                'is_pay'=>1,
                'is_work'=>0
            ];

            M('orders')->where($orderMap)->find();

            $data['number'] = getWorkNumber();

            if($data['type']==0){
                $data['content'] = '新购设备-安装';
                $data['title']   = '设备安装';
            }
            $data['create_at']=time();
            $data['update_at']=time();
            M('work')->add($data);

            E('工单开启成功,请等待安装服务人员联系',200);
        } catch (\Exception $e) {
            $this->toJson($e);
        }
    }
    
}   


