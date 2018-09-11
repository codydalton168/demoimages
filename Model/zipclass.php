<?php

/**
* Zip file creation class.
* Makes zip files.
*
* Last Modification and Extension By :
*
*  Hasin Hayder
*  HomePage : www.hasinme.info
*  Email : countdraculla@gmail.com
*  IDE : PHP Designer 2005
*
*
* Originally Based on :
*
*  http://www.zend.com/codex.php?id=535&single=1
*  By Eric Mueller <eric@themepark.com>
*
*  http://www.zend.com/codex.php?id=470&single=1
*  by Denis125 <webmaster@atlant.ru>
*
*  a patch from Peter Listiak <mlady@users.sourceforge.net> for last modified
*  date and time of the compressed file
*
* Official ZIP file format: http://www.pkware.com/appnote.txt
*
* @access  public
*/
class zipfileclass{
	
		/**
		 * Array to store compressed data
		 *
		 * @var  array    $datasec
		 */
		var $datasec      = array();
		
		/**
		 * Central directory
		 *
		 * @var  array    $ctrl_dir
		 */
		var $ctrl_dir     = array();
		
		/**
		 * End of central directory record
		 *
		 * @var  string   $eof_ctrl_dir
		 */
		var $eof_ctrl_dir = "\x50\x4b\x05\x06\x00\x00\x00\x00";
		
		/**
		 * Last offset position
		 *
		 * @var  integer  $old_offset
		 */
		var $old_offset   = 0;
		
		
              function __construct(){


              }


		/**
		 * Converts an Unix timestamp to a four byte DOS date and time format (date
		 * in high two bytes, time in low two bytes allowing magnitude comparison).
		 *
		 * @param  integer  the current Unix timestamp
		 *
		 * @return integer  the current date in a four byte DOS format
		 *
		 * @access private
		 */

		function unix2DosTime($unixtime = 0) {
		    $timearray = ($unixtime == 0) ? getdate() : getdate($unixtime);
		
		    if ($timearray['year'] < 1980) {
		        $timearray['year']    = 1980;
		        $timearray['mon']     = 1;
		        $timearray['mday']    = 1;
		        $timearray['hours']   = 0;
		        $timearray['minutes'] = 0;
		        $timearray['seconds'] = 0;
		    } // end if
		
		    return (($timearray['year'] - 1980) << 25) | ($timearray['mon'] << 21) | ($timearray['mday'] << 16) |
		            ($timearray['hours'] << 11) | ($timearray['minutes'] << 5) | ($timearray['seconds'] >> 1);
		} // end of the 'unix2DosTime()' method
		


		/**
		 *
		 * Function to force the download of the archive as soon as it is created
		 *
		 * @param archiveName string - name of the created archive file
		 * @access public
		 * @return ZipFile via Header
		 */

		function forceDownload($archiveName) {

			if(ini_get('zlib.output_compression')) {
		        	ini_set('zlib.output_compression', 'Off');
		    	}
		
			// Security checks
			if( $archiveName == "" ) {
				echo "<html><title>Public Photo Directory - Download </title><body><BR><B>ERROR:</B> The download file was NOT SPECIFIED.</body></html>";
				exit;
			} elseif ( ! file_exists( $archiveName ) ) {
				echo "<html><title>Public Photo Directory - Download </title><body><BR><B>ERROR:</B> File not found.</body></html>";
				exit;
			}

                     // addition by Jorg Weske
                     $file_extension = strtolower(substr(strrchr($archiveName,"."),1));



                     switch( $file_extension ){

                       case "pdf": $ctype="application/pdf"; break;
                       case "exe": $ctype="application/octet-stream"; break;
                       case "zip": $ctype="application/zip"; break;
                       case "doc": $ctype="application/msword"; break;
                       case "xls": $ctype="application/vnd.ms-excel"; break;
                       case "ppt": $ctype="application/vnd.ms-powerpoint"; break;
                       case "gif": $ctype="image/gif"; break;
                       case "png": $ctype="image/png"; break;
                       case "jpeg":
                       case "jpg": $ctype="image/jpg"; break;
                       default: $ctype="application/force-download";
                     }







			header("Pragma: public");
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Cache-Control: private",false);
			header("Content-Type: $ctype");
                     header("Content-Disposition: attachment; filename=\"".basename($archiveName)."\";" );
			header("Content-Transfer-Encoding: binary");
			header("Content-Length: ".filesize($archiveName));

                     ob_clean();
                     flush();

			readfile($archiveName);

		}
		
		/**
		 * Adds "file" to archive
		 *
		 * @param  string   file contents
		 * @param  string   name of the file in the archive (may contains the path)
		 * @param  integer  the current timestamp
		 *
		 * @access public
		 */
		function addFile($data, $name, $time = 0){

		    $name     = str_replace('\\', '/', $name);
		
		    $dtime    = dechex($this->unix2DosTime($time));
		    $hexdtime = '\x' . $dtime[6] . $dtime[7]
		              . '\x' . $dtime[4] . $dtime[5]
		              . '\x' . $dtime[2] . $dtime[3]
		              . '\x' . $dtime[0] . $dtime[1];
		    eval('$hexdtime = "' . $hexdtime . '";');
		
		    $fr   = "\x50\x4b\x03\x04";
		    $fr   .= "\x14\x00";            // ver needed to extract
		    $fr   .= "\x00\x00";            // gen purpose bit flag
		    $fr   .= "\x08\x00";            // compression method
		    $fr   .= $hexdtime;             // last mod time and date
		
		    // "local file header" segment
		    $unc_len = strlen($data);
		    $crc     = crc32($data);
		    $zdata   = gzcompress($data);
		    $zdata   = substr(substr($zdata, 0, strlen($zdata) - 4), 2); // fix crc bug
		    $c_len   = strlen($zdata);
		    $fr      .= pack('V', $crc);             // crc32
		    $fr      .= pack('V', $c_len);           // compressed filesize
		    $fr      .= pack('V', $unc_len);         // uncompressed filesize
		    $fr      .= pack('v', strlen($name));    // length of filename
		    $fr      .= pack('v', 0);                // extra field length
		    $fr      .= $name;
		
		    // "file data" segment
		    $fr .= $zdata;
		
		    // "data descriptor" segment (optional but necessary if archive is not
		    // served as file)
		    $fr .= pack('V', $crc);                 // crc32
		    $fr .= pack('V', $c_len);               // compressed filesize
		    $fr .= pack('V', $unc_len);             // uncompressed filesize
		
		    // add this entry to array
		    $this -> datasec[] = $fr;
		
		    // now add to central directory record
		    $cdrec = "\x50\x4b\x01\x02";
		    $cdrec .= "\x00\x00";                // version made by
		    $cdrec .= "\x14\x00";                // version needed to extract
		    $cdrec .= "\x00\x00";                // gen purpose bit flag
		    $cdrec .= "\x08\x00";                // compression method
		    $cdrec .= $hexdtime;                 // last mod time & date
		    $cdrec .= pack('V', $crc);           // crc32
		    $cdrec .= pack('V', $c_len);         // compressed filesize
		    $cdrec .= pack('V', $unc_len);       // uncompressed filesize
		    $cdrec .= pack('v', strlen($name) ); // length of filename
		    $cdrec .= pack('v', 0 );             // extra field length
		    $cdrec .= pack('v', 0 );             // file comment length
		    $cdrec .= pack('v', 0 );             // disk number start
		    $cdrec .= pack('v', 0 );             // internal file attributes
		    $cdrec .= pack('V', 32 );            // external file attributes - 'archive' bit set
		
		    $cdrec .= pack('V', $this -> old_offset ); // relative offset of local header

		    $this -> old_offset += strlen($fr);
		
		    $cdrec .= $name;
		
		    // optional extra field, file comment goes here
		    // save to central directory
		    $this -> ctrl_dir[] = $cdrec;
		} // end of the 'addFile()' method
		
		
		/**
		 * Dumps out file
		 *
		 * @return  string  the zipped file
		 *
		 * @access public
		 */

		function file(){

		    $data    = implode('', $this -> datasec);
		    $ctrldir = implode('', $this -> ctrl_dir);
		
		    return
		        $data .
		        $ctrldir .
		        $this -> eof_ctrl_dir .
		        pack('v', sizeof($this -> ctrl_dir)) .  // total # of entries "on this disk"
		        pack('v', sizeof($this -> ctrl_dir)) .  // total # of entries overall
		        pack('V', strlen($ctrldir)) .           // size of central dir
		        pack('V', strlen($data)) .              // offset to start of central dir
		        "\x00\x00";                             // .zip file comment length
		} // end of the 'file()' method
		
		
		/**
		 * A Wrapper of original addFile Function
		 *
		 * Created By Hasin Hayder at 29th Jan, 1:29 AM
		 *
		 * @param array An Array of files with relative/absolute path to be added in Zip File
		 *
		 * @access public
		 */

		function addFiles($files /*Only Pass Array*/){

			foreach($files as $file){
				//directory check
				if (is_file($file)) {

					$data = implode("",file($file));
		                	$new_file = $this->prefix_name.end(explode('/', $file));
		                	$this->addFile($data, $new_file);
				}
			}
		}
		
		/**
		 * A Wrapper of original file Function
		 *
		 * Created By Hasin Hayder at 29th Jan, 1:29 AM
		 *
		 * @param string Output file name
		 *
		 * @access public
		 */
		
		function output($file){
		    $fp=fopen($file,"w");
		    fwrite($fp,$this->file());
		    fclose($fp);
		}


              function folderToZip($folder, &$zipFile, $exclusiveLength) {
                     $handle = opendir($folder);
                     while (false !== $f = readdir($handle)) {
                            if ($f != '.' && $f != '..') {
                                   $filePath = "$folder/$f";
                                   // Remove prefix from file path before add to zip.
                                   $localPath = substr($filePath, $exclusiveLength);
                                   if (is_file($filePath)) {

                                          $zipFile->addFile($filePath, $localPath);


                                   } elseif (is_dir($filePath)) {
                                          // Add sub-directory.

                                          $zipFile->addEmptyDir($localPath);

                                          $this->folderToZip($filePath, $zipFile, $exclusiveLength);
                                   }
                            }
                     }
                     closedir($handle);
              }

              function zipDir($sourcePath, $outZipPath){

                     $pathInfo = pathInfo($sourcePath);
                     $parentPath = $pathInfo['dirname'];
                     $dirName = $pathInfo['basename'];

                     $z = new ZipArchive();
                     $z->open($outZipPath,ZipArchive::CREATE|ZipArchive::OVERWRITE);
                     $z->addEmptyDir($dirName);
                     $this->folderToZip($sourcePath, $z, strlen("$parentPath/"));
                     $z->close();

              }
		/*
		*
		**      $file    來源路徑檔案
		*       $dir     解壓 路徑
		*       		
		*       $tofile  是否預先解壓某檔案,可用 array('','') 方式,沒有就解壓全部
		*/								


		function unzipoutput($file,$dir='./',$tofile){
	
			if(!$file || !file_exists($file)){
	
				return false;
			}
	
	
			$zip = new ZipArchive();
			$res = $zip->open($file);
			if ($res === TRUE) {
			
				if($tofile){
				   	$zip->extractTo($dir,$tofile);
				} else {
				   	$zip->extractTo($dir);
				}
	    		
	    			
	    			
	    			
	    			$zip->close();
	    			return true;
			} else {
	    			return false;
			}
	
		
		
	
		}

} 
?>