jQuery(function($) {		
	
	var id = 0;
	
	$(document).on('click','.user_comment',function(){
		$('.standart_shadow, #window_user_comment').show();
		
		id = $(this).attr('id').replace('ucomment-','');
		var text = $(this).find('.user_comment_text').html();
		
		$('#hide_user_comment').val(text);
		$('#hide_user_id').val(id);
		
		return false;
	});
	
	$(document).on('click', '.standart_window_close', function(){
		$('.standart_shadow, #window_user_comment').hide();
		return false;
	});	

	var thet = '';
	$('.user_ajax_form').ajaxForm({
	    dataType:  'json',
        beforeSubmit: function(a,f,o) {
		    thet = f;
			thet.find('input[type=submit]').prop('disabled',true);
        },
		error: function(res, res2, res3) {
			console.log('Error text, text1:' + res + ',text2: ' + res2 + ',text3:' + res3);
			for (key in res) {
				console.log(key + ' = ' + res[key]);
			}
		},		
        success: function(ht) {
			if(ht['status'] == 'error') {
				alert(ht['status_text']);
			} else {
				$('.standart_shadow, #window_user_comment').hide();

				if(ht['response'].length > 1){
					$('#ucomment-'+id).find('.user_comment_text').html(ht['response']);
					$('#ucomment-'+id).addClass('has_comment');
				} else {
					$('#ucomment-'+id).find('.user_comment_text').html('');
					$('#ucomment-'+id).removeClass('has_comment');
				}	
			}
		    thet.find('input[type=submit]').prop('disabled',false);
        }
    });	
	
});