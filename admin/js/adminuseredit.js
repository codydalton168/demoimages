




function  postcheck() {

		var errormsg='';

		if($("[name='username']").val().length < "1"){
	        	errormsg += "*姓名必須要有。\r\n";
		}

		var Reg = /^[-a-zA-Z0-9_\.]+@([0-9A-Za-z][0-9A-Za-z-]+\.)+[A-Za-z]{2,5}$/;
		if(!Reg.test($("[name='email']").val())){
	    		errormsg += "*您輸入E-mail格式錯誤。\r\n";
		}






		if($("[name='action']").val() == 'addsave'){



				if($("[name='password']").val().length < "1"){
	        				errormsg += "*您現在操作新增管理員,密碼必須輸入?\r\n";
				}
		}


		if(errormsg.length > 0){
			alert(errormsg);
			return false;
		}

		var formData = new FormData($('#fromedata')[0]);

		$.ajax({
			url: 'admin.php?adminis=adminuser&action='+$("[name='action']").val()+'&t='+new Date().getTime(),
	  		type: 'POST',
                     processData: false,
			contentType: false,
	              data: formData,
			success: function(data, textStatus, XMLHttpRequest){
				if(data == 'success') {

                                   if($("[name='action']").val() == 'addsave'){
						alert("新增成功");
					} else {
						alert("更新成功");
                                          window.location="admin.php?adminis=adminuser&page="+$("[name='page']").val();
					}
				}
			},error:function (xhr, ajaxOptions, thrownError){
				if(xhr.status == '404'){
					alert("請求主機驗證異常?請聯絡管理員");
					return false;
				}
			}
		});

}











$(document).ready(function(){

	$("[name='gender']").click(function(){
		$("[name='gender']").each(function(){
                     //$(this).removeAttr('checked').closest('.middle').removeClass('selected');
                     //$(this).checked = false;
			$(this).prop("checked", false);
		});
              //$(this).checked = true;
              //$(this).addClass('selected');
              $(this).prop("checked",true);
	});


	if($("[name='gender']:checked").length < 1){

              $("[name='gender']").eq(0).prop("checked",true);
	}





});