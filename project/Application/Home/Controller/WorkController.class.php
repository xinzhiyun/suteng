<?php
namespace Home\Controller;

use Common\Tool\File;
use Common\Tool\Work;
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


            $data['device_code'] = $post['device_code'];


            $data['is_examine'] = 0;
            $data['create_at']=time();
            $data['update_at']=time();
            $res = M('work')->add($data);

            if ($res) {

                Work::add($res,1);

                E('工单开启成功,请等待安装服务人员联系',200);
            }else{
                E('添加失败请重试',200);
            }

        } catch (\Exception $e) {
            $this->toJson($e);
        }
    }

    /**
     * 发起安装工单
     */
    public function workAddInatall()
    {
        try {
            $post = I('post.');

            $data['type'] = 1;

            if ( empty($post['device_code']) ) {
                E('请确认设备编码',400022);
            }
            if ( empty($post['device_type']) ) {
                E('请确认设备型号',400023);
            }

            if (empty($post['kphone']) || empty($post['kname'])) {
                E('请确认联系方式',400022);
            }

            if (empty($post['anry_time']) || empty($post['anry_period'])) {
                E('请确认预约时间',400022);
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

            $data['kname'] = $post['kname'];
            $data['kphone'] = $post['kphone'];

            $data['uid'] = session('user.id');

            $data['number'] = getWorkNumber();

            $data['anry_time'] = $post['anry_time'];
            $data['anry_period'] = $post['anry_period'];

            $data['content'] = '新购设备-安装 型号:';
            $data['title']   = '设备安装';

            $data['device_code'] = $post['device_code'];

            $data['is_examine'] = 0;
            $data['create_at']=time();
            $data['update_at']=time();
            $res = M('work')->add($data);

            if ($res) {
                Work::add($res,1);
                E('工单开启成功,请等待安装服务人员联系',200);
            }else{
                E('添加失败请重试',200);
            }

        } catch (\Exception $e) {
            $this->toJson($e);
        }
    }
    // 获取评价控制
    public function getEvaluate()
    {
        $evaluate =  M('service_evaluate');
        $eval = $evaluate->where('pid=0')->select();
        $list=[];
        foreach ($eval as $e){
            $data = $evaluate->where('pid='.$e['id'])->select();
            $list[$e['star']][]=$data;
        }
        $this->toJson(['data'=>$list],'获取成功!');
    }
}


