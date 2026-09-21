<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
</head>

<body>
    <label for="">Fazenda Casa Blanca - Nº da Mãe:</label>
    <select id="estado" name="estado" style="width: 150px;">
    </select>

    <label for="">ID Selecionado:
        <input type="text" name="codigo_numerico" id="codigo_numerico">
    </label>

    <label for="">Chave Primaria:
        <input type="text" name="codigo_id" id="codigo_id">
    </label>

    <script>
		window.addEventListener("load", function(){
			var local = 77;
		    $.post("lista_femeas_nascimento.php", {local:local}, function(valor){
		        $("select[name=estado]").html(valor);
		    });
		});

        $(document).ready(function() {
            $('#estado').select2();

            $('#estado').change(function(){
			    var options = $('#estado option:selected');
			    $(options).each(function(){
			        var desc = $(this).bind('#estado').text();

	        		$("#codigo_id").val($("#estado").val());
                    $("#codigo_numerico").val(desc);
                    //alert ('Estado selecionado: ' + $("#estado").val() +'-'+desc);
			    });
    		});   

        });

    </script>
</body>
</html>
