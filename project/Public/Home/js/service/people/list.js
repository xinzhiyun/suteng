var take = new Vue({
	el:"#app",
	data(){
		return{
			search: "",
			timetype: '预约时间',
			list: [],		//列表
			type: '',
			notice: '加载中...',
			noticestyle: 'block',
			loadstyle: 'block',
			page: ''
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
		// 点击搜索小图标或回车
		subSea(word){
			var vm = this;
			console.log('word: ',word);
			if(!word){
				layuiHint('请输入手机号');
				return
			}
			$.ajax({
				url: getURL('Home', 'servicePeople/getList'),
				type: 'get',
				data: {word: word, type: vm.type},
				success: function(res){
					console.log('res: ',res);
					if(res.status == 200){
						if(!res.data.length){
							vm.list = [];
							layuiHint('查无记录');
							vm.loadstyle = 'none';
							return
						}
						vm.noticestyle = 'none';
						vm.list = res.data;
						vm.page = 1;
					}else{
						vm.list = [];
						layuiHint('查无记录');
						vm.loadstyle = 'none';
					}
				},
				error: function(err){
					console.log('err: ',err);
				}
			})
		},
		// 加载更多
		loadmore() {
			var vm = this;
			// 请求分页数据
			this.getList(function(res){
				console.log('more_res: ',res);
				if(res.status == 200){
					if(!res.data.length){
						layuiHint('没有更多数据');
						vm.loadstyle = 'none';
						return
					}
					vm.noticestyle = 'none';
					for(var i=0; i<res.data.length; i++){
						vm.list.push({
							number: res.data[i].number,
							device_type: res.data[i].device_type,
							anry_time: res.data[i].anry_time,
							pass_at: res.data[i].pass_at
						})
					}
					vm.page++;
				}else{
					layuiHint('没有更多数据');
					vm.loadstyle = 'none';
				}
			}, this.type, this.page++)
		},
	    // 服务详情
		getDetail(wid){
			// number 工单号
			// type: 工单类型：1
			var _this = this;
			location.href = getURL('Home', 'servicePeople/detail')
			 + '?type=' + this.type
			 + '&wid=' + wid;
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
			console.log('listres: ',res);
			if(res.status == 200){
				if(!res.data.length){
					vm.notice = '暂无数据';
					layuiHint('查无数据');
					vm.loadstyle = 'none';
					return
				}
				vm.noticestyle = 'none';
				vm.list = res.data;
				vm.page = 1;
			}else{
				layuiHint(res.msg);
				vm.loadstyle = 'none';
			}
		}, this.type);

	}
});