

function checkformdb(){


	if($('.username').val().length < 1) {
		$("#msg").html(' <img src=\"images/cancel.png\" align=\"absbottom\"/>&nbsp;  請輸入帳戶...');
		return false;
	}


	var Reg = /^[-a-zA-Z0-9_\.]+@([0-9A-Za-z][0-9A-Za-z-]+\.)+[A-Za-z]{2,5}$/;
	if(!Reg.test(trim($(".username").val()))){
		$("#msg").html(' <img src=\"images/cancel.png\" align=\"absbottom\"/>您輸入&nbsp;E-mail&nbsp;格式錯誤。');
		return false;
	}



	if($('.password').val().length < 1) {
		$("#msg").html(' <img src=\"images/cancel.png\" align=\"absbottom\"/>&nbsp;  請輸入密碼...');
		return false;
	}


       $(".username").attr('disabled', true);

       $(".password").attr('disabled', true);


       $("[name='buttons']").attr('disabled', true).text("正在驗證帳戶及密碼中....");


	$("#msg").html("&nbsp;<img src=\"images/loading.gif\" align=\"absmiddle\">&nbsp;正在驗證帳戶及密碼中....");



	$.ajax({
  		type: 'POST',
		url: 'admin.php?t='+new Date().getTime(),
              data: 'username='+$('.username').val()+'&password='+$('.password').val(),
		success: function(data, textStatus, XMLHttpRequest){

			if(data == 'success'){
					setTimeout(function(){
						$("#msg").html("<img src=\"images/success.png\" start=\"fileopen\" loop=\"-1\" align=\"absmiddle\">&nbsp; 驗證成功,取的帳戶權限!");

					},1000);

					setTimeout(function(){
						window.location.reload();
					},1300);

					return false;

			} else {

					$("#msg").html("<img src=\"images/cancel.png\" start=\"fileopen\" loop=\"-1\" align=\"absmiddle\">&nbsp; 您並沒有任何權限進入或者帳戶及密碼錯誤!");

					setTimeout(function(){
					       $(".username").attr('disabled', false);
					       $(".password").attr('disabled', false);
					       $("[name='buttons']").attr('disabled', false).text("提 交");
						$("#msg").html("");

					},1000);

					return false;

			}

		},error:function (xhr, ajaxOptions, thrownError){
			if(xhr.status == 404 || xhr.status == 500){
				alert("請求主機驗證異常?請聯絡管理員");
				return false;
			}


		}
	});


}



//去除前後(左右)空白
function trim(string) {
    return string.replace(/(^[\s]*)|([\s]*$)/g, "");
}




jQuery(document).ready(function(){


       $("[name='buttons']").click(function() {
              checkformdb();
       });


	$(document).bind('keydown',function(event){

              if(event.which == 13 ) {

			checkformdb();
		}
	});




});
