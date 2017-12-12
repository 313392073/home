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
    <script type="text/javascript" src="/wechat/Public/Admin/js/echarts.min.js"></script>
    <style type="text/css">
        .box{
            width: 800px;
            height: 95px;
            padding:20px;
            border: 1px solid #ccc;
            border-radius: 12px;
            box-shadow: 2px 2px 5px #ddd;
            font-size: 18px;
            overflow: hidden;
        }
        .box>p{
            height: 30px;
            background-color: #3e7b9f;
            border-radius: 12px;
            padding: 20px;
            float: left;
            width: 44%;
        }
        .box>p>span{
            font-size: 20px;
            font-weight: bold;
            color: red
        }
        .selected{
            padding: 15px 0;
        }
    </style>
</head>
<body>
    <div class="box">
        <p >总注册人数：<span ><?php echo ($total); ?></span></p>
        <p style="background-color: #caa45b;margin-left: 10px">目前在线人数：<span> <?php echo ($online); ?></span></p>
    </div>
    <div class="selected">
        <span>查看历史在线，请选择日期：</span>
        <input type="text" name="timed" class="easyui-datebox" id="timed">
        <a href="#" class="easyui-linkbutton" iconCls="icon-search" onclick="doSearch()">查询</a>
    </div>
    <div id="main" style="height: 400px;width: 70%;min-width: 950px"></div>
    <script type="text/javascript">
        $(function () {
            getTime($("#timed"));
            var timed = getLocalTimes();
            console.log(timed);
            $.ajax({
                type:'post',
                url:"<?php echo U('Admin/Data/dataOnline');?>",
                dataType:'json',
                data:{'timed':timed},
                success:function (res) {
                    var data = JSON.parse(res);
                    var result = Object.keys(data);
                    result = result.sort();
                    var arr = [];
                    result.forEach(function (k) {
                        arr.push(data[k]);
                    });
                    getData(result,arr);
                }
            });
        })
        function getData(xobj,yobj) {
            var myChart = echarts.init(document.getElementById('main'));
            var option = {
                title: {
                    text: '玩家最高在线',
                    left:'center',
                },
                tooltip: {
                    show: true
                },
                legend: {
                    data:['在线人数']
                },
                xAxis : [
                    {
                        type : 'category',
                        boundaryGap:true,
                        alignWithLabel:'auto',
                        name:'时点(24小时制)',
                        data : xobj
                    }
                ],
                yAxis : [
                    {
                        type : 'value',
                        name:'在线玩家',
                    }
                ],
                series : [
                    {
                        "name":"时点最高在线",
                        "type":"bar",
                        "data":yobj
                    }
                ]
            };
            // 为echarts对象加载数据
            myChart.setOption(option);
        }
    </script>
    <script type="text/javascript">
       function doSearch() {
           var timed = $("#timed").datebox('getValue');
           $.ajax({
               type:'post',
               url:"<?php echo U('Admin/Data/dataOnline');?>",
               dataType:'json',
               data:{'timed':timed},
               success: function (res) {
                   var data = JSON.parse(res);
                   var result = Object.keys(data);
                   result = result.sort();
                   var arr = [];
                   result.forEach(function (k) {
                       arr.push(data[k]);
                   });
                   getData(result,arr);
               }
           })
       }
       function getTime(obj) {
           var curr_time=new Date();
           var strDate=curr_time.getFullYear()+"-";
           strDate +=curr_time.getMonth()+1+"-";
           strDate +=curr_time.getDate()+"-";
           strDate +=" "+curr_time.getHours()+":";
           strDate +=curr_time.getMinutes()+":";
           $(obj).val(strDate);
       }

       function getLocalTimes() {
           var curr_time=new Date();
           var strDate=curr_time.getFullYear()+"-";
           strDate +=curr_time.getMonth()+1+"-";
           strDate +=curr_time.getDate();
           return strDate;
       }
    </script>
</body>
</html>