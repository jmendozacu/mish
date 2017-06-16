/**
 * VES_VendorsQuote
 */

var NEW_ITEM_COUNT = 0;

/**
 * Add new proposal
 * @param itemId
 * @param itemOriginalPrice
 */
function addNewProposal(itemId, itemOriginalPrice){
	var newItemId = ++NEW_ITEM_COUNT;
	
	var newQtyHtml = '<li class="proposal-container" id="proposal-qty-new_'+newItemId+'"><input name="proposal['+itemId+'][new_'+newItemId+'][qty]" type="text" class="input-text required-entry validate-number validate-greater-than-zero proposal-qty" /></li>'
	$('proposal-qty-container-'+itemId).select('li').last().insert({after:newQtyHtml});
	$('proposal-qty-new_'+newItemId).select('input').first().observe('keyup',function(){
		Validation.reset($(this)); /*Reset the validation of the element*/
	});
	
	
	var newPriceHtml 	= '<div id="proposal-new_'+newItemId+'" class="clearer proposal-container">'
					+ ' <input type="radio" disabled="disabled" id="proposal-new_'+newItemId+'-radio" name="item['+itemId+'][default_proposal]" value="new_'+newItemId+'" data-item-id="'+itemId+'">'
					+ ' <input type="text" name="proposal['+itemId+'][new_'+newItemId+'][price]" value="'+itemOriginalPrice+'" size="3" class="required-entry validate-zero-or-greater validate-number input-text proposalprice" id="price-new_'+newItemId+'" data-proposal="new_'+newItemId+'" data-item-id="'+itemId+'"/>'
					+ ' <a class="remove-proposal" href="javascript: void(0);" onclick="removeProposal(\'new_'+newItemId+'\')">Remove</a> <a href="javascript: void(0);" class="save-proposal" style="display: inline-block;" onclick="saveProposal(\'new_'+newItemId+'\','+itemId+',true)">Save</a></div>';
	$('proposal-price-container-'+itemId).select('.proposal-container').last().insert({after:newPriceHtml});
	
	updateProposalPriceEvent($('price-new_'+newItemId));
	
	var newMarginHtml = '<li class="proposal-container" id="proposal-margin-new_'+newItemId+'">0%</li>'
	$('proposal-margin-container-'+itemId).select('li').last().insert({after:newMarginHtml});
}

/**
 * Remove a proposal
 * @param proposalId
 */
function removeProposal(proposalId,isRemoveExistProposal){
	if($('proposal-'+proposalId+'-radio').checked){
		alert(Translator.translate('You can not remove default proposal.'));
		return;
	}
	if(typeof(isRemoveExistProposal) != 'undefined' && isRemoveExistProposal){
		if(confirm(Translator.translate('Are you sure?'))){
			new Ajax.Request(REMOVE_PROPOSAL_URL, {
				method:'post',
				onComplete: function(transport) {
					/*process the response*/
					var response = transport.responseText;
					if(response.isJSON()){
						response = response.evalJSON();
						if(response.success){
							$('proposal-'+proposalId).remove();
							$('proposal-qty-'+proposalId).remove();
							$('proposal-margin-'+proposalId).remove();
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
					proposal_id: proposalId,
				},
				onFailure: function() {
					alert(Translator.translate('Can not remove the proposal. Please try again.')); 
				}
			});
		}
	}else{
		$('proposal-'+proposalId).remove();
		$('proposal-qty-'+proposalId).remove();
		$('proposal-margin-'+proposalId).remove();
	}
}

/**
 * Save proposal of a quote item
 * @param int proposalId
 * @param int itemId
 * @param boolean isNewObject
 */
function saveProposal(proposalId,itemId,isNewObject){
	var saveBtn = $('proposal-'+proposalId).select('.save-proposal').first();
	
	if(saveBtn.hasClassName('loading')) return; /*The save button is now loading icon so just do nothing.*/
	if(Validation.validate($('price-'+proposalId))){
		var qtyObj 			= $('proposal-qty-'+proposalId).select('input').first();
		var proposalPrice 	= $('price-'+proposalId).value;
		var proposalQty		= qtyObj?qtyObj.value:$('proposal-qty-'+proposalId).innerHTML;
		
		if(typeof(isNewObject) != 'undefined' && isNewObject){
			if(!Validation.validate(qtyObj)) return;
		}

		saveBtn.toggleClassName('loading');
		new Ajax.Request(SAVE_PROPOSAL_URL, {
			method:'post',
			onComplete: function(transport) {
				saveBtn.toggleClassName('loading');
				saveBtn.hide();
				/*process the response*/
				var response = transport.responseText;
				if(response.isJSON()){
					response = response.evalJSON();
					if(response.success){
						$('proposal-qty-'+proposalId).replace(response.qty_html);
						$('proposal-'+proposalId).replace(response.price_html);
						$('proposal-margin-'+proposalId).replace(response.margin_html);
						
						/*Update events*/
						updateProposalPriceEvent($('price-'+response.proposal_id));
						updateProposalRadioEvent($('proposal-'+response.proposal_id+'-radio'));

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
				proposal_id: isNewObject?'':proposalId,
				price: proposalPrice,
				qty:proposalQty,
				quote_item_id:itemId,
				is_first:$('proposal-'+proposalId).hasClassName('first')?1:0
			},
			onFailure: function() {
				saveBtn.toggleClassName('loading');
				alert(Translator.translate('Can not save the proposal. Please try again.')); 
			}
		});
	}
}

function saveAllProposals(itemId){
	var data = {};
	var validate = true;
	$('proposal-price-container-'+itemId).select('.proposalprice').each(function(proposalPriceObj){
		var proposalId = proposalPriceObj.readAttribute('data-proposal');
		if(Validation.validate($('price-'+proposalId))){
			var qtyObj 			= $('proposal-qty-'+proposalId).select('input').first();
			var proposalPrice 	= $('price-'+proposalId).value;
			var proposalQty		= qtyObj?qtyObj.value:$('proposal-qty-'+proposalId).innerHTML;
			
			if(typeof(qtyObj) != 'undefined'){
				if(!Validation.validate(qtyObj)) validate = false;
			}
			data[proposalId]={qty:proposalQty, price:proposalPrice,new_object:qtyObj?1:0};
		}else{
			validate = false;
		}
	});
	
	if(!validate) {alert(Translator.translate('Data is not valid.'));return;}
	for(proposalId in data){
		var saveBtn = $('proposal-'+proposalId).select('.save-proposal').first();
		saveBtn.addClassName('loading');
		saveBtn.show();
	}
	
	new Ajax.Request(SAVE_ALL_PROPOSAL_URL, {
		method:'post',
		onComplete: function(transport) {
			saveBtn.toggleClassName('loading');
			saveBtn.hide();
			/*process the response*/
			var response = transport.responseText;
			if(response.isJSON()){
				response = response.evalJSON();
				if(response.success){
					$('proposal-qty-container-'+itemId).update(response.qty_html);
					$('proposal-price-container-'+itemId).update(response.price_html);
					$('proposal-margin-container-'+itemId).update(response.margin_html);
					
					/*Update events*/
					$('proposal-price-container-'+itemId).select('.proposalprice').each(function(el){
						updateProposalPriceEvent(el);
					});
					
					$('proposal-price-container-'+itemId).select('.proposal-radio').each(function(el){
						updateProposalRadioEvent(el);
					});
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
			quote_item_id:itemId,
			data: Object.toJSON(data)
		},
		onFailure: function() {
			for(proposalId in data){
				var saveBtn = $('proposal-'+proposalId).select('.save-proposal').first();
				saveBtn.removeClassName('loading');
			}
			alert(Translator.translate('Can not save the proposals. Please try again.')); 
		}
	});
}

/**
 * Remove quote item
 * @param itemId
 */
function removeQuoteItem(itemId){
	if(!confirm(Translator.translate('Are you sure?'))){
		return;
	}
	
	new Ajax.Request(REMOVE_QUOTE_ITEM_URL, {
		method:'post',
		onComplete: function(transport) {
			/*process the response*/
			var response = transport.responseText;
			if(response.isJSON()){
				response = response.evalJSON();
				if(response.success){
					$('vendorsquote-items').replace(response.items);
		        	vendorsQuoteInitEvents();
					
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
			quote_item_id:itemId,
		},
		onFailure: function() {
			alert(Translator.translate('Can not remove the item. Please try again.')); 
		}
	});
}


/**
 * Update Proposal Price Input Event
 * @param el
 */
function updateProposalPriceEvent(el){
	el.observe('keyup',function(){
		Validation.reset(el); /*Reset the validation of the element*/
		var saveBtn = el.up().select('.save-proposal').first();
		if(el.value != el.defaultValue){
			saveBtn.show();
		}else{
			saveBtn.hide();
		}
		
		/*Calculate the margin*/
		
		var value 		= el.value;
		var marginObj 	= $('proposal-margin-'+el.readAttribute('data-proposal'));
		var itemOriginPrice	= $('item-'+el.readAttribute('data-item-id')+'-origin-price').value;
		if(!isNaN(value)){
			marginObj.update(Math.round((value-itemOriginPrice)*100 / itemOriginPrice)+'%');
		}else{
			marginObj.update('0%');
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

/**
 * Submit a quote message
 * @param int quoteId
 * @param string messageId
 * @param string messageListId
 * @param boolean isNotifyCustomer
 */
function submitQuoteMessage(quoteId, messageId, messageListId, isNotifyCustomer){
	var message = $(messageId).value;
	if(!message){
		alert(Translator.translate('Please enter the message.'));
		return;
	}
	new Ajax.Request(SEND_QUOTE_MESSAGE_URL, {
		method:'post',
		onComplete: function(transport) {
			var response = transport.responseText;
			if(response.isJSON()){
				response = response.evalJSON();
				if(response.success){
					$(messageListId).replace(response.message_list);
					$(messageId).value = '';
					if($('message_notify')) $('message_notify').checked = '';
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
			quote_id: quoteId,
			message:message,
			is_notify_customer: isNotifyCustomer?1:0
		},
		onFailure: function() {
			alert(Translator.translate('Can not send the message. Please try again.')); 
		}
	});
}
/**
 * Init price event and radio event
 */
function vendorsQuoteInitEvents(){
	$$('.quote-tables .proposalprice').each(function(el){
		updateProposalPriceEvent(el);
	});
	
	$$('.proposal-radio').each(function(el){
		updateProposalRadioEvent(el);
	});
}


Event.observe(window, 'load', function(){
	vendorsQuoteInitEvents();
});

var Quotation = new Class.create();
Quotation.prototype = {
	initialize : function(data){
		if(!data) data = {};
		this.gridProducts   = $H({});
		this.productPriceBase = {};
		this.currencySymbol = data.currency_symbol ? data.currency_symbol : '';
		this.loadBaseUrl = data.load_base_url ? data.load_base_url : '';
		this.grid = null;
	},
	setGrid: function(grid){
		this.grid = grid;
	},
	
	showSearchProductGrid: function(){
		$('vendors-quote-search-items').show();
		$('show-search-product-btn').hide();
	},
	
	hideSearchProductGrid: function(){
		$('vendors-quote-search-items').hide();
		$('show-search-product-btn').show();
	},
	
	productGridCheckboxCheck : function(grid, element, checked){
        if (checked) {
            if(element.inputElements) {
                this.gridProducts.set(element.value, {});
                var product = this.gridProducts.get(element.value);
                for (var i = 0; i < element.inputElements.length; i++) {
                    var input = element.inputElements[i];
                    if (!input.hasClassName('input-inactive')) {
                        input.disabled = false;
                        if (input.name == 'qty' && !input.value) {
                            input.value = 1;
                        }
                    }

                    if (input.checked || input.name != 'giftmessage') {
                        product[input.name] = input.value;
                    } else if (product[input.name]) {
                        delete(product[input.name]);
                    }
                }
            }
        } else {
            if(element.inputElements){
                for(var i = 0; i < element.inputElements.length; i++) {
                    element.inputElements[i].disabled = true;
                }
            }
            this.gridProducts.unset(element.value);
        }
        grid.reloadParams = {'products[]':this.gridProducts.keys()};
    },
    productGridRowClick : function(grid, event){
        var trElement = Event.findElement(event, 'tr');
        var qtyElement = trElement.select('input[name="qty"]')[0];
        var eventElement = Event.element(event);
        var isInputCheckbox = eventElement.tagName == 'INPUT' && eventElement.type == 'checkbox';
        var isInputQty = eventElement.tagName == 'INPUT' && eventElement.name == 'qty';
        if (trElement && !isInputQty) {
            var checkbox = Element.select(trElement, 'input[type="checkbox"]')[0];
            var confLink = Element.select(trElement, 'a')[0];
            var priceColl = Element.select(trElement, '.price')[0];
            if (checkbox) {
                // processing non composite product
                if (confLink.readAttribute('disabled')) {
                    var checked = isInputCheckbox ? checkbox.checked : !checkbox.checked;
                    grid.setCheckboxChecked(checkbox, checked);
                // processing composite product
                } else if (isInputCheckbox && !checkbox.checked) {
                    grid.setCheckboxChecked(checkbox, false);
                // processing composite product
                } else if (!isInputCheckbox || (isInputCheckbox && checkbox.checked)) {
                    var listType = confLink.readAttribute('list_type');
                    var productId = confLink.readAttribute('product_id');
                    if (typeof this.productPriceBase[productId] == 'undefined') {
                        var priceBase = priceColl.innerHTML.match(/.*?([\d,]+\.?\d*)/);
                        if (!priceBase) {
                            this.productPriceBase[productId] = 0;
                        } else {
                            this.productPriceBase[productId] = parseFloat(priceBase[1].replace(/,/g,''));
                        }
                    }
                    productConfigure.setConfirmCallback(listType, function() {
                        // sync qty of popup and qty of grid
                        var confirmedCurrentQty = productConfigure.getCurrentConfirmedQtyElement();
                        if (qtyElement && confirmedCurrentQty && !isNaN(confirmedCurrentQty.value)) {
                            qtyElement.value = confirmedCurrentQty.value;
                        }
                        // calc and set product price
                        var productPrice = parseFloat(this._calcProductPrice() + this.productPriceBase[productId]);
                        priceColl.innerHTML = this.currencySymbol + productPrice.toFixed(2);
                        // and set checkbox checked
                        grid.setCheckboxChecked(checkbox, true);
                    }.bind(this));
                    productConfigure.setCancelCallback(listType, function() {
                        if (!$(productConfigure.confirmedCurrentId) || !$(productConfigure.confirmedCurrentId).innerHTML) {
                            grid.setCheckboxChecked(checkbox, false);
                        }
                    });
                    productConfigure.setShowWindowCallback(listType, function() {
                        // sync qty of grid and qty of popup
                        var formCurrentQty = productConfigure.getCurrentFormQtyElement();
                        if (formCurrentQty && qtyElement && !isNaN(qtyElement.value)) {
                            formCurrentQty.value = qtyElement.value;
                        }
                    }.bind(this));
                    productConfigure.showItemConfiguration(listType, productId);
                }
            }
        }
    },
    /**
     * Calc product price through its options
     */
    _calcProductPrice: function () {
        var productPrice = 0;
        var getPriceFields = function (elms) {
            var productPrice = 0;
            var getPrice = function (elm) {
                var optQty = 1;
                if (elm.hasAttribute('qtyId')) {
                    if (!$(elm.getAttribute('qtyId')).value) {
                        return 0;
                    } else {
                        optQty = parseFloat($(elm.getAttribute('qtyId')).value);
                    }
                }
                if (elm.hasAttribute('price') && !elm.disabled) {
                    return parseFloat(elm.readAttribute('price')) * optQty;
                }
                return 0;
            };
            for(var i = 0; i < elms.length; i++) {
                if (elms[i].type == 'select-one' || elms[i].type == 'select-multiple') {
                    for(var ii = 0; ii < elms[i].options.length; ii++) {
                        if (elms[i].options[ii].selected) {
                            productPrice += getPrice(elms[i].options[ii]);
                        }
                    }
                }
                else if (((elms[i].type == 'checkbox' || elms[i].type == 'radio') && elms[i].checked)
                        || ((elms[i].type == 'file' || elms[i].type == 'text' || elms[i].type == 'textarea' || elms[i].type == 'hidden')
                            && Form.Element.getValue(elms[i]))
                ) {
                    productPrice += getPrice(elms[i]);
                }
            }
            return productPrice;
        }.bind(this);
        productPrice += getPriceFields($(productConfigure.confirmedCurrentId).getElementsByTagName('input'));
        productPrice += getPriceFields($(productConfigure.confirmedCurrentId).getElementsByTagName('select'));
        productPrice += getPriceFields($(productConfigure.confirmedCurrentId).getElementsByTagName('textarea'));
        return productPrice;
    },
    productGridRowInit : function(grid, row){
        var checkbox = $(row).select('.checkbox')[0];
        var inputs = $(row).select('.input-text');
        if (checkbox && inputs.length > 0) {
            checkbox.inputElements = inputs;
            for (var i = 0; i < inputs.length; i++) {
                var input = inputs[i];
                input.checkboxElement = checkbox;

                var product = this.gridProducts.get(checkbox.value);
                if (product) {
                    var defaultValue = product[input.name];
                    if (defaultValue) {
                        if (input.name == 'giftmessage') {
                            input.checked = true;
                        } else {
                            input.value = defaultValue;
                        }
                    }
                }

                input.disabled = !checkbox.checked || input.hasClassName('input-inactive');

                Event.observe(input,'keyup', this.productGridRowInputChange.bind(this));
                Event.observe(input,'change',this.productGridRowInputChange.bind(this));
            }
        }
    },
    productGridRowInputChange : function(event){
        var element = Event.element(event);
        if (element && element.checkboxElement && element.checkboxElement.checked){
            if (element.name!='giftmessage' || element.checked) {
                this.gridProducts.get(element.checkboxElement.value)[element.name] = element.value;
            } else if (element.name=='giftmessage' && this.gridProducts.get(element.checkboxElement.value)[element.name]) {
                delete(this.gridProducts.get(element.checkboxElement.value)[element.name]);
            }
        }
    },
    
    /**
     * Submit configured products to quote
     */
    productGridAddSelected : function(){
        if(this.productGridShowButton) Element.show(this.productGridShowButton);
        if(!this.grid.reloadParams || !this.grid.reloadParams["products[]"].size()) {
        	alert(Translator.translate('Please select an item'));
        	return;
        }
        var area = ['search', 'items'];
        // prepare additional fields and filtered items of products
        var fieldsPrepare = {};
        var itemsFilter = [];
        var products = this.gridProducts.toObject();
        for (var productId in products) {
            itemsFilter.push(productId);
            var paramKey = 'item['+productId+']';
            for (var productParamKey in products[productId]) {
                paramKey += '['+productParamKey+']';
                fieldsPrepare[paramKey] = products[productId][productParamKey];
            }
        }
        this.productConfigureSubmit('product_to_add', area, fieldsPrepare, itemsFilter);
        productConfigure.clean('quote_items');
        this.gridProducts = $H({});
    },
    /**
     * Submit batch of configured products
     *
     * @param listType
     * @param area
     * @param fieldsPrepare
     * @param itemsFilter
     */
    productConfigureSubmit : function(listType, area, fieldsPrepare, itemsFilter) {
        // prepare loading areas and build url
        this.loadingAreas = area;
        var url = this.loadBaseUrl + 'block/' + area + '?isAjax=true';

        // prepare additional fields
        fieldsPrepare = this.prepareParams(fieldsPrepare);
        fieldsPrepare.reset_shipping = 1;
        fieldsPrepare.json = 1;

        // create fields
        var fields = [];
        for (var name in fieldsPrepare) {
            fields.push(new Element('input', {type: 'hidden', name: name, value: fieldsPrepare[name]}));
        }

        productConfigure.addFields(fields);

        // filter items
        if (itemsFilter) {
            productConfigure.addItemsFilter(listType, itemsFilter);
        }

        // prepare and do submit
        productConfigure.addListType(listType, {urlSubmit: url});
        productConfigure.setOnLoadIFrameCallback(listType, function(response){
        	//this.grid.reloadParams = null;
        	//this.grid.reload();
        	/*Replace the items block by a new one.*/
        	$('vendorsquote-items').replace(response.items);
        	this.hideSearchProductGrid();
        	$(this.grid.containerId).select('.checkbox,.qty').each(function(el){
        		if(el.type == 'checkbox') el.checked = '';
        		else el.value = '';
        	});
        	vendorsQuoteInitEvents();
        	
        }.bind(this));
        
        productConfigure.submit(listType);
        // clean
        this.productConfigureAddFields = {};
    },
    prepareParams : function(params){
        if (!params) {
            params = {};
        }
/*        if (!params.customer_id) {
            params.customer_id = this.customerId;
        }
        if (!params.store_id) {
            params.store_id = this.storeId;
        }
        if (!params.currency_id) {
            params.currency_id = this.currencyId;
        }*/
        if (!params.form_key) {
            params.form_key = FORM_KEY;
        }

        return params;
    },
}