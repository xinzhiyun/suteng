<?php
namespace Home\Model;
use Think\Model;
class CartModel extends Model
{
    // 自动验证
    protected $_validate = array(
        array('uid','require','请先登陆！'),
        array('num', '/^\d$/', '请确认购买数量', '3', 'regex'),
        array('gid','require','请先刷新页面！'),
    );
    // 自动完成
    protected $_auto = array (
        array('addtime','time',3,'function'),
    );

    // 获取当前用户购物车信息
    public function getCart($where)
    {
        $data = $this
            ->alias('c')
            ->where($where)
            ->join('__USERS__ u ON c.uid=u.id', 'LEFT')
            ->join('__GOODS__ g ON c.gid=g.id', 'LEFT')
            ->join('__ATTR_VAL__ av ON g.id=av.gid', 'LEFT')
            ->join('__ATTR__ a ON av.aid=a.id', 'LEFT')
            ->join('__GOODS_DETAIL__ gd ON g.id=gd.gid', 'LEFT')
            ->join('__PIC__ p ON g.id=p.gid', 'LEFT')
            ->join('__PRICE__ pr ON g.id=pr.gid','LEFT')
            ->field('p.picname,p.path,a.attr,av.val,gd.*,g.name,c.id,c.num,pr.price')
            ->select();
        return $data;
    }
}
