<?php
error_reporting(E_ERROR | E_PARSE);
function_exists('set_magic_quotes_runtime') && set_magic_quotes_runtime(0);
function_exists('date_default_timezone_set') && date_default_timezone_set("Asia/Taipei");
define('WEB_ROOT',checkroot(__FILE__));
require_once(WEB_ROOT.'Model/security.php');
require_once(WEB_ROOT.'Model/Mobile_Detect.php');
security::Getglobal();
security::globalcheck();
require_once(WEB_ROOT.'Model/command.php');
require_once(WEB_ROOT.'Model/cookie.php');
require_once(WEB_ROOT.'Model/gzip.php');
require_once(WEB_ROOT.'data/sqlconfig.php');
require_once(WEB_ROOT.'data/config.php');


$onlineip = GetcheckIp();

security::AntiCC();

$SELF = $GServer['PHP_SELF'];
$HTTPHOST =  "http://$GServer[HTTP_HOST]".substr($SELF,0,strrpos($SELF,'/'));
$HTTPREFERER = $GServer['HTTP_REFERER'];
$REQUEST_URI = $GServer['PHP_SELF'].($GServer['QUERY_STRING'] ? '?'.$GServer['QUERY_STRING'] : '');
$ALLHTTP = "http://{$GServer[HTTP_HOST]}{$REQUEST_URI}";

$timestamp = time();

$VersionNumber='';

GzipModel::StartGzip();

$today_time = linuxTime(showdate($timestamp,'Y-m-d')); //今日

$month_time = linuxTime(showdate($timestamp,'Y-m').'-1');//當月


Startsql();

unset($default_host,$default_user,$default_pwassword,$default_name,$default_charset,$default_pconnect,$default_lp);



function meassage($msg,$jumpurl,$Second='2'){
	global $seo;
	ob_end_clean();

	if(empty($jumpurl)){
		$Second='0';
	}

	require_once printHTML('meassage');

	$output = ob_get_contents();

	if($GLOBALS['default_rewrite']){
		$output =  HtmlRewrite($output);
	}


	ob_end_clean();

	$output = ObFirstr($output);

	if($default_redundancy){
		$output = compress_html($output);
	}

	$output = removeBOM($output);

	echo GzipModel::GzipExport($output);
	unset($output);
	GzipModel::claseGZIP();
	exit;


}


function printHTML($template,$EXT="html"){

    	if(file_exists(WEB_ROOT."template/{$template}.$EXT")){

        	return WEB_ROOT."template/{$template}.$EXT";

	} else {
        	exit("No $template_$ShopLang.$EXT Files..");
    	}
}




function ajaxlist(){
	global $default_obstart;
	$output = ob_get_contents();
	ob_end_clean();
	echo $output;

}


function ajaxfooter(){
	global $default_obstart;
	$output = ob_get_contents();

       $output = ObFirstr($output);

	ob_end_clean();

	$output = removeBOM($output);

	//header("Content-Type: text/xml;charset=UTF-8");

	if($default_redundancy){
		$output = compress_html($output);
	}


	echo GzipModel::GzipExport($output);
	unset($output);
	GzipModel::claseGZIP();
	exit;
}




function footer(){
	global $default_obstart,$timestamp,$default_redundancy;

       $output = ob_get_contents();

       $output = preg_replace("/<!--{.*?}-->/is","",$output);

	if($GLOBALS['default_rewrite']){
		$output =  HtmlRewrite($output);
	}

	ob_end_clean();

	$output = ObFirstr($output);

	if($default_redundancy){
		$output = compress_html($output);
	}

	$output = removeBOM($output);



	//$output .= "<script language=\"JavaScript\" src=\"Statistics{$timestamp}.html\"></script>";




	echo GzipModel::GzipExport(versioncache($output));
	unset($output);
	GzipModel::claseGZIP();
	exit;
}


function versioncache($output){


	if($GLOBALS[VersionNumber]){

		$output = preg_replace("/href=('|\")(.+\.(css)\"?)('|\")/i","href=\\1\\2?v={$GLOBALS[VersionNumber]}\\4",$output);

		$output = preg_replace("/src=('|\")(.+\.(js)\"?)('|\")/i","src=\\1\\2?v={$GLOBALS[VersionNumber]}\\4",$output);


              $img = showdate($GLOBALS[timestamp],'YmdHis');


              $output = preg_replace("/src=('|\")(attachment.+\.(png|jpg|gif|jpeg|bmp)\"?)('|\")/i","src=\\1\\2?v={$img}\\4",$output);
	}
	return $output;

}


function ObFirstr($str) {

       $str = str_replace(array("\r",'<!--<!---->-->', '<!---->-->', '<!--<!---->', "<!---->\n", '<!-- -->', "<!--\n-->","<!---->"),'', $str);

	return $str;
}




function HtmlRewrite($output){


	$output = preg_replace("/index.php(\s*[^\>]+\s*)category=([0-9]+)&page=([0-9]+)/","index-category-\\2-\\3.html",$output);

	$output = preg_replace("/index.php(\s*[^\>]+\s*)category=([0-9]+)/","index-category-\\2.html",$output);

	$output = preg_replace("/index.php(\s*[^\>]+\s*)page=([0-9]+)/","index-\\2.html",$output);

       $output = preg_replace("/(.+?).php/","\\1.html",$output);



       $output = str_replace("sharer.html","sharer.php",$output);

	return $output;
}





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




function printEmailHTML($template,$path="mail",$EXT="html"){

	if(file_exists(WEB_ROOT."mail/$template.$EXT")){


        	return WEB_ROOT."mail/$template.$EXT";


	} else {

		exit("ERROR:mail/$template.$EXT");
	}
}


function edmcontent($template){

	if(file_exists(WEB_ROOT."$template")){
		return file_get_contents(WEB_ROOT."$template");
	} else {
		exit("ERROR:$template");
	}

}


function mailcontents(){

       $output = ob_get_contents();

	if($GLOBALS['default_rewrite']){
		$output =  HtmlRewrite($output);
	}


       ob_end_clean();

       $output = ObFirstr($output);

	if($default_redundancy){
		$output = compress_html($output);
	}

	$output = removeBOM($output);


	return $output;
}



function mailclean(){
	GzipModel::claseGZIP();
}


function Startsql(){
	global $db,$default_host,$default_port,$default_user,$default_pwassword,$default_name,$default_charset,$default_pconnect,$default_lp;

	if (!is_object($GLOBALS['db'])) {

		require_once security::escapePath(WEB_ROOT.'Model/mysqli_db.php');

		$db = new DBdriver($default_host,$default_port,$default_user,$default_pwassword,$default_name,$default_charset,$default_pconnect,$default_lp);

	}
}


?>