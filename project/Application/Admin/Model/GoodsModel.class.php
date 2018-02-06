<?php
namespace Admin\Model;
use Think\Model;

/**
 * 分类
 */
class GoodsModel extends BaseModel
{
    // 自动验证
    protected $_validate = array(
        array('cid','/^[\d]{0,10}$/', '请选择分类','regex'),
        array('name', 'require', '商品名不能为空'),
        // array('name','','商品名已经重复',0,'unique',1),
        // array('name','/^[\(\)（）【】\[\]—_\-a-zA-Z0-9\x{4e00}-\x{9fa5}]{1,660}$/u', '商品名称格式不正确','regex'),
        // array('price','/^[\d]+[\.][\d]{2}$/', '请输入正确的价格','regex'),
        // array('stock','/^[1-9][\d]{0,10}$/', '请输入正确的库存','regex'),
    );

    // 自动完成
    protected $_auto = array(
        array('addtime', 'time', 1, 'function'),
        array('updatetime', 'time', 3, 'function'),
    );

    public function getGoodsList($where=array())
    {
        // $count = $this
        //     ->where($where)
        //     ->alias('g')
        //     ->join('__ATTR_VAL__ av ON g.id=av.gid', 'LEFT')
        //     ->join('__ATTR__ a ON av.aid=a.id', 'LEFT')
        //     ->join('__GOODS_DETAIL__ gd ON g.id=gd.gid', 'LEFT')
        //     ->join('__PIC__ p ON g.id=p.gid', 'LEFT')
        //     ->join('__CATEGORY__ c ON g.cid=c.id', 'LEFT')
        //     ->field('p.*,g.*,c.name cname,av.val,a.attr,gd.*,p.path')
        //     ->order(' addtime desc')
        //     ->count();
        // $Page       = new \Think\Page($count,10);
        // page_config($Page);
        // $show       = $Page->show();
        $goodsData = $this
            ->where($where)
            ->alias('g')
            ->join('__ATTR_VAL__ av ON g.id=av.gid', 'LEFT')
            ->join('__ATTR__ a ON av.aid=a.id', 'LEFT')
            ->join('__GOODS_DETAIL__ gd ON g.id=gd.gid', 'LEFT')
            ->join('__PIC__ p ON g.id=p.gid', 'LEFT')
            ->join('__CATEGORY__ c ON g.cid=c.id', 'LEFT')
            ->field('p.*,g.*,c.name cname,av.val,a.attr,gd.*,p.path')
            ->order(' addtime desc')
            ->limit($Page->firstRow.','.$Page->listRows)
            ->select();
        $goodsData = [
            'goodsData' => $goodsData,
            // 'show' => $show,
        ];
        foreach($goodsData['goodsData'] as $val){
            $key = $val['gid'];
            if(isset($arr[$key])) {
                $arr[$key]['attr'] .= $val['attr'].':'.$val['val'].'|';
            } else {
                $arr[$key] = $val;
                $arr[$key]['attr'] = $val['attr'].':'.$val['val'].'|';
            }
        }
        $goodsData['goodsData'] = array_values($arr);
        // dump($goodsData);
        return $goodsData;
    }

    // 属性处理
    public function AttrAction($gid)
    {
        $attr = $this
            ->where('av.gid='.$gid)
            ->alias('g')
            ->join('__ATTR_VAL__ av ON g.id=av.gid', 'LEFT')
            ->join('__ATTR__ a ON av.aid=a.id', 'LEFT')
            ->field('av.gid,a.attr,av.val')
            ->select();
        return $attr;
    }


}
