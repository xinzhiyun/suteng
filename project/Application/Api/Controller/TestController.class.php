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
        file_put_contents('filename', 'data'."\n", FILE_APPEND);
    }

    // redis连接
    public function connectRedis()
    {
        $redis = new \Redis();
        $redis->connect('127.0.0.1',6379);
        $redis->set('test','PassWord@!');
        dump($redis->get(date('i',$time)));
    }
}