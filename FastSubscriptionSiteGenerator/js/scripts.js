$.fn.adaptStepItemHeight = function () {
    // adapt step height to content (if multiple lines)
	var maxHeight=$('.checkout-bar li:first').innerHeight();
	var maxHeight0=maxHeight;
	$('.checkout-bar li').each(function(i)
	{
	   var val=$(this).innerHeight(); // get the Height
	   if(val>maxHeight)
		maxHeight=val;
	});
	if(maxHeight!=maxHeight0)
	{
		$('.checkout-bar li').each(function(i)
		{
		   var val=$(this).innerHeight(); // get the Height
		   //alert("element:"+i+" hiet="+val+" vs max="+maxHeight);
		   if(maxHeight-val>=17)
		   {
			$(this).before().addClass('before17');
		   }
		   else if(maxHeight-val>=34)
		   {
			$(this).before().addClass('before34');
		   }
		});
	}
};



