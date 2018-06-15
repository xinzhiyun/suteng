 
// 顶部导航栏、下拉刷新
!function(){
     
    // loading 加载等待
    var loading = document.createElement('div');
    loading.setAttribute('class', 'loadingdiv');
    // loading.innerHTML = '<div>'+
    // '<i class="am-icon-spinner am-icon-pulse am-icon-lg"></i>'+
    // '<div class="am-text-center" style="margin-top:10px;">正在加载 ...</div></div>';
    //.loadingdiv {width:30vmin;height:30vmin;display:none;position:fixed;max-width:140px;max-height:140px;left:50%;top:50%;border-radius:4px;transform:translate(-50%,-50%);color:#eee;background:rgba(0,0,0,.8);z-index: 10000;}.loadingdiv>div {width:100%;height:100%;display:flex;align-items:center;justify-content:center;flex-flow:column;}
    
    loading.setAttribute('class', 'loadingdiv');
    loading.innerHTML = '<div class="load"></div>'+
        '<p>加载中···</p>';
    //.loadingdiv{width:30vmin;height:30vmin;max-width:120px;max-height:120px;min-width:120px;min-height:120px;position:fixed;top:-60px;right:0;left:0;bottom:0;margin:auto;display:flex;flex-flow:column;align-items:center;justify-content:center;border-radius:6px;background:rgba(0,0,0,.7);box-sizing:content-box;z-index:10000;}.load{width:30px;height:30px;display:block;margin-bottom:16px;border-radius:50%;border:2px solid #999;border-right:2px solid #eee;animation:run linear 1s infinite}.loadingdiv>p{color:#fff;font-size:14px}@keyframes run{0%{transform:rotate(0)}25%{transform:rotate(90deg)}50%{transform:rotate(180deg)}75%{transform:rotate(270deg)}100%{transform:rotate(360deg)}}
    // 添加到页面
    document.body.appendChild(loading);
    // 提示框，确认取消框，loading框, navbar等的样式
    var style = document.createElement('style');
    var stylecode = '.loadingdiv{width:30vmin;height:30vmin;max-width:120px;max-height:120px;min-width:120px;min-height:120px;position:fixed;top:-60px;right:0;left:0;bottom:0;margin:auto;display:flex;flex-flow:column;align-items:center;justify-content:center;border-radius:6px;background:rgba(0,0,0,.7);box-sizing:content-box;z-index:10000;}.load{width:30px;height:30px;display:block;margin-bottom:16px;border-radius:50%;border:2px solid #999;border-right:2px solid #eee;animation:run linear 1s infinite}.loadingdiv>p{color:#fff;font-size:14px}@keyframes run{0%{transform:rotate(0)}25%{transform:rotate(90deg)}50%{transform:rotate(180deg)}75%{transform:rotate(270deg)}100%{transform:rotate(360deg)}}#noticeDiv {width:100vw;position:fixed;padding: 0 4%;top:30%;left:50%;text-align:center;margin-left:-50%;transition:.2s ease;z-index:999;transform:scale(0);z-index:9999;}#noticeDiv>span {display:inline-block;padding:10px;border-radius:4px;box-shadow:0 0 10px rgba(0,0,0,.2);}#confirmDiv {width: 80vw;max-width:400px;position:fixed;top:40%;left:50%;text-align:center;border-radius:6px;box-shadow:0 0 10px rgba(110,101,110,.2);color:#000;transform:translate(-50%,-50%);z-index:999;background:#fff;}#confirmDiv>div {padding:20px 16px;text-align:left;text-align:center;}#confirmDiv>div:nth-of-type(1){margin: 20px 0;font-size: .8rem;}#confirmdiv3 {width:100%;padding:0 !important;border-radius:0 0 6px 6px;border-top: 1px solid #a1a1a1;overflow: hidden;}#confirmdiv3::after {content:"";width:100%;height:100%;display:block;clear:both;visibility:hidden;}#confirmdiv3>span {width:50%;display:inline-block;position:relative;padding:10px 0;text-align:center;background: #eee;}#confirmtrue {float:right;color:#0d94f3;border-left: 1px solid #d9d9d9;}#confirmfalse {float:left;color:#555;}#confirmfalse:active::after,#confirmtrue:active::after {content: "";width: 100%;height: 100%;display: block;position: absolute;top: 0;left: 0;background: rgba(0,0,0,.1);}#confirmPar {width:100vw;height:100vh;display:none;position:fixed;top:0;left:0;background:rgba(0,0,0,.2);z-index:9999;}@media screen and (max-width: 379px){#noticeDiv,#confirmDiv {font-size: .7rem;}}@media screen and (min-width: 380px){.loadingdiv>p{font-size:18px;}#noticeDiv,#confirmDiv,#confirmDiv>div {font-size: 18px !important;}}#fadeshow {display: block;-webkit-animation: showload .5s forwards;animation: showload .5s forwards;}#fadehide {display: block;-webkit-animation: hideload .5s forwards;animation: hideload .5s forwards;}@-webkit-keyframes showload {from {opacity: 0;}to {opacity: 1;}}@keyframes showload {from {opacity: 0;}to {opacity: 1;}}@-webkit-keyframes hideload {from {opacity: 1;}to {opacity: 0;}}@keyframes hideload {from {opacity: 1;}to {opacity: 0;}}';
    stylecode += '.go2Top{width:12vmin;height:12vmin;line-height:12vmin;display:none;position:fixed;border-radius: 50%;bottom:10vmin;right:2vmin;text-align:center;color:#0e90d2;background:#fff;box-shadow:0px 2px 16px 0px rgba(0,0,0, .1);transform: translate3d(0,0,0);font-weight:bold;z-index:99;}.go2Top:active{box-shadow: 0px 2px 16px 0px rgba(0,0,0, .1) inset;}';
    stylecode += '.upfresh{width:30vmin;height:30vmin;max-width:120px;max-height:120px;min-width:120px;min-height:120px;position:relative;top:20px;right:0;left:0;margin:auto;display:flex;flex-flow:column;align-items:center;justify-content:center;border-radius:6px;background:rgba(0,0,0,.7);box-sizing:content-box;z-index:10000}.load3{width:20px;height:20px;display:block;position:absolute;top:0;bottom:0;left:0;right:0;margin:auto;border-radius:50%;border:2px solid #333;border-right:2px solid rgba(0,0,0,0);animation:run3 linear 1s infinite}.upfresh>p{color:#fff;font-size:14px}@keyframes run3{0%{transform:rotate(0)}25%{transform:rotate(90deg)}50%{transform:rotate(180deg)}75%{transform:rotate(270deg)}100%{transform:rotate(360deg)}}';
    style.innerHTML = stylecode;
    document.head.appendChild(style);

    var navbar = document.createElement('div');
    var fresh = document.createElement('div');  // 下拉刷新
    navbar.setAttribute('id', 'navbar');
    navbar.setAttribute('class','am-cf');
    navbar.innerHTML = '<span class="iconfont icon-zuojiantou" style="color:#808080;font-size:0.8rem;" onclick="history.go(-1)">&emsp;</span>'+
        '<h2>title</h2>'+
        '<a class="back2home"></a>';
    var fc = document.body.firstChild;
    fresh.setAttribute('id','refresh');
    fresh.setAttribute('style','width: 50px;height: 50px;line-height: 40px;position: fixed;font-size:1rem;top: 0;left:50%;padding:4px;border-radius:50%;text-align: center;transition: .3s ease-in;transform: translate(-50%,-120%);background:#f6f6f6;box-shadow:0 0 10px rgba(0,0,0,.2);z-index: 99999;');
    fresh.innerHTML = '<div class="load3"></div>';
    // 添加到页面body下最前面
    document.body.insertBefore(navbar,fc);
    // 下拉刷新
    fc = document.body.firstChild;
    document.body.insertBefore(fresh,fc);
    // 获取title
    var title = document.getElementById('navbar').getElementsByTagName('h2');
    title[0].innerText = document.getElementsByTagName('title')[0].innerText;

    // 显示loading
    var loaddiv = document.querySelector('.loadingdiv');
    loaddiv.setAttribute('id', 'fadeshow');
    loaddiv.setAttribute('style', 'display:flex;');

}()

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

/*
    生成提示框、确认取消框和loading 加载模块的页面元素
    并添加样式到页面
 */ 
!function(){

    // 顶部按钮
    var go2Top = document.createElement('div');
    go2Top.setAttribute('class', 'go2Top');
    go2Top.setAttribute('ontouchstart', 'goTop()');
    go2Top.innerHTML = '<i class="iconfont icon-xiangshang1"></i>';

    // 生成提示框元素
    var notice = document.createElement('div');
    notice.setAttribute('id', 'noticeDiv');
    notice.innerHTML = '<span></span>';

    // 生成确认取消框元素
    var confirm = document.createElement('div');
    // 遮罩层
    confirm.setAttribute('id','confirmPar');
    var contain = '<div id="confirmDiv">'+
        '<div>'+
            '<p><i class="am-icon-question-circle-o"></i>&nbsp;'+
            '<span class="text" style="color:#0d94f3;"></span>&nbsp;</p>'+
        '</div>'+
        '<div id="confirmdiv3">'+
            '<span id="confirmfalse" >取消</span>'+
            '<span id="confirmtrue" >确定</span>'+
        '</div>'+
    '</div>';
    confirm.innerHTML = contain;

    document.body.appendChild(confirm);
    document.body.appendChild(notice);
    document.body.appendChild(go2Top);
}()

/**
 * 提示框函数 noticeFn()
 * @param {字符串} obj.text 提示的文字内容
 * @param {字符串} obj.fcolor 提示的文字颜色
 * @param {字符串} obj.bgcolor 提示框背景颜色
 * @param {字符串} obj.time 提示框消失的时间
 */
var noticeint;
var noticeFn = function(obj){
    // {text:'哈哈很少见哈' + $(this)[0].innerText,fcolor:'',bgcolor:'',time:''}
    /*
        用法：
        noticeFn({text:'哈哈很少见哈' + $(this)[0].innerText});
     */
    //设置字体颜色 
    obj.fcolor = obj.fcolor || '#fff';
    //设置背景颜色
    obj.bgcolor = obj.bgcolor || 'rgba(70,70,70,.9)';
    // 消失时间
    obj.time = obj.time || 1000;
    // console.log(obj.time);
    var notice = document.getElementById('noticeDiv');
    var noticeSpan = notice.getElementsByTagName('span')[0];
    
    // 防止多次点击, 弹框跳动
    if(noticeSpan.innerHTML){
        
        notice.setAttribute('style',
            'transform:scale(1.4);'
        );
        setTimeout(function(){
            notice.setAttribute('style',
                'transition: .3s ease;transform:scale(1);'
            );
        },0)
        
        // 防止多次点击, 弹框跳动
        clearTimeout(noticeint);
        noticeSpan.innerHTML = obj.text;
        // 自动消失
        noticeint = setTimeout(function(){
            notice.style.opacity = '.5';
            notice.style.transform = 'scale(1.1)';
            setTimeout(function(){
                notice.style.transform = 'scale(0)';
                noticeSpan.innerHTML = '';
            },100);
        },obj.time);
        return
    }
    noticeSpan.innerHTML = obj.text;
    noticeSpan.style.color = obj.fcolor;
    noticeSpan.style.background = obj.bgcolor;

    notice.setAttribute('style',
        'display:block;opacity:1;transform:scale(1);'
    );

    // 自动消失
    noticeint = setTimeout(function(){
        notice.style.opacity = '.5';
        notice.style.transform = 'scale(1.1)';
        setTimeout(function(){
            notice.style.transform = 'scale(0)';
            noticeSpan.innerHTML = '';
        },100);
    },obj.time);
}

//验证手机号码
var phoneCheck = function(val){
    return /^1[3,4,5,6,7,8]\d{9}$/.test(val)
} 
//验证email
var emailCheck = function(val){
    return /^([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+@([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+\.[a-zA-Z]{2,3}$/.test(val)
}
//验证中文
var chineseCheck = function(val){
    return /^[\u4e00-\u9fa5]{2,255}$/.test(val);
}