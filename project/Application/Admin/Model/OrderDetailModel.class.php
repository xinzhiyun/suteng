<?php
namespace Admin\Model;
use Think\Model;
class OrderDetailModel extends BaseModel
{
    public function getInfo($where=array())
    {
        $data = $this
            ->where($where)
            ->alias('od')
            ->join('__SHOP_ORDER__ o ON od.order_id=o.order_id', 'LEFT')
            ->join('__GOODS__ g ON od.gid=g.id', 'LEFT')
            ->join('__ADDRESS__ a ON o.address_id=a.id', 'LEFT')
            ->field('')
            ->select();
        return $data;
    }
}