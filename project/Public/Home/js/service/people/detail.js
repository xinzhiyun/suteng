var take = new Vue({
	el:"#app",
	data(){
		return{
			search: "",
			detail: {},		//列表
			type: '',
			wid: '',		// 工单id
			notice: '加载中...',
			noticestyle: 'block',
			success: false,
			picList: [],
			qa: '',
			noticetext: ''
		}
	},
	methods:{
		// 完成，确认弹框
		openModal(number){
			// number: 1拒绝，2完成
			if(number == 1){
				this.noticetext = '确定忽略此单?';
			}else if(number == 2){
				this.noticetext = '确定完成了吗?';
			}
			this.qa = number;
			$('#my-alert').modal();  
		},
		// 确认发送数据给后台
		Yes(){
			// qa: 1拒绝，2完成
			console.log('qa: ',this.qa);
			this.operateWork(this.qa);
			
			// 隐藏弹出框
			$(".wrap").hide().next().hide().next().show();
		},
		// 工单操作
		operateWork(qa){
			// qa: 1拒绝，2完成
			$.ajax({
				url: getURL('Home', 'ServicePeople/passWork'),
				type: 'post',
				data: {wid: this.wid, operate: this.qa},
				success: function(res){
					console.log('res: ',res);
					if(res.status == 200){
						setTimeout(function(){
							location.href = getURL('Home', 'ServicePeople/index');
						},1000)
					}else{
						layuiHint(res.msg);
					}

				},
				error: function(err){
					console.log('err: ',err);
					layuiHint('系统出错，请稍后再试');
				}
			})
		},
		// 获取详情数据
		getDetail (workid, callback) {
			var vm = this;
			console.log('workid: ',workid);
			$.ajax({
				url: getURL('Home', 'ServicePeople/getDetail'),
				type: 'post',
				data: {wid: workid},
				success: function(res){
					console.log('res: ',res);
                    callback(res);
                },
				error: function(err){
					console.log('err: ',err);
				}
			})
		},
		// 图片查看
		photoSwipe(index) {
			var vm = this;
			var pswpElement = document.querySelectorAll('.pswp')[0];
			var items = [];
			for(var i = 0; i < vm.picList.length; i++) {
				items.push({
					src: vm.picList[i],
					w: 320,
					h: 640
				});
			}
			// define options (if needed)
			var options = {
			    // optionName: 'option value'
			    // for example:
			    history: true,
			    index: index // start at first slide
			};
			
			var gallery = new PhotoSwipe( pswpElement, PhotoSwipeUI_Default, items, options);
			gallery.init();
		}
	},
	created() {
		var vm = this;
		// 显示服务类型
		var textList = ['待安装', '待维修', '待维护'];
		this.type = GetQueryString('type');
		console.log('textList[+this.type]', textList[+this.type]);
		if(this.type != 'all'){
			$('#navbar>h2').text(textList[+this.type]);
			document.title = textList[+this.type];
		}else{
			$('#navbar>h2').text('工单详情');
			document.title = '工单详情';
			this.success = true;
		}
		this.wid = GetQueryString('wid');
		// 获取详情数据
		this.getDetail(this.wid, function(res){
			if(res.status == 200){
            	if(!res.data){
            		vm.notice = "查无数据";
					
				}
				vm.noticestyle = "none";
				for(var i in res.data){
					vm.detail[i] = res.data[i];
				}
				vm.detail.create_at = timetrans(vm.detail.create_at);
				vm.picList = JSON.parse(vm.detail.pic);
				console.log('vm.detail: ',vm.detail);
			}else{
				vm.notice = res.msg;
			}
		})
	},
	mounted() {
		$('.loadingdiv').hide();
	}
});