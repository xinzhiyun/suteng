var payment = new Vue({
	el:"#app",
	data(){
		return{
			search:"",			//搜索
			deviceCode:[],		//设备编码
			showModel: "8" ,		//选择设备编码字体图标
			userInfo:"",		//用户信息
			setMeal:[],			//套餐
			money:"0",			//支付金额
			index:"",			//支付方式的下标
			seplaceholder: '请输入搜索关键字',		// 按照手机或设备编码搜索
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
	    	console.log('type: ',type);
	    	if(this.searchtype == 2){
	    		this.seplaceholder = '请输入设备编码';

	    	}else if(this.searchtype == 1){
	    		this.seplaceholder = '请输入手机号码';
	    	}
	    },
		// 查找
		lookup(word){
			console.log('word: ',word);
			// 查找手机号码/设备编码
			// type: 1手机号，2设备编码
			$.ajax({
				url: getURL('Home', 'ServicePeople/deviceSearch'),
				data: {word: word, type: this.searchtype},
				type: "post",
				success: function(res){
					console.log("res: ",res);
				},
				error: function(err){
					console.log("err: ",err);
				}
			});
			$("#mask").show();
		},
		// 选择设备编码
		select(even){
			var _this = this;
			var e = even || window.event;
			e.preventDefault();
			var el = e.target;
			$(el).parent("li").css({"background":"#f5f5f5"}).siblings().css({"background":"#fff"});
			var index = $(el).parent("li").attr("index");
			switch(index){
				case index:
					_this.showModel = index;
					break;
			}
			var deCode = $(el).parent("li").children().eq(0).text();
			setTimeout(function(){
				_this.urlPub("deCode",deCode);
				$(".search").hide().next().hide().next().show();
			},1000);
		},
		// 选择套餐
		mealSelect(money,even){
			var e = even || window.event;
			e.preventDefault();
			var el = e.currentTarget;
			var _this = this;
			_this.money = money;
			$(el).css({"background":"#2EB6AA","color":"#fff"}).siblings().css({"background":"#fff","color":"#808080"});
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
				_this.urlPub("Pay",1);
				return false;
			}else if(_this.way[_this.index] == "支付宝支付"){
				console.log("支付宝支付");
				_this.urlPub("Pay",1);
				return false;
			}else if(_this.way[_this.index] == "银联支付"){
				console.log("银联支付");
				_this.urlPub("Pay",1);
				return false;
			}else{
				noticeFn({text:"请选择支付方式",time:"1500"});
				return false;
			}
		}
	},
	created:function(){
		var _this = this;
		var href = location.search.split("?")[1];
		if(href == undefined){
			$("#one").css("color","#2EB6AA");
		}else{
			var key = href.split("=")[0];
			var value = href.split("=")[1];
			$("#two").css("color","#2EB6AA");
			if(key == "deCode"){
				$(".search").hide().next().hide().next().show();
				// 将设备编码传后台
				$.ajax({
					url:"",
					data:{code:value},
					type:"post",
					Type:"json",
					success:function(res){
						console.log("成功",res);
					},
					error:function(res){
						console.log("失败",res);

					}
				});
				// 用户信息
				var userInfo = {name:"沈腾",phone:"13526325422",code:"868575025659666",model:"净水机 BL (axl-114)"};
				// 套餐
				var setMeal = [{day:"30",money:"100"},{day:"60",money:"200"},{day:"90",money:"300"}]
				_this.userInfo = userInfo;
				_this.setMeal = setMeal;
			}else if(key == "Pay"){
				$("#two").css("color","#B3B3B3");
				$("#three").css("color","#2EB6AA");
				$(".search").hide().next().hide().next().hide().next().hide().next().show();
				$("body").css({"background":"#fff"});
				setTimeout(function(){
					var url = getURL("Home","Index/payment");
					location.href = url;
				},1000);
			}
		}
	},
	mounted:function(){
	}
});
// 13526241555
// icon-selectcircle  选中