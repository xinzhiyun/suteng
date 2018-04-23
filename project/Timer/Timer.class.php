<?php
namespace TimerClient;
/**
 * 定时器
 */
class Timer
{
    // 设备命令集
    private static $msgs=[
        '0'=>['DeviceID'=>'', 'PackType'=>'SetData','Vison'=>'0','DeviceStause'=>'8','PackNum'=>4],//开机
        '1'=>['DeviceID'=>'', 'PackType'=>'SetData','Vison'=>'0','DeviceStause'=>'7','PackNum'=>4],//关机
        '2'=>['DeviceID'=>'', 'PackType'=>'SetData','Vison'=>'0','DeviceStause'=>'8','PackNum'=>4],//开启加热  等待确定命令宝
        '3'=>['DeviceID'=>'', 'PackType'=>'SetData','Vison'=>'0','DeviceStause'=>'8','PackNum'=>4],//关闭加热  等待确定命令宝
    ];

    private static $timer_pre = "shizhou_timer_";

    public function __construct()
    {
        global $redis;
        global $db;
        include_once './lib/Gateway.php';
        include_once './lib/Connection.php';

        $redis = new \Redis();
        $redis->connect('127.0.0.1',6379);

        $db = new \Workerman\MySQL\Connection('127.0.0.1', '3306', 'root', 'shizhouwangluo', 'shizhou');
    }

    /**
     * [Timer 定时器 每半小时执行一次]
     */
    public function Timer_min()
    {
        global $redis;
        $items = $this->getCache(self::$timer_pre);
        $time = time();
        //发送设备命令
        foreach ($items as $k=>$li){
            //比对时间 设备控制
            $minute = $li['minute']+5;//做延时容错 5分钟精度
            $ftime = strtotime(date('Y-m-d',time()).' '.$li['hour'].':'.$minute);
            if ($ftime <= $time) {
                $DeviceIDs[]= $li['device_code'];
                $eqs[]=$li;
                $msg = self::$msgs[$li['type']]??'';

                if( !empty($msg) and is_array($msg) and !empty($li['device_code']) ){
                    //写定时缓存 用于清除缓存
                    $key ="devices_".$li['device_code'];
                    $arr = [];
                    if ($redis->hExists($key,'timer')) {
                        $timer_c=$redis->hget($key,'timer');
                        $arr = unserialize($timer_c);
                        if(!empty($arr) and is_array($arr)){
                            if(!in_array($li['id'],$arr)){
                                $arr[]=$li['id'];
                            }
                        }else{
                            $arr = [];
                            $arr[]=$li['id'];
                        }

                    } else {
                        $arr[]=$li['id'];
                    }

                    $redis->hset($key,'timer',serialize($arr));


                    //发送设备命令
                    $msg['DeviceID'] = $li['device_code'];

                    $this->sendMsg($msg);
                }
            }
            unset($ftime);
            unset($msg);
        }

        usleep(10);
        //创建下一次的缓存数据
        $this->makeCache(self::$timer_pre);

    }

    /**
     * [Timer 定时器]
     * 每天执行一次 每天 0:01运行 用于添加每天的任务
     */
    public function Timer_day()
    {
        // 重复的 开启状态的 已执行的 数据 重新开启
        $map['repeat']=1;
        $map['state']=1;
        $map['playstate']=1;

        $data['playstate']=0;
        M('task')->where($map)->save($data);
        $this->makeCache(self::$timer_pre);
    }

    /**
     * 信息发送
     * @param  [type] $msg [description]
     * @return [type] [description]
     */
    public function sendMsg($message)
    {
        if(isset($message['DeviceID'])){
            \GatewayClient\Gateway::sendToUid($message['DeviceID'], $message);
        }
    }

    /**
     * 获取缓存
     * @param  string $pre [前缀]
     * @return Array       [缓存数据]
     */
    public function getCache($pre='')
    {
        global $redis;
        if(empty($pre)) return '';

        $pre = $pre."*";

        $list = $redis->keys($pre);
        $items = [];
        for ($i=0; $i< count($list); $i++){
            $res = [];
            $res = $redis->hMGet($list[$i],['id','device_code','hour','minute','type']);
            $res['key'] = $list[$i];
            $items[]  =  $res;
//            $redis->delete($list[$i]);
        }
        return $items;
    }


    /**
     * @return array
     */
    public function get_device_no($did)
    {
        global $redis;
        global $db;

        $key= "device_no_".$did;
        if ($redis->hExists($key,'device_code')) {
            $device_code  = $redis->hget($key,'device_code');
        } else {
            $device_code = $db->select('device_code')->from('st_devices')->where('id='.$did)->single();
            $redis->hset($key,'device_code',$device_code);
        }
        return $device_code;
    }

    /**
     * [makeCache 创建未来一小时内要执行的任务]
     * @param  String $key [缓存的前缀]
     * @return [type]      []
     */
    public function makeCache($key='')
    {
        global $redis;
        global $db;
        if(empty($key)) return '';
        $time = time();
        $h=date('G',$time)+1;
        $h = $h>24 ? 1 : $h;

        $where=[
            'st_task.state=1',
            'st_task.playstate=0',
            "st_task.hour<$h",
        ];
        $page = $db->select('count(*) num')
            ->from('st_task')->innerJoin('st_devices','st_task.did=st_devices.id')
            ->where($where)
            ->row();
        $num = $page['num'];
        $p = 10000;
        $n = (int)ceil($num / $p);

        for ($i=1;$i<=$n;$i++){
            $data = $db->select('st_task.*,st_devices.device_code')
                ->from('st_task')->innerJoin('st_devices','st_task.did=st_devices.id')
                ->where($where)
                ->setPaging($p)->page($i)
                ->query();
            foreach ($data as $val){
                $k = self::$timer_pre.$val['id'];
                usleep(5);

                $redis->hMset($k,$val);
            }
        }
    }
}
