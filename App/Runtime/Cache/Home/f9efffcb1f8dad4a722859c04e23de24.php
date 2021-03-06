<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>百亿云商务平台登录系统</title>
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
    <link rel="stylesheet" href="/wechat/Public/App/css/login.css">
    <script src="/wechat/Public/App/js/bootstrap.min.js"></script>
    <script src="/wechat/Public/App/js/common.js"></script>
</head>
<body>
    <div class="box">
        <div class="box_title"><p>百亿云商务平台</p></div>
        <div id="contents">
            <!--<form id="login_form">-->
                <div class="login_box">
                    <div class="input_div">
                        <label for="username" class="">账号</label>
                        <input type="email" class="input_form" id="username" placeholder="Accounts">
                    </div>
                    <div class="input_div">
                        <label for="password" class="">密码</label>
                        <input type="password" class="input_form" id="password" placeholder="Password">
                    </div>
                    <div class="input_div ver_code">
                        <label for="ensure" class="">验证码</label>
                        <input type="number" class="input_form" id="ensure" placeholder="Code">
                        <img alt="验证码" id="ver_img" src="<?php echo U('Home/login/verify_c',array());?>" title="点击刷新">
                    </div>
                    <div class="user_agre">
                        <input type="checkbox" id="agreement" checked="checked">
                        <a href="<?php echo U('Home/Look/userAgree');?>">用户协议</a>
                    </div>
                    <button class="btn submit_div" id="btn">登录</button>
                    <div style="text-align: center;margin-top:10px"> <a style="color: #f3ab26;" href="<?php echo U('Home/Look/findPwd');?>">忘记密码</a></div>
                </div>

            <!--</form>-->
            <div id="mask">
                <div class="mask_content">
                    <p id="mask_info"></p>
                    <button class="btn btn-primary" id="qure">确定</button>
                </div>
            </div>
        </div>
        <script type="text/javascript">
            $(function () {
                var ver_img = $("#ver_img");
                var ver_src = ver_img.attr("src");
                ver_img.attr("title","点击刷新");
                $("#ver_img").on("click",function () {
                    if(ver_src.indexOf('?')>0) {
                        $(this).attr("src",ver_src+"&random="+Math.random())
                    }else{
                        $(this).attr("src",ver_src.replace(/\?.*$/,'')+'?'+Math.random());
                    }
                })
            });

            $("#btn").on("click",function () {
                var user_id = $("#username").val();
                var pwd = $("#password").val();
                var ensure = $("#ensure").val();

                var state = $("#agreement").is(":checked");
                if(!state) {
                    $("#mask_info").html('请阅读《用户协议》');
                    $("#mask").css("display",'block');
                    return false;
                }
                check_reg(pwd,'密码格式为6-10位的数字+字母');
                check_reg(user_id,'用户账号格式为6-10位的数字+字母');

                notEmpty(ensure,'请填写验证码');
                notEmpty(pwd,'请填写登录密码');
                notEmpty(user_id,'请填写您的用户帐号');

                $.ajax({
                    type:'post',
                    url:"<?php echo U('Home/Login/loginCheck');?>",
                    dataType:'json',
                    data:{'user_id':user_id,'pwd':pwd,'ensure':ensure},
                    success: function (data) {
                        switch (data) {
                            case 'erocode':
                                altMsg('输入的验证码错误！',$("#ensure"));
                                break;
                            case 'eropwd':
                                altMsg("输入的密码错误！",$("#pwd"));
                                break;
                            case 'eroid':
                                altMsg("输入的用户账号错误！",$("#user_id"));
                                break;
                            case 'noperm':
                                altMsg("抱歉，输入的账号已被封号");
                                break;
                            case 'success':
                                window.location.href = "<?php echo U('Home/Index/agent');?>";
                                break;
                        }
                    }
                })
            });
        </script>
    </div>
</body>
</html>