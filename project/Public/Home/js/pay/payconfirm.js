var payConfirm = new Vue({
	el: '.payConfirm',
	data() {
		return {
			info: {},		// 所有数据
			goods: [],		// 订单数据
			order_id: '',	// 订单id
			allMoney: 0,	// 总价格
			express: [],
			einfo: [],		// 选择的快递信息
			invoice: '我要开发票', 	//发票信息
			voiceArr: '',
			payway: '',		// 1:金币，2：银币，3：微信支付
			noticeText: '加载中...',
		}
	},
	created() {
		var vm = this;
		var href = location.href;
		vm.order_id = href.substr(href.indexOf('?order_id=')+10);
		console.log('order_id: ',vm.order_id);
		sessionStorage.setItem("order_id", vm.order_id);
		vm.info = info;
		// 请求订单数据
		vm.getOrderInfo();
		// 发票
		if(sessionStorage.getItem("voiceArr")){
			vm.voiceArr = JSON.parse(sessionStorage.getItem("voiceArr"));
			if(vm.voiceArr.type){
				vm.invoice = vm.voiceArr.type;
			}
			$('.invoice .icon-weixuanzhong').attr('class','iconfont icon-xuanzhongduigou');
		}else{
			$('.invoice .icon-xuanze').attr('class','iconfont icon-weixuanzhong');
		}
	},
	methods: {
		// 请求订单数据
		getOrderInfo() {
			var vm = this;
			$.ajax({
				url: getGoodsByOrder,
				type: 'get',
				success: function(res){
					if(res.code == 200){
						$(".loading").css("display", "none");
						for(var i=0;i<3; i++){
							vm.goods = res.msg;
						}
						// vm.goods = res.msg;
						// 默认第一个快递
						for(var i=0; i<res.msg.length; i++){
							vm.einfo.push({
								cid: res.msg[i].Courier[0].cid,
								cname: res.msg[i].Courier[0].cname,
								cprice: res.msg[i].Courier[0].cprice,
							})
						}
						var postage = {
							gid: vm.goods[0].gid,
							order_id: vm.order_id,
							cid: res.msg[0].Courier[0].cid,
							cname: res.msg[0].Courier[0].cname,
							cprice: res.msg[0].Courier[0].cprice,
						};
						// 提交快递信息
						vm.upPostage(postage);
						// 计算价格
						vm.calcAllmoney();
					}
				},
				error: function(err){}
			})
		},
		// 快递选择
		expressChange(index){
			var vm = this;
			var express = $('.express');
			vm.einfo = [];
			var postage = {};
			for(var i=0; i<express.length; i++){
				var temp = $(express[i]).find('option:selected');
				vm.einfo.push({
					gid: vm.goods[index].gid,
					order_id: vm.order_id,
					cid: temp[0].getAttribute('eid'),
					cname: temp[0].getAttribute('value'),
					cprice: temp[0].getAttribute('price')
				})
				postage = {
					gid: vm.goods[index].gid,
					order_id: vm.order_id,
					cid: temp[0].getAttribute('eid'),
					cname: temp[0].getAttribute('cname'),
					cprice: temp[0].getAttribute('price')
				}
				console.log('temp: ',temp);
			}
			console.log('vm.einfo: ',vm.einfo);
			// 计算价格
			vm.calcAllmoney();
			vm.upPostage(postage);
		},
		// 计算总价格
		calcAllmoney() {
			var vm = this;
			vm.allMoney = 0;
			// 商品价格
			for(var i=0; i<vm.goods.length; i++){
				vm.allMoney += (+vm.goods[i].price)*(+vm.goods[i].num);
			}
			// 快递费
			for(var j=0; j<vm.einfo.length; j++){
				for(var k=0; k<vm.goods.length; k++){
					vm.allMoney += (+vm.einfo[j].cprice)*(+vm.goods[k].num);
				}
			}
		},
		// 提交快递信息
		upPostage(postage) {
			$.ajax({
				url: updateOrder,
				data: {postage: postage},
				type: 'post',
				success: function(res){
					console.log('res: ',res);
				},
				error: function(err){}
			})
		},
		// 选择支付方式
		choosePay(index) {
			var vm = this;
			vm.payway = index;
			console.log('index: ',index);
			for(var i=0; i<$('.paymidd .iconfont').length;i++){
				$('.paymidd span.iconfont').eq(i).attr('class','iconfont icon-weixuanzhong');
			}
			$('.paymidd span.iconfont').eq(+index-1).attr('class','iconfont icon-xuanzhongduigou');
		},
		// 获取支付用的数据
		prePay() {
			$('.paystyle').animate({top: 0});
		},
		// 立即支付
		payNow() {
			var vm = this;
			console.log('payway: ',vm.payway);
			if(!info.data){
				layuiHint('请先添加个地址');
				setTimeout(function(){
					location.href = getURL('Home', 'address/newAddress');
				},1000)
				return
			}
			if(!vm.payway){
				layuiHint('请选择支付方式');
				return;
			}
			var way;
			if(vm.payway == 1){
				// 金币支付
				way = orderPayByGold;
			}else if(vm.payway == 2){
				// 银币支付
				way = orderPayBySilver;
			}else if(vm.payway == 3){
				// 微信支付
				way = orderPay;
			}
			console.log('way: ',way);
			$.ajax({
				url: way,
				type: 'post',
				data: {order: vm.order_id},
				success: function(res){
					console.log('res: ',res);
					if(vm.payway == 1 || vm.payway == 2){
						if(res.code == 200){
							// 金币、银币支付
							layuiHint('支付成功');
							// setTimeout(function(){
							// 	history.replaceState({}, null, getURL('Home', 'Shop/index'));//改变历史记录
							// 	location.href = getURL('Home', 'PaymentSystem/paytosuccess');
							// },500);
						}else{
							layuiHint(res.msg);
						}
						return;
					}
					// 调用微信支付方法
					weixinPay(res,function(res){
						if(res.result == 'ok'){
							layuiHint('支付成功');
							setTimeout(function(){
								history.replaceState({}, null, getURL('Home', 'Shop/index'));//改变历史记录
								location.href = getURL('Home', 'PaymentSystem/paytosuccess');
							},500);
						}else if(res.result == 'other'){
							layuiHint('支付失败');
						}else{
							layuiHint('遇到未知问题，请稍后再试');
						}
					});
				}
			})
			
		},
		// 关闭支付面板
		closepp() {
			$('.paystyle').animate({top: '150%'});
		}

	}
})