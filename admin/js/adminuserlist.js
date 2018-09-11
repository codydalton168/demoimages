

function selectall(value){

	if(value == 1){


			$('.checkBox').each(function(){

					if($(this).val() != 1){
						$(this).prop("checked", true);
					}


			});

	} else {

			$('.checkBox').each(function(){
					$(this).prop("checked", false);
			});

	}

}



function  adminuserajax(strdata){

		var strchecked = $("[name='selid[]']:checked");

		if(strchecked.length < 1){
			alert("您沒選擇名單?");
			return false;
		}



       	if(strdata == 'userdel'){

			if(!confirm("確定要刪除您所選名單嗎?")){
				return false;
			}

		}

	       AdminMaessage("送出資料更新");

		var arrText= [];

		arrText.push({"name":'type',"value":trim(strdata)});

		$('#no-more-tables input, select ,textarea').each(function(){
			if(this.type== 'radio' && !this.checked){
				return;

			} else if(this.type== 'checkbox' && !this.checked){

				return;
			}

	              var title = this.name;
	              var val = this.value;
			if(val.length>0){
				arrText.push({"name":title,"value":trim(val)});

			}
		});



		$.ajax({
			url: 'admin.php?adminis=adminuser&action=batlist&t='+new Date().getTime(),
	  		type: 'POST',
	              data: arrText,
			success: function(data, textStatus, XMLHttpRequest){
				if(data == 'success') {
				       $.unblockUI();
					setTimeout(function(){
						window.location.reload();
					},1000);

				} else {
				       $.unblockUI();
					alert(data);
				}


			},error:function (xhr, ajaxOptions, thrownError){
				if(xhr.status == '404'){
					alert("請求主機驗證異常?請聯絡管理員");
					return false;
				}
			}
		});

}



$(document).ready(function () {





});