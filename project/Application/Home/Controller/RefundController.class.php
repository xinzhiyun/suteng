<?php
namespace Home\Controller;
use Org\Util\Date;
use Org\Util\Strings;
/**
 * [退款及退款退货]
 */
class RefundController extends CommonController
{

    public function showGoods()
    {
        if (IS_AJAX) {
           $data = D('Refund')->relation(['goods'])->where(['uid'=>$_SESSION['user']['id']])->select();

            foreach ($data as $key => $value) {
                foreach ($value['goods'] as $k => $val) {                                  
                    $data[$key]['goods'][$k] = M('order_detail')
                        ->alias('d')
                        ->where(['d.order_id'=>$val['oid'],'d.gid'=>(int)$val['gid']])
                        ->join('__GOODS__ g ON g.id = d.gid','LEFT')
                        ->join('__GOODS_DETAIL__ g_d ON g.id = g_d.gid','LEFT')
                        ->join('__PIC__ p ON g.id = p.gid','LEFT')
                        ->field(array('p.path'=>'orderimg','g.name'=>'productname','g.desc'=>'productbrief','d.gid','d.price'=>'price','d.num'=>'productnumber','g_d.is_install'=>'is_install','g_d.is_hire'=>'is_hire'))
                        ->find();
               } 
            }
            if ($data) {
               return $this->ajaxReturn(['code'=>200,'data'=>$data]);
            }else{
               return $this->ajaxReturn(['code'=>400,'msg'=>'没有数据']);
            } 
        }
    }

    /**
     * 退款退货申请
     * @return [type] [description]
     */
    public function create()
    {
        // 商品
        $data['serial_num'] = $this->getNumber();
        $data['uid']        = $_SESSION['user']['id'];
        $data['total_amount'] = 999;
        $data['method']     = 1;
        $data['reason']     = 0;
        $data['description'] = '描述';
        $data['create_time'] = time();
        $data['goods'] = [
            0 => [
                'oid' => 36,
                'gid' => 66,   
                'amount' => 33
            ],
            1 => [
                'oid' => 36,
                'gid' => 67,
                'amount' => 35
            ],
            2 => [
                'oid' => 36,
                'gid' => 68,
                'amount' => 78
            ]
        ];
        $result = D('Refund')->relation(true)->add($data);
        echo 'success:'.$result;die;
    }

    /**
     * 退货填写物流信息
     * @return [type] [description]
     */
    public function logistics()
    {   
        if (IS_AJAX) {
            $data['rf_id'] = 2;
            $data['name']   = '顺风快递';
            $data['number'] = '65891546455546';
            $res = D('refund_logistics')->data($data)->add();
            if ($res) {
                return $this->ajaxReturn(['code'=>200,'msg'=>'发货成功']);
            } else {
                return $this->ajaxReturn(['code'=>400,'msg'=>'发货失败']);
            }
        }       
    }

    /**
     * 生成工单编号
     * @return [type] [description]
     */
    protected function getNumber()
    {
        $string = new Strings;
        // $str1 = $string->randString(3,0);   // 生成字母随机字符
        $str2 = $string->randString(5,1);   // 生成数字随机字符
        $str3 = $string->randString(3,1);   // 生成数字随机字符
        $number = time().$str2.$str3;
        if (D('Refund')->where(['serial_num'=>$number])->find()) {
            return $this->getNumber();
        }
        return $number;
    }
}