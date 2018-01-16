<?php
namespace Admin\Model;
use Think\Model;

/**
 * Class DevicesModel
 * @package Admin\Model
 * 设备添加操作
 * @author 陈昌平 <chenchangping@foxmail.com>
 */
class DevicesModel extends Model
{
    // 自动验证
    protected $_validate = array(
        array('device_code', '15', '编码格式不正确', '0', 'length'),
        array('device_code', '/^\d{15}$/', '编码格式不正确', '2', 'regex'),
        array('device_code', '', '已经添加', '1', 'unique'),
    );

    // 自动完成
    protected $_auto = array(
        array('device_statu', '1'),
        array('addtime', 'time', 1, 'function'),
    );

    // 获取产品类型
    public function getCate()
    {
        $data = M('DeviceType')->field('id')->select();
        $res = array_column( $data, 'id' );
        return $res;
    }
    //
    public function getDevicesInfo($map)
    {
        // 分页
        $count = $this
            ->where($map)
            ->alias('d')
            ->join("__DEVICES_STATU__ statu ON d.device_code=statu.DeviceID", 'LEFT')
            ->join("__BINDING__ bind ON d.id=bind.did", 'LEFT')
            ->join("__VENDORS__ vendors ON bind.vid=vendors.id", 'LEFT')
            // ->join("__DEVICE_TYPE__ type ON d.type_id=type.id", 'LEFT')
            ->order('statu.updatetime')
            ->limit($page->firstRow.','.$page->listRows)
            ->count();
        $page = new \Think\Page($count, 15);
        $page->rollPage = 10;
        getPageConfig($page);
        $show = $page->show();

        // 查询数据
        $data = $this
            ->where($map)
            ->alias('d')
            ->join("__DEVICES_STATU__ statu ON d.device_code=statu.DeviceID", 'LEFT')
            ->join("__BINDING__ bind ON d.id=bind.did", 'LEFT')
            ->join("__VENDORS__ vendors ON bind.vid=vendors.id", 'LEFT')
            // ->join("__DEVICE_TYPE__ type ON d.type_id=type.id", 'LEFT')
            ->order('statu.updatetime')
            ->limit($page->firstRow.','.$page->listRows)
            ->select();
        // 分配返回数据
        $assign = [
            'show' => $show,
            'data' => $data,
        ];
        return $assign;
    }
}
