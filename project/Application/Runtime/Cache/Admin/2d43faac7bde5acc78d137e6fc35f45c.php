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
        <div class="row-fluid fl" id="main">
            <div class="tableBox">
                <div class="ibox-title">
                    <h5>用户组 <small>用户组详情</small></h5>
                    <div class="ibox-tools">
                        <a href="<?php echo U('Admin/Rule/check_user', array('group_id'=>$group_data['group_id']));?>" >
                            <i class="fa fa-user-plus"></i>
                            添加成员
                        </a>
                    </div>
                </div>
                <div class="ibox-content">
                    <table class="table table-bordered table-hover">
                        <caption><h3 class="ruleTitle"><span style="color:red"><?php echo ($name['title']); ?></span>的成员分配情况</h3></caption>
                        <thead>
                            <tr>
                                <th width="10%">
                                    用户ID
                                </th>
                                <th>
                                    用户名
                                </th>
                                <th>
                                    操作
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(is_array($data)): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "$empty" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?><tr>
                                    <th>
                                        <?php echo ($v['id']); ?>
                                    </th>
                                    <td>
                                        <?php echo ($v['user']); ?>
                                    </td>
                                    <td>
                                        <a href="javascript:if(confirm('确定删除？'))location='<?php echo U('Admin/Rule/delete_user_from_group',array('uid'=>$v['id']));?>'">
                                            删除
                                        </a>
                                    </td>
                                </tr><?php endforeach; endif; else: echo "$empty" ;endif; ?>
                        </tbody>
                    </table>
                </div>
                <script>
                    $('.pagination ul a').unwrap('div').wrap('<li></li>');
                    $('.pagination ul span').wrap('<li class="active"></li>')
                </script>
            </div>
        </div>
    </div>

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