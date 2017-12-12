/**
 * Created by Administrator on 2017/10/23.
 */
$(function () {
    $("#qure").on("click",function () {
        $("#mask").css("display",'none');
    })
})

function notEmpty(obj,msg) {
    if($.trim(obj) == '') {
        $("#mask_info").html(msg);
        $("#mask").css("display",'block');
        return false;
    }
}
function check_reg(obj,msg) {
    var regs = /^(?=.*[a-zA-Z])(?=.*\d)[a-zA-Z\d]{6,10}$/;
    if(!regs.test($.trim(obj))){
        $("#mask_info").html(msg);
        $("#mask").css("display",'block');
        return false;
    }
}
function altMsg(msg,obj) {
    $(obj).val('');
    $("#mask_info").html(msg);
    $("#mask").css("display",'block');
    return false;
}

function showMsg(msg) {
    $("#mask_info").html(msg);
    $("#mask").css("display",'block');
}

function settime(obj,countdown) {
    if(countdown == 0) {
        obj.removeAttribute("disabled");
        obj.value = '重新获取验证码';
        return;
    }else{
        obj.setAttribute("disabled","disable");
        obj.value = "重新发送（"+countdown+"）";
        countdown--;
    }
    setTimeout(function () {
        settime(obj,countdown);
    },1000);
}

function checkId(id,text) {
    var regs = /^\d{6}$/;
    var tags = regs.test(id);
    if(!tags) {
        showMsg(text);
        return false;
    }
}
