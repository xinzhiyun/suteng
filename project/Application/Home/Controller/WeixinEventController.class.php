<?php
namespace Home\Controller;
use Think\Controller;

class WeixinEventController extends Controller
{
	// 接受微信服务器下发的事件
    public function getEventData()
    {
        // echo md5('suteng');
    	// 实例化微信验证对象服务器第一次接入使用
    	// $wechatObj = new \Org\Util\WechatCallbackapiTest;
    	// 执行验证方法
    	// $wechatObj->valid();
        // dump($wechatObj->valid());die;
    	// 接受微信推送的事件
    	$xml=file_get_contents('php://input', 'r');
        // file_put_contents('./tmp.txt', $xml);die;
//      	$xml = "<xml><ToUserName><![CDATA[gh_90934e9fb0c8]]></ToUserName>
//<FromUserName><![CDATA[onLe70SfHSwyjUrqtIgt4MGN7mI8]]></FromUserName>
//<CreateTime>1530606207</CreateTime>
//<MsgType><![CDATA[event]]></MsgType>
//<Event><![CDATA[subscribe]]></Event>
//<EventKey><![CDATA[qrscene_1281]]></EventKey>
//<Ticket><![CDATA[gQFv8DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyajVPUkVac1RlWGgxMDAwMHcwM0sAAgQvFDtbAwQAAAAA]]></Ticket>
//</xml>";


		if($xml){
            // 输出空字符串，回复微信服务器
			//echo '1';

			// 转成php数组
			$data = $this->toArray($xml);

            // 获取公众号微信用户
            $toUser   = $data['FromUserName'];
            // 获取微信号微信用户
            $fromUser = $data['ToUserName'];

            // 判断如果是关注事件
            if($data['Event'] == 'subscribe'){
                    // 关注事件推送消息
//                    $this->Subscribe($toUser,$fromUser);
                    // 查询微信二维码信息表
                    $ticket = $data['Ticket'] ? true : false;


                    if($ticket){
                        // 查询微信用户类型
                        $wxid = M('wechat')->where("`open_id`='{$data['FromUserName']}'")->find()['type'];


                        // 匹配微信用户类型 {0：会员 1：分销商}
                        switch ($wxid) {
                            case '0':
                                    //回复图文消息，回复用户登录图文消息
                                    $this->reactUser($toUser,$fromUser);
                                break;
                            case '1':

                                    // 新增成功，回复分销商注册图文消息
                                     $this->reactVendor($toUser,$fromUser);

                                break;
                            default:
                                    // 查询微信二维码信息表注册类型{0:会员直接注册 1:会员推荐会员 2：分销商推荐会员 3：分公司推荐会员 4:分公司邀请分销商 5:分销商邀请分销商}
                                    $show['ticket'] = $data['Ticket'];
                                    $dimensionData  = M('dimension')->where($show)->field('recommend,leavel,recommend')->find();
                                    $recommend      = $dimensionData["recommend"]-0;

                                    // 准备微信信息表数据
                                    if($recommend>3){
                                        // 分销商 类型
                                        $addData['type']            = 1;
                                        // 分销商级别
                                        $addData['leavel']          = $dimensionData['leavel'];
                                        // 邀请类型
                                        $addData['recommend']          = $dimensionData['recommend'];
                                        // 操作注册
                                        $addData['action']          = 0;
                                        // 用户微信唯一标识
                                        $addData['open_id']             = $data['FromUserName'];
                                        // 上级二维码票据
                                        $addData['invitation_ticket']   = $data['Ticket'];
                                        // 创建时间
                                        $addData['addtime']             = time();
                                        // 修改时间
                                        $addData['updatetime']          = $addData['addtime'];
                                        // 执行新增操作
                                        $res = M('wechat')->add($addData);
                                        // 判断是否新增成功
                                        if($res){
                                            // 新增成功，回复分销商注册图文消息
                                            $this->reactVendor($toUser,$fromUser);
                                        }else{
                                            // 新增失败
                                            echo '';
                                        }

                                    }else{
                                        // 会员 类型
                                        $addData['type']            = 0;
                                        $addData['action']          = 0;
                                        // 邀请类型
                                        $addData['recommend']          = $dimensionData['recommend'];
                                        // 用户微信唯一标识
                                        $addData['open_id']             = $data['FromUserName'];
                                        // 上级二维码票据
                                        $addData['invitation_ticket']   = $data['Ticket'];
                                        // 创建时间
                                        $addData['addtime']             = time();
                                        // 修改时间
                                        $addData['updatetime']          = $addData['addtime'];
                                        // 执行新增操作
                                        $res = M('wechat')->add($addData);

                                        if($res){
                                            //回复图文消息，回复用户登录图文消息
                                            $this->reactUser($toUser,$fromUser);  
                                        }else{
                                            // 新增失败
                                            echo '';
                                        }
                                    } 
                                break;
                        }

                    }else{
                        // 扫普通二维码
                        // 会员 类型
                        $addData['type']            = 0;
                        $addData['action']          = 0;
                        // 用户微信唯一标识
                        $addData['open_id']             = $data['FromUserName'];
                        // 创建时间
                        $addData['addtime']             = time();
                        // 修改时间
                        $addData['updatetime']          = $addData['addtime'];
                        // 执行新增操作
                        $res = M('wechat')->add($addData);
                        if($res){
                            //回复图文消息，回复用户登录图文消息
                            $this->reactUser($toUser,$fromUser);

                        }else{
                            echo '';
                        }
                    }
                exit;
            }

            // 判断是否已关注带参数二维码事件
            if($data['Event'] == 'SCAN'){
                // 查询微信用户类型
                $wxid = M('wechat')->where("`open_id`='{$data['FromUserName']}'")->find()['type'];

                switch ($wxid) {
                    case '0':
                            //回复图文消息，回复用户登录图文消息
                            $this->reactUser($toUser,$fromUser);
                        break;
                    case '1':
                            // 新增成功，回复分销商注册图文消息
                            $this->reactVendor($toUser,$fromUser);
                        break;
                    default:
                            // 查询微信二维码信息表
                            $show['ticket'] = $data['Ticket'];
                            $dimensionData  = M('dimension')->where($show)->field('recommend,leavel,recommend')->find();
                            $recommend      = $dimensionData["recommend"]-0;

                            // 准备微信信息表数据
                            if($recommend>3){
                                // 分销商 类型
                                $addData['type']            = 1;
                                // 分销商级别
                                $addData['leavel']          = $dimensionData['leavel'];
                                // 邀请类型
                                $addData['recommend']          = $dimensionData['recommend'];
                                // 操作注册
                                $addData['action']          = 0;
                                // 用户微信唯一标识
                                $addData['open_id']             = $data['FromUserName'];
                                // 上级二维码票据
                                $addData['invitation_ticket']   = $data['Ticket'];
                                // 创建时间
                                $addData['addtime']             = time();
                                // 修改时间
                                $addData['updatetime']          = $addData['addtime'];
                                // 执行新增操作
                                $res = M('wechat')->add($addData);
                                // 判断是否新增成功
                                if($res){
                                    // 新增成功，回复分销商注册图文消息
                                    $this->reactVendor($toUser,$fromUser);
                                }else{
                                    // 新增失败
                                    echo '';
                                }

                            }else{
                                // 会员 类型
                                $addData['type']            = 0;
                                $addData['action']          = 0;
                                // 邀请类型
                                $addData['recommend']          = $dimensionData['recommend'];
                                // 用户微信唯一标识
                                $addData['open_id']             = $data['FromUserName'];
                                // 上级二维码票据
                                $addData['invitation_ticket']   = $data['Ticket'];
                                // 创建时间
                                $addData['addtime']             = time();
                                // 修改时间
                                $addData['updatetime']          = $addData['addtime'];
                                // 执行新增操作
                                $res = M('wechat')->add($addData);

                                if($res){
                                    //回复图文消息，回复用户登录图文消息
                                    $this->reactUser($toUser,$fromUser); 
                                }else{
                                    // 新增失败
                                    echo '';
                                }
                            } 
                        break;
                }
                exit;
            }

            // 判断如果是取消关注事件
            if($data['Event'] == 'unsubscribe'){
                //file_put_contents('./del.txt', $xml);
                // 实例化微信信息类型
                $Wechat = new WechatController;
                // 调用删除微信信息的方法
                $Wechat->delete($data['FromUserName']);
                exit;
            }

            // 判断如果是上报地理位置事件
            if($data['Event'] == 'LOCATION'){
                //file_put_contents('./del.txt', $xml);
                // 实例化微信信息类型
                $Wechat = new WechatController;
                // 调用上报用户地理位置的方法
                $Wechat->location($data);
                exit;
            }
		}
	}

    public function reactUser($toUser,$fromUser){
        //回复图文消息
        // $toUser   = $toUser;
        // $fromUser = $fromUser;
        $title = '会员登录'; 
        $description = '欢迎加入速腾集团，成为速腾集团全球合伙人。'; 
        $src = 'http://test.dianqiukj.com/Public/Vendors/user.jpg';
        $url = 'http://test.dianqiukj.com/index.php/Home/Index/index';
        $template = "<xml>
                        <ToUserName><![CDATA[%s]]></ToUserName>
                        <FromUserName><![CDATA[%s]]></FromUserName>
                        <CreateTime>%s</CreateTime>
                        <MsgType><![CDATA[%s]]></MsgType>
                        <ArticleCount>1</ArticleCount>
                        <Articles>
                            <item>
                                <Title><![CDATA[%s]]></Title> 
                                <Description><![CDATA[%s]]></Description>
                                <PicUrl><![CDATA[%s]]></PicUrl>
                                <Url><![CDATA[%s]]></Url>
                            </item>
                        </Articles>
                    </xml> ";
        
        echo sprintf($template, $toUser, $fromUser, time(), 'news',$title,$description,$src,$url);
    }

    public function reactVendor($toUser,$fromUser){
        //回复图文消息
        $toUser   = $toUser;
        $fromUser = $fromUser;

        $title = '分销商注册'; 
        $description = '欢迎加入速腾集团，成为速腾集团全球合伙人。'; 
        $src = 'http://test.dianqiukj.com/Public/Vendors/partner.jpg';
        $url = 'http://test.dianqiukj.com/index.php/Home/RegisteredVendor/index.html';
        $template = "<xml>
                        <ToUserName><![CDATA[%s]]></ToUserName>
                        <FromUserName><![CDATA[%s]]></FromUserName>
                        <CreateTime>%s</CreateTime>
                        <MsgType><![CDATA[%s]]></MsgType>
                        <ArticleCount>1</ArticleCount>
                        <Articles>
                            <item>
                                <Title><![CDATA[%s]]></Title> 
                                <Description><![CDATA[%s]]></Description>
                                <PicUrl><![CDATA[%s]]></PicUrl>
                                <Url><![CDATA[%s]]></Url>
                            </item>
                        </Articles>
                    </xml> ";

        echo sprintf($template, $toUser, $fromUser, time(), 'news',$title,$description,$src,$url);
    }

    // 关注事件推送图文消息
    public function Subscribe($toUser, $fromUser)
    {
        $wx_config = M('system_config');
        $map['auid'] = 1;
        $info = $wx_config->where($map)
                        ->alias('s')
                        // ->join("__ADMINUSER__ admin ON s.auid=admin.id", 'LEFT')
                        ->field("s.title,s.description,s.src,s.url")
                        ->find();

        $title = $info['title'];
        $description = $info['description'];
        $src = '';
        $url = '';
        $template = "<xml>
                        <ToUserName><![CDATA[%s]]></ToUserName>
                        <FromUserName><![CDATA[%s]]></FromUserName>
                        <CreateTime>%s</CreateTime>
                        <MsgType><![CDATA[%s]]></MsgType>
                        <ArticleCount>1</ArticleCount>
                        <Articles>
                            <item>
                                <Title><![CDATA[%s]]></Title> 
                                <Description><![CDATA[%s]]></Description>
                                <PicUrl><![CDATA[%s]]></PicUrl>
                                <Url><![CDATA[%s]]></Url>
                            </item>
                        </Articles>
                    </xml> ";

        echo sprintf($template, $toUser, $fromUser, time(), 'news', $title, $description, $src, $url);
        
    }



    /**
     * 将xml转为array
     * @param  string $xml xml字符串
     * @return array       转换得到的数组
     */
    public function toArray($xml){   
        //禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        $result= json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);        
        return $result;
    }
}