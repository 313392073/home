<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/30
 * Time: 20:58
 */
namespace Admin\Controller;
use Think\Controller;
class AgentController extends AuthController
{
    public function getAgentList()
    {
        $uOb = M('User');
        $result = $uOb->where("delete_tag=0 and game_id != 0")->field("agent_id,game_id,tea_name,name,city,login_id,role,sub_player,(total_reagent + total_replayer) as total_rebate,((total_reagent + total_replayer) - total_withdraw) as wait_withdraw,create_time,state")->order("create_time desc")->select();
        if ($result) {
            $this->ajaxReturn($result);
        } else {
            $this->ajaxReturn('error');
        }
    }

    public function modState() {
        $agent_id = I('agent_id');
        $state = I('state');
        $uOb = M('User');
        $result = $uOb->where("agent_id='%s'","$agent_id")->save(array('state'=>$state));
        if($result) {
            $this->ajaxReturn('success');
        }else{
            $this->ajaxReturn('error');
        }
    }

    public function InfoSearch() {
        $search = I('search');
        $uOb = M('User');
        $condition['login_id'] = "$search";
        $condition['name'] = "$search";
        $condition['agent_id'] = "$search";
        $condition['tea_name'] = "$search";
        $condition['city'] = "$search";
        $condition['_logic'] = 'OR';
        $result = $uOb->where($condition)->field('agent_id,notice,game_id,tea_name,city,name,tel,login_id,sub_player,(total_reagent + total_replayer) as total_rebate,((total_reagent + total_replayer) - total_withdraw) as wait_withdraw,create_time,sub_player')->order('create_time desc')->select();
        if($result) {
            $this->ajaxReturn($result);
        }else{
            $this->ajaxReturn('error');
        }
    }

    public function lookDetail() {
        $agent_id = I('agent_id');
        $uOb = M('User');
        $result = $uOb->where("agent_id='%s'","$agent_id")->find();
        if($result) {
            $this->ajaxReturn($result);
        }else{
            $this->ajaxReturn('error');
        }
    }

    public function editInfo() {
        $uOb = M('User');
        $data = I('post.');
        $result = $uOb->where("agent_id='%s'",$data['agent_id'])->save(array('tea_name'=>$data['tea_name'],'notice'=>$data['notice'],'name'=>$data['named'],'sub_player'=>$data['sub_player'],'tel'=>$data['tel'],'city'=>$data['city']));
        if($result) {
            $this->ajaxReturn('success');
        }else{
            $this->ajaxReturn('error');
        }
    }

    public function getUpreview() {
        $rOb = M('Uprecord');
        $result = $rOb->alias('r')->join('__USER__ u on u.agent_id=r.agent_id')
        ->where('tag=0')->field('r.*,u.tea_name,u.agent_id,u.tea_name,u.game_id,u.login_id,u.name,u.role,(u.total_reagent + u.total_replayer) as total_rebate')->order('add_time desc')->select();
        if($result) {
            $this->ajaxReturn($result);
        }else{
            $this->ajaxReturn('error');
        }
    }

    public function agreeUprole() {
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
                $this->ajaxReturn('success');
            }else{
                $this->ajaxReturn('error');
            }
        }else{
            $this->ajaxReturn('error');
        }
    }

    public function setRebate() {
        $rebate1 = I('rebate1');
        $rebate2 = I('rebate2');
        $rebate3 = I('rebate3');
        $rOb = M('Ratio');
        $result = $rOb->where('id=1')->save(array('rebate1'=>$rebate1,'rebate2'=>$rebate2,'rebate3'=>$rebate3));
        if($result) {
            $this->ajaxReturn('success');
        }else{
            $this->ajaxReturn('error');
        }
    }

    public function getWithdraw() {
        $wOb = M('Withdraw');
        $result = $wOb->where('status=0')->order('add_time desc')->select();
        if($result) {
            $this->ajaxReturn($result);
        }else{
            $this->ajaxReturn('error');
        }
    }
    public function getTwidthdraw(){
        $tOb = M('Wdtrue');
        $time = time() - 604800 ;
        $result = $tOb->where('add_time <='.$time)->order('add_time desc')->select();
        if($result) {
            $this->ajaxReturn($result);
        }else{
            $this->ajaxReturn('error');
        }
    }
    public function transferAgent() {
        $agent_id = I('agent_id');
        $order_id = I('order_id');
    }
    public function getPlayerInfo(){
        $uOb = M('User');
        $search = I('search');
        $result = $uOb->where("game_id='%s'",$search)->find();
        if($result) {
            $this->ajaxReturn('isAgent');
        }else{
            $url=C('API_URL').'/?act=queryUserAgent&uid='.$search;
            $curl_res=get_curl($url);
            if($curl_res['code']) {
                $this->ajaxReturn('noSource');
            }else{
                $tag = false;
                if($curl_res['agentId'] == 0) {
                    $res = $uOb->add(array('game_id'=>$search,'refer'=>0,'create_time'=>time()));
                    $tag = true;
                }else{
                    $record = $uOb->where("game_id='%s'",$curl_res['agentId'])->find();
                    $res = $uOb->add(array('game_id'=>$search,'refer'=>$record['agent_id'],'create_time'=>time()));
                    $tag = true;
                }
                $re = $uOb->where("game_id='%s'",$search)->find();
                if($tag) {
                    $this->ajaxReturn($re['agent_id']);
                }else{
                    $this->ajaxReturn('error');
                }
            }
        }
    }

    public function saveInfo() {
        $data = I('post.');
        $uOb = M('User');
        $result = $uOb->where("agent_id='%s'",$data['agent_id'])->save(array("login_id"=>$data['login_id'],"tea_name"=>$data['tea_name'],"city"=>$data['city'],"notice"=>$data['notice'],"name"=>$data['named'],"tel"=>$data['tel'],'password'=>md5("dyqp0000")));
        if($result) {
            $url=C('API_URL').'/?act=setInviter&uid='.$data['search'].'&username='.$data['named'].'&notice='.$data['notice'].'&city='.$data['city'].'&login_id='.$data['login_id'];
            $curl_res=get_curl($url);
            $this->ajaxReturn('success');
        }else{
            $this->ajaxReturn('error');
        }
    }
}