<?php
namespace Admin\Controller;
use Admin\Controller\CommonController;
use Think\Controller;

/**
 * Class DevicesController
 * @package Admin\Controller
 * @author 陈昌平 <chenchangping@foxmail.com>
 */
class DevicesController extends CommonController
{
    /**
     * 显示设备列表
     */
    public function devicesList()
    {
        $device = D('Devices');
        // 查询条件
        $map = '';
        if(!empty($_GET['code'])) $map['device_code'] = array('like',"%{$_GET['code']}%");
        $count = $device                                                            
            ->where($map)
            ->alias('d')
            ->join('__TYPE__ t ON d.type_id=t.id', 'LEFT')
            ->join('__DEVICES_STATU__ ds ON d.device_code=ds.DeviceID', 'LEFT')
            ->join('__VENDORS__ v ON d.vid=v.id', 'LEFT')
            ->limit($Page->firstRow.','.$Page->listRows)
            ->field('d.device_code,d.type_id,d.addtime,v.user,d.device_statu,t.typename,ds.AliveStause')
            ->count();
        $Page       = new \Think\Page($count,20);
        page_config($Page);
        $show       = $Page->show();
        $devices = $device
            ->where($map)
            ->alias('d')
            ->join('__TYPE__ t ON d.type_id=t.id', 'LEFT')
            ->join('__DEVICES_STATU__ ds ON d.device_code=ds.DeviceID', 'LEFT')
            ->join('__VENDORS__ v ON d.vid=v.id', 'LEFT')
            ->limit($Page->firstRow.','.$Page->listRows)
            ->field('d.device_code,d.type_id,d.addtime,v.user,d.device_statu,t.typename,ds.AliveStause')
            ->select();
        $filterType = M('type')->where(['status'=>0])->select();
        // dump($devices);
        $assign = [
            'deviceInfo' => $devices,
            'deviceType' => $filterType,
            'page'=> $show,
        ];
        $this->assign($assign);
        $this->display('devicesList');
    }

    /**
     * 显示设备添加页面
     */
    public function show_add_device()
    {
        $res = M('DeviceType')->select();
        $this->assign('res', $res);
        $this->display('show_add_device');
    }

    /**
     * 设备添加处理
     */
    public function add_device( $code=null )
    {
        try {
            $devices = D('devices');
            $code = I('post.');
            if(!$devices->create()){
                E($devices->getError(), 202);
            }
            if(!$devices->add()){
                E('添加失败', 201);
            } else {
                E('添加成功', 200);
            }
        } catch (\Exception $e) {
            $err = [
                'code' => $e->getCode(),
                'msg' => $e->getMessage(),
            ];
            return $this->ajaxReturn($err);
        }

    }

    // 设备详情
    public function deviceDetail()
    {
        $map['device_code'] = I('get.code');
        $device = D('Devices');
        // 状态信息
        $statu = $device->getDeviceInfo($map);

        // 滤芯信息
        $filter = $device->getFilterInfo($map);
        $filterInfo = $device->getFilterDetail($filter);
        $assign = [
            'statu' => $statu,
            'filterInfo' => $filterInfo,
            'filter' => $filter,
        ];
        dump($assign);
        $this->assign($assign);
        $this->display('devices_detail');
    }

    /**
     * 批量上传
     * @return [type] [description]
     */
    public function upload()
    {
        try {
            header("Content-Type:text/html;charset=utf-8");
            $upload = new \Think\Upload(); // 实例化上传类
            $upload->maxSize = 3145728; // 设置附件上传大小
            $upload->exts = array(
                'xls',
                'xlsx'
            ); // 设置附件上传类
            $upload->savePath = '/'; // 设置附件上传目录
            $info = $upload->uploadOne($_FILES['batch']);
            $filename = './Uploads' . $info['savepath'] . $info['savename'];
            $exts = $info['ext'];
            if (! $info) {
                // 上传错误提示错误信息
                E($upload->getError(),202);
            } else {
                // 上传成功
                $this->goods_import($filename, $exts);

            }
        } catch (\Exception $e) {
            $err = [
                'code' => $e->getCode(),
                'msg' => $e->getMessage(),
            ];
            $this->ajaxReturn($err);
        }
    }

    public function save_import($data)
    {
        $i = 0;
        foreach ($data as $key => $val) {
            $_POST['device_code'] = $val['A'];
            $_POST['type_id'] = $val['B'];
            $Devices = D('Devices');
            $res = D('Devices')->getCate();
            $info = $Devices->create();
            if($info){
                $res = $Devices->add();
                if (!$res) {
                    E('导入失败啦！', 202);
                }
            } else {
                E('第'.($i+1).'条数据开始不正确或是已经添加', 203);
            }
            $i ++;
        }
        E('共'.$i.'条数据导入成功', 200);
    }

    private function getExcel($fileName, $headArr, $data)
    {
        vendor('PHPExcel');
        $date = date("Y_m_d", time());
        $fileName .= "_{$date}.xls";
        $objPHPExcel = new \PHPExcel();
        $objProps = $objPHPExcel->getProperties();
        // 设置表头
        $key = ord("A");
        foreach ($headArr as $v) {
            $colum = chr($key);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($colum . '1', $v);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($colum . '1', $v);
            $key += 1;
        }
        $column = 2;
        $objActSheet = $objPHPExcel->getActiveSheet();

        foreach ($data as $key => $rows) {
            // 行写入
            $span = ord("A");
            foreach ($rows as $keyName => $value) {
                // 列写入
                $j = chr($span);
                $objActSheet->setCellValue($j . $column, $value);
                $span ++;
            }
            $column ++;
        }

        $fileName = iconv("utf-8", "gb2312", $fileName);
        // 重命名表
        // 设置活动单指数到第一个表,所以Excel打开这是第一个表
        $objPHPExcel->setActiveSheetIndex(0);
        header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition: attachment;filename=\"$fileName\"");
        header('Cache-Control: max-age=0');

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output'); // 文件通过浏览器下载
        exit();
    }

    protected function goods_import($filename, $exts = 'xls')
    {
        // 导入PHPExcel类库，因为PHPExcel没有用命名空间，只能inport导入
        vendor('PHPExcel');
        // 创建PHPExcel对象，注意，不能少了\
        $PHPExcel = new \PHPExcel();
        // 如果excel文件后缀名为.xls，导入这个类
        if ($exts == 'xls') {
            $PHPReader = new \PHPExcel_Reader_Excel5();
        } else
            if ($exts == 'xlsx') {
                $PHPReader = new \PHPExcel_Reader_Excel2007();
            }

        // 载入文件
        $PHPExcel = $PHPReader->load($filename);
        // 获取表中的第一个工作表，如果要获取第二个，把0改为1，依次类推
        $currentSheet = $PHPExcel->getSheet(0);
        // 获取总列数
        $allColumn = $currentSheet->getHighestColumn();
        // 获取总行数
        $allRow = $currentSheet->getHighestRow();
        // 循环获取表中的数据，$currentRow表示当前行，从哪行开始读取数据，索引值从0开始
        for ($currentRow = 2; $currentRow <= $allRow; $currentRow ++) {
            // 从哪列开始，A表示第一列
            for ($currentColumn = 'A'; $currentColumn <= $allColumn; $currentColumn ++) {
                // 数据坐标
                $address = $currentColumn . $currentRow;
                // 读取到的数据，保存到数组$arr中
                $data[$currentRow][$currentColumn] = $currentSheet->getCell($address)->getValue();
            }
        }
        $this->save_import($data);
    }

    // 设备删除 
    public function del()
    {
        $code = I('get.');
        if(empty($code)){
            $this->error('设备编码错误');
        }
        $res = M('devices')->where($code)->find();
        if($res['uid']) $this->error("已绑定了用户，不可删除");
        if($res){
            $delBind = M('binding')->where('did='.$res['id'])->delete();
        }
        if($res || $delBind){
            $data = M('devices')->where($code)->delete();
        }
        if(!$data){
            $this->error('删除不成功');
        }
        $this->success('删除成功');

    }

    // 设备绑定经销商方法
    public function bind()
    {
        $vendors = M('vendors')->field('id,user,leavel')->select();
        $devices = M('devices')->where('bind_status=0')->select();
        $assign = [
            'user' => $vendors,
            'devices' => $devices,
        ];
        $this->assign($assign);
        $this->display();
    }

    // 设备绑定经销商
    public function bindAction()
    {
        try {
            $where['id'] = I('post.id');
            $data['vid'] = I('post.vid');
            if($_POST['vid'] == '--') E('请选择经销商','605');
            if($_POST['id'] == '--') E('请选择设备','604');
            $data['bind_status'] = 1;
            $res = M('devices')->where($where)->save($data);
            if($res){
                E('绑定成功',200);
            } else {
                E('绑定失败',603);
            }
        } catch (\Exception $e) {
            $err = [
                'code' => $e->getCode(),
                'msg' => $e->getMessage(),
            ];
            $this->ajaxReturn($err);
        }

    }
    // 滤芯显示
    public function filterList()
    {
        $filters = D('Filters');
        $data = $filters->where('status=0')->select();
        $assign = [
            'data' => $data,
        ];
        $this->assign($assign);
        $this->display();
    }

    // 滤芯添加处理
    public function filterAction()
    {
        try {
            $filters = D('Filters');
            $data    = I('post.');
            if(!$filters->create()) E($filters->getError(),'606');
            $upload           = new \Think\Upload();// 实例化上传类
            $upload->maxSize  =     3145728 ;// 设置附件上传大小
            $upload->exts     =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
            $upload->rootPath =     './Uploads/'; // 设置附件上传根目录
            $upload->savePath =     ''; // 设置附件上传（子）目录
            // 上传文件
            $info   =   $upload->upload();
            if(!$info) {
                E($upload->getError(),'606');
            }
            $data['picpath'] = $info['picpath']['savepath'].$info['picpath']['savename'];
            $data['addtime'] = time();
            $data['updatetime'] = time();
            // dump($data);
            $res = $filters->add($data);
            if($res){
                E('添加成功',200);
            } else {
                E('添加失败',603);
            }
        } catch (\Exception $e) {
            $err = [
                'code' => $e->getCode(),
                'msg'  => $e->getMessage(),
            ];
            $this->ajaxReturn($err);
        }
    }

    // 修改滤芯
    public function filterEdit()
    {
        try {
            $filter           = D('Filters');
            $data             = I('post.');
            $where['id']      = $data['id'];
            $upload           = new \Think\Upload();// 实例化上传类
            $upload->maxSize  =     3145728 ;// 设置附件上传大小
            $upload->exts     =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
            $upload->rootPath =     './Uploads/'; // 设置附件上传根目录
            $upload->savePath =     ''; // 设置附件上传（子）目录
            // 上传文件
            $info             =   $upload->upload();
            if($info) {
                $data['picpath'] = $info['picpath']['savepath'].$info['picpath']['savename'];
            }
            foreach ($data as $key => $value) {
                if(empty($value) || $value == 'undefined'){
                    unset($data[$key]);
                }
            }
            unset($data['id']);
            $data['updatetime'] = time();
            $res = $filter->where($where)->save($data);
            if($res){
                E('修改完成',200);
            } else {
                E('数据有误',603);
            }
        } catch (\Exception $e) {
            $err = [
                'code' => $e->getCode(),
                'msg'  => $e->getMessage(),
            ];
            $this->ajaxReturn($err);
        }
    }

    // 滤芯删除
    public function filtersDel()
    {
        try {
            $filter = D('Filters');
            $id['id'] = I('post.id');
            $res = $filter->where($id)->save(['status'=>1]);
            if($res) {
                E('删除成功', 200);
            } else {
                E('删除失败', 604);
            }
        } catch (\Exception $e) {
            $err = [
                'code' => $e->getCode(),
                'msg' => $e->getMessage(),
            ];
            $this->ajaxReturn($err);
        }
    }

    // 产品类型
    public function product()
    {
        $type = D('Type');
        $filters = D('Filters');
        $data = $type->where('status=0')->select();
        $filter = $filters->where(['status'=>0])->select();
        $assign = [
            'data' => $data,
            'filter' => $filter,
        ];
        $this->assign($assign);
        $this->display();
    }

    // 产品类型添加
    public function productAction()
    {
        try {
            $type = D('Type');
            $filter = I('post.arr');
            $data['typename'] = I('post.typename');
            $data['addtime'] = time();
            $data['updatetime'] = time();
            $i = 1;
            foreach ($filter as $key => $value) {
                // $res = M('filters')->where('id='.$value)->field('filtername,alias')->find();
                $data['filter'.$i] = $value;
                $i++;
            }
            if(!$type->create($data)) E($type->getError());
            $res = $type->add($data);
            if($res){
                E('设置成功', 200);
            } else {
                E('设置失败', 603);
            }
        } catch (\Exception $e) {
            $err = [
                'code' => $e->getCode(),
                'msg' => $e->getMessage(),
            ];
            $this->ajaxReturn($err);
        }
    }

    // 修改类型
    public function productEdit()
    {
        try {
            $type = D('Type');
            $arr = I('post.');
            $where['id'] = I('post.id');
            $data['typename'] = $arr['typename'];
            // $i = 1;
            // foreach ($arr['arr'] as $key => $value) {
            //     $data['filter'.$i] = $value;
            //     $i++;
            // }
            // dump($arr['arr']);
            for ($i=1; $i <= 8; $i++) { 
                $data['filter'.$i] = $arr['arr'][$i-1];
            }
            $data['updatetime'] = time();
            // dump($data);die;
            $res = $type->where($where)->save($data);
            if($res){
                E('更新成功',200);
            } else {
                E('请重新更新',603);
            }
        } catch (\Exception $e) {
            $err = [
                'code' => $e->getCode(),
                'msg' => $e->getMessage(),
            ];
            $this->ajaxReturn($err);
        }
    }

    // 类型删除
    public function productDel()
    {
        try {
            $type = D('Type');
            $where = I('post.');
            $type->startTrans();
            $res = $type->where($where)->delete();
            if($res){
                $type->commit();
                E('删除成功',200);
            } else {
                $type->rollback();
                E('无法删除', 603);
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
