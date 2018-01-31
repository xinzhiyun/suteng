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
        $uid = session('user.id');
        $address ->where('uid='.$uid)->select();
        $this->display();
    }

    /**
     * [newAddress 添加新地址]
     * @return [type] [description]
     */
    public function newAddress()
    {
        $this->display();
    }
}


