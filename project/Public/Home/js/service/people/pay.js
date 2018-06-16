var payment = new Vue({
	el:"#app",
	data(){
		return{
			search:"",			//搜索
			deviceCode:[],		//设备编码
			showModel: "8" ,		//选择设备编码字体图标
			userInfo: {},		//用户信息
			setMeal:[],			//套餐
			device_code: '',	// 设备id
			mealid: '',			// 选中的套餐id
			money:"0",			//支付金额
			index:"",			//支付方式的下标
			seplaceholder: '请输入手机号',		// 按照手机或设备编码搜索
			searchtype: '1',
			way:["微信支付","支付宝支付","银联支付"],	//支付方式
		}
	},
	methods:{
		urlPub(key,value){
	      var url = window.document.location.href.toString();
	      var href = url.split("?")[0];
	      location.href = href+"?"+key+"="+value;
	    },
	    // 选择手机号或者设备编码搜索
	    searchchange(e) {
	    	var type = e.currentTarget.getAttribute('value');
	    	this.searchtype = type;
	    	// console.log('type: ',type);
	    	if(this.searchtype == 2){
	    		this.seplaceholder = '请输入设备编码';

	    	}else if(this.searchtype == 1){
	    		this.seplaceholder = '请输入手机号码';
	    	}
	    },
		// 查找
		lookup(word){
			var vm = this;
			console.log('word: ',word);
			if(!word){
				layuiHint('请输入手机号或设备编码查询');
				return
			}
			// 查找手机号码/设备编码
			// type: 1手机号，2设备编码
			$.ajax({
				url: getURL('Home', 'ServicePeople/deviceSearch'),
				data: {word: word, type: this.searchtype},
				type: "get",
				success: function(res){
					console.log("res: ",res);
					if(res.status == 200){
						if(!res.data.length){
							layuiHint('查无数据');
							return
						}
						vm.deviceCode = res.data;
						$("#mask").show();
					}else{
						layuiHint(res.msg);
					}
				},
				error: function(err){
					console.log("err: ",err);
				}
			});
			
		},
		// 选择设备编码
		select(device_code, e){
			var _this = this;
			var el = e.target;
			$(el).parent("li").css({"background":"#f5f5f5"}).siblings().css({"background":"#fff"});
			var index = $(el).parent("li").attr("index");
			switch(index){
				case index:
					_this.showModel = index;
					break;
			}
			console.log("device_code: ",device_code);
			setTimeout(function(){
				_this.urlPub("device_code",device_code);
				$(".search").hide().next().hide().next().show();
			},100);
		},
		// 选择套餐
		mealSelect(money,mealid,even){
			var e = even || window.event;
			e.preventDefault();
			var el = e.currentTarget;
			var _this = this;
			_this.money = money;
			_this.mealid = mealid;
			$(el).css({"background":"#2EB6AA","color":"#fff",border:'none'}).siblings().css({"background":"#fff","color":"#808080",border:'1px solid #cccccc'});
			$("#Pay").css({"background":"#2eb6aa"});
		},
		// 支付
		PayMask(money){
			if(money == "0"){
				noticeFn({text:"您还未选择套餐呦，请先选择再支付！",time:"1500"});
				return false;
			}
			$(".mask2").show();
			$("#two").css("color","#B3B3B3");
			$("#three").css("color","#2EB6AA");

		},
		// 选择支付方式字体图标
		Wayselect(even){
			var _this = this;
			var e = even || window.event;
			e.preventDefault();
			var el = e.currentTarget;
			var index = $(el).attr("index");
			_this.index = index;
			switch(index){
				case index:
					_this.showModel = index;
					break;
			}
		},
		// 关闭
		close(){
			$(".mask2").hide();
		},
		// 立即支付
		payBut(){
			var _this = this;
			if(_this.way[_this.index] == "微信支付"){
				console.log("微信支付");
				getPayInfo(_this.device_code, _this.mealid);
				// _this.urlPub("Pay",1);
				return false;
			}else if(_this.way[_this.index] == "支付宝支付"){
				console.log("支付宝支付");
				// _this.urlPub("Pay",1);
				return false;
			}else if(_this.way[_this.index] == "银联支付"){
				console.log("银联支付");
				// _this.urlPub("Pay",1);
				return false;
			}else{
				noticeFn({text:"请选择支付方式",time:"1500"});
				return false;
			}
		}
	},
	created (){
		var _this = this;
		var href = location.search.split("?")[1];
		_this.device_code = GetQueryString('device_code');
		var pay = GetQueryString('Pay');
		if(href == undefined){
			$("#one").css("color","#2EB6AA");
		}
		if(_this.device_code){
			console.log('device_code: ',_this.device_code);
			$("#two").css("color","#2EB6AA");
			$(".search").hide().next().hide();
			$(".detail").show();
			// 将设备编码传后台
			$.ajax({
				url: getURL('Home', 'ServicePeople/getSetmeal'),
				data: {device_code:_this.device_code},
				type: "post",
				success:function(res){
					console.log("res: ",res);
					if(res.status == 200){
						if(!res.data.length){
							layuiHint('暂无套餐数据，无法充值');
							return
						}
						_this.userInfo = {
							name: res.info.name,
							phone: res.info.phone,
							device_code: _this.device_code,
							device_type: res.info.device_type
						}
						_this.setMeal = res.data;
						console.log('setMeal: ',_this.setMeal);
					}else{
						layuiHint(res.msg);
					}
				},
				error:function(res){
					console.log("err: ",res);

				}
			})
		}else if(pay){
			$("#two").css("color","#B3B3B3");
			$("#three").css("color","#2EB6AA");
			$(".search").hide().next().hide();
			$('.paySuc').show();
			$("body").css({"background":"#fff"});
			setTimeout(function(){
				
				location.href = url;
			},1000);
		}
	},
	mounted(){
	}
});
// 13526241555
// icon-selectcircle  选中