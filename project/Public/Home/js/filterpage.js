$(function(){
	var lvxinArr = ['#lvxinDetail', '#lvxinReset_base' ,'#lvxinBuy'];
	 //websocket
    var timer = null;
    var identify = 0;
	websocket = new WebSocket("ws://120.27.12.1:6001");
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
			console.log('websocket_data: ',data);
			var dataList = JSON.parse(data.data);//读取websocket数据，转换为json对象
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
			if(data.PackType == "Select"){	//返回查询数据类型数据
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

			}else if(data.PackType == "SetData"){	//设置数据类型数据
			    // console.log(dataList);
	            identify = 0;
				for(var i=0; i<CmdList.length; i++){
					if(CmdList[i].cmd.PackNum==dataList.PackNum){
						CmdList.splice(i,1);
						break;
					}
				}
	            ajson={
	                "DeviceID": deviceId,
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