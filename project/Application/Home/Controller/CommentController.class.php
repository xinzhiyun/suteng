<?php
namespace Home\Controller;
use Think\Controller;
use \Org\Util\WeixinJssdk;
class CommentController extends CommonController
{
    /**
     * [reward 发表评论]
     * @return [type] [description]
     */
    public function index()
    {
        $this->wx_info();
        $this->display();
    }

    /**
     * [reward 商品评论成功]
     * @return [type] [description]
     */
    public function commentsucceed()
    {
        p(I(''));die;
        try {
            $comment = D('Comment');
            $data = I('post.');
            $orderid = $data['orderid'];
            // dump($data);
            // $_SESSION['user']['id'] = 27;
            $data['uid'] = session('user.id');


            // $savePath = 'Uploads/pic/';
            // // 二进制文件上传简单处理
            // if (!empty($_FILES["UploadForm"])) {
            //     foreach ($_FILES["UploadForm"]["tmp_name"] as $key => $value) {
            //         $image = $_FILES["UploadForm"]["tmp_name"][$key];

            //         $$key = fopen($image, "r");

            //         $file = fread($$key, $_FILES["UploadForm"]["size"][$key]); //二进制数据流

            //         $imgUp = new \Org\Util\ImageUpload(time().mt_rand(100000,9999999).'.png',$savePath);
            //         $info[] = $imgUp->stream2Image($file);
            //         fclose ( $$key );
            //     }
            // }else{
            //     $info = [];
            //     // E('没有文件上传', 602);
            // }   
  
            if(!empty($data['pic'])){
                $info = $this->downloadPic($data['pic']);
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
                    // 'status' => $data['status'],
                    'content' => $data['content'],
                    'addtime' => time()
                ];
                $com_status[] = $comment->add($comments);
            }
            // 处理图片写入
            if(!empty($info)){
                                // print_r($comments);die;
                // unset($data['file']);
                foreach($com_status as $coms){
                    // foreach ($info as $key => $value) {
                    //     $com_pic[] = [
                    //         'cid' => $coms,
                    //         'path' => $value
                    //     ];
                    // }
                    $com_pic[] = [
                            'cid' => $coms,
                            'path' => $info
                        ];
                }           
                // print_r($com_status);
                // dump($com_status);die;
                $pic_status = D("ComPic")->addAll($com_pic);
            }
            $res = D('ShopOrder')->where(['uid'=>$data['uid'],'order_id'=>$orderid])->setField(['status'=>7]);
            // var_dump($res);dump($orderid);
            if($res){                
                $comment->commit();
                E('评论成功', 200);
                $this->success('评论成功');
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

    public function comment(){
        // 1.获取参数
        $status = I('status');
        $content = I('content');
        $orderid = I('orderid');        
        $gid = 153;
        $pics = I('pic');
        $uid = session('user.id');

        $comment = D('Comment');
        // 2.验证参数
        // 3.判断是否已经评论过
        if($comment->where(['uid'=>$uid,'gid'=>$gid,'order_id'=>$orderid])->find()){
            $this->ajaxReturn(['code'=>605,'msg'=>'你已经评论过了，不能再次评论！']);
        }
        // 4.判断图片，处理文件上传
        if(!empty($pics)){
            $info = $this->downloadPic($pics);           
        }
        // p(I(''));die;
        // 5.处理添加评论
        $comment->startTrans();
            // 根据订单获取所有商品
            $data = array(
                'status' => $status,
                'order_id' => $orderid,
                'uid' => $uid,
                'gid' => $gid,
                'content' => $content,
                'addtime' => time()
            );
           $a = $comment->data($data)->add();

            D("ComPic")->data(['path'=>$info])->save();   
            $res = D('Order_detail')->where(['order_id'=>$orderid,'gid'=>$gid])->setField(['status'=>7]);

            $count = D('Order_detail')->where(['order_id'=>$orderid,'status'=>['NEQ',7]])->count();
            if($count < 1){
                D('ShopOrder')->where(['uid'=>$uid,'order_id'=>$orderid])->setField(['status'=>7]);
            }

        if($res){                
            $comment->commit();
            $this->ajaxReturn(['code'=>200,'msg'=>'评论成功']);
        } else {
            $comment->rollback();
            // E('评论失败', 603);
            $this->ajaxReturn(['code'=>603,'msg'=>'评论失败']);
            // $this->error('评论失败');
        }

        // 6.判断处理订单状态

    }

    //下载图片
    public function downloadPic($paths='')
    {

        $path_info = '/Pic/comment/'.date('Y-m-d',time());

        $file=md5($paths).".jpg";


        $dir =rtrim(THINK_PATH,"ThinkPHP/").'/Public'.$path_info;
        if(!is_dir($dir)){
            mkdir($dir,0777,true);
        }
        $path_info = $path_info.'/'.$file;

        $path = './Public'.$path_info;
        
        $weixin = new WeixinJssdk;
        $ACCESS_TOKEN = $weixin->getAccessToken();

        $url="https://api.weixin.qq.com/cgi-bin/media/get?access_token=$ACCESS_TOKEN&media_id=$paths";
        // $url = "http://img.taopic.com/uploads/allimg/140729/240450-140HZP45790.jpg";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        $file = curl_exec($ch);
        curl_close($ch);

        $resource = fopen($path, 'a');
        fwrite($resource, $file);
        fclose($resource);
        return $path_info;

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
            $with = ['good','pics','price'];
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
        $pic = D('pic');
        $shop_order = D('shopOrder');
        if(empty($gid)){
            foreach($data as $key => $value){
                $data[$key]['good']['pic'] = $pic->field('path')->getByGid($value['good']['id'])['path'];
                $data[$key]['good']['price'] = $shop_order->field('g_price')->getByOrderId($value['order_id'])['g_price'];
            }
        }
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
