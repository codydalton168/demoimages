



$(document).ready(function(e) {


       $('#sliphover').sliphover({
              target : '.item',
              caption: 'data-caption',
		backgroundColor:'rgba(0,0,0,0.9)'
       });



	$(window).scroll(function(e) {

		//若滾動條離頂部大於100元素
		if($(window).scrollTop()>100){
			$("#gotop").fadeIn(100);
		} else {
			$("#gotop").fadeOut(100);
		}
	});





	//點擊回到頂部的元素
	$("#gotop").click(function(e) {
	     //以1秒的間隔返回頂部
	     $('body,html').animate({scrollTop:0},500);
	});


	$("#gotop").mouseover(function(e) {
        	$(this).css("background","url(images/1.png) no-repeat 0px 0px");
	});

	$("#gotop").mouseout(function(e) {
        	$(this).css("background","url(images/1.png) no-repeat -70px 0px");
	});
});