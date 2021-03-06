//设置字体自适应大小
(function (doc, win) {
	var docEl = doc.documentElement,
	resizeEvt = 'orientationchange' in window ? 'orientationchange' : 'resize',
	recalc = function () {
		var clientWidth = docEl.clientWidth;
		if (!clientWidth) return;
		docEl.style.fontSize = 20 * (clientWidth / 320) + 'px';
	};
	if (!doc.addEventListener) return;
	win.addEventListener(resizeEvt, recalc, false);
	doc.addEventListener('DOMContentLoaded', recalc, false);
})(document, window);

//设置软件盘弹出后优化页面布局
function solveCompatible(obj){
	var oHeight = $(document).height(); //浏览器当前的高度
	$(window).resize(function(){
		if($(document).height() < oHeight){
			obj.css("position","static");
		}else{
			obj.css("position","absolute");
		} 
	});
}

// 图片懒加载
function lazyLoad(block){
	var block = document.querySelector(block);	// 图片所在区域
	var img = block.querySelectorAll('img[data-src]');
	// console.log('block: %s, img: %s', block, img);
	if(!img.length)	return;
	for(var i=0; i<img.length; i++){
		(function(img){
			setTimeout(function(){
				// console.log('img: ',img);
				var datasrc = img.getAttribute('data-src');
				var src = img.getAttribute('src');
				if(datasrc != src){
					img.setAttribute('src', datasrc);
				}
			},200*i);
		})(img[i])
	}
}
/**
* 获取网址（解决在js文件中无法使用thinkPHP的大U方法的问题）
* @param {string} [_home] [Home 或 Coms]
* @param {string} [_url] [模块，如 Index/index]
*/ 
function getURL(_home, _url){
	var href = location.href;
	var homeurl, homeindex;
	// 默认首页
	_url = _url || 'Index/index';
	// 获取 Home 或 Coms 的下标
	if(href.indexOf(_home) > -1){
		homeindex = href.indexOf(_home) + _home.length;
	}
	homeurl = href.substring(0, homeindex) + '/' + _url;
	return homeurl;
}

/**
* 移动删除函数	 李小摄
* @param {Object} 选择器
* @param {Object} 自定义属性，用于记录要删除内容ID值
*/
function slipDel(parent,obj,i){
	$(parent).on("touchstart", obj, function(e) {
		// 判断默认行为是否可以被禁用
		if (e.cancelable) {
			// 判断默认行为是否已经被禁用
			if (!e.defaultPrevented) {
				//e.preventDefault();
			}
		}   
		startX = e.originalEvent.changedTouches[0].pageX,
		startY = e.originalEvent.changedTouches[0].pageY;
	});
	$(parent).on("touchend",obj, function(e) {         
		// 判断默认行为是否可以被禁用
		if (e.cancelable) {
			// 判断默认行为是否已经被禁用
			if (!e.defaultPrevented) {
				//e.preventDefault();
			}
		}               
		moveEndX = e.originalEvent.changedTouches[0].pageX,
		moveEndY = e.originalEvent.changedTouches[0].pageY,
		X = moveEndX - startX,
		Y = moveEndY - startY;
		//console.dir(this)
		$(this).parent().css({'position': 'relative','overflow': 'hidden'});
		// 右滑
		if ( X > 0 ) {
			$(this).css({'position': 'relative','left':'0px'});
			$(this).children("span").remove();
		}
		// 左滑
		else if ( X < 0 ) {
			// var parentHeight=$(this).parent().attr('height');
			// console.dir(parentHeight);
			// line-height: 4rem;	字体居中给obj>span{line-height:X}
			$(this).css({'position': 'relative','left':'-2rem'});
			$(this).append("<span class='del' style='display: inline-block;width: 2rem;height: 100%;position: absolute;right: -2rem;top:0;text-align: center;background: #D3520C;color:#fff;'>删除</span>");
		}
	});
}

// 
// 基于layui 弹框提示封装
function layuiHint(text){
	layui.use('layer', function(){
		var layer = layui.layer;
		layer.msg(text);
	});  
}


// 验证姓名，手机号，地址的正则变量定义
var	nameReg = /^([a-zA-Z0-9_\u4e00-\u9fa5]){2,30}$/,
phoneReg = /^(1[3|4|5|7|8])\d{9}$/,
addressReg = /^(?=.*?[\u4E00-\u9FA5])[\dA-Za-z\u4E00-\u9FA5]{8,}/;

//去空格
function trimFn(str){
	str += '';
	return str.replace(/^(\s)|(\s*)/g,'');
}
//验证中文
var chineseCheck = function(val){
	return /^[\u4e00-\u9fa5]{2,255}$/.test(val);
}
//验证特殊字符
var specailCheck = function(val){
	return /^[`~!@#$^&*()=|{}':;',\[\].<>/?~！@#￥……&*（）——|{}【】\s‘；：”“'。，、？]$/.test(val)
}
// 验证身份证
var cardCheck = function(val) {
	return /(^\d{15}$)|(^\d{18}$)|(^\d{17}(\d|X|x)$)/.test(val);
}


// 格式化日期时间
function timetrans(date){
	var date = new Date(date*1000);//如果date为13位不需要乘1000
	var Y = date.getFullYear() + '-';
	var M = (date.getMonth()+1 < 10 ? '0'+(date.getMonth()+1) : date.getMonth()+1) + '-';
	var D = (date.getDate() < 10 ? '0' + (date.getDate()) : date.getDate()) + ' ';
	var h = (date.getHours() < 10 ? '0' + date.getHours() : date.getHours()) + ':';
	var m = (date.getMinutes() <10 ? '0' + date.getMinutes() : date.getMinutes()) + ':';
	var s = (date.getSeconds() <10 ? '0' + date.getSeconds() : date.getSeconds());
	//return Y+M+D+h+m+s;
	return Y+M+D;
}

function dateformat(date){
	var date = new Date(date*1000);//如果date为13位不需要乘1000
	var Y = date.getFullYear() + '-';
	var M = (date.getMonth()+1 < 10 ? '0'+(date.getMonth()+1) : date.getMonth()+1) + '-';
	var D = (date.getDate() < 10 ? '0' + (date.getDate()) : date.getDate()) + ' ';
	var h = (date.getHours() < 10 ? '0' + date.getHours() : date.getHours()) + ':';
	var m = (date.getMinutes() <10 ? '0' + date.getMinutes() : date.getMinutes()) + ':';
	var s = (date.getSeconds() <10 ? '0' + date.getSeconds() : date.getSeconds());
	return Y+M+D+h+m+s;
}
//获取url参数
function GetQueryString(name) {  
	var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i");  
	var r = window.location.search.substr(1).match(reg);  //获取url中"?"符后的字符串并正则匹配
	var context = "";  
	if (r != null)  
	context = r[2];  
	reg = null;  
	r = null;  
	return context == null || context == "" || context == "undefined" ? "" : context;  
}