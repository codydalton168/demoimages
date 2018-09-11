<?php

/*
採集器...

*/



@set_time_limit(0);
@ignore_user_abort(TRUE);








require_once('global.php');



//exit("1");

$savedir = date('Ym',time());


$uri = 'http://sdemo.precise-marketing.com/nico_demo/index.html';
$getRate = file_get_contents($uri);


preg_match_all('/<div[^>]*class="thumbnail"[^>]*>(.*?)<\/div>/is', $getRate,$out);

foreach ($out[0] as $key => $value){

       preg_match('/<a[^>]*href="(.*?)"[^>]*>(.*?)<\/a>/is', $value, $match2);

       preg_match('/<img[^>]*src="(.*?)"[^>]*>/is',$match2[2], $img);

       echo "$match2[1]  $img[1] <br />";


	$upimagestype = strtolower(substr(strrchr($img[1],'.'),1));

	$prename = time()."_".rand(1,1000).".".$upimagestype;

	$upatch =   "attachment/$savedir/".$prename;


       file_put_contents($upatch, file_get_contents('http://sdemo.precise-marketing.com/nico_demo/'.$img[1]));

       $category=0;
       if(strpos($img[1],"sample")!==false){
              $category='9';
       }



      	$db->update("INSERT INTO demodata SET ".SqlSingle(array(
      			'category'	=>	$category,
                     'subject'     =>     '未有名稱',
      			'images'	=>	$upatch,
                     'httpurl'     =>     $match2[1],
                     'postdate'    =>     $timestamp
      	)));

}



//sample/d27/index.html


//print_r($out);





/*
                    <div class="thumbnail">
                        <a href="http://www.wiviamachinery.com.tw/" target="_blank">
                            <img src="img/03-028.jpg">
                        </a>
                    </div>


*/









?>