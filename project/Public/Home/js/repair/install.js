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

    // 设备确认选择
    $(".deviceZhe").on("click", ".confirmDevice", function() {

        // 选择完后隐藏
        $(".deviceZhe").css("display", "none");
        for(var i = 0; i < installList.length; i++) {
            if(service_code == installList[i].id) {
                var userInfo = JSON.parse(installList[i].addressinfo);
                console.log("该设备的用户的信息", userInfo);
                $(".repaireName").val(userInfo.name);//写入用户名
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
    })
    // 设备列表选择
    $(".serviceNum").on("click", function() {
        // 获取用户选择完的结果
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
        
        $(".repaireAdd").html("<span>联系地址：</span><span id='areaID' class='areabtn'>"+ province +' '+ city +' '+ area +"</span>");

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
        var info = {};
        var username = $(".repaireName").val();//用户名
        var userphone = $("input[name='phone']").val();//电话
        var Detailadd = $("#Laddr").val(); //详细地址
        var content = $("textarea[name='content']").val();//问题描述/备注
        console.log(province,city,area,province_id,city_id,area_id, service_code, username, userphone, Detailadd, content);  
        info = {
            device_code: device_code,
            username: username,
            userphone: userphone,
            province: province,
            city: city,
            area: area,
            province_id: province_id,
            city_id: city_id,
            area_id: area_id,
            Detailadd: Detailadd
        };

        if($(".deviceVal").text() == "请选择") {
            layuiHint("请选择设备");
            return ;
        }

        if(!username) {
            console.log("请输入名字")
            layuiHint("请输入名字");
            return ;
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
            layuiHint("请选择联系地址!");
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
            layuiHint("请选择联系地址");
            return ;
        }
        console.log('info: ',info);

        /*
        发送到后台
        */
        $.ajax({
            url: getURL("Home", "Work/workAdd"),
            type: 'post',
            async: false,
            data: info,
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
                    setTimeout(function(){
                        location.href = getURL("Home", "VipCenter/minepack");
                    },900);
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
