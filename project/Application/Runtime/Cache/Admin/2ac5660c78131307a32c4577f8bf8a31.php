<?php if (!defined('THINK_PATH')) exit();?> <!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>页面</title>
    <meta name="keywords" content="H+后台主题,后台bootstrap框架,会员中心主题,后台HTML,响应式后台">
    <meta name="description" content="H+是一个完全响应式，基于Bootstrap3最新版本开发的扁平化主题，她采用了主流的左右两栏式布局，使用了Html5+CSS3等现代技术">

    <link rel="shortcut icon" href="favicon.ico"> <link href="/project/suteng/project/Public/Admin/css/bootstrap.min.css?v=3.3.6" rel="stylesheet">
    <link href="/project/suteng/project/Public/Admin/css/font-awesome.css?v=4.4.0" rel="stylesheet">
    <link href="/project/suteng/project/Public/Admin/css/plugins/iCheck/custom.css" rel="stylesheet">
    <link href="/project/suteng/project/Public/Admin/css/animate.css" rel="stylesheet">
    <link href="/project/suteng/project/Public/Admin/css/style.css?v=4.1.0" rel="stylesheet">
    
    <style type="text/css">
       .ibox-content table{
           
            table-layout:fixed;/* 只有定义了表格的布局算法为fixed，下面td的定义才能起作用。 */
        }
       .ibox-content td{
            width:100%;
            word-break:keep-all;/* 不换行 */
            white-space:nowrap;/* 不换行 */
            overflow:hidden;/* 内容超出宽度时隐藏超出部分的内容 */
            text-overflow:ellipsis;/* 当对象内文本溢出时显示省略标记(...) ；需与overflow:hidden;一起使用。*/
        }
    </style>

</head>

<body class="gray-bg">
    
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-sm-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>工单管理 <small>工单列表</small></h5>
                        <div class="ibox-tools">
                            <a href="javascript:;" onclick="add()" class="close-link">
                                <i class="fa fa-user-plus"></i>
                                添加工单
                            </a>
                        </div>
                    </div>
                    <div class="ibox-content">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>工单编号</th>
                                    <th>主题</th>
                                    <th>维护类型</th>
                                    <th>工作内容</th>
                                    <th>地址</th>
                                    <th>处理结果</th>
                                    <th>创建时间</th>
                                    <th>处理时间</th>
                                    <th>操作</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(empty($pageList["data"])): ?><tr><td><h3>暂无工单.....</h3></td></tr>
                                <?php else: ?>
                                    <?php if(is_array($pageList["data"])): $i = 0; $__LIST__ = $pageList["data"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$val): $mod = ($i % 2 );++$i;?><tr>
                                            <td><?php echo ($val["number"]); ?></td>
                                            <td><?php echo ($val["title"]); ?></td>
                                            <td>
                                                <?php switch($val["type"]): case "0": ?>安装<?php break;?>
                                                    <?php case "1": ?>维修<?php break;?>
                                                    <?php case "2": ?>维护<?php break;?>
                                                    <?php default: endswitch;?>
                                            </td>
                                            <td><a class="" href="javascript:;" onclick="details(<?php echo ($val["id"]); ?>)"><?php echo ($val["content"]); ?></a></td>
                                            <td style=""><?php echo ($val["address"]); ?></td>
                                            <td>
                                                <?php switch($val["result"]): case "0": ?>未处理<?php break;?>
                                                    <?php case "1": ?>正在处理<?php break;?>
                                                    <?php case "2": ?>已处理<?php break;?>
                                                    <?php default: endswitch;?>
                                            </td>
                                            <td><?php echo (date('Y-m-d H:i:s',$val["create_at"])); ?></td>
                                            <td><?php echo (date('Y-m-d H:i:s',$val['time'] ? $val["time"] : '')); ?></td>
                                            <td><?php if(($val["result"]) < "2"): ?><a class="btn btn-outline btn-success" data-id="<?php echo ($val["id"]); ?>" data-number="<?php echo ($val["number"]); ?>" data-title="<?php echo ($val["title"]); ?>" data-type="<?php echo ($val["type"]); ?>" data-content="<?php echo ($val["content"]); ?>" data-result="<?php echo ($val['result'] + 1); ?>" href="javascript:;" onclick="edit(this,<?php echo ($val["id"]); ?>)"><?php switch($val["result"]): case "0": ?>委派<?php break;?>
                                                    <?php case "1": ?>处理<?php break;?>
                                                    <?php default: endswitch;?></a><?php endif; ?></td>
                                        </tr><?php endforeach; endif; else: echo "" ;endif; endif; ?>
                            </tbody>
                        </table>
                        <nav aria-label="Page navigation">
                            <ul class="pagination">
                                <?php echo (bootstrap_page_style($pageList["page"])); ?>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- 弹框信息 -->
            <!-- 新增工单开始 -->
            <div class="modal inmodal" id="st-add" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content animated bounceInRight">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                                &times;
                            </button>
                            <h4 class="modal-title" id="myModalLabel">
                                新增工单
                            </h4>
                        </div>
                        <div class="modal-body">
                            <form id="bjy-form1" class="form-inline" action="<?php echo U('Admin/work/add');?>"
                            method="post">
                                <div class="errmsg" style="text-align: center;color: red;">
                                    msgerror
                                </div>
                                <table class="table table-striped table-bordered table-hover table-condensed">                               
                                    <tr>
                                        <th width="16%">
                                            工单标题：
                                        </th>
                                        <td>
                                            <input class="input-medium" id="add_title" type="text" name="title" class="form-control" value="">
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>
                                            维护类型：
                                        </th>
                                        <td>
                                            <div class="radio i-checks">
                                                <label class="">
                                                    <div class="iradio_square-green checked" style="position: relative;"><input type="radio" checked="" name="add_type" value="0" style="position: absolute; opacity: 0;"><ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; background: rgb(255, 255, 255); border: 0px; opacity: 0;"></ins></div> <i></i> 安装
                                                </label>
                                                <label class="">                                       
                                                    <div class="iradio_square-green" style="position: relative;"><input type="radio" name="add_type" value="1" style="position: absolute; opacity: 0;"><ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; background: rgb(255, 255, 255); border: 0px; opacity: 0;"></ins></div> <i></i> 维修
                                                </label>
                                                <label class="">
                                                    <div class="iradio_square-green" style="position: relative;"><input type="radio" name="add_type" value="2" style="position: absolute; opacity: 0;"><ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; background: rgb(255, 255, 255); border: 0px; opacity: 0;"></ins></div> <i></i> 维护
                                                </label>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>
                                            工单内容：
                                        </th>
                                        <td>
                                            <textarea id="add_content" name="content" cols="30" rows="5" class="input-medium" ></textarea>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>
                                            工单地址：
                                        </th>
                                        <td>
                                          <div data-toggle="distpicker">
                                            <div class="form-group">
                                                 <select class="input-medium" name="province" id="province" data-province="---- 选择省 ----"></select>
                                                  <select class="input-medium" name="city" id="city" data-city="---- 选择市 ----"></select>
                                                  <select class="input-medium" name="district" id="district" data-district="---- 选择区 ----"></select>
                                            </div>
                                          </div>
                                        </td>
                                    </tr>                                     
                                    <tr>
                                        <th>
                                        </th>
                                        <td>
                                            <input id="add_submit" class="btn btn-success" type="button" value="确认">
                                        </td>
                                    </tr>
                                </table>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- 新增工单结束 -->
            <!-- 修改工单开始 -->
            <div class="modal inmodal" id="st-edit" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content animated bounceInRight">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                                &times;
                            </button>
                            <h4 class="modal-title" id="edit-modal-title">
                                修改工单
                            </h4>
                        </div>
                        <div class="modal-body">
                            <div class="edit_errmsg" style="text-align: center;color: red;">
                                    msgerror
                                </div>
                            <form id="bjy-form2" class="form-inline" action=""
                            method="post">
                                <input class="input-medium" type="text" id="edit_id" name="id" class="form-control" value="" readonly="" hidden="">
                                <table class="table table-striped table-bordered table-hover table-condensed">
                                    <tr>
                                        <th width="16%">
                                            工单号：
                                        </th>
                                        <td>
                                            <p id="number"></p>                                            
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>
                                            工单标题：
                                        </th>
                                        <td>
                                            <p id="title"></p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>
                                            委派人：
                                        </th>
                                        <td>
                                            <input class="input-medium" type="text" name="name" id="edit_name" class="form-control" value="">
                                        </td>
                                    </tr>
                                     <tr>
                                        <th>
                                            电话：
                                        </th>
                                        <td>
                                            <input class="input-medium" type="text" name="phone" id="edit_phone" class="form-control" value="">
                                        </td>
                                    </tr>
                                            <input class="input-medium" type="text" name="result" id="edit_result" class="form-control" value="" hidden="">
                                    <tr>
                                        <th>
                                        </th>
                                        <td>
                                            <input id="edit_submit" class="btn btn-success" type="button" value="确认">
                                        </td>
                                    </tr>
                                </table>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- 修改工单结束 -->
            <!-- 查看详情开始 -->
            <div class="modal inmodal bs-example-modal-lg" id="st-details" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content animated bounceInRight">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                                &times;
                            </button>
                            <h4 class="modal-title" id="myModalLabel">
                                工单详情
                            </h4>
                        </div>
                        <div class="modal-body">
                            <table class="table table-striped table-bordered table-hover table-condensed">
                                <tr>
                                    <td width="25%" style="font-weight: bold;">工单编号</td>
                                    <td id="details_number"></td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold;">主题</td>
                                    <td id="details_title"></td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold;">提交人</td>
                                    <td id="details_name"></td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold;">提交人电话</td>
                                    <td id="details_phone"></td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold;">处理人</td>
                                    <td id="details_dw_name"></td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold;">处理人电话</td>
                                    <td id="details_dw_phone"></td>
                                </tr><tr>
                                    <td style="font-weight: bold;">工单类型</td>
                                    <td id="details_type"></td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold;">维护内容</td>
                                    <td id="details_content"></td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold;">处理结果</td>
                                    <td id="details_result"></td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold;">提交时间</td>
                                    <td id="details_create_at"></td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold;">处理时间</td>
                                    <td id="details_time"></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

    <!-- 全局js -->
    <script src="/project/suteng/project/Public/Admin/js/jquery.min.js?v=2.1.4"></script>
    <script src="/project/suteng/project/Public/Admin/js/bootstrap.min.js?v=3.3.6"></script>
    <script src="/project/suteng/project/Public/Admin/js/plugins/metisMenu/jquery.metisMenu.js"></script>
    <script src="/project/suteng/project/Public/Admin/js/plugins/slimscroll/jquery.slimscroll.min.js"></script>
    <script src="/project/suteng/project/Public/Admin/js/plugins/layer/layer.min.js"></script>

    <!-- 自定义js -->
    <script src="/project/suteng/project/Public/Admin/js/hplus.js?v=4.1.0"></script>
    <script type="text/javascript" src="/project/suteng/project/Public/Admin/js/contabs.js"></script>

    <!-- 第三方插件 -->
    <script src="/project/suteng/project/Public/Admin/js/plugins/pace/pace.min.js"></script>

    
    <script src="/project/suteng/project/Public/Admin/js/Bootstrap_shengshiqu/js/distpicker.data.js"></script>
    <script src="/project/suteng/project/Public/Admin/js/Bootstrap_shengshiqu/js/distpicker.js"></script>  
    <script src="/project/suteng/project/Public/Admin/js/plugins/validate/jquery.validate.min.js"></script>
    <script type="text/javascript">
         // 添加工单

    function add() {
        $('.errmsg').text('');               
        $('#st-add').modal('show');        
    }
        //     $('#add_submit').click(function(){
    $('#add_submit').click(function(){
        var data = {
                'title' : $('#add_title').val(),
                'type' : $("input[name='add_type']:checked").val(),
                'address' : $('#province').val() + $('#city').val() + $('#district').val(),
                'content' : $('#add_content').val()
            }
        $.ajax({
            type : 'POST',
            url : "<?php echo U('Admin/work/add');?>",
            data : data,
            success : function(msg){
                if (msg.code == 200) {
                    $('#st-add').modal('hide');
                    window.location.href="<?php echo U('Admin/work/index');?>?p=";
                }
                if (msg.code == 400) {
                    text = '';
                    $.each(msg.msg, function(i, n){
                      text = text + '*' + n + '</br>';                     
                    });
                    $('.errmsg').html(text);
                    console.log(msg);
                }               
            }
        });
    });
        //         console.log(formdata);

        //     // $.ajax({
        //     //    type: "POST",
        //     //    url: <?php echo U('Admin/work/test');?>,
        //     //    data: formdata,
        //     //    success: function(msg){
        //     //         console.log(msg);
        //     //    }
        //     // });
        // });
    function edit(obj,id){
        var id      = $(obj).data('id');
            number  = $(obj).data('number');
            title   = $(obj).data('title');
            result    = $(obj).data('result');
            p = "<?php echo I('get.p');?>";
            $("input[name='id']").val(id);
            $('#number').html(number);
            $("#title").html(title); 
            $('#edit_result').val(result);

            if (result == 2) {
                $('#edit_name').parent().parent().hide();
                $('#edit_phone').parent().parent().hide();
                $('#edit_submit').val('完成处理');
                $('#edit-modal-title').text('确认工单');               
            } else {
                $('#edit_name').parent().parent().show();
                $('#edit_phone').parent().parent().show();
                $('#edit_submit').val('委派');
                $('#edit-modal-title').text('委派工单');
            }

            // 表单验证

            // <?php echo U('Admin/work/edit_work','p='.I('get.p'));?>

            $('.edit_errmsg').text('');
            $('#st-edit').modal('show');

    }
    $('#edit_submit').click(function(){        
        var editdata = {
                'id' : $('#edit_id').val(),
                'result' : $('#edit_result').val()
            }
            if (result === 1) {
                editdata.name = $('#edit_name').val();
                editdata.phone = $('#edit_phone').val();
            }
            // console.log(editdata);
        $.ajax({
           type: "POST",
           url: "<?php echo U('Admin/work/edit');?>",
           data: editdata,
           success: function(msg){
                if (msg.code == 200) {
                    $('#st-edit').modal('hide');
                    window.location.href="<?php echo U('Admin/work/index');?>?p="+p;
                }
                if (msg.code == 400) {
                    text = '';
                    $.each(msg.msg, function(i, n){
                      text = text + '*' + n + '</br>';                     
                    });
                    $('.edit_errmsg').html(text);
                }
                console.log(msg);
           }
        });
    });
    // 维护类型单选框处理
    $('.i-checks > label').hover(
          function () {
            $(this).children('div').addClass("hover");
          },
          function () {
            $(this).children('div').removeClass("hover");
          }
        );
    $('.i-checks > label').click( function () {
        $(this).children('div').addClass("checked").parent().siblings().children('div').removeClass("checked");
        $(this).children('div > input').attr("checked").parent().siblings().children('div > input').removeAttr("checked");
    })

    
    function details(id) {  
        $('#st-details').modal('show');    
        $.ajax({
           type: "GET",
           url: "<?php echo U('Admin/Work/getWork');?>",
           data: "id=" + id,
           success: function(msg){
                if (msg.code == 10) {
                    msg.data.name?name=msg.data.name:name="";
                    msg.data.phone?phone=msg.data.phone:phone="";
                    msg.data.time?time=msg.data.time:time="";
                    // console.log(msg.data)
                    $('#details_number').text(msg.data.number);
                    $('#details_title').text(msg.data.title);                   
                    $('#details_name').text(msg.data.Vendors.name);
                    $('#details_phone').text(msg.data.Vendors.phone);
                    $('#details_dw_name').text(name);
                    $('#details_dw_phone').text(phone);
                    $('#details_type').text(msg.data.type);
                    $('#details_content').text(msg.data.content);
                    $('#details_result').text(msg.data.result);
                    $('#details_create_at').text(msg.data.create_at);
                    $('#details_time').text(time);                    
                }
           }
        });
    }

    // 分页处理
    $(".page").html();
    pageHtml = $(".page").html();
    $('.page').remove();
    $('ul.pagination').html(pageHtml);


    </script>

</body>
</html>