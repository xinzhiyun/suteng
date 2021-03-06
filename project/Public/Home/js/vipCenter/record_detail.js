
var recoed_detail = new Vue({
    el: '#recoed_detail',
    data () {
        return {
            notice: '正在加载...',
            noticestyle: 'block',
            workid: GetQueryString('workid'),
            name: '',			// 处理人
            detailList: [],
            showEval: '',	// 是否显示评价按钮
            evalPanel: '',		// 评价面板的样式
            starList: [],		// 所有星星的数据	
            textList: [],		// 对应星星的选项数据
            star: 5,			// 星星数量
            text: '',			// 对安装师傅说的话
            nonameclass: 'iconfont icon-fangxingweixuanzhong',
            showEvalPanel: false,
            text_focus: false
        } 
    },
    mounted() {},
    created (){
        var vm = this;
        // 获取详情数据
        vm.getDetail(vm.workid);

        hashchangeFn(function(hash){
            if(hash.indexOf('#evaluate') < 0){
                // 隐藏
                document.title = '工单详情';
                vm.showEvalPanel = false;
                vm.evalPanel = 'transform:translateY(100%)';
            }else{
                vm.showEvalPanel = true;
                document.title = '服务评价';
                // 显示
                vm.evalPanel = 'transform:translateY(0%)';
            }
        })

        // 监听hashchange事件
        function hashchangeFn(callback) {
            var haschangeflag = false;
            window.addEventListener('hashchange', function(){
                haschangeflag = true;
                callback(location.hash);
            }, false);
            // console.log('haschangeflag: ',haschangeflag);
            // console.log('hash: ',location.hash);
            // 刷新保持
            if(!haschangeflag){
                callback(location.hash);
            }
        }
        var height = window.innerHeight;
        window.onresize = function(){
            console.log('top: ',top);
            var evalpanel = document.querySelector('.evalPanel');
            if(height == window.innerHeight){
                evalpanel.style.transform = 'translateY(0%)';
                vm.text_focus = false;
            }
            
        }
    },
    methods: { 
        // 获取详情数据
        getDetail (workid) {
            var vm = this;
            console.log('workid: ',workid);
            $.ajax({
                url: getURL('Home', 'vipCenter/showWorkTimeInfo'),
                type: 'post',
                data: {number: workid},
                success: function(res){
                    console.log('res: ',res);
                    if(res.status == 200){
                        vm.name = res.name;
                        if(res.data && res.data.length){
                            vm.noticestyle = "none";
                            var content = [];
                            for(var i=0; i<res.data.length; i++){
                                res.data[i].time = res.data[i].time.split(' ');
                                // 二维数组
                                content.push([]);
                                var temp = [];
                                temp = JSON.parse(res.data[i].content);
                                for(var j=0; j<temp.length; j++){
                                    content[i].push(temp[j]);

                                }

                            }
                            console.log('content:',content);
                            vm.detailList = res.data;
                            vm.detailList['content'] = content;
                            
                        }else{
                            vm.notice = "工单未审核或不存在";
                            layuiHint('工单未审核或不存在');
                        }
                        if(res.evaluaction){
                            // 需要评价
                            vm.showEval = 'block';
                            // 获取评价的数据
                            vm.getEvaluate(workid);
                        }
                    }else{
                        layuiHint(res.msg);
                    }
                },
                error: function(err){
                    console.log('err: ',err);
                }
            })
        },
        // 获取评论的数据
        getEvaluate (workid) {
            console.log('workid: ',workid);
            var vm = this;
            $.ajax({
                url: getURL('Home', 'Work/getEvaluate'),
                type: 'get',
                data: {number: workid},
                success: function(res){
                    console.log('评论星星数据res: ',res);
                    vm.starList = res.data;
                    // 五星
                    vm.textList = res.data[5][0];
                },
                error: function(err){
                    console.log('err: ',err);
                }
            })
        },
        // 点击评论按钮
        evaluate () {
            var vm = this;
            vm.showEvalPanel = true;
            vm.evalPanel = 'transform:translateY(0);';
            location.href = location.href + '#evaluate';
        },
        // 点击星星
        xingx (index) {
            this.star = index;
            console.log('star: ',this.star);
            var evalel = document.querySelectorAll('.eval');
            for(var i=0; i<evalel.length; i++){
                evalel[i].setAttribute('class', '');
            }
            // 根据星星数量显示不同的选项
            this.textList = this.starList[this.star][0];

        },
        // 评价多选
        textSelect (e) {
            var el = e.currentTarget;
            var elclass = el.getAttribute('class');
            if(elclass == 'eval'){
                el.setAttribute('class', '');
            }else{
                el.setAttribute('class', 'eval');
            }
        },
        // 提交评论数据
        upEval () {
            var vm = this;
            var info = {};		// 提交的数据
            var evalidArr = [];
            var textEval = document.querySelector('.text').querySelectorAll('.eval');
            // 遍历评论选项的id
            for(var i=0; i<textEval.length; i++){
                evalidArr.push(textEval[i].getAttribute('tid'))
            }
            if(!evalidArr.length){
                layuiHint('请至少选择一个评价');
                return
            }
            info = {
                workid: vm.workid,		// 工单id
                star: vm.star,			// 评价星星数量
                evalid: evalidArr,		// 评价选项id集合
            };
            // 对安装师傅说的话
            if(vm.text){
                console.log('vm.text.length: ',vm.text.length);
                if(vm.text.length > 120){
                    layuiHint('仅限120字以内');
                    return
                }
                info['text'] = vm.text;
            }
            console.log('info: ',info);
            // 提交评论数据
            $.ajax({
                url: getURL('Home', 'VipCenter/evaluAction'),
                type: 'post',
                data: info,
                success: function(res){
                    console.log('res: ',res);
                    if(res.status == 200){
                        history.replaceState({}, null, getURL('Home', 'VipCenter/service_record'));
                        document.body.innerHTML = '<div class="notic">'+
                            '<i class="iconfont icon-dagouyouquan"></i>'+
                            '<p>评价成功</p>'+
                            '<p>感谢你们对我们服务的支持！</p>'+
                        '</div>';
                        setTimeout(function(){
                            location.reload();
                        },3000)
                    }else{
                        layuiHint(res.msg);
                    }
                },
                error: function(err){
                    console.log('err: ',err);
                    layuiHint('系统遇到问题，请稍后再试');
                }
            })
        },
        noname () {
            if(this.nonameclass.indexOf('xuanzhongduigou') < 0){
                this.nonameclass = 'iconfont icon-xuanzhongduigou';
            }else{
                this.nonameclass = 'iconfont icon-fangxingweixuanzhong';
            }
            
        },
        // text点击
        textfocus() {
            if(!this.text_focus && !window.__wxjs_is_wkwebview){
                this.text_focus = true;
                var top = $('.say>textarea').offset().top;
                // console.log('top: ',top);
                var evalpanel = document.querySelector('.evalPanel');
                evalpanel.style.transform = 'translateY(0)';
                evalpanel.style.transform = 'translateY(-'+ top/2.4 +'px)';
            }
        },
        // 失去焦点
        textblur() {
            this.text_focus = false;
            var evalpanel = document.querySelector('.evalPanel');
            evalpanel.style.transform = 'translateY(0)';
        }
    }
})
