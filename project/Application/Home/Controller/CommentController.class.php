<?php
namespace Home\Controller;

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
            $upload = new \Think\Upload();// 实例化上传类
            $upload->maxSize   =     3145728 ;// 设置附件上传大小
            $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
            $upload->rootPath  =     './Uploads/'; // 设置附件上传根目录
            $upload->savePath  =     ''; // 设置附件上传（子）目录

            // 上传文件
            $info   =   $upload->upload();
            if(!$info) {// 上传错误提示错误信息
                E($upload->getError(),'606');
            }
            $comment->startTrans();
            $com_status = $comment->add($data);
            $com_pic['path'] = $info['pic']['savepath'].$info['pic']['savename'];
            $com_pic['cid'] = $com_status;
            $pic_status = M("com_pic")->add($com_pic);
            if($com_status&&$pic_status){
                $comment->commit();
                E('评论成功', 200);
            } else {
                $comment->rollback();
                E('评论失败', 603);
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
     * [saleServic 售后评分]
     * @return [type] [description]
     */
    public function saleServic()
    {
        $this->display();
    }

}
