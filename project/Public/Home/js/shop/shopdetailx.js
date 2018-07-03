var vm = new Vue({
    el: ".main",
    data() { 
        return {
            goodsInfo: {},
            upInfo: {},             // 待提交的数据
            gocart: false,          //立即购买/加入购物车/
            DisFlag : 0,            //选项卡
            checkList: [],          // 选择的套餐规格
            lastCheckPname: '',     // 上次选择的规格
            isBuy: '',              // 点击的是购买、加入购物车
            canBuy: '',             // 是否可以购买
            numVal: 1,              //默认数量
            goodDetail: [
                {src :public + "/Home/images/1a.jpg"},
                {src :public + "/Home/images/1a.jpg"},
                {src :public + "/Home/images/1a.jpg"},
                {src :public + "/Home/images/1a.jpg"},
            ],
            products: [
                {src :public + "/Home/images/goods1.png"},
                {src :public + "/Home/images/goods1.png"},
                {src :public + "/Home/images/goods1.png"},
                {src :public + "/Home/images/goods1.png"},
            ]
        }
    },
    created() {
        // layui.use("layer", function() {
        //     layer = layui.layer;
        // })
        // 默认选中第一个
        $(".titleKa>li>span").eq(0).addClass("selecteds");
        // 模拟
        // this.goodsInfo = {
        //     goodsid: '',            // 商品编号
        //     name: '速腾智能净水机H款',      // 商品名称
        //     desc: '全新智能升级的净水机',   // 商品描述
        //     rate: '98%',            // 好评率
        //     newprice: '1398.00',    // 打折价格？会员价格
        //     price: '1398.00',       // 旧价格
        //     leaseprice: '300/月',   // 租赁价格
        //     subjoin: '购买送1000银币',
        //     postage: '0.00',        // 邮费
        //     address: '广东中山',    // 发货地址？产地？
        //     buyattr: [
        //         // 购买规格选项
        //         {
        //             title: '颜色',
        //             id: 1,
        //             data: [
        //                 {
        //                     name: '高压白',
        //                     id: '11'
        //                 }
        //             ]
        //         }
        //     ],               
        //     showattr: [                // 显示商品规格
        //         {
        //             title: '尺寸',          // 名称
        //             content: '14'         // 内容
        //         },
        //         {
        //             title: '重量',          // 名称
        //             content: '4kg'         // 内容
        //         }
        //     ]
        // }
        var gid = GetQueryString('gid');
        if(gid){
            // 获取商品数据
            this.getDetail(gid);
        }
        // 清空发票信息
        if(sessionStorage.getItem("voiceArr")){
            sessionStorage.setItem("voiceArr", '');
        }
    },
    methods: {
        // 立即购买
        buyNow(val) {
            // 显示选项面板
            this.gocart = val;
            this.isBuy = true;
        },
        // 加入购物车
        addCart(val) {
            // 显示选项面板
            this.gocart = val;
            this.isBuy = false;
        },
        // 关闭面板
        closePanel(val) {
            // 关闭选项面板
            this.gocart = val;
            // 清空已选规格
            this.checkList.length = 0;
        },
        // 选项卡
        chooice(e) {
            var vm = this;
            var ev = ev || window.event;
            var target = ev.target || ev.srcElement;
            if(target.nodeName.toLowerCase() == "span" ) {
                $(target).addClass("selecteds").parent("li").siblings("li").children("span").removeClass("selecteds");
                vm.DisFlag = $(target).attr("index");
            }
        },
        // 选中规格
        choice(index, id, pname, name, e) {
            // id:属性id, pname:父级, name:属性名
            var vm = this;
            var ev = e || window.event;
            var target = ev.target || ev.srcElement;
            // console.log(target);
            $(target).css("border", "1px solid #518CF8").parent().siblings().children().css("border", "1px solid #747474");
            // 已选规格
            if(vm.lastCheckIndex === index){
                // 选择同一项目的不同规格
                vm.checkList[+index] = {};
                vm.$set(vm.checkList, index, {
                    name: name,
                    pname: pname,
                    id: +id
                })
            }else{
                // 修改同一项目的不同规格（已存在的时候）
                vm.checkList[+index] = {};
                vm.$set(vm.checkList, index, {
                    name: name,
                    pname: pname,
                    id: +id
                })
            }
            // console.log('index: %s, vm.lastCheckIndex: %s',index, vm.lastCheckIndex);
            console.log('vm.checkList: ',vm.checkList);
            // 记录本地点击的id
            vm.lastCheckIndex = index;
            // 待删（把下面库存查询加入后）
            $('.confirmBtn p').css({background: '#CA0010'});
            // 查询库存
            // vm.getStock({
            //     gid: vm.goodsInfo.id,
            //     price: vm.goodsInfo.price,
            //     skuattr: vm.checkList
            // },function(res){
            //     vm.canBuy = true;
            //     // res.maxNum;
            // })
        },
        // 获取商品详情数据
        getDetail(gid) {
            var vm = this;
            console.log('gid: ',gid);
            $.ajax({
                url: getDetailURL,
                type: 'post',
                data: {id: gid},
                success: function(res){
                    console.log('res: ',res);
                    if(res.status == 200){
                        vm.goodsInfo = res.data;
                        vm.$nextTick(function(){
                            lazyLoad('.main');	// 图片懒加载
                            // 轮播图
                            var mySwiper = new Swiper ('.swiper-container', {
                                // loop: true,
                                zoom : true,
                                paginationType: 'fraction',
                                // 如果需要分页器
                                pagination: '.swiper-pagination',
                            })
                        })
                    }
                },
                error: function(err){
                    console.log('err: ',err);
                }
            })
        },
        // 去购物车
        cart() {
            location.href = shopindex + '#_cart';
        },
        // 确认选择
        conPay() {
            var vm = this;
            // 未选择所有项\或无库存
            // if(!vm.canBuy){
            //     layuiHint('选择的商品已无库存，请选择其他搭配');
            //     return;
            // }
            if(vm.isBuy){
                var arr = [];
                // 点击的购买
                vm.upInfo = {
                    gid: vm.goodsInfo.id,
                    money: (+vm.goodsInfo.price)*(+vm.numVal),  // 总价
                    skuattr: vm.checkList,
                    price: vm.goodsInfo.price,
                    num: vm.numVal
                };
                arr.push(vm.upInfo);
                // 生成订单号
                $.ajax({
                    url: getOrder,
                    type: 'post',
                    data: {info: JSON.stringify(arr)},
                    success: function(res){
                        if(res.code == 200){
                            // 跳转到订单确认页面
					        location.href = payConfirm+'?order_id='+res.msg;
                        }else{
                            layuiHint(res.msg);
                        }
                    },
                    error: function(err){
                        layuiHint('系统遇到问题，请稍后再试');
                    }
                })
            }else{
                // 加入购物车
                vm.upInfo = {
                    gid: vm.goodsInfo.id,
                    skuattr: vm.checkList,
                    price: vm.goodsInfo.price,
                    num: vm.numVal,
                    name: vm.goodsInfo.name,
                    desc: vm.goodsInfo.desc
                };
            }

            console.log('vm.isBuy: ',vm.isBuy);
            console.log('vm.upInfo: ',vm.upInfo);
        },
        // 加减
        numjisuan(val) {
            var vm = this;
            var maxNum = 1;
            // 查询库存
            // vm.getStock({
            //     gid: vm.goodsInfo.id,
            //     price: vm.goodsInfo.price,
            //     skuattr: vm.checkList
            // },function(res){
            //     vm.canBuy = true;
            //     maxNum = res.maxNum;
            // })
            // 加
            if(val == "add") {
                vm.numVal >= 10 ? "库存不够" : vm.numVal++;
                // vm.numVal = Number(vm.numVal) >= Number(maxNum) ? Number(maxNum) : vm.numVal++;
            }
            console.log(vm.numVal)

            // 减
            if(val == "reduce") {
                vm.numVal < 1 ? vm.numVal=1 : vm.numVal--;
                // vm.numVal = vm.numVal < 1 ? 1 : vm.numVal--;
            }
        },
        // 查询库存
        getStock(data, callback) {
            var vm = this;
            var len = 0;
            // 计算是否选择完全部的项目
            for(var i=0; i<vm.checkList.length; i++){
                if(vm.checkList[i]){
                    len++;
                }
            }
            // 未选择所有项
            if(len != vm.goodsInfo.attr.length){
                vm.canBuy = false;
                $('.confirmBtn p').css({background: '#999'});
            }else{
                // 查询库存
                $.ajax({
                    url: getStock,
                    type: 'post',
                    data: data,
                    success: function(res){
                        console.log('res: ',res);
                        if(res.code == 200){
                            callback({maxNum: res.data});
                            $('.confirmBtn p').css({background: '#CA0010'});
                        }else{
                            layuiHint(res.msg);
                            $('.confirmBtn p').css({background: '#999'});
                        }
                    },
                    error: function(err){}
                })
            }
            
        }
    },
})