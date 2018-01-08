<?php if (!defined('THINK_PATH')) exit();?> <!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>页面</title>
    <meta name="keywords" content="H+后台主题,后台bootstrap框架,会员中心主题,后台HTML,响应式后台">
    <meta name="description" content="H+是一个完全响应式，基于Bootstrap3最新版本开发的扁平化主题，她采用了主流的左右两栏式布局，使用了Html5+CSS3等现代技术">

    <link rel="shortcut icon" href="favicon.ico"> <link href="/Public/Admin/css/bootstrap.min.css?v=3.3.6" rel="stylesheet">
    <link href="/Public/Admin/css/font-awesome.css?v=4.4.0" rel="stylesheet">
    <link href="/Public/Admin/css/plugins/iCheck/custom.css" rel="stylesheet">
    <link href="/Public/Admin/css/animate.css" rel="stylesheet">
    <link href="/Public/Admin/css/style.css?v=4.1.0" rel="stylesheet">
    
    <link rel="stylesheet" type="text/css" href="/Public/">

</head>

<body class="gray-bg">
    
    <div class="content">
        <div class="row-fluid fl" id="main">
            <div class="tableBox">
                <div class="ibox-title">
                    <h5>设备管理 <small>设备列表</small></h5>
                    <div class="ibox-tools">
                        <i class="fa fa-user-plus"></i>
                        <a href="javascript:;" onclick="add()">添加设备</a>
                        <i class="fa fa-user-plus"></i>
                        <a href="javascript:;" onclick="adds()">批量添加设备</a>
                    </div>
                </div>
                <div class="ibox-content">
                    <table class="table table-bordered table-hover">
                        <form action="<?php echo U('Admin/Menu/order');?>" method="post">
                            <thead>
                                <tr>
                                    <th width="5%">
                                        序号
                                    </th>
                                    <th>
                                        设备编码
                                    </th>
                                    <th>
                                        设备状态
                                    </th>
                                    <th>
                                        操作
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(is_array($data)): foreach($data as $key=>$v): ?><tr>
                                        <td>
                                            <input class="input-medium" style="width:40px;height:25px;" type="text"
                                            name="<?php echo ($v['id']); ?>" value="<?php echo ($v['order_number']); ?>">
                                        </td>
                                        <td>
                                            <?php echo ($v['_name']); ?>
                                        </td>
                                        <td>
                                            <?php echo ($v['mca']); ?>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a data-toggle="modal" class="btn btn-outline btn-success" data-target="#myModal" navId="<?php echo ($v['id']); ?>" navName="<?php echo ($v['name']); ?>" onclick="add_child(this)">
                                                    添加子菜单
                                                </a>
                                                <a data-toggle="modal" class="btn btn-outline btn-success" data-target="#myModal" navId="<?php echo ($v['id']); ?>" navName="<?php echo ($v['name']); ?>" navMca="<?php echo ($v['mca']); ?>"
                                                navIco="<?php echo ($v['ico']); ?>" onclick="edit(this)">
                                                    修改
                                                </a>
                                                <a class="btn btn-outline btn-success deletBnt" ruleId="<?php echo ($v['id']); ?>">
                                                    删除
                                                </a>
                                            </div>
                                        </td>
                                    </tr><?php endforeach; endif; ?>
                                <tr>
                                    <th>
                                        <input class="btn btn-success" type="submit" value="排序">
                                    </th>
                                    <td>
                                    </td>
                                    <td>
                                    </td>
                                    <td>
                                    </td>
                                </tr>
                            </tbody>
                        </form>
                    </table>
                </div>
            </div>
            <!-- 弹框信息 -->
            <div class="modal inmodal" id="st-add" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content animated bounceInRight">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                                &times;
                            </button>
                            <h4 class="modal-title" id="myModalLabel">
                                添加设备
                            </h4>
                        </div>
                        <div class="modal-body">
                            <form id="bjy-form" class="form-inline" action="<?php echo U('Admin/Devices/add_device');?>"
                            method="post">
                                <input type="hidden" name="pid" value="0">
                                <table class="table table-striped table-bordered table-hover table-condensed">
                                    <tr>
                                        <th width="20%">
                                            设备编码：
                                        </th>
                                        <td>
                                            <input class="input-medium" type="text" name="name">
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>
                                            设备类型：
                                        </th>
                                        <td>
                                            <select name="type_id" class="selectAgency">
                                                <?php if(is_array($res)): foreach($res as $key=>$vo): ?><option value="<?php echo ($vo["id"]); ?>"><?php echo ($vo["typename"]); ?></option><?php endforeach; endif; ?>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>
                                        </th>
                                        <td>
                                            <input class="btn btn-success add_device" value="添加">
                                        </td>
                                    </tr>
                                </table>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal inmodal" id="st-adds" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content animated bounceInRight">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                                &times;
                            </button>
                            <h4 class="modal-title" id="myModalLabel">
                                批量添加设备
                            </h4>
                        </div>
                        <div class="modal-body">
                            <form id="bjy-form" class="form-inline" action="<?php echo U('Admin/Devices/upload');?>"
                            method="post">
                                <input type="hidden" name="pid" value="0">
                                <table class="table table-striped table-bordered table-hover table-condensed">
                                    <tr>
                                        <th width="30%">
                                            添加导入文件：
                                        </th>
                                        <td>
                                            <input type="file" name="batch" class="filename">
                                        </td>
                                        <th>
                                            <button class="btn btn-success add_devices">添加</button>
                                        </th>
                                    </tr>
                                </table>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- footer part -->
        </div>
    </div>

    <!-- 全局js -->
    <script src="/Public/Admin/js/jquery.min.js?v=2.1.4"></script>
    <script src="/Public/Admin/js/bootstrap.min.js?v=3.3.6"></script>
    <script src="/Public/Admin/js/plugins/metisMenu/jquery.metisMenu.js"></script>
    <script src="/Public/Admin/js/plugins/slimscroll/jquery.slimscroll.min.js"></script>
    <script src="/Public/Admin/js/plugins/layer/layer.min.js"></script>

    <!-- 自定义js -->
    <script src="/Public/Admin/js/hplus.js?v=4.1.0"></script>
    <script type="text/javascript" src="/Public/Admin/js/contabs.js"></script>

    <!-- 第三方插件 -->
    <script src="/Public/Admin/js/plugins/pace/pace.min.js"></script>

    
    <script src="/Public/Admin/layui/layui.js"></script>
    <script>
    // 添加设备
    function add() {
        $('#st-add').modal('show');
    }
    // 批量添加设备
    function adds() {
        $('#st-adds').modal('show');
    }

    $(".add_device").ajax({
        url:'Devices/add_device',
        type:'post',
        dataType:'json',
        success:function(res){
            alert(1);
        }
    });

    $('.pagination ul a').unwrap('div').wrap('<li></li>');
    $('.pagination ul span').wrap('<li class="active"></li>')

    </script>

</body>
</html>