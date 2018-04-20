<?php
namespace Home\Model;
use Think\Model;
class FiltersModel extends Model
{
    public function getFilters($where=array())
    {
        // $this
        //     ->alias('f')
        //     ->where($where)
        //     ->join('')
        
        // 滤芯信息
        $filter = M('Devices')
            ->where($where)
            ->alias('d')
            ->join("__TYPE__ t ON d.type_id=t.id", 'LEFT')
            ->field('t.*') 
            ->find();
        // dump($filter);
        $data['filterInfo'] = $this->getFilterDetail($filter);
        return $data;
    }


    // 查询滤芯详情
    public function getFilterDetail($type)
    {
//        unset($sum['id'],$sum['typename'],$sum['addtime']);
//        $sum = array_filter($sum);
        foreach ($type as $k=> $v) {
            if(strstr($k,'filter') and !empty($v) ){
                $sum[$k] = $v;
            }
        }
        foreach ($sum as $key => $value) {
            $str = stripos($value,'-');
            $map['filtername'] = substr($value, 0,$str);
            $map['alias'] = substr($value, $str+1);
            // dump($map);
            $res[] = M('filters')->where($map)->find();
        }
        array_filter($res);
        // dump($res);
        return $res;
    }
}