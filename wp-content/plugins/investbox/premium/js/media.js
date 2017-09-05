jQuery(document).ready(function($){
    var tgm_media_frame;
	var value_id;
	var thet;
    $(document.body).on('click.tgmOpenMediaManager', '.tgm-open-media', function(e){
        e.preventDefault();

		value_id = $(this).attr('id');
		thet = $(this).parents('.premium_uploader');

        if ( tgm_media_frame ) {
            tgm_media_frame.open();
            return;
        }
        tgm_media_frame = wp.media.frames.tgm_media_frame = wp.media({
            className: 'media-frame tgm-media-frame',
            frame: 'select',
            multiple: false,
            title: tgm_nmp_media.title,
            library: {
                type: tgm_nmp_media.library,
            },
            button: {
                text:  tgm_nmp_media.button
            }
        });
        tgm_media_frame.on('select', function(){
            var media_attachment = tgm_media_frame.state().get('selection').first().toJSON();
            $('#'+value_id+'_value').val(media_attachment.url);
			if(thet.find('.premium_uploader_img').find('img').length > 0){
			    thet.find('.premium_uploader_img').find('img').attr('src',media_attachment.url);
			} else {
			    thet.find('.premium_uploader_img').html('<img src="'+ media_attachment.url +'" alt="" />');
			}
			thet.find('.premium_uploader_hide').addClass('act');
        });
        tgm_media_frame.open();
    });
	
	$(document).on('click', '.premium_uploader_hide', function(){
		$(this).removeClass('act');
		var par = $(this).parents('.premium_uploader');
		par.find('.premium_uploader_img').find('img').remove();
		par.find('input').val('');
	});
	
});