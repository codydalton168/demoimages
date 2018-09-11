<?php
error_reporting(E_ERROR | E_PARSE);
define('WEB_ROOT',checkroot(__FILE__));
function_exists('set_magic_quotes_runtime') && set_magic_quotes_runtime(0);
function_exists('date_default_timezone_set') && date_default_timezone_set("Asia/Taipei");
include(WEB_ROOT.'Model/security.php');
security::Getglobal();
//security::globalcheck();
include(WEB_ROOT.'Model/command.php');
include(WEB_ROOT.'Model/cookie.php');
include(WEB_ROOT.'Model/gzip.php');
include(WEB_ROOT.'admin/admin_cp.php');


if(!$adminis){

       include(WEB_ROOT."admin/index.php");

} elseif(file_exists(WEB_ROOT."admin/$adminis.php")){

	include(WEB_ROOT."admin/$adminis.php");

}else{
	exit("Can not read  $adminis.php Files");
}

/*
include_once
require_once
*/

function checkroot($path=null){
	if (empty($path)) {
		return './';
	} else {
		if (strpos($path,'\\')!==false) {
			return substr($path,0,strrpos($path,'\\')).'/';
		} elseif (strpos($path,'/')!==false) {
			return substr($path,0,strrpos($path,'/')).'/';
		} else {
			return './';
		}
	}
}
?>