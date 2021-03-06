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
    <link rel="stylesheet" href="/wechat/Public/App/css/agentInfo.css">
    <script src="/wechat/Public/App/js/bootstrap.min.js"></script>
    <script src="/wechat/Public/App/js/common.js"></script>
    <script src="/wechat/Public/App/js/qrcode.min.js"></script>
</head>
<body>
<div class="box">
    <div class="row title">
        <a href="<?php echo U('Home/Index/agent');?>"><i class="fa fa-home col-xs-2" aria-hidden="true"></i></a>
        <p class="col-xs-8">个人信息</p>
        <a href="<?php echo U('Home/Login/out');?>"><i class="fa fa-sign-out col-xs-2" aria-hidden="true"></i></a>
    </div>
    <div class="contents">
        <div class="content_box">
            <div class="text_box">
                <div class="left_text">
                    <p>姓名：
                        <span><?php echo ($name); ?></span>
                        <!--<button class="btn" data-toggle="modal" data-target="#xg_name" style="margin-left: 10px;background-color: #D0A968;color: #FFFFFF">修改</button>-->
                    </p>
                    <p>代理ID：<span><?php echo ($agent_id); ?></span><input id="hagent_id" type="hidden" value="<?php echo ($agent_id); ?>"></p>
                    <p>茶楼名称：<span><?php echo ($tea_name); ?></span></p>
                    <p>茶楼区域：<span><?php echo ($city); ?></span></p>
                    <p>游戏ID：<span data-game="<?php echo ($game_id); ?>" id="game_id"><?php echo ($game_id); ?></span></p>
                    <p>游戏昵称：<span><?php echo ($nick_name); ?></span></p>
                </div>
                <div class="er_img"><img src="<?php echo ($urls); ?>" style="width: 150px;height: 150px" alt="二维码"></div>
            </div>
            <div class="btn_group">
                <button class="btn" data-toggle="modal" data-target="#withdraw">申请结算</button>
                <button class="btn" data-toggle="modal" data-target="#xg_pwd">修改密码</button>
            </div>
            <div class="btn_group">
                <?php if($role != 0): ?><button class="btn" data-toggle="modal" data-target="#giveCard" onclick="getResource()">赠送房卡</button><?php endif; ?>
                <button class="btn" data-toggle="modal" data-target="#buyCard">购买房卡</button>
                <input type="hidden" id="buyDatas">
            </div>
        </div>
        <div class="modal fade" tabindex="-1" role="dialog" id="xg_name" aria-labelledby="myLargeModalLabel">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="input_div">旧姓名<input type="text" value="<?php echo ($name); ?>" disabled></div>
                    <div class="input_div">新姓名<input type="text" placeholder="请输入新姓名" id="new_name"></div>
                    <div class="btn_group">
                        <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                        <button type="button" class="btn btn-primary" id="modName">修改</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" tabindex="-1" role="dialog" id="xg_pwd" aria-labelledby="myLargeModalLabel">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <p style="color:#CAA45B;font-size: 12px;">如不是自己的手机号，请联系商务或者上级修改 </p>
                    <div class="input_div">当前手机号<input type="text" data-tel="<?php echo ($tel); ?>" value="<?php echo ($tel); ?>" id="tel" disabled></div>
                    <div class="input_div ycode">验证码<input type="text" placeholder="请输入验证码" id="ensure"><input type="button" value="获取验证码" id="getCode" /></div>
                    <div class="input_div">新密码<input type="text" placeholder="请输您的新密码" id="pwd"></div>
                    <div class="input_div">确认新密码<input type="text" placeholder="请再次输入您的新密码" id="en_pwd"></div>
                    <div class="btn_group">
                        <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                        <button type="button" class="btn btn-primary" id="modPwd">修改</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" tabindex="-1" role="dialog" id="withdraw" aria-labelledby="myLargeModalLabel">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <p class="wechat_img"><img src="/wechat/Public/App/img/WeChat.png" alt="wechat"></p>
                    <p style="color: red" ><span style="color: #222222;">*可结算金额共有</span> ￥<span id="widthdraw"><?php echo ($widthdraw); ?></span></p>
                    <div class="amount">
                        <ul class="row lists">
                            <li class="col-xs-5 list-group-item lion" data-amount="100" data-pro="1"></li>
                            <li class="col-xs-5 list-group-item" data-amount="500" data-pro="2"></li>
                            <li class="col-xs-5 list-group-item" data-amount="1000" data-pro="3"></li>
                            <li class="col-xs-5 list-group-item" data-amount="3000" data-pro="4"></li>
                        </ul>
                    </div>
                    <p style="color: red">*微信结算，每天只能结算一次</p>
                    <div class="btn_group">
                        <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                        <button type="button" class="btn btn-primary" id="mod_withdraw">确定</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" tabindex="-1" role="dialog" id="ercodes" aria-labelledby="myLargeModalLabel">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="btn_group">
                        <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" tabindex="-1" role="dialog" id="giveCard" aria-labelledby="myLargeModalLabel">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="input_div">剩余房卡数:<input type="text" id="surCard" disabled></div>
                    <div class="input_div" style="position: relative">赠送游戏ID:<input type="text" id="give_id" placeholder="请输入游戏ID"><button style="position: absolute;top: -10px;right: 5px;padding: 5px 8px;" id="getName" class="btn btn-primary">匹配</button></div>
                    <div class="input_div">游戏昵称&emsp;:<input type="text" id="nick_name" disabled placeholder="请匹配游戏昵称"></div>
                    <div class="input_div">赠送数量&emsp;:<input type="text" id="give_num" placeholder="请输入赠送的数量"></div>
                    <div class="btn_group">
                        <button type="button" class="btn btn-default" data-dismiss="modal" onclick="getBack()">取消</button>
                        <button type="button" class="btn btn-primary" id="gBtn">确定</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" tabindex="-1" role="dialog" id="buyCard" aria-labelledby="myLargeModalLabel">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content" style="position: relative">
                    <?php if(empty($refer)): ?><p class="wechat_img"><img src="/wechat/Public/App/img/120.png" alt=""></p>
                        <p class="cash">
                            <span>100元100房卡</span>
                            <span class="cringt">200元200房卡</span>
                        </p>
                        <p class="cash">
                            <span>500元500房卡</span>
                            <span class="cringt">1000元1000房卡</span>
                        </p>
                        <div class="amount">
                            <ul class="row lists buys">
                                <li class="col-xs-5 list-group-item lion" data-amount="100" data-pro="1" onclick="callpay(this)"></li>
                                <li class="col-xs-5 list-group-item" data-amount="200" data-pro="2" onclick="callpay(this)"></li>
                                <li class="col-xs-5 list-group-item" data-amount="500" data-pro="3" onclick="callpay(this)"></li>
                                <li class="col-xs-5 list-group-item" data-amount="1000" data-pro="4" onclick="callpay(this)"></li>
                            </ul>
                        </div>
                        <div id="qrcode"></div><?php endif; ?>
                    <?php if(!empty($refer)): ?><div style="text-align: center">
                            <p style="border-bottom: 2px dashed #CCCCCC;padding-bottom: 5px;">温馨提示！</p>
                            <p>购买折扣房卡，请联系您的上级：<?php echo ($tel); ?></p>
                        </div><?php endif; ?>
                </div>
            </div>
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
            <li><a href="<?php echo U('Home/Index/agent');?>"><i class="fa fa-handshake-o" aria-hidden="true"></i>成员管理</a></li>
            <li><a href="<?php echo U('Home/Index/order');?>"><i class="fa fa-address-card-o" aria-hidden="true"></i>订单查询</a></li>
            <li class="dropup sub_menus">
                <a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-jpy" aria-hidden="true"></i>推广费</a>
                <ul class="dropdown-menu">
                    <li><a href="<?php echo U('Home/Index/rebate_owner');?>">推广费（馆主）</a></li>
                    <li><a href="<?php echo U('Home/Index/rebate_user');?>">推广费（用户）</a></li>
                </ul>
            </li>
            <li class="lion"><a href="<?php echo U('Home/Index/agentInfo');?>"><i class="fa fa-user-circle" aria-hidden="true"></i>个人信息</a></li>
        </ul>
    </footer>
</div>
<script type="text/javascript">
    $(function () {
        $(".lists>li").each(function () {
            $(this).html($(this).data("amount")+'￥');
        });

        $(".lists>li").on("click", function () {
            $(this).addClass("lion").siblings('li').removeClass("lion");
        })
    });
    $("#modName").on("click",function () {
        var new_name = $("#new_name").val();
        if(new_name == '') {
            notEmpty(new_name,'新的姓名不能为空！');
            return false;
        }else{
            if($.trim(new_name).length < 1 || $.trim(new_name).length > 5) {
                showMsg('姓名长度为2-4位数');
                return false;
            };
            $.ajax({
                type:'post',
                url:"<?php echo U('Home/Index/modName');?>",
                dataType:'json',
                data:{'new_name':new_name},
                success: function (data) {
                    if(data == 'success') {
                        showMsg('修改姓名成功！');
                        setTimeout(function () {
                            window.location.href="<?php echo U('Home/Index/agentInfo');?>";
                        },1000)
                    }else{
                        showMsg('修改姓名失败！');
                        setTimeout(function () {
                            window.location.href="<?php echo U('Home/Index/agentInfo');?>";
                        },1000)
                    }
                }
            })
        }
    });

    $("#mod_withdraw").on("click", function () {
        var withdraw = $.trim($("#widthdraw").html());
        var hagent_id = $.trim($("#hagent_id").val());
        var ondraw = 0;
        var pro_id = 0;
        $("#withdraw .lists>li").each(function () {
            if($(this).hasClass("lion")) {
                ondraw = $.trim($(this).data("amount"));
                pro_id = $.trim($(this).data("pro"));
            }
        });
        if(Number(withdraw) < Number(ondraw)) {
            showMsg("可结算金额有限,不能结算！");
        }else{
            $.ajax({
                type:"post",
                url:"<?php echo U('Home/Index/withDraws');?>",
                dataType:'json',
                data:{"agent_id":hagent_id,'ondraw':ondraw,'pro_id':pro_id},
                success: function (data) {
                    switch (data) {
                        case 'success':
                            showMsg('申请结算成功！');
                            setTimeout(function () {
                                window.location.href="<?php echo U('Home/Index/agentInfo');?>";
                            },500);
                            break;
                        case 'failure':
                            showMsg('申请失败,请重试！');
                            setTimeout(function () {
                                window.location.href="<?php echo U('Home/Index/agentInfo');?>";
                            },500);
                            break;
                        case 'error':
                            showMsg('申请失败,请重试！');
                            setTimeout(function () {
                                window.location.href="<?php echo U('Home/Index/agentInfo');?>";
                            },500);
                            break;
                        case 'noUser':
                            showMsg('申请失败,请重试！');
                            setTimeout(function () {
                                window.location.href="<?php echo U('Home/Index/agentInfo');?>";
                            },500);
                            break;
                        case 'timeIn':
                            showMsg("注意：每天只能发起一笔提现申请！")
                            setTimeout(function () {
                                window.location.href="<?php echo U('Home/Index/agentInfo');?>";
                            },500);
                            break;
                    }
                }
            })
        }
    });
    $("#getCode").on("click", function () {
        settime(this,60);
        var tel = $.trim($("#tel").data("tel"));
        $.ajax({
            type:"post",
           // url:"<?php echo U('Home/Index/sendCode');?>",
            dataType:'json',
            data:{'tel':tel},
            success: function (data) {
                if(data == 'success') {
                    console.log(data);
//                        showMsg('验证码已发送到你手机上了，请注意查收！')
//                        settime($("#getCode"),60);
                }else{
                    showMsg('验证码发送失败，请稍后再试！');
                }
            }
        })
    });

    $("#modPwd").on("click", function () {
        var ensure = $.trim($("#ensure").val());
        var pwd = $.trim($("#pwd").val());
        var en_pwd = $.trim($("#en_pwd").val());
        if(en_pwd == '' || pwd == '' || ensure == '') {
            notEmpty(en_pwd,'请输入确认密码');
            notEmpty(pwd,'请输入新密码！');
            notEmpty(ensure,'请输入验证码！');
            return false;
        }else{
            if(pwd !== en_pwd) {
                showMsg("两次密码不一致，请重新输入！");
                return false;
            };
            check_reg(pwd,"密码格式为6-10位的数字+字母");
            check_reg(en_pwd,"密码格式为6-10位的数字+字母");
            $.ajax({
                type:"post",
                url:"<?php echo U('Home/Index/checkCode');?>",
                dataType:"json",
                data:{"ensure":ensure,'pwd':pwd,'en_pwd':en_pwd},
                success: function (datas) {
                    if(datas == 'notCode') {
                        showMsg("验证码错误或者已过时，请重新输入！");
                        setTimeout(function () {
                            window.location.href = "<?php echo U('Home/Index/agentInfo');?>";
                        },5000);
                    }else if(datas == 'repeat') {
                        showMsg("新密码不能和原密码相同，请重新输入！");
                        setTimeout(function () {
                            window.location.href = "<?php echo U('Home/Index/agentInfo');?>";
                        },500);
                    }else if(datas == 'success') {
                        showMsg('修改密码成功！');
                        setTimeout(function () {
                            window.location.href = "<?php echo U('Home/Index/agentInfo');?>";
                        },500);
                    }else if(datas == 'error') {
                        showMsg('网络错误，修改失败！');
                        setTimeout(function () {
                            window.location.href = "<?php echo U('Home/Index/agentInfo');?>";
                        },500);
                    }
                }
            })
        }
    });
    function jsApiCall1(obj){
        $.ajax({
            type:'post',
            url:"<?php echo U('Home/Paywx/getData');?>",
            dataType:'json',
            data:{'num':obj[0],'id':obj[1],'game_id':obj[2]},
            success:function (datas) {
                if(datas == 'error') {
                    showMsg("网络错误");
                }else {
                    datas = JSON.parse(datas);
                    WeixinJSBridge.invoke(
                        'getBrandWCPayRequest',
                        datas,
                        function(res){
                            WeixinJSBridge.log(res.err_msg);
                            if(res.err_msg == 'get_brand_wcpay_request:ok'){
                                window.location.href = "<?php echo U('Home/Index/agentInfo');?>";
                            }else if(res.err_msg == 'get_brand_wcpay_request:cancel'){
                                showMsg("你已取消订单！");
                            }else{
                                showMsg('支付失败！');
                            }
//                            alert(res.err_code+res.err_desc+res.err_msg);
                        }
                    );
                }
            }
        });
    }
    function callpay(This){
        var num = $.trim($(This).data('amount'));
        var id = $.trim($(This).data('pro'));
        var game_id = $.trim($("#game_id").data("game"));
        if (typeof WeixinJSBridge == "undefined"){
            if( document.addEventListener ){
                document.addEventListener('WeixinJSBridgeReady', jsApiCall1.bind(this), false);
            }else if (document.attachEvent){
                document.attachEvent('WeixinJSBridgeReady', jsApiCall1.bind(this));
                document.attachEvent('onWeixinJSBridgeReady', jsApiCall1.bind(this));
            }
        }else{
            jsApiCall1([num,id,game_id]);
        }
    }
    $("#gBtn").on("click", function () {
        var nick_name = $.trim($("#nick_name").val());
        if(nick_name == '') {
            showMsg("请先匹配游戏ID");
        }else{
            var surCard = $.trim($("#surCard").val());
            var give_id = $.trim($("#give_id").val());
            var give_num = $.trim($("#give_num").val());
            var game_id = $.trim($("#game_id").data("game"));
            checkId(give_id,"请输入合法的游戏ID");
            var reg = /^[1-9]\d*$/;
            var tag = reg.test(give_num);
            if(!tag) {
                showMsg("请输入合法的赠送数量");
                return false;
            }
            if(parseInt(surCard) < parseInt(give_num)) {
                showMsg("房卡不足，请充值！");
                return false;
            }
            if(give_id == game_id) {
                showMsg("请输入合法的账号，不能给自己充值！");
                window.location.href = "<?php echo U('Home/Index/agentInfo');?>?game_id="+game_id;
            }
            $.ajax({
                type:'post',
                url:"<?php echo U('Home/Index/giveCard');?>",
                dataType:'json',
                data:{'game_id':game_id,'give_id':give_id,'give_num':give_num},
                success: function (data) {
                    console.log(data);
                    if(data == 'success') {
                        showMsg('赠送成功');
                        setTimeout(function () {
                            window.location.href = "<?php echo U('Home/Index/agentInfo');?>?game_id="+game_id;
                        },500);
                    }else{
                        showMsg('网络错误，赠送失败！');
                        setTimeout(function () {
                            window.location.href = "<?php echo U('Home/Index/agentInfo');?>?game_id="+game_id;
                        },1000);
                    }
                }
            })
        }
    });
    function getResource() {
        $("#give_id").val('');
        $("#give_num").val('');
        $("#give_id").removeAttr("disabled");
        var game_id = $.trim($("#game_id").data("game"));
        $.ajax({
            type:"get",
            url:"<?php echo U('Home/Index/getSource');?>?game_id="+game_id,
            dataType:'json',
            success: function (data) {
                if(data == 'error') {
                    showMsg("网路错误，获取资源失败");
                    setTimeout(function () {
                        window.location.href = "<?php echo U('Home/Index/agentInfo');?>?game_id="+game_id;
                    },500);
                }else if(data == 'logerr'){
                    showMsg("请先登录游戏获取资源！");
                    setTimeout(function () {
                        window.location.href = "<?php echo U('Home/Index/agentInfo');?>?game_id="+game_id;
                    },500);
                }else{
                    $("#surCard").val(data);
                }
            }
        })
    }
    $("#getName").on("click", function () {
        var give_id = $.trim($("#give_id").val());
        var hagent_id = $.trim($("#hagent_id").val());
        var game_id = $.trim($("#game_id").data("game"));
        if(give_id == '') {
            showMsg("请输入要匹配的游戏ID");
            return false;
        }else{

            if(give_id == game_id) {
                showMsg("不能匹配自己的账号！");
                window.location.href = "<?php echo U('Home/Index/agentInfo');?>?game_id="+game_id;
            }
            $.ajax({
                type:'post',
                url:"<?php echo U('Home/Index/matchId');?>",
                dataType:'json',
                data:{'give_id':give_id,'agent_id':hagent_id},
                success: function (data) {
                    if(data == 'error') {
                        showMsg("匹配失败，请输入正确的游戏ID");
                        setTimeout(function () {
                            window.location.href = "<?php echo U('Home/Index/agentInfo');?>?game_id="+game_id;
                        },500);
                    }else if(data == 'hasAgent') {
                        showMsg('该游戏ID不是您的下级，匹配失败');
                        setTimeout(function () {
                            window.location.href = "<?php echo U('Home/Index/agentInfo');?>?game_id="+game_id;
                        },500);
                    }else{
                        $("#nick_name").val(data);
                        $("#give_id").attr('disabled','disabled');
                    }
                }
            })
        }
    })
    function getBack() {
        $("#give_id").val('');
        $("#nick_name").val('');
        $("#give_num").val('');
    }
</script>
</body>
</html>