// 选中的会员等级价格
var selectPrice;
$(".goldGrade").on("touchstart", function() {
    if($(this).attr("index") == "0") {
        // 添加类名 改变背景颜色 
        $(this).addClass("selectChange").siblings().removeClass("selectChange");
        // 通过改变index标识
        $(this).attr("index", "1").siblings().attr("index", "0");
        // 点击出现会员说明
        $(this).children(".goldInfo").css("display", "block").end().siblings().children(".goldInfo").css("display", "none");
        // 选中确认变色 否则不变
        $(".confirmBtn > p").css("background", "rgb(46, 182, 170)");
        selectPrice = parseInt($(this).children(".selectPrice").html()) + ".00";
    }else {
        $(this).removeClass("selectChange").siblings().removeClass("selectChange");
        $(".confirmBtn > p").css("background", "rgb(69, 69, 69, .5)");
        $(this).children(".goldInfo").css("display", "none").end().siblings().children(".goldInfo").css("display", "none");
        $(this).attr("index", "0").siblings().attr("index", "0");
        // 选中确认变色 否则不变
        $(".confirmBtn > p").css("background", "#A2A2A2");
    }
})
// 阻止事件冒泡
$(".goldInfo").on("touchstart", function(e) {
    e.stopPropagation();
});
// 点击叉叉小图标
$(".icon-shibai").on("touchstart", function() {
    $(this).parent().css("display", "none");
});

// 点击提交
$(".confirmBtn").on("touchstart", function() {
    if($(".goldGrade").hasClass("selectChange")) {
        // 显示付款面板
        $(".payKuan").css("display", "block");
        // 选中会员价格
        $(".selectFinall").html(selectPrice);
    }
});
// 点击面板叉叉小图标
$(".icon-chacha").on("touchstart", function() {
    $(".payKuan").css("display", "none");
});

// 选择付款
$(".payWay").on("touchstart", function() {
    // 选择付款方式
    $(this).children(".xuanzhong").addClass("icon-xuanzhongduigou").css("color", "#56AAF1").removeClass("icon-weixuanzhong").end().siblings().children(".xuanzhong").addClass("icon-weixuanzhong").removeClass("icon-xuanzhongduigou").css("color", "#646664");

})
