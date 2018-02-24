<?php
namespace Admin\Controller;
use Admin\Controller\CommonController;
use Think\Controller;

class MailController extends CommonController
{
	/**
	 * [index 站内信首页]
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
				$addData['recipients'] = '所有人';
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
	 * [index 站内信首页]
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

    	$data = M('mail')->where($map)->select();

	    $assign = [
	        'data' => $data,
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

    // 删除套餐
    public function del()
    {
        try {
	    	// 准备删除条件
	    	$delData['id'] = I('post.id');
	    	// 执行删除操作
	    	$delRes = M('mail')->where($delData)->delete();
        	// 执行更新操作
        	if($delRes){
        		E('套餐删除成功',200);
        	}else{
        		E('套餐删除失败',605);
        	}
        } catch (\Exception $e) {
            $err = [
                'code' => $e->getCode(),
                'msg' => $e->getMessage(),
            ];
            $this->ajaxReturn($err);
        }
    }
}