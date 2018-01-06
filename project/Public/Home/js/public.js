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