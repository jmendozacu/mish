window.onload=function(){
	
	entry_type='entry_type';
	entry_type_value=document.getElementById("entry_type").value;	
	
	entry_url='entry_url';
	entry_url_value=document.getElementById("entry_url").value;	
	
	entry_catalogtype='entry_catalogtype';
	entry_catalogtype_value=document.getElementById("entry_catalogtype").value;	
	Cookies.create(entry_type,entry_type_value,50);
	Cookies.create(entry_url,entry_url_value,50);
	Cookies.create(entry_catalogtype,entry_catalogtype_value,50);
}
function addUrlToCookie(ele){
	ulr=ele.value;
	id=ele.id;
	Cookies.create(id,ulr,50);
}
function addFieldMapping()
{
	var entityType = $('profile_entity_type').value;
	Element.insert($('map_container_'+entityType), {bottom: $('map_template_'+entityType).innerHTML});
}
function removeFieldMapping(button)
{
	Element.remove(button.parentNode);
}
function setMapFileField(select)
{
	select.parentNode.getElementsByTagName('input')[0].value = select.value;
}
/* COOKIES OBJECT */
var Cookies = {
        // Initialize by splitting the array of Cookies
	init: function () {
		var allCookies = document.cookie.split('; ');
		for (var i=0;i<allCookies.length;i++) {
			var cookiePair = allCookies[i].split('=');
			this[cookiePair[0]] = cookiePair[1];
		}
	},
        // Create Function: Pass name of cookie, value, and days to expire
	create: function (name,value,days) {
		if (days) {
			var date = new Date();
			date.setTime(date.getTime()+(days*24*60*60*1000));
			var expires = "; expires="+date.toGMTString();
		}
		else var expires = "";
		document.cookie = name+"="+value+expires+"; path=/";
		this[name] = value;
	},
        // Erase cookie by name
	erase: function (name) {
		this.create(name,'',-1);
		this[name] = undefined;
	}
};
Cookies.init();