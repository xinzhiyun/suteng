<?php
namespace Pc\Controller;

use Think\Controller;

class UsersController extends Controller
{
    // PC端用户注册
    public function register()
    {
        // 显示模板
        $this->display('register');
    }

    // PC端用户登录
    public function login()
    {
        // 显示模板
        $this->display('login');
    }

    // PC端用户退出登录
    public function logout()
    {
        echo 'PC用户登录';
    }

    // PC修改登录密码
    public function resetLogonPwd()
    {
        // echo 'PC修改登录密码';
        $this->display('resetLogonPwd');
        
    }

    // 验证码方法
    public function yzma()
    {
        $config = array(
            'font-Size' =>   30, //验证码大小
            'length'    =>   4,  //验证码个数
            'useNoise'  =>   false, //关闭验证码杂点
            'useCurve'  => false,//关闭曲线
        );
        $Verify = new \Think\Verify($config);
        $Verify->entry();
    }




    // 验证
    public function yz()
    {
        $x1min = $_SESSION['yzmcode'][0]['x'];
        $x1max = $_SESSION['yzmcode'][0]['x']+$_SESSION['yzm'][0]['size']+50;
        $y1min = $_SESSION['yzmcode'][0]['y']-$_SESSION['yzm'][0]['size']-50;
        $y1max = $_SESSION['yzmcode'][0]['y'];

        $x2min = $_SESSION['yzmcode'][1]['x'];
        $x2max = $_SESSION['yzmcode'][1]['x']+$_SESSION['yzm'][1]['size']+50;
        $y2min = $_SESSION['yzmcode'][1]['y']-$_SESSION['yzm'][1]['size']-50;
        $y2max = $_SESSION['yzmcode'][1]['y'];


        $arr=json_decode($_POST['obj'], true); 
        $x1 = $arr[0]['x'];
        $y1 = $arr[0]['y'];
        $x2 = $arr[1]['x'];
        $y2 = $arr[1]['y'];

        if($x1min<=$x1 && $x1<=$x1max && $y1min<=$y1 && $y1 <=$y1max){
            if($x2min<=$x2  && $x2<=$x2max && $y2min<=$y2 && $y2<=$y2max){
                echo 1;
            }else{
                echo -1;
            }
        }else{
            echo -1;
        }
    }

    // 点击验证码
    public function yzm()
    {
        // // 启动会话
        // session_start();
        // 随机图片名
        $name = mt_rand(1,85);
        // 设置图片路径
        $filename = getcwd().'/Public/Pc/code/'.$name.'.jpg';
        // 打开图片
        $image = imagecreatefromjpeg($filename);
        // 定义颜色
        $hei = imagecolorallocate($image,0,0,0);
        $bai = imagecolorallocate($image,245,246,248);
        // 字体
        $fonts = [
            getcwd().'/Public/Pc/code/'.'font/msyh.ttf',
            getcwd().'/Public/Pc/code/'.'font/msyhbd.ttf',
            getcwd().'/Public/Pc/code/'.'font/simfang.ttf',
            getcwd().'/Public/Pc/code/'.'font/simkai.ttf',
            getcwd().'/Public/Pc/code/'.'font/simhei.ttf',
        ];

        // 文字
        $str = '人之初性本善相近习远苟不教乃迁道贵以专昔孟母择邻处子学断机杼窦燕山有义方五名俱扬养父过严师惰非所宜幼老何为玉琢成器知少时亲友礼仪香九龄能温席孝于当执融四岁让梨弟长先首次见闻某数识文一而十百千万三才者天地光日月星纲君臣夫妇顺曰春夏秋冬此运穷南北西东应乎中水火木金土行仁智信常容紊稻粱菽麦黍稷六谷食马牛羊鸡犬豕畜饲喜怒哀惧爱恶欲七情具匏革石丝与竹八音高曾祖身孙自至玄族伦恩从兄则恭序朋敬忠同凡训蒙须讲究详诂句读必小终书论语二篇群记言止德说作庸孔伋偏易大修齐平治经通熟如始可诗号求连归藏周典谟诰誓命奥我公著官存体戴注述圣乐备国风雅颂讽咏既亡寓褒贬别传左氏梁明撮其要事荀杨及庄诸史考世系羲农黄帝皇居上唐虞揖逊称盛禹商汤武王家下载社伐纣诛最久辙堕逞干戈尚游战霸强雄出嬴秦兼并楚汉争兴业建莽篡年献魏蜀吴鼎迄两晋宋继陈承朝都陵元分宇迨隋再失统绪起除乱创基灭改代皆由炎受禅混辽绝舆图广超前祚废太洪京崇祯权阉肆寇林李闯神焚清景靖克定古今全在兹衰实录若目口诵心惟斯夕仲尼项橐贤勤赵令鲁彼仕且披蒲编削简无勉头悬锥刺股苦囊萤映雪虽贫辍负薪挂角劳犹卓苏泉发愤籍悔迟尔生早思灏对廷魁多士众异立志莹泌赋棋颖悟奇效蔡姬辨琴谢韫吟女聪敏男警刘晏举童正字已致亦是守夜司晨曷蚕吐蜂酿蜜物壮泽民声显裕后遗满籯功戏益戒哉';
        $maxNum = (strlen($str)/3-3);
        $start = mt_rand(1,$maxNum);

        $str = mb_substr( $str, $start, 3, 'utf-8');

        $characters = [];

        for ($i=0; $i < 3; $i++) { 
            $character = mb_substr( $str, $i, 1, 'utf-8');
            $fontSize = mt_rand(30,50);
            $x = mt_rand(25+($i*100),($i+1)*80);
            $y = mt_rand(80,200);
            $characters[$i]['character'] = $character;
            $characters[$i]['x'] = $x;
            $characters[$i]['y'] = $y;
            $characters[$i]['size'] = $fontSize;
            imagettftext($image,$fontSize,mt_rand(-30,30),$x,$y,$hei,$fonts[mt_rand(0,4)],$character);
        }
        // 随机删除一个字
        unset($characters[mt_rand(0,2)]);
        // 重新排序数组
        $characters = array_values($characters);
        // 打乱顺序
        shuffle($characters);
        // 存储验证码信息、
        $_SESSION['yzmcode']=$characters;
        // 画矩形
        imagerectangle($image,0,290,70,250,$bai);
        // 填充矩形
        imagefilledrectangle($image,0,290,70,250,$bai);

        // 绘制干扰线
        for ($i=0; $i < 3; $i++) { 
            // 随机颜色
            $color = imagecolorallocate($image,mt_rand(0,255),mt_rand(0,255),mt_rand(0,255));
            imageline($image,0,mt_rand(250,290),70,mt_rand(250,290),$color);
        }

        for ($i=0; $i < 2; $i++) { 
            imagettftext($image,14,10,mt_rand(5+($i*30),20+$i*30),mt_rand(275,285),$hei,getcwd().'/Public/Pc/code/'.'font/msyh.ttf',$characters[$i]['character']);
        }

        // 告诉浏览器输出图片
        header("Content-type: image/jpeg");
        // 输出图片
        imagejpeg($image); 
        // 关闭画布
        imagedestroy($image);
    }
}