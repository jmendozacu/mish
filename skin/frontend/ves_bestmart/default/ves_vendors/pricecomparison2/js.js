if(typeof(Product.ConfigurableSwatches) !='undefined'){
	Product.ConfigurableSwatches.prototype.onOptionClickOrigin = Product.ConfigurableSwatches.prototype.onOptionClick;
	Product.ConfigurableSwatches.prototype.onOptionClick = function(attr){
		this.onOptionClickOrigin(attr);
		
		priceComparisonTable.render();
	};
}