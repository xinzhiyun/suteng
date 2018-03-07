<?php
namespace Api\Controller;
use Think\Controller;
use Think\Log;
use Org\Util\Gateway;
use Workerman\Lib\Timer;

// 定时器
class TestController extends Controller
{
    public function __construct()
    {
        Gateway::$registerAddress = '127.0.0.1:6504';
    }

    // 执行定时器
    public function action()
    {
        // Log::write('计划任务','action');
        $redis = new \Redis();
        $redis->connect('127.0.0.1',6379);
        $res = $redis->keys('*');
        file_put_contents('filename', json_encode($res)."\n", FILE_APPEND);
    }

    // redis连接
    public function connectRedis()
    {
        $redis = new \Redis();
        $redis->connect('127.0.0.1',6379);
        // $redis->set('test','PassWord@!');
        // dump(date('i',time()));
        // echo $redis->get(date('i',time()));
        $res = $redis->keys('*');
        dump($res);
    }

    // Gatewayworker 定时器
    public function setTime()
    {
        Timer::add(10, function(){
            echo "timer\n";
        });
    }
}