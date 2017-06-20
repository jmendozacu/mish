/**
 * Vendor CMS App
 */
var LayoutUpdate = Class.create();
LayoutUpdate.prototype = {
	initialize: function(containerId,config, translation){
		this.container_id 	= containerId;
		this.container		= $(containerId);
		this.config			= config;
		this.translation	= translation;
		this.number			= 0;
	},
	init: function(options){
		var _this = this;
		options.each(function(data){
			_this.processOption(data);
		});
	},
	/**
	 * Add new frontend instance
	 */
	addLayoutUpdate: function(){
		var template = this.getPageGroupsTemplate();
		var appTemplateSyntax = /(^|.|\\r|\\n)({{(\w+)}})/;
		var template = new Template(template, appTemplateSyntax);
        var html = template.evaluate({number:this.number});
		this.container.insert(html);
		this.number ++;
	},
	
	/**
	 * Add new frontend instance with value
	 */
	processOption: function(data){
		var _this = this;
		/*Fire Event depend on type of option*/
		this.container.fire("vendorscms:process_option_frontend_app_options", { data: data,frontendInstance:_this});
		this.container.fire("vendorscms:process_option_"+data.type, { data: data,frontendInstance:_this});
	},
	getPageGroupsTemplate:function(){
		var html = '<div id="page_group_container_{{number}}"><div class="option-box">';
		html += '<input type="hidden" name="frontend_instance[{{number}}][option_id]" id="frontend_instance_{{number}}_option_id" value="" />';
		html += '<div class="option-title"><button onclick="frontendInstance.removePageGroup(this)" class="scalable delete" type="button" title="'+this.translation.REMOVE_LAYOUT_UPDATE+'" id="remove_layout_update_{{number}}"><span><span><span>'+this.translation.REMOVE_LAYOUT_UPDATE+'</span></span></span></button><label for="frontend_instance[{{number}}][page_group]">'+this.translation.DISPLAY_ON+' <span class="required">*</span></label>'+this.getPageGroupsSelectTemplate()+'</div>'
		html += '</div></div>';
		return html;
	},
	getPageGroupsSelectTemplate: function(){
		var select = '<select id="frontend_instance_{{number}}_page_group" name="frontend_instance[{{number}}][page_group]" class="required-entry select page_group_select" onchange="$(this).fire(\'vendorscms:page_group_field_change\',{ number: {{number}}});">';
		select += '<option value="">'+ this.translation.PLEASE_SELECT+'</option>';
		this.config.each(function(s){
			select +='<option value="'+s.name+'">'+s.title+'</option>';
		});
		select += '</select>';
		return select;
	},
	
	/**
	 * Display Page Group
	 */
	displayPageGroup:function(number){
		var selectedTemplate = $('frontend_instance_'+number+'_page_group').value;
		if(selectedTemplate){
			var pageGroupTemplate;
			this.config.each(function(s){
				if(s.name == selectedTemplate) pageGroupTemplate = s.template;
			});
			
			var appTemplateSyntax = /(^|.|\\r|\\n)({{(\w+)}})/;
			var template = new Template(pageGroupTemplate, appTemplateSyntax);
	        var html = template.evaluate({number:number});
	        
			if($('page_group_'+number)){
				$('page_group_'+number).update(html);
			}else{
				$$('#page_group_container_'+number+' .option-box').first().insert('<div id="page_group_'+number+'" class="page_group">'+html+'</div>');
			}
		}else{
			if($('page_group_'+number)){
				$('page_group_'+number).remove();
			}
		}
	},
	
	/**
	 * Remove Page Group
	 */
	removePageGroup:function(obj){
		var removeButton = $(obj);
		removeButton.up(2).remove();
	}
}
/**
 * Handle frontend instance fields.
 */
document.observe("vendorscms:process_option_frontend_instance", function(event) {
	var data 	= event.memo.data;
	var _this 	= event.memo.frontendInstance;
	/*Add new frontend instance*/
	var template = _this.getPageGroupsTemplate();
	var appTemplateSyntax = /(^|.|\\r|\\n)({{(\w+)}})/;
	var template = new Template(template, appTemplateSyntax);
    var html = template.evaluate({number:_this.number});
    _this.container.insert(html);
    
    /*Update field values*/
	data.options.each(function(s){
		var field = $(data.type+'_'+_this.number+'_'+s.field);
		if(field){
			if((typeof(s.value) == 'object') && field.tagName == 'SELECT' && field.getAttribute('multiple')){
				/*Multiple select*/
				field.select('option').each(function(option){
					if(s.value.indexOf(option.value) != -1){
						option.setAttribute('selected','selected');
					}
				});
			}else{
				field.value = s.value;
			}
		}
		if(s.field == 'page_group'){
			_this.container.fire('vendorscms:page_group_field_change',{ number: _this.number});
		}
	});
	
	_this.number ++;
});


document.observe("vendorscms:process_option_frontend_app_options", function(event) {
	var data 	= event.memo.data;
	var _this 	= event.memo.frontendInstance;
	console.log(data);
	/*Update field values*/
	data.options.each(function(s){
		var field = $('app_'+data.type+'_'+s.field);
		if(field){
			field.value = s.value;
		}
	});
});


document.observe("vendorscms:page_group_field_change", function(event) {
	var number 	= event.memo.number;
	frontendInstance.displayPageGroup(number);
});