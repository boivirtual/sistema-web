function limpa_mens(){
	$(".alert_erro").hide();
}

function gerar_nova_senha() {
	var senha_nova = $("#nova_senha").val();
	var repita_senha = $("#confirme_nova_senha").val();
	var email_usuario = $("#email_usuario").val();

	if (senha_nova==''){
		$(".alert_erro .negrito").html('Atenção! ');
		$(".alert_erro span").html('Informe a nova senha.');
		$(".alert_erro").show();
		return;
	}

	if (repita_senha==''){
		$(".alert_erro .negrito").html('Atenção! ');
		$(".alert_erro span").html('Senhas informadas devem ser iguais.');
		$(".alert_erro").show();
		return;
	}

	if (repita_senha!=senha_nova){
		$(".alert_erro .negrito").html('Atenção! ');
		$(".alert_erro span").html('Senhas informadas devem ser iguais.');
		$(".alert_erro").show();
		return;
	}

	$.post("gravar_redefinir_senha.php",{email_usuario: email_usuario, senha_nova: senha_nova}, function(valor){

        var php = valor.split("<|>");

		if (php[0]==9){
			//alert ('Atenção! ' + php[1]);
			$(".alert_erro .negrito").html('Atenção! ');
			$(".alert_erro span").html(php[1]);
			$(".alert_erro").show();
		}
		else if (php[0]==0){
			//alert ('Sucesso! ' + php[1]);
			$(".alert_mens .negrito").html('Sucesso! ');
			$(".alert_mens span").html(php[1]);
			$(".alert_mens").show();
			//out = setTimeout('redirecionar_login()', 3000);
		}
	});
}

function redirecionar_login(){
	location.href = "../index.php";
}

$(document).ready(function(){

    $("#username").click(function(){
		$(".alert_erro").hide();
    });

    $("#pass").click(function(){
		$(".alert_erro").hide();
    });

    $("#confirme_nova_senha").click(function(){
		$(".alert_erro").hide();
    });
  
    $("#nova_senha").click(function(){
		$(".alert_erro").hide();
    });

});


