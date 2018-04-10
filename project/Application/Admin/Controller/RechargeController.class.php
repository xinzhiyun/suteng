<?php
namespace Admin\Controller;
use Admin\Controller\CommonController;

/**
 * 充值控制器
 */
class RechargeController extends CommonController
{
	// {{:U('Admin/Recharge/add_gold')}}
	/**
	 * [添加金币套餐]
	 */
    public function add_gold()
    {
    	if(IS_POST){
            try {
        		// 创建数据
    			$post = I('post.');
    			// 实例化验证类
    			$validate 	= new \Org\Util\Validate;
    			// 金额正则验证
    			if($validate->original('/^[\d]{1,10}[\.][\d]{2}$/',$post['money'])){
    				$addData['money'] = $post['money'];
    			}else{
    				E('套餐金额格式不正确！',603);
    			}
    			// 正则验证金币数量
    			if($validate->original('/^[\d]+$/',$post['gold_num'])){
    				$addData['gold_num'] = $post['gold_num'];
    			}else{
    				E('金币数量格式不正确！',603);
    			}
    			// 正则验证描述
    			if($validate->original('/^.{0,50}$/',$post['content'])){
    				$addData['content'] = $post['content'];
    			}else{
    				E('套餐描述格式不正确！',603);
    			}
    			// 是否上架
    			if($post['status']){
    				$addData['status'] = 1;
    			}
    			// 设置添加时间和更新时间
    			$addData['addtime'] = $addData['updatetime'] = time();
            	// 实例化金币套餐模型
        		$addRes = M('gold')->add($addData);
        		// 执行插入操作
        		if($addRes){
        			E('套餐添加成功！',200);
        		}else{
        			E('套餐添加失败！',605);
        		}
            } catch (\Exception $e) {
                $err = [
                    'code' => $e->getCode(),
                    'msg' => $e->getMessage(),
                ];
                $this->ajaxReturn($err);
            }

    	}else{
    		$this->display();
    	}
    }

	/**
	 * [添加银币套餐]
	 */
    public function add_silver()
    {
    	if(IS_POST){
            try {
        		// 创建数据
    			$post = I('post.');
    			// 实例化验证类
    			$validate 	= new \Org\Util\Validate;
    			// 金额正则验证
    			if($validate->original('/^[\d]{1,10}[\.][\d]{2}$/',$post['money'])){
    				$addData['money'] = $post['money'];
    			}else{
    				E('套餐金额格式不正确！',603);
    			}
    			// 正则验证金币数量
    			if($validate->original('/^[\d]+$/',$post['silver_num'])){
    				$addData['silver_num'] = $post['silver_num'];
    			}else{
    				E('银币数量格式不正确！',603);
    			}
    			// 正则验证描述
    			if($validate->original('/^.{0,50}$/',$post['content'])){
    				$addData['content'] = $post['content'];
    			}else{
    				E('套餐描述格式不正确！',603);
    			}
    			// 是否上架
    			if($post['status']){
    				$addData['status'] = 1;
    			}
    			// 设置添加时间和更新时间
    			$addData['addtime'] = $addData['updatetime'] = time();
            	// 实例化金币套餐模型
        		$addRes = M('silver')->add($addData);
        		// 执行插入操作
        		if($addRes){
        			E('套餐添加成功！',200);
        		}else{
        			E('套餐添加失败！',605);
        		}
            } catch (\Exception $e) {
                $err = [
                    'code' => $e->getCode(),
                    'msg' => $e->getMessage(),
                ];
                $this->ajaxReturn($err);
            }

    	}else{
    		$this->display();
    	}
    }

	/**
	 * [添加金币兑换银币套餐]
	 */
    public function add_gold_silver()
    {
    	if(IS_POST){
            try {
        		// 创建数据
    			$post = I('post.');
    			// 实例化验证类
    			$validate 	= new \Org\Util\Validate;
    			// 金额正则验证
    			if($validate->original('/^[\d]{1,11}$/',$post['gold'])){
    				$addData['gold'] = $post['gold'];
    			}else{
    				E('套餐金币格式不正确！',603);
    			}
    			// 正则验证金币数量
    			if($validate->original('/^[\d]+$/',$post['silver_num'])){
    				$addData['silver_num'] = $post['silver_num'];
    			}else{
    				E('银币数量格式不正确！',603);
    			}
    			// 正则验证描述
    			if($validate->original('/^.{0,50}$/',$post['content'])){
    				$addData['content'] = $post['content'];
    			}else{
    				E('套餐描述格式不正确！',603);
    			}
    			// 是否上架
    			if($post['status']){
    				$addData['status'] = 1;
    			}
    			// 设置添加时间和更新时间
    			$addData['addtime'] = $addData['updatetime'] = time();
    			// print_r($addData);die;
            	// 实例化金币套餐模型
        		$addRes = M('gold_silver')->add($addData);
        		// 执行插入操作
        		if($addRes){
        			E('套餐添加成功！',200);
        		}else{
        			E('套餐添加失败！',605);
        		}
            } catch (\Exception $e) {
                $err = [
                    'code' => $e->getCode(),
                    'msg' => $e->getMessage(),
                ];
                $this->ajaxReturn($err);
            }

    	}else{
    		$this->display();
    	}
    }

	/**
	 * [更新金币套餐]
	 */
    public function update_gold()
    {
    	if(IS_POST){
            try {
        		// 创建数据
    			$post = I('post.');
    			// 准备查询条件
    			$showData['id'] = $post['saveid'];
    			// 查询套餐
    			$data = M('gold')->where($showData)->find();
	    		// 实例化验证类
	    		$validate 	= new \Org\Util\Validate;
	    		// 金额有数据，并和原金额不一样，进行验证
    			if(!empty($post['money'])  && $post['money'] != $data['money']){
	    			// 金额正则验证
	    			if($validate->original('/^[\d]{1,10}[\.][\d]{2}$/',$post['money'])){
	    				$addData['money'] = $post['money'];
	    			}else{
	    				E('套餐金额格式不正确！',603);
	    			}
    			}
    			
	    		// 金币数量有数据，并和原金币数量不一样，进行验证
    			if(!empty($post['gold_num'])  && $post['gold_num'] != $data['gold_num']){
	    			// 正则验证金币数量
	    			if($validate->original('/^[\d]+$/',$post['gold_num'])){
	    				$addData['gold_num'] = $post['gold_num'];
	    			}else{
	    				E('金币数量格式不正确！',603);
	    			}
	    		}

	    		// 金币套餐描述有数据，并和原金币套餐描述不一样，进行验证
    			if(!empty($post['content'])  && $post['content'] != $data['content']){
	    			// 正则验证描述
	    			if($validate->original('/^.{0,50}$/',$post['content'])){
	    				$addData['content'] = $post['content'];
	    			}else{
	    				E('套餐描述格式不正确！',603);
	    			}
	    		}

	    		// 是否上架
	    		if(!empty($post['status'])){
	    			$post['status'] = 1;
	    		}else{
	    			$post['status'] = 0;
	    		}

	    		// 金币套餐描述有数据，并和原金币套餐描述不一样，进行验证
    			if($post['status'] != $data['status']){
    				// 更新状态
	    			$addData['status'] = $post['status'];
	    		}

	    		// 更新数据不为空
	    		if(!empty($addData)){
	    			// 设置更新时间
	    			$addData['updatetime'] = time();
	    			// print_r($addData);die;
	    			// print_r($data);die;
	            	// 实例化金币套餐模型
	        		$addRes = M('gold')->where($showData)->save($addData);
	        		// 执行插入操作
	        		if($addRes){
	        			E('套餐修改成功！',200);
	        		}else{
	        			E('套餐修改失败！',605);
	        		}
	    		}else{
	    			// 未修改
	    			E('您没有修改该套餐！',605);
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

	/**
	 * [更新银币套餐]
	 */
    public function update_silver()
    {
    	if(IS_POST){
            try {
        		// 创建数据
    			$post = I('post.');
    			// 准备查询条件
    			$showData['id'] = $post['saveid'];
    			// 查询套餐
    			$data = M('silver')->where($showData)->find();
	    		// 实例化验证类
	    		$validate 	= new \Org\Util\Validate;
	    		// 金额有数据，并和原金额不一样，进行验证
    			if(!empty($post['money'])  && $post['money'] != $data['money']){
	    			// 金额正则验证
	    			if($validate->original('/^[\d]{1,10}[\.][\d]{2}$/',$post['money'])){
	    				$addData['money'] = $post['money'];
	    			}else{
	    				E('套餐金额格式不正确！',603);
	    			}
    			}
    			
	    		// 金币数量有数据，并和原金币数量不一样，进行验证
    			if(!empty($post['silver_num'])  && $post['silver_num'] != $data['silver_num']){
	    			// 正则验证金币数量
	    			if($validate->original('/^[\d]+$/',$post['silver_num'])){
	    				$addData['silver_num'] = $post['silver_num'];
	    			}else{
	    				E('银币数量格式不正确！',603);
	    			}
	    		}

	    		// 金币套餐描述有数据，并和原金币套餐描述不一样，进行验证
    			if(!empty($post['content'])  && $post['content'] != $data['content']){
	    			// 正则验证描述
	    			if($validate->original('/^.{0,50}$/',$post['content'])){
	    				$addData['content'] = $post['content'];
	    			}else{
	    				E('套餐描述格式不正确！',603);
	    			}
	    		}

	    		// 是否上架
	    		if(!empty($post['status'])){
	    			$post['status'] = 1;
	    		}else{
	    			$post['status'] = 0;
	    		}

	    		// 金币套餐描述有数据，并和原金币套餐描述不一样，进行验证
    			if($post['status'] != $data['status']){
    				// 更新状态
	    			$addData['status'] = $post['status'];
	    		}

	    		// 更新数据不为空
	    		if(!empty($addData)){
	    			// 设置更新时间
	    			$addData['updatetime'] = time();
	    			// print_r($addData);die;
	    			// print_r($data);die;
	            	// 实例化金币套餐模型
	        		$addRes = M('silver')->where($showData)->save($addData);
	        		// 执行插入操作
	        		if($addRes){
	        			E('套餐修改成功！',200);
	        		}else{
	        			E('套餐修改失败！',605);
	        		}
	    		}else{
	    			// 未修改
	    			E('您没有修改该套餐！',605);
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

	/**
	 * [更新金币兑换银币套餐]
	 */
    public function update_gold_silver()
    {
    	if(IS_POST){
            try {
        		// 创建数据
    			$post = I('post.');
    			// 准备查询条件
    			$showData['id'] = $post['saveid'];
    			// 查询套餐
    			$data = M('gold_silver')->where($showData)->find();
	    		// 实例化验证类
	    		$validate 	= new \Org\Util\Validate;
	    		// 金币有数据，并和原金币不一样，进行验证
    			if(!empty($post['gold'])  && $post['gold'] != $data['gold']){
	    			// 金额正则验证
	    			if($validate->original('/^[\d]{1,11}$/',$post['gold'])){
	    				$addData['gold'] = $post['gold'];
	    			}else{
	    				E('套餐金币格式不正确！',603);
	    			}
    			}
    			
	    		// 银币数量有数据，并和原银币数量不一样，进行验证
    			if(!empty($post['silver_num'])  && $post['silver_num'] != $data['silver_num']){
	    			// 正则验证金币数量
	    			if($validate->original('/^[\d]+$/',$post['silver_num'])){
	    				$addData['silver_num'] = $post['silver_num'];
	    			}else{
	    				E('银币数量格式不正确！',603);
	    			}
	    		}

	    		// 金币套餐描述有数据，并和原金币套餐描述不一样，进行验证
    			if(!empty($post['content'])  && $post['content'] != $data['content']){
	    			// 正则验证描述
	    			if($validate->original('/^.{0,50}$/',$post['content'])){
	    				$addData['content'] = $post['content'];
	    			}else{
	    				E('套餐描述格式不正确！',603);
	    			}
	    		}

	    		// 是否上架
	    		if(!empty($post['status'])){
	    			$post['status'] = 1;
	    		}else{
	    			$post['status'] = 0;
	    		}

	    		// 金币套餐描述有数据，并和原金币套餐描述不一样，进行验证
    			if($post['status'] != $data['status']){
    				// 更新状态
	    			$addData['status'] = $post['status'];
	    		}

	    		// 更新数据不为空
	    		if(!empty($addData)){
	    			// 设置更新时间
	    			$addData['updatetime'] = time();
	            	// 实例化金币套餐模型
	        		$addRes = M('gold_silver')->where($showData)->save($addData);
	        		// 执行插入操作
	        		if($addRes){
	        			E('套餐修改成功！',200);
	        		}else{
	        			E('套餐修改失败！',605);
	        		}
	    		}else{
	    			// 未修改
	    			E('您没有修改该套餐！',605);
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

	/**
	 * [显示金币套餐列表]
	 */
    public function gold_list()
    {
        $map = '';
        if (!empty(I('get.key')) && !empty(I('get.keywords'))) {
            switch (I('get.keywords')) {
                case '上架':
                        $map['status'] = 1;
                    break;
                case '下架':
                        $map['status'] = 0;
                    break;
                
                default:
                    $map['status'] = I('get.keywords');
                    break;
                    
            }
        }
		$total = M('gold')->where($map)->count();
        $page  = new \Think\Page($total,8);
        page_config($page);
        $pageButton =$page->show();
        $data = M('gold')->where($map)->limit($page->firstRow.','.$page->listRows)->select();
        $assign = [
			'data' => $data,
			'page' =>bootstrap_page_style($pageButton)
        ];
        $this->assign($assign);
        $this->display();
    }

	/**
	 * [显示银币套餐列表]
	 */
    public function silver_list()
    {

        $map = '';
        if (!empty(I('get.key')) && !empty(I('get.keywords'))) {
            switch (I('get.keywords')) {
                case '上架':
                        $map['status'] = 1;
                    break;
                case '下架':
                        $map['status'] = 0;
                    break;
                
                default:
                    $map['status'] = I('get.keywords');
                    break;
            }
        }
        $total = M('silver')->where($map)->count();
        $page  = new \Think\Page($total,8);
        page_config($page);
        $pageButton =$page->show();
        $data = M('silver')->where($map)->limit($page->firstRow.','.$page->listRows)->select();
        $assign = [
			'data' => $data,
			'page' =>bootstrap_page_style($pageButton)
        ];
        $this->assign($assign);
        $this->display();
    }

	/**
	 * [显示金币兑换银币套餐列表]
	 */
    public function gold_silver_list()
    {
        $map = '';
        if (!empty(I('get.key')) && !empty(I('get.keywords'))) {
            switch (I('get.keywords')) {
                case '上架':
                        $map['status'] = 1;
                    break;
                case '下架':
                        $map['status'] = 0;
                    break;
                
                default:
                    $map['status'] = I('get.keywords');
                    break;
            }
        }
        $total = M('gold_silver')->where($map)->count();
        $page  = new \Think\Page($total,8);
        page_config($page);
        $pageButton =$page->show();
        $data = M('gold_silver')->where($map)->limit($page->firstRow.','.$page->listRows)->select();
        $assign = [
			'data' => $data,
			'page' =>bootstrap_page_style($pageButton)
        ];
        $this->assign($assign);
        $this->display();
    }

	/**
	 * [删除金币套餐]
	 */
    public function del_gold()
    {
    	if(IS_POST){
    		$this->del('gold');
    	}
    }

	/**
	 * [删除银币套餐]
	 */
    public function del_silver()
    {
    	if(IS_POST){
    		$this->del('silver');
    	}
    }

	/**
	 * [删除银币兑换套餐]
	 */
    public function del_gold_silver()
    {
    	if(IS_POST){
    		$this->del('gold_silver');
    	}
    }

    // 删除套餐
    public function del($table)
    {
        try {
	    	// 准备删除条件
	    	$delData['id'] = I('post.id');
	    	// 执行删除操作
	    	$delRes = M($table)->where($delData)->delete();
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

	/**
	 * [更新上下架]
	 */
    public function status($table)
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
        	$saveRes = M($table)->where($whereData)->save($saveData);
        	// 执行更新操作
        	if($saveRes){
        		E($post['status']?'套餐下架成功':'套餐上架成功',200);
        	}else{
        		E($post['status']?'套餐下架失败':'套餐上架失败',605);
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
	 * [更新金币套餐上下架]
	 */
    public function gold_status()
    {
    	if(IS_POST){
    		$this->status('gold');
    	}
    }

	/**
	 * [更新银币套餐上下架]
	 */
    public function silver_status()
    {
    	if(IS_POST){
    		$this->status('silver');
    	}
    }

	/**
	 * [更新银币兑换套餐上下架]
	 */
    public function gold_silver_status()
    {
    	if(IS_POST){
    		$this->status('gold_silver');
    	}
    }

	/**
	 * [根据ID获取金币套餐]
	 */
    public function get_id_gold()
    {
        // 准备查询添加
    	$this->get_id('gold');
    }

	/**
	 * [根据ID获取银币套餐]
	 */
    public function get_id_silver()
    {
        // 准备查询添加
    	$this->get_id('silver');
    }

	/**
	 * [根据ID获取金币兑换银币套餐]
	 */
    public function get_id_gold_silver()
    {
        // 准备查询添加
    	$this->get_id('gold_silver');
    }

	/**
	 * [根据ID获取套餐]
	 */
    public function get_id($table)
    {
        // 准备查询添加
        $showData['id'] = I('post.id');
        // 执行查询
        $arr = M($table)->where($showData)->find();

        // 判断数据是否获取成功
        if($arr){
        	$data['data'] = $arr;
        	$data['code'] = 200;
        	$data['msg']	= '套餐数据获取成功！';
        }else{
        	$data['data'] = null;
        	$data['code'] = 605;
        	$data['msg']	= '套餐数据获取失败！';
        }

        // 返回JSON数据
        $this->ajaxReturn($data);
    }
}