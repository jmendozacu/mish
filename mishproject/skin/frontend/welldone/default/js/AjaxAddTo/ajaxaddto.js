function showMessage(data,id,action){
    if(data)
    {
        if(action=='wishlist')
        {
            jQuery('#modalAddToCart .wishlist-hide').hide();
        }else
        {
            turnBtnLoad('.add-to-cart-btn-'+id,'off');
            jQuery('#modalAddToCart .wishlist-hide').show();
        }
        jQuery('#modalAddToCart .msg-success').html(data.message);
    }
    $j('#modalAddToCart').modal("toggle");

}

function showLoader(id){
    turnBtnLoad('.add-to-cart-btn-'+id,'on');
}

function turnBtnLoad(selector,flag) {
    if(flag=='off'){
        jQuery(selector).removeClass('btn--wait');
    }
    else
    {
        jQuery(selector).addClass('btn--wait');
    }
}

function setLocationAjax(url,id){
    if (url.indexOf("?")){
        url = url.split("?")[0];
    }
    url += 'isAjax/1';
    url = url.replace("checkout/cart","ajax/index");

    if(window.location.href.match("https://") && !url.match("https://")){
        url = url.replace("http://", "https://");
    }

    if(window.location.href.match("http://") && !url.match("http://")){
        url = url.replace("https://", "http://");
    }

    showLoader(id);
    try {
        jQuery.ajax({
            url : url,
            dataType : 'json',
            success : function(data) {
                showMessage(data,id);
                if(data.status != 'ERROR'){
                    ajax_add_to_cart_post_update(data);
                }
            }
        });
    } catch (e) {

    }
}

function ajaxCompare(url,id){
    url = url.replace("catalog/product_compare/add","ajax/whishlist/compare");
    if (url.indexOf("?")){
        url = url.split("?")[0];
    }
    url += 'isAjax/1/';
    if(window.location.href.match("https://") && !url.match("https://")){
        url = url.replace("http://", "https://");
    }
    if(window.location.href.match("http://") && !url.match("http://")){
        url = url.replace("https://", "http://");
    }
    try {
        ajax_loading_on();
        jQuery.ajax( {
            url : url,
            dataType : 'json',
            success : function(data) {
                if(data.status != 'ERROR'){
                    if(jQuery('#compareBox').length){
                        jQuery('#compareBox').replaceWith(data.sidebar);
                    }else{
                        jQuery('body').append(data.sidebar)
                    }
                    setTimeout(function(){
                        $j('#compareBox').stop(true, false).removeClass('minimize').addClass('open');
                        $j('body').removeClass('compare-minimize');
                    },300)

                    $j('.hide-compare').on('click',function(e) {
                        e.preventDefault();
                        $j('#compareBox').stop(true, false).removeClass('open').addClass('minimize');
                        $j('body').addClass('compare-minimize');
                    });

                    $j('.show-compare').on('click',function(e) {
                        e.preventDefault();
                        $j('#compareBox').stop(true, false).removeClass('minimize').addClass('open');
                        $j('body').removeClass('compare-minimize');
                    });

                    $j('.close-compare').on('click',function(e) {
                        e.preventDefault();
                        $j('#compareBox').stop(true, false).removeClass('minimize').removeClass('open');
                        $j('body').removeClass('compare-minimize');
                    });
                    ajax_loading_off();
                }
            }
        });
    } catch (e) {}
}

function ajaxWishlist(url,id){
    url = url.replace("wishlist/index","ajax/whishlist");
    if (url.indexOf("?")){
        url = url.split("?")[0];
    }
    url += 'isAjax/1/';
    if(window.location.href.match("https://") && !url.match("https://")){
        url = url.replace("http://", "https://");
    }
    if(window.location.href.match("http://") && !url.match("http://")){
        url = url.replace("https://", "http://");
    }
    try {
        jQuery.ajax( {
            url : url,
            dataType : 'json',
            success : function(data) {
                showMessage(data,id,'wishlist');
                if(data.status != 'ERROR'){
                    if(jQuery('.list-user-menu').length){
                        jQuery('.list-user-menu').replaceWith(data.toplink);
                    }
                }
            }
        });
    } catch (e) {}
}


function ajax_add_to_cart_post_update(data) {
    if(jQuery('.header__cart').length){
        jQuery('.header__cart').html(data.sidebar);
    }

    if(jQuery('.list-user-menu').length){
        jQuery('.list-user-menu').replaceWith(data.toplink);
    }

    update_favicon();

}

function getCartItems(){
    return $j('.header__cart .badge.badge--menu').text();
};

/*function setCartItemsFixedMenu(){
    $j('.badge.fixed_menu').text(getCartItems());
}*/

function update_favicon() {
    if(getCartItems()>0 && $j('body').hasClass('favicon-countable'))
    {
        var favicon = new Favico({   fontStyle : 'normal',  animation : 'slide' });
        favicon.badge(getCartItems());
    }
}
function ajax_loading_on(){
    $j('body').addClass('progress-loading');
    $j('#ajax_loader-wrapper').show();
    $j('body').css('cursor', 'wait');
}
function ajax_loading_off(){
    $j('body').removeClass('progress-loading');
    $j('#ajax_loader-wrapper').hide();
    $j('body').css('cursor', 'inherit');
}