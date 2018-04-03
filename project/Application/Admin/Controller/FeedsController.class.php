<?php
namespace Admin\Controller;
use Think\Controller;

/**
 * 客户建议及报修控制器
 * 
 * @author 潘宏钢 <619328391@qq.com>
 */

class FeedsController extends CommonController 
{
	/**
     * 建议列表
     * @author 潘宏钢 <619328391@qq.com>
     */
    public function feedslist()
    {	
        // 根据用户昵称进行搜索
        $map = '';
        if (!empty(I('get.key')) && !empty(I('get.keywords'))) {
            $map[I('get.key')] = array('like',"%".I('get.keywords')."%");
        }
        $where['keywords'] =I('get.keywords');
        $user = M('feeds');
        $total = $user->where($map)
                        ->join('st_users ON st_feeds.uid = st_users.id')
                        ->field('st_feeds.*,st_users.nickname')
                        ->count();
        $page  = new \Think\Page($total,8);
        $pageButton =$page->show();

        $userlist = $user
                        // ->join('st_users ON st_feeds.uid = st_users.id','LEFT')
                        // ->field('st_feeds.*,st_users.nickname')->order(array('st_feeds.id'=>'desc'))
                        // ->limit($page->firstRow.','.$page->listRows)
                        ->select(false);

        dump($userlist);
        $this->assign('list',$userlist);
        $this->assign('where',$where);
        $this->assign('button',$pageButton);
        $this->display();
    }

    /**
     * 客户建议的删除功能
     * @author 潘宏钢 <619328391@qq.com>
     */
    public function feedsdel($id)
    {
        
        $res = M('feeds')->delete($id);
        if($res){
            return $this->ajaxReturn(['state'=>true,'msg'=>'删除成功']);
        }else{
            return $this->ajaxReturn(['state'=>false,'msg'=>'删除失败']);
        }
    
    }

    /**
     * 报修列表
     * @author 潘宏钢 <619328391@qq.com>
     */
    public function repairlist()
    {
        // 根据用户昵称进行搜索
        $map = '';
        if (!empty(I('get.key')) && !empty(I('get.keywords'))) {
            $map[I('get.key')] = array('like',"%".I('get.keywords')."%");

        }
        $user = M('repair');
        $total = $user->where($map)
                        ->join('st_users ON st_repair.uid = st_users.id')
                        ->field('st_repair.*,st_users.nickname')
                        ->count();
        $page  = new \Think\Page($total,8);
        $pageButton =$page->show();

        $userlist = $user->where($map)
                        ->join('st_users ON st_repair.uid = st_users.id')
                        ->field('st_repair.*,st_users.nickname')
                        ->limit($page->firstRow.','.$page->listRows)
                        ->select();
        // dump($userlist);
        $where['key'] =I('get.key');
        $where['keywords'] =I('get.keywords');

        $this->assign('list',$userlist);
        $this->assign('where',$where);
        $this->assign('button',$pageButton);
        $this->display(); 
    }

    /**
     * 报修更改状态
     * @author 潘宏钢 <619328391@qq.com>
     */
    public function edit($id,$status)
    {
        $work = M("repair");
        $res = $work->where('id='.$id)->setField('status',$status); 
        if($res){
            return $this->ajaxReturn(['state'=>true,'msg'=>'修改成功']);
        }else{
            return $this->ajaxReturn(['state'=>false,'msg'=>'修改失败']);
        }
    }
    
}