<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function index(){
        $this->display();
    }
    public function emptyCount() {
        $agent_id = session('agent_id');
        $uOb = M('User');
        $result = $uOb->where("refer='%s'",$agent_id)->field("game_id,create_time")->select();
        $count = 0;
        if($result) {
            for($i=0;$i<count($result);$i++) {
                if($result[$i]['game_id'] == 0) {
                    if($result[$i]['create_time'] + 86400 < time()) {
                        $uOb->where("game_id='%s'",$result[$i]['game_id'])->delete();
                        $count++;
                    }
                }
            }
            $uOb->where("agent_id='%s'",$agent_id)->setInc('sub_player',$count);
        }
    }
    public function agent() {
        $this->emptyCount();
        $this->assign('user_id',session('user_id'));
        $user_id = session('user_id');
        $agent_id = session('agent_id');
        $uOb=M('User');
        $aOb = M('Agent');
        $result=$uOb->where("login_id='%s'","$user_id")->find();
        $re = $aOb->where("role='%s'",$result['role'])->find();
        $maxExpv = $re['amount'];
        $this->assign('max',$maxExpv);
        $this->assign('expv',floor($result['total_reagent']+$result['total_replayer']));
        $str = 0;
        if(strlen($maxExpv)>=5) {
            $str = substr($maxExpv,0,(strlen($maxExpv)-4)).'万';
        }else{
            $str = $maxExpv;
        }
        $this->assign('maxExpv',$str);
        $subs = $uOb->where('refer='."'$agent_id'")->field('login_id,name,agent_id,tea_name,city')->order('create_time desc')->select();
        $strs = '';
        if(strlen($result['name'])>=9) {
            $strs = substr($result['name'],0,9).'...';
        }else{
            $strs = $result['name'];
        }
        $this->assign('name',$strs);
        $this->assign('role',$result['role']);
        $this->assign('sub_player',$result['sub_player']);
        $this->assign('agent_id',$result['agent_id']);
        $this->assign('total_rebate',$result['total_rebate']);
        $this->assign('subs',$subs);
        $this->display();
    }

    public function upRecord() {
        $agent_id = I('agent_id');
        $rOb = M('Uprecord');
        $result = $rOb->where("agent_id='%s'",$agent_id)->find();
        if($result) {
            if($result['tag'] == 1) {
                $res = $rOb->where("agent_id='%s'",$agent_id)->save(array('tag'=>0,'add_time'=>time()));
                if($res) {
                    $this->ajaxReturn('success');
                }else{
                    $this->ajaxReturn('error');
                }
            }else{
                $this->ajaxReturn('error');
            }
        }else{
            $re = $rOb->add(array('agent_id'=>"$agent_id",'tag'=>0,'add_time'=>time()));
            if($re) {
                $this->ajaxReturn('success');
            }else{
                $this->ajaxReturn('error');
            }
        }
    }
    public function searchInfo() {
        $search = I('search');
        $agent_id = session('agent_id');
        $uOb = M('User');
        $condition['login_id'] = $search;
        $condition['name'] = $search;
        $condition['agent_id'] = $search;
        $condition['tea_name'] = $search;
        $condition['city'] = $search;
        $condition['_logic'] = 'OR';
        $result = $uOb->where($condition)->field('login_id,name,agent_id,tea_name,game_id,city,refer')->order('create_time desc')->select();
        $arr = array();
        if($result) {
            for($i=0;$i<count($result);$i++) {
                if($result[$i]['refer'] == $agent_id) {
                    array_push($arr,$result[$i]);
                }
            }
            $this->ajaxReturn($arr);
        }else{
            $this->ajaxReturn('error');
        }
    }

    public function agentDetail() {
        $agent_id = I('agent_id');
        $uOb = M('User');
        $result = $uOb->where("agent_id='%s'",$agent_id)->find();
        if($result && $result['game_id'] != 0) {
            $url=C('API_URL').'/?act=querySourceByUid&uid='.$result['game_id'];
            $curl_res=get_curl($url);
            if($curl_res['code']) {
                $this->assign('cash',0);
                $this->assign('card',0);
            }else{
                $this->assign('cash',$curl_res['status']['cash']);
                $this->assign('card',$curl_res['status']['card']);
            }
        }
        $this->assign('agent_id',$result['agent_id']);
        $this->assign('login_id',$result['login_id']);
        $this->assign('game_id',$result['game_id']);
        $this->assign('nick_name',$result['nick_name']);
        $this->assign('tea_name',$result['tea_name']);
        $this->assign('agent_id',$result['agent_id']);
        $this->assign('city',$result['city']);
        $this->assign('names',$result['name']);
        $this->assign('tel',$result['tel']);
        $this->assign('card_id',$result['card_id']);
        $this->assign('name',$result['name']);
        $this->assign('notice',$result['notice']);
        if($result['game_id']) {
            $gotEwm = A('Home/Public');
            $urls = $gotEwm->getEwm($agent_id);
            $this->assign('urls',$urls);
        }
        $this->display();
    }

    public function saveInfo() {
        $data = I('POST.');
        $agent_id = $data['agent_id'];
        $gotEwm = A('Home/Public');
        $urls = $gotEwm->getEwm($agent_id);
        $uOb = M('User');
        $result = $uOb->where("agent_id='%s'",$data['agent_id'])->save(array('game_id'=>$data['game_id'],'city'=>$data['city'],'tea_name'=>$data['tea_name'],'name'=>$data['names'],'tel'=>$data['tel'],'card_id'=>$data['card_id'],'notice'=>$data['notice'],'password'=>md5('dyqp0000'),'','city'=>$data['city'],'remark'=>$data['remark']));
        if($result){
            $res = $uOb->where("agent_id='%s'",$data['agent_id'])->find();
            $url=C('API_URL').'/?act=setInviter&uid='.$data['game_id'].'&username='.$data['names'].'&notice='.$data['notice'].'&city='.$data['city'].'&login_id='.$res['login_id'];
            $this->ajaxReturn('success');
        }else{
            $this->ajaxReturn('error');
        }
    }

    public function printEwm() {
        $uOb = M('User');
        $result = $uOb->where("game_id=110002")->find();
        $gotEwm = A('Home/Public');
        $urls = $gotEwm->getEwm($result['agent_id']);
        echo $urls;
        echo "<img src='.$urls.'/>";
    }
    public function checkUid() {
        $agent_id = I('agent_id');
        $game_id = I('game_id');
        $uOb = M('User');
        $res = $uOb->where("agent_id='%s'",$agent_id)->find();
        $aagent_id = $res['refer'];
        $record = $uOb->where("agent_id='%s'",$aagent_id)->find();
        $agame_id = $record['game_id'];
        $url=C('API_URL').'/?act=queryHasUser&uid='.$game_id;
        $curl_res=get_curl($url);
        if($curl_res['code'] == 20) {
            $this->ajaxReturn('noeffect'); //玩家id无效
        }else if($curl_res['isAgent'] == true) {
            $this->ajaxReturn('isAgent'); //已经是代理
        }else if($curl_res['isAgent'] == false){
            if($curl_res['agentId'] != 0) {  //已经绑定
                $this->ajaxReturn('hasupper');
            }else{
                $urls=C('API_URL').'/?act=setUserInviter&uid='.$game_id.'&inviter='.$agame_id;
                $curl_ress=get_curl($urls);
                if($curl_ress['agentId'] == $agame_id) {
                    $re = $uOb->where("agent_id='%s'",$agent_id)->save(array('game_id'=>$game_id,'nick_name'=>$curl_ress['userName'])); //设置成功
                    if($re) {
                        $this->ajaxReturn('success'); //设置成功
                    }else{
                        $this->ajaxReturn('error'); //设置失败
                    }
                }else if($curl_res['code'] == 811){
                    $this->ajaxReturn('logerr');//先登录游戏
                }else{
                    $this->ajaxReturn('failure');//设置的参数有问题
                }
            }
        }

        $result = $uOb->where("game_id='%s'",$game_id)->find();
        if($result) {

        }
    }

    public function agentInfo() {
        $uOb=M('User');
        if(I('user_id')) {
            $user_id = I('user_id');
        }else{
            $user_id = session('user_id');
        }
        $agent_id = session('agent_id');
        $result=$uOb->where("login_id='%s'",$user_id)->find();
        $role = $this->isAgent($result['agent_id']);
        $widthdraw = ($result['total_reagent']/1+$result['total_replayer']/1) - $result['total_withdraw']/1;
        $this->assign('widthdraw',$widthdraw);
        $this->assign('name',$result['name']);
        $this->assign('agent_id',$result['agent_id']);
        $this->assign('tea_name',$result['tea_name']);
        $this->assign('city',$result['city']);
        $this->assign('refer',$result['refer']);
        $this->assign('game_id',$result['game_id']);
        $this->assign('nick_name',$result['nick_name']);
        $this->assign('tel',$result['tel']);
        $this->assign('role',$role);

        $gotEwm = A('Home/Public');
        $urls = $gotEwm->getEwm($agent_id);
        $this->assign('urls',$urls);
        $this->display();
    }

    public function isAgent($obj) {
        $class = 0;
        $uOb=M('User');
        $result = $uOb->where("agent_id='%s'",$obj)->find();
        if($result && $result['refer'] == 0) {
            return $class = 1;
        }else{
            $re = $uOb->where("agent_id='%s'",$result['refer'])->find();
            if($re && $re['refer'] == 0) {
                return $class = 2;
            }else{
                $res = $uOb->where("agent_id='%s'",$re['refer'])->find();
                if($res && $res['refer'] == 0) {
                    return $class = 3;
                }else{
                    return $class = 0;
                }
            }
        }
    }

    public function buyCard() {
        $game_id = I('game_id');
        $num = I('num');
        $id = I('id');
        $gotUrl = A('Home/Paywx');
        $data = $gotUrl->test($num,$id,$game_id);
        \Think\Log::write($data,'哈哈');
        if($data) {
            $this->ajaxReturn($data);
        }else{
            $this->ajaxReturn('error');
        }
    }
    public function getSource() {
        $game_id = I('game_id');
        $url=C('API_URL').'/?act=querySourceByUid&uid='.$game_id;
        $curl_res=get_curl($url);
        if($curl_res['code']) {
            if($curl_res['code'] == 811) {
                $this->ajaxReturn('logerr');
            }else{
                $this->ajaxReturn('error');
            }
        }else{
            $this->ajaxReturn($curl_res['status']['card']);
        }
    }
//    public function giveCard() {
//        $give_id = I('give_id');
//        $give_num = I('give_num');
//        $game_id = I('game_id');
//        $uOb = M('User');
//        $sOb = M('Sendsource');
//        $res = $uOb->where("game_id='%s'",$game_id)->find();
//        $role = $this->isAgent($res['agent_id']);
//        if($role == 1 || $role == 2) {
//            $tag = false;
//            $res = $uOb->where("refer='%s'",$res['agent_id'])->field("game_id")->select();
//            foreach ($res as $v) {
//                \Think\Log::write($v,'WARN');
//                if($v['game_id'] == $give_id) {
//                    $url=C('API_URL').'/?act=giveResource&giver='.$game_id.'&receiver='.$give_id.'&num='.$give_num.'&type=card';
//                    $curl_res=get_curl($url);
//                    if($curl_res == 0){
//                        $sOb->add(array('agent_id'=>$game_id,'game_id'=>$give_id,'number'=>$give_num,'status'=>1,'add_time'=>time()));
//                        $tag = true;
//                    }else{
//                        $sOb->add(array('agent_id'=>$game_id,'game_id'=>$give_id,'number'=>$give_num,'status'=>0,'add_time'=>time()));
//                        $this->ajaxReturn('error');
//                    }
//                }
//            }
//            if($tag) {
//                $this->ajaxReturn('success');
//            }else{
//                $this->ajaxReturn('noeffect');
//            }
//        }else if($role == 3) {
//            $url=C('API_URL').'/?act=giveResource&giver='.$game_id.'&receiver='.$give_id.'&num='.$give_num.'&type=card';
//            $curl_res=get_curl($url);
//            if($curl_res == 0){
//                $sOb->add(array('agent_id'=>$game_id,'game_id'=>$give_id,'number'=>$give_num,'status'=>1,'add_time'=>time()));
//                $this->ajaxReturn('success');
//            }else{
//                $sOb->add(array('agent_id'=>$game_id,'game_id'=>$give_id,'number'=>$give_num,'status'=>0,'add_time'=>time()));
//                $this->ajaxReturn('error');
//            }
//        }
//    }
    public function matchId() {
        $give_id = I('give_id');
        $agent_id = I('agent_id');
        $uOb = M('User');
        $result = $uOb->where("game_id='%s' and refer='%s'",$give_id,$agent_id)->find();
        if($result) {
            $this->ajaxReturn($result['nick_name']);
        }else{
            $url=C('API_URL').'/?act=queryNameAndAgentById&uid='.$give_id;
            $curl_res=get_curl($url);
            if($curl_res['code']) {
                $this->ajaxReturn('error');
            }else if($curl_res['agentId'] == 0){
                $this->ajaxReturn($curl_res['name']);
            }else{
                $this->ajaxReturn('hasAgent');
            }
        }
    }
    public function giveCard() {
        $give_id = I('give_id');
        $give_num = I('give_num');
        $game_id = I('game_id');
        $uOb = M('User');
        $sOb = M('Sendsource');
        $tag = false;
        $url=C('API_URL').'/?act=giveResource&giver='.$game_id.'&receiver='.$give_id.'&num='.$give_num.'&type=card';
        $curl_res=get_curl($url);
        \Think\Log::write($curl_res,'WARN');
        if($curl_res == 0){
            $sOb->add(array('agent_id'=>$game_id,'game_id'=>$give_id,'number'=>$give_num,'status'=>1,'add_time'=>time()));
            $tag = true;
        }else{
            $sOb->add(array('agent_id'=>$game_id,'game_id'=>$give_id,'number'=>$give_num,'status'=>0,'add_time'=>time()));
            $tag = true;
        }
        if($tag) {
            $this->ajaxReturn('success');
        }else{
            $this->ajaxReturn('error');
        }
    }

    public function modName() {
        $uOb=M('User');
        $new_name = I('new_name');
        $user_id = session('user_id');
        $result=$uOb->where('login_id='."'$user_id'")->save(array('name'=>$new_name));
        if($result) {
            $this->ajaxReturn('success');
        }else{
            $this->ajaxReturn('error');
        }
    }

    public function sendCode() {
        $tel = I('tel');
        $code = A('Home/Msn');
        $codes = rand(10000, 90000);
        $codes = $code->send_verify("$tel");
        setcookie('code',"'$codes'",time()+120);
        if($codes){
            $this->ajaxReturn('success');
        }else{
            $this->ajaxReturn('error');
        }

    }

    public function checkCode() {
        $agent_id = session('agent_id');
        $uOb = M('User');
        $result = $uOb->where("agent_id='%s'","$agent_id")->find();
        $ensure = I("ensure");
        $en_pwd = I('en_pwd');
        $code = $_COOKIE['code'];
        setcookie('code','',time()-120);
        if("'$ensure'" != "$code") {
            $this->ajaxReturn('notCode');
        }else{
            if(md5($en_pwd) == $result['password']) {
                $this->ajaxReturn('repeat');
            }else{
                $re = $uOb->where("agent_id='%s'","$agent_id")->save(array('password' => md5("$en_pwd")));
                if($re) {
                    $this->ajaxReturn('success');
                }else{
                    $this->ajaxReturn('error');
                }
            }
        }
    }
    public function withDraws() {
        $agent_id = I('agent_id');
        $amount = I('ondraw');
        $pro_id = I('pro_id');
        $uOb = M('User');
        $wOb = M('Withdraw');
        $record = $wOb->where("user_id='%s'",$agent_id)->select();
        $dateStr = date('Y-m-d', time());
        $date  = time();
        $timestamp = strtotime($dateStr) + 86400;
        $distance = $timestamp - $date;
        if($record) {
            for($i=0;$i<count($record);$i++) {
                if($record[$i]['add_time'] + $distance > time()) {
                    $this->ajaxReturn('timeIn');
                    return;
                }
            }
            $result = $uOb->where("agent_id='%s'","$agent_id")->find();
            if($result) {
                $re = $wOb->add(array('user_id'=>$agent_id,'number'=>$amount,'add_time'=>time(),'pro_id'=>$pro_id,'tel'=>$result['tel'],'name'=>$result['name'],'login_id'=>$result['login_id'],'order_id'=>date('YmdHis',time()).rand(10000, 99999)));
                if($re) {
                    $res = $uOb->where("agent_id='%s'","$agent_id")->setInc('total_withdraw',"$amount");
                    if($res) {
                        $this->ajaxReturn('success');
                    }else{
                        $this->ajaxReturn('failure');
                    }
                }else{
                    $this->ajaxReturn('error');
                }
            }else{
                $this->ajaxReturn('noUser');
            }
        }else{
            $result = $uOb->where("agent_id='%s'","$agent_id")->find();
            if($result) {
                $re = $wOb->add(array('user_id'=>$agent_id,'number'=>$amount,'add_time'=>time(),'pro_id'=>$pro_id,'tel'=>$result['tel'],'name'=>$result['name'],'login_id'=>$result['login_id'],'order_id'=>date('YmdHis',time()).rand(10000, 99999)));
                if($re) {
                    $res = $uOb->where("agent_id='%s'","$agent_id")->setInc('total_withdraw',"$amount");
                    if($res) {
                        $this->ajaxReturn('success');
                    }else{
                        $this->ajaxReturn('failure');
                    }
                }else{
                    $this->ajaxReturn('error');
                }
            }else{
                $this->ajaxReturn('noUser');
            }
        }
    }

    public function createLoginid() {
        $agent_id = session('agent_id');
        $login_id = I('login_id');
        $uOb = M('User');
        $result = $uOb->field('*')->where("login_id='%s'",$login_id)->find();
        if($result) {
            $this->ajaxReturn('exist');
        }else{
            $res = $uOb->where("agent_id='%s'",$agent_id)->field('sub_player')->find();
            if($res['sub_player'] < 1) {
                $this->ajaxReturn('error');
            }else{
                $re = $uOb->where("agent_id="."$agent_id")->setDec('sub_player',1);
                if($re) {
                    $record = $uOb->add(array('login_id'=>"$login_id",'refer'=>$agent_id,'create_time'=>time()));
                    if($record) {
                        $this->ajaxReturn('success');
                    }else{
                        $this->ajaxReturn('failure');
                    }
                }else{
                    $this->ajaxReturn('setfailure');
                }
            }
        }
    }

    public function order() {
        $agent_id = session('agent_id');
        $wOb = M('Withdraw');
        $result = $wOb->where('user_id='."'$agent_id'")->order('add_time desc')->select();
        $this->assign('arr',$result);
        $this->display();
    }

    public function orderSearch() {
        $agent_id = session("agent_id");
        $search = I('search');
        $wOb = M('Withdraw');
        $result = $wOb->where("order_id='%s' and user_id='%s'",$search,$agent_id)->find();
        if($result) {
            $this->ajaxReturn($result);
        }else{
            $this->ajaxReturn('error');
        }
    }

    public function rebate_owner() {
        $agent_id = session('agent_id');
        $aOb = M('Reagent');
        $uOb = M('User');
        $re = $uOb->where("agent_id='%s'",$agent_id)->find();
        $result = $aOb->where("agent_user_id='%s'",$re['game_id'])->order('add_time desc')->select();
        $this->assign('total_reagent',$re['total_reagent']);
        $this->assign('arr',$result);
        $this->display();
    }

    public function reAgent() {
        $agent_id = session('agent_id');
        $search = I('search');
        $condition['player_id'] = "$search";
        $condition['name'] = "$search";
        $condition['login_id'] = "$search";
        $condition['_logic'] = 'OR';
        $aOb = M('Reagent');
        $result = $aOb->where($condition)->order('add_time desc')->select();
        $arr = array();
        if($result) {
            for($i=0;$i<count($result);$i++) {
                if($result[$i]['agent_id'] == $agent_id) {
                    array_push($arr,$result[$i]);
                }
            }
            $this->ajaxReturn($arr);
        }else{
            $this->ajaxReturn('error');
        }
    }

    public function rePlayer() {
        $agent_id = session('agent_id');
        $search = I('search');
        $condition['player_id'] = "$search";
        $condition['name'] = "$search";
        $condition['_logic'] = 'OR';
        $pOb = M('Replayer');
        $uOb = M('User');
        $re = $uOb->where("agent_id='%s'",$agent_id)->find();
        $result = $pOb->where($condition)->order('add_time desc')->select();
        $arr = array();
        if($result) {
            for($i=0;$i<count($result);$i++) {
                if($result[$i]['agent_user_id'] == $re['game_id']) {
                    array_push($arr,$result[$i]);
                }
            }
            $this->ajaxReturn($arr);
        }else{
            $this->ajaxReturn('error');
        }
    }

    public function rebate_user() {
        $agent_id = session('agent_id');
        $pOb = M('Replayer');
        $uOb = M('User');
        $re = $uOb->where("agent_id='%s'",$agent_id)->find();
        $result = $pOb->where("agent_user_id='%s'",$re['game_id'])->order('add_time desc')->select();
        $this->assign('total_replayer',$re['total_replayer']);
        $this->assign('arr',$result);
        $this->display();
    }
    public function main(){
        echo "<h1>测试</h1>";
    }
    function qrcode($url="weixin://wxpay/bizpayurl?pr=c78U3g7",$level=3,$size=4) {
        Vendor('phpqrcode.phpqrcode');
        $errCorrectionLevel = intval($level);
        $matrixPointSize = intval($size);
        $object = new \QRcode();
        $object->png($url,false,$errCorrectionLevel,$matrixPointSize,2);
    }
}