<?php
namespace Home\Model;
use Think\Model;
use Think\Model\RelationModel;
class RefundModel extends RelationModel
{
    // // 自动验证
    // protected $_validate = array(
    //     array('typename','require','类型名称不能为空'),
    //     array('typename','/^[a-zA-Z0-9\x{4e00}-\x{9fa5}]{1,660}$/u','类型名称不能使用特殊字符',1,'regex'),
    //     array('typename','','该类型名称已存在，请换一个试试，如商务A型',0,'unique',1)
    // );


    // // 自动完成
    // protected $_auto = array (
    //     array('addtime','time',3,'function'), // 对addtime字段在新增和编辑的时候写入当前时间戳
    // );
    
    protected $_link = array(
        'goods'=>array(
                'mapping_type'  => self::HAS_MANY,
                'class_name'    => 'refund_goods',
                'foreign_key'   => 'rf_id',
                'mapping_name'  => 'goods',
                'mapping_fields' => 'oid,gid'
        ),
        'logistics'=>array(
                'mapping_type'  => self::HAS_ONE,
                'class_name'    => 'refund_logistics',
                'foreign_key'   => 'rf_id',
                'mapping_name'  => 'logistics',
                'mapping_fields' => 'name,number,fahuo_time'
        ),
    );

}
