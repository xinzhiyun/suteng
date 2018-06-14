var take = new Vue({
	el:"#app",
	data(){
		return{
			search: "",
			timetype: '预约时间',
			list: [],		//列表
			type: '',
			notice: '加载中...',
			noticestyle: 'block'
		}
	},
	methods:{
		// 获取列表数据
		getList (callback, type, page) {
			console.log(type, page)
			var vm = this;
			var option = {
				url: getURL('Home', 'ServicePeople/getList'),
				type: 'get',
				success: function(res){
					// console.log('list res: ',res);
					if(res.status == 200){
						callback(res);
					}else{
						vm.loadstyle = 'none';
						layuiHint('查无数据');
					}
				},
				error: function(err){
					console.log('err: ',err);
					layuiHint('系统出错，请稍后再试'); 
				}
			}
			// type: 0待安装, 1待维修，2待维护
			if(type+''){
				option['data'] = {};
				option['data']['type'] =  type;
			}
			if(page){
				// 请求分页数据
				if(typeof option['data'] != "object") {
					option['data'] = {};
				}
				option['data']['p'] = vm.page;
			}
			// 获取数据
			$.ajax(option);
		},
		// (公共)
		urlPub:function(key,value){
	      var url = window.document.location.href.toString();
	      var href = url.split("?")[0];
	      location.href = href+"?"+key+"="+value;
	    },
	    // 查找(公共)
		subPub(){
			var vm = this;
			
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
		getDetail(number){
			// number 工单号
			// type: 工单类型：1
			var _this = this;
			location.href = getURL('Home', 'servicePeople/detail')
			 + '?type=' + this.type
			 + '&number=' + number;
		}
	},
	created() {
		var vm = this;
		// 显示服务类型
		var textList = ['待安装', '待维修', '待维护'];
		this.type = GetQueryString('type');
		if(this.type != 'all'){
			$('#navbar>h2').text(textList[+this.type]);
			document.title = textList[+this.type];
		}else{
			vm.timetype = '完成时间';
			$('#navbar>h2').text('服务记录');
			document.title = '服务记录';
		}
		console.log('textList[+this.type]', textList[+this.type]);


		// 获取列表数据
		this.getList(function(res){
			console.log('res: ',res);
			if(res.status == 200){
				if(!res.data.length){
					vm.notice = '暂无数据';
					layuiHint('查无数据');
					return
				}
				vm.noticestyle = 'none';
				vm.list = res.data;
			}else{
				layuiHint(res.msg);
			}
		}, this.type);

	}
});