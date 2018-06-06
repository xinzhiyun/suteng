<?php
namespace Home\Controller;

use Zend\Serializer\Adapter\AdapterInterface;

class WorkController extends CommonController
{
    /**
     * 发起安装工单
     */
    public function workAdd()
    {
        try {
            $post = I('post.');
//            if (empty($post['sid'])) {
//                E('请选择服务站', 40103);
//            }
            if (!isset($post['type'])) {
                E('请选择服务类型', 40102);
            } else {
                $data['type'] = $post['type'];
            }
            if($data['type']==0){
                if(empty($post['install_id']))E('请选择设备进行安装', 40102);
            }



            if (!empty($post['install_id'])) {
                $data['install_id'] = $post['install_id'];
            }

            if (empty($post['province']) ||
                empty($post['city']) ||
                empty($post['district']) ||
                empty($post['province_id']) ||
                empty($post['city_id']) ||
                empty($post['district_id']) ||
                empty($post['address'])
            ) {
                E('请重新检查地址信息',400022);
            }

            $data['province'] = $post['province'];
            $data['city'] = $post['city'];
            $data['district'] = $post['district'];

            $data['province_id'] = $post['province_id'];
            $data['city_id'] = $post['city_id'];
            $data['district_id'] = $post['district_id'];

            $data['address'] =$post['address'];

            $data['number'] = getWorkNumber();

            if($data['type']==0){
                $data['content'] = '新购设备-安装';
                $data['title']   = '设备安装';
            }
            $data['create_at']=time();
            $data['update_at']=time();
            $res = M('work')->add($data);

            if ($res) {

                E('工单开启成功,请等待安装服务人员联系',200);
            }

        } catch (\Exception $e) {
            $this->toJson($e);
        }
    }

//    /**
//     *  获取用户的设备安装列表
//     */
//    public function getDeviceInstallList()
//    {
//        $map['uid'] = session('user.id');
//        $map['status'] = I('status',0);
//        $list = M('shop_order_device_install')->where($map)->select();
//
//        $this->toJson(['data'=>$list],'获取成功',200);
//    }
    
}   


