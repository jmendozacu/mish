/**
 * Vendor CMS App
 */
var PriceComparison = Class.create();
PriceComparison.prototype = {
	initialize: function(tableId,pagerId,data,config){
		this.table_id 		= tableId;
		this.table			= $(tableId);
		this.tableContainer = $(tableId+'-container');
		this.tableBody		= this.table.select('tbody').first();
		
		this.pager_id = pagerId;
		this.pager = $(pagerId);
		
		this.data			= data;
		
		this.sort_by		= config.default_sort;
		this.sort_direction = 'ASC';
		this.filters		= {};
		
		this.num_of_display_products = config.num_of_display_products;
		
		this.is_configurable = config.is_configurable;
		this.sp_config = typeof(spConfig)!= 'undefined'?spConfig:null;
		this.init();
	},
	/**
	 * Init
	 */
	init: function(){
		var _this = this;
		this.render();
		this.updateSelectedColumnClass();
		
		/*Add events to configurable options*/
		if(this.sp_config){
			this.sp_config.settings.each(function(element){
				element.observe('change',function(){
					_this.render();
				});
			});
		}
		
		/* Add events for sortable column */
		this.table.select('thead .sortable').each(function(el){
			el.observe('click',function(){
				_this.table.select('thead .selected').each(function(elHead){
					elHead.removeClassName('selected');
					elHead.removeClassName('asc');
					elHead.removeClassName('desc');
				});
				
				var sortBy = el.readAttribute('data-sortby');
				if(_this.sort_by == sortBy){
					if(_this.sort_direction == 'ASC') _this.sort_direction = 'DESC';
					else _this.sort_direction = 'ASC';
				}else{
					_this.sort_by = sortBy;
					_this.sort_direction = 'ASC';
				}
				_this.updateSelectedColumnClass();
				_this.render();
			});
		});
		
		
		/*Add event to the pager*/
		this.pager.select('a').first().observe('click',function(){
			var containerHeight = _this.table.getHeight();
			if(_this.tableContainer.hasClassName('explained')){
				containerHeight = _this.getContainerHeight();
				_this.tableContainer.removeClassName('explained');
				_this.pager.removeClassName('showing');
			}else{
				_this.tableContainer.addClassName('explained');
				_this.pager.addClassName('showing');
			}
			
			new Effect.Morph(_this.table_id+'-container', {
				style: 'height:'+containerHeight+'px',
				duration: 0.2
			});
		});
	},
	updateSelectedColumnClass: function(){
		this.table.select('thead .'+this.sort_by).first().addClassName('selected').addClassName(this.sort_direction.toLowerCase());
	},
	/**
	 * Set filter
	 * @param object filters
	 */
	setFilter: function(filters){
		this.filters = filters;
		this.render();
		return this;
	},
	
	/**
	 * Set direction
	 * @param object filters
	 */
	setSortDirection: function(direction){
		this.sort_direction = direction;
		this.render();
		return this;
	},
	
	/**
	 * Sort table 
	 */
	sort: function(){
		var newTableData = [];
		var _this = this;
		for(vendorId in _this.data){
			row = _this.data[vendorId];
			
			/*Check if the current row match with the filter.*/
			var check = true;
			for(x in _this.filters){
				if(row[x] != _this.filters[x] && row['data'][x]!= _this.filters[x]){check = false; break;}
			}
			
			/*Additional filter if the product is configurable product*/
			var check1 = true;
			if(_this.is_configurable && typeof(_this.sp_config) != 'undefined'){
				check1 = false;
				for(y in row.data.additional_info){
					var addInfo = row.data.additional_info[y];
					var addInfoCheck = true;
					_this.sp_config.settings.each(function(setting){
						if(setting.value && !setting.disabled){
							var attributeId = setting.attributeId;
							if(addInfo[attributeId] != setting.value) addInfoCheck = false;
						}
					});
					if(addInfoCheck){
						check1 = true;
						/*Update the price of configurable product to the simple product price.*/
						_this.data[vendorId]['column_values']['product_price'] = addInfo.price;
						_this.data[vendorId]['data']['qty'] = addInfo.qty;
					}
				}
			}
			
			if(check && check1){
				var isAddedToNewTableData = false;
				for(i = 0; i < newTableData.length; i ++){
					if(_this.sort_direction.toUpperCase() =='ASC'){
						if(newTableData[i]['column_values'][_this.sort_by] > row['column_values'][_this.sort_by]){
							for(j = newTableData.size(); j > i; j --){
								newTableData[j] = newTableData[j-1];
							}
							newTableData[i] = row;
							isAddedToNewTableData = true;
							break;
						}
					}else{
						if(newTableData[i]['column_values'][_this.sort_by] < row['column_values'][_this.sort_by]){
							for(j = newTableData.size(); j > i; j --){
								newTableData[j] = newTableData[j-1];
							}
							newTableData[i] = row;
							isAddedToNewTableData = true;
							break;
						}
					}
				}
				if(!isAddedToNewTableData){
					newTableData.push(row);
				}
			}
		};
		var content = '';
		if(newTableData.size()){
			newTableData.each(function(row){
				content += row.pricecomparison_html;
			});
		}else{
			content = '<tr><td colspan="1000">'+Translator.translate('No vendor is selling this product');+'</td></tr>';
		}
		return content;
	},
	/**
	 * Get height of container to show only max number of items.
	 */
	getContainerHeight: function(){
		var _this = this;
		var containerHeight = this.table.select('thead').first().getHeight(); /*Init the container heigh twith the height of table header */
		var i = 0;
		this.table.select('tbody tr').each(function(tr){
			if(i < _this.num_of_display_products){
				containerHeight += tr.getHeight();
			}
			i ++;
		});
		
		return containerHeight;
	},
	/**
	 * Hide all products if the number of products is greater than the max number of showing products.
	 */
	hideItems: function(){
		var _this = this;
		var numberOfCurrentItems = this.table.select('tbody tr').size();
		if(!this.num_of_display_products || (numberOfCurrentItems <= this.num_of_display_products)){
			this.tableContainer.setStyle({height: 'auto'});
			this.pager.hide();
		}else{
			if(!this.pager.hasClassName('showing')) {
				var containerHeight = this.getContainerHeight();
				this.tableContainer.setStyle({height: containerHeight+'px'});
			}
			this.pager.show();
		}
		
	},
	updatePrices: function(){
		/*Reupdate the vendor product price for configurable products only.*/
		if(!this.is_configurable) return;
		
		var _this = this;
		for(vendorId in this.data){
			if($('vendor-'+vendorId+'-price')){
				var price = _this.data[vendorId]["column_values"]["product_price"];
				price = _this.sp_config.formatPrice(price);
				$('vendor-'+vendorId+'-price').update('<span class="price">'+price+'</span>');
			}
		}
	},
	checkProductQty: function(){
		var _this = this;
		for(vendorId in this.data){
			if($('vendor-'+vendorId+'-price')){
				var qty = _this.data[vendorId]["data"]["qty"];
				
				if(parseFloat(qty)<=0){
					$$('.add_to_cart'+vendorId).first().update('<span class="out-of-stock">'+Translator.translate('Out of Stock')+'</span>');
				}else{
					$$('.add_to_cart'+vendorId).first().update(_this.data[vendorId]['column_html']['add_to_cart']);
				}
			}
		}
	},
	/**
	 * Render html code to the price comparison table.
	 */
	render: function(){
		var content = this.sort();
		this.tableBody.update(content);
		this.updatePrices();
		this.checkProductQty();
		this.hideItems();
	}
}