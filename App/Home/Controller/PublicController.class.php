<?php
namespace Home\Controller;
use Think\Controller;
class PublicController extends Controller {
    public function __construct(){

    }
    public function index(){
        //获得参数 signature nonce token timestamp echostr
        $nonce     = $_GET['nonce'];
        $token     = 'baiYY123';
        $timestamp = $_GET['timestamp'];
        $echostr   = $_GET['echostr'];
        $signature = $_GET['signature'];
        //形成数组，然后按字典序排序
        $array = array($nonce, $timestamp, $token);
        sort($array);
        //拼接成字符串,sha1加密 ，然后与signature进行校验
        $str = sha1( implode( $array ) );
        if( $str  == $signature && $echostr ){
            //第一次接入weixin api接口的时候
            echo  $echostr;
            exit;
        }else{
            $this->definedItem();
            $this->responseMss();
        }

    }

    public function  show(){
        echo 'baiYY123';
    }

    public function responseMss() {
        $uOb = M('User');
        $postArr = $GLOBALS['HTTP_RAW_POST_DATA'];
        $postObj = simplexml_load_string( $postArr );
        $toUser   = $postObj->FromUserName;
        $fromUser = $postObj->ToUserName;
        $msgType  =  'text';
        $info = $this->getInfo($toUser);
        $infoo = json_decode($info,true);
        $unionId = $infoo['unionid']; //获取唯一的unionid
        $content = '';
        if(strtolower($postObj->MsgType) == 'event') {
            //事件推送
            if(strtolower($postObj->Event) == 'subscribe') {//扫码首次关注后
                $scene_id = substr(trim($postObj->EventKey),8);
                if(!$postObj->EventKey) {
                    $content = "大渝棋牌，全民娱乐🔥\n线上茶馆，轻松创业🍭\n感谢关注大渝棋牌微信公众号，恭喜你已经找到了最好玩的约牌组织!热门活动及游戏咨询请添加客服妹纸微信：dyqp001或dyqp002🍭\n<a href='http://wx.baiyy.com/dayugm/wechat/index.php/Home/Login/login.html'>点击下载《大渝棋牌》</a>".$postObj->EventKey."嘿嘿";
                }else{
                    $re = $uOb->where("agent_id='%s'",$scene_id)->find();
                    $url=C('API_URL').'/?act=queryAgentId&openid='.$unionId.'&agentId='.$re['game_id'];
                    $curl_res=get_curl($url);
                    if($curl_res['agentId'] == 0 || $curl_res['agentId'] == $re['game_id']) {
                        $content = "大渝棋牌，人人精彩🔥\n加入茶楼失败！\n你已经加入了“".$re['tea_name']."”,加入时间“".date('Y-m-d h:i:s',$curl_res['time'])."”,您的游戏ID为：“".$curl_res['uid']."”\n加入茶楼失败🌷\n<a href='http://wx.baiyy.com/dayugm/wechat/index.php/Home/Login/login.html'>点击下载《大渝棋牌》🌷</a>";
                    }else{
                        $content = "大渝棋牌，人人精彩🔥\n加入茶楼成功！\n你已经加入了“".$re['tea_name']."”,加入时间“".date('Y-m-d h:i:s', $curl_res['time'])."”,您的游戏ID为：“".$curl_res['uid']."”\n赶快打开\n<a href='http://wx.baiyy.com/dayugm/wechat/index.php/Home/Login/login.html'>点击下载《大渝棋牌》</a>，进行游戏吧！".$scene_id.$re['game_id'];
                    }
                    $this->replyText($toUser,$fromUser,$msgType,$content);
                }
            }else if(strtoupper($postObj->Event) == 'SCAN') {
                //扫码再次扫码进入
                $scene_id = substr(trim($postObj->EventKey),8);
                $re = $uOb->where("agent_id='%s'",$scene_id)->find();
                $url=C('API_URL').'/?act=queryAgentId&openid='.$unionId.'&agentId='.$re['game_id'];
                $curl_res=get_curl($url);
                $content = "大渝棋牌，全民娱乐\n加入茶楼失败！！！您已经加入了“".$re['tea_name']."”加入时间“".date('Y-m-d h:i:s',$curl_res['time'])."”,您的游戏ID为：“".$curl_res['uid']."\n加入茶楼失败🌹”\n<a href='http://www.wx.baiyy.com'>点击下载《大渝棋牌》</a>";
            }else if(strtoupper($postObj->Event) == 'CLICK') {
                if(strtolower($postObj->EventKey) == 'item1'){
                    $time = time();
                    $msgType = 'news';
                    $title = '0元开业，月入过万！这样的商机你要吗？';
                    $desctiption = '有赚无赔是的茶馆经营攻略，看完这篇，所有的玩家都抢着开茶馆了';
                    $picUrl = 'http://wx.baiyy.com/dayugm/wechat/Public/PicImg/pic.jpg';
                    $url = 'http://wx.baiyy.com/dayugm/wechat/index.php/Home/Look/look';
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
                                </xml>";
                    $info     = sprintf($template, $toUser, $fromUser, $time, $msgType,$title,$desctiption,$picUrl,$url);
                    echo $info;
                }
                //自定义菜单点击
            }else if(strtoupper($postObj->Event) == 'VIEW') {
                //自定义菜单跳转
                $content = '不能跳转';
                $this->replyText($toUser,$fromUser,$msgType,$content);
            }
            $this->replyText($toUser,$fromUser,$msgType,$content);
        }else if(strtolower($postObj->MsgType) == 'text') {
            //文本消息
            if(trim($postObj->Content) == 1) {
                $content = $this->getInfo($toUser);
            }else if(trim($postObj->Content) == 2){
                $data = $this->getInfo($toUser);
                $datas = json_decode($data,true);
                $content = $datas['unionid'];
            }else{
                $content = "大渝棋牌，全民娱乐🌷\n游戏问题请加客服微信：dyqp001\n招商加盟请加商务微信：dyqp002\n<a href='http://wx.baiyy.com/dayugm/wechat/index.php/Home/Login/login.html'>点击了解详情</a>\n上班时间9:30-18:30\n如非上班时间请直接描述您的问题，上班后立即回复您哦💕\n<a href='http://www.wx.baiyy.com'>点击下载《大渝棋牌》</a>";
            }
            $this->replyText($toUser,$fromUser,$msgType,$content);
        }else if(strtolower($postObj->MsgType) == 'image') {
            //图片消息
            $content = "大渝棋牌，全民娱乐🌷\n游戏问题请加客服微信：dyqp001\n招商加盟请加商务微信：dyqp002\n<a href='http://wx.baiyy.com/dayugm/wechat/index.php/Home/Login/login.html'>点击了解详情</a>\n上班时间9:30-18:30\n如非上班时间请直接描述您的问题，上班后立即回复您哦💕\n<a href='http://www.wx.baiyy.com'>点击下载《大渝棋牌》</a>";
            $this->replyText($toUser,$fromUser,$msgType,$content);
        }else if(strtolower($postObj->MsgType) == 'voice') {
            //语音消息
            $content = "大渝棋牌，全民娱乐🌷\n游戏问题请加客服微信：dyqp001\n招商加盟请加商务微信：dyqp002\n<a href='http://wx.baiyy.com/dayugm/wechat/index.php/Home/Login/login.html'>点击了解详情</a>\n上班时间9:30-18:30\n如非上班时间请直接描述您的问题，上班后立即回复您哦💕\n<a href='http://www.wx.baiyy.com'>点击下载《大渝棋牌》</a>";
            $this->replyText($toUser,$fromUser,$msgType,$content);
        }else if(strtolower($postObj->MsgType) == 'video') {
            //视频消息
            $content = "大渝棋牌，全民娱乐🌷\n游戏问题请加客服微信：dyqp001\n招商加盟请加商务微信：dyqp002\n<a href='http://wx.baiyy.com/dayugm/wechat/index.php/Home/Login/login.html'>点击了解详情</a>\n上班时间9:30-18:30\n如非上班时间请直接描述您的问题，上班后立即回复您哦💕\n<a href='http://www.wx.baiyy.com'>点击下载《大渝棋牌》</a>";
            $this->replyText($toUser,$fromUser,$msgType,$content);
        }else if(strtolower($postObj->MsgType) == 'shortvideo') {
            //小视频
            $content = "大渝棋牌，全民娱乐🌷\n游戏问题请加客服微信：dyqp001\n招商加盟请加商务微信：dyqp002\n<a href='http://wx.baiyy.com/dayugm/wechat/index.php/Home/Login/login.html'>点击了解详情</a>\n上班时间9:30-18:30\n如非上班时间请直接描述您的问题，上班后立即回复您哦💕\n<a href='http://www.wx.baiyy.com'>点击下载《大渝棋牌》</a>";
            $this->replyText($toUser,$fromUser,$msgType,$content);
        }else if(strtolower($postObj->MsgType) == 'shortvideo') {
            //地理位置
            $content = "大渝棋牌，全民娱乐🌷\n游戏问题请加客服微信：dyqp001\n招商加盟请加商务微信：dyqp002\n<a href='http://wx.baiyy.com/dayugm/wechat/index.php/Home/Login/login.html'>点击了解详情</a>\n上班时间9:30-18:30\n如非上班时间请直接描述您的问题，上班后立即回复您哦💕\n<a href='http://www.wx.baiyy.com'>点击下载《大渝棋牌》</a>";
            $this->replyText($toUser,$fromUser,$msgType,$content);
        }
    }

    public function replyText($toUser,$fromUser,$msgType,$content){
        $time = time();
        $template = "<xml>
                            <ToUserName><![CDATA[%s]]></ToUserName>
                            <FromUserName><![CDATA[%s]]></FromUserName>
                            <CreateTime>%s</CreateTime>
                            <MsgType><![CDATA[%s]]></MsgType>
                            <Content><![CDATA[%s]]></Content>
                            </xml>";
        $info     = sprintf($template, $toUser, $fromUser, $time, $msgType, $content);
        echo $info;
    }

    //消息处理函数
    public function reponseMsg(){
        //1.获取到微信推送过来post数据（xml格式）
        $postArr = $GLOBALS['HTTP_RAW_POST_DATA'];
        //2.处理消息类型，并设置回复类型和内容
        /*<xml>
        <ToUserName><![CDATA[toUser]]></ToUserName>
        <FromUserName><![CDATA[FromUser]]></FromUserName>
        <CreateTime>123456789</CreateTime>
        <MsgType><![CDATA[event]]></MsgType>
        <Event><![CDATA[subscribe]]></Event>
        </xml>*/
        $postObj = simplexml_load_string( $postArr );
        //$postObj->ToUserName = '';
        //$postObj->FromUserName = '';
        //$postObj->CreateTime = '';
        //$postObj->MsgType = '';
        //$postObj->Event = '';
        // gh_e79a177814ed
        //判断该数据包是否是订阅的事件推送
        if( strtolower( $postObj->MsgType) == 'event'){
            //如果是关注 subscribe 事件
            $content = '';
            if( strtolower($postObj->Event == 'subscribe') ){
//                if(trim($postObj)->EventKey == '000000') {
//                    $content  = '大渝棋牌，全民娱乐\n线上茶馆，轻松创业\n感谢关注大渝棋牌微信公众号，恭喜你已找到了最好玩的约牌组织！\n热门活动及游戏咨询请添加客服妹儿微信：dygame01或dygame02\n"<a href="#">点击下载《大渝棋牌》</a>"';
//                }else{
//
//                    $content  = '大渝棋牌，全民娱乐\n线上茶馆，轻松创业\n感谢关注大渝棋牌微信公众号，恭喜你已找到了最好玩的约牌组织！\n热门活动及游戏咨询请添加客服妹儿微信：dygame01或dygame02\n成功加入“钱多多茶楼”茶楼您的游戏ID为：<a href="#">点击下载《大渝棋牌》</a>';
//                }
                //回复用户消息(纯文本格式)
                $toUser   = $postObj->FromUserName;
                $fromUser = $postObj->ToUserName;
                $time     = time();
                $msgType  =  'text';
                $content = '欢迎关注百亿云的微信公众账号'.$postObj->EventKey.'+'.$this->getInfo($toUser);
                $template = "<xml>
                            <ToUserName><![CDATA[%s]]></ToUserName>
                            <FromUserName><![CDATA[%s]]></FromUserName>
                            <CreateTime>%s</CreateTime>
                            <MsgType><![CDATA[%s]]></MsgType>
                            <Content><![CDATA[%s]]></Content>
                            </xml>";
                $info     = sprintf($template, $toUser, $fromUser, $time, $msgType, $content);
                echo $info;
                /*<xml>
                <ToUserName><![CDATA[toUser]]></ToUserName>
                <FromUserName><![CDATA[fromUser]]></FromUserName>
                <CreateTime>12345678</CreateTime>
                <MsgType><![CDATA[text]]></MsgType>
                <Content><![CDATA[你好]]></Content>
                </xml>*/


            }else if(strtolower($postObj->Event == 'SCAN')){
                $toUser   = $postObj->FromUserName;
                $fromUser = $postObj->ToUserName;
                $time     = time();
                $msgType  =  'text';
//                $content = '大渝棋牌，人人精彩"\n"游戏问题请添加客服微信：dygame01"\n"招商加盟请添加商务微信：dygame02"\n"<a href="#">点击了解详情</a>"\n"上班时间：9:30-18:30"\n"如非上班时间请直接描述您的问题"\n"上班后立即回复您哦！"\n"<a href="#">点击下载《大渝棋牌》</a>'.$postObj->EventKey;
                $content  = '欢迎关注百亿云的微信公众账号'.$postObj->FromUserName.'-'.$postObj->ToUserName;
                $template = "<xml>
                            <ToUserName><![CDATA[%s]]></ToUserName>
                            <FromUserName><![CDATA[%s]]></FromUserName>
                            <CreateTime>%s</CreateTime>
                            <MsgType><![CDATA[%s]]></MsgType>
                            <Content><![CDATA[%s]]></Content>
                            </xml>";
                $info     = sprintf($template, $toUser, $fromUser, $time, $msgType, $content);
                echo $info;
            }else if(strtoupper($postObj->Event == 'CLICK')) {
                switch (trim($postObj->EventKey)) {
                    case 'item1':
                        $content  = '点击菜单一';
                        break;
                    case 'item2':
                        $content = '点击菜单二';
                        break;
                }
                if(trim($postObj->EventKey) == 'item1') {
                    $toUser   = $postObj->FromUserName;
                    $fromUser = $postObj->ToUserName;
                    $time     = time();
                    $msgType  =  'text';

                    $template = "<xml>
                            <ToUserName><![CDATA[%s]]></ToUserName>
                            <FromUserName><![CDATA[%s]]></FromUserName>
                            <CreateTime>%s</CreateTime>
                            <MsgType><![CDATA[%s]]></MsgType>
                            <Content><![CDATA[%s]]></Content>
                            </xml>";
                    $info     = sprintf($template, $toUser, $fromUser, $time, $msgType, $content);
                    echo $info;
                }
            }
        }
        //根据用户输入消息进行回复
        else if(strtolower( $postObj->MsgType) == 'text'){
            $toUser   = $postObj->FromUserName;
            $fromUser = $postObj->ToUserName;
            $time     = time();
            $msgType  =  'text';
            //$content  = 'imooc is very good'.$postObj->FromUserName.'-'.$postObj->ToUserName;
            $template = "<xml>
                            <ToUserName><![CDATA[%s]]></ToUserName>
                            <FromUserName><![CDATA[%s]]></FromUserName>
                            <CreateTime>%s</CreateTime>
                            <MsgType><![CDATA[%s]]></MsgType>
                            <Content><![CDATA[%s]]></Content>
                            </xml>";
            switch( trim($postObj->Content)){
                case 1:
                    $content = '您输入的数字是1';
                    break;
                case 2:
                    $content = '您输入的数字是2';
                    break;
                case 3:
                    $content = '<a href="http://www.baidu.com">百度</a>';
                    break;
                case '图片':
                    $arr=array(
                        array('title'=>'支付',
                            'description'=>'支付的图片',
                            'picUrl'=>'http://119.23.251.242/dayugm/wechat/Public/App/img/1.jpg',
                            'url'=>'http://www.wx.baiyy.com'),
                        array('title'=>'微信',
                            'description'=>'微信的支付',
                            'picUrl'=>'http://119.23.251.242/dayugm/wechat/index.php/Public/App/img/1.jpg',
                            'url'=>'http://www.http://wx.baiyy.com'),
                        array('title'=>'baidu',
                            'description'=>'baidu描述',
                            'picUrl'=>'http://www.imooc.com/static/img/common/logo.png',
                            'url'=>'http:/http://wx.baiyy.com/dayugm/wechat/index.php/Home/Login/login.html'),
                    );
                    $content = '<a href="http://www.baidu.com">百度</a>';
                    $template_tuWen = "<xml>
                                <ToUserName><![CDATA[%s]]></ToUserName>
                                <FromUserName><![CDATA[%s]]></FromUserName>
                                <CreateTime>%s</CreateTime>
                                <MsgType><![CDATA[%s]]></MsgType>
                                <ArticleCount>".count($arr)."</ArticleCount>
                                <Articles>";
                    foreach($arr as $k=>$v){
                        $template_tuWen .= "<item>
                                <Title><![CDATA[".$v['title']."]]></Title>
                                <Description><![CDATA[".$v['description']."]]></Description>
                                <PicUrl><![CDATA[".$v['picUrl']."]]></PicUrl>
                                <Url><![CDATA[".$v['url']."]]></Url>
                                </item>";
                    }

                    $template_tuWen .="</Articles>
                                </xml>";
                    $info     = sprintf($template_tuWen, $toUser,$fromUser,$time,'news');
                    echo $info;
                    break;
            }

            $info     = sprintf($template, $toUser, $fromUser, $time, $msgType, $content);
            echo $info;
        }

    }

    //$url  接口url string
    //$type 请求类型string
    //$res  返回类型string
    //$arr= 请求参数string
    public function http_curl($url,$type='get',$res='json',$arr=''){

        //1.初始化curl
        $ch  =curl_init();
        //2.设置curl的参数
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);

        if($type == 'post'){
            curl_setopt($ch,CURLOPT_POST,1);
            curl_setopt($ch,CURLOPT_POSTFIELDS,$arr);
        }
        //3.采集
        $output =curl_exec($ch);

        //4.关闭
        curl_close($ch);
        if($res=='json'){
            if(curl_error($ch)){
                //请求失败，返回错误信息
                return curl_error($ch);
            }else{
                //请求成功，返回错误信息

                return json_decode($output,true);
            }
        }
    }
    //获取微信服务器IP地址
    function  getWxServerIp(){
        $accessToken ="Y7cYto0UvJz1U-9NpN04lhQuBkO5BO7Sim6hCZ0GkZlLLfisnvXLjg6VTX_v7veESGX24zAIlu31GD5YXjQFWd5AYXkQTv5a1iGIwk9oxL4gPeWC3fCciWTp2e5ftVZvUXFcAHAHKS";
        $url = "https://api.weixin.qq.com/cgi-bin/getcallbackip?access_token=".$accessToken;
        $ch  =curl_init();
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        $res =curl_exec($ch);
        //5.关闭curl
        curl_close($ch);
        if(curl_error($ch)){
            var_dump(curl_error($ch));
        }
        $arr=json_decode($res,true);
        echo "<pre>";
        var_dump($arr);
        echo "</pre>";
    }
//返回access_token *session解决办法 存mysql memcache
    public function  getWxAccessToken(){
        $appid = 'wx3a1ee3b59d50e2c1';
        $appsecret = '3474a2584a8d62608fbe5d0ee541c1a6';
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$appid."&secret=".$appsecret;
        $res=$this->http_curl($url,'get');
//            echo $res;
        $access_token =$res['access_token'];
        return $access_token;
//        if( $_SESSION['access_token'] && $_SESSION['expire_time']>time()){
//            //如果access_token在session没有过期
////            echo $_SESSION['access_token'];;
//            return $_SESSION['access_token'];
//        }else{
//            //如果access_token比存在或者已经过期，重新取access_token
//            //1 请求url地址
//            $appid = 'wx3a1ee3b59d50e2c1';
//            $appsecret = '3474a2584a8d62608fbe5d0ee541c1a6';
//            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$appid."&secret=".$appsecret;
//            $res=$this->http_curl($url,'get');
////            echo $res;
//            $access_token =$res['access_token'];
//            //将重新获取到的aceess_token存到session
//            $_SESSION['access_token']=$access_token;
//            $_SESSION['expire_time']=time()+7000;
//            return $access_token;
//        }
    }
    public function  definedItem(){
        //创建微信菜单
        //目前微信接口的调用方式都是通过 curl post/get
        header('content-type:text/html;charset=utf-8');
        $access_token=$this ->getWxAccessToken();
        $url ='https://api.weixin.qq.com/cgi-bin/menu/create?access_token='.$access_token;
        $postArr=array(
            'button'=>array(
                array(
                    'name'=>urlencode('招商加盟'),
                    'type'=>'click',
                    'key'=>'item1',
                ),
                array(
                    'name'=>urlencode('新手必看'),
                    'sub_button'=>array(
                        array(
                            'name'=>urlencode('新手教程'),
                            'type'=>'view',
                            'url'=>'http://wx.baiyy.com/dayugm/wechat/index.php/Home/public/responseMss'
//                            'key'=>'item2'
                        ),//第一个二级菜单
                        array(
                            'name'=>urlencode('下载游戏'),
                            'type'=>'view',
                            'url'=>'http://wx.baiyy.com/dayugm/wechat/index.php/Home/Look/look'
                        ),//第二个二级菜单
                    )
                ),

                array(
                    'name'=>urlencode('关于我们'),
                    'sub_button'=>array(
                        array(
                            'name'=>urlencode('我的茶楼'),
                            'type'=>'view',
                            'url'=>'http://47.74.148.181/dayugm/wechat/index.php/Home/Login/login.html'
                        ),
                        array(
                            'name'=>urlencode('官方网站'),
                            'type'=>'view',
                            'url'=>'http://wx.baiyy.com/dayugm/wechat/index.php/Home/Login/login.html'
                        )

                    )
                ),//第三个一级菜单

            ));
        $postJson = urldecode(json_encode($postArr));
        $res = $this->http_curl($url,'post','json',$postJson);
    }

    public function getEwm($scene_id) {
        $access_token = $this->getWxAccessToken();
        $url = 'https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token='.$access_token;
//       临时的
//        $data = array(
//            "expire_seconds"=>604800,
//            "action_name"=>"QR_SCENE",
//            "action_info"=>array(
//                "scene"=>array("scene_id"=> 10000)
//            ),
//        );
//        $postData = urldecode(json_encode($data));

//        $scene_id = R("Home/Login/setSceneid");
//        永久二维码
        $datas = array(
            "action_name"=>"QR_LIMIT_SCENE",
            "action_info"=>array(
                "scene"=>array("scene_id"=> $scene_id)
            ),
        );
        $postsData =urldecode(json_encode($datas));

        $res = $this->http_curl($url,'post','json',$postsData);
        $result = $res['ticket'];
        $urli = "https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=".urlencode($result);
//        echo "<img src='".$urli."'/>";
        return $urli;
    }

//    获取用户的基本信息
    public function getInfo($openId) {
        $access_token = $this->getWxAccessToken();
        $url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$access_token."&openid=".$openId;
        $res = $this->http_curl($url,'post');
        $result = json_encode($res);
        return $result;
    }
    public function info() {
        if (isset($_GET['code'])){
            echo $_GET['code'];
            $aUrl="https://api.weixin.qq.com/sns/oauth2/access_token?appid=wx3a1ee3b59d50e2c1&secret=3474a2584a8d62608fbe5d0ee541c1a6&code=".$_GET['code']."&grant_type=authorization_code";
            $data = redirect($aUrl);
            var_dump($data);
        }else{
            echo "NO CODE";
        }
    }
    public function getId() {
        $url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx3a1ee3b59d50e2c1&redirect_uri=http://wx.baiyy.com/dayugm/wechat/index.php/Home/public/info&response_type=code&scope=snsapi_userinfo&state=111#wechat_redirect";
        redirect($url,0);
    }
    public function getOpenid() {
        if(!$_GET['code']) {
            $rurl = 'http://wx.baiyy.com/dayugm/wechat/index.php/Home/Wxpay/info';
            $url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx3a1ee3b59d50e2c1&redirect_uri=".$rurl."&response_type=code&scope=snsapi_base&state=111#wechat_redirect";
            redirect($url,0);
        }else{
            $aUrl="https://api.weixin.qq.com/sns/oauth2/access_token?appid=wx3a1ee3b59d50e2c1&secret=3474a2584a8d62608fbe5d0ee541c1a6&code=".$_GET['code']."&grant_type=authorization_code";
            $data = http_curl($aUrl);
            return $data['openid'];
        }
    }
    public function getIn() {
        $data = redirect("https://api.weixin.qq.com/sns/oauth2/access_token?appid=wx3a1ee3b59d50e2c1&secret=3474a2584a8d62608fbe5d0ee541c1a6&code=021QIiWE0J4d8j2ZpNTE05zCWE0QIiWi&grant_type=authorization_code");
//        $data = $this->http_curl("https://api.weixin.qq.com/sns/oauth2/access_token?appid=wx3a1ee3b59d50e2c1&secret=3474a2584a8d62608fbe5d0ee541c1a6&code=021QIiWE0J4d8j2ZpNTE05zCWE0QIiWi&grant_type=authorization_code");
        var_dump($data);
    }
    public function getIc() {
        $rurl = 'http://wx.baiyy.com/dayugm/wechat/index.php/Home/public/info';
        $url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx3a1ee3b59d50e2c1&redirect_uri=".$rurl."&response_type=code&scope=snsapi_base&state=111#wechat_redirect";
        $data = redirect($url);
    }
}