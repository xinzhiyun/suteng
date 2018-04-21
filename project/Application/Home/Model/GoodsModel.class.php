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
            ->join('__ATTR_VAL__ av ON g.id=av.gid', 'LEFT')
            ->join('__ATTR__ a ON av.aid=a.id', 'LEFT')
            ->join('__GOODS_DETAIL__ gd ON g.id=gd.gid', 'LEFT')
            ->join('__PIC__ p ON g.id=p.gid', 'LEFT')
            ->join('__PRICE__ pr ON g.id=pr.gid', 'LEFT')
            ->join('__CATEGORY__ c ON g.cid=c.id', 'LEFT')
            ->field('g.name,g.addtime,g.updatetime,av.*,g.cid,c.name cname,a.attr,gd.is_install,gd.is_hire,gd.desc,p.path,pr.price')
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
