var shopindex = new Vue({
	el: '.shopindex',
	data() {
		return {
			menuList: [],	// 分类按钮
			blockList: [],	// 商品块集合
		}
	},
	created() {
		var vm = this;
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
	},
	methods: {
		// 点击商品图片
		todetail(gid) {
			console.log('gid: ', gid);
		},
	}
})