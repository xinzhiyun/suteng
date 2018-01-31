<?php
namespace Admin\Model;
use Think\Model;

class ShopOrderModel extends BaseModel
{
    // 获取订单数据
    public function getOrders($where=null)
    {
        $data = $this
            ->where($where)
            ->alias('o')
            // ->join('__GOODS__ g ON o.gid=g.id', 'LEFT')
            // ->join('__GOODS_DETAIL__ gd ON g.id=gd.gid', 'LEFT')
            // ->join('__PIC__ p ON g.id=p.gid', 'LEFT')
            ->join('__ADDRESS__ a ON o.address_id=a.id', 'LEFT')
            ->join('__USERS__ u ON o.uid=u.id', 'LEFT')
            ->field('a.address,a.phone,a.name,o.order_id,o.g_cost,o.g_price,o.g_num,o.addtime,o.status,o.mode')
            ->select();
        return $data;
    }
}
