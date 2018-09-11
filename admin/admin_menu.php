<?php
!defined('WEB_ROOT') && exit('Forbidden');

$menuleft = array(
	'adminuser' => array(
		'name'        =>	'管理員',
              'class'       =>     'icon-male',
		'option'=> array(
                            'adminuser'                  =>	'<a href="javascript:;" onclick="topmenu(\'adminuser\'); return false;"><i class="icon-double-angle-right"></i>管理員名單</a>'
		),
	),

/*
       'system' => array(
		'name'	=>	'系統設定',
              'class'       =>     'icon-credit-card',
		'option'=> array(
                            'meta'                       =>     '<a href="javascript:;" onclick="return topmenu(\'meta\');return false;">Meta 設定</a>',
                            'setting'                    =>     '<a href="javascript:;" onclick="return topmenu(\'setting\');return false;">系統環境設定</a>',
                            'task'                       =>     '<a href="javascript:;" onclick="return topmenu(\'task\');return false;">系統計劃任務</a>',
                            'mail'                       =>     '<a href="javascript:;" onclick="return topmenu(\'mail\');return false;">郵件伺服器設定</a>',
                            //'regis'                      =>     '<a href="javascript:;" onclick="return topmenu(\'regis\');return false;">會員註冊設定</a>',
                            'payment'                    =>     '<a href="javascript:;" onclick="return topmenu(\'payment\');return false;">支付管理</a>',
                            'sentway'                    =>     '<a href="javascript:;" onclick="return topmenu(\'sentway\');return false;">運送管理</a>',
                            'area'                       =>     '<a href="javascript:;" onclick="return topmenu(\'area\');return false;">地區列表</a>',
		),
	),*/


);



$admintype = array(




);

?>