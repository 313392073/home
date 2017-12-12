<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/7
 * Time: 17:12
 */
namespace Admin\Controller;
use Think\Controller;
class PayController extends Controller {
//    public function index(){
//        $str = $this->pay();
//        $str_url=urlencode($str);
//        $appid = "wx3a1ee3b59d50e2c1";
//        $url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$appid.'&redirect_uri='.$str_url.'&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect';
//        header("Location:".$url);
//    }
    public function pay($openId,$num,$orderId){
        $appid = "wx3a1ee3b59d50e2c1";
        $mch_appid=$appid;
        $mchid='1480796272';//商户号
        $nonce_str='qyzf'.rand(100000, 999999);//随机数
        $partner_trade_no=date ( "YmdHis" ).rand(10000, 99999);//商户订单号
        $openid = $openId;//用户唯一标识
        $check_name='NO_CHECK';//校验用户姓名选项，NO_CHECK：不校验真实姓名 FORCE_CHECK：强校验真实姓名（未实名认证的用户会校验失败，无法转账）OPTION_CHECK：针对已实名认证的用户才校验真实姓名（未实名认证用户不校验，可以转账成功）
        $re_user_name='测试';//用户姓名
        $amount=floor($num);//金额（以分为单位，必须大于100 （100就是一块））
        $desc='代理提现';//描述
        $spbill_create_ip=$_SERVER["REMOTE_ADDR"];//请求ip
//封装成数据
        $dataArr=array();
        $dataArr['amount']=$amount;
        $dataArr['check_name']=$check_name;
        $dataArr['desc']=$desc;
        $dataArr['mch_appid']=$mch_appid;
        $dataArr['mchid']=$mchid;
        $dataArr['nonce_str']=$nonce_str;
        $dataArr['openid']=$openid;
        $dataArr['partner_trade_no']=$partner_trade_no;
        $dataArr['re_user_name']=$re_user_name;
        $dataArr['spbill_create_ip']=$spbill_create_ip;

        $sign=$this->getSign($dataArr);
        $tOb = M('Withdraw');
        $res = $tOb->where("order_id='%s'",$orderId)->save(array('torder_id'=>$partner_trade_no,'openid'=>$openId));

//echo "-----<br/>签名：".$sign."<br/>*****";//die;
        $data="<xml>
                <mch_appid>".$mch_appid."</mch_appid>
                <mchid>".$mchid."</mchid>
                <nonce_str>".$nonce_str."</nonce_str>
                <partner_trade_no>".$partner_trade_no."</partner_trade_no>
                <openid>".$openid."</openid>
                <check_name>".$check_name."</check_name>
                <re_user_name>".$re_user_name."</re_user_name>
                <amount>".$amount."</amount>
                <desc>".$desc."</desc>
                <spbill_create_ip>".$spbill_create_ip."</spbill_create_ip>
                <sign>".$sign."</sign>
                </xml>";
//var_dump($data);

        $ch = curl_init ();
        $MENU_URL="https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers";
        curl_setopt ( $ch, CURLOPT_URL, $MENU_URL );
        curl_setopt ( $ch, CURLOPT_CUSTOMREQUEST, "POST" );
        curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, FALSE );
        curl_setopt ( $ch, CURLOPT_SSL_VERIFYHOST, FALSE );

        $zs1="./ThinkPHP/Payment/apiclient_cert.pem";
        $zs2="./ThinkPHP/Payment/apiclient_key.pem";
        curl_setopt($ch,CURLOPT_SSLCERT,$zs1);
        curl_setopt($ch,CURLOPT_SSLKEY,$zs2);
// curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01;
// Windows NT 5.0)');
        curl_setopt ( $ch, CURLOPT_FOLLOWLOCATION, 1 );
        curl_setopt ( $ch, CURLOPT_AUTOREFERER, 1 );
        curl_setopt ( $ch, CURLOPT_POSTFIELDS, $data );
        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );

        $info = curl_exec ( $ch );
        if (curl_errno ( $ch )) {
            return false;
            echo 'Errno:::::::' . curl_error ( $ch );
        }
//        \Think\Log::write($openId,'嘿嘿');
        curl_close ( $ch );
//echo "-----<br/>请求返回值：";
//        echo "请求的结果：<br />";

        $Data = $this->xmlToArray($info);
        if(array_key_exists("return_code",$Data) && $Data['return_code'] == 'SUCCESS') {
            if($Data['result_code'] == 'SUCCESS') {
                $ress = $tOb->where("torder_id='%s'",$Data['partner_trade_no'])->save(array('status'=>1));
                if($ress) {
                    return true;
                }else{
                    return false;
                }
            }
        }
    }
    public function xmlToArray($xml) {
        libxml_disable_entity_loader(true);
        $result = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $result;
    }
    function getSign($Obj)
    {
        //var_dump($Obj);//die;
        foreach ($Obj as $k => $v)
        {
            $Parameters[$k] = $v;
        }
        //签名步骤一：按字典序排序参数
        ksort($Parameters);
        $String = $this->formatBizQueryParaMap($Parameters, false);
        //echo '【string1】'.$String.'</br>';
        //签名步骤二：在string后加入KEY
        $String = $String."&key=baiYYbaiYYbaiYYbaiYYbaiYYbaiYY12";
        //echo "【string2】".$String."</br>";
        //签名步骤三：MD5加密
        $String = md5($String);
        //echo "【string3】 ".$String."</br>";
        //签名步骤四：所有字符转为大写
        $result_ = strtoupper($String);
        //echo "【result】 ".$result_."</br>";
        return $result_;
    }
    function formatBizQueryParaMap($paraMap, $urlencode)
    {
        //var_dump($paraMap);//die;
        $buff = "";
        ksort($paraMap);
        foreach ($paraMap as $k => $v)
        {
            if($urlencode)
            {
                $v = urlencode($v);
            }
            //$buff .= strtolower($k) . "=" . $v . "&";
            $buff .= $k . "=" . $v . "&";
        }
        $reqPar = 0;
        if (strlen($buff) > 0)
        {
            $reqPar = substr($buff, 0, strlen($buff)-1);
        }
        //var_dump($reqPar);//die;
        return $reqPar;
    }
}