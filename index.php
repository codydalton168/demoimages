<?php
require_once('global.php');
include(WEB_ROOT."cache/category_cache.php");
security::Getglobals(array('category','page'),'GET');


if($category>0){

 	$search_sql="where category LIKE '%$category%'";

	$addpage = "&category=$category&";

       ${'class_'.$category} = "class=\"current_all\"";

} else {
       $class_all = "class=\"current_all\"";
}


//幾筆為一頁
$defaultpage = 21;
(!is_numeric($page) || $page<1) && $page = 1;
$limit = "LIMIT ".($page-1)*$defaultpage.",$defaultpage";
@extract($db->getone("SELECT COUNT(*) AS count FROM demodata $search_sql"));
$pages = showpage($count,$page,ceil($count/$defaultpage),"index.php?".$addpage);
$query =$db->query("SELECT * FROM demodata $search_sql ORDER BY postdate DESC $limit");
while($rt = $db->fetch_array($query)){
	$newslist[]=$rt;
}
$db->free_result();





if(file_exists(WEB_ROOT."cache/textads.txt")){
	$content = file_get_contents(WEB_ROOT."cache/textads.txt");
}




require_once printHTML('index');footer();
?>