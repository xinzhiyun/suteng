var take = new Vue({
	el:"#app",
	data(){
		return{
			search: "",
			detail: {},		//列表
			type: '',
			wid: '',		// 工单id
			notice: '加载中...',
			noticestyle: 'block'
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
		getDetail (workid) {
			var vm = this;
			console.log('workid: ',workid);
			$.ajax({
				url: getURL('Home', 'ServicePeople/getDetail'),
				type: 'post',
				data: {wid: workid},
				success: function(res){
					console.log('res: ',res);
                    if(res.status == 200){
                    	if(res.data && res.data.length){
                    		vm.noticestyle = "none";
							vm.detail = res.data;
							
						}else{
                    		vm.notice = "查无数据";
						}
					}else{
						vm.notice = res.msg;
					}
                },
				error: function(err){
					console.log('err: ',err);
				}
			})
		},
	},
	created() {
		// 显示服务类型
		var textList = ['待安装', '待维修', '待维护'];
		this.type = GetQueryString('type');
		console.log('textList[+this.type]', textList[+this.type]);
		$('#navbar>h2').text(textList[+this.type]+'详情');
		document.title = textList[+this.type]+'详情';
		this.wid = GetQueryString('number');
		// 获取详情数据
		this.getDetail(this.wid, function(res){
			console.log('res: ',res);
		})
	},
	mounted() {
		$('.loadingdiv').hide();
	}
});