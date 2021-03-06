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
<table id="dg" class="easyui-datagrid" title="购买列表" toolbar="#tb"></table>
<div id="tb">
    玩家ID或昵称: <input class="easyui-textbox" id="game_id" style="width:160px">
    <a href="#" class="easyui-linkbutton" iconCls="icon-search" onclick="doSearch()">查询</a>
    订单号: <input class="easyui-textbox" id="order_id" style="width:160px">
    <a href="#" class="easyui-linkbutton" iconCls="icon-search" onclick="cdoSearch()">查询</a>&emsp;&emsp;
    <a href="<?php echo U('Admin/Data/dataNowday');?>" class="easyui-linkbutton">日统计</a>&emsp;
    <a href="<?php echo U('Admin/Data/dataMonth');?>" class="easyui-linkbutton">月统计</a>
</div>
<script type="text/javascript">
    $(function () {
        $("#dg").datagrid({
            loadMsg : '数据加载中……',
            url:"<?php echo U('Admin/Data/dataOnlineBuy');?>",
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
                {field:'game_id',width:100,align:'center',title:'游戏ID'},
                {field:'username',width:100,align:'center',title:'微信昵称'},
                {field:'order_id',width:200,align:'center',title:'订单号'},
                {field:'number',width:120,align:'center',title:'金额'} ,
                {field:'amount',width:120,align:'center',title:'购买数量'} ,
                {field:'add_time',width:180,align:'center',title:'购买时间',formatter:
                    function (value) {
                        value = getLocalTime(value);
                        return value;
                    }
                },
                {field:'status',width:120,align:'center',title:'购买状态',formatter:
                    function (value) {
                        if(value == 1) {
                            return '成功';
                        }else{
                            return '失败';
                        }
                    }
                }
            ]]
        })
    });
    function doSearch() {
        $('#dg').datagrid('load',{
            game_id: $.trim($('#game_id').val())
        });
    }
    function cdoSearch() {
        $('#dg').datagrid('load',{
            order_id: $.trim($('#order_id').val())
        });
    }
</script>
</body>
</html>