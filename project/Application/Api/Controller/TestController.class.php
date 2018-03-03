<?php
namespace Api\Controller;
use Think\Controller;
use Think\Log;
class TestController extends Controller
{
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
}