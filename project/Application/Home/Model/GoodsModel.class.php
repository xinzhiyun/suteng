<?php
namespace Home\Model;
use Think\Model;

/**
 * 商品模型
 */
class GoodsModel extends Model
{
    // 获取商品信息
    public function getGoodsList($map=array())
    {
        $goods = $this
            ->alias('g')
            ->where($map)
            ->join('__GOODS_RELATION_BLOCK__ grb ON g.id=grb.gid', 'LEFT')
            ->join('__GOODS_BLOCK__ gb ON gb.id=grb.bid', 'LEFT')
            ->join('__GOODS_PRICE__ pr ON g.id=pr.gid', 'LEFT')
            ->field('g.name,g.gpic,pr.price,gb.bname')
            ->order('gb.id')
            // ->limit(1)
            ->select();
        return $goods;
    }

    // 获取评论数据
    public function getComment($where=null)
    {
        $data = $this
            ->where($where)
            ->alias('g')
            ->join('__COMMENT__ c ON g.id=c.gid', 'LEFT')
            ->join('__COM_PIC__ cp ON c.id=cp.cid', 'LEFT')
            ->join('__USERS__ u ON c.uid=u.id', 'LEFT')
            ->field('cp.path,u.nickname,u.head,c.content')
            ->select();
        return $data;
    }

    // 属性参数处理
    public function attrAction()
    {
        
    }
    
}
