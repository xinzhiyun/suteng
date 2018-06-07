$(function(){
    var device_now = sessionStorage.getItem("device_now");
    $(".device_code").text(device_now);
    // 选择日期
    $("#currentdate").click(function(){

      //选择服务日期，
      laydate.render({
        elem: '#currentdate', //指定元素
        show: true,
        format: 'yyyy年MM月dd日',
        btns: ['clear', 'confirm'],
        min: 1
      });

    })
    var pic = []; // 上传的图片
    // 点击上传图片
    $(".file_upload").click(function(){
      var len = $("#picShow>span").length;
      if(len == 3){
        layuiHint("最多三张图片！");
        return
      }
      // 浏览上传图片
      wxuploadimg(function(res){
        console.log('res: ',res);

        var $span = $("<span></span>");
        var $span1 = $("<span>X</span>");
        var $img = $('<img src="" alt="" />');

        $img[0].width = "100%";
        $img[0].height = "90%";
        $img[0].src = res['src'];
        
        $span1.addClass("delPic");
        $span1.attr("time", new Date().getTime());
        $span1.css({zIndex: '999'});
        $span.append($span1);
        $span.append($img[0]);

        // 显示图片
        $('#picShow').append($span);
        // 待发送给后台的图片id
        // $('input[name="pic"]').val(res.media_Id);
        pic.push({
          pic: res.media_Id,
          time: new Date().getTime()
        })
      });
      console.log('pic: ',pic);
    })
    var formData = new FormData($('#form')[0]);
    // 删除图片
    $("#picShow").on("click", ".delPic", function(){
      // console.log($(this).parent());
      $(this).parent().remove();
      var time = $(this).attr('time');
      for(var i=0; i<pic.length; i++){
        if(pic[i].time == time){
          pic.splice(i,1);
        }
      }
    })
    /*
      点击'提交'
     */
    $(".btn").on('touchend', function(){
      /*
          date : 日期,
          device_code : 设备id,
          reason : 报修原因,
          suggestTxt : 建议,
          truename : 姓名,
          phone : 电话,
          address : 报修地址
       */
      var nameReg = /^([a-zA-Z0-9_\u4e00-\u9fa5]){2,30}$/;
      var phoneReg = /^1[3|4|5|8][0-9]\d{4,8}$/;
      var addressReg = /^(?=.*?[\u4E00-\u9FA5])[\dA-Za-z\u4E00-\u9FA5]{6,}/;
      var date = $(".currentdate").val().replace(/[年月]/g,'-').replace(/[日]/,'');
      var device_code = $(".device_code").text();
      var reason = $(".reason>option:selected");
      var content = $("#suggestTxt").val();
      var truename = $("#Lname").val();
      var phone = $("#Ltel").val();
      var address = $("#Laddr").val();
      var filePic = $('#picShow>span').length;

      console.log('device_code:', device_code + '\n' +
       'date:',date + '\n' + 'reason:', reason[0].innerText + '\n' + 
       'content:', content + '\n' + 'truename:', truename + '\n' + 
       'phone:', phone + '\n' + 'address:', address
       );

      //清空，防止多次点击提交
      formData.delete('device_code');
      formData.delete('date');
      formData.delete('reason');
      formData.delete('content');
      formData.delete('truename');
      formData.delete('phone');
      formData.delete('address');
      formData.delete('pic');

      console.group();

      //重新添加
      formData.append('device_code', device_code);
      formData.append('date', date);
      formData.append('reason', reason[0].innerText);
      formData.append('content', content);
      formData.append('truename', truename);
      formData.append('phone', phone);
      formData.append('address', address);
      formData.append('pic', JSON.stringify(pic));

      console.log(formData.getAll('pic') )
      // console.log(formData.getAll('date'))
      // console.log(formData.getAll('reason'))
      // console.log(formData.getAll('content'))
      // console.log(formData.getAll('truename'))
      // console.log(formData.getAll('phone'))
      // console.log(formData.getAll('address'))

      console.groupEnd();
      if($('.device_code')[0].selectedIndex == 0){
          console.log('请选择保修的设备！');
          layuiHint('请选择保修的设备！');
          return
          
      }
      if(!date){
          console.log('请选择日期！');
          layuiHint('请选择日期！');
          return

      }
      if($('.reason')[0].selectedIndex == 0){
          console.log('请选择报修原因！');
          layuiHint('请选择报修原因！');
          return
          
      }
      if(!content){
          console.log('希望您提供宝贵的建议！');
          layuiHint('希望您提供宝贵的建议！');
          return
          
      }
      if(!filePic){
          console.log('请上传报修图片！');
          layuiHint('请上传报修图片！');
          return
          
      }
      if(!nameReg.test(truename)){
          console.log('请输入正确格式的姓名！');
          layuiHint('请输入正确格式的姓名！');
          return
          
      }
      if(!phoneReg.test(phone)){
          console.log('请输入正确格式的手机号码！');
          layuiHint('请输入正确格式的手机号码！');
          return
          
      }
      if(!addressReg.test(address)){
          console.log('请输入正确格式的地址！');
          layuiHint('请输入详细地址含: 省市区县路和号');
          return
          
      }

      /*
         发送到后台
       */
      $.ajax({
          url: getURL('Home', 'Repair/add'),
          type: 'post',
          async: false,
          data: formData,
          cache: false,
          processData: false,
          contentType: false,
          success: function(res){
            $(".btn").off('touchend');    // 解绑事件
            console.log('请求成功！', res);
            if(res.code == 200){
              layuiHint('报修成功，请稍候将有专人处理！');
              // 将当前历史记录替换为水机首页
              history.pushState({}, null, getURL('Home', 'Index/index'));
              setTimeout(function(){
                location.href = getURL('Home', 'VipCenter/minepack');
              },900);

            }

          },
          error: function(res){
            console.log('请求失败！', res);
            layuiHint('系统遇到问题，请稍后重试！');

          }
      })

    });
})