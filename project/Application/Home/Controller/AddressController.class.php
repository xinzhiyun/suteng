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
            'data' => json_encode($data),
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
        try {
            $address = D('Address');
            $data = I('post.');
            $res = $address->create();
            if(!$res) E($address->getError(),603);
            $res = $address->add();
            if($res){
                E('地址已添加',200);
            } else {
                E('请重新添加',605);
            }
        } catch (\Exception $e) {
            $err = [
                'code' => $e->getCode(),
                'msg' => $e->getMessage(),
            ];
            $this->ajaxReturn($err);
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
            if($res){
                E('已修改默认地址',200);
            } else {
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
}


