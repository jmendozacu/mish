/*VesBoxChat Class*/
var VesBoxChat = Class.create();
VesBoxChat.prototype = {
    /**
     /* Construct
     /* @param int clockId
     **/
    initialize: function(vendor_id,box,commandArray,processUrl,deleteBoxUrl,showBoxUrl){
        this.delete_box_url = deleteBoxUrl;
        this.update_show_box_url = showBoxUrl;
        this.vendor_id = vendor_id;
        this.process_url = processUrl;
        this.box_session = box;
        this.tick_sound = new Audio(AUDIO_SOUND);
        this.current_box 		= '';
        this.after_box		= new Array();
        this.excuted_commands 	= commandArray;	/*store excuted command ids*/
        

        this.listenCommand();
        this.setStyleBox();
        this.optionStyleBox();
        
    },


    listenCommand: function(){
    	
        var _this = this;
        var commands = this.excuted_commands.join();
        var box = this.box_session.join();
      //  alert(box);
        new Ajax.Request(this.process_url, {
            method:'post',
            parameters: {type: _this.type,commands: commands,'box':box,'vendor':_this.vendor_id},
            onSuccess: function(transport) {
                if (transport.responseText.isJSON()) {
                	//alert("tst");
                    var response = transport.responseText.evalJSON();
                    /*Excute command*/
                    if(response.has_command){
                        response.commands.each(function(command){
                            _this.excuteCommand(command);
                        });
                    }
                    if(response.update_time){
                        ChatOption.updateTime(response.data);
                    }
                    if(response.message_box){
                        _this._addMessageToSession(response.message_data);
                    }

                    if(response.box_data){
                        _this._procesSesssionBox(response.box_data);
                    }

                    if(response.status_vendor){
                        var data = response.status_vendor.evalJSON();
                        _this._changeStatusClass(data.session_id, data.vendor_id, data.class,data.title);
                    }
                }else{
                    //alert(transport.responseText);
                }
                /*Listen command again*/

                _this._checkSoundAudio();

                setTimeout(function(){_this.listenCommand()},5000);

            },
            onFailure: function() { _this.listenCommand();}
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
                this._createVendorBox();
                break;
            case 'decline_box':
                this._declineBox(command.data,session[0]);
                break;
            case 'accept_box':
                this._acceptBox(command.data,session[0]);
                break;
            case 'end_box':
                this._endBox(command.data,session[0]);
                break;
            case "msg_type_vendor":
                this._addTypeMsg(command.data,session[0]);
                break;
            default :
                break;
        }
    },

    _applyStorage : function(){
        if (localStorage.length != 0) {
                $('ves-livechat-support').value  = localStorage.getItem('ves-livechat-support');
                this.optionStyleBox();
        }
    },

    _checkSoundAudio : function(){
        ChatOption.getStatusAudio();
    },

    _procesSesssionBox : function(data){
        var _this=this;
        data.evalJSON().each(function(tmp){
            _this._changeStatusClass(tmp.session_id,tmp.vendor_id,tmp.class,tmp.title);
            if(tmp.message && $("ves-tabs-chatlive-"+tmp.session_id)){
                if($("ves_livechat_message_note_"+tmp.session_id)){
                    $("ves_livechat_message_note_"+tmp.session_id).remove();
                }

                $$("#ves-tabs-chatlive-"+tmp.session_id+" .discussion").each(function(div){
                    div.insert({bottom:tmp.message})
                });

                $("ves_livechat_message_reply_"+tmp.session_id).setAttribute("disabled","disabled");

                $$("#ves-tabs-chatlive-"+tmp.session_id+" .message").each(function(div){
                    div.scrollTop = div.scrollHeight;
                });

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
                ChatOption.setStorage();
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
    _addTypeMsg:function(data,session_id){
        var _this = this;
        data.evalJSON().each(function(tmp){
            if($("ves-tabs-chatlive-"+session_id)){
                if(tmp.check == "true"){
                    if($("ves_message_typing_"+session_id)){
                        $("ves_message_typing_"+session_id).remove();
                    }
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
        ChatOption.setStorage();
    },
    _changeStatusClass : function(session,vendor_id,class_status,title){
        ChatOption.setClassStatus(session,vendor_id,class_status,title);
    },
    _addMessage: function(data,session_id){
        var _this= this;

        if($("ves_message_typing_"+session_id)){
            $("ves_message_typing_"+session_id).remove();
        }
        data.evalJSON().each(function(tmp){
            if(tmp.type == 2){
                var html = ChatOption.htmlMessageVendor(session_id,tmp.content,tmp.id,tmp.name);
                _this._playSoundAudio();
            }
            else{
                var html = ChatOption.htmlMessage(session_id,tmp.content,tmp.id,tmp.name);
            }
            $$("#ves-tabs-chatlive-"+session_id+" .discussion").each(function(div){
                if(!$("ves-content-message-"+tmp.id) && !$("ves-content-message-"+tmp.increment_id)) {
                     div.insert({bottom:html});
                }
            });
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

        ChatOption.setStorage();

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

    },

    _playSoundAudio : function(){
        var audio = VES_Vendor_Livechat_Cookie.get("audio_customer", true);
        var _this = this;
       // if (Modernizr.audio) {
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
       // }
        return;
    },

    _endBox:function(data,session_id){
        data.evalJSON().each(function(tmp){
            if(tmp.check_close){
                 if($("ves_livechat_message_note_"+session_id)){
                     $("ves_livechat_message_note_"+session_id).remove();
                 }
                 var li = '<li id="ves_livechat_message_note_'+session_id+'" class="note">';
                 li += tmp.text_frontend;
                 li += '</li>';
                 $$("#ves-tabs-chatlive-"+session_id+" .discussion").each(function(div){
                    div.insert({bottom:li})
                 });

                $("ves_livechat_message_reply_"+session_id).setAttribute("disabled","disabled");
                $$("#ves-tabs-chatlive-"+session_id+" .message").each(function(div){
                    div.scrollTop = div.scrollHeight;
                });

                $$("#ves-tabs-chatlive-"+session_id+" .icon-closed").each(function(span){
                    span.setAttribute("onclick","ChatOption.closedSessionBox("+session_id+")")
                });
            }
            else{
                return;
            }
        });

    },
    _acceptBox:function(data,session_id){
        data.evalJSON().each(function(tmp){
            $("ves_livechat_message_note_"+session_id).update(tmp.text_frontend);
        });
        $("ves_livechat_message_reply_"+session_id).removeAttribute("disabled");
        $("ves_livechat_message_reply_"+session_id).removeClassName("disabled");

        ChatOption.setStorage();
    },

    _declineBox:function(data,session_id){
        data.evalJSON().each(function(tmp){
            $("ves_livechat_message_note_"+session_id).update(tmp.text_frontend);
        });
        $$("#ves-tabs-chatlive-"+session_id+" .icon-closed").each(function(span){
            span.setAttribute("onclick","control.deleteBox("+session_id+")")
        });
        ChatOption.setStorage();
    },

    setStyleBox:function(){
    	//alert("ts");
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
                if(box_id) {
                    if (content.visible()) {
                        right.hide();
                        content.hide();
                        _this.updateShowOn("hide", box_id);
                    }
                    else {
                        right.show();
                        content.show();
                        _this.updateShowOn("show", box_id);
                    }

                    if (header.hasClassName("blink")) {
                        header.removeClassName("blink");
                    }
                }
                else{
                    ChatOption.closeBox();
                }
                ChatOption.setStorage();
                _this.optionStyleSingelBox(box_id);
            });
        });

        $$(".ves-tabs-chatlive .content .reply textarea").each(function(content){
            content.observe("keypress",function(event){
                var value = content.value;
                // alert(value);
                var key = event.which || event.keyCode;
                var id = content.readAttribute("rel");
                switch (key) {
                    default:
                        ChatOption.msgType(id,value,control);
                        break;
                    case Event.KEY_RETURN:
                        event.preventDefault();
                        control.replyMessage(id);
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
        $$(".ves-tabs-chatlive .content .message").each(function(div){
            div.scrollTop = div.scrollHeight;
        });
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
        var _this= this;
        $$("#ves-tabs-chatlive-"+box_id+" .icon").each(function(div){
            div.observe('click',function(e){
                Event.stop(e);
                e.stopPropagation();
            });
        });
        $$("#ves-tabs-chatlive-"+box_id+" .content .message").each(function(div){
            div.scrollTop = div.scrollHeight;
        });
    },
    updateShowOn :function(style,box_id){
        var _this = this;
        new Ajax.Request(_this.update_show_box_url, {
            method:'post',
            parameters: {style: style,box_id:box_id},
            onSuccess: function(transport) {
                if (transport.responseText.isJSON()) {
                }
            },
            onFailure: function() {
                alert('Please Refesh Page');
            }
        });
    },
};

/*ChatControl Class*/
var ChatControl = Class.create();
ChatControl.prototype = {
    initialize: function(url){
        this.url = url;
    },
    init: function(session,data){
        this.sendCommand('reset',session,data);
    },
    createBox: function(session,messsage){
        this.sendCommand('create_box',session,messsage);
    },
    deleteBox:function(session){
        if($("ves-tabs-chatlive-"+session)){
            var vendor_id = $("ves-tabs-chatlive-"+session).readAttribute("vendor");
            $("ves-tabs-chatlive-"+session).remove();
            ChatOption.delBoxSession(session);
            livechat.setStyleBox();
            livechat.optionStyleBox();
            ChatOption.getNumberElementBox();
            this.sendCommand('closed_box_frontend',session,"");
            ChatOption.setStorage();
            if($("ves-tabs-chatlive-start-"+vendor_id)){
                ChatOption.closeBox();
                $("ves-tabs-chatlive-start-"+vendor_id).show();
            }
            ChatOption.getNumberElementBox();
            ChatOption.setStorageClickBox(vendor_id,"false");
        }
    },
    replyMessage:function(session){
        if($("ves_livechat_message_reply_"+session).value != "" && $("ves_livechat_message_reply_"+session).value != null){
            var message = $("ves_livechat_message_reply_"+session).value;
            var name = $("ves_livechat_message_reply_"+session).readAttribute("name");
            var increment_id = new Date().getTime();
            var html = ChatOption.htmlMessage(session,message,increment_id,name);

            if($$("#ves-tabs-chatlive-"+session+" .discussion li").last().hasClassName("typing")){
                $$("#ves-tabs-chatlive-"+session+" .discussion li").last().insert({before:html});
            }
            else{
                $$("#ves-tabs-chatlive-"+session+" .discussion").each(function(div){
                    div.insert({bottom:html})
                });
            }

            $$("#ves-tabs-chatlive-"+session+" .content .message").each(function(div){
                div.scrollTop = div.scrollHeight;
            });
            $("ves_livechat_message_reply_"+session).value = "";

            var msgType = msgTypeObject[session];
            msgType.setDefaultOption();

            var data = new Array();
            data.push(session);
            data.push(increment_id);
            this.sendCommand('send_message_customer',data.join(),message);
            ChatOption.setStorage();
        }

    },
    sendCommand: function(command,session,data){
        new Ajax.Request(this.url, {
            method:'post',
            parameters: {command: command,session:session, data: data},
            onSuccess: function(transport) {
                if (transport.responseText.isJSON()) {
                    var response = transport.responseText.evalJSON();
                    /*Excute command */
                    if(response.success){
                    }else{
                       // alert(response.msg);
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
            if($("ves-tabs-chatlive-"+session)){
                var vendor_id = $("ves-tabs-chatlive-"+session).readAttribute("vendor");
                $("ves-tabs-chatlive-"+session).remove();
                ChatOption.delBoxSession(session);
                livechat.setStyleBox();
                livechat.optionStyleBox();
                ChatOption.getNumberElementBox();
                ChatOption.setStorage();
                if($("ves-tabs-chatlive-start-"+vendor_id)){
                    ChatOption.closeBox();
                    $("ves-tabs-chatlive-start-"+vendor_id).show();
                }
                ChatOption.getNumberElementBox();
                ChatOption.setStorageClickBox(vendor_id,"false");
            }
        },
        showEditInfoUser : function(){
            $$("#ves-start-chat-live .user-name").each(function(li){
                li.show();
            });
            $$("#ves-start-chat-live .user-email").each(function(li){
                li.show();
            });
            $$("#ves-start-chat-live .user-info").each(function(li){
                li.hide();
            });
            var name = ChatOption.getCookie("name");
            var email = ChatOption.getCookie("email");
            if(!$("ves_vendors_livechat_customer_id").value && name != null && email !=null){
                $("ves-livechat-chat-name").value =  name;
                $("ves-livechat-chat-email").value = email;
            }
        },
        hideEditInfoUser : function(){
            var name = ChatOption.getCookie("name");
            var email = ChatOption.getCookie("email");
            if(!$("ves_vendors_livechat_customer_id").value && name != null && email !=null){
                $$("#ves-start-chat-live .user-name").each(function(li){
                    li.hide();
                });
                $$("#ves-start-chat-live .user-email").each(function(li){
                    li.hide();
                });
                $$("#ves-start-chat-live .user-info").each(function(li){
                    li.show();
                });
                $("ves-livechat-chat-name").value =  name;
                $("ves-livechat-chat-email").value = email;
                $("ves-livechat-chat-name-text").update(name);
                $("ves-livechat-chat-email-text").update(email);
            }
        },
        showEditInfoContact : function(){
            $$("#ves-contact-chat-live .user-name").each(function(li){
                li.show();
            });
            $$("#ves-contact-chat-live .user-email").each(function(li){
                li.show();
            });
            $$("#ves-contact-chat-live .user-info").each(function(li){
                li.hide();
            });
            var name = ChatOption.getCookie("name");
            var email = ChatOption.getCookie("email");
            if(!$("ves_vendors_livechat_customer_id").value && name != null && email !=null){
                $("ves-livechat-contact-name").value =  name;
                $("ves-livechat-contact-email").value = email;
            }
            this.showSendAnother();
        },
        hideEditInfoContact : function(check){
            var name = ChatOption.getCookie("name");
            var email = ChatOption.getCookie("email");
            var message = ChatOption.getCookie("message");
            if(!$("ves_vendors_livechat_customer_id").value && name != null && email !=null){
                $("ves-livechat-contact-name").value =  name;
                $("ves-livechat-contact-email").value = email;
                $("ves-livechat-contact-name-text").update(name);
                $("ves-livechat-contact-email-text").update(email);
                $$("#ves-contact-chat-live .user-name").each(function(li){
                    li.hide();
                });
                $$("#ves-contact-chat-live .user-email").each(function(li){
                    li.hide();
                });
                $$("#ves-contact-chat-live .user-info").each(function(li){
                    li.show();
                });
            }
            if(message != null && check == true){
                $$("#ves-contact-chat-live .user-info .content-info .content-info-bottom").each(function(li){
                    li.show();
                    li.update(message);
                });
                $$("#ves-contact-chat-live .user-message").each(function(li){
                    li.hide();
                });
                $$("#ves-contact-chat-live .button-set .ves-livechat-submit-chat").each(function(li){
                    li.hide();
                });
                $$("#ves-contact-chat-live .button-set .ves-livechat-submit-another").each(function(li){
                    li.show();
                });
            }
        },
        showSendAnother : function(){
            $$("#ves-contact-chat-live .user-info .content-info .content-info-bottom").each(function(li){
                li.hide();
            });
            $$("#ves-contact-chat-live .user-message").each(function(li){
                li.show();
            });
            $$("#ves-contact-chat-live .button-set .ves-livechat-submit-chat").each(function(li){
                li.show();
            });
            $$("#ves-contact-chat-live .button-set .ves-livechat-submit-another").each(function(li){
                li.hide();
            });
        },
        setCookie : function(name,email,message){
            var name_old = VES_Vendor_Livechat_Cookie.get("name", true);
            var email_old = VES_Vendor_Livechat_Cookie.get("email", true);
            var message_old = VES_Vendor_Livechat_Cookie.get("message", true);
            if(name != name_old){
                VES_Vendor_Livechat_Cookie.set("name",name);
            }
            if(email != email_old){
                VES_Vendor_Livechat_Cookie.set("email",email);
            }
            if(message != message_old && message != null){

                VES_Vendor_Livechat_Cookie.set("message",message);
            }
        },
        getCookie : function(key){
            return VES_Vendor_Livechat_Cookie.get(key,true);
        },
        msgType : function(session,text,control){
            var object = msgTypeObject[session];
            object.msgType(session,text);
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
        delBoxSession : function(session){
            var box_sessions = livechat.box_session;
            var key = null;
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
        htmlMessageVendor : function(session,message,message_id,message_title){
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
        getNumberElementBox : function(){
            var arr =  $('ves-livechat-support').childElements();
            var i = 0;
            var x = 20;
            arr.each(function(node){
                if(1 <= i && i <=3){
                    x =  x + 290;
                }
                i++;
            });
            if(arr.length > 4) x =  x + 70;
            $$(".ves-livechat-box-create").each(function(node){
                node.setStyle({"right": x+"px"});
            });

        },
        
        createBoxOnline : function(id,title){
			
            this.getNumberElementBox();
            var check = true;
            $$(".ves-tabs-chatlive").each(function(e){
                if(e.readAttribute("vendor") == id && typeof id != 'undefined') check = false;
            });

            if(!check) return;

			if(typeof title != 'undefined'){
				 $$("#ves_vendor_ticket_title h1").each(function(el){
					el.update(title);
				});
			}
        
            var _this = this;

            $$(".ves-livechat-box-create").each(function(node){
                node.setStyle({"width": "280px"});
                node.select(".ves-livechat-box-button").first().hide();
                node.select(".ves-livechat-box-content").first().show();
                $("ves-livechat-box-create-form-online").show();
                $("ves-livechat-box-create-form-offline").hide();
             
                node.show();
                if(typeof id != 'undefined' &&  $("ves_vendors_livechat_vendor_id_online")) $("ves_vendors_livechat_vendor_id_online").value = id;
                 if($("ves_vendors_livechat_vendor_id_offline")) $("ves_vendors_livechat_vendor_id_offline").value = "";
                _this.hideEditInfoUser();
            });
        },
        
        createBoxOffline : function(id,title){
            this.getNumberElementBox();
            
        	if(typeof title != 'undefined'){
				 $$("#ves_vendor_ticket_title h1").each(function(el){
					el.update(title);
				});
			}
           
         
            var _this = this;
            $$(".ves-livechat-box-create").each(function(node){
            	
                node.setStyle({"width": "280px"});
                node.select(".ves-livechat-box-button").first().hide();
                node.select(".ves-livechat-box-content").first().show();
                $("ves-livechat-box-create-form-online").hide();
                $("ves-livechat-box-create-form-offline").show();
                node.show();
                if($("ves_vendors_livechat_vendor_id_online")) $("ves_vendors_livechat_vendor_id_online").value = "";
                if(typeof id != 'undefined' && $("ves_vendors_livechat_vendor_id_offline")) $("ves_vendors_livechat_vendor_id_offline").value = id;
                _this.hideEditInfoContact(false);
            });
        },
        
        closeBox : function(){
            $$(".ves-livechat-box-create").each(function(node){
                node.setStyle({"width": "auto"});
                node.select(".ves-livechat-box-button").first().show();
                node.select(".ves-livechat-box-content").first().hide();
                $("ves-livechat-box-create-form-online").hide();
                $("ves-livechat-box-create-form-offline").hide();
            });
        },
        
     
        
        updateTime:function(data){
            data.evalJSON().each(function(tmp){
                if($("ves-tabs-chatlive-"+tmp.session_id)){

                    if( $("ves_livechat_message_note_"+tmp.session_id)) {
                        $("ves_livechat_message_note_"+tmp.session_id).remove();
                    }
                    $$("#ves-tabs-chatlive-"+tmp.session_id+" .discussion").each(function(e){
                        e.insert({bottom:tmp.message});
                    });
                    $("ves_livechat_message_reply_"+tmp.session_id).setAttribute("disabled","disabled");


                    $$("#ves-tabs-chatlive-"+tmp.session_id+" .icon-closed").each(function(span){
                        span.setAttribute("onclick","ChatOption.closedSessionBox("+tmp.session_id+")")
                    });
                    ChatOption.setStorage();
                    $$("#ves-tabs-chatlive-"+tmp.session_id+" .content .message").each(function(div){
                        div.scrollTop = div.scrollHeight;
                    });

                }
                else{
                    return;
                }
            });
        },
        
        setClassStatus:function(session_id,vendor_id,class_status,title){
            if($("ves_vendor_livechat_status_"+session_id) && session_id != null){
                $("ves_vendor_livechat_status_"+session_id).removeClassName("ves-livechat-online");
                $("ves_vendor_livechat_status_"+session_id).removeClassName("ves-livechat-invisible");
                $("ves_vendor_livechat_status_"+session_id).removeClassName("ves-livechat-busy");
                $("ves_vendor_livechat_status_"+session_id).removeClassName("ves-livechat-offline");
                $("ves_vendor_livechat_status_"+session_id).addClassName(class_status);
            }
            if($("ves-tabs-chatlive-start-"+vendor_id)){
                if(!$("ves_vendor_livechat_status_"+session_id)) this.setFunctionCreateBox(vendor_id,class_status,title);
                else{
                    $("ves-tabs-chatlive-start-"+vendor_id).hide();
                }
            }
        },
        
        setFunctionCreateBox : function(vendor_id,class_status,title){
            var function_box = null;
            var class_name =  null;
            switch (class_status) {
                case 'ves-livechat-busy' :
                    function_box = 'ChatOption.createBoxOnline()';
                    class_name = "icon-online";
                    break;
                case 'ves-livechat-online' :
                    function_box = 'ChatOption.createBoxOnline()';
                    class_name = "icon-online";
                    break;
                default :
                    class_name = "icon-offline";
                    function_box = 'ChatOption.createBoxOffline()';
                    break;
            }
            $$("#ves-tabs-chatlive-start-"+vendor_id+" .ves-livechat-box-button").each(function(node){
                    node.setAttribute("onclick",function_box);
                    node.select("h1").first().update(title);
                    node.select("span").first().removeClassName("icon-online");
                    node.select("span").first().removeClassName("icon-offline");
                    node.select("span").first().addClassName(class_name);
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
            var audio = VES_Vendor_Livechat_Cookie.get("audio_customer", true);
            if(audio == 1){
                    status_class = "status-audio-on";
                    status_class_old = "status-audio-off";
            }
            else if(audio == 0){
                    status_class = "status-audio-off";
                    status_class_old = "status-audio-on";
            }

            $$(".ves-tabs-chatlive .top-bar .right .icon-audio").each(function(e){
                e.removeClassName(status_class_old);
                e.addClassName(status_class);
            });

            return true;
        },
        changeAudioStatus : function(){
            var status_class = "status-audio-on";
            var status_class_old = "status-audio-off";
            var audio = VES_Vendor_Livechat_Cookie.get("audio_customer", true);
            switch (audio){
                case 1:
                    VES_Vendor_Livechat_Cookie.set("audio_customer", 0);
                    status_class = "status-audio-off";
                    status_class_old = "status-audio-on";
                    break;
                case 0 :
                    VES_Vendor_Livechat_Cookie.set("audio_customer", 1);
                    status_class_old = "status-audio-off";
                    status_class = "status-audio-on";
                    break;
                default :
                    VES_Vendor_Livechat_Cookie.set("audio_customer", 0);
                    status_class = "status-audio-off";
                    status_class_old = "status-audio-on";
                    break;
            }

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
        setStorage : function(sesssion_id){
            if (Modernizr.localstorage) {
                try {
                	if(localStorage.removeItem("ves-livechat-support")) localStorage.removeItem("ves-livechat-support");
                	localStorage.setItem("ves-livechat-support", JSON.stringify($('ves-livechat-support').innerHTML));
                    if(typeof sesssion_id != 'undefined'){
                        if(localStorage.removeItem("ves-livechat-support-create")) localStorage.removeItem("ves-livechat-support-create");
                        localStorage.setItem("ves-livechat-support-create", sesssion_id);
                    }
                } catch (e) {
                    if (e == QUOTA_EXCEEDED_ERR) {
                        alert('Quota exceeded!');
                    }
                }
            } else {
                alert('Cannot store user preferences as your browser do not support local storage');
            }
        },
        setStorageClickBox : function(id,value){
            if (Modernizr.localstorage) {
                try {
                    if(localStorage.removeItem("ves-livechat-support-click-box-"+id)) localStorage.removeItem("ves-livechat-support-click-box-"+id);
                    localStorage.setItem("ves-livechat-support-click-box-"+id, value);
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
                this.control.sendCommand('msg_type_frontend',session,true);
            }
            this.checkTypeing = setTimeout(function(){
                _this.setIsTypeing(false);
                _this.setUpdatedTypeing(false);
                _this.control.sendCommand('msg_type_frontend',session,false);
            }, 20000);
        }
        else{
            if(text == "" &&  this.is_typeping == true){
                this.setIsTypeing(false);
                this.setUpdatedTypeing(false);
                clearTimeout(this.checkTypeing);
                this.control.sendCommand('msg_type_frontend',session,false);
            }
        }
    },
}