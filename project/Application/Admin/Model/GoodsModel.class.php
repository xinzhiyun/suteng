<?php
namespace Admin\Model;
// use Think\Model;
use Think\Model\RelationModel;
// use Org\Util\Date;
/**
 * 分类
 */
class GoodsModel extends RelationModel
{

    protected $_link = array(
        'category'=>array(
                'mapping_type'  => self::BELONGS_TO,
                'class_name'    => 'category',
                'foreign_key'   => 'cid',
                'mapping_name'  => 'category',
                'mapping_fields' => 'id,name'
        ),

        'price'=>array(
                'mapping_type'  => self::HAS_MANY,
                'class_name'    => 'price',
                'foreign_key'   => 'gid',
                'mapping_name'  => 'price',
                'mapping_fields' => 'id,price,grade'
        ),

        'attr_val'=>array(
                'mapping_type'  => self::HAS_MANY,
                'class_name'    => 'attr_val',
                'foreign_key'   => 'gid',
                'mapping_name'  => 'attr_val',
                'mapping_fields' => 'aid,aname,val'
        ),

        'goods_courier'=>array(
                'mapping_type'  => self::HAS_MANY,
                'class_name'    => 'goods_courier',
                'foreign_key'   => 'gid',
                'mapping_name'  => 'goods_courier',
                'mapping_fields' => 'id,cid,cprice,cname'
        ),

        'pic'=>array(
            'mapping_type'  => self::HAS_MANY,
            'class_name'    => 'pic',
            'foreign_key'   => 'gid',
            'mapping_name'  => 'pics',
            'mapping_fields' => 'path'
        ),

        'goods_detail'=>array(
                'mapping_type'  => self::HAS_MANY,
                'class_name'    => 'goods_detail',
                'foreign_key'   => 'gid',
                'mapping_name'  => 'goods_detail',
                'mapping_fields' => 'id,cost,is_install,is_hire'
        ),

    );

    /**
     * 获取商品详细信息
     * @param  int $id 主键id
     * @return array     查询的数组数据
     */
    public function getGoodInfo($id)
    {
         $data = $this
            ->relation(['category','price','attr_val','goods_courier','goods_detail'])
            ->where('id='.$id)
            ->find();
        return $data;
    }







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
            // ->join('__PIC__ p ON g.id=p.gid', 'LEFT')
            ->join('__CATEGORY__ c ON g.cid=c.id', 'LEFT')
            ->join('__INVENTORY__ i on i.gid=g.id' , 'LEFT')
            ->field('c.name cname,av.val,a.attr,gd.*,i.allnum,i.abnormalnum,g.*')
            ->order(' addtime desc')
            ->limit($Page->firstRow.','.$Page->listRows)
            ->relation('pics')
            ->select();
            // p($goodsData);
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

    public function getPrice($map=array())
    {
        $data = $this
            ->alias('g')
            ->where($map)
            ->join('__PRICE__ p ON g.id=p.gid', 'LEFT')
            ->field('p.*')
            ->select();
        return $data;
    }
}
