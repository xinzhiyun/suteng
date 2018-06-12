(function() {
    console.log("typeList: ",typeList);
    var province,city,area;//省市区
    // 设备id号
    var service_code;
    // 地区选择
    var province_id,city_id,area_id;
    // 选择结果
    var selectResult = "";

    if(typeList.length){     // 遍历设备列表
        var deviceList = '';
        for(var i=0; i<typeList.length; i++){
            deviceList += '<li class="option">'+ typeList[i].typename +'</li>';
        }
        $('.select').html(deviceList);
    }
    // 显示设备选择面板
    $('.selDevice').on('touchend', function(){
        $('.select').fadeIn('fast');
    })
    // 设备列表选择
    $('.select').on('touchend', '.option', function(){
        for(var i=0; i<$('.select>.option').length; i++){
            $('.select>.option').eq(i).attr('class', 'option');
        }
        $(this).attr('class', 'option opselect');
        selectResult = $(this).text();
        $('.deviceVal').text(selectResult);
        $('.select').fadeOut('slow');
    })
    
    // 选择地区
    $(".repaireAdd").on("touchend", ".areabtn", function(){
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
    $('.areaDiv').on('touchend', 'p', function(){
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
    $('.areaChoosebg').on('touchend', function(){
        $('#areaChoose').fadeOut('slow');
    })
    
    // 扫描设备编码
    $('.devicep').on('touchend', function scancode() {
        wx.scanQRCode({
          needResult: 1, // 默认为0，扫描结果由微信处理，1则直接返回扫描结果，
          scanType: ["qrCode","barCode"], // 可以指定扫二维码还是一维码，默认二者都有
          success: function (res) {
            var result = res.resultStr; // 当needResult 为 1 时，扫码返回的结果
            if(result){
                $('.deviceid').val(result.substr(-16));
            }else{
                console.dir('扫描失败：10000');
            }
          }
        })
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
        var info = {};
        var device_code = $('.deviceid').val();
        var phoneReg = /^1[3|4|5|8][0-9]\d{4,8}$/;
        var addressReg = /^(?=.*?[\u4E00-\u9FA5])[\dA-Za-z\u4E00-\u9FA5]{6,}/;
        var username = $(".repaireName").val();//用户名
        var userphone = $("input[name='phone']").val();//电话
        var address = $("#Laddr").val(); //详细地址
        var anry_time = $('.anry_time').val();
        var anry_period = $('.anry_period').val();

        info = {
            device_code: device_code,   // 设备id
            device_type: selectResult,   // 设备id
            kname: username,         // 联系人
            kphone: userphone,       // 联系电话
            province: province,
            city: city,
            district: area,
            province_id: province_id,
            city_id: city_id,
            district_id: area_id,
            address: address,
            anry_time: anry_time,       // 预约年月日
            anry_period: anry_period    // 预约时间段
        };
        console.log('info: ',info);

        if($(".deviceVal").text() == "请选择") {
            layuiHint("请选择设备型号");
            return ;
        }
        if(!device_code){
            layuiHint('请扫描设备编码');
            return
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
        if(address) {
            if(!addressReg.test(address)) {
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

        if(!anry_time){
            layuiHint('请选择预约日期');
            return
        }
        if(!anry_period){
            layuiHint('请选择预约时间段');
            return
        }
        /*
        发送到后台
        */
        $.ajax({
            url: getURL("Home", "Work/workAddInatall"),
            type: 'post',
            data: info,
            success: function(res){
                $(".btn").off('touchend');    // 解绑事件
                console.log('请求成功！', res);
                if(res.status == 200){
                    layuiHint(res.msg);
                    // 将当前历史记录替换为水机首页
                    history.pushState({}, null, getURL("Home", "Index/index"));
                    setTimeout(function(){
                        location.href = getURL("Home", "VipCenter/minepack");
                    },2000);
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
