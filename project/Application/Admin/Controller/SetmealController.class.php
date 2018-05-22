<?php
namespace Admin\Controller;
use Think\Controller;

/**
 * 充值套餐控制器
 * 后台用来设置充值套餐和浏览充值套餐的控制器
 * 
 * @author 潘宏钢 <619328391@qq.com>
 */

class SetmealController extends CommonController 
{
	/**
     * 套餐列表
     * 
     * @author 潘宏钢 <619328391@qq.com>
     */
    public function index()
    {	
       // 根据名称进行搜索
        $map = '';
        if (!empty(I('get.key')) && !empty(I('get.keywords'))) {
            $map[I('get.key')] = array('like',"%".I('get.keywords')."%");
        }
        // ---- 解决非第一页搜索条件$_GET['p']不等于1的情况【start】
        if(I('sou')){
            $_GET['p'] = 1;
            unset($_GET['sou']);
        }
        // ---- 【end】
        $type = M('setmeal');
        
        $total =$type->where($map)
                    ->join(' LEFT JOIN st_type ON st_setmeal.tid = st_type.id')
                    ->field('st_setmeal.*,st_type.typename')
                    ->count();
        $page  = new \Think\Page($total,8);
        // D('devices')->getPageConfig($page);
        $pageButton =$page->show();

        $list = $type->where($map)
                    ->limit($page->firstRow.','.$page->listRows)
                    ->join(' LEFT JOIN st_type ON st_setmeal.tid = st_type.id')
                    ->field('st_setmeal.*,st_type.typename')
                    ->order('addtime desc')
                    ->select();

        // dump($list);die;
        $this->assign('list',$list);
        $this->assign('button',bootstrap_page_style($pageButton));
        $this->display();
    }

    public function add()
    {
        if (IS_POST) {
            // dump($_POST);die;
            $_POST['addtime'] = time();
            $setmeal = D('setmeal');
            $info = $setmeal->create();
           
            if($info){

                $res = $setmeal->add();
                if ($res) {
                    return $this->ajaxReturn(['code'=>200,'msg'=>'套餐设置成功']);
                } else {
                    return $this->ajaxReturn(['code'=>500,'msg'=>'套餐设置失败']);
                }
            
            } else {
                // getError是在数据创建验证时调用，提示的是验证失败的错误信息
                return $this->ajaxReturn(['code'=>500,'msg'=>$setmeal->getError()]);
            }

        }else{
            $type = M('type');
            $list = $type->field('id,typename')->select();
            $this->assign('list',$list);
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
        if (IS_AJAX) {
            $map['id'] = I("post.id");
            $res = M('setmeal')->where($map)->delete();
            if($res) {
                $this->ajaxReturn(['code'=>200,'msg'=>'删除成功']);
            } else {
                $this->ajaxReturn(['code'=>200,'msg'=>'删除成功']);
            }
        }           
    }

    

}