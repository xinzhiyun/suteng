<?php
/**
 * Created by PhpStorm.
 * User: 李振东
 * Time: 2018/3/29 下午2:37
 */

namespace Common\Tool;


class Device
{
    private static $pre = 'devices_';       //缓存的前缀
    private static $fields = array(         //缓存中的字段
        'id',               //设备表的ID
        'sid',              //设备状态表的ID
        'timer'             //定时的数据 --json

    );

    public function __construct()
    {
        global $redis;
        $redis = new \Redis();
        $redis->connect('127.0.0.1',6379);
    }

    /**
     * 设备数据
     * @param $key 设备号
     * @return mixed
     */
    public function get_devices_info($device_code,$field='')
    {
        global $redis;
        $key = self::$pre.$device_code;

        if (empty($field)) { //获取所有

        }

        if ($redis->hExists($key,$field)) {
            return $redis->hget($key,$field);
        } else {
            return $this->make_cache($device_code,$field);
        }
    }

    /**
     * 创建设备缓存
     */
    public function make_cache($device_code,$field)
    {
        global $redis;
        $key = self::$pre.$device_code;

        if (in_array($field,self::$fields)) {
            switch ($field) {
                case "id":
//                case "id":
                    $val = M('devices')->where('device_code='.$device_code)->getField($field);
                    break;
                case "sid":
                    $val = M('devices_statu')->where('DeviceID='.$device_code)->getField('id');
                    break;
            }
            $redis->hset($key,$field,$val);
            return $val;
        }
    }

    /**
     * 直接设定缓存值
     * @param $device_code
     * @param $field
     * @param $val
     */
    public function set($device_code,$field,$val)
    {
        global $redis;
        $key = self::$pre.$device_code;
        return $redis->hset($key,$field,$val);
    }




}