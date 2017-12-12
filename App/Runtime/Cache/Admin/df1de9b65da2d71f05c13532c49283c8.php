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
    <table id="dg" title="角色列表" toolbar="#tb"></table>
    <div id="tb">
        <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-add" onclick="addClick()">添加</a>
        <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-edit" onclick="modClick()">修改</a>
        <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-remove" onclick="delClick()">删除</a>
        <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-group_go" onclick="setClick()">设置权限</a>
    </div>
    <div id="dlgEdit" class="easyui-dialog" style="padding:10px 20px;width:400px;"
         closed="true" buttons="#dlgEdit-buttons" modal="true">
        <form id="fmEdit" method="post" novalidate>
            <div class="easyui-panel" title="添加角色" style="padding:5px;">
                <div class="fitem">
                    <label>角色名称:</label>
                    <input name="title" id="title" class="easyui-textbox" data-options="height:30">
                </div>
                <div class="fitem">
                    <label>角色描述:</label>
                    <input name="describe" id="describe" class="easyui-textbox" data-options="height:30" />
                </div>
                <div class="fitem">
                    <label>状态</label>
                    <select class="easyui-combobox" id="groupStatus" name="status" data-options="required:true,height:30,width:60,editable:false">
                        <option value="0">禁用</option>
                        <option value="1">启用</option>
                    </select>
                </div>
            </div>
        </form>
        <div id="dlgEdit-buttons">
            <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-ok" onclick="saveUser()" style="width:90px">保存</a>
            <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dlgEdit').dialog('close')" style="width:90px">取消</a>
        </div>
    </div>

    <!--修改-->
    <div id="mogEdit" class="easyui-dialog" style="padding:10px 20px;width:400px;"
         closed="true" buttons="#mogEdit-buttons" modal="true">
        <form id="modfmEdit" method="post" novalidate>
            <div class="easyui-panel" title="编辑角色" style="padding:5px;">
                <div class="fitem">
                    <label>角色名称:</label>
                    <input type="hidden" name="id" id="idm">
                    <input name="title" id="titlem" class="easyui-textbox" data-options="height:30">
                </div>
                <div class="fitem">
                    <label>角色描述:</label>
                    <input name="describe" id="describem" class="easyui-textbox" data-options="height:30" />
                </div>
                <div class="fitem">
                    <label>状态</label>
                    <select class="easyui-combobox" id="groupStatusm" name="status" data-options="required:true,height:30,width:60,editable:false">
                        <option value="0">禁用</option>
                        <option value="1">启用</option>
                    </select>
                </div>
            </div>
        </form>
        <div id="mogEdit-buttons">
            <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-ok" onclick="editUser()" style="width:90px">保存</a>
            <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#mogEdit').dialog('close')" style="width:90px">取消</a>
        </div>
    </div>

    <!--权限设置-->
    <div id="dlgAccess" class="easyui-dialog" style="padding:10px 20px;width:400px;"
    closed="true" buttons="#dlgAccess-buttons" modal="true">

        <ul id="tree" class="easyui-tree">

        </ul>
        <div id="dlgAccess-buttons">
        <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-ok" onclick="accessUser()" style="width:90px">保存</a>
        <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dlgAccess').dialog('close')" style="width:90px">取消</a>
    </div>
    </div>
    <script type="text/javascript">
        $(function () {
            $("#dg").datagrid({
                loadMsg : '数据加载中……',
                url:"<?php echo U('Admin/Manger/getMangerList');?>",
                pagination:true,
                singleSelect:true,
                rownumbers:true,
                pageSize: 25, //页大小
                pageList: [25,50,75,100],
                pagePosition:'bottom',
                fit:true,
                fitColumn:true,
                striped: true,
                idField:'id',
                columns:[[
                    {field:'id',hidden:true},
                    {field:'title',width:200,align:'center',title:'角色名称'},
                    {field:'describe',width:200,align:'center',title:'角色描述'},
                    {field:'status',width:120,align:'center',title:'状态',formatter:
                        function (value) {
                            if(value == 1) {
                                return '<span style="color: green">√</span>';
                            }else{
                                return '<span style="color: red">×</span>';
                            }
                        }
                    }
                ]]
            })
        });
        function addClick() {
            $('#dlgEdit').dialog('open').dialog('setTitle','角色添加');
            $('#fmEdit').form('clear');
            $('#groupStatus').combobox('select',1);
        }
        function saveUser() {
            var title = $.trim($("#title").val());
            var describe = $.trim($("#describe").val());
            var status = $("#groupStatus").combobox("getValue")
            if(title == '' || describe == '') {
                $.messager.alert('温馨提示！','角色名称或角色描述不能为空','error');
                return false;
            }else{
                if(title.length<2 || describe.length<2) {
                    $.messager.alert('温馨提示！','请输入合法的角色名称或角色描述','error');
                    return false;
                }else{
                    $.ajax({
                        type:'post',
                        url:"<?php echo U('Admin/Manger/addGroup');?>",
                        dataType:'json',
                        data:{'title':title,'describe':describe,'status':status},
                        success: function (data) {
                            if(data == 'success') {
                                $.messager.alert('温馨提示！','角色添加成功','info',function () {
                                    $('#dlgEdit').dialog('close');
                                    $('#dg').datagrid('reload');
                                });
                            }else{
                                $.messager.alert('温馨提示！','角色添加失败','error');
                            }
                        }
                    })
                }
            }
        }
        
        function delClick() {
            var row = $('#dg').datagrid('getSelected');
            if(row.id == 1) {
                $.messager.alert('错误提示','管理员不能删除！','error');
                return false;
            }
            $.messager.confirm('删除提示','真的要删除此角色组吗?删除将不能再恢复！',function (r) {
                if(r) {
                    $.ajax({
                        type:'get',
                        url:"<?php echo U('Admin/Manger/delGroup');?>?id="+row.id,
                        dataType:'json',
                        success: function (data) {
                            if(data == 'success') {
                                $.messager.alert('温馨提示！','角色删除成功','info',function () {
                                    $('#dg').datagrid('reload');
                                });
                            }else if(data == 'error'){
                                $.messager.alert('温馨提示！','角色删除失败','error');
                            }else if(data == 'noaccess') {
                                $.messager.alert('温馨提示！','管理员不能删除','error',setTimeout(function () {
                                    window.location.href = "<?php echo U('Admin/Manger/mangerList');?>";
                                },500));
                            }
                        }
                    })
                }
            })
        }
        function modClick() {
            var row = $('#dg').datagrid('getSelected');
            if(row.id == 1) {
                $.messager.alert('错误提示','管理员不能修改！','error');
                return false;
            }
            $('#mogEdit').dialog('open').dialog('setTitle','编辑角色');
            $('#modfmEdit').form('load',row);
        }
        function editUser() {
            var title = $.trim($("#titlem").val());
            var describe = $.trim($("#describem").val());
            var status = $("#groupStatusm").combobox("getValue")
            var id = $.trim($("#idm").val());
            if(title == '' || describe == '') {
                $.messager.alert('温馨提示！','角色名称或角色描述不能为空','error');
                return false;
            }else{
                if(title.length<2 || describe.length<2) {
                    $.messager.alert('温馨提示！','请输入合法的角色名称或角色描述','error');
                    return false;
                }else{
                    $.ajax({
                        type:'post',
                        url:"<?php echo U('Admin/Manger/editGroup');?>",
                        dataType:'json',
                        data:{'title':title,'describe':describe,'status':status,'id':id},
                        success: function (data) {
                            if(data == 'success') {
                                $.messager.alert('温馨提示！','角色修改成功','info',function () {
                                    $('#mogEdit').dialog('close');
                                    $('#dg').datagrid('reload');
                                })
                            }else{
                                $.messager.alert('温馨提示！','角色修改失败','error');
                            }
                        }
                    })
                }
            }
        }
        function setClick() {
            var row = $('#dg').datagrid('getSelected');
            if(row.id == 1) {
                $.messager.alert('错误提示','管理员权限不能更改！','error');
                return false;
            }
            $("#tree").tree({
                url:"<?php echo U('Admin/Manger/getTree');?>",
                checkbox: true,
                cascadeCheck:false,//层叠选中
                lines:true,
                queryParams:{gid:row.id}, //传的参数
                onCheck:function (node,checked) {
                    var parentNode = $("#tree").tree('getParent',node.target);
                    if(checked == true) {
                        if(parentNode != null) {
                            var parentNode1 = $("#tree").tree('getParent',parentNode.target);
                            $("#tree").tree('check',parentNode.target);
                        }
                    }else{
                        var childNode = $("#tree").tree('getChildren',node.target);
                        if(childNode.length>0) {
                            for(var i=0;i<childNode.length;i++) {
                                $("#tree").tree('uncheck',childNode[i].target)
                            }
                        }
                    }
                },
                onLoadError: function (data) {
                    $.messager.confirm('错误提示',data,function (r) {
                        $("#dlgAccess").dialog('close');
                    })
                }
            });
            $('#dlgAccess').dialog({
                title:'权限设置&emsp;&nbsp;<span style="color: red">'+row.title+'</span>',
                resizable:true,
                onClose:function () {
                    $("#tree").tree('collapseAll')
                }
            }).dialog('open');
        }
        
//        function setClick() {
//            var row = $('#dg').datagrid('getSelected');
//            $('#tree').tree({
//                url:"<?php echo U('Admin/Manger/getTree');?>",
//                checkbox:true,
//                checkOnSelect:true,
//                lines:true,
//                queryParams:{gid:row.id}, //传的参数
//                onlyLeafCheck:true,
//                cascadeCheck:true,
//                loadFilter:function (data,patent) {
//                    console.log(patent)
//                }
//
//            });
//            $('#dlgAccess').dialog({
//                title:'权限设置&emsp;&nbsp;<span style="color: red">'+row.title+'</span>',
//                resizable:true,
//                onClose:function () {
//                    $("#tree").tree('collapseAll')
//                }
//            }).dialog('open');
//        }
        
//        function getChecked() {
//            var arr = [];
//            var checkeds = $("#tree").tree('getChecked','checked');
//            for(var i=0;i<checkeds.length;i++) {
//                arr.push(checkeds[i].id);
//            }
//        }
        
        function accessUser() {
            var row = $('#dg').datagrid('getSelected');
            if(row.id == 1) {
                $.messager.alert('错误提示','管理员权限不能更改！','error');
                return false;
            }
            if(!row.id) {
                $.messager.alert('错误提示','不能编辑空的角色权限！','error');
                return false;
            }
            var node = $("#tree").tree('getChecked');
            var rule = [];
            for(var i=0;i<node.length;i++) {
                rule.push(node[i].id);
            }
            var rules = rule.join(',');
            $.ajax({
                type:'post',
                url:"<?php echo U('Admin/Manger/setAccess');?>",
                dataType:'json',
                data:{'id':row.id,'rules':rules},
                success: function (data) {
                    if(data == 'success') {
                        $.messager.alert('温馨提示！','权限设置成功','info',function () {
                            $('#dlgAccess').dialog('close');
                            $('#dg').datagrid('reload');
                        })
                    }else{
                        $.messager.alert('温馨提示！','权限设置失败','error');
                    }
                }
            })
        }
    </script>
</body>
</html>