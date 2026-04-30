/*mapa_gado_typeahead*/
    $(document).ready(function(){

        $('#codigo_mae_consulta').typeahead({
            source: function(query, result) {
                $.ajax({
                    url:"fetch_femeas_servidas.php",
                    method:"POST",
                    data:{query:query,
                          local: $('#local_id').val()},
                    dataType:"json",
                    success:function(data)
                    {
                        result($.map(data, function(item){
                        return item;
                        }));
                    }
                })
            }
        });

        $("#codigo_mae_consulta").click(function(){
            $("#codigo_mae_consulta").val('');
            $("#codigo_mae_animal").val('');
            document.getElementById("codigo_mae_consulta").style.borderColor = "";
            $(".desc_novo_nascimento").html('');
            return;
        });

        $('#id_animal_morte').typeahead({
            source: function(query, result) {
                $.ajax({
                    url:"fetch.php",
                    method:"POST",
                    data:{query:query,
                          local: $('#local_morte').val()},
                    dataType:"json",
                    success:function(data)
                    {
                        result($.map(data, function(item){
                        return item;
                        }));
                    }
                })
            }
        });

        $('#id_animal_morte').click(function(){
            $('#codigo_id_morte').val(0);
            $('#descricao_animal_morte').text('');
            $('.alert_erro_animal .negrito').html('');
            $('.alert_erro_animal span').html('');
            $('.alert_erro_animal').hide();
            return;
        });

    });
