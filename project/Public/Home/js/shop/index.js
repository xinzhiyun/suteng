
// ajax 请求全部数据
var html = $.ajax({
	url: ThinkPHP.shopIndexUrl,
	async: false
   }).responseText;

data = JSON.parse(html);
var cartCount = data.cartinfo,
	goods 	  = data.goods,
	banner 	  = data.banner,
	cate 	  = data.cate;
console.log('banner: ',banner);
window.onload = function(){
	// 轮播图
	var bannerHTML = '';
	if(banner){
		banner.map(function(img, index){
			bannerHTML += '<a class="pic swiper-slide" goods_gid="'+ img.id
			+'" href="javascript:;"><img src="/Uploads/'+ img.pic +'" /></a>'
		})
		$('.swiper-wrapper').html(bannerHTML);
	}
	// 自动轮播 
	var mySwiper = new Swiper('.swiper-container', {
		autoplay: 5000,//可选选项，自动滑动
		pagination : '.swiper-pagination',
	})
}
//首页显示购物车数量
// console.log($("#cartInfo").val())
if(cartCount){
	$(".cartnum>span").text(cartCount);
}else {
	// 如果购物车没有商品
	$(".cartnum>span").text(0);
}

//存放分栏的商品信息
var CategoryHTML = '', categoryArr = [];
var goodblockHTML = '';		//分栏商品模板
var _htmlArr = [];	//存放对应分类数据的数组
var searchArr = [];		//存放搜索用数据
	var tab_now = 0;	// 当前点击的 tab
	var isLoad = [];	//滚动懒加载的标志
	// lazyArr: 懒加载的内容, 跟当前分类下一致，
	// 数组的每个子元素长度为6个商品（数量遍历商品时给出）
	var lazyArr = [];	

/************ 数据遍历 -- 开始 **************/
// 获取商品分类
var _cate;
if(cate){
	//有数据
	_cate = cate;
	// console.log(_cate);
	// 遍历分类
	for(var i=0;i<_cate.length; i++){
		CategoryHTML += '<span class="tab" cid="'+ i +'" index='+i+'>'+ _cate[i].name +'</span>';
		categoryArr.push(_cate[i].name);
	}

	/*
		遍历模板
	 */
	for(var i=0; i<_cate.length; i++){
		goodblockHTML += 
			'<div class="goodsBlock" index='+i+'>'+
			  '<div class="goods" >'+
				<!-- /*所有商品*/ -->
				'<div class="goodstitle">'+
					'<h3><span>——</span> 特惠专区 <span>——</span></h3>'+
				'</div>'+
				'<div class="allgoods">'+
					'<ul></ul>'+
					'</div>'+
				'</div>'+
			'</div>';
	}

	// 横线
	var _lineHTML = '<div id="line"></div>';
	$("#header").html(CategoryHTML+ _lineHTML);
	$("#content").append(goodblockHTML);

}else{
	$("#header").html('暂无分类');
	// console.log('暂无分类');
}
// 顶部滚动过渡效果
// $("#header").css({width: ($("#header>span").length+2)*20 + 'vw'});
/*
	遍历商品
 */
var _goods;
var lazyNum = 0;	// '|'的加载次数
if(goods != 'null'){
	//有数据
	_goods = goods;
	// console.log(_goods);
	if(_cate){
		for(var i=0;i<_cate.length; i++){
			_htmlArr[[i]] = '';		//使用前先初始化
			searchArr[[i]] = '';		//使用前先初始化
			for(var j=0; j<_goods.length; j++){
				if(_goods[j].cid == _cate[i].id){	//对应分类下有商品
					lazyNum++; 
					// console.log(_goods[j].cid, _cate[i].id);
					searchArr[i] += '<a href="javascript:;" goods_gid="'+_goods[j].gid+'">'+
							'<p>'+ _goods[j].name +'</p>'+
						'</a>';

					_htmlArr[i] += 
						'<li>'+
							'<a class="pic" goods_gid="'+_goods[j].gid+'" href="javasctipt:;" cid="'+ i +'">'+
								'<span ><img src="/Uploads/'+_goods[j].path+'" alt="正在加载中..."></span>'+
								'<p class="name">'+ _goods[j].name +'</p>'+
								'<b class="price">¥'+ _goods[j].price +'</b>'+
							'</a>'+
							'<i class="iconfont icon-jiarugouwuche"></i>'+
						'</li>';
					if(lazyNum > 4 && (lazyNum+1)%4 == 0){
						// console.log('lazyNum: ', lazyNum);
						// console.log('(lazyNum+1)%6: ',(lazyNum+1)%4);
						//每隔6个添加 '|' ，后面懒加载用
						_htmlArr[i] += '|';
						// lazyNum = 0;
					}
				}else{
					_htmlArr[i] += '';
				}
			}
		}

	}
}else{
	//无数据
	$(".allgoods>ul").html('<h3 style="text-align:center;padding:5% 0;">暂无内容，敬请期待！<div></div></h3>');
	// console.log('暂无商品');
}

// 遍历偏移量
for(var i=0; i<categoryArr.length; i++){
	isLoad.push([false]);
	$("#content").find('.goodsBlock').eq(i).css({left: i*100 +'vw'});
	// console.log($("#content").find('.goodsBlock').attr("index"))
}
// console.log('isLoad: ',isLoad)
/*
	如果第一页有内容则加载第一页
 */
// console.log('_htmlArr: ',_htmlArr)
if(_htmlArr[0]){
	// $(".allgoods>ul").eq(0).html(_htmlArr[0]);
	// console.log($("#content>div").eq(0).attr("index"))
	var html = _htmlArr[tab_now].substr(0,_htmlArr[0].length-1);
	if(html.split('|').length <= 1){
		if(_htmlArr[0].lastIndexOf('|') == _htmlArr[0].length-1){
			_htmlArr[0] = _htmlArr[0].substring(0, _htmlArr[0].length-1);
		}
		$(".allgoods>ul").eq(0).html(_htmlArr[0]);
	}else{
		if(html.lastIndexOf('|') == html.length-1){
			html = html.substring(0, html.length-1);
		}
		//如果数据多余6条， 则先加载6条，剩下的懒加载
		$(".allgoods>ul").eq(0).html(html.split('|')[0]);
	}
	
}else{
	$(".allgoods>ul").eq(0).html('<h3 style="text-align:center;padding:5% 0;">暂无内容，敬请期待！<div></div></h3>');
	$('#container').off('scroll');
}

/*********** 数据遍历 -- 结束 **************/


/*
	点击顶部导航栏，切换订单状态
 */ 
var len = 10;
var ullen;	//记录当前分类的数据状态，有则不用重新生成

$("#header").on("click",'.tab',function(){
	// 回到顶部
	document.body.scrollTop = -10000;
	$("#container")[0].scrollTop = -10000;
	// console.log(document.body.scrollTop, $("#container")[0].scrollTop)
	var _this = $(this);
	//如果当前分类下有商品, 且允许滚动， 否则禁止
	if(_htmlArr[_this.attr("index")] && ullen < 1){	
		
		$('#container').css({
			overflowY : 'scroll'
		})
	}else if(!_htmlArr[_this.attr("index")]){

		$('#container').css({
			overflowY : 'hidden'
		})
	}
	// 当前点击的tab
	tab_now = Number($(this).attr('index'));
	// 调用content 切换函数
	tabContent($(this));
	// loading
	$("#loading").fadeIn('fast');
	for(var i=0; i<$('.goodsBlock').length; i++){
		$('.goodsBlock').eq(i).fadeOut('fast');
	}
	$('.goodsBlock').eq(tab_now).fadeIn('fast');
	setTimeout(function(){
		$("#loading").fadeOut('fast');
	},600);
	
	sessionStorage.setItem('shopCid', $(this).attr('cid'));
	var _height = $('.goodsBlock').eq(tab_now)[0].offsetHeight + $(".shopTop")[0].offsetHeight;
	// 防止多余滚动
	$('#container').css({
		height: _height + 'px'
	})
	$('#content').css({
		height: $('.goodsBlock').eq(tab_now)[0].offsetHeight + 'px'
	})
	// console.log('tab_now: ',tab_now);
	lazyArr = _htmlArr[tab_now].split('|');
	// 监听滚动
	$('#container').on('scroll', function(){
		// console.log('++++++++++++++ scroll ++++++++++++++');
		// console.log('408 ',isLoad[tab_now][0]);
		if(isLoad[tab_now][0] == false){
			lazyLoad($(this));
		}
	})

	console.log('lazyArr: ',lazyArr)
});
// 切换的函数
function tabContent(_this){

	ullen = $(".allgoods").eq(_this.attr("index")).find("ul")[0].childElementCount;
	//如果当前分类下有商品, 且页面未生成，则添加数据，否则不改动
	if(_htmlArr[_this.attr("index")] && ullen < 1){	
		var nowhtml = _htmlArr[_this.attr("index")];
		// console.log(nowhtml.lastIndexOf('|')==nowhtml.length-1);

		if(nowhtml.lastIndexOf('|') == nowhtml.length-1){
			nowhtml = nowhtml.substring(0,nowhtml.length-1);

			$(".allgoods>ul").eq(_this.attr("index")).html(nowhtml);
		}else{

			$(".allgoods>ul").eq(_this.attr("index")).html(nowhtml);
		}
		// console.log(nowhtml);
		
		$('#container').css({
			overflowY : 'scroll'
		})
	}else if(!_htmlArr[_this.attr("index")]){
		$(".allgoods>ul").eq(_this.attr("index")).html('<h3 style="text-align: center;padding:5% 0;">暂无内容，敬请期待！<div></div></h3>');

		$('#container').css({
			overflowY : 'hidden'
		})
	}
	// console.log('ullen: ',ullen)
	$('#header>span').removeClass('tabnow');
	_this.addClass('tabnow');

	/******* 切换联动 *******/

	//切换内容
	$("#content")
	.css({transform: 'translateX(-'+ _this.attr("index") +'00vw)'});
	//横线移动
	$("#line").css({left: _this[0].offsetLeft+_this[0].clientWidth/2-$("#line")[0].clientWidth/2 + 'px'});

	/******* 切换联动 *******/
	if(!_htmlArr[tab_now]){
		$('#container').off('scroll');
	}
}
// console.log($('.tab')[0].offsetLeft+$('.tab')[0].clientWidth-$("#line")[0].clientWidth/2)
// 页面加载时顶部分类横线位置初始化

$("#line").css({left: $('.tab')[0].offsetLeft+$('.tab')[0].clientWidth/2-$("#line")[0].clientWidth/2 + 'px'});
$('#header>span').eq(0).addClass('tabnow');


// 状态恢复(点击过去商品详情后，后退回来商城首页)
if(sessionStorage.getItem('shopCid')){
	var cid = Number(sessionStorage.getItem('shopCid'));
	tabContent($('.tab').eq(cid));
	tab_now = +cid;
	$('#header')[0].scrollLeft = $('.tab').eq(tab_now-1)[0].offsetLeft;
	// console.log($('#header')[0].scrollLeft)
	// 初始化
	// sessionStorage.setItem('shopCid', '');
	// console.log('tab_now: ',tab_now);
}

/************** 懒加载 -- 开始 ***************/
/*
	监听滚动事件
 */ 
	// loading 动画
$("#loading").fadeOut('slow');
// '|' 每6个li 添加一次，所以超过6个商品时，每次拉倒底部，再加载6个
lazyArr = _htmlArr[tab_now].split('|');

// console.log('lazyArr: ', lazyArr);

var num = 1;	//加载次数， 达到当前分类数组长度时，停止滚动
var scrollH, offsetH, scrollTop;
$('#container').on('scroll', function(){
	// console.log('++++++++++++++ scroll ++++++++++++++');
	console.log(isLoad[tab_now][0])
	if(isLoad[tab_now][0] == false) {		//停止懒加载标志: isLoad[tab_now][0] == true
		lazyLoad($(this));
	}
})
// 懒加载函数
function lazyLoad(_this){

	/*
		触底加载，直到没有数据
		tab_now: 当前点击的tab
	 */
		scrollH = _this[0].scrollHeight;
		offsetH = _this[0].offsetHeight;
		scrollTop = _this[0].scrollTop;

	// console.log(scrollTop/(scrollH - offsetH));
	// console.log('scrollHeight - offsetHeight: ', scrollH - offsetH);
	// console.log('scrollTop: ',scrollTop);

	if(scrollTop/(scrollH - offsetH) >= 0.99){	//触底加载，直到没有数据
		// console.group()
		// console.log('scrollAppend: ',lazyArr[num]);

		if(_htmlArr[tab_now]){	//对应分类下有多数据才添加到页面
			$(".allgoods>ul").eq(tab_now).append(lazyArr[num]);
			num++;
		}
		// 如果没有数据了， 解绑监听scroll
		if(num = lazyArr.length){
			$('#container').off('scroll');
			$(".allgoods>ul").eq(tab_now).append('<li class="nomore"><p>已无更多数据，休息一下吧！</p><span></span><span></span></li>');
			num = 1;
			isLoad[tab_now] = true;		//停止懒加载标志
			// return
		}
	}
	// console.log('tab_now: ', tab_now);
	// console.log('num: ', num);
	// console.log('lazyArr.length: ', lazyArr.length);
	// console.groupEnd()
}

/*********** 懒加载 -- 结束 ************/