<?php
namespace Admin\Model;
use Think\Model;
use Org\Util\Date;

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
        array('device_code', '', '设备重复添加', '1', 'unique'),
    );

    // 自动完成
    protected $_auto = array(
        array('device_statu', '1'),
        array('addtime', 'time', 1, 'function'),
    );

    // 获取当月充值数据
    public function getCurrentMonth()
    {
        $date = new Date();
        $firstDayOfMonth = $date->firstDayOfMonth();
        $firstat = strtotime($firstDayOfMonth);
        $lastDayOfMonth = $date->lastDayOfMonth();
        $lastat = strtotime($lastDayOfMonth) + 24*60*60;

       $map['addtime'] = array(array('gt',$firstat),array('lt',$lastat), 'and');
       // $map['_query'] = "status=1";
       $data = $this
                ->where($map)
                ->select();
                // dump($data);
       return $data;
    }

    // 当月每一天的数据条数
    public function getTotalByEveryDay($data=[])
    {
        if (count($data) == 0) {
            $data = $this->getCurrentMonth();
        }
        $date = new Date();
        $maxDayOfMonth = $date->maxDayOfMonth();
        $firstDayOfMonth = $date->firstDayOfMonth();
        $startat = strtotime($firstDayOfMonth);
        $result = [];

        for ($i=0; $i < $maxDayOfMonth; $i++) { 
          foreach ($data as $key => $value) {
            if ($value['addtime'] >= $startat && $value['addtime'] <= $startat+24*60*60) {
              $result["$i"+1]['count'] += 1;
              $result["$i"+1]['money'] += $value['money'];
              $result["$i"+1]['num']  += $value['num'];
              $result["$i"+1]['flow'] += $value['currentflow'];            
            }else{
              if (!array_key_exists($i+1,$result)) {
                $result["$i"+1] = null;
              }
            }
          }
          $startat = $startat+24*60*60;
        }
        return $result;
    }

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
    public function getFilterDetail($list)
    {
        foreach ($list as $k=> $v) {
            if(strstr($k,'filter') and !empty($v) ){
                $sum[$k] = $v;
            }
        }
        //unset($sum['id'],$sum['typename'],$sum['addtime']);
        foreach ($sum as $key => $value) {
            $str = stripos($value,'-');
            $map['filtername'] = substr($value, 0,$str);
            $map['alias'] = substr($value, $str+1);
            $res[] = M('filters')->where($map)->find();
        }
        $res = array_filter($res);
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

    // 获取经销商信息
    public function getVendors($map=array())
    {
        $data = $this
            ->alias('d')
            ->where($map)
            ->join('__VENDORS__ v ON d.vid=v.id', 'LEFT')
            ->field('v.*')
            ->find();
        return $data;
    }
}
