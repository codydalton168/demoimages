<?php

class Captcha{

	//預設字形
       var $font = 'Model/VeraSansBold.ttf';

	//高度
       var $height= '50';

	//寬度
       var $width = '140';

	//預定產生 4 位驗証碼
       var $characters = '4';

	//字型大小
       var $font_size = '30';

	//產生類型 true  字型深顏色  false 隨即顏色
       var $fontcolortype = false;


	//背景產生類別
       var $Backgroundtype = '0';

	//是否產生背景填充
       var $Backgroundopen = false;
	/*
           自訂義背景顏色
	    $Backgroundtype = 1  $Backgroundcolor  才會生效
	*/
       var $Backgroundcolor = '#ffffff';

	//產生干擾點
       var $NoisePoint = false;

	//產生干擾線
       var $NoiseLine = false;

	//產生框
       var $ShowBorder = false;

	var $colorlistarray = array();

	//字型扭區
	var $Crooked = false;

	function Captcha(){
              $this->colorlistarray =array(
			'#000000',
			'#2F4F4F',
			'#191970',
			'#0000CD',
			'#9932CC',
			'#836FFF',
			'#0000FF',
			'#00008B',
			'#8B0000',
			'#8B008B',
			'#5D478B',
			'#551A8B',
			'#68228B',
			'#8B2252',
			'#FF3030',
			'#8B658B',
			'#008B45',
			'#00868B',
			'#FF0000',
			'#FF1493',
			'#FF8C00',
			'#B22222',
			'#9932CC'
		);

	}


	function createcode(){

              $this->Backgroundcolors = sscanf($this->Backgroundcolor, '#%2x%2x%2x');

		$fonty = array("0","10","20","30","-10","-20","-30");


		$cok = array("0","15","25","35","75");

		$code = $this->generateCode($this->characters);

		$image = @imagecreate($this->width, $this->height) or die('Cannot initialize new GD image stream');


		if($this->Backgroundopen){

                     if($this->Backgroundtype == 1){

				imagecolorallocate($image,$this->Backgroundcolors[0],$this->Backgroundcolors[1],$this->Backgroundcolors[2]);

			} else {

				imagecolorallocate($image, mt_rand(100,200),mt_rand(100,200),mt_rand(100,200));
			}


		}



		//$background_color = imagecolorallocate($image, 255, 255, 255);

		//$text_color = imagecolorallocate($image, 20, 40, 100);

		$bgcolor=ImageColorAllocate($image,0,0,0);

		$bgcolortrans=ImageColorTransparent($image,$bgcolor);

		$noise_color = 255;//imagecolorallocate($image,41,36,33);

		//@產生干擾點

		if($this->NoisePoint){

			for ($i = 0; $i < ($this->width * $this->height) / 3; $i++) {

				imagefilledellipse($image, mt_rand(0, $this->width), mt_rand(0, $this->height), 1, 1, $noise_color);

			}

		}
		//@產生干擾線

		if($this->NoiseLine){  //($this->width * $this->height) / 70

			for ($i = 0; $i < 50; $i++) {
	             		$noise_color = imagecolorallocate($image,mt_rand(0, 255), mt_rand(0, 255), 20);
				imageline($image, 0, mt_rand(0, $this->width), $this->width, mt_rand(0, $this->height), $noise_color);
			}
		}


		//生成字型驗證碼

              $count = count($this->colorlistarray);
              $rand=0;
		for($s=0; $s < $this->characters; $s++){

                     //mt_srand((double)microtime()*1000000);

                     $usercodes = $code[$s];

			$textbox = imagettfbbox($font_size, 0, $this->font,$usercodes);

			$y = $this->height/2 + $this->font_size/2; //($this->font_size - $textbox[5])/2;





			$x = $this->width *  $s / $this->characters + 5;

			if($this->Crooked){


	                     $rand = mt_rand(0,6);


				if($rand == 1){

	                           	$x = $x + 6;


				} else if($rand == 2 ){

					if($s == 0){
	                           		$x = $x + 16;
					} else {
						$x = $x + 6;
					}

				} else if($rand == 3){

					if($s == 0){
	                           		$x = $x + 12;
					} else {
						$x = $x + 6;
					}


				} else if($rand == 4){

					if($s == 0){
	                           		$x = $x + 6;
					} else {
						$x = $x - 6;
					}

				} else if($rand == 5){

					if($s == 0){
	                           		$x = $x + 10;
					} else {
						$x = $x - 10;
					}

	                     } else if($rand == 6){
					if($s == 0){
	                           		$x = $x + 5;
					} else {
						$x = $x - 5;
					}

				}

			}





			//mt_rand(1,8) + $width  * $s / $characters;



			if($this->fontcolortype){

                     	$ck = sscanf($this->colorlistarray[rand(0,$count)], '#%2x%2x%2x');

				$colors=imageColorAllocate($image,$ck[0],$ck[1],$ck[2]);

			} else {
				$colors=imageColorAllocate($image,mt_rand(200,255),mt_rand(100,200),mt_rand(100,255));
			}

			imagettftext($image,$this->font_size,$fonty[$rand], $x, $y, $colors, $this->font,$usercodes);

		}



		if($this->ShowBorder){

		       $bordercolorline = "255, 255, 255";
		       imageline($image, 0, 0, $this->width, 0, $bordercolorline); // 上橫
		       imageline($image, 0, 0, 0,$this->height, $bordercolorline); // 左豎
		       imageline($image, 0, $this->height-1, $this->width, $this->height-1, $bordercolorline); // 下橫
		       imageline($image,$this->width-1, 0, $this->width-1, $this->height, $bordercolorline); // 右豎

		}

		header('Content-Type: image/PNG');

		imagePNG($image);

		imagedestroy($image);


		CookieModel::ShowCookie("capcha",$code,'0');



	}



	function generateCode($characters)       {
		//$possible = 'ABCDEFGHJKLMPQRTUVWXYZ23456789';
		$possible = 'abcdefghijklmnopqrswxyz2346789';
		$code = '';
		$i = 0;
		while ($i < $characters) {
			$code.= substr($possible,mt_rand(0,strlen($possible)-1),1);
			$i++;
		}
		return $code;
	}

}

?>