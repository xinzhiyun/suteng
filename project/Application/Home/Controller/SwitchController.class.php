<?php
namespace Home\Controller;

class SwitchController extends CommonController
{

    /**
     * [index 定时开关]
     * @return [type] [description]
     */
    public function index()
    {
        $this->display();
    }

    /**
     * [setTime 设置时间]
     * @return [type] [description]
     */
    public function setTime()
    {
        $this->display();
    }

    // 定时加热开关
    public function heatSwitch()
    {
        $this->display();
    }

    // 添加加热开关
    public function addHeatSet()
    {
        $this->display();
    }
}


