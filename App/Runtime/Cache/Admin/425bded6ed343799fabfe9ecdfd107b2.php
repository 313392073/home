<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <link rel="stylesheet" type="text/css" href="/wechat/Public/Common/easyui/themes/default/easyui.css">
    <link rel="stylesheet" type="text/css" href="/wechat/Public/Common/easyui/css/admin.css">
    <link rel="stylesheet" type="text/css" href="/wechat/Public/Common/easyui/themes/icon.css">
    <script type="text/javascript" src="/wechat/Public/Common/easyui/js/jquery.min.js"></script>
    <script type="text/javascript" src="/wechat/Public/Common/easyui/js/jquery.easyui.min.js"></script>
    <script type="text/javascript" src="/wechat/Public/Common/easyui/js/easyloader.js"></script>
    <script type="text/javascript" src="/wechat/Public/Common/easyui/js/easyui-lang-zh_CN.js"></script>
    <script type="text/javascript" src="/wechat/Public/Common/easyui/js/common.js"></script>
    <script type="text/javascript" src="/wechat/Public/Common/easyui/js/extends.js"></script>
    <script type="text/javascript" src="/wechat/Public/Admin/js/time.js"></script>
</head>
<body>
    <table id="dg" class="easyui-datagrid" title="馆主列表" toolbar="#tb"></table>
    <script type="text/javascript">
        $(function () {
            $("#dg").datagrid({
                loadMsg : '数据加载中……',
                url:"<?php echo U('Admin/Manger/getLoginlog');?>",
                pagination:true,
                singleSelect:true,
                pageSize: 25, //页大小
                pageList: [25,50,75,100],
                pagePosition:'bottom',
                fit:true,
                fitColumn:true,
                striped: true,
                idField:'id',
                columns:[[
                    {field:'id',hidden:true},
                    {field:'username',width:'25%',align:'center',title:'登录账号'},
                    {field:'ip',width:'25%',align:'center',title:'登录IP'} ,
                    {field:'add_time',width:'25%',align:'center',title:'注册时间',formatter:
                        function (value) {
                            value = getLocalTime(value);
                            return value;
                        }
                    },
                    {field:'type',width:'25%',align:'center',title:'操作', formatter:
                        function(value){
                            if(value == 'login') {
                                return '登录';
                            }
                        }
                    },
                ]]
            })
        });
    </script>
</body>
</html>