var shopindex = new Vue({
	el: '.shopindex',
	data() {
		return {
			banner: [],					// 轮播图
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
			search: '',					// 搜索关键字
			srcArr: [
				public + '/Home/images/shop/home',
				public + '/Home/images/shop/category',
				public + '/Home/images/shop/addcart',
				public + '/Home/images/shop/mine'
			],
			titleList: ['商城首页','分类','购物车'],
			categoryTitle: '',  //二级分类标题
			noCateContent: '选择左边分类',
			cart_none: '',
			moneyCalc: [],	// 购物车选中的商品
			checkNum: 0,	// 结算的商品数量
			checkMoney: 0,	// 结算金额
			selectedSrc: '',
			emptySrc: '',
			cartnum: '',
		}
	},
	created() {
		var vm = this;
		var href = location.href;
		// 刷新保持状态
		// 首页_home, 分类category, 购物车cart, 
		for(var i=0; i<vm.srcArr.length; i++){
			vm.tabsrc.push(vm.srcArr[i])
		}

		if(href.indexOf('_home') > 0){
			vm.tabClick(1, '_home');

		}else if(href.indexOf('category') > 0){
			vm.tabClick(2, 'category');

		}else if(href.indexOf('cart') > 0){
			vm.tabClick(3, 'cart');

		}else{
			// 首页
			vm.tabsrc[0] = vm.tabsrc[0] + '_light';
		}
		// 清除大分类点击记录
		window.clickCid = '';
		
		// 获取主题商品数据
		vm.getGoodsList();
		// 一级分类
		vm.categoryList = JSON.parse(category);
		// console.log('category: ',category);
		// 加载第一条
		vm.$nextTick(function(){
			vm.getCate(vm.categoryList[0], vm.categoryList[0].id)
		})
		/**
		 * 以下是模拟数据
		 */
		// 分类按钮
		// vm.menuList = [
		// 	{src: public+'/Home/images/shop/waterpurifier.png',name: '净水器',type: '1'},
		// 	{src: public+'/Home/images/shop/waterheater.png',name: '热水器',type: '2'},
		// 	{src: public+'/Home/images/shop/treater.png',name: '净化器',type: '3'},
		// 	{src: public+'/Home/images/shop/smallma.png',name: '小家电',type: '4'},
		// 	{src: public+'/Home/images/shop/digital.png',name: '数码',type: '5'},
		// 	{src: public+'/Home/images/shop/computer.png',name: '电脑',type: '6'},
		// 	{src: public+'/Home/images/shop/phone.png',name: '手机',type: '7'},
		// 	{src: public+'/Home/images/shop/house.png',name: '家居',type: '8'}
		// ];
		// 商品块集合
		// vm.blockList = [
		// 	{
		// 		title: '新品推介',
		// 		goods: [
		// 			{gid: '12',src: '/Home/images/shop/waterpurifier.png',desc: '良心净水器，买到就是赚到',price: '1020'},
		// 			{gid: '13',src: '/Home/images/shop/waterpurifier.png',desc: '良心净水器，买到就是赚到',price: '520'},
		// 			{gid: '14',src: '/Home/images/shop/waterpurifier.png',desc: '良心净水器，买到就是赚到',price: '1120'},
		// 			{gid: '15',src: '/Home/images/shop/waterpurifier.png',desc: '良心净水器，买到就是赚到',price: '1120'},
		// 			{gid: '16',src: '/Home/images/shop/waterpurifier.png',desc: '良心净水器，买到就是赚到',price: '1120'},
		// 			{gid: '17',src: '/Home/images/shop/waterpurifier.png',desc: '良心净水器，买到就是赚到',price: '1120'},
		// 			{gid: '18',src: '/Home/images/shop/waterpurifier.png',desc: '良心净水器，买到就是赚到',price: '1120'}
		// 		]
		// 	},
		// 	{
		// 		title: '限时促销',
		// 		goods: [
		// 			{gid: '12',src: '/Home/images/shop/waterpurifier.png',desc: '良心净水器，买到就是赚到',price: '1020'},
		// 			{gid: '13',src: '/Home/images/shop/waterpurifier.png',desc: '良心净水器，买到就是赚到',price: '520'},
		// 			{gid: '14',src: '/Home/images/shop/waterpurifier.png',desc: '良心净水器，买到就是赚到',price: '1120'},
		// 			{gid: '15',src: '/Home/images/shop/waterpurifier.png',desc: '良心净水器，买到就是赚到',price: '1120'},
		// 			{gid: '16',src: '/Home/images/shop/waterpurifier.png',desc: '良心净水器，买到就是赚到',price: '1120'},
		// 			{gid: '17',src: '/Home/images/shop/waterpurifier.png',desc: '良心净水器，买到就是赚到',price: '1120'},
		// 			{gid: '18',src: '/Home/images/shop/waterpurifier.png',desc: '良心净水器，买到就是赚到',price: '1120'}
		// 		]
		// 	}
		// ];

		// 购物车数据
		// vm.cartList = [
		// 	{src: '',gid:'1',name:'滤芯外部活性炭',attr:'蒂芬妮蓝',price:'1456',num:'1'},
		// 	{src: '',gid:'2',name:'滤芯外部活性炭',attr:'蒂芬妮蓝',price:'1456',num:'16'},
		// 	{src: '',gid:'3',name:'滤芯外部活性炭',attr:'蒂芬妮蓝',price:'1456',num:'3'},
		// 	{src: '',gid:'4',name:'滤芯外部活性炭',attr:'蒂芬妮蓝',price:'1456',num:'11'},
		// 	{src: '',gid:'5',name:'滤芯外部活性炭',attr:'蒂芬妮蓝',price:'1456',num:'6'},
		// ];
	},
	mounted() {
		var cBlock = document.querySelectorAll('.cBlock');	// 购物车商品
		this.selectedSrc = public+'/Home/images/shop/cart_select.png';
		this.emptySrc = public+'/Home/images/shop/cart_none.png';
	},
	methods: {
		// 点击轮播图
		bannerClk(link) {
			location.href = link;
		},
		// 请求主题商品数据
		getGoodsList() {
			var vm = this;
			$.ajax({
				url: shopurl,
				type: 'post',
				success: function(res){
					console.log('res: ',res);
					vm.blockList = res.goods;
					vm.menuList = res.cate;
					vm.banner = res.banner;
					vm.cartnum = res.cartInfo;
					vm.menuList.push({
						pic: public+'/Home/images/shop/house.png',
						name: '更多...',
						id: '8'
					})
					vm.$nextTick(function(){// 轮播图
						var mySwiper = new Swiper ('.swiper-container', {
							autoplay: 3000,
							loop: true,
							preventClicks : false,//默认true
							// 如果需要分页器
							pagination: '.swiper-pagination',
						})
						lazyLoad('#container');	// 图片懒加载
					})
				},
				error: function(err){
					console.log('err: ',err);
				}
			})
		},
		// 点击首页分类按钮
		menuBtnClk(index, name, id) {
			var vm = this;
			// console.log('id: ',id);
			if(name == '更多...'){
				location.href = shopurl + '#category';
				return
			}
			location.href = shopurl + '#category';
			vm.getCate({id: id, name:name}, id);
			$('.category>.fl>p').eq(index).addClass('cateSelect');
		},
		// 点击分类页的广告
		advClk(link){
			console.log('link: ', link);
			location.href = link;
		},
		// 点击商品图片
		todetail(gid) {
			// console.log('gid: ', gid);
			if(gid){
				location.href = shopdetail + '?gid=' + gid;
			}
		},
		// 点击底部导航按钮
		tabClick(tabclk, text){
			if(tabclk == 4){
				location.href = getURL('Home', 'vipCenter/index');
				return;
			}
			// tabclk: 1首页，2分类，3购物车，
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
			for(var i=0;i<$('.category>.fl>p').length; i++){
				$('.category>.fl>p').eq(i).removeClass('cateSelect');
			}
			window.clickCid = cid;
			vm.cateSelect = cate;
			// console.log('cate: ',cate);
			// console.log('cid: ',cid);
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
							var mySwiper1 = new Swiper ('.swiper-container1', {
								autoplay: 3000,
								loop: true,
								preventClicks : false,//默认true
								// 如果需要分页器
								pagination: '.swiper-pagination1',
							})
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

		},
		// 点击大分类下的小分类商品图片
		subClick(cid) {
			// id:一级分类is, cid:二级分类id
			console.log('cid: ',cid);
			location.href = shoplist + '?id=' + this.categoryID + '&cid=' + cid;
		},
        // 搜索
        searchFn(search) {
			console.log('search: ',search);
			if(search){
				// 跳到搜索列表页
				location.href = shoplist + '?search=' + search;
			}
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
		deleteCart(index, gid, e) {
			var vm = this;
			var el = e.currentTarget;
			console.log('gid: ',gid);
			console.log('index: ',index);
			layer.confirm('确认删除？删除后无法恢复！', 
	            {
	              btn: ['确定','取消'] //按钮
	            }, 
	            function(){
		            // 发送ajax让后台删除这条地址在数据库的数据
		            $.ajax({
		            	url: getURL('Home', 'ShoppingCart/cartDel'),
		            	type: 'post',
		            	data: {'id': gid},
		            	success: function(res){
		            		console.log('成功！', res)
		            		//后台返回参数确认删除成功
		            		if(res.code == 200){
								// 删除这条数据
								vm.cartList.splice(index, 1);
		            			layHint('删除成功！');
		            		}else{
		            			layHint('删除失败！');
		            		}
							return true;
		            	},
		            	error: function(res){
		            		console.log('失败！', res)
		            		layHint('删除失败！');
		            	}
					})
	            },
	            function(){
	                layer.msg('已取消！', {icon: 1});
	                return false;
	            }
	        );
			
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
				vm.moneyCalc[index] = {gid: gid,price: price,num: num,skuattr: ''};
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
			// 同步结算勾选数量、金额
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
						num: vm.cartList[i].num,
						skuattr: '',
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
			var arr = [];
			// for(var i=0;)
			// vm.upInfo = {
			// 	gid: vm.goodsInfo.id,
			// 	money: (+vm.goodsInfo.price)*(+vm.numVal),  // 总价
			// 	skuattr: vm.checkList,
			// 	price: vm.goodsInfo.price,
			// 	num: vm.numVal
			// };
			// arr.push(vm.upInfo);
				
			// 购物车结算
			$.ajax({
				url: '',
				data: {info: JSON.stringify(arr)},
				type: 'post',
				success: function(res){
					console.log('res: ',res);
					if(res.code == 200){
						location.href = payConfirm;
					}else{
						layuiHint(res.msg);
					}
				},
				error: function(err){
					console.log('err: ',err);
				}
			})
		}
	}
})