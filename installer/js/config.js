jQuery(function($){
	
    $('.ajax_post_form').ajaxForm({
        dataType:  'json',
		beforeSubmit: function(a,f,o) {
			f.addClass('thisactive');
			$('.thisactive input[type=submit]').attr('disabled',true);
        },
        success: function(ht) {
            if(ht['otv']){
				alert(ht['text']);
			} 
				
		    $('.thisactive input[type=submit]').attr('disabled',false);
			$('.thisactive').removeClass('thisactive');
        }
    });
	
		function str_rand() {
			var result       = '';
			var words        = '0123456789qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM';
			var max_position = words.length - 1;
			for( i = 0; i < 16; ++i ) {
				var position = Math.floor ( Math.random() * max_position );
				result = result + words.substring(position, position + 1);
			}
			return result;
		}

		function check_pass(){
			
			var password = $('#adm_new_password').val();
			var s_letters = "qwertyuiopasdfghjklzxcvbnm"; // Буквы в нижнем регистре
			var b_letters = "QWERTYUIOPLKJHGFDSAZXCVBNM"; // Буквы в верхнем регистре
			var digits = "0123456789"; // Цифры
			var specials = "!@#$%^&*()_-+=\|/.,:;[]{}"; // Спецсимволы
			var is_s = false; // Есть ли в пароле буквы в нижнем регистре
			var is_b = false; // Есть ли в пароле буквы в верхнем регистре
			var is_d = false; // Есть ли в пароле цифры
			var is_sp = false; // Есть ли в пароле спецсимволы
			for (var i = 0; i < password.length; i++) {
			/* Проверяем каждый символ пароля на принадлежность к тому или иному типу */
				if (!is_s && s_letters.indexOf(password[i]) != -1) is_s = true;
				else if (!is_b && b_letters.indexOf(password[i]) != -1) is_b = true;
				else if (!is_d && digits.indexOf(password[i]) != -1) is_d = true;
				else if (!is_sp && specials.indexOf(password[i]) != -1) is_sp = true;
			}
			var rating = 0;
			if (is_s) rating++; // Если в пароле есть символы в нижнем регистре, то увеличиваем рейтинг сложности
			if (is_b) rating++; // Если в пароле есть символы в верхнем регистре, то увеличиваем рейтинг сложности
			if (is_d) rating++; // Если в пароле есть цифры, то увеличиваем рейтинг сложности
			if (is_sp) rating++; // Если в пароле есть спецсимволы, то увеличиваем рейтинг сложности
			/* Далее идёт анализ длины пароля и полученного рейтинга, и на основании этого готовится текстовое описание сложности пароля */
			if (password.length < 1){
				$('#adm_new_password').removeClass('orange').removeClass('green').addClass('red');				
			} else if (password.length < 8 && rating < 3){
				$('#adm_new_password').removeClass('orange').removeClass('green').addClass('red');
			} else if (password.length >= 8 && rating < 3){
				$('#adm_new_password').removeClass('red').removeClass('green').addClass('orange');				
			} else if (password.length >= 8 && rating >= 3){
				$('#adm_new_password').removeClass('red').removeClass('green').addClass('green');
			}
			
		}
		
		$('#adm_generate_pass').click(function(){
			$('#adm_new_password').attr('value',str_rand());
			check_pass();
			return false;
		});
		
		$('#adm_new_password').live('change', function(){
			check_pass();
		});
		
		$('#adm_new_password').live('keyup', function(){
			check_pass();
		});	

		if($('#adm_new_password').length > 0){
			check_pass();
		}
	
});