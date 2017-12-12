<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/13
 * Time: 19:24
 */
namespace Admin\Controller;
use Think\Controller;
class MainController extends AuthController {
    public function index(){
        $uOb = M('Admin');
        $this->assign('username',session('admin_id'));
        $this->assign('logintime',session('login_time'));
        $result = $uOb->where("admin_id='%s'",session('admin_id'))->find();
        $this->assign('ID',$result['id']);

        $id = session('id');
        $rOb = M('Rule');
        $result = $rOb->field('id,title,name,pid,condition')->select();
        $arr = array();
        foreach ($result as $k=>$v) {
            if($result[$k]['pid'] == 0) {
                array_push($arr,$result[$k]);
            }
        }
        foreach ($arr as $k=>$v) {
            $arr[$k]['sub'] = [];
        }
        $uOb = M('Admin');
        $role = $uOb->where("id='%s'",$id)->find();
        //当前的id所在的权限组
        $gOp = M('Group');
        $ges = $gOp->where("id='%s'",$role['role'])->find();
        $this->assign('role',$ges['title']);
        $rules = $ges['rules'];
        $rerules = explode(',',$rules);
        //根据权限组找到有权限的列表
        foreach ($result as $k=>$v) {
            if($id == 1) {
                if($result[$k]['pid'] == $arr[0]['id']) {
                    array_push($arr[0]['sub'],$result[$k]);
                }
                if($result[$k]['pid'] == $arr[1]['id']) {
                    array_push($arr[1]['sub'],$result[$k]);
                }
                if($result[$k]['pid'] == $arr[2]['id']) {
                    array_push($arr[2]['sub'],$result[$k]);
                }
                if($result[$k]['pid'] == $arr[3]['id']) {
                    array_push($arr[3]['sub'],$result[$k]);
                }
                if($result[$k]['pid'] == $arr[4]['id']) {
                    array_push($arr[4]['sub'],$result[$k]);
                }
            }else{
                foreach ($rerules as $s=>$n) {
                    if($result[$k]['id'] == $rerules[$s]) {
                        if($result[$k]['pid'] == $arr[0]['id']) {
                            array_push($arr[0]['sub'],$result[$k]);
                        }
                        if($result[$k]['pid'] == $arr[1]['id']) {
                            array_push($arr[1]['sub'],$result[$k]);
                        }
                        if($result[$k]['pid'] == $arr[2]['id']) {
                            array_push($arr[2]['sub'],$result[$k]);
                        }
                        if($result[$k]['pid'] == $arr[3]['id']) {
                            array_push($arr[3]['sub'],$result[$k]);
                        }
                    }
                }
            }
        }
        $this->assign('auth',$arr);
        $this->display();
    }


    public function main(){
        $aOb = M('Admin');
        $gOb = M('Group');
        $this->assign('username',session('admin_id'));
        $res = $gOb->where("id='%s'",session('role'))->find();
        $this->assign('role',$res['title']);
        $this->assign('logintime',session('login_time'));
        $result = $aOb->where("admin_id='%s'",session('username'))->find();
        $this->assign('ID',$result['id']);
        $this->display();
    }
    public function test(){
        $this->display();
    }
    public function modInfo(){
        $ID = I('ID');
        $username = I('username');
        $pwd = I('pwd');
        $aOb = M('Admin');
        $result = $aOb->where("id='%s'",$ID)->save(array('admin_id'=>$username,'password'=>md5($pwd)));
        if($result) {
            $this->ajaxReturn('success');
        }else{
            $this->ajaxReturn('error');
        }
    }

    public function home(){
        $this->display();
    }
}