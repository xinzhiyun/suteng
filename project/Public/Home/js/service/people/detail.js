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
		}
	},
	methods:{
		// 完成，确认弹框
		openModal(){
			$('#my-alert').modal();  
		},
		// 确认发送数据给后台
		Yes(){
			console.log(333);
			// 传变量
			$.ajax({
				url:"",
				data:{},
				type:"post",
				Type:"json",
				success:function(res){
					console.log("成功",res);
				},
				error:function(res){
					console.log("失败",res);

				}
			});
			$(".wrap").hide().next().hide().next().show();
			setTimeout(function(){
				var url = getURL("Home","ServicePeople/wait_task");
				location.href = url;
			},1000);
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