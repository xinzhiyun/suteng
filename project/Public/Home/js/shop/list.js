//分类详情、搜索结果页
var goodsList = new Vue({
    el: '.categoryDetail',
    data() {
        return {
            searchList: [],     // 查询列表数据
            filterList: [],     // 筛选选项数据
            showFilter: false,     // 是否显示筛选层
            sortmode: '0',      // 排序模式0:降序,1升序
            listid: '',         // 一级id
            listcid: '',        // 二级id
            lastsort: '',       // 上次点击的排序项目
            search: '',         // 搜索关键字
            loadText: '加载中...',
            loadStyle: '',
            arrow_up: '',       // 上箭头（升序）
            arrow_down: '',     // 下箭头（降序）
            arrow_empty: '',    // 空
            sou: 1,             // 分页
            priceLow: '',       // 最低价
            priceHigh: ''       // 最高价
        }
    },
    created() {
        var vm  = this;

        // vm.searchList = [
        //     {id: '1',pic: '111111',name:'滤芯外部活性炭',price:'11'},
        //     {id: '2',pic: '222222',name:'滤芯外部活性炭',price:'11'},
        //     {id: '3',pic: '333333',name:'滤芯外部活性炭',price:'11'},
        //     {id: '4',pic: '444444',name:'滤芯外部活性炭',price:'11'},
        //     {id: '5',pic: '555555',name:'滤芯外部活性炭',price:'11'},
        // ];
        // vm.$nextTick(function(){
        //     // 图片懒加载
        //     lazyLoad('.list');
        // })
        // 排序箭头
        vm.arrow_up = public+'/Home/images/shop/arrow_up.png';
        vm.arrow_down = public+'/Home/images/shop/arrow_down.png';
        vm.arrow_empty = public+'/Home/images/shop/arrow_empty.png';
        vm.listid = GetQueryString('id');
        vm.listcid = GetQueryString('cid');
        if(GetQueryString('search')){
            vm.search = decodeURI(GetQueryString('search'));
            // 搜索
            vm.searchFn(GetQueryString('search'));
        }else{
            var option = {
                id: vm.listid,
                sort: 0,
                sortmode: vm.sortmode
            }
            if(vm.listcid){
                option['cid'] = vm.listcid;
            }
            console.log('option: ',option);
            // 获取查询列表
            vm.getGoodsList(option);
            vm.lastsort = 0;    // 时间
        }
        
    },
    mounted() {
        var vm = this;
        // 获取筛选数据
        vm.filterList = [
            {   title: '品牌',
                data: [
                    {name: '美的', id: '1'},
                    {name: '格力', id: '2'},
                    {name: '维嘉', id: '3'},
                    {name: '创维', id: '4'}
                ]
            },
            {   title: '品牌',
                data: [
                    {name: '美的', id: '1'},
                    {name: '格力', id: '2'},
                    {name: '维嘉', id: '3'},
                    {name: '创维', id: '4'}
                ]
            }
        ];
        
    },
    methods: {
        // 跳到商品详情界面
        getDeatil(gid) {
            console.log('gid: ',gid);
            location.href = shopdetail + '?gid=' + gid;
        },
        // 获取查询列表
        getGoodsList(option) {
            // option.id: 一级分类id
            // option.cid： 当前(二级)分类id
            // option.sort：排序模式（0：时间，1人气，2价格，3销量）
            // sortmode: 0:降序,1升序
            var vm = this;
            vm.loadStyle = 'display:block;';
            vm.loadText = '加载中...';
            $.ajax({
                url: getGoodsList,
                type: 'post',
                data: option,
                success: function(res){
                    console.log('res: ',res);
                    if(res.status == 200){
                        if(!res.data.length){
                            console.log('查无结果');
                            vm.searchList.length = 0;
                            vm.loadText = '查无数据';
                            return
                        }
                        vm.loadStyle = 'display:none;';
                        vm.searchList = res.data;
                        vm.$nextTick(function(){
                            // 图片懒加载
                            lazyLoad('.list');
                        })
                    }
                },
                error: function(err){
                    console.log('err: ',err);
                }
            })
        },
        // 点击修改排序规则
        getSort(sorttype) {
            // sorttype: 排序模式（0：时间，1人气，2价格，3销量）
            var vm = this;
            console.log('sorttype: %s, vm.lastsort: %s',sorttype,vm.lastsort);
            console.log('vm.sortmode: ',vm.sortmode);
            var sortitem = $('.filterBtnMenu .fimg');
            for(var i=0; i<sortitem.length; i++){
                sortitem.attr('src', vm.arrow_empty);
            }
            // 判断是否点击的是同一个
            if(vm.lastsort == sorttype){
                if(vm.sortmode == 0){   // 其他默认降序
                    sortitem.eq(+sorttype).attr('src',vm.arrow_up);
                    vm.sortmode = 1;

                }else if(vm.sortmode == 1){
                    sortitem.eq(+sorttype).attr('src',vm.arrow_down);
                    vm.sortmode = 0;
                }
            }else{  //  第一次点击
                if(sorttype == 2){
                    // 价格(默认升序)
                    sortitem.eq(+sorttype).attr('src',vm.arrow_up);
                    vm.sortmode = 1;

                }else{  // 其他默认降序
                    sortitem.eq(+sorttype).attr('src',vm.arrow_down);
                    vm.sortmode = 0;
                }
            }
            // 请求参数
            var option = {
                id: vm.listid,
                sort: sorttype,
                sortmode: vm.sortmode
            }
            if(vm.listcid){
                option['cid'] = vm.listcid;
            }
            // 获取排序排序详情
            vm.getGoodsList(option);
            // 记录当前点击的排序项目(0：时间，1人气，2价格，3销量）
            vm.lastsort = sorttype;
        },
        // 搜索
        searchFn(search) {
            console.log('search: ',search);
            if(!search){
                layuiHint('请输入需要搜索的内容');
                return;
            }
            var vm = this;
            vm.sortmode = 0;
            // 请求参数
            var option = {
                sort: 0,                // 时间
                search: search,         // 搜索关键字
                sortmode: vm.sortmode,  // 排序模式
                sou: vm.sou             // 分页
            }
            if(vm.listcid){
                option['cid'] = vm.listcid;
            }
            console.log('option: ',option);
            // 获取搜索详情
            vm.getGoodsList(option);
        },
        // 点击筛选
        filterMode() {
            // this.showFilter = true;
            var href = location.href;
            location.href = href.substr(0,href.indexOf('#')) + '#filterMode';
        }
    }
})