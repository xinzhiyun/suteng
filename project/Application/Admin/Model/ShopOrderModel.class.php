<?php
namespace Admin\Model;
use Think\Model;

class ShopOrderModel extends BaseModel
{
    // 获取订单数据
    public function getOrders($where=null)
    {
        $count      = $this
            ->where($where)
            ->alias('o')
            ->join('__ADDRESS__ a ON o.address_id=a.id', 'LEFT')
            ->join('__USERS__ u ON o.uid=u.id', 'LEFT')
            ->field('a.address,a.phone,a.name,o.order_id,o.g_cost,o.g_price,o.g_num,o.addtime,o.status,o.mode,o.g_type')
            ->count();
        $Page       = new \Think\Page($count,10);
        $show = page_style($Page);
//        page_config($Page);
//        $show       = $Page->show();
        $data = $this
            ->where($where)
            ->alias('o')
            ->join('__ADDRESS__ a ON o.address_id=a.id', 'LEFT')
            ->join('__USERS__ u ON o.uid=u.id', 'LEFT')
            ->join('__INVOICE__  i ON o.order_id = i.oid', 'LEFT')
            ->field('a.address,a.phone,a.name,o.order_id,o.g_cost,o.g_price,o.g_num,o.addtime,o.status,o.mode,o.g_type,i.id invoice_id,i.is_electronic invoice_is_electronic')
            ->order('o.addtime desc')
            ->limit($Page->firstRow.','.$Page->listRows)
            ->select();
        $data = [
            'show' => $show,
            'data' => $data,
        ];
        // p($data);
        return $data;
    }

    public function getUserInfo($map=array())
    {
        $data = $this
            ->alias('o')
            ->where($map)
            ->join('__ADDRESS__ a ON o.uid=a.uid', 'LEFT')
            ->find();
        return $data;
    }
}
