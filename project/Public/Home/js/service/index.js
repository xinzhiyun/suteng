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
            this.index = window.location.search.substr(-1, 1) ? window.location.search.substr(-1, 1) : 0;
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
                $.ajax({ 
                    url: getURL("Home", "ServiceLogin/index"),
                    type: "post",
                    data: {
                        name:vm.userInfos.name,
                        password:vm.userInfos.password
                    },
                    success: function(res) {
                        console.log("登录", res);
                        if(res.status == 200) {
                            layuiHint(res.msg);
                            setTimeout(function() {
                                location.href = '{{:U("Home/Service/index")}}';
                            }, 1000);
                            
                        }else{
                            layuiHint(res.msg);
                        }
                    },
                    error: function(res) {
                        console.log("失败", res);
                    }
                })
            },
            
        }
    })
})();