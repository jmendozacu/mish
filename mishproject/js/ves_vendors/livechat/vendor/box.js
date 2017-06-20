/*VesBoxChat Class*/
var VesBoxChat = Class.create();
VesBoxChat.prototype = {
    /**
     /* Construct
     /* @param int clockId
     **/
    initialize: function(box,commandArray,processUrl,deleteBoxUrl,showBoxUrl,createTabUrl,updateStatusUrl,control){
        this.delete_box_url = deleteBoxUrl;
        this.update_show_box_url = showBoxUrl;
        this.create_tab_url = createTabUrl;
        this.update_status_box_url = updateStatusUrl;
        this.box_session = box;
        this.control_object = control;
        this.process_url = processUrl;
        this.check_request = true;
        this.check_style_box = true;
        this.current_box 		= '';
        this.after_box		= new Array();
        this.excuted_commands 	= commandArray;	/*store excuted command ids*/

        this.tick_sound = new Audio(AUDIO_SOUND);

        /*clock states*/
        this.setLoaderVarien();
        /*Question type*/
        this.setStyleBox();
        this.optionStypeTab();
        this.clearScrollAll();
        this.listenCommand();
        this.setStyleBox();
        this.optionStyleBox();

    },

    setLoaderVarien : function(){
        var varienLoaderHandler = new Object();
        var _this = this;
        var url = _this.process_url;
        if (!url.match(new RegExp('[?&]isAjax=true',''))) {
            url = url.match(new RegExp('\\?',"g")) ? url + '&isAjax=true' : url + '?isAjax=true';
        }
        varienLoaderHandler.handler = {
            onComplete: function(transport) {
                if(transport.url!=url){
                    Ajax.activeRequestCount = 0;
                }
                if(Ajax.activeRequestCount == 0 && transport.url!=url) {
                    toggleSelectsUnderBlock($('loading-mask'), true);
                    Element.hide('loading-mask');
                }
            }
        };
        Ajax.Responders.register(varienLoaderHandler.handler);
    },

    listenCommand: function(){
        var _this = this;
        var commands = this.excuted_commands.join();
        var box = this.box_session.join();
        new Ajax.Request(this.process_url, {
            method:'post',
            parameters: {type: _this.type,commands: commands,'box':box},
            loaderArea:false,
            onComplete: function(transport) {
            },
            onSuccess: function(transport) {
                if (transport.responseText.isJSON()) {
                    var response = transport.responseText.evalJSON();
                    /*Excute command*/
                    if(response.is_logged_out){
                        window.location.reload();
                    }
                    else {
                        if (response.has_command) {
                            response.commands.each(function (command) {
                                _this.excuteCommand(command)
                            });
                        }
                        if (response.update_time) {
                            ChatOption.updateTime(response.data);
                        }
                        if (response.message_box) {
                            _this._addMessageToSession(response.message_data);
                        }
                        if (response.box_data) {
                            _this._procesSesssionBox(response.box_data);
                        }
                        _this._checkSoundAudio();
                        setTimeout(function(){_this.listenCommand()},5000);
                    }
                }

            },
            onFailure: function() { _this.listenCommand(); }
        });
    },

    excuteCommand: function(command_from_ajax){
        var session = command_from_ajax.split('||');
        var excuted_commands = this.excuted_commands;
        var check_command = false;

        for(var i=0; i < excuted_commands.length ; i ++){
            var explode_command = excuted_commands[i].split("||");
            if(explode_command[0] == session[0]){
                var commad =  explode_command[1].split("|");
                var new_comand = commad.slice(-3);
                var string_new_comand = new_comand.join("|");
                excuted_commands[i] = explode_command[0]+"||"+string_new_comand+"|"+session[1];
                check_command = true;
            }
        }
        if(check_command == false){
            this.excuted_commands.push(session[0]+"||"+session[1]);
        }
        else{
            this.excuted_commands = excuted_commands;
        }
        var command = session[2].evalJSON();
        var _this = this;
        switch(command.command){
            case 'create_box':
                this._createVendorBox(command.data,session[0]);
                break;
            case 'decline_box':
                this._declineBox(command.data,session[0]);
                break;
            case 'accept_box':
                this._acceptBox(command.data,session[0]);
                break;
            case 'closed_box_frontend':
                this._deleteBoxFrontend(command.data,session[0]);
                break;

            case 'msg_type_frontend':
                this._addTypeMsg(command.data,session[0]);
                break;
            default :
                break;
        }
    },
    
    _applyStorage : function(){
        if (localStorage.length != 0) {
                document.getElementById('ves-livechat-support').innerHTML = localStorage.getItem('ves-livechat-support-vendors');
                this.optionStyleBox();
        }
    },
    
    _procesSesssionBox : function(data){
        var _this=this;
        data.evalJSON().each(function(tmp){
            if(tmp.message){
        	  if($$("#main-user-info-tab-"+tmp.session_id+" .button-option")){
                  $$("#main-user-info-tab-"+tmp.session_id+" .button-option").each(function(e){
                      $(e).remove();
                  });
              }
              if($("ves_livechat_message_button_"+tmp.session_id)){
                  $("ves_livechat_message_button_"+tmp.session_id).remove();
              }
              $$("#main-user-info-tab-"+tmp.session_id+" .button-chat .end").each(function(e){
                  $(e).hide();
              });

                if($("ves_livechat_message_note_"+tmp.session_id)){
                    $("ves_livechat_message_note_"+tmp.session_id).remove();
                }
                $$("#ves-tabs-chatlive-content .content-" +tmp.session_id+" .discussion").each(function(e){
                    e.insert({bottom:tmp.message});
                });
                $$("#ves-tabs-chatlive-" +tmp.session_id+" .discussion").each(function(e){
                    e.insert({bottom:tmp.message});
                });

                $$("#ves-tabs-chatlive-"+tmp.session_id+" .icon-closed").each(function(span){
                    span.setAttribute("onclick","ChatOption.closedSessionBox("+tmp.session_id+")");
                });

                if($("chat_message_"+tmp.session_id)){
                    if($("chat_message_"+tmp.session_id).hasClassName("disabled") == false){
                        $("chat_message_"+tmp.session_id).addClassName("disabled");
                    }
                }
                if($("chat_message_"+tmp.session_id)){
                    $("chat_message_"+tmp.session_id).setAttribute("disabled","disabled");
                }

                if($("ves_livechat_visitor_"+tmp.session_id)){
                    $("ves_livechat_visitor_"+tmp.session_id).remove();
                }

                _this.clearScrollSingel(tmp.session_id);

                var box_sessions = _this.box_session;
                var key = null;

                for(var i=0; i < box_sessions.length ; i ++){
                    var explode_box = box_sessions[i].split("||");
                    if(explode_box[0] == tmp.session_id){

                        key = box_sessions.indexOf(box_sessions[i]);
                    }
                }
                if(key != null) box_sessions.splice(key, 1);
                _this.box_session = box_sessions;
                var box_commands = _this.excuted_commands;
                var key1 = null;

                for(var i=0; i < box_commands.length ; i ++){
                    var explode_box = box_commands[i].split("||");
                    if(explode_box[0] == tmp.session_id){

                        key1 = box_commands.indexOf(box_commands[i]);
                    }
                }
                if(key1 != null) box_commands.splice(key1, 1);
                _this.excuted_commands = box_commands;

                if($("ves-livechat-support")) ChatOption.setStorage();
            }
        });
    },
    _addMessageToSession : function(data){
        var _this = this;
        var box_sessions = this.box_session;
        data.evalJSON().each(function(box){
            var check_command = false;
            var sesseion_id = box.session_id;
            for(var i=0; i < box_sessions.length ; i ++){
                var explode_box = box_sessions[i].split("||");
                if(explode_box[0] == sesseion_id){
                    box_sessions[i] = box_sessions[i]+"|"+box.message_ids;
                    check_command = true;
                }
            }
            if(check_command == false){
                box_sessions.push(sesseion_id+"||"+box.message_ids);
            }
            _this._addMessage(box.message_texts,sesseion_id);
        });
        this.box_session = box_sessions;
    },

    _playSoundAudio : function(){
        var audio = VES_Vendor_Livechat_Cookie.get("audio_vendor", true);
        var _this = this;
	   switch (audio){
	            case 1:
	                _this.tick_sound.play();
	                break;
	            case 0 :
	                _this.tick_sound.pause();
	                break;
	            default :
	                _this.tick_sound.play();
	                break;
	        }
        return;
    },

    _checkSoundAudio : function(){
        ChatOption.getStatusAudio();
    },

    _addTypeMsg:function(data,session_id){
        var _this = this;
        data.evalJSON().each(function(tmp){
            if($("tab-"+session_id) || $("ves-tabs-chatlive-"+session_id)){
                if(tmp.check == "true"){
                    if($("ves_message_typing_"+session_id)){
                        $("ves_message_typing_"+session_id).remove();
                    }
                    $$("#ves-tabs-chatlive-content .content-"+session_id+" .discussion").each(function(div){
                        div.insert({bottom:tmp.message});
                    });
                    $$("#ves-tabs-chatlive-"+session_id+" .discussion").each(function(div){
                        div.insert({bottom:tmp.message});
                    });
                }
                else{
                    if($("ves_message_typing_"+session_id)){
                        $("ves_message_typing_"+session_id).remove();
                    }
                }
            }
        });
        if($("ves-tabs-chatlive-"+session_id)) ChatOption.setStorage();
    },
    _addMessage: function(data,session_id){
        var _this = this;
        data.evalJSON().each(function(tmp){
            if($("tab-"+session_id) || $("ves-tabs-chatlive-"+session_id)){
                if(!$("ves-content-message-"+tmp.id) && !$("ves-content-message-"+tmp.increment_id)) {
                    if(tmp.type == 1){
                        var html = ChatOption.htmlMessageCustomer(session_id,tmp.content,tmp.id,tmp.name);
                        _this._playSoundAudio();
                        $$("#ves-tabs-chatlive-header .tab-selector-"+session_id).each(function(div){
                            if(div.hasClassName("checked") == false){
                                div.addClassName("blink");
                                if($("ves_livechat_visitor_"+session_id)){
                                    $("ves_livechat_visitor_"+session_id).addClassName("blink");
                                }
                            }
                        });
                        $$("#ves-tabs-chatlive-"+session_id+" .message").each(function(div){
                        	 if(div.up().up().visible() == true){
	                            if(div.up().visible() == true){
	                                div.scrollTop = div.scrollHeight;
	                            }
	                            else{
	                                div.up().previous().addClassName("blink");
	                            }
                        	 }
                        	 else{
                        		 var rel = div.up().up().readAttribute("rel");
                                 $$(".ves-livechat-box-hidden-list").each(function(e){
                                     if(e.hasClassName("ves_list_blink") == false)
                                     e.addClassName("ves_list_blink");
                                 });
                                 $$(".ves-livechat-box-hidden-list .list-hidden ul li").each(function(e){
                                     if(e.readAttribute("rel") ==  rel)
                                         e.addClassName("ves_list_blink");
                                 });
                        	 }
                        });

                        if($("ves_message_typing_"+session_id)){
                            $("ves_message_typing_"+session_id).remove();
                        }

                    }
                    else{
                        var html = ChatOption.htmlMessage(session_id,tmp.content,tmp.id,tmp.name);
                    }

                    if($("tab-"+session_id)){
                        if($$("#ves-tabs-chatlive-content .content-"+session_id+" .discussion li").last().hasClassName("typing")){
                            $$("#ves-tabs-chatlive-content .content-"+session_id+" .discussion li").last().insert({before:html});
                        }
                        else{
                            $$("#ves-tabs-chatlive-content .content-"+session_id+" .discussion").each(function(div){
                                div.insert({bottom:html})
                            });
                        }
                    }
                    if($("ves-tabs-chatlive-"+session_id)) {
                        if ($$("#ves-tabs-chatlive-" + session_id + " .discussion li").last().hasClassName("typing")) {
                            $$("#ves-tabs-chatlive-" + session_id + " .discussion li").last().insert({before: html});
                        }
                        else {
                            $$("#ves-tabs-chatlive-" + session_id + " .discussion").each(function (div) {
                                div.insert({bottom: html})
                            });
                        }
                    }

                    if($("ves-tabs-chatlive-"+session_id))  ChatOption.setStorage();
                    $$(".ves-tabs-chatlive .top-bar").each(function(header){
                        header.observe("click",function(event){
                            $$("#ves-tabs-chatlive-"+session_id+" .message").each(function(div){
                                div.scrollTop = div.scrollHeight;
                            });

                            if(header.hasClassName("blink")){
                                header.removeClassName("blink");
                            }
                        });
                    });
                    _this.clearScrollSingel(session_id);
                }
            }
            else{
                if(tmp.type == 1) {
                    if($("ves_livechat_visitor_"+session_id)){
                        $("ves_livechat_visitor_"+session_id).addClassName("blink");
                    }
                }
            }
        });


    },
    _deleteBoxFrontend:function(data,session_id){
        var _this = this;
        data.evalJSON().each(function(tmp){
            if(tmp.check_close){
                if($("ves_livechat_message_button_"+session_id)){
                    $("ves_livechat_message_button_"+session_id).remove();
                }
                if($("ves_livechat_message_note_"+session_id)){
                    $("ves_livechat_message_note_"+session_id).remove();
                }
                $$("#ves-tabs-chatlive-content .content-" +session_id+" .discussion").each(function(e){
                    e.insert({bottom:tmp.text_backend});
                });
                $$("#ves-tabs-chatlive-" +session_id+" .discussion").each(function(e){
                    e.insert({bottom:tmp.text_backend});
                });
                if($("chat_message_"+session_id)){
                    if($("chat_message_"+session_id).hasClassName("disabled") == false){
                        $("chat_message_"+session_id).addClassName("disabled");
                    }
                }
                if($("chat_message_"+session_id)){
                    $("chat_message_"+session_id).setAttribute("disabled","disabled");
                }
                if($$("#main-user-info-tab-"+session_id+" .button-option")){
                    $$("#main-user-info-tab-"+session_id+" .button-option").each(function(e){
                        $(e).remove();
                    });
                }
                $$("#main-user-info-tab-"+session_id+" .button-chat .end").each(function(e){
                    $(e).hide();
                });
                if($("ves_livechat_visitor_"+session_id)){
                    $("ves_livechat_visitor_"+session_id).remove();
                }
                $$("#ves-tabs-chatlive-"+session_id+" .icon-closed").each(function(span){
                    span.setAttribute("onclick","ChatOption.closedSessionBox("+session_id+")")
                });
                _this.clearScrollSingel(session_id);
            }
        });

    },
    _acceptBox:function(data,session_id){
        data.evalJSON().each(function(tmp){
            if($("ves_livechat_message_note_"+session_id)){
                $("ves_livechat_message_note_"+session_id).update(tmp.text_backend);
            }
            else{
                var note = '<li id="ves_livechat_message_note_'+session_id+'" class="note" />';
                note += tmp.text_backend;
                note += '</li>';
                $$("#ves-tabs-chatlive-"+session_id+" .discussion").each(function(div){
                    div.insert({bottom:note});
                });
            }
            $$("#main-user-info-tab-"+session_id+" .button-chat").each(function(e){
                $(e).insert({top:tmp.button});
            });
            $$("#ves-tabs-chatlive-content .content-"+session_id+" .box-content-reply").each(function(div){
                div.insert({bottom:tmp.reply})
            });

            $$("#main-user-info-tab-"+session_id+" .button-option").each(function(e){
                $(e).remove();
            });
            if($("ves_livechat_message_button_"+session_id)){
                $("ves_livechat_message_button_"+session_id).remove();
            }

        });
        $("chat_message_"+session_id).removeClassName("disabled");
        $("chat_message_"+session_id).removeAttribute("disabled");
    },
    _declineBox:function(data,session_id){
        data.evalJSON().each(function(tmp){
        	
        	 $$("#main-user-info-tab-"+session_id+" .button-option").each(function(e){
                 $(e).remove();
             });
             if($("ves_livechat_message_button_"+session_id)){
                 $("ves_livechat_message_button_"+session_id).remove();
             }
             
            if($("ves_livechat_message_note_" +session_id)){
                $("ves_livechat_message_note_" +session_id).update(tmp.text_backend);
            }
            else{
                var note = '<li id="ves_livechat_message_note_'+session_id+'" class="note" />';
                note += tmp.text_backend;
                note += '</li>';
                $$("#ves-tabs-chatlive-"+session_id+" .discussion").each(function(div){
                    div.insert({bottom:note});
                });

                $$("#ves-tabs-chatlive-"+session_id+" .icon-closed").each(function(span){
                    span.setAttribute("onclick","control.deleteBox("+session_id+")")
                });
            }
        });

    },
    _createVendorBox : function(data,session_id){
        var _this = this;
        data.evalJSON().each(function(tmp){
            var message_id = tmp.value;
            var sessions = session_id+"||"+message_id;
            _this.box_session.push(sessions);
        });
        //alert(message_id);
        new Ajax.Request(_this.create_tab_url, {
            method:'post',
            parameters: {session_id: session_id},
            loaderArea:false,
            onSuccess: function(transport) {
                if (transport.responseText.isJSON()) {
                    var response = transport.responseText.evalJSON();
                    if(response.success){
                        if( $("ves-tabs-chatlive-header") || $("ves-tabs-chatlive-content")){
                            $("ves-tabs-chatlive-header").insert({bottom:response.header});
                            $("ves-tabs-chatlive-content").insert({bottom:response.content});
                            $("ves-tabs-chatlive-info").insert({bottom:response.info});
                            
                            if($("ves_livechat_visitor_"+response.session_id)){
                                $("ves_livechat_visitor_"+response.session_id).addClassName("blink");
                            }
                            else{
                                $("ves_livechat_visitor").insert({bottom:response.visitor});
                            }
                            
                            radio.push(response.session_id);
                            _this._playSoundAudio();
                            _this.optionStypeTab();
                            _this.clearScrollSingel(response.session_id);
                            _this.clearScrollVisitor();
                        }
                        else{
                            if($("ves-livechat-support") && !$("ves-tabs-chatlive-"+response.session_id)){
                                $("ves-livechat-support").insert({bottom:response.box});
                                _this._playSoundAudio();
                                _this.setStyleBox();
                                $$("#ves-tabs-chatlive-"+response.session_id+" .message").each(function(div){
                                 	 if(div.up().up().visible() == true){
         	                            if(div.up().visible() == true){
         	                                div.scrollTop = div.scrollHeight;
         	                            }
         	                            else{
         	                                div.up().previous().addClassName("blink");
         	                            }
                                 	 }
                                 	 else{
                                 		 var rel = div.up().up().readAttribute("rel");
                                          $$(".ves-livechat-box-hidden-list").each(function(e){
                                              if(e.hasClassName("ves_list_blink") == false)
                                              e.addClassName("ves_list_blink");
                                          });
                                          $$(".ves-livechat-box-hidden-list .list-hidden ul li").each(function(e){
                                              if(e.readAttribute("rel") ==  rel)
                                                  e.addClassName("ves_list_blink");
                                          });
                                 	 }
                                 });
                                ChatOption.setStorage();
                                _this.optionStyleBox();
  
                            }
                        }
                        msgTypeObject[response.session_id] = new MessageTypePing(_this.control_object);
                    }else{
                    	if(response.note){
                   		 	alert(response.note);
                   		 	ChatOption.delBoxSession(session_id);
                   		 	ChatOption.removeBoxHtml(session_id);
                    	}
                    }
                }
            },

        });

    },
    optionStypeTab:function(){
        $$('.livechat-tabs .tab a').each(function(c){
            $(c).observe("click",function(e){
                var position = $(c).readAttribute("rel");
                for (var i=0;i<radio.length;i++){
                    if(position != radio[i]){
                        $$(".chat-content .content-"+radio[i]).invoke('removeClassName',"active");
                        $$(".livechat-tabs .tab .tab-selector-"+radio[i]).invoke('removeClassName',"checked");
                        if($("main-user-info-tab-"+radio[i])) $("main-user-info-tab-"+radio[i]).hide();
                    }
                }
                if($(c).hasClassName("blink")) $(c).removeClassName("blink");
                if($("ves_livechat_visitor_"+position)) {
                    if($("ves_livechat_visitor_"+position).hasClassName("blink")) {
                        $("ves_livechat_visitor_"+position).removeClassName("blink");
                    }
                }
                $$(".chat-content .content-"+position).invoke('addClassName',"active");
                $$(".livechat-tabs .tab .tab-selector-"+position).invoke('addClassName',"checked");
                if($("main-user-info-tab-"+position)) $("main-user-info-tab-"+position).show();
                ;
            });
        });
    },
    setStyleBox:function(){
    	 if(!$('ves-livechat-support')) return;
    	 $$(".ves-livechat-box-hidden-list .list-hidden ul li").each(function(e){
             e.remove();
         });
         var arr =  $('ves-livechat-support').childElements();
         var i = 0;
         var x = 20;
         var size = arr.length;
         var hide_px = 0;
         arr.each(function(node){
             if(1 < i && i <=3){
                  node.show();
                  x =  x + 290;
                  node.setStyle({"right": x+"px"});
             }
             else if(i == 1){
                 node.setStyle({"right": "20px"});
             }
             else if(i == 0){
                 if(size > 4){
                      node.setStyle({"right": 290*3+x+"px"});
                      node.show();
                 }
                 else{
                     node.hide();
                 }
             }
             else{
                 node.setStyle({"right": x+"px"});
                 node.hide();
                 var name = node.select("h1").first().innerHTML;
                 $$(".ves-livechat-box-hidden-list .list-hidden ul").each(function(e){
                     e.insert("<li rel='"+i+"'>"+name+"</li>");
                 });
             }
             i++;
         });
    },
    optionStyleBox:function(){
        var _this= this;
        $$(".ves-tabs-chatlive .top-bar").each(function(header){
            header.observe("click",function(event){
                var right = header.select(".right").first();
                var box_id = header.readAttribute("rel");
                var content = header.next();
                if(content.visible()){
                    right.hide();
                    content.hide();
                    _this.updateShowOn("hide",box_id);
                }
                else{
                    right.show();
                    content.show();
                    _this.updateShowOn("show",box_id);
                }
                
                if(header.hasClassName("blink")){
                    header.removeClassName("blink");
                }
                
                ChatOption.setStorage();
                _this.optionStyleSingelBox(box_id);
            });
        });

        $$('.ves-tabs-chatlive .icon').each(function(div){
            div.observe('click',function(e){
                Event.stop(e);
                e.stopPropagation();
            });
        });

        $$(".ves-tabs-chatlive .content .reply textarea").each(function(content){
            content.observe("keypress",function(event){
                var value = content.value;
                var key = event.which || event.keyCode;
                var id = content.readAttribute("rel");
                switch (key) {
                    default:
                        ChatOption.msgType(id,value,control);
                        break;
                    case Event.KEY_RETURN:
                        event.preventDefault();
                        control.replyMessage(id,true);
                        var object = msgTypeObject[id];
                        object.setIsTypeing(false);
                        object.setUpdatedTypeing(false);
                        break;
                  }
            });
        });
        
        _this._clickListHiddenBox();

    },
    _clickListHiddenBox : function(){
        $$('.ves-tabs-chatlive .icon').each(function(div){
            div.observe('click',function(e){
                Event.stop(e);
                e.stopPropagation();
            });
        });
        this.clearScrollAll();
        var _this= this;
        $$(".ves-livechat-box-hidden-list .list-hidden ul li").each(function(li){
            var rel = li.readAttribute("rel");
            li.observe('click',function(e){
                $$(".ves-livechat-box-hidden-list .list-hidden ul li").each(function(e){
                    e.remove();
                });
                var arr =  $('ves-livechat-support').childElements();
                var i = 0;
                arr.each(function(node){
                    if(i ==  rel) {
                        node.show();
                        $$(".ves-livechat-box-hidden-list .list-hidden ul li").each(function(e){
                            if(e.readAttribute("rel") == i) e.remove();
                        });
                    }
                    if(i >= 3 && i !=  rel) {
                        node.hide();
                        var name = node.select("h1").first().innerHTML;
                        $$(".ves-livechat-box-hidden-list .list-hidden ul").each(function(e){
                            e.insert("<li rel='"+i+"'>"+name+"</li>");
                        });
                    }
                    i++;
                });
                ChatOption.setStorage();
                _this._clickListHiddenBox();
            });
        });

    },
    
    optionStyleSingelBox : function(box_id){
        var _this = this;

        $$("#ves-tabs-chatlive-"+box_id+" .icon").each(function(div){
            div.observe('click',function(e){
                Event.stop(e);
                e.stopPropagation();
            });
        });

        this.clearScrollSingel(box_id);
    },
    clearScrollAll : function(){
        $$(".ves-tabs-chatlive .content .box-content-message").each(function(div){
            div.scrollTop = div.scrollHeight;
        });
        $$(".ves-tabs-chatlive .content .message").each(function(div){
            div.scrollTop = div.scrollHeight;
        });

    },
    clearScrollSingel : function(session_id){
        $$(".ves-tabs-chatlive .content-"+session_id+" .box-content-message").each(function(div){
            div.scrollTop = div.scrollHeight;
        });
        $$("#ves-tabs-chatlive-"+session_id+" .message").each(function(div){
            div.scrollTop = div.scrollHeight;
        });
    },
    clearScrollVisitor : function(){
        $$("#ves-tabs-chatlive-content .page-bottom").each(function(div){
            div.scrollTop = div.scrollHeight;
        });
    },
    updateShowOn :function(style,box_id){
        var _this = this;
        new Ajax.Request(_this.update_show_box_url, {
            method:'post',
            loaderArea:false,
            parameters: {style: style,box_id:box_id},
            onSuccess: function(transport) {
            },
        });
    },
    deleteBox : function(box_id){
        loadingBox.show();
        var _this = this;
        new Ajax.Request(_this.delete_box_url, {
            method:'post',
            parameters: {box_id: box_id},
            onSuccess: function(transport) {
                if (transport.responseText.isJSON()) {
                    var response = transport.responseText.evalJSON();
                    if(response.success){
                        loadingBox.close();
                        $("ves-tabs-chatlive-"+box_id).remove();
                        _this.setStyleBox();
                    }else{
                        alert(response.msg);
                    }
                }
            },
            onFailure: function() {
                alert('Please Refesh Page');
            }
        });
    }
};

/*ChatControl Class*/
var ChatControl = Class.create();
ChatControl.prototype = {
    initialize: function(url){
        this.url = url;
        this.check = false;
    },
    init: function(box,data){
        this.sendCommand('reset',session,data);
    },
    createBox: function(session){
        this.sendCommand('create_box',session,null);
    },

    hideBox:function(session){
        $("main-user-info-tab-"+session).hide();

        $$(".livechat-tabs .tab .tab-selector-"+session).each(function(e){
            $(e).up().remove();
        });

        $$(".ves-tabs-chatlive .content-"+session).each(function(e){
            $(e).remove();
        });

        $$(".chat-content .content-0").invoke('addClassName',"active");
        $$(".livechat-tabs .tab .tab-selector-0").invoke('addClassName',"checked");

        this.sendCommand('hidden_box',session,null);
    },
    endBox:function(session,storega){
    	ChatOption.removeBoxHtml(session);
        if(storega==true) ChatOption.delBoxSession(session);
        this.sendCommand('end_box',session,null);
    },
    deleteBox:function(session){
        $("ves-tabs-chatlive-"+session).remove();
        livechat.setStyleBox();
        livechat.optionStyleBox();
        ChatOption.delBoxSession(session);
        this.sendCommand('end_box',session,null);
        ChatOption.setStorage();
    },
    declineBox : function(session,storega){
        $$("#main-user-info-tab-"+session+" .button-option").each(function(e){
            $(e).remove();
        });
        if($("ves_livechat_message_button_"+session)){
            $("ves_livechat_message_button_"+session).remove();
        }
        this.sendCommand('decline_box',session,null);
        if(storega==true) ChatOption.setStorage();
    },
    acceptBox:function(session,storega){

        $$("#main-user-info-tab-"+session+" .button-option").each(function(e){
            $(e).remove();
        });
        if($("ves_livechat_message_button_"+session)){
            $("ves_livechat_message_button_"+session).remove();
        }
        this.sendCommand('accept_box',session,null);
        if(storega==true) ChatOption.setStorage();
    },
    replyMessage:function(session,storage){

        if($("chat_message_"+session).value != "" && $("chat_message_"+session).value != null){
            var message = $("chat_message_"+session).value;
            var name = $("chat_message_"+session).readAttribute("name");
            var increment_id = new Date().getTime();
            var html = ChatOption.htmlMessage(session,message,increment_id,name);
            if($("tab-"+session)){
                if($$("#ves-tabs-chatlive-content .content-"+session+" .discussion li").last().hasClassName("typing")){
                    $$("#ves-tabs-chatlive-content .content-"+session+" .discussion li").last().insert({before:html});
                }
                else{
                    $$("#ves-tabs-chatlive-content .content-"+session+" .discussion").each(function(div){
                        div.insert({bottom:html})
                    });
                }
                $$(".ves-tabs-chatlive .content-"+session+" .box-content-message").each(function(div){
                    div.scrollTop = div.scrollHeight;
                });
            }
            if($("ves-tabs-chatlive-"+session)) {
                if ($$("#ves-tabs-chatlive-" + session + " .discussion li").last().hasClassName("typing")) {
                    $$("#ves-tabs-chatlive-" + session + " .discussion li").last().insert({before: html});
                }
                else {
                    $$("#ves-tabs-chatlive-" + session + " .discussion").each(function (div) {
                        div.insert({bottom: html})
                    });
                }


                $$("#ves-tabs-chatlive-" + session + " .content .message").each(function (div) {
                    div.scrollTop = div.scrollHeight;
                });
            }
            $("chat_message_"+session).value = null;

            var msgType = msgTypeObject[session];
            msgType.setDefaultOption();

            var data = new Array();
            data.push(session);
            data.push(increment_id);
            
            
            this.sendCommand('send_message_vendor',data.join(),message,null);
            if(storage == true) ChatOption.setStorage();
        }
    },
    sendCommand: function(command,session,data){
        new Ajax.Request(this.url, {
            method:'post',
            parameters: {command: command,session:session, data: data},
            loaderArea :false,
            onSuccess: function(transport) {

                if (transport.responseText.isJSON()) {
                    var response = transport.responseText.evalJSON();
                    /*Excute command */
                    if(response.is_logged_out) window.location.reload();
                    if(response.success && response.data){
                        var data = response.data.evalJSON();
                        var box_sessions = livechat.box_session;
                        data.each(function(tmp){
                            for(var i=0; i < box_sessions.length ; i ++){
                                var explode_box = box_sessions[i].split("||");
                                if(explode_box[0] == session){
                                    box_sessions[i] = box_sessions[i]+"|"+tmp.message_id;
                                    check_command = true;
                                }
                            }
                            if(check_command == false){
                                box_sessions.push(session+"||"+tmp.message_id);
                            }
                        });
                        livechat.box_session = box_sessions;
                    }else{
                    	if(response.note){
                    		 alert(response.note);
                    		 ChatOption.delBoxSession(session);
                    		 ChatOption.removeBoxHtml(session);
                    	}
                    }

                }

            },
            onFailure: function() {
                alert('Please Refesh Page');
            }
        });
    }
}

/*LiveChatLoadingBox Class*/
var LiveChatLoadingBox = Class.create();
LiveChatLoadingBox.prototype = {
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


/*ChatOption Function*/
var ChatOption = function() {
    return {
        closedSessionBox : function(session){
            $("ves-tabs-chatlive-"+session).remove();
            ChatOption.delBoxSession(session);
            livechat.setStyleBox();
            livechat.optionStyleBox();
            ChatOption.setStorage();
        },
        msgType : function(session,text,control){
            var object = msgTypeObject[session];
            object.msgType(session,text);
        },
        delBoxSession : function(session){
            var box_sessions = livechat.box_session;
            var key = null;
            //  alert(sesseion_id);
            for(var i=0; i < box_sessions.length ; i ++){
                var explode_box = box_sessions[i].split("||");
                if(explode_box[0] == session){
                    key = box_sessions.indexOf(box_sessions[i]);
                }
            }
            if(key != null) box_sessions.splice(key, 1);
            
            livechat.box_session = box_sessions;
            var box_commands = livechat.excuted_commands;
            var key1 = null;
            for(var i=0; i < box_commands.length ; i ++){
                var explode_box = box_commands[i].split("||");
                if(explode_box[0] == session){
                    key1 = box_commands.indexOf(box_commands[i]);
                }
            }
            if(key1 != null) box_commands.splice(key1, 1);
            livechat.excuted_commands = box_commands;
        },
        removeBoxHtml : function(session){
        	if($("main-user-info-tab-"+session)) $("main-user-info-tab-"+session).hide();
            $$(".livechat-tabs .tab .tab-selector-"+session).each(function(e){
                $(e).up().remove();
            });

            $$(".ves-tabs-chatlive .content-"+session).each(function(e){
                $(e).remove();
            });

            if($("ves_livechat_visitor_"+session)){
                $("ves_livechat_visitor_"+session).remove();
            }
            $$(".chat-content .content-0").invoke('addClassName',"active");
            $$(".livechat-tabs .tab .tab-selector-0").invoke('addClassName',"checked");
            
            if($("ves-tabs-chatlive-"+session)){
            	$("ves-tabs-chatlive-"+session).remove();
	            livechat.setStyleBox();
	            livechat.optionStyleBox();
	            ChatOption.setStorage();
            }
        },
        changeStatus : function(status) {
            loadingBox.show();
            new Ajax.Request(CHANGE_STATUS_URL, {
                method:'post',
                parameters:{status: status},
                onComplete: function (transport) {
                    if (transport.responseText.isJSON()) {
                        var response = transport.responseText.evalJSON();
                        /*Excute command */
                        if(response.success){
                        }else{
                        }
                        loadingBox.close();
                    }
                }
            });
        },
        htmlMessage : function(session,message,message_id,message_title){
            var myDate=new Date();
            var date = dateFormat(myDate, "mmmm d, yyyy, h:MM TT");
            var html = '<li class="self" id="ves-content-message-'+message_id+'">';
            html += '<div class="avatar">';
            html += '<img src="'+AVATAR_URL+'" />';
            html += '</div>';
            html += '<div class="messages">';
            html += '<p>';
            html += '<span class="title">'+message_title+' : </span>';
            html += message;
            html += '</p>';
            html += '<time datetime="2014-04-23 02:33:47">'+date+'</time>'
            html += '</div>';
            html += '</li>';
            return html;
        },
        htmlMessageCustomer : function(session,message,message_id,message_title){
            var myDate=new Date();
            var date = dateFormat(myDate, "mmmm d, yyyy, h:MM TT");
            var html = '<li class="other" id="ves-content-message-'+message_id+'">';
            html += '<div class="avatar">';
            html += '<img src="'+AVATAR_URL+'" />';
            html += '</div>';
            html += '<div class="messages">';
            html += '<p>';
            html += '<span class="title">'+message_title+' : </span>';
            html += message;
            html += '</p>';
            html += '<time datetime="2014-04-23 02:33:47">'+date+'</time>'
            html += '</div>';
            html += '</li>';
            return html;
        },
        viewBox : function(session){
            if($("tab-"+session)){
                this.showTab(session);
            }
            else{
                this.openTabAjax(session);
            }
        },
        openTabAjax : function(session){
            loadingBox.show();
            var _this = this;
            new Ajax.Request(livechat.create_tab_url, {
                method:'post',
                parameters: {session_id: session},
                onSuccess: function(transport) {
                    if (transport.responseText.isJSON()) {
                        var response = transport.responseText.evalJSON();
                        if(response.success){
                            loadingBox.close();
                            $("ves-tabs-chatlive-header").insert({bottom:response.header});
                            $("ves-tabs-chatlive-content").insert({bottom:response.content});
                            $("ves-tabs-chatlive-info").insert({bottom:response.info});
                            radio.push(response.session_id);
                            livechat.optionStypeTab();
                            livechat.clearScrollSingel(response.session_id);
                            livechat.clearScrollVisitor();
                            _this.showTab(response.session_id);
                        }else{
                        	loadingBox.close();
                        	if(response.note){
                       		 	alert(response.note);
                       		 	ChatOption.delBoxSession(session);
                       		 	ChatOption.removeBoxHtml(session);
                        	}
                        }
                    }
                }
            });
        },
        showTab : function(session){
            $("main-user-info-tab-"+session).show();

            $$(".livechat-tabs .tab .tab-selector-"+session).each(function(e){
                $(e).up().show();
            });
            if($("ves_livechat_visitor_"+session).hasClassName("blink")){
                $("ves_livechat_visitor_"+session).removeClassName("blink");
            }
            if($("tab-"+session).hasClassName("blink")){
                $("tab-"+session).removeClassName("blink");
            }
            $$(".ves-tabs-chatlive .content-"+session).each(function(e){
                $(e).show();
            });
            $$(".chat-content .content-"+session).invoke('addClassName',"active");
            $$(".livechat-tabs .tab .tab-selector-"+session).invoke('addClassName',"checked");
            $$(".chat-content .content-0").invoke('removeClassName',"active");
            $$(".livechat-tabs .tab .tab-selector-0").invoke('removeClassName',"checked");
        },
        updateTime : function(data){
            data.evalJSON().each(function(tmp){
                if($("tab-"+tmp.session_id) || $("ves-tabs-chatlive-"+tmp.session_id)){
                    if( $("ves_livechat_message_button_"+tmp.session_id)) {
                        $("ves_livechat_message_button_"+tmp.session_id).remove();
                    }

                    if( $("ves_livechat_message_note_"+tmp.session_id)) {
                        $("ves_livechat_message_note_"+tmp.session_id).remove();
                    }

                    $$("#ves-tabs-chatlive-content .content-" +tmp.session_id+" .discussion").each(function(e){
                        e.insert({bottom:tmp.message});
                    });
                    $$("#ves-tabs-chatlive-" +tmp.session_id+" .discussion").each(function(e){
                        e.insert({bottom:tmp.message});
                    });

                    $$("#ves-tabs-chatlive-"+tmp.session_id+" .icon-closed").each(function(span){
                        span.setAttribute("onclick","ChatOption.closedSessionBox("+tmp.session_id+")")
                    });

                    if($("chat_message_"+tmp.session_id)){
                        if($("chat_message_"+tmp.session_id).hasClassName("disabled") == false){
                            $("chat_message_"+tmp.session_id).addClassName("disabled");
                        }
                    }
                    if($("chat_message_"+tmp.session_id)){
                        $("chat_message_"+tmp.session_id).setAttribute("disabled","disabled");
                    }

                    $$("#main-user-info-tab-"+tmp.session_id+" .button-option").each(function(e){
                        $(e).hide();
                    });


                    $$("#main-user-info-tab-"+tmp.session_id+" .button-chat .end").each(function(e){
                        $(e).hide();
                    });



                    if($("ves_livechat_visitor_"+tmp.session_id)){
                        $("ves_livechat_visitor_"+tmp.session_id).remove();
                    }

                    ChatOption.setStorage();

                    $$("#ves-tabs-chatlive-"+tmp.session_id+" .message").each(function(div){
                        div.scrollTop = div.scrollHeight;
                    });
                }
                else{
                    return;
                }
            });
        },
        getObjectMsgType : function(session_ids,control){
            var objectMsgType = {};
            session_ids.evalJSON().each(function(id){
                objectMsgType[id] = new MessageTypePing(control);
            });
            return objectMsgType;
        },


        getStatusAudio : function(){
            var status_class = "status-audio-on";
            var status_class_old = "status-audio-off";
            var audio = VES_Vendor_Livechat_Cookie.get("audio_vendor", true);
            if(audio == 1){
                status_class = "status-audio-on";
                status_class_old = "status-audio-off";
            }
            else if(audio == 0){
                status_class_old = "status-audio-on";
                status_class = "status-audio-off";
            }
            $$("#ves-tabs-chatlive-status .icon-audio").each(function(e){
                e.removeClassName(status_class_old);
                e.addClassName(status_class);
            });


            $$(".ves-tabs-chatlive .top-bar .right .icon-audio").each(function(e){
                e.removeClassName(status_class_old);
                e.addClassName(status_class);
            });

            return true;
        },
        changeAudioStatus : function(){
            var status_class = "status-audio-on";
            var status_class_old = "status-audio-off";
            var audio = VES_Vendor_Livechat_Cookie.get("audio_vendor", true);

            switch (audio){
                case 1:
                    VES_Vendor_Livechat_Cookie.set("audio_vendor", 0);
                    status_class = "status-audio-off";
                    status_class_old = "status-audio-on";
                    break;
                case 0 :
                    VES_Vendor_Livechat_Cookie.set("audio_vendor", 1);
                    status_class_old = "status-audio-off";
                    status_class = "status-audio-on";
                    break;
                default :
                    VES_Vendor_Livechat_Cookie.set("audio_vendor", 0);
                    status_class = "status-audio-off";
                    status_class_old = "status-audio-on";
                    break;
            }

            $$("#ves-tabs-chatlive-status .icon-audio").each(function(e){
                e.removeClassName(status_class_old);
                e.addClassName(status_class);
            });

            $$(".ves-tabs-chatlive .top-bar .right .icon-audio").each(function(e){
                e.removeClassName(status_class_old);
                e.addClassName(status_class);
            });

            return true;
        },
        showHiddenList : function(event){
            if(event.hasClassName("ves_list_blink") == true)
                event.removeClassName("ves_list_blink");
            $$(".ves-livechat-box-hidden-list .list-hidden ul").each(function(e){

                if(e.getStyle("display") == "block"){
                    e.hide();
                }
                else{
                    e.show();
                }
            });

            ChatOption.setStorage();
        },
        setStorage : function(){
            if (Modernizr.localstorage) {
                try {
                    if(localStorage.removeItem("ves-livechat-support-vendors")) localStorage.removeItem("ves-livechat-support-vendors");
                    localStorage.setItem('ves-livechat-support-vendors', JSON.stringify($('ves-livechat-support').innerHTML));
                } catch (e) {
                    if (e == QUOTA_EXCEEDED_ERR) {
                        alert('Quota exceeded!');
                    }
                }
            } else {
                alert('Cannot store user preferences as your browser do not support local storage');
            }

        },

        showConfirmBox : function(id){
            $("ves_livechat_box_confirm-"+id).show();
            $("ves_livechat_box_overlay-"+id).show();
        },
        hideConfirmBox : function(id){
            $("ves_livechat_box_confirm-"+id).hide();
            $("ves_livechat_box_overlay-"+id).hide();
        }

    }
}();

/*MessageTypePing Class*/
var MessageTypePing = Class.create();
MessageTypePing.prototype = {
    initialize: function(control){
        this.control = control;
        this.is_typeping = false;
        this.is_updated_typing_status = false;
        this.checkTypeing = null;
    },
    setIsTypeing :function(type){
        this.is_typeping = type;
    },
    setUpdatedTypeing : function(type){
        this.is_updated_typing_status = type;
    },
    setDefaultOption : function(){
        this.is_typeping = false;
        this.is_updated_typing_status = false;
        clearTimeout(this.checkTypeing);
        this.checkTypeing = null;
    },
    msgType : function(session,text){
        var _this = this;
        if(text != "" && text != null && this.is_typeping == false){
            this.setIsTypeing(true);
            if(this.is_updated_typing_status == false){
                this.setUpdatedTypeing(true);
                this.control.sendCommand('msg_type_vendor',session,true);
            }
            this.checkTypeing = setTimeout(function(){
                _this.setIsTypeing(false);
                _this.setUpdatedTypeing(false);
                _this.control.sendCommand('msg_type_vendor',session,false);
            }, 20000);
        }
        else{
            if(text == "" &&  this.is_typeping == true){
                this.setIsTypeing(false);
                this.setUpdatedTypeing(false);
                clearTimeout(this.checkTypeing);
                this.control.sendCommand('msg_type_vendor',session,false);
            }
        }
    },
}