<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/3
 * Time: 14:57
 */
namespace Home\Controller;
use Think\Controller;
class WxpayController extends Controller {
    public function erCode($price, $proId, $game_id,$openId) {
        ini_set('date.timezone','Asia/Shanghai');
        import ( "@.Controller.WxPay.WxPayApi" );
        import ( "@.Controller.WxPay.WxPayNativePay" );
        import ( "@.Controller.WxPay.WxPayData" );
        $notify = new \WxPayNativePay ();
        $obj = array();
        $obj['game_id'] = $game_id;
        $obj['pro_id'] = $proId;
        $obj['order_id'] = date ( "YmdHis" ).\WxPayConfig::MCHID ;
        $obj['number'] = $price/100;
		$input = new \WxPayUnifiedOrder ();
		$input->SetBody ( "百亿云科技" . date("YmdHis").\WxPayConfig::MCHID  );
		$input->SetAttach ( $obj['game_id'] );
		$input->SetOut_trade_no ( $obj['order_id'] );
		$input->SetTotal_fee ($obj['number']);
		$input->SetTime_start ( date ( "YmdHis" ) );
		$input->SetTime_expire ( date ( "YmdHis", time () + 600 ) );
		$input->SetGoods_tag ( "card_tag" );
		$input->SetNotify_url ( "http://47.74.148.181/dayugm/wechat/index.php/Home/Wxpay/notify" );
		$input->SetTrade_type ( "JSAPI" );
		$input->SetOpenid($openId);
		$input->SetProduct_id ( $obj['pro_id'] );
		$result = $notify->GetPayUrl ( $input );
        $url2 = $result ["code_url"];
        $bOb = M('Buylist');
        if($url2) {
            $result = $bOb->add(array('pro_id'=>$obj['pro_id'],'game_id'=>$obj['game_id'],'order_id'=>$obj['order_id'],'number'=>$obj['number'].'/100','add_time'=>time(),'status'=>0));
        }
		$url = urldecode($url2);
		return $url;
//        \QRcode::png ( $url );
	}


    public function notify() {
        $data ='{"appid":"wx3a1ee3b59d50e2c1","attach":"wx3a1ee3b59d50e2c1","bank_type":"CFT","cash_fee":"1","fee_type":"CNY","is_subscribe":"Y","mch_id":"1480796272","nonce_str":"fit7dx23o69ihjtduegagfkghagq60mb","openid":"obj5EwQrliNjjvmK_C6zr3-wb3aw","out_trade_no":"201711041706421480796272","result_code":"SUCCESS","return_code":"SUCCESS","sign":"93DA2CD26650C4A4046517D68D29F533","time_end":"20171104170652","total_fee":"1","trade_type":"NATIVE","transaction_id":"4200000018201711042435094758"}';
        $data = json_decode($data,true);
        \Think\Log::write($data,'WARN');
//        $bOb = M('Buylist');
        if(array_key_exists ( "return_code", $data )  && $data['result_code'] == 'SUCCESS' ) {
            if($data['result_code'] == 'SUCCESS') {
                $url=C('API_URL').'/?act=alterResource&uid=110002&num=10&type=card&reason=buy';
                $curl_res=get_curl($url);
                var_dump($curl_res);
                if($curl_res == 0) {
                    $this->success('支付成功','Home/Index/agentInfo');
                }else{
                    $this->error('支付失败');
                }
//                $res = $bOb->where("order_id='%s'",$data['out_trade_no'])->save(array('status'=>1));
//                if($res) {
//                    $url=C('API_URL').'/?act=alterResource&uid='.$result['game_id'].'&num='.$num.'&type=card&reason=buy';
//                    $curl_res=get_curl($url);
//                    if($curl_res == 0) {
//                        $this->success('支付成功','Home/Index/agentInfo');
//                    }else{
//                        $this->error('支付失败');
//                    }
//                }
            }
        }
        // 给微信返回支付状态值
        //$notify = new \PayNotifyCallBack ();
        // 返回状态
//        $notify->Handle ( false );
    }

    public function getOpenid(){
        $openid = $_COOKIE[''];
    }

    public function xmlToArray($xml) {
        libxml_disable_entity_loader(true);
        $result = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $result;
    }
//	public function notify() {
//		// 获取微信回调的数据
//		$notifiedData = $GLOBALS ['HTTP_RAW_POST_DATA'];
//		// 加载相关的类
//		import ( "@.Controller.WxPay.WxPayNativePay" );
//		import ( "@.Controller.WxPay.WxPayData" );
//		import ( "@.Controller.WxPay.WxPayNotify" );
//		import ( "@.Controller.WxPay.PayNotifyCallBack" );
//		// 转成数组 并写入缓存
//		F ( "wx_notified_data", \WxPayDataBase::FromXml_4_babbage ( $notifiedData ) );
//		// 吧xml原型也写入xml
//		F ( "wx_notified_data_xml", $notifiedData );
//
//		// 给微信返回支付状态值
//		$notify = new \PayNotifyCallBack ();
//		// 返回状态
//		$notify->Handle ( false );
//	}
//	public function getNotify() {
//		print_r ( F ( "wxpay_HTTP_RAW_POST_DATA" ) );
//	}
}
