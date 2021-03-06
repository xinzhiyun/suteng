<?php
namespace Api\Controller;
use Think\Controller;
use Think\Log;
use Org\Util\Gateway;
use Common\Tool\Device;
use Common\Tool\Timer;
class ActionController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        Gateway::$registerAddress = '127.0.0.1:9504';

        Device::connect();
    }


    /**
     * 接收信息 信息分发
     */
    public function receive()
    {

        $message = I('post.');
        $client_id = $message['client_id'];
        unset($message['client_id']);
        if(empty($message['DeviceID'])){
            $mes  = Gateway::getSession($client_id);
            $message['DeviceID']  = $mes['DeviceID'];
        }
//        var_export($message);

        // 判断数据传输的对象
        if( $message['soure']=='Close')
        {
            $this->Close($message['DeviceID']);
            //$this->getws($client_id, $message);//发送 断开的状态  (以后)
        }
        else if( $message['soure']=='TCP'){
            $this->gettcp($client_id, $message);
        } else {
            $this->getws($client_id, $message);
        }
    }

    /**
     * web端信息 数据处理
     */
    public function getws($client_id, $message)
    {
        if( $message['PackType'] == 'login' ){
            Gateway::joinGroup( $client_id, $message['DeviceID'] );
        } else {
            Gateway::sendToUid($message['DeviceID'], $message);
        }
    }

    /**
     * 设备消息处理
     */
    public function gettcp($client_id, $message)
    {
        if( empty( Gateway::getSession($client_id) ) ){
            Gateway::setSession($client_id, $message);
        } else {
            $res = Gateway::getSession($client_id);

            $message['DeviceID'] = $res['DeviceID'];
        }
        $message['DeviceID'] = trim($message['DeviceID']);

        if($message['PackType'] == 'login'){
            Gateway::bindUid($client_id, $message['DeviceID']);
        }

        // 信息正常回复
        if( isset($message['DeviceID']) ){
            if( Gateway::getClientCountByGroup($message['DeviceID']) > 0 ){
                Gateway::sendToGroup( $message['DeviceID'], json_encode($message) );
            }
        }
        Gateway::sendToClient($client_id, $message);

        var_export($message);

        $message['sid'] = Device::get_devices_info($message['DeviceID'],'sid');
        Log::write(json_encode($message), '设备信息包分发');

        switch ($message['PackType']) {
            case 'login':
                $this->loginAction($message);
                break;

            case 'SetData':
                $this->setdataAction($message);
                break;

            case 'Select':
                $this->selectAction($message);
                break;

            default:
                $data['NetStause']=1;
                if(!empty($message['sid'])){
                    M('devices_statu')->where("id=" . $message['sid'])->save($data);
                }
                break;
        }
    }

    /**
     * 设备连接关闭
     */
    public function Close($DeviceID)
    {
        $status_id = M('devices_statu')->where("DeviceID=" . $DeviceID)->getField('id');

        if($status_id){
            $data = ['NetStause'   => 0];
            $this->updateData($status_id, $data);
        }
    }

    // 登陆数据处理
    public function loginAction($message)
    {
        $data = [
            'Device'      => $message['Device'],
            'ICCID'       => $message['ICCID'],
            'CSQ'         => $message['CSQ'],
            'Loaction'    => $message['Loaction'],
            'NetStause'   =>1,
        ];

        if( empty($message['sid']) ){
            $data['DeviceID']=$message['DeviceID'];
            $res = $this->saveData($data);
            if($res){
                $data['updatetime'] = time();
                $data['device_status'] = 1;
                M('devices')->where('device_code=' . $message['DeviceID'])->save($data);
            }
        } else {
            if($this->check_info($message['sid'])){
                $this->updateData($message['sid'], $data);
            }
        }

//        Device::get_devices_info($message['DeviceID'],'sid');
    }

    /**
     * 设备查询包处理
     * @param $message
     * @return array
     */
    public function selectAction($message)
    {
        $data = [
            'DeviceStause' => $message['DeviceStause'],
            'NetStause'    =>1,
            'RawTDS'       => $message['RawTDS'],
            'PureTDS'      => $message['PureTDS'],
            'Temperature'  => $message['Temperature'],
        ];
        if ( isset($message['SumPump'])              )       $data['SumPump']     = $message['SumPump'];

        if ( isset($message['FilerNum']) || $message['FilerNum'] != null ) {
            $res = $this->filterAction($message);

            $data = array_merge( $data, $res );
        }

        if ( empty( $message['sid'] ) ){
            $message['sid'] = Device::get_devices_info($message['DeviceID'],'sid');
        }

        $data_statu = M('devices_statu')->where('id='.$message['sid'])->getField('data_statu');

        if ($message['sid']) {
            if ( $data_statu != 0 ) {
                $this->sysnc( $message['DeviceID'] );//信息同步
            } else {
                if ( isset($message['ReFlow'])       )       $data['ReFlow']      = $message['ReFlow'];
                if ( isset($message['Reday'])        )       $data['ReDay']       = $message['Reday'];
                if ( isset($message['SumFlow'])      )       $data['SumFlow']     = $message['SumFlow'];
                if ( isset($message['SumDay'])       )       $data['SumDay']      = $message['SumDay'];
                if ( isset($message['FilterMode'])   )       $data['FilterMode']  = $message['FilterMode'];
                if ( isset($message['LeasingMode'])  )       $data['LeasingMode'] = $message['LeasingMode'];
                //if ( isset($message['AliveStause'])  )       $data['AliveStause'] = $message['AliveStause'];

                if ( isset($message['CSQ'])          )       $data['CSQ']         = $message['CSQ'];
                if ( isset($message['ICCID'])        )       $data['ICCID']       = $message['ICCID'];
                if ( isset($message['Device'])       )       $data['Device']      = $message['Device'];
                if ( isset($message['ICCID'])        )       $data['ICCID']       = $message['ICCID'];

                if ( isset($message['FilerNum']) ) {
                    for ($i=1;$i<=$message['FilerNum'];$i++) {
                        if( isset($message['ReFlowFilter'.$i]) ) $data['ReFlowFilter'.$i] = $message['ReFlowFilter'.$i];
                        if( isset($message['ReDayFilter'.$i])  ) $data['ReDayFilter'.$i]  = $message['ReDayFilter'.$i];
                    }
                }
            }
            $this->updateData($message['sid'], $data);
        }
    }

    /**
     * 设备设置包处理
     * @param $message
     * @return array
     */
    public function setdataAction($message)
    {
        if (isset($message['PackNum'])) {

            if ($message['PackNum'] == 5) {  //更新
                M('devices_statu')->where("id=" . $message['sid'])->save(['data_statu'=>0]);
            }

            if ($message['PackNum'] == 6 ) {  //激活
                M('devices_statu')->where("id=" . $message['sid'])->save(['data_statu'=>0,
                    'AliveStause'=>1]);
                $did = Device::get_devices_info($message['DeviceID'],'id');
                if($did){
                    M('devices')->where('id='.$did)->save(['device_statu'=>3]);
                }

            }

            if ($message['PackNum'] == 4 ) { //定时任务

                //获取定时器缓存 进行删除
                $timers = Device::get_devices_info($message['DeviceID'],'timer');
                $timers = unserialize($timers);
                $oldtimers = $timers;

                foreach ($timers as $k=> $timer){
                    if (Timer::play($timer)) {
                        unset($oldtimers[$k]);
                    }
                }
                Device::hset($message['DeviceID'],'timer',serialize($oldtimers));
            }
        }
    }

    /**
     * 推送信息到设备
     * @param $dcode
     */
    public function sysnc($dcode)
    {
        $status_id = Device::get_devices_info($dcode,'sid');

        $this->check_info($status_id);
    }

    /**
     * 库数据检查  单向推送到设备
     * $id  数据串的id
     * 数据状态 0正常 1更新 2 需要激活
     */
    public function check_info($id)
    {
        $data = M('devices_statu')->find($id);
        if (isset($data['data_statu']) && $data['data_statu'] > 0 ){
            $msg = $this->get_data($data);
            if($msg) $this->sendMsg($msg);

            return false;
        }
        return true;
    }

    /**
     * 处理数据结构
     */
    public function get_data($data)
    {
        if(!isset($data['deviceid'])){
            return false;
        }
        $msg['DeviceID'] = $data['deviceid'];
        $msg['PackType'] = "SetData";
        $msg['Vison']    = 0;

        $PackNumArr      = ['1'=>5,'2'=>6];//设备传输包标识 5更新 6激活 用于回调请求时数据处理

        $msg['PackNum']  = $PackNumArr[$data['data_statu']]?:0;

        //设备模式
        $msg['LeasingMode'] = $data['leasingmode'];
        $msg['FilterMode']  = $data['filtermode'];

        //剩余
        $msg['ReFlow'] = empty($data['reflow'])? 0 : $data['reflow'];
        $msg['Reday']  = empty($data['reday'] )? 0 : $data['reday'];

        $msg['SumFlow']     = empty($data['sumflow'])? 0 :$data['sumflow'];
        $msg['SumDay']      = empty($data['sumday'] )? 0 :$data['sumday'];

        if ($data['data_statu'] == 2) {  //设备激活
            $msg['AliveStause'] = 1;
            $msg['SumFlow']     = 0;
            $msg['SumDay']      = 0;
        }

        $filter_life=$this->get_filter_info($data['deviceid']);
        if(!empty($filter_life)) {
            $filenum=count($filter_life);
            $msg['FilerNum'] = $filenum;

            if ($msg['PackNum'] == 5) { //设备更新
                for ($i = 1; $i <= $filenum; $i++ ) {
                    $msg['ReFlowFilter'. $i]     = $data['reflowfilter'.$i];
                    $msg['ReDayFilter'. $i]      = $data['redayfilter'.$i];
                    $msg['FlowLifeFilter'. $i]   = $filter_life[$i-1]['flowlife'];
                    $msg['DayLifeFiter'. $i]     = $filter_life[$i-1]['timelife'];
                }
            }

            if ($msg['PackNum'] == 6) {  //设备激活
                for ($i = 1; $i <= $filenum ; $i++) {
                    $msg['ReFlowFilter'. $i]     = $filter_life[$i-1]['flowlife'];
                    $msg['ReDayFilter'. $i]      = $filter_life[$i-1]['timelife'];
                    $msg['FlowLifeFilter'. $i]   = $filter_life[$i-1]['flowlife'];
                    $msg['DayLifeFiter'. $i]     = $filter_life[$i-1]['timelife'];
                }

            }
        }

        return $msg;
    }

    /**
     * sendMsg 向设备发送信息 信息推送
     * @param   array $message     信息串
     * @return
     */
    public function sendMsg($message)
    {
        Log::write(json_encode($message), 'sendMsg 发送信息包');
        var_export($message);
        if(isset($message['DeviceID'])){
            Gateway::sendToUid($message['DeviceID'], $message);
        }
    }


    // 存储数据 将数据存到 devices_statu表中
    public function saveData($data)
    {
        $data['addtime'] = time();
        return M('devices_statu')->add($data);
    }

    // 更新数据
    public function updateData($id, $data)
    {

        $data['updatetime'] = time();
        return M('devices_statu')->where("id={$id}")->save($data);
    }

    // 滤芯处理
    public function filterAction($message)
    {
        $data = array();
        for( $i = 1; $i <= $message['FilerNum']; $i ++)
        {
            $data['ReFlowFilter' . $i]   = $message['ReFlowFilter' . $i];
            $data['ReDayFilter' . $i]    = $message['ReDayFilter' . $i];
        }
        if (empty($message['FilterMode'])) {
            if ($data['ReFlowFilter1']==-1) {
                $data['FilterMode']=0;
            }
            if ( $data['ReDayFilter1']==-1 ){
                $data['FilterMode']=1;
            }
            if ($data['ReDayFilter1']>=0 and  $data['ReFlowFilter1']>=0) {
                $data['FilterMode']=2;
            }
        }
        return $data;
    }


    public function get_filter_info($dcode)
    {
        $type_id = Device::get_devices_info($dcode,'type_id');

        if( empty($type_id) ) return [];
        $type = M('type')->where("id={$type_id}")->find();

        foreach ($type as $k=> $v) {
            if(strstr($k,'filter') and !empty($v) ){
                $sum[$k] = $v;
            }
        }

        foreach ($sum as $key => $value) {
            $str = stripos($value,'-');
            $map['filtername'] = substr($value, 0,$str);
            $map['alias'] = substr($value, $str+1);
//            $res[] = M('filters')->where($map)->field('timelife,flowlife')->find();
            $res[] = M('filters')->where($map)->field('timelife,flowlife')->find();
        }
        array_filter($res);
        return $res;
    }

    /**
     * 设备初始化
     * @param  [type] $dcode [description]
     * @return [type] [description]
     *
     * @Author 李振东 lzdong@foxmail.com 2018-04-02
     */
    public function devices_init($dcode,$data=[])
    {
        $message['DeviceID'] = $dcode;
        $message['PackType'] = "SetData";
        $message['Vison']    = 0;
        $message['PackNum']  = 6;

        $filter =  $this->get_filter_info($dcode);
        foreach ($filter as $key =>$value) {
            $i =$key+1;
            $sdata[ 'ReFlowFilter'.$i]      = $value['flowlife'];
            $sdata[ 'FlowLifeFilter'.$i]    = $value['flowlife'];

            $sdata[ 'ReDayFilter'.$i]       = $value['timelife'];
            $sdata[ 'DayLifeFiter'.$i]      = $value['timelife'];
        }
        $message = array_merge($message,$sdata);

        if(empty($data['ReDay'])){
            $message['Reday']         = $sdata['Reday']           = '365';
        }

        if(empty($data['SumDay'])){
            $message['SumDay']        = $sdata['SumDay']           = '0';
        }

        if(empty($data['AliveStause'])){
            $message['AliveStause']   = $sdata['AliveStause']     = '1';
        }

        if(empty($data['FilterMode'])){
            $message['FilterMode']    = $sdata['FilterMode']     = '0';
        }

        if(empty($data['LeasingMode'])){
            $message['LeasingMode']   = $sdata['LeasingMode']     = '0';
        }

        if(empty($data['data_statu'])){
            $sdata['data_statu']      = '2';
        }

        $id = M('devicesStatu')->where('DeviceID='.$dcode)->getField('id');

        if(empty($id)) {
            $sdata['DeviceID'] = $dcode;
            $res = M('devicesStatu')->add($sdata);
        } else {
            $res = M('devicesStatu')->where('id='.$id)->save($sdata);
        }

        $this->sendMsg($message);
    }

    public function test()
    {
        //$this->sysnc('868575025659808');
//

        $this->check_info(73);

//        $message['DeviceID'] = '868575025672249';
//        $message['PackType'] = "SetData";
//        $message['Vison']    = 0;
//        $message['Reday']    = '226';
////
////        $this->get_filter_info('868575025659121');
////
//////        $this->check_info(94);
//        $this->sendMsg($message);
    }

}