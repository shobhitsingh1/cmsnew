// JavaScript Jquery Expand Collapse divs Document
//Current pagename
var path = window.location.pathname;
var pthName = path.substring(path.lastIndexOf('/') + 1);

(function($){

//Default Expand Div Content OnClick
$.fn.expandCollapseDivContent = function(){

	$(this).each(function(index){
		
		var divExpClient = $(this).children("div:first").attr('id');
																											
		$("#" + divExpClient).click(function(){

			var spanExpClient = $("#" + divExpClient + " span:eq(0)").attr('id');
								
			$("#" + divExpClient + "Content").slideToggle();
			
																		
			if($("#" + spanExpClient).text() == "+")
			{
				$("#" + spanExpClient).html("−");
			}
			else
			{
				$("#" + spanExpClient).text("+");
			}				
							
		});
	
	});
};

//Default Expand All Div Content
$.fn.defaultExpandDivContent = function(){

	$(this).each(function(index){
		
		var divExpClient = $(this).children("div:first").attr('id');
																																														
		var spanExpClient = $("#" + divExpClient + " span:eq(0)").attr('id');
									
		$("#" + divExpClient + "Content").slideToggle();
																			
		if($("#" + spanExpClient).text() == "+")
		{
			$("#" + spanExpClient).html("−");
		}
		else
		{
			$("#" + spanExpClient).text("+");
		}				
	
	});
};



})(jQuery);

$(document).ready(function(){
		
	$(".admin_list li a[href='"+ pthName + "']").each(function(){
				
		var pid = $(this).parent().parent().parent().prop('id');
		
		var span_id = pid.replace("Content", "Sign");
		
		if($("#" + span_id).text() == "+")
		{
			$("#" + span_id).html("−");
		}
		else
		{
			$("#" + span_id).text("+");
		}
				
		$("#" + pid ).slideToggle();
	});

});

/*function expandCollapseAdminLeftMenu()
{
	$(".admin_box").each(function(index){
			
		var divExpClient = $(this).children("div:first").attr('id');
																	
		$("#" + divExpClient).click(function(){
																			
			var spanExpClient = $("#" + divExpClient + " span:eq(0)").attr('id');
								
			$("#" + divExpClient + "Content").slideToggle();
															
			if($("#" + spanExpClient).text() == "+")
			{
				$("#" + spanExpClient).html("−");
			}
			else
			{
				$("#" + spanExpClient).text("+");
			}				
							
		});
	
	});

}*/