<?php
namespace Admin\Controller;
use Common\Tool\Work;
use Org\Util\Date;
use Org\Util\Strings;
/**
 * 工单控制器
 * 用来添加工单，浏览工单列表等
 *
 * @author Yi <675218529@qq.com>
 */

class WorkController extends CommonController
{
	/**
     * 工单列表
     *
     * @author Yi <675218529@qq.com>
     */
    public function index()
    {
       // 根据名称进行搜索
        $map = [];
        if(strlen($_GET['number'])) $map['number'] = array('like',"%{$_GET['number']}%");

        strlen(I('get.type')) ? (int)$map['type'] = I('get.type'):'';

        strlen(I('get.result')) ? (int)$map['result'] = I('get.result'):'';
        strlen(I('get.is_examine')) ? (int)$map['is_examine'] = I('get.is_examine'):'';
        // $map['leavel'] = $this->leavel;
        $work = D('work');
        $workList = $work->getPage($work,$map,'create_at desc');
        $where['number'] = $_GET['number'];
        $where['type'] = I('get.type');
        $where['result'] = I('get.result');
        $where['is_examine'] = I('get.is_examine');

        $area = M('area')->where('parentid=0')->select();

        $this->assign('area',$area);
        $this->assign('pageList',$workList);
        $this->assign('where',$where);
        $this->display();
    }

    public function getWork($id)
    {
        if (!$id) {
            $message = ['code' => 0,
                        'msg' => '参数错误',
                        'status' => 'error'
                        ];
            $this->ajaxReturn($message);
        }
        $work = D('work');
        $data = $work->getWorkInfoByID($id);
                $data = $work->getAll($data);
                // dump($workList);die;
        if ($data) {
            $message = ['code' => 10,
                        'data' => $data,
                        'status' => 'success'
                    ];
        }
        $this->ajaxReturn($message);
    }

    /**
     *  添加工单
     */
    public function add()
    {
        if (IS_AJAX) {
            $data['uid']        = session('adminInfo.id');
            $data['title']      = I('post.title');
            $data['type']       = I('post.type');
            $data['content']    = I('post.content');
            $data['province']       = I('post.province');
            $data['city']       = I('post.city');
            $data['district']       = I('post.district');
            $data['address']    = I('post.address');
            $data['number']     = getWorkNumber();
            $data['create_at']  = time();

            $device_type = D('work');
            $info = $device_type->create();

            if($info){
                $res = $device_type->addData($data);
                if ($res) {
                    return $this->ajaxReturn([
                        'code' => 200,
                        'msg' => '添加成功'.time(),
                        'status' => 'success'
                    ]);
                } else {
                    return $this->ajaxReturn([
                        'code' => 410,
                        'msg' => '添加失败',
                        'status' => 'error'
                    ]);
                }
            } else {
                // getError是在数据创建验证时调用，提示的是验证失败的错误信息
                return $this->ajaxReturn([
                        'code' => 400,
                        'msg' => $device_type->getError(),
                        'status' => 'error'
                    ]);
            }
        }else{
            $this->display();
        }
    }

    /**
     * 审核工单
     */
    public function examine()
    {
        try {
            $data = I('post.');

            if(empty($data['id']) || empty($data['is_examine'])){
                E('数据错误',40002);
            }
            $map['id'] = $data['id'];

            $info = M('work')->where($map)->find();

            if(empty($info)){
                E('工单不存在!',201);
            }
            if($data['is_examine'] ==2){
                $savetata['result']=9;
            }else{
                $savetata['result']=0;
            }
            $savetata['is_examine'] = $data['is_examine'];
            $savetata['examine_at'] = time();
            $res = M('work')->where($map)->save($savetata);
            if($res) {
                if($data['is_examine']==1){
                    Work::add($data['id'],'2');
                }else{
                    Work::add($data['id'],'3');
                }

                // 分配工单
                if($data['is_examine'] ==1){
                    if( !$this->autoService($data['id']) ){
                        E('审核成功,自动派遣失败,请手动派遣',200);
                    }
                }

                E('审核完成',200);
            } else {
                E('修改失败,请重试!',201);
            }

        } catch (\Exception $e) {
            $this->toJson($e);
        }
    }

    /**
     * 修改工单处理状态
     * @param  [type] $id     [description]
     * @param  [type] $result [description]
     * @return [type]         [description]
     */
    public function edit()
    {
        try {
            $post = I('post.');
            $id = $post['id'];

            if(empty($post['action']) || empty($post['id'])){
                E('参数错误',40001);
            }
            //处理结果(0：待处理(服务站) 1处理中(工作人员) 2已完成(工作人员) 3完成 (待客户评价) 4 评价完成   9 工单关闭 )
            if($post['action']=='close'){//关闭
                $data['result']=9;
            } else if ($post['action']=='over'){ // 完成
                $data['result']=3;
            } else {
                E('参数错误',40002);
            }

            $device_type = D('work');
            $res = $device_type->where(['id'=>$id])->save($data);

            if($res){
                $status = [
                    '9'=>99,// 关闭
                    '3'=>8
                ];
                if(in_array($data['result'], $status)){
                    Work::add($post['id'], (string)$status[$data['result']]);
                }

                E('修改成功',200);
            }else{
                E('修改失败',40010);
            }


//        if($info){
//            $res = $device_type->where(['id'=>$id])->save($data);
//            if ($res) {
//                return $this->ajaxReturn([
//                    'code' => 200,
//                    'msg' => '工单修改成功啦！！！',
//                    'status' => 'success'
//                ]);
//            } else {
//                return $this->ajaxReturn([
//                    'code' => 410,
//                    'msg' => '工单修改失败',
//                    'status' => 'error'
//                ]);
//            }
//        } else {
//            // getError是在数据创建验证时调用，提示的是验证失败的错误信息
//            return $this->ajaxReturn([
//                    'code' => 400,
//                    'msg' => $device_type->getError(),
//                    'status' => 'error'
//                ]);
//        }

        } catch (\Exception $e) {
            $this->toJson($e);
        }

    }

    //指定派遣
    public function setService()
    {
        try {
            $post = I('post.');
            if( empty($post['wid']) ) {
                E('参数数据错误,请刷新重试',400001);
            }

            $work_data =  M('work')->where('id='.$post['wid'])->find();
            if( empty($work_data) ){
                E('工单不存在!',400002);
            }

            if(!empty($post['sid'])){
                $service = M('service')->where('id='.$post['sid'])->find();
                if( empty($service) ){
                    E('服务站不存在!',400003);
                }
                $sid = $post['sid'];
                $name = $service['company'];
                $service_mode = 0;

                if ($service['status'] >1 ) {
                    $sid = $service['t_id'];
                    $name = $service['t_company'];
                }

                // 第三方 服务商 待客户需求
                if ($service['status'] == 3) {
                    $service_mode = 1;
                }
                $saveData=[
                    'sid'=>$sid,
                    'service_mode'=>$service_mode,
                    'company'=>$name,
                    'leaflets_at'=>time()
                ];
                $res =  M('work')->where('id='.$post['wid'])->save($saveData);
                Work::add($post['wid'], '4');

            }
            if(!empty($post['suid'])){
                $user = M('service_users')->where('id='.$post['suid'])->find();
                if( empty($user) ){
                    E('该人员不存在!',40002);
                }
                $saveData=[
                    'dw_uid'=>$post['suid'],
                    'name'=>$user['name'],
                    'sn'=>$user['sn'],
                    'phone'=>$user['phone'],
                    'result'=>1,
                    'anry_at'=>time(),
                    'refuse' => 0,
                    'anry_period'=>$post['anry_period'],
                    'anry_time'=>$post['anry_time']
                ];
                $res =  M('work')->where('id='.$post['wid'])->save($saveData);
                Work::add($post['wid'], '5');
            }

            if($res){
                E('派遣成功!',200);
            }else{
                E('派遣失败,请重试!',400010);
            }
        } catch (\Exception $e) {
            $this->toJson($e);
        }
    }


    // 自动派遣
    public function autoService($wid)
    {
        $work_data =  M('work')->where('id='.$wid)->find();

        if(!empty($work_data) && empty($work_data['sid'])){
            $map['province_id'] = $work_data['province_id'];
            $map['city_id'] = $work_data['city_id'];
            $map['district_id'] = $work_data['district_id'];

//            $map['status'][] = ['GT',0];
//            $map['status'][] = ['LT',4];
            $service = M('service')->where($map)->find();


            if(empty($service)){
                return false;
            }
            //状态{0:未开通 1:已开通 2:被代管(服务站代管) 3:被代管(第三方代管) 4:被关闭 }
            if($service['status']==0 || $service['status']==4 ){
                return false;
            }
            $sid = $service['id'];
            $name = $service['company'];
            $service_mode = 0;

            if ($service['status'] >1 ) {
                $sid = $service['t_id'];
                $name = $service['t_company'];
            }

            if ($service['status'] == 3) {
                $service_mode = 1;
            }

            $work_res = M('work')->where('id='.$wid)->save( ['sid'=>$sid, 'service_mode'=>$service_mode, 'company'=>$name ,'leaflets_at'=>time()] );

            if($work_res){
                // 写工单记录
                return  Work::add($wid, '4');
            }
        }
    }

}
