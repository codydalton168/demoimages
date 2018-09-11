


//去除前後(左右)空白
function trim(string) {
	//if(string.length > 0){
    		return string.replace(/(^[\s]*)|([\s]*$)/g, "");
	//}
}

//去左空白
function lTrim(string) {
	//if(string.length > 0){
    		return string.replace(/(^[\s]*)/g, "");
	//}
}

//去除右空白
function rTrim(string) {
	//if(string.length > 0){
    		return string.replace(/([\s]*$)/g, "");
	//}
}

//去除任何空白
function allTrim(string) {
	//if(string.length > 0){
    		return string.replace(/([\s])/g, "");
	//}

}






function topmenu(n) {
	window.location = "admin.php?adminis=" + n + "&t=" + new Date().getTime();
}



//重新整理
function getreload(){
        window.location.reload();
}




function QuitSystem() {


	bootbox.confirm("確定要登出嗎？", function(result) {
		if(result) {


				window.location = "admin.php?quit=quit&t="+new Date().getTime();


		}
	});



}


$(document).ready(function(){


	//根據瀏覽器大小自動調整

	$(window).resize(function(){

	});


});