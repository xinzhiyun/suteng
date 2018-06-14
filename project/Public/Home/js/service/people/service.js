var service = new Vue({
	el:"#app",
	data(){
		return{
			search:"",		//input的值
			list:[],		//列表
			detail:"",		//服务详情
		}
	},
	methods:{
		// (公共)
		urlPub:function(key,value){
	      var url = window.document.location.href.toString();
	      var href = url.split("?")[0];
	      location.href = href+"?"+key+"="+value;
	    },
	    // 查找(公共)
		subPub(){
			var _this = this;
			var reg = /^(1[35789]\d{9}|\d{15})$/;//手机号码
			console.log(_this.search)
			if(_this.search == ""){
				noticeFn({text:"请输入姓名或者手机号码",time:"1500"});
				return false;
			}else if(!reg.test(_this.search)){
				noticeFn({text:"您输入的格式不正确！",time:"1500"});
				return false;
			}else if(chineseCheck(_this.search)){
				noticeFn({text:"您输入的格式不正确！",time:"1500"});
				return false;
			}else{
				// 查找手机号码/设备编码
				$.ajax({
					url:"",
					data:{data:_this.search},
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
			}
		},
		// 回车搜索
		lookup(){
			var _this = this;
			_this.subPub();
		},
		// 点击搜索小图标
		subSea(){
			var _this = this;
			_this.subPub();
		},
		// 服务详情
		userList(status){
			var _this = this;
			_this.urlPub("status",status);
		},
	},
	created:function(){
		var _this = this;
		var  href = location.href.split("?")[1];
		if(href == undefined){
			// 用户列表
			$(".wrap").show().next().hide();
		}else{
			// 服务详情
			var key = href.split("=")[0];
			var value = href.split("=")[1];
			if(key == "status"){
				console.log(key)
				console.log(value)
				$(".wrap").hide().next().show();
				// 通过手机号码查找用户信息
				$.ajax({
					url:"",
					data:{status:value},
					type:"post",
					Type:"json",
					success:function(res){
						console.log("成功",res);
					},
					error:function(res){
						console.log("失败",res);

					}
				});
				var detail = {
					name:"沈腾",
					phone:"13526325411",
					add:"广州市番禺区创源路三楼",
					model:"净水机BL(axl-114)",
					code:"868575025659808",
					time:"2018-06-01",
					type:"安装",
					origin:"用户",
				}
				_this.detail = detail;  //服务详情
			}
		}
	},
	mounted() {
		$('.loadingdiv').hide();
	}
});