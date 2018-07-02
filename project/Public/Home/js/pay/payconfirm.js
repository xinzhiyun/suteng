var payConfirm = new Vue({
	el: '.payConfirm',
	data() {
		return {
			info: {},		// 所有数据
			allMoney: 0,	// 总价格
			address: true,
			express: [],
			einfo: [],	// 选择的快递信息
		}
	},
	created() {
		var vm = this;
		vm.express = [
			{name: '中通快递', money: '12'},
			{name: '申通快递', money: '16'},
			{name: '圆通快递', money: '17'},
		]
		vm.express = info.goodsCourier['14'];
		vm.info = info;
		// 模拟
		vm.info = {
			user: {
				name: '小鸡壳',
				phone: '15315645789',
				address: '广州市番禺区大夫山北段362号钟村镇点球电子科技集团'
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
		var goods = vm.info.order.goods;
		// 计算总价格
		for(var i=0; i<goods.length; i++){
			vm.allMoney += (+goods[i].price)*(+goods[i].num);
		}
	},
	methods: {
		// 请求订单数据
		getOrderInfo() {
			$.ajax({
				url: payConfirm,
				type: 'get',
				success: function(res){
					if(res.code == 200){
						// vm.info = res.data;
					}
				},
				error: function(err){}
			})
		},
		// 快递选择
		expressChange(){
			var vm = this;
			var express = $('.express');
			for(var i=0; i<express.length; i++){
				var temp = $(express[i]).find('option:selected');
				vm.einfo.push({
					id: temp[0].getAttribute('eid'),
					name: temp[0].getAttribute('value'),
				})
				console.log('temp: ',temp);
			}
			console.log('vm.einfo: ',vm.einfo);
		}
	}
})