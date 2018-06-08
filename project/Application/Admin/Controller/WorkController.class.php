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
            if($data['is_examine'] ==2){
                $savetata['result']=9;
            }else{
                $savetata['result']=0;
            }
            $savetata['is_examine'] = $data['is_examine'];
            $res = M('work')->where($map)->save($savetata);
            if($res) {
                E('修改成功',200);
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

        if (IS_AJAX) {
            $id = I('post.id');

            $data['result']      = I('post.result');
            $data['dw_uid']     = session('adminInfo.id');
                        if ($data['result'] == 1) {
                $data['name']       = I('name');
                $data['phone']      = I('phone');
            }
            $data['time']  = time();

            $device_type = D('work');
            $info = $device_type->create();

            if($info){
                $res = $device_type->where(['id'=>$id])->save($data);
                if ($res) {
                    return $this->ajaxReturn([
                        'code' => 200,
                        'msg' => '工单修改成功啦！！！',
                        'status' => 'success'
                    ]);
                } else {
                    return $this->ajaxReturn([
                        'code' => 410,
                        'msg' => '工单修改失败',
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
     * 删除类型方法（废除）
     * 不做删除，只做隐藏，如果要做删除产品类型，请确保产品类型没有被设备所用
     *
     * @author 潘宏钢 <619328391@qq.com>
     */
    public function del()
    {

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

            $work_res = M('work')->where('id='.$wid)->save( ['sid'=>$sid,'service_mode'=>$service_mode] );

            if($work_res){
                // 写工单记录
                Work::add($wid, 3);
            }
        }
    }

}
