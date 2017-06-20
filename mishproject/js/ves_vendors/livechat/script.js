
var VesComparison = Class.create();
VesComparison.prototype = {
    initialize: function(dialog){
        this.dialog 	= $(loadingId);
    },
    showDialog: function(){
        new Dialog.Box(this.dialog);
        this.dialog.toggle();
    },
}
