<?php

$db_user='root';
$db_pwd='123456';
//$db_pwd='baiYY123sql';
//$db_pwd='baisql123';
$db_name='dygm';

if ($_SERVER['HTTP_HOST']=='localhost') {
    $yuming='http://localhost/dayugm/wechat/Home/Login';
}else{
    $yuming='http://192.168.0.188/dayugm/wechat/Home/Login';
}

return array(
    //'配置项'=>'配置值'
    'MODULE_ALLOW_LIST'=>array('Admin','Home'),//允许的分组列表
    'DEFAULT_MODULE'=>'Home', //默认分组
    'DEFAULT_CONTROLLER'=>'Login',//默认控制器
    'DEFAULT_ACTION'=>'login',//默认方法

    'DB_TYPE'=>'mysql',     //数据库类型
    'DB_HOST'=>'localhost',//服务器地址
//    'DB_HOST'=>'rm-wz98fy6fmvo2jsnrezo.mysql.rds.aliyuncs.com',//服务器地址
    'DB_NAME'=>$db_name,       //数据库名称
    'DB_CHARSET'=>'utf8',   //数据库编码方式默认为utf8
    'DB_PORT'=>'3306',      //端口
    'DB_USER'=>$db_user,      //用户名
    'DB_PWD'=>$db_pwd,       //密码
    'DB_PREFIX'=>'by_',   //数据库表前缀

    //权限验证设置
    'AUTH_CONFIG'=>array(
        'AUTH_ON'=>true, //认证开关
        'AUTH_TYPE'=>1, // 认证方式，1为时时认证；2为登录认证。
        'AUTH_GROUP'=>'by_group', //用户组数据表名
        'AUTH_GROUP_ACCESS'=>'by_group_access', //用户组明细表
        'AUTH_RULE'=>'by_rule', //权限规则表
        'AUTH_USER'=>'by_admin'//用户表
    ),

    'LANG_SWITCH_ON' =>  true,   // 开启语言包功能
    'DEFAULT_LANG' =>  'zh-cn', // 默认语言
    'LANG_LIST'=>  'zh-cn',
    'LANG_AUTO_DETECT' =>  false,

    'UPLOADS'=>dirname(dirname(dirname(dirname(__FILE__)))).'/Public/Uploads/',//图片文件上传保存地址
    'YUMING'=>$yuming,//域名
//     'API_URL'=>'http://192.168.0.158:131000',//接口域名,
//    'API_URL'=>'http://119.23.251.242:13100',//接口域名,
    'API_URL'=>'http://39.108.74.251:13100',//接口域名,
//    'API_URL'=>'http://192.168.0.66:13100',//接口域名,
//    'API_URL'=>'http://192.168.0.196:13100',//接口域名,
//    'API_URL'=>'http://192.168.0.196:3000',//接口域名,
//    'API_URL'=>'http://47.74.148.181:13100',//接口域名,
//    'API_URL'=>'http://192.168.0.108:3000',//接口域名,
    'GAME_RESOURCE_KEY' => 'card',

);