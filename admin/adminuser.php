<?php
!defined('WEB_ROOT') && exit('Forbidden');
security::Getglobals(array('action','uid'),'GET');


if(empty($action)){

	$defaultpage = 10;
	(!is_numeric($page) || $page<1) && $page = 1;
	$limit = "LIMIT ".($page-1)*$defaultpage.",$defaultpage";

	@extract($db->getone("SELECT COUNT(*) AS count FROM adminuser"));
	$pages = Newpage($count,$page,ceil($count/$defaultpage),$admin_file."&");

	$query = $db->query("SELECT * FROM adminuser ORDER BY regtime DESC $limit");

	while($rt =  $db->fetch_array($query)){

              $rt[disabled]  = $rt[uid] == 1 ? "disabled" : "";


		$rt[faceimg] = $rt[faceimg] ? "<img src='$rt[faceimg]' style='width:29px;height:29px;'/>" : "<img src='admin/image/avatar.png' style='width:29px;height:29px;'/>";

              $rt[groupid] = $leveldb[$rt[groupid]][grouptitle];

              $rt[regtime] =showdate($rt[regtime],'Y-m-d H:i:s');

              $rt[logintime] = showdate($rt[logintime],'Y-m-d H:i:s');

		$manager[]=$rt;
	}
       $db->free_result($query);


} else if($action == 'edit'){

       if(empty($uid)){
	      adminmsg("錯誤!");
       }



	@extract($db->getone("SELECT * FROM adminuser where uid ='$uid'"));

       $gender && ${'checked_'.$gender}= 'checked';


       $regtime =showdate($regtime,'Y-m-d H:i:s');

       $logintime = showdate($logintime,'Y-m-d H:i:s');














} else if($action == 'addsave'){
	security::GlobalsALL('POST');


      	$db->update("INSERT INTO adminuser SET ".SqlSingle(array(
      			'username'	=>	$username,
                     'gender'      =>     $gender,
      			'email'	=>	$email,
                     'password'    =>     md5($password),
                     'onlineip'    =>     '127.0.0.1',
                     'ipfrom'      =>     '0.0.0.0',
                     'regtime'     =>     $timestamp,
                     'logintime'   =>     $timestamp
      	)));




       echo "success";ajaxfooter();




} else if($action == 'editsave'){

	security::GlobalsALL('POST');


      	$db->update("update adminuser SET " . SqlSingle(array(
      			'username'	=>	$username,
                     'gender'      =>     $gender,
      			'email'	=>	$email
      	))." WHERE uid=".GetFilter($uid));



       if(!empty($password)){


              $db->update("update adminuser SET password='".md5($password)."' WHERE uid=".GetFilter($uid));

              if($admincpid == $uid){

	             $country = GeoipCheck('2');

	             $rt = $db->getone("select * FROM adminuser where uid=".GetFilter($uid));


                     CookieModel::ShowCookie("AdminUser",CookieModel::ValueEncryption($rt[uid]."|".$rt[password]."|".$country),'24E');

              }


       }



       echo "success";ajaxfooter();




} else if($action == 'delface'){

       security::Getglobals(array('uid'),'POST');

       @extract($db->getone("SELECT faceimg FROM adminuser WHERE uid=".GetFilter($uid)));
       if($faceimg){
              Punlink($faceimg);
              $db->update("update adminuser SET faceimg='' WHERE uid=".GetFilter($uid));
       }



} else if($action == 'batlist'){

       security::Getglobals(array('selid','type'),'POST');


	foreach($selid as $k => $v){
		if(is_numeric($v)){
			$selids .= $selids ? ','.$v : $v;
		}
	}

	if(!$selids){
              echo "未選擇任何操作";ajaxfooter();
       }

       if($type == 'userenable'){

    		$db->update("UPDATE adminuser SET situation='1' WHERE uid IN($selids)");

       } else if($type == 'userstop'){


    		$db->update("UPDATE adminuser SET situation='0' WHERE uid IN($selids)");


       } else if($type == 'userdel'){


    		$db->update("DELETE FROM adminuser WHERE uid IN($selids)");

       }

       echo "success";ajaxfooter();

}





$template='adminuser';

include template('index');footer();

?>