var vm = new Vue({
    el: ".main",
    data() {
        return {
            goodsInfo: {},
            gocart: false,//立即购买/加入购物车/
            DisFlag : 0,//选项卡
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
            ],
            guige: [
                {title: "集水器A"},
                {title: "集水器B"},
                {title: "集水器C"},
            ],
            color: [
                {title: "高雅白色"},
                {title: "高雅白色"},
                {title: "高雅白色"},
            ]
        }
    },
    created() {
        // 默认选中第一个
        $(".titleKa>li>span").eq(0).addClass("selecteds");
        // 模拟
        this.goodsInfo = {
            goodsid: '',            // 商品编号
            name: '速腾智能净水机H款',      // 商品名称
            desc: '全新智能升级的净水机',   // 商品描述
            rate: '98%',            // 好评率
            newprice: '1398.00',    // 打折价格？会员价格
            price: '1398.00',       // 旧价格
            leaseprice: '300/月',   // 租赁价格
            subjoin: '购买送1000银币',
            postage: '0.00',        // 邮费
            address: '广东中山',    // 发货地址？产地？
            buyattr: [
                // 购买规格选项
                {
                    title: '颜色',
                    id: 1,
                    data: [
                        {
                            name: '高压白',
                            id: '11'
                        }
                    ]
                }
            ],               
            showattr: [                // 显示商品规格
                {
                    title: '尺寸',          // 名称
                    content: '14'         // 内容
                },
                {
                    title: '重量',          // 名称
                    content: '4kg'         // 内容
                }
            ]
        }
    },
    methods: {
        // 加入购物车/立即购买
        menban(val) {
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
        // 选中规格和颜色
        choice() {
            var vm = this;
            var ev = ev || window.event;
            var target = ev.target || ev.srcElement;
            console.log(target);
            $(target).css("border", "1px solid #518CF8").parent().siblings().children().css("border", "1px solid #747474");
        },
    },
})