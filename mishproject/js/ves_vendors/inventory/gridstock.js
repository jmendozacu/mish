function qtybox(divid, qty) {

    var inpt = '<span id="product_' + divid + '"><input type="text" name="qty" maxlength="6" value=' + qty + ' id="inpt_' + divid + '"></span>';
    jQuery("#product_" + divid).replaceWith(inpt);
    jQuery("#inpt_" + divid).focus();
    jQuery("#inpt_" + divid)
            .focusout(function () {
                jQuery("#product_" + divid).html(qty);
                jQuery.ajax({
                    method:"POST",
                    data:{product_id:divid,field:qty,store:0,value:1000},
                    url: "/vendors/inventory_ajax/savedata"                    
                }).done(function () {
                    alert("done");
                });
            })
            .blur(function () {
                jQuery("#product_" + divid).html(qty);
            });
}