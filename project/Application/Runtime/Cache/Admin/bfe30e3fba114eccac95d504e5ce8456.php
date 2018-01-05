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
                        <h5>权限管理 <small>权限组</small></h5>
                        <div class="ibox-tools">
                            <i class="fa fa-user-plus"></i>
                            <a href="javascript:;" onclick="add()">添加用户组</a>
                        </div>
                    </div>
                    <div class="ibox-content">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>
                                        用户组名
                                    </th>
                                    <th>
                                        操作
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(is_array($data)): foreach($data as $key=>$v): ?><tr>
                                        <td>
                                            <?php echo ($v['title']); ?>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a class="btn btn-outline btn-success" href="javascript:;" ruleId="<?php echo ($v['id']); ?>" ruleTitle="<?php echo ($v['title']); ?>" onclick="edit(this)">
                                                    修改
                                                </a>
                                                <a class="btn btn-outline btn-success deleteBtn" ruleId="<?php echo ($v['id']); ?>" href="javascript:;">
                                                    删除
                                                </a>
                                                <a class="btn btn-outline btn-success" href="<?php echo U('Admin/Rule/rule_group',array('id'=>$v['id']));?>">
                                                    分配权限
                                                </a>
                                                <a class="btn btn-outline btn-success" href="<?php echo U('Admin/Rule/group_list',array('group_id'=>$v['id']));?>">
                                                    成员列表
                                                </a>
                                            </div>
                                        </td>
                                    </tr><?php endforeach; endif; ?>
                            </tbody>
                        </table>
                    </div>
                    
                </div>
                <!-- footer part -->
                
            </div>
        </div>
    </div>
    <!-- 弹框信息 -->
    <div class="modal fade" id="bjy-add" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content animated bounceInRight">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                        &times;
                    </button>
                    <h4 class="modal-title" id="myModalLabel">
                        添加用户组
                    </h4>
                </div>
                <div class="modal-body">
                    <form id="bjy-form" class="form-inline" action="<?php echo U('Admin/Rule/add_group');?>"
                    method="post">
                        <table class="table table-striped table-bordered table-hover table-condensed">
                            <tr>
                                <th width="15%">
                                    用户组名：
                                </th>
                                <td>
                                    <input class="input-medium" type="text" name="title">
                                </td>
                            </tr>
                            <tr>
                                <th>
                                </th>
                                <td>
                                    <input class="btn btn-success" type="submit" value="添加">
                                </td>
                            </tr>
                        </table>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="bjy-edit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content animated bounceInRight">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                        &times;
                    </button>
                    <h4 class="modal-title" id="myModalLabel">
                        修改规则
                    </h4>
                </div>
                <div class="modal-body">
                    <form id="bjy-form" class="form-inline" action="<?php echo U('Admin/Rule/edit_group');?>"
                    method="post">
                        <input type="hidden" name="id">
                        <table class="table table-striped table-bordered table-hover table-condensed">
                            <tr>
                                <th width="12%">
                                    规则名：
                                </th>
                                <td>
                                    <input class="input-medium" type="text" name="title">
                                </td>
                            </tr>
                            <tr>
                                <th>
                                </th>
                                <td>
                                    <input class="btn btn-success" type="submit" value="修改">
                                </td>
                            </tr>
                        </table>
                    </form>
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

    
    <script src="/suteng/project/Public/Admin/layui/layui.js"></script>
    <script>
        // 添加菜单
        function add() {
            $("input[name='title']").val('');
            $('#bjy-add').modal('show');
        }

        // 修改菜单
        function edit(obj) {
            var ruleId = $(obj).attr('ruleId');
            var ruletitle = $(obj).attr('ruletitle');
            $("input[name='id']").val(ruleId);
            $("input[name='title']").val(ruletitle);
            $('#bjy-edit').modal('show');
        }
        $(".deleteBtn").click(function(){
            var id=$(this).attr('ruleId');
            layui.use('layer', function(){
                var layer = layui.layer;
                layer.confirm('确定删除?', {icon: 3, title:'温馨提示'}, function(index){
                    window.location.href='delete_group?id='+id;                
                });
            });
        });
        $('.pagination ul a').unwrap('div').wrap('<li></li>');
        $('.pagination ul span').wrap('<li class="active"></li>')
    </script>

</body>
</html>