var payment = new Vue({
	el:"#app",
	data(){
		return{
			blue:".blue",
			gray:".gray",
			search:"",			//搜索
			deviceCode:[],		//设备编码
			showModel: "" ,		//选择设备编码字体图标
			userInfo:"",		//用户信息
			setMeal:[],			//套餐
			money:"0",			//支付金额
			way:"微信支付",				//支付方式
		}
	},
	methods:{
		urlPub:function(key,value){
	      var url = window.document.location.href.toString();
	      var href = url.split("?")[0];
	      location.href = href+"?"+key+"="+value;
	    },
		// 查找
		lookup(){
			var _this = this;
			var reg = /^(1[35789]\d{9}|\d{15})$/;//手机号码
			console.log(_this.search)
			if(_this.search == ""){
				noticeFn({text:"请输入手机号码/设备编码",time:"1500"});
				return false;
			}else if(!reg.test(_this.search)){
				noticeFn({text:"您输入的格式不正确！",time:"1500"});
				return false;
			}else{
				// 查找手机号码/设备编码
				$.ajax({
					url:"",
					data:"",
					type:"post",
					Type:"json",
					success:function(res){
						console.log("成功",res);
					},
					error:function(res){
						console.log("失败",res);

					}
				});
				console.log("成功查找数据!");
				$("#mask").show();
				_this.deviceCode = [{code:"868575025659777"},{code:"86857502565888"},{code:"86857502565999"}];
			}
		},
		// 选择设备编码
		select(even){
			// var _this,e,el,index,deCode;
			var _this = this;
			var e = even || window.event;
			e.preventDefault();
			var el = e.target;
			var index = $(el).parent("li").attr("index");
			switch(index){
				case index:
					_this.showModel = index;
					break;
			}
			var deCode = $(el).parent("li").children().eq(0).text();
			_this.urlPub("deCode",deCode);
			$(".search").hide().next().hide().next().show();
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
		},
		// 选择支付方式体图标
		Wayselect(even){
			var _this = this;
			var e = even || window.event;
			e.preventDefault();
			var el = e.target;
			var index = $(el).parent("li").attr("index");
			_this.way= $(el).text();
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
			if(_this.way == "微信支付"){
				console.log("微信支付");
				_this.urlPub("Pay",1);
				return false;
			}else if(_this.way == "支付宝支付"){
				console.log("支付宝支付");
				_this.urlPub("Pay",1);
				return false;
			}else if(_this.way == "银联支付"){
				console.log("银联支付");
				_this.urlPub("Pay",1);
				return false;
			}else{
				noticeFn({text:"请选择支付方式",time:"1500"});
				return false;
			}
			setTimeout(function(){
				var url = getURL("Home","Index/payment");
				location.href = url;
			},1000);
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
			}
		}
	},
	mounted:function(){
	}
});
// 13526241555
// icon-selectcircle  选中