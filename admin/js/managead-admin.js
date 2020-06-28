(function ($) {
    'use strict';

    jQuery(document).ready(function () {
        function managead_get_current_count() {
            jQuery.ajax({
                url : managead_block_count.ajax_url,
                type : 'post',
                data : {
                    action : 'managead_get_current_count',
                },
                success: function (response) {
                    addNewAdElement(response);
                }
            });
        }

        function addNewAdElement(manageadNewCount) {
            var elementLayout = $('#managead_field_template').text();
            var newItem = elementLayout.replace( /\%id\%/g, manageadNewCount );

            jQuery('.managead_ad_list').append(newItem);

            var lastAddedItem = $('.managead_field:last-child');
            jQuery(lastAddedItem).addClass("active");
            lastAddedItem.find('.managead_field_body').stop().slideToggle("slow");
            lastAddedItem.find('.managead_field_body').css("display", "block");
        }

        $('.managead_new_add').click(function(){
            jQuery.ajax({
                url : managead_block_count.ajax_url,
                type : 'post',
                data : {
                    action : 'managead_count_increase',
                },
                success: function () {
                    managead_get_current_count();
                }
            });
        });

        jQuery('.delete_ad').click(function () {
            var result = confirm("If you delete it, the places you use this ad won't work.");
            if (result) {
                jQuery(this).closest('.managead_field').remove();
            }
        });

        jQuery('.managead_field_title').on( "click", function(e) {
            var accordion = jQuery(this).closest('.managead_field');
            if (jQuery(this).hasClass('active')){
                jQuery(this).removeClass("active");
                accordion.find('.managead_field_body').stop().slideToggle("slow");
            } else {
                jQuery(this).addClass("active");
                accordion.find('.managead_field_body').stop().slideToggle("slow");
                accordion.find('.managead_field_body').css("display", "block");
            }
            e.stopImmediatePropagation();
            e.preventDefault();
        });
    });

})(jQuery);
