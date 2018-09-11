<?php
!defined('WEB_ROOT') && exit('Forbidden');
security::Getglobals(array('adminis','quit','action','page'),'GET',true);
security::Getglobals(array('step','username','password'),'POST');
if ($adminis && strpos($adminis,'..') !== false) {
	exit('Forbidden');
}

include(WEB_ROOT."data/config.php");
include(WEB_ROOT."data/sqlconfig.php");


$admin_file = 'admin.php?adminis='.$adminis;
$REQUESTURI = $GServer['PHP_SELF'].'?'.$GServer['QUERY_STRING'];
$SELF = $GServer['PHP_SELF'];
$HTTPHOST =  "http://$GServer[HTTP_HOST]".substr($SELF,0,strrpos($SELF,'/'));
$HTTPREFERER = $GServer[HTTP_REFERER];
$timestamp = time();


${'class_'.$adminis} = 'class="active"';

!$adminis && $class_main = 'class="active"';



$onlineip = GetcheckIp();


if($quit == 'quit'){
	CookieModel::ShowCookie("AdminUser","","0");
	header("Location: admin.php");
	exit;
}


if($_GET[ajax] || $_POST[ajax]){
	@header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' );
	@header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
	@header( 'Cache-Control: no-store, no-cache, must-revalidate' );
	@header( 'Cache-Control: post-check=0, pre-check=0', false );
	@header( 'Pragma: no-cache' );
}
 
GzipModel::StartGzip();

$today_time = linuxTime(showdate($timestamp,'Y-m-d')); //今日

$month_time = linuxTime(showdate($timestamp,'Y-m').'-1');//當月


if(!$adminis && $username && $password){

	checkpass($username,$password);

} else {

	list($admincpid,$adminname,$admincountry)= adminchecks();
}







function ckadminmenu($data){
	global $admincpid,$level;
	$int = 0;

	if(!is_array($data)){
		return false;
	}

       foreach($data as $key => $value){
		if($level[$value]){
  			$int++;
		}

	}

	//if(count($data) == $int){
	if($int > 0){
		return true;

	} else {

		return false;
	}

}





function adminchecks(){
	global $db,$_GET;

	if(CookieModel::GetCookie('AdminUser')){

		list($admincpid,$password,$adminleval,$admincountry) = explode("|",CookieModel::ValueEncryption(CookieModel::GetCookie('AdminUser'),'DECODE'));


		if($admincpid){

			if (!is_object($GLOBALS['db'])) {
				Startsql();
			}


			$rt = $db->getone("select uid,username,password,logintime FROM adminuser where uid=".GetFilter($admincpid));

			if(!$rt[uid] || $admincpid != $rt[uid] || $password != $rt[password]){

			  	CookieModel::ShowCookie("AdminUser","","0");

			  	adminmsg("&#24744;&#27794;&#26377;&#27402;&#38480;&#35370;&#21839;");

			} else {



                            if($GLOBALS[timestamp]-$rt[logintime] > 5 * 60){

                                   //$country = GeoipCheck('2');

                                   $db->update("update adminuser set onlineip='{$GLOBALS[onlineip]}',ipfrom='$admincountry',logintime='{$GLOBALS[timestamp]}' where uid=".GetFilter($rt[uid]));

                            }




                            return array($rt[uid],$rt[username],$admincountry);
			}

		}


	}  else {


              include template('login');footer();

	}


}


function checkpass($admid,$admpw){
	global $db,$onlineip,$timestamp;

	if(!$admid && !$admpw){
		echo "error";
		exit;
	}

	if (!is_object($GLOBALS['db'])) {
		Startsql();
	}

	$country = GeoipCheck('2');

	$rt = $db->getone("select * FROM adminuser where email='$admid' AND password=".GetFilter(md5($admpw)));


	if($rt[uid]){


		CookieModel::ShowCookie("AdminUser",CookieModel::ValueEncryption($rt[uid]."|".$rt[password]."|".$country),'24E');

		$db->update("update adminuser set onlineip='$onlineip',ipfrom='$country',logintime='$timestamp' where uid=".GetFilter($rt[uid]));

		echo "success";
		exit;



	} else {

		echo "error";
		exit;


	}


}



function adminmsg($msg,$jumpurl="",$Second=1){
	global $GServer;

	if(!$Second){
		ObHeader($jumpurl);
	}


	ob_end_clean();
	if(!$msg && $jumpurl){
		//header("Location: $jumpurl");
		ObHeader($jumpurl);
		exit;
	}

	include template('message');footer();
}




function ajaxfooter(){
	global $default_obstart;
	$output = ob_get_contents();

       $output = ObFirstr($output);

	ob_end_clean();

	$output = removeBOM($output);
	//header("Content-Type: text/xml;charset=UTF-8");

	echo GzipModel::GzipExport($output);
	unset($output);
	GzipModel::claseGZIP();
	exit;
}


function footer($Clearnewline=false){
	global $default_obstart,$timestamp;

       $output = ob_get_contents();

       //$output = preg_replace("/<!--(.*?)-->/is","",$output);


	ob_end_clean();

       $output = ObFirstr($output);

	if($Clearnewline){
		$output = compress_html($output);
	}

	$output = removeBOM($output);
	/*
	$output = preg_replace(
		"/\<form([^\<\>]*)\saction=['|\"]?([^\s\"'\<\>]+)['|\"]?([^\<\>]*)\>/ies",
		"FormCheck('\\1','\\2','\\3')",
		rtrim($output,'<!--')
	);*/




	echo GzipModel::GzipExport($output);
	unset($output);
	GzipModel::claseGZIP();
	exit;
}








function EncodeUrl($url) {
	global $timestamp;


	$url_a = substr($url,strrpos($url,'?')+1);
	substr($url,-1) == '&' && $url = substr($url,0,-1);
	parse_str($url_a,$url_a);
	$source = '';
	foreach ($url_a as $key => $val) {
		$source .= $key.$val;
	}

	$url .= "&t=$timestamp";
	return $url;
}
function FormCheck($pre,$url,$add){
	$pre = stripslashes($pre);
	$add = stripslashes($add);
	return "<form{$pre} action=\"".EncodeUrl($url)."\"{$add}>";
}


function ObFirstr($str) {

       $str = str_replace(array("\r",'<!--<!---->-->', '<!---->-->', '<!--<!---->', "<!---->\n", '<!-- -->', "<!--\n-->","<!---->"),'', $str);

	return $str;
}




function template($template,$EXT="html"){

    	if(file_exists(WEB_ROOT."admin/template/".$template.".$EXT")){

        	return WEB_ROOT."admin/template/".$template.".$EXT";
	}else{
        	exit("No $template.$EXT Files..");
    	}
}


function valueexport($input,$t = null) {
	$output = '';
	if (is_array($input)) {
		$output .= "array(\r\n";
		foreach ($input as $key => $value) {
			$output .= $t."\t".valueexport($key,$t."\t").' => '.valueexport($value,$t."\t");
			$output .= ",\r\n";
		}
		$output .= $t.')';
	} elseif (is_string($input)) {
		$output .= "'".str_replace(array("\\","'"),array("\\\\","\'"),$input)."'";
	} elseif (is_int($input) || is_double($input)) {
		$output .= "'".(string)$input."'";
	} elseif (is_bool($input)) {
		$output .= $input ? 'true' : 'false';
	} else {
		$output .= 'NULL';
	}
	return $output;
}




function Startsql(){
	global $db,$default_host,$default_port,$default_user,$default_pwassword,$default_name,$default_charset,$default_pconnect,$default_lp;

	if (!is_object($GLOBALS['db'])) {

		include security::escapePath(WEB_ROOT."Model/mysqli_db.php");

		$db = new DBdriver($default_host,$default_port,$default_user,$default_pwassword,$default_name,$default_charset,$default_pconnect,$default_lp);
	}
}




function delcontent($context){
	preg_match_all('/src=[\"\']?([^\"\']?.*(jpeg|bmp|png|jpg|gif))[\"\']?/i', $context, $array);
	foreach($array[1] as $imgurl){
              if(strpos($imgurl,"attachment/")!==false){
			if(file_exists(WEB_ROOT.$imgurl)){
				Punlink($imgurl);
			}
		}
	}
}









?>