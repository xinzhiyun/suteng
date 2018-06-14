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

            //工单类型(0：安装 1：维修 2：维护)
            $content_pre = ['1'=>'设备维修','2'=>'设备维护'];
            $data['content'] = $content_pre[$data['type']] .'-设备:'.$post['device_code'].'<br/>'.$post['content'];

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
                    $post['pic'] = explode(',', $post['pic'] );
                }
                if( is_array($post['pic']) ){
                    $data['pic'] = json_encode($post['pic']);
                }
            }

            $data['kname'] = $post['kname'];
            $data['kphone'] = $post['kphone'];

            $data['uid'] = session('user.id');

            $data['number'] = getWorkNumber();


            $data['device_code'] = $post['device_code'];
            // device_type
            $type = M('devices')->where('device_code='.$post['device_code'])->getField('type_id');
            $device_type = M('type')->where('id='.$type)->getField('typename');

            $data['device_type'] = $device_type;
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

            $data['type'] = 0;

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

            $data['content'] = '新购设备-安装 型号:'.$post['device_type'];
            $data['title']   = '设备安装';

            $data['device_code'] = $post['device_code'];
            $data['device_type'] = $post['device_type'];

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

    // 图片上传
    public function picUpload()
    {
        $pic = I('pic');
        if(!empty($pic)){
            if(is_string($pic)){
                $pic = explode(',',$pic);
            }
            if( is_array($pic) ){
                foreach ($pic as $p) {
                    $pics[] = File::downloadPic('work',$p);
                }
            }
            $data['pic'] = json_encode($pics);
            $this->toJson(['data'=>$data],'上传成功!',200);
        }else{
            $this->toJson(['data'=>[]],'无图片上传!',400001);
        }
    }
}


