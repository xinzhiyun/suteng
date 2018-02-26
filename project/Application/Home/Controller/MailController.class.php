<?php
namespace Home\Controller;
use \Org\Util\WeixinJssdk;

/**
 * 站内信
 */
class MailController extends CommonController
{
    /**
     * [get_announcement 获取公告信息]
     * @return [type] [description]
     */
    public function get_announcement()
    {
        // 类型公告
        $map['type'] = 0;
        // 状态启用
        $map['status'] = 0;
        // 查询公告
        $data = M('mail')->where($map)->select();

        if($data){
            $message    = ['code' => 200, 'message' => '公告信息查询成功!', 'data' => $data];
        }else{
            $message    = ['code' => 403, 'message' => '暂无公告信息!'];
        }

        // 返回JSON格式数据
        $this->ajaxReturn($message);
    }

    /**
     * [count_mail 未读站内信条数]
     * @return [type] [description]
     */
    public function count_mail()
    {
        // 用户标识
        $map['recipients_code'] = $_SESSION['user']['code'];
        // 类型站内信
        $map['type'] = 1;
        // 未读状态
        $map['status'] = 2;
        // 查询公告
        $data = M('mail')->where($map)->select();

        if($data){
            $message    = ['code' => 200, 'message' => '站内信查询成功!', 'num' => count($data)];
        }else{
            $message    = ['code' => 403, 'message' => '暂无站内信!', 'num' => 0];
        }

        // 返回JSON格式数据
        $this->ajaxReturn($message);
    }


    /**
     * [get_announcement 获取用户站内信]
     * @return [type] [description]
     */
    public function get_mail()
    {
        // 用户标识
        $map['recipients_code'] = $_SESSION['user']['code'];
        // 类型站内信
        $map['type'] = 1;

        // 查询公告
        $data = M('mail')->where($map)->select();

        if($data){
            $message    = ['code' => 200, 'message' => '站内信查询成功!', 'data' => $data];
        }else{
            $message    = ['code' => 403, 'message' => '暂无站内信!'];
        }

        // 返回JSON格式数据
        $this->ajaxReturn($message);
    }

    /**
     * [reading_mail 用户读取信件时修改状态]
     * @return [type] [description]
     */
    public function reading_mail()
    {
        if(IS_POST){
            // 站内信ID
            $map['id'] = I('post.id');
            // 修改状态
            $saveData['status'] = 3;

            $res = M('mail')->where($map)->save($saveData);

            if($res){
                $message    = ['code' => 200, 'message' => '站内信状态修改成功!'];
            }else{
                $message    = ['code' => 403, 'message' => '站内信状态修改失败!'];
            }

            // 返回JSON格式数据
            $this->ajaxReturn($message);
        }
    }

    /**
     * [del_mail 用户删除信息时回收]
     * @return [type] [description]
     */
    public function del_mail()
    {
        if(IS_POST){
            // 站内信ID
            $map['id'] = I('post.id');
            // 修改状态
            $saveData['status'] = 4;

            $res = M('mail')->where($map)->save($saveData);

            if($res){
                $message    = ['code' => 200, 'message' => '站内信状态修改成功!'];
            }else{
                $message    = ['code' => 403, 'message' => '参数错误!'];
            }

            // 返回JSON格式数据
            $this->ajaxReturn($message);
        }
    }

    /**
     * [noe_mail 获取指定一条站内信]
     * @return [type] [description]
     */
    public function noe_mail()
    {
        if(IS_POST){
            // 站内信ID
            $map['id'] = I('post.id');

            // 查询公告
            $data = M('mail')->where($map)->find();

            if($data){
                $message    = ['code' => 200, 'message' => '站内信查询成功!', 'data' => $data];
            }else{
                $message    = ['code' => 403, 'message' => '暂无站内信!'];
            }

            // 返回JSON格式数据
            $this->ajaxReturn($message);
        }
    }
}


