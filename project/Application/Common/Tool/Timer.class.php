<?php
/**
 * Created by PhpStorm.
 * User: 李振东
 * Time: 2018/3/29 下午2:37
 */

namespace Common\Tool;

class Timer extends Redis
{
    private static $pre = 'shizhou_timer_';       //缓存的前缀
    private static $fields = array(         //缓存中的字段
        'id',               //表的ID
        'device_code',
        'repeat',
    );
    private static $notnullfields=array(    //非空字段
        'id',               //表的ID
        'device_code',
        'repeat',
    );

    public static function get_info($timer_id,$field='')
    {
        self::connect();
        $key = self::$pre.$timer_id;
        if (empty($field)) { //获取所有

        }

        if (self::$redis->hExists($key,$field)) {
            $res = self::$redis->hget($key,$field);
            //返回值为空 就重建缓存
            if (in_array($field,self::$notnullfields)) {
                $res = $res ?? self::make_cache($device_code,$field);
                if($field == 'device_code' and empty($res)) {

                }
            }
            return $res;
        } else {
            return self::make_cache($device_code,$field);
        }
    }

    public static function make_cache($id,$field)
    {
        self::connect();
        $key = self::$pre.$id;

        if ( in_array( $field, self::$fields ) ) {
            switch ( $field ) {
                case "device_code":
                      $did = M('task')->where('id='.$id)->getField('did');
                      $val = M('devices')->where('id='.$did)->getField('device_code');
                    self::$redis->hset($key,$field,$val);
                break;
                case "id":
                case 'repeat':
                    $val = M('task')->where('id='.$id)->getField($field);
                    if(!empty($val)){
                        self::$redis->hset($key,$field,$val);
                    }
                break;
            }
            return $val;
        }
    }

    public static function del($id)
    {
        self::connect();
        $key = self::$pre.$id;

        return self::$redis->delete($key);
    }

    //开启和关闭定时
    public static function statu($id,$state)
    {
        self::connect();
        $res = false;
        $time = time();
        if(M('task')->where('id='.$id)->save(['state'=>$state,'updatetime'=>$time])) {
            $res = true;
            if ($state == '1') {
                $key = self::$pre.$id;
                $val = M('task')->where('id='.$id)->find();
                $res = self::$redis->hMset($key,$val);
            } else {
                $res = self::del($id);
            }
        }
        return $res;
    }

    //定时器 数据执行一次
    public static function play($timer_id)
    {
        self::connect();
        $repeat = self::get_info($timer_id,'repeat');
        if (self::del($timer_id) ) {
            $data['playtime']=time();
            $data['playstate']=1;

            if ($repeat==1) {
                M('task')->where('id='.$timer_id)->save($data);
            } else {
                $data['state']=0;
                M('task')->where('id='.$timer_id)->save($data);
            }
            return true;
        }
        return false;
    }

}