var filter = new Vue({
    el: '#filter',
    data() {
        return {
            restoreData: [],
            dataList: [],
            filtermode: '',
        }
    },
    created() {
        var vm = this;
        // vm.filtermode = 0;
        console.log('dataList: ',vm.dataList);
        // vm.dataList = [{
        //     name: 'PP棉',
        //     desc: '有效去除水中的泥沙、铁锈、你屋交替等大于5微米的颗粒杂质',
        //     reday: '5',
        //     reflow: '325',
        //     sumday: '97',
        //     sumflow: '1000'
        // },{
        //     name: 'RO膜',
        //     desc: '有效去除水中的泥沙、铁锈、你屋交替等大于5微米的颗粒杂质',
        //     reday: '15',
        //     reflow: '125',
        //     sumday: '92',
        //     sumflow: '1000'
        // }]
    },
    methods: {
        reset(index, filterName, e) {   // 复位
            var vm = this;
            console.log('index: ',index);
            console.log('vm.restoreData[index]: ',vm.restoreData[index]);
            var ajson;//数据对象
            var tipsText = "确定要复位<a>滤芯" + filterName +"</a>吗";
            //弹框提示
            layui.use('layer', function(){
                var layer = layui.layer;
                layer.confirm(tipsText, {icon: 3, title:'温馨提示'}, function(index){
                    layer.close(index);
                    ajson = {
                        "DeviceID": vm.deviceid,
                        "PackType": "SetData",
                        "Vison": 0,
                    };
                    var type = 0;
                    ajson['ReFlowFilter'+(index+1)] = vm.restoreData[index].flowlife;
                    ajson['ReDayFilter'+(index+1)] = vm.restoreData[index].timelife;
                    ajson['FlowLifeFilter'+(index+1)] = vm.restoreData[index].flowlife;
                    ajson['DayLifeFiter'+(index+1)] = vm.restoreData[index].timelife;
                    ajson['type'] = '复位中';
                    type = index;
                    //发送数据
                    console.log('ajson: ',ajson);
                    websoket.send(JSON.stringify(ajson));
                    CmdList.push({
                        cmd: ajson,
                        type: type
                    });
                    identify = 1;
                    timer = setTimeout(function(){
                        if(identify == 1){
                            // layui.use('layer', function(){
                            //     var layer = layui.layer;
                            //     layer.msg('复位超时！');
                            // });

                            identify=0;
                        }
                    },10000)
                })
            })
        }
    }
})