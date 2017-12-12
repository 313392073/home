<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/30
 * Time: 16:38
 */
namespace Admin\Controller;
use Think\Controller;
class DataController extends AuthController {
    public function getMax($arr){
        $max = $arr[0];
        foreach ($arr as $k => $v) {
            if($arr[$k]>$max){
                $max = $arr[$k];
            }
        }
        return $max;
    }

    public function dataOnlineBuy(){
        if(I('rows')) {
            $bOb = M('Buy');
            $limit = I('rows');
            $page = I('page');
            $offset = ($page-1)*$limit;
            $game_id = I('game_id');
            $order_id = I('order_id');
            $tag = preg_match("/^\d{6}$/",I('game_id'));
            if(!empty(I('order_id'))) {
                $count = $bOb->where('order_id='.$order_id)->count();
                $result = $bOb->where('order_id='.$order_id)->order('add_time desc')->limit($offset,$limit)->select();
            }else if(!empty(I('game_id') && $tag)) {
                $count = $bOb->where('game_id='.$game_id)->count();
                $result = $bOb->where('game_id='.$game_id)->order('add_time desc')->limit($offset,$limit)->select();
            }else if(!empty(I('game_id') && !$tag)){
                $count = $bOb->where("username='%s'",$game_id)->count();
                $result = $bOb->where("username='%s'",$game_id)->order('add_time desc')->limit($offset,$limit)->select();
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

    public function dataOnline(){
        if(I('timed')) {
            $nowTime = strtotime(date(I('timed')));
            $endTime = strtotime(date(I('timed')))+86400;
            $gOb = M('Gamestat');
            $result = $gOb->where("stamp>=".$nowTime.' and stamp <='.$endTime)->select();
            foreach ($result as $k=>$v) {
                $result[$k]['stamp'] = date('Y-m-d H:i:s',$result[$k]['stamp']);
            }
            $arr = [];
            for ($i=1;$i<25;$i++) {
                $arr[$i>9?$i:('0'.$i)] = 0;
            }
            foreach ($result as $k=>$v) {
                $database = '';
                if(substr($result[$k]['stamp'],11,2) == 00) {
                    $database == 24;
                }else{
                    $database = substr($result[$k]['stamp'],11,2);
                }
                if($result[$k]['online'] > $arr[$database]){
                    $arr[substr($result[$k]['stamp'],11,2)] = $result[$k]['online'];
                }
            }
            $this->ajaxReturn(json_encode($arr));

        }else{
            $url=C('API_URL').'/?act=queryOnlineNum';
            $curl_res=get_curl($url);
            $this->assign('total',$curl_res['total']);
            $this->assign('online',$curl_res['online']);
            $this->display();
        }
    }

    public function getPlayers(){
        if(!empty(I('timed'))) {
            $nowTime = strtotime(date(I('timed')));
            $endTime = strtotime(date(I('timed')))+86400;
        }else{
            $nowTime = strtotime(date('Ymd'));
            $endTime = strtotime(date('Ymd'))+86400;
        }
        $gOb = M('Gamestat');
        $result = $gOb->where("stamp>=".$nowTime.' and stamp <='.$endTime)->select();
        foreach ($result as $k=>$v) {
            $result[$k]['stamp'] = date('Y-m-d H:i:s',$result[$k]['stamp']);
        }
        $arr = [];
        for ($i=1;$i<25;$i++) {
            $arr[$i>9?$i:('0'.$i)] = 0;
        }
        foreach ($result as $k=>$v) {
            $database = '';
            if(substr($result[$k]['stamp'],11,2) == 00) {
                $database == 24;
            }else{
                $database = substr($result[$k]['stamp'],11,2);
            }
            if($result[$k]['online'] > $arr[$database]){
                $arr[substr($result[$k]['stamp'],11,2)] = $result[$k]['online'];
            }
        }
        $this->ajaxReturn(json_encode($arr));
    }

    public function dataConsume(){
        if(I('rows')) {
            $limit = I('rows');
            $page = I('page');
            $offset = ($page-1)*$limit;

            $rOb = M('Resourcelog');
            if(I('search') && empty(I('start_time'))) {
                $total = $rOb->where("reason = 'roomFinished' and user_id = ".I('search'))->sum('card_change');
                $count = $rOb->where("reason = 'roomFinished' and user_id = ".I('search'))->count();
                $result = $rOb->where("reason = 'roomFinished' and user_id = ".I('search'))->order('add_time desc')->limit($offset,$limit)->select();
            }else if(I('search') && I('start_time')) {
                $total = $rOb->where("reason = 'roomFinished' and user_id = ".I('search')." and add_time >= ".I('start_time')." and add_time <= " .I('end_time'))->sum('card_change');
                $count = $rOb->where("reason = 'roomFinished' and user_id = ".I('search')." and add_time >= ".I('start_time')." and add_time <= " .I('end_time'))->count();
                $result = $rOb->where("reason = 'roomFinished' and user_id = ".I('search')." and add_time >= ".I('start_time')." and add_time <= ".I('end_time'))->order('add_time desc')->limit($offset,$limit)->select();
            }else if(!I('search') && I('start_time')) {
                $total = $rOb->where("reason = 'roomFinished' and add_time >= ".I('start_time')." and add_time <= " .I('end_time'))->sum('card_change');
                $count = $rOb->where("reason = 'roomFinished' and add_time >= ".I('start_time')." and add_time <= " .I('end_time'))->count();
                $result = $rOb->where("reason = 'roomFinished' and add_time >= ".I('start_time')." and add_time <= ".I('end_time'))->group('user_id')->field('*,SUM(card_change) as total')->order('total')->limit($offset,$limit)->select();
            }else{
                $time = time() - 259200;
                $total = $rOb->where("reason = 'roomFinished' and add_time >=".$time)->sum('card_change'); //总消耗统计
                $count = $rOb->where("reason = 'roomFinished' and add_time >= ".$time)->group('user_id')->count();
                $result = $rOb->where("reason = 'roomFinished' and add_time >= ".$time)->group('user_id')->field('*,SUM(card_change) as total')->order('total')->limit($offset,$limit)->select(); // 每一个人这段时间的消耗
            }

            $arr = array();
            $this->assign('total',$total);
            if($result) {
                $arr['total'] = $count;
                $arr['rows'] = $result;
                $arr['num'] = $total;
            }else{
                $arr['total'] = 0;
                $arr['rows'] = 0;
                $arr['num'] = $total;
            }
            $this->ajaxReturn(json_decode(json_encode($arr)));
        }else{
            $this->display();
        }
    }
    public function dataNowday(){
        if(I('timed')) {
            $nowTime = strtotime(date(I('timed')));
            $endTime = strtotime(date(I('timed')))+86400;
            $bOb = M('Buy');
            $total = $bOb->where("add_time >= ".$nowTime.' and add_time <= '.$endTime.' and status = 1')->field('add_time,number')->sum('number');  //当天总充值
            $result = $bOb->where("add_time >= ".$nowTime.' and add_time <= '.$endTime.' and status = 1')->select();  //充值的明细
            foreach ($result as $k=>$v) {
                $result[$k]['add_time'] = date('Y-m-d H:i:s',$result[$k]['add_time']);
            }
            $arr = [];
            for ($i=1;$i<25;$i++) {
                $arr[$i>9?$i:('0'.$i)] = 0;
            }

            foreach ($result as $k=>$v) {
                $database = '';
                if(substr($result[$k]['add_time'],11,2) == 00) {
                    $database == 24;
                }else{
                    $database = substr($result[$k]['add_time'],11,2);
                }

                $arr[$database] += $result[$k]['number'];
            }
            $brr = array();
            $brr['total'] = $total?$total:0;
            $brr['data'] = $arr;
            $this->ajaxReturn($brr);
        }else{
            $this->display();
        }
    }

    public function dataMonth(){
        if(I('timed')) {
            $bOb = M('Buy');
            $data = substr(I('timed'),0,7);
            $firstTime=date($data.'-01', strtotime(date("Y-m-d"))); //第一天
            $lastTime = date('Y-m-d', strtotime("$firstTime +1 month -1 day")); //最后一天
            $nowTime = strtotime(date($firstTime));
            $endTime = strtotime(date($lastTime))+86400;
            $month = date('t', strtotime(I('timed')));
            $total = $bOb->where("add_time >= ".$nowTime.' and add_time <= '.$endTime.' and status = 1')->field('add_time,number')->sum('number');  //当天总充值
            $result = $bOb->where("add_time >= ".$nowTime.' and add_time <= '.$endTime.' and status = 1')->select();  //充值的明细
            foreach ($result as $k=>$v) {
                $result[$k]['add_time'] = date('Y-m-d H:i:s',$result[$k]['add_time']);
            }
            $arr = [];
            for ($i=1;$i<$month+1;$i++) {
                $arr[$i>9?$i:('0'.$i)] = 0;
            }
            foreach ($result as $k=>$v) {
                if($result[$k]['add_time']) {
                    $database = substr($result[$k]['add_time'],8,2);
                }else{
                    $database = '';
                }
                $arr[$database] += $result[$k]['number'];
            }
            $brr = array();
            $brr['total'] = $total?$total:0;
            $brr['data'] = $arr;
            $this->ajaxReturn($brr);
        }else{
            $this->display();
        }
    }

    public function getMonth(){
        $bOb = M('Buy');
        if(!empty(I('timed'))) {
            $data = substr(I('timed'),0,7);
            $firstTime=date($data.'-01', strtotime(date("Y-m-d"))); //第一天
            $lastTime = date('Y-m-d', strtotime("$firstTime +1 month -1 day")); //最后一天
            $nowTime = strtotime(date($firstTime));
            $endTime = strtotime(date($lastTime))+86400;
            $month = date('t', strtotime(I('timed')));
        }else{
            $nowTime = date('Y-m-01', strtotime(date("Y-m-d")));
            $endTime = date('Y-m-d', strtotime("$nowTime +1 month -1 day"));
            $nowTime = strtotime(date($nowTime));
            $endTime = strtotime(date($endTime))+86400;
            $month = date("t");
        }
        $total = $bOb->where("add_time >= ".$nowTime.' and add_time <= '.$endTime.' and status = 1')->field('add_time,number')->sum('number');  //当天总充值
        $result = $bOb->where("add_time >= ".$nowTime.' and add_time <= '.$endTime.' and status = 1')->select();  //充值的明细
        foreach ($result as $k=>$v) {
            $result[$k]['add_time'] = date('Y-m-d H:i:s',$result[$k]['add_time']);
        }
        $arr = [];
        for ($i=1;$i<$month+1;$i++) {
            $arr[$i>9?$i:('0'.$i)] = 0;
        }
        foreach ($result as $k=>$v) {
            if($result[$k]['add_time']) {
                $database = substr($result[$k]['add_time'],8,2);
            }else{
                $database = '';
            }
            $arr[$database] += $result[$k]['number'];
        }
        $brr = array();
        $brr['total'] = $total?$total:0;
        $brr['data'] = $arr;
        $this->ajaxReturn($brr);
    }

    public function dataUserstat(){
        if(I('rows')) {
            $limit = I('rows');
            $page = I('page');
            $offset = ($page-1)*$limit;
            $sOb = M('Statremain');
            $startTime = date("Y-m-d G:i:s",I('start_time'));
            $endTime = date("Y-m-d G:i:s",I('end_time'));
            $count = $sOb->where("stat_time>'".$startTime."' and stat_time< '".$endTime."'")->count();
            $result = $sOb->where("stat_time>'".$startTime."' and stat_time< '".$endTime."'")->limit($offset,$limit)->order('stat_time desc')->select();
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

    public function getUserStat(){

    }

    public function userStatExport(){
        $startTime = date("Y-m-d G:i:s",I('start_time'));
        $endTime = date("Y-m-d G:i:s",I('end_time'));
        $xlsName = '实时留存表';
        $xlsCell = array(
            array('stat_time','日期'),
            array('dru','新注册用户'),
            array('r1','次日留存'),
            array('r2','次日留存'),
            array('r3','三日留存'),
            array('r7','七日留存'),
            array('r15','十五日留存'),
            array('r30','三十日留存'),
        );
        $sOb = M('Statremain');
        $result = $sOb->where("stat_time>'".$startTime."' and stat_time< '".$endTime."'")->order('stat_time desc')->select();
        foreach ($result as $k=>$v) {
            if($result[$k]['dru'] == 0) {
                $result[$k]['r1'] = 0;
                $result[$k]['r2'] = 0;
                $result[$k]['r3'] = 0;
                $result[$k]['r7'] = 0;
                $result[$k]['r15'] = 0;
                $result[$k]['r30'] = 0;
            }
            if($result[$k]['r1'] == 0) {
                $result[$k]['r1'] = 0;
            }else{
                $result[$k]['r1'] = round($result[$k]['r1']/$result[$k]['dru'],2).'*100'.'%';
            }
            if($result[$k]['r2'] == 0) {
                $result[$k]['r2'] = 0;
            }else{
                $result[$k]['r2'] = round($result[$k]['r2']/$result[$k]['dru'],2).'*100'.'%';
            }
            if($result[$k]['r3'] == 0) {
                $result[$k]['r3'] = 0;
            }else{
                $result[$k]['r3'] = round($result[$k]['r3']/$result[$k]['dru'],2).'*100'.'%';
            }
            if($result[$k]['r7'] == 0) {
                $result[$k]['r7'] = 0;
            }else{
                $result[$k]['r7'] = round($result[$k]['r7']/$result[$k]['dru'],2).'*100'.'%';
            }
            if($result[$k]['r15'] == 0) {
                $result[$k]['r15'] = 0;
            }else{
                $result[$k]['r15'] = round($result[$k]['r15']/$result[$k]['dru'],2).'*100'.'%';
            }
            if($result[$k]['r30'] == 0) {
                $result[$k]['r30'] = 0;
            }else{
                $result[$k]['r30'] = round($result[$k]['r30']/$result[$k]['dru'],2).'*100'.'%';
            }
        }
        action(session('admin_id'),0,'userStatExport',null,null);
        exportExcel($xlsName,$xlsCell,$result);
    }
}