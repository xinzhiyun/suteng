var vm = new Vue({
    el: ".main",
    data() {
        return {
            goodsInfo: {},
            gocart: false,      //立即购买/加入购物车/
            DisFlag : 0,        //选项卡
            checkList: [],      // 选择的套餐规格
            lastCheckIndex: '',    // 上次选择的规格
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
    },
    methods: {
        // 立即购买
        buyNow(val) {
            // 显示选项面板
            this.gocart = val;
        },
        // 加入购物车
        addCart(val) {
            // 显示选项面板
            this.gocart = val;
        },
        // 关闭面板
        closePanel(val) {
            // 关闭选项面板
            this.gocart = val;
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
            console.log(target);
            $(target).css("border", "1px solid #518CF8").parent().siblings().children().css("border", "1px solid #747474");
            // 已选规格
            if(vm.lastCheckIndex == index && vm.checkList[+index]){
                // 选择同一项目的不同规格
                vm.checkList[+index] = {};
                vm.$set(vm.checkList, index, {
                    name: name,
                    pname: pname,
                    id: +id
                })
            }else{
                if(vm.checkList[+index]){
                    // 修改同一项目的不同规格（已存在的时候）
                    vm.checkList[+index] = {};
                    vm.$set(vm.checkList, index, {
                        name: name,
                        pname: pname,
                        id: +id
                    })
                }else{
                    // 点击位选择的项目
                    vm.checkList.push({
                        name: name,
                        pname: pname,
                        id: +id
                    })
                }
            }
            console.log('vm.lastCheckIndex: ',vm.lastCheckIndex);
            console.log('vm.checkList: ',vm.checkList);
            // 选择了所有项
            if(vm.checkList.length == vm.goodsInfo.attr.length){
                // 查询库存
            }
            // 记录本地点击的id
            vm.lastCheckIndex = index;
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
                            // 图片懒加载
                            lazyLoad('.swiper-wrapper');
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
        }
    },
})