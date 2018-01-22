<?php
namespace Admin\Controller;
use Home\Controller\DimensionController;
use Think\Controller;

class VendorsController extends CommonController
{
    public function index()
    {
        $this->display();
    }

    /**
     * [vendor_list 经销商列表]
     * @return [type] [description]
     */
    public function vendor_list()
    {
        $data = D('vendors')->vendorList();
        $assign = [
            'data' => $data,
        ];
        $this->assign($assign);
        $this->display();
    }

    /**
     * [company_add 分公司添加]
     * @return [type] [description]
     */
    public function company_add(){
        if(IS_POST){
            // 没有上传图片报错信息组
            $emptyError = array(
                'positive' =>  '身份证正面图片必须上传',
                'opposite' =>  '身份证反面图片必须上传',
                'handheld' =>  '手持身份证正面图片必须上传',
                'licence'  =>  '营业执照图片必须上传',
                'protocol' =>  '协调文件图片必须上传',
            );

            // 图片超出上传文件大小报错组
            $sizeError = array(
                'positive' =>  '身份证正面图片超出允许上传文件大小3M',
                'opposite' =>  '身份证反面图片超出允许上传文件大小3M',
                'handheld' =>  '手持身份证正面图片超出允许上传文件大小3M',
                'licence'  =>  '营业执照图片超出允许上传文件大小3M',
                'protocol' =>  '协调文件图片超出允许上传文件大小3M',
            );

            // 图片类型报错组
            $typeError = array(
                'positive' =>  '身份证正面图片类型错误,允许jpg, gif, png, jpeg',
                'opposite' =>  '身份证反面图片类型错误,允许jpg, gif, png, jpeg',
                'handheld' =>  '手持身份证正面图片类型错误,允许jpg, gif, png, jpeg',
                'licence'  =>  '营业执照图片类型错误,允许jpg, gif, png, jpeg',
                'protocol' =>  '协调文件图片类型错误,允许jpg, gif, png, jpeg,doc,docx',
            );

            // 允许的图片类型
            $type = array(
                "image/jpg",
                "image/png",
                "image/gif",
                "image/jpeg",
            );

            $typeDoc = array(
                "image/jpg",
                "image/png",
                "image/gif",
                "image/jpeg",
                'application/msword',
                'application/octet-stream',
            );

            // 遍历上传图片信息
            foreach ($_FILES as $key => $value) {
                // 不上传图片检测
                if($_FILES[$key]['error'] == 4){
                    // 反馈XX图片必须上传
                    $this->error($emptyError[$key]);
                    return false;
                }

                // 图片大小检测
                if($_FILES[$key]['size'] > 3145728){
                    // 反馈XX图片大小错误
                    $this->error($sizeError[$key]);
                    return false;
                }
            }

            // 接收上装文件
            $filesImg = $_FILES;
            // 从原数组中去除协议文件
            array_pop($filesImg);

            // 图片文件类型检查
            foreach ($filesImg as $key => $value) {
                // 图片类型检测
                if(!in_array($filesImg[$key]['type'], $type)){
                    // 反馈XX图片类型错误
                    $this->error($typeError[$key]);
                    return false;
                }
            }

            // 协议类型检测
            if(!in_array($_FILES['protocol']['type'], $typeDoc)){
                // 反馈XX图片类型错误
                $this->error($typeError['protocol']);
                return false;
            }
            
            // 开启事务
            $vendors = D("vendors");
            $vendors->startTrans();

            // 处理图片
            $info = $this->upload();

            // 检查图片是否上传成功
            if($info){
                // 图片上传成功，实例化vendors对象
                $data = $vendors->create();
                if (!$data){ // 创建数据对象
                     // 如果创建失败 表示验证没有通过 输出错误提示信息
                     $this->success($vendors->getError());
                     return false;
                }else{
                    // 实例化二维码信息类
                    $getCode            = new DimensionController;
                    // 获取分公司永久二维码
                    $ticket             = $getCode->office();
                    // 二维码获取成功
                    if($ticket){
                        // 将分公司唯一标识加人数组中
                        $data['code']    = $this->vendor_code();
                        // 设置级别为分公司
                        $data['leavel']     = 1;
                        // 设置状态为待审批
                        $data['status']     = 3;
                        // 设置添加责任人
                        $data['add_liable'] = $_SESSION['adminInfo']['user'];
                        // 将图片合并入数据中
                        $newData            = array_merge($data,$info,$ticket); 
                        //dump($newData);die;
                        // 验证通过 写入新增数据
                        if($vendors->add($newData)){
                            // 执行事务
                            $vendors->commit();
                            $this->success('申请已经提交，待审核中');
                            return false;
                        }else{
                            // 事务回滚
                            $vendors->rollback();
                            $this->error('资料提交失败了，请重新提交！');
                            return false;
                        }
                    }else{
                        $this->error('邀请二维码创建失败，请重新申请！');
                        return false;
                    }


                }
            }else{
                $this->error('图片上传失败，请重新上传！');
                return false;
            }
        }else{
            $this->display();
        }
    }

    /**
     * [company_list 查看分公司列表]
     * @return [type] [description]
     */
    public function company_list()
    {
        $data = D('vendors')->companyList();
        $assign = [
            'data' => $data,
        ];
        $this->assign($assign);
        $this->display();
    }

    /**
     * [vendor_reviewed 分销商审核列表]
     * @return [type] [description]
     */
    public function vendor_reviewed()
    {
        $data = D('vendors')->vendorReviewed();
        $assign = [
            'data' => $data,
        ];
        $this->assign($assign);
        $this->display();
    }

    /**
     * [vendor_reviewed 分销商审核列表]
     * @return [type] [description]
     */
    public function reviewed()
    {
        // 更新条件
        $saveData['id'] = I('post.id');
        // 更新数据
        $data['status'] = I('post.status');
        // 审核-责任人
        $data['auditing'] = $_SESSION['adminInfo']['user'];
        // 执行更新
        $res = D('vendors')->where($saveData)->save($data);
        // 判断信息是否修改成功
        if($res){
            // 查询成功，设置返回前端的数据
            $message     = ['code' => 200, 'message' => '审核成功'];
        } else {
            // 查询失败，设置返回前端的数据
            $message     = ['code' => 403, 'message' => '审核失败'];
        }
        // 返回JSON格式数据
        $this->ajaxReturn($message);
    }

    /**
     * [reviewed 分销商审核详情信息]
     * @return [type] [description]
     */
    public function vendor_data()
    {
        // 接收查询的ID号
        $showData['id'] = I('post.id');
        // 根据ID查询分公司详细信息
        $data = M('vendors')->where($showData)->find();
        // 判断信息是否查询成功
        if($data){
            // 查询成功，设置返回前端的数据
            $message['res']     = ['code' => 200, 'message' => 'OK'];
            $message['data']    = $data;
        } else {
            // 查询失败，设置返回前端的数据
            $message['res']     = ['code' => 403, 'message' => '查询失败'];
            $message['data']    = null;
        }
        // 返回JSON格式数据
        $this->ajaxReturn($message);
    }

    /**
     * [upload 图片上传]
     * @return [type] [description]
     */
    public function upload()
    {
        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize   =     3145728;// 设置附件上传大小3MB
        $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg','doc','docx');// 设置附件上传类型
        $upload->rootPath  =     './Public/'; // 设置附件上传根目录
        $upload->savePath  =     '/Vendors/'; // 设置附件上传（子）目录
        // 上传文件 
        $info   =   $upload->upload();
        if(!$info) {// 上传错误提示错误信息
            return false;
            // $this->error($upload->getError());
        }else{
            // 上传成功
            foreach ($info as $key=>$file) {
                // dump($info);die;
                $pic[$key] = $file['savepath'].$file['savename'];
            }
            // $this->success('上传成功！');
            return $pic;
        }
    }

    /**
     * 获取分销商唯一标识
     */
    public function vendor_code()
    {        
        do {
          // 生成分销商唯一标识
          $code = strtoupper(substr(md5(mt_rand(100000, 999999)), mt_rand(1,9), 6));
          //查询销商标识是否存在
          $oid = M('vendors')->where("`code`='{$code}'")->field('id')->find();
          // 如果订单号已存在再重新获取一次
        } while ($oid);

        // 绝对唯一的32位订单ID号
        return $code;
    }
}
