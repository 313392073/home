<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/22
 * Time: 19:06
 */
namespace Admin\Controller;
use Think\Auth;
use Think\Controller;
class MangerController extends AuthController {
    public function mangerList() {
        $this->display();
    }
    public function getMangerList(){
        $gOb = M('Group');
        $limit = I('rows');
        $page = I('page');
        $offset = ($page-1)*$limit;
        $count = $gOb->field('*')->count();
        $result = $gOb->order('id desc')->limit($offset,$limit)->select();
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

    public function addGroup(){
        $title = I('title');
        $describe = I('describe');
        $status = I('status');
        $gOb = M('Group');
        $result = $gOb->add(array('title'=>$title,'describe'=>$describe,'status'=>$status));
        if($result) {
            $this->ajaxReturn('success');
        }else{
            $this->ajaxReturn('error');
        }
    }

    public function delGroup(){
        $gOb = M('Group');
        $id = $_GET['id'];
        if($id == 1) {
            $this->ajaxReturn('noaccess');exit;
        }
        $result = $gOb->where("id='%s'",$id)->delete();
        if($result) {
            $this->ajaxReturn('success');
        }else{
            $this->ajaxReturn('error');
        }
    }

    public function editGroup(){
        $title = I('title');
        $describe = I('describe');
        $status = I('status');
        $ID = I('id');
        $gOb = M('Group');
        $result = $gOb->where("id='%s'",$ID)->save(array('title'=>$title,'describe'=>$describe,'status'=>$status));
        if($result) {
            $this->ajaxReturn('success');
        }else{
            $this->ajaxReturn('error');
        }
    }

    public function getTree(){
        $id  = I('id');  //父级的id
        $gid = I('gid'); //角色组id
        $gOb = M('Group');
        $rOb = M('Rule');
        $map['pid'] = $id?$id:0;
        $rules = $gOb->where("id='%s'",$gid)->getField('rules'); //角色组rules
        $data = $rOb->where($map)->field('id,title as text,pid as parentId,state')->select();
        if(!empty($rules)) {
            $arr = explode(',',$rules);
            foreach ($data as $k=>$v) {
                if(in_array($v['id'],$arr)) {
                    $data[$k]['checked'] = true;
                }
            }
        }
        $this->ajaxReturn(json_decode(json_encode($data)));
    }

    public function test(){
        $this->display();
    }

    public function setAccess(){
        $id = I('id');
        $rules = I('rules');
        $gOb = M('Group');
        if($id == 1) {
            $this->ajaxReturn('noaccess');exit;
        }
        $result = $gOb->where("id='%s'",$id)->save(array('rules'=>$rules));
        if($result) {
            $this->ajaxReturn('success');
        }else{
            $this->ajaxReturn('error');
        }
    }

    public function mangerUser(){
        $gOb = M('Group');
        $gre = $gOb->field('id,title')->select();
        $this->assign('roles',$gre);
        if(I('rows')) {
            $limit = I('rows');
            $page = I('page');
            $offset = ($page-1)*$limit;
            $aOb = M('Admin');
            $count = $aOb->field('id')->count();
            $result = $aOb->field('id,admin_id,role,name,tel,login_time')->order('login_time desc')->limit($offset,$limit)->select();
            foreach ($result as $k=>$v) {
                $result[$k]['title'] = '';
                foreach ($gre as $i=>$s) {
                    if($result[$k]['role'] == $gre[$i]['id']) {
                        $result[$k]['title'] = $gre[$i]['title'];
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

    public function addAdmin(){
        $adminId = I('admin_id');
        $adminName = I('admin_name');
        $adminRole = I('admin_role');
        $aOb = M('Admin');
        $gOb = M('Group_access');
        $result = $aOb->where("admin_id='%s'",$adminId)->find();
        if($result) {
            $this->ajaxReturn('hasAdmin');
        }else{
            $record = $aOb->add(array('admin_id'=>$adminId,'password'=>md5('dyqp0000'),'role'=>$adminRole,'name'=>$adminName));
            $resl = $aOb->where("admin_id='%s'",I('admin_id'))->find();
            $res = $gOb->add(array('uid'=>$resl['id'],'group_id'=>I('admin_role')));
            if($res) {
                $this->ajaxReturn('success');
            }else{
                $this->ajaxReturn('error');
            }
        }
    }

    public function editAdmin(){
        $Id = I('Id');
        $adminName = I('admin_name');
        $adminRole = I('admin_role');
        $aOb = M('Admin');
        $result = $aOb->where("id='%s'",$Id)->save(array('role'=>$adminRole,'name'=>$adminName));
        if($result) {
            $this->ajaxReturn('success');
        }else{
            $this->ajaxReturn('error');
        }
    }

    public function delAdmin(){
        $aOb = M('Admin');
        $id = $_GET['id'];
        if($id == 1) {
            $this->ajaxReturn('noaccess');exit;
        }
        $result = $aOb->where("id='%s'",$id)->delete();
        if($result) {
            $this->ajaxReturn('success');
        }else{
            $this->ajaxReturn('error');
        }
    }

    public function mangerLoginlog(){
        $this->display();
    }
    public function getLoginlog(){
        $limit = I('rows');
        $page = I('page');
        $offset = ($page-1)*$limit;
        $aOb = M('Action');
        $result = $aOb->where("type='login'")->order('add_time desc')->limit($offset,$limit)->select();;
        $count = $aOb->where("type='login'")->count();
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
    public function mangerRule(){
        $rOb = M('Rule');
        $result = $rOb->where("pid = 0")->field('id,title')->select();
        $this->assign('moduleName',$result);
        $this->display();
    }

    public function getRule(){
        $limit = I('rows');
        $page = I('page');
        $offset = ($page-1)*$limit;
        $rOb = M('Rule');
        $result = $rOb->where('pid != 0')->field('id,name,title,status,pid')->limit($offset,$limit)->select();
        $res = $rOb->field('id,title')->select();
        foreach ($result as $k=>$v) {
            $result[$k]['moduleName'] = '';
            foreach ($res as $i=>$n) {
                if($result[$k]['pid'] == $res[$i]['id']) {
                    $result[$k]['moduleName'] = $res[$i]['title'];
                }
            }
        }
        $count = $rOb->where('pid != 0')->field('pid')->count();
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

    public function editRule(){
        $Id = I('Id');
        $named = I('named');
        $title = I('title');
        $pid = I('pid');
        $status = I('status');
        $rOb = M('Rule');
        $result = $rOb->where("id='%s'",$Id)->save(array('name'=>$named,'title'=>$title,'pid'=>$pid,'status'=>$status));
        if($result) {
            $this->ajaxReturn('success');
        }else{
            $this->ajaxReturn('error');
        }
    }

    public function addRule(){
        $named = I('named');
        $title = I('title');
        $pid = I('pid');
        $status = I('status');
        $rOb = M('Rule');
        $record = $rOb->where("name='%s'",$named)->find();
        if($record) {
            $this->ajaxReturn('hasRule');
        }else{
            $result = $rOb->add(array('name'=>$named,'title'=>$title,'pid'=>$pid,'status'=>$status));
            if($result) {
                $this->ajaxReturn('success');
            }else{
                $this->ajaxReturn('error');
            }
        }
    }

    public function delRule(){
        $Id = $_GET['id'];
        $rOb = M('Rule');
        $result = $rOb->where("id='%s'",$Id)->delete();
        if($result) {
            $this->ajaxReturn('success');
        }else{
            $this->ajaxReturn('error');
        }
    }
}