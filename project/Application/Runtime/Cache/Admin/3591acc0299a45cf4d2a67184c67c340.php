<?php if (!defined('THINK_PATH')) exit();?> <!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>页面</title>
    <meta name="keywords" content="H+后台主题,后台bootstrap框架,会员中心主题,后台HTML,响应式后台">
    <meta name="description" content="H+是一个完全响应式，基于Bootstrap3最新版本开发的扁平化主题，她采用了主流的左右两栏式布局，使用了Html5+CSS3等现代技术">

    <link rel="shortcut icon" href="favicon.ico"> <link href="/suteng/project/Public/Admin/css/bootstrap.min.css?v=3.3.6" rel="stylesheet">
    <link href="/suteng/project/Public/Admin/css/font-awesome.css?v=4.4.0" rel="stylesheet">
    <link href="/suteng/project/Public/Admin/css/plugins/iCheck/custom.css" rel="stylesheet">
    <link href="/suteng/project/Public/Admin/css/animate.css" rel="stylesheet">
    <link href="/suteng/project/Public/Admin/css/style.css?v=4.1.0" rel="stylesheet">
    
</head>

<body class="gray-bg">
    
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-sm-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>经销商管理 <small>经销商审核</small></h5>
                        <div class="ibox-tools">
                        </div>
                    </div>
                    <div class="ibox-content">

                        <table class="table">
                            <thead>
                                <tr>
                                    <th>序号</th>
                                    <th>姓名</th>
                                    <th>电话</th>
                                    <th>座机</th>
                                    <th>身份证</th>
                                    <th>上级经销商</th>
                                    <th>分配用户名</th>
                                    <th>提交时间</th>
                                    <th>详情</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(empty($data)): ?><tr><td><h3>没有待审核消息</h3></td></tr>
                                <?php else: ?>
                                    <?php if(is_array($data)): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$val): $mod = ($i % 2 );++$i;?><tr>
                                            <td><?php echo ($i); ?></td>
                                            <td><?php echo ($val["name"]); ?></td>
                                            <td><?php echo ($val["phone"]); ?></td>
                                            <td><?php echo ($val["telphone"]); ?></td>
                                            <td><?php echo ($val["idcard"]); ?></td>
                                            <td><?php echo ($parent_user); ?></td>
                                            <td><?php echo ($val["user"]); ?></td>
                                            <td><?php echo (date("Y-m-d H:i:s",$val["addtime"])); ?></td>
                                            <td>
                                                <a onclick="access('<?php echo ($val["id"]); ?>')" href="#">通过</a>
                                                <a onclick="faild('<?php echo ($val["id"]); ?>')" href="#">拒绝</a>
                                            </td>
                                        </tr><?php endforeach; endif; else: echo "" ;endif; endif; ?>
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        function access(id){
            $.ajax({
                url:"reviewed",
                type:"post",
                data:{'id':id},
                success:function(res){
                    alert(res.message)
                    window.location.reload()
                },
                error:function(res){
                    alert(res.message)
                }
            })
        }
    </script>

    <!-- 全局js -->
    <script src="/suteng/project/Public/Admin/js/jquery.min.js?v=2.1.4"></script>
    <script src="/suteng/project/Public/Admin/js/bootstrap.min.js?v=3.3.6"></script>
    <script src="/suteng/project/Public/Admin/js/plugins/metisMenu/jquery.metisMenu.js"></script>
    <script src="/suteng/project/Public/Admin/js/plugins/slimscroll/jquery.slimscroll.min.js"></script>
    <script src="/suteng/project/Public/Admin/js/plugins/layer/layer.min.js"></script>

    <!-- 自定义js -->
    <script src="/suteng/project/Public/Admin/js/hplus.js?v=4.1.0"></script>
    <script type="text/javascript" src="/suteng/project/Public/Admin/js/contabs.js"></script>

    <!-- 第三方插件 -->
    <script src="/suteng/project/Public/Admin/js/plugins/pace/pace.min.js"></script>

    
</body>
</html>