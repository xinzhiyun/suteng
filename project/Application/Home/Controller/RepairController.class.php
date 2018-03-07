<?php
namespace Home\Controller;

class RepairController extends CommonController
{

    /**
     * [index 报修主页]
     * @return [type] [description]
     */
    public function index()
    {   

        // 调试用默认用户
        $openId = $_SESSION['homeuser']['open_id'];
        // 查询绑定设备
        $user_device = D('UserDevice');
        $map['ud.uid'] = session('user.id');

        $bind_device = $user_device->getBindInof($map); 
        $this->assign('bindInfo',$bind_device);
        
        $this->display();
    }

    public function add()
    {
    	if (IS_POST) {
    		$id = $_SESSION['user']['id'];
        	// $device_code = M('devices')->where($id)->find();

            
            $picpath = $this->upload();
            if ($picpath) {
                // 接收用户输入数据
                $repair = D('repair');
                // $device_code = M('Devices')->where('id='.session('device.did'))->getfield('device_code');
                if(!$repair->create()){
                    $this->delPic('./Public/'.$picpath[0]);
                    return $this->error($repair->getError());
                };
                $arr = array(
                    'device_code' => I('device_code'),
                    'date' => I('date'),
                    'truename' => I('truename'),
                    'phone' => I('phone'),
                    'reason' => I('reason'),
                    'content' => I('content'),
                    'uid' => $id,
                    'address' => I('address'),
                    'addtime' => time(),
                    'picpath' => $picpath[0]
                );

            }else{
                $this->error('您没有上传图片，请重新上传');
            }
            // 实例化
            $repair = M('repair');
            if ($repair->add($arr)) {
                $this->success('感谢您的建议，我们会仔细阅读并做出相应调整，谢谢！',U('Index/index'));
            }else{
                $this->delPic('./Public/'.$picpath);
                $this->error('一不小心服务器偷懒了~');
            }
	        
    	}
    }

    public function upload(){
	    $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize   =     5242880 ;// 设置附件上传大小
        $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
        $upload->rootPath  =     './Public/'; // 设置附件上传根目录
        $upload->savePath  =     '/Pic/repair/'; // 设置附件上传（子）目录
        // 上传文件 
        $info   =   $upload->upload();
        if(!$info) {// 上传错误提示错误信息
            return false;
            // $this->error($upload->getError());
        }else{
            // 上传成功
            foreach ($info as $file) {
                // dump($info);die;
                $pic[] = $file['savepath'].$file['savename'];
            }
            // $this->success('上传成功！');
            return $pic;
        }
	}

    public function delPic($file)
    {
        if (file_exists($file)) {
            return unlink($file);
        }
    }
}


