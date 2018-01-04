<?php
namespace Admin\Controller;

use Think\Controller;

class VendorController extends CommonController
{
    public function index()
    {

        $this->display();
    }

    // 经销商列表
    public function vendor_list()
    {
        $data = M('vendors')->where("parent_code='{$_SESSION['adminInfo']['code']}'")->order('updatetime desc')->select();
        $assign = [
            'data' => $data,
        ];
        $this->assign($assign);
        $this->display();
    }

    // 经销商审核列表
    public function vendor_reviewed()
    {
        $map['parent_code'] = 156345;
        $map['status'] = 0;
        $parent_user = M('vendors')->where("code=".$map['parent_code'])->getField('user');
        $data = M('vendors')->where($map)->select();
        $assign = [
            'data' => $data,
            'parent_user' => $parent_user,
        ];
        $this->assign($assign);
        $this->display();
    }

    // 审核
    public function reviewed()
    {
        $id = I('post.');
        $data = ['status' => 1, 'updatetime' => time()];
        $res = M('vendors')->where($id)->save($data);
        if($res){
            $message = ['code' => 200, 'message' => 'OK'];
        } else {
            $message = ['code' => 403, 'message' => '审核失败'];
        }
        $this->ajaxReturn($message);
    }

    // 经销商添加
    public function vendor_add(){
        if(IS_POST){
            $users_data = I('post.');
            // 验证数据不能为空
            $res = $this->check_empty($users_data);
            if(!empty($res)) $this->error($res);
            // 检验添加的有效信息
            if($this->check_vendor_addinfo($users_data)) $this->error('该经销商已经注册过了');
            // 验证密码是否一致
            if($users_data['password'] != $users_data['repassword']) $this->error('两次密码不一致，请重新输入');
            // 随机生成经销商校验码
            $vendor_code = substr(md5(mt_rand(100000, 999999)), mt_rand(1,9), 6);
            $parent_code = $this->check_parent_code(['user' => $users_data['parent_vendor']]);
            if(!is_array($parent_code)) $this->error($parent_code);
            $data = $this->action_add_vendor($users_data, $parent_code, $vendor_code);
            // dump($data);die;
            $bool = M('vendors')->add($data);
            if($bool){
                $this->success('申请已经提交，待审核中');
                return false;
            }else{
                $this->error('提交失败了！');
                return false;
            }
        }
        $this->display();
    }

    // 简单的验证一下不能有空的数据
    public function check_empty($data)
    {
        if(is_array($data)){
            foreach ($data as $key => $value) {
                if(empty($value))
                {
                    return "所有的信息必须填完";
                }
            }
        }
    }

    // 查询上级经销商
    public function check_parent_code($parent)
    {
        $res = M('vendors')->where($parent)->find();
        if(is_null($res)) return "参数错误";
        return $res;
    }

    // 整理经销商添加的数据
    public function action_add_vendor($data, $parent_code, $vendor_code)
    {
        $leavel = $parent_code['leavel'] + 1;
        if($leavel >= 3){
            $this->error('无法添加分销商');
            return false;
        }
        $res = [
            'name'        => $data['name'],
            'phone'       => $data['phone'],
            'telphone'    => $data['telphone'],
            'idcard'      => $data['idcard'],
            'parent_code' => $parent_code['code'],
            'code'        => $vendor_code,
            'leavel'      => $leavel,
            'user'        => $data['user'],
            'password'    => md5($data['password']),
            'addtime'     => time(),
            'updatetime'  => time(),
        ];

        return $res;
    }

    // 验证添加的经销商是否有重复添加的行为
    public function check_vendor_addinfo($add_info)
    {
        // 逻辑暂时为空
        $res = M('vendors')->where("idcard='{$add_info['idcard']}'")->getField('idcard');
        return $res;
    }
}