    function initializeGoogleMap() {
            var mapOptions = {
              zoom: 13,
              mapTypeId: google.maps.MapTypeId.ROADMAP,
              center: initPosition
            };

            map = new google.maps.Map(document.getElementById('map_canvas'),
                    mapOptions);

            for (var i = 0; i < neighborhoods.length; i++) {
                markers.push(new google.maps.Marker({
                    title:'This is title of store',
                    map:map,
                    draggable:false,
                    animation: google.maps.Animation.DROP,

                    position: neighborhoods[i]
                }));

            }

            for(var i = 0; i < markers.length; i++){
                var marker = markers[i];
                attachSecretMessage(marker,i);
            }

            directionsDisplay = new google.maps.DirectionsRenderer();
            geocoder = new google.maps.Geocoder();
            directionsDisplay.setMap(map);



            var input = document.getElementById('searchTextField');

            var autocomplete = new google.maps.places.Autocomplete(input);

            autocomplete.bindTo('bounds', map);
            autocomplete.setTypes(['geocode']);

            google.maps.event.addListener(autocomplete, 'place_changed', function() {
              var place = autocomplete.getPlace();
              if (!place.geometry) {
                  return;
              }
              if (place.geometry.viewport) {
                map.fitBounds(place.geometry.viewport);
              } else {
                map.setCenter(place.geometry.location);
                map.setZoom(17);  // Why 17? Because it looks good.
              }

              if(customer_marker_check){
                  if(customer_marker) refeshAddress();
                  findYourAddress(place.geometry.location);
              }

              if(new_markers  != null ){
                      new_markers.setPosition(place.geometry.location);
              }

              if(edit_marker != null){
                  edit_marker.setPosition(place.geometry.location);
              }
            });

  }
    
  function attachSecretMessage(marker, num) {
	      var infowindow = new google.maps.InfoWindow({ 
	  		content: '<div style="width:270px;">This is test <strong>message</strong></div>'
	      });
    	  updateInfoWindow(messages[num],infowindow,$("logo_url_"+num).value);
    	  google.maps.event.addListener(marker, 'click', function() {
    		for (var i = 0; i < infowindows.length; i++) {
    			  infowindows[i].close();
    		}
    		$$('#address_list input[type="radio"]').each(function(c){
			      if($(c).value == num) $(c).checked = true ;
    		});
    	    infowindow.open(marker.get('map'), marker);
    	    infowindows.push(infowindow);
             $j(".map-colorbox-logo").colorbox({rel:'map-colorbox-logo'});
    	  });
    	  google.maps.event.addListener(marker, 'position_changed', function() {
  			$('map_position').value =getCurrentPosition(marker);
  		  });

          return true;
  }

    
  function getCurrentPosition(marker){
	var x = marker.getPosition().lat();
	var y = marker.getPosition().lng();
	return x+'|'+y;
  }
  
  function updateInfoWindow(message,infowindow,logo){
	  html = '<div style="width:300px;">';
      html += '<div style="width: 80px;float: left; margin-right:10px;" id="map-marker-logo">';
	  if(logo) {html += '<a class="map-colorbox-logo" href="'+logo+'"><img src="'+logo+'" width="80" style="display: block;float: left; margin-right:10px;" /></a>';}
      html += '</div>';
	  html += '<div style="float: right;width:210px;">';
	  html +='<h3 id="map-marker-title" style="color:#3399cc">'+message[0]+'</h3>';
      html +='<p style="font-weight: bold;">';
      html += '<span id="map-marker-address">'+message[1]+'</span>,';
      html += '<span id="map-marker-city">'+message[2]+'</span>,';
      if(message[3] != "" && message[3] != null){
          html += '<span id="map-marker-region">'+message[3]+'</span>,';
      }
      html += '<span id="map-marker-country">'+message[4]+'</span>';
      html += '</p>';
	  html +='<p id="map-marker-telephone">'+message[5]+'</p>';
      html +='<p id="map-marker-postcode">'+message[6]+'</p>';
	  html += '</div>';
	  html +='</div>';
	  infowindow.setContent(html);
      $j(".map-colorbox-logo").colorbox({rel:'map-colorbox-logo'});
  }
  
  function findMapAddress(lat,lng,num){
	  var latlng = new google.maps.LatLng(lat,lng);
	  map.panTo(latlng);
	  for (var i = 0; i < infowindows.length; i++) {
		  infowindows[i].close();
	  }
	  var infowindow = new google.maps.InfoWindow({ 
		  		content: '<div style="width:270px;">This is test <strong>message</strong></div>'
	  });
	  updateInfoWindow(messages[num],infowindow,$("logo_url_"+num).value);
	  infowindow.open(markers[num].get('map'), markers[num]);
	  infowindows.push(infowindow);
      $j(".map-colorbox-logo").colorbox({rel:'map-colorbox-logo'});
  }
  
  function newMapAddress(){
      if(new_markers) {
          new_markers.setMap(null);
          new_markers = null;
      }
      $('new-map-address-form').reset();
	  $('new-map-address').show();
	  var marker = new google.maps.Marker({
		    position: map.getCenter(),
		    map: map,
		    draggable:true,

	 });
	 map.panTo(map.getCenter());
	 for (var i = 0; i < infowindows.length; i++) {
      	  infowindows[i].close();
     }
	 html = '<div style="width:300px;">';
	 html += '<div style="width: 80px;float: left; margin-right:10px;" id="map-marker-logo">';
	 if($("logo_url").value) {
		 html += '<img src="'+$("logo_url").value+'" width="80" />';
	 }
	 html += '</div>';
	 html += '<div style="float: right;width:210px;">';
	 html +='<h3 id="map-marker-title" style="color:#3399cc">Title</h3><p style="font-weight: bold;">';
     html += '<span id="map-marker-address">Address</span>,';
     html += '<span id="map-marker-city">City</span>,';
     html += '<span id="map-marker-region">State</span>,';
     html += '<span id="map-marker-country">Country</span>';
     html += '</p>';
	 html +='<p id="map-marker-telephone">Telephone</p>';
     html +='<p id="map-marker-postcode">Postcode</p>';
	 html += '</div>';
	 html +='</div>';
	 var infowindow = new google.maps.InfoWindow({ 
	  		content: html
	 });
	 infowindow.open(map,marker);

	 new_markers = marker;
	 $('map_position').value =getCurrentPosition(marker);
	 google.maps.event.addListener(marker, 'position_changed', function() {
			$('map_position').value =getCurrentPosition(marker);
	 });
  }
  
  function cancelForm(){
	  $('new-map-address').hide();
	  $('new-map-address-form').reset();
      new_markers.setMap(null);
      new_markers = null;
      var image = $("map_logo").value;
      if(image != null && image != "")	deleteLogo(image,false);

	  $$('#address_list input[type="radio"]:checked').each(function(c){
	      $(c).checked = false;
	  });
  }
  
  function cancelFormEdit(){
        var image = $("map_logo_edit").value;
        if(image != null && image != "") deleteLogo(image,false);
		var disblel_num = $("marker_num").value ;
		var position = new google.maps.LatLng($("map_position_current_lat").value , $("map_position_current_lng").value );
		markers[disblel_num].setDraggable(false);
        $("edit-map-address-form").up().up().removeClassName("active-edit");
		$("edit-map-address-form").up().previous().show();
		$("edit-map-address-form").up().hide();
		$("edit-map-address-form").up().update(null);
		markers[disblel_num].setMap(null);
		var marker = new google.maps.Marker({
			    position: position,
			    map: map,
			    draggable:false,
		});
		markers[disblel_num] = marker;
		attachSecretMessage(marker,disblel_num);
		  $$('#address_list input[type="radio"]:checked').each(function(c){
		      $(c).checked = false;
		  });
  }
  function deleteMarker(map_id , num){
	  if(!confirm("Are you sure!")) return;
	  new Ajax.Request(DELETE_MARKER_URL, {
			method:'post',
			parameters:{id: map_id},
			onCreate: function(obj) {
				  Element.show('loading-mask');
				  },
			onSuccess: function(transport) {
				try {
          			if (transport.responseText.isJSON()) {
          				var response = transport.responseText.evalJSON();
      	                if(response.redirect) {window.location = (response.redirect);return;}
      	                if(response.success){
          	                $('address_list').update(response.list);
          	                for (var i = 0; i < infowindows.length; i++) {
          	            	  infowindows[i].close();
          	              	}
          	                messages.splice(num,1);
          	                markers[num].setMap(null);
          	                markers.splice(num,1);
                            if(markers.length) {
                                findMapAddress(markers[markers.length - 1].getPosition().lat(), markers[markers.length - 1].getPosition().lng(), messages.length - 1);
                            }
          	                $$('#address_list input[type="radio"]').each(function(c){
             			      if($(c).value == messages.length - 1) $(c).checked = true ;
          	              	});	
          	              
          	                for(var i = 0; i < markers.length; i++){
          	                	var marker = markers[i];
          	                	attachSecretMessage(marker,i);
          	                }
      	                }else{
          	                alert(response.msg);
      	                }
          			}else{
      	                alert(transport.responseText);
          			}
                }catch(e){console.log(e);}
			},
		    onComplete:function(transport){
			    Element.hide('loading-mask');
		    }
	});
  }
  
  function showFormEdit(map_id,num){
	  new Ajax.Request(LOAD_FORM_EDIT_URL, {
			method:'post',
			parameters:{id: map_id,num:num },
			onCreate: function(obj) {
				  Element.show('loading-mask');
				  },
			onSuccess: function(transport) {
				try {
        			if (transport.responseText.isJSON()) {
        				var response = transport.responseText.evalJSON();
    	                if(response.redirect) {window.location = (response.redirect);return;}
    	                if(response.success){
    	                	if($("edit-map-address-form")){
    	                		cancelFormEdit();
    	                	}
    	                	$("box-map-"+map_id).hide();
                            var html_form = response.html_form;
    	                	$j("#box-map-edit-"+map_id).append(html_form);
                            $("box-map-edit-"+map_id).up().addClassName("active-edit");
    	                	$("box-map-edit-"+map_id).show();
                            $$('#address_list input[type="radio"]:checked').each(function(c){
                                $(c).checked = false;
                            });

    	                	findMapAddress(response.position_lat,response.position_lng,num);
    	                	markers[num].setDraggable(true);
    	                	google.maps.event.addListener(markers[num], 'position_changed', function() {
    	              			if($('map_position_edit')) $('map_position_edit').value =getCurrentPosition(markers[num]);
    	              		});

                            edit_marker = markers[num];

    	                }else{
        	                alert(response.msg);
    	                }
        			}else{
    	                alert(transport.responseText);
        			}
              }catch(e){console.log(e);}
			},
		    onComplete:function(transport){
			    Element.hide('loading-mask');
		    }
	});
  }
  
  function findMapAddressCustomer(lat,lng,num){
	  var latlng = new google.maps.LatLng(lat,lng);

	  map.panTo(latlng);
	  for (var i = 0; i < infowindows.length; i++) {
		  infowindows[i].close();
	  }
	  var infowindow = new google.maps.InfoWindow({ 
		  		content: '<div style="width:270px;">This is test <strong>message</strong></div>'
		      });
	  //infowindow.close();
	  updateInfoWindow(messages[num],infowindow,$("logo_url_"+num).value);
	  infowindow.open(markers[num].get('map'), markers[num]);
	  infowindows.push(infowindow);
	  
	  
	  $$('#address_list ul li').each(function(c){
	      if($(c).readAttribute("rel") == num) $(c).addClassName("active") ;
	      else{
	    	  $(c).removeClassName("active") ;
	      }
	  });
	  
	  if(customer_marker){
		  directionsDisplay.setMap(map);
		  customer_marker.setDraggable(false);
		  var request = {
					origin:customer_marker.getPosition(),
					destination:markers[num].getPosition(),
					travelMode: google.maps.DirectionsTravelMode.DRIVING
				};
		  directionsService.route(request, function(response, status) {
				  if (status == google.maps.DirectionsStatus.OK) {
					 directionsDisplay.setDirections(response);
				  }
				  else{
					  infowindow.close();
                      updateInfoWindow(messages[num],infowindow,$("logo_url_"+num).value);
                      infowindow.open(markers[num].get('map'), markers[num]);
				  }
		  });
	  }

      $j(".map-colorbox-logo").colorbox({rel:'map-colorbox-logo'});
  }

  function findMapSearch(position){
      var marker = new google.maps.Marker({
          position: position,
          map: map,
          draggable:true,
          icon : image
      });
      map.panTo(map.getCenter());
  }

  function findYourAddress(position){
	 var image = JS_PATH+"ves_vendors/map/icon/icon_my_address.png";
	 if(!customer_marker){
		 var marker = new google.maps.Marker({
			    position: position,
			    map: map,
			    draggable:true,
			    icon : image
		 });
		 map.panTo(map.getCenter());

		 for (var i = 0; i < infowindows.length; i++) {
	      	  infowindows[i].close();
	     }
		 html = '<div style="width:300px;">';
		 html = '<h3>Your Address</h3>'
		 html +='</div>';
		 var infowindow = new google.maps.InfoWindow({ 
		  		content: html
		 });
		 infowindow.open(map,marker);
		 customer_marker = marker;
		 $$('#address_list input[type="radio"]:checked').each(function(c){
		      $(c).checked = false;
		 });
		 $$('#address_list ul li').each(function(c){
		    	  $(c).removeClassName("active") ;
		 });
		 var marker_find  = find_closest_marker(marker.getPosition());
		 if(marker_find){
			  setDefaultMarkerMap(getCurrentPosition(marker_find));
			  directionsDisplay.setMap(map);
			  customer_marker.setDraggable(false);
			  var request = {
						origin:customer_marker.getPosition(),
						destination:marker_find.getPosition(),
						travelMode: google.maps.DirectionsTravelMode.DRIVING
					};
			  directionsService.route(request, function(response, status) {
					  if (status == google.maps.DirectionsStatus.OK) {
						directionsDisplay.setDirections(response);
					  }
					  else{
						  refeshAddress();
                          marker.setMap(map);
                          $$('#address_list ul li').each(function(c){
                               $(c).removeClassName("active") ;
                          });
					  }
					  
			  });
		 }
	 }
  }
  
  function refeshAddress(){
	 if(customer_marker){
		 customer_marker.setMap(null);
		 customer_marker = null;
		 directionsDisplay.setMap(null);
	 }
  }
  
  function setDefaultMarkerMap(location){
	  $position = location.split("|");

	  for(var i = 0; i < markers.length; i++){
	    	var marker = markers[i];
	    	if(marker.getPosition().lat() == $position[0] && marker.getPosition().lng() == $position[1]){
	    		$$('#address_list input[type="radio"]').each(function(c){
				      if($(c).value == i) $(c).checked = true ;
	    		});
	    		$$('#address_list ul li').each(function(c){
				      if($(c).readAttribute("rel") == i) $(c).addClassName("active") ;
	    		});
	    		findMapAddress(marker.getPosition().lat() , marker.getPosition().lng(),i);
                break;
	    	}
	  }

      $j(".map-colorbox-logo").colorbox({rel:'map-colorbox-logo'});

  }
  
  function find_closest_marker( position ) {
	  var lat1 = position.lat();
	  var lon1 = position.lng();
	  var pi = Math.PI;
	  var R = 6371; //equatorial radius
	  var distances = [];
	  var closest = -1;
	  var closestMarker = null;
	  var i = 0;
	  markers.each(function(marker){
		  var lat2 = marker.position.lat();
		  var lon2 = marker.position.lng();

		  var chLat = lat2-lat1;
		  var chLon = lon2-lon1;


		  var dLat = chLat*(pi/180);
		  var dLon = chLon*(pi/180);

		  var rLat1 = lat1*(pi/180);
		  var rLat2 = lat2*(pi/180);

		  var a = Math.sin(dLat/2) * Math.sin(dLat/2) +
		  Math.sin(dLon/2) * Math.sin(dLon/2) * Math.cos(rLat1) * Math.cos(rLat2); 
		  var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a)); 
		  var d = R * c;

		  distances[i] = d;
		  if ( closest == -1 || d < distances[closest] ) {
			  closestMarker = marker;
			  closest	= i;
		  }
		  i ++;
	  });
	    return closestMarker;
	}

    function deleteLogo(image,flag){
        new Ajax.Request(DELETE_LOGO_URL, {
            method:'post',
            parameters:{image: image},
            onSuccess: function(transport) {
                var response = transport.responseText.evalJSON();
                if(response.errorcode == false){
                    $j(".qq-upload-button").show();
                    $('box-map-image-thumbnail').remove();
                    if(flag == false){
                      $j("#map-marker-logo").html('<img src="'+$j("#logo_url").val()+'" width="100" />');
                    }
                    document.getElementById('map_logo').value = null;
                }
                else{
                    alert(response.error);
                }
            },
        });
    }

    function deleteLogoAddress(id){
        new Ajax.Request(DELETE_LOGO_URL_ADDRESS, {
            method:'post',
            parameters:{id: id},
            onSuccess: function(transport) {
                var response = transport.responseText.evalJSON();
                if(response.errorcode == false){
                    $j("#file-edit-form").show();
                    $('box-map-image-thumbnail-address').remove();
                    $j("#map-marker-logo").html('<img src="'+$j("#logo_url").val()+'" width="100" />');
                    var disblel_num = $("marker_num").value ;
                    $j("#map-thumbnail-logo-"+disblel_num).html('<a class="map-colorbox-logo" href="'+$j("#logo_url").val()+'"><img src="'+$j("#logo_url").val()+'" width="50px" height="50px" /></a>');
                    $j("#logo_url_"+disblel_num).val($j("#logo_url").val());
                    $j("#map_logo_edit").val(null);
                 
                }
                else{
                    alert(response.error);
                }
            },
        });
    }


    function initClearImageTmp(){
        Ajax.Responders.unregister(varienLoaderHandler.handler);
        new Ajax.Request(CLEAR_IMAGE_TMP, {
            method:'post',
            onSuccess: function(transport) {
                var response = transport.responseText.evalJSON();
                if(response.errorcode == false){
                    Ajax.Responders.register(varienLoaderHandler.handler);
                }
                else{
                    alert(response.error);
                }
            },
        });
    }
    /*
    function findMapAddressByProductName(){
        if(customer_marker) refeshAddress();
    	var q = $("vendor-search-address-by-product").value;
    	if(q == null || q == "" ){
    	   alert(NOTE_VALIDATE_NAME);
  		   return;
  	    }
    	
    	for(var i = 0; i < markers.length; i++){
            var marker = markers[i];
            marker.setMap(null);
    	}
    	messages = new Array();
    	markers = new Array();
    	$("address_list").hide();
    	$("address_list").update(null);
        loadingBox.show();
    
        new Ajax.Request(SAEARCH_ADDRESS_BY_PRODUCT, {
            method:'post',
            parameters:{q:q},
            onSuccess: function(transport) {
                var response = transport.responseText.evalJSON();
                if(response.load){
                	$("address_list").update(response.list);
                	$("address_list").show();
                	response.messages.evalJSON().each(function(message){
                		messages.push(message.evalJSON());
                	});

                	
                	response.neighborhoods.evalJSON().each(function(marker){
                		 var positions = marker.split('|');
                		 var position = new google.maps.LatLng(positions[0],positions[1]);
                		 
                		 markers.push(new google.maps.Marker({
                             title:'This is title of store',
                             map:map,
                             draggable:false,
                             animation: google.maps.Animation.DROP,
                             position: position
                         }));
                	});

                    for(var i = 0; i < markers.length; i++){
                        var marker = markers[i];
                        attachSecretMessage(marker,i);
                    }
                    
                }
                loadingBox.close();
            },
        });
    }
    
    */
    function codeAddress() {
        var address = null;
	    var city = $('vendor-map-city').value;
        var country = $('vendor-map-country').value;
        var zip = $('vendor-map-zip').value;
        var region = $('vendor-map-region').value;
        var region_id = $('vendor-map-region-id').value;
        var name = $("vendor-map-name").value;
        var attribute = $("vendor-map-attribute").value;
        if(customer_marker) refeshAddress();
        if(name == null || name == "" ){
            alert(NOTE_VALIDATE_NAME);
            return;
        }

        if(!city){
            if(!region_id){
                if(region) address = region;
            }
            else{
                var option = $('vendor-map-region-id').select('option:selected').first();
                address  = option.readAttribute("title");
            }
        }
        else{
            address = city;
        }

    	for(var i = 0; i < markers.length; i++){
            var marker = markers[i];
            marker.setMap(null);
    	}
    	messages = new Array();
    	markers = new Array();
    	$("address_list").hide();
    	$("address_list").update(null);
 	    loadingBox.show();
    	new Ajax.Request(SAEARCH_ADDRESS_BY_ADDRESS, {
            method:'post',
            parameters:{name:name,city:city,country:country,zip:zip,region_id:region_id,region:region,attribute:attribute},
            onSuccess: function(transport) {
                var response = transport.responseText.evalJSON();
                if(response.load){
                	response.messages.evalJSON().each(function(message){
                		messages.push(message.evalJSON());
                	});
                	response.neighborhoods.evalJSON().each(function(marker){
                		 var positions = marker.split('|');
                		 var position = new google.maps.LatLng(positions[0],positions[1]);
                		 
                		 markers.push(new google.maps.Marker({
                             title:'This is title of store',
                             map:map,
                             draggable:false,
                             animation: google.maps.Animation.DROP,
                             position: position
                         }));
                	});
                    if(!address){
                        $("address_list").update(response.list);
                        $("address_list").show();
                        for(var i = 0; i < markers.length; i++){
                            var marker = markers[i];
                            attachSecretMessage(marker,i);
                        }
                    }
                    else{
                        var rds = $('vendor-map-radius').value;
                        if( rds == null  || rds == "") rds = 1000;
                        var radius = parseInt(rds, 10)*1000;
                        geocoder.geocode( { 'address': address}, function(results, status) {
                            if (status == google.maps.GeocoderStatus.OK) {
                                 map.setCenter(results[0].geometry.location);
                                 var searchCenter = results[0].geometry.location;

                                  if (circle) circle.setMap(null);
                                  circle = new google.maps.Circle({
                                        center:searchCenter,
                                        radius: radius,
                                        map: map,
                                        strokeWeight: 0,
                                        fillOpacity: 0
                                  });

                                  var foundMarkers = new Array();
                                  var bounds = new google.maps.LatLngBounds();
                                  $("address_list").hide();
                                  $("address_list").update(response.list);
                                   for (var i=0; i<markers.length;i++) {
                                        if (google.maps.geometry.spherical.computeDistanceBetween(markers[i].getPosition(),searchCenter) < radius) {
                                          bounds.extend(markers[i].getPosition())
                                          markers[i].setMap(map);
                                          foundMarkers.push(i);
                                        } else {
                                          markers[i].setMap(null);
                                        }
                                   }
                                   if (foundMarkers.length > 0) {
                                        $$("#address_list ul li").each(function(li){
                                            li.hide();
                                            var rel = li.readAttribute("rel");
                                            for (var i=0; i<foundMarkers.length;i++) {
                                               if( rel == foundMarkers[i]){
                                                   li.show();
                                                   li.addClassName("last") ;
                                               }
                                               else{
                                                   li.removeClassName("last") ;
                                               }
                                            }
                                        });
                                        //findYourAddress(searchCenter);
                                        $("address_list").show();
                                        map.fitBounds(bounds);
                                   } else {
                                        map.fitBounds(circle.getBounds());
                                   }

                        } else {
                          alert('Geocode was not successful for the following reason: ' + status);
                        }
                      });
                    }
                }
                loadingBox.close();
            },

        });

	}
    
    function resetFormSearch(){
        refeshAddress();
        $('vendor-map-city').value= "";
        $('vendor-map-country').value= "";
        $('vendor-map-zip').value = "";
        $('vendor-map-region').value= "";
        $('vendor-map-region-id').value= "";
        $("vendor-map-name").value= "";
    	$("vendor-map-radius").value = "";
        $("vendor-map-attribute").value = "";
    	for(var i = 0; i < markers.length; i++){
            var marker = markers[i];
            marker.setMap(null);
    	}
    	messages = new Array();
    	markers = new Array();
    	$("address_list").hide();
    	$("address_list").update(null);
    }
    
    
    
    var MapLoadingBox = Class.create();
    MapLoadingBox.prototype = {
        initialize: function(loadingId, overlayId){
            this.loading 	= $(loadingId);
            this.overlay 	= $(overlayId);
        },
        show: function(){
            this.loading.show();
            this.overlay.show();
        },
        isShow: function(){
            return this.loading.getStyle('display')=='block';
        },
        close: function(){
            this.loading.hide();
            this.overlay.hide();
        }
    }