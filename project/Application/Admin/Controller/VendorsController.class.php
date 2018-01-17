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

    // 经销商列表
    public function vendor_list()
    {
        // 查询条件
        $_GET['code'] = '1';
        $map['code'] = empty($_GET['code'])?'':array('like',"%{$_GET['code']}%");

        $data = D('vendors')->getAll($map);
        $assign = [
            'data' => $data,
        ];
        $this->assign($assign);
        $this->display();
    }

    // 经销商审核列表
    public function vendor_reviewed()
    {
        $map['parent_code'] = 156345;
        $map['status'] = 0;
        $parent_user = M('vendors')->where("code=".$map['parent_code'])->getField('user');
        $data = M('vendors')->where($map)->select();
        $assign = [
            'data' => $data,
            'parent_user' => $parent_user,
        ];
        $this->assign($assign);
        $this->display();
    }

    // 审核
    public function reviewed()
    {
        $id = I('post.');
        $data = ['status' => 1, 'updatetime' => time()];
        $res = M('vendors')->where($id)->save($data);
        if($res){
            $message = ['code' => 200, 'message' => 'OK'];
        } else {
            $message = ['code' => 403, 'message' => '审核失败'];
        }
        $this->ajaxReturn($message);
    }

    /**
     * [vendor_add 经销商添加]
     * @return [type] [description]
     */
    public function vendor_add(){
        if(IS_POST){
            $vendors = D("vendors"); // 实例化User对象
            if (!$vendors->create()){ // 创建数据对象
                 // 如果创建失败 表示验证没有通过 输出错误提示信息
                 $this->success($vendors->getError());
                 return false;
            }else{
                 // 验证通过 写入新增数据
                if($vendors->add()){
                    $this->success('申请已经提交，待审核中');
                    return false;
                }else{
                    $this->error('提交失败了！');
                    return false;
                }
            }
        }else{
            $this->display();
        }
    }



    // 简单的验证一下不能有空的数据
    public function check_empty($data)
    {
        if(is_array($data)){
            foreach ($data as $key => $value) {
                if(empty($value))
                {
                    return "所有的信息必须填完";
                }
            }
        }
    }

    // 查询上级经销商
    public function check_parent_code($parent)
    {
        $res = M('vendors')->where($parent)->find();
        if(is_null($res)) return "参数错误";
        return $res;
    }

    // 整理经销商添加的数据
    public function action_add_vendor($data, $parent_code, $vendor_code)
    {
        $leavel = $parent_code['leavel'] + 1;
        if($leavel >= 3){
            $this->error('无法添加分销商');
            return false;
        }
        $res = [
            'name'        => $data['name'],
            'phone'       => $data['phone'],
            'telphone'    => $data['telphone'],
            'idcard'      => $data['idcard'],
            'parent_code' => $parent_code['code'],
            'code'        => $vendor_code,
            'leavel'      => $leavel,
            'user'        => $data['user'],
            'password'    => md5($data['password']),
            'addtime'     => time(),
            'updatetime'  => time(),
        ];

        return $res;
    }

    // 验证添加的经销商是否有重复添加的行为
    public function check_vendor_addinfo($add_info)
    {
        // 逻辑暂时为空
        $res = M('vendors')->where("idcard='{$add_info['idcard']}'")->getField('idcard');
        return $res;
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

            // 图片超出上传文件大小报错组
            $typeError = array(
                'positive' =>  '身份证正面图片类型错误,允许jpg, gif, png, jpeg',
                'opposite' =>  '身份证反面图片类型错误,允许jpg, gif, png, jpeg',
                'handheld' =>  '手持身份证正面图片类型错误,允许jpg, gif, png, jpeg',
                'licence'  =>  '营业执照图片类型错误,允许jpg, gif, png, jpeg',
                'protocol' =>  '协调文件图片类型错误,允许jpg, gif, png, jpeg',
            );

            // 允许的图片类型
            $type = array(
                "image/jpg",
                "image/png",
                "image/gif",
                "image/jpeg",
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

                // 图片类型检测
                if(!in_array($_FILES[$key]['type'], $type)){
                    // 反馈XX图片类型错误
                    $this->error($typeError[$key]);
                    return false;
                }
            }
            
            // 处理图片
            $info = $this->upload();
            // 检查图片是否上传成功
            if($info){
                $vendors = D("vendors"); // 实例化User对象
                $data = $vendors->create();
                if (!$data){ // 创建数据对象
                     // 如果创建失败 表示验证没有通过 输出错误提示信息
                     $this->success($vendors->getError());
                     return false;
                }else{
                    $getCode            = new DimensionController;
                    $ticket             = $getCode->office();
                    $newData            = array_merge($data,$info); // 将图片合并入数据中
                    $newData['code']    = $this->vendor_code();     // 将分公司唯一标识加人数组中
                    $newData['leavel']  = 1;
                    $newData['status']  = 3;
                    $newData = array_merge($newData,$ticket);
                    //dump($newData);die;
                    // 验证通过 写入新增数据
                    if($vendors->add($newData)){
                        $this->success('申请已经提交，待审核中');
                        return false;
                    }else{
                        $this->error('提交失败了！');
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
     * [upload 图片上传]
     * @return [type] [description]
     */
    public function upload()
    {
        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize   =     3145728;// 设置附件上传大小3MB
        $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
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
