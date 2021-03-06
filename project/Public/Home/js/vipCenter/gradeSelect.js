// 选中的会员等级价格
var selectPrice;
// 选中会员名称
var selectName;
// 选中会员识别号
var selectStatus
var gradePanduan = $(".gradePanduan").val();//获取会员等级
$(".goldGrade").on("touchend", function() {

    // 钻石会员 其他会员不可以选
    if(gradePanduan == 0) {
        parent.layer.msg('您已经是最高级了，亲！');
        return false;
    }
    // 黄金会员 选中黄金和青铜会员不可以选
    if((gradePanduan == 1 && $(this).attr("data") == 1) || (gradePanduan == 1 && $(this).attr("data") == 2)) {
        parent.layer.msg('您只能升级为钻石会员！');
        return false;
    }
    // 青铜会员 选中青铜不可以选
    if(gradePanduan == 2 && $(this).attr("data") == 2) {
        parent.layer.msg('您只能升级到钻石或黄金会员！');
        return false;
    }
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
        selectName = $(this).children("p").children(".gradeName").html()
        selectStatus = parseInt($(this).children('.goldInfo').children(".gradeStatus").children().val())

    }else {
        $(this).removeClass("selectChange").siblings().removeClass("selectChange");
        $(".confirmBtn > p").css("background", "rgb(69, 69, 69, .5)");
        $(this).children(".goldInfo").css("display", "none").end().siblings().children(".goldInfo").css("display", "none");
        $(this).attr("index", "0").siblings().attr("index", "0");
        // 选中确认变色 否则不变
        $(".confirmBtn > p").css("background", "#A2A2A2");

    }
    for(var i=0; i<$(".goldGrade").length; i++){
        $(".goldGrade").eq(i).removeClass('selectChange');
        $(".goldGrade").eq(i).find(".goldInfo").css("display", "none");
    }
    $(this).addClass("selectChange");
    $(this).find(".goldInfo").css("display", "block");
})
// 阻止事件冒泡
$(".goldInfo").on("touchstart", function(e) {
    e.stopPropagation();
});
// 点击叉叉小图标
$(".icon-shibai").on("touchstart", function() {
    $(this).parent().css("display", "none");
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
