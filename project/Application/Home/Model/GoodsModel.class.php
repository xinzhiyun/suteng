<?php
namespace Home\Model;
use Think\Model;

/**
 * 商品模型
 */
class GoodsModel extends Model
{
    // 获取商品信息
    public function getGoodsList($map=null)
    {
        $goods = $this
            ->alias('g')
            ->where($map)
            ->join('__ATTR_VAL__ av ON g.id=av.gid', 'LEFT')
            ->join('__ATTR__ a ON av.aid=a.id', 'LEFT')
            ->join('__GOODS_DETAIL__ gd ON g.id=gd.gid', 'LEFT')
            ->join('__PIC__ p ON g.id=p.gid', 'LEFT')
            ->join('__CATEGORY__ c ON g.cid=c.id', 'LEFT')
            ->field('g.name,g.addtime,g.updatetime,av.*,c.name cname,a.attr,gd.*')
            ->select();
        return $goods;
    }
}
