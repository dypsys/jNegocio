// bootstrap carousel in caregories grid and list
function productSlider() {
    jQuery('.carousel').carousel();
}


//intial all
jQuery(document).ready(function() {
    // productSlider();


    /* ---------------------------------------------------------------------- */
    /*	Opacity animation on hover
     /* ---------------------------------------------------------------------- */

    if (jQuery.browser.msie && (jQuery.browser.version == 8 || jQuery.browser.version == 7 || jQuery.browser.version == 6)) {

    } else {

        jQuery("#toggle_sidebar").hover(function() {
            jQuery(".sidebar").stop().animate({opacity: .55}, 300)
        }, function() {
               jQuery(".sidebar").stop().animate({opacity: 1}, 300)
            }
        );
    }


    /* ---------------------------------------------------------------------- */
    /* Toggle sidebar
     /* ---------------------------------------------------------------------- */
    jQuery("#toggle_sidebar").click(function() {
        jQuery(this).toggleClass("collapse");
        jQuery(".sidebar").toggleClass("close");
        if (jQuery("#maincontent").hasClass("middle")) {
            jQuery("#maincontent").toggleClass('span6 span12');
        } else {
            jQuery("#maincontent").toggleClass('span9 span12');
        }
    });
});