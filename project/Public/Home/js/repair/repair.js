(function() {
    console.log("安装",installList, "报修",bindInfo);
    var province,city,area;//省市区
    // 设备id号
    var service_code;
    // 地区选择
    var province_id,city_id,area_id;
    // 选择结果
    var selectResult = "";
    // 服务类型
    var serviceType;
    // 点击上传图片
    $(".file_upload").click(function(){
        var len = $("#picShow>span").length;
        if(len == 3){
            layuiHint("暂时只支持三张图片！");
            return
        } 
        // takePicture(1);
        // 浏览上传图片 
        wxuploadimg(function(res){
            console.log('res: ',res);
            
            var $span = $("<span></span>");
            var $span1 = $("<span>X</span>");
            var $img = $('<img src="" alt="" index="" />');
            
            $img[0].width = "100%";
            $img[0].height = "90%";
            $img[0].src = res['src'];
            $(img)[0].attr("index", res.media_Id);
            
            $span1.addClass("delPic");
            $span1.css({zIndex: '999'});
            $span.append($span1);
            $span.append($img[0]);
            
            // 显示图片
            $('#picShow').append($span);
            // 待发送给后台的图片id
            // $('input[name="pic"]').val(res.media_Id);
            console.log("传给后台的id",res.media_Id);
        });
    })
    var formData = new FormData($('#form')[0]);
    // 删除图片
    $("#picShow").on("click", ".delPic", function(){
        $(this).parent().remove();
        $(this).siblings().attr("index", "");
        // formData.delete('pic');
    })
    // 点击服务选择
    $(".selService").on("click", function() {
        // 显示弹窗
        $(".serverZhe").css("display", "block");
    });
    // 弹窗列表选择
    $(".selectOption>ul").on("click", "li", function(e) {
        $(this).children("i").removeClass("iconfont icon-emptycircle").addClass("iconfont icon-selectcircle").parent().siblings().children("i").removeClass("iconfont icon-selectcircle").addClass("iconfont icon-emptycircle");
    })
    // 服务确认选择
    $(".serverZhe").on("click", ".confirmSelect", function() {
        // 选择完后隐藏
        $(".serverZhe").css("display", "none");
        selectResult = $(".serverZhe").find(".icon-selectcircle").siblings().text();
        serviceType = $(".serverZhe").find(".icon-selectcircle").siblings().attr("index");
        console.log(selectResult);
        $(".selService").html(
            "<span class='serviceVal'>服务选择：</span><span>"+ selectResult +"</span>"
        )
        $(".selDevice").html('<span>设备列表：</span><span class="deviceVal">请选择</span><i class="iconfont icon-down"></i>');
        if(selectResult == "安装") {
            $(".repairMidd").children("h3").text("备注：").end().children("textarea").attr("placeholder", "请输入备注");
            $(".repairMidd").children("p").css("display", "none").end().children(".repairPic").css("display", "none");
        }else {
            $(".repairMidd").children("h3").text("问题描述：").end().children("textarea").attr("placeholder", "请描述您所遇到的问题");
            $(".repairMidd").children("p").css("display", "block").end().children(".repairPic").css("display", "block");
        }
        service_code = "";
    });
    // 设备确认选择
    $(".deviceZhe").on("click", ".confirmDevice", function() {
        // 选择完后隐藏
        $(".deviceZhe").css("display", "none");
        var ZheVal = $(".deviceZhe").find(".icon-selectcircle").siblings().text();
        console.log("设备确认选择",ZheVal);
        // 设备id号
        service_code = $(".deviceZhe").find(".icon-selectcircle").parent().attr("index");
        if(ZheVal) {
            $(".selDevice").html(
                "<span>设备列表：</span><span class='deviceVal'>"+ $(".deviceZhe").find(".icon-selectcircle").siblings().text() +"</span>"
            )
        }else {
            $(".selDevice").html('<span>设备列表：</span><span class="deviceVal">请选择</span><i class="iconfont icon-down"></i>');
        }
        
        // 确认后切换相对应设备的用户的信息
        if(selectResult == "安装") {
            for(var i = 0; i < installList.length; i++) {
                if(service_code == installList[i].id) {
                    var userInfo = JSON.parse(installList[i].addressinfo);
                    console.log("该设备的用户的信息", userInfo);
                    $(".repaireName").text(userInfo.name);//写入用户名
                    $("input[name='phone']").val(userInfo.phone);//写入电话
                    $(".repaireAdd").html('<span>报修地址：</span><span id="areaID" class="areabtn">'+userInfo.province + userInfo.city + userInfo.area +'</span>');//写入省市区
                    $("textarea[name='address']").val(userInfo.address);//写入详细地址
                    // 省市区赋值
                    province = userInfo.province;
                    city = userInfo.city;
                    area = userInfo.area;
                    province_id = userInfo.province_id;
                    city_id = userInfo.city_id;
                    area_id = userInfo.area_id;
                }
            }
        }else {
            for(var i = 0; i < bindInfo.length; i++) {
                if(service_code == bindInfo[i].device_code) {
                    var userInfo = JSON.parse(bindInfo[i].addressinfo);
                    console.log("该设备的用户的信息", userInfo);
                    $(".repaireName").text(userInfo.name);//写入用户名
                    $("input[name='phone']").val(userInfo.phone);//写入电话
                    $(".repaireAdd").html('<span>报修地址：</span><span id="areaID" class="areabtn">'+userInfo.province + userInfo.city + userInfo.area +'</span>');//写入省市区
                    $("textarea[name='address']").val(userInfo.address);//写入详细地址
                    // 省市区赋值
                    province = userInfo.province;
                    city = userInfo.city;
                    area = userInfo.area;
                    province_id = userInfo.province_id;
                    city_id = userInfo.city_id;
                    area_id = userInfo.area_id;
                }
            }
        }
    })
    // 设备列表选择
    $(".serviceNum").on("click", function() {
        if(selectResult) {
            // 获取用户选择完的结果
            if(selectResult == "安装") {
                var content = "";
                if(installList.length) {
                    for(var i = 0; i < installList.length; i++) {
                        // 默认选中第一个 改变弹框选择内容
                        if(i == 0) {
                            content = content + '<li index="'+installList[i].id+'"><i class="iconfont icon-selectcircle"></i><span>'+ installList[i].gname +'</span></li>';
                        }else {
                            content = content + '<li index="'+installList[i].id+'"><i class="iconfont icon-emptycircle"></i><span>'+ installList[i].gname +'</span></li>';
                        }
                    }
                    content = content + '<p class="confirmDevice">确定</p>';
                }else {
                    content = "<li>无设备</li><p class='confirmDevice'>确定</p>";
                }
            }else if(selectResult == "维修" || selectResult == "维护"){
                var content = "";
                if(bindInfo.length) {
                    for(var i = 0; i < bindInfo.length; i++) {
                        if(i == 0) {
                            content = content + '<li index="'+bindInfo[i].device_code+'"><i class="iconfont icon-selectcircle"></i><span>'+ bindInfo[i].device_code +'</span></li>';
                        }else {
                            content = content + '<li index="'+bindInfo[i].device_code+'"><i class="iconfont icon-emptycircle"></i><span>'+ bindInfo[i].device_code +'</span></li>';
                        }
                    }
                    content = content + '<p class="confirmDevice">确定</p>'; 
                }else {
                    content = "<li>无设备</li><p class='confirmDevice'>确定</p>";
                }
                
            }
            $(".deviceZhe>ul").html(content);
            $(".deviceZhe").css("display", "block");
        }else {
            layuiHint('请先选择服务');
        }
        
    });
    
    // 蒙版点击隐藏
    $(".selectOption").on("click", function(e) {
        var ev = e || window.event;
        var target = ev.target || srcElement;
        if(target.nodeName.toLowerCase() == "div") {
            $(".selectOption").css("display", "none");
        }
    });
    
    // 选择地区
    $(".repaireAdd").on("click", ".areabtn", function(){
        $("#areaChoose").css({display: "block"});
    });
    // 获取地址数据
    function getAddress(parentid, callback){
        $.ajax({
            url: getURL("Home", "Address/getNextArea"),
            type: 'post',
            data: {parentid: parentid},
            success: function(res){
                console.log('res: ',res);
                if(res.status == 200){
                    callback(res.data);	
                }else{
                    layHint(res.msg);
                }
            },
            error: function(err){
                console.log('err: ',err);
                // layHint('系统遇到问题，请稍后再试');
            }
        })
    }
    
    // 获取省份数据
    getAddress(0, function(res){
        if(res.length){
            var html = '';
            for(var i=0; i<res.length; i++){
                html += '<p aid='+ res[i].id +'>'+ res[i].areaname +'</p>';
            }
            $(".province").html(html);
        }
    });
    
    // 选择省份，城市，区县
    $('.areaDiv').on('click', 'p', function(){
        var parent = $(this).parent()[0].className;
        var aid = $(this).attr('aid');	// 省份id
        var _this = $(this);
        console.log('parent: ',parent);
        
        // 根据省份id 获取城市数据
        getAddress(aid, function(res){
            if(parent == 'province'){
                province = _this.text();
                province_id = _this.attr('aid');
                console.log('cityres: ',res);
                // 点击省份
                console.log('city: ');
                var cityhtml = '';
                for(var i=0; i<res.length; i++){
                    cityhtml += '<p aid='+ res[i].id +'>'+ res[i].areaname +'</p>';
                }
                $('.city').html(cityhtml);
                for(var j=0; j<$('.province>p').length; j++){
                    $('.province>p').eq(j).removeClass('addrselect');
                }
                _this.attr('class','addrselect');
                
            }else if(parent == 'city'){
                city = _this.text();
                city_id = _this.attr('aid');
                // 点击城市
                console.log('areares: ',res);
                var areahtml = '';
                for(var i=0; i<res.length; i++){
                    areahtml += '<p aid='+ res[i].id +'>'+ res[i].areaname +'</p>';
                }
                $('.area').html(areahtml);
                for(var j=0; j<$('.city>p').length; j++){
                    $('.city>p').eq(j).removeClass('addrselect');
                }
                _this.attr('class','addrselect');
                
            }else if(parent == 'area'){
                area = _this.text();
                area_id = _this.attr('aid');
                // 点击区县
                console.log('area: ',_this);
                for(var j=0; j<$('.area>p').length; j++){
                    $('.area>p').eq(j).removeClass('addrselect');
                }
                _this.attr('class','addrselect');
                areaover();
            }
            console.log('province,city,area: ',province,city,area);
            console.log('province_id,city_id,area_id: ',province_id,city_id,area_id);
        })
    })
    
    // 选择完区县后自动消失
    function areaover(){
        if(!province){
            layuiHint('请选择省份');
            return
        }
        if(!city){
            layuiHint('请选择城市');
            return
        }
        if(!area){
            layuiHint('请选择区县');
            return
        }
        
        // $('.areabtn').val(province +' '+ city +' '+ area);
        
        $(".repaireAdd").html("<span>报修地址：</span><span id='areaID' class='areabtn'>"+ province +' '+ city +' '+ area +"</span>");

        setTimeout(function(){
            $('#areaChoose').fadeOut('fast');
        },300);
    }
    
    // 关闭地址选择
    $('.areaChoosebg').click(function(){
        $('#areaChoose').fadeOut('slow');
    })
    
    
    /*
    点击'提交'
    */
    $(".btn").on('touchend', function(){
        /**
        *安装/报修
        type    提交类型 工单类型(0：安装   1：维修   2：维护)
        kname   客户姓名   kphone  客户电话
        
        content:备注 (安装(非必要)/报修(必要))  device_code 设备id 
        
        province city district 三级地址
        
        province_id city_id district_id 三级地址的ID
        
        address 地址详情  pic 图片(安装(非必要)/报修(必要))
        
        */
        var phoneReg = /^1[3|4|5|8][0-9]\d{4,8}$/;
        var addressReg = /^(?=.*?[\u4E00-\u9FA5])[\dA-Za-z\u4E00-\u9FA5]{6,}/;

        var username = $(".repaireName").text();//用户名
        var userphone = $("input[name='phone']").val();//电话
        var Detailadd = $("#Laddr").val(); //详细地址
        var content = $("textarea[name='content']").val();//问题描述/备注
        console.log(province,city,area,province_id,city_id,area_id, service_code, username, userphone, Detailadd, content);
        var filePic = [];//存放图片的id
        var picImgs = $('#picShow>span>img')
        if(picImgs.length) {
            for(var i = 0; i < picImgs.length; i++) {
                filePic[i] = picImgs.eq(i).attr("index");
            }
        }
        console.log("图片id",filePic)
        //清空，防止多次点击提交
        formData.delete('device_code');
        formData.delete('username');
        formData.delete('userphone');
        formData.delete('Detailadd');
        formData.delete('content');
        formData.delete('province');
        formData.delete('city');
        formData.delete('area');
        formData.delete('province_id');
        formData.delete('city_id');
        formData.delete('area_id');
        formData.delete('pic');
        formData.delete('type');
        if($(".serviceVal").text() == "请选择") {
            console.log("请选择服务")
            layuiHint("请选择服务");
            return ;
        }
        if($(".deviceVal").text() == "请选择") {
            layuiHint("请选择设备");
            return ;
        }
        if(selectResult != "安装") {
            if(!content){
                layuiHint('请输入你所遇到的问题,谢谢!');
                return;
            }
            if(!$('#picShow>span').length){
                console.log('请上传报修图片！');
                layuiHint('请上传报修图片！');
                return;
            }
        }
        if(userphone) {
            if(!phoneReg.test(userphone)) {
                layuiHint("请输入正确的手机号码!");
                return;
            }
        }else {
            layuiHint("手机号码不能为空!");
            return;
        }
        if(!(province && city && area)) {
            layuiHint("请选择报修地址!");
            return;
        }
        if(Detailadd) {
            if(!addressReg.test(Detailadd)) {
                layuiHint("请输入正确的地址!");
                return;
            }
        }else {
            layuiHint("详细地址不能为空!");
            return;
        }
        if($(".areabtn").text() == "请选择") {
            layuiHint("请选择报修地址");
            return ;
        }
        //重新添加
        formData.append('device_code', service_code);
        formData.append('kname', username);
        formData.append('kphone', userphone);
        formData.append('address', Detailadd);
        formData.append('content', content);
        formData.append('province', province);
        formData.append('city', city);
        formData.append('district', area);
        formData.append('province_id', province_id);
        formData.append('city_id', city_id);
        formData.append('district_id', area_id);
        formData.append('pic', filePic);
        formData.append('type', serviceType);
        
        console.log(province, city, area, province_id, city_id, area_id, service_code, "formdata", formData)

        /*
        发送到后台
        */
        $.ajax({
            url: getURL("Home", "work/workAdd   "),
            type: 'post',
            async: false,
            data: formData,
            cache: false,
            processData: false,
            contentType: false,
            success: function(res){
                $(".btn").off('touchend');    // 解绑事件
                console.log('请求成功！', res);
                if(res.status == 200){
                    layuiHint(res.msg);
                    // 将当前历史记录替换为水机首页
                    history.pushState({}, null, getURL("Home", "Index/index"));
                    // setTimeout(function(){
                    //     location.href = getURL("Home", "VipCenter/minepack");
                    // },900);
                }else {
                    layuiHint(res.msg);
                }
            },
            error: function(res){
                console.log('请求失败！', res);
                layuiHint('系统遇到问题，请稍后重试！');
            }
        })
    });
})();
