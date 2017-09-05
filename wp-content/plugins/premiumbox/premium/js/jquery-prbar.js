/*
version: 0.1
*/
(function($) {
    var defaults = { 
		trigger: '',
		start_title: 'we determine the number of requests...',
		end_title: 'number of requests defined',
		success_class: 'deactive',
		line_text: 'start %now% of %max% step',
		end_progress: 'action is completed',
		line_success: 'step %now% is success',
		success: function(){ }
	};
    var options;
 
    $.fn.PrBar = function(params){
        options = $.extend({}, defaults, options, params);
        var thet = $(this);
 
		var trigger = options['trigger'];
		var start_title = options['start_title'];
		var end_title = options['end_title'];
		var line_text = options['line_text'];
		var end_progress = options['end_progress'];
		var line_success = options['line_success'];
		var end_func = options['success'];
		var c_url = '';
		var r_url = '';
		var r_title = '';
		var count_request = 0;
		var count_user = 0;
		var max_request = 0;
		var now_obj = '';
		
		function create_log(log_text, log_class=''){ 
			var date = new Date();
			var now = date.getHours()+':'+date.getMinutes()+':'+date.getSeconds();
			$('.prbar_log').after('<div class="prbar_line '+ log_class +'">['+ now +'] '+ log_text +'</div>');
		}
		
		$(document).on('click', trigger, function(){
			now_obj = $(this);
			var title = $.trim($(this).attr('data-title'));
			$('.prbar_title').html(title);
			$('.premium_shadow, .prbar_wrap').show();
			$('.prbar_log_wrap .prbar_line').remove();
			$('.prbar_ind, .prbar_num').hide();
			$('.prbar_ind_text').html('0%');
			$('.prbar_ind_abs').css({'width':'0px'});
			$('.prbar_num_count').html('0');
			$('.prbar_control').hide();
			$('.prbar_close, .prbar_submit').addClass('deactive');
			
			c_url = $.trim($(this).attr('data-count-url'));
			
			if(c_url){
				
				create_log(start_title);
				
				var param='action=progressbar';
				$.ajax({
					type: "POST",
					url: c_url,
					dataType: 'json',
					data: param,
					error: function(res, res2, res3){
						$('.prbar_close').removeClass('deactive');
						create_log('error:'+ res2, 'color_red');
						for (key in res) {
							console.log(key + ' = ' + res[key]);
						}						
					},			
					success: function(res)
					{
						$('.prbar_close').removeClass('deactive');
						
						if(res['status'] == 'error'){
							create_log(res['status_text'], 'color_red');
						}
						if(res['status'] == 'success'){
							create_log(end_title, 'color_green');
							count_request = parseInt(res['count']);
							r_title = res['status_text'];
							$('.prbar_num_count').val(count_request);
							$('.prbar_num').show();
							$('.prbar_control').show();
							r_url = res['link'];
							$('.prbar_submit').removeClass('deactive');
						}						
					}
				});				
				
			} else {
				$('.prbar_close').removeClass('deactive');
				create_log('error data-count-url', 'color_red');
			}
			
			return false;
		});
		
		function set_progress(now){
			var one_pers = max_request / 100;
			var now_pers = Math.ceil(now / one_pers);
			$('.prbar_ind').show();
			$('.prbar_ind_text').html(now_pers+'%');
			var wid_ind = Math.ceil(now_pers * $('.prbar_ind').width() / 100);
			$('.prbar_ind_abs').css({'width': wid_ind+'px'});
		}
		
		function get_results_for(ind){
			if(max_request >= ind){
				
				var now_line_text = line_text.replace('%now%',ind).replace('%max%',max_request).replace('%text%', r_title);
				create_log(now_line_text);
				
				var param='limit='+count_user+'&idspage='+ind;
				$.ajax({
					type: "POST",
					url: r_url,
					dataType: 'json',
					data: param,
					error: function(res, res2, res3){
						$('.prbar_close').removeClass('deactive');
						create_log('error:'+ res2, 'color_red');
						for (key in res) {
							console.log(key + ' = ' + res[key]);
						}						
					},			
					success: function(res)
					{
						if(res['status'] == 'error'){
							create_log(res['status_text'], 'color_red');
						}
						if(res['status'] == 'success'){
							var now_line_success = line_success.replace('%now%',ind).replace('%text%',res['status_text']);
							create_log(now_line_success, 'color_green');
						}	
						
						set_progress(ind);
						ind = ind+1;
						get_results_for(ind);					
					}
				});				
				
			} else {
				$('.prbar_close, .prbar_submit').removeClass('deactive');				
				create_log(end_progress, 'color_green');
				end_func.apply(null, [now_obj]);
			}
		}
		
		$('.prbar_submit').on('click', function(){
			$('.prbar_close, .prbar_submit').addClass('deactive');
			
			count_user = parseInt($('.prbar_count').val());
			if(count_user < 1){ count_user = 1; }
			count_request = parseInt($.trim($('.prbar_num_count').val()));
			if(count_request < 1){ count_request = 1; }
			max_request = Math.ceil(count_request / count_user);
			
			get_results_for(1);				
			
			return false;
		});		
		
		$('.prbar_close:not(.deactive)').on('click', function(){
			$('.premium_shadow, .prbar_wrap').hide();
			return false;
		});		

 
        return this;
    };
})(jQuery);