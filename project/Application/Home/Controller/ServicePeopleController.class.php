<?php
namespace Home\Controller;
use \Org\Util\WeixinJssdk;
use Think\Controller;
use Common\Tool\Work;
use Common\Tool\Weixin;
/**
 * 服务站 工作人员
 */

class ServicePeopleController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        //$_SESSION='';
        // 检查微信
        if(empty($_SESSION['open_id'])){
            $openid = Weixin::GetOpenid();
            $_SESSION['open_id']=$openid;
        }


        // 自动登录
        if(empty($_SESSION['servicepeople'])){
            $info = M('service_users')->where("open_id='{$_SESSION['open_id']}'")->find();
            if($info) {
                unset($info['password']);
                $_SESSION['servicepeople'] = $info;
            }else{
                $this->redirect("Home/ServiceLogin/peopleIndex");
            }
        }
    }

    public function index(){
        $_SESSION['servicepeople']['company'] = M('service')
            ->cache('cache_company_name',60)
            ->where('id='.$_SESSION['servicepeople']['sid'])
            ->getField('company');

        $work_model = M('work');

        $map = [
            'sid'=>$_SESSION['servicepeople']['sid'],
            'dw_uid'=>$_SESSION['servicepeople']['id'],
            'result'=>1
        ];
        $work['wait_install']  = $work_model->where($map)->where(['type'=>0])->count();
        $work['wait_repair']   = $work_model->where($map)->where(['type'=>1])->count();
        $work['wait_maintain'] = $work_model->where($map)->where(['type'=>2])->count();
        $this->assign('work',$work);
        $this->display();
    }

    // 代缴费搜索
    public function deviceSearch()
    {
        try {
            if( empty($_GET['type']) || empty($_GET['word']) ) {
                E('参数错误!',40001);
            }

            if($_GET['type'] == 1){
                $map['ud.phone']=['like','%'.$_GET['word'].'%'];  //客户手机
            }elseif($_GET['type'] == 2){
                $map['d.device_code']=['like','%'.$_GET['word'].'%'];   //设备编码
            }else{
                E('参数错误!',40002);
            }

            $list = M('devices')
                ->alias('d')
                ->where($map)
                ->join('__USER_DEVICE__ ud ON d.id=ud.did', 'LEFT')
                ->field('d.device_code,d.type_id')
                ->select();

            $this->toJson(['data'=>$list],'获取成功');
        } catch (\Exception $e) {
            $this->toJson($e);
        }
    }

    public function getSetmeal()
    {
        try {
            if( empty($_POST['device_code'])) {
                E('参数错误!',40001);
            }
            $device = M('devices')->where('device_code='.$_POST['device_code'])->find();

            $user=M('user_device')->where('did='.$device['id'])->find();

            $device_type = M('type')->where('id='.$device['type_id'])->getField('typename');
            $info=[
                'name'=>$user['name'],
                'phone'=>$user['phone'],
                'device_type'=>$device_type,
            ];

            $list =  M('setmeal')->where('tid='.$device['type_id'])->select();
            $this->toJson(['data'=>$list,'info'=>$info],'获取成功');
        } catch (\Exception $e) {
            $this->toJson($e);
        }
    }

    // 代缴费
    public function pay(){
        $weixin = new \Org\Util\WeixinJssdk();
        $signPackage = $weixin->getSignPackage();
        $this->assign('wxinfo',$signPackage);
        $this->display();
    }

    // 代缴费 订单
    public function order()
    {
        try {
            $post = I('post.');
            if(empty($post['setmeal_id']) || empty($post['device_code'])){
               E('参数错误!',40001);
            }
            $info =  M('setmeal')->where('id='.$post['setmeal_id'])->find();
            if(empty($info)){
                E('套餐信息错误!',40002);
            }
            $devicesInfo =  M('devices')->where('device_code='.$post['device_code'])->find();
            if(empty($devicesInfo)){
                E('设备信息错误!',40003);
            }
            $uid = M('userDevice')->where('did='.$devicesInfo['id'])->getField('uid');
            if(empty($uid)){
                E('设备未绑定用户!',40004);
            }

            $orders = M('orders');
            $orderSetmeal = M('order_setmeal');

            $orders->startTrans();
            $orderSetmeal->startTrans();

            $orderId = getOrderId();


            $order['order_id']      = $orderId;
            $order['user_id']       = $uid;
            $order['device_id']     = $devicesInfo['id'];

            $order['total_num']     = 1;
            $order['total_price']   = $info['money'];
            $order['created_at']    = time();



            $dataDS = [
                'order_id'      =>  $orderId,
                'uid'           =>  $uid,
                'device_id'     =>  $devicesInfo['id'],
                'setmeal_id'    =>  $info['id'],
                'type_id'       =>  $devicesInfo['type_id'],
                'remodel'       =>  $info['remodel'],
                'money'         =>  $info['money'],
                'flow'          =>  $info['flow'],
                'describe'      =>  $info['describe'],
                'goods_num'     =>  1,
                'goods_price'   =>  $info['money'],
                'status'        =>  0,
                'created_at'    =>  time()
            ];
            $insertId = $orderSetmeal->data($dataDS)->add();

            $ordersRes = $orders->add($order);

            if($ordersRes && $insertId){
                // 执行事务
                $orders->commit();
                $orderSetmeal->commit();

            }else{
                // 事务回滚
                $orders->rollback();
                $orderSetmeal->rollback();
                E('订单创建失败',40010);
            }

            $content = '套餐代充:'.$info['describe'];
            $openId = $_SESSION['open_id'];
            $url = 'http://'.$_SERVER['SERVER_NAME'].U('Home/WeiXinPay/setmealNotify');

            $this->uniformOrder($openId,$info['money'],$orderId,$content,$url);
        } catch (\Exception $e) {
            $this->toJson($e);
        }
    }

    /**
     * 统一下单订单支付并返回数据 JsApi
     * @return string json格式的数据，可以直接用于js支付接口的调用
     * @param  [type] $openId    用户openid
     * @param  [type] $money     订单金额(原金额 未乘100的)
     * @param  [type] $order_id  订单id
     * @param  [type] $content    订单详情
     * @param  [type] $notify_url 回调地址
     */
    public function uniformOrder($openId,$money,$order_id,$content,$notify_url)
    {
        $content = substr($content,0,80);
        $money = $money * 100;                          // 将金额强转换整数

        $money = 1;                                     // 冲值测试额1分钱 上线取消此行

        vendor('WxPay.jsapi.WxPay#JsApiPay');
        $tools = new \JsApiPay();

        vendor('WxPay.jsapi.WxPay#JsApiPay');
        $input = new \WxPayUnifiedOrder();
        //$input->SetDetail($uid);

        $input->SetBody($content);                      // 产品内容

        $input->SetAttach($order_id);                   // 唯一订单ID

        $input->SetOut_trade_no(gerOrderId());          // 设置商户系统内部的订单号,32个字符内、可包含字母, 其他说明见商户订单号
        $input->SetTotal_fee($money);                   // 产品金额单位为分

        //$input->SetTime_start(date("YmdHis"));        // 设置订单生成时间
        //$input->SetTime_expire(date("YmdHis", time() + 300));// 设置订单失效时间
        //$input->SetGoods_tag($uid);

        $input->SetNotify_url($notify_url);             // 微信充值回调地址
        $input->SetTrade_type("JSAPI");           // 支付方式 JS-SDK 类型是：JSAPI
        // 用户在公众号的唯一标识
        $input->SetOpenid($openId);

        $order = \WxPayApi::unifiedOrder($input);       // 统一下单

        // 返回支付需要的对象JSON格式数据
        $jsApiParameters = $tools->GetJsApiParameters($order);

        echo $jsApiParameters;
        exit;
    }

    // 其他方式支付-提交
    public function applyOtherPay()
    {
        try {
            $post = I('post.');
            $map=[
                'open_id'=>$_SESSION['open_id'],
                'status'=>2
            ];
            $info = M('service_apply')->where($map)->count();
            if( empty($info) ){
                E('信息错误',40001);
            }

            $saveData = [
                'paytype'=>2,
                'status'=>3
            ];
            $res = M('service_apply')->where('id='.$info['id'])->save($saveData);

            if( $res ){
                E('工单不存在',40002);
            }else{
                E('工单不存在',40002);
            }
        } catch (\Exception $e) {
            $this->toJson($e);
        }
    }

    // 获取服务(任务)列表
    public function getList()
    {
        $p = I('p',1);
        $_GET['p']=$p;

        $map['dw_uid'] = $_SESSION['servicepeople']['id'];

        strlen(I('type'))?$map['type'] = I('type'):'';
        $map['result'] = 1;
        if (!empty($map['type']) && $map['type']=='all') {
            unset($map['type']);
            $map['result']=['gt',2];
        }

        if (!empty($_GET['word'])) {
            if(is_numeric($_GET['word'])){
                $map['kphone']=['like','%'.$_GET['word'].'%'];
            }else{
                $map['kname']=['like','%'.$_GET['word'].'%'];
            }
        }

        $total = M('work')
            ->where($map)
            ->count('id');
        if(empty($total)){
            $this->toJson(['data'=>[]],'获取成功');
        }

        $page  = new \Think\Page($total,10);
        $list = M('work')
            ->where($map)
            ->limit($page->firstRow.','.$page->listRows)
//            ->field('id,kname,kphone,create_at')
            ->select();
        $list = replace_array_value($list,[
            'pass_at'=>['date','Y-m-d H:i:s'],
        ]);
        $this->toJson(['data'=>$list],'获取成功');
    }

    // 获取工单详情
    public function getDetail()
    {
        try {
            $post = I('post.');
            if( empty($post['wid']) ){
                E('工单信息错误',400022);
            }

            $map['id'] = $post['wid'];
            $info = M('work')->where($map)->find();
            if( empty($info) ){
                E('工单不存在',400022);
            }
            $this->toJson(['data'=>$info,200]);
        } catch (\Exception $e) {
            $this->toJson($e);
        }
    }

    // 工单完成
    public function passWork()
    {
        try {
            $post = I('post.');
            if( empty($post['wid']) ){
                E('工单信息错误',400022);
            }


            // 1 拒绝 2 完成
            if( empty($post['operate']) ){
                E('信息错误',400023);
            }


            if($post['operate']=='1'){
                $data['result'] = 1;
                $data['refuse'] = 1;// 拒绝状态
            } elseif($post['operate']=='2') {
                $data['result'] = 2;
            } else {
                E('信息错误',400024);
            }



            $map['id'] = $post['wid'];
            $info = M('work')->where($map)->find();
            if( empty($info) ){
                E('工单不存在',400022);
            }

            $data['update_at']=time();
            $data['pass_at'] = time();
            //$data['result'] = 2;///------------------固定的状态值----------------------------------
            $res = M('work')->where($map)->save($data);

            if ($res) {
                Work::add($info['id'], 7); //完成
                E('提交成功!',200);
            }else{
                E('提交失败,请重试!',400001);
            }
        } catch (\Exception $e) {
            $this->toJson($e);
        }
    }

    // 用户信息
    public function servicePersonal()
    {
        $info = M('service_users')->where(['id'=>$_SESSION['servicepeople']['id']])->find();
        $this->assign('info',$info);
        $this->display();
    }
    
    //用户信息编辑
    public function setInfo()
    {
        try {
            $post = I('post.');
            if(!empty($post['opassword'])){
                if(empty($post['password'])){
                    E('密码不能为空!',40002);
                }

                $info = M('service_users')->where('id='.$_SESSION['servicepeople']['id'])->find();
                if (md5($post['opassword']) != $info['password']) {
                    E('旧密码不对,请重试!',40003);
                }

                $saveData['password'] = md5($post['password']);
            }

            if(!empty($post['name'])){
                $saveData['name'] = $post['name'];
            }
            if(!empty($post['phone'])){
                $saveData['phone'] = $post['phone'];
            }

            if (empty($saveData))E('无信息提交!',40001);

            $saveData['updatetime']=time();

            $res = M('service_users')->where('id='.$_SESSION['servicepeople']['id'])->save($saveData);
            if ($res) {
                E('修改成功!',200);
            }else{
                E('修改失败,请重试!',40001);
            }
        } catch (\Exception $e) {
            $this->toJson($e);
        }
    }    

    // 微信 解绑
    public function removeWeixin()
    {
        try {
            $saveData = [
                'open_id'=>'',
                'wxname'=>'',
                'updatetime'=>time()
            ];
            $res = M('service_users')->where(['id'=>$_SESSION['servicepeople']['id']])->save($saveData);

            if ($res) {
                E('解绑成功!',200);
            }else{
                E('解绑失败,请重试!',400001);
            }
        } catch (\Exception $e) {
            $this->toJson($e);
        }
    }

    // 微信 绑定
    public function bindingWeixin()
    {
        if( empty($_SESSION['open_id']) ){
            $_SESSION['open_id'] = Weixin::GetOpenid();
        }
        $data = Weixin::getWeiXinUserInfo($_SESSION['open_id']);
        if(!empty($data['open_id'])){
            $saveData = [
                'open_id'=> $data['open_id'],
                'wxname'=>  $data['nickname'],
                'updatetime'=>time()
            ];
            $res = M('service_users')->where(['id'=>$_SESSION['servicepeople']['id']])->save($saveData);
            if($res){
                notice('绑定成功!','servicePersonal');
            }
        }
        notice('绑定失败,请重试!','servicePersonal');
    }
}
