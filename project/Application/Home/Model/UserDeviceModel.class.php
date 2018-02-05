<?php
namespace Home\Model;
use Think\Model;
class UserDeviceModel extends Model
{
    public function getBindInof($uid)
    {
        $data = $this
            ->alias('ud')
            ->where('ud.uid='.$uid)
            ->join('__DEVICES__ d ON ud.did=d.id', 'LEFT')
            ->select();
        return $data;
    }
}