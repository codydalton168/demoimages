<?php
!defined('WEB_ROOT') && exit('Forbidden');

Class DBERROR {

	function DBERROR($debugmsg) {
		global $default_name,$onlineip,$db,$default_obstart,$default_host,$REQUEST_URI,$default_charset;

		include(WEB_ROOT.'Model/sql_error_msg.php');


		/*
		*  判斷 IP  是否顯示資料詳細錯誤訊息
		*
		*   列如 一組 IP  114.35.22.14  或  網段 114.35.22
		*/

		$checkerrormsg='0';

		$default_selectip=array('192','127');


		if(count($default_selectip) > 0){


			$ip = explode(".",$onlineip);

			if(in_array($ip[0],$default_selectip) || in_array($ip[0].'.'.$ip[1],$default_selectip) || in_array($ip[0].'.'.$ip[1].'.'.$ip[2],$default_selectip) || in_array($ip[0].'.'.$ip[1].'.'.$ip[2].'.'.$ip[3],$default_selectip)) {
				$checkerrormsg='1';
			}
		}




		$sqlerror = mysqli_error($db->MysqliDB);

		$sqlerrno = mysqli_errno($db->MysqliDB);

		$sqlerror = str_replace($default_host,'default_host',$sqlerror);
	
		ob_end_clean();

		$default_obstart && function_exists('ob_gzhandler') ? ob_start('ob_gzhandler') : ob_start();
		
		echo"<!DOCTYPE HTML>\n<head>\n<meta charset='utf-8'/>\n";
		echo"<title>Error Messages</title>\n";
		echo"<style type='text/css'>\n";
		echo"P,BODY{FONT-FAMILY:tahoma,arial,sans-serif;FONT-SIZE:12px;}\n";
		echo"a{ TEXT-DECORATION: none;}\n";
		echo"a:hover{ text-decoration: underline;}\n";
		echo"table{TABLE-LAYOUT:fixed;WORD-WRAP: break-word;padding: 10px;}\n";
		echo"td{ BORDER-RIGHT: 1px; BORDER-TOP: 0px; FONT-SIZE: 16pt; COLOR: #000000;}\n";
		echo ".rounded-corners {margin: 50px auto 0px auto;border: 1px solid #ccc;width:800px;-moz-border-radius: 10px;-webkit-border-radius: 10px;-khtml-border-radius: 10px;border-radius: 10px;}\n";
		echo"</style>\n<body>\n";
		echo"<div class='rounded-corners'>\n<table>\n<tr>\n\t<td>";
		echo"<b style='text-align:left;margin:0px 0px 0px 5px;'>Error Message</b><br /><br /><div style='margin-left:0px;'>";
		if($checkerrormsg){
			echo "$debugmsg <br />";
		}

		if($sqlerror && $sqlerrno){

			if($checkerrormsg){

				echo"$sqlerror<br /><br />";
			}

			echo"錯誤訊息代碼 : $sqlerrno<br />";

			if($checkerrormsg){

				echo"描述訊息 : <b>".($errorData[$sqlerrno] ? $errorData[$sqlerrno] : '')."</b><br /><br />";
			}
		}

		echo"\n\t</div></td>\n</tr>\n</table></div>\n";
		echo"</body>\n</html>";

		//$this->dblog($debugmsg.'|'.$sqlerror.'|'.$sqlerrno.'|'.$errorData[$sqlerrno]);

		exit;
	}



	function dblog($msg){
		$msg = str_replace(array("\n","\r","<"),array('','','&lt;'),$msg);
		if (file_exists(WEB_ROOT.'cache/dblog.txt')){
			writeover(WEB_ROOT.'cache/dblog.txt',"$msg\n", 'ab');
		} else{
			writeover(WEB_ROOT.'cache/dblog.txt',"\n$msg\n");
		}
	}


}
?>