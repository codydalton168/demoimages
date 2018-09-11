<?php
!defined('WEB_ROOT') && exit('Forbidden');


if(empty($action)){

	$query      = $db->query("SELECT * FROM category ORDER BY ordernum");
	while ($rt   = $db->fetch_array($query)) {
		$category[]   = $rt;
	}
       $db->free_result();



	$total = $db->getone("SELECT fid FROM category ORDER BY fid DESC LIMIT 1");


       !$total[fid] && $total[fid]=0;


       //$total[fid]+=1;


} else if($action == "delcategory"){


       security::Getglobals(array('fid'),'POST');


	$rt = $db->getone("SELECT InputFile FROM category WHERE fid='$fid'");

       if($rt[InputFile]){
              Punlink($rt[InputFile]);
       }


       $db->update("DELETE FROM category WHERE fid='$fid'");

       category_cache();

       echo "success"; ajaxfooter();




} else if($action == "delcategoryimg"){


       security::Getglobals(array('fid'),'POST');


	$rt = $db->getone("SELECT InputFile FROM category WHERE fid='$fid'");

       if($rt[InputFile]){
              Punlink($rt[InputFile]);
       }


       $db->update("UPDATE category SET InputFile='' WHERE fid='$fid'");

       category_cache();

       echo "success"; ajaxfooter();


} else if($action == "batupdate"){


       security::Getglobals(array('category'),'POST');



       foreach ($_FILES as $key => $temp) {
                     $keyNum = explode('_',$key)[1];

                     $upimages_name = $temp['name'];
       		$upimages_type = $temp['type'];
       		$upimages_temp = $temp['tmp_name'];
                     $upimages_size = $temp['size'];

       		$upimagestype = strtolower(substr(strrchr($upimages_name,'.'),1));

                     $filename= substr(bin2hex(md5($timestamp.mt_rand(1,1000).$admincpid)),0,10).".".$upimagestype;

       		//$upatch =   "attachment/gallery/$savedir/temp.$upimagestype";

                     $thumbnailUrl = "attachment/$filename";

       		@move_uploaded_file($upimages_temp,WEB_ROOT.$thumbnailUrl);

                     $InputFiledb[$keyNum] = $thumbnailUrl;

       }


       //print_r($InputFiledb);
       //exit;




       foreach ($category as $key => $value){

		$value = trim(Charcv($value));
		if($value){
			$db->getupdate(
	                        "SELECT fid  FROM category where fid ='$key'",
	                        "UPDATE category SET subject='$value' where fid ='$key'",
	                        "INSERT INTO category SET fid='$key',subject='$value'"
			);




                     if($InputFiledb[$key]){
			    $db->update("UPDATE category SET InputFile='{$InputFiledb[$key]}' where fid ='$key'");
                     }


		}



       }

       category_cache();
       echo "success"; ajaxfooter();



} else if($action == "Sequence"){

       security::Getglobals(array('json'),'POST');

	if(empty($json)){

              echo "未知 json";ajaxfooter();
	}


       $datadb = json_decode($json,true);

       foreach ($datadb as $key => $val){

              $i++;

		$db->update("UPDATE category SET ordernum='$i' where fid ='$val[id]'");

       }


       category_cache();

       echo "success";ajaxfooter();

}




$template='category';

include template('index');footer();









function category_cache(){
	global $db;


	$space="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";

	$category_all_select="\r\n\$category_select='\r\n";

	$categoryname = "\$categoryname=array(\r\n";

	$category_select="\r\n\$listselect=\"<select name='category' class='span5 m-wrap'><option value='0'>請選擇分類...</option>\r\n";

	$query = $db->query("SELECT * FROM category ORDER BY ordernum");
	while ($rt = $db->fetch_array($query)) {

              $categoryname.="\t'$rt[fid]'\t=>array(\r\n\t\t'subject'\t=>\t'$rt[subject]',\r\n\t\t'InputFile'\t=>\t'$rt[InputFile]'\r\n\t),\r\n";

              $category_all_select.="<li><a href=\"admin.php?adminis=demodata&fid=$rt[fid]\">$rt[subject]</a></li>\r\n";

              $category_select.="<option value='$rt[fid]'>$rt[subject]</option>\r\n";
	}
       $db->free_result();



	$category_select.="</select>\";\r\n";

       $category_all_select.="';";


	$categoryname.= ");\r\n";


	writeover(WEB_ROOT."cache/category_cache.php","<?php\r\n".$categoryname."\r\n".$category_all_select."\r\n".$category_select."\r\n?".">");

}














?>