$(function(){
	var lvxinArr = ['#lvxinDetail', '#lvxinReset_base' ,'#lvxinBuy'];
	 //websocket
    var timer = null;
    var identify = 0;
	websocket = new WebSocket("ws://120.78.184.0:6001");
	var PackNum = 0;	//包数据
	var CmdList = [];	//命令队列
	var deviceid = filterData.deviceid;
	console.log('deviceid: ',deviceid);
	if(deviceid){
		websocket.onopen = function(){
			console.log('121212');
			//包数据
			ajson = {
				"DeviceID": deviceid,
				"PackType": "login",
				"Vison": 0,
			};
			websocket.send(JSON.stringify(ajson));
			setTimeout(function(){
	            ajson.PackType = "Select";
	            websocket.send(JSON.stringify(ajson));
	            setInterval(function(){
	                websocket.send(JSON.stringify(ajson));
	            },10000);
	        },1000);
		}
		//实时接收数据
		websocket.onmessage = function(data){
			var dataList = JSON.parse(data.data);//读取websocket数据，转换为json对象
			console.log('websocket_data: ',dataList);
			if(dataList && dataList.ReDayFilter1){
				filter.dataList = [];
				for(var i=0; i<filterInfo.length; i++){
					// 显示用的数据
					filter.dataList.push({
						name: filterInfo[i].filtername,                    // 滤芯名称
						desc: filterInfo[i].introduce,                     // 滤芯描述
						reday: dataList["ReDayFilter"+(i+1)],           // 剩余天数
						reflow: dataList["ReFlowFilter"+(i+1)],         // 剩余流量
						sumday: dataList.sumday,                        // 已用天数
						sumflow: dataList.sumflow                       // 已用流量
					})
				}
				// console.log('filter.dataList :', filter.dataList);
			}
			filter.leasingmode = dataList.LeasingMode;
			if(dataList.PackType == "Select"){	//返回查询数据类型数据
				filter.dataList = [];
				dataList.sumday = dataList.sumday ? dataList.sumday : 1;
				dataList.sumflow = dataList.sumflow ? dataList.sumflow : 1;
				for(var i=0; i<filterInfo.length; i++){
					// 显示用的数据
					filter.dataList.push({
						name: filterInfo[i].filtername,                    // 滤芯名称
						desc: filterInfo[i].introduce,                     // 滤芯描述
						reday: dataList["ReDayFilter"+(i+1)],           // 剩余天数
						reflow: dataList["ReFlowFilter"+(i+1)],         // 剩余流量
						sumday: dataList.sumday,                        // 已用天数
						sumflow: dataList.sumflow                       // 已用流量
					})
				}

			}else if(dataList.PackType == "SetData"){	//设置数据类型数据
			    // console.log(dataList);
	            identify = 0;
				for(var i=0; i<CmdList.length; i++){
					if(CmdList[i].cmd.PackNum==dataList.PackNum){
						CmdList.splice(i,1);
						break;
					}
				}
	            ajson={
	                "DeviceID": deviceid,
	                "PackType": "Select",
	                "Vison": 0
	            };
	            websocket.send(JSON.stringify(ajson));

			}
			
		}
	}

    //页面关闭后关闭websocket;
    window.onunload = function(){
        websocket.close();
    };
    






























	
})