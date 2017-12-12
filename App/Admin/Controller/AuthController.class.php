<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/23
 * Time: 18:54
 */
namespace Admin\Controller;
use Think\Controller;
class AuthController extends Controller{
    protected function _initialize(){
        $admin_id = session('admin_id');
        if(!$admin_id) {
//            $this->redirect(U('Admin/Login/index'));
            $this->error('当前用户未登录或登录超时，请重新登录', U('Admin/Login/index'));
        }
        $not_check = array('Admin/Main/main','Admin/Login/login','Admin/Main/home');

        //当前操作的请求                 模块名/方法名
        if(in_array(MODULE_NAME.'/'.CONTROLLER_NAME.'/'.ACTION_NAME, $not_check)){
            return true;
        }

        $auth=new \Think\Auth();
        $Id = session('id');
        if($Id == 1) {
            return true;
        }else{
            if(!$auth->check(MODULE_NAME.'/'.CONTROLLER_NAME.'/'.ACTION_NAME,$Id)){
                $str = getallheaders()['Accept'];
                if(strpos($str,'json') == false) {
                    $this->error('抱歉，你没有权限',U('Admin/Main/home'),0);
                }else {
                    $this->ajaxReturn('noaccess');
                }
            }
        }
    }

}