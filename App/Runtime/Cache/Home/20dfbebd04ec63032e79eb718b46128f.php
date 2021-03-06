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
    <link rel="stylesheet" href="/wechat/Public/App/css/agent.css">
    <script src="/wechat/Public/App/js/bootstrap.min.js"></script>
    <script src="/wechat/Public/App/js/common.js"></script>
</head>
<body>
    <div class="box">
        <div class="top_bar">
            <div class="top_box">
                <div class="user_info row">
                    <span class="user_name col-xs-5">姓名：<?php echo ($name); ?></span>
                    <span class="user_ID col-xs-4" id="agent_id" data-target="<?php echo ($agent_id); ?>">ID：<?php echo ($agent_id); ?></span>
                    <a href="<?php echo U('Home/Index/agentInfo');?>" class="col-xs-2"><img src="/wechat/Public/App/img/user_img.png" title="个人信息"></a>
                </div>
                <?php if($role > 3): ?><div class="quotas row"><span class="col-xs-4 item">剩余配额：<?php echo ($sub_player); ?></span> <span class="col-xs-7 item">经验值：已达上限！</span></div><?php endif; ?>
                <?php if($role <= 3): ?><div class="quotas row"><span class="col-xs-4 item">剩余配额：<?php echo ($sub_player); ?></span> <span class="col-xs-6 item">经验值：<?php echo ($expv); ?>/<?php echo ($maxExpv); ?></span><input type="button" class="btn btn-xs col-xs-2 item" id="ubtn" value="升阶"></div><?php endif; ?>
            </div>
        </div>
        <div class="contents">
            <div class="nav_bars row">
                <button class="col-xs-2 icons" id="refresh"><i class="fa fa-refresh" aria-hidden="true"></i></button>
                <div class="col-xs-8 input_div"><input type="text" id="search" name="search"><a href="javascript:void(0)" id="sbtn"><i class="fa fa-search" aria-hidden="true"></i></a></div>
                <button class="col-xs-2 icons" data-toggle="modal" data-target="#myModal" ><i class="fa fa-plus" aria-hidden="true"></i></button>
            </div>
            <div class="content_box">
                <div class="info_box">
                    <?php if(empty($subs)): ?><div class="info_list">暂时没有任何成员信息</div><?php endif; ?>
                    <?php if(!empty($subs)): if(is_array($subs)): foreach($subs as $key=>$v): ?><div class="info_list" onclick="perClick(this)">
                                <div style="color: #000000"><span>登录账号：<?php echo ($v["login_id"]); ?></span></div>
                                <div>姓名：<?php echo ($v["name"]); ?></div>
                                <div>茶楼ID：<?php echo ($v["agent_id"]); ?></div>
                                <div>茶楼区域：<?php echo ($v["city"]); ?></div>
                                <div>茶楼名称：<?php echo ($v["tea_name"]); ?></div>
                                <div class="arrow_right"><a href="<?php echo U('Home/Index/agentDetail');?>?agent_id=<?php echo ($v["agent_id"]); ?>"><i class="fa fa-chevron-right" aria-hidden="true"></i></a></div>
                            </div><?php endforeach; endif; endif; ?>
                </div>
            </div>
            <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-body">
                            <p class="motitle">登录账号 <input type="text" placeholder="登录账号" id="mologin_id"></p>
                            <p class="descs">*账号创建后，不可修改 <br> 密码默认和账号一致，可在“编辑”修改或者下级可自行修改登录账号，请使用字母加数字组合<br></p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                            <button type="button" class="btn btn-primary" id="mobtn">确定</button>
                        </div>
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
        (function(win) {
            var doc = win.document;
            var docEl = doc.documentElement;
            var tid;
            function refreshRem() {
                var width = docEl.getBoundingClientRect().width;
                if (width > 540) { // 最大宽度
                    width = 540;
                }
                var rem = width / 6.4/5;
                docEl.style.fontSize = rem + 'px';
            }
            win.addEventListener('resize', function() {
                clearTimeout(tid);
                tid = setTimeout(refreshRem, 300);
            }, false);
            win.addEventListener('pageshow', function(e) {
                if (e.persisted) {
                    clearTimeout(tid);
                    tid = setTimeout(refreshRem, 300);
                }
            }, false);
            refreshRem();
        })(window);

        function perClick(This) {
            var url = $(This).find("div.arrow_right>a").attr("href");
            window.location.href = url;
        }

        $("#sbtn").on("click",function () {
            var search = $.trim($("#search").val());
            if(search == '') {
                notEmpty(search,'请输入要查询的信息！');
                return false;
            }else{
                $.ajax({
                    type:"post",
                    url:"<?php echo U('Home/Index/searchInfo');?>",
                    dataType:'json',
                    data:{'search':search},
                    success: function (data) {
                        if(data == 'error') {
                            showMsg('未查询到任何相关信息！');
                            $(".info_box").html("未查询到任何相关信息");
                        }else if(data == ''){
                            $(".info_box").html("未查询到任何相关信息");
                        }else{
                            var str = '';
                            var url = '';
                            for(var i=0;i<data.length;i++) {
                                url = "<?php echo U('Home/Index/agentDetail');?>?agent_id="+data[i].agent_id;
                                str += "<div class='info_list' onclick='perClick(this)'><div style='color: #000000'><span>登录账号："+data[i].login_id+"</span></div><div>姓名："+data[i].name+"</div><div>茶楼ID："+data[i].agent_id+"</div><div>茶楼区域："+data[i].city+"</div><div>茶楼名称："+data[i].tea_name+"</div><div class='arrow_right'><a href='"+url+"'><i class='fa fa-chevron-right' aria-hidden='true'></i></a></div></div>"
                            }
                            $(".info_box").html(str);
                        }
                    }
                });
            }
        })
        
        
        $("#refresh").on("click", function () {
            window.location.href = "<?php echo U('Home/Index/agent');?>";
        })
        $("#mologin_id").on("blur", function () {
            var mologin_id = $.trim($("#mologin_id").val());
            if(mologin_id == '') {
                notEmpty(mologin_id,'创建的账号不能为空');
                return false;
            }else{
                var regs = /^(?=.*[a-zA-Z])(?=.*\d)[a-zA-Z\d]{6,10}$/;
                var tags = regs.test(mologin_id);
                if(!tags) {
                    showMsg('账号格式为6-10位字母拼音+数字的组合');
                    return false;
                }
            }
        });
        $("#mobtn").on("click", function () {
            var mologin_id = $.trim($("#mologin_id").val());
            if(mologin_id == '') {
                notEmpty(mologin_id,'创建的账号不能为空');
                return false;
            }else{
                var regs = /^(?=.*[a-zA-Z])(?=.*\d)[a-zA-Z\d]{6,10}$/;
                var tags = regs.test(mologin_id);
                if(!tags) {
                    showMsg('账号格式为6-10位字母拼音+数字的组合');
                    return false;
                }else{
                    $.ajax({
                        type:'post',
                        url:"<?php echo U('Home/Index/createLoginid');?>",
                        dataType:'json',
                        data:{'login_id':mologin_id},
                        success: function (data) {
                            switch (data) {
                                case 'exist':
                                    showMsg('账号已被占用，请重新输入!');
                                    break;
                                case 'error':
                                    showMsg('你开通代理的配额不足，创建账号失败！');
                                    setTimeout(function () {
                                        window.location.href = "<?php echo U('Home/Index/agent');?>";
                                    },1000);
                                    break;
                                case 'setfailure':
                                    showMsg('配额出错，创建账号失败！');
                                    setTimeout(function () {
                                        window.location.href = "<?php echo U('Home/Index/agent');?>";
                                    },1000);
                                    break;
                                case 'failure':
                                    showMsg('创建账号失败！');
                                    setTimeout(function () {
                                        window.location.href = "<?php echo U('Home/Index/agent');?>";
                                    },1000);
                                    break;
                                case 'success':
                                    showMsg('创建账号成功');
                                    setTimeout(function () {
                                        window.location.href = "<?php echo U('Home/Index/agent');?>";
                                    },500);
                                    break;
                            }
                        }
                    })
                }
            }
        });
        $("#ubtn").on("click", function () {
            var agent_id = $.trim($("#agent_id").data("target"));
            var expv = parseInt("<?php echo ($expv); ?>");
            var maxExpv = parseInt("<?php echo ($max); ?>");
            if(expv < maxExpv) {
                showMsg('经验值还未达到提升的要求，继续努力！！！')
                return false;
            }else{
                $.ajax({
                    type:"get",
                    url:"<?php echo U('Home/Index/upRecord');?>?agent_id="+agent_id,
                    dataType:'json',
                    success: function (data) {
                        if(data == 'success') {
                            showMsg('升阶申请成功');
                            setTimeout(function () {
                                window.location.href = "<?php echo U('Home/Index/agent');?>";
                            },500);
                        }else{
                            showMsg('升阶申请失败');
                        }
                    }
                })
            }
        });
    </script>
</body>
</html>