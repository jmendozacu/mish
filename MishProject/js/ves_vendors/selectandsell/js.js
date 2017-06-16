/**
 * Vendor Select And Sell
 */

function vesSelectAndSell(url,button){
	button.setAttribute('disabled','disabled');
	var buttonSpan = button.select('span span').first();
	buttonSpan.update(vesSelectAndSellTranslation.LOADING);
	new Ajax.Request(url, {
		onSuccess: function(transport) {
			$('vendor_product_container').update(transport.responseText);
			$('vendor_product_adding_tool').show();
			button.removeAttribute('disabled');
			buttonSpan.update(vesSelectAndSellTranslation.SELL_A_PRODUCT_LIKE_THIS);
	  	}
	});
}

function cancelSeletAndSell(){
	$('vendor_product_adding_tool').hide();
}