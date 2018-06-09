<?php
namespace Home\Controller;

use Common\Tool\File;
use Think\Log;
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

            if (!isset($post['type'])) {
                E('请选择服务类型', 40102);
            } else {
                $data['type'] = $post['type'];
            }

            if ( empty($post['device_code']) ) {
                E('请确认设备信息',400022);
            }

            if (empty($post['kphone']) || empty($post['kname'])) {
                E('请确认联系方式',400022);
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

            $data['address'] = $post['address'];


            if(!empty($post['pic'])){
                if(is_string($post['pic'])){
                    $post['pic'] = explode(',',$post['pic']);
                }
                if( is_array($post['pic']) ){
                    foreach ($post['pic'] as $pic) {
                        $pics[] = File::downloadPic('work',$pic);
                    }
                }
                $data['pic'] = json_encode($pics);
            }

            $data['kname'] = $post['kname'];
            $data['kphone'] = $post['kphone'];

            $data['uid'] = session('user.id');

            $data['number'] = getWorkNumber();

            if($data['type']==0){
                $data['content'] = '新购设备-安装';
                $data['title']   = '设备安装';
                $data['install_id'] = $post['device_code'];
            }else{
                $data['device_code'] = $post['device_code'];
            }

            $data['is_examine'] = 0;
            $data['create_at']=time();
            $data['update_at']=time();
            $res = M('work')->add($data);

            if ($res) {
                if( $data['type']==0 && !empty($data['install_id']) ){
                    M('shop_order_device_install')->where('id='.$data['install_id'])->save(['status'=>1,'updatetime'=>time()]);
                }

                E('工单开启成功,请等待安装服务人员联系',200);
            }else{
                E('添加失败请重试',200);
            }

        } catch (\Exception $e) {
            $this->toJson($e);
        }
    }

    // 工单信息(时间线)
    public function showWorkTimeInfo()
    {
        try {
            $number = I('number');
            if( empty($number ) ) {
                E('请确认工单号',400022);
            }
            $info = M('work')->where('number='.$number)->find();
            if( empty($info ) ) {
                E('工单信息不存在',400022);
            }
            $list = M('work_note')->where('wid='.$info['id'])->order('id desc')->select();

            $this->toJson(['data'=>$list],'获取成功!');

        } catch (\Exception $e) {
            $this->toJson($e);
        }
    }
}


