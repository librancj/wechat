<?php
define("TOKEN", "starstyle");
$wechatObj = new wechatCallback();
$wechatObj->responseMsg();
class wechatCallback
{
	public function valid()
    {
        $echoStr = $_GET["echostr"];

        if($this->checkSignature()){
        	echo $echoStr;
        	exit;
        }
    }

	private function checkSignature()
	{
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];	       		
		$token = TOKEN;
		$tmpArr = array($token, $timestamp, $nonce);
		sort($tmpArr, SORT_STRING);
		$tmpStr = implode( $tmpArr );
		$tmpStr = sha1( $tmpStr );
		
		if( $tmpStr == $signature ){
			return true;
		}else{
			return false;
		}
	}

    public function responseMsg()
    {
		$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];

		if (!empty($postStr)){               
              	$postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
                $cliname = $postObj->FromUserName;
                $myname = $postObj->ToUserName;
                $sendtime = $postObj->CreateTime;               
	            $type = $postObj->MsgType;
	            $msgid = $postObj->MsgId;
                $keyword = trim($postObj->Content);
                $picurl = $postObj->PicUrl;
                $mediaid = $postObj->MediaId;
                $thumbid = $postObj->ThumbMediaId;
                $voicefmt = $postObj->Format;
	            $customevent = $postObj->Event;
	            $latitude = $postObj->Location_X;
	            $longitude = $postObj->Location_Y;
	            $mapscale = $postObj->Scale;
	            $mapinfo = $postObj->Label;
	            $linktitle = $postObj->Title;
	            $linkinfo = $postObj->Description;
	            $linkurl = $postObj->Url;
                $time = time();
            	switch ($type)
            {
            	case "text":
            	$resultStr = $this->receiveText($postObj);
                break;
                case "event":
                $resultStr = $this->receiveEvent($postObj);
                break;
            	case "image":
            	$resultStr = $this->receiveImage($postObj);
                break;
                case "voice":
                $resultStr = $this->receiveVoice($postObj);
                break;
            	case "video":
            	$resultStr = $this->receiveVideo($postObj);
                break;
                case "Location":
                $resultStr = $this->receiveLocation($postObj);
                break;        
                case "Link":
                $resultStr = $this->receiveLink($postObj);
                break;                                           
                default:
                $resultStr = "";
                break;
            }

    private function receiveText($object)
    {
        $keyword = $object->Content;      
		$resultStr = $this->sendText($object, $contentStr);
        return $resultStr;
    }

    private function receiveEvent($object)
    {
        $contentStr = "";
        switch ($object->Event)
        {
            case "subscribe":
                $contentStr = "Thanks For Subscribe";
                break;
            default:
                break;
        }
        $resultStr = $this->sendText($object, $contentStr);
        return $resultStr;
    }        

    private function sendText($object, $content)
    {
        $textTpl = "<xml>
					<ToUserName><![CDATA[%s]]></ToUserName>
					<FromUserName><![CDATA[%s]]></FromUserName>
					<CreateTime>%s</CreateTime>
					<MsgType><![CDATA[text]]></MsgType>
					<Content><![CDATA[%s]]></Content>
					</xml>";
        $resultStr = sprintf($textTpl, $object->FromUserName, $object->ToUserName, time(), $content);
        return $resultStr;
    }

    private function sendImage($object, $mediaid)
    {
        $textTpl = "<xml>
					<ToUserName><![CDATA[%s]]></ToUserName>
					<FromUserName><![CDATA[%s]]></FromUserName>
					<CreateTime>%s</CreateTime>
					<MsgType><![CDATA[image]]></MsgType>
					<Image>
					<MediaId><![CDATA[%s]]></MediaId>
					</Image>
					</xml>";
        $resultStr = sprintf($textTpl, $object->FromUserName, $object->ToUserName, time(), $content);
        return $resultStr;
    } 

    private function sendVoice($object, $mediaid)
    {
        $textTpl = "<xml>
					<ToUserName><![CDATA[%s]]></ToUserName>
					<FromUserName><![CDATA[%s]]></FromUserName>
					<CreateTime>%s</CreateTime>
					<MsgType><![CDATA[voice]]></MsgType>
					<Voice>
					<MediaId><![CDATA[%s]]></MediaId>
					</Voice>
					</xml>";
        $resultStr = sprintf($textTpl, $object->FromUserName, $object->ToUserName, time(), $content);
        return $resultStr;
    } 

    private function sendVideo($object, $mediaid)
    {
        $textTpl = "<xml>
					<ToUserName><![CDATA[%s]]></ToUserName>
					<FromUserName><![CDATA[%s]]></FromUserName>
					<CreateTime>%s</CreateTime>
					<MsgType><![CDATA[video]]></MsgType>
					<Video>
					<MediaId><![CDATA[%s]]></MediaId>
					<Title><![CDATA[%s]]></Title>
					<Description><![CDATA[%s]]></Description>
					</Video>
					</xml>";
        $resultStr = sprintf($textTpl, $object->FromUserName, $object->ToUserName, time(), $content);
        return $resultStr;
    }  

    private function sendMusic($object, $mediaid)
    {
        $textTpl = "<xml>
					<ToUserName><![CDATA[%s]]></ToUserName>
					<FromUserName><![CDATA[%s]]></FromUserName>
					<CreateTime>%s</CreateTime>
					<MsgType><![CDATA[music]]></MsgType>
					<Music>
					<Title><![CDATA[%s]]></Title>
					<Description><![CDATA[%s]]></Description>
					<MusicUrl><![CDATA[%s]]></MusicUrl>
					<HQMusicUrl><![CDATA[%s]]></HQMusicUrl>
					<ThumbMediaId><![CDATA[%s]]></ThumbMediaId>
					</Music>
					</xml>";
        $resultStr = sprintf($textTpl, $object->FromUserName, $object->ToUserName, time(), $content);
        return $resultStr;
    }  

    private function sendNews($object, $arr_item)
    {
        if(!is_array($arr_item))
        return;
        $itemTpl = "<item>
       				<Title><![CDATA[%s]]></Title>
        			<Description><![CDATA[%s]]></Description>
        			<PicUrl><![CDATA[%s]]></PicUrl>
        			<Url><![CDATA[%s]]></Url>
    				</item>";
        $item_str = "";
        foreach ($arr_item as $item)
        $item_str .= sprintf($itemTpl, $item['Title'], $item['Description'], $item['PicUrl'], $item['Url']);

        $newsTpl = "<xml>
					<ToUserName><![CDATA[%s]]></ToUserName>
					<FromUserName><![CDATA[%s]]></FromUserName>
					<CreateTime>%s</CreateTime>
					<MsgType><![CDATA[news]]></MsgType>
					<Content><![CDATA[]]></Content>
					<ArticleCount>%s</ArticleCount>
					<Articles>$item_str
					</Articles>
					</xml>";

        $resultStr = sprintf($newsTpl, $object->FromUserName, $object->ToUserName, time(), count($arr_item));
        return $resultStr;
    }

        }
    }        
}

?>
