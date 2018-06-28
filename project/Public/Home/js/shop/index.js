var shopindex = new Vue({
	el: '.shopindex',
	data() {
		return {
			menuList: [],				// 分类按钮
			blockList: [],				// 商品块集合
			classifyList: [],			// 一级分类类目
			classifyContentList: [],	// 二级分类
			cartList: [],				// 购物车商品集合
			cateSelect: '',
			tabclk: 1, 					// 底部按钮
			tabText: 'home',
			tabsrc: [],
			srcArr: [
				public + '/Home/images/shop/home',
				public + '/Home/images/shop/classify',
				public + '/Home/images/shop/addcart',
				public + '/Home/images/shop/mine'
			],
			titleList: ['商城首页','分类','购物车','我的'],
		}
	},
	created() {
		var vm = this;
		var href = location.href;
		// 刷新保持状态
		// 首页_home, 分类classify, 购物车cart, 我的mine
		for(var i=0; i<vm.srcArr.length; i++){
			vm.tabsrc.push(vm.srcArr[i])
		}

		if(href.indexOf('_home') > 0){
			vm.tabClick(1, '_home');

		}else if(href.indexOf('classify') > 0){
			vm.tabClick(2, 'classify');

		}else if(href.indexOf('cart') > 0){
			vm.tabClick(3, 'cart');

		}else if(href.indexOf('mine') > 0){
			vm.tabClick(4, 'mine');

		}else{
			// 首页
			vm.tabsrc[0] = vm.tabsrc[0] + '_light';
		}
		// 清除大分类点击记录
		window.clickCid = '';
		/**
		 * 以下是模拟数据
		 */
		// 分类按钮
		vm.menuList = [
			{src: public+'/Home/images/shop/waterpurifier.png',name: '净水器',type: '1'},
			{src: public+'/Home/images/shop/waterheater.png',name: '热水器',type: '2'},
			{src: public+'/Home/images/shop/treater.png',name: '净化器',type: '3'},
			{src: public+'/Home/images/shop/smallma.png',name: '小家电',type: '4'},
			{src: public+'/Home/images/shop/digital.png',name: '数码',type: '5'},
			{src: public+'/Home/images/shop/computer.png',name: '电脑',type: '6'},
			{src: public+'/Home/images/shop/phone.png',name: '手机',type: '7'},
			{src: public+'/Home/images/shop/house.png',name: '家居',type: '8'}
		];
		// 商品块集合
		vm.blockList = [
			{
				title: '新品推介',
				goods: [
					{gid: '12',src: '/Home/images/shop/waterpurifier.png',desc: '良心净水器，买到就是赚到',price: '1020'},
					{gid: '13',src: '/Home/images/shop/waterpurifier.png',desc: '良心净水器，买到就是赚到',price: '520'},
					{gid: '14',src: '/Home/images/shop/waterpurifier.png',desc: '良心净水器，买到就是赚到',price: '1120'},
					{gid: '15',src: '/Home/images/shop/waterpurifier.png',desc: '良心净水器，买到就是赚到',price: '1120'},
					{gid: '16',src: '/Home/images/shop/waterpurifier.png',desc: '良心净水器，买到就是赚到',price: '1120'},
					{gid: '17',src: '/Home/images/shop/waterpurifier.png',desc: '良心净水器，买到就是赚到',price: '1120'},
					{gid: '18',src: '/Home/images/shop/waterpurifier.png',desc: '良心净水器，买到就是赚到',price: '1120'}
				]
			},
			{
				title: '限时促销',
				goods: [
					{gid: '12',src: '/Home/images/shop/waterpurifier.png',desc: '良心净水器，买到就是赚到',price: '1020'},
					{gid: '13',src: '/Home/images/shop/waterpurifier.png',desc: '良心净水器，买到就是赚到',price: '520'},
					{gid: '14',src: '/Home/images/shop/waterpurifier.png',desc: '良心净水器，买到就是赚到',price: '1120'},
					{gid: '15',src: '/Home/images/shop/waterpurifier.png',desc: '良心净水器，买到就是赚到',price: '1120'},
					{gid: '16',src: '/Home/images/shop/waterpurifier.png',desc: '良心净水器，买到就是赚到',price: '1120'},
					{gid: '17',src: '/Home/images/shop/waterpurifier.png',desc: '良心净水器，买到就是赚到',price: '1120'},
					{gid: '18',src: '/Home/images/shop/waterpurifier.png',desc: '良心净水器，买到就是赚到',price: '1120'}
				]
			}
		];

		// 一级分类
		vm.classifyList = [
			{name: '热水器', cid: '12'},
			{name: '净水器', cid: '13'},
			{name: '瓦胆系列', cid: '142'},
			{name: '空气净化', cid: '14'},
			{name: '生活用品', cid: '112'},
			{name: '数码家电', cid: '242'}
		]

		// 购物车数据
		vm.cartList = [
			{src: '',gid:'1',name:'滤芯外部活性炭',attr:'蒂芬妮蓝',price:'1456',num:'1'},
			{src: '',gid:'2',name:'滤芯外部活性炭',attr:'蒂芬妮蓝',price:'1456',num:'16'},
			{src: '',gid:'3',name:'滤芯外部活性炭',attr:'蒂芬妮蓝',price:'1456',num:'3'},
			{src: '',gid:'4',name:'滤芯外部活性炭',attr:'蒂芬妮蓝',price:'1456',num:'11'},
			{src: '',gid:'5',name:'滤芯外部活性炭',attr:'蒂芬妮蓝',price:'1456',num:'6'},
		];
	},
	methods: {
		// 点击商品图片
		todetail(gid) {
			console.log('gid: ', gid);
		},
		// 点击底部导航按钮
		tabClick(tabclk, text){
			// tabclk: 1首页，2分类，3购物车，4我的
			this.tabclk = tabclk;
			// console.log('tabclk: ',tabclk);
			// 切换title
			document.title = this.titleList[+tabclk-1];
			// 切换图标
			this.tabsrc = [].concat(this.srcArr);
			this.tabsrc[tabclk-1] = this.srcArr[tabclk-1] + '_light';
			location.href = shopurl + '#' + text;
			this.tabText = text;
		},
		// 点击分类类目
		getCate(cate, cid) {
			// 点击同一个分类类目
			if(window.clickCid == cid) return;
			window.clickCid = cid;
			this.cateSelect = cate;
			console.log('cid: ',cid);
			// 获取对应类目下的商品详细分类
			// $.ajax({
			// 	url: '',
			// 	type: 'get',
			// 	data: {cid: cid},
			// 	success: function(res){
			// 		console.log('res: ',res);
			// 	},
			// 	error: function(err){
			// 		console.log('err: ',err);
			// 	}
			// })

			this.classifyContentList = [
				{
					title: '净水器',
					subType: [
						{src: '',name:'lopo玩具',scid: '12'},
						{src: '',name:'毛衣刷',scid: '13'},
						{src: '',name:'宝宝杯',scid: '14'},
						{src: '',name:'小小苏',scid: '15'},
						{src: '',name:'规划局规划国际化',scid: '25'},
					]
				},
				{
					title: '净化器',
					subType: [
						{src: '',name:'lopo玩具',scid: '12'},
						{src: '',name:'毛衣刷',scid: '13'},
						{src: '',name:'宝宝杯',scid: '14'},
						{src: '',name:'小小苏',scid: '15'},
						{src: '',name:'规划局规划国际化',scid: '25'},
					]
				}
			];
		},
		// 点击大分类下的小分类商品图片
		subClick(scid) {
			console.log('scid: ',scid);
		}
	}
})