var servicedetail = new Vue({
	el: '#servicedetail',
	data () {
		return {
			type: '',
			result: '',// result: 0待处理 1处理中 2待验收 3(完成)
			workid: '',//工单号
			info: {},//用户信息
			picList: [],
			confirm : "提交",
			installer: "",//安装工人信息
			installInfo : {
				id: "",
				name: "",
				phone: ""
			},//安装工人提交信息
			ifFlag : false
		} 
	},
	created (){
		this.workid = GetQueryString('workid');//工单号
		this.result = GetQueryString('result');
		// 不同状态改变按钮 标题
		if(this.result == 0) {
			this.confirm = "提交";
			$("title").html("待办工单");
		}else if(this.result == 1) {
			// $(".btnup").attr("disabled", "disabled").css("backgroundColor", "rgb(46,182,170,.5)");
			$(".btnup").css("display", "none");//隐藏提交按钮
			$("title").html("处理中工单");
		}else if(this.result == 2) {
			this.confirm = "审核通过";
			$("title").html("待验收工单");
		}else if(this.result == 3 || this.result == 9) {
			$(".btnup").remove();
			$("title").html("已完成工单");
		}
		
		console.log('result: ',this.result, "workid", this.workid);
		// 根据工单号请求后台拿到数据
		this.getDetail(this.workid);
	},
	methods: {
		getDetail (workid){
			// 获取详情信息
			$.ajax({
				url: getURL('Home', 'Service/getDetail'),
				type: 'post',
				data: {number: workid},
				success: function(res){
					console.log('res: ',res.data);
					if(res.status == 200){
						var typeList = ['安装', '维修', '维护'];
						res.data.name = res.data.name ? res.data.name : '未派工';
						res.data.type = typeList[res.data.type];// 工单类型
						
						res.data.pic = res.data.pic ? JSON.parse(res.data.pic) : [];// 工单图片
						servicedetail.info = res.data;//用户信息
						if(res.data.pic.length){
							for(var i=0; i<res.data.pic.length; i++){
								servicedetail.picList.push({
									path: res.data.pic[i]
								})
							}
						}
						if(res.people.length) { 
							servicedetail.installer = res.people;//安装工人
						}else {
							servicedetail.installer = ['请选择','暂无工人'];
						}
						// 时间线
						res.data.create_at = dateformat(res.data.create_at); //工单生成时间
						res.data.examine_at = res.data.examine_at ? dateformat(res.data.examine_at) : '-- -- -- ';//后台审核时间
						res.data.anry_at = res.data.anry_at ? dateformat(res.data.anry_at) : '-- -- --';//派工时间
						res.data.check_at = res.data.check_at ? dateformat(res.data.check_at) : '-- -- --';//售后确认时间
						res.data.pass_at = res.data.pass_at ? dateformat(res.data.pass_at) : '-- -- --';//工单完成时间
						res.data.over_at = res.data.over_at ? dateformat(res.data.over_at) : '-- -- --';//工单结束时间
						
					}else{
						layuiHint(res.msg);
					}
				},
				error: function(err){
					console.log('err: ',err);
				}
			})
		},
		changeC () {
			$("#changeC").css("color", "#7A7A7A");
		},
		changeC2 (e) {
			
			var el = e.currentTarget;
			this.installInfo.phone = $(el).find('option:selected').attr("index");
			this.installInfo.id = $(el).find('option:selected').attr("title");
			this.installInfo.name = $(el).find('option:selected').val();
			$("#changeC2").css("color", "#7A7A7A");
		},
		getit () {
			var vm = this;
			// 我要接单
			if(vm.result == "0") {
				// dw_uid 服务人员的ID name 服务人 phone 服务人电话 anry_time   年月日 anry_period 时间段
				var date = $(".currentdate").val();//预约日期
				var anzhuang = $("#changeC2").val();
				var time = $("#changeC").val();//预约时间
				// 验证提示
				if(!date) {
					layuiHint("请选择预约日期");
					return ;
				}
				if(!time) {
					layuiHint("请选择预约时间");
					return ;
				}
				if(!anzhuang) {
					layuiHint("请选择安装人员");
					return ;
				}
				console.log("预约时间", time, "预约日期", date, "安装工人信息",vm.installInfo);
				$.ajax({
					url: getURL("Home", "Service/packwork"),
					type: "post",
					data: {
						dw_uid: vm.installInfo.id,
						name: vm.installInfo.name,
						phone: vm.installInfo.phone,
						anry_time: date,
						anry_period: time,
						number: vm.workid
					},
					success: function(res) {
						console.log("成功", res);
						if(res.status == 200) {
							layuiHint(res.msg);
							setTimeout(function() {
								location.href = getURL('Home', 'Service/paySuccess')+'?title=提交成功';
							}, 1000);
						}else {
							layuiHint(res.msg);
						}
					},
					error: function(res) {
						console.log("出错", res);
						layuiHint("系统出错，请稍后重试");
					}
				})
			}else if(vm.result == "2") {	// 点击审核通过
				console.log(vm.workid)
				if(vm.info.name == '未派工')｛
					layuiHint('未派工，请先派工处理');
					return
				｝
				layui.use('layer', function(){
					var layer = layui.layer;
					layer.confirm("确认通过审核?", function(index) {
						$.ajax({
							url: getURL("Home", "Service/checkPass"),
							type: "post",
							data: {number: vm.workid},
							success: function(res) {
								console.log("待验收", res)
								if(res.status == 200) {
									layuiHint(res.msg)
									setTimeout(function() {
										location.href = getURL('Home', 'Service/paySuccess')+'?title=提交成功';
									}, 1000);
								}else {
									layuiHint(res.msg);
								}
							},
							error: function(res) {
								console.log("失败", res)
								layuiHint("系统出错, 请稍后再试");	
							}
						})
						layer.close(index);
					})
				});  
				
			}
		},
		// 日期
		selectDate () {
			//选择服务日期，
			laydate.render({
				elem: '#currentdate', //指定元素
				show: true,
				format: 'yyyy-MM-dd',
				btns: ['clear', 'confirm'],
				min: 0,
				done: function(value, date, endDate){
					$(".currentdate").css("color", "#7A7A7A");
				}
			});
		},
		// 显示时间线
		tiemF() {
			if(this.ifFlag) {
				$(".icon-icon123").removeClass("iconfont icon-icon123 rdeg").addClass("iconfont icon-icon123 rdeg2");
				$(".dTime").slideUp("slow");
				this.ifFlag = false;
			}else {
				$(".icon-icon123").removeClass("iconfont icon-icon123 rdeg2").addClass("iconfont icon-icon123 rdeg");
				$(".dTime").slideDown("slow");
				this.ifFlag = true;
			}
		},
		openPhotoSwipe(index) {
			console.log('index: ',index);
			var pswpElement = document.querySelectorAll('.pswp')[0];
			var items = [];
			for(var i = 0; i < servicedetail.picList.length; i++) {
				items.push({
					src: servicedetail.picList[i].path,
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
	}
})	