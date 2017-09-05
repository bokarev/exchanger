jQuery(function($){
		
/* topped */
	$(window).scroll(function(){
		var npos = $(window).scrollTop();
		if(npos > 200){
			$('#topped').show();
		} else {
			$('#topped').hide();
		}
	});	
	
	$('#topped').click(function(){
		$('body,html').animate({scrollTop : 0},500);		
		return false;	
	});
/* end topped */

/* lang */
	$('.langlist_title').click(function(){
		$('.langlist_ul').show();
		
		return false;
	});
	
    $(document).click(function(event) {
        if ($(event.target).closest(".langlist_ul").length) return;

		$('.langlist_ul').hide();
		
        event.stopPropagation();
    });		
/* end lang */

	function top_menu(){
		if($('#fix_div').length > 0){
			var npos = $(window).scrollTop();
			var hei = 0;
			if($('#wpadminbar').length > 0){
				hei = parseInt($('#wpadminbar').height());
			}
			var one = parseInt($('#fix_div').offset().top) - hei;
			var wid = $(window).width();
			if(wid >= 320){
				if(npos > one){
					$('#fix_elem').css({'position': 'fixed', 'top': hei});
				} else {
					$('#fix_elem').css({'position':'absolute', 'top': '0px'});
				}
			} else {
				$('#fix_elem').css({'position':'absolute', 'top': '0px'});
			}
		}
	}

	$(window).on('scroll', function(){
	    top_menu();	
	});
	$(window).on('resize', function(){
		top_menu();
	});
	$(window).on('load', function(){
		top_menu();
	});	

	$('.js_menu li').hover(function(){
	    $(this).find('ul:first').show('drop');
	}, function(){
	    $(this).find('ul:first').stop(true,true).hide('drop');
	});	
	
	$('.js_menu li a').on('click', function(){
		var href = $(this).attr('href');
		if(href == '#'){
			return false;
		}
	});
	
	$('.js_menu').each(function(){
	    $(this).find('ul').find('li:first').before('<div class="ugmenu"></div>');
	});	

	$('table').each(function(){
	    $(this).find('th:first').addClass('th1');
		$(this).find('th:last').addClass('th2');
	    $(this).find('tr:last').find('td:first').addClass('td1');
		$(this).find('tr:last').find('td:last').addClass('td2');	
	});	

	$(document).on('click', '.checkbox', function(){
	    if($(this).hasClass('act')){
		    $(this).removeClass('act');
			$(this).find('input').prop('checked', false);
		} else {
		    $(this).addClass('act');
			$(this).find('input').prop('checked', true);
		}
		$(this).find('input').trigger('change');
	});	
	
/* social link */
	$('.social_link').on('click', function(){
		var link_url = $(this).attr('href');
		window.open(link_url,'','menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');
		
		return false;
	});
/* end social link */	
	
/* help exchange */	
	$(document).on('mouseenter', '.js_window_wrap',function(){
		$(this).addClass('showed');
	});	
	$(document).on('mouseleave', '.js_window_wrap',function(){
		$(this).removeClass('showed');
	});	
/* end help exchange */

/* tooltips */
	$(document).on('focusin', '.has_tooltip input, .has_tooltip textarea',function(){
		$(this).parents('.has_tooltip').addClass('showed');
	});
	$(document).on('click', '.field_tooltip_label',function(){
		$(this).parents('.has_tooltip').addClass('showed');
	});
	$(document).on('focusout', '.has_tooltip input, .has_tooltip textarea',function(){
		$(this).parents('.has_tooltip').removeClass('showed');
	});	
/* end tooltips */

/* widget reserv */
	$('.widget_reserv_filter').on('click', function(){
		$('.widget_reserv_filter').removeClass('current');
		$(this).addClass('current');
		var id = $(this).attr('data-id');
		$('.widget_reserv_vt').hide();
		$('.widget_reserv_vt_'+id).show();
		
		return false;
	});
/* end widget reserv */	
	
	$(document).Jselect();
});