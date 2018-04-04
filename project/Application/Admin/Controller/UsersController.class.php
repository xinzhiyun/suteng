<?php
namespace Admin\Controller;
use Think\Controller;

/**
 * 前台用户控制器
 * 
 * @author 潘宏钢 <619328391@qq.com>
 */

class UsersController extends CommonController 
{
	/**
     * 前台用户列表
     * @author 潘宏钢 <619328391@qq.com>
     */
    public function index()
    {	
       // 根据名称进行搜索
        $map = '';
        if(strlen($_GET['nickname'])) $map['nickname'] = array('like',"%{$_GET['nickname']}%");

        strlen(I('get.grade')) ? (int)$map['grade'] = I('get.grade'):'';

        strlen(I('get.sex')) ? (int)$map['sex'] = I('get.sex'):'';

        $user = D('users');
        $total = $user->where($map)->count();
        $page  = new \Think\Page($total,8);
        page_config($page);
        $pageButton =$page->show();

        $userlist = $user->where($map)->limit($page->firstRow.','.$page->listRows)->select();

        $assign = [
            'data' => $userlist,
            'button' => $pageButton,
        ];
        $where['nickname'] = $_GET['nickname'];
        $where['grade'] = I('get.grade');
        $where['sex'] = I('get.sex');
        $this->assign($assign);
        $this->assign('where',$where);
        $this->display();
    }

    /**
     * 推送信息方法
     * @author 潘宏钢 <619328391@qq.com>
     */
    public function message($id)
    {   
        if (IS_POST) {
 
            // 接收数据
            // 尊敬的${content}，测试喝水建议。
            $phone = $_POST['phone'];
            $content = '用户' . $_POST['name'] . '您好！'.$_POST['content'];

            // 开始接口代码
            $sms = new \Org\Util\SmsDemo;
            $response = $sms::sendSms(
                "阿里云短信测试专用", // 短信签名
                "SMS_112475574", // 短信模板编号
                $phone, // 短信接收者
                Array(  // 短信模板中字段的值
                    "content"=>$content,
                    "product"=>"dsd"
                ),
                "123"   // 流水号,选填
            );

            // 信息推送状态判断
            if($response->Code=='OK'){
                $this->error('消息推送成功！');
            }else{
                $this->error('消息推送失败，错误码：' . $response->Code);
            }

        }else{
            $user = M('users');
            $userinfo = $user->where('id='.$id)->select();
            $this->assign('list',$userinfo);
            $this->display();
        }
    }

    public function user_info()
    {
        $map['id'] = I('get.id');

        // 用户信息
        $user = M('users')->where($map)->find();

        $maps['uid'] = $user['id'];
        // 充值记录
        $flow = M('flow')->where($maps)->find();

        // 消费记录
        $consume = M('consume')->where($maps)->find();
        $assign = [
            'user' => json_encode($user),
            'flow' => json_encode($flow),
            'consume' => json_encode($consume),
        ];
        $this->assign($assign);
        $this->display();

    }


    /**
     * 编辑用户方法
     * @author 潘宏钢 <619328391@qq.com>
     */
    public function edit($id,$status)
    {
        $users = M("users");
        $data['status'] = $_GET['status'];
        $res = $users->where('id='.$id)->save($data); 
        if ($res) {
             $this->redirect('users/index');
        } else {
            $this->error('修改失败啦！');
        }
    }

    /**
     * 用户充值流水列表
     * @author 潘宏钢 <619328391@qq.com>
     */
    public function flow()
    {
        // 根据用户昵称进行搜索
        $map = '';
        if(!empty($_GET['name'])) $map['name'] = array('like',"%{$_GET['name']}%");

        $flow = M('flow');
        $total = $flow->where($map)
                                ->join('st_users ON st_flow.user_id = st_users.id')
                                ->field('st_flow.*,st_users.nickname,st_users.balance')
                                ->count();
        $page  = new \Think\Page($total,8);
        $pageButton =$page->show();

        $list = $flow->where($map)->limit($page->firstRow.','.$page->listRows)
                                ->join('st_users ON st_flow.user_id = st_users.id')
                                ->field('st_flow.*,st_users.nickname,st_users.balance')
                                ->select();
        // dump($list);die;
        $this->assign('list',$list);
        $this->assign('button',$pageButton);
        $this->display();        
    }

    /**
     * 用户消费记录列表
     * @author 潘宏钢 <619328391@qq.com>
     */
    public function consume()
    {
        // 根据用户昵称进行搜索
        $map = '';
        if(!empty($_GET['name'])) $map['name'] = array('like',"%{$_GET['name']}%");

        $consume = M('consume');
        $total = $consume->where($map)
                                ->join('st_users ON st_consume.uid = st_users.id')
                                // ->join('st_card ON st_consume.icid = st_card.id')
                                ->field('st_consume.*,st_users.name')
                                ->count();
        $page  = new \Think\Page($total,8);
        $pageButton =$page->show();

        $list = $consume->where($map)->limit($page->firstRow.','.$page->listRows)
                                ->join('st_users ON st_consume.uid = st_users.id')
                                // ->join('st_card ON st_consume.icid = st_card.id')
                                ->field('st_consume.*,st_users.name,st_users.balance')
                                ->select();
        // dump($list);die;
        $this->assign('list',$list);
        $this->assign('button',$pageButton);
        $this->display();        
    }   

    // 用户详情
    public function user_detail()
    {
        $user = D('Users');
        $map['u.id'] = $where['id'] = I('get.id');

        // 当前用户信息

        $dev = D('UserDevice')
            ->where(['st_user_device.uid'=>I('get.id')])
            ->join('st_devices ON st_user_device.did = st_devices.id','LEFT')
            ->select();
        $flow = D('flow')
            ->where(['st_flow.user_id'=>I('get.id')])
            ->join('st_orders ON st_flow.order_id = st_orders.device_id','LEFT')
            ->field('st_flow.*,st_orders.device_id')
            ->select();

        // 用户绑定信息
        $assign = [
            'uInfo' => $dev,
            'bInfo' => $flow,
        ];
        $this->assign($assign);
        $this->display('userDetail');
    }


    // 设备解绑
    public function unBind()
    {
        try {
            $user = D("Users");
            $where['device_code'] = I('post.device_code');
            $data['uid'] = I('post.uid');
            $res = $user->unBind($where,$data);
            if($res){
                E('解绑成功',200);
            } else {
                E('解绑失败',603);
            }
        } catch (\Exception $e) {
            $err = [
                'code' => $e->getCode(),
                'msg' => $e->getMessage(),
            ];
            $this->ajaxReturn($err);
        }
    }

    public function user_upgrade()
    {
        // 查询标准会员升级钻石会员条件
        $user =  M('config')->where('id=1')->field('user_upgrade')->find();

        if(IS_POST){
            // 接收表单数据
            $data = I('post.');


            // 实例化验证类
            $validate   = new \Org\Util\Validate;
            // 判断A级分销商加盟费是否修改
            if($data['user_upgrade'] != $user['user_upgrade']){
                // 验证是加盟费格式
                if($validate->original('/^[\d]{1,18}[\.][\d]{2}$/',$data['user_upgrade'])){
                    $saveData['user_upgrade'] = $data['user_upgrade'];

                    $res = M('config')->where('id=1')->save($saveData);

                    if($res){
                        $message = ['code' => 200, 'message' =>'升级条件充值额度设置成功！'];
                    }else{
                        $message = ['code' => 403, 'message' =>'升级条件充值额度修改失败，请重试！'];
                    }

                }else{
                    $message = ['code' => 403, 'message' =>'升级条件充值额度格式试不正确，请检测！'];
                }
            }else{
                $message = ['code' => 403, 'message' =>'您没有修改升级条件额度！'];
            }

            // 返回JSON格式数据
            $this->ajaxReturn($message);  
        }else{
            // dump($data);die;
            $this->assign('data',$user);
            $this->display('user_upgrade');
        }
    }
}
