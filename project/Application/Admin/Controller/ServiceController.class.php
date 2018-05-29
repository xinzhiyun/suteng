<?php
namespace Admin\Controller;


class ServiceController extends CommonController
{
    /**
     * 服务站管理
     */
    public function index()
    {
        $map = [];
        $area = M('area')->where('parentid=0')->select();


        $count = M('service')->where($map)->count();
        $Page       = new \Think\Page($count,15);
        $data = M('service')->where($map)
            ->limit($Page->firstRow.','.$Page->listRows)
            ->select();
        page_config($Page);
        $show       = $Page->show();

        $assign = [
            'area' => $area,
            'data' => $data,
            'page'=> bootstrap_page_style($show),
        ];
        $this->assign($assign);
        $this->display();
    }

    /**
     * 服务站详情
     */
    public function detail()
    {
        $this->display();
    }

    /**
     * 设置预设服务站
     */
    public function preset()
    {
        try {
            $area = M('area')->where('parentid=0')->select();
            $i = 0;
            foreach ($area as $item) {
                $area_item = M('area')->where('parentid='.$item['id'])->select();

                foreach ($area_item as $area_item_list){

                    $item_list = M('area')->where('parentid='.$area_item_list['id'])->select();
                    foreach ($item_list as $item_list_value) {
                        $map = [];
                        $map['province_id'] = $item['id'];
                        $map['city_id']     = $area_item_list['id'];
                        $map['district_id'] = $item_list_value['id'];
                        $service_item = M('service')->where($map)->find();
                        if(empty($service_item)) {
                            $map['company'] = $item_list_value['areaname'].'服务站';
                            $map['status']  = 0;
                            $map['addtime'] = time();
                            $map['province'] = $item['areaname'];
                            $map['city']     = $area_item_list['areaname'];
                            $map['district'] = $item_list_value['areaname'];
                            $map['addressinfo'] = $item['areaname'].$area_item_list['areaname'].$item_list_value['areaname'];

                            $res=M('service')->add($map);
                            if($res){
                                $i++;
                            }
                        }
                    }
                }
            }

            $this->toJson(array(
                'num'=>$i,
            ),'创建成功');

        } catch (\Exception $e) {
            $this->toJson($e);
        }
    }


}