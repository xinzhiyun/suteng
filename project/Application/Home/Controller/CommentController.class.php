<?php
namespace Home\Controller;
use Think\Controller;

class CommentController extends CommonController
{
    /**
     * [reward 发表评论]
     * @return [type] [description]
     */
    public function index()
    {
        $this->display();
    }

    /**
     * [reward 商品评论成功]
     * @return [type] [description]
     */
    public function commentsucceed()
    {
        try {
            $comment = D('Comment');
            $data = I('post.');
            $orderid = $data['orderid'];
            // dump($data);
            $data['uid'] = session('user.id');
            $savePath = 'Uploads/pic/';
            // 二进制文件上传简单处理
            if (!empty($_FILES["UploadForm"])) {
                foreach ($_FILES["UploadForm"]["tmp_name"] as $key => $value) {
                    $image = $_FILES["UploadForm"]["tmp_name"][$key];

                    $$key = fopen($image, "r");

                    $file = fread($$key, $_FILES["UploadForm"]["size"][$key]); //二进制数据流

                    $imgUp = new \Org\Util\ImageUpload(time().mt_rand(100000,9999999).'.png',$savePath);
                    $info[] = $imgUp->stream2Image($file);
                    fclose ( $$key );
                }
            }else{
                E('没有文件上传', 602);
            }   
        // print_r($info);
        // die;
        // print_r($_FILES);die;

           
            if(!$info) {// 上传错误提示错误信息
                E($upload->getError(),606);
            }
            if(!(count($info) <= 3)){

                E('只能添加三张图片',604);
            } 
            $comment->startTrans();
            // 根据订单获取所有商品
            $goods = D('order_detail')->where(['order_id'=>$orderid])->field('gid')->select();
            foreach($goods as $key => $value){
                // 判断该用户是否已经对这个商品评价过，如果评价过，就不能评价了           
                if($comment->where(['uid'=>$data['uid'],'gid'=>$value['gid'],'order_id'=>$orderid])->find()){                   
                    E('你已经评论过了，不能再次评论！', 605);
                    break;
                }
                $comments = [];
                $comments = [
                    'order_id' => $orderid,
                    'gid' => $value['gid'],
                    'uid' => $data['uid'],
                    'status' => $data['status'],
                    'content' => $data['content'],
                    'addtime' => time()
                ];
                $com_status[] = $comment->add($comments);
            }
            // print_r($comments);die;
            // unset($data['file']);
            foreach($com_status as $coms){
                foreach ($info as $key => $value) {
                    $com_pic[] = [
                        'cid' => $coms,
                        'path' => $value
                    ];
                }
            }
            
            
            // print_r($com_status);
            // dump($com_status);die;
            $pic_status = D("ComPic")->addAll($com_pic);
            if($com_status&&$pic_status){
                $res = D('ShopOrder')->where(['uid'=>$data['uid'],'order_id'=>$orderid])->setField(['status'=>7]);
                $comment->commit();
                E('评论成功', 200);
                // $this->success('评论成功');
            } else {
                $comment->rollback();
                E('评论失败', 603);
                // $this->error('评论失败');
            }
        } catch (\Exception $e) {
            $this->rmfiles($info);
            $err = [
                'code' => $e->getCode(),
                'msg' => $e->getMessage(),
            ];
            $this->ajaxReturn($err);
            // $this->error('评论失败');                     
        }
    }

    public function getComments($gid='')
    {
        $gid = I('get.gid');
        // $gid = 71;
        $map['status'] = 1; //查询不隐藏的评论
        $comment = D('Comment');
        if(empty($gid)){
            //  获取单个用户的评论
            $map['uid'] = session('user.id');
            // $map['uid'] = 27;
            $with = ['good','pics'];
        } else {
            // 获取单个商品的评论
            if(is_numeric($gid)){
                $map['gid'] = $gid;
                $with = ['user','pics'];
                $pageNumber = I('get.page_number')?I('get.page_number'):1;
                $pageSize = I('get.page_size')?I('get.page_size'):10;
                $comment->page($pageNumber.','.$pageSize);
            } else {
                return $this->ajaxReturn(['code'=>401,'msg'=>'参数错误']);
            }           
        }
        $data = $comment->where($map)->relation($with)->select();
        return $this->ajaxReturn(['code'=>200,'data'=>$data]);
    }

    /**
     * [saleServic 售后评分]
     * @return [type] [description]
     */
    public function saleServic()
    {
        $this->display();
    }

    /**
     * [删除文件]
     * @param  Array $files 文件路径数组
     * @return Boolean        
     */
    public function rmfiles($files)
    {
        foreach ($files as $key => $value) {
            return unlink($value);
        }
    }
}
