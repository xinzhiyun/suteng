<?php
namespace Home\Controller;
use Think\Controller;

class TaskController extends CommonController
{


    /**
     * [get_time 获取开机、关机、开启加热、关闭加热]
     * @return [type] [0:开机，1：关机,2：开启加热，3：关闭加热，不传参：获取全部]
     * @return：[repeat] [0：一次，1：每天]
     */
    public function set_time()
    {
        // dump($_SESSION);die;
        if(IS_POST){
            // 接收AJAX数据
            $data = I('post.');
           // var_dump($data);
            //exit;
            // 获取时间
            $time = $data['time'];
            // 从$data数数组中删除$data['time']
            unset($data['time']);
            // 切割时间
            $timeArr = explode(":",$time);
            // 添加小时
            $data['hour'] = $timeArr[0];
            // 添加分钟
            $data['minute'] = $timeArr[1];
            // 用户ID
            $data['uid'] = $_SESSION['user']['id'];
            // 准备查询当前设备条件
            $showDdevice['uid'] = $_SESSION['user']['id'];
            $showDdevice['status'] = 1;
            // 查询当前设备ID
            $data['did'] = M('user_device')->where($showDdevice)->find()['did'];
            $data['addtime'] = $data['updatetime'] = time();
            // 执行添加
            $addRes = M('task')->add($data);
            // 判断是否添加成功
            if($addRes){
                // 设置成功
                
                // 查询设备码
                $code = M('Devices')->where('id='.$data['did'])->getField('device_code');

                // 实例化redis
                $redis = new \Redis();
                // redis连接
                $redis->connect('127.0.0.1',6379);
                // 查询当前设备下的设置状态是否存在
                $res = $redis->exists($code[$data['type']]);

                // 判断当前设备的设置状态
                if($res){
                    // 获取
                    $value = $redis->get($code[$data['type']]);
                    var_dump($value);exit;
                    $arr[] = json_decode($value);
                    $arr[] = $data;
                }
                var_dump($code[$data['type']]);
                $redis->set($code[$data['type']],json_encode($data));

                 var_dump($redis->get($code[$data['type']]));die;
                // 设置返回提示码
                $data['code'] = 200;
                // 设置返回提示信息
                $data['msg']  = '定时任务设置成功！';
               // echo $redis->get();

            }else{
                echo 1;
                // 设置失败
                $data['code'] = 403;
                $data['msg']  = '定时任务设置失败！';
            }

            // 返回数据
            $this->ajaxReturn($data);
        }
    }



    /**
     * [get_time 获取开机、关机、开启加热、关闭加热]
     * @return [type] [0:开机，1：关机,2：开启加热，3：关闭加热，不传参：获取全部]
     */
    public function get_time()
    {
        $type = I('post.type');


        if(!empty($type)){
            $showData['type'] = $type;
        }
        // 准备查询当前设备条件
        $showDdevice['uid'] = $_SESSION['user']['id'];

        $showDdevice['status'] = 1;
        // 查询当前设备ID
        $showData['did'] = M('user_device')->where($showDdevice)->find()['did'];
        $showData['uid'] = $_SESSION['user']['id'];

        $res = M('task')->where($showData)->select();

        if($res){
            $data['code'] = 200;
            $data['msg']  = '数据获取成功！';
            $data['data'] = $res;
        }else{
            $data['code'] = 405;
            $data['msg']  = '暂时定时任务！';
            $data['data'] = null;
        }

        // 返回数据
        $this->ajaxReturn($data);
    }
    /*
     * [update 修改是否定时启动关机]
     */
    public function update()
    {
        //用户ID 筛选条件
        $where['uid'] = $_SESSION['user']['id'];
        //task表ID
        $where['id'] = I('post.id');
        // 查询这条记录状态
        $info = M('task')->where($where)->getField('state');
        //等于1 则修改禁用  //等于0 则启用
        if ($info == '1') {
           $update_info = M('task')->where($where)->setField('state',0);
           if ($update_info) {
               $message    = ['code' => 200, 'message' => '禁用成功!'];
           } else {
               $message    = ['code' => 403, 'message' => '禁用失败!'];
           }
        } else {
            $update_info = M('task')->where($where)->setField('state',1);
            if ($update_info) {
                $message    = ['code' => 200, 'message' => '启用成功!'];
            } else {
                $message    = ['code' => 403, 'message' => '启用失败!'];
            }
        }
        $this->ajaxReturn($message);
    }
    /**
     * [del 删除定时设置]
     * @return [type] [description]
     */
    public function del()
    {
        // 接收删除条件
        $delData['id'] = I('post.id');
        // $delData['id'] = 67;
        if($delData['id']){
            $res = M('task')->where($delData)->delete();
            if($res){
                $message    = ['code' => 200, 'message' => '删除成功!'];
            }else{
                $message    = ['code' => 403, 'message' => '删除失败，请刷新后重试!'];
            }
        }else{
            $message    = ['code' => 403, 'message' => '删除失败，请指定删除条件!'];
        }
        // dump($message);
        // 返回数据
        $this->ajaxReturn($message);
    }
}