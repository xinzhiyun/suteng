(function() {
    var codeFlag = false;//存放验证码
    
    var vm = new Vue({
        el: ".main",
        data() {
            return {
                // 个人信息
                userInfos: {
                    name: "",
                    password: "",
                    code: "",
                },
                passEyes: {
                    tY: "password",
                    eye: "iconfont icon-iconcloseeye eyes",
                    canFlag: true
                },
                index: "",//不同页面
            }
        },
        created() {
            layui.use("layer", function() {
                layer = layui.layer;
            });
            // 获取url
            this.index = window.location.search.substr(-1, 1) ? window.location.search.substr(-1, 1) : 0;
            if(this.index == 0) {
                $("title").text("服务站登录");
            }else if(this.index == 1) {
                $("title").text("经销商登录");
            }else if(this.index == 2) {
                $("title").text("安装人员登录");
            }
        },
        methods: {
            // 密码显示
            canEye() {
                if(this.passEyes.canFlag) {
                    this.passEyes.tY = "text";
                    this.passEyes.eye = "iconfont icon-eye eyes";
                    this.passEyes.canFlag = false;
                }else {
                    this.passEyes.tY = "password";
                    this.passEyes.eye = "iconfont icon-iconcloseeye eyes";
                    this.passEyes.canFlag = true;
                }
            },
            // 提交审核
            verify() {
                var vm = this;
                if(!vm.userInfos.name) {
                    layer.msg("姓名不能为空");
                    return;
                }
                if(!vm.userInfos.password) {
                    layer.msg("请输入密码");
                    return;
                }
                if(!vm.userInfos.code) {
                    layer.msg("请输入验证码");
                    return;
                }
                var src = $(".yCord>img").attr("src") + "?s" + Math.random;
                
                if(vm.index == 0 || vm.index == "") {
                    // 服务站
                    var data = {
                        name:vm.userInfos.name,
                        password:vm.userInfos.password,
                        code: vm.userInfos.code
                    }
                    var url = getURL("Home", "ServiceLogin/index");
                    var goURL = getURL("Home", "Service/index");
                    
                    goAjax(data, url, goURL);
                }else if(vm.index == 1) {
                    // 经销商
                }else if(vm.index == 2) {
                    // 安装人员
                }
            },
            // 提交
            goAjax(data, url, goURL) {
                // 服务站
                $.ajax({ 
                    url: url,
                    type: "post",
                    data: data,
                    success: function(res) {
                        console.log("登录", res);
                        if(res.status == 200) {
                            layuiHint(res.msg);
                            setTimeout(function() {
                                location.href = goURL;
                            }, 1000);
                        }else{
                            layuiHint(res.msg);
                            $(".yCord>img").attr("src", src);
                        }
                    },
                    error: function(res) {
                        console.log("失败", res);
                        $(".yCord>img").attr("src", src);
                    }
                })
            }
        }
    })
})();