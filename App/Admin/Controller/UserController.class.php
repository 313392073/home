<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/13
 * Time: 16:54
 */
namespace Admin\Controller;
use Think\Controller;
class UserController extends AuthController{
    public function userList() {
        if(I('rows')){
            $limit = I('rows');
            $page = I('page');
            $offset = ($page-1)*$limit;
            if($_GET['nextPlayer']) {
                $search = $_GET['nextPlayer'];
                $uOb = M('User');
                $rOb = M('Replayer');
                $result = $uOb->where("agent_id='%s'",$search)->find();
                $arr = array();
                $url = C('API_URL').'/?act=queryAgentUsers&uid='.$result['game_id'];
                $curl_res = get_curl($url);
                if($curl_res['code']) {
                    $arr['total'] = 0;
                    $arr['rows'] = 0;
                    $arr['has'] = 'has';
                }else{
                    $count = count($curl_res['items']);
                    if($curl_res['items']) {
                        $record = $rOb->where("agent_user_id='%s'",$result['game_id'])->group('user_id')->field('*,SUM(amount) as total')->select();
                        foreach ($curl_res['items'] as $k=>$v) {
                            $curl_res['items'][$k]['total'] = number_format(0,2);
                            foreach ($record as $kk=>$vv) {
                                if($curl_res['items'][$k]['uid'] == $record[$kk]['user_id']) {
                                    $curl_res['items'][$k]['total'] = number_format($record[$kk]['total'],2);
                                }
                            }
                        }
                        $curl_res['items'] = $this->arraySequence($curl_res['items'],'total','SORT_DESC');
                        $curl_res['items'] = array_slice($curl_res['items'],$offset,$limit);
                        $arr['total'] = $count;
                        $arr['rows'] = $curl_res['items'];
                        $arr['has'] = 'has';
                    }else{
                        $arr['total'] = 0;
                        $arr['rows'] = 0;
                        $arr['has'] = 'has';
                    }
                }
                $this->ajaxReturn(json_decode(json_encode($arr)));
            }else if($_GET['nextAgent']){
                $search = $_GET['nextAgent'];
                $uOb = M('User');
                $result = $uOb->where("agent_id='%s'",$search)->find();
                $game_id = $result['game_id'];
                $rOb = M('Reagent');
                $arr = array();
                $reuser = $uOb->where("refer='%s' and game_id != 0",$search)->field('login_id,agent_id,name,city,create_time,game_id')->select();
                $rerebate = $rOb->where("agent_user_id='%s'",$game_id)->field('player_user_id,agent_user_id,amount,SUM(amount) as total')->group('player_user_id')->select();

                foreach ($reuser as $k=>$v) {
                    $reuser[$k]['total'] = number_format(0,2);
                    foreach ($rerebate as $kk => $vv) {
                        if($reuser[$k]['game_id'] == $rerebate[$kk]['player_user_id']) {
                            $reuser[$k]['total'] = number_format($rerebate[$kk]['total'],2);
                        }
                    }
                }
                $arr = $this->arraySequence($reuser,'total','SORT_DESC');
                $count = count($arr);
                $res = array_slice($arr,$offset,$limit);
                if($reuser) {
                    $arr['total'] = $count;
                    $arr['rows'] = $res;
                    $arr['has'] = 'has';
                }else{
                    $arr['total'] = 0;
                    $arr['rows'] = 0;
                    $arr['has'] = 'has';
                }
                $this->ajaxReturn(json_decode(json_encode($arr)));
            }else{
                $uOb = M('User');
                $search = I('search');

                if(I('search') == '初级代理') {
                    $search = 1;
                }else if(I('search') == '中级代理') {
                    $search = 2;
                }else if(I('search') == '高级代理') {
                    $search = 3;
                }else if(I('search') == '钻石代理') {
                    $search = 4;
                }
                $condition['login_id'] = $search;
                $condition['name'] = $search;
                $condition['game_id'] = $search;
                $condition['agent_id'] = $search;
                $condition['tea_name'] = $search;
                $condition['role'] = $search;
                $condition['city'] = $search;
                $condition['_logic'] = 'OR';
                if(!empty($search)) {
                    $arrs = array();
                    $res = $uOb->where($condition)->field('agent_id,notice,game_id,tea_name,state,city,name,tel,login_id,role,sub_player,(total_reagent + total_replayer) as total_rebate,((total_reagent + total_replayer) - total_withdraw) as wait_withdraw,create_time,sub_player')->order('create_time desc')->select();
                    foreach ($res as $k=>$v) {
                        if($res[$k]['delete_tag'] == 0 && $res[$k]['game_id'] != 0) {
                            array_push($arrs,$res[$k]);
                        }
                    }
                    $count = count($arrs);
                    $result = array_slice($arrs,$offset,$limit);
                }else{
                    $count = $uOb->where("delete_tag=0 and game_id != 0")->count();
                    $result = $uOb->where("delete_tag=0 and game_id != 0")->field("agent_id,game_id,card_id,tel,tea_name,name,notice,city,login_id,role,sub_player,(total_reagent + total_replayer) as total_rebate,((total_reagent + total_replayer) - total_withdraw) as wait_withdraw,create_time,state")->order("create_time desc")->limit($offset,$limit)->select();
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
            }
        }else{
            $this->display();
        }

    }


    public function userListExport(){
        $xlsName = '馆主列表';
        $xlsCell = array(
            array('agent_id','馆主ID'),
            array('login_id','馆主账号'),
            array('tea_name','茶馆名称'),
            array('city','茶馆区域'),
            array('tel','手机号码'),
            array('name','真实姓名'),
            array('nick_name','昵称'),
            array('role','代理级别'),
            array('total_reagent','馆主返利'),
            array('total_replayer','玩家返利'),
            array('total_withdraw','提现总额'),
            array('wait_withdraw','待提现额'),
            array('create_time','注册时间')
        );
        $xlsModel = M('User');
        $xlsData = $xlsModel->where('game_id != 0')->field('*,((total_reagent + total_replayer)- total_withdraw) wait_withdraw')->order('create_time desc')->select();
        foreach ($xlsData as $k => $v) {
            switch ($v['role']) {
                case '1':
                    $xlsData[$k]['role'] = '初级代理';
                    break;
                case '2':
                    $xlsData[$k]['role'] = '中级代理';
                    break;
                case '3':
                    $xlsData[$k]['role'] = '高级代理';
                    break;
                case '3':
                    $xlsData[$k]['role'] = '钻石代理';
                    break;
            }
            $xlsData[$k]['create_time'] = date('Y-m-d H:i',$v['create_time']);
        }
        action(session('admin_id'),0,'userListExport',null,null);
        exportExcel($xlsName,$xlsCell,$xlsData);
    }

    public function getUserList(){
        $limit = I('rows');
        $page = I('page');
        $offset = ($page-1)*$limit;
        $uOb = M('User');
//        $search = $_GET['search'];
        $search = I('search');

        if(I('search') == '初级代理') {
            $search = 1;
        }else if(I('search') == '中级代理') {
            $search = 2;
        }else if(I('search') == '高级代理') {
            $search = 3;
        }else if(I('search') == '钻石代理') {
            $search = 4;
        }
        $condition['login_id'] = $search;
        $condition['name'] = $search;
        $condition['game_id'] = $search;
        $condition['agent_id'] = $search;
        $condition['tea_name'] = $search;
        $condition['role'] = $search;
        $condition['city'] = $search;
        $condition['_logic'] = 'OR';
        if(!empty($search)) {
            $arrs = array();
            $res = $uOb->where($condition)->field('agent_id,notice,game_id,tea_name,state,city,name,tel,login_id,role,sub_player,(total_reagent + total_replayer) as total_rebate,((total_reagent + total_replayer) - total_withdraw) as wait_withdraw,create_time,sub_player')->order('create_time desc')->select();
            foreach ($res as $k=>$v) {
                if($res[$k]['delete_tag'] == 0 && $res[$k]['game_id'] != 0) {
                    array_push($arrs,$res[$k]);
                }
            }
            $count = count($arrs);
            $result = array_slice($arrs,$offset,$limit);
        }else{
            $count = $uOb->where("delete_tag=0 and game_id != 0")->count();
            $result = $uOb->where("delete_tag=0 and game_id != 0")->field("agent_id,game_id,card_id,tel,tea_name,name,notice,city,login_id,role,sub_player,(total_reagent + total_replayer) as total_rebate,((total_reagent + total_replayer) - total_withdraw) as wait_withdraw,create_time,state")->order("create_time desc")->limit($offset,$limit)->select();
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
    }

    public function arraySequence($array, $field, $sort = 'SORT_DESC')
    {
        $arrSort = array();
        foreach ($array as $uniqid => $row) {
            foreach ($row as $key => $value) {
                $arrSort[$key][$uniqid] = $value;
            }
        }
        array_multisort($arrSort[$field], constant($sort), $array);
        return $array;
    }
    public function getNextuser(){
        $limit = I('rows');
        $page = I('page');
        $offset = ($page-1)*$limit;
        $search = $_GET['search'];
        $uOb = M('User');
        $result = $uOb->where("agent_id='%s'",$search)->find();
        $game_id = $result['game_id'];
        $rOb = M('Reagent');
        $arr = array();
        $reuser = $uOb->where("refer='%s' and game_id != 0",$search)->field('login_id,agent_id,name,city,create_time,game_id')->select();
        $rerebate = $rOb->where("agent_user_id='%s'",$game_id)->field('player_user_id,agent_user_id,amount,SUM(amount) as total')->group('player_user_id')->select();

        foreach ($reuser as $k=>$v) {
            $reuser[$k]['total'] = number_format(0,2);
            foreach ($rerebate as $kk => $vv) {
                if($reuser[$k]['game_id'] == $rerebate[$kk]['player_user_id']) {
                    $reuser[$k]['total'] = number_format($rerebate[$kk]['total'],2);
                }
            }
        }
        $arr = $this->arraySequence($reuser,'total','SORT_DESC');
        $count = count($arr);
        $res = array_slice($arr,$offset,$limit);
        if($reuser) {
            $arr['total'] = $count;
            $arr['rows'] = $res;
            $arr['has'] = 'has';
        }else{
            $arr['total'] = 0;
            $arr['rows'] = 0;
            $arr['has'] = 'has';
        }
        $this->ajaxReturn(json_decode(json_encode($arr)));
    }

    public function userRebatetotal(){
        if(I('rows')) {
            $limit = I('rows');
            $page = I('page');
            $offset = ($page-1)*$limit;
            $pOb = M('Replayer');
            $rOb = M('Reagent');
            $uOb = M('User');
            $ulist = $uOb->where("delete_tag=0 and game_id != 0")->field("agent_id,login_id,role,name,city,create_time,game_id,tea_name")->order("create_time desc")->select();
            //30%的返利
            $resultt = $pOb->group('agent_user_id')->field('*,sum(amount) as total')->select();
            foreach ($ulist as $k => $v) {
                $ulist[$k]['trebate'] = number_format(0,3);
                foreach ($resultt as $kk => $vv) {
                    if($ulist[$k]['game_id'] == $resultt[$kk]['agent_user_id']) {
                        $ulist[$k]['trebate1'] = $resultt[$kk]['total'];
                        $ulist[$k]['trebate'] = number_format($resultt[$kk]['total'],3);
                    }
                }
            }
            //8%的返利
            $resulte = $rOb->where('rebate_level = 1')->group('agent_user_id')->field('*,sum(amount) as total')->select();
            foreach ($ulist as $k => $v) {
                $ulist[$k]['erebate'] = number_format(0,3);
                foreach ($resulte as $kk => $vv) {
                    if($ulist[$k]['game_id'] == $resulte[$kk]['agent_user_id']) {
                        $ulist[$k]['erebate1'] = $resulte[$kk]['total'];
                        $ulist[$k]['erebate'] = number_format($resulte[$kk]['total'],3);
                    }
                }
            }
            //5%的返利
            $resultf = $rOb->where('rebate_level = 2')->group('agent_user_id')->field('*,sum(amount) as total')->select();
            foreach ($ulist as $k => $v) {
                $ulist[$k]['frebate'] = number_format(0,3);
                foreach ($resulte as $kk => $vv) {
                    if($ulist[$k]['game_id'] == $resultf[$kk]['agent_user_id']) {
                        $ulist[$k]['frebate1'] = $resultf[$kk]['total'];
                        $ulist[$k]['frebate'] = number_format($resultf[$kk]['total'],3);
                    }
                }
            }

            foreach ($ulist as $k=>$v) {
                $total = $ulist[$k]['trebate1']+$ulist[$k]['erebate1']+$ulist[$k]['frebate1'];
                $ulist[$k]['total'] = number_format($total,3);
            }
            $count = count($ulist);
            $res = array_slice($ulist,$offset,$limit);
            if($ulist) {
                $arr['total'] = $count;
                $arr['rows'] = $res;
            }else{
                $arr['total'] = 0;
                $arr['rows'] = 0;
            }
            $this->ajaxReturn(json_decode(json_encode($arr)));
        }else{
            $this->display();
        }
    }

    public function getnextPlayer(){
        $limit = I('rows');
        $page = I('page');
        $offset = ($page-1)*$limit;
        $search = $_GET['search'];
        $uOb = M('User');
        $rOb = M('Replayer');
        $result = $uOb->where("agent_id='%s'",$search)->find();
        $arr = array();
        $url = C('API_URL').'/?act=queryAgentUsers&uid='.$result['game_id'];
        $curl_res = get_curl($url);
        if($curl_res['code']) {
            $arr['total'] = 0;
            $arr['rows'] = 0;
            $arr['has'] = 'has';
        }else{
            $count = count($curl_res['items']);
            if($curl_res['items']) {
                $record = $rOb->where("agent_user_id='%s'",$result['game_id'])->group('user_id')->field('*,SUM(amount) as total')->select();
                foreach ($curl_res['items'] as $k=>$v) {
                    $curl_res['items'][$k]['total'] = number_format(0,2);
                    foreach ($record as $kk=>$vv) {
                        if($curl_res['items'][$k]['uid'] == $record[$kk]['user_id']) {
                            $curl_res['items'][$k]['total'] = number_format($record[$kk]['total'],2);
                        }
                    }
                }
                $curl_res['items'] = $this->arraySequence($curl_res['items'],'total','SORT_DESC');
                $curl_res['items'] = array_slice($curl_res['items'],$offset,$limit);
                $arr['total'] = $count;
                $arr['rows'] = $curl_res['items'];
                $arr['has'] = 'has';
            }else{
                $arr['total'] = 0;
                $arr['rows'] = 0;
                $arr['has'] = 'has';
            }
        }
        $this->ajaxReturn(json_decode(json_encode($arr)));
    }

    public function modState(){
        $agent_id = I('agent_id');
        $state = I('state');
        $uOb = M('User');
        $record = $uOb->where("agent_id='%s'",$agent_id)->find();
        $result = $uOb->where("agent_id='%s'",$agent_id)->save(array('state'=>$state));
        if($result) {
            action(session('admin_id'),I('agent_id'),'modState',$record['state'],$state);
            $this->ajaxReturn('success');
        }else{
            $this->ajaxReturn('error');
        }
    }

    public function userInfoSave(){
        $data = I('post.');
        $uOb = M('User');
        $res = $uOb->where("agent_id='%s'",$data['agent_id'])->find();
        $this->assign('login_id',$res['login_id']);
        if($res['login_id']) {
            $result = $uOb->where("agent_id='%s'",$data['agent_id'])->save(array('name'=>urldecode($data['name']),'card_id'=>$data['card_id'],'tel'=>$data['tel'],'tea_name'=>$data['tea_name'],'city'=>urldecode($data['city']),'notice'=>urldecode($data['notice']),'sub_player'=>$data['sub_player']));
        }else{
            $result = $uOb->where("agent_id='%s'",$data['agent_id'])->save(array('login_id'=>$data['login_id'],'name'=>urldecode($data['name']),'card_id'=>$data['card_id'],'tel'=>$data['tel'],'tea_name'=>$data['tea_name'],'city'=>urldecode($data['city']),'notice'=>urldecode($data['notice']),'sub_player'=>$data['sub_player']));
        }
        $url=C('API_URL').'/?act=setInviterAttr&uid='.$res['game_id'].'&username='.$data['name'].'&notice='.$data['notice'].'&city='.$data['city'].'&login_id='.$data['login_id'].'&agentId='.$data['agent_id'];
        $curl_res=get_curl($url);
        $Datas = array();
        $Datas['agent_id'] = $res['agent_id'];
        $Datas['name'] = $res['name'];
        $Datas['login_id'] = $res['login_id'];
        $Datas['card_id'] = $res['card_id'];
        $Datas['tel'] = $res['tel'];
        $Datas['tea_name'] = $res['tea_name'];
        $Datas['city'] = $res['city'];
        $Datas['notice'] = $res['notice'];
        $Datas['sub_player'] = $res['sub_player'];
        $Datas = json_encode($Datas);
        $data = array();
        $data['agent_id'] = $res['agent_id'];
        $data['name'] = urldecode($res['name']);
        $data['login_id'] = $res['login_id'];
        $data['card_id'] = $res['card_id'];
        $data['tel'] = $res['tel'];
        $data['tea_name'] = $res['tea_name'];
        $data['city'] = urldecode($res['city']);
        $data['notice'] = urldecode($res['notice']);
        $data['sub_player'] = $res['sub_player'];
        $data = json_encode($data);
        if($result){
            action(session('admin_id'),$res['agent_id'],'userInfoSave',$Datas,$data);
            $this->ajaxReturn('success');
        }else{
            $this->ajaxReturn('error');
        }
    }

    public function userHoist() {
        if(I('rows')) {
            $limit = I('rows');
            $page = I('page');
            $offset = ($page-1)*$limit;
            $rOb = M('Uprecord');
            $count = $rOb->where("tag=0")->count();
            $result = $rOb->alias('r')->join('__USER__ u on u.agent_id=r.agent_id')
                ->where('tag=0')->field('r.*,u.city,u.tea_name,u.sub_player,u.agent_id,u.tea_name,u.game_id,u.login_id,u.name,u.role,(u.total_reagent + u.total_replayer) as total_rebate,((total_reagent + total_replayer) - total_withdraw) as wait_withdraw')->order('add_time desc')->limit($offset,$limit)->select();
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
    public function agreeHoist(){
        $agent_id = I('agent_id');
        $rOb = M('Uprecord');
        $uOb = M('User');
        $aOb = M('Agent');
        $re = $uOb->where("agent_id='%s'",$agent_id)->find();
        $role = $aOb->where("role='%s'",$re['role'])->find();
        $uprole = $aOb->where("role='%s'",($re['role']+1))->find();
        $result = null;
        $sub_player = $re['sub_player'];
        $differ = $uprole['sub_player']-$role['sub_player'];
        if($re['role'] > 3) {
            $result = $uOb->where("agent_id='%s'",$agent_id)->save(array('role'=>4,'sub_player'=>($sub_player+$differ)));
        }else{
            $result = $uOb->where("agent_id='%s'",$agent_id)->save(array('role'=>($re['role']+1),'sub_player'=>($sub_player+$differ)));
        }
        if($result) {
            $res = $rOb->where("agent_id='%s'",$agent_id)->save(array('tag'=>1));
            if($res) {
                action(session('admin_id'),$agent_id,'agreeHoist',$role['role'],($role['role']+1));
                $this->ajaxReturn('success');
            }else{
                $this->ajaxReturn('error');
            }
        }else{
            $this->ajaxReturn('error');
        }
    }
    public function userWithdraw(){
        if(I('rows')) {
            $limit = I('rows');
            $page = I('page');
            $offset = ($page-1)*$limit;
            $wOb = M('Withdraw');
            $count = $wOb->where("status=0 and delete_tag=0")->count();
            $result = $wOb->where('status=0 and delete_tag=0')->order('add_time desc')->limit($offset,$limit)->select();
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

    public function agreeWithdraw(){
        $agent_id = I('agent_id');
        $order_id = I('order_id');
        $wOb = M('Withdraw');
        $re = $wOb->where("order_id='%s'",$order_id)->find();
        $uOb = M('User');
        $res = $uOb->where("agent_id='%s'",$agent_id)->find();
        $getPay = A('Admin/Pay');
        $pays = $getPay->pay($res['openid'],$re['number'],$order_id);
        if($pays) {
            $record = $wOb->where("order_id='%s'",$order_id)->save(array('status'=>1,'mod_time'=>time()));
            action(session('admin_id'),$agent_id,'agreeWithdraw',null,null);
            $this->ajaxReturn('success');
        }else{
            $this->ajaxReturn('error');
        }
    }

    public function delWidthdraw(){
        $agent_id = I('agent_id');
        $order_id = I('order_id');
        $wOb = M('Withdraw');
        $uOb = M('User');
        $result = $wOb->where("order_id='%s'",$order_id)->save(array('status'=>1,'delete_tag'=>1,'mod_time'=>time()));
        if($result) {
            $res = $wOb->where("order_id='%s'",$order_id)->find();
            $recode = $uOb->where("agent_id='%s'",$agent_id)->setDec('total_withdraw',$res['number']);
            if($recode) {
                action(session('admin_id'),$agent_id,'delWidthdraw',null,null);
                $this->ajaxReturn('success');
            }else{
                $this->ajaxReturn('error');
            }
        }else{
            $this->ajaxReturn('error');
        }

    }

    public function userSetrebate(){
        $rOb = M('Ratio');
        $result = $rOb->where('id=1')->find();
        $this->assign('rebate1',$result['rebate1']);
        $this->assign('rebate2',$result['rebate2']);
        $this->assign('rebate3',$result['rebate3']);
        $this->display();
    }

    public function setRebate() {
        $rebate1 = I('rebate1');
        $rebate2 = I('rebate2');
        $rebate3 = I('rebate3');
        $rOb = M('Ratio');
        $result = $rOb->where('id=1')->save(array('rebate1'=>$rebate1,'rebate2'=>$rebate2,'rebate3'=>$rebate3));
        if($result) {
            action(session('admin_id'),0,'setRebate',null,null);
            $this->ajaxReturn('success');
        }else{
            $this->ajaxReturn('error');
        }
    }
    public function userSetinit(){
        $aOb = M('Agent');
        $result = $aOb->field('role,sub_player')->select();
        $data = array();
        foreach ($result as $k=>$v) {
            if($result[$k]['role'] == 1) {
                $data['sub1'] = $result[$k]['sub_player'];
            }
            if($result[$k]['role'] == 2) {
                $data['sub2'] = $result[$k]['sub_player'];
            }
            if($result[$k]['role'] == 3) {
                $data['sub3'] = $result[$k]['sub_player'];
            }
            if($result[$k]['role'] == 4) {
                $data['sub4'] = $result[$k]['sub_player'];
            }
        }
        $this->assign('sub1',$data['sub1']);
        $this->assign('sub2',$data['sub2']);
        $this->assign('sub3',$data['sub3']);
        $this->assign('sub4',$data['sub4']);
        $this->display();
    }

    public function setUserinit(){
        $data = I('post.');
        $aOb = M('Agent');
        $result1 = $aOb->where("role=1")->save(array('sub_player'=>$data['sub1']));
        $result2 = $aOb->where("role=2")->save(array('sub_player'=>$data['sub2']));
        $result3 = $aOb->where("role=3")->save(array('sub_player'=>$data['sub3']));
        $result4 = $aOb->where("role=4")->save(array('sub_player'=>$data['sub4']));
        if($result1 || $result2 || $result3 || $result4) {
            action(session('admin_id'),0,'setUserinit',null,null);
            $this->ajaxReturn('success');
        }else{
            $this->ajaxReturn('error');
        }
    }

    public function userWithdrawList(){
        if(I('rows')) {
            $wOb = M('Withdraw');
            $limit = I('rows');
            $page = I('page');
            $offset = ($page-1)*$limit;
            $start_time = I('start_time');
            $end_time = I('end_time');
            $search = I('search');
            if(!empty(I('start_time'))) {
                $count = $wOb->where('mod_time >= '.$start_time.' and mod_time <= '.$end_time.' and status=1')->count();
                $result = $wOb->where('mod_time >= '.$start_time.' and mod_time <= '.$end_time.' and status=1')->order('mod_time desc')->limit($offset,$limit)->select();
            }else if(!empty(I('search'))) {
                $count = $wOb->where('status=1 and user_id='.$search)->count();
                $result = $wOb->where('status=1 and user_id='.$search)->order('mod_time desc')->limit($offset,$limit)->select();
            }else{
                $count = $wOb->where('status=1')->count();
                $result = $wOb->where('status=1')->order('mod_time desc')->limit($offset,$limit)->select();
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

    public function getWithdrawExport(){
        $xlsName = '馆主提现列表';
        $xlsCell = array(
            array('user_id','馆主ID'),
            array('login_id','馆主账号'),
            array('tel','手机号码'),
            array('name','真实姓名'),
            array('pro_id','提现产品号'),
            array('number','提现金额'),
            array('order_id','订单号'),
            array('mod_time','审核时间')
        );
        $xlsModel = M('Withdraw');
        $xlsData = $xlsModel->where('status = 1 and delete_tag = 0')->field('*')->order('mod_time desc')->select();
        foreach ($xlsData as $k => $v) {
            $xlsData[$k]['mod_time'] = date('Y-m-d H:i',$v['mod_time']);
        }
        action(session('admin_id'),0,'getWithdrawExport',null,null);
        exportExcel($xlsName,$xlsCell,$xlsData);
    }

    public function userBuyList(){
        if(I('rows')) {
            $bOb = M('Buylist');
            $limit = I('rows');
            $page = I('page');
            $offset = ($page-1)*$limit;
            $agent_id = I('agent_id');
            $order_id = I('order_id');
            $tag = preg_match("/^\d{4}$/",I('agent_id'));
            if(!empty(I('order_id'))) {
                $count = $bOb->where("order_id='%s'",$order_id)->count();
                $result = $bOb->where("order_id='%s'",$order_id)->order('add_time desc')->limit($offset,$limit)->select();
            }else if(!empty(I('agent_id')) && $tag) {
                $count = $bOb->where("agent_id='%s'",$agent_id)->count();
                $result = $bOb->where("agent_id='%s'",$agent_id)->order('add_time desc')->limit($offset,$limit)->select();
            }else if(!empty(I('agent_id')) && !$tag){
                $count = $bOb->where("name='%s'",$agent_id)->count();
                $result = $bOb->where("name='%s'",$agent_id)->order('add_time desc')->limit($offset,$limit)->select();
            }else{
                $count = $bOb->field('*')->count();
                $result = $bOb->field('*')->order('add_time desc')->limit($offset,$limit)->select();
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

    public function userRebate(){
        if(I('rows')) {
            $uOb = M('User');
            $rOb = M('Reagent');
            $limit = I('rows');
            $page = I('page');
            $offset = ($page-1)*$limit;
            $time = time()-604800;
            $agent_id = I('agent_id');
            $res = $uOb->where("agent_id='%s'",$agent_id)->find();
            $ulist = $uOb->field('game_id,agent_id,tea_name')->select();

            $start_time = I('start_time');
            $end_time = I('end_time');

            if(!empty(I('start_time')) && !I('agent_id') ) {
                $count = $rOb->where('add_time >= '.$start_time.' and add_time <= '.$end_time)->count();
                $result = $rOb->where('add_time >= '.$start_time.' and add_time <= '.$end_time)->order('add_time desc')->limit($offset,$limit)->select();
            }else if(I('agent_id')){
                $count = $rOb->where('agent_user_id='.$res['game_id'].' and add_time >= '.$start_time.' and add_time <= '.$end_time)->count();
                $result = $rOb->where('agent_user_id='.$res['game_id'].' and add_time >= '.$start_time.' and add_time <= '.$end_time)->order('add_time desc')->limit($offset,$limit)->select();
            }else{
                $count = $rOb->where('add_time >'.$time)->count();
                $result = $rOb->where('add_time >'.$time)->order('add_time desc')->limit($offset,$limit)->select();
            }

            foreach ($result as $k => $v){
                $result[$k]['agent_id'] = 0;
                $result[$k]['player_id'] = 0;
                foreach ($ulist as $kk => $vv) {
                    if($result[$k]['agent_user_id'] == $ulist[$kk]['game_id']) {
                        $result[$k]['agent_id'] = $ulist[$kk]['agent_id'];
                    }
                    if($result[$k]['player_user_id'] == $ulist[$kk]['game_id']) {
                        $result[$k]['player_id'] = $ulist[$kk]['agent_id'];
                    }
                }
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

    public function userRplayer(){
        if(I('rows')) {
            $uOb = M('User');
            $rOb = M('Replayer');
            $limit = I('rows');
            $page = I('page');
            $offset = ($page-1)*$limit;
            $time = time()-604800;
            $agent_id = I('agent_id');
            $res = $uOb->where("agent_id='%s'",$agent_id)->find();
            $ulist = $uOb->field('game_id,agent_id,tea_name')->select();

            $start_time = I('start_time');
            $end_time = I('end_time');
            if(!empty(I('start_time')) && !I('agent_id') ) {
                $count = $rOb->where('add_time >= '.$start_time.' and add_time <= '.$end_time)->count();
                $result = $rOb->where('add_time >= '.$start_time.' and add_time <= '.$end_time)->order('add_time desc')->limit($offset,$limit)->select();
            }else if(I('agent_id')){
                $count = $rOb->where('agent_user_id='.$res['game_id'].' and add_time >= '.$start_time.' and add_time <= '.$end_time)->count();
                $result = $rOb->where('agent_user_id='.$res['game_id'].' and add_time >= '.$start_time.' and add_time <= '.$end_time)->order('add_time desc')->limit($offset,$limit)->select();
            }else{
                $count = $rOb->where('add_time >'.$time)->count();
                $result = $rOb->where('add_time >'.$time)->order('add_time desc')->limit($offset,$limit)->select();
            }

            foreach ($result as $k => $v){
                $result[$k]['agent_id'] = 0;
                foreach ($ulist as $kk => $vv) {
                    if($result[$k]['agent_user_id'] == $ulist[$kk]['game_id']) {
                        $result[$k]['agent_id'] = $ulist[$kk]['agent_id'];
                    }
                }
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
}