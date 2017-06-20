checkVendorId(vendor,url){
	$('vendor-loading').show();
	new Ajax.Request(url, {
		parameters: {vendor_id: vendor},
		onSuccess: function(transport) {
			$('vendor-loading').hide();
	  	}
	});
}