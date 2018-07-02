var payConfirm = new Vue({
	el: '.payConfirm',
	data() {
		return {
			info: {},		// 所有数据
			allMoney: 0,	// 总价格
		}
	},
	created() {
		var vm = this;
		// 模拟
		vm.info = {
			address: {
				name: '小即可',
				phone: '15315645789',
				addr: '广州市番禺区大夫山北段362号钟村镇点球电子科技集团'
			},
			order: {
				money: '189',	// 合计价格
				postage: '8',	// 邮费
				express: '',	// 快递
				sendAddr: '',	// 发货地
				integral: '',	// 所需积分
				goods: [
					{
						id: '11',
						pic: '1111111',
						name: '滤芯外部活性炭',
						skuattr: '',
						price: '119',
						num: '1'
					},
					{
						id: '12',
						pic: '22222222',
						name: '滤芯外部活性炭',
						skuattr: '',
						price: '119',
						num: '1'
					}
				]
			}
		};
		// 计算总价格
		for(var i=0; i<vm.info.goods.length; i++){
			vm.allMoney += (+vm.info.goods[i].price)*(+vm.info.goods[i].num);
		}
	},
	methods: {}
})