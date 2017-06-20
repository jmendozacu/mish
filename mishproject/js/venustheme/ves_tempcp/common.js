/**
 * common.js v1.0.0
 * http://www.vesnustheme.com
 *
 * Licensed under the MIT license.
 * http://www.opensource.org/licenses/mit-license.php
 * 
 * Copyright 2013, Venustheme
 * http://www.vesnustheme.com
 */

(function() {
    'use strict';
    $.localCache = {
        /* @static */
        timeout: 30000,
        data: {},
        /* @prototype */
        init: function( timeout ) {
            // saves name on the monster instance
            this.timeout = timeout;
        },
        remove: function (url) {
            delete this.data[url];
        },
        exist: function (url) {
            return !!this.data[url] && ((new Date().getTime() - this.data[url]._) < this.timeout);
        },
        get: function (url) {
            return this.data[url].data;
        },
        set: function (url, cachedData, callback) {
            this.remove(url);
            this.data[url] = {
                _: new Date().getTime(),
                data: cachedData
            };
            if (jQuery.isFunction(callback)) callback(cachedData);
        } 
    }
    $.localCache.init(30000);
})(jQuery);

var text_confirm_delete_item = "";
var text_cart_total = "%total% item(s) - %price%";
var text_cart_total2 = "%total%";
var text_waiting = "Adding....";
var extendFunctions = {};
var quickview_popup_width = "60%";
var quickview_popup_height = "80%";
var ajax_cart_mode = "popup"; //popup | flying
var ajax_minicart_selector = ".cart-total";

function flyImageToCart(imgtodrag) {
    var cart = jQuery(ajax_minicart_selector);

        if (imgtodrag && typeof(imgtodrag) != "undefined" && imgtodrag !="" && imgtodrag != null) {
            var imgclone = imgtodrag.clone()
                .offset({
                top: imgtodrag.offset().top,
                left: imgtodrag.offset().left
            })
                .css({
                'opacity': '0.5',
                    'position': 'absolute',
                    'height': '150px',
                    'width': '150px',
                    'z-index': '100'
            })
                .appendTo(jQuery('body'))
                .animate({
                    'top': cart.offset().top + 10,
                    'left': cart.offset().left + 10,
                    'width': 75,
                    'height': 75
            }, 1000, 'easeInOutExpo');
            
            setTimeout(function () {
                cart.effect("shake", {
                    times: 2
                }, 200);
            }, 1500);

            imgclone.animate({
                'width': 0,
                'height': 0
            }, function () {
                jQuery(this).detach()
            });
        }
}
function lazyLoadIDefault(){
    if(jQuery("img.lazy").length > 0) {
        jQuery("img.lazy").lazyload({ event: "scroll whenever-i-want", threshold : 200, effect: "show"});/*effect: show | fadein*/
    }
}

function isInIframe() {
    var isInIframe = (window.location != window.parent.location) ? true : false;
    return isInIframe;
}

function isIEBroswer() {

    var ua = window.navigator.userAgent;
    var msie = ua.indexOf("MSIE ");

    if (msie > 0 || !!navigator.userAgent.match(/Trident.*rv\:11\./))      // If Internet Explorer, return version number
        return true;

   return false;
}

// http://coveroverflow.com/a/11381730/989439
function mobilecheck() {
    var check = false;
    (function(a){if(/(android|ipad|playbook|silk|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test(a)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0,4)))check = true})(navigator.userAgent||navigator.vendor||window.opera);
    return check;
}


function click_delete_item( ){
   return confirm( text_confirm_delete_item );
}
function getAjaxCart() {
    var document_object = window.document;
    if(isInIframe()) {
        document_object = window.parent.document;
    }
    if(jQuery.cookie("ves_added_cart") && (1==jQuery.cookie("ves_added_cart"))){
        jQuery.ajax({
            url: minicart_url,
            dataType: 'json', 
            type : 'get',
            success: function(data){
                var div_element = jQuery("<div>");
                jQuery(div_element).html(data.html);
                
                if( jQuery("#cart-total", document_object).length > 0 ) {

                    var cart_total = text_cart_total.replace("%total%", data.summary_qty );
                    cart_total = cart_total.replace("%price%", data.subtotal );
                    
                    jQuery("#cart-total", document_object).html( cart_total );
                }

                if( jQuery("#cart-total2", document_object).length > 0 ) {

                    var cart_total2 = text_cart_total2.replace("%total%", data.summary_qty );
                    cart_total2 = cart_total2.replace("%price%", data.subtotal );
                    
                    jQuery("#cart-total2", document_object).html( cart_total2 );
                }

                if( jQuery(div_element).find(".block-cart").length > 0){
                   jQuery('.block-cart', document_object).html( jQuery(div_element).find(".block-cart").first().html() );
                }

                if(jQuery('.block-cart .btn-remove', document_object).length > 0){
                    jQuery('.block-cart .btn-remove', document_object).attr('onclick','').unbind('click');
                    jQuery('.block-cart .btn-remove', document_object).off("click");
                    jQuery(".block-cart .btn-remove", document_object).bind('click', function(e){
                        e.preventDefault();
                       
                        if(click_delete_item()){
                           deleteItemCart( jQuery(this).attr("href") );  
                        }
                        
                        return false;
                    });
                }
                if(jQuery("#minicart-content", document_object).length > 0) {
                    jQuery("#minicart-content .owlcarousel", document_object).each(function(){
                        var config = {
                            navigation : true, // Show next and prev buttons
                            slideSpeed : 600,
                            paginationSpeed : 400,
                            navigationText: ["<a href='javascript:;'><i class='fa fa-angle-left'></i></a>","<a href='javascript:;'><i class='fa fa-angle-right'></i></a>"],
                            pagination : true,
                            autoPlay : false,
                            lazyLoad: false,
                            responsive: true,
                            autoWidth: false,
                            autoHeight: true,
                            items : 3
                         };
                        var owl = jQuery(this).owlCarousel( config );
                    })
                }
            }
        });
        jQuery.cookie("ves_added_cart", 0);
    } else {
        jQuery('#cart', document_object).addClass('active');
        if(jQuery("#minicart-content", document_object).length > 0) {
            jQuery('#minicart-content .block-cart', document_object).html( jQuery("#cart").find(".block-cart").first().html() );
            if(jQuery('#minicart-content .block-cart .btn-remove'), document_object.length > 0){
                jQuery('#minicart-content .block-cart .btn-remove', document_object).attr('onclick','').unbind('click');
                jQuery('#minicart-content .block-cart .btn-remove', document_object).off("click");
                jQuery("#minicart-content .block-cart .btn-remove", document_object).bind('click', function(e){
                    e.preventDefault();
                   
                    if(click_delete_item()){
                       deleteItemCart( jQuery(this).attr("href") );  
                    }
                    
                    return false;
                });
            }

            jQuery("#minicart-content .owlcarousel", document_object).each(function(){
                var config = {
                    navigation : true, // Show next and prev buttons
                    slideSpeed : 600,
                    paginationSpeed : 400,
                    pagination : true,
                    navigationText: ["<a href='javascript:;'><i class='fa fa-angle-left'></i></a>","<a href='javascript:;'><i class='fa fa-angle-right'></i></a>"],
                    autoPlay : false,
                    lazyLoad: false,
                    responsive: true,
                    autoWidth: false,
                    autoHeight: true,
                    items : 3
                 };
                var owl = jQuery(this).owlCarousel( config );
            })
        }
    }
}
function showMiniCart(){

    getAjaxCart();
    jQuery('#cart').addClass('active');
    setTimeout(function(){
        jQuery('#cart').removeClass('active');
        if(jQuery("#minicart-content").length > 0) {
            jQuery("#minicart-content").slideUp();
        }
    }, 5000);

}

function deleteItemCart( removeUrl ){
    removeUrl += '&isAjax=1';
    jQuery.ajax({
            url: removeUrl,
            dataType: 'json',
            type: 'post',
            data: "isAjax=1",
            success: function(data) {
                jQuery.cookie("ves_added_cart", 1 );
                showMiniCart();
            }
    });
}


function addToCart( addtocartURL, obj ) {
    var obj = obj || null;
    quantity = typeof(quantity) != 'undefined' ? quantity : 1;
    
    if(obj) {
       var $qty_obj = jQuery(obj).parent().find("input[name=qty]");
       if($qty_obj.length > 0) {
            quantity =  parseInt( $qty_obj.val() );
       } else {
            quantity = 0
       }
       if(!quantity) {
           var $qty_obj2 = jQuery(obj).parent().parent().find("input[data-inputqty=true]");
           if($qty_obj2.length > 0) {
                quantity =  parseInt( $qty_obj2.val() );
           } else {
                quantity = 1;
           }
       }
    }
    
    if(addtocartURL.match(/checkout\/cart/)){
        if(ajax_cart_mode == "popup") {
            var width = jQuery(window).width(), height = jQuery(window).height();
            if(!mobilecheck() && !isInIframe() ) {
                jQuery.colorbox({overlayClose: true,
                            opacity: 0.5,
                            initialWidth: "26%",
                            initialHeight: "15%",
                            top: "20%",
                            html: ''});
            } else {
                if(jQuery("#ajax-cart-loading").length <= 0 ) {
                    var $loading_image = jQuery('<div class="ajax-loading-wrapper" id="ajaxcartLoading"><img class="ajaxloading" id="ajax-cart-loading" src="'+ajax_loading_image_url+'" alt="Loading..."/></div>');

                    jQuery("body").append( $loading_image );
                }

                if(jQuery("#ajaxcartLoading").length > 0) {
                    jQuery("#ajaxcartLoading").show();
                }
            }
        } else if(ajax_cart_mode == "flying") {
            if(obj) {
                var product_image_id = jQuery(obj).data("productimage");
                if(product_image_id && jQuery(product_image_id).length > 0){
                    var product_image = jQuery(product_image_id).eq(0);
                } else {
                    var product_image = jQuery(obj).parent().parent().parent().find(".product-image > img").eq(0);
                }
                
                flyImageToCart(product_image);
            }
        }

        jQuery.ajax({
            url: addtocartURL,
            dataType: 'json',
            type: 'post',
            data: "qty="+quantity+"&isAjax=1",
            success: function(data) {
                jQuery('.success, .warning, .attention, .information, .error').remove();
                if (data.status == "SUCCESS") {
                    if(ajax_cart_mode == "popup") {
                        if(!mobilecheck() && !isInIframe() ) {
                            jQuery.colorbox({overlayClose: true,
                                            opacity: 0.5,
                                            width: "30%",
                                            top: "20%",
                                            html: data.message});
                            setTimeout(function(){
                                jQuery.colorbox.close();
                            }, 10000);
                        } else {
                            if(jQuery("#ajaxcartLoading").length > 0) {
                                jQuery("#ajaxcartLoading").hide();
                            }
                            jQuery('html, body').animate({ scrollTop: 0 }, 'slow');
                            jQuery('#notification').html('<div class="success ajax-cart-success" style="display: none;border:1px solid">' + data.message + '<a class="close btn-remove" href="javascript:;" onclick="jQuery(\'.success, .warning, .attention, .information, .error\').remove()">X</a></div>');
                            
                            jQuery('.success').fadeIn('slow').delay(5000).hide(0);
                        }
                    } else if(ajax_cart_mode == "flying") {
                        jQuery('html, body').animate({ scrollTop: 0 }, 'slow'); 
                    }
                    jQuery.cookie("ves_added_cart", 1 );

                    jQuery('#cart > .heading a').click();
                    

                    if (typeof extendFunctions !== "undefined" && typeof extendFunctions.showMiniCart !== "undefined") {
                        extendFunctions.showMiniCart.each(function(item_function, index) {
                            if(jQuery.isFunction(item_function)) {
                                item_function(data);
                            }
                        })
                    }
                   // if(mobilecheck()) {
                    //    jQuery('html, body').animate({ scrollTop: 0 }, 'slow'); 
                   // }
                    
                }else{
                    setLocation( addtocartURL );
                }
            }
        });
    }else{
        setLocation( addtocartURL );
    }
    
}

(function($) {
 var SidebarMenuEffects = (function() {

    function hasParentClass( e, classname ) {
        if(e === document) return false;
        if( classie.has( e, classname ) ) {
            return true;
        }
        return e.parentNode && hasParentClass( e.parentNode, classname );
    }
    function parseBool(val)
    {
        if ((typeof val === 'string' && (val.toLowerCase() === 'true' || val.toLowerCase() === 'yes')) || val === 1)
            return true;
        else if ((typeof val === 'string' && (val.toLowerCase() === 'false' || val.toLowerCase() === 'no')) || val === 0)
            return false;

        return val;
    }

    function createOffcanvasMenu(off_menu_selector, off_menu_id, is_verticalmenu_actived, effect) {
        var $mcontent = $(off_menu_selector + ' .navbar .navbar-nav');
        var $mmenutab = $(off_menu_selector + ' .navbar-offcanvas-tab');
        var $mcontent_parent = $mcontent.parent();
        var is_offcanvas = $(off_menu_selector).data("offcanvas");

        if($mmenutab.length > 0) {
            var tab_label = $mmenutab.html();
        } else {
            var tab_label = '<i class="fa fa-bars"></i>';
        }

        if(typeof(effect) == 'undefined' || effect == "" || effect == 0) {
            effect = 3;
        }

        if(is_offcanvas == "" || typeof(is_offcanvas) == "undefined") {
            is_offcanvas = true;
        }
        is_offcanvas = parseBool(is_offcanvas);
        
        if(is_offcanvas) {

            //If vertical menu
            if($("#"+off_menu_id).length > 0) {
                var $offcmenu = $('#'+off_menu_id);
            } else {
                var $offcmenu = $('<nav id="'+off_menu_id+'" class="offcanvas-menu offcanvas-effect-'+effect+' hidden-lg hidden-md"><div class="menu-offcanvas-inner"></div></nav>');
            }
            //If exists off canvas menu tab
            var is_canvastab_exists = false;

            if(typeof($("#offCanvasMenuTab")) == "undefined" || $("#offCanvasMenuTab").length <= 0) {
                var $tab_wrapper = $('<div class="offcanvas-menu-tab" id="offCanvasMenuTab"><div class="tab-nav"><ul class="nav nav-tabs"></ul></div><div class="tab-content"></div></div>');
            } else {
                var $tab_wrapper = $("#offCanvasMenuTab");
                is_canvastab_exists = true;
            }
            
            /* Append navigation on top */
            var $navigation_top = $mcontent_parent.find(".close-block");
           
            /* Append site logo on top */
            /* Append search form on top */
            /* Append social links on bottom (facebook, twitter, google plus, instagram, youtube, website) and Append copyright on bottom */
            var $menufooter = $mcontent_parent.find(".menu-footer");
            
            //Insert tab content
            if(is_verticalmenu_actived) { //Is vertical menu
                var $vertical_tab_nav = $('<li><a href="#offcanvasmenu-vertical" data-toggle="tab">'+tab_label+'</a></li>');
                var $vertical_tab_content = $('<div class="tab-pane offcanvas-menu-tab" id="offcanvasmenu-vertical"></div>');

                //Append content
                $(".nav-tabs", $tab_wrapper ).append( $vertical_tab_nav );
                $vertical_tab_content.append( $mcontent.clone() );
                //Append top navigation
                if($navigation_top.length > 0) {
                    $vertical_tab_content.prepend($navigation_top.first());
                }
                //Append footer block
                if($menufooter.length > 0) {
                    $vertical_tab_content.append($menufooter.first());
                }
                $(".tab-content", $tab_wrapper ).append( $vertical_tab_content );

            } else { //Else megamenu menu
                var $menu_tab_nav = $('<li><a href="#offcanvasmenu-horizontal" data-toggle="tab">'+tab_label+'</a></li>');
                var $menu_tab_content = $('<div class="tab-pane offcanvas-menu-tab" id="offcanvasmenu-horizontal"></div>');

                //Append content
                $(".nav-tabs", $tab_wrapper ).append( $menu_tab_nav );
                $menu_tab_content.append( $mcontent.clone() );
                //Append top navigation
                if($navigation_top.length > 0) {
                    $menu_tab_content.prepend($navigation_top.first());
                }
                //Append footer block
                if($menufooter.length > 0) {
                    $menu_tab_content.append($menufooter.first());
                }
                $(".tab-content", $tab_wrapper ).append( $menu_tab_content );
            }

            if(!is_canvastab_exists) {
                $(".menu-offcanvas-inner", $offcmenu ).append( $tab_wrapper );
                $("body").append( $offcmenu );

                $('#offCanvasMenuTab .nav-tabs a:first').tab('show');
            }
            
            $(".navbar-nav", $offcmenu  ).removeClass("navbar-nav").removeClass("nav").removeClass("verticalmenu").addClass("megamenu").addClass("menu-offcanvas-content");


            var $btn = $(off_menu_selector + " .navbar-toggle");

            var eventtype = mobilecheck() ? 'touchstart' : 'click';
            $($btn).bind( eventtype, function(e){
                $("#offcanvas-container").toggleClass(  "offcanvas-menu-open" ).addClass( "offcanvas-effect-"+effect );
                 
                $("#page").bind( eventtype , function (){
                    $("#offcanvas-container").removeClass(  "offcanvas-menu-open" );
                    $("#page").unbind( eventtype );
                } );

                e.stopPropagation();       
               return false;
            } );//End button event register

        }
    }
    function init() {
        
        $(document).ready( function(){
            
            if($(".offset-canvas-menu-play").length > 0) {
                var off_menu_id = "menu-offcanvas";
                var effect_type = 3;
                
                $(".offset-canvas-menu-play").each(function() {
                    var is_offcanvas_tmp = $(this).data("offcanvas");
                    
                    var off_menu_selector = "#mainmenutop";
                    var is_verticalmenu_actived = false;

                    if(is_offcanvas_tmp == true ) {
                        off_menu_selector = "#" + $(this).attr("id");
                    }
                    if($(this).hasClass("verticalmenu")) {
                        is_verticalmenu_actived = true;
                    }

                    //Create off canvas menu
                    createOffcanvasMenu(off_menu_selector, off_menu_id, is_verticalmenu_actived, effect_type);

                }) //End foreach

                //Start Register Events
                var $btn = $("#"+off_menu_id+" .menu-offcanvas-inner .button-close-menu");

                var eventtype = mobilecheck() ? 'touchstart' : 'click';
                $($btn).bind( eventtype, function(e){
                        $("#offcanvas-container").toggleClass(  "offcanvas-menu-open" ).addClass( "offcanvas-effect-"+effect_type );
                         
                        $("#page").bind( eventtype , function (){
                            $("#offcanvas-container").removeClass(  "offcanvas-menu-open" );
                            $("#page").unbind( eventtype );
                        } );

                        e.stopPropagation();       
                       return false;
                } );
                /*  Fix First Click Menu */
                $(document.body).on(eventtype, '#'+off_menu_id+' [data-toggle="dropdown"]' ,function(){
                    var clickable = $(this).data("clickable");
                    clickable = (typeof(clickable) != "undefined")?clickable:1;

                    if(!mobilecheck()) {
                        clickable = 1;
                    }

                    if(clickable == 1) {
                        if(!$(this).parent().hasClass('open') && this.href && this.href != '#' && this.href != 'javascript:;' && this.href != 'javascript:void(0);'){
                            window.location.href = this.href;
                        }
                    }

                    return false;
                });

                $(document.body).on(eventtype, '#'+off_menu_id+' .open-child' ,function(){
                    var find_sub_class = ".dropdown-menu";
                    if ($(this).parent().hasClass('over')) {
                        $(this).parent().removeClass('over').find('>'+find_sub_class).removeClass("in");
                        $(this).parent().removeClass('over').find('>'+find_sub_class).slideUp(200);
                    }else{
                        $(this).parent().removeClass('over').find('>'+find_sub_class).addClass("in");
                        $(this).parent().parent().find('>li.over').removeClass('over').find('>'+find_sub_class).slideUp(200);
                        $(this).parent().addClass('over').find('>'+find_sub_class).slideDown(200);
                    }
                });

                //End Register Events
                
            } //End If $(".offset-canvas-menu-play").length > 0
            
           
        } ); // End document ready  
    } // End function init()

    init();//Run init function
})();

$('.dropdown').on("hide.bs.dropdown", function( e) {
    $(this).removeClass("open");
    e.preventDefault();
    return false;
});
/* Offcanvas Sidebars */
$(window).load(function() {
   

    if($("#minicart-content").length > 0) {
        $("#minicart-content .close-btn").bind("click", function(e) {
            $("#minicart-content").slideToggle();
            $('#cart').removeClass('active');
        })

    }
    
    if( $("#columns").hasClass("offcanvas-siderbars") ) { 
        //$('.offcanvas-sidebars-buttons button').hide();
        $( ".sidebar" ).parent().parent().find("section").addClass("main-column");
        $( ".sidebar" ).each( function(){
            $('[data-for="'+$(this).attr("id")+'"]').show();
            $(this).parent().attr("id","ves-"+$(this).attr("id") ).addClass("offcanvas-sidebar");
        } );
        $(".offcanvas-sidebars-buttons button").bind( "click", function(){
            if( $(this).data("for") == "column-right" ){
                 $(".offcanvas-siderbars").removeClass("column-left-active");
            }else {
                 $(".offcanvas-siderbars").removeClass("column-right-active");
            }
            $(".offcanvas-siderbars").toggleClass( $(this).data("for")+"-active" );
            $("#ves-"+$(this).data("for") ).toggleClass("canvas-show");
        } );
     }

     /** 
     * 
     * Automatic apply  OWL carousel
     */
    if($(".owl-carousel-play .owl-carousel").length > 0) {
    $(".owl-carousel-play .owl-carousel").each( function(){
        var items_desktop = $(this).data( 'slide-desktop' );
        var items_desktop_small = $(this).data( 'slide-desktop-small' );
        var items_tablet = $(this).data( 'slide-tablet' );
        var items_tablet_small = $(this).data( 'slide-tablet-small' );
        var items_mobile = $(this).data( 'slide-mobile' );
        var items_custom = $(this).data( 'slide-custom' );

        var lazyload = $(this).data( 'lazyload' );

        //Desktop
        if(items_desktop && items_desktop != "false" && items_desktop != "0") {
            items_desktop = JSON.parse("["+items_desktop+"]");
        } else if(items_desktop == "false" || items_desktop == "0") {
            items_desktop = false;
        } else {
            items_desktop = [1199,4];
        }
        //Desktop Small
        if(items_desktop_small && items_desktop_small != "false" && items_desktop_small != "0") {
            items_desktop_small = JSON.parse("["+items_desktop_small+"]");
        } else if(items_desktop_small == "false" || items_desktop_small == "0") {
            items_desktop_small = false;
        } else {
            items_desktop_small = [979,3];
        }
        //Tablet
        if(items_tablet && items_tablet != "false" && items_tablet != "0") {
            items_tablet = JSON.parse("["+items_tablet+"]");
        } else if(items_tablet == "false" || items_tablet == "0") {
            items_tablet = false;
        } else {
            items_tablet = [768,2];
        }
        //Tablet Small
        if(items_tablet_small && items_tablet_small != "false" && items_tablet_small != "0") {
            items_tablet_small = JSON.parse("["+items_tablet_small+"]");
        } else if(items_tablet_small == "false" || items_tablet_small == "0") {
            items_tablet_small = false;
        } else {
            items_tablet_small = false;
        }
        //Mobile
        if(items_mobile && items_mobile != "false" && items_mobile != "0") {
            items_mobile = JSON.parse("["+items_mobile+"]");
        } else if(items_mobile == "false" || items_mobile == "0") {
            items_mobile = false;
        } else {
            items_mobile = [479,1];
        }
        //Custom 
        if(items_custom && items_custom != "false" && items_custom != "0") {
            items_custom = JSON.parse("["+items_custom+"]");
        } else if(items_custom == "false" || items_custom == "0") {
            items_custom = false;
        } else {
            items_desktop = false;
        }

        //Custom 
        if(lazyload && lazyload != "false" && lazyload != "0") {
            lazyload = true;
        } else if(lazyload == "false" || lazyload == "0") {
            lazyload = false;
        } else {
            lazyload = false;
        }

        var navigationText = ["<a href='javascript:;'><i class='fa fa-angle-left'></i></a>","<a href='javascript:;'><i class='fa fa-angle-right'></i></a>"];
        if(navText = $(this).data('navigation-text')){
            var navText = $(this).data('navigation-text');
            console.log(navText);
            if(typeof(navText) == 'object' && navText.prev && navText.next){
                var prevText = navText.prev.replace(/`/g,"'");
                var nextText = navText.next.replace(/`/g,"'");
                navigationText = [prevText,nextText];
            }
        }
        var config = {
            navigation : $(this).data( 'navigation' ), // Show next and prev buttons
            slideSpeed : $(this).data( 'slide-speed' ),
            paginationSpeed : 400,
            pagination : $(this).data( 'pagination' ),
            navigationText: navigationText,
            autoPlay : $(this).data( 'auto' ),
            lazyLoad: lazyload,
            responsive: true,
            autoWidth: false,
            autoHeight: true,
            itemsDesktop : items_desktop,
            itemsDesktopSmall : items_desktop_small,
            itemsTablet : items_tablet,
            itemsTabletSmall : items_tablet_small,
            itemsMobile : items_mobile,
            itemsCustom : items_custom
         };
        var owl = $(this);
        if( $(this).data('slide-default') == 1 ){
            config.singleItem = true;
        } else {
            config.items = $(this).data( 'slide-default' );
        }
        $(this).owlCarousel( config );
        $('.owl-left',$(this).parent()).click(function(){
              owl.trigger('owl.prev');
              return false; 
        });
        $('.owl-right',$(this).parent()).click(function(){
            owl.trigger('owl.next');
            return false; 
        });
     } );
    }
} );

 


$(window).ready( function(){
    /*  Fix First Click Menu */
  
    $('[data-toggle="tooltip"]').tooltip();
    
    $(".quantity-adder .add-action").click( function(){
        if( $(this).hasClass('add-up') ) {  
            $("[name=qty]",'.quantity-adder').val( parseInt($("[name=qty]",'.quantity-adder').val()) + 1 );
        }else {
            if( parseInt($("[name=qty]",'.quantity-adder').val())  > 1 ) {
                $("input",'.quantity-adder').val( parseInt($("[name=qty]",'.quantity-adder').val()) - 1 );
            } 
        }
    } );

    /* custom title style for modules in mail content */
    $( ".box-heading" ).each( function(){
         if( $(this).children('span').length  ){
            var re = /\s+/;
            var text = $(this).text().split(re);
            if( text.length > 1){ 
                var otext = $(this).text().replace( text[0], '<span>'+text[0]+'</span>');
                $(this).children('span').html( otext );
            }
       
         }
    } );

} );


    $(window).ready( function(){
        $.cookie("ves_added_cart", 0);
        //Init Color Box popup
        if(jQuery('.colorbox').length > 0) {
            jQuery('.colorbox').colorbox({
                        overlayClose: true,
                        opacity: 0.5,
                        rel: false,
                        onLoad:function(){
                            /*
                            jQuery("#cboxNext").remove(0);
                            jQuery("#cboxPrevious").remove(0);
                            jQuery("#cboxCurrent").remove(0);
                            */
                        }
                    });
        }
        //Init quick view popup


        if(jQuery('.ves-colorbox').length > 0) {
            jQuery(".ves-colorbox").each(function(){
                var popup_width = jQuery(this).data("width");
                var popup_height = jQuery(this).data("height");

                if(typeof (popup_width) == "undefined" || popup_width == "") {
                    popup_width = quickview_popup_width;
                }

                if(typeof (popup_height) == "undefined" || popup_height == "") {
                    popup_height = quickview_popup_height;
                }
                

                jQuery(this).colorbox({
                        width: popup_width, 
                        height: popup_height,
                        overlayClose: true,
                        opacity: 0.5,
                        iframe: true, 
                });
            })
            
        }
        

        if(jQuery(".scrollup").length > 0) {
            // scroll-to-top button show and hide
            jQuery(document).ready(function(){
                jQuery(window).scroll(function(){
                    if (jQuery(this).scrollTop() > 100) {
                        jQuery('.scrollup').fadeIn();
                    } else {
                        jQuery('.scrollup').fadeOut();
                }
            });
            // scroll-to-top animate
            jQuery('.scrollup').click(function(){
                jQuery("html, body").animate({ scrollTop: 0 }, 600);
                    return false;
                });
            });
        }
        /* Ajax Cart */
        /*if(typeof(ajaxCart) != "undefined" && ajaxCart){*/

            $('#cart > .heading').on('click','a', function(e) {

                e.preventDefault();

                $('#cart').addClass('active');

                //$('#cart').load(minicart_url + ' #cart > *');

                getAjaxCart();

                if(!jQuery.cookie("ves_added_cart") || (jQuery.cookie("ves_added_cart") && (0==jQuery.cookie("ves_added_cart") ) )) {
                    if($("#minicart-content").length > 0) {
                        $("#minicart-content").slideToggle();
                    }
                }
                if($("#minicart-content").length > 0) {
                    
                } else {
                    $(document).on('mouseleave', '#cart', function() {
                        $(this).removeClass('active');
                    });
                }

                $("body").not("#cart").click(function(){
                    $(this).removeClass('active');
                })
            });
            if($('.block-cart .btn-remove').length > 0){

                $('.block-cart .btn-remove').attr('onclick','').unbind('click');
                $(".block-cart .btn-remove").bind('click', function(e){

                    e.preventDefault();
                   
                    if(click_delete_item()){
                       deleteItemCart( $(this).attr("href") );  
                    }
                    
                    return false;
                });
            }
            

       /* } */
        var $quickview_body = jQuery(".tempcp-quickview-view");
        if($quickview_body.length > 0) {
            var $compare_link = $quickview_body.find(".link-compare");
            if($compare_link.length <= 0) {
                $compare_link = $quickview_body.find(".compare");
            }
            
            if($compare_link.length > 0) {
                $compare_link.click(function(){
                    var url = jQuery(this).attr("href");
                    var $parent_iframe = window.parent.jQuery(".cboxIframe");
                    if($parent_iframe.length > 0 ){ 
                        $parent_iframe.first().attr("src", url);
                        parent.location.reload();
                    }

                    return false;
                })
            }
        }

        //Init Color Box popup
        if(jQuery('.carousel').length > 0 && jQuery('.carousel').find(".carousel-inner").length > 0) {
            /*
            jQuery(".carousel").swipe( {
                //Generic swipe handler for all directions
                swipeLeft:function(event, direction, distance, duration, fingerCount) {
                    jQuery(this).carousel('next'); 
                },
                swipeRight: function() {
                    jQuery(this).carousel('prev'); 
                },
                //Default is 75px, set to 0 for demo so any distance triggers swipe
                threshold:0
            });*/
        }
        //Init quick view popup
        var addtocartFormId = "#product_addtocart_form";
        if(jQuery("#product_addtocart_form_from_popup").length > 0){
           addtocartFormId = "#product_addtocart_form_from_popup";
        }
        if(typeof(productAddToCartForm) != "undefined" && typeof(ajaxCart) != "undefined" && ajaxCart){
            productAddToCartForm.submit = function(button, url) {
                if(jQuery(button).length > 0 && jQuery(button).data("action") == "checkout-form-submit") {
                    return true;
                }
                if (this.validator.validate()) {
                    var form = this.form;
                    var oldUrl = form.action;
                    if (url) { form.action = url; } 
                    var e = null;

                    if(!url){ url = $(addtocartFormId).attr('action'); }
                    /*url = url.replace("checkout/cart","ajax/index");*/

                    //var data = $(addtocartFormId).serialize();
                    var data = new FormData($(addtocartFormId)[0]);
                    data.append("isAjax", 1);
                    //data += '&isAjax=1';
                    /*$('#ajax_loader').show();*/
                    /*
                    jQuery(button).hide();
                    jQuery(button).parent().append('<button class="btn button ajax-loading" type="button">' + text_waiting + '</button>');
                    */
                    if(ajax_cart_mode == "popup") {
                        var width = jQuery(window).width(), height = jQuery(window).height();
                        if(!mobilecheck() && !isInIframe()) {
                            jQuery.colorbox({overlayClose: true,
                                        opacity: 0.5,
                                        initialWidth: "26%",
                                        initialHeight: "15%",
                                        top: "20%",
                                        html: ''});
                        } else {
                            if(jQuery("#ajax-cart-loading").length <= 0 ) {
                                var $loading_image = jQuery('<div class="ajax-loading-wrapper" id="ajaxcartLoading"><img class="ajaxloading" id="ajax-cart-loading" src="'+ajax_loading_image_url+'" alt="Loading..."/></div>');

                                jQuery("body").append( $loading_image );
                            }

                            if(jQuery("#ajaxcartLoading").length > 0) {
                                jQuery("#ajaxcartLoading").show();
                            }
                        }
                    } else if(ajax_cart_mode == "flying") {
                        flyImageToCart(jQuery("#image-main"));
                    }
                   
                    try {
                         $.ajax({
                                url: url,
                                dataType: 'json', 
                                type : 'post',
                                data: data,
                                async: false,
                                contentType: false,
                                processData: false,
                                success: function(data){ 
                                    /*$('#ajax_loader').hide();*/
                                    /*
                                    jQuery(button).show();
                                    jQuery(".ajax-loading", jQuery(button).parent()).remove();*/

                                    jQuery('.success, .warning, .attention, .information, .error').remove();

                                    if (data.status == "SUCCESS") {
                                        
                                        if(ajax_cart_mode == "popup") {
                                            if(!mobilecheck() && !isInIframe() ) {
                                                
                                                jQuery.colorbox({overlayClose: true,
                                                                opacity: 0.5,
                                                                width: "30%",
                                                                top: "20%",
                                                                html: data.message});
                                                setTimeout(function(){
                                                    jQuery.colorbox.close();
                                                }, 10000);
                                                
                                            } else {
                                                if(jQuery("#ajaxcartLoading").length > 0) {
                                                    jQuery("#ajaxcartLoading").hide();
                                                }
                                               jQuery('html, body').animate({ scrollTop: 0 }, 'slow'); 
                                               jQuery('#notification').html('<div class="success ajax-cart-success" style="display: none;border:1px solid">' + data.message + '<a onclick="jQuery(\'.success, .warning, .attention, .information, .error\').remove()" class="close btn-remove" href="javascript:;">X</a></div>');
                                            
                                                jQuery('.success').fadeIn('slow').delay(5000).hide(0);

                                            }
                                        } else if(ajax_cart_mode == "flying") {
                                            jQuery('html, body').animate({ scrollTop: 0 }, 'slow'); 
                                        }
                                        jQuery.cookie("ves_added_cart", 1);
                                        showMiniCart();
                                        
                                        //if(mobilecheck()) {
                                            //jQuery('html, body').animate({ scrollTop: 0 }, 'slow'); 
                                        //}
                                    } else if(data.status == "ERROR") {
                                        if(!mobilecheck() && !isInIframe() ) {
                                            
                                            jQuery.colorbox({overlayClose: true,
                                                            opacity: 0.5,
                                                            width: "30%",
                                                            top: "20%",
                                                            html: data.message});
                                            setTimeout(function(){
                                                jQuery.colorbox.close();
                                            }, 10000);
                                            
                                        } else {
                                            alert( data.message );
                                        }    
                                    }else{
                                        setLocation( url );
                                    }
                                } 
                                }); 
                    } catch (e) { } 
                    this.form.action = oldUrl; 
                    if (e) { throw e; } 
                 }
                 return false;
            }.bind(productAddToCartForm);
        }
        
    });

})(jQuery);
