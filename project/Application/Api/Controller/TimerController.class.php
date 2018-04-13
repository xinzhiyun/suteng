<?php
namespace Api\Controller;
use Think\Controller;
use Think\Log;

/**
 * 定时器
 * @package Api\Controller
 */
class TimerController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        global $redis;
        $redis = new \Redis();
        $redis->connect('127.0.0.1',6379);
    }




}