




function submitTest(){

	if($("[name='keyword']").val().length < 1){
		return false;
	}

       $("[name='FORM']").submit();

}


function postcheck(fid){


       if(!confirm('確定要刪除嗎？')) return false;



	$.ajax({
    		type: 'post',
		url: 'admin.php?adminis=demodata&action=delaction&t='+new Date().getTime(),
		data: 'fid='+fid,
		success: function(data, textStatus, XMLHttpRequest){
			if(data == 'success'){
                            $('#list_'+fid).remove();
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


$(document).ready(function(){




	$("[name='category[]']").click(function(e) {



		var s = $(this).attr("id").split('_');

		$.ajax({
	    		type: 'post',
			url: 'admin.php?adminis=demodata&action=upcategory&t='+new Date().getTime(),
			data: 'fid='+s[1] + '&id='+s[2]+"&checked="+$(this).prop('checked'),
			success: function(data, textStatus, XMLHttpRequest){
				if(data == 'success'){

	                            alert('分類修改完成');

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

	});



	$("[name='subject']").change(function(){

		if($(this).val().length > 0 ){




                     var s = $(this).attr("id").split('_');

			$.ajax({
		    		type: 'post',
				url: 'admin.php?adminis=demodata&action=upaction&t='+new Date().getTime(),
				data: 'fid='+s[1] + '&upname='+s[0]+'&value='+$(this).val(),
				success: function(data, textStatus, XMLHttpRequest){
					if(data == 'success'){

		                            alert('標題修改完成');

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
	});


	$("[name='category']").change(function(){

		if($(this).val() != '0'){

                     var s = $(this).attr("id").split('_');

			$.ajax({
		    		type: 'post',
				url: 'admin.php?adminis=demodata&action=upaction&t='+new Date().getTime(),
				data: 'fid='+s[1] + '&upname='+s[0]+'&value='+$(this).val(),
				success: function(data, textStatus, XMLHttpRequest){
					if(data == 'success'){

		                            alert('分類修改完成');

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

	});
});

