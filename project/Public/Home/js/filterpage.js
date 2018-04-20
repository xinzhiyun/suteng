$(function(){
	var lvxinArr = ['#lvxinDetail', '#lvxinReset_base' ,'#lvxinBuy'];
	var filterName='';
	var filterNumber=0;
	//页面加载时获取滤芯数据
	var data = JSON.parse($(".filterData").val());//滤芯剩余量数据
	var res = JSON.parse($(".lvxin_data").val()).filterInfo;//滤芯详情数据
	// console.dir(data);
	// console.log(res);

	// 当当前设备为零售模式时候，不显示滤芯购买页面
	if(data){
		deviceId = data.deviceid;
		filtermode = data.filtermode;
		if(data.leasingmode=="0"){
			$("#filterBuy").hide();
			$("#line").css({"width":"50%","marginLeft":"50%"});
			$("#header span").css("width","33.33%");
			$("#content").css("left","-100vw");
			$("#footer div").hide();
			$("#footer span").hide();
			$("#footer button").show();
		}else{
			$("#footer div").show();
			$("#footer span").show();
			$("#footer button").hide();
			$("#line").css({marginLeft: "66.66%"});
		}
	}else{
		filtermode = '';
		deviceId = '';
		$("#footer div").show();
		$("#footer span").show();
		$("#footer button").hide();
		$("#line").css({marginLeft: "66.66%"});
	}
	// 点击顶部tab切换内容
	$(".tabTitle").click(function(){

		// 隐藏其他模块
		for(var i=0; i<lvxinArr.length; i++){
			$(lvxinArr[i]).fadeOut();
		}
		// 显示当前模块
		$(lvxinArr[$(this).attr("index")]).fadeIn();

		if($(this).html()=="滤芯购买"){
			$("#footer").show();
			$("button").hide().siblings().show();
		}else{
			$("#footer").css("border-top","none");
			if($(this).html()=="滤芯详情"){
				$("#footer").hide();
			}else{
				$("#footer").show();
				$("button").show().siblings().hide();
			}
		}
		// 横线移动
		if(data){
			if(data.leasingmode=="0"){
				$("#line").css({marginLeft: $(this).attr("index")*50 + '%'});
			}else{
				$("#line").css({marginLeft: $(this).attr("index")*33.33 + '%'});
			}
		}else{
			$("#line").css({marginLeft: $(this).attr("index")*33.33 + '%'});
		}
		// 内容切换
		$("#content").css({transform: 'translateX(-'+ $(this).attr("index") +'00vw)'});
		
		//滤芯选中状态样式
		$("#lvxinReset").on("click","li",function(){
			$(this).children(".progress").children(".iconfont").removeClass('iconfont icon-kuang1').addClass('iconfont icon-xuanze');
			$(this).siblings().children(".progress").children(".iconfont").removeClass('iconfont icon-xuanze').addClass('iconfont icon-kuang1');
			filterName=$(this).children("p").children().eq(0).text();
			filterNumber=$(this).index();
		})
		
	})
	
	console.log(data);
	
	var filtermode;
	var moHTML = '',lvxinIntroduceHTML='';//html box
	var timelife;//滤芯总时间
	var flowlife;//滤芯总流量
	var reday;//滤芯剩余时间
	var reflow;//滤芯剩余流量
	var redaypercent;//滤芯剩余时间百分比
	var reflowpercent;//滤芯剩余流量百分比
	
	if(res){
		console.log(res);
		for(var i=0; i<res.length; i++){ 
			if(res[i]){
				timelife = (res[i].timelife?res[i].timelife:0);
				flowlife = (res[i].flowlife?res[i].flowlife:0);
				reday = (data['redayfilter'+(i+1)]?data['redayfilter'+(i+1)]:0);
				reflow = (data['reflowfilter'+(i+1)]?data['reflowfilter'+(i+1)]:0);
				redaypercent = (((data['redayfilter'+(i+1)]/res[i].timelife)*100).toFixed(2)>100)?100:(((data['redayfilter'+(i+1)]/res[i].timelife)*100).toFixed(2));
				reflowpercent = (((data['reflowfilter'+(i+1)]/res[i].flowlife)*100).toFixed(0)>100)?100:(((data['reflowfilter'+(i+1)]/res[i].flowlife)*100).toFixed(0));
				if(filtermode == ''){
					return
				}
				// console.log('filtermode: ',filtermode);
			    if(filtermode == 0){//时长
			        // 遍历膜的详情, 不存在则不遍历
			        if(res[i].filtername){
			            moHTML += '<li class="_padding">'+
			                '<p><span class="">'+ res[i].filtername +
			                '</span><span class="_float_right">'+
			                (redaypercent?redaypercent:0)
			                +'%</span></p>'+
			                '<p class="_text_align">'+
			                    '<div class="progress _progress _width">'+
			                       '<div class="iconfont icon-kuang1"><span name='+ 'filter'+i +'></span></div>'+
			                      '<div class="progress-bar progress-bar-striped" role="progressbar" style="width:'+
			                     (redaypercent?redaypercent:0)+
			                      '%" aria-valuenow="10" aria-valuemin="0" aria-valuemax="100"></div>'+
			                    '</div>'+
			                '</p>'+
			            '</li>';
			        }
			        //滤芯简介
			        if(res[i].introduce){
			            lvxinIntroduceHTML += '<div>'+
			                '<h3>'+
			                    res[i].filtername +
			                '</h3>'+
			                '<p>'+
			                    res[i].introduce +
			                '</p>'+
			                '<p>'+
			                    '<span class="_font_weigth">总寿命：'+ timelife +
			                    '天</span>'+
			                    '<span class="_float_right _font_weigth">剩余：'+ reday +
			                    '天</span>'+
			                '</p>'+
			            '</div>';
			        }
			    }else if(filtermode == 1){//流量
			        //流量模式
			         // 遍历滤芯详情, 不存在则不遍历
			        if(res[i].filtername){

			            moHTML += '<li class="_padding">'+
			                '<p><span class="">'+ res[i].filtername +
			                '</span><span class="_float_right">'+
			                (reflowpercent?reflowpercent:0)
			                +'%</span></p>'+
			                '<p class="_text_align">'+
			                    '<div class="progress _progress _width">'+
			                       '<div class="iconfont icon-kuang1"><span name='+ 'filter'+i +'></span></div>'+
			                      '<div class="progress-bar progress-bar-striped" role="progressbar" style="width:'+
			                     (reflowpercent?reflowpercent:0) +
			                      '%" aria-valuenow="10" aria-valuemin="0" aria-valuemax="100"></div>'+
			                    '</div>'+
			                '</p>'+
			            '</li>';
			        }

			        //滤芯简介
			        if(res[i].introduce && data.reflowfilter+[i+1]){
			            lvxinIntroduceHTML += '<div>'+
			                '<h3>'+
			                    res[i].filtername +
			                '</h3>'+
			                '<p>'+
			                    res[i].introduce +
			                '</p>'+
			                '<p>'+
			                    '<span class="_font_weigth">总寿命：'+ flowlife +
			                    'L</span>'+
			                    '<span class="_float_right _font_weigth">剩余：'+ reflow +
			                    'L</span>'+
			                '</p>'+
			            '</div>';
			        }
			    }else if(filtermode == 2){//时常&流量
			        // 遍历膜的详情, 不存在则不遍历
			       if(redaypercent > reflowpercent){//时间剩余比例小则显示时间
			            if(res[i].filtername){
			                moHTML += '<li class="_padding">'+
			                    '<p><span class="">'+ res[i].filtername +
			                    '</span><span class="_float_right">'+
			                    (redaypercent?redaypercent:0) +
			                    '%</span></p>'+
			                    '<p class="_text_align">'+
			                        '<div class="progress _progress _width">'+
			                           '<div class="iconfont icon-kuang1"><span name='+ 'filter'+i +'></span></div>'+
			                          '<div class="progress-bar progress-bar-striped" role="progressbar" style="width:'+
			                         (redaypercent?redaypercent:0) +
			                          '%" aria-valuenow="10" aria-valuemin="0" aria-valuemax="100"></div>'+
			                        '</div>'+
			                    '</p>'+
			                '</li>';
			            }
			            //滤芯简介
			            if(res[i].introduce){
			                lvxinIntroduceHTML += '<div>'+
			                    '<h3>'+
			                        res[i].filtername +
			                    '</h3>'+
			                    '<p>'+
			                        res[i].introduce +
			                    '</p>'+
			                    '<p>'+
			                        '<span class="_font_weigth">总寿命：'+ timelife +
			                        '</span>'+
			                        '天<span class="_float_right _font_weigth">剩余：'+ reday +
			                        '天</span>'+
			                    '</p>'+
			                '</div>';
			            }
			        }else{//流量剩余比例小则显示流量
			            if(res[i].filtername){
			                moHTML += '<li class="_padding">'+
			                    '<p><span class="">'+ res[i].filtername +
			                    '</span><span class="_float_right">'+
			                   	(reflowpercent?reflowpercent:0) +
			                    '%</span></p>'+
			                    '<p class="_text_align">'+
			                        '<div class="progress _progress _width">'+
			                           '<div class="iconfont icon-kuang1"><span name='+ 'filter'+i +'></span></div>'+
			                          '<div class="progress-bar progress-bar-striped" role="progressbar" style="width:'+
			                         (reflowpercent?reflowpercent:0) +
			                          '%" aria-valuenow="10" aria-valuemin="0" aria-valuemax="100"></div>'+
			                        '</div>'+
			                    '</p>'+
			                '</li>';
			            }
			            //滤芯简介
			            if(res[i].introduce){
			                lvxinIntroduceHTML += '<div>'+
			                    '<h3>'+
			                        res[i].filtername +
			                    '</h3>'+
			                    '<p>'+
			                        res[i].introduce +
			                    '</p>'+
			                    '<p>'+
			                        '<span class="_font_weigth">总寿命：'+ flowlife +
			                        '</span>'+
			                        'L<span class="_float_right _font_weigth">剩余：'+ reflow +
			                        'L</span>'+
			                    '</p>'+
			                '</div>';
			            }
			        }
			    }
			}
		}
	}
	$("#lvxinDetailUL").html(moHTML);
	$("#lvxinReset").html(moHTML);
	$("#lvxinIntroduce").html(lvxinIntroduceHTML);
	 //websoket
    var timer=null;
    var identify=0;
	var websoket=new WebSocket("ws://120.27.12.1:6001");
	var PackNum=0;//包数据
	var CmdList=[];//命令队列
	var deviceId;
	if(deviceId){
		websoket.onopen=function(){
			//包数据
			ajson={
				"DeviceID":deviceId,
				"PackType":"login",
				"Vison":0,
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
		websoket.onmessage=function(data){
			var dataList=JSON.parse(data.data);//读取websoket数据，转换为json对象
			var leasingmode = dataList.LeasingMode;
			var moHTML = '',lvxinHTML = '',lvxinIntroduceHTML = '';// 存放膜、滤芯、滤芯简介的遍历的标签
			// console.log(dataList);
			if(dataList.PackType=="Select")//返回查询数据类型数据
			{			
				for(var i=0; i< parseInt(dataList.FilerNum); i++){
					
					timelife = (res[i].timelife?res[i].timelife:0);
					flowlife = (res[i].flowlife?res[i].flowlife:0);
					reday = (dataList['ReDayFilter'+(i+1)]?dataList['ReDayFilter'+(i+1)]:0);
					reflow = (data['reflowfilter'+(i+1)]?data['reflowfilter'+(i+1)]:0);
					redaypercent = ((parseInt(dataList['ReDayFilter'+(i+1)])/res[i].timelife)*100).toFixed(2);
					reflowpercent = ((parseInt(dataList['ReFlowFilter'+(i+1)])/res[i].flowlife)*100).toFixed(2);

					if(parseInt(dataList['ReDayFilter1']) > 0 && parseInt(dataList['ReFlowFilter1']) <= 0 ){//按时间
						// 遍历膜的详情, 不存在则不遍历
						if(res[i].filtername){
							moHTML += '<li class="_padding">'+
											'<p><span class="">'+ res[i].filtername +
											'</span><span class="_float_right">'+
											redaypercent +
											'%</span></p>'+
											'<p class="_text_align">'+
												'<div class="progress _progress _width">'+
												   '<div class="iconfont icon-kuang1"><span name='+ 'filter'+i +'></span></div>'+
												  '<div class="progress-bar progress-bar-striped" role="progressbar" style="width:'+
													redaypercent +
												  '%" aria-valuenow="10" aria-valuemin="0" aria-valuemax="100"></div>'+
												'</div>'+
											'</p>'+
										'</li>';
						}
						
						//滤芯简介
						if(res[i].introduce){
							dataList['ReDayFilter'+(i+1)] = !dataList['ReDayFilter'+(i+1)] ? 0 : dataList['ReDayFilter'+(i+1)];
							lvxinIntroduceHTML += '<div>'+
								'<h3>'+
									res[i].filtername +
								'</h3>'+
								'<p>'+
									res[i].introduce +
								'</p>'+
								'<p>'+
									'<span class="_font_weigth">总寿命：'+ timelife +
									'天</span>'+
									'<span class="_float_right _font_weigth">剩余：'+ parseInt(dataList['ReDayFilter'+(i+1)]) +
									'天</span>'+
								'</p>'+
							'</div>';
						}
					}else if(parseInt(dataList['ReFlowFilter1'])>0 && parseInt(dataList['ReDayFilter1'])<=0){//流量模式
						if(res[i].filtername){// 遍历滤芯详情, 不存在则不遍历
							moHTML += '<li class="_padding">'+
								'<p><span class="">'+ res[i].filtername +
								'</span><span class="_float_right">'+
								reflowpercent +
								'%</span></p>'+
								'<p class="_text_align">'+
									'<div class="progress _progress _width">'+
									   '<div class="iconfont icon-kuang1"><span name='+ 'filter'+i +'></span></div>'+
									  '<div class="progress-bar progress-bar-striped" role="progressbar" style="width:'+
									 reflowpercent +
									  '%" aria-valuenow="10" aria-valuemin="0" aria-valuemax="100"></div>'+
									'</div>'+
								'</p>'+
							'</li>';
						}
						if(res[i].introduce && data.ReFlowFilter+[i+1]){//滤芯简介
							lvxinIntroduceHTML += '<div>'+
								'<h3>'+
									res[i].filtername +
								'</h3>'+
								'<p>'+
									res[i].introduce +
								'</p>'+
								'<p>'+
									'<span class="_font_weigth">总寿命：'+ flowlife +
									'L</span>'+
									'<span class="_float_right _font_weigth">剩余：'+ reflow +
									'L</span>'+
								'</p>'+
							'</div>';
						}	
					}else if(parseInt(dataList['ReFlowFilter1'])>0 && parseInt(dataList['ReDayFilter1'])>0){//按流量&时间
					   if(reflowpercent > redaypercent){//时间剩余比例小则显示时间
							if(res[i].filtername){// 遍历膜的详情, 不存在则不遍历 
								moHTML += '<li class="_padding">'+
									'<p><span class="">'+ res[i].filtername +
									'</span><span class="_float_right">'+
									redaypercent +
									'%</span></p>'+
									'<p class="_text_align">'+
										'<div class="progress _progress _width">'+
										   '<div class="iconfont icon-kuang1"><span name='+ 'filter'+i +'></span></div>'+
										  '<div class="progress-bar progress-bar-striped" role="progressbar" style="width:'+
										 redaypercent +
										  '%" aria-valuenow="10" aria-valuemin="0" aria-valuemax="100"></div>'+
										'</div>'+
									'</p>'+
								'</li>';
							}
							if(res[i].introduce){//滤芯简介
								lvxinIntroduceHTML += '<div>'+
									'<h3>'+
										res[i].filtername +
									'</h3>'+
									'<p>'+
										res[i].introduce +
									'</p>'+
									'<p>'+
										'<span class="_font_weigth">总寿命：'+ timelife +
										'</span>'+
										'天<span class="_float_right _font_weigth">剩余：'+ reday +
										'天</span>'+
									'</p>'+
								'</div>';
							}
						}else{
							//流量剩余比例小则显示流量
							if(res[i].filtername){

								moHTML += '<li class="_padding">'+
									'<p><span class="">'+ res[i].filtername +
									'</span><span class="_float_right">'+
								   reflowpercent +
									'%</span></p>'+
									'<p class="_text_align">'+
										'<div class="progress _progress _width">'+
										   '<div class="iconfont icon-kuang1"><span name='+ 'filter'+i +'></span></div>'+
										  '<div class="progress-bar progress-bar-striped" role="progressbar" style="width:'+
										 reflowpercent +
										  '%" aria-valuenow="10" aria-valuemin="0" aria-valuemax="100"></div>'+
										'</div>'+
									'</p>'+
								'</li>';
							}

							//滤芯简介
							if(res[i].introduce){
								lvxinIntroduceHTML += '<div>'+
									'<h3>'+
										res[i].filtername +
									'</h3>'+
									'<p>'+
										res[i].introduce +
									'</p>'+
									'<p>'+
										'<span class="_font_weigth">总寿命：'+ flowlife +
										'</span>'+
										'L<span class="_float_right _font_weigth">剩余：'+ reflow +
										'L</span>'+
									'</p>'+
								'</div>';
							}
						}
					}

				}

				// 遍历滤芯详情，然后添加到$("#lvxinDetailUL")
				$("#lvxinDetailUL").html(moHTML + lvxinHTML);
				//遍历膜、滤芯简介
				$("#lvxinIntroduce").html(lvxinIntroduceHTML);
				// 遍历滤芯复位，然后添加到$("#lvxinDetailUL")
				$("#lvxinReset").html(moHTML + lvxinHTML);

				// 剩余比例小于100%
				for(var i=0; i<$("._float_right").length; i++){

					parseInt($("._float_right").eq(i).text()) > 100
					? $("._float_right").eq(i).text('100%')
					: parseInt($("._float_right").eq(i).text())
				}
			}
			else if(dataList.PackType=="SetData")//设置数据类型数据
			{
			    // console.log(dataList);
	            identify=0;
				for(var i=0;i<CmdList.length;i++){
					if(CmdList[i].cmd.PackNum==dataList.PackNum)
					{
						CmdList.splice(i,1);
						break;
					}
				}
	            ajson={
	                "DeviceID":deviceId,
	                "PackType":"Select",
	                "Vison":0,
	            };
	            websoket.send(JSON.stringify(ajson));

			}
			
		}	
	}
	//复位按钮操作
	$('._btn').click(function(){
		var _this=$(this).text();
		var ajson;//数据对象
		//判断操作类型
		if(filterName==""){
			layui.use('layer', function(){
			  var layer = layui.layer;
			  layer.msg('请选择要复位的滤芯项');
			});
		} else {
			var tipsText = "确定要"+ _this + "<a>滤芯" + filterName +"</a>吗";
			//弹框提示
			layui.use('layer', function(){
				var layer = layui.layer;
				layer.confirm(tipsText, {icon: 3, title:'温馨提示'}, function(index){
					layer.close(index);
					ajson={
						"DeviceID":deviceId,
						"PackType":"SetData",
						"Vison":0,
						"PackNum":PackNum,
					};
					var type=0;
                    ajson['ReFlowFilter'+(filterNumber+1)]=res[filterNumber].flowlife;
                    ajson['ReDayFilter'+(filterNumber+1)]=res[filterNumber].timelife;
                    ajson['FlowLifeFilter'+(filterNumber+1)]=res[filterNumber].flowlife;
                    ajson['DayLifeFiter'+(filterNumber+1)]=res[filterNumber].timelife;
                    ajson['type']='复位中';
					type=filterNumber;
					//发送数据
                    //console.log(ajson);
					websoket.send(JSON.stringify(ajson));
					CmdList.push({
						cmd:ajson,
						type:type
					});
                    identify=1;
					timer=setTimeout(function(){
                        if(identify==1){
                            // layui.use('layer', function(){
                            //     var layer = layui.layer;
                            //     layer.msg('复位超时！');
                            // });

                            identify=0;
                        }
                    },10000)
				});
			});
		}

	});
    //页面关闭后关闭websoket;
    window.onunload=function(){
        websoket.close();
    };
    






























	
})