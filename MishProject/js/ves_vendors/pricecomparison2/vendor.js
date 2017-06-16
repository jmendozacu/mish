/**
 * Vendor CMS App
 */
var ConfigurableProductOptions = Class.create();
ConfigurableProductOptions.prototype = {
	initialize: function(tableId,data){
		this.table_id 		= tableId;
		this.table			= $(tableId);
		this.tableBody		= this.table.select('tbody').first();
		
		this.init();
	},
	/**
	 * Init
	 */
	init: function(){
		var _this = this;
		/*Disable all field for rows that the checkbox is not checked*/
		this.tableBody.select('.option-checkbox').each(function(checkbox){
			var simpleProductId = checkbox.readAttribute('data-product-id');
			_this.updateTextboxes(checkbox,simpleProductId);
			
			checkbox.observe('click',function(){
				var simplePId = checkbox.readAttribute('data-product-id');
				_this.updateTextboxes(checkbox,simplePId);
			});
			
			checkbox.up(1).select('td').each(function(td){
				td.observe('click',function(event){
					if(td.hasClassName('no-event')) return;
					
					var childCheckbox = this.up().select('.option-checkbox').first();
					if(event.element() == childCheckbox) return;
					var simplePId = childCheckbox.readAttribute('data-product-id');
					if(childCheckbox.checked){
						childCheckbox.checked = '';
					}else{
						childCheckbox.checked = 'checked';
					}
					_this.updateTextboxes(childCheckbox,simplePId);
				});
			});
			
		});
		
	},
	updateTextboxes: function(checkbox, simpleProductId){
		if(checkbox.checked){
			$('product-option-qty-'+simpleProductId).removeAttribute('disabled');
			$('product-option-price-'+simpleProductId).removeAttribute('disabled');
		}else{
			$('product-option-qty-'+simpleProductId).writeAttribute('disabled','disabled');
			$('product-option-price-'+simpleProductId).writeAttribute('disabled','disabled');
		}
	},
	test: function(){
		var content = this.sort();
		this.tableBody.update(content);
		this.hideItems();
	}
}