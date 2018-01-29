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
            ->join('__GOODS__ g ON o.gid=g.id', 'LEFT')
            ->join('__GOODS_DETAIL__ gd ON g.id=gd.gid', 'LEFT')
            ->join('__PIC__ p ON g.id=p.gid', 'LEFT')
            ->join('__ADDRESS__ a ON o.uid=a.uid', 'LEFT')
            ->join('__USERS__ u ON a.uid=u.id', 'LEFT')
            ->select();
        return $data;
    }
}
