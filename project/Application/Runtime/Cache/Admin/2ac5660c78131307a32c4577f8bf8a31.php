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
    
</head>

<body class="gray-bg">
    
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-sm-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>工单管理 <small>工单列表</small></h5>
                        <div class="ibox-tools">
                            <a href="vendor_add" class="close-link">
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
                                    <th>处理人</th>
                                    <th>处理人电话</th>
                                    <th>维护类型</th>
                                    <th>工作内容</th>
                                    <th>地址</th>
                                    <th>处理结果</th>
                                    <th>处理时间</th>
                                    <th>操作</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(empty($pageList["data"])): ?><tr><td><h3>暂无工单.....</h3></td></tr>
                                <?php else: ?>
                                    <?php if(is_array($pageList["data"])): $i = 0; $__LIST__ = $pageList["data"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$val): $mod = ($i % 2 );++$i;?><tr>
                                            <td><?php echo ($val["number"]); ?></td>
                                            <td><?php echo ($val["name"]); ?></td>
                                            <td><?php echo ($val["phone"]); ?></td>
                                            <td><?php echo ($val["type"]); ?></td>
                                            <td><?php echo ($val["content"]); ?></td>
                                            <td><?php echo ($val["address"]); ?></td>
                                            <td><?php echo ($val["result"]); ?></td>
                                            <td><?php echo (date('Y-m-d H:i:s',$val["time"])); ?></td>
                                            <td>
                                                <?php switch($val["result"]): case "0": ?><a href="/project/suteng/project/index.php/Admin/Work/edit/id/<?php echo ($val["id"]); ?>/result/1">正在处理</a><?php break;?>
                                                    <?php case "1": ?><a href="/project/suteng/project/index.php/Admin/Work/edit/id/<?php echo ($val["id"]); ?>/result/2">已处理</a><?php break;?>
                                                    <?php default: endswitch;?>
                                            </td>
                                        </tr><?php endforeach; endif; else: echo "" ;endif; endif; ?>
                            </tbody>
                        </table>
                        <div class="pagination">
                            <ul>
                                <?php echo ($pageList["page"]); ?>
                            </ul>
                        </div>
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

    
</body>
</html>