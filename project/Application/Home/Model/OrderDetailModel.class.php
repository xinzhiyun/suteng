<?php
namespace Home\Model;
use Think\Model\RelationModel;
class OrderDetailModel extends RelationModel
{

    protected $_link = array(

        'pic'=>array(
            'mapping_type'  => self::HAS_MANY,
            'class_name'    => 'pic',
            'foreign_key'   => 'gid',
            'mapping_name'  => 'pics',
            'mapping_fields' => 'path'
        ),

    );

    // 查询订单数据
    public function getGoodsDetail($map=array())
    {
        $data = $this
            ->alias('od')
            ->where($map)
            ->join('__SHOP_ORDER__ so ON od.order_id=so.order_id', 'LEFT')
            ->join('__USERS__ u ON so.uid=u.id', 'LEFT')
            ->join('__PRICE__ p ON u.grade=p.grade AND od.gid=p.gid', 'LEFT')
            ->join('__GOODS__ g ON od.gid=g.id', 'LEFT')
            ->join('__GOODS_DETAIL__ gd ON g.id=gd.gid', 'LEFT')
            ->join('__PIC__ pic ON g.id=pic.gid', 'LEFT')
            ->field('g.name,gd.desc,p.price,od.num,pic.path,g.id,od.order_id')
            ->select();
        return $data;
    }
}