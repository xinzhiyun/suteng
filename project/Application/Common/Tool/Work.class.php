<?php
/**
 * Created by PhpStorm.
 * User: 李振东
 * Time: 2018/3/29 下午2:37
 */

namespace Common\Tool;

use Think\Log;

class Work
{
    public static $title = [
        '1'=>  '工单生成',
        '2'=>  '工单信息审核',  //通知是否通过审核
        '3'=>  '工单信息审核失败',  //通知是否通过审核

        '4'=>  '服务站分配',    //服务站的信息
        '5'=>  '预约派遣',      //安装师傅的信息 预约时间
        '6'=>  '预约派遣-重新预约',

        '7'=>  '服务完成',      //安装工人 完成
        '8'=>  '服务站验收',    //服务站验收成功

        '9'=>  '评价完成',

        '99'=>  '工单完成',      // 工单完成(关闭)
    ];


    /** 添加工单记录  //下版本的方法
     * @param $wid int 工单ID
     * @param $mode int 信息类型
     * @param array $info array 信息
     */
    public static function add($wid, $mode)
    {
        $work =  M('work')->where('id='.$wid)->find();
        if(empty($work)) {
            Log::write('工单'.$wid.'不存在','ERR 工单记录');
            return false;
        }
        $data['wid']  = $wid;
        $data['time'] = date("Y-m-d H:i:s",time());

        $data['title'] = self::$title[$mode];
        $content = [];
        $content[] = ['title'=>'工单号','value'=>$work['number']];
        switch ($mode) {
            case '1':
                $content[] = ['title'=>'系统正在优先为您就近安排服务站,预计10分钟.'];
                break;

            case '2':
                $content[] = ['title'=>'我们将尽快为您安排上门服务，请稍等待.'];
                break;

            case '3':
                $content[] = ['title'=>'您提交的工单不符合售后服务相关规定,请重新提交.'];
                $content[] = ['title'=>'若有疑问，请致电客服：<a class="tel" href="tel:020-23325238">020-23325238</a>'];
                break;

            case '4':
                if (empty($work['sid'])) { return false; }
                if ($work['service_mode']==1) { // 第三方
                    $service_info =  M('service_other')->where('id='.$work['sid'])->find();
                }else{
                    $service_info =  M('service')->where('id='.$work['sid'])->find();
                }

                $content[] = ['title'=>'系统已分配“'.$service_info['company'].'”为您服务.'];
                $content[] = ['title'=>'服务站联系人', 'value'=>$service_info['name']];
                $content[] = [
                    'title'=>'服务站客服电话',
                    'value'=>"<a class='tel' href='tel:{$service_info['telephone']}'>".$service_info['telephone'].'</a>'
                ];
                break;

            case '5':
                if (empty($work['name'])) { return false; }

                $content[] = ['title'=>'已委派服务人员', 'value'=>$work['name']];
                $content[] = ['title'=>'联系电话', 'value'=>"<a class='tel' href='tel:{$work['phone']}'>".$work['phone'].'</a>'];
                $content[] = ['title'=>'上门服务时间', 'value'=>$work['anry_time'].$work['anry_period']];
                $content[] = ['title'=>'服务人员会电话联系您,请保持电话畅通，谢谢.'];
                break;

            case '6':
                $content[] = ['title'=>'已委派服务人员', 'value'=>$work['name']];
                $content[] = ['title'=>'联系电话', 'value'=>"<a class='tel' href='tel:{$work['phone']}'>".$work['phone'].'</a>'];
                $content[] = ['title'=>'上门服务时间', 'value'=>$work['anry_time'].$work['anry_period']];
                $content[] = ['title'=>'服务人员会电话联系您,请保持电话畅通，谢谢.'];

                break;

            case '7':
                $content[] = ['title'=>'服务人员', 'value'=>$work['name']];
                $content[] = ['title'=>'联系电话', 'value'=>"<a class='tel' href='tel:{$work['phone']}'>".$work['phone'].'</a>'];
                $content[] = ['title'=>'系统已收到安装人员为您上门服务的详细记录.'];

                break;

            case '8':
                $content[] = ['title'=>'服务人员', 'value'=>$work['name']];
                $content[] = ['title'=>'联系电话', 'value'=>"<a class='tel' href='tel:{$work['phone']}'>".$work['phone'].'</a>'];
                $content[] = ['title'=>'您好,请您对此次的售后服务进行评价.'];
                break;

            case '9':
                $content[] = ['title'=>'服务人员', 'value'=>$work['name']];
                $content[] = ['title'=>'联系电话', 'value'=>"<a class='tel' href='tel:{$work['phone']}'>".$work['phone'].'</a>'];
                $content[] = ['title'=>'评价成功,感谢您对我们工作的支持.'];
                break;

            case '99':
                $content[] = ['title'=>'服务人员', 'value'=>$work['name']];
                $content[] = ['title'=>'联系电话', 'value'=>"<a class='tel' href='tel:{$work['phone']}'>".$work['phone'].'</a>'];
                $content[] = ['title'=>'您好,您此次的工单服务圆满完成.'];
                $content[] = ['title'=>'再次感谢您对我们工作的支持.'];
                break;
        }
        $data['content'] = json_encode($content);

        return M('work_note')->add($data);
    }


    /** 添加工单记录
     * @param $wid int 工单ID
     * @param $mode int 信息类型
     * @param array $info array 信息
     */
    public static function adds($wid, $mode)
    {
        $work =  M('work')->where('id='.$wid)->find();
        if(empty($work)) {
            Log::write('工单'.$wid.'不存在','ERR 工单记录');
            return false;
        }
        $data['wid']  = $wid;
        $data['time'] = date("Y-m-d H:i:s",time());

        $data['title'] = self::$title[$mode];

        switch ($mode) {
            case 1:
                $data['content'] = '系统正在优先为您就近安排服务站,预计10分钟.';
                break;

            case 2:
                $data['content'] = '系统审核通过.';
                break;

            case 3:
                $data['content'] = '系统审核未通过,请检查.';

                break;
            case 4:
                if (empty($work['sid'])) { return false; }
                if ($work['service_mode']==1) { // 第三方
                    $service_info =  M('service_other')->where('id='.$work['sid'])->find();
                }else{
                    $service_info =  M('service')->where('id='.$work['sid'])->find();
                }

                $_html  = '站点名称:'.$service_info['company'].'<br>';
                $_html .= '地址:'.$service_info['addressinfo'].'<br>';
//                $_html .= '联系人:'.'<br>';
                $_html .= '客服电话:'.$service_info['telephone'].'<br>';
                $_html .= '服务站点正在为您优先安排安装师傅上门安装预计需要1-3个工作日.';
                $data['content'] = $_html;
                break;

            case 5:
                if (empty($work['name'])) { return false; }
                $_html  = '安装师傅:'.$work['name'].'<br>';
                $_html .= '联系电话:'.$work['phone'].'<br>';
                $_html .= '预约时间:'.$work['anry_time'].$work['anry_period'].'<br>';
                $_html .= '请保持电话通畅,方便师傅联系上门服务.';
                $data['content'] = $_html;
                break;
            case 7:
                $data['content'] = "服务人员完成任务,待验收!";
                break;
            case 8:
                $data['content'] = '任务已完成,请点击下面 评价 对本次服务进行评价.';
                break;
            case 99:
                $data['content'] = '工单已关闭';
                break;

        }
        return M('work_note')->add($data);
    }

    //  评价算法 自动评价 默认4星
    public static function evaluAction($number)
    {
        if(empty($number)){
            return false;
        }
        $map['number'] = $number;
        $work = M('work')->where($map)->find();
        if(empty($work)){ return false; }

        if($work['result']==4){  // 客户评价
            if (is_json($work['evaluate'])){
                $evaluate = json_decode($work['evaluate']);
            }
            $num = 0;
            foreach ($evaluate as $value){
                if( is_array($value)) {
                    $num += $value['num'];
                } else {
                    $num += $value->num;
                }
            }
            $num += M('service_evaluate')->where('star='.$work['star'])->getField('num');

            $service_users = M('service_users')->where('id='.$work['dw_uid'])->find();

            if(!empty($service_users)){
                $starnum = $service_users['star'] + $num;
                if($starnum>5){ $starnum = 5; }
                M('service_users')->where('id='.$work['dw_uid'])->save(['star'=>$starnum]);
            }

        }else{ //自动评价 默认4星
            $saveData = [
                'evaluatetype'=>2,
                'update_at'=>time(),
                'star'=>4,
            ];
            M('work')->where($map)->save($saveData);
        }
    }





}