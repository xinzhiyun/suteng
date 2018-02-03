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
    			// 设置添加时间和更新时间
    			$addData['addtime'] = $addData['updatetime'] = time();
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
        $this->display();
    }

	/**
	 * [更新银币套餐]
	 */
    public function update_silver()
    {
        $this->display();
    }

	/**
	 * [更新金币兑换银币套餐]
	 */
    public function update_gold_silver()
    {
        $this->display();
    }

	/**
	 * [显示金币套餐列表]
	 */
    public function gold_list()
    {
        $this->display();
    }

	/**
	 * [显示银币套餐列表]
	 */
    public function silver_list()
    {
        $this->display();
    }

	/**
	 * [显示金币兑换银币套餐列表]
	 */
    public function gold_silver_list()
    {
        $this->display();
    }

	/**
	 * [删除金币套餐]
	 */
    public function del_gold()
    {
        $this->display();
    }

	/**
	 * [删除银币套餐]
	 */
    public function del_silver()
    {
        $this->display();
    }

	/**
	 * [删除金币兑换银币套餐]
	 */
    public function del_gold_silver()
    {
        $this->display();
    }
}