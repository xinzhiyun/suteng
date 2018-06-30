var vm = new Vue({
    el: ".main",
    data() {
        return {
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