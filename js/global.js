jQuery(function($) {
   $(document).on('click', '#f1orbitbtn_add_slide', function() {
        var new_li = $('#f1-orbit-slides-extra li:first').clone();
       $(new_li).css('display', 'none');
       $('#f1-orbit-slides').append(new_li);
       $('#f1-orbit-slides li:last').slideDown(400);
       return false;
   });
    $(document).on('blur', '#slider_title', function() {
        if(!$('#slider_slug').val()) {
            $('#slider_slug').val($(this).val().replace(/\s+/g,'-').replace(/[^a-zA-Z0-9\-]/g,'').toLowerCase());
        }
    });
});