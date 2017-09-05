jQuery(function($) {	

    function inex_checknumbr(mixed_var) {
	    return ( mixed_var == '' ) ? false : !isNaN( mixed_var );
    }
	
	function inex_dej(par){
		var tarid = par.find('.depositchangetar').val();
		var min = parseFloat($('.tars_'+tarid).find('.the_minsum').val());
		var maxi = parseFloat($('.tars_'+tarid).find('.the_maxsum').val());
		if(min === 0){
			par.find('.change_min_sum_line').hide();
		} else {
			par.find('.change_min_sum_line').show();
		}		
		if(maxi === 0){
			par.find('.change_max_sum_line').hide();
		} else {
			par.find('.change_max_sum_line').show();
		}
		par.find('.change_min_sum').html(min);
		par.find('.change_max_sum').html(maxi);
		
		var sum = par.find('.depositchangesumm').val();
		if(inex_checknumbr(sum) && sum > 0){
			var pers = parseFloat($('.tars_'+tarid).find('.the_pers').val());
			par.find('.changepers').html(pers);
			if(sum >= min){
				if(sum <= maxi || maxi == 0){
					var profit = (sum / 100 * pers).toFixed(2);
					par.find('.changedohod').html(profit);
					par.find('.depositchangesumm').removeClass('error');
				} else {
					par.find('.depositchangesumm').addClass('error');
				}				
			} else {
				par.find('.depositchangesumm').addClass('error');
			}
		} else {
			par.find('.depositchangesumm').addClass('error');
		}
	}
	
	$('.depositkow').on('keyup', function(){
		$(this).removeClass('error');
	});
	$('.depositkow').on('change', function(){
		$(this).removeClass('error');
	});	
    $('.depositchangesumm').on('keyup', function(){
	    inex_dej($(this).parents('.onesystem'));
    });

    $('.depositchangesumm').on('change', function(){
	    inex_dej($(this).parents('.onesystem'));
    });	
	
	$('.depositchangetar').on('change', function(){
		inex_dej($(this).parents('.onesystem'));
	});
	
	$('.goinvest').on('click', function(){
		var par = $(this).parents('.onesystem');
		var tarid = par.find('.depositchangetar').val();
		var min = parseFloat($('.tars_'+tarid).find('.the_minsum').val());
		var maxi = parseFloat($('.tars_'+tarid).find('.the_maxsum').val());
		var sum = par.find('.depositchangesumm').val();
		if(inex_checknumbr(sum) && sum >= min){
			if(sum <= maxi || maxi == 0){
				
				var account = par.find('.depositkow').val();
				if(account.length < 4){
					par.find('.depositkow').addClass('error');
					return false;
				}
			
			} else {
				par.find('.depositchangesumm').addClass('error');
				return false;
			}			
		} else {
			par.find('.depositchangesumm').addClass('error');
			return false;
		}
	});
	
	$('.onesystem').eq(0).find('.onesystembody').show();
	
	$('.onesystemtitle').on('click', function(){
		$(this).parents('.onesystem').find('.onesystembody').slideToggle(200);
		return false;
	});
	
	$('.hasinfo').on('click', function(){
		var id = $(this).attr('href').replace('#','');
		$('.thedep_'+id).toggle();
		
		return false;
	});
	
});