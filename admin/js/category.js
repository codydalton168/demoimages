






function InputFiledelclick(fid){


       if(!confirm('確定要刪除嗎？')) return false;

	$.ajax({
  		type: 'POST',
		url: 'admin.php?adminis=category&action=delcategoryimg&ajax=1&t='+new Date().getTime(),
              data: 'fid='+fid,
		success: function(data, textStatus, XMLHttpRequest){
			if(data == 'success'){
       			$("#InputFile_Msg_"+fid).html('<a href="javascript:;" onclick="InputFileclick(\''+fid+'\');" title="上傳">上傳</a>');

			} else {

                            alert(data);
			}

		},error:function (xhr, ajaxOptions, thrownError){
			if(xhr.status == 404){
				alert("請求主機驗證異常?請聯絡管理員");
				return false;
			}
		}
	});

}



function InputFileclick(fid){

	$('#InputFile_'+fid).click();
}



function delclse(fid){





       $("#InputFile_Msg_"+fid).html('<a href="javascript:;" onclick="InputFileclick(\''+fid+'\');" title="上傳">上傳</a>');

	$("#InputFile_"+fid).after($("#InputFile_"+fid).clone(true)).remove();

}




function showText(fid){

       //var file =$("#InputFile_"+fid).files[0];


	var fileExtension = ['jpeg', 'jpg', 'png', 'gif'];

	if ($.inArray($("#InputFile_"+fid).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
		alert("只能上傳 : " + fileExtension.join(', ') + " 檔案格式");
		$("#InputFile_"+fid).after($("#InputFile_"+fid).clone(true)).remove();
		return false;

	}

       var filename = $("#InputFile_"+fid).val().toLowerCase();


       $("#InputFile_Msg_"+fid).html(filename+"&nbsp;&nbsp;&nbsp;&nbsp;\
              <a href=\"javascript:;\" onclick=\"delclse('"+fid+"');\" title=\"取消\">[取消]</a>\
	");


	$("[name='submits']").prop('disabled',false);

}









function deltype(fid){

       if(!confirm('確定要刪除嗎？')) return false;

	$.ajax({
  		type: 'POST',
		url: 'admin.php?adminis=category&action=delcategory&ajax=1&t='+new Date().getTime(),
              data: 'fid='+fid,
		success: function(data, textStatus, XMLHttpRequest){
			if(data == 'success'){
				$("#o_"+fid).remove();
       			$("[name='sequence']").prop('disabled',false);

				window.location.reload();

			} else {

                            alert(data);
			}

		},error:function (xhr, ajaxOptions, thrownError){
			if(xhr.status == 404){
				alert("請求主機驗證異常?請聯絡管理員");
				return false;
			}
		}
	});


}





function delinser(ids){
       if(!confirm('確定要刪除嗎？')) return false;
	$("#o_"+ids).remove();
       id--;
       $("[name='sequence']").prop('disabled',false);
}








function addinsert() {
	id++;



	var str='<div class="dd-item" id="o_'+id+'" style="width:100%;vertical-align:middle;"   >\
			<div class="dd-handle dd2-handle" style="height:49px;" onmousedown="alert(\''+'該分類是新增分類項目,需先提交更新存儲'+'\');">\
				<i class="normal-icon icon-reorder blue bigger-130"></i>\
				<i class="drag-icon icon-move bigger-125"></i>\
			</div>\
			<div class="dd2-content">\
                            <input type="text" name="category['+id+']" value=""  placeholder="分類名稱..."  style="background:#fff;"/>\
                            <input type="file" name="InputFile_'+id+'" id="InputFile_'+id+'" contentEditable="false"  onchange="showText(\''+id+'\')"  accept="image/gif, image/jpg, image/jpeg, image/png" style="display:none;" />\
				<span id="InputFile_Msg_'+id+'">\
                                   <a href="javascript:;" onclick="InputFileclick(\''+id+'\');" title="上傳">上傳</a>\
                            </span>\
				<div class="pull-right action-buttons">\
                                          <a href="javascript:;"  onclick="delinser(\''+id+'\');"><i class="icon-trash bigger-130"></i></a>\
				</div>\
                     </div>\
              </div>';


	alert(str);



	$("#append").prepend(str);

	$("[name='submits']").prop('disabled',false);

}









jQuery(function($){


	$('.dd').nestable({
       	 group: 1
	});



	$('.dd2-content').on('mousedown', function(e){
		e.stopPropagation();
	});




	$('.dd-handle').on('mousedown', function(e){
		$("[name='sequence']").prop('disabled',false);
	});




	$("input[type='text']").click(function(e) {
                     $("[name='submits']").prop('disabled',false);

	});


	$("[name='sequence']").click(function(e) {



			if($(".dd-item").length > 0){

				//window.JSON.stringify($('.dd').nestable('serialize'))

				$.ajax({
			  		type: 'POST',
					url: 'admin.php?adminis=category&action=Sequence&ajax=1&t='+new Date().getTime(),
			              data: 'json='+window.JSON.stringify($('.dd').nestable('serialize')),
			              //dataType: 'json',
			              //async: false,
					success: function(data, textStatus, XMLHttpRequest){
						if(data == 'success'){
			                            alert("儲存排序成功");

							window.location.reload();

							//$('#nestable_list_3').nestable('serialize')
			              		//$("#nestable_list_1_output").html(JSON.stringify($('#nestable_list_3').nestable('serialise')));
						}
					},error:function (xhr, ajaxOptions, thrownError){
						if(xhr.status == 404){
							alert("請求主機驗證異常?請聯絡管理員");
							return false;
						}
					}
				});

			}

	});





	$("[name='submits']").click(function(e) {


		if($(".dd-item").length > 0){


			var fd = new FormData();

			$('#fromesdata input, select ,textarea').each(function(){
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
			                     fd.append(title,trim(val));
					}
				}
			});







			$.ajax({
				url: 'admin.php?adminis=category&action=batupdate&t='+new Date().getTime(),
		  		type: 'POST',
				data: fd,
		              processData: false,
				contentType: false,
				success: function(data, textStatus, XMLHttpRequest){
					if(data == 'success') {
						alert("更新成功");

						window.location.reload();

					}
				},error:function (xhr, ajaxOptions, thrownError){
					if(xhr.status == '404'){
						alert("請求主機驗證異常?請聯絡管理員");
						return false;
					}
				}
			});
		}

	});



       





});