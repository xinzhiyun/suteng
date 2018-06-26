window.onload = function(){
    function addwater(){//制水状态
        $(".makewater").show().siblings().hide();
        $("body").css({height:'100vh',filter:'grayscale(0%)'});
        $(".btmTxt").html("制水");
    }
    function wash(){//冲洗状态 1
        $(".btmTxt").html("冲洗");
        $(".xuanhuan").show().siblings().hide();
        $("body").css({height:'100vh',filter:'grayscale(0%)'}); 
    }
    function lesswater(){// 缺水状态
        $(".queshui").show().siblings().hide();
        $("body").css({height:'100vh',filter:'grayscale(0%)'});
        $(".btmTxt").html("缺水");
    }
    function fullwater(){// 水满状态2
        $(".shuiman").show().siblings().hide();
        $("body").css({height:'100vh',filter:'grayscale(0%)'});
        $(".btmTxt").html("水满");
    }
    function service (){// 修检状态 5
        $(".service").show().siblings().hide();
        $("body").css({height:'100vh',filter:'grayscale(0%)'});
        $(".btmTxt").html("检修");
    }
    function shutdown(){//关机状态 7
        $(".shutdown").show().siblings().hide();
        $("body").css({height:'100vh',filter:'grayscale(100%)'});
        $(".btmTxt").html("设备已关机");
        $(".clickBtn").css({color:'#f00',filter:'grayscale(0%)'});
    }
    function arrearage(){//欠费 6
        $(".arrearage").show().siblings().hide();
        $("body").css({height:'100vh',filter:'grayscale(100%)'});
        $(".btmTxt").html("欠费");
    }
    function outLine(){//离线 7
        $(".outLine").show().siblings().hide();
        $("body").css({height:'100vh',filter:'grayscale(100%)'});
        $(".btmTxt").html("设备已离线");
    }
    function machineStatus() {
        if(_status=="0"){// 制水状态
            addwater();
            $('.icon-guanji').addClass('poweron');
            $('.icon-guanji').siblings('p').css({color:'#4D70FF'});
        }else if(_status=="2"){// 水满状态
            fullwater();
            $('.icon-guanji').addClass('poweron');
            $('.icon-guanji').siblings('p').css({color:'#4D70FF'});
        }else if(_status=="3"){// 缺水状态
            lesswater();
            $('.icon-guanji').addClass('poweron');
            $('.icon-guanji').siblings('p').css({color:'#4D70FF'});
        }else if(_status=="4"){// 漏水状态

        }else if(_status=="5"){// 检修状态
            service();
            $('.icon-guanji').addClass('poweron');
            $('.icon-guanji').siblings('p').css({color:'#4D70FF'});
        }else if(_status=="6"){// 欠费停机状态
            arrearage();
            $('.icon-guanji').addClass('poweroff');
            $('.icon-guanji').siblings('p').css({color:'#333'});
        }else if(_status=="7"){// 关机状态
            shutdown();
            $('.icon-guanji').addClass('poweroff');
            $('.icon-guanji').siblings('p').css({color:'#333'});
        }else{// 冲洗状态
            wash();
            $('.icon-guanji').addClass('poweron');
            $('.icon-guanji').siblings('p').css({color:'#4D70FF'});
        }
    }
    //页面加载时获取数据
    var getdataList = JSON.parse($(".deviceInfo").val());//滤芯剩余量数据
    console.log(getdataList)
    var res = JSON.parse($(".res").val());//滤芯详情数据
    var _status=getdataList.devicestause;//获取设备当前状态-> 0:制水 1:冲洗 2:水满 3:缺水 4漏水 5:检修 6:欠费停机 7:关机 8:开机(仅命令)

    machineStatus();//执行当前设备状态

    // $(".rawtdsVal").html(getdataList.rawtds?getdataList.rawtds:'--');//纯水TDS
    $(".phVal").html(getdataList.phval?getdataList.phval:'--');//ph酸碱值
    // $(".rawtdsVal2").html(getdataList.puretds?getdataList.puretds:'--');//原水值
    // $(".puretdsVal").html(getdataList.rawtds?getdataList.rawtds:'--');//原水值

    $(".puretds").html(getdataList.puretds?getdataList.puretds:'--');//纯水值
    $(".rawtds").html(getdataList.rawtds?getdataList.rawtds:'--');//原水值

    $(".hotwater").html((getdataList.temperature?getdataList.temperature:'--')+"&#8451;");
    $(".coldwater").html(parseInt(Math.random()*10+10)+"&#8451;");
    $(".roomtemperature").html(parseInt(Math.random()*10+20)+"&#8451;");
    var usedflow = getdataList.sumslow;
    var usedday = getdataList.sumday;
    var reflow = getdataList.reflow;
    var reday = getdataList.reday;
    if(getdataList.leasingmode=="0"){//按零售
        $(".surplusVal").html("<b>"+(usedflow?usedflow:'--')+"L</b></br>已用流量");
        $(".alreadyUsedVal").html("<b>"+(usedday?usedday:'--')+"天</b></br>已用天数");

    }else if(getdataList.leasingmode=="1"){//按流量
        $(".surplusVal").html("<b>"+(reflow?reflow:'--')+"L</b></br>剩余流量");
        $(".alreadyUsedVal").html("<b>"+(usedflow?usedflow:'--')+"L</b></br>已用流量");
    }else if(getdataList.leasingmode=="2"){//按天数
        $(".surplusVal").html("<b>"+(reday?reday:'--')+"天</b></br>剩余天数");
        $(".alreadyUsedVal").html("<b>"+(usedday?usedday:'--') + "天</b></br>已用天数");
    }else if(getdataList.leasingmode=="3"){//流量&时间

    }




    //websoket
    var websoket=new WebSocket("ws://120.78.184.0:6001");
    var PackNum=0;//包数据
    var CmdList=[];//命令队列
    var numAdd=0;
    var deviceId=getdataList.device_code;
    // 获取设备id
    // var deviceId=228733445596778;
    websoket.onopen=function(){
        console.log('websocket已连接');
        //包数据
        ajson={
            "DeviceID":deviceId,
            "PackType":"login",
            "Vison":0
        };
        websoket.send(JSON.stringify(ajson));
        setTimeout(function(){
            ajson.PackType="Select";
            websoket.send(JSON.stringify(ajson));
            setInterval(function(){
                websoket.send(JSON.stringify(ajson));
            },10000);
        },1000);
    }
    //实时接收数据
    websoket.onmessage=function(data)
    {
        var dataList=JSON.parse(data.data);//读取websoket数据，转换为json对象
        //console.log(dataList.LeasingMode);
        _status=dataList.DeviceStause;
        machineStatus();//执行水机状态方法
        console.log('message: ',dataList);
        
        if(dataList.PackType=="Select")//返回查询数据类型数据
         {	//console.log(dataList);
            numAdd=0;
            var usedflow=dataList.SumFlow;
            var usedday=dataList.SumDay;
            var reflow = dataList.ReFlow;
            var reday = dataList.Reday;
            var hotwater = dataList.hotwater;
            var coldwater = dataList.coldwater;
            var roomtemperature = dataList.roomtemperature;
            var rawtds = dataList.RawTDS;
            var phval = dataList.phval; 
            var puretds = dataList.PureTDS;
            //1.设备状态页面数据显示
            // $(".rawtdsVal").html((rawtds?rawtds:'--'));//纯水值
            // $(".phVal").html(phval?phval:'--');//ph酸碱值
            // $(".puretdsVal").html((puretds?puretds:'--'));//原水值
            // $(".rawtdsVal2").html((rawtds?rawtds:'--'));//纯水值

            // $(".rawtdsVal").html(getdataList.rawtds?getdataList.rawtds:'--');//纯水TDS
            $(".phVal").html(phval?phval:'--');//ph酸碱值
            // $(".rawtdsVal2").html(getdataList.puretds?getdataList.puretds:'--');//原水值
            // $(".puretdsVal").html(getdataList.rawtds?getdataList.rawtds:'--');//原水值

            $(".puretds").html(puretds?puretds:'--');//纯水值
            $(".rawtds").html(rawtds?rawtds:'--');//原水值

            $(".hotwater").html((hotwater?hotwater:'--')+"&#8451;");//热水温度
            // $(".coldwater").html((coldwater?coldwater:12)+"&#8451;");//冷水温度
            // $(".roomtemperature").html((roomtemperature?roomtemperature:45)+"&#8451;");//室内温度

            if(dataList.LeasingMode=="0"){//按零售
                $(".surplusVal").html("已用流量："+(usedflow?usedflow:'--')+"L");
                $(".alreadyUsedVal").html("已用天数："+(usedday?usedday:'--')+"天");

            }else if(dataList.LeasingMode=="1"){//按流量
                $(".surplusVal").html("剩余流量："+(reflow?reflow:'--')+"L");
                $(".alreadyUsedVal").html("已用流量："+(usedflow?usedflow:'--')+"L");
            }else if(dataList.LeasingMode=="2"){//按天数
                $(".surplusVal").html("剩余天数："+(reday?reday:'--')+"天");
                $(".alreadyUsedVal").html("已用天数：" + (usedday?usedday:'--') + "天");
            }else if(dataList.LeasingMode=="3"){//流量&时间
            }

        }
        else if(dataList.PackType=="SetData"){//设置数据类型数据
            // identify=0;
            for(var i=0;i<CmdList.length;i++){
                if(CmdList[i].cmd.PackNum==dataList.PackNum){
                    if(CmdList[i].cmd.type=="关机中"){
                        shutdown();

                    }else if(CmdList[i].cmd.type=="开机中"){
                        machineStatus();

                    } else if(CmdList[i].cmd.type=="冲洗中"){
                        wash();

                    }else  if(CmdList[i].cmd.type=="加热中"){
                        layui.use('layer', function(){
                            var layer = layui.layer;
                            layer.msg('已经给您加热了，天气冷小心烫着！');
                            // $('.heat').show().fadeOut(6000).fadeIn(3);
                            // setTimeout(function(){
                            //         $('.heat').hide();
                            // },6000);
                        })
                    }

                    CmdList.splice(i,1);
                    break;
                }
            }
        }
        //显示当前设备状态
        machineStatus();
    }
    //90秒后判断设备是否离线
    setInterval(function(){
        numAdd++;
        if(numAdd ==90){
            outLine();
        }
    }, 1000);

    //冲洗按钮操作
    $('.washBtn').click(function(){
        var statusTxt=$(".btmTxt").html();
        if(statusTxt=="设备已关机"||statusTxt=="设备已离线"||statusTxt=="欠费"||statusTxt=="检修"){
            layui.use('layer', function(){
                var layer = layui.layer;
                layer.msg('当前设备状态不能冲洗');
            });
            return false;
        }else{
            var ajson;//数据对象
            var type="";
            //判断操作类型
            //弹框提示
            layui.use('layer', function(){
                var layer = layui.layer;
                layer.confirm("确定要冲洗"+deviceId+"水机吗？", {icon: 3, title:'温馨提示'}, function(index){
                    layer.close(index);
                    ajson={
                        "DeviceID":deviceId,
                        "PackType":"SetData",
                        "Vison":0,
                        "PackNum":PackNum,
                        "curTime":0,
                    };
                    //根据当前设备状态设置按钮文本
                    ajson['DeviceStause']=1;
                    ajson['type']='冲洗中';
                    //发送数据
                    websoket.send(JSON.stringify(ajson));
                    CmdList.push({
                        cmd:ajson,
                        type:"冲洗中"
                    });
                });
            });
        }

    });
    //开机/关机按钮操作
    $('.clickBtn').click(function(){
        var statusTxt=$(".btmTxt").html();
        var _this='';
        if(statusTxt=="设备已离线"||statusTxt=="设备已关机"){
            _this='开机';
        }else{
            _this='关机';
        }
        var ajson;//数据对象
        //判断操作类型
        var tipsText = "确定要"+ _this + deviceId +"吗？";
        //弹框提示
        layui.use('layer', function(){
            var layer = layui.layer;
            layer.confirm(tipsText, {icon: 3, title:'温馨提示'}, function(index){
                layer.close(index);
                $("body").css({height:'100vh',filter:'grayscale(100%)'});
                ajson={
                    "DeviceID":deviceId,
                    "PackType":"SetData",
                    "Vison":0,
                    "PackNum":PackNum,
                    "curTime":0,
                };
                //根据当前设备状态设置按钮文本
                var type=0;
                if(_this=="开机"){
                    ajson['DeviceStause']=8;
                    ajson['type']='开机中';

                }else if(_this=="关机"){
                    ajson['DeviceStause']=7;
                    ajson['type']='关机中';

                }
                //发送数据
                websoket.send(JSON.stringify(ajson));
                CmdList.push({
                    cmd:ajson,
                    type:type
                });
                // identify=1;
                // setTimeout(function(){
                //     if(identify==1){
                //         identify=0;
                //     }
                // },10000)
            });
        });
    });
    // 加热操作
    $('.heatTxt').click(function(){
        var statusTxt=$(".btmTxt").html();
        if(statusTxt=="设备已关机"||statusTxt=="设备已离线"||statusTxt=="欠费"||statusTxt=="检修"){
            layui.use('layer', function(){
                var layer = layui.layer;
                layer.msg('当前设备状态不能加热');
            });
            return false;
        }else{
            layui.use('layer', function(){
                var layer = layui.layer;
                layer.confirm('亲，确定要加热吗?', {icon: 3, title:'温馨提示'}, function(index){
                    layer.close(index);
                    
                    ajson={
                        "DeviceID":deviceId,
                        "PackType":"SetData",
                        "Vison":0,
                        "PackNum":PackNum,
                    };
                    //根据当前设备状态设置按钮文本
                    ajson['type']='加热中';
                    //发送数据
                    websoket.send(JSON.stringify(ajson));
                    CmdList.push({
                        cmd:ajson,
                        type:"加热中"
                    });
                });
            });
        }
    });
    //页面关闭后关闭websoket;
    window.onunload=function(){
        websoket.close();
    };

    // 点击底部按钮
    $('.chbuBotom li').on('click', function(){
        var thisicon = $(this).find('i');
        var text = $(this).find('p');
        if(text.text() == '电源'){
            thisicon.css({backgroundPosition: '0 -2.46rem'});
            text.css({color: '#4D70FF'});

        }else if(text.text() == '滤芯'){
            thisicon.css({backgroundPosition: '-2.4rem -2.46rem'});
            text.css({color: '#4D70FF'});

        }else if(text.text() == '冲洗'){
            thisicon.css({backgroundPosition: '-9rem -6px'});
            text.css({color: '#4D70FF'});

        }else if(text.text() == '充值'){
            thisicon.css({backgroundPosition: '-4.6rem -2.46rem'});
            text.css({color: '#4D70FF'});

        }else if(text.text() == '我的'){
            thisicon.css({backgroundPosition: '-6.84rem -2.46rem'});
            text.css({color: '#4D70FF'});
        }
    })

}