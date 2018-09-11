<?php
!defined('WEB_ROOT') && exit('Forbidden');

function Description_split($str){

       $str = str_replace("&nbsp;","",$str);

       $str = preg_replace(array("/\<(.+?)\>/is","/[\s]+/"),"",$str);

       return substrs($str,'200');

}



function Keywords_split($str){

       $str = str_replace("&nbsp;","",$str);

       $str = preg_replace(array("/\<(.+?)\>/is","/[\s]+/"),"",$str);

       return implode(',',utf8_str_split($str,"2"));
}




/**
 * @version $Id: str_split.php 10381 2008-06-01 03:35:53Z pasamio $
 * @package utf8
 * @subpackage strings
*/

function utf8_str_split($str, $split_len = 1){

     if (!preg_match('/^[0-9]+$/', $split_len) || $split_len < 1)
         return FALSE;

     $len = mb_strlen($str, 'UTF-8');
     if ($len <= $split_len)
         return array($str);

     preg_match_all('/.{'.$split_len.'}|[^\x00]{1,'.$split_len.'}$/us', $str, $ar);

     return $ar[0];
}








function formatBytes($size,$fileqtype=true){

       if($fileqtype){
              $units = array("Byte","KB","MB","GB","TB","PB","EB","ZB","YB");
       }

       if($size < 1){
              return '0 Byte';
       } else {

              return number_format($size/pow(1024, $i=floor(log($size, 1024))), ($i >= 1) ? 2 : 0).' '.$units[$i];
       }

}

function AutoTransitionlink($message) {

	$message=preg_replace("/(?<=[^\]a-z0-9-=\"'\\/])((https?|ftp|gopher|news|telnet|mms|rtsp):\/\/|www\.)([a-z0-9\/\-_+=.~!%@?#%&;:$\\│\|]+)/i", "<a href=\"\\1\\3\" target=\"_blank\">\\1\\3<\/a>", ' '.$message);

	return $message;
}



/**
 * 截取字串
 *
 * @param string $content 內容
 * @param int $length 截取字串長度
 * @param string $add 是否省略?，Y|N
 * @return string
 */
function substrs($content, $length, $add = 'Y') {

	if (strlen($content) > $length) {

              $content = substr(compress_html($content), 0, $length);


		$hex = '';
		$len = strlen($content) - 1;
		for ($i = $len; $i >= 0; $i -= 1) {
			$ch = ord($content[$i]);
			$hex.=" $ch";
			if (($ch & 128) == 0 || ($ch & 192) == 192) {
				return substr($content, 0, $i).($add == 'Y' ? ' ....' : '');
			}
		}




		return $content.$hex.($add == 'Y' ? ' ....' : '');
	}
	return $content;
}



/**
* 壓縮 html 清除換行符,清除製表符,去掉註釋標記
* @param $string
* @return 壓縮後的 $string
*/

function compress_html($string) {
	//$string = str_replace("\r\n",'', $string);
	$string = str_replace("\n",'', $string);
	$string = str_replace("\t",'', $string);

	$pattern = array(
		"/> *([^ ]*) *</",
		"/[\s]+/",
		"/<!--[^!]*-->/",
		//"/\" /",
		"/ \"/",
		"'/\*[^*]*\*/'"
	);
	$replace = array(
		">\\1<",
		" ",
		"",
		//"\"",
		"\"",
		""
	);

	$string = preg_replace($pattern, $replace, $string);

	return $string;
}







/*
*   跟具外網公用  IP  來判斷來自哪個國家
*
*   @INT geoiptype 類型
*
*   http://geolite.maxmind.com/download/geoip/database/GeoLiteCountry/GeoIP.dat.gz
*
*  GeoipCheck('2');
*
*/

function GeoipCheck($geoiptype='1'){
		global $onlineip;

		include(WEB_ROOT.'Model/geoip.php');

		$Stringreturn = "";

		if(!$onlineip){
			return 'No IP';
		}


		if(strpos($onlineip,'192.168') !== false || strpos($onlineip,'127.0') !== false){
			return mb_convert_encoding('區網 IP','utf-8','big5');
		}



		// 打開各國IP二進位庫
		$gi = geoip_open(WEB_ROOT.'Model/GeoIP.dat',GEOIP_STANDARD);


		// 獲取國家代碼
		if($geoiptype == '1'){

			$Stringreturn = geoip_country_code_by_addr($gi,$onlineip);

		} else if($geoiptype == '2'){

			// 獲取國家名稱
			$Stringreturn = geoip_country_name_by_addr($gi,$onlineip);

			// 獲取國家名稱/代碼
		} else if($geoiptype == '3'){

			$countrycode = geoip_country_code_by_addr($gi,$onlineip);
			$countryname = geoip_country_name_by_addr($gi,$onlineip);
			$Stringreturn = array($countrycode,$countryname);
		}

		// 關閉文件
		geoip_close($gi);

		return $Stringreturn;
}








/*
* @return String
* 取出正確 IP  包含隱藏 Proxy IP
*/

function GetcheckIp(){
	global $db_proxyopen;
	//是否屏障代理性 Porxy  IP 訪問
	if($db_proxyopen && ($_SERVER['HTTP_X_FORWARDED_FOR'] || $_SERVER['HTTP_VIA'] || $_SERVER['HTTP_PROXY_CONNECTION'] || $_SERVER['HTTP_USER_AGENT_VIA'] || $_SERVER['HTTP_CACHE_INFO'] || $_SERVER['HTTP_PROXY_CONNECTION'])){
		header("HTTP/1.1: 404 Not Found");
		exit;

	}
	if ($_SERVER['HTTP_X_FORWARDED_FOR'] && $_SERVER['REMOTE_ADDR']) {
			if (strstr($_SERVER['HTTP_X_FORWARDED_FOR'], ',')) {
				$x = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
				$_SERVER['HTTP_X_FORWARDED_FOR'] = trim(end($x));
			}
			if (preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $_SERVER['HTTP_X_FORWARDED_FOR'])) {
				$onlineip = $_SERVER['HTTP_X_FORWARDED_FOR'];
			}
	} elseif ($_SERVER['HTTP_CLIENT_IP'] && preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $_SERVER['HTTP_CLIENT_IP'])) {
		$onlineip = $_SERVER['HTTP_CLIENT_IP'];
	}

	if(!$onlineip && preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/',$_SERVER['REMOTE_ADDR'])){
		$onlineip = $_SERVER['REMOTE_ADDR'];
	}

	!$onlineip && $onlineip = "No IP";

	return $onlineip;
}

/*
* @return String
* Unix 時間戳記轉正確時間
*/

function showdate($timestamp,$timeformat=null,$ty=false){

	$default_timedf='8';

	$default_datefm='Y-m-d H:i';

	$timeformat = $timeformat ? $timeformat : $default_datefm ;

	$d = date($timeformat,$timestamp);

	if($ty == true){

		$gh = date('H',$timestamp);

		if($gh < 6){
  			$datename=mb_convert_encoding("凌晨",'utf-8','big5');
		}elseif($gh < 7){
  			$datename=mb_convert_encoding("早上",'utf-8','big5');
		}elseif($gh < 12){
  			$datename=mb_convert_encoding("上午",'utf-8','big5');
		}elseif($gh < 13){
  			$datename=mb_convert_encoding("中午",'utf-8','big5');
		}elseif($gh < 18){
  			$datename=mb_convert_encoding("下午",'utf-8','big5');
		}else{
  			$datename=mb_convert_encoding("晚上",'utf-8','big5');
		}

		$d = str_replace(" "," $datename ",$d);
	}
	return $d;
}




function Timestamps($time,$timechecklang=true,$cutemsg){
	global $timestamp,$today_time;

	if ($timechecklang) {

		$timelang = array(
			'yesterday'	=>mb_convert_encoding('昨天','utf-8','big5'),
			'hour'		=>mb_convert_encoding('小時前','utf-8','big5'),
			'minute'	=>mb_convert_encoding('分鐘前','utf-8','big5'),
			'qiantian'	=>mb_convert_encoding('天前','utf-8','big5')
		);
	}



       if($cutemsg){
              $timelangpostmsg = $cutemsg;
       } else {
              $timelangpostmsg = mb_convert_encoding('發表於','utf-8','big5');
       }



	$decrease = $timestamp-$time;
	$thistime = linuxTime(showdate($time,'Y-m-d'));
	$result = showdate($time);
	$day -= floor($thistime-$today_time)/86400;
	if ($thistime == $today_time ) {
		if ($decrease <= 3600) {
			return ceil($decrease/60).$timelang['minute'].$timelangpostmsg;
		} else {
			return ceil($decrease/3600).$timelang['hour'].$timelangpostmsg;
		}
	} elseif ($thistime ==  $today_time-86400) {

		return $timelang['yesterday'].$timelangpostmsg;

	} elseif ($thistime <= $today_time-172800 && $day < 4) {


		return $day.$timelang['qiantian'].$timelangpostmsg;

	} else {

		return showdate($time,'Y-m-d');
	}
}









/*
* @return String
* 訂單編號隨機產生亂數 4 位
*/

function Orderform(){
       
	$possible = 'abcdefghijklmnopqrswxyzABCDEFGHIJKLMNOPQRSWXYZ';
	$code = '';
	$i = 0;
	while ($i < 2) {
		$code.= substr($possible, mt_rand(0, strlen($possible) - 1), 1);
		$i++;
	}
	return $code;
}



/*
* @return String
* 隨機產生亂數 6 位
*/
function codenum(){
	$possible = 'abcdefghijklmnopqrswxyz2346789';
	$code = '';
	$i = 0;
	while ($i < 6) {
		$code.= substr($possible, mt_rand(0, strlen($possible) - 1), 1);
		$i++;
	}
	return $code;
}

//前端分頁
function showpage($count, $page, $numofpage, $url, $max = null, $ajaxurl = '') {
	global $default_obstart,$defaultpage;
	$count = intval($count);
	$page = intval($page);
	$numofpage = intval($numofpage);
	$max = intval($max);
	$total = $numofpage;
	$pages=null;

	if (!empty($max)) {
		$max = (int) $max;
		$numofpage > $max && $numofpage = $max;
	}
	/*
	if ($numofpage <= 1 || !is_numeric($page)) {
		return '';
	}*/


	if (is_numeric($page)){

		list($pre, $next) = array($page - 1, $page + 1);

		list($url, $mao) = explode('#', $url);

		$mao && $mao = '#' . $mao;

		$pre == 0 && $pre=1;

		$pages = '<nav class="text-center"><ul class="pagination">';



       		if($page > 4){
       			$pages.= "<li><a href=\"{$url}page=1$mao\" " . ($ajaxurl ? " onclick=\"return ajaxpage('{$ajaxurl}page=1')\"" : '')." title=\"&#31532;&#19968;&#38913;\">&lt;&lt;</a></li>";
       		}

       		if($numofpage > 1){
       			$pages.= "<li><a href=\"{$url}page=$pre$mao\" " . ($ajaxurl ? " onclick=\"return ajaxpage('{$ajaxurl}page=$pre')\"" : '')." title=\"&#19978;&#19968;&#38913;\">&lt;</a></li>";
       		}

       		for ($i = $page - 2; $i <= $page - 1; $i++) {
       			if ($i < 1) continue;
       			$pages .= "<li><a href=\"{$url}page=$i$mao\"" . ($ajaxurl ? " onclick=\"return ajaxpage('{$ajaxurl}page=$i')\"" : '')." title=\"&#31532;{$i}&#38913;\">$i</a></li>";
       		}


       		$pages.= "<li class=\"active\"><a href=\"javascript:;\">$page</a></li>";


       		if ($page < $numofpage) {
       			$flag = 0;
       			$topage = $numofpage>2 ? '1' : '0';

       			for ($i = $page + 1; $i <= $numofpage-$topage; $i++) {
       				$pages .= "<li><a href=\"{$url}page=$i$mao\"" . ($ajaxurl ? " onclick=\"return ajaxpage('{$ajaxurl}page=$i')\"" : '')." title=\"&#31532;{$i}&#38913;\">$i</a></li>";
       				$flag++;
       				if ($flag == 2) break;
       			}
       		}

       		if($total > 3 && $page < $total){

       			$pages.= "<li><a  href=\"{$url}page=$total$mao\" " . ($ajaxurl ? " onclick=\"return ajaxpage('{$ajaxurl}page=$total')\"" : '') . " title=\"&#26368;&#24460;&#31532;{$total}&#38913;\">...$total</a></li>";

       		}

       		if($page < $total){

       			$pages.= "<li><a href=\"{$url}page=$next$mao\" " . ($ajaxurl ? " onclick=\"return ajaxpage('{$ajaxurl}page=$next')\"" : '')." title=\"&#19979;&#19968;&#38913;\">&gt;</a></li>";

       		}





       		$pages.= "<li><a href=\"javascript:;\">&#20849; {$total} &#38913;/";

       		//$pages.= "&#x6BCF;&#x9801; $defaultpage &#x7B46/";


       		$pages.= "&#x5171;&#x6709; $count &#31558;</a></li>";


		     $pages.= "</ul></nav>";


              }


		return $pages;



}

//後臺分頁
function Newpage($count, $page, $numofpage, $url, $max = null, $ajaxurl = '') {
	global $default_obstart;
	$count = intval($count);
	$page = intval($page);
	$numofpage = intval($numofpage);
	$max = intval($max);
	$total = $numofpage;
	$pages=null;

	if (!empty($max)) {
		$max = (int) $max;
		$numofpage > $max && $numofpage = $max;
	}
	
	if ($numofpage <= 1 || !is_numeric($page)) {
		return '';
	}

	if (is_numeric($page)){

		list($pre, $next) = array($page - 1, $page + 1);

		list($url, $mao) = explode('#', $url);

		$mao && $mao = '#' . $mao;

		$pre == 0 && $pre=1;

		$pages = "<div ><ul class=\"pagination\">";


		if($page > 4){


			$pages.= "<li class=\"disabled\"><a href=\"{$url}page=1$mao\" " . ($ajaxurl ? " onclick=\"return ajaxpage('{$ajaxurl}page=1')\"" : '')." title=\"&#31532;&#19968;&#38913;\">&lt;&lt;</a></li>";

		}


		$pages.= "<li class=\"disabled\"><a href=\"{$url}page=$pre$mao\" " . ($ajaxurl ? " onclick=\"return ajaxpage('{$ajaxurl}page=$pre')\"" : '')." title=\"&#19978;&#19968;&#38913;\">&lt;</a></li>";




		for ($i = $page - 2; $i <= $page - 1; $i++) {
			if ($i < 1) continue;
			$pages .= "<li><a href=\"{$url}page=$i$mao\"" . ($ajaxurl ? " onclick=\"return ajaxpage('{$ajaxurl}page=$i')\"" : '')." title=\"&#31532;{$i}&#38913;\">$i</a></li>";
		}


		$pages.= "<li class=\"active\"><a href=\"javascript:;\"><b>$page</b></a></li>";


		if ($page < $numofpage) {
			$flag = 0;
			$topage = $numofpage>2 ? '1' : '0';

			for ($i = $page + 1; $i <= $numofpage-$topage; $i++) {
				$pages .= "<li><a href=\"{$url}page=$i$mao\"" . ($ajaxurl ? " onclick=\"return ajaxpage('{$ajaxurl}page=$i')\"" : '')." title=\"&#31532;{$i}&#38913;\">$i</a></li>";
				$flag++;
				if ($flag == 2) break;
			}
		}

		if($total > 3 && $page < $total){

			$pages.= "<li><a  href=\"{$url}page=$total$mao\" " . ($ajaxurl ? " onclick=\"return ajaxpage('{$ajaxurl}page=$total')\"" : '') . " title=\"&#26368;&#24460;&#31532;{$total}&#38913;\">...$total</a></li>";

		}

		if($page < $total){

			$pages.= "<li class=\"disabled\"><a href=\"{$url}page=$next$mao\" " . ($ajaxurl ? " onclick=\"return ajaxpage('{$ajaxurl}page=$next')\"" : '')." title=\"&#19979;&#19968;&#38913;\">&gt;</a></li>";

		}

		$pages.= "<li ><a href=\"javascript:;\">&#20849; {$total} &#38913;/$count &#31558;</a></li>";



		$pages.= "</ul></div>";

		return $pages;
	}
}


/*
*   @param  string $path  目錄路徑
*   @param  string $Spath  預設刪除全部
*   @param  bool false   只刪除 目錄子資料及檔案
*   @param  array NoFile  array('1','2')  保留目錄及檔案
*   對某一個目錄刪除包含裡面檔案
*
*/
function deldir($path,$Spath=true,$NoFile=""){

	if ($NoFile && !is_array($NoFile)) {
		$NoFile = array($NoFile);
	}

	if (file_exists($path)) {
		if (is_file($path)) {
			@unlink($path);
		} else {
			$handle = opendir($path);
			while ($file = readdir($handle)) {
				if ($file!='' && !in_array($file,array('.','..'))) {

					if($NoFile && in_array($file,$NoFile)){
						continue;
					}

					if (is_dir("$path/$file")) {

						deldir("$path/$file");

					} else {

						@unlink("$path/$file");
					}
					rmdir("$path/$file");
				}
			}
			closedir($handle);
			if($Spath){
				rmdir($path);
			}
		}
	}
}






/**
 * 讀取文件
 *
 * @param string $fileName 文件絕對路徑
 * @param string $method 讀取模式
 */
function readover($fileName, $method = 'rb') {
	$fileName = security::escapePath($fileName);
	$data = '';
	if ($handle = @fopen($fileName, $method)) {
		flock($handle, LOCK_SH);
		$data = @fread($handle, filesize($fileName));
		fclose($handle);
	}
	return $data;
}

/*
*
*
*  寫入檔案
*  @param  string  $filename  路徑/檔名
*  @param  string  $data  內容
*  @param  string $method 類型  w+ a+  rb+
*  @param  string $iflock 鎖定檔案
*  @param  string $check 撿查路徑符號
*  @param  string $chmod  設定為 Linux 讀寫 777
*/

function writeover($filename,$data,$method='rb+',$iflock=1,$check=1,$chmod=1){
	$check && strpos($filename,'..')!==false && exit('Forbidden');
	touch($filename);
	$handle = fopen($filename,$method);
	$iflock && flock($handle,LOCK_EX);
	fwrite($handle,$data);
	$method=='rb+' && ftruncate($handle,strlen($data));
	fclose($handle);
	$chmod && @chmod($filename,0777);
}

/*
* @param  string $fileName  路徑/檔名
*刪除檔案
*/
function Punlink($fileName) {
	return @unlink(security::escapePath($fileName));
}



/*
*
*  @ 建立目錄
*
*/

function createFolderd($path,$write=false){
	if (!is_dir($path)) {
		createFolderd(dirname($path));
		@mkdir($path);
		@chmod($path,0777);
		@fclose(@fopen($path.'/index.html','w'));
		@chmod($path.'/index.html',0777);
              $write && writeover(WEB_ROOT.'cache/del.txt',$path."\n",'a+');
	}


}

/*
*
**   SQL  過濾格式
*
*
*/

function GetFilter($var,$strip = true) {
	if (is_array($var)) {
		foreach ($var as $key => $value) {
			$var[$key] = trim(GetFilter($value,$strip));
		}
		return $var;
	} elseif (is_numeric($var)) {
		return " '".trim($var)."' ";
	} else {
		return " '".addslashes($strip ? stripslashes(trim($var)) : trim($var))."' ";
	}
}

function SqlSingle($arraydb,$strip=true) {
	$arraydb = GetFilter($arraydb,$strip);
	$strdb = '';
	foreach ($arraydb as $key => $value) {
          	$strdb .= ($strdb ? ', ' : ' ').$key.'='.$value;
	}
	return $strdb;
}


function SqlMulti($array,$strip=true) {
	$str = '';
	foreach ($array as $val) {
		if (!empty($val)) {
			$str .= ($str ? ', ' : ' ') . '(' . showImplode($val,$strip) .') ';
		}
	}
	return $str;
}
function showImplode($array,$strip=true) {
	return implode(',',GetFilter($array,$strip,true));
}


/*
*      @return   string
*     String   日期轉  unix  時間
*
*/


function linuxTime($time){
	return strtotime($time);
}


/*
*    @return   string
*    特殊符號過濾
*
*/

function Charcv($string){
	$string = str_replace(array("\0","%00","\r"), '', $string);
	$string = preg_replace(array('/[\\x00-\\x08\\x0B\\x0C\\x0E-\\x1F]/','/&(?!(#[0-9]+|[a-z]+);)/is'), array('', '&amp;'), $string);
	$string = str_replace(array("%3C",'<'), '&lt;', $string);
	$string = str_replace(array("%3E",'>'), '&gt;', $string);
	$string = str_replace(array('"',"'","\t",'  '), array('&quot;','&#39;','    ','&nbsp;&nbsp;'), $string);
	return $string;
}


/*
* 判斷是否有 BOM 自動移除
* @param $str
* @return $str
*/
function removeBOM($str = ''){
    if(substr($str, 0,3) == pack("CCC",0xef,0xbb,0xbf)){
		$str = substr($str, 3);
    }
    return $str;
}




function ObHeader($URL) {
	echo '<meta http-equiv="expires" content="0">';
	echo '<meta http-equiv="Pragma" content="no-cache">';
	echo '<meta http-equiv="Cache-Control" content="no-cache">';
	echo "<meta http-equiv='refresh' content='0;url=$URL'>";
	footer();
	//exit;
}




function DecryptValue($array) {

	if($GLOBALS[$array]){
              $str = base64_decode(substr($GLOBALS[$array],16,strlen($GLOBALS[$array])-32));
              return $str;
	}
}



//電子郵件發送
function MailSend($email,$mail_subject,$mail_content) {
	global $ShopLang,$seozhtw,$seoenus;

	if(!$email){
              echo mb_convert_encoding('發送mail未輸入','utf-8','big5');
              return false;
	} else if(!$mail_subject){
		echo mb_convert_encoding('發送主旨未輸入','utf-8','big5');
		return false;
	} else if(!$mail_content){
              echo mb_convert_encoding('發送內容未輸入','utf-8','big5');
              return false;
	}




       if(!$GLOBALS['default_smtpfrom'] && !$GLOBALS['default_smtppassword'] && !$GLOBALS['default_smtpusername'] && !$GLOBALS['default_smtp'] && !$GLOBALS['default_method']){

              echo 'Mail server 參數錯誤?';
              return false;
	}



	//$mail_subject = mb_convert_encoding($mail_subject,'utf-8','big5');

	//$mail_subject = addslashes($mail_subject);

	//$mail_content = addslashes($mail_content);



	$mail = new PHPMailer();

	//$mail->setLanguage('zh', WEB_ROOT.'Model/phpmailer/language/');

	$mail->CharSet = "utf-8";

	$mail->Encoding = "8bit";

	$mail->ContentType = 'text/html';

	$mail->Priority   = '1';

	$mail->XMailer = "Nikon Digital Technology System";

       // 使用 Mail
    	if ($GLOBALS['default_method'] == 1){
		$mail->IsMail();

       // 使用 Sendmail
    	}elseif ($GLOBALS['default_method'] == 2){

		$mail->IsSendmail();

        // 使用 SMTP
    	}elseif ($GLOBALS['default_method'] == 0){

       		//if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {

			$mail->IsSMTP();

		//}
    	}

	$mail->Host  = $GLOBALS['default_smtp'];

	$mail->Port = $GLOBALS['default_smtpPort'];


	if($GLOBALS[default_smtpauth]){

		$mail->SMTPAuth = true;
	}

	if($GLOBALS['default_secure']){
		$mail->SMTPSecure = $GLOBALS['default_secure'];
	}


	$mail->Username = $GLOBALS['default_smtpusername'];

	$mail->Password = $GLOBALS['default_smtppassword'];





	//$mail->From     = $GLOBALS['default_smtpusername'];

	if($ShopLang == 'zhtw'){

		$mail->addReplyTo($GLOBALS['default_smtpfrom'], $seozhtw[index][title].mb_convert_encoding('客服','utf-8','big5'));

		$mail->FromName = $seozhtw[index][title].$GLOBALS[default_mail_name];

	} else if($ShopLang == 'enus'){

		$mail->addReplyTo($GLOBALS['default_smtpfrom'], $seoenus[index][title].' Server Suppert');

		$mail->FromName = $seoenus[index][title].$GLOBALS[default_mail_name];
	}


	/*
	$mail->DKIM_domain = '103.17.9.124';
	$mail->DKIM_private = 'rsa.private';
	$mail->DKIM_selector = '103.17.9.124';
	$mail->DKIM_passphrase ='rsa.public';
	$mail->DKIM_identity = '103.17.9.124';
	*/
	$mail->Sender= $GLOBALS['default_smtpusername'];

	$mail->ConfirmReadingTo = $GLOBALS['default_smtpfrom'];

	$mail->AddAddress($email);

	$mail->WordWrap = 50;  

       //$mail->SMTPDebug  = 2;

       //$mail->Debugoutput = 'html';

	//$mail->AddAttachment("mailhtml/logo.png", "nlogo.png");    // 附件

        $mail->IsHTML(true);

        $mail->Subject = $mail_subject;

        $mail->MsgHTML($mail_content);

        //$mail->Body    = $mail_content;
        //$mail->AltBody = $mail_content;

	if(!$mail->Send()){
		//echo 'Mailer Error: ' . $mail->ErrorInfo;
		//exit;

		$mail->clearAllRecipients();
		$mail->clearReplyTos();
		$mail->clearBCCs();
		$mail->clearCCs();

		$mail->clearAddresses();
		$mail->clearAttachments();
       		return false;

	} else {
		$mail->clearAllRecipients();
		$mail->clearReplyTos();
		$mail->clearBCCs();
		$mail->clearCCs();

		$mail->clearAddresses();
		$mail->clearAttachments();
		return true;
	}
}



/**
* 操作加鎖
*
* @param string $t 鎖定名稱
* @param int $u 附加名稱
* @return bool 是否成功
*/

function Lock($t, $u = 0) {
	global $timestamp;


	if(!file_exists(WEB_ROOT."cache/$u$t.lock")){
		writeover(WEB_ROOT."cache/$u$t.lock","LOCK");
		return true;

	} else if($timestamp-filemtime(WEB_ROOT."cache/$u$t.lock") > 5 * 60){
		writeover(WEB_ROOT."cache/$u$t.lock","LOCK");
		return true;
	} else {
		return false;
	}

}


/**
* 操作解鎖
*
* @param string $t 鎖定名稱
* @param int $u 附加名稱
*/

function UnLock($t = '', $u = 0) {
       if(file_exists(WEB_ROOT."cache/$u$t.lock")){
		Punlink("cache/$u$t.lock");
	}
}


function Filetime($file) {
	global $timestamp;

       if(!file_exists($file)){
		writeover($file,"LOCK");
	}

	return intval($timestamp-filemtime($file));
}





class Birthdate{


       /*
       產生民國年及西元
       西元轉民國
       */

       function RepublicTO($s,$e,$ifse=true){
       	global $timestamp;
       	if(!$s){
       		$s='0';
       	}

       	if($e){
       		$e=showdate($timestamp,'Y')+1;
       	}

       	for($i=$s;$i<$e;$i++){
           		$ch_date = explode("-", $i);
           		$ch_date[0] = $ch_date[0]-1911;
               	$date = '000000';
               	if ($ch_date[0] > 0 ) $date = $ch_date[0]."".$ch_date[1]."".$ch_date[2];
       		if($ifse){
       			$uploaddb[] = array('id'=>$i,'year'=>$date);
       		} else {
       			$uploaddb[$i] = array('year'=>$date);
       		}
       	}
       	return $uploaddb;

       }



       function rmonth($bir2=''){

              $bh5 = mb_convert_encoding('月份', "UTF-8", "big5");
              $bh6 = mb_convert_encoding('月', "UTF-8", "big5");

              $datastart="<select name=\"month\" id=\"month\" class=\"form-control\" tabindex=\"1\"><option value=\"0\">$bh5</option>";
              for($i=1;$i<13;$i++){
                     $ifk = $bir2 ==  $i ? "selected" : "";
                     $datastart.="<<option value=\"$i\" $ifk>$i $bh6</option>";
              }

              $datastart.="</select>-";


       	return $datastart;

       }

       function rday($bir3=''){

              $bh7 = mb_convert_encoding('幾號', "UTF-8", "big5");
              $bh8 = mb_convert_encoding('號', "UTF-8", "big5");


              $datastart="<select name=\"day\" id=\"day\" class=\"form-control\" tabindex=\"1\"><option value=\"0\">$bh7</option>";
              

              for($i=1;$i<32;$i++){
                     $ifk = $bir3 ==  $i ? "selected" : "";
                     $datastart.="<option value=\"$i\" $ifk>$i $bh8</option>";
              }

              $datastart.="</select>";

		return $datastart;
       }





       function creditBirthdate($bir1,$bir2,$bir3){
		global $timestamp;

		if($datebirth){
		     list($bir1,$bir2,$bir3) = explode("-",$datebirth);
		}


		$endtime = showdate($timestamp,'Y')+1;

		$settime = $endtime - 65;

		$RepublicTo = Birthdate::RepublicTO($settime,$endtime);


		$bh1 = mb_convert_encoding('西元年', "UTF-8", "big5");
		$bh2 = mb_convert_encoding('西元', "UTF-8", "big5");
		$bh3 = mb_convert_encoding('年', "UTF-8", "big5");
		$bh4 = mb_convert_encoding('民國', "UTF-8", "big5");





		$datastart="<select name=\"year\" id=\"year\"  class=\"form-control\" tabindex=\"1\"><option value=\"0\" >$bh1</option>";

		foreach($RepublicTo as $key => $value){

                     $ifk = $bir1 ==  $value[id] ? "selected" : "";

                     $datastart.="<option value=\"$value[id]\" $ifk>$bh2 $value[id] $bh3($bh4 $value[year] $bh3)</option>";
                     //$datastart.="<option value=\"$value[id]\" $ifk>$bh2 $value[id] $bh3</option>";
		}
		$datastart.="</select>-";

		$datastart.=Birthdate::rmonth($bir2);
		$datastart.=Birthdate::rday($bir3);



		return $datastart;
       }




       function rmonth_enus($bir2=''){

              $datastart="<select name=\"month\" id=\"month\" class=\"form-control\" tabindex=\"1\"><option value=\"0\">Month</option>";
              for($i=1;$i<13;$i++){
                     $ifk = $bir2 ==  $i ? "selected" : "";
                     $datastart.="<<option value=\"$i\" $ifk>$i $bh6</option>";
              }

              $datastart.="</select>";


		return $datastart;

       }

       function rday_enus($bir3=''){


              $datastart="<select name=\"day\" id=\"day\" class=\"form-control\" tabindex=\"1\"><option value=\"0\">Day</option>";
              

              for($i=1;$i<32;$i++){
                     $ifk = $bir3 ==  $i ? "selected" : "";
                     $datastart.="<option value=\"$i\" $ifk>$i $bh8</option>";
              }

              $datastart.="</select>";

		return $datastart;
       }






       function creditBirthdate_enus($bir1,$bir2,$bir3){
		global $timestamp;

		if($datebirth){
		     list($bir1,$bir2,$bir3) = explode("-",$datebirth);
		}


		$endtime = showdate($timestamp,'Y')+1;

		$settime = $endtime - 65;

		$RepublicTo = Birthdate::RepublicTO($settime,$endtime);

		/*
		$bh1 = mb_convert_encoding('西元年', "UTF-8", "big5");


		$bh2 = mb_convert_encoding('西元', "UTF-8", "big5");
		$bh3 = mb_convert_encoding('年', "UTF-8", "big5");
		$bh4 = mb_convert_encoding('民國', "UTF-8", "big5");
		*/

		$datastart.=Birthdate::rday_enus($bir3).'-';


		$datastart.=Birthdate::rmonth_enus($bir2).'-';


		$datastart.="<select name=\"year\" id=\"year\"  class=\"form-control\" tabindex=\"1\"><option value=\"0\" >Year</option>";

		foreach($RepublicTo as $key => $value){

                     $ifk = $bir1 ==  $value[id] ? "selected" : "";

                     $datastart.="<option value=\"$value[id]\" $ifk>$bh2 $value[id] $bh3</option>";
		}
		$datastart.="</select>";




		return $datastart;
       }






}









?>