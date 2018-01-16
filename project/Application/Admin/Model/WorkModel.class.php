<?php
namespace Admin\Model;
use Org\Util\Date;
/**
 * Class 工单数据处理
 * @package Admin\Model
 * @author 潘宏钢 <619328391@qq.com>
 */
class WorkModel extends BaseModel
{
    protected $patchValidate = true;
    // 自动验证
    protected $_validate = array(
        array('id','require','ID不能为空'),
        array('uid','require','uID不能为空'),
        array('dw_uid','require','处理人ID不能为空'),
        array('title','require','标题不能为空'),
        array('type','require','类型不能为空'),
        array('number','require','工单号不能为空'),
        array('name','require','name不能为空'),
        array('phone','/^1[34578]\d{9}$/','电话号码格式不对'),
        array('content','require','工作内容不能为空'),
        array('address','require','地址不能为空'),
    );

    protected $_link = array(
        'Vendors'=>array(
                'mapping_type'  => self::BELONGS_TO,
                'class_name'    => 'Vendors',
                'foreign_key'   => 'uid',
                'mapping_name'  => 'Vendors',
                'mapping_fields' => 'user,name,phone'
        ),
        'dwVendor'=>array(
                'mapping_type'  => self::BELONGS_TO,
                'class_name'    => 'Vendors',
                'foreign_key'   => 'dw_uid',
                'mapping_name'  => 'dwVendor',
                'mapping_fields' => 'user,name,phone'
        ),
    );

    // 自动完成
    // protected $_auto = array (
    //     array('addtime','time',3,'function'), // 对addtime字段在新增和编辑的时候写入当前时间戳
    // );


     //
    public function getWorksInfo($map)
    {
        // 分页
        $count = $this
            ->where($map)
            ->limit($page->firstRow.','.$page->listRows)
            ->count();
        $page=new_page($count, 15);


        // 查询数据
        $data = $this
            ->where($map)
            ->order('create_at desc')
            ->relation(['Vendors','dwVendor'])
            ->limit($page->firstRow.','.$page->listRows)
            ->select();
        // 分配返回数据
        $assign = [
            'page' => bootstrap_page_style($page->show()),
            'data' => $data,
        ];
        return $assign;
    }

    public function getWorkInfoByID($id)
    {
         $data = $this
            ->relation(['Vendors','dwVendor'])
            ->find($id);
        return $data;
    }

    // 处理查询数据
    public function getAll($list)
    {
        $type = array('安装','维修','维护');
        $result = array('未处理','正在处理','已处理');
        $list['create_at'] = date('Y-m-d H:i:s',$list['create_at']);
        if ($list['time']) {
            $list['time'] = date('Y-m-d H:i:s',$list['time']);
        }
        $list['type'] = $type[$list['type']];
        $list['result'] = $result[$list['result']];
        return $list;
    }
}
