<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/14
 * Time: 21:03
 */
namespace Admin\Controller;
use Think\Controller;
class PlayerController extends AuthController {
    public function playerList(){
        if(I('rows')) {
            $page = I('page');
            $rows = I('rows');
            $arr = array();
            $datas = array();
            if(!empty(I('search'))) {
                $tag = preg_match("/^\d{6}$/",I('search'));
                if($tag) {
                    $url=C('API_URL').'/?act=queryAllUsers&page='.$page.'&rows='.$rows.'&uid='.I('search');
                    $curl_res=get_curl($url);
                    if(count($curl_res['userInfo']) > 1) {
                        $curl_res['userInfo']['name'] = $curl_res['userInfo']['info']['name'];
                        $curl_res['userInfo']['createTime'] = $curl_res['userInfo']['info']['createTime'];
                        $curl_res['userInfo']['logoutTime'] = $curl_res['userInfo']['marks']['logoutTime']?$curl_res['items']['info']['logoutTime']:$curl_res['items'][$i]['info']['createTime'];
                        $curl_res['userInfo']['card'] = $curl_res['userInfo']['status']['card'];
                        $curl_res['userInfo']['coin'] = $curl_res['userInfo']['status']['coin'];
                        $arr['total'] = 1;
                        array_push($datas,$curl_res['userInfo']);
                        $arr['rows'] = $datas;
                    }else{
                        $arr['total'] = 0;
                        $arr['rows'] = 0;
                    }
                }else{
                    $url=C('API_URL').'/?act=queryUserByName&page='.$page.'&rows='.$rows.'&name='.I('search');
                    $curl_res=get_curl($url);
                    if($curl_res) {
                        for($i=0;$i<count($curl_res['arr']);$i++) {
                            $curl_res['arr'][$i]['name'] = $curl_res['arr'][$i]['info']['name'];
                            $curl_res['arr'][$i]['createTime'] = $curl_res['arr'][$i]['info']['createTime'];
                            $curl_res['arr'][$i]['logoutTime'] = $curl_res['arr'][$i]['marks']['logoutTime']?$curl_res['items'][$i]['marks']['logoutTime']:$curl_res['items'][$i]['info']['createTime'];
                            $curl_res['arr'][$i]['coin'] = $curl_res['arr'][$i]['status']['coin'];
                            $curl_res['arr'][$i]['card'] = $curl_res['arr'][$i]['status']['card'];
                        }
                        $arr['total'] = count($curl_res['arr']);
                        $arr['rows'] = $curl_res['arr'];
                    }else{
                        $arr['total'] = 0;
                        $arr['rows'] = 0;
                    }
                }
            }else{
                $url=C('API_URL').'/?act=queryAllUsers&page='.$page.'&rows='.$rows;
                $curl_res=get_curl($url);
                if($curl_res) {
                    for($i=0;$i<count($curl_res['items']);$i++) {
                        $curl_res['items'][$i]['name'] = $curl_res['items'][$i]['info']['name'];
                        $curl_res['items'][$i]['createTime'] = $curl_res['items'][$i]['info']['createTime'];
                        $curl_res['items'][$i]['logoutTime'] = $curl_res['items'][$i]['marks']['logoutTime']?$curl_res['items'][$i]['marks']['logoutTime']:$curl_res['items'][$i]['info']['createTime'];
                        $curl_res['items'][$i]['coin'] = $curl_res['items'][$i]['status']['coin'];
                        $curl_res['items'][$i]['card'] = $curl_res['items'][$i]['status']['card'];
                    }
                    $arr['total'] = $curl_res['sumNums'];
                    $arr['rows'] = $curl_res['items'];
                }else{
                    $arr['total'] = 0;
                    $arr['rows'] = 0;
                }
            }
            $this->ajaxReturn(json_decode(json_encode($arr)));
        }else{
            $this->display();
        }
    }


    public function playerListExport(){
        $xlsName = '玩家列表';
        $xlsCell = array(
            array('uid','玩家ID'),
            array('name','真实姓名'),
            array('createTime','注册时间')
        );
        $url=C('API_URL').'/?act=exportUsersInfo&startTime='.I('start_time').'&endTime='.I('end_time');
        $curl_res=get_curl($url);
        $xlsData = $curl_res['arr'];
        foreach ($xlsData as $k=>$v) {
            $xlsData[$k]['createTime'] = date('Y-m-d H:i',$xlsData[$k]['createTime']);
        }
        action(session('admin_id'),0,'playerListExport',null,null);
        exportExcel($xlsName,$xlsCell,$xlsData);
    }

    public function modBlock(){
        $uid = I('uid');
        $block = I('block');
        $url=C('API_URL').'/?act=setBlock&uid='.$uid.'&block='.$block;
        $curl_res=get_curl($url);
        action(session('admin_id'),$uid,'modBlock',null,null);
        $this->ajaxReturn($curl_res);
    }
    public function getPlayerInfo(){
        if(I('search')) {
            $uOb = M('User');
            $search = I('search');
            $result = $uOb->where("game_id='%s'",$search)->find();
            if($result) {
                $this->ajaxReturn('isAgent');
            }else{
                $urll=C('API_URL').'/?act=getUser&uid='.$search;
                $curl_ress=get_curl($urll);
                if($curl_ress['code']) {
                    $this->ajaxReturn('noUser');
                }else{
                    $url=C('API_URL').'/?act=queryUserAgent&uid='.$search;
                    $curl_res=get_curl($url);
                    if($curl_res == null){
                        $this->ajaxReturn('error');
                    }else if($curl_res['code']) {
                        $this->ajaxReturn('noSource');
                    }else{
                        $this->ajaxReturn($curl_res['agentId']);
                    }
                }
            }
        }else{
            $data = I('post.');
            $uOb = M('User');
            $refer = $uOb->where("game_id='%s'",$data['refer'])->find();  //代理自己的game_id
            $res = $uOb->where("login_id='%s'",$data['login_id'])->find();
            $tres = $uOb->where("tel='%s'",$data['tel'])->find();
            if($res) {
                $this->ajaxReturn('lexist');
                return;
            }
            if($tres) {
                $this->ajaxReturn('texist');
                return;
            }
            $url=C('API_URL').'/?act=queryAllUsers&uid='.$data['game_id'];
            $curl_res=get_curl($url);
            $nick_name = $curl_res['userInfo']['info']['name']?$curl_res['userInfo']['info']['name']:'';
            $result = $uOb->add(array('game_id'=>$data['game_id'],'refer'=>$refer['agent_id'],'nick_name'=>urldecode($nick_name),'create_time'=>time(),"login_id"=>$data['login_id'],"tea_name"=>urldecode($data['tea_name']),"city"=>urldecode($data['city']),"notice"=>urldecode($data['notice']),"name"=>urldecode($data['named']),"tel"=>$data['tel'],'password'=>md5("dyqp0000")));
            $recode = $uOb->where("game_id='%s'",$data['game_id'])->find();
            if($result) {
                $url=C('API_URL').'/?act=setInviter&uid='.$data['game_id'].'&username='.$data['named'].'&notice='.$data['notice'].'&city='.$data['city'].'&login_id='.$data['login_id'].'&agentId='.$recode['agent_id'];
                $curl_res=get_curl($url);
                action(session('admin_id'),$data['game_id'],'getPlayerInfo',null,null);
                $this->ajaxReturn('success');
            }else{
                $this->ajaxReturn('error');
            }
        }
    }
    public function saveInfo(){
        $data = I('post.');
        $uOb = M('User');
        $refer = $uOb->where("game_id='%s'",$data['refer'])->find();  //代理自己的game_id
        $res = $uOb->where("login_id='%s'",$data['login_id'])->find();
        $tres = $uOb->where("tel='%s'",$data['tel'])->find();
        if($res) {
            $this->ajaxReturn('lexist');
            return;
        }
        if($tres) {
            $this->ajaxReturn('texist');
            return;
        }
        $url=C('API_URL').'/?act=queryAllUsers&uid='.$data['game_id'];
        $curl_res=get_curl($url);
        $nick_name = $curl_res['userInfo']['info']['name']?$curl_res['userInfo']['info']['name']:'';
        $result = $uOb->add(array('game_id'=>$data['game_id'],'refer'=>$refer['agent_id'],'nick_name'=>urldecode($nick_name),'create_time'=>time(),"login_id"=>$data['login_id'],"tea_name"=>urldecode($data['tea_name']),"city"=>urldecode($data['city']),"notice"=>urldecode($data['notice']),"name"=>urldecode($data['named']),"tel"=>$data['tel'],'password'=>md5("dyqp0000")));
        $recode = $uOb->where("game_id='%s'",$data['game_id'])->find();
        if($result) {
            $url=C('API_URL').'/?act=setInviter&uid='.$data['game_id'].'&username='.$data['named'].'&notice='.$data['notice'].'&city='.$data['city'].'&login_id='.$data['login_id'].'&agentId='.$recode['agent_id'];
            $curl_res=get_curl($url);
            $this->ajaxReturn('success');
        }else{
            $this->ajaxReturn('error');
        }
    }

    public function playerLog(){
        if(I('rows')) {
            $rOb = M('Resourcelog');
            $limit = I('rows');
            $page = I('page');
            $offset = ($page-1)*$limit;
            if(!empty(I('uid'))) {
                $count = $rOb->where('user_id='.I('uid').' and add_time>='.I('start_time').' and add_time <='.I('end_time'))->count();
                $result = $rOb->where('user_id='.I('uid').' and add_time>='.I('start_time').' and add_time <='.I('end_time'))->order('add_time desc')->limit($offset,$limit)->select();
            }else{
                $time = time()-608400;
                $count = $rOb->where("add_time >= ".$time)->count();
                $result = $rOb->where("add_time >= ".$time)->order('add_time desc')->limit($offset,$limit)->select();
                $arr = array();
            }
            if($result) {
                $arr['total'] = $count;
                $arr['rows'] = $result;
            }else{
                $arr['total'] = 0;
                $arr['rows'] = 0;
            }
            $this->ajaxReturn(json_decode(json_encode($arr)));
        }else{
            $this->display();
        }
    }


    public function playerMatch(){
        if(I('rows')) {
            $limit = I('rows');
            $page = I('page');
            $offset = ($page-1)*$limit;
            $mOb = M('Matchroomlog');
            if(!empty(I('timed'))) {
                $startTime = strtotime(date(I('timed')));
                $endTime = strtotime(date(I('timed')))+86400;
                $count = $mOb->where("add_time >= ".$startTime." and add_time <= ".$endTime)->count();
                $result = $mOb->where("add_time >= ".$startTime." and add_time <= ".$endTime)->limit($offset,$limit)->order('add_time desc')->select();
            }else{
                $time = time() - 432000;
                $count = $mOb->where("add_time >= ".$time)->count();
                $result = $mOb->where("add_time >= " .$time)->limit($offset,$limit)->order('add_time desc')->select();
            }
            $arr = array();
            if($result) {
                $arr['total'] = $count;
                $arr['rows'] = $result;
            }else{
                $arr['total'] = 0;
                $arr['rows'] = 0;
            }
            $this->ajaxReturn(json_decode(json_encode($arr)));
        }else{
            $this->display();
        }
    }

    public function getMatchDetail(){
        $mOb = M('Matchroomlog');
        $Id = I('Id');
        $result = $mOb->where("id='%s'",$Id)->find();
        $this->assign('time',$result['add_time']);
        $this->assign('info',json_decode($result['info'],true));
        $this->display();
    }

    public function playerChange(){
        if($_GET['search']) {
            $search = $_GET['search'];
            $tag = preg_match("/^\d{6}$/",$search);
            $arr = array();
            $data = array();
            if($tag) {
                $url = C('API_URL').'/?act=getUser&uid='.$search;
                $curl_res = get_curl($url);
                if($curl_res['code']) {
                    $arr['total'] = 0;
                    $arr['rows'] = 0;
                }else{
                    $curl_res['createTime'] = $curl_res['info']['createTime'];
                    $curl_res['name'] = $curl_res['info']['name'];
                    $curl_res['logoutTime'] = $curl_res['marks']['logoutTime'];
                    $curl_res['card'] = $curl_res['status']['card'];
                    $curl_res['coin'] = $curl_res['status']['coin'];
                    array_push($data,$curl_res);
                    $arr['total']=1;
                    $arr['rows']=$data;
                }
            }else{
                $arr['total'] = 0;
                $arr['rows'] = 0;
            }
            $this->ajaxReturn(json_decode(json_encode($arr)));
        }else{
            $this->display();
        }
    }
    public function setPlayerSource(){
        $uid = I('uid');
        $cards = I('cards');
        $url=C('API_URL').'/?act=alterResource&uid='.$uid.'&num='.$cards.'&type=card&reason=adminChange';
        $curl_res=get_curl($url);
        if($curl_res['code']) {
            $this->ajaxReturn('error');
        }else{
            action(session('admin_id'),$uid,'setPlayerSource',null,$cards);
            $this->ajaxReturn('success');
        }
    }
}