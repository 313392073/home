<?php
/*//获取用户id
function getUserId(){
        return session("userid")?session("userid"):1;
}*/
//无限极分类
function oneL($cate,$pid=0,$deli='',$num=0){
		$carr=array();
		$num+=1;
		$deli=$deli;
		$deli1=str_repeat($deli,$num);
		
		foreach ($cate as $v) {
			if ($v['pid']==$pid) {
				$v['deli']=$deli1;
				$carr[]=$v;
				$carr=array_merge($carr,oneL($cate,$v['service_id'],$deli,$num));
			}
		}
	return $carr;
}
//管理员日志
function adminLog($action){
	$data['admin_id']=session("adminid");
	$data['admin_action']=$action;
	$data['log_time']=time();
	$data['log_ip']=get_real_ip();
	$aOb=M('Admin_log');
	$aOb->add($data);
}

function action($admin,$user,$type,$ori,$detail){
    $aOb = M('Action');
    $data = array();
    $data['username'] = $admin;
    $data['gmname'] = $user;
    $data['type'] = $type;
    $data['original'] = $ori;
    $data['detail'] = $detail;
    $data['ip'] = get_client_ip();
    $data['add_time'] = time();
    $aOb->add($data);
}

function get_real_ip(){ 
	$ip=false; 
	if(!empty($_SERVER['HTTP_CLIENT_IP'])){ 
		$ip=$_SERVER['HTTP_CLIENT_IP']; 
	}
	if(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){ 
		$ips=explode (', ', $_SERVER['HTTP_X_FORWARDED_FOR']); 
		if($ip){ array_unshift($ips, $ip); $ip=FALSE; }
		for ($i=0; $i < count($ips); $i++){
			if(!eregi ('^(10│172.16│192.168).', $ips[$i])){
				$ip=$ips[$i];
				break;
			}
		}
	}
	return ($ip ? $ip : $_SERVER['REMOTE_ADDR']); 
}
 
// 首先需要检测b目录是否存在 
function make_dir(){
	$dir=date('Y-m-d');
	if (!is_dir(C('UPLOADS').$dir.'/')){
		mkdir(C('UPLOADS').$dir.'/',0777); 
	}
	return C('UPLOADS').$dir.'/';
}

/** 删除所有空目录 
* @param String $path 目录路径 
*/
function rm_empty_dir($path){ 
  if(is_dir($path) && ($handle = opendir($path))!==false){ 
    while(($file=readdir($handle))!==false){// 遍历文件夹 
      if($file!='.' && $file!='..'){ 
        $curfile = $path.'/'.$file;// 当前目录 
        if(is_dir($curfile)){// 目录 
          rm_empty_dir($curfile);// 如果是目录则继续遍历 
          if(count(scandir($curfile))==2){//目录为空,=2是因为.和..存在
            rmdir($curfile);// 删除空目录 
          } 
        } 
      } 
    } 
    closedir($handle); 
  } 
} 
/**
 * 字符串截取，支持中文和其他编码
 * @static
 * @access public
 * @param string $str 需要转换的字符串
 * @param string $start 开始位置
 * @param string $length 截取长度
 * @param string $charset 编码格式
 * @param string $suffix 截断显示字符
 * @return string
 */
function msubstr($str, $start=0, $length, $charset="utf-8", $suffix=true) {
    if(function_exists("mb_substr"))
        $slice = mb_substr($str, $start, $length, $charset);
    elseif(function_exists('iconv_substr')) {
        $slice = iconv_substr($str,$start,$length,$charset);
        if(false === $slice) {
            $slice = '';
        }
    }else{
        $re['utf-8']   = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
        $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
        $re['gbk']    = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
        $re['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
        preg_match_all($re[$charset], $str, $match);
        $slice = join("",array_slice($match[0], $start, $length));
    }
    return $suffix ? $slice.'***' : $slice;
}

function showPage($re,$pageSize=''){
	$count=count($re);
	$pageSize=$pageSize?$pageSize:10;		
	$pageOb=new \Think\Page($count,$pageSize);
	$pageOb->setConfig('header','<span class="rows">共 %TOTAL_ROW% 条记录&nbsp; %NOW_PAGE%/%TOTAL_PAGE%页</span>');
	$pageOb->setConfig('first','首页');
	$pageOb->setConfig('prev','上一页');
	$pageOb->setConfig('next','下一页');		
	$pageOb->setConfig('last','最后一页');
	$pageOb->setConfig('theme','%HEADER%  %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%');
    if (empty($_GET['p'])) {
       $_GET['p']=1;
    }
	$offset=($_GET['p']-1)*$pageSize;
	$arr=array_slice($re,$offset,$pageSize);
	$str=$pageOb->show();
	if ($str=='') {
		$str='共 0 条记录';
	}
	$data=array('pageStr'=>$str,'arr'=>$arr);
	return $data;
	/*$this->assign('str',$str);
	$this->assign('arr',$arr);
    $this->assign('p',$_GET['p']);*/
}

 function get_curl($url){
 	ini_set('max_execution_time', '100');
	$ch = curl_init();
	//设置选项，包括URL
//	echo $url;
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	//执行并获取HTML文档内容
	$output = curl_exec($ch);
//	echo curl_error($ch);
	//释放curl句柄
	curl_close($ch);
	//打印获得的数据
	return json_decode($output,true);

}

 function post_curl($url,$post_data){
 	$ch=curl_init();
 	curl_setopt($ch, CURLOPT_URL, $url);
 	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
 	curl_setopt($ch, CURLOPT_POST, 1);
 	curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
 	$output=curl_exec($ch);
 	curl_close($ch);
 	return json_decode($output,true);
}

//校验验证码
function check_verify($code, $id = ""){
    $verify = new \Think\Verify();
    return $verify->check($code, $id);
}

function authCheck($rule,$uid,$type=1, $mode='url', $relation='or')
{
    //超级管理员跳过验证
    $auth = new \Think\Auth();
    //获取当前uid所在的角色组id
    $groups = $auth->getGroups($uid);
    session('gid', $groups[0]['group_id']);//角色组id
    session('gname', $groups[0]['title']);//角色组名称

    //设置的是一个用户对应一个角色组,所以直接取值.如果是对应多个角色组的话,需另外处理
    if (in_array($groups[0]['group_id'], C('ADMINISTRATOR'))) {
        return true;
    } else if (in_array($rule, C('NO_AUTH_RULES'))) {
        return true;
    } else {
        return $auth->check($rule, $uid, $type, $mode, $relation) ? true : false;
    }
}

function unicode_encode($str, $encoding = 'GBK', $prefix = '&#', $postfix = ';') {
    $str = iconv($encoding, 'UCS-2', $str);
    $arrstr = str_split($str, 2);
    $unistr = '';
    for($i = 0, $len = count($arrstr); $i < $len; $i++) {
        $dec = hexdec(bin2hex($arrstr[$i]));
        $unistr .= $prefix . $dec . $postfix;
    }
    return $unistr;
}


function exportExcel($expTitle,$expCellName,$expTableData) {
    $xlsTitle = iconv('utf8','gb2312',$expTitle); //文件名称
    $fileName = $expTitle.date('Y-m-d');  //自定义文件名
    $cellNum = count($expCellName);
    $dataNum = count($expTableData);
    vendor("PHPExcel.PHPExcel");
//        import('PHPExcel','','.php');
    $objPHPExcel = new \PHPExcel();
    $cellName = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ');

    $objPHPExcel->getActiveSheet()->getColumnDimension('A1')->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getStyle()->getFont()->setSize(16);
    $objPHPExcel->getActiveSheet()->getStyle()->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet(0)->mergeCells('A1:'.$cellName[$cellNum-1].'1');  //合并单元格
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1',$expTitle.' 导出时间:'.date('Y-m-d H:i:s'));

    for($i=0;$i<$cellNum;$i++){
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($cellName[$i].'2', $expCellName[$i][1]);
    }
    // Miscellaneous glyphs, UTF-8
    for($i=0;$i<$dataNum;$i++){
        for($j=0;$j<$cellNum;$j++){
            $objPHPExcel->getActiveSheet(0)->setCellValue($cellName[$j].($i+3), $expTableData[$i][$expCellName[$j][0]]);
        }
    }

    header('pragma:public');
    header('Content-type:application/vnd.ms-excel;charset=utf-8;name="'.$xlsTitle.'.xls"');
    header("Content-Disposition:attachment;filename=$fileName.xls");//attachment新窗口打印inline本窗口打印

    $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel5');
    $objWriter->save('php://output');
    exit;
}

