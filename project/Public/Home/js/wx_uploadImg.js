

function takePicture(nums) {  
	
 	wx.chooseImage({  
     	count: 1,  
     	needResult: 1,  
     	sizeType: ['original', 'compressed'], // 可以指定是原图还是压缩图，默认二者都有  
     	sourceType: ['album', 'camera'], // 可以指定来源是相册还是相机，默认二者都有  
     	success: function (data) {                  
         	localIds = data.localIds[0];   // 返回选定照片的本地ID列表，localId可以作为img标签的src属性显示图片        
         	                  
         	var num1 = nums;
         	wxuploadImage(localIds,num1);
         	wxgetLocalImgData(localIds,num1);
     	},  
     	fail: function (res) {  
     		console.log('失败：',JSON.stringify(res));
     	}    
 	});  
 
} 
function wxuploadImage(e,num) {

  wx.uploadImage({  
      localId: e, // 需要上传的图片的本地ID，由chooseImage接口获得  
      isShowProgressTips: 1, // 默认为1，显示进度提示  
      success: function (res) {                                        
          mediaId = res.serverId;                     
          $('input[name="pic"]').val(mediaId);
      },  
      fail: function (error) {  
          picPath = '';  
          localIds = '';  
      }          
  });
}
var $span = $("<span></span>");
var $span1 = $("<span>X</span>");
var $img = $('<img src="" alt="" />');
function wxgetLocalImgData(e,num){

	$img[0].width = "100%";
	$img[0].height = "90%";

    if(window.__wxjs_is_wkwebview){
	    wx.getLocalImgData({
	        localId: e, // 图片的localID
	        success: function (res) {                    
            var localData = res.localData; // localData是图片的base64数据，可以用img标签显示
            localData = localData.replace('jgp', 'jpeg');//iOS 系统里面得到的数据，类型为 image/jgp,因此需要替换一下                                            
			      $img[0].src = localData;

	        },
	        fail:function(res){
	          	alert("显示失败");
	        }
	    });
    }else{
        $img[0].src = e;
        
    }

	$span1.addClass("delPic");
	$span1.css({zIndex: '999'});
	$span.append($span1);
	$span.append($img[0]);
	$('#picShow').append($span);
	
}