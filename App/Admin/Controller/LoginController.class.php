<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/13
 * Time: 15:03
 */
namespace Admin\Controller;
use Think\Controller;
class LoginController extends Controller {
    public function index() {
        if(!empty($_SESSION['admin_id'])) {
            $this->redirect(U('Admin/Main/main'));
        }
        $this->display();
    }
    public function verify_c(){
        ob_end_clean();
        $Verify = new \Think\Verify();
        $Verify->fontSize = 18;
        $Verify->length = 5;
        $Verify->codeSet = '0123456789';
        $Verify->imageW = 180;
        $Verify->imageH = 50;
        $Verify->expire = 600;
        $Verify->entry();
    }
    public function checkLogin(){
        $username = I('username');
        $pwd = md5(urldecode(I('pwd')));
        $code = I('code');
        $aOb = M('Admin');
        $result = $aOb->where("admin_id='%s'",$username)->find();
        if($result) {
            if($result['password'] != $pwd) {
                $this->ajaxReturn('eropwd');
            }
            if(!check_verify($code)){
                $this->ajaxReturn('erocode');
            }
            session('id',$result['id']);
            session('admin_id',$result['admin_id']);
            session('role',$result['role']);
            session('name',$result['name']);
            $aOb->where("admin_id='%s'",$username)->save(array('login_time'=>time()));
            session('login_time',time());

            action($username,'0','login',null,null);
            $this->ajaxReturn('success');
        }else{
            $this->ajaxReturn('eroid');
        }
    }
    public function logout(){
        unset($_SESSION['id']);
        unset($_SESSION['admin_id']);
        unset($_SESSION['role']);
        unset($_SESSION['name']);
        header("location:".U('Admin/Login/index'));
    }
}