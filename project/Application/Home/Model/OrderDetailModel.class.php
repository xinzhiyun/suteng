<?php
namespace Home\Model;
use Think\Model;
class OrderDetailModel extends Model
{
    public function getGoodsDetail($map=array())
    {
        $data = $this
            ->alias('od')
            ->where($map)
            // ->join('__')
            ->select();
            return $data;
    }
}