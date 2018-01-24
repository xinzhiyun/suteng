<?php
namespace Admin\Controller;
use Think\Controller;

class CommissionController extends CommonController
{
    public function index()
    {
        $this->display();
    }

    /**
     * [commission 佣金设置]
     * @return [type] [description]
     */
    public function commission_set()
    {
        $this->display();
    }

    /**
     * [commission 佣金按比例分配]
     * @return [type] [description]
     */
    public function commission_proportion()
    {
        dump(I());die;
        $this->display();
    }

}
