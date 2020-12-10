/**
 * Upload file function for admin media uploader
 *
 * @param button
 * @constructor
 */
function CMB_uploadImage(button){
    let $button = jQuery(button);
    let $delete = $button.closest('tr').find('.custom_media_delete_btn');
    let $image = $button.closest('tr').find('.custom_media_image');
    let $input = $button.closest('tr').find('.custom_media_input');

    let uploader = wp.media({
        title: 'Custom image',
        library: {
            uploadedTo: wp.media.view.settings.post.id,
            type: 'image'
        },
        button: {
            text: 'Use this image'
        },
        multiple: false
    }).on('select', function () {
        let attachment = uploader.state().get('selection').first().toJSON();
        $image.attr('src', attachment.url).show();
        $input.val(attachment.id);
        $delete.show();
    })
    .open();
}

/**
 * Delete function for admin media uploader
 *
 * @param button
 * @constructor
 */
function CMB_deleteImage(button){
    let $button = jQuery(button);
    let $image = $button.closest('tr').find('.custom_media_image');
    let $input = $button.closest('tr').find('.custom_media_input');

    $button.hide();
    $image.attr('src', '').hide();
    $input.val('');
}
