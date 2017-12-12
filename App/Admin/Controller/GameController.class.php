<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/17
 * Time: 10:23
 */
namespace Admin\Controller;
use Think\Controller;
class GameController extends AuthController{
    public function gameFeedback(){
        if(I('rows')) {
            $limit = I('rows');
            $page = I('page');
            $offset = ($page-1)*$limit;
            $fOb = M('Feedback');
            $count = $fOb->count();
            $result = $fOb->order('add_time desc')->limit($offset,$limit)->select();
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

    public function gameFeedbackDeatil(){
        $id = I('id');
        $fOb = M('Feedback');
        $result = $fOb->where("id='%s'",$id)->find();
        $this->assign('arr',$result);
        $this->display();
    }

    public function saveFeedback(){
        $id = I('ids');
        $fOb = M('Feedback');
        $result = $fOb->where("id='%s'",$id)->save(array('status'=>1));
        if($result) {
            $record = $fOb->where("id='%s'",$id)->find();
            action(session('admin_id'),$record['user_id'],'saveFeedback',null,null);
            $this->ajaxReturn('success');
        }else{
            $this->ajaxReturn('error');
        }
    }
    public function gameContent(){
        $url=C('API_URL').'/?act=getContent';
        $curl_res=get_curl($url);
        $this->assign('gmContent',$curl_res['notice']);
        $this->display();
    }
    public function setGmcontent(){
        $gmContent = I('gmContent');
        $url=C('API_URL').'/?act=setContent&name=notice&content='.urlencode($gmContent);
        $curl_res=get_curl($url);
        action(session('admin_id'),0,'setGmcontent',null,null);
        $this->ajaxReturn('success');
    }

    public function gameInitial(){
        $url=C('API_URL').'/?act=getInitResource';
        $curl_res=get_curl($url);
        $this->assign('card',$curl_res['card']);
        $this->assign('coin',$curl_res['coin']);
        $this->display();
    }
    public function setGmInitial(){
        $card = I('card');
        $coin = I('coin');
        $url=C('API_URL').'/?act=setInitResource&coin='.$coin.'&card='.$card.'&cash=0';
        $curl_res=get_curl($url);
        action(session('admin_id'),0,'setGmInitial',null,null);
        $this->ajaxReturn('success');
    }
    public function gameRoom(){
        $url=C('API_URL').'/?act=getActivityCreateRoom';
        $curl_res=get_curl($url);
        $this->assign('startTime',$curl_res['startTime']);
        $this->assign('endTime',$curl_res['endTime']);
        $this->assign('rate',$curl_res['rate']);
        $this->display();
    }
    public function setGameRoom(){
        $start_time = I('start_time');
        $end_time = I('end_time');
        $rate = I('rate');
        $url=C('API_URL').'/?act=setActivityCreateRoom&startTime='.$start_time.'&endTime='.$end_time.'&rate='.$rate;
        $curl_res=get_curl($url);
        action(session('admin_id'),0,'setGameRoom',null,null);
        $this->ajaxReturn('success');
    }

    public function gameRecord(){
        if(I('rows')) {
            $limit = I('rows');
            $page = I('page');
            $offset = ($page-1)*$limit;
            $gOb = M('Cardroomlog');

            if(I('search')) {
                $count = $gOb->where("room_id='%s'",I('search'))->count();
                $result = $gOb->where("room_id='%s'",I('search'))->order('add_time desc')->limit($offset,$limit)->select();
            }else{
                $time = time() - 289200;
                $count = $gOb->where("add_time>".$time)->count();
                $result = $gOb->where('add_time >= '.$time)->limit($offset,$limit)->order('add_time desc')->select();
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
    public function getGameDetail(){
        $gOb = M('Cardroomlog');
        $Id = I('Id');
        $result = $gOb->where("id='%s'",$Id)->find();
        $this->assign('data',$result);
        $arr = json_decode($result['info'],true);
        $this->assign('arr',$arr);
        $this->assign('player',$arr['players']);
        $this->display();
    }

    public function gameExchange(){
        if(!empty(I('rows'))) {
            $limit = I('rows');
            $page = I('page');
            $offset = ($page-1)*$limit;
            $eOb = M('Exchange');
            $count = $eOb->where("status=0")->count();
            $result = $eOb->where("status=0")->order('add_time desc')->limit($offset,$limit)->select();
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
    public function modExchange(){
        $eOb = M('Exchange');
        $Id = $_GET['Id'];
        $result = $eOb->where("id='%s'",$Id)->save(array('status'=>1,'mod_time'=>time()));
        if($result) {
            $record = $eOb->where("id='%s'",$Id)->find();
            action(session('admin_id'),$record['user_id'],'modExchange',null,null);
            $this->ajaxReturn('success');
        }else{
            $this->ajaxReturn('error');
        }
    }

    public function gameExchangeList(){
        if(I('rows')) {
            $limit = I('rows');
            $page = I('page');
            $offset = ($page-1)*$limit;
            $tag = preg_match("/^\d{6}$/",I('search'));
            $eOb = M('Exchange');
            if(!empty(I('search'))  && $tag) {
                $count = $eOb->where("status=1 and user_id=".I('search'))->count();
                $result = $eOb->where("status=1 and user_id=".I('search'))->limit($offset,$limit)->order('add_time desc')->select();
            }else if(!empty(I('search')) && !$tag) {
                $count = $eOb->where("status=1 and user_name='%s'",I('search'))->count();
                $result = $eOb->where("status=1 and user_name='%s'",I('search'))->limit($offset,$limit)->order('add_time desc')->select();
            }else{
                $count = $eOb->where("status=1")->count();
                $result = $eOb->where("status=1")->limit($offset,$limit)->order('add_time desc')->select();
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

    public function gameEmail(){
        $this->display();
    }
    public function sendEmail(){
        if(!empty(I('uid'))) {
            if(!empty(I('attach'))) {
                $attach =array('resource'=>array('card'=>I('attach')));
                $url=C('API_URL').'/?act=userAddMail&uid='.I('uid').'&title='.I('title').'&content='.I('playerContent').'&attach='.json_encode($attach);
                $curl_res=get_curl($url);
                if($curl_res['code']) {
                    $this->ajaxReturn('error');
                }else{
                    action(session('admin_id'),I('uid'),'sendEmail',null,null);
                    $this->ajaxReturn('success');
                }
            }else{
                $url=C('API_URL').'/?act=userAddMail&uid='.I('uid').'& title='.I('title').'&content='.I('playerContent');
                $curl_res=get_curl($url);
                if($curl_res['code']) {
                    $this->ajaxReturn('error');
                }else{
                    action(session('admin_id'),I('uid'),'sendEmail',null,null);
                    $this->ajaxReturn('success');
                }
            }

        }else{
            if(!empty(I('attach'))) {
                $attach =array('resource'=>array('card'=>I('attach')));
                $url=C('API_URL').'/?act=addMailToOnline&content='.I('groupContent').'&title='.I('title').'&attach='.json_encode($attach);
                $curl_res=get_curl($url);
                if($curl_res['code']) {
                    $this->ajaxReturn('error');
                }else{
                    action(session('admin_id'),0,'sendEmail',null,null);
                    $this->ajaxReturn('success');
                }
            }else{
                $url=C('API_URL').'/?act=addMailToOnline&content='.I('groupContent').'&title='.I('title');
                $curl_res=get_curl($url);
                if($curl_res['code']) {
                    $this->ajaxReturn('error');
                }else{
                    action(session('admin_id'),0,'sendEmail',null,null);
                    $this->ajaxReturn('success');
                }
            }
        }
    }
}