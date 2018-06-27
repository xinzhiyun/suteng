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
                    conpassword: "" 
                },
                passEyes: {
                    tY: "password",
                    eye: "iconfont icon-iconcloseeye eyes",
                    canFlag: true
                },
                isIsset : false,//判断用户是否被注册
            }
        },
        created() {
            layui.use("layer", function() {
                layer = layui.layer;
            })
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
            // 判断用户是否存在
            userIsset() {
                console.log("失焦");
                var vm = this;
                $.ajax({
                    url: getURL("Home", ""),
                    type: "post",
                    data: vm.userInfos.name,
                    success: function(res) {
                        console.log("success",res);
                        if(res.status == 200) {
                            
                        }else {
                            layer.msg(res.msg);
                        }
                    },
                    error: function(res) {
                        console.log("err", res);
                    }
                })
            },
            // 提交审核
            verify() {
                var vm = this;
                if(!vm.userInfos.name) {
                    layer.msg("姓名不能为空");
                    return;
                }else {
                    if(!/^[0-9A-Za-z]{6,8}$/.test(vm.userInfos.name)) {
                        layer.msg("由6-8位数字或字母组成，不支持中文");
                        return ;
                    }
                }
                if(!vm.userInfos.password) {
                    layer.msg("请输入密码");
                    return;
                }
                if(vm.userInfos.conpassword != vm.userInfos.password) {
                    layer.msg("两次密码不一样");
                    return;
                }

                if(vm.isIsset) {
                    layer.msg("该用户已被注册");
                    return;
                }
                // $.ajax({
                //     url: getURL("Home", ""),
                //     type: "post",
                //     data: data,
                //     success: function(res) {
                //         console.log("success",res);
                //         if(res.status == 200) {
                
                //         }else {
                //             layer.msg(res.msg);
                //         }
                //     },
                //     error: function(res) {
                //         console.log("err", res);
                //     }
                // })
            },
            
        }
    })
})();