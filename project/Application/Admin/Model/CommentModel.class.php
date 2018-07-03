<?php
namespace Admin\Model;
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
                'mapping_fields' => 'id,name,gpic'
        ),
        'user'=>array(
                'mapping_type'  => self::BELONGS_TO,
                'class_name'    => 'users',
                'foreign_key'   => 'uid',
                'mapping_name'  => 'user',
                'mapping_fields' => 'id,nickname,head'
        ),
    );
}