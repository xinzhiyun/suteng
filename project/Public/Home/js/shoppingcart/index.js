$(function(){
	var allNum = []; //购物车商品的库存量
	/*
		请求购物车列表数据
	 */ 
	var itemCarHTML = '';
	$.ajax({
		url: getURL('Home', 'ShoppingCart/cart'),
		type: 'post',
		success: function(res){
			console.log("成功！", res);
			if(res){
				for(var i=0; i<res.length; i++){
					res[i].desc = !res[i].desc ? '暂无描述' : res[i].desc;
					itemCarHTML += '<div class="itemCar clearfix" gid="'+ res[i].gid +'" index_id="'+ res[i].id +'">'+
						'<!-- 选中及图片 -->'+
						'<div class="itemcarLeft clearfix">'+
							'<div class="xuanzhong "><i class="iconfont icon-kuang1"></i></div>'+
							'<div class="carpic" gid="'+ res[i].gid +'"><img src="/Uploads/'+ res[i].path +'" alt=""></div>'+
						'</div>'+
						'<!-- 文字说明及价格 -->'+
						'<div class="itemcarRight">'+
							'<h3>'+ res[i].name +'</h3>'+
							'<p class="roTxt">'+ res[i].desc +'</p>'+
							'<p class="roPrice clearfix">'+
								'<b class="price">￥<span>'+ res[i].price +'</span></b>'+
								'<span class="num">'+
									'<input type="button" value="-" class="minus">'+
									'<input class="number" type="number" value="'+ res[i].num +'" min="1" max="19">'+
									'<input type="button" value="+" class="plus">'+
								'</span>'+
							'</p>'+
							'<p class="'+res[i].gid+' stock">暂无库存</p>'+
						'</div>'+
					'</div>';
					allNum[res[i].gid] = res[i].allnum;
				}
				// 添加到购物车页面
				$(".shpingcarAll").html(itemCarHTML);
			}else{
				// 购物车没有商品
				$(".noItem").css({display: 'flex'})
			}
		},
		error: function(res){
			console.log("失败！", res)
		}
	})
	/*
		结算
	 */
	$("#toPay").click(function(){
		var shopCar_data = [];
		var chooseGoods = $(".itemCar").find("i[class='iconfont icon-xuanze']");
		priceAll = 0; //清空价格
		
		if($(".priceAll>span").text() != 0){
			for(var i=0; i<chooseGoods.length; i++){
				// 将选中商品的详细信息传到后台
				shopCar_data.push({
					'path': chooseGoods.eq(i).parents(".itemCar").find(".carpic>img").attr("src"),
					'desc': chooseGoods.eq(i).parents(".itemCar").find(".roTxt").text(),
					'name': chooseGoods.eq(i).parents(".itemCar").find(".itemcarRight>h3").text(),
					'gid': chooseGoods.eq(i).parents(".itemCar").attr("gid"),
					'num': chooseGoods.eq(i).parents(".itemCar").find(".number").val(),
					'price': chooseGoods.eq(i).parents(".itemCar").find(".price>span").text(),
					'money': parseFloat(chooseGoods.eq(i).parents(".itemCar").find(".price>span").text())*parseFloat(chooseGoods.eq(i).parents(".itemCar").find(".number").val())
				});
				
			}

			//清除其他页面的sessionStorage
			sessionStorage.setItem("lvxinData", '');
			sessionStorage.setItem("goods_data", '');
			
			// 将购物车选中的商品存到本地sessionStorage, 给订单确认页面使用
			sessionStorage.setItem("shopCar_data",JSON.stringify(shopCar_data));
			console.log(shopCar_data)
			// 发送ajax请求让后台生成订单号
			$.ajax({
				url: getURL('Home','PaymentSystem/information'),
				type: 'post',
				data: {'data':JSON.stringify(shopCar_data)},
				success: function(res){
					console.log('库存查询成功！', res);
					var resType = typeof res;
					// typeof JSON.parse(res): number--返回订单号  object--查询到某商品无库存
					if(resType == 'string'){
						sessionStorage.setItem('order_id',Number(res));
						// 跳转到订单确认页面
						location.href = getURL('Home', 'Pay/payConfirm')+'?order_id=' + res;
					} else if(resType == 'object') {
						if(res.code == "604") {
							console.log(typeof res.data)
							for(var i = 0; i < res.data.length; i++) {
								// 库存余额剩余为零
								if(res.data[i].status == "fail") {
									// 显示无库存提示
									$("."+res.data[i].gid).text("剩余库存" + allNum[res.data[i].gid] + "件").css("display", "block");
									// 让无库存的商品去掉勾勾
									$("."+res.data[i].gid).parent(".itemcarRight").siblings(".itemcarLeft").children(".xuanzhong").children("i").removeClass('iconfont icon-xuanze').addClass('iconfont icon-kuang1');
								}else {
									// 有库存但是库存小于用户选中的数量
									var userNum = $("." + res.data[i].gid).siblings(".roPrice").children(".num").children(".number").val(); //用户选择的数量
									if(userNum > allNum[res.data[i].gid]) {
										$("."+res.data[i].gid).text("剩余库存" + allNum[res.data[i].gid] + "件").css("display", "block");
										// 让无库存的商品去掉勾勾
										$("."+res.data[i].gid).parent(".itemcarRight").siblings(".itemcarLeft").children(".xuanzhong").children("i").removeClass('iconfont icon-xuanze').addClass('iconfont icon-kuang1');
									}

								}
							}
							// 重新计算商品的价格
							var noStock = $(".itemCar").find("i[class='iconfont icon-xuanze']");
							console.log(noStock)
							for(var i = 0; i < noStock.length; i++) {
								priceAll += parseFloat($(noStock).eq(i).parent().parent().siblings(".itemcarRight").children(".roPrice").children(".price").children("span").text()) * parseFloat($(noStock).eq(i).parent().parent().siblings(".itemcarRight").children(".roPrice").children(".num").children(".number").val());
								console.log(priceAll, $(noStock).eq(i).parent().parent().siblings(".itemcarRight").children(".roPrice").children(".num").children(".number").val())
							}
							$(".priceAll>span").text(priceAll);
							console.log(priceAll);
						}
					}else {
						parent.layer.msg('系统异常');
					}
				},
				error: function(res){
					console.log('失败！', res);
					parent.layer.msg('系统异常');
				}
			})
		}else{
       		parent.layer.msg('你还没选择购买的商品！');
		}
	})

	var priceAll = 0;
	
	/*
		 按加'+'号
	 */
	$(".shpingcarAll").on("touchend",'.plus',function(){
		// 获取数量
		var value = $(".number").val();
		$(this).siblings(".number")[0].value++;
		// 增加数量不能超过库存的数量
		var plusParent = $(this).parent(".num").parent(".roPrice").parent(".itemcarRight").parent();//当前加号所在的块级
		var kucun = parseInt(allNum[$(plusParent).attr("gid")]);//库存
		var buyNum = parseInt($(this).siblings(".number").val());//购买的数量
		if(kucun) {
			if(buyNum > kucun) {
				parent.layer.msg('限购' + kucun + "件!");
				$(this).siblings(".number").val(kucun);
			}
		}else {
			var selectK = $(this).parent(".num").parent(".roPrice").parent(".itemcarRight").siblings(".itemcarLeft").children(".xuanzhong").children("i");//选中框
			$(selectK).removeClass("iconfont icon-xuanze").addClass("iconfont icon-kuang1");
			$(this).parent().parent().siblings(".stock").css("display", "block");
			// $(this).siblings(".number").val(1);
		}
		// console.log("库存",kucun , "购买数量", buyNum);
		
		var chooseGoods = $(".itemCar").find("i[class='iconfont icon-xuanze']");
		priceAll = 0;	//合计归零，后面重新计算
		if(chooseGoods) {
			// 将金额实时更新到合计
			for(var i=0; i<chooseGoods.length; i++){
				// 选中商品的价格 goodP  选中商品的数量 goodN
				var goodP = parseInt($(chooseGoods[i]).parent().parent(".itemcarLeft").siblings(".itemcarRight").children(".roPrice").children(".price").children().text());
				var goodN = parseInt($(chooseGoods[i]).parent().parent(".itemcarLeft").siblings(".itemcarRight").children(".roPrice").children(".num").children(".number").val());
				priceAll += goodP * goodN;
			}
		}
		$(".priceAll>span").text(priceAll);
	});
	/*
		按减'-'号
	 */
	$(".shpingcarAll").on("touchend",".minus",function(){
		// 获取数量
		var value = $(".number").val();
		for(var i=0; i<$(this).siblings(".number").length; i++){
			$(this).siblings(".number").val() <= 1
			? 1
			: $(this).siblings(".number")[i].value--
		}
		// 判断数量不能超过库存的数量
		var plusParent = $(this).parent(".num").parent(".roPrice").parent(".itemcarRight").parent();//当前减号所在的块级
		var kucun = parseInt(allNum[$(plusParent).attr("gid")]);//库存
		// var buyNum = parseInt($(this).siblings(".number").val());//购买的数量
		if(!kucun) {
			var selectK = $(this).parent(".num").parent(".roPrice").parent(".itemcarRight").siblings(".itemcarLeft").children(".xuanzhong").children("i");//选中框
			$(selectK).removeClass("iconfont icon-xuanze").addClass("iconfont icon-kuang1");
			$(this).parent().parent().siblings(".stock").css("display", "block");
			// $(this).siblings(".number").val(1);
		}
		// console.log($(this).siblings(".number")[0].value);
		var chooseGoods = $(".itemCar").find("i[class='iconfont icon-xuanze']");
		console.log("chooseGoods", chooseGoods);
		priceAll = 0;	//合计归零，后面重新计算
		// 将金额实时更新到合计
		// for(var i=0; i<chooseGoods.length; i++){
		// 	// 判断是否选中框框
		// 	if($(".itemCar").find("i[class='iconfont icon-xuanze']")){
		// 		var itemCar = $(".shpingcarAll").find(".itemCar").eq(i);
		// 		priceAll += parseFloat(itemCar.find(".price>span").text())*parseFloat(itemCar.find(".number").val());
		// 		console.log('price: ',itemCar.find(".price>span").text())
		// 		console.log('num: ',itemCar.find(".number").val())
		// 	}
			
		// }
		if(chooseGoods) {
			// 将金额实时更新到合计
			for(var i=0; i<chooseGoods.length; i++){
				// 选中商品的价格 goodP  选中商品的数量 goodN
				var goodP = parseInt($(chooseGoods[i]).parent().parent(".itemcarLeft").siblings(".itemcarRight").children(".roPrice").children(".price").children().text());
				var goodN = parseInt($(chooseGoods[i]).parent().parent(".itemcarLeft").siblings(".itemcarRight").children(".roPrice").children(".num").children(".number").val());
				priceAll += goodP * goodN;
			}
		}
		$(".priceAll>span").text(priceAll);

	});
	// 数量输入
	$('.shpingcarAll').on('keyup', '.number', function(e){
		var val = $(this).val();
		if(val.length == 1){
			$(this).val(val.replace(/^[0-9]/g,''));
		}else{
			$(this).val(val.replace(/\D/g, ''));
		}
		if(val.length >= 4){
			$(this).css({width: val.length-0+25 + 'vw'});
		}else{
			$(this).css({width: 'auto'});
		}
	})

	// 点击图片跳到商品详情
	$('.shpingcarAll').on('click', '.carpic', function(){
		var gid = $(this).attr('gid');
		console.log('gid: ',gid);
		location.href = getURL('Home', 'Shop/shoppingdetail') + '?gid=' + gid;
	})
	// 商品左滑，出现删除按钮
   	slipDel('.shpingcarAll','.itemCar','goodsnumbe');

   	// 购物车删除
   	$(".shpingcarAll").on("click",'.del', function(e){
   		var _this = $(this);
       	// console.log(e);
       	var index_id = $(this).parents(".itemCar").attr("index_id");
       	// console.log(index_id)
       	layer.confirm('确认删除？删除后无法恢复！', 
            {
              btn: ['确定','取消'] //按钮
            }, 
            function(){
		       	// 发送请求删除商品在数据库的数据
		       	$.ajax({
					url: getURL('Home', 'ShoppingCart/cartDel'),
					type: 'post',
					data: {'id': index_id},
					success: function(res){
						console.log('成功！ ',res);
						// 移除当前删除商品的元素
						_this.parents('.itemCar').remove();
						// 删除后计算总价格
						// removePrice = _this.siblings('.itemcarRight').children(".roPrice").children(".price").children("span").text() * _this.children(".roPrice").children(".num").children(".number").val();
						// priceAll -= removePrice;


						var chooseGoods = $(".itemCar").find("i[class='iconfont icon-xuanze']");
						priceAll = 0;	//合计归零，后面重新计算

						// 将金额实时更新到合计
						for(var i=0; i<chooseGoods.length; i++){
							if($(".itemCar").find("i[class='iconfont icon-xuanze']")){
								var itemCar = $(".shpingcarAll").find(".itemCar").eq(i);
								priceAll += parseFloat(itemCar.find(".price>span").text())*parseFloat(itemCar.find(".number").val())
							}
						}
						$(".priceAll>span").text(priceAll);


						// 弹出删除成功信息
		       			parent.layer.msg('删除成功！');
					},
					error: function(res){
						console.log('失败！ ',res);
		       			parent.layer.msg('删除失败！');
					}
				})
	            return true;
            },
            function(){
                layer.msg('已取消！', {icon: 1});
                return false;
            }
        );
   	})

	// 选中框
	$('.shpingcarAll').on('click','.xuanzhong',function(){
		var chooseGoods = $(".itemCar").find("i[class='iconfont icon-xuanze']");
		
		var _class=$(this).children().attr('class');

		// 无选择商品
		if(_class=='iconfont icon-xuanze'){
			$(this).children().removeClass('iconfont icon-xuanze').addClass('iconfont icon-kuang1');
			
			priceAll = 0;
			var chooseGoods = $(".itemCar").find("i[class='iconfont icon-xuanze']");
			// 实时将总价格更新到合计
			for(var i=0; i<chooseGoods.length; i++){
				
				priceAll += parseFloat(chooseGoods.eq(i).parents(".itemCar").find(".number")[0].value)*parseFloat(chooseGoods.eq(i).parents(".itemCar").find(".price>span").text());
			} 
			// 将价格显示到合计
			$(".priceAll>span").text(priceAll);
		// 有选择商品
		}else{
			$(this).children().removeClass('iconfont icon-kuang1').addClass('iconfont icon-xuanze');
			priceAll = 0;
			var chooseGoods = $(".itemCar").find("i[class='iconfont icon-xuanze']");
			// 实时将总价格更新到合计
			for(var i=0; i<chooseGoods.length; i++){
				
				priceAll += parseFloat(chooseGoods.eq(i).parents(".itemCar").find(".number")[0].value)*parseFloat(chooseGoods.eq(i).parents(".itemCar").find(".price>span").text());
			} 
			// 将价格显示到合计
			$(".priceAll>span").text(priceAll);
		}
		// 如果没有一个选中
		if(!$(".icon-xuanze").length){
			console.log('没了');
			$(".priceAll>span").text(0);
		}
		// 全部选中了
		if($(".itemCar").find(".icon-xuanze").length == $(".itemCar").length){
			$('.shoppingcarBott>p>i').removeClass('iconfont icon-kuang1').addClass('iconfont icon-xuanze');
		}else if($(".itemCar").find(".icon-xuanze").length != $(".itemCar").length){
			// 未全选
			$('.shoppingcarBott>p>i').removeClass('iconfont icon-xuanze').addClass('iconfont icon-kuang1');
		}
	})
	// 全选事件
	$('.shoppingcarBott>p>i').click(function(){
		var _quanclass=$(this).attr('class');
		
		// 全选
		if(_quanclass.indexOf('icon-kuang1') > -1){
			$(this).removeClass('iconfont icon-kuang1').addClass('iconfont icon-xuanze');
			var _class=$('.itemcarLeft>.xuanzhong>i');

			for(var i = 0; i < _class.length; i++) {
				_class.eq(i).removeClass(_class.eq(i).attr("class")).addClass('iconfont icon-xuanze');
			}
			priceAll = 0;
			var chooseGoods = $(".itemCar").find("i[class='iconfont icon-xuanze']");
			// 实时将总价格更新到合计
			for(var i=0; i<chooseGoods.length; i++){
				priceAll += parseFloat(chooseGoods.eq(i).parents(".itemCar").find(".number")[0].value)*parseFloat(chooseGoods.eq(i).parents(".itemCar").find(".price>span").text());
			} 
			// 将价格显示到合计
			$(".priceAll>span").text(priceAll);
		}else{
			// 全不选
			$(this).removeClass('iconfont icon-xuanze').addClass('iconfont icon-kuang1');
			var _class=$('.itemcarLeft>.xuanzhong>i').attr('class');
			console.log("全部不选的", _class)
			$('.itemcarLeft>.xuanzhong>i').removeClass(_class).addClass('iconfont icon-kuang1');

			priceAll = 0;
			var chooseGoods = $(".itemCar").find("i[class='iconfont icon-xuanze']");
			// 实时将总价格更新到合计
			for(var i=0; i<chooseGoods.length; i++){
				
				priceAll += parseFloat(chooseGoods.eq(i).parents(".itemCar").find(".number")[0].value)*parseFloat(chooseGoods.eq(i).parents(".itemCar").find(".price>span").text());

				// console.log(chooseGoods.eq(i).parents(".itemCar").find(".number")[0].value);
				// console.log(chooseGoods.eq(i).parents(".itemCar").find(".price>span").text());
			} 
			// 将价格显示到合计
			$(".priceAll>span").text(priceAll);
		}
		
	})
})