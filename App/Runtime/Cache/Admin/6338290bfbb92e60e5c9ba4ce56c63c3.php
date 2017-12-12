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
    <table id="dg" title="用户管理" toolbar="#tb"></table>
    <div id="tb">
        <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-add" onclick="addClick()">添加</a>
        <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-edit" onclick="modClick()">修改</a>
        <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-remove" onclick="delClick()">删除</a>
    </div>
    <div id="dlgEdit" class="easyui-dialog" style="padding:10px 20px;width:400px;"
         closed="true" buttons="#dlgEdit-buttons" modal="true">
        <form id="fmEdit" method="post" novalidate>
            <div class="easyui-panel" title="添加用户" style="padding:5px;">
                <div class="fitem">
                    <label>登录账号:</label>
                    <input name="admin_id" class="easyui-textbox" id="admin_id" data-options="height:30">
                </div>
                <div class="fitem">
                    <label>姓&emsp;&emsp;名:</label>
                    <input type="hidden" name="id" >
                    <input name="title" class="easyui-textbox" id="admin_name" data-options="height:30">
                </div>
                <div class="fitem">
                    <label>角&nbsp;&nbsp;色&nbsp;&nbsp;组:</label>
                    <select class="easyui-combobox" name="title" id='admin_role' data-options="height:30" style="width: 80px;">
                        <?php if(is_array($roles)): foreach($roles as $key=>$v): ?><option value="<?php echo ($v['id']); ?>"><?php echo ($v['title']); ?></option><?php endforeach; endif; ?>
                    </select>
                </div>
            </div>
        </form>
        <div id="dlgEdit-buttons">
            <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-ok" onclick="addUser()" style="width:90px">保存</a>
            <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dlgEdit').dialog('close')" style="width:90px">取消</a>
        </div>
    </div>

    <div id="modEdit" class="easyui-dialog" style="padding:10px 20px;width:400px;"
         closed="true" buttons="#modEdit-buttons" modal="true">
        <form id="modfmEdit" method="post" novalidate>
            <div class="easyui-panel" title="编辑用户" style="padding:5px;">
                <div class="fitem">
                    <label>登录账号:</label>
                    <input type="hidden" name="id" id="Id">
                    <input name="admin_id" class="easyui-textbox" disabled data-options="height:30">
                </div>
                <div class="fitem">
                    <label>姓&emsp;&emsp;名:</label>
                    <input type="hidden" name="id" >
                    <input name="name" class="easyui-textbox" id="mod_name" data-options="height:30">
                </div>
                <div class="fitem">
                    <label>角&nbsp;&nbsp;色&nbsp;&nbsp;组:</label>
                    <select class="easyui-combobox" name="title" id='mod_role' data-options="height:30" style="width: 80px;">
                        <?php if(is_array($roles)): foreach($roles as $key=>$v): ?><option value="<?php echo ($v['id']); ?>"><?php echo ($v['title']); ?></option><?php endforeach; endif; ?>
                    </select>
                </div>
            </div>
        </form>
        <div id="modEdit-buttons">
            <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-ok" onclick="editUser()" style="width:90px">保存</a>
            <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#modEdit').dialog('close')" style="width:90px">取消</a>
        </div>
    </div>

    <script type="text/javascript">
        $(function () {
            $("#dg").datagrid({
                loadMsg : '数据加载中……',
                url:"<?php echo U('Admin/Manger/mangerUser');?>",
                pagination:true,
                singleSelect:true,
                rownumbers:true,
                pageSize: 5, //页大小
                pageList: [5,10,15,20],
                pagePosition:'bottom',
                fit:true,
                fitColumn:true,
                striped: true,
                idField:'id',
                columns:[[
                    {field:'id',hidden:true},
                    {field:'admin_id',width:200,align:'center',title:'登录账号'},
                    {field:'name',width:200,align:'center',title:'姓名'},
                    {field:'title',width:200,align:'center',title:'角色名称'},
//                    {field:'tel',width:200,align:'tel',title:'电话'},
                    {field:'login_time',width:120,align:'center',title:'最后一次登录时间',formatter:
                        function (value) {
                            if(value == 0) {
                                value = 0;
                            } else {
                                value = getLocalTime(value);
                            }
                            return value;
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
        
        function addUser() {
            var admin_id = $.trim($("#admin_id").val());
            var admin_name = $.trim($("#admin_name").val());
            var admin_role = $("#admin_role").combobox("getValue");
            if(admin_name == '' || admin_id == '' || admin_role == '') {
                if(admin_id == '') {
                    $.messager.alert('温馨提示！','登录账号不能为空','error');
                    return false;
                }
                if(admin_name == '') {
                    $.messager.alert('温馨提示！','姓名不能为空','error');
                    return false;
                }
                if(admin_role == '') {
                    $.messager.alert('温馨提示！','请选择用户角色','error');
                    return false;
                }
            }else{
                var regs = /^(?=.*[a-zA-Z])(?=.*\d)[a-zA-Z\d]{6,10}$/;
                var tag = regs.test(admin_id);
                if(!tag) {
                    $.messager.alert('温馨提示！','请输入合法的登录账号，6-10位英文加数字','error');
                    return false;
                }else{
                    $.ajax({
                        type:'post',
                        url:"<?php echo U('Admin/Manger/addAdmin');?>",
                        dataType:'json',
                        data:{'admin_id':admin_id,'admin_name':admin_name,'admin_role':admin_role},
                        success: function (data) {
                            if(data == 'hasAdmin') {
                                $.messager.alert('温馨提示！','该管理员账号已存在，请重新输入','error');
                            }else if(data == 'success') {
                                $.messager.alert('温馨提示！','添加管理员成功','info',function () {
                                    $('#dlgEdit').dialog('close');
                                    $('#dg').datagrid('reload');
                                });
                            }else if(data == 'error') {
                                $.messager.alert('温馨提示！','网络出错，添加失败','error');
                            }
                        }
                    })
                }
            }
        }
        
//       修改
        function modClick() {
            var row = $('#dg').datagrid('getSelected');
            if(row.id == 1) {
                $.messager.alert('错误提示','管理员不能修改！','error');
                return false;
            }
            $('#modEdit').dialog('open').dialog('setTitle','编辑用户');
            $('#modfmEdit').form('load',row);
        }
        function editUser() {
            var Id = $.trim($("#Id").val());
            var admin_name = $.trim($("#mod_name").val());
            var admin_role = $("#mod_role").combobox("getValue");
            if(admin_name == '' || admin_role == '') {
                if(admin_name == '') {
                    $.messager.alert('温馨提示！','姓名不能为空','error');
                    return false;
                }
                if(admin_role == '') {
                    $.messager.alert('温馨提示！','请选择用户角色','error');
                    return false;
                }
            }else{
                $.ajax({
                    type:'post',
                    url:"<?php echo U('Admin/Manger/editAdmin');?>",
                    dataType:'json',
                    data:{'Id':Id,'admin_name':admin_name,'admin_role':admin_role},
                    success: function (data) {
                        if(data == 'success') {
                            $.messager.alert('温馨提示！','修改管理员信息成功','info',function () {
                                $('#modEdit').dialog('close');
                                $('#dg').datagrid('reload');
                            });
                        }else if(data == 'error') {
                            $.messager.alert('温馨提示！','网络出错，修改失败','error',function () {
                                $('#dg').datagrid('reload');
                            });
                        }
                    }
                })
            }
        }
        
        //删除
        function delClick() {
            var row = $('#dg').datagrid('getSelected');
            if(row.id == 1) {
                $.messager.alert('错误提示','管理员不能删除！','error');
                return false;
            }
            $.messager.confirm('删除提示','真的要删除此管理员吗?删除将不能再恢复！',function (r) {
                if(r) {
                    $.ajax({
                        type:'get',
                        url:"<?php echo U('Admin/Manger/delAdmin');?>?id="+row.id,
                        dataType:'json',
                        success: function (data) {
                            if(data == 'success') {
                                $.messager.alert('温馨提示！','删除成功','info',function () {
                                    $('#dg').datagrid('reload');
                                });
                            }else if(data == 'error'){
                                $.messager.alert('温馨提示！','网络出错，删除失败','error');
                            }else if(data == 'noaccess') {
                                $.messager.alert('温馨提示！','管理员不能删除','error',setTimeout(function () {
                                    window.location.href = "<?php echo U('Admin/Manger/mangerUser');?>";
                                },500));
                            }
                        }
                    })
                }
            })
        }
    </script>
</body>
</html>