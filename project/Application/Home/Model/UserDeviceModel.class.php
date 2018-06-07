<?php
namespace Home\Model;
use Think\Model;
class UserDeviceModel extends Model
{
    public function getBindInof($map=array())
    {
        $data = $this
            ->alias('ud')
            ->where($map)
            ->join('__DEVICES__ d ON ud.did=d.id', 'LEFT')
            ->field('d.device_code,ud.status,ud.addressinfo')
            ->select();
        return $data;
    }

    public function getBindType($map=array())
    {
        $data = $this
            ->alias('ud')
            ->where($map)
            ->join('__DEVICES__ d ON ud.did=d.id', 'LEFT')
            ->field('d.type_id tid')
            ->find();

        return $data;
    }

}