jQuery(function($){ 
	
	$(document).on('click','.js_reply_close',function(){
	    $(this).parents('.js_reply_wrap').fadeOut(500);
		
	    return false;
	});	
	
    $(document).on('click','.premium_helptitle span',function(){
	    $(this).parents('.premium_wrap_help').toggleClass('act');
	    return false;
	});

    $('.pn_datepicker').datepicker({ 
		maxDate: "+1Y",
		dateFormat: 'dd.mm.yy',
		changeYear: true
    });	
	
    $('.pn_timepicker').datetimepicker({ 
		maxDate: "+1Y",
		changeYear: true,
		dateFormat: 'dd.mm.yy',
		timeFormat: 'hh:mm',
		separator: ' '
    });

	function getCaret(el) { 
		if (el.selectionStart) { 
			return el.selectionStart; 
		} else if (document.selection) { 
			el.focus(); 
	 
			var r = document.selection.createRange(); 
			if (r == null) { 
				return 0; 
			} 
	 
			var re = el.createTextRange(), 
				rc = re.duplicate(); 
			re.moveToBookmark(r.getBookmark()); 
			rc.setEndPoint('EndToStart', re); 
	 
			return rc.text.length; 
		}  
		return 0; 
	}
	
	function setSelectionRange(input, selectionStart, selectionEnd) {
		if (input.setSelectionRange) {
			input.focus();
			input.setSelectionRange(selectionStart, selectionEnd);
		}
		else if (input.createTextRange) {
			var range = input.createTextRange();
			range.collapse(true);
			range.moveEnd('character', selectionEnd);
			range.moveStart('character', selectionStart);
			range.select();
		}
	}		

	$(document).on('click', '.prem_tagtext span',function(){
	    var thet = $(this).parents('.premium_wrap_standart').find('textarea');
		var shortcode = $(this).find('.prem_span_hide').html();

		var sectionid = parseInt(getCaret(thet.get(0)));
		thet.val(thet.val().substr(0, sectionid) + shortcode + thet.val().substr(sectionid, thet.val().length));
		setSelectionRange(thet.get(0),sectionid + shortcode.length, sectionid + shortcode.length);
		
		return false;
	});	

	$(document).on('keydown', '.premium_content .wp-list-table input', function( e ){
		if(e.which == 13){
			$(this).parents('form').find('input[name=save]').click();
			return false;
		}
	});		
	
});