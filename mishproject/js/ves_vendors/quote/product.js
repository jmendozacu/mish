/**
 * Add prudct to quote
 * @param string url
 */
function addToQuote(url){
	var productForm = $('product_addtocart_form');
    if (productForm) {
        var validator = new Validation(productForm);
        if (validator)
            if (validator.validate()) {
            		var oldAction = productForm.readAttribute('action');
                	productForm.writeAttribute('action', url);
                    productForm.submit();
                    productForm.writeAttribute('action', oldAction);
            }
    }
}

/**
 * Submit a quote message
 * @param int quoteId
 * @param string messageId
 * @param string messageListId
 */
function submitQuoteMessage(quoteId, messageId, messageListId,customerEmail,button,loading){
	var message = $(messageId).value;
	if(!message){
		alert(Translator.translate('Please enter the message.'));
		return;
	}
	button.setAttribute('disabled','disabled');
	loading.show();
	new Ajax.Request(SEND_QUOTE_MESSAGE_URL, {
		method:'post',
		onComplete: function(transport) {
			var response = transport.responseText;
			if(response.isJSON()){
				response = response.evalJSON();
				if(response.success){
					$(messageListId).replace(response.message_list);
					$(messageId).value = '';
					button.removeAttribute('disabled');
					loading.hide();
				}else{
					button.removeAttribute('disabled');
					loading.hide();
					alert(response.msg);
					window.location.reload();
				}
			}else{
				button.removeAttribute('disabled');
				loading.hide();
				alert(response);
				window.location.reload();
			}
		},
		parameters: {
			quote_id: quoteId,
			message:message,
			email: customerEmail
		},
		onFailure: function() {
			alert(Translator.translate('Can not send the message. Please try again.')); 
		}
	});
}


/**
 * Update events for radio proposal button.
 * @param el
 */
function updateProposalRadioEvent(el){
	el.observe('click',function(){
		var itemId 		= el.readAttribute('data-item-id');
		var proposal_id = el.value;
		new Ajax.Request(UPDATE_DEFAULT_PROPOSAL_URL, {
			method:'post',
			onComplete: function(transport) {
				var response = transport.responseText;
				if(response.isJSON()){
					response = response.evalJSON();
					if(response.success){
						
					}else{
						alert(response.msg);
						window.location.reload();
					}
				}else{
					alert(response);
					window.location.reload();
				}
			},
			parameters: {
				proposal_id: proposal_id,
				quote_item_id:itemId
			},
			onFailure: function() {
				alert(Translator.translate('Can not save the default proposal. Please try again.')); 
			}
		});
	});
}

Event.observe(window, 'load', function(){
	$$('.proposal-radio').each(function(el){
		updateProposalRadioEvent(el);
	});
});