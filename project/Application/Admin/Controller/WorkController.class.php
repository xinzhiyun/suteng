<?php
namespace Admin\Controller;
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
        $map = '';
        if(!empty($_GET['name'])) $map['name'] = array('like',"%{$_GET['name']}%");

        $work = D('work');
        $workList = $work->getPage($work,$map,'create_at desc');

        $this->assign('pageList',$workList);
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
     * 添加工单
     */
    public function add()
    {
        if (IS_AJAX) {
            $data['uid']        = session('adminInfo.id');
            $data['title']      = I('post.title');
            $data['type']       = I('post.type');
            $data['content']    = I('post.content');
            $data['address']    = I('post.address');
            $data['number']     = $this->getWorkNumber();
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


    /**
     * 生成工单编号
     * @return [type] [description]
     */
    protected function getWorkNumber()
    {
        $date = new Date(time());
        $string = new Strings;
        $dateStr = $date->format("%Y%m%d%H%M");   // 根据时间戳生成的字符串
        $str1 = $string->randString(3,0);   // 生成字母随机字符
        $str2 = $string->randString(5,1);   // 生成数字随机字符
        $workNumber = $str1.$dateStr.$str2;
        return $workNumber;
    }
}
