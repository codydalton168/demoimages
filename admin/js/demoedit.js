


function delimg(fid){

       if(!confirm('確定要刪除嗎？')) return false;


	$.ajax({
    		type: 'post',
		url: 'admin.php?adminis=demodata&action=deledit&t='+new Date().getTime(),
		data: 'fid='+fid,
		success: function(data, textStatus, XMLHttpRequest){
			if(data == 'success'){
				$("#delimg").html('').prepend('<input type="file" name="images" class="default" accept="image/gif, image/jpg, image/jpeg, image/png" />');

			} else {

				alert("處理資料異常?請聯絡管理員");
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





function postcheck(){

	if($("[name='images']").length > 0  && $("[name='images']").val().length < 1){
        	alert("網站預覽圖必須要有");
        	$("[name='images']").focus();
		return false;
	}


	if($("[name='category[]']:checked").length < 1){
        	alert("網站分類必須要有");
		return false;
	}


	if($("[name='subject']").val().length < 1){
        	alert("網站標題必須要有");
        	$("[name='subject']").focus();
		return false;
	}

	if($("[name='httpurl']").val().length < 1){
        	alert("網站網址必須要有");
        	$("[name='httpurl']").focus();
		return false;
	}


	var fd = new FormData();

	//fd.append('productimg',$("[name='productimg']")[0].files[0]);


	$('.form-horizontal input, select ,textarea').each(function(){
		if(this.type== 'radio' && !this.checked){
			return;

		} else if(this.type== 'checkbox' && !this.checked){

			return;

		} else if(this.type == 'file'){

			fd.append(this.name,$("[name='"+this.name+"']")[0].files[0]);

		} else if(this.type != 'file'){

	              var title = this.name;
	              var val = this.value;
			if(title.length>0 && val.length>0){
	                     fd.append(title,val);
			}
		}


	});

	$(".btn").attr('disabled', true);

	$("#sendsave").text('正在傳送資料');



	$.ajax({
    		type: 'post',
		url: 'admin.php?adminis=demodata&action='+ $("[name='action']").val()  +'&t='+new Date().getTime(),
		data: fd,
              processData: false,
		contentType: false,
		success: function(data, textStatus, XMLHttpRequest){
			if(data == 'success'){

				if($("[name='action']").val() == 'editsave'){

					window.location = "admin.php?adminis=demodata&page="+$("[name='page']").val();

				} else {

					window.location = "admin.php?adminis=demodata";
				}

				//alert(data);
			} else {


				$(".btn").attr('disabled',false);
				$("#sendsave").text('儲存');

				alert("處理資料異常?請聯絡管理員");
				return false;
			}


		},error:function (xhr, ajaxOptions, thrownError){
			if(xhr.status == 404 || xhr.status == 500){

				$(".btn").attr('disabled',false);
				$("#sendsave").text('儲存');
				alert("請求主機驗證異常?請聯絡管理員");
				return false;
			}
		}
	});





	return false;
}




$(document).ready(function(){
	/*
	$("[name='category']").click(function(){
		$("[name='category']").each(function(){
			$(this).prop("checked", false);
		});
              $(this).prop("checked",true);
	});*/

	/*
	if($("[name='category']:checked").length < 1){

              $("[name='category']").eq(0).prop("checked",true);
	}*/





});