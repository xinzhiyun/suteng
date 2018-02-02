<?php
namespace Home\Controller;

class AddressController extends CommonController
{

    /**
     * [index 地址]
     * @return [type] [description]
     */
    public function index()
    {
        $address = D('Address');
        $data['uid'] = session('user.id');
        $data = $address ->where($data)->select();
        $assign = [
            'data' => $data,
        ];
        $this->assign($assign);
        $this->display();
    }

    /**
     * [newAddress 添加新地址]
     * @return [type] [description]
     */
    public function newAddress()
    {
        if(IS_POST){
            try {
                $address = D('Address');
                // 开启事务
                $address->startTrans();
                // 接收表单数据
                $postData = I('post.');
                // 数据验证
                $res = $address->create();
                // 数据验证不通过返回错误
                if(!$res) E($address->getError(),603);
                // 设置默认地址修改状态为：真
                $status = true;
                // 如果新增地址并设置默认地址
                if($postData['status']==0){
                    // 准备查询条件
                    $showData['uid'] = session('user.id');
                    $showData['status'] = 0;
                    // 查询是否有默认地址
                    $defaultAddress = $address->where($showData)->find();
                    
                    // 如果有默认地址
                    if($defaultAddress){
                        // 准备修改数据
                        $saveData['status'] = 1;
                        // 将原先默认地址修改为普通地址
                        $defaultRes = $address->where($showData)->save($saveData);
                        // 如果修改不成功
                        if(!$defaultRes){
                            // 将默认地址修改状态改为：假
                            E('默认地址设置失败',605);
                            $status = false;
                        }
                    }
                }else{
                    // 查询用户是否存在默认地址
                    // 准备查询条件
                    $showData['uid'] = session('user.id');
                    $showData['status'] = 0;
                    // 查询是否有默认地址
                    $defaultAddress = $address->where($showData)->find();
                    // 如果用户没有默认地址
                    if(!$defaultAddress){
                        // 更改地址状态为默认地址
                        $res['status'] = 0;
                    }
                }

                // 拼接详细地址
                $data['address'] = $postData['province'].$postData['city'].$postData['area'].$postData['town'].$postData['addressDetial'];
                // 获取用户ID
                $data['uid'] = session('user.id');
                // 数据合并
                $newData = array_merge($data,$res);
                // 添加新地址
                $res = $address->add($newData);

                // 新增地址和默认地址设置成功
                if($res && $status){
                    // 提交事务
                    $address->commit();
                    E('地址已添加',200);
                } else {
                    // 事务回滚
                    $address->rollback();
                    E('请重新添加',605);
                }
            } catch (\Exception $e) {
                $err = [
                    'code' => $e->getCode(),
                    'msg' => $e->getMessage(),
                ];
                $this->ajaxReturn($err);
            }
        } else {
            $this->display();
        }
    }

    // 更新默认地址
    public function edit_address()
    {
        try {
            $address = D('Address');
            $data['id'] = I('post.id');
            $where['uid'] = session('user.id');
            $status = 1;
            $address->startTrans();
            $res_status = $address->where($where)->save(['status'=>$status]);
            if(!$res_status) E('请刷新页面',603);
            $res = $address->where($data)->save(['status'=>0]);
            // dump($res);die;
            if($res){
                $address->commit();
                E('已修改默认地址',200);
            } else {
                $address->rollback();
                E('请重试',605);
            }
        } catch (\Exception $e) {
            $err = [
                'code' => $e->getCode(),
                'msg' => $e->getMessage(),
            ];
            $this->ajaxReturn($err);
        }
    }

    // 修改地址
    public function save_address()
    {
        try {
            // 实例化地址模型
            $address = D('Address');
            // 接收要修改的地址ID
            $whereData['id'] = I('post.id');
            // 接收表单数据
            // 数据验证
            $postData = $address->create();
            // 数据验证不通过返回错误
            if(!$postData) E($address->getError(),603);
            // 删除掉地址ID
            unset($postData['id']);          
            // 开启事务
            $address->startTrans();

            // 设置默认地址修改状态为：真
            $status = true;
            // 如果新增地址并设置默认地址
            if($postData['status']==0){
                // 准备查询条件
                $showData['uid'] = session('user.id');
                $showData['status'] = 0;
                // 查询是否有默认地址
                $defaultAddress = $address->where($showData)->find();
                
                // 如果有默认地址
                if($defaultAddress){
                    // 准备修改数据
                    $saveData['status'] = 1;
                    // 将原先默认地址修改为普通地址
                    $defaultRes = $address->where($showData)->save($saveData);
                    // 如果修改不成功
                    if(!$defaultRes){
                        // 将默认地址修改状态改为：假
                        E('默认地址设置失败',605);
                        $status = false;
                    }
                }
            }else{
                // 查询用户是否存在默认地址
                // 准备查询条件
                $showData['uid'] = session('user.id');
                $showData['status'] = 0;
                // 查询是否有默认地址
                $defaultAddress = $address->where($showData)->find();
                // 如果用户没有默认地址
                if(!$defaultAddress){
                    // 更改地址状态为默认地址
                    $postData['status'] = 0;
                }
            }

            // 修改地址信息
            $res = $address->where($whereData)->save($postData);

            if($res && $status){
                $address->commit();
                E('地址修改成功！',200);
            } else {
                $address->rollback();
                E('地址修改失败！',605);
            }
        } catch (\Exception $e) {
            $err = [
                'code' => $e->getCode(),
                'msg' => $e->getMessage(),
            ];
            $this->ajaxReturn($err);
        }
    }

    // 删除地址
    public function del_address(){
        if(IS_POST){
            try {
                $address = D('Address');
                $delData['id'] = I('post.id');
                $address->startTrans();
                $res = $address->where($delData)->delete();
                // print_r($res);die;
                if($res){
                    $address->commit();
                    E('地址删除成功了！',200);
                } else {
                    $address->rollback();
                    E('地址删除失败了！',603);
                }
            } catch (\Exception $e) {
                $err = [
                    'code' => $e->getCode(),
                    'msg' => $e->getMessage(),
                ];
                $this->ajaxReturn($err);
            }
        }
    }

    // 获取对于ID好
}


