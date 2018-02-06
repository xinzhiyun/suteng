<?php
namespace Home\Controller;

class SuggestController extends CommonController
{

    /**
     * [index 建议主页]
     * @return [type] [description]
     */
    public function index()
    {
    		$this->display();    
    }

    public function add()
    {
    	if (IS_AJAX) {
    		$data['uid'] = $_SESSION['user']['id'];
    		$data['content'] = I('post.content');
    		$feeds = D("feeds");
	        $res = $feeds->add($data); 
	        if($res){
	            return $this->ajaxReturn(['state'=>true,'msg'=>'添加成功','data'=>$data]);
	        }else{
	            return $this->ajaxReturn(['state'=>false,'msg'=>'添加失败','data'=>$data]);
	        }
    	}
    }
}


