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
    
<div class="content">
    
    <div class="row-fluid fl" id="main">
        <div class="tableBox">
            <div class="ibox-title">
                <h5>用户组列表 <small>分配权限</small></h5>
            </div>
            <div class="ibox-content">
                <h1 class="text-center ruleTitle">
                    为
                    <span style="color:red">
                        <?php echo ($group_data['title']); ?>
                    </span>
                    分配权限
                </h1>
                <form action="" method="post">
                    <input type="hidden" name="id" value="<?php echo ($group_data['id']); ?>">
                    <table class="table table-bordered table-hover">
                        <?php if(is_array($rule_data)): foreach($rule_data as $key=>$v): if(empty($v['_data'])): ?><tr class="b-group">
                                    <th width="10%">
                                        <label>
                                            <?php echo ($v['title']); ?>
                                            <input type="checkbox" name="rule_ids[]" value="{$v['id']}" <?php if(in_array($v['id'],$group_data['rules'])): ?>checked="checked"<?php endif; ?>
                                            onclick="checkAll(this)" >
                                        </label>
                                    </th>
                                    <td>
                                    </td>
                                </tr>
                                <?php else: ?>
                                <tr class="b-group">
                                    <th width="10%">
                                        <label>
                                            <?php echo ($v['title']); ?>
                                            <input type="checkbox" name="rule_ids[]" value="<?php echo ($v['id']); ?>" <?php if(in_array($v['id'],$group_data['rules'])): ?>checked="checked"<?php endif; ?>
                                            onclick="checkAll(this)">
                                        </label>
                                    </th>
                                    <td class="b-child">
                                        <?php if(is_array($v['_data'])): foreach($v['_data'] as $key=>$n): ?><table class="table table-striped table-bordered table-hover table-condensed">
                                                <tr class="b-group">
                                                    <th width="10%">
                                                        <label>
                                                            <?php echo ($n['title']); ?>
                                                            <input type="checkbox" name="rule_ids[]" value="<?php echo ($n['id']); ?>" <?php if(in_array($n['id'],$group_data['rules'])): ?>checked="checked"<?php endif; ?>
                                                            onclick="checkAll(this)">
                                                        </label>
                                                    </th>
                                                    <td>
                                                        <?php if(!empty($n['_data'])): if(is_array($n['_data'])): $i = 0; $__LIST__ = $n['_data'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$c): $mod = ($i % 2 );++$i;?><label>
                                                                    &emsp;<?php echo ($c['title']); ?>
                                                                    <input type="checkbox" name="rule_ids[]" value="<?php echo ($c['id']); ?>" <?php if(in_array($c['id'],$group_data['rules'])): ?>checked="checked"<?php endif; ?>
                                                                    >
                                                                </label><?php endforeach; endif; else: echo "" ;endif; endif; ?>
                                                    </td>
                                                </tr>
                                            </table><?php endforeach; endif; ?>
                                    </td>
                                </tr><?php endif; endforeach; endif; ?>
                        <tr>
                            <th>
                            </th>
                            <td>
                                <input class="btn btn-success" type="submit" value="提交">
                            </td>
                        </tr>
                    </table>
                </form>
            </div>
            
        </div>
        <!-- footer part -->
        
    </div>
</div>
<!-- 弹框信息 -->
<div class="modal fade" id="bjy-add" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
            aria-hidden="true">
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
<div class="modal fade" id="bjy-edit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
aria-hidden="true">
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

    
<script>
    function checkAll(obj) {
        $(obj).parents('.b-group').eq(0).find("input[type='checkbox']").prop('checked', $(obj).prop('checked'));
    }
    $('.pagination ul a').unwrap('div').wrap('<li></li>');
    $('.pagination ul span').wrap('<li class="active"></li>')
</script>

</body>
</html>