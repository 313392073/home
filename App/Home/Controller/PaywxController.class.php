<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/10
 * Time: 15:12
 */

namespace Home\Controller;
use Think\Controller;
class PaywxController extends Controller {
    public function test() {
        ini_set('date.timezone','Asia/Shanghai');
        import ( "@.Controller.WxPay.WxPayJsApiPay" );
        import ( "@.Controller.WxPay.WxPayApi" );
//        import ( "@.Controller.WxPay.log" );
        //获取用户的openid
        $tools = new \JsApiPay();
        $openId = $tools->GetOpenid();
        //统一下单
        $input = new \WxPayUnifiedOrder();
        $input->SetBody("百亿云科技");
        $input->SetAttach("test");
        $input->SetOut_trade_no(\WxPayConfig::MCHID.date("YmdHis"));
        $input->SetTotal_fee("1");
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + 600));
        $input->SetGoods_tag("test");
        $input->SetNotify_url("http://47.74.148.181/dayugm/wechat/index.php/Home/Paywx/notify");
        $input->SetTrade_type("JSAPI");
        $input->SetOpenid($openId);
//        同意下单的支付信息
        $order = \WxPayApi::unifiedOrder($input);
        \Think\Log::write($order,'WARN');
        $jsApiParameters = $tools->GetJsApiParameters($order);
        //获取共享收获地址js函数参数
        $editAddress = $tools->GetEditAddressParameters();
        $data = array();
        $data['order'] = $jsApiParameters;
        $data['address'] = $editAddress;
        return $data;

    }
    public function pay(){
        $data = $this->test();
        $this->assign("jsApiParameters",$data['order']);
        $this->assign("editAddress",$data['address']);
        $this->display();
    }
    public function notify() {
        import ( "@.Controller.WxPay.WxPayNativePay" );
        import ( "@.Controller.WxPay.WxPayData" );
        import ( "@.Controller.WxPay.WxPayNotify" );
        import ( "@.Controller.WxPay.PayNotifyCallBack" );

        $xmlData = $GLOBALS['HTTP_RAW_POST_DATA'];
        $Data = $this->xmlToArray($xmlData);
//        $bOb = M('Buylist');
//        $status = 0;
        \Think\Log::write($Data['out_trade_no'],'WARN');
//        if(array_key_exists ( "return_code", $Data )  && $Data['result_code'] == 'SUCCESS' ) {
//            if($Data['result_code'] == 'SUCCESS') {
//                $res = $bOb->where("order_id='%s'",$Data['out_trade_no'])->save(array('status'=>1));
//                $result = $bOb->where("order_id='%s'",$Data['out_trade_no'])->find();
//                $num = $result['number']*100;
//                if($res) {
//                    $url=C('API_URL').'/?act=alterResource&uid='.$result['game_id'].'&num='.$num.'&type=card&reason=buy';
//                    $curl_res=get_curl($url);
//                    if($curl_res == 0) {
//                        $this->success('支付成功','http://47.74.148.181/dayugm/wechat/index.php/Home/Index/agentInfo.html?user_id='.$result['game_id']);
//                    }else{
//                        $this->error('支付失败');
//                    }
//                }else{
//                    $this->error('支付失败');
//                }
//            }else{
//                $this->error('支付失败');
//            }
//        }
        // 给微信返回支付状态值
        $notify = new \PayNotifyCallBack ();
        // 返回状态
        $notify->Handle ( false );
    }
}