<?php
namespace Home\Model;
use Think\Model;
class CommentModel extends Model
{
    public function getInfo($map=array())
    {
        $data = $this
            ->alias('c')
            ->where($map)
            ->join('__GOODS__ g ON c.gid=g.id', 'LEFT')
            ->join('__PIC__ p ON c.gid=p.gid', 'LEFT')
            ->join('__SHOP_ORDER__ so ON c.uid=so.uid', 'LEFT')
            ->join('__ORDER_DETAIL__ od ON so.order_id=od.order_id','LEFT')
            ->field('g.name,p.path,c.content,c.installer_graded,c.attitude,c.dressing,c.addtime')
            ->select();
        return $data;
    }
}