<?php
!defined('WEB_ROOT') && exit('Forbidden');
//cookie �[�K key
$cookiekey = "@#$%^&*".$GServer[HTTP_HOST]."@#$%^&*";

class CookieModel{

       function GethttpsSecure(){

              $httpsSecure = false;

		if (!$GServer['REQUEST_URI'] || ($https = @parse_url($GServer['REQUEST_URI']))===false) {
			$https = array();
		}


		if ($https['scheme']=='https' || (empty($https['scheme']) && ($GServer['HTTP_SCHEME']=='https' || $GServer['HTTPS'] && strtolower($GServer['HTTPS'])!='off'))) {
			$httpsSecure = true;
		}

		return $httpsSecure;
	}



	function GetAdminUser($ckVar){

		$ckHttponly = false;

		if ($ckVar=='AdminUser') {
			$httpagent = strtolower($GServer['HTTP_USER_AGENT']);
			if (!($httpagent && preg_match('/msie ([0-9]\.[0-9]{1,2})/i', $httpagent) && strstr($httpagent, 'mac'))) {
				$ckHttponly = true;
			}
		}

		return $ckHttponly;
	}

	function ShowCookie($ckVar,$ckValue,$ckTime='F'){
		global $timestamp,$Cookiepath,$Cookiedomain,$GServer;

		//cookie �x�s�ɮ�  �@�~ 31536000     �@�Ӥ� 2592000  �@�� 86400  1 �p�� 3600    �Y��  0


              $httpsSecure = CookieModel::GethttpsSecure();

              $ckHttponly = CookieModel::GetAdminUser($ckVar);

              $ckVar = CookieModel::GetCodeEcy($ckVar);

		if ($ckTime=='F') {

			$ckTime = $timestamp+31536000;

              //�C 6 �Ѩ��
		} else if ($ckTime=='6day') {

			$ckTime = $timestamp+518400;
              //�C�� 1 �����
		} else if ($ckTime=='month') {

			$ckTime = $timestamp+518400;
              //10 �~���
		} else if ($ckTime=='G') {

			$ckTime = $timestamp+315360000; // 10 �~

              //�{�b�}�l 24 �p�ɨ�
		} else if ($ckTime=='24H') {

			$ckTime = $timestamp+86400;

              //�{�b�}�l����12:00:00���

		} else if ($ckTime=='24E') {

                     $ckTime = strtotime(date('Y-m-d',$timestamp+86400));

		} elseif ($ckValue=='' && $ckTime==0) {

			return setcookie($ckVar,'',$timestamp-31536000,$Cookiepath,$Cookiedomain,$httpsSecure,$ckHttponly);

		} 


		return setcookie($ckVar,$ckValue,$ckTime,$Cookiepath,$Cookiedomain,$httpsSecure,$ckHttponly);


	}


	function GetCodeEcy($Var){

		$ckVar = substr(md5($Var.CookieModel::CookieKEY()),0,8);
		return $ckVar;
	}


	function GetCookie($ckVar){
		global $timestamp;

              $ckVar = CookieModel::GetCodeEcy($ckVar);

		return $_COOKIE[$ckVar];
	}

	function CookieKEY(){
		global $GServer,$cookiekey;
		$varuse = $GServer[HTTP_HOST].$cookiekey;
		return substr(md5($varuse),0,5);
	}

	function ValueEncryption($string,$action='ENCODE'){
		global $GServer,$cookiekey;

		$very= $GServer[HTTP_HOST].$cookiekey;

		//$GServer["HTTP_USER_AGENT"].$very

		$lensize = md5($very);

		$key	= substr($lensize,8,18);

		if($action == 'ENCODE'){
			$string = $string;
		} else {
			$code = substr($string,10,strlen($string)-22);
			$code = str_replace("%","=",$code);
			$string = base64_decode($code);
		}

		$len	= strlen($key);
		$code	= '';
		for($i=0; $i<strlen($string); $i++){
			$k = $i % $len;
			$code.= $string[$i] ^ $key[$k];
		}

		if($action == 'DECODE'){
			$code =$code;
		} else {
			$code = strtoupper(substr($lensize,0,10)).base64_encode($code).strtoupper(substr($lensize,22,strlen($very)));
			$code = str_replace("=","%",$code);
		}

		return $code;
	}

}



?>