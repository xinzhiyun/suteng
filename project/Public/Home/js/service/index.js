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
            }
        },
        created() {
            layui.use("layer", function() {
                layer = layui.layer;
            });
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
                $.ajax({ 
                    url: getURL("Home", "ServiceLogin/index"),
                    type: "post",
                    data: {
                        name:vm.userInfos.name,
                        password:vm.userInfos.password,
                        code: vm.userInfos.code
                    },
                    success: function(res) {
                        console.log("登录", res);
                        if(res.status == 200) {
                            layuiHint(res.msg);
                            setTimeout(function() {
                                location.href = getURL("Home", "Service/index");
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
                });
            },
        }
    })
})();