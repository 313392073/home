<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>百亿云商务平台</title>
    <meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta name="apple-mobile-app-capable" content="yes">
    <meta name="apple-mobile-app-status-bar-style" content="black">
    <meta name="description" content="百亿云代理登录系统">
    <meta name="keywords" content="百亿云代理登录系统">
    <meta name="format-detection" content="telphone=no, email=no"/>
    <script src="/wechat/Public/App/js/jquery.js"></script>
    <link rel="stylesheet" href="/wechat/Public/App/css/common.css">
    <link rel="stylesheet" href="/wechat/Public/App/css/bootstrap.min.css">
    <link rel="stylesheet" href="/wechat/Public/App/font/css/font-awesome.min.css">
    <link rel="stylesheet" href="/wechat/Public/App/css/agentDetail.css">
    <script src="/wechat/Public/App/js/bootstrap.min.js"></script>
    <script src="/wechat/Public/App/js/common.js"></script>
</head>
<body>
    <div class="box">
        <div class="title row">
            <a href="<?php echo U('Home/Index/agent');?>"><i class="fa fa-chevron-left col-xs-2" aria-hidden="true"></i></a>
            <p class="col-xs-8">成员编辑</p>
            <i class="col-xs-2"></i>
        </div>
        <div class="contents">
            <div class="content_box">
                <div>账号：<span><?php echo ($login_id); ?></span><input type="hidden" id="agent_id" value="<?php echo ($agent_id); ?>"> <input type="hidden" id="game_id" value="<?php echo ($game_id); ?>"></div>
                <?php if(empty($game_id)): ?><div class="list">游戏ID：<input type="text" name="game_id" class="game_id" placeholder="请输入游戏ID"><button class="btn btn-primary" id="cbtn">匹配</button></div><?php endif; ?>
                <?php if(!empty($game_id)): ?><div class="list">游戏ID：<input type="text" name="game_id" class="game_id" value="<?php echo ($game_id); ?>" disabled></div><?php endif; ?>
                <?php if(!empty($nick_name)): ?><div class="list">游戏昵称：<input  style="color: red" type="text" name="nick_name" class="nick_name" value="<?php echo ($nick_name); ?>" disabled></div><?php endif; ?>
                <?php if(!empty($tea_name)): ?><div class="list">茶楼名称：<input type="text" name="tea_name" class="tea_name" value="<?php echo ($tea_name); ?>" disabled></div><?php endif; ?>
                <?php if(empty($tea_name)): ?><div class="list">茶楼名称：<input type="text" name="tea_name" class="tea_name" placeholder="请输入茶楼名称"></div><?php endif; ?>
                <?php if(empty($city)): ?><div class="list">茶楼区域：<input type="text" name="city" class="city" placeholder="请输入茶楼区域"></div><?php endif; ?>
                <?php if(!empty($city)): ?><div class="list">茶楼区域：<input type="text" name="city" value="<?php echo ($city); ?>" class="city" disabled></div><?php endif; ?>
                <?php if(empty($notice)): ?><div class="list">茶楼公告：<input type="text" name="notice" class="notice" placeholder="请输入茶楼显示公告"></div><?php endif; ?>
                <?php if(!empty($notice)): ?><div class="list">茶楼公告：<input type="text" name="notice" class="notice" value="<?php echo ($notice); ?>" disabled></div><?php endif; ?>
                <div style="position: relative">
                    <p>元宝余额： <span><?php echo ($cash); ?></span></p>
                    <p>房卡余额： <span><?php echo ($card); ?></span></p>
                    <div class="pub_img"><img src="<?php echo ($urls); ?>" style="width: 80px;height: 80px;"></div>
                </div>
                <?php if(empty($names)): ?><div class="list">姓名：<input type="text" name="names" class="names" placeholder="请输入姓名"></div><?php endif; ?>
                <?php if(!empty($names)): ?><div class="list">姓名：<input type="text" name="names" value="<?php echo ($names); ?>" class="names" disabled></div><?php endif; ?>
                <?php if(empty($tel)): ?><div class="list">手机号：<input type="text" name="tel" class="tel" placeholder="请输入手机号"></div><?php endif; ?>
                <?php if(!empty($tel)): ?><div class="list">手机号：<input type="text" name="tel" class="tel" value="<?php echo ($tel); ?>" disabled></div><?php endif; ?>
                <?php if(empty($card_id)): ?><div class="list">身份证：<input type="text" name="card_id" class="card_id" placeholder="请输入身份证"></div><?php endif; ?>
                <?php if(!empty($card_id)): ?><div class="list">身份证：<input type="text" name="card_id" class="card_id" value="<?php echo ($card_id); ?>" disabled></div><?php endif; ?>
                <div class="list">备注：<input type="text" name="remark" id="remark" placeholder="请输入备注"></div>
                <p style="color: red;text-align: center">*提示：文本框中信息，修改后，点击保存，信息即修改</p>
                <p style="text-align: center"><button class="btn btn-primary" id="saveBtn">保存</button></p>
            </div>
            <div id="mask">
                <div class="mask_content">
                    <p id="mask_info"></p>
                    <button class="btn btn-primary" id="qure">确定</button>
                </div>
            </div>
        </div>
        <footer class="footers">
            <ul>
                <li class="lion"><a href="<?php echo U('Home/Index/agent');?>"><i class="fa fa-handshake-o" aria-hidden="true"></i>成员管理</a></li>
                <li><a href="<?php echo U('Home/Index/order');?>"><i class="fa fa-address-card-o" aria-hidden="true"></i>订单查询</a></li>
                <li class="dropup sub_menus">
                    <a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-jpy" aria-hidden="true"></i>推广费</a>
                    <ul class="dropdown-menu">
                        <li><a href="<?php echo U('Home/Index/rebate_owner');?>">推广费（馆主）</a></li>
                        <li><a href="<?php echo U('Home/Index/rebate_user');?>">推广费（用户）</a></li>
                    </ul>
                </li>
                <li><a href="<?php echo U('Home/Index/agentInfo');?>"><i class="fa fa-user-circle" aria-hidden="true"></i>个人信息</a></li>
            </ul>
        </footer>
    </div>
    <script type="text/javascript">
        $("#saveBtn").on("click",function () {
            var ygame_id = $.trim($("#game_id").val());
            var agent_id = $.trim($("#agent_id").val());
            var game_id = $.trim($(".game_id").val());
            var tea_name = $.trim($(".tea_name").val());
            var notice = $.trim($(".notice").val());
            var names = $.trim($(".names").val());
            var tel = $.trim($(".tel").val());
            var card_id = $.trim($(".card_id").val());
            var remark = $.trim($("#remark").val());
            var city = $.trim($(".city").val());
            var params = {"agent_id":agent_id,"game_id":game_id,'tea_name':tea_name,'notice':notice,'names':names,'tel':tel,'card_id':card_id,'remark':remark,'city':city};
            if(ygame_id == 0) {
                showMsg("请先匹配游戏ID");
                $(".game_id").focus();
                return false
            }else{
                if(game_id =='' || tea_name == '' || notice == '' || names == '' || tel == '' || city == '') {
                    if(tel == '') {
                        notEmpty(tel,'手机号是必填项！');
                    }
                    if(names == '') {
                        notEmpty(names,'姓名是必填项！');
                    }
                    if(notice == '') {
                        notEmpty(notice,'茶楼公告是必填项！');
                    }
                    if(tea_name == '') {
                        notEmpty(tea_name,'茶楼名称是必填项！');
                    }
                    if(city == '') {
                        notEmpty(city,'茶楼区域是必填项！');
                    }
                    if(game_id == '') {
                        notEmpty(game_id,'游戏ID是必填项！');
                    }
                    return false;
                }else{
                    var sreg = /^1[3|4|5|7|8][0-9]{9}$/;
                    var stag = sreg.test(tel);
                    if(!stag) {
                        showMsg('手机号码的格式不正确，请重新输入！');
                        return false;
                    }
                    if(card_id == '') {
                        $.ajax({
                            type:"post",
                            url:"<?php echo U('Home/Index/saveInfo');?>",
                            dataType:'json',
                            data:params,
                            success: function (data) {
                                if(data == 'success') {
                                    showMsg('信息保存成功');
                                    setTimeout(function () {
                                        window.location.href="<?php echo U('Home/Index/agentDetail');?>?agent_id="+agent_id;
                                    },500);
                                }else{
                                    showMsg('信息保存失败!');
                                    setTimeout(function () {
                                        window.location.href="<?php echo U('Home/Index/agentDetail');?>?agent_id="+agent_id;
                                    },500);
                                }
                            }
                        });
                    }else{
                        var creg = /^[1-9]\d{5}[1-9]\d{3}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}([0-9]|X)$/;
                        var ctag = creg.test(card_id);
                        if(!ctag){
                            showMsg('身份证的格式不正确，请重新输入！');
                            return false;
                        }else{
                            $.ajax({
                                type:"post",
                                url:"<?php echo U('Home/Index/saveInfo');?>",
                                dataType:'json',
                                data:params,
                                success: function (data) {
                                    if(data == 'success') {
                                        showMsg('信息保存成功');
                                        setTimeout(function () {
                                            window.location.href="<?php echo U('Home/Index/agentDetail');?>?agent_id="+agent_id;
                                        },500);
                                    }else{
                                        showMsg('信息保存失败!');
                                        setTimeout(function () {
                                            window.location.href="<?php echo U('Home/Index/agentDetail');?>?game_id="+game_id;
                                        },500);
                                    }
                                }
                            });
                        }
                    }
                }
            }
        });
        $("#cbtn").on("click", function () {
            var game_id = $.trim($(".game_id").val());
            var agent_id = $.trim($("#agent_id").val());
            var regs = /^\d{6}$/;
            var tag = regs.test(game_id);
            if(game_id == '') {
                showMsg('请输入游戏ID');
                return false;
            }else{
                if(!tag) {
                    showMsg('游戏ID只能是六位数字');
                    return false;
                }else{
                    $.ajax({
                        type:'post',
                        url:"<?php echo U('Home/Index/checkUid');?>",
                        dataType:'json',
                        data:{"game_id":game_id,"agent_id":agent_id},
                        success: function (data) {
                            switch (data) {
                                case 'noeffect':
                                    showMsg("匹配失败，请输入正确ID！");
                                    setTimeout(function () {
                                        window.location.href="<?php echo U('Home/Index/agentDetail');?>?agent_id="+agent_id;
                                    },500);
                                    break;
                                case 'isAgent':
                                    showMsg("匹配失败，该玩家已经是代理了！");
                                    setTimeout(function () {
                                        window.location.href="<?php echo U('Home/Index/agentDetail');?>?agent_id="+agent_id;
                                    },500);
                                    break;
                                case 'hasupper':
                                    showMsg("匹配失败，该玩家不属于您！");
                                    setTimeout(function () {
                                        window.location.href="<?php echo U('Home/Index/agentDetail');?>?agent_id="+agent_id;
                                    },500);
                                    break;
                                case 'failure':
                                    showMsg("游戏ID错误，匹配失败！");
                                    setTimeout(function () {
                                        window.location.href="<?php echo U('Home/Index/agentDetail');?>?agent_id="+agent_id;
                                    },500);
                                    break;
                                case 'error':
                                    showMsg("网络错误，匹配失败！");
                                    setTimeout(function () {
                                        window.location.href="<?php echo U('Home/Index/agentDetail');?>?agent_id="+agent_id;
                                    },500);
                                    break;
                                case 'success':
                                    showMsg("匹配成功");
                                    setTimeout(function () {
                                        window.location.href="<?php echo U('Home/Index/agentDetail');?>?agent_id="+agent_id;
                                    },500);
                            }
                        }
                    })
                }
            }
        })
    </script>
</body>
</html>