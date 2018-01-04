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
                <div class="ibox-title">
                    <h5>用户组列表 <small>添加用户</small></h5>
                </div>
                <div class="contentRight">
                    <div class="tabbable">
                        <table class="table table-striped table-bordered table-hover table-condensed">
                            <tr>
                                <th width="10%">
                                    搜索用户名：
                                </th>
                                <td>
                                    <form class="form-inline" action="">
                                        <input class="input-medium" type="text" name="username" value="<?php echo ($_GET['username']); ?>">
                                        <input class="btn btn-sm btn-success" type="submit" value="搜索">
                                    </form>
                                </td>
                            </tr>
                        </table>
                        <table class="table table-striped table-bordered table-hover table-condensed">
                            <caption><h3><span style="color: red"><?php echo ($group_name); ?></span>用户组操作</h3></caption>
                            <tr>
                                <th width="10%">
                                    用户名
                                </th>
                                <th>
                                    操作
                                </th>
                            </tr>
                            <?php if(is_array($user_data)): foreach($user_data as $key=>$v): ?><tr>
                                    <th>
                                        <?php echo ($v['user']); ?>
                                    </th>
                                    <td>
                                        <?php if(in_array($v['id'],$uids)): ?>已经是<?php echo ($group_name); ?>小组成员
                                            <?php else: ?>
                                            <a href="<?php echo U('Admin/Rule/add_user_to_group',array('uid'=>$v['id'],'group_id'=>$_GET['group_id'],'username'=>$_GET['username']));?>">
                                                设为<?php echo ($group_name); ?>小组成员
                                            </a><?php endif; ?>
                                    </td>
                                </tr><?php endforeach; endif; ?>
                        </table>
                    </div>
                </div>
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