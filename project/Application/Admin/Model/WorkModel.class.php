<?php
namespace Admin\Model;
use Org\Util\Date;
use Think\Model\RelationModel;
/**
 * Class 工单数据处理
 * @package Admin\Model
 * @author 潘宏钢 <619328391@qq.com>
 */
class WorkModel extends RelationModel
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


    /**
     * 获取work表的记录
     * @param  array $map 查询条件
     * @return array      查询结果和分页数据
     */
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

    /**
     * 获取work表的一条记录
     * @param  int $id 主键id
     * @return array     查询的数组数据
     */
    public function getWorkInfoByID($id)
    {
         $data = $this
            ->relation(['Vendors','dwVendor'])
            ->find($id);
        return $data;
    }

    /**
     * 处理查询数据
     * @param  array $list 一维数组
     * @return array       一维数组
     */
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

    /**
     * 添加数据
     * @param  array $data  添加的数据
     * @return int          新增的数据id
     */
    public function addData($data){
        // 去除键值首尾的空格
        foreach ($data as $k => $v) {
            $data[$k]=trim($v);
        }
        $id=$this->add($data);
        return $id;
    }

    /**
     * 获取分页数据
     * @param  subject  $model  model对象
     * @param  array    $map    where条件
     * @param  string   $order  排序规则
     * @param  integer  $limit  每页数量
     * @param  integer  $field  $field
     * @return array            分页数据
     */
    public function getPage($model,$map,$order='',$limit=10,$field=''){
        $count=$model
            ->where($map)
            ->count();
        $page=new_page($count,$limit);
        // setPageConf($page);
        // 获取分页数据
        if (empty($field)) {
            $list=$model
                ->where($map)
                ->order($order)
                ->limit($page->firstRow.','.$page->listRows)
                ->select();
        }else{
            $list=$model
                ->field($field)
                ->where($map)
                ->order($order)
                ->limit($page->firstRow.','.$page->listRows)
                ->select();
        }
        $data=array(
            'data'=>$list,
            'page'=>$page->show()
            );
        return $data;
    }
}
