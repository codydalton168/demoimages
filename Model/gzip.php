<?php
!defined('WEB_ROOT') && exit('Forbidden');


class GzipModel{

	// gzip

	/**
	 * 開啟輸出緩存
	 *
	 * @return bool
	 */

	function StartGzip(){
		GzipModel::ObGetMode() == 1 ? ob_start('ob_gzhandler') : ob_start();
	}


	/**
	 * 判斷輸出模式是否為可壓縮
	 *
	 * @global string $default_obstart
	 * @return int 1為可壓縮
	 */

	function ObGetMode() {
		static $sOutputMode = null;
		if ($sOutputMode !== null) {return $sOutputMode;}
		$sOutputMode = 0;
		if ($GLOBALS['default_obstart'] && function_exists('ob_gzhandler') && GzipModel::checkoutputzip() != 'ob_gzhandler' && (!function_exists('ob_get_level') || ob_get_level() < 1)) {
			$sOutputMode = 1;
		}
		return $sOutputMode;
	}




	/**
	 * 壓縮內容，並設置響應頭為壓縮格式
	 *
	 * @global string $default_obsize
	 * @param string $output 要壓縮的內容
	 * @return string
	 */
	function GzipExport($output){
              $output = trim($output);
		ob_end_clean();

		$getENCODING =  security::CodeServer('HTTP_ACCEPT_ENCODING');

		if(!headers_sent() && $GLOBALS['default_obsize'] && $getENCODING && GzipModel::checkoutputzip() != 'ob_gzhandler'){
			$encoding = '';
			if (strpos($getENCODING, 'x-gzip') !== false) {
				$encoding = 'x-gzip';
			} elseif (strpos($getENCODING, 'gzip') !== false) {
				$encoding = 'gzip';
			}

			if($encoding && function_exists('crc32') && function_exists('gzcompress')){
				header('Content-Encoding: '.$encoding);
				$outputlen  = strlen($output);
				$outputzip  = "\x1f\x8b\x08\x00\x00\x00\x00\x00";
				$outputzip .= substr(gzcompress($output,$GLOBALS['default_obsize']),0,-4);
				$outputzip .= @pack('V',crc32($output));
				$output = $outputzip.@pack('V',$outputlen);
			} else {
				GzipModel::StartGzip();
			}
		} else {
			GzipModel::StartGzip();
		}
		return $output;
	}


	/*
		清除 GZIP  快取
	*/
	function claseGZIP($ob = null) {

		if (php_sapi_name() != 'apache2handler' && php_sapi_name() != 'apache2filter') {
			if (GzipModel::checkoutputzip() == 'ob_gzhandler') {return;}
			if ($ob && ob_get_length() !== false && ob_get_status() && !GzipModel::ObGetMode($GLOBALS['default_obstart'])) {
				@ob_flush();
			}
			flush();
		}


	}
	/*
	撿查是否支援 GZIP
	*/
	function checkoutputzip() {
		static $sOutputHandler = null;
		if ($sOutputHandler === null) {
			if (@ini_get('zlib.output_compression')) {
				$sOutputHandler = 'ob_gzhandler';
			} else {
				$sOutputHandler = @ini_get('output_handler');
			}
		}
		return $sOutputHandler;
	}

	// gzip

}



?>