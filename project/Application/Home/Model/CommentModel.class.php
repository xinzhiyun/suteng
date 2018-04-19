<?php
namespace Home\Model;
use Think\Model\RelationModel;
class CommentModel extends RelationModel
{
    protected $_link = array(
        'pic'=>array(
            'mapping_type'  => self::HAS_MANY,
            'class_name'    => 'ComPic',
            'foreign_key'   => 'cid',
            'mapping_name'  => 'pics',
            'mapping_fields' => 'path'
        ),
        'good'=>array(
                'mapping_type'  => self::BELONGS_TO,
                'class_name'    => 'goods',
                'foreign_key'   => 'gid',
                'mapping_name'  => 'good',
                'mapping_fields' => 'id,name,pic'
        ),
        'user'=>array(
                'mapping_type'  => self::BELONGS_TO,
                'class_name'    => 'users',
                'foreign_key'   => 'uid',
                'mapping_name'  => 'user',
                'mapping_fields' => 'id,nickname,head'
        ),
    );

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