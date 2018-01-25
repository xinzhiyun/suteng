<?php
namespace Admin\Model;

/**
 * Class 佣金管理
 * @package Admin\Model\
 * @author 吴智彬 <519002008@qq.com>
 */
class CommissionModel extends BaseModel
{   
    /**
     * [$_validate 自动验证]
     * @var array
     */
    protected $_validate = array(
        array('user_y','/^[\d]+$/','会员银币分配占比格式不正确',1,'regex'),
        array('inviter_y','/^[\d]+$/','会员邀请人银币分配占比格式不正确',1,'regex'),
        array('user_j','/^[\d]+$/','会员邀请人金币分配占比格式不正确',1,'regex'),
        array('inviter_j','/^[\d]+$/','会员邀请人金币分配占比格式不正确',1,'regex'),
        array('vendor_a','/^[\d]+$/','A级分销商分配占比',1,'regex'),
        array('vendor_b','/^[\d]+$/','B级分销商分配占比',1,'regex'),
        array('vendor_c','/^[\d]+$/','C级分销商分配占比',1,'regex'),
        array('vendor_i','/^[\d]+$/','分销商邀请人分配占比',1,'regex'),
    );

    // /**
    //  * [$_auto 自动完成]
    //  * @var array
    //  */
    // protected $_auto = array ( 
    //     array('addtime','time',1,'function'),     // 对addtime字段在新增和编辑的时候写入当前时间戳
    //     array('updatetime','time',3,'function'),  // 对update_time字段在更新的时候写入当前时间戳
    // );
}