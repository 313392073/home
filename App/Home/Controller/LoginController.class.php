<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/20
 * Time: 15:39
 */
namespace Home\Controller;
use Think\Controller;
class LoginController extends BaseController {
    public function _initialize() {
        layout(false);
    }
    public function login() {
        $this->display();
    }

    public function loginCheck(){
        $login_id = I('user_id');
        $pwd = md5(I('pwd'));
        $ensure = I('ensure');

        $uOb = M('User');
        $result = $uOb->where("login_id='%s'",$login_id)->find();
        if($result) {
            if($result['password'] != $pwd) {
                $this->ajaxReturn('eropwd');
            }
            if(!check_verify($ensure)){
                $this->ajaxReturn('erocode');
            }
            if($result['state'] != 0) {
                $this->ajaxReturn('noperm');
            }
            session('user_id', $result['login_id']);
            session('game_id', $result['game_id']);
            session('agent_id', $result['agent_id']);
            $this->ajaxReturn('success');
        }else{
            $this->ajaxReturn('eroid');
        }
    }

    public function setSceneid() {
        $agent_id = session('agent_id');
        return $agent_id;
    }

    public function verify_c(){
        $Verify = new \Think\Verify();
        $Verify->fontSize = 18;
        $Verify->length = 5;
        $Verify->useNoise = false;
        $Verify->useImgBg = false;
        $Verify->codeSet = '0123456789';
        $Verify->imageW = 180;
        $Verify->imageH = 50;
        $Verify->expire = 600;
        $Verify->entry();
    }
	public function ver() {
		echo PHP_VERSION;
	}
    // 检查验证码
//    $verify = I('param.verify','');
//    if(!check_verify($verify)){
//    $this->error("亲，验证码输错了哦！",$this->site_url,9);
//    }
    public function out() {
        session('game_id',null);
        session('user_id',null);
        header("location:".U('Home/Login/login'));
    }

}