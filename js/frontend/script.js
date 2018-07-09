function dialog(object2, params)
{
	if(typeof object2 !="object")
	{
		object2 ={};
	}
	var  object1 =
	{
		buttons: 
		[
			{
				text: "close",
				click: function() 
				{
					$( this ).dialog( "close" );
				}
			}
		]
	};
	$.extend( object1, object2 );
	if($( "#dialog").length ==1)
	{
		$( "#dialog p").text(object1.message); 
		$( "#dialog").dialog(object1);
	}else
	{
		$( "#dialog p", parent.document).text(object1.message); 
		$( "#dialog" , parent.document).dialog(object1);
	}
	// $('.ui-dialog').css({left:"875px"});
	// $('.ui-dialog',parent.document).css({left:"875px"});
}
$(function() {
    
});
