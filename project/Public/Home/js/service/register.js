(function() {
    var codeFlag = false;//存放验证码
    var vm = new Vue({
        el: ".main",
        data() {
            return {
                // 服务站信息
                serviceInfos: {
                    station: "请选择区域",
                    detailAddress: "",
                    stationer: ""
                },
                // 个人信息
                userInfos: {
                    name: "",
                    phone: "",
                    idcard: "" 
                },
                // 公司信息
                companyInfos: {
                    comName: "",
                    comUser: "",
                },
                // 账号设置
                accountInfos: {
                    account: "",
                    password: "",
                    confirmPassword: ""
                },
                areaDis: false,//地区显示板 
                areaProvince: "",//省
                areaCity: "",//市
                area: "",//区
                // province_id: "",
                // city_id: "",
                // area_id: "",
                pics: [],//存放图片的路径
                aleady: false,//服务站是否被注册
                areachoose: "",//选择相对应的服务站
                stationPhone: "",//客服电话
                ifstation: "请选择服务站",
                stationId: "",//服务站id
                imgURL: []
            }
        },
        created() {
            var vm = this;
            // 封装layer
            layui.use("layer", function() {
                layer = layui.layer;
            });
            var isIsset = sessionStorage.getItem("saveAllInfo");
            if(isIsset == '1') {
                var infos = JSON.parse(sessionStorage.getItem("allInfo"))
                console.log(JSON.parse(sessionStorage.getItem("allInfo")));
                vm.serviceInfos.detailAddress = infos.serviceInfos.detailAddress;//详细地址
                vm.serviceInfos.stationer = infos.serviceInfos.stationer; //客服电话
                vm.userInfos = infos.userInfos;//用户信息
                vm.companyInfos = infos.companyInfos;//公司信息
                vm.accountInfos.account = infos.accountInfos.account;//账号设置
            }
        },
        methods: {
            // 选择地区
            chooseArea() {
                var vm = this;
                // 获取省份数据
                vm.getAddress(0, function(res){
                    console.log("省市区", res);
                    if(res.length){
                        vm.areaProvince = res;
                        vm.areaCity = "";
                        vm.area = "";
                        vm.areaDis = true;
                    }
                });
            },
            // 获取省份数据
            getAddress(parentid, callback) {
                $.ajax({
                    url: getURL("Home", "ServiceLogin/getNextArea"),
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
            },
            // 点击暗处隐藏
            areaY(e) {
                var ev = ev || window.event;  
                var target = ev.target || ev.srcElement;
                var className =  $(target).prop("className");//获取类名
                if(className == "areaChoosebg") {
                    this.areaDis = false;
                }
            },
            // 选择地区
            getArea(e) {
                var vm = this;
                var ev = ev || window.event;
                var target = ev.target || ev.srcElement;
                var obj = target.nodeName.toLowerCase();//获取到的节点
                if(obj == "p") {
                    console.log($(target).text());
                    vm.getAddress($(target).attr("aid"), function(res) {
                        var className = $(target).parent().prop("className");
                        if(className == "province") {
                            vm.areaCity = res;
                            
                            vm.province = $(target).text();
                            // vm.province_id = $(target).attr("aid");
                        }else if(className == "city") {
                            vm.area = res;
                            
                            vm.city = $(target).text();
                            // vm.city_id = $(target).attr("aid");
                        }else if(className == "area") {
                            vm.area = $(target).text();
                            // vm.area_id = $(target).attr("aid");
                            vm.serviceInfos.station = vm.province + ' ' + vm.city + ' ' + vm.area;
                            
                            $(".icon-right").css("display", "none");
                            $(".areabtn").css("color", "#373737");
                            setTimeout(function(){
                                vm.areaDis = false;
                            },300);
                            vm.areachoose = "";
                            // console.log(vm.province, vm.city, vm.area, vm.province_id, vm.city_id, vm.area_id)
                            data = {
                                province_id: vm.province_id,
                                city_id : vm.city_id,
                                district_id : vm.area_id
                            }
                            vm.getStation(data, function(res) {
                                console.log("服务站",res.data);
                                if(res.data.length) {
                                    vm.ifstation = "请选择服务站";
                                    vm.areachoose = res.data;
                                }else {
                                    // 无服务站操作
                                    setTimeout(function() {
                                        vm.aleady = true;
                                        vm.stationPhone = "tel:" + res.phone;
                                    }, 300);
                                    vm.ifstation = "暂无服务站";
                                    vm.areachoose = "";
                                }
                            })
                        }
                    })
                }
            },
            // 选择男女
            chooseSex() {
                var ev = ev || window.event;  
                var target = ev.target || ev.srcElement;
                var tagName = target.nodeName.toLowerCase()
                if(tagName == "span") {
                    var tagObj = $(target).children("i").prop("className");
                    if(tagObj == "iconfont icon-emptycircle") {
                        $(target).children("i").removeClass("iconfont icon-emptycircle").addClass("iconfont icon-selectcircle");
                        $(target).siblings("span").children("i").removeClass("iconfont icon-selectcircle").addClass("iconfont icon-emptycircle");
                    }
                }else if(tagName == "i") {
                    var tagObj = $(target).prop("className")
                    if(tagObj == "iconfont icon-emptycircle") {
                        $(target).removeClass("iconfont icon-emptycircle").addClass("iconfont icon-selectcircle");
                        $(target).parent().siblings("span").children("i").removeClass("iconfont icon-selectcircle").addClass("iconfont icon-emptycircle");
                    }
                }
            },
            // 上传图片
            imageChoose() {
                var ev = ev || window.event;  
                var target = ev.target || ev.srcElement;
                console.log("target", target);
                var _this = this;
                var len = $("#picShow>span").length;
                if(len == 3){
                    layuiHint("暂时只支持三张图片！");
                    return
                }
                _this.pics = [];   // 初始化
                var nums = 3 - len;  // 最多三张图片
                if(window.__wxjs_is_wkwebview){
                    nums = 1 //苹果只能上传一张
                }
                // 浏览上传图片 
                wxuploadimg(nums, function(res){
                    console.log('res: ',res);
                    for(var i=0; i<res.length; i++){
                        console.log('res: ',res[i]);
                        if(res[i].media_Id){
                            (function locfn(media_Id, src, i){
                                console.log("传给后台的id",media_Id);
                                _this.pics.push(media_Id);
                            })(res[i].media_Id, res[i].src, i)
                        }
                    }
                    setTimeout(function(){
                        _this.picUpload(target);
                    },0)
                })
            },
            // 图片显示
            picUpload(target) {
                var _this = this;
                $.ajax({
                    url: getURL('Home', 'ServiceLogin/picUpload'),
                    type: 'post',
                    data: {pic: _this.pics},
                    success: function(res){
                        console.log('res: ',res);
                        if(res.status == 200){
                            var picList = '';
                            picList = JSON.parse(res.data.pic);
                            for(var i=0; i<picList.length; i++){
                                (function upfn(src){
                                    console.log("图片src", src);
                                    var $span = $("<span class='bottx'></span>");
                                    var $span1 = $("<span @click='removePic($event)' class='topx'>x</span>");
                                    var $img = $('<img src="" alt="" index="" />');
                                    $img[0].width = "100%";
                                    $img[0].height = "90%";
                                    $img[0].src = '/Public'+src;
                                    
                                    $span1.addClass("delPic");
                                    $span1.css({zIndex: '9'});
                                    $span.append($span1);
                                    $span.append($img[0]);
                                    // 显示图片
                                    $(target).siblings(".picShow").append($span);
                                    
                                })(picList[i])
                            }
                        }else{
                            layuiHint(res.msg);
                        }
                    },
                    error: function(err){
                        console.log('err: ',err);
                        layuiHint('系统出错，请稍后再试');
                    }
                })
            },
            // 删除图片
            removePic() {
                var ev = ev || window.event;
                var target = ev.target || ev.srcElement;
                $(target).parent().remove();
            },
            // 获取服务站
            getStation(data, callback) {
                console.log("开始获取")
                $.ajax({
                    url: getURL("Home", "ServiceLogin/getService"),
                    type: "post",
                    data: data,
                    success: function(res) {
                        console.log("res", res);
                        if(res.status == 200) {
                            callback(res);
                        }else {
                            layer.msg(res.msg);
                        }
                    },
                    error: function(res) {
                        
                    }
                })
            },
            // 选择服务站
            stachoose(e) {
                var ev = ev || window.event;  
                var target = ev.target || ev.srcElement;
                this.stationId = $(target).val();//服务站id
                $("#sel").css("color", "#373737");
            },
            // 提交审核
            verify(val) {
                var vm = this;
                console.log(
                    "开通区域",vm.serviceInfos.station,
                    "详细地址",vm.serviceInfos.detailAddress,
                    "服务站客服",vm.serviceInfos.stationer,
                    "联系人",vm.userInfos.name,
                    "联系电话",vm.userInfos.phone,
                    "身份证号码",vm.userInfos.idcard,
                    "所属公司",vm.companyInfos.comName,
                    "公司法人",vm.companyInfos.comUser,
                    "账号",vm.accountInfos.account,
                    "密码",vm.accountInfos.password,
                    "确认密码",vm.accountInfos.confirmPassword
                );
                if(!vm.serviceInfos.station || vm.serviceInfos.station == "请选择区域") {
                    layer.msg("请选择区域");
                    return;
                }
                if(vm.stationId == "") {
                    layer.msg("请选择服务站");
                    return;
                }
                if(!vm.serviceInfos.detailAddress) {
                    layer.msg("请填入详细地址");
                    return;
                }
                if(!vm.serviceInfos.stationer) {
                    layer.msg("请输入客服电话");
                    return;
                }else {
                    if(!phoneReg.test(vm.serviceInfos.stationer)) {
                        layer.msg("请输入正确的电话号码");
                        return;
                    }
                }
                if(!vm.userInfos.name) {
                    layer.msg("姓名不能为空");
                    return;
                }
                if(!vm.userInfos.phone) {
                    layer.msg("请输入联系电话");
                    return;
                }else {
                    if(!phoneReg.test(vm.serviceInfos.stationer)) {
                        layer.msg("请输入正确的电话号码");
                        return;
                    }
                }
                if(!vm.userInfos.idcard) {
                    layer.msg("请输入身份证号码");
                    return;
                }else {
                    if(!cardCheck(vm.userInfos.idcard)) {
                        layer.msg("请输入正确的身份证号码");
                        return;
                    }
                }
                if(!vm.companyInfos.comName) {
                    layer.msg("请输入公司名称");
                    return;
                }
                if(!vm.companyInfos.comUser) {
                    layer.msg("请输入公司法人");
                    return;
                }
                if(!$(".yinye").children().length) {
                    layer.msg("请上传营业执照")
                    return;
                }
                if(!$(".hezuo").children().length) {
                    layer.msg("请上传合作协议")
                    return;
                }
                if(!vm.accountInfos.account) {
                    layer.msg("请输入账号");
                    return;
                }
                if(!vm.accountInfos.password) {
                    layer.msg("请输入密码");
                    return;
                }
                if(vm.accountInfos.confirmPassword != vm.accountInfos.password) {
                    layer.msg("两次密码不一样");
                    return;
                }
                // 男/女
                var sex = $(".icon-selectcircle").parent().text();
                sex = sex == "男" ? 1: 2;
                console.log(sex);
                // 营业执照图片
                console.log($(".yinye").find("img").length);
                var yinyePic = [];
                for(var i = 0; i < $(".yinye").find("img").length; i++) {
                    yinyePic.push($($(".yinye").find("img")[i]).prop("src"))
                }
                console.log("yinyepic", yinyePic);
                // 合作协议图片
                var hezuoPic = [];
                for(var i = 0; i < $(".hezuo").find("img").length; i++) {
                    hezuoPic.push($($(".hezuo").find("img")[i]).prop("src"))
                }
                console.log("hezuo", hezuoPic);
                // 保存 type - 0  审核 type - 1 
                if(val == 1) {
                    var data = {
                        sid: vm.stationId,//服务站id
                        name: vm.userInfos.name,//姓名
                        sex: sex,//性别
                        phone: vm.userInfos.phone,//手机
                        idcard: vm.userInfos.idcard,//身份证号
                        province: vm.province,//省
                        city: vm.city,//市
                        district: vm.area,//区
                        addressinfo: vm.serviceInfos.detailAddress,//地址详情
                        telephone: vm.serviceInfos.stationer,//客服电话
                        account: vm.accountInfos.account,//账号
                        password: vm.accountInfos.password,//密码
                        repassword: vm.accountInfos.confirmPassword,//确认密码
                        company: vm.companyInfos.comName,//公司名称
                        legal: vm.companyInfos.comUser,//公司法人
                        business: yinyePic,//营业照片
                        agreement: hezuoPic,//协议照片
                        type: 1
                    }
                }else {
                    var data = {
                        // sid: vm.stationId,//服务站id
                        name: vm.userInfos.name,//姓名
                        sex: sex,//性别
                        phone: vm.userInfos.phone,//手机
                        idcard: vm.userInfos.idcard,//身份证号
                        // province: vm.province,//省
                        // city: vm.city,//市
                        // district: vm.area,//区
                        addressinfo: vm.serviceInfos.detailAddress,//地址详情
                        telephone: vm.serviceInfos.stationer,//客服电话
                        account: vm.accountInfos.account,//账号
                        // password: vm.accountInfos.password,//密码
                        // repassword: vm.accountInfos.confirmPassword,//确认密码
                        company: vm.companyInfos.comName,//公司名称
                        legal: vm.companyInfos.comUser,//公司法人
                        business: yinyePic,//营业照片
                        agreement: hezuoPic //协议照片
                    }
                }
                console.log(data);
                $.ajax({
                    url: getURL("Home", "ServiceLogin/apply"),
                    type: "post",
                    data: data,
                    success: function(res) {
                        console.log("success",res);
                        if(res.status == 200) {
                            layer.msg(res.msg);
                            setTimeout(function() {
                                location.href = getURL("Home", "ServiceLogin/finalTip") + "?index=1";
                            }, 1000);
                        }else {
                            layer.msg(res.msg);
                        }
                    },
                    error: function(res) {
                        console.log("err", res);
                    }
                })
                
            },
            // 取消打电话给客服
            notPhone() {
                vm.aleady = false;
            },
        }
    })
})();