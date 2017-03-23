var highest = -999;

$( document ).ready(function() {	
	// corrigir o erro da scrollbar qdo minimiza menu
	$('.sidebar-panel .brand a.toggle-sidebar').on("click", function(){
		var menu_esta_minimizado;
		var div_parent = $(this).parent().parent().parent();
		var div_scrollbar = $(this).parent().parent().find('nav');
		
		// console.log(div_parent);
		// console.log(div_scrollbar);
		
		if(div_parent.hasClass("layout-small-menu")){
			menu_esta_minimizado = true;
		}
		else{
			menu_esta_minimizado = false;
		}
		
		if( !menu_esta_minimizado ){
			div_scrollbar.perfectScrollbar();
		}
	});
	// ----------------------------------------------
});

//Numeric only control handler
jQuery.fn.ForceNumericOnly =
function()
{
    return this.each(function()
    {
        $(this).keydown(function(e)
        {
            var key = e.charCode || e.keyCode || 0;
            // allow backspace, tab, delete, enter, arrows, numbers and keypad numbers ONLY
            // home, end, period, and numpad decimal
            return (
                key == 8 || 
                key == 9 ||
                key == 13 ||
                key == 46 ||
                key == 110 ||
                key == 190 ||
                (key >= 35 && key <= 40) ||
                (key >= 48 && key <= 57) ||
                (key >= 96 && key <= 105));
        });
    });
};

$("a.mvc-ajax").on('click', function(){
	var controller = $(this).attr("data-controller");
	var action = $(this).attr("data-action");
	var target = $(this).attr("data-target");
	var vars = "";
	
	ajaxResposta(controller, action, vars, target);
});

(function($) {
    $.fn.toggleDisabled = function(){
        return this.each(function(){
            this.disabled = !this.disabled;
        });
    };
})(jQuery);

function getMaxZIndex(){
	$("*").each(function() {
	    var current = parseInt($(this).css("z-index"), 10);
	    if(current && highest < current) highest = current;
	});
	
	return highest;
}

function getUrlHome(){
	return "http://app.megafinancas.com.br";
}

var Base64 = {

    // private property
    _keyStr : "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",

    // public method for encoding
    encode : function (input) {
        var output = "";
        var chr1, chr2, chr3, enc1, enc2, enc3, enc4;
        var i = 0;

        input = Base64._utf8_encode(input);

        while (i < input.length) {

            chr1 = input.charCodeAt(i++);
            chr2 = input.charCodeAt(i++);
            chr3 = input.charCodeAt(i++);

            enc1 = chr1 >> 2;
            enc2 = ((chr1 & 3) << 4) | (chr2 >> 4);
            enc3 = ((chr2 & 15) << 2) | (chr3 >> 6);
            enc4 = chr3 & 63;

            if (isNaN(chr2)) {
                enc3 = enc4 = 64;
            } else if (isNaN(chr3)) {
                enc4 = 64;
            }

            output = output +
            this._keyStr.charAt(enc1) + this._keyStr.charAt(enc2) +
            this._keyStr.charAt(enc3) + this._keyStr.charAt(enc4);

        }

        return output;
    },

    // public method for decoding
    decode : function (input) {
        var output = "";
        var chr1, chr2, chr3;
        var enc1, enc2, enc3, enc4;
        var i = 0;

        input = input.replace(/[^A-Za-z0-9\+\/\=]/g, "");

        while (i < input.length) {

            enc1 = this._keyStr.indexOf(input.charAt(i++));
            enc2 = this._keyStr.indexOf(input.charAt(i++));
            enc3 = this._keyStr.indexOf(input.charAt(i++));
            enc4 = this._keyStr.indexOf(input.charAt(i++));

            chr1 = (enc1 << 2) | (enc2 >> 4);
            chr2 = ((enc2 & 15) << 4) | (enc3 >> 2);
            chr3 = ((enc3 & 3) << 6) | enc4;

            output = output + String.fromCharCode(chr1);

            if (enc3 != 64) {
                output = output + String.fromCharCode(chr2);
            }
            if (enc4 != 64) {
                output = output + String.fromCharCode(chr3);
            }

        }

        output = Base64._utf8_decode(output);

        return output;

    },

    // private method for UTF-8 encoding
    _utf8_encode : function (string) {
        string = string.replace(/\r\n/g,"\n");
        var utftext = "";

        for (var n = 0; n < string.length; n++) {

            var c = string.charCodeAt(n);

            if (c < 128) {
                utftext += String.fromCharCode(c);
            }
            else if((c > 127) && (c < 2048)) {
                utftext += String.fromCharCode((c >> 6) | 192);
                utftext += String.fromCharCode((c & 63) | 128);
            }
            else {
                utftext += String.fromCharCode((c >> 12) | 224);
                utftext += String.fromCharCode(((c >> 6) & 63) | 128);
                utftext += String.fromCharCode((c & 63) | 128);
            }

        }

        return utftext;
    },

    // private method for UTF-8 decoding
    _utf8_decode : function (utftext) {
        var string = "";
        var i = 0;
        var c = c1 = c2 = 0;

        while ( i < utftext.length ) {

            c = utftext.charCodeAt(i);

            if (c < 128) {
                string += String.fromCharCode(c);
                i++;
            }
            else if((c > 191) && (c < 224)) {
                c2 = utftext.charCodeAt(i+1);
                string += String.fromCharCode(((c & 31) << 6) | (c2 & 63));
                i += 2;
            }
            else {
                c2 = utftext.charCodeAt(i+1);
                c3 = utftext.charCodeAt(i+2);
                string += String.fromCharCode(((c & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
                i += 3;
            }

        }

        return string;
    }

}

function ajaxModal(controller, action, vars){
	$.ajax({
		type : "POST",
		url : getUrlHome() + '/'+controller+'/'+action,
		data : vars,
		beforeSend: function(request){
			showCarregando();
		},
		complete: function(){
			closeCarregando();
		},
		success : function(response) {
			openRemodal(response);
		}
	});
}

function ajaxResposta(controller, action, vars, target){
	$.ajax({
		type : "POST",
		url : getUrlHome() + '/'+controller+'/'+action,
		data : vars,
		beforeSend: function(request){
			showCarregando();
		},
		complete: function(){
			closeCarregando();
		},
		success : function(response) {
			$(target).html(response);
			setTimeout( "initPlugins();", 250 );
		}
	});
}

function openRemodal(html){
	$("body div#data-remodal").remove();
	$("body").append("<div class='remodal' id='data-remodal'><button data-remodal-action='close' class='remodal-close' aria-label='Close'></button>"+html+"</div>");
	setTimeout( "var v_modal = $('#data-remodal').remodal(); v_modal.open();", 150 );
	setTimeout( "initPlugins();", 250 );
}

function closeRemodal(){
	$('.remodal-overlay').remove();
	$('.remodal-wrapper').remove();
}

function enviaEmailRegister(email){
	$.ajax({
		type : "POST",
		url : getUrlHome() + '/Usuario/enviaEmailRegister',
		data : 'e=' + Base64.encode(email),
		beforeSend: function(request){
			request.setRequestHeader("Mf-Skip-Session-Validation", "true");
		},
		success : function(response) {
			if(response == "true"){
				$("div.sweet-alert .register-p-error").html("Cadastro efetuado com sucesso! Acesse seu email e confirme o cadastro para começar a usar o Mega Finanças.").show();
				setTimeout(" $('div.sweet-alert .register-p-error').hide() ", 12000);
				return false;
			}
			else{
				$("div.sweet-alert .register-p-error").html("Cadastro efetuado com sucesso! Clique <a href=\"javascript:enviaEmailRegister('"+email+"');\">aqui</a> para receber o email de confirmação.").show();
				setTimeout(" $('div.sweet-alert .register-p-error').hide() ", 30000);
				return false;
			}
		}
	});
}

function showMessage(v_title, v_msg, v_type){
	setTimeout("swal({title: '"+v_title+"', text: '"+v_msg+"', type: '"+v_type+"', html: true,});", 200);
}

function showSuccess(v_title, v_msg){
	showMessage(v_title, v_msg, "success");
}

function showError(v_title, v_msg){
	showMessage(v_title, v_msg, "error");
}

function showAlert(v_title, v_msg){
	showMessage(v_title, v_msg, "info");
}

function showCarregando(){
	swal({
		title: "Carregando",
		text: "<center><img width='48' heigth='48' src='" + getUrlHome() + "/html/images/loader.gif' /></center>",
		html: true,
		type: "info",
		customClass: "swal-sem-btn-confirm",
	});
}

function closeCarregando(){
	swal.close();
}

function initPlugins(){
	// only integer
	$(".inpt-only-int").ForceNumericOnly();
	// ============
	
	// auto numeric
	$( ".inpt-auto-numeric" ).each(function() {
		var v_aSign = "R$ "; // displays the desired currency symbol
		var v_pSign = "p"; // controls the placement of the currency symbol (p, s)
		var v_vMin = "-9999999999999.99"; // controls the minimum value allowed
		var v_vMax = "9999999999999.99"; // controls the maximum value allowed
		var v_wEmpty = "empty"; // controls input display behavior (empty, zero, sign)
		
		var value = "";
		var tem_prop = false;
		var prop_name = "";
		
		prop_name = "data-an-asign";
		tem_prop = $(this).hasOwnProperty(prop_name);
		if(tem_prop){
			value = $(this).attr(prop_name);
			if(value != ""){
				v_aSign = value;
			}
		}
		
		prop_name = "data-an-psign";
		tem_prop = $(this).hasOwnProperty(prop_name);
		if(tem_prop){
			value = $(this).attr(prop_name);
			if(value != ""){
				v_pSign = value;
			}
		}
		
		prop_name = "data-an-vmin";
		tem_prop = $(this).hasOwnProperty(prop_name);
		if(tem_prop){
			value = $(this).attr(prop_name);
			if(value != ""){
				v_vMin = value;
			}
		}
		
		prop_name = "data-an-vmax";
		tem_prop = $(this).hasOwnProperty(prop_name);
		if(tem_prop){
			value = $(this).attr(prop_name);
			if(value != ""){
				v_vMax = value;
			}
		}
		
		prop_name = "data-an-wempty";
		tem_prop = $(this).hasOwnProperty(prop_name);
		if(tem_prop){
			value = $(this).attr(prop_name);
			if(value != ""){
				v_wEmpty = value;
			}
		}
		
		$( this ).autoNumeric('init', {
			aSep: '.',
			aDec: ',',
			aSign: v_aSign,
			pSign: v_pSign,
			vMin: v_vMin,
			vMax: v_vMax,
			wEmpty: v_wEmpty,
		});
	});
	// ============
	
	// color picker
	$('.inpt-color-picker').colorpicker({
		align: 'right',
		format: 'hex',
	});
	$('.dv-color-picker').colorpicker({
		align: 'right',
		format: 'hex',
	});
	
	$('.inpt-color-picker, .dv-color-picker').colorpicker().on('showPicker.colorpicker', function(event){
		var maxZIndex = getMaxZIndex();
		maxZIndex += 1;
		
		$('.colorpicker').css({'z-index':maxZIndex, 'width':'131px'});
	});
	$('.inpt-color-picker, .dv-color-picker').colorpicker().on('changeColor.colorpicker', function(event){
		// $(this).css({'boder-color':event.color.toHex()});
		// console.log(event.color.toHex());
	});
	// ============
	
	// nestable
	$('.obj-nestable').nestable({group:1});
	// ========
	
	// scroll
	$('.scrollable').perfectScrollbar({
      wheelPropagation: true,
      suppressScrollX: true,
      includePadding: true
    });
	// ======
	
	// date picker
    $('.inpt-date-picker').datepicker({
        format: "dd/mm/yyyy",
        todayBtn: true,
        language: "pt-BR",
        autoclose: true
    });
	// ===========
    
    // tooltip e popover
    $("[data-toggle=tooltip]").tooltip();

    $("[data-toggle=popover]")
      .popover()
      .click(function (e) {
        e.preventDefault();
    });
    // =================
}

// processo de register new user ===================
function validaRegister(email, senha, terms){
	$.ajax({
		type : "POST",
		url : getUrlHome() + '/Usuario/validaRegister',
		data : 'e=' + Base64.encode(email) + '&s=' + Base64.encode(senha),
		beforeSend: function(request){
			request.setRequestHeader("Mf-Skip-Session-Validation", "true");
		},
		success : function(response) {
			// a resposta vem como JSON: ret_email | ret_senha
			// remove o loading
			$("div.sweet-alert .register-p-error").html("").hide();
			$(".sa-button-container").show();
			// ----------------
			
			var obj_resp = $.parseJSON(response);
			if(jQuery.isEmptyObject(obj_resp)){
				$("div.sweet-alert .register-p-error").html("Ocorreu um problema na sua requisi&ccedil;&atilde;o. Tente novamente em breve.").show();
				setTimeout(" $('div.sweet-alert .register-p-error').hide() ", 4000);
				return false;
			}
			else if(obj_resp.ret_email != ""){
				$("div.sweet-alert .register-p-error").html(obj_resp.ret_email).show();
				setTimeout(" $('div.sweet-alert .register-p-error').hide() ", 4000);
				return false;
			}
			else if(obj_resp.ret_senha != ""){
				$("div.sweet-alert .register-p-error").html(obj_resp.ret_senha).show();
				setTimeout(" $('div.sweet-alert .register-p-error').hide('slow') ", 4000);
				return false;
			}
			else{
				if(!terms){
					$("div.sweet-alert .register-p-error").html("Voc&ecirc; precisa aceitar os termos de uso.").show();
					setTimeout(" $('div.sweet-alert .register-p-error').hide('slow') ", 4000);
					return false;
				}
				else{
					prossegueRegister(email, senha);
				}
			}
		}
	});
}

function prossegueRegister(email, senha){
	$.ajax({
		type : "POST",
		url : getUrlHome() + '/Usuario/prossegueRegister',
		data : 'e=' + Base64.encode(email) + '&s=' + Base64.encode(senha),
		beforeSend: function(request){
			request.setRequestHeader("Mf-Skip-Session-Validation", "true");
		},
		success : function(response) {
			// a resposta vem JSON: status | msg
			var obj_resp = $.parseJSON(response);
			// console.log(obj_resp);
			
			if(jQuery.isEmptyObject(obj_resp)){
				$("div.sweet-alert .register-p-error").html("Ocorreu um problema na sua requisi&ccedil;&atilde;o. Tente novamente em breve.").show();
				setTimeout(" $('div.sweet-alert .register-p-error').hide() ", 4000);
				return false;
			}
			else if(obj_resp.status == "0"){ // erro
				$("div.sweet-alert .register-p-error").html(obj_resp.msg).show();
				setTimeout(" $('div.sweet-alert .register-p-error').hide() ", 4000);
				return false;
			}
			else{ // ok
				enviaEmailRegister(email);
			}
		}
	});
}

function forgetPassword(email){
	$.ajax({
		type : "POST",
		url : getUrlHome() + '/home/postForgetPassword',
		data : 'e=' + Base64.encode(email),
		beforeSend: function(request){
			request.setRequestHeader("Mf-Skip-Session-Validation", "true");
		},
		success : function(response) {
			var arr_resp = response.split("#@#");
			var resp = arr_resp[0];
			var msg = arr_resp[1];
			
			if(resp == "OK"){
				// coloca o loading
		    	$("div.sweet-alert .register-p-error").html("<center>"+msg+"</center>").show();
		    	$(".sa-button-container").show();
		    	// ----------------
			}
			else if(resp == "ERRO"){
				// coloca o loading
		    	$("div.sweet-alert .register-p-error").html("<center>"+msg+"</center>").show();
		    	$(".sa-button-container").show();
		    	// ----------------
			}
			else{
				showAlert("Erro ao recuperar senha", "O servidor não conseguiu buscar as informações para recuperar a senha. Tente novamente em alguns minutos.");
			}
		}
	});
}

$("a#login-register-button").on('click', function(){
	$.ajax({
		type : "POST",
		url : getUrlHome() + '/home/loginTelaRegister',
		data : '',
		beforeSend: function(request){
			request.setRequestHeader("Mf-Skip-Session-Validation", "true");
			showCarregando();
		},
		success : function(response) {
			swal({
			    title: "Cadastro",
			    text: response,
			    html: true,
			    allowEscapeKey: false,
			    showCancelButton: true,
			    cancelButtonText: "Fechar",
			    confirmButtonColor: "#3B7F98",
			    confirmButtonText: "Cadastrar",
			    closeOnConfirm: false,
			    closeOnCancel: false
			}, function(isConfirm) {
			    if (isConfirm) {
			    	// coloca o loading
			    	$("div.sweet-alert .register-p-error").html("<center><img width='24' heigth='24' src='" + getUrlHome() + "/html/images/loader.gif' /></center>").show();
			    	$(".sa-button-container").hide();
			    	// ----------------
			    	
			    	var email = $("div.sweet-alert #register-mail").val();
			    	var senha = $("div.sweet-alert #register-password").val();
			    	var terms = $("div.sweet-alert #register-terms").prop("checked");
			    	
			    	validaRegister(email, senha, terms);
			    } else {
			    	swal.close();
			    }
			});
		}
	});
	return;
});

$("a#btn-login-fcbk").on('click', function(){
	FB.login(function(response) {
        if (response.authResponse) {
            // User authorized app
        	FB.api('/me', {fields: 'id,name,first_name,last_name,picture,email,gender'}, function(objResponse) {
        		// console.log(objResponse);
        		
        		var fcbk_id = Base64.encode(objResponse.id);
        		var fcbk_name = Base64.encode(objResponse.name);
        		var fcbk_first_name = Base64.encode(objResponse.first_name);
        		var fcbk_last_name = Base64.encode(objResponse.last_name);
        		var fcbk_email = Base64.encode(objResponse.email);
        		var fcbk_gender = Base64.encode(objResponse.gender);
        		var fcbk_picture = Base64.encode(objResponse.picture.data.url);
        		
        		$.ajax({
					type : "POST",
					url : getUrlHome() + '/home/loginFcbk',
					data : 'i='+fcbk_id+'&n='+fcbk_name+'&f='+fcbk_first_name+'&l='+fcbk_last_name+'&e='+fcbk_email+'&g=' + fcbk_gender + '&p=' + fcbk_picture,
					beforeSend: function(request){
						request.setRequestHeader("Mf-Skip-Session-Validation", "true");
						showCarregando();
					},
					complete: function(){
						closeCarregando();
					},
					success : function(response) {
						var arr_resp = response.split("#@#");
						var resp = arr_resp[0];
						var msg = arr_resp[1];
						
						if(resp == "OK"){
							$("#loginForm #hddn-fcbk").val(msg);
							$("#loginForm").submit();
						}
						else if(resp == "ERRO"){
							showAlert("Erro ao fazer login", msg);
						}
						else{
							showAlert("Erro ao fazer login", "O servidor não conseguiu buscar as informações do Facebook. Tente novamente em alguns minutos.");
						}
					}
				});
        	});
        } else {
            // User cancelled login or did not fully authorize
        }
    }, {
    	scope: 'email,public_profile',
    	return_scopes: true
    });
});

$("a#btn-login-twttr").on('click', function(){
	$.ajax({
		type : "POST",
		url : getUrlHome() + '/home/loginTwttr',
		beforeSend: function(request){
			request.setRequestHeader("Mf-Skip-Session-Validation", "true");
			showCarregando();
		},
		complete: function(){
			closeCarregando();
		},
		success : function(response) {
			var arr_resp = response.split("#@#");
			var resp = arr_resp[0];
			var msg = arr_resp[1];
			
			if(resp == "REDIRECT"){
				document.location.href = msg;
			}
			else{
				showAlert("Login com o Twitter", "Erro ao fazer login com o Twitter. Tente novamente em breve.");
			}
		}
	});
});

$("a#forget-password-button").on('click', function(){
	$.ajax({
		type : "POST",
		url : getUrlHome() + '/home/forgetPassword',
		data : '',
		beforeSend: function(request){
			request.setRequestHeader("Mf-Skip-Session-Validation", "true");
			showCarregando();
		},
		success : function(response) {
			swal({
			    title: "Recuperar Senha",
			    text: response,
			    html: true,
			    allowEscapeKey: false,
			    showCancelButton: true,
			    cancelButtonText: "Fechar",
			    confirmButtonColor: "#3B7F98",
			    confirmButtonText: "Confirmar",
			    closeOnConfirm: false,
			    closeOnCancel: false
			}, function(isConfirm) {
			    if (isConfirm) {
			    	
			    	// coloca o loading
			    	$("div.sweet-alert .register-p-error").html("<center><img width='24' heigth='24' src='" + getUrlHome() + "/html/images/loader.gif' /></center>").show();
			    	$(".sa-button-container").hide();
			    	// ----------------
			    	
			    	var email = $("div.sweet-alert #forget-mail").val();
			    	forgetPassword(email);
			    } else {
			    	swal.close();
			    }
			});
		}
	});
	return;
});
//=================================================

// Controller Usuario =============================
$('body').on('click','#btn-grava-configs',function(){
	var controller = "Usuario";
	var action = "postConfigs";
	var vars = $("#frm-configs").serialize();
	
	$.ajax({
		type : "POST",
		url : getUrlHome() + '/'+controller+'/'+action,
		data : vars,
		beforeSend: function(request){
			showCarregando();
		},
		complete: function(){
			closeCarregando();
		},
		success : function(response) {
			var arr_resp = response.split("#@#");
			var resp = arr_resp[0];
			var msg = arr_resp[1];
			
			if(resp == "OK"){
				showSuccess("Gravar configurações", msg);
				return;
			}
			else if(resp == "ERRO"){
				showError("Gravar configurações: Erro", msg);
				return;
			}
			else{
				showAlert("Gravar configurações", "Não conseguimos concluir a gravação. Tente novamente em breve.");
			}
		}
	});
});

$('body').on('click','#btn-confirmar-senha',function(){
	var controller = "Usuario";
	var action = "postConfirmarSenha";
	var vars = $("#frm-configs").serialize();
	
	$.ajax({
		type : "POST",
		url : getUrlHome() + '/'+controller+'/'+action,
		data : vars,
		beforeSend: function(request){
			showCarregando();
		},
		complete: function(){
			closeCarregando();
		},
		success : function(response) {
			var arr_resp = response.split("#@#");
			var resp = arr_resp[0];
			var msg = arr_resp[1];
			
			if(resp == "OK"){
				$("#inpt-nova-senha").val('');
				$("#inpt-repetir-nova-senha").val('');
				setTimeout("$('.dv-chng-psswrd').toggle();", 200);
				
				showSuccess("Confirmar senha", msg);
				return;
			}
			else if(resp == "ERRO"){
				showError("Confirmar senha: Erro", msg);
				return;
			}
			else{
				showAlert("Confirmar senha", "Não conseguimos concluir a altera&ccedil;&atilde;o de senha. Tente novamente em breve.");
			}
		}
	});
});
// ================================================

// Controller Conta ===============================
$('body').on('click','#btn-nova-conta',function(){
	var controller = "Conta";
	var action = "incluir";
	var vars = "";
	
	ajaxModal(controller, action, vars);
});

$('body').on('click','#btn-grava-nova-conta, #btn-edita-nova-conta',function(){
	var id_btn = $(this).attr("id");
	var vars = $("#frm-nova-conta").serialize();
	
	if(id_btn == "btn-grava-nova-conta"){
		var controller = "Conta";
		var action = "postIncluir";
		var alert_tit = "Nova Conta";
	}
	else if(id_btn == "btn-edita-nova-conta"){
		var controller = "Conta";
		var action = "postEditar";
		var alert_tit = "Editar Conta";
	}
	
	$.ajax({
		type : "POST",
		url : getUrlHome() + '/'+controller+'/'+action,
		data : vars,
		dataType: "json",
		beforeSend: function(request){
			showCarregando();
		},
		complete: function(){
			closeCarregando();
		},
		success : function(response) {
			if(response.retorno == "OK"){
				closeRemodal();
				ajaxResposta('Conta', 'index', '', '.main-content');
				setTimeout("showSuccess('"+alert_tit+"', '"+response.msg+"');", 250);
				return;
			}
			else if(response.retorno == "ERRO"){
				showError(alert_tit + ": Erro", response.msg);
				return;
			}
			else{
				showAlert(alert_tit, "Não conseguimos concluir a requisição. Tente novamente em breve.");
			}
		}
	});
});

$('body').on('click','#lnk-edit-conta',function(){
	var id = $(this).attr("data-id");
	var controller = "Conta";
	var action = "editar";
	var vars = "id=" + id;
	
	ajaxModal(controller, action, vars);
});

$('body').on('click','#lnk-delete-conta',function(){
	var con_id = $(this).attr("data-id");
	
	swal({
	  title: "Deletar Conta",
	  text: "Deseja deletar essa conta? Essa operação não pode ser desfeita.",
	  type: "warning",
	  showCancelButton: true,
	  confirmButtonColor: "#DD6B55",
	  confirmButtonText: "Deletar",
	  cancelButtonText: "cancelar",
	  closeOnConfirm: false
	},
	function(){
		
		var controller = "Conta";
		var action = "postDeletar";
		var vars = "id=" + con_id;
		var msg_title = "Deletar Conta";
		
		$.ajax({
			type : "POST",
			url : getUrlHome() + '/'+controller+'/'+action,
			data : vars,
			dataType: "json",
			beforeSend: function(request){
				showCarregando();
			},
			complete: function(){
				closeCarregando();
			},
			success : function(response) {
				if(response.retorno == "OK"){
					ajaxResposta('Conta', 'index', '', '.main-content');
					showSuccess(msg_title, response.msg);
					return;
				}
				else if(response.retorno == "ERRO"){
					showError(msg_title + ": Erro", response.msg);
					return;
				}
				else{
					showAlert(msg_title, "Não conseguimos concluir a gravação. Tente novamente em breve.");
				}
			}
		});
		
	});
});
// ================================================

// Controller MovimentacaoCat =====================
$('body').on('click','#btn-nova-categoria',function(){
	var controller = "MovimentacaoCat";
	var action = "incluir";
	var vars = "";
	
	ajaxModal(controller, action, vars);
});

$('body').on('click','#lnk-edit-categorias',function(){
	var id = $(this).attr("data-id");
	var controller = "MovimentacaoCat";
	var action = "editar";
	var vars = "id=" + id;
	
	ajaxModal(controller, action, vars);
});

$('body').on('click','#btn-grava-nova-categoria, #btn-edita-nova-categoria',function(){
	var id_btn = $(this).attr("id");
	var vars = $("#frm-nova-categoria").serialize();
	
	if(id_btn == "btn-grava-nova-categoria"){
		var controller = "MovimentacaoCat";
		var action = "postIncluir";
		var alert_tit = "Nova Categoria";
	}
	else if(id_btn == "btn-edita-nova-categoria"){
		var controller = "MovimentacaoCat";
		var action = "postEditar";
		var alert_tit = "Editar Categoria";
	}
	
	$.ajax({
		type : "POST",
		url : getUrlHome() + '/'+controller+'/'+action,
		data : vars,
		dataType: "json",
		beforeSend: function(request){
			showCarregando();
		},
		complete: function(){
			closeCarregando();
		},
		success : function(response) {
			if(response.retorno == "OK"){
				closeRemodal();
				ajaxResposta('MovimentacaoCat', 'index', '', '.main-content');
				setTimeout("showSuccess('"+alert_tit+"', '"+response.msg+"');", 250);
				return;
			}
			else if(response.retorno == "ERRO"){
				showError(alert_tit + ": Erro", response.msg);
				return;
			}
			else{
				showAlert(alert_tit, "Não conseguimos concluir a requisição. Tente novamente em breve.");
			}
		}
	});
});

$('body').on('click','#lnk-delete-categorias',function(){
	var mc_id = $(this).attr("data-id");
	
	swal({
	  title: "Deletar Categoria",
	  text: "Deseja deletar essa categoria? Essa operação não pode ser desfeita.",
	  type: "warning",
	  showCancelButton: true,
	  confirmButtonColor: "#DD6B55",
	  confirmButtonText: "Deletar",
	  cancelButtonText: "cancelar",
	  closeOnConfirm: false
	},
	function(){
		
		var controller = "MovimentacaoCat";
		var action = "postDeletar";
		var vars = "id=" + mc_id;
		var msg_title = "Deletar Categoria";
		
		$.ajax({
			type : "POST",
			url : getUrlHome() + '/'+controller+'/'+action,
			data : vars,
			dataType: "json",
			beforeSend: function(request){
				showCarregando();
			},
			complete: function(){
				closeCarregando();
			},
			success : function(response) {				
				if(response.retorno == "OK"){
					closeRemodal();
					ajaxResposta('MovimentacaoCat', 'index', '', '.main-content');
					setTimeout("showSuccess('"+msg_title+"', '"+response.msg+"');", 250);
					return;
				}
				else if(response.retorno == "ERRO"){
					showError(msg_title + ": Erro", response.msg);
					return;
				}
				else{
					showAlert(msg_title, "Não conseguimos concluir a gravação. Tente novamente em breve.");
				}
			}
		});
		
	});
});

$('body').on('click','#lnk-delete-subcategorias',function(){
	var mc_id = $(this).attr("data-id");
	
	swal({
	  title: "Deletar Subcategoria",
	  text: "Deseja deletar essa subcategoria? Essa operação não pode ser desfeita.",
	  type: "warning",
	  showCancelButton: true,
	  confirmButtonColor: "#DD6B55",
	  confirmButtonText: "Deletar",
	  cancelButtonText: "cancelar",
	  closeOnConfirm: false
	},
	function(){
		
		var controller = "MovimentacaoCat";
		var action = "postDeletarSubCat";
		var vars = "id=" + mc_id;
		var msg_title = "Deletar Subcategoria";
		
		$.ajax({
			type : "POST",
			url : getUrlHome() + '/'+controller+'/'+action,
			data : vars,
			dataType: "json",
			beforeSend: function(request){
				showCarregando();
			},
			complete: function(){
				closeCarregando();
			},
			success : function(response) {				
				if(response.retorno == "OK"){
					$("div#dv-tb-subcat").html(response.html);
					// closeRemodal();
					// ajaxResposta('MovimentacaoCat', 'index', '', '.main-content');
					// setTimeout("showSuccess('"+msg_title+"', '"+response.msg+"');", 250);
					return;
				}
				else if(response.retorno == "ERRO"){
					// showError(msg_title + ": Erro", response.msg);
					return;
				}
				else{
					showAlert(msg_title, "Não conseguimos concluir a gravação. Tente novamente em breve.");
				}
			}
		});
		
	});
});

$('body').on('click','#btn-insere-subcategorias',function(){
	var cat_id = $(this).attr("data-id");
	var subcat_name = $(this).parent().parent().find("#subcat-name").val();
	
	var controller = "MovimentacaoCat";
	var action = "insereSubcategorias";
	var vars = "mc_id=" + cat_id + "&subcat_desc=" + subcat_name;
	
	$.ajax({
		type : "POST",
		url : getUrlHome() + '/'+controller+'/'+action,
		data : vars,
		dataType: "json",
		beforeSend: function(request){
			showCarregando();
		},
		complete: function(){
			closeCarregando();
		},
		success : function(response) {
			if(response.retorno == "OK"){
				$("#subcat-name").val("");
				$("#subcat-name").focus();
				$("div#dv-tb-subcat").html(response.html);
				return;
			}
			else if(response.retorno == "ERRO"){
				showError(alert_tit + ": Erro", response.msg);
				return;
			}
			else{
				showAlert(alert_tit, "Não conseguimos concluir a requisição. Tente novamente em breve.");
			}
		}
	});
});

$('body').on('change','div#dv-hold-mov-cat #mov_cat',function(){
	var id_cat_id = $(this).val();
	var target = $(this).parent().attr("data-spn");
	var nome = $(this).parent().attr("data-nome");
	
	var controller = "MovimentacaoCat";
	var action = "pegaHtmlSubCat";
	var vars = "id_cat_pai="+id_cat_id+"&valor=&nome=" + nome;
	
	ajaxResposta(controller, action, vars, target);
});
// ================================================

// Controller Movimentacao ========================
$('body').on('click','#btn-nova-receita, #lnk-edit-receita, #btn-nova-despesa, #lnk-edit-despesa, #edita-mov-competencia, #btn-nova-transferencia, #lnk-edit-transferencia',function(){
	var id_btn = $(this).attr("id");
	var vars = "a=a";
	
	if(id_btn == "btn-nova-receita"){
		var controller = "Movimentacao";
		var action = "incluir";
		var alert_tit = "Nova Receita";
	}
	else if(id_btn == "lnk-edit-receita"){
		var controller = "Movimentacao";
		var action = "editar";
		var alert_tit = "Editar Receita";
		
		vars += "&id=" + $(this).data("id");
	}
	else if(id_btn == "btn-nova-despesa"){
		var controller = "Movimentacao";
		var action = "incluirDespesa";
		var alert_tit = "Nova Despesa";
	}
	else if(id_btn == "lnk-edit-despesa"){
		var controller = "Movimentacao";
		var action = "editarDespesa";
		var alert_tit = "Editar Despesa";
		
		vars += "&id=" + $(this).data("id");
	}
	else if(id_btn == "edita-mov-competencia"){
		var controller = "Movimentacao";
		var action = "editarCompetencia";
		var alert_tit = "Editar Competência";
		
		vars += "&id=" + $(this).data("id") + "&str_movimentacao=" + $(this).data("str-movimentacao");
	}
	else if(id_btn == "btn-nova-transferencia"){
		var controller = "Movimentacao";
		var action = "incluirTransferencia";
		var alert_tit = "Nova Transferência";
	}
	else if(id_btn == "lnk-edit-transferencia"){
		var controller = "Movimentacao";
		var action = "editarTransferencia";
		var alert_tit = "Editar Transferência";
		
		vars += "&id=" + $(this).data("id");
	}
	
	vars += "&alert_tit=" + alert_tit;
	ajaxModal(controller, action, vars);
});

$('body').on('click','#btn-post-nova-receita, #btn-editar-nova-receita, #btn-post-nova-despesa, #btn-editar-nova-despesa, #btn-post-editar-mov-competencia, #btn-post-nova-transferencia, #btn-post-edita-transferencia',function(){
	var id_btn = $(this).attr("id");
	
	if(id_btn == "btn-post-nova-receita"){
		var controller = "Movimentacao";
		var action = "postNovaReceita";
		var alert_tit = "Nova Receita";
		var actionResp = "indexReceitas";
		var vars = $("#frm-nova-receita").serialize();
	}
	else if(id_btn == "btn-editar-nova-receita"){
		var controller = "Movimentacao";
		var action = "postEditarReceita";
		var alert_tit = "Editar Movimentação";
		var actionResp = "indexReceitas";
		var vars = $("#frm-editar-receita").serialize();
	}
	else if(id_btn == "btn-post-nova-despesa"){
		var controller = "Movimentacao";
		var action = "postNovaDespesa";
		var alert_tit = "Nova Despesa";
		var actionResp = "indexDespesas";
		var vars = $("#frm-nova-despesa").serialize();
	}
	else if(id_btn == "btn-editar-nova-despesa"){
		var controller = "Movimentacao";
		var action = "postEditarDespesa";
		var alert_tit = "Editar Despesa";
		var actionResp = "indexDespesas";
		var vars = $("#frm-editar-despesa").serialize();
	}
	else if(id_btn == "btn-post-editar-mov-competencia"){
		var controller = "Movimentacao";
		var action = "postEditarCompetencia";
		var alert_tit = "Editar Competência";
		var str_movimentacao = $(this).data("str-movimentacao");
		var actionResp = (str_movimentacao == "receita") ? "indexReceitas": "indexDespesas";
		var vars = $("#frm-editar-mov-competencia").serialize();
	}
	else if(id_btn == "btn-post-nova-transferencia"){
		var controller = "Movimentacao";
		var action = "postNovaTransferencia";
		var alert_tit = "Nova Transferência";
		var actionResp = "indexTransferencias";
		var vars = $("#frm-nova-transferencia").serialize();
	}
	else if(id_btn == "btn-post-edita-transferencia"){
		var controller = "Movimentacao";
		var action = "postEditarTransferencia";
		var alert_tit = "Editar Transferência";
		var actionResp = "indexTransferencias";
		var vars = $("#frm-edita-transferencia").serialize();
	}
	
	vars += "&" + $("#frmChangeConta").serialize();
	
	$.ajax({
		type : "POST",
		url : getUrlHome() + '/'+controller+'/'+action,
		data : vars,
		dataType: "json",
		beforeSend: function(request){
			showCarregando();
		},
		complete: function(){
			closeCarregando();
		},
		success : function(response) {
			if(response.retorno == "OK"){
				closeRemodal();
				ajaxResposta('Movimentacao', actionResp, vars, '.main-content');
				setTimeout("showSuccess('"+alert_tit+"', '"+response.msg+"');", 250);
				return;
			}
			else if(response.retorno == "ERRO"){
				showError(alert_tit + ": Erro", response.msg);
				return;
			}
			else{
				showAlert(alert_tit, "Não conseguimos concluir a requisição. Tente novamente em breve.");
			}
		}
	});
});

$('body').on('click','#lnk-delete-receita, #lnk-delete-despesa, #lnk-delete-transferencia',function(){
	var mov_id = $(this).attr("data-id");
	var id_btn = $(this).attr("id");
	
	if(id_btn == "lnk-delete-receita"){
		var title = "Deletar Receita";
		var text = "Deseja deletar essa Receita? Essa operação não pode ser desfeita.";
		var msg_title = "Deletar Receita";
		var actionRet = "indexReceitas";
	}
	else if(id_btn == "lnk-delete-despesa"){
		var title = "Deletar Despesa";
		var text = "Deseja deletar essa Despesa? Essa operação não pode ser desfeita.";
		var msg_title = "Deletar Despesa";
		var actionRet = "indexDespesas";
	}
	else if(id_btn == "lnk-delete-transferencia"){
		var title = "Deletar Transferência";
		var text = "Deseja deletar essa Transferência? Essa operação não pode ser desfeita.";
		var msg_title = "Deletar Transferência";
		var actionRet = "indexTransferencias";
	}
	
	swal({
	  title: title,
	  text: text,
	  type: "warning",
	  showCancelButton: true,
	  confirmButtonColor: "#DD6B55",
	  confirmButtonText: "Deletar",
	  cancelButtonText: "cancelar",
	  closeOnConfirm: false
	},
	function(){
		var controller = "Movimentacao";
		var action = "postDeletar";
		var vars = "id=" + mov_id;
		
		$.ajax({
			type : "POST",
			url : getUrlHome() + '/'+controller+'/'+action,
			data : vars,
			dataType: "json",
			beforeSend: function(request){
				showCarregando();
			},
			complete: function(){
				closeCarregando();
			},
			success : function(response) {
				if(response.retorno == "OK"){
					vars = $("#frmChangeConta").serialize();
					
					ajaxResposta('Movimentacao', actionRet, vars, '.main-content');
					showSuccess(msg_title, response.msg);
					return;
				}
				else if(response.retorno == "ERRO"){
					showError(msg_title + ": Erro", response.msg);
					return;
				}
				else{
					showAlert(msg_title, "Não conseguimos concluir a gravação. Tente novamente em breve.");
				}
			}
		});
	});
});

$('body').on('click','a#mov_mostra_parcelas, a#ccm_mostra_parcelas',function(){
	var mov_id = $(this).data("id");
	var id_btn = $(this).attr("id");
	
	if(id_btn == "mov_mostra_parcelas"){
		var controller = "Movimentacao";
		var action = "mostraParcelas";
		var vars = "mov_id="+mov_id;
	} else if (id_btn == "ccm_mostra_parcelas") {
		var controller = "CartaoCredito";
		var action = "mostraParcelas";
		var vars = "ccm_id="+mov_id;
	}
	
	ajaxModal(controller, action, vars);
});
// ================================================

// Controller CartaoCredito =======================
$('body').on('click','#btn-novo-cartao, #lnk-edit-cartao, #btn-novo-lancamento-cartao, #lnk-edit-lcto-cartao',function(){
	var cc_id = $(this).attr("data-id");
	var id_btn = $(this).attr("id");
	
	if(id_btn == "btn-novo-cartao"){
		var controller = "CartaoCredito";
		var action = "incluir";
		var vars = "";
	}
	else if(id_btn == "lnk-edit-cartao"){
		var controller = "CartaoCredito";
		var action = "editar";
		var vars = "id=" + cc_id;
	}
	else if(id_btn == "btn-novo-lancamento-cartao"){
		var controller = "CartaoCredito";
		var action = "incluirLctoCartao";
		var vars = "";
	}
	else if(id_btn == "lnk-edit-lcto-cartao"){
		var controller = "CartaoCredito";
		var action = "editarLctoCartao";
		var vars = "id=" + cc_id;
	}
	
	ajaxModal(controller, action, vars);
});

$('body').on('click','#btn-grava-novo-cartao, #btn-grava-edita-cartao, #btn-grava-novo-lcto-cartao, #btn-grava-edita-lcto-cartao',function(){
	var id_btn = $(this).attr("id");
	var vars = $("#frm-novo-cartao").serialize();
	
	if(id_btn == "btn-grava-novo-cartao"){
		var controller = "CartaoCredito";
		var action = "postIncluir";
		var alert_tit = "Novo Cart&atilde;o";
		var actionResponse = "index";
		var varsResponse = "";
	}
	else if(id_btn == "btn-grava-edita-cartao"){
		var controller = "CartaoCredito";
		var action = "postEditar";
		var alert_tit = "Editar Cart&atilde;o";
		var actionResponse = "index";
		var varsResponse = "";
	}
	else if(id_btn == "btn-grava-novo-lcto-cartao"){
		var controller = "CartaoCredito";
		var action = "postIncluirLctoCartao";
		var alert_tit = "Inserir Lan&ccedil;amento";
		var actionResponse = "indexLancamentos";
		var varsResponse = $('#frmChangeCartao').serialize();
	}
	else if(id_btn == "btn-grava-edita-lcto-cartao"){
		var controller = "CartaoCredito";
		var action = "postEditarLctoCartao";
		var alert_tit = "Editar Lan&ccedil;amento";
		var actionResponse = "indexLancamentos";
		var varsResponse = $('#frmChangeCartao').serialize();
	}
	
	$.ajax({
		type : "POST",
		url : getUrlHome() + '/'+controller+'/'+action,
		data : vars,
		dataType: "json",
		beforeSend: function(request){
			showCarregando();
		},
		complete: function(){
			closeCarregando();
		},
		success : function(response) {
			if(response.retorno == "OK"){
				closeRemodal();
				ajaxResposta('CartaoCredito', actionResponse, varsResponse, '.main-content');
				setTimeout("showSuccess('"+alert_tit+"', '"+response.msg+"');", 250);
				return;
			}
			else if(response.retorno == "ERRO"){
				showError(alert_tit + ": Erro", response.msg);
				return;
			}
			else{
				showAlert(alert_tit, "Não conseguimos concluir a requisição. Tente novamente em breve.");
			}
		}
	});
});

$('body').on('click','#lnk-delete-cartao, #lnk-delete-lcto-cartao',function(){
	var cc_id = $(this).attr("data-id");
	var id_btn = $(this).attr("id");
	
	if(id_btn == "lnk-delete-cartao"){
		var controller = "CartaoCredito";
		var action = "postDeletar";
		var vars = "id=" + cc_id;
		var alert_tit = "Deletar Cartão";
		var alert_msg = "Deseja deletar esse cartão? Essa operação não pode ser desfeita.";
		var actionResponse = "index";
		var varsResponse = "";
	}
	else if(id_btn == "lnk-delete-lcto-cartao"){
		var controller = "CartaoCredito";
		var action = "postDeletarMov";
		var vars = "id=" + cc_id;
		var alert_tit = "Deletar Lançamento";
		var alert_msg = "Deseja deletar esse lançamento? Essa operação não pode ser desfeita. (Se for parcelamento, deletará todas as parcelas!)";
		var actionResponse = "indexLancamentos";
		var varsResponse = $('#frmChangeCartao').serialize();
	}
	
	swal({
	  title: alert_tit,
	  text: alert_msg,
	  type: "warning",
	  showCancelButton: true,
	  confirmButtonColor: "#DD6B55",
	  confirmButtonText: "Deletar",
	  cancelButtonText: "cancelar",
	  closeOnConfirm: false
	},
	function(){
		$.ajax({
			type : "POST",
			url : getUrlHome() + '/'+controller+'/'+action,
			data : vars,
			dataType: "json",
			beforeSend: function(request){
				showCarregando();
			},
			complete: function(){
				closeCarregando();
			},
			success : function(response) {				
				if(response.retorno == "OK"){
					closeRemodal();
					ajaxResposta('CartaoCredito', actionResponse, varsResponse, '.main-content');
					setTimeout("showSuccess('"+alert_tit+"', '"+response.msg+"');", 250);
					return;
				}
				else if(response.retorno == "ERRO"){
					showError(msg_title + ": Erro", response.msg);
					return;
				}
				else{
					showAlert(msg_title, "Não conseguimos concluir a gravação. Tente novamente em breve.");
				}
			}
		});
		
	});
});
// ================================================

$('body').on('click','#chbx-movimentacao-repetir',function(){
	$("#mov_repetir_por").toggleDisabled().val("");
});

$('body').on('change','.dd',function(){
	var obj = $('.dd').nestable('serialize');
	
	console.log(obj);
});