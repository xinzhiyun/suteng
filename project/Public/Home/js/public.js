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
	        $(this).append("<span onclick='del("+$(this).attr(i)+")' style='display: inline-block;width: 2rem;height: 100%;position: absolute;right: -2rem;top:0;text-align: center;background: #D3520C;color:#fff;'>删除</span>");
	    }
	});
}