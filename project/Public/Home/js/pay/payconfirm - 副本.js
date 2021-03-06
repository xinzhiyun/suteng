$(function(){
	
	// 用户金币数量：gold_num; 银币数量：silver; 金币换算：gold_rate; 银币换算：silver_rate;
	var gold_num = parseInt(JSON.parse($("#jinyinbi").val()).gold_num);
	var silver = parseInt(JSON.parse($("#jinyinbi").val()).silver);
	var gold_rate = JSON.parse($("#rate").val()).gold_rate;
	var silver_rate = JSON.parse($("#rate").val()).silver_rate;

	console.log("金银币数量", gold_num, silver);

	
	// /******** 付款过程--结束--	**********/
	order_id = JSON.parse(sessionStorage.getItem("order_id"));
	
	// 快递数据
	var postage = JSON.parse($("#goodsCourier").val());
	// console.log(postage)
	var addressInfo = $("#addressInfo").val();//收货数据
	var selectPostage = "";//快递选择
	var posOption = "";//option添入内容
	var postageAll = []; //列入多个商品多个邮费
	// console.log("邮费",postage)

	// 循环快递对象
	if(postage) {
		for(var j in postage) {
			if(postage[j].length != 0) {
				for(var i=0; i < postage[j].length; i++) {
					// if(postageSelect[i] == postage[j][i].cid) {
					// 	// 从商品订单付款过来的默认邮费选择
					// 	posOption = posOption + "<option cid='"+postage[j][i].cid+"' index='"+postage[j][i].cprice+"' selected='selected'>"+postage[j][i].cname+"</option>";
					// }else {
						posOption = posOption + "<option cid='"+postage[j][i].cid+"' index='"+postage[j][i].cprice+"'>"+postage[j][i].cname+"</option>";
					}
				// }
				selectPostage = "<p class='postage'><span class='posway'>配送方式</span><select name='selectPostage' class='selectPostage'><option index='0' cid=''>选择快递</option>"+posOption+"</select><span class='youfei'></span><span class='iconfont icon-dayuhao-copy-copy poselct'></span></p>";

				postageAll[j]=selectPostage;
				posOption = "";//清空

			}
			
		}
	}
	// else {
	// 	// 若无快递数据时友情提示
	// 	selectPostage = "<p class='postage'><span class='posway'>暂无快递选择</span></p>";
	// }

	$("#hLeft").click(function(){
		location.href = getURL('Home', 'Address/index');
	})
	
	
	//获取从后台传来的订单号
	var order_id;
	if(sessionStorage.getItem('order_id')){
		order_id = sessionStorage.getItem('order_id');
		// console.log(order_id);
	}
	
	// 获取后台传来的默认地址
	var addressInfo;
	if(($("#addressInfo").val())!='null'){
		// alert(1)
		addressInfo = JSON.parse($("#addressInfo").val());
		console.log(addressInfo);
		if(addressInfo){
			var addresssHTML = '<p>'+
			'<span>收货人：'+ addressInfo.name +'</span>'+
			'<span>'+ addressInfo.phone +'</span>'+
			'</p>'+
			'<p>'+
			'<span>收货地址：'+ addressInfo.address +'</span>'+
			'</p>';
					// 将用户收货信息添加到页面上
					$("#hLeft").html(addresssHTML);
		}
	} else{
		$("#hLeft").html('<p style="text-align:center;">请选择地址或添加新地址</p>');
	}
	$("#main").append('<h3 class="dingdanID">订单编号：'+order_id+'</h3>')
	/*
	订单遍历
	*/
	// 从本地sessionStorage中获取订单数据
	var lvxinData, goods_data, shopCar_data, goods_dataArr;
	if(sessionStorage.getItem("lvxinData")){
		// 从滤芯购买传来
		lvxinData = JSON.parse(sessionStorage.getItem("lvxinData"));
	}
	if(sessionStorage.getItem("goods_data")){
		// 从商品详情传来
		goods_data = JSON.parse(sessionStorage.getItem("goods_data"));
	}
	var postageSelect = [];
	if(sessionStorage.getItem("shopCar_data")){
		// 购物车传来
		shopCar_data = JSON.parse(sessionStorage.getItem("shopCar_data"));
		// 商品订单付款的邮费选择
		for(var i = 0; i < shopCar_data.length; i++) {
			postageSelect[i] = shopCar_data[i].cid
		}
		console.log("postageSelect", postageSelect);
	}
	console.log('lvxinData： ',lvxinData);
	console.log('goods_data： ',goods_data);
	console.log('shopCar_data： ',shopCar_data);

	var goodsHTML = '';	//存放需要支付的商品信息
	var numAll = 0;		//存放订单的总数量
	var priceAll = 0;	//存放订单的总价格
	var datanum;
	var totalPub;		//订单总价格
	// 如果有购买滤芯，从’滤芯购买‘过来
	if(lvxinData){
		
		// 遍历需要支付的滤芯商品信息
		for(var i=0; i<lvxinData.length;i++){
			// console.dir(lvxinData[i])
			lvxinData[i].desc = lvxinData[i].desc ? lvxinData[i].desc : '暂无描述';
			goodsHTML += '<div class="goods" v-data="'+lvxinData[i].gid+'">'+
			'<div class="infos">'+
			'<div class="img"><img src="'+ lvxinData[i].picpath +'" alt="正在加载..."></div>'+
			'<div class="dec">'+
			'<h5>'+ lvxinData[i].filtername +'</h5>'+
			'<p>'+ lvxinData[i].desc +'</p>'+
			'<div>'+
			'<span class="price">'+ lvxinData[i].price +'</span>'+
			'<span class="num">X&nbsp;<b>'+ lvxinData[i].num +'</b></span>'+
			'</div>'+
			'</div>'+
			'</div>'+ postageAll[73]+
			'</div>';
						// 统计数量和价格
						numAll += parseInt(lvxinData[i].num);
						priceAll += parseInt(lvxinData[i].priceAll);
					}
					priceAll += Number(postageMoney * numAll);	//加上快递费价格
					
					$("#main").append(goodsHTML);	//滤芯订单的信息
					$(".numAll").text(numAll);		//滤芯订单的总数量
					$(".priceAll").text('￥'+priceAll);	//滤芯订单的总价格
					$("#footerContain>div>span").text('￥'+priceAll);	//滤芯订单的总价格
					$(".paytopRight>p").text('$'+ parseInt(priceAll));	//滤芯订单的总价格

	}else if(goods_data){	// 如果有商品购买，从商品详情’立即购买‘过来
		// 获取商品id
		var goid = goods_data.goodsDetail.gid;
		// 遍历需要支付的商品信息
		goods_data.goodsDetail.desc = goods_data.goodsDetail.desc
		? goods_data.goodsDetail.desc
		: '暂无描述'; 
		goodsHTML += '<div class="goods" v-data="'+goods_data.goodsDetail.gid+'">'+
		'<div class="infos">'+ 
		'<div class="img"><img src="/Uploads/'+ goods_data.goodsDetail.path +'" alt="正在加载..."></div>'+
		'<div class="dec">'+
		'<h5>'+ goods_data.goodsDetail.name +'</h5>'+
		'<p>'+ goods_data.goodsDetail.desc +'</p>'+
		'<div>'+
		'<span class="price">'+ goods_data.goodsDetail.price +'</span>'+
		'<span class="num">X&nbsp;<b>'+ goods_data.num +'</b></span>'+
		'</div>'+
		'</div>' 
		+'</div>'+
		selectPostage+
		'</div>';
		// 统计数量和价格 单个
		numAll = parseInt(goods_data.num);
		priceAll = parseInt(goods_data.goodsDetail.price * goods_data.num);
		//快递费
		$("#main").append(goodsHTML);	//订单的信息
		$(".numAll").text(numAll);		//订单的总数量
		$(".priceAll").text('￥'+ (parseFloat(priceAll)).toFixed(2));	//订单的总价格
		$("#footerContain>div>span").text('￥'+(parseFloat(priceAll)).toFixed(2));	//订单的总价格
		$(".paytopRight>p").text('$'+ parseInt(priceAll));	//订单的总价格

	}else if(shopCar_data){	// 如果有商品购买，从购物车’结算‘过来
		// console.log(shopCar_data.length)
		// 商品id
		var goid = [];
		for(var i=0; i<shopCar_data.length; i++){
			goid.push(shopCar_data[i].gid);
			if(shopCar_data[i]){
				shopCar_data[i].desc = shopCar_data[i].desc
				? shopCar_data[i].desc
				: '暂无描述';

				// 无快递数据时友情提示
				// postageAll[shopCar_data[i].gid] = postageAll[shopCar_data[i].gid] ? postageAll[shopCar_data[i].gid] : "<p class='postage'><span class='posway'>暂无快递选择</span></p>";

				// console.log("商品id",shopCar_data[i].gid)
				// 遍历需要支付的商品信息
				goodsHTML += '<div class="goods" v-data=" '+ shopCar_data[i].gid +' ">'+
				'<div class="infos">'+
				'<div class="img"><img src="'+ shopCar_data[i].path +'" alt="正在加载..."></div>'+
				'<div class="dec">'+
				'<h5>'+ shopCar_data[i].name +'</h5>'+
				'<p>'+ shopCar_data[i].desc +'</p>'+
				'<div>'+
				'<span class="price">'+ shopCar_data[i].price +'</span>'+
				'<span class="num">X&nbsp;<b>'+ shopCar_data[i].num +'</b></span>'+
				'</div>'+
				'</div>'+
				'</div>'+ postageAll[shopCar_data[i].gid]+
				'</div>';
			}
			// 统计数量和价格 多个
			numAll += parseInt(shopCar_data[i].num);
			priceAll += parseInt((parseInt(shopCar_data[i].num) * parseInt(shopCar_data[i].price)));//总价，但不考虑邮费 选择邮费再加上

        	// console.log(priceAll,shopCar_data[i].num, shopCar_data[i].price)
		}
		$("#main").append(goodsHTML);	//订单的信息
		$(".numAll").text(numAll);		//订单的总数量
		$(".priceAll").text('￥'+ priceAll);	//订单的总价格
		$("#footerContain>div>span").text('￥'+priceAll);	//订单的总价格
		$(".paytopRight>p").text('$'+ parseInt(priceAll));	//订单的总价格
	}
	// 邮费放置
	var postageArr = [];
	for(var i = 0; i < $(".goods").length; i++) {
		postageArr[i] = 0;
		$(".goods").eq(i).attr("data", i);
		// 设置商品订单里已有的邮费选择
		// var postageCid = $(".selectPostage").eq(i).children();//获取所有的邮费对象
		// for(var j = 0; j < postageCid.length; j++) {
		// 	if(postageSelect[i] == $(postageCid[j]).attr("cid")) {
		// 		// console.log("选中的邮费cid",$(postageCid[j]).attr("cid"), "传过来邮费cid",postageSelect[i], i, $(".selectPostage").eq(i).children(), j);
		// 		// var selectedObj = $(".selectPostage").eq(i).children()[cid=$(postageCid[j]).attr("cid")];
		// 		var selectedObj = $(".goods").eq(i).children(".postage").children(".selectPostage").children()[cid=$(postageCid[j]).attr("cid")];
		// 		var cids = postageSelect[i];
		// 		var index = $(postageCid[j])[0].index;
		// 		$(".selectPostage").eq(i).find("option").eq(index).attr("selected", "selected");
		// 		console.log("123",$(postageCid[j]), index, $(".selectPostage").eq(i))
		// 		// console.log("duixiang ", $(selectedObj).parent().parent(), selectedObj);
		// 		// (function(selectedObj) {
		// 		// 	console.log("youfei", selectedObj)
		// 		// 	setTimeout(function() {
		// 		// 		$(selectedObj).parent().children(selectedObj).attr("selected", "selected");
		// 		// 	}, 0);
					
		// 		// })(selectedObj)
				
		// 	}
			
		// }
		
	}
	
	
	sessionStorage.setItem("origin",$(".postage").html());
	// 存储快递信息
	var storageAll = [];
	for(var i = 0;i<sessionStorage.length;i++){
		// 库中所有的键
        var storage_value_all = sessionStorage.key(i);
        var number_all = storage_value_all.split("_")[0];
        var attrData = storage_value_all.split("_")[1];
        if(number_all == "data"){
        	var info = sessionStorage.getItem(storage_value_all);
        	$(".goods").eq(attrData).html(info);
        	storageAll.push(storage_value_all);
        }
	}
	// console.log("storageAll"+storageAll);
	// var dingdan = $(".dingdanID").html().substr(5,);
	var dingdan = $(".dingdanID").html().split("：")[1];
	console.log(dingdan);
	if(sessionStorage.getItem("code") != dingdan){
		console.log(storageAll);
		for(var i = 0;i<storageAll.length;i++){
			sessionStorage.removeItem(storageAll[i]);
		}
		for(var i = 0;i<$(".postage").length;i++){
			$(".postage").eq(i).html(sessionStorage.getItem("origin"));
		}
		sessionStorage.removeItem("totalPub");
		console.log("换订单号删除信息")
	}else{
		console.log("同一个订单号")
	}
	var postageMoney = sessionStorage.getItem("postageMoney");
	// 点击选择邮费
	var postageName, postageNum, postageMoney, cid, num, self;
	$(".selectPostage").on("change", function() {
		var dingdan = $(".dingdanID").html().split("：")[1];
		sessionStorage.setItem("code",dingdan);
		// 清除存储子啊sessionStorage快递信息
		datanum = $(this).parents(".goods").attr("data");
		sessionStorage.removeItem("data_"+datanum);
		// 获取当前点击到的goods块的data值
		var posNum = $(this).parent().parent(".goods").attr("data");
		// 邮费种类
		postageName = $(this).val();
		// 邮费价格
		postagePrice = $(this).find("option:selected").attr("index");
		// 邮费id
		cid = $(this).find("option:selected").attr("cid");
		// 邮费对象
		postageData = {cname: postageName, cprice: postagePrice, cid: cid};
		console.log(postageName, postagePrice, cid);
		// 选中邮费所在块的商品的数量
		num = parseInt($(this).parent().siblings(".infos").children(".dec").children("div").children(".num").children("b").text());
		// 邮费价格乘以数量
		postageArr[posNum] = postagePrice * num;
		console.log(33)
		// 获取点击到的商品的id
		console.log("商品id", $(this).parent().parent(".goods").attr("v-data"));
		var gid = $(this).parent().parent(".goods").attr("v-data");
		function sum(arr) {
			var s=0;
			if(arr.length==0){
				return 0;
			}else if(arr.length==1){
				return arr[0];
			}else{
				for(var i=0;i<arr.length;i++){
					s+=parseInt(arr[i]);
				}
				return s;
			}
		}
		postageMoney = sum(postageArr);//总邮费
		totalPub = (parseFloat(priceAll) + parseFloat(postageMoney)).toFixed(2);
		sessionStorage.setItem("totalPub",totalPub);
		$(".priceAll").text('￥'+ (parseFloat(priceAll) + parseFloat(postageMoney)).toFixed(2));	//订单的总价格
		$(".paytopRight>p").text('$'+(parseInt(priceAll) + parseInt(postageMoney)));	//订单的总价格
		$("#footerContain>div>span").text('￥'+(parseFloat(priceAll) + parseFloat(postageMoney)).toFixed(2));	//订单的总价格
		$(this).siblings(".youfei").text(postagePrice);
		//获取商品价格
		sessionStorage.setItem("goodsPrice", parseInt(priceAll) + parseInt(postageMoney));//支付面板的价格存储
		$('.paymidd>ul>li>span').removeClass('iconfont icon-xuanze').addClass('iconfont icon-kuang1');
		$('.paymidd>ul>li>span').eq(0).removeClass('iconfont icon-kuang1').addClass('iconfont icon-xuanze');//切换会第一个
		if($(this).find("option:selected").val() != "选择快递" && $(this).prev().html() == "配送方式") {
			// 快递选择向右图标隐藏
			$(this).siblings(".poselct").css("display", "none");
			$(this).siblings(".posway").css("display", "none");
		}else {
			// 快递选择向右图标显示
			$(this).siblings(".poselct").css("display", "block");
			$(this).siblings(".posway").css("display", "block");
			$(this).siblings(".youfei").text("");
		}
		var opDom = $("option:selected",this);
		opDom.attr("selected","selected").siblings().attr("selected",false);
		sessionStorage.setItem("data_"+datanum,$(this).parents(".goods").html());
		
		// 提交后台
		$.ajax({
			url: getURL('Home', 'Pay/updateOrder'),
			data: {orderId: order_id, postageData: postageData, good_id: gid},//将订单id和邮费id传递
			type: "post",
			success: function(res) {
				console.log("邮费成功", res);
			},
			error: function(res) {
				console.log("邮费失败", res);
			}
		})
	})
	// 订单总价钱
	totalPub = sessionStorage.getItem("totalPub");
	if(totalPub){
		$(".priceAll").text('￥'+totalPub);
		$(".paytopRight>p").text('$'+ parseInt(totalPub));
		$("#footerContain>div>span").text('￥'+totalPub);
	}
	
	// 点击发票
	$(".kaifapiao").on("click", function() {
		for(var i = 0; i < $(".selectPostage").length; i++) {
			if($(".selectPostage").eq(i).find("option:selected").val() == "选择快递") {
				parent.layer.msg('请选择快递方式');
				return false;
			}
		}
	});
	/*
	发票信息
	*/
	var voiceArr;
	// 如果有session存在发票信息， 则更新发票信息， 没有默认为不开发票
	if(sessionStorage.getItem("voiceArr")){
		voiceArr = JSON.parse(sessionStorage.getItem("voiceArr"));
		if(voiceArr.type){
			$(".ynticket>a>span").text(voiceArr.type)
		}
	}else {
		voiceArr = {
			'type': "不开发票",
			'info': "",
			'voicehead': "",
			'phone': ""
		}
	}
	/******** 订单遍历--结束--	**********/
	/*
	付款过程
	*/
	// 点击付款界面左上角" X "
	$("#closePay").on("click",function(){
		$("#header").css({filter: 'brightness(100%)'});
		$("#main").css({filter: 'brightness(100%)'});
		$("#footer").css({filter: 'brightness(100%)'});
		
		$("#payWay").animate({bottom: '-100vh'}).css({display: 'none'});
	})
	// 点击付款按钮
	//支付方式
	$('#footerContain').click(function(){
		// 点击支付之前要求选择快递方式
		// if() {}
		if($("#hLeft>p").text() == "请选择地址或添加新地址"){
			parent.layer.msg('请添加一个地址！');
			setTimeout(function(){
				location.href = getURL('Home', 'Address/newAddress');
			},500);
			return false;
		}
		for(var i = 0; i < $(".selectPostage").length; i++) {
			if($(".selectPostage").eq(i).find("option:selected").val() == "选择快递") {
				parent.layer.msg('请选择快递方式');
				return false;
			}
		}

		$('.paystyle').slideDown("slow");
		
	});
	// 付款方式的单选框
	$('.paymidd>ul>li>span').click(function(){
		goodsPrice = sessionStorage.getItem("goodsPrice");//获取支付面板的总价格
		$('.paymidd>ul>li>span').removeClass('iconfont icon-xuanze').addClass('iconfont icon-kuang1');
		$(this).removeClass('iconfont icon-kuang1').addClass('iconfont icon-xuanze');
		var fuhao = $(this).parent().text();//获取购买方式
		// 判断选择方式是否为：金币或者银币
		if(fuhao == "金币") {
			// 金额兑换成金币
			var goldPrice = (goodsPrice / gold_rate);
			// 将￥替换成$符号
			$(".paytopRight>p").text("$"+goldPrice);
		}else if(fuhao == "银币") {
			// 金额兑换成银币
			var silverPrice = (goodsPrice / silver_rate);
			// 将￥替换成$符号
			$(".paytopRight>p").text("$"+silverPrice);
		}
		else {
			// 将$替换成￥符号
			$(".paytopRight>p").text("￥"+ goodsPrice + ".00");
		}
		console.log("选中的支付方式",$(this).parent().text());
	})
	// 付款方式页面隐藏
	$('.paytop>.paytopLeft>i').click(function(){
		$('.paystyle').slideUp("slow");
	})
	// 模态框的js
	
	/*
	点击立即支付
	*/
	$(".payBott").click(function(){
		goodsPrice = sessionStorage.getItem("goodsPrice");//获取支付面板的总价格
		// 判断选择的是什么支付方式
		var index = $(".icon-xuanze").parents("li").attr("index");
		console.log("价格",$(".paytopRight>p").text().substr(1));
		switch(index){
			case '1':
			console.log('金币');
			if((gold_num * gold_rate) < goodsPrice) {
				parent.layer.msg('金币不足!');
				setTimeout(function(){
					$(".paystyle").slideUp("slow");
				}, 1000);
				return false;
			}
			//如果有默认地址
			if($("#addressInfo").val()){
				//请求微信支付方法
				$.ajax({
					url: getURL('Home', 'PaymentSystem/orderPayByGold'),
					type: 'post',
					data: {order: order_id, voiceArr: voiceArr},
					success: function(res){
						console.log("金币支付",res);
						if(res.code == 200) {
							parent.layer.msg('支付成功');
							setTimeout(function(){
								history.replaceState({}, null, getURL('Home', 'Shop/index'));//改变历史记录
								sessionStorage.setItem("goodsPrice", "");//清空总价格
								location.href = getURL('Home', 'PaymentSystem/paytosuccess');
							},500);
						}else if(res.code == -1) {
							parent.layer.msg('已经支付过啦!');
							setTimeout(function(){
								location.href = getURL('Home', 'Shop/index');
							},500);
						}else if(res.code == 1001) {
							parent.layer.msg('金币不足');
							setTimeout(function(){
								$(".paystyle").slideUp("slow");
							}, 1000);
						}else {
							parent.layer.msg('支付失败，请重试!');
							setTimeout(function(){
								$(".paystyle").slideUp("slow");
							}, 1000);
						}
					},
					error: function(res){
						console.log('失败！ ',res);
						parent.layer.msg('系统出了一点小问题，请稍后再试！');
					}
				});
			}else{
				parent.layer.msg('请添加一个地址');
				setTimeout(function(){
					location.href = getURL('Home', 'Address/newAddress');
				},500);
			}
			break;
			case '2':
				console.log('银币');
				if((silver * silver_rate) < goodsPrice) {
					parent.layer.msg('银币不足!');
					setTimeout(function(){
						$(".paystyle").slideUp("slow");
					}, 1000);
					return false
				}
				//如果有默认地址
				if($("#addressInfo").val()){ 
					//请求微信支付方法
					$.ajax({
						url: getURL('Home', 'PaymentSystem/orderPayBySilver'),
						type: 'post',
						data: {order: order_id, voiceArr: voiceArr},
						success: function(res){
							console.log("银币支付",res);
							if(res.code == 200) {
								parent.layer.msg('支付成功');
								setTimeout(function(){
									history.replaceState({}, null, getURL('Home', 'Shop/index'));//改变历史记录
									sessionStorage.setItem("goodsPrice", "");//清空总价格
									location.href = getURL('Home', 'PaymentSystem/paytosuccess');
								},500);
							}else if(res.code == -1) {
								parent.layer.msg('已经支付过啦!');
								setTimeout(function(){
									location.href = getURL('Home', 'Shop/index');
								},500);
							}else if(res.code == 1001) {
								parent.layer.msg('银币不足');
								setTimeout(function(){
									$(".paystyle").slideUp("slow");
								}, 1000);
							}else {
								parent.layer.msg('支付失败，请重试!');
								setTimeout(function(){
									$(".paystyle").slideUp("slow");
								}, 1000);
							}
						},
						error: function(res){
							console.log('失败！ ',res);
							parent.layer.msg('系统出了一点小问题，请稍后再试！');
						}
					});
				}else{
					parent.layer.msg('请添加一个地址');
					setTimeout(function(){
						location.href = getURL('Home', 'Address/newAddress');
					},500);
				}
			break;
			case '3':
			console.log('微信支付');
			//如果有默认地址
			if($("#addressInfo").val()){
				//请求微信支付方法
				$.ajax({
					url: getURL('Home', 'PaymentSystem/orderPay'),
					type: 'post',
					data: {order: order_id, voiceArr: voiceArr},
					success: function(res){
						console.log(res);
						if(res == -1){
							console.log('支付错误！');
							parent.layer.msg('支付错误！');
						}else{
							if($("#addressInfo").val()){
								// 调用微信支付方法
								weixinPay(res);
							}else{
								//无地址
								parent.layer.msg('请添加一个地址');
								setTimeout(function(){
									location.href = getURL('Home', 'Address/newAddress');
								},500);
							}
						}
					},
					error: function(res){
						console.log('失败！ ',res);
						parent.layer.msg('系统出了一点小问题，请稍后再试！');
					}
				});
			}else{
				parent.layer.msg('请添加一个地址');
				setTimeout(function(){
					location.href = getURL('Home', 'Address/newAddress');
				},500);
			}
			break;
			case '4':
			console.log('支付宝支付');
			break;
			case '5':
			console.log('银联支付');
			break;
			
			default:
			console.log('无');
			break;
		}
	})
	/*
	微信支付方法
	*/
	function weixinPay(res){
		WeixinJSBridge.invoke(
			'getBrandWCPayRequest',
			JSON.parse(res),
			function(res){
				if(res.err_msg.substr(-2) == 'ok'){

					parent.layer.msg('付款成功！');
					//付款成功后的操作
					setTimeout(function(){
						
						location.href = getURL('Home', 'PaymentSystem/paytosuccess');
					},500);
				
			}else if(res.err_msg.substr(-6) == 'cancel'){
				//取消订单操作
			}else{
				parent.layer.msg('付款失败！');
				//付款失败操作
				setTimeout(function(){
					location.href = getURL('Home', 'PaymentSystem/paytofailed');
				},500);
				
			}
		}
		)
	}
	
})