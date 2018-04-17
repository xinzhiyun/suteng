<?php
return array(
    // 'action_begin'=>array('Home\\Behaviors\\test','Home\\Behaviors\\test1'),//一个标签位可以有多个行为，使用数组即可。
    // 如果是3.2.1以上版本 则需要改成
    'action_begin'=>array('Admin\\Behaviors\\CheckAuthBehavior')
);