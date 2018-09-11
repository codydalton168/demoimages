<?php

class security{

       /*
       *
       *   判斷訪問網站 USER 是否搜索引擎
       *  return boot
       */


       function ifsearchrobot(){

              $spanrobot = "bot|crawl|spider|slurp|sohu-search|lycos|robozilla|traveler|ia_archiver|heritrix|urllib|alexa|ask|yacy|legs|trivialnutch|rambler|tool|netcraft|search|larbin";

              if(preg_match("/($spanrobot)/i", strtolower($_SERVER['HTTP_USER_AGENT']))){
                     return true;
              } else {
                     return false;
              }

       }



	//遠程跨網資料傳送  curl_setopt  方式
	function http_post_data($url, $data_string='',$method='GET') {


		$ch = curl_init();


		curl_setopt($ch, CURLOPT_TIMEOUT, '3600');//超時時間
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Keep-Alive: 300','Connection: keep-alive')) ;
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 11; Windows NT 6.3; WOW64; Trident/7.0; GTB7.4; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; InfoPath.2)');

		if($method == 'POST'){

			curl_setopt($ch, CURLOPT_URL, $url);
                     curl_setopt($ch, CURLOPT_CUSTOMREQUEST,  'POST');
       		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
			curl_setopt($ch, CURLOPT_POSTFIELDS,$data_string);
			//curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
			/*
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				    'Content-Type: application/json',
				    'Content-Length: '.strlen($data_string))
			);*/


		} else if($method == 'GET'){

			curl_setopt($ch, CURLOPT_URL, $url);
       		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		}



              curl_setopt ($curl, CURLOPT_HEADER, 0);

		$replaydata = curl_exec($ch);

		curl_close($ch);



		return $replaydata;
	}



	//遠程跨網資料傳送
	function postget($host,$data='',$method='GET',$showagent=null,$port=null,$timeout=30){
		global $GServer;
		$parse = @parse_url($host);
		if (empty($parse)) return false;
		if ((int)$port>0) {
			$parse['port'] = $port;
		} elseif (!$parse['port']) {
			$parse['port'] = '80';
		}
		$parse['host'] = str_replace(array('http://','https://'),array('','ssl://'),"$parse[scheme]://").$parse['host'];
		if (!$fp=@fsockopen($parse['host'],$parse['port'],$errnum,$errstr,$timeout)) {
			return false;
		}
		$method = strtoupper($method);
		$wlength = $wdata = $responseText = '';
		$parse['path'] = str_replace(array('\\','//'),'/',$parse['path'])."?$parse[query]";
		if ($method=='GET') {
			$separator = $parse['query'] ? '&' : '';
			substr($data,0,1)=='&' && $data = substr($data,1);
			$parse['path'] .= $separator.$data;
		} elseif ($method=='POST') {
			$wlength = "Content-length: ".strlen($data)."\r\n";
			$wdata = $data;
		}

		$write = "$method $parse[path] HTTP/1.0\r\n";
		$write.= "Host: $parse[host]\r\n";
		$write.= "User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64; Trident/7.0; rv:11.0) like Gecko\r\n";
		$write.= "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8\r\n";
		$write.= "Accept-Charset:UTF-8\r\n"; 
		$write.= "Accept-Language: zh-TW\r\n"; 
		//$write.= "Accept-Encoding: gzip, deflate\r\n"; 
		//$write.= "Connection: keep-alive\r\n"; 
		$write.= "Referer: http://{$GServer[HTTP_HOST]}\r\n";
		$write.= "Content-type: application/x-www-form-urlencoded;\r\n";
		$write.= "{$wlength}Connection: close\r\n\r\n$wdata";

		@fwrite($fp,$write);
		while ($data = @fread($fp, 4096)) {
			$responseText .= $data;
		}
		@fclose($fp);
		empty($showagent) && $responseText = trim(stristr($responseText,"\r\n\r\n"),"\r\n");
		return $responseText;
	}




      /*
       *      取得某目錄的檔案
       *      string $dirpath  路徑
       *      string $limit  限制過濾副檔名
       *
      */
      function Folderinfo($dirpath,$limit="") {

		if($limit && !is_array($limit)){
			$limit = explode(",",$limit);
		}


              $arealord = @opendir($dirpath);
              while($dir = @readdir($arealord)) {
                     if($dir && !in_array($dir,array('.','..','.svn','index.htm','index.html','.htaccess'))) {


                           if($limit && in_array(pathinfo($dir,PATHINFO_EXTENSION),$limit)) {
                                    $filename[] = $dir;

                           } else if(!$limit) {

                                    $filename[] = $dir;
                           }
                     }
              }

              closedir($arealord);
              return $filename;
      }





	//路徑轉換
	function escapePath($fileName, $ifCheck = true) {
		if (!security::_escapePath($fileName, $ifCheck)) {
			exit('Forbidden');
		}
		return $fileName;
	}


	//路徑轉換

	function _escapePath($fileName, $ifCheck = true) {
		$tmpname = strtolower($fileName);
		$tmparray = array('://',"\0");
		$ifCheck && $tmparray[] = '..';
		if (str_replace($tmparray, '', $tmpname) != $tmpname) {
			return false;
		}
		return true;
	}

	//目錄轉換

	function escapeDir($dir) {
		$dir = str_replace(array("'",'#','=','`','$','%','&',';'), '', $dir);
		return rtrim(preg_replace('/(\/){2,}|(\\\){1,}/', '/', $dir), '/');
	}



	// $_GET $_POST
	function globalcheck(){
		global $_GET,$_POST;

		foreach ($_POST as $Pkey => $Pvalue) {
                    
			$_POST[$Pkey] = security::secure_input($Pvalue);


		}

		foreach ($_GET as $Gkey => $Gvalue) {
			$_GET[$Gkey] = security::secure_input($Gvalue);
		}

	}



	function GlobalsALL($methoddata){

		if ($methoddata == 'GET') {

			foreach ($_GET as $key => $value) {
				if(isset($_GET[$key])) {
					$GLOBALS[$key] = $_GET[$key];
				}
			}


		} elseif ($methoddata == 'POST') {

			foreach ($_POST as $key => $_value) {

				if(isset($_POST[$key])) {

                              	$GLOBALS[$key] = $_POST[$key];
				}
			}
		}

	}



	/*
	*$arraydata  允許通過變數
	*$methoddata 決定是否GET或POST來通過,無的話就全部通過!
	*/

	function Getglobals($arraydata,$methoddata=ALL,$Chver=flase){

		if(!is_array($arraydata)){
			$arraydata = array($arraydata);
		}

		foreach ($arraydata as $key) {
			if ($key == 'GLOBALS'){
			 	continue;
			}

			$GLOBALS[$key] = NULL;

			if ($methoddata == 'GET' && isset($_GET[$key])) {

				$GLOBALS[$key] = $_GET[$key];

			} elseif ($methoddata == 'POST' && isset($_POST[$key])) {

				$GLOBALS[$key] = $_POST[$key];
			} 

			if($methoddata == 'ALL'){
				if (isset($_POST[$key])) {
					$GLOBALS[$key] = trim($_POST[$key]);
				}
				if (isset($_GET[$key])) {
					$GLOBALS[$key] = trim($_GET[$key]);
				}
			}

			//變數檢查轉換  防 xss  flase => 存在  true=>空
			if(isset($GLOBALS[$key]) && $Chver == 'true'){
				$GLOBALS[$key] = security::Chrconversion($GLOBALS[$key]);
			}
		}

	}

	//字符轉換
	function Chrconversion($string) {
		$string = str_replace(array("\0","%00","\r"), '', $string);
		$string = preg_replace(array('/[\\x00-\\x08\\x0B\\x0C\\x0E-\\x1F]/','/&(?!(#[0-9]+|[a-z]+);)/is'), array('', '&amp;'), $string);
		$string = str_replace(array("%3C",'<'), '&lt;', $string);
		$string = str_replace(array("%3E",'>'), '&gt;', $string);
		$string = str_replace(array('"',"'","\t",'  '), array('&quot;','&#39;','    ','&nbsp;&nbsp;'), $string);
		return $string;
	}



	//檢查允許通過變數,否則一律都刪除
	function Getglobal(){

		//$Variable = array('GLOBALS' => 1,'_GET' => 1,'_POST' => 1,'_REQUEST' => 1,'_COOKIE' => 1,'_SERVER' => 1,'_ENV' => 1,'_FILES' => 1);

		$Variable = array('GLOBALS' => 1,'_GET' => 1,'_POST' => 1,'_COOKIE' => 1,'_SERVER' => 1,'_FILES' => 1,'_REQUEST' => 1);
		foreach ($GLOBALS as $key => $value) {
			if (!isset($Variable[$key])) {
				$GLOBALS[$key] = null;
				unset($GLOBALS[$key]);
			}
		}


		$data= array(
				'HTTP_REFERER',
				'HTTP_HOST',
				'HTTP_X_FORWARDED_FOR',
				'HTTP_USER_AGENT',
				'HTTP_CLIENT_IP',
				'HTTP_SCHEME',
				'HTTPS',
				'PHP_SELF',
				'REQUEST_URI',
				'REQUEST_METHOD',
				'REMOTE_ADDR',
				'SCRIPT_NAME',
				'QUERY_STRING',
				'HTTP_ACCEPT_ENCODING'
		);


		$GLOBALS['GServer'] = security::CodeServer($data);

		if(!$GLOBALS['GServer']['PHP_SELF']){
			$GLOBALS['GServer']['PHP_SELF'] = security::CodeServer('SCRIPT_NAME');
		}


	}

	//變數轉換
	function slashes(&$array) {
		if (is_array($array)) {
			foreach ($array as $key => $value) {
				if (is_array($value)) {
					security::slashes($array[$key]);
				} else {
					$array[$key] = addslashes($value);
				}
			}
		}
	}

	// 加載類文件
	function import($file) {
		if (!is_file($file)) return false;
		require_once $file;
	}


	//html轉換輸出
	function htmlEscape($param) {
		return trim(htmlspecialchars($param, ENT_QUOTES));
	}


	//過濾 HTML 標簽
	function stripTags($param) {
		return trim(strip_tags($param));
	}

	//整型數過濾
	function int($param) {
		return intval($param);
	}

	//字符過濾前後空白
	function str($param) {
		return trim($param);
	}


	//是否數組
	function isArray($params) {
		return (!is_array($params) || !count($params)) ? false : true;
	}


	//變數是否在數組中存在

	function inArray($param, $params) {
		return (!in_array((string)$param, (array)$params)) ? false : true;
	}



	//是否 object
	function isObj($param) {
		return is_object($param) ? true : false;
	}


	//是否是布爾型
	function isBool($param) {
		return is_bool($param) ? true : false;
	}


	//是否是數字型
	function isNum($param) {
		return is_numeric($param) ? true : false;
	}

	
	 //獲取服務器變量

	function CodeServer($data) {
		$server = array();
		$array = (array) $data;
		foreach ($array as $key) {
			$server[$key] = NULL;
			if (isset($_SERVER[$key])) {
				$server[$key] = str_replace(array('<','>','"',"'",'%3C','%3E','%22','%27','%3c','%3e'), '', $_SERVER[$key]);
			}
		}
		return is_array($data) ? $server : $server[$keys];
	}
	


	/*
	*	防 Xss  隱碼攻擊
	*	最後變數都過濾掉無效
	*/

       function sacarXss($val) {
              $val = preg_replace('/([\x00-\x08][\x0b-\x0c][\x0e-\x20])/', '', $val);
              /*
              $search = 'abcdefghijklmnopqrstuvwxyz';
              $search .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
              $search .= '1234567890!@#$%^&*()';
              */
              $search .= '!@#$%^&*()';
              $search .= '~`";:?+/={}[]-_|\'\\';
              for ($i = 0; $i < strlen($search); $i++) {
                     $val = preg_replace('/(&#[x|X]0{0,8}'.dechex(ord($search[$i])).';?)/i', $search[$i], $val); // with a ;
                     $val = preg_replace('/(&#0{0,8}'.ord($search[$i]).';?)/', $search[$i], $val); // with a ;
              }
              $ra1 = array('javascript', 'vbscript', 'expression', 'applet', 'meta', 'xml', 'blink', 'link', 'style', 'script', 'embed', 'object', 'iframe', 'frame', 'frameset', 'ilayer', 'layer', 'bgsound', 'title', 'base');
              $ra2 = array('onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload');
              $ra = array_merge($ra1, $ra2);
              $found = true;
              while ($found == true) {
                     $val_before = $val;
                     for ($i = 0; $i < sizeof($ra); $i++) {
                            $pattern = '/';
                            for ($j = 0; $j < strlen($ra[$i]); $j++) {
                                          if ($j > 0) {
                                                 $pattern .= '(';
                                                 $pattern .= '(&#[x|X]0{0,8}([9][a][b]);?)?';
                                                 $pattern .= '|(&#0{0,8}([9][10][13]);?)?';
                                                 $pattern .= ')?';
                                          }
                                          $pattern .= $ra[$i][$j];
                            }
                            $pattern .= '/i';
                            $replacement = substr($ra[$i], 0, 2).'<x>'.substr($ra[$i], 2);
                            $val = preg_replace($pattern, $replacement, $val);
                            if ($val_before == $val) {
                                   $found = false;
                            }
                     }
              }
              return $val;
       }




	/*
	*	防 sql 及 php  隱碼攻擊
	*	最後變數都過濾掉產生無效
	*/	

	function secure_input($val, $charset = 'UTF-8'){


		if (is_array($val)){
			$output = array();
			foreach ($val as $key => $data){
				$output[$key] = security::secure_input($data, $charset);
			}
			return $output;
	
		}else{


                     if(security::isNum($val)){
                            $val = (int)$val;
                     }


			//防 sql 及  php sell

			$ra1 = array('update','show table','insert into','select','fopen','file','copy','move_uploaded_file',
                     'file_put_contents','fwrite','fputs','passthru','shell_exec','exec','system','mysql_query','mysql_unbuffered_query',
                     'mysql_select_db','mysql_drop_db','mysql_db_query','mysqli_query','mysqli_unbuffered_query',
                     'mysqli_select_db','mysqli_drop_db','mysqli_db_query','sqlite_query','sqlite_exec','sqlite_array_query','and',
                     'sqlite_unbuffered_query','phpinfo','<?php','../');

                     $ra2= array('UPDATE','SHOW TABLE','INSERT INTO','SELECT','FOPEN','FILE','COPY','MOVE_UPLOADED_FILE',
                     'FILE_PUT_CONTENTS','FWRITE','FPUTS','PASSTHRU','SHELL_EXEC','EXEC','SYSTEM','MYSQL_QUERY','MYSQL_UNBUFFERED_QUERY',
                     'MYSQL_SELECT_DB','MYSQL_DROP_DB','MYSQL_DB_QUERY','MYSQLI_QUERY','MYSQLI_UNBUFFERED_QUERY',
                     'MYSQLI_SELECT_DB','MYSQLI_DROP_DB','MYSQLI_DB_QUERY','SQLITE_QUERY','SQLITE_EXEC','SQLITE_ARRAY_QUERY','AND',
                     'SQLITE_UNBUFFERED_QUERY','PHPINFO','<?PHP','../');

                     $sqlf = array_merge($ra1, $ra2);

                     $val = str_replace($sqlf,'', $val);


			// Encode special chars
			//$val = htmlentities($val, ENT_QUOTES, $charset);

			return $val;


		}
	}

	/*
	*
	*      防止重新整理攻擊
	*      說明:未超過規定之內重新整理判斷  cc   攻擊
	*
	*/

	function AntiCC(){
		global $onlineip,$ifmodel,$default_refreshtime;


		if($default_refreshtime){

		              if($ifmodel && $ifmodel == 'NOCC'){
		                     return false;
				}


				//屏障區網

				if($onlineip){
			              $ipdb = explode(".",$onlineip);
					if(in_array($ipdb[0],array('192','127'))){
						return false;
					}
				}

		              $REQUEST_URI = $_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'];


				session_start();
		              $timestamp =time();
				if(isset($_SESSION['lastview'])){
		       		$lastvisit = $_SESSION['lastview']['timestamp'];
		       		$lastpath = $_SESSION['lastview']['REQUEST_URI'];


					if($lastpath && $REQUEST_URI == $lastpath && $timestamp - $lastvisit  < $default_refreshtime) {

		                            //exit("11");
						//警告:系統偵測到使用者異常非法重新整理!
						//目前已經記錄您的IP 位於 國家,將不排除追究法律責任

						$UserIPcountrys = GeoipCheck('2');


						echo "Warning: the system detects user exception illegal rearranged!<BR />";

						echo "Now records your IP '".$onlineip."' is located in ".$UserIPcountrys." country, would not preclude legal";


						unset($_SESSION['lastview']);
						session_unset();
						session_destroy();
						//setcookie('lastview','',$timestamp - 86400);

						exit;


					} else {

						session_unset();
						session_destroy();

		       			$_SESSION['lastview']['timestamp']   = $timestamp;
		       			$_SESSION['lastview']['REQUEST_URI'] = $REQUEST_URI;

		                            session_write_close();
					}


				} else {

		       			$_SESSION['lastview']['timestamp']   = $timestamp;
		       			$_SESSION['lastview']['REQUEST_URI'] = $REQUEST_URI;

		                            session_write_close();

				}

		}

	}

       /*
              自助加解密
       */


       function DisEncryption($str,$encode='encode') {

              if(strlen($str) > 0){

                     if($encode == 'encode'){

                            $ba64 = base64_encode($str);
                            $lengthNum = strlen($ba64)/2;
                            $scor = substr($ba64,0,$lengthNum);
                            $scos = substr($ba64,$lengthNum,strlen($ba64));

                            $extname = strtoupper(md5($ba64));
                            $expr = substr($extname,0,16);
                     	$exps = substr($extname,16,strlen($extname));

                            return $lengthNum.$scor.$expr.$scos.$exps;

                     } else if($encode == 'decode'){

                            if(preg_match("/^\d{0,3}/",$str,$r)) {

                                   $le = strlen($r[0]);

                                   return base64_decode(substr($str,$le,$r[0]).substr($str,($le+$r[0]+16),$r[0]));
                            }
                     }
              }
       }

       function FolderSecurity() {

              //createFolderd(WEB_ROOT."cache/session/");

              if(!in_array(strtolower(PHP_OS),array('win32','winnt','windows'))) {

                     if (substr(sprintf("%o",fileperms(WEB_ROOT."attachment")),-4) != '0777') {
                            chmod(WEB_ROOT."attachment",0777);
                     }

                     if (substr(sprintf("%o",fileperms(WEB_ROOT."cache")),-4) != '0777') {
                            chmod(WEB_ROOT."cache",0777);
                     }

                     if (substr(sprintf("%o",fileperms(WEB_ROOT."template")),-4) != '0700') {
                            chmod(WEB_ROOT."template",0700);
                     }

                     if (substr(sprintf("%o",fileperms(WEB_ROOT."Model")),-4) != '0700') {
                            chmod(WEB_ROOT."Model",0700);
                     }

                     if (substr(sprintf("%o",fileperms(WEB_ROOT."data")),-4) != '0700') {
                            chmod(WEB_ROOT."data",0700);
                     }
                     /*
                     if (substr(sprintf("%o",fileperms(WEB_ROOT."cache/session/")),-4) != '0777') {
                            chmod(WEB_ROOT."cache/session/",0777);
                     }*/
              }


              if(!file_exists(WEB_ROOT."attachment/.htaccess")){
                     $str="<FilesMatch \".(php|asp|jsp|js)$\">\r\nDeny from all\r\n</FilesMatch>";
                     writeover(WEB_ROOT."attachment/.htaccess",$str);
                     $str='';
              }


              if(!file_exists(WEB_ROOT."cache/.htaccess")){
                     $str="<FilesMatch \".(php|asp|jsp|js)$\">\r\nDeny from all\r\n</FilesMatch>";
                     writeover(WEB_ROOT."cache/.htaccess",$str);
                     $str='';
              }
              


       }

}



class L extends security{

       function Loader($fileDir,$filename){

              if(!$fileDir || !$filename){
                     return false;
              }

		if (file_exists(WEB_ROOT.$fileDir.'/'.$filename.'.php')){
                            include security::escapePath(WEB_ROOT.$fileDir.'/'.$filename.'.php');
              }
       }



}

?>