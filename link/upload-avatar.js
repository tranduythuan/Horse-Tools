jQuery(document).ready(function($){
    $(document).on('click', '.horsetools-image', function(e) {
        e.preventDefault();
        var button = $(this);
        var horsetools_image_id = $('#horsetools_image_id');
        var mediaUploader = wp.media({
            title: 'Select Image',
            button: {
                text: 'Select Image'
            },
            multiple: false  
        });
        mediaUploader.open();
        mediaUploader.on('select', function(){
            var attachment = mediaUploader.state().get('selection').first().toJSON();
            horsetools_image_id.val(attachment.id);
            $('#horsetools-img').attr('src', attachment.url).show();
        });
    });
    $(document).on('click', '#reset-hinh-anh', function(e) {
        e.preventDefault();
        var horsetools_image_id = $('#horsetools_image_id');
        horsetools_image_id.val('');
        $('#horsetools-img').attr('src', '').hide();
    });
});
