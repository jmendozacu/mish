/**
 * 
 */
 
function typeOfBannerChange(type, load)
{
	switch(type.value){
	case '0':		
		document.getElementById('easing').disabled='';		
		document.getElementById('display_description').disabled='';		
		document.getElementById('delay').disabled='';		    	
		obj = document.getElementById('easing');    	
		g = obj.getElementsByTagName('optgroup');    	
		$(g[0]).setStyle({'display':''});		
		$(g[1]).setStyle({'display':'none'});	
	break;				
	case '1':		
		document.getElementById('easing').disabled='';		
		document.getElementById('display_description').disabled='';		
		document.getElementById('delay').disabled='';		    	
		obj = document.getElementById('easing');    	
		g = obj.getElementsByTagName('optgroup');    	
		$(g[0]).setStyle({'display':'none'});		
		$(g[1]).setStyle({'display':''});	break;
    default:
    	document.getElementById('easing').disabled='';
    	document.getElementById('display_description').disabled='';
    	document.getElementById('delay').disabled='';
    	
		obj = document.getElementById('easing');
    	g = obj.getElementsByTagName('optgroup');
    	$(g[0]).setStyle({'display':''});
    	$(g[1]).setStyle({'display':'none'});

    }
	if(!load) document.getElementById('easing').selectedIndex = 0;
}

window.onload = function(){
	//typeOfBannerChange(document.getElementById('template'), true);
}



 function showEasyslideFieldset(el) {
	el.show();
	el.previous().show();
    var inputs = el.select("input");
     inputs.each(function(e){
         if(!e.hasClassName("required-entry"))
             e.addClassName("required-entry");
     });
}
function hideEasyslideFieldset(el) {
	el.hide();
	el.previous().hide();
	var inputs = el.select("input");
    inputs.each(function(e){
        if(e.hasClassName("required-entry"))
        e.removeClassName("required-entry");
    });
    
	var selects = el.select("select");
	selects.each(function(e){
        if(e.hasClassName("required-entry"))
        e.removeClassName("required-entry");
    });
}
document.observe('dom:loaded', function() {
	var easysliderTypeMapping = [
		$('prototype_fieldset'),
		$('nivo_fieldset')
	];
	
	$('template').observe('change', function() {
		var show = parseInt(this.getValue());
		var hide = (show == 0 ? 1 : 0);
		showEasyslideFieldset(easysliderTypeMapping[show]);
		hideEasyslideFieldset(easysliderTypeMapping[hide]);
	});
	var show = parseInt($('template').getValue());
	var hide = (show == 0 ? 1 : 0);
	showEasyslideFieldset(easysliderTypeMapping[show]);
	hideEasyslideFieldset(easysliderTypeMapping[hide]);
});