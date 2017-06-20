
var ComparisonLoadingBox = Class.create();
ComparisonLoadingBox.prototype = {
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

var Comparison = function() {
    return {
        deleteItem : function(product_id,current_product_id){
            comparisonloadingBox.show();
            new Ajax.Request(DELETE_ITEM_PRODUCT_VENDOR, {
                method:'post',
                parameters: {current_product_id: current_product_id,product_id:product_id},
                onSuccess: function(transport) {
                    if (transport.responseText.isJSON()) {
                        var response = transport.responseText.evalJSON();
                        alert(response.msg);
                    }
                    else{
                        $("pricecomparison-table").update(transport.responseText);
                        $("pricecomparison-menu").previous().setAttribute("onclick","pcShowMenu()");
                    }
                    comparisonloadingBox.close();
                },
                onFailure: function() {
                    alert('Please Refesh Page');
                }
            });
        },
        showDialogProduct : function(title){
            $("pricecomparison-menu").hide();
            comparisonloadingBox.show();
            new Ajax.Request(SHOW_LIST_PRODUCT_VENDOR, {
                method:'GET',
                parameters: {page: 1},
                onSuccess: function(transport) {
                    if (transport.responseText.isJSON()) {
                        var response = transport.responseText.evalJSON();
                        /*Excute command */
                        if(response.success){
                            $("list_product_vendor_comparison").update(response.list_item);
                            $("page-list-product-vendor-comparison").update(response.page_html);
                            decorateTable('ves-product-vendor-table');
                            Modalbox.show($('ves-pricecomarison-selectproduct'), {title: title, width:800,height:550});
                           // productloadingBox = new ComparisonLoadingBox('ves_pricecomparison_loading_product','ves_pricecomparison_overlay_product');
                        }else{
                            alert(response.msg);
                        }
                    }
                    else{
                        alert(transport.responseText);
                    }
                    comparisonloadingBox.close();
                },
                onFailure: function() {
                    alert('Please Refesh Page');
                }
            });
        },
        changePage : function(page){
            Comparison.showLoadingBox();
          //  productloadingBox.show();
           // $("ves_pricecomparison_loading_page").show();
            new Ajax.Request(SHOW_LIST_PRODUCT_VENDOR, {
                method:'GET',
                parameters: {page: page},
                onSuccess: function(transport) {
                    if (transport.responseText.isJSON()) {
                        var response = transport.responseText.evalJSON();
                        /*Excute command */
                        if(response.success){
                            $("list_product_vendor_comparison").update(response.list_item);
                            $("page-list-product-vendor-comparison").update(response.page_html);
                            decorateTable('ves-product-vendor-table');
                            //Modalbox.show($('ves-pricecomarison-selectproduct'), {title: title, width:800,height:550});
                        }else{
                            alert(response.msg);
                        }
                    }
                    else{
                        alert(transport.responseText);
                    }
                    //$("ves_pricecomparison_loading_page").hide();
                    Comparison.closeLoadingBox();
                },
                onFailure: function() {
                    alert('Please Refesh Page');
                }
            });
        },
        seacrh : function(page){
            var q = $("search-product-q").value;
           // $("ves_pricecomparison_loading_page").show();
            Comparison.showLoadingBox();
            new Ajax.Request(SEARCH_LIST_PRODUCT_VENDOR, {
                method:'GET',
                parameters: {q: q,page:page},
                onSuccess: function(transport) {
                    if (transport.responseText.isJSON()) {
                        var response = transport.responseText.evalJSON();
                        /*Excute command */
                        if(response.success){
                            $("list_product_vendor_comparison").update(response.list_item);
                            $("page-list-product-vendor-comparison").update(response.page_html);
                            decorateTable('ves-product-vendor-table');
                            //Modalbox.show($('ves-pricecomarison-selectproduct'), {title: title, width:800,height:550});
                        }else{
                            alert(response.msg);
                        }
                    }
                    else{
                        alert(transport.responseText);
                    }
                  //  $("ves_pricecomparison_loading_page").hide();
                    Comparison.closeLoadingBox();
                },
                onFailure: function() {
                    alert('Please Refesh Page');
                }
            });
        },
        changePageSearch : function(page){
            var q = $("search-product-q").value;
       //     $("ves_pricecomparison_loading_page").show();
            Comparison.showLoadingBox();
            new Ajax.Request(SEARCH_LIST_PRODUCT_VENDOR, {
                method:'GET',
                parameters: {page: page,q:q},
                onSuccess: function(transport) {
                    if (transport.responseText.isJSON()) {
                        var response = transport.responseText.evalJSON();
                        /*Excute command */
                        if(response.success){
                            $("list_product_vendor_comparison").update(response.list_item);
                            $("page-list-product-vendor-comparison").update(response.page_html);
                            decorateTable('ves-product-vendor-table');
                            //Modalbox.show($('ves-pricecomarison-selectproduct'), {title: title, width:800,height:550});
                        }else{
                            alert(response.msg);
                        }
                    }
                    else{
                        alert(transport.responseText);
                    }
                  //  $("ves_pricecomparison_loading_page").hide();
                    Comparison.closeLoadingBox();
                },
                onFailure: function() {
                    alert('Please Refesh Page');
                }
            });
        },
        chooseProduct:function(id,name){
           var current_id = $("ves_comparison_current_product").value;
           if(confirm("Do you want to sell '"+name+"' on this page ?")){
               Modalbox.hide();
               comparisonloadingBox.show();
               new Ajax.Request(CHOOSE_PRODUCT_COMPARISON, {
                   method:'post',
                   parameters: {id: id,current_id:current_id},
                   onSuccess: function(transport) {
                       if (transport.responseText.isJSON()) {
                           var response = transport.responseText.evalJSON();
                           alert(response.msg);
                       }
                       else{
                           $("pricecomparison-table").update(transport.responseText);
                           $("pricecomparison-menu").previous().setAttribute("onclick","Comparison.alertBox()");
                       }
                       comparisonloadingBox.close();
                   },
                   onFailure: function() {
                       alert('Please Refesh Page');
                   }
               });
           }
        },
        alertBox : function(){
            alert("Your product is already listed on this page");
        },
        closeLoadingBox : function(){
            $("ves_pricecomparison_loading_product").hide();
            $("ves_pricecomparison_overlay_product").hide();
        },
        showLoadingBox : function(){
            $("ves_pricecomparison_loading_product").show();
            $("ves_pricecomparison_overlay_product").show();
        }
    }
}();