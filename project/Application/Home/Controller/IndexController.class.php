<?php
namespace Home\Controller;

use Think\Controller;
use Org\Util\Validate;
class IndexController extends Controller
{
    public function index()
    {
        $validate = new Validate;

        if($validate->original('/^\d+$/','1111111')){
        	echo '验证通过！';
        }else{
        	echo '不通过验证';
        }

    }
}