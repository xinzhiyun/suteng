var editAddressOld;
//获取编辑地址的数据
if(sessionStorage.getItem("editAddressOld")){
	editAddressOld = JSON.parse(sessionStorage.getItem("editAddressOld"));
	console.log(editAddressOld);
	document.title = '编辑地址';	//设置当前页面title为编辑地址
	//将编辑的地址信息显示在编辑框中
	$(".name").val(editAddressOld.name);
	$(".phone").val(editAddressOld.phone);
	$(".areabtn").val(editAddressOld.province+editAddressOld.city+editAddressOld.area);
	$("#addressDetial").val(editAddressOld.address);
	if(editAddressOld.status == 0){
		//选的是默认地址
		$("#normalAddrdess")[0].checked = true;
	}else{
		$("#normalAddrdess")[0].checked = false;
	}
	// console.log($("#normalAddrdess")[0].checked)
}
//编辑地址改变
var addressChange = false;
$(".area").click(function(){
	addressChange = true;
})

var lvxinData = sessionStorage.getItem("lvxinData"),
	shopCar_data = sessionStorage.getItem("shopCar_data"),
	goods_data = sessionStorage.getItem("goods_data");
// 解决移动端虚拟键盘开启时底部内容被顶上来的问题
solveCompatible($("#footer"));
/*
	name: 收货人
	phone: 电话
	areaID: 市县
	town: 街道
 */
// 验证姓名，电话，地区的正则表达式
var nameReg, phoneReg, areaReg;
nameReg = /^[\w\-\u4e00-\u9fa5]{2,255}$/u;
phoneReg = /^1((((3[0-35-9])|([5|8][0-9])|(4[5|7|9])|66|(7[3|5-8])|(9[8|9]))\d)|(34[0-8]))\d{7}$/;
areaReg = /^[\w\-\u4e00-\u9fa5]{2,255}$/u;

// 点击“保存”
$("#saveAddress").click(function(){
	var nameRes = nameReg.test($(".name").val());
	var phoneRes = phoneReg.test($(".phone").val());
	var areaIDRes = nameReg.test(trimFn($("#areaID").val()));
	var addressDetialRes = nameReg.test($("#addressDetial").val());

	var editAddressObj = {};	//存储需要发送给后台的数据
	var editName = $(".name").val(),
		editPhone = $(".phone").val();
	if(sessionStorage.getItem("editAddressOld")){
		/*
			编辑地址
		 */

		if(editName != editAddressOld.name){		//如果修改了姓名
			editAddressObj['name'] = editName;
		}
		if(editPhone != editAddressOld.phone){		//如果修改了手机号
			editAddressObj['phone'] = editPhone;
		}
		if(addressChange){	//如果有修改地址

	    	editAddressObj['province'] = province;
	    	editAddressObj['city'] = city;
	    	editAddressObj['area'] = area;

	    	editAddressObj['province_id'] = province_id;
	    	editAddressObj['city_id'] = city_id;
	    	editAddressObj['area_id'] = area_id;
			editAddressObj['address'] = editAddressObj['province']+editAddressObj['city']+editAddressObj['area']+$("#addressDetial").val();
			
		}else{
			editAddressObj['address'] = editAddressOld['province']+editAddressOld['city']+editAddressOld['area']+$("#addressDetial").val();
		}
		// console.log(addressChange)
		// console.log('editAddressOld: ',editAddressOld)
		console.log('editAddressObj: ', editAddressObj)
		editAddressObj['id'] = editAddressOld.id;		//地址id
		if(nameRes && phoneRes && areaIDRes && addressDetialRes){

    		console.log('通过！');
	    	if($("#normalAddrdess").attr("checked")){
	    		//设为默认地址
	    		editAddressObj['status'] = 0;
	    	}else{
	    		editAddressObj['status'] = 1;
	    	}
	    	console.log(editAddressObj);
	    	// console.log($("#addressDetial").val() == editAddressOld['address'],$(".name").val() == editAddressOld['name'], $(".phone").val() == editAddressOld['phone'])
	    	// console.log($("#addressDetial").val(),editAddressOld['address'])
	    	// console.log($(".name").val(),editAddressOld['name']) 
	    	// console.log($(".phone").val(),editAddressOld['phone'])

	    	if(!addressChange && $("#addressDetial").val()==editAddressOld['address'] && $(".name").val()==editAddressOld['name'] && $(".phone").val()==editAddressOld['phone'] && editAddressObj['status']==editAddressOld['status']){
				layHint('你什么都没修改！');
				//返回地址管理
				setTimeout(function(){
					location.href = getURL('Home', 'Address/index');
				},1000);
	    		return;
	    	}else{
	    		if(addressChange && $('#areaID').val().indexOf('请选择') > -1){
	    			layHint('请重新选择地址！');
	    			return false;
	    		}
		    	$.ajax({ 
		    		url: getURL('Home', 'Address/save_address'),
		    		type: 'post',
		    		data: editAddressObj,
		    		success: function(res){
		    			// console.log('成功！',res);
		    			if(res.code == 200){
		    				layHint('保存成功！');
		    				sessionStorage.setItem("editAddressObj", JSON.stringify(editAddressObj));
		    				// 如果存在订单号则跳回订单确认界面
		    				if(sessionStorage.getItem("order_id")){
		    					// 判断页面来源(滤芯和商城);
								var buyType = window.location.search;
								
			    				setTimeout(function(){
			    					// 滤芯
									if(buyType.substr(-1, 1) == '0') {
										history.replaceState({}, null, getURL('Home', 'Pay/lvxinPay'));
										location.href = getURL('Home', 'Pay/lvxinPay');
									// 商城
									}else if(buyType.substr(-1, 1) == '1') {
										history.replaceState({}, null, getURL('Home', 'Pay/payConfirm'));
										location.href = getURL('Home', 'Pay/payConfirm');
										
									}else {
										history.replaceState({}, null, getURL('Home', 'Address/index'));
										location.href = getURL('Home', 'VipCenter/index');
									}
			    				},500);
							}else{
								// 没有订单号则返回到‘我的’界面
								setTimeout(function(){
			    					history.replaceState({}, null, getURL('Home', 'VipCenter/index'));
			    					location.href = getURL('Home', 'VipCenter/index');
			    				},500);
							}
		    			}else{
		    				// console.log('失败！',res);
		    				layHint('保存失败！');
		    			}
		    		},
		    		error: function(res){
		    			console.log('失败！',res);
		    			layHint('发生未知问题，请稍后再试！');
		    		}
		    	})
	    	}
		}else if(!nameRes){
    		// 验证姓名
    		// console.log('请输入符合规范的姓名！');
    		layHint('请输入合法的姓名！')

    	}else if(!phoneRes){
    		// console.log('请输入正确的电话号码！');
    		layHint('请输入正确的电话号码！')

    	}else if(!areaIDRes){
    		// console.log('请选择地区！');
    		layHint('请选择地区请选择地区！')
    		
    	}else if(!addressDetialRes){
    		// console.log('请输入详细地址！');
    		layHint('请输入详细地址！')
    		
    	}

    	// console.log(editAddressObj,Object.getOwnPropertyNames(editAddressObj).length);
        // console.log(nameRes, phoneRes, addressDetialRes);
	}else{
		/*
			新建地址
		 */
    	// 存储新建地址的个人信息
    	var nameRes = nameReg.test($(".name").val());
    	var phoneRes = phoneReg.test($(".phone").val());
    	var areaI = $("#areaID").val();
    	var addressDetialRes = nameReg.test($("#addressDetial").val());

	    // console.log($("#normalAddrdess")[0].checked)
    	var newAddressObj = {};
    	if(!nameRes){
    		// 验证姓名
    		console.log('请输入符合规范的姓名！');
    		layHint('请输入合法的姓名！');
    		return

    	}else if(!phoneRes){
    		console.log('请输入正确的电话号码！');
    		layHint('请输入正确的电话号码！')
    		return

    	}else if(!area){
    		console.log('请选择地区！');
    		console.log('areaIDRes: ',areaIDRes, $("#areaID").val());
    		layHint('请选择地区！')
    		return
    		
    	}else if(!addressDetialRes){
    		console.log('请输入详细地址！');
    		layHint('请输入详细地址！')
    		return
    		
    	}

    	newAddressObj['name'] = $(".name").val();
    	newAddressObj['phone'] = $(".phone").val();
    	newAddressObj['province'] = province;
    	newAddressObj['city'] = city;
    	newAddressObj['area'] = area;
    	newAddressObj['province_id'] = window.province_id;
    	newAddressObj['city_id'] = window.city_id;
    	newAddressObj['area_id'] = window.area_id;
    	newAddressObj['addressDetial'] = $("#addressDetial").val();
    	if($("#normalAddrdess").attr("checked")){
    		//设为默认地址
    		newAddressObj['status'] = 0;
    	}else{
    		newAddressObj['status'] = 1;
    	}
    	console.log(newAddressObj);
    	// 全部判断通过则发送请求让后台添加到数据库
    	$.ajax({
    		url: getURL('Home', 'Address/newAddress'),
    		type: 'post',
    		data: newAddressObj,
    		success: function(res){
    			console.log('成功！ ', res);
    			layHint('添加成功！');
    			//后台返回信息添加成功后的操作
    			if(res.code == 200){
	    			// 如果有订单号,自动跳回去订单提交那里
	    			if(sessionStorage.getItem("order_id")){
	    				// console.log("当前页面路径", window.locatzion.search)
						
	    				// 如果有商品数据
	    				if(goods_data || lvxinData || shopCar_data){
	    					// 判断页面来源(滤芯和商城);
							var buyType = window.location.search;
							// 滤芯
							if(buyType.substr(-1, 1) == '0') {
								history.replaceState({}, null, getURL('Home', 'Pay/lvxinPay'));
								setTimeout(function() {
									location.href = getURL('Home', 'Pay/lvxinPay');
								}, 500);
								
							// 商城
							}else if(buyType.substr(-1, 1) == '1') {
								history.replaceState({}, null, getURL('Home', 'Pay/payConfirm'));
								setTimeout(function() {
									location.href = getURL('Home', 'Pay/payConfirm');
								}, 500);
								
							}else {
								// 从地址管理进来，回到‘我的’界面
								history.replaceState({}, null, getURL('Home', 'Address/index'));
								setTimeout(function() {
									location.href = getURL('Home', 'VipCenter/index');
								}, 500);
								
							}
	    				}
	    			}else if(sessionStorage.getItem('lvxinData')){
	    				// 无订单号， 回到‘我的’界面
    					history.replaceState({}, null, getURL('Home', 'VipCenter/index'));
						layHint('保存成功');
    					setTimeout(function(){
    						location.href = getURL('Home', 'Pay/lvxinPay');
    					},500)
	    			}else{
	    				// 无订单号， 回到‘我的’界面
    					history.replaceState({}, null, getURL('Home', 'VipCenter/index'));
						layHint('保存成功');
    					setTimeout(function(){
    						location.href = getURL('Home', 'VipCenter/index');
    					},500)
    				}
	    		}else if(res.code == 605){
					layHint('添加失败，请稍后再试！');
	    		}else{
	    			layHint('很抱歉，系统遇到未知问题！');
	    		}
    		},
    		error: function(res){
    			console.log('失败！ ', res);
    		}
    	})
   	}
})
//提示框函数
function layHint(text){
	layui.use('layer', function(){
        var layer = layui.layer;
        layer.msg(text);
    });
}


var province_id,city_id,area_id;
// 选择地区
$(".areabtn").on("click", function(){
	$("#areaChoose").css({display: "block"});
});

// 获取地址数据
function getAddress(parentid, callback){
	$.ajax({
		url: getURL('Home', 'Address/getNextArea'),
		type: 'post',
		data: {parentid: parentid},
		success: function(res){
			console.log('res: ',res);
			if(res.status == 200){
				callback(res.data);	
			}else{
				layHint(res.msg);
			}
		},
		error: function(err){
			console.log('err: ',err);
			layHint('系统遇到问题，请稍后再试');
		}
	})
}

// 获取省份数据
getAddress(0, function(res){
	if(res.length){
		var html = '';
		for(var i=0; i<res.length; i++){
			html += '<p aid='+ res[i].id +'>'+ res[i].areaname +'</p>';
		}
		$(".province").html(html);
	}
});

var province,city,area;
// 选择省份，城市，区县
$('.areaDiv').on('click', 'p', function(){
	var parent = $(this).parent()[0].className;
	var aid = $(this).attr('aid');	// 省份id
	var _this = $(this);
	console.log('parent: ',parent);

	// 根据省份id 获取城市数据
	getAddress(aid, function(res){
		if(parent == 'province'){
			province = _this.text();
			province_id = _this.attr('aid');
			console.log('cityres: ',res);
			// 点击省份
			console.log('city: ');
			var cityhtml = '';
			for(var i=0; i<res.length; i++){
				cityhtml += '<p aid='+ res[i].id +'>'+ res[i].areaname +'</p>';
			}
			$('.city').html(cityhtml);
			for(var j=0; j<$('.province>p').length; j++){
				$('.province>p').eq(j).removeClass('addrselect');
			}
			_this.attr('class','addrselect');

		}else if(parent == 'city'){
			city = _this.text();
			city_id = _this.attr('aid');
			// 点击城市
			console.log('areares: ',res);
			var areahtml = '';
			for(var i=0; i<res.length; i++){
				areahtml += '<p aid='+ res[i].id +'>'+ res[i].areaname +'</p>';
			}
			$('.area').html(areahtml);
			for(var j=0; j<$('.city>p').length; j++){
				$('.city>p').eq(j).removeClass('addrselect');
			}
			_this.attr('class','addrselect');

		}else if(parent == 'area'){
			area = _this.text();
			area_id = _this.attr('aid');
			// 点击区县
			console.log('area: ',_this);
			for(var j=0; j<$('.area>p').length; j++){
				$('.area>p').eq(j).removeClass('addrselect');
			}
			_this.attr('class','addrselect');
			areaover();
		}
		console.log('province,city,area: ',province,city,area);
		console.log('province_id,city_id,area_id: ',province_id,city_id,area_id);
	})
})

// 选择完区县后自动消失
function areaover(){
	if(!province){
		layuiHint('请选择省份');
		return
	}
	if(!city){
		layuiHint('请选择城市');
		return
	}
	if(!area){
		layuiHint('请选择区县');
		return
	}

	$('.areabtn').val(province +' '+ city +' '+ area);
	setTimeout(function(){
		$('#areaChoose').fadeOut('fast');
	},300);
}

// 关闭地址选择
$('.areaChoosebg').click(function(){
	$('#areaChoose').fadeOut('slow');
})