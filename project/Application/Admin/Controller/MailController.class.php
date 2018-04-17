<?php
namespace Admin\Controller;
use Admin\Controller\CommonController;
use Think\Controller;

class MailController extends CommonController
{
	/**
	 * [index 发布公告]
	 * @return [type] [description]
	 */
    public function index()
    {	
    	if(IS_POST){
    		$addData = I('post.');

    		if(empty($addData['title'])){
    			$message    = ['code' => 403, 'message' => '公告标题必须填写!'];
    		}else if(empty($addData['content'])){
    			$message    = ['code' => 403, 'message' => '公告内容必须填写!'];
    		}else{
    			$addData['sender'] = $_SESSION['adminInfo']['user'];
	    		$addData['sender_code'] = $_SESSION['adminInfo']['code'];
				$addData['recipients'] = '所有人';
				$addData['recipients_code'] = '所有人';
				$addData['type'] = 0;
				$addData['status'] = 0;
				$addData['updatetime'] = $addData['addtime'] = time();

	    		$res = M('mail')->add($addData);

	    		if($res){
		            // 公告设置成功
		            $message    = ['code' => 200, 'message' => '公告已成功发布!'];
	    		}else{
	    			// 公告设置失败
		            $message    = ['code' => 403, 'message' => '公告发布失败!'];
	    		}
    		}

	        // 返回JSON格式数据
	        $this->ajaxReturn($message);

    	}else{
        	$this->display();
    	}
    }

	/**
	 * [announcement 公告列表]
	 * @return [type] [description]
	 */
    public function announcement()
    {	
    	$map['type'] = 0;

        if(!empty($_GET)){
        	if($_GET['key'] == 'title'){
        	    $map['title'] = array('like',"%{$_GET['keywords']}%");
        	}
        	if($_GET['key'] == 'content'){
        	    $map['content'] = array('like',"%{$_GET['keywords']}%");
        	}
        }

		$total = M('mail')->where($map)->count();
        $page  = new \Think\Page($total,8);
        page_config($page);
        $pageButton =$page->show();
    	$data = M('mail')->where($map)->limit($page->firstRow.','.$page->listRows)->order('addtime desc')->select();

	    $assign = [
			'data' => $data,
			'page' =>bootstrap_page_style($pageButton)
	    ];


	    // 分配数据
	    $this->assign($assign);

        $this->display();
    }

	/**
	 * [更新禁用启用]
	 */
    public function status()
    {
        try {
        	// 创建数据
    		$post = I('post.');
    		// 设置更新条件
    		$whereData['id'] 	= $post['id'];
    		// 设置更新数据
    		$saveData['status'] = $post['status'] ^ 1;
    		// 设置更新时间
    		$saveData['updatetime'] = time();
        	// 实例化金币套餐模型
        	$saveRes = M('mail')->where($whereData)->save($saveData);
        	// 执行更新操作
        	if($saveRes){
        		E($post['status']?'公告禁用成功':'公告启用成功',200);
        	}else{
        		E($post['status']?'公告禁用失败':'公告启用失败',605);
        	}
        } catch (\Exception $e) {
            $err = [
                'code' => $e->getCode(),
                'msg' => $e->getMessage(),
            ];
            $this->ajaxReturn($err);
        }
    }

    // 删除站内信
    public function del()
    {
        try {
	    	// 准备删除条件
	    	$delData['id'] = I('post.id');
	    	// 执行删除操作
	    	$delRes = M('mail')->where($delData)->delete();
        	// 执行更新操作
        	if($delRes){
        		E('删除成功',200);
        	}else{
        		E('删除失败',605);
        	}
        } catch (\Exception $e) {
            $err = [
                'code' => $e->getCode(),
                'msg' => $e->getMessage(),
            ];
            $this->ajaxReturn($err);
        }
    }

    /**
     * [send_email 发送站内信]
     * @return [type] [description]
     */
    public function send()
    {
    	if(IS_POST){

    	}else{
    		$this->display('send');
    	}
    }


    // 获取用户列表
    public function userlist()
    {
    	if(IS_POST){
	    	$user = I('post.user');
	    	$map['nickname'] = array('like',"%{$user}%");
	    	$data = M('users')->where($map)->field('code,nickname')->select();
	    	if($data){
	    		$message    = ['code' => 200, 'message' => '用户查询成功!', 'data' => $data];
	    	}else{
	    		$message    = ['code' => 403, 'message' => '没有匹配的用户!'];
	    	}

	        // 返回JSON格式数据
	        $this->ajaxReturn($message);
    	}
    }

    /**
     * 给用户发站内信
     */
    public function usermail()
    {
    	$post = I('post.');
    	// 准备用户查询条件
    	$showUser['code'] = $post['code'];



    	if(empty($post['title'])){
    		$message    = ['code' => 403, 'message' => '公告标题必须填写!'];
    	}else if(empty($post['content'])){
    		$message    = ['code' => 403, 'message' => '公告内容必须填写!'];
    	}else{
	    	// 查询用户
	    	$userData = M('users')->where($showUser)->field('code,nickname')->find();

	    	if($userData){
	    			$addData['sender'] = $_SESSION['adminInfo']['user'];
		    		$addData['sender_code']   = $_SESSION['adminInfo']['code'];
					$addData['recipients_code'] = $userData['code'];
					$addData['recipients'] = $userData['nickname'];
					$addData['title'] = $post['title'];
					$addData['content'] = $post['content'];
					$addData['type'] = 1;
					$addData['status'] = 2;
					$addData['updatetime'] = $addData['addtime'] = time();

		    		$res = M('mail')->add($addData);

		    		if($res){
		    			$message    = ['code' => 200, 'message' => '站内信已成功投递到用户信箱!'];	
		    		}else{
		    			$message    = ['code' => 403, 'message' => '站内信投递失败，请刷新后重试!'];
		    		}
	    	}else{
	    		$message    = ['code' => 403, 'message' => '收件人错误，没有这个用户!'];
	    	}
    	}

	    // 返回JSON格式数据
	    $this->ajaxReturn($message);
    }

	/**
	 * [mail 站内信列表]
	 * @return [type] [description]
	 */
    public function mail()
    {	
    	$map['type'] = 1;

        if(!empty($_GET)){

        	$map[$_GET['key']] = array('like',"%{$_GET['keywords']}%");

        	// if($_GET['key'] == 'content'){
        	//     $map['content'] = array('like',"%{$_GET['keywords']}%");
        	// }
        }

		$total = M('mail')->where($map)->count();
        $page  = new \Think\Page($total,8);
        page_config($page);
        $pageButton =$page->show();
    	$data = M('mail')->where($map)->limit($page->firstRow.','.$page->listRows)->select();

	    $assign = [
			'data' => $data,
			'page' =>bootstrap_page_style($pageButton)
	    ];

	    // 分配数据
	    $this->assign($assign);

        $this->display();
    }
}