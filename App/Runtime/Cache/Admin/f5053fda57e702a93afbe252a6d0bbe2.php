<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en" class="panel-fit">
<head>
    <meta charset="UTF-8">
    <title>百亿云商务平台后台首页</title>
    <link rel="stylesheet" type="text/css" href="/wechat/Public/Common/easyui/themes/default/easyui.css">
    <link rel="stylesheet" type="text/css" href="/wechat/Public/Common/easyui/css/admin.css">
    <link rel="stylesheet" type="text/css" href="/wechat/Public/Common/easyui/themes/icon.css">
    <link rel="stylesheet" type="text/css" href="/wechat/Public/Common/Font/css/font-awesome.min.css">
    <script type="text/javascript" src="/wechat/Public/Common/easyui/js/jquery.min.js"></script>
    <script type="text/javascript" src="/wechat/Public/Common/easyui/js/jquery.easyui.min.js"></script>
    <script type="text/javascript" src="/wechat/Public/Common/easyui/js/easyloader.js"></script>
    <script type="text/javascript" src="/wechat/Public/Common/easyui/js/easyui-lang-zh_CN.js"></script>
    <script type="text/javascript" src="/wechat/Public/Common/easyui/js/common.js"></script>
    <script type="text/javascript" src="/wechat/Public/Common/easyui/js/extends.js"></script>
    <style type="text/css">
        .fa{
            width: 20px;
            text-align: center;
        }
    </style>
</head>
<body class="panel-noscroll">
    <div class="easyui-layout" style="width: 100%;height: 100%;">
        <div data-options="region:'north'" border="false">
            <div class="topLine">
                <p class="fl"><span class="icon icon-users">&nbsp;</span>登录账号：<?php echo ($username); ?>&nbsp;&nbsp;&nbsp;<span class="icon icon-role">&nbsp;</span>角色：<?php echo ($role); ?>&nbsp;&nbsp;&nbsp;<span class="icon icon-clock">&nbsp;</span>登陆时间：<?php echo (date('Y-m-d H:i:s',$logintime)); ?></p>
                <p class="fr">
                    <span class="icon icon-arrow_refresh">&nbsp;</span><a  title="更新缓存" href="javascript:void(0)" onclick="clearCache()">更新缓存</a>
                    <span class="icon icon-set">&nbsp;</span><a title="账号设置" href="#" onclick="setClick()">设置</a>
                    <span class="icon icon-door_out">&nbsp;</span><a title="退出" href="<?php echo U('Admin/Login/logout');?>">退出</a>
                </p>
            </div>
        </div>
        <div data-options="region:'south',split:true" style="height:50px;"></div>
        <div data-options="region:'west',split:true" title="导航菜单" style="width:200px;">
            <div class="easyui-accordion">
                <div title="代理模块" data-options="iconCls:'icon icon-user_edit',selected:true" style="padding:10px;">
                    <ul class="navlist">
                        <li>
                            <a href="javascript:void(0)" onclick="addTab('馆主列表','<?php echo U('Admin/User/userList');?>')"><span style="color: #1375e5;font-size: 14px;" class="fa fa-users" aria-hidden="true">&nbsp;</span><span class="nav">馆主列表</span></a>
                        </li>
                        <li>
                            <a href="javascript:void(0)" onclick="addTab('升阶审核','<?php echo U('Admin/User/userHoist');?>')"><span style="color: #ea9a18;font-size: 14px" class="fa fa-hand-o-up" aria-hidden="true">&nbsp;</span><span class="nav">升阶审核</span></a>
                        </li>
                        <li>
                            <a href="javascript:void(0)" onclick="addTab('返利设置','<?php echo U('Admin/User/userSetrebate');?>')"><span style="font-size: 14px;color: #fd1418" class="fa fa-cc">&nbsp;</span><span class="nav">返利设置</span></a>
                        </li>
                        <li>
                            <a href="javascript:void(0)" onclick="addTab('初始配额','<?php echo U('Admin/User/userSetinit');?>')"><span style="font-size: 14px;color: #e3d400" class="fa fa-calendar-o">&nbsp;</span><span class="nav">初始配额</span></a>
                        </li>
                        <li>
                            <a href="javascript:void(0)" onclick="addTab('提现审核','<?php echo U('Admin/User/userWithdraw');?>')"><span style="font-size: 14px;color: #ee7800" class="fa fa-deaf">&nbsp;</span><span class="nav">提现审核</span></a>
                        </li>
                        <li>
                            <a href="javascript:void(0)" onclick="addTab('提现列表','<?php echo U('Admin/User/userWithdrawList');?>')"><span style="font-size: 14px;color: #5c5cd8" class="fa fa-list-alt">&nbsp;</span><span class="nav">提现列表</span></a>
                        </li>
                        <li>
                            <a href="javascript:void(0)" onclick="addTab('后台购买','<?php echo U('Admin/User/userBuyList');?>')"><span style="color: #bb63e3;font-size: 14px;" class="fa fa-paypal">&nbsp;</span><span class="nav">后台购买</span></a>
                        </li>
                        <li>
                            <a href="javascript:void(0)" onclick="addTab('馆主返利','<?php echo U('Admin/User/userRebate');?>')"><span style="color: #e9791a;font-size: 14px;" class="fa fa-random">&nbsp;</span><span class="nav">馆主返利</span></a>
                        </li>
                        <li>
                            <a href="javascript:void(0)" onclick="addTab('玩家返利','<?php echo U('Admin/User/userRplayer');?>')"><span style="color: #56b2b4;font-size: 14px;" class="fa fa-money">&nbsp;</span><span class="nav">玩家返利</span></a>
                        </li>
                        <li>
                            <a href="javascript:void(0)" onclick="addTab('返利统计','<?php echo U('Admin/User/userRebatetotal');?>')"><span style="color: #e60033;font-size: 16px;" class="fa fa-jpy">&nbsp;</span><span class="nav">返利统计</span></a>
                        </li>
                    </ul>
                </div>
                <div title="游戏模块" data-options="iconCls:'icon-award_star_gold_1'" style="padding:10px;">
                    <ul class="navlist">
                        <li>
                            <a href="javascript:void(0)" onclick="addTab('公告设置','<?php echo U('Admin/Game/gameContent');?>')"><span style="color: #e40403;font-size: 14px;" class="fa fa-bullhorn">&nbsp;</span><span class="nav">公告设置</span></a>
                        </li>
                        <li>
                            <a href="javascript:void(0)" onclick="addTab('初始设置','<?php echo U('Admin/Game/gameInitial');?>')"><span style="color: #5ebada;font-size: 14px;" class="fa fa-hourglass-start">&nbsp;</span><span class="nav">初始设置</span></a>
                        </li>
                        <li>
                            <a href="javascript:void(0)" onclick="addTab('房间消耗','<?php echo U('Admin/Game/gameRoom');?>')"><span style="color: #f0ad4e;font-size: 14px;" class="fa fa-crosshairs">&nbsp;</span><span class="nav">房间消耗</span></a>
                        </li>
                        <li>
                            <a href="javascript:void(0)" onclick="addTab('系统邮件','<?php echo U('Admin/Game/gameEmail');?>')"><span style="color: #9bd199;font-size: 14px;" class="fa fa-reply-all">&nbsp;</span><span class="nav">系统邮件</span></a>
                        </li>
                        <li>
                            <a href="javascript:void(0)" onclick="addTab('游戏反馈','<?php echo U('Admin/Game/gameFeedback');?>')"><span style="color: #8bd4f2;font-size: 14px;" class="fa fa-gamepad">&nbsp;</span><span class="nav">游戏反馈</span></a>
                        </li>
                        <li>
                            <a href="javascript:void(0)" onclick="addTab('牌局记录','<?php echo U('Admin/Game/gameRecord');?>')"><span style="color: #eed716;font-size:14px;" class="fa fa-address-card-o">&nbsp;</span><span class="nav">牌局记录</span></a>
                        </li>
                        <li>
                            <a href="javascript:void(0)" onclick="addTab('兑换审核','<?php echo U('Admin/Game/gameExchange');?>')"><span style="color: #005904;font-size:14px;" class="fa fa-wpforms">&nbsp;</span><span class="nav">兑换审核</span></a>
                        </li>
                        <li>
                            <a href="javascript:void(0)" onclick="addTab('兑换列表','<?php echo U('Admin/Game/gameExchangeList');?>')"><span style="color: #FFAA25;font-size:14px;" class="fa fa-file-archive-o">&nbsp;</span><span class="nav">兑换列表</span></a>
                        </li>
                    </ul>
                </div>
                <div title="玩家模块" data-options="iconCls:'icon-group_add'" style="padding:10px;">
                    <ul class="navlist">
                        <li>
                            <a href="javascript:void(0)" onclick="addTab('玩家列表','<?php echo U('Admin/Player/playerList');?>')"><span style="color: #f27c50;font-size: 14px;" class="fa fa-slideshare">&nbsp;</span><span class="nav">玩家列表</span></a>
                        </li>
                        <li>
                            <a href="javascript:void(0)" onclick="addTab('日志消耗','<?php echo U('Admin/Player/playerLog');?>')"><span style="color: #83bdf1;font-size: 14px;" class="fa fa-file-text">&nbsp;</span><span class="nav">日志消耗</span></a>
                        </li>
                        <li>
                            <a href="javascript:void(0)" onclick="addTab('比赛日志','<?php echo U('Admin/Player/playerMatch');?>')"><span  style="color: #b6a5bf;font-size: 14px;" class="fa fa-universal-access">&nbsp;</span><span class="nav">比赛日志</span></a>
                        </li>
                        <li>
                            <a href="javascript:void(0)" onclick="addTab('资源增减','<?php echo U('Admin/Player/playerChange');?>')"><span style="color: #81a514;font-size: 14px;" class="fa fa-address-book-o">&nbsp;</span><span class="nav">资源增减</span></a>
                        </li>
                    </ul>
                </div>
                <div title="数据模块" data-options="iconCls:'icon-search'" style="padding:10px;">
                    <ul class="navlist">
                        <li>
                            <a href="javascript:void(0)" onclick="addTab('在线充值','<?php echo U('Admin/Data/dataOnlineBuy');?>')"><span style="color: #0068ce;font-size: 14px;" class="fa fa-shopping-cart">&nbsp;</span><span class="nav">在线充值</span></a>
                        </li>
                        <li>
                            <a href="javascript:void(0)" onclick="addTab('实时在线','<?php echo U('Admin/Data/dataOnline');?>')"><span class="icon icon-users">&nbsp;</span><span class="nav">实时在线</span></a>
                        </li>
                        <li>
                            <a href="javascript:void(0)" onclick="addTab('消耗统计','<?php echo U('Admin/Data/dataConsume');?>')"><span class="icon icon-users">&nbsp;</span><span class="nav">消耗统计</span></a>
                        </li>
                        <li>
                            <a href="javascript:void(0)" onclick="addTab('实时数据','<?php echo U('Admin/Data/dataUserstat');?>')"><span class="icon icon-users">&nbsp;</span><span class="nav">实时数据</span></a>
                        </li>
                    </ul>
                </div>
                <div title="管理模块" data-options="iconCls:'icon-search'" style="padding:10px;">
                    <ul class="navlist">
                        <li>
                            <a href="javascript:void(0)" onclick="addTab('角色管理','<?php echo U('Admin/Manger/mangerList');?>')"><span class="icon icon-users">&nbsp;</span><span class="nav">角色管理</span></a>
                        </li>
                        <li>
                            <a href="javascript:void(0)" onclick="addTab('用户管理','<?php echo U('Admin/Manger/mangerUser');?>')"><span class="icon icon-users">&nbsp;</span><span class="nav">用户管理</span></a>
                        </li>
                        <!--<li>-->
                            <!--<a href="javascript:void(0)" onclick="addTab('规则管理','<?php echo U('Admin/Manger/mangerRule');?>')"><span class="icon icon-users">&nbsp;</span><span class="nav">规则管理</span></a>-->
                        <!--</li>-->
                        <li>
                            <a href="javascript:void(0)" onclick="addTab('登录日志','<?php echo U('Admin/Manger/mangerLoginlog');?>')"><span class="icon icon-users">&nbsp;</span><span class="nav">用户管理</span></a>
                        </li>

                    </ul>
                </div>
            </div>
        </div>
        <div id="maind" data-options="region:'center',title:'主要内容',border:'false'" class="easyui-tabs"></div>
    </div>
    <div id="dlgEdit" class="easyui-dialog" style="padding:10px 20px;width:400px;"
         closed="true" buttons="#dlgEdit-buttons" modal="true">
        <form id="fmEdit" method="post" novalidate>
            <div class="easyui-panel" style="padding:5px;">
                <div class="fitem">
                    <input type="hidden" value="<?php echo ($ID); ?>" id="ID">
                    <label>登录账号:</label>
                    <input name="username" id="username" disabled class="easyui-textbox" data-options="height:30" value="<?php echo ($username); ?>">
                </div>
                <div class="fitem">
                    <label>新密码:</label>
                    <input name="pwd" id="pwd" type="text" class="easyui-textbox" data-options="height:30">
                </div>
            </div>
        </form>
        <div id="dlgEdit-buttons">
            <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-ok" onclick="saveAdmin()" style="width:90px">保存</a>
            <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dlgEdit').dialog('close')" style="width:90px">取消</a>
        </div>
    </div>
    <script type="text/javascript">
        function setClick() {
            $('#dlgEdit').dialog('open').dialog('setTitle','修改信息');
        }
        
        function saveAdmin() {
            var ID = $.trim($("#ID").val());
            var username = $.trim($("#username").val());
            var pwd = $.trim($("#pwd").val());
            var regs = /^(?=.*[a-zA-Z])(?=.*\d)[a-zA-Z\d]{6,10}$/;
            var tag = /^[a-zA-Z]{4,10}$/;
            if(username == '' || pwd == '') {
                $.messager.alert('温馨提示！','登录账号或新密码不能为空','error');
                return false;
            }else{
                if(!tag.test(username)) {
                    $.messager.alert('温馨提示！','请输入合法的登录账号','error');
                    return false;
                }
                if(!regs.test(pwd)) {
                    $.messager.alert('温馨提示！','请输入合法的新密码','error');
                    return false;
                }
                $.ajax({
                    type:'post',
                    url:"<?php echo U('Admin/Main/modInfo');?>",
                    dataType:'json',
                    data:{'username':username,'pwd':pwd,'ID':ID},
                    success: function (data) {
                        if(data == 'success') {
                            $.messager.alert('温馨提示','信息修改成功','info',setTimeout(function () {
                                window.location.href = "<?php echo U('Admin/Main/main');?>";
                            },500));
                        }else{
                            $.messager.alert('温馨提示','网络错误，信息修改失败','error',setTimeout(function () {
                                window.location.href = "<?php echo U('Admin/Main/main');?>";
                            },500));
                        }
                    }
                })
            }
        }
        
        function addTab(title, url){
            if ($('#maind').tabs('exists', title)){
                $('#maind').tabs('select', title);
            } else {
                var content = '<iframe scrolling="auto" frameborder="0"  src="'+url+'" style="width:100%;height:100%;"></iframe>';
                $('#maind').tabs('add',{
                    title:title,
                    content:content,
                    closable:true
                });
            }
        }
//        //清除缓存
        function clearCache(){
            var url="<?php echo U('Admin/System/clearCache');?>";
            $.ajax({
                type:"get",
                url:url,
                success: function(result){
                    if(result.status) $.messager.confirm('提示消息','缓存更新成功!',function(r){location.href="<?php echo U('Admin/Main/main');?>";});
                }
            });
        }
    </script>
</body>
</html>