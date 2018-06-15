$(function(){
	// var postage = $("#postage").val();
	var sold_num = $('#sold_num').val();
	//清除历史发票信息sessionStorage
	sessionStorage.setItem("voiceArr", '');
	//显示购物车数量
	if(typeof Number($("#cartInfo").val()) == 'number'){
		$('.cartnum>span').text($("#cartInfo").val())

	}else{
		$('.cartnum>span').text(0);
	}

	//存放ajax请求成功返回的商品信息
	var goodsDetail;
	// 从地址栏URL中获取从商城首页点击的商品唯一id
	var goods_gid = location.search.substr(location.search.indexOf("=")+1);
	// console.log(goods_gid)
	var desc;
	var goodsPicArr = [];	//顶部图片浏览
	var picPathArr = []; 	//存放后台传过来的图片路径转换的数组
	var commPicArr = [];	//存储每条评论的图片
	var commentInfo_html = '';
	var norms = [];	 	//规格
	var arr = [];
	var gObj = {};
	var emHTML = '';
	var name = [];
	var eachNume = [];
	var val = [];
	var eachVal = [];
	// ajax发送商品id查询商品详情
	$.ajax({
		url: getURL('Home', 'Shop/goods_detail'),
		type: 'get',
		async: false,
		data: {'id': goods_gid},
		success: function(res){
			console.log('商品详情成功！ ',res);
			//显示购物车数量
			$(".icon-gouwuche>span").text(res.cartnum);
			goodsDetail = res.goodsDetail[0];
			var goodsDetail_html = ''
			desc = !goodsDetail.desc ? '商品暂无描述' : goodsDetail.desc;
			goodsDetail.attr = !goodsDetail.attr ? '' : goodsDetail.attr;
			
			// 轮播图片
			for(var i = 0; i < goodsDetail.pics.length; i++) {
				// console.log(goodsDetail.pics[i].path)
				// 商品图片
				goodsPicArr.push(goodsDetail.pics[i].path);
			}
			// 规格
			for(var i = 0;i<res.goodsDetail.length;i++){
				name.push(res.goodsDetail[i].aname);
				val.push(res.goodsDetail[i].val);
			}
			// 去重
			for(var i = 0;i<name.length;i++){
				if(eachNume.indexOf(name[i]) == -1){
					eachNume.push(name[i])
					eachVal.push(val[i])
				} 
			}
			// 规格
			for(var i = 0;i<eachNume.length;i++){
				gObj = {
					name : eachNume[i],
					val : eachVal[i]
				}
				norms.push(gObj)
				// emHTML += '<span class="guige" style="color:#F04B7E;font-size:0.5rem;width:7rem;display:inline-block">'+ norms[i].name +':'+norms[i].val+'</span>'; 
				emHTML += '<span class="guige" style="color:#F04B7E;font-size:0.5rem;">'+ norms[i].name +':'+norms[i].val+'</span>'+'</br>';
			}
			console.log(norms)
			// 输出商品详情
			goodsDetail_html = '<div class="detailpic"><ul id="wrapul" class="my-gallery" data-pswp-uid="001"></ul><span class="closepicshow">X</span><span class="picnum"><b>1</b><span></span></span></div>'+
				'<!-- /*商品说明*/ -->'+
				'<div class="detailTxt">'+
					'<span><b>￥'+ goodsDetail.price +'</b><em>已售： '+ sold_num +'</em></span>'+
					'<b>'+ goodsDetail.name +'</b>'+
					'<p>'+ emHTML +'</p>'+
					// '<em>'+ emHTML+'</em>'+
				'</div>'+
				'<!-- /*商品选择，数量，邮费*/ -->'+
				'<div class="detailist">'+
					'<p>'+
						'<span>数量</span>'+
						'<span class="num">'+
							'<input type="button" value="—" class="minus">'+
							'<input class="number" type="number" value="1" min="1" max="19">'+
							'<input type="button" value="+" class="plus">'+
						'</span>'+
					'</p>'+
				'</div>';
			// 将商品信息添加到页面
			$(".detailTop>div").html(goodsDetail_html);

			/* 顶部图片滑动部分 -- 开始 */
			$('.picnum>span').text('/'+ goodsPicArr.length);
			var goodsPicHTML = '';
			goodsPicArr.map(function(value,index){
				goodsPicHTML += '<li index="'+ index +'" src="/Uploads/'+ value +'"><img data-src="/Uploads/'+ value +'" src="/Public/Home/images/loading.gif" alt="加载中..."></li>';
			})
			$('.detailpic>ul').html(goodsPicHTML);
			$('.moredetail>div').html(goodsPicHTML);	// 详情介绍图片
			// 设置图片宽度
			$('.detailpic>ul')[0].style.width = goodsPicArr.length*100 + '%';
			$('.detailpic>ul').find('li').css({width: 100/goodsPicArr.length + '%'});
			var wrapul = document.getElementById('wrapul');
			var wrapli = wrapul.getElementsByTagName('li');
			var imgScale = null;
			// 实例化滑动
			var move = new tMove({
				elem: wrapul, 
				lilen: wrapli.length,
				scale: true
			},function(res){
				// 当前页数
				var nowpic = Math.abs(res.offleft/window.innerWidth);
				// console.log('nowpic: ', nowpic);
				if(res.scale){
					console.log('双击', res);
					// 当前图片
					var imgnow = res.elem.childNodes[nowpic];
					console.log("当前图片",imgnow);
				}else {
					// offleft为零则第一张
					nowpic = res.offleft == 0 ? 1 : nowpic+1;
					// 显示当前页数
					$('.picnum>b').text(Math.round(nowpic));
					return
				}
				
			});
			// 点击查看
			view();
			/* 顶部图片滑动部分 -- 结束 */

			// console.log(picPathArr, '\n', commPicArr);
			// 遍历评论列表的每个评论的图片
			for(var m=0; m<commPicArr.length; m++){
				for(var n=0; n<commPicArr[m].length; n++){
					// console.log('468: ', commPicArr[m][n].img)
					$(".detailItem").eq(m).append(commPicArr[m][n].img)
				}
			}
			// 只显示2条评论
			var _length=$('.detailItem').length;
			if (_length>2) {
				$('.detailItem').css('display','none');
				$('.detailItem:nth-child(1)').css('display','block');
				$('.detailItem:nth-child(2)').css('display','block');
			}
		},
		error: function(res){
			console.log('失败！ ',res)
		}
	})
	function view(){
		var items = [];
		var pswpElement = document.querySelectorAll('.pswp')[0];
		var lipic = $('#wrapul>li');
		// console.log('lipic: ',lipic);
		for(var i=0; i<lipic.length; i++){
			// console.log('src: ',lipic[i].getAttribute('src'));
			// 查看的图片
			items.push({
				index: i,
				src: lipic[i].getAttribute('src'),
				w: 320,
				h: 640
			})
		}
		// console.log('items: ',items);
		// Initializes and opens PhotoSwipe

		// define options (if needed)
		var options = {
		    // optionName: 'option value'
		    // for example:
		    history: true,
		    index: 0 // start at first slide
		};
		$('#wrapul').on('click', 'li', function(){
			var index = $(this).attr('index');
			options.index = +index;
			// console.log('options: ',options);
			var gallery = new PhotoSwipe( pswpElement, PhotoSwipeUI_Default, items, options);
			gallery.init();

		})
	}
	var statusList = [100, 60, 0];		// 好评，中评，差评
	var statusText = ['好评', '中评', '差评'];		// 好评，中评，差评
	var statusDesc = ['质量非常好，与卖家描述一致，非常满意', '质量一般，没有卖家描述那么好', '差的太离谱了，与卖家描述严重不符'];		// 好评，中评，差评
	var commetHTML = '';	//存放评论相关商品的数据
	// 请求评论数据
	$.ajax({
		url: getURL('Home', 'Comment/getComments'),
		type: 'get',
		data: {gid: goods_gid},
		success: function(res){
			console.log('请求评论成功： ',res);
			var picStr = '';	//存放图片的html
			var picArr = [];	//存放评论图片的数组
			var text_html = '';		//商品评价
			var nickname,head;
			var numindex = 0;
			// 如果有评价则遍历出来
			for(var i=0; i<res.data.length; i++){
				if(res.data[i].user == null) {
					nickname = '不知道是谁';
					head = '';
				}else {
					nickname = res.data[i].user.nickname == null ? '不知道是谁' : res.data[i].user.nickname;
					head = !res.data[i].user.head ? '' : res.data[i].user.head;
				}
				if(res.data[i].installer_graded == 0){	
					// 用户对商品评论,
					text_html = '<!-- 评论内容 -->'+
						'<div class="itemgoodsBott">'+
							'<p class="cfix pos"><span class="fleft"><img src="'+ head +'" alt="">'+
							'</span><span>&nbsp;'+ nickname +'</span><span>'+ timestampToTime(res.data[i].addtime) +'</span></p>'+
							'<p class="cfix xingx">'+
								'<i index="1" class="iconfont pingfen fleft"><span style="width:'+ statusList[res.data[i].grade-0-1] +'%"></span></i>'+
								'<span class="fleft desc">'+ statusText[res.data[i].grade-0-1] + '<span>（'+ statusDesc[res.data[i].grade-0-1] +'）</span></span>'+
							'</p>'+
							'<p class="ccontent">'+ res.data[i].content +'</p>'+
							'<div class="commpic">'+
								'<ul></ul>'+
							'</div>'+
						'</div>';     
				}else{		//安装评价
					text_html = '<!-- 五星评分 -->'+
						'<div class="mysaleContain">'+
							'<div class="mysaleBot">'+
								'<p class=".clearfix">'+
									'<b>安装人员评分</b>'+
									'<i index="1" class="iconfont pingfen"><span style="width:'+ res.data[i].installer_graded*20 +'%"></span></i>'+
								'</p>'+
								'<p class=".clearfix">'+
									'<b>服务态度评分</b>'+
									'<i index="1" class="iconfont pingfen"><span style="width:'+ res.data[i].attitude*20 +'%"></span></i>'+
								'</p>'+
								'<p class=".clearfix">'+
									'<b>着装评分</b>'+
									'<i index="1" class="iconfont pingfen"><span style="width:'+ res.data[i].dressing*20 +'%"></span></i>'+
								'</p>'+
								'<p>安装师傅认真负责，很好的一次用户体现</p>'+
							'</div>'+
						'</div>';
				}
				commetHTML += text_html;
				// 判断是否接收到图片数据
				if(!res.data[i].pics.length) { 
					dataPic = "";
					picArr.push({'picHTML':''}); //无图片， 则为空
				}else {
					picStr = '';	//先清空再生成
					for(var j=0; j<res.data[i].pics.length; j++){
						numindex++;
						// console.log('numindex',numindex);
						picStr += '<li><img class="compic" index="'+ numindex +'" src="'+res.data[i].pics[j].path+'" alt="图片加载中"></li>';
					}
					picArr.push({'picHTML':picStr});
				}
			}
			console.log(picArr)
			if(commetHTML){
				$(".detailItemall").html(commetHTML);		//评价的内容
				//遍历评价的图片
				for(var i=0; i<picArr.length; i++){
					$(".commpic").eq(i).children("ul").html(picArr[i].picHTML);	
					// console.log(picArr[i].picHTML)		//评价的图片
				}
			}else{
				$(".detailItemall").html("<div style='width:100%;display:flex;justify-content:center;align-items:center;'>暂无评论</div>");
			}
			// 图片浏览
			setTimeout(function(){
				picview();
			},0)
		},
		error: function(err){
			console.log('请求失败： ',err);
		}
	})

	function picview(){
		var items = [];
		var pswpElement = document.querySelectorAll('.pswp')[0];
		var compic = $('.compic');
		// console.log('compic: ',compic);
		for(var i=0; i<compic.length; i++){
			// console.log('src: ',compic[i].getAttribute('src'));
			// 查看的图片
			items.push({
				index: i,
				src: compic[i].getAttribute('src'),
				w: 320,
				h: 640
			})
		}
		// console.log('items: ',items);
		// Initializes and opens PhotoSwipe

		// define options (if needed)
		var options = {
		    // optionName: 'option value'
		    // for example:
		    history: true,
		    index: 0 // start at first slide
		};
		$('body').on('click', '.compic', function(){
			var index = $(this).attr('index');
			options.index = +index-1;
			// console.log('options: ',options);
			var gallery = new PhotoSwipe( pswpElement, PhotoSwipeUI_Default, items, options);
			gallery.init();

		})
	}
	var price;
	/*
		 按加'+'号
	 */
	$(".shopdetailContain").on("touchend",'.plus',function(){
		price = 0;	//合计归零，后面重新计算
		// 获取数量
		var value = $(".number").val();
		$(this).siblings(".number")[0].value++;

	});
	/*
		按减'-'号
	 */
	$(".shopdetailContain").on("touchend",".minus",function(){
		price = 0;	//合计归零，后面重新计算
		// 获取数量
		var value = $(".number").val();
		for(var i=0; i<$(this).siblings(".number").length; i++){
			$(this).siblings(".number").val() <= 1
			? 1
			: $(this).siblings(".number")[i].value--
		}
		// console.log($(this).siblings(".number")[0].value);

	});
	// 数量输入
	$('.number').on('keyup', function(e){
		var val = $(this).val();
		if(val.length == 0){
			$(this).val(val.replace(/^[0-9]/g,''));
		}else{
			$(this).val(val.replace(/\D/g, ''));
		}
		if(val.length >= 6){
			$(this).css({width: val.length-0+18 + 'vw'});
		}else{
			$(this).css({width: 'auto'});
		}
	})
	$('.number').on('change', function(e){
		if(!$(this).val() || $(this).val() == 0){
			$(this).val(1);
		}
	})
	var detailContainscTop = $('.detailtitle').height()+$('.detailtitle').offset().top;
	// 点击‘更多’展开评论或者收起
	$('.pinglunshow>p').on('click', function(){
		// console.dir($(this).find("span").attr('class'));
		var _class=$(this).find("span").attr('class');

		if(_class=='iconfont icon-shuangxiajiantou'){ // 收起状态 -> 展开
			// $('.detailItem:nth-of-type(1)').css('display','block');
			$('.detailItem').slideDown('slow');

			$(this).find("span").removeClass('iconfont icon-shuangxiajiantou').addClass('iconfont icon-xiangshangjiantou');

			$('.pinglunshow b').text('收起评论');
			$('.slide').show();
			$('html').scrollTop(detailContainscTop);
			$('body').scrollTop(detailContainscTop);
			$('.detailItemall').css({height: '70vh',overflowY: 'scroll'});
			setTimeout(function(){
				$('.slide').hide();
			},3000);
		}else{
			$(this).find("span").removeClass('iconfont icon-xiangshangjiantou').addClass('iconfont icon-shuangxiajiantou');

			$('.detailItem').slideUp('fast');

			// $('.detailItem').eq(0).slideDown('slow');
			// $('.detailItem').eq(1).slideDown('slow');
			$('.detailItem').eq(0).css('display','block');
			$('.detailItem').eq(1).css('display','block');
			$('.detailItemall').css({height: 'auto',overflowY: 'hidden'});
			$('.slide').hide();
			$('.pinglunshow b').text('查看更多');

		}
	})

	/*
		点击‘加入购物车’
	 */
	$("#add2ShopCart").on('touchend', function(){
		
		var num = $(".number").val();	//选择的数量
		var addtime = new Date().getTime();	//添加进购物车的时间
		// var expressMoney = $(".postagename").text();	//快递费
		// var expressMoney = goodsCourier[postageNum];	//快递费goodsCourier[postageNum]
		/*
			goods_gid:商品唯一ID
			num: 商品数量
		 */
		var data = JSON.stringify([{'gid':goods_gid,'num':num}])
		console.log(data)
		$.ajax({
			url: getURL('Home', 'ShoppingCart/shopAdd'),
			type: 'post',
			data: {'gid': goods_gid,'num':num},
			success: function(res){
				console.log('成功！ ', res)
				//更新购物车数量
				$('.icon-gouwuche>span').text(res);
				sessionStorage.setItem('cartnum', res);
       			parent.layer.msg('添加成功！');
			},
			error: function(res){
				console.log('失败！ ', res)
       			parent.layer.msg('添加失败！');
			}
		})
	})

	/*
		点击‘立即购买’
	 */
	$("#buyNow").on('touchend', function(){
		//清除其他页面的sessionStorage
		sessionStorage.setItem("lvxinData", '');
		sessionStorage.setItem("shopCar_data", '');
		var num = $(".number").val();	//选择的数量

		console.log('num: ',num);
		if(num == 0 || !num){
			parent.layer.msg('商品数量不能是0或者为空');
			return
		}else if(num < 0){
			parent.layer.msg('商品数量不能小于0');
			return
		}
		// var expressMoney = $(".postagename").text();	//快递费
		// var postageInfo = goodsCourier[postageNum];
		/*
			goods_gid:商品唯一ID
			num: 商品数量
			uid: 用户id
		 */
		var goods_data = JSON.stringify({'goodsDetail': goodsDetail,'num': num});
		sessionStorage.setItem("goods_data",goods_data);
		//发送给后台生成订单编号的数据
		var goods_dataArr = [];
		var money = num*(goodsDetail.price);	//订单总价
		goods_dataArr.push({'num':num,'gid': goodsDetail.gid,'money': money});

		sessionStorage.setItem("goods_dataArr",JSON.stringify(goods_dataArr));
		// 发送ajax请求让后台生成订单号    
		$.ajax({ 
			url: getURL('Home', 'PaymentSystem/information'),
			type: 'post',
			data: {'data': JSON.stringify(goods_dataArr)},
			success: function(res){
				console.log('成功！', res);
				if(res.code == 604){
					parent.layer.msg(res.msg);
					return;
				}
				
				if(typeof JSON.parse(res) == 'number'){
					sessionStorage.setItem('order_id',res);
					// alert('{{:U("Pay/payConfirm/order_id/'+res+'")}}')

					// 跳转到订单确认页面
					location.href = getURL('Home', 'Pay/payConfirm')+'?order_id='+res;
				}else{
					//支付发生错误，无法生成订单号
					parent.layer.msg('系统发生错误，请稍后再试！');
				}

			},
			error: function(res){
				console.log('失败！', res);
			}
		})
	})

	// 时间戳转换
	function timestampToTime(timestamp) {
		var date = new Date(timestamp * 1000);//时间戳为10位需*1000，时间戳为13位的话不需乘1000
		var Y = date.getFullYear() + '/';
		var M = (date.getMonth()+1 < 10 ? '0'+(date.getMonth()+1) : date.getMonth()+1) + '/';
		var D = date.getDate() < 10 ? '0'+date.getDate() + ' ' : date.getDate()+' ';
		var h = (date.getHours() < 10 ? '0'+ date.getHours() : date.getHours()) + ':';
		var m = (date.getMinutes() < 10 ? '0'+ date.getMinutes() : date.getMinutes());
		// var s = date.getSeconds();
		return Y+M+D+h+m;
	}

})
setTimeout(function(){
	// console.log($('img[data-src]'));
	lazyLoad($('img[data-src]'));
	
},0);
// 监听页面滚动
document.body.onscroll = function(e){
	e = event || window.event;
	var scheight = window.innerHeight;
	var scrollHeight = document.body.scrollHeight || document.documentElement.scrollHeight;
	var scrollTop = document.body.scrollTop || document.documentElement.scrollTop;
	// console.log('scrollHeight: ',scrollHeight);
	// console.log('scrollTop + height: ',scrollTop + scheight);
	console.log($('.moredetail').scrollTop());
	if($('.moredetail').scrollTop() == 0){
		// 详情介绍顶部
		$('.moredetail').css({
			// transform: 'translateY(0%)',
		});
	}
	if(scrollHeight <= scrollTop + scheight + 10){
		console.log('滑到底部了');
		getDetail();
	}else{
	}
}

// 获取商品详情介绍
function getDetail(){
	// $.ajax({
	// 	url: '{{:U("")}}',
	// 	type: 'get',
	// 	success: function(res){
	// 		console.log('res: ',res);
	// 	},
	// 	error: function(err){
	// 		console.log('err: ',err);
	// 	}
	// })
	
	$('.moredetail').css({
		// transform: 'translateY(0)'
	});
}

// 图片懒加载
function lazyLoad(img){
	for(var i=0; i< img.length; i++){
		(function(img){
			setTimeout(function(){
				// console.log('img: ',img);
				img.setAttribute('src', img.getAttribute('data-src'));
			},500)
		})(img[i])
	}
}