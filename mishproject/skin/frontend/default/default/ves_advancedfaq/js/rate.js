  $j(document).ready(function() {
        
	  $j('.ratings_stars').hover(
            function() {
            	 $j(this).prevAll().andSelf().addClass('ratings_over');
            	 $j(this).nextAll().removeClass('ratings_vote'); 
            },
            function() {
            	 $j(this).prevAll().andSelf().removeClass('ratings_over');
            }
        );
        
    });

    function set_votes(widget) {

        var avg =  $j(widget).data('fsr').whole_avg;
        var votes =  $j(widget).data('fsr').number_votes;
        var exact =  $j(widget).data('fsr').dec_avg;

        
        $j(widget).find('.star_' + avg).prevAll().andSelf().addClass('ratings_vote');
        $j(widget).find('.star_' + avg).nextAll().removeClass('ratings_vote'); 
    }