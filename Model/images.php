<?php
!defined('WEB_ROOT') && exit('Forbidden');



class imageModel{

              function UploadEffects($upname='productimg'){

                     $savedir = date('Ym',time());

                     createFolderd(WEB_ROOT.'attachment/'.$savedir);

                     if(!$upname){
                            return false;
                     }

              	if(is_uploaded_file($_FILES[$upname]['tmp_name'])){

                            $upimages_name = $_FILES[$upname]['name'];

              		$upimages_type = $_FILES[$upname]['type'];

              		$upimages_temp = $_FILES[$upname]['tmp_name'];

              		$upimagestype = strtolower(substr(strrchr($upimages_name,'.'),1));

                            $filename= substr(bin2hex(md5($GLOBALS['timestamp'].mt_rand(1,1000).$GLOBALS['admincpid'])),0,10).".".$upimagestype;

                            $upatch =   "attachment/$savedir/$filename";
     
              		@move_uploaded_file($upimages_temp,WEB_ROOT.$upatch);

                            if($GLOBALS['default_bswidth'] >0 && $GLOBALS['default_bsheight']>0){
                                   imageModel::Losslesscut($upatch,$GLOBALS['default_bswidth'], $GLOBALS['default_bsheight'],100);
                            }
                     }


                     if($GLOBALS['default_wateopen'] && $GLOBALS['default_thumb_wate'] && $upatch){


                            if($GLOBALS['default_wateopen'] == 1){



                                   imageModel::TextWatermark($upatch,$GLOBALS['default_watermark_location'],false,$GLOBALS['default_watermark_text'],WEB_ROOT."Model/fonts/".$GLOBALS['default_watermark_ttf'],$GLOBALS['default_watermark_size'],$GLOBALS['default_watermark_color']);


                            } else if($GLOBALS['default_wateopen'] == 2){


                                   imageModel::imageWaterMark($upatch,$GLOBALS['default_watermark_location'],$GLOBALS['default_wate_img']);


                            }

                     }


                     if($upatch){
                            return $upatch;
                     }


              }



              /*
              *   TextWatermark("��l�ɮ׸��|","��m(0~9)","�r���O�_�䷽ utf-8","�B���L����r","�r�����|��m","�r���j�p","�r���C��");
              */


              function TextWatermark($groundImage,$waterPos=0,$iconv="false",$waterText="",$fontsfile="",$textFont="12",$textColor="#1E90FF"){

                     $formatMsg = "�Ȥ�����Ӥ��榡�A�ХιϤ��B�z�n��N�Ϥ��ഫ�� GIF�BJPG�BPNG�榡�C";

                     //Ū���I���Ϥ�
                     if(!empty($groundImage) && file_exists($groundImage)){
                            $ground_info = getimagesize($groundImage);
                            $ground_w    = $ground_info[0];//���o�I���Ϥ����e
                            $ground_h    = $ground_info[1];//���o�I���Ϥ�����
                            switch($ground_info[2]){//���o�I���Ϥ����榡

                                   case 1:$ground_im = imagecreatefromgif($groundImage);break;
                                   case 2:$ground_im = imagecreatefromjpeg($groundImage);break;
                                   case 3:$ground_im = imagecreatefrompng($groundImage);break;
                                   default:die($formatMsg);
                            }

                     } else {

                            die("�ݭn�[�B���L���Ϥ����s�b�I");
                     }

                     if($iconv){
                            $waterText =  iconv("big5","UTF-8//TRANSLIT//IGNORE",$waterText);
                     }

                     //exit("$fontsfile");
                     //��r�B���L
                     !$fontsfile && $fontsfile = WEB_ROOT."Model/fonts/chinesew12.ttc";
   
                     $temp = imagettfbbox($textFont,0,$fontsfile,$waterText);  //���o�ϥ� TrueType �r�骺�奻���d��

                     $w = $temp[2] - $temp[6];
                     $h = $temp[3] - $temp[7];

                     unset($temp);

   
                     if(($ground_w<$w) || ($ground_h<$h)){
                            echo "�ݭn�[�B���L���Ϥ������שμe�פ�B���L".$label."�٤p�A�L�k�ͦ��B���L�I";
                            return;
                     }


                     switch($waterPos){
                            case 0://�H��
                                   $posX = rand(0,($ground_w - $w));
                                   $posY = rand(0,($ground_h - $h));
                            break;
                            case 1://1�����ݩ~��

                                   $posX = 0;
                                   $posY = $textFont + 10;
                            break;
                            case 2://2�����ݩ~��

                                   $posX = (int)($ground_w - $w)/2;
                                   $posY = $textFont + 10;
                            break;
                            case 3://3�����ݩ~�k
                                   $posX = $ground_w - $w - 30;
                                   $posY = $textFont + 10;
                            break;
                            case 4://4�������~��
                                   $posX = 0;
                                   $posY = ($ground_h - $h) / 2;
                            break;
                            case 5://5�������~��
                                   $posX = ($ground_w - $w) / 2;
                                   $posY = ($ground_h - $h) / 2;
                            break;
                            case 6://6�������~�k
                                   $posX = $ground_w - $w - 30;
                                   $posY = ($ground_h - $h) / 2;
                            break;
                            case 7://7�����ݩ~��
                                   $posX = 0;
                                   $posY = $ground_h - $h;
                            break;
                            case 8://8�����ݩ~��
                                   $posX = ($ground_w - $w) / 2;
                                   $posY = $ground_h - $h;
                            break;
                            case 9://9�����ݩ~�k
                                   $posX = $ground_w - $w - 30;
                                   $posY = $ground_h - $h;
                            break;
                            default://�H��
                                   $posX = rand(0,($ground_w - $w));
                                   $posY = rand(0,($ground_h - $h));
                            break;
                     }
                     //��r�B���L
                     if(!empty($textColor) && (strlen($textColor)==7)){

                            $R = hexdec(substr($textColor,1,2));
                            $G = hexdec(substr($textColor,3,2));
                            $B = hexdec(substr($textColor,5));

                     } else {

                            die("�B���L��r�C��榡�����T�I");
                     }


                     imagettftext($ground_im,$textFont, 0, $posX,$posY, imagecolorallocate($ground_im,$R,$G,$B), $fontsfile,$waterText);


                     //ImageTTFText ($ground_im,$textFont, 0,$posX-10,$posY+10, imagecolorallocate($ground_im,$R,$G,$B), $fontsfile, mb_convert_encoding($waterText,'utf-8','big5'));




                     //ImageTTFText($ground_im, $textFont, 0, $posX-10,$posY+10,imagecolorallocate($ground_im,$R,$G,$B),$fontsfile,mb_convert_encoding($waterText,'utf-8','big5'));


                     //�ͦ��B���L�᪺�Ϥ�
                     @unlink($groundImage);
                     switch($ground_info[2]){//���o�I���Ϥ����榡
                            case 1:imagegif($ground_im,$groundImage);break;
                            case 2:imagejpeg($ground_im,$groundImage,100);break;
                            case 3:imagepng($ground_im,$groundImage);break;
                            default:die($errorMsg);
                     }

                     //���񤺦s
                     if(isset($water_info)) unset($water_info);
                     if(isset($water_im)) imagedestroy($water_im);
                     unset($ground_info);
                     imagedestroy($ground_im);
              }


              /*
              *    �Ϥ��B���L
              *   imageWaterMark("��l�ɮ׸��|","��m(0~9)","�n�X���B���L�Ϥ����|");
              */

              function imageWaterMark($groundImage,$waterPos=0,$waterImage=""){
                     $isWaterImage = FALSE;
                     $formatMsg = "�Ȥ�����Ӥ��榡�A�ХιϤ��B�z�n��N�Ϥ��ഫ�� GIF�BJPG�BPNG�榡�C";


                     if(empty($waterImage) || !file_exists(WEB_ROOT.$waterImage)){
                            return false;
                     }


                     //Ū�����L���
                     if(!empty($waterImage) && file_exists(WEB_ROOT.$waterImage)){
                            $isWaterImage = TRUE;
                            $water_info = getimagesize(WEB_ROOT.$waterImage);
                            $water_w    = $water_info[0];//���o���L�Ϥ����e
                            $water_h    = $water_info[1];//���o���L�Ϥ�����



                            switch($water_info[2]){//���o���L�Ϥ����榡
                                   case 1:$water_im = imagecreatefromgif(WEB_ROOT.$waterImage);break;
                                   case 2:$water_im = imagecreatefromjpeg(WEB_ROOT.$waterImage);break;
                                   case 3:$water_im = imagecreatefrompng(WEB_ROOT.$waterImage);break;
                                   default:die($formatMsg);
                            }
                     }
                     //Ū���I���Ϥ�
                     if(!empty($groundImage) && file_exists(WEB_ROOT.$groundImage)){
                            $ground_info = getimagesize(WEB_ROOT.$groundImage);
                            $ground_w    = $ground_info[0];//���o�I���Ϥ����e
                            $ground_h    = $ground_info[1];//���o�I���Ϥ�����
                            switch($ground_info[2]){//���o�I���Ϥ����榡

                                   case 1:$ground_im = imagecreatefromgif(WEB_ROOT.$groundImage);break;
                                   case 2:$ground_im = imagecreatefromjpeg(WEB_ROOT.$groundImage);break;
                                   case 3:$ground_im = imagecreatefrompng(WEB_ROOT.$groundImage);break;
                                   default:die($formatMsg);
                            }

                     } else {

                            die("�ݭn�[�B���L���Ϥ����s�b�I");
                     }



                     //���L��m

                     //�Ϥ����L
                     $w = $water_w;
                     $h = $water_h;



                     if(($ground_w<$w) || ($ground_h<$h)){
                            echo "�ݭn�[�B���L���Ϥ������שμe�פ�B���L�Ϥ����٤p�A�L�k�ͦ��B���L�I";
                            return;
                     }


                     switch($waterPos){
                            case 0://�H��
                                   $posX = rand(0,($ground_w - $w));
                                   $posY = rand(0,($ground_h - $h));
                            break;
                            case 1://1�����ݩ~��

                                   $posX = 0;
                                   $posY = $textFont + 10;
                            break;
                            case 2://2�����ݩ~��

                                   $posX = (int)($ground_w - $w)/2;
                                   $posY = $textFont + 10;
                            break;
                            case 3://3�����ݩ~�k
                                   $posX = $ground_w - $w - 30;
                                   $posY = $textFont + 10;
                            break;
                            case 4://4�������~��
                                   $posX = 0;
                                   $posY = ($ground_h - $h) / 2;
                            break;
                            case 5://5�������~��
                                   $posX = ($ground_w - $w) / 2;
                                   $posY = ($ground_h - $h) / 2;
                            break;
                            case 6://6�������~�k
                                   $posX = $ground_w - $w - 30;
                                   $posY = ($ground_h - $h) / 2;
                            break;
                            case 7://7�����ݩ~��
                                   $posX = 0;
                                   $posY = $ground_h - $h;
                            break;
                            case 8://8�����ݩ~��
                                   $posX = ($ground_w - $w) / 2;
                                   $posY = $ground_h - $h;
                            break;
                            case 9://9�����ݩ~�k
                                   $posX = $ground_w - $w - 30;
                                   $posY = $ground_h - $h;
                            break;
                            default://�H��
                                   $posX = rand(0,($ground_w - $w));
                                   $posY = rand(0,($ground_h - $h));
                            break;
                     }


                     //�]�w�Ϲ����V��Ҧ�
                     imagealphablending($ground_im, true);

                     //�Ϥ��B���L
                     imagecopy($ground_im, $water_im, $posX, $posY, 0, 0, $water_w,$water_h);//�����B���L��ؼФ��


                     //�ͦ��B���L�᪺�Ϥ�
                     @unlink($groundImage);
                     switch($ground_info[2]){//���o�I���Ϥ����榡

                            case 1:imagegif($ground_im,$groundImage);break;
                            case 2:imagejpeg($ground_im,$groundImage,100);break;
                            case 3:imagepng($ground_im,$groundImage);break;
                            default:die($errorMsg);
                     }

                     //���񤺦s
                     if(isset($water_info)) unset($water_info);
                     if(isset($water_im)) imagedestroy($water_im);
                     unset($ground_info);
                     imagedestroy($ground_im);
              }




              /*
              *    �̤j�p��C�Y��
              *    ImageResize($from_filename, $save_filename, $in_width=180, $in_height=180, $quality=80){
              *                 �ӷ����|         �s�x���|       �e               ��               ��q
              *
              */


              function ImageResize($from_filename, $save_filename, $in_width=180, $in_height=180, $quality=80){
              	$allow_format = array('jpg', 'png', 'gif');
              	$sub_name = $t = '';
              	$upimagestype = strtolower(substr(strrchr($from_filename,'.'),1));
              	if(!in_array($upimagestype,$allow_format)){
              		return false;
              	}

              	// Get new dimensions
               	$img_info = getimagesize(WEB_ROOT.$from_filename);
              	$width    = $img_info['0'];
              	$height   = $img_info['1'];
              	$imgtype  = $img_info['2'];
              	$imgtag   = $img_info['3'];
              	$bits     = $img_info['bits'];
              	$channels = $img_info['channels'];
              	$mime     = $img_info['mime'];


              	// ���o�Y�b���d�򤺪����
              	$percent = imageModel::getResizePercent($width, $height, $in_width, $in_height);
              	$new_width  = $width * $percent;
              	$new_height = $height * $percent;

                     // Resample
                     $image_new = imagecreatetruecolor($new_width, $new_height);

              	if($upimagestype == "jpg"){

              		    	$image = imagecreatefromjpeg($from_filename);
                  			imagecopyresampled($image_new, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
                  			imagejpeg($image_new, WEB_ROOT.$save_filename, $quality);
              			imagedestroy($image_new);

              	} else if($upimagestype == "png"){


              		    	$image = imagecreatefrompng($from_filename);
                  			imagecopyresampled($image_new, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
                  			imagepng($image_new, WEB_ROOT.$save_filename);
              			imagedestroy($image_new);

              	} else if($upimagestype == "gif"){

              		    	$image = imagecreatefromgif($from_filename);
                  			imagecopyresampled($image_new, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
                  			imagegif($image_new, WEB_ROOT.$save_filename);
              			imagedestroy($image_new);
              	}

              }


              function getResizePercent($source_w, $source_h, $inside_w, $inside_h){
                  if ($source_w < $inside_w && $source_h < $inside_h) {
                      return 1; // Percent = 1, �p�G����w�p�Y�Ϫ��p�N�����Y
                  }
                  $w_percent = $inside_w / $source_w;
                  $h_percent = $inside_h / $source_h;
                  return ($w_percent > $h_percent) ? $h_percent : $w_percent;
              }

              /*
                     ���ŹϤ�

              */
              function cut($filename,$Purpose='false',$waterPos='5',$width,$height){
              	$allow_format = array('jpg', 'png', 'gif');
              	$upimagestype = strtolower(substr(strrchr($filename,'.'),1));
              	if(!in_array($upimagestype,$allow_format)){
              		return false;
              	}

                     if(file_exists($filename)){

                            $ground_info = getimagesize($filename);
                            $ground_w    = $ground_info[0];//���o�I���Ϥ����e
                            $ground_h    = $ground_info[1];//���o�I���Ϥ�����

                            $src = imagecreatefromstring(file_get_contents($filename));

                            if(strpos($waterPos,"|")!==false){
                                   list($cutex,$cutey) = explode("|",$waterPos);
                            } 


                            switch($waterPos){
                                   case 0://�H��
                                          $x = rand(0,($ground_w - $width));
                                          $y = rand(0,($ground_h - $height));
                                   break;
                                   case 1://1�����ݩ~��

                                          $posX = 0;
                                          $posY = $textFont + 10;
                                   break;
                                   case 2://2�����ݩ~��

                                          $posX = (int)($ground_w - $w)/2;
                                          $posY = $textFont + 10;
                                   break;
                                   case 3://3�����ݩ~�k
                                          $posX = $ground_w - $w - 30;
                                          $posY = $textFont + 10;
                                   break;
                                   case 4://4�������~��
                                          $posX = 0;
                                          $posY = ($ground_h - $h) / 2;
                                   break;
                                   case 5://5�������~��
                                          $posX = ($ground_w - $w) / 2;
                                          $posY = ($ground_h - $h) / 2;
                                   break;
                                   case 6://6�������~�k
                                          $posX = $ground_w - $w - 30;
                                          $posY = ($ground_h - $h) / 2;
                                   break;
                                   case 7://7�����ݩ~��
                                          $posX = 0;
                                          $posY = $ground_h - $h;
                                   break;
                                   case 8://8�����ݩ~��
                                          $posX = ($ground_w - $w) / 2;
                                          $posY = $ground_h - $h;
                                   break;
                                   case 9://9�����ݩ~�k
                                          $posX = $ground_w - $w - 30;
                                          $posY = $ground_h - $h;
                                   break;
                                   default://�ۭq
                                          $x = $cutex;
                                          $y = $cutey;
                                   break;
                            }

                            //�̲׫O�s���Ϥ����e�M���A�M���n����ҡA�_�h�|�ܧ�
                            $final_width = 100;
                            $final_height = round($final_width * $height / $width);


                            // �ŵ�
                            //�N���Űϰ�ƻs��s�Ϥ��W�A�îھڷ��M�ؼЪ��e���i���Y��Ϊ̩Ԥ�
                            $new_image = imagecreatetruecolor($final_width, $final_height);
                            imagecopyresampled($new_image, $src, 0, 0, $x, $y, $final_width, $final_height, $width, $height);



                            if($upimagestype == "jpg"){

                                   imagejpeg($new_image, $Purpose, 100);

                            } else if($upimagestype == "png"){

           			       imagepng($new_image, $Purpose);

                            } else if($upimagestype == "gif"){

           			       imagegif($new_image, $Purpose);
                            }
                            imagedestroy($src);
                            imagedestroy($new_image);


                     }
              }

              /*
              * �L�l���ŹϤ�
              * �Y�ϵ��ťX���w�ϰ�Τ@�j�p�ؤo�榡
              */

              function Losslesscut($filename,$Purpose='false',$width,$height){

              	$allow_format = array('jpg', 'png', 'gif');
              	$upimagestype = strtolower(substr(strrchr($filename,'.'),1));
              	if(!in_array($upimagestype,$allow_format)){
              		return false;
              	}

                     if(file_exists($filename)){

                            $imgstream = file_get_contents($filename);

                     	//list($w, $h, $type, $attr) = getimagesize($filename);

                            $im = imagecreatefromstring($imgstream);

                            $w = imagesx($im);
                            $h = imagesy($im);



                            if($w > $width && $h > $height){

                                   if($w>$h){
                                          //�Ϥ��e�j��
                                          $sx = abs(($h-$w)/2);
                                          $sy = 0;
                                          $thumbw = $h;
                                          $thumbh = $h;
                                   } else {
                                          //�Ϥ����j�󵥩�e
                                          $sy = abs(($w-$h)/2.5);
                                          $sx = 0;
                                          $thumbw = $w;
                                          $thumbh = $w;
                                   }

                                   if(function_exists("imagecreatetruecolor")) {
                                          $dim = imagecreatetruecolor($width,$height);
                                   } else {
                                          $dim = imagecreate($width,$height);
                                   }

                                   imageCopyreSampled($dim,$im,0,0,$sx,$sy,$width,$height,$thumbw,$thumbh);


                                   if($upimagestype == "jpg"){

                                          imagejpeg($dim, $Purpose, 100);


                                   } else if($upimagestype == "png"){

                  			       imagepng($dim, $Purpose);

                                   } else if($upimagestype == "gif"){

                  			       imagegif($dim, $Purpose);
                                   }

                     		imagedestroy($dim);


                            }


                     }

              }


              function ManyImageMerge($arraydata,$Purpose='false'){
       		if(!is_array($arraydata)){
       			$arraydata = array($arraydata);
       		}

                     foreach($arraydata as $key=>$imag){
                            list($ground_w,$ground_h)= getimagesize(WEB_ROOT.$imag);

                            if($ground_w > 800 && $ground_h > 400){
                                   $ground_h= 400;
                            }

                            $heightauto+= $ground_h + 50;
                     }
                     $heightauto+=50;


                     $dests =imagecreatetruecolor(800,$heightauto);
                     $white=imagecolorallocate($dests,255,255,255);

                     imagecolortransparent($dests,$white);
                     //imagefill($dests,0,0,$white);
                     imagealphablending($dests, false);
                     imagesavealpha($dests, true);
                     /*
                     list($ground_w,$ground_h)= getimagesize(WEB_ROOT.'c1.jpg');
                     $dest_x = (800/2) - ($ground_w/2);
                     $dest_y = ($ground_h/2)+50;

                     $source = imagecreatefromjpeg(WEB_ROOT.'c1.jpg');
                     imagecopy($dests,$source,0,0,-$dest_x,-50,800,$heightauto);
                     imagefill($dests,0,0,$white);
                     imagedestroy($source);
                     */

                     $desty=0;
                     foreach($arraydata as $key => $imag){
                                   list($ground_w,$ground_h)= getimagesize(WEB_ROOT.$imag);

                                   $dest_x = (800/2) - ($ground_w/2);

                                   if($ground_w < 800 && $ground_h < 400){

                                          if($key == 0){
                                                 $dest_y = 50;
                                                 $desty+= 100 + $ground_h;
                                          } else {

                                                 $dest_y = $desty;
                                                 $desty +=  50 + $ground_h;
                                          }

                                          $upimagestype = strtolower(substr(strrchr($imag,'.'),1));
                                   	if($upimagestype == "jpg"){
                                                 $source = imagecreatefromjpeg(WEB_ROOT.$imag);
                                   	} else if($upimagestype == "png"){
                                                 $source = imagecreatefrompng(WEB_ROOT.$imag);
                                   	} else if($upimagestype == "gif"){
                                                 $source = imagecreatefromgif(WEB_ROOT.$imag);
                                   	}

                                   } else {

                                                 $im = imagecreatefromstring(file_get_contents(WEB_ROOT.$imag));
                                                 $w = imagesx($im);
                                                 $h = imagesy($im);

                                                 $source = imagecreatetruecolor(800, 400);

                                                 imageCopyreSampled($source,$im,0,0,0,0,800,400,$w,$h);

                                                 $dest_x = (800/2) - (800/2);

                                                 if($key == 0){

                                                        $dest_y = 50;
                                                        $desty+= 100 + 400;
                                                 } else {
                                                        $dest_y = $desty;
                                                        $desty +=  50 + 400;
                                                 }

                                   }

                                   imagealphablending($source, false);
                                   imagesavealpha($source, true);

                                   imagecopy($dests,$source,$dest_x,$dest_y,0,0,800,$heightauto);
                                   
                                   imagedestroy($source);

                     }


                     //imagedestroy($source);
                     //imagecolortransparent($dests, $white);
                     imagefill($dests,0,0,$white);
                     ImageJpeg($dests,$Purpose,100);
                     imagedestroy($dests);
              }

}



?>