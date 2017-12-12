<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/1
 * Time: 18:35
 */
namespace Home\Controller;
use Think\Controller;
class LookController extends Controller {
    public function userAgree() {
        $this->display();
    }
    public function look() {
        $this->display();
    }
    public function findPwd() {
        $this->display();
    }
    public function index(){
        $this->display();
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
        $login_id = I('login_id');
        $uOb = M('User');
        $result = $uOb->where("login_id='%s'","$login_id")->find();
        $ensure = I("ensure");
        $pwd = I('pwd');
        $code = $_COOKIE['code'];
        setcookie('code','',time()-120);
        if("'$ensure'" != "$code") {
            $this->ajaxReturn('notCode');
        }else{
            if(md5($pwd) == $result['password']) {
                $this->ajaxReturn('repeat');
            }else{
                $re = $uOb->where("login_id='%s'","$login_id")->save(array('password' => md5("$pwd")));
                if($re) {
                    $this->ajaxReturn('success');
                }else{
                    $this->ajaxReturn('error');
                }
            }
        }
    }
    public function getTel(){
        $login_id = I('login_id');
        $uOb = M('User');
        $result = $uOb->where("login_id='%s'",$login_id)->field("tel")->find();
        if($result) {
            $this->ajaxReturn($result['tel']);
        }else{
            $this->ajaxReturn('error');
        }
    }
}