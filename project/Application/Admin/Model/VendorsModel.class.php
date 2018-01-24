<?php
namespace Admin\Model;

/**
 * Class 分销商管理
 * @package Admin\Model\
 * @author 吴智彬 <519002008@qq.com>
 */
class VendorsModel extends BaseModel
{   
    /**
     * [$_validate 自动验证]
     * @var array
     */
    protected $_validate = array(
        array('name','/^[\w\x{4e00}-\x{9fa5}]{1,32}$/u','用户名称格式不对！',1,'regex'), // 真实用户名，不为空时验证，数字字母下划线中文 1-32位   
        array('phone','/^1((((3[0-35-9])|([5|8][0-9])|(4[5|7|9])|66|(7[3|5-8])|(9[8|9]))\d)|(34[0-8]))\d{7}$/','手机号码格式不对！',1,'regex'), // 正确的手机号码
        array('identity','/(^[1-9]\d{5}(18|19|([23]\d))\d{2}((0[1-9])|(10|11|12))(([0-2][1-9])|10|20|30|31)\d{3}[0-9Xx]$)|(^[1-9]\d{5}\d{2}((0[1-9])|(10|11|12))(([0-2][1-9])|10|20|30|31)\d{2}[0-9Xx]$)/','身份证号码格式不对！',1,'regex'), // 真实身份证号码，不为空时验证，15或18位身份证号码
        array('company','/^[\w\x{4e00}-\x{9fa5}]{2,50}$/u','公司名称格式不对！',1,'regex'), // 公司名称，不为空时验证，数字字母下划线中文 2-50位
        array('telephone','/^(((0\d{2,3})|(85[2|3]))\-)?([2-9]\d{6,7})(\-\d{1,4})?$/','电话号码格式不对！',1,'regex'), // 座机号码，不为空时验证，正确的座机号码支持 区号-电话号码-分机号码
        array('address','/^[\w\-\x{4e00}-\x{9fa5}]{6,255}$/u','公司地址格式不对！',1,'regex'), // 公司地址，不为空时验证，数字字母下划线横线中文 6-255位   
        array('user','','账号已经存在！',0,'unique',1), // 在新增的时候验证user字段是否唯一
        array('user','/^[a-zA-Z][\w]{1,29}$/','账号格式不对！',1,'regex'), // 字母开头，数字字母下划线，总长度 2-30位
        array('repassword','require','密码必须填写',1),
        array('repassword','password','确认密码不正确',0,'confirm'), // 验证确认密码是否和密码一致
    );

    /**
     * [$_auto 自动完成]
     * @var array
     */
    protected $_auto = array ( 
        array('password','md5',3,'function') ,    // 对password字段在新增和编辑的时候使md5函数处理
        array('addtime','time',1,'function'),     // 对addtime字段在新增和编辑的时候写入当前时间戳
        array('updatetime','time',3,'function'),  // 对update_time字段在更新的时候写入当前时间戳
    );

    /**
     * [getAll 处理查询数据]
     * @return [array] [处理后的数据]
     */
    public function getAll($map)
    {   

        $list = $this->where($map)->order('updatetime desc')->select();
        
        $leavel = array('超级管理员','分公司','A级分销商','B级分销商','C级分销商');
        $status = array('身份信息填写','公司信息填写','签署协议','等待审批','身份证审批失败','公司信息审批失败','协议审批失败','审批成功','禁用分销商');
        foreach ($list as $key => $val) {
            $list[$key]['leavel'] = $leavel[$val['leavel']];
            $list[$key]['status'] = $status[$val['status']];
        }
        return $list;
    }

    /**
     * [companyList 分公司列表]
     * @return [type] [description]
     */
    public function companyList(){
        // 查询分公司数据
        $list = $this->where('`leavel`=1')->order('updatetime desc')->select();
        // 返回格式化后数据
        return $this->formatData($list);

    }

    /**
     * [vendorReviewed 分销商待审核列表]
     * @return [type] [description]
     */
    public function vendorReviewed(){
        
        // 查询分公司数据
        $list = $this->where('`leavel`>1 AND `status`=3')->order('updatetime desc')->select();
        // 返回格式化后数据
        return $this->formatData($list);
    }
    

    /**
     * [companyList 分销商列表]
     * @return [type] [description]
     */
    public function vendorList(){
        // 查询分公司数据
        $list = $this->where('`leavel`>1')->order('updatetime desc')->select();
        // 返回格式化后数据
        return $this->formatData($list);

    }

    public function formatData($list){
        $leavel = array('超级管理员','分公司','A级分销商','B级分销商','C级分销商');
        $status = array('身份信息填写','公司信息填写','签署协议','等待审批','身份证审批失败','公司信息审批失败','协议审批失败','审批成功','禁用分销商');
        foreach ($list as $key => $val) {
            $list[$key]['leavel'] = $leavel[$val['leavel']];
            $list[$key]['status'] = $status[$val['status']];
        }
        return $list;
    }
}