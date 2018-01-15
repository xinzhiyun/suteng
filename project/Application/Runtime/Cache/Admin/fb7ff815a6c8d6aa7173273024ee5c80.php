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
    
    <link href="/project/suteng/project/Public/Admin/css/plugins/clockpicker/clockpicker.css" rel="stylesheet">

</head>

<body class="gray-bg">
    
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-sm-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                    <h5>工单管理 <small>修改工单</small></h5>
                </div>
                    <div class="ibox-content">
                        <form action="" method="post" class="form-horizontal">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">工单号</label>

                                <div class="col-md-3">
                                    <input type="text" class="form-control" value="<?php echo ($data["number"]); ?>" readonly="">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">工单标题</label>

                                <div class="col-md-3">
                                    <input type="text" name="title" class="form-control" value="<?php echo ($data["title"]); ?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">维护类型</label>
                                <div class="radio i-checks">
                                    <label class="">
                                        <div class="iradio_square-green <?php echo ($data['type'] == 0?'checked':''); ?>" style="position: relative;"><input type="radio" <?php echo ($data['type'] == 0?'checked':''); ?> name="type" value="0" style="position: absolute; opacity: 0;"><ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; background: rgb(255, 255, 255); border: 0px; opacity: 0;"></ins></div> <i></i> 安装
                                    </label>
                                    <label class="">                                       
                                        <div class="iradio_square-green <?php echo ($data['type'] == 1?'checked':''); ?>" style="position: relative;"><input type="radio" name="type" <?php echo ($data['type'] == 1?'checked':''); ?> value="1" style="position: absolute; opacity: 0;"><ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; background: rgb(255, 255, 255); border: 0px; opacity: 0;"></ins></div> <i></i> 维修
                                    </label>
                                    <label class="">
                                        <div class="iradio_square-green <?php echo ($data['type'] == 2?'checked':''); ?>" style="position: relative;"><input type="radio" name="type" <?php echo ($data['type'] == 2?'checked':''); ?> value="2" style="position: absolute; opacity: 0;"><ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; background: rgb(255, 255, 255); border: 0px; opacity: 0;"></ins></div> <i></i> 维护
                                    </label>
                                </div>                  
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">工作内容</label>

                                <div class="col-md-3">
                                    <textarea name="content" cols="30" rows="5" class="form-control" ></textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">地址</label>

                                <div class="col-md-3">
                                    <input type="text" name="address" class="form-control" value="<?php echo ($data["address"]); ?>">
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <div class="col-sm-4 col-sm-offset-2">
                                    <button class="btn btn-primary" type="submit">确认修改</button>
                                    <button class="btn btn-white" type="reset">重置</button>
                                </div>
                            </div>
                        </form>
                    </div>
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

    


    <!-- Data picker -->
    <script src="/project/suteng/project/Public/Admin/js/plugins/datapicker/bootstrap-datepicker.js"></script>

    <!-- Image cropper -->
    <script src="/project/suteng/project/Public/Admin/js/plugins/cropper/cropper.min.js"></script>

    <script src="/project/suteng/project/Public/Admin/js/demo/form-advanced-demo.js"></script>
    <script type="text/javascript">
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
    </script>

</body>
</html>