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
        $data = M('Type')->field('id')->select();
        $res = array_column( $data, 'id' );
        return $res;
    }
    //
    public function getDevicesInfo($map)
    {
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

    // 获取设备信息
    public function getDeviceInfo($map=array())
    {
        $data = $this
            ->alias('d')
            ->where($map)
            ->join('__DEVICES_STATU__ ds ON d.device_code=ds.DeviceID', 'LEFT')
            ->find();
        return $data;
    }

    // 查询滤芯详情
    public function getFilterDetail($sum)
    {
        unset($sum['id'],$sum['typename'],$sum['addtime']);
        $sum = array_filter($sum);
        foreach ($sum as $key => $value) {
            $str = stripos($value,'-');
            $map['filtername'] = substr($value, 0,$str);
            $map['alias'] = substr($value, $str+1);
            $res[] = M('filters')->where($map)->find();
        }
        return $res;
    }

    // 获取滤芯数据
    public function getFilterInfo($map=array())
    {
        $data = $this
            ->alias('d')
            ->where($map)
            ->join('__TYPE__ t ON d.type_id=t.id', 'LEFT')
            ->find();
        return $data;
    }
}
