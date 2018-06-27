$(function(){
	var lvxinArr = ['#lvxinDetail', '#lvxinReset_base' ,'#lvxinBuy'];
	 //websoket
    var timer = null;
    var identify = 0;
	var websoket = new WebSocket("ws://120.27.12.1:6001");
	var PackNum = 0;	//包数据
	var CmdList = [];	//命令队列
	var deviceid = filterData.deviceid;
	console.log('deviceid: ',deviceid);
	if(deviceid){
		websoket.onopen = function(){
			console.log('121212');
			//包数据
			ajson = {
				"DeviceID": deviceid,
				"PackType": "login",
				"Vison": 0,
			};
			websoket.send(JSON.stringify(ajson));
			setTimeout(function(){
	            ajson.PackType = "Select";
	            websoket.send(JSON.stringify(ajson));
	            setInterval(function(){
	                websoket.send(JSON.stringify(ajson));
	            },10000);
	        },1000);
		}
		//实时接收数据
		websoket.onmessage = function(data){
			console.log('data: ',data);
			var dataList = JSON.parse(data.data);//读取websoket数据，转换为json对象
			if(dataList){
				filter.dataList = [];
				for(var i=0; i<filterInfo.length; i++){
					// 显示用的数据
					filter.dataList.push({
						name: filterInfo[i].filtername,                    // 滤芯名称
						desc: filterInfo[i].introduce,                     // 滤芯描述
						reday: data["redayfilter"+(i+1)],           // 剩余天数
						reflow: data["reflowfilter"+(i+1)],         // 剩余流量
						sumday: data.sumday,                        // 已用天数
						sumflow: data.sumflow                       // 已用流量
					})
				}
			}
			var leasingmode = data.LeasingMode;
			if(data.PackType == "Select")//返回查询数据类型数据
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
			else if(data.PackType == "SetData")//设置数据类型数据
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

    //页面关闭后关闭websoket;
    window.onunload = function(){
        websoket.close();
    };
    






























	
})