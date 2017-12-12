<?php
namespace Admin\Controller;
use Think\Controller;
class IndexController extends AuthController {
    public function index(){
       $this->display();
    }
    public function agentList() {
        $this->display();
    }
    public function agentDetail() {
        $agent_id = I('agent_id');
        $uOb = M('User');
        $result = $uOb->where("agent_id='%s'","$agent_id")->find();
        $this->assign('tea_name',$result['tea_name']);
        $this->assign('agent_id',$result['agent_id']);
        $this->assign('login_id',$result['login_id']);
        $this->assign('game_id',$result['game_id']);
        $this->assign('named',$result['name']);
        $this->assign('tel',$result['tel']);
        $this->assign('sub_num',$result['sub_player']);
        $this->assign('total_rebate',($result['total_reagent']-$result['total_replayer']));
        $this->assign('wait_withdraw',($result['total_reagent']-$result['total_replayer'] - $result['total_withdraw']));
        $this->assign('create_time',$result['create_time']);
        $this->display();
    }
    public function agentSearch() {
        $this->display();
    }
    public function agentUpreview() {
        $this->display();
    }
    public function agentWithdraw() {
        $this->display();
    }
    public function agentWithdrawList() {
        $this->display();
    }
    public function agentRebate() {
        $rOb = M('Ratio');
        $result = $rOb->where('id=1')->find();
        $this->assign('rebate1',$result['rebate1']);
        $this->assign('rebate2',$result['rebate2']);
        $this->assign('rebate3',$result['rebate3']);
        $this->display();
    }
    public function playerHost() {
        $this->display();
    }
    public function gameNotice() {
        $this->display();
    }
    public function gameInit() {
        $this->display();
    }
    public function gameRoom() {
        $this->display();
    }
    public function gameEmail() {
        $this->display();
    }
    public function gameFeedback() {
        $this->display();
    }
    public function gameMatch() {
        $this->display();
    }
    public function playerSearch() {
        $this->display();
    }
    public function playerLog() {
        $this->display();
    }
    public function playerMatch() {
        $this->display();
    }
    public function playerModify() {
        $this->display();
    }
    public function dataTotal() {
        $this->display();
    }
    public function dataOnline() {
        $this->display();
    }
    public function dataExpend() {
        $this->display();
    }
}