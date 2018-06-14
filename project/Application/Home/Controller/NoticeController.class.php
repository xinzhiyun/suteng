<?php
namespace Home\Controller;
use Think\Controller;
class NoticeController extends Controller
{
    public function index()
    {
    	$this->display();
    }

    public function notice($url,$arr)
    {
        $this->redirect($url,$arr);
    }
}


