<?php
namespace Home\Controller;
use Home\Controller\WechatController;
class WeixinEventController
{
	// 接受微信服务器下发的事件
    public function getEventData()
    {
        $str = 'gQHU8DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyd0lSUWtlMmJkOTAxMDAwMHcwMzgAAgRkqFhaAwQAAAAA';
        $str2 ='gQHU8DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyd0lSUWtlMmJkOTAxMDAwMHcwMzgAAgRkqFhaAwQAAAAA';
        if($str===$str2){
            echo '1111111';
        }
    	// 实例化微信验证对象服务器第一次接入使用
    	// $wechatObj = new \Org\Util\WechatCallbackapiTest;
    	// 执行验证方法
    	// $wechatObj->valid();

    	// 接受微信推送的事件
    	$xml=file_get_contents('php://input', 'r');
        file_put_contents('./tmp.txt', $xml);
     	$xml = "<xml>
    <ToUserName><![CDATA[gh_0f61dfebc264]]></ToUserName>
    <FromUserName><![CDATA[oQktJwL8ioR4DoxSQmikdzekbUyU]]></FromUserName>
    <CreateTime>1515807148</CreateTime>
    <MsgType><![CDATA[event]]></MsgType>
    <Event><![CDATA[SCAN]]></Event>
    <EventKey><![CDATA[100]]></EventKey>
    <Ticket><![CDATA[gQHU8DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyd0lSUWtlMmJkOTAxMDAwMHcwMzgAAgRkqFhaAwQAAAAA]]></Ticket>
    <Encrypt><![CDATA[aGdkN02NePCRFYysQHaI0sQx7IW2BrMfl4LMKt5fi/SFyiOLtjnIH9qKQjwBTTFKE7xYvvVTc5+lkgZ0SVx+898kaEYygcoZ5RCQGYuJ3RuJnZUl3hST03cQ9O3179lmosxHDNqpMSI4CnZlsjsahLa+f3Jz2gNfDC5K9avm89vup2eB7f//qevadHK49Qtk9Icb82OJvah9d24DS7uowj07Ff2xo132OIhqz4mNtN11hw1PChsAh1c7zQYsY37QoxX7DEqWLj2jSo2VScsJx04jlo99upPV44oCCuL+kRFfhpqo1CBdyKS8PhKfOCWVlLqH3qKiN/iQYELEhCyTubZmohGOSQOR1XJdN0DUfUyyv8H2BnSXfR3AL2lhkgfzD1/3Yds1h+awE/3zrnkUWl6K1jcB2LXWhzbNImih4X/FcP9FGYW8dktgPeQVbsRpUCO5QTEKpvzYDkhpCwKfRw7diDIMh+vXs1ghbzM0eBHBAwCrboEmLwwhR1pox9AmxCn7uGRWE7Y3XEQ5+r3n3yt8kKyYf54HiNu7ErQfBLItxVc5+R8V096ED9pIFD6O5fZ2zi9D4KbUEEpX8RtjvQ==]]></Encrypt>
</xml>";


		if($xml){
            // 输出空字符串，回复微信服务器
			//echo '1';

			// 转成php数组
			$data = $this->toArray($xml);

            echo '<pre>';
            print_r($data);
            echo '</pre>';
            dump($data);die;
            // 判断如果是关注事件
            if($data['Event'] == 'subscribe'){
            //echo 2;   
                // 实例化微信信息类型
                $Wechat = new WechatController;
                // 调用填写微信信息的方法
                $Wechat->add($data['FromUserName']);
                
                // file_put_contents('./add.txt', $xml);
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