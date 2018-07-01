var shopindex = new Vue({
	el: '.shopindex',
	data() {
		return {
			menuList: [],				// 分类按钮
			blockList: [],				// 商品块集合
			categoryList: [],			// 一级分类类目
			categoryContentList: [],	// 二级分类
			categoryID: '',				// 一级分类id
			categoryAdv: [],
			cartList: [],				// 购物车商品集合
			cateSelect: '',
			tabclk: 1, 					// 底部按钮
			tabText: 'home',
			tabsrc: [],
			srcArr: [
				public + '/Home/images/shop/home',
				public + '/Home/images/shop/category',
				public + '/Home/images/shop/addcart',
				public + '/Home/images/shop/mine'
			],
			titleList: ['商城首页','分类','购物车','我的'],
			categoryTitle: '',  //二级分类标题
			noCateContent: '选择左边分类',
			cart_none: '',
			moneyCalc: [],	// 购物车选中的商品
			checkNum: 0,	// 结算的商品数量
			checkMoney: 0,	// 结算金额
			selectedSrc: '',
			emptySrc: '',
		}
	},
	created() {
		var vm = this;
		var href = location.href;
		// 刷新保持状态
		// 首页_home, 分类category, 购物车cart, 我的mine
		for(var i=0; i<vm.srcArr.length; i++){
			vm.tabsrc.push(vm.srcArr[i])
		}

		if(href.indexOf('_home') > 0){
			vm.tabClick(1, '_home');

		}else if(href.indexOf('category') > 0){
			vm.tabClick(2, 'category');

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
		vm.categoryList = JSON.parse(category);
		// console.log('category: ',category);
		// 加载第一条
		vm.$nextTick(function(){
			vm.getCate(vm.categoryList[0], vm.categoryList[0].id)
		})
		// vm.categoryList = [
		// 	{name: '豪大大大鸡排', id: '1'},
		// 	{name: '养生瓦胆煲', id: '2'},
		// 	{name: '光能路灯', id: '3'},
		// 	{name: '净化器', id: '4'},
		// 	{name: '电热水器', id: '5'},
		// 	{name: 'cece2', id: '9'},
		// 	{name: '', id: '25'}
		// ]

		// 购物车数据
		vm.cartList = [
			{src: '',gid:'1',name:'滤芯外部活性炭',attr:'蒂芬妮蓝',price:'1456',num:'1'},
			{src: '',gid:'2',name:'滤芯外部活性炭',attr:'蒂芬妮蓝',price:'1456',num:'16'},
			{src: '',gid:'3',name:'滤芯外部活性炭',attr:'蒂芬妮蓝',price:'1456',num:'3'},
			{src: '',gid:'4',name:'滤芯外部活性炭',attr:'蒂芬妮蓝',price:'1456',num:'11'},
			{src: '',gid:'5',name:'滤芯外部活性炭',attr:'蒂芬妮蓝',price:'1456',num:'6'},
		];
	},
	mounted() {
		var cBlock = document.querySelectorAll('.cBlock');	// 购物车商品
		this.selectedSrc = public+'/Home/images/shop/cart_select.png';
		this.emptySrc = public+'/Home/images/shop/cart_none.png';
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
			var vm = this;
			vm.categoryID = cate.id;
			vm.categoryTitle = cate.name;
			// 点击同一个分类类目
			if(window.clickCid == cid) return;
			window.clickCid = cid;
			vm.cateSelect = cate;
			console.log('cid: ',cid);
			// 获取对应类目下的商品详细分类
			$.ajax({
				url: getURL('Home','Shop/getCategory'),
				type: 'post',
				data: {id: cid},
				success: function(res){
					console.log('res: ',res);
					if(res.status == 200){
						if(!res.data.length){
							vm.noCateContent = '此分类下暂无数据';
							vm.categoryContentList.length = 0; // 清空详细分类内容
							vm.categoryAdv.length = 0; // 清空广告
							return;
						}
						vm.categoryAdv = res.adv;	// 广告
						vm.categoryContentList = res.data;	// 详细分类
						vm.noCateContent = false;
						vm.$nextTick(function(){
							lazyLoad('.category>.cright');	// 图片懒加载
						})
					}else{
						vm.categoryContentList.length = 0; // 清空
						vm.noCateContent = '此分类下暂无数据';
					}
				},
				error: function(err){
					console.log('err: ',err);
				}
			})

			// this.categoryContentList = [
			// 	{
			// 		title: '净水器',
			// 		subType: [
			// 			{pic: '',name:'lopo玩具',scid: '12'},
			// 			{pic: '',name:'毛衣刷',scid: '13'},
			// 			{pic: '',name:'宝宝杯',scid: '14'},
			// 			{pic: '',name:'小小苏',scid: '15'},
			// 			{pic: '',name:'规划局规划国际化',scid: '25'},
			// 		]
			// 	},
			// 	{
			// 		title: '净化器',
			// 		subType: [
			// 			{pic: '',name:'lopo玩具',scid: '12'},
			// 			{pic: '',name:'毛衣刷',scid: '13'},
			// 			{pic: '',name:'宝宝杯',scid: '14'},
			// 			{pic: '',name:'小小苏',scid: '15'},
			// 			{pic: '',name:'规划局规划国际化',scid: '25'},
			// 		]
			// 	}
			// ];
		},
		// 点击大分类下的小分类商品图片
		subClick(cid) {
			// id:一级分类is, cid:二级分类id
			console.log('cid: ',cid);
			location.href = shoplist + '?id=' + this.categoryID + '&cid=' + cid;
		},
		// 购物车商品左滑、右滑
		slideDelete(e, gid) {
			var el = e.currentTarget;
			// console.log('gid: ',gid);
			// console.log('e: ',e);
			// console.group();
			if(!window.touchX){
				window.touchX = e.changedTouches[0].pageX;
				// console.log('touchX: ',window.touchX);
			}else{
				if(window.touchX - e.changedTouches[0].pageX < 0 && window.touchX - e.changedTouches[0].pageX <= -30){
					// console.clear();
					// console.log('右滑');
					el.setAttribute('style','transform:translateX(0);');
					window.isDeleteShow = true;
					window.touchX = '';
					
				}else if(window.touchX - e.changedTouches[0].pageX > 0 && window.touchX - e.changedTouches[0].pageX >= 30){
					// console.clear();
					// console.log('左滑');
					el.setAttribute('style','transform:translateX(-40px);');
					window.isDeleteShow = false;
					window.touchX = '';
				}
			}
			// console.log('window.touchX - e.changedTouches[0].pageX: ',window.touchX - e.changedTouches[0].pageX);
			// console.groupEnd();
		},
		slideEnd() {
			window.touchX = '';
		},
		// 删除购物车商品
		deleteCart(gid, e) {
			var el = e.currentTarget.parentNode;
			console.log('gid: ',gid);
			
		},
		// 购物车勾选
		cartSelect(gid, price, index, num, e) {
			var vm = this;
			var el = e.currentTarget;
			var src = el.querySelector('img').getAttribute('src');
			//gid商品id, price单价, index序号
			console.log('gid: %s, price: %s, index: %s',gid, price, index);
			if(src.indexOf('select') < 0){	// 选中
				el.querySelector('img').setAttribute('src', vm.selectedSrc);
				vm.moneyCalc[index] = {gid: gid,price: price,num: num};
			}else{	// 取消选中
				el.querySelector('img').setAttribute('src', vm.emptySrc);
				vm.moneyCalc[index] = '';
			}
			
			// 同步结算勾选数量、金额
			vm.calculator();
		},
		// 计算金额
		calculator() {
			var vm = this;
			vm.checkNum = 0;
			vm.checkMoney = 0;
			for(var i=0; i<vm.moneyCalc.length; i++){
				if(vm.moneyCalc[i]){
					vm.checkNum++;
					vm.checkMoney += Number(vm.moneyCalc[i].price)*Number(vm.moneyCalc[i].num);
				}
			}
			cBlock = document.querySelectorAll('.cBlock');	// 购物车商品
			// 全选
			// 购物车商品打钩位置
			var cartSelectAll = document.querySelector('.cartCalc').querySelector('img');
			if(vm.checkNum == cBlock.length){
				cartSelectAll.setAttribute('src', vm.selectedSrc);
			}else{
				cartSelectAll.setAttribute('src', vm.emptySrc);
			}
		},
		// 修改数量
		numChange(bool, index, e) {
			var el = e.currentTarget;
			var span = el.parentNode.querySelectorAll('span')[0];
			var input = el.parentNode.querySelector('input');
			var vm = this;
			console.log('bool: ',bool);
			console.log('vm.moneyCalc[index]: ',vm.moneyCalc);
			console.log('vm.moneyCalc[index]: ',vm.moneyCalc[+index]);
			if(bool === 1){
				// 加
				if(vm.moneyCalc[+index]){
					++vm.moneyCalc[+index].num;
				}
				// 数量变化
				vm.cartList[index].num++;
				span.style.background = '#fff';
			}else if(bool === -1){
				// 减
				if(vm.moneyCalc[+index] && vm.moneyCalc[+index].num >= 2){
					--vm.moneyCalc[+index].num;
				}else if(vm.moneyCalc[+index] && vm.moneyCalc[+index].num == 1){
					span.style.background = '#f1f1f1';
				}
				// 数量变化
				if(vm.cartList[index].num >= 2){
					vm.cartList[index].num--;
				}
			}else if(bool === 11){
				console.log('val: ',el.value-0,typeof (el.value-0) );
				// 手动修改
				if(+el.value >= 1 && typeof(el.value-0) === 'number'){
					vm.cartList[index].num = el.value-0;
				}else{
					el.value = 1;
				}
				if(vm.moneyCalc[+index]){
					vm.moneyCalc[+index].num = el.value;
				}
			}
			vm.calculator();
		},
		// 全选、全不选
		checkAll(e) {
			var img = e.currentTarget.querySelector('img');
			cBlock = document.querySelectorAll('.cBlock');
			var cartCheckAll = document.querySelectorAll('.cBlock>.fl>img');
			var vm = this;
			if(vm.checkNum != vm.cartList.length){
				// 全选操作
				console.log('all');
				vm.moneyCalc.length = 0;	// 清空
				for(var i=0; i<vm.cartList.length; i++){
					vm.moneyCalc.push({
						gid: vm.cartList[i].gid,
						price: vm.cartList[i].price,
						num: vm.cartList[i].num
					})
					
					//打钩
					cartCheckAll[i].setAttribute('src', vm.selectedSrc);
				}
				img.setAttribute('src', vm.selectedSrc);
			}else{
				// 取消全选操作
				console.log('none');
				// 清空
				vm.moneyCalc.length = 0;
				img.setAttribute('src', vm.emptySrc);
				for(var i=0; i<vm.cartList.length; i++){
					//打钩清空
					cartCheckAll[i].setAttribute('src', vm.emptySrc);
				}
			}
			console.log('vm.moneyCalc: ',vm.moneyCalc);
			// 同步结算勾选数量、金额
			vm.calculator();
		},
		// 点击结算
		goPay() {
			var vm = this;
			console.log('vm.moneyCalc: ',vm.moneyCalc);
			if(!vm.moneyCalc.length){
				// 未选中
				return
			}
		}
	}
})