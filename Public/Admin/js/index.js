$(function(){
	$(".side-menu>li>a").click(function(){
		$(this).siblings("ul").slideToggle();
		$(this).parent("li").addClass("current").siblings("li").removeClass("current");
	
		
		if($(this).siblings("ul").css("display","block")){
			$(".side-menu>li>a").find("span").next("i").removeClass("icon-angle-down");
			$(this).find("span").next("i").addClass("icon-angle-down");
		}else{
			$(this).find("span").next("i").removeClass("icon-angle-down");
			$(this).find("span").next("i").addClass("icon-angle-left");
		}
	});

	//显示的子菜单
	var flag = true;
	$(".minimalize-styl-2").click(function(){
		if(flag){
			$(".sub-menu").css("display","block");
			flag = false;
		}else{
			$(".sub-menu").css("display","none");
			flag = true;
		}
	})

	$(".side-menu ul li a").on("click",function() {
		$(".side-menu ul li a").removeClass("current2");
		$(this).addClass("current2");
		var showHref=$(this).data("href");
		var strHref=showHref.substr(0,showHref.length-5);
		$("#showContentIframe").attr("src",strHref);
		
		var txt = $(this).text();
		$(".matter").text(txt);
	
	});
	
	
	$(".sub-menu .side-menu ul li a").on("click",function(){
		$(".sub-menu").css("display","none");
		flag = true;
	});
	
	$("#topnav ul>li>a").on("click",function(){
		var showHref=$(this).data("href");
		var strHref=showHref.substr(0,showHref.length-5);
		$("#showContentIframe").attr("src",strHref);

		var txt = $(this).text();
		$(".matter").text(txt);
	});
    //
	// var roleValue = $.trim($("#adminrole").text());
	// if(roleValue == 0) {
	// 	$("#adminrole").closest("div").hide();
	// }

	var clubMember = $.trim($(".clubMember").eq(0).text());
	if(clubMember == '' || clubMember == 'GAME_CLUB') {
		$(".clubMember").hide();
	}

    var groupMember = $.trim($(".groupMember").eq(0).text());
    if(groupMember == '' || groupMember == 'GAME_GROUP') {
        $(".groupMember").hide();
    }

    var onLinePay = $.trim($("li.onLinePay").eq(0).text());
    if(onLinePay == '' || onLinePay == 'GAME_ONLINEPAY') {
    	$("li.onLinePay").hide();
	}

    var payRank = $.trim($("li.payRank").eq(0).text());
    // console.log(payRank);
    if(payRank == '' || payRank == "GAME_RANK") {
        $("li.payRank").hide();
    }

	var feefback = $.trim($("li.feedback").eq(0).text());
	if(feefback == '' || feefback == 'GAME_FEEDBNCK') {
		$("li.feedback").hide();
	}

    var gameRecord = $.trim($("li.gameRecord").eq(0).text());
    if(gameRecord == '' || gameRecord == 'GAME_RECORD_LIST') {
        $("li.gameRecord").hide();
    }

	$(".icon-backward").on('click',function(){
		history.back();
	});

	$(".icon-forward").on('click',function () {
		history.forward();
    });


    var listTab=$("#listTab>li");

    var arr = [];
    for(var i=0;i<listTab.length;i++) {
        arr.push(listTab[i]);
    }

    for(var k= 0;k<arr.length;k++) {
        if(arr[k].innerText == '') {
            $(arr[k]).hide();
        }
    }
});