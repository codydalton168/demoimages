<?php
!defined('WEB_ROOT') && exit('Forbidden');
security::Getglobals(array('action','fid'),'GET');
include(WEB_ROOT."cache/category_cache.php");


if(empty($action)){


       if($_POST[keyword]){
              security::Getglobals(array('keyword'),'POST');
       }

       if($_GET[keyword]){
              security::Getglobals(array('keyword'),'GET');
       }


       if($keyword){

              $keyword = trim($keyword);

		$search_sql="where subject LIKE '%$keyword%'";

		$addpage = "&keyword=".rawurlencode($keyword)."&";
       }


       if($fid){
		$search_sql="where category LIKE '%$fid%'";
		$addpage = "&fid=$fid&";
       }







	$defaultpage = 10;
	(!is_numeric($page) || $page<1) && $page = 1;
	$limit = "LIMIT ".($page-1)*$defaultpage.",$defaultpage";

	@extract($db->getone("SELECT COUNT(*) AS count FROM demodata $search_sql"));


	$pages = Newpage($count,$page,ceil($count/$defaultpage),$admin_file."&".$addpage);

	$query = $db->query("SELECT * FROM demodata $search_sql  ORDER BY postdate DESC $limit");

	while($rt =  $db->fetch_array($query)){




              //$category = str_replace("name='category'","name='category' id='category_$rt[fid]' ",$listselect);

              //$rt[category] =  str_replace("value='$rt[category]'","value='$rt[category]' selected",$category);





              $rt[subject] = "<input type=\"text\" name=\"subject\" id='subject_$rt[fid]' value=\"$rt[subject]\" />";




              //$rt[category] = $categoryname[$rt[category]] ? $categoryname[$rt[category]] : '無分類';

              $rt[postdate] = showdate($rt[postdate],'Y-m-d');

		$manager[]=$rt;
	}
       $db->free_result($query);




} else if($action == 'upcategory'){

	security::GlobalsALL('POST');

       if(empty($fid)){
	      adminmsg("錯誤!");
       }

       $r = $db->getone("SELECT category  FROM demodata WHERE fid=".GetFilter($fid));




       if($checked == 'false'){
              foreach(explode(',',$r[category]) as $k => $v){
                     if($v != $id && $v != ',' && $v){
              		$selids[]=$v;
                     }
              }

              if(count($selids)>0){
                     $selids = implode(',',$selids);
              } else {
                     $selids = '';
              }



       } else {

              if($r[category]){
                     $selids = $r[category].$id.',';
              } else {
                     $selids = ','.$id.',';
              }

       }


       $db->update("UPDATE demodata SET category='$selids' WHERE fid=".GetFilter($fid));

       echo "success";ajaxfooter();



} else if($action == 'upaction'){

	security::GlobalsALL('POST');

       if(empty($fid)){
	      adminmsg("錯誤!");
       }



       $db->update("UPDATE demodata SET $upname='$value' WHERE fid=".GetFilter($fid));


       echo "success";ajaxfooter();


} else if($action == 'edit'){

       if(empty($fid)){
	      adminmsg("錯誤!");
       }

	@extract($db->getone("SELECT * FROM demodata where fid ='$fid'"));


} else if($action == 'editsave'){

	security::GlobalsALL('POST');

       if(empty($fid)){
	      adminmsg("錯誤!");
       }

	if(is_uploaded_file($_FILES['images']['tmp_name'])){
              $upimages_name = $_FILES['images']['name'];
		$upimages_type = $_FILES['images']['type'];
		$upimages_temp = $_FILES['images']['tmp_name'];

              $savedir = date('Ym',time());

              createFolderd(WEB_ROOT.'attachment/'.$savedir);

		$upimagestype = strtolower(substr(strrchr($upimages_name,'.'),1));

		$prename = $timestamp."_".rand(1,1000).".".$upimagestype;

		$upatch =   "attachment/$savedir/".$prename;

		@move_uploaded_file($upimages_temp,WEB_ROOT.$upatch);

              Punlink($oimages);

       } else {

              $upatch = $oimages;
       }



	if(is_array($category)){
		$category_groups=','.implode(',',$category).',';
	}else{
		$category_groups='';
	}



      	$db->update("UPDATE demodata SET ".SqlSingle(array(
      			'category'	=>	$category_groups,
                     'subject'     =>     $subject,
                     'description' =>     $description,
      			'images'	=>	$upatch,
                     'httpurl'     =>     $httpurl,
      	))." where fid ='$fid' ");

       echo "success";ajaxfooter();



} elseif ($action=='delaction'){

	security::Getglobals(array('fid'),'POST');

       if(empty($fid)){
              adminmsg("未知 fid");
       }

       @extract($db->getone("SELECT images FROM demodata WHERE fid=".GetFilter($fid)));

       if($images){
              Punlink($images);
       }



       $db->update("DELETE FROM `demodata` WHERE fid=".GetFilter($fid));

       echo "success";ajaxfooter();


} elseif ($action=='deledit'){

	security::Getglobals(array('fid'),'POST');

       if(empty($fid)){
              adminmsg("未知 fid");
       }

       @extract($db->getone("SELECT images FROM demodata WHERE fid=".GetFilter($fid)));

       if($images){
              Punlink($images);
       }


       $db->update("UPDATE demodata SET images='' WHERE fid=".GetFilter($fid));

       echo "success";ajaxfooter();



} else if($action == 'addsave'){


	security::GlobalsALL('POST');





	if(is_uploaded_file($_FILES['images']['tmp_name'])){
              $upimages_name = $_FILES['images']['name'];
		$upimages_type = $_FILES['images']['type'];
		$upimages_temp = $_FILES['images']['tmp_name'];

              $savedir = date('Ym',time());

              createFolderd(WEB_ROOT.'attachment/'.$savedir);



		$upimagestype = strtolower(substr(strrchr($upimages_name,'.'),1));

		$prename = $timestamp."_".rand(1,1000).".".$upimagestype;

		$upatch =   "attachment/$savedir/".$prename;

		@move_uploaded_file($upimages_temp,WEB_ROOT.$upatch);

       }

	if(is_array($category)){
		$category_groups=','.implode(',',$category).',';
	}else{
		$category_groups='';
	}
      	$db->update("INSERT INTO demodata SET ".SqlSingle(array(
      			'category'	=>	$category_groups,
                     'subject'     =>     $subject,
                     'description' =>     $description,
      			'images'	=>	$upatch,
                     'httpurl'     =>     $httpurl,
                     'postdate'    =>     $timestamp
      	)));




       echo "success";ajaxfooter();




}


$template='demodata';

include template('index');footer();


?>