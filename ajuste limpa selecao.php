$(document).ready(function() {
    
    // =================================================================================
    // 1. CONFIGURAÇÃO GLOBAL (Define o texto de "Limpar Seleção" para todos)
    //    Isso elimina a necessidade do atributo data-deselect-all-text no HTML.
    // =================================================================================
    $.fn.selectpicker.defaults = {
        deselectAllText: 'Limpar Seleção',
        actionsBox: true, // Garante que a caixa de ações esteja habilitada
        // Não vamos definir selectAllText aqui para garantir que o elemento
        // seja criado e possamos removê-lo de forma explícita na etapa 3.
    };
    
    // =================================================================================
    // 2. LÓGICA DE VISIBILIDADE E REMOÇÃO (Aplicada a cada selectpicker)
    // =================================================================================
    $('.selectpicker').each(function() {
        
        const $selectElement = $(this);
        
        // Inicializa o selectpicker (usa as defaults acima)
        $selectElement.selectpicker();
        
        // Localiza a barra de ações gerada pelo plugin (onde está o botão Limpar Seleção)
        const $dropdownMenu = $selectElement.closest('.bootstrap-select').find('.dropdown-menu');
        const $actionsBox = $dropdownMenu.find('.bs-actionsbox');

        // Se a barra de ações não for encontrada, paramos para este elemento
        if (!$actionsBox.length) {
            return; 
        }

        // NOVO: Aplica o alinhamento à direita para a barra de ações
        $actionsBox.css('text-align', 'right');
        
        // -----------------------------------------------------------
        // 3. REMOÇÃO FORÇADA do "Selecionar Todos"
        // Esta linha é mantida, pois garante a remoção do botão indesejado,
        // limpando o espaço visual.
        // -----------------------------------------------------------
        $actionsBox.find('.bs-select-all').remove();
        
        // -----------------------------------------------------------
        // 4. FUNÇÃO DE VISIBILIDADE (Mostrar/Ocultar o container da barra de ações)
        // -----------------------------------------------------------
        function atualizarVisibilidadeActionsBox() {
            // Pega o array de valores selecionados
            const selecoes = $selectElement.val(); 
            
            // Se há seleções, mostra a barra de ações; caso contrário, esconde.
            if (selecoes && selecoes.length > 0) {
                $actionsBox.show(); 
            } else {
                $actionsBox.hide(); 
            }
        }
        
        // 5. Executa na inicialização (Estado inicial do formulário)
        atualizarVisibilidadeActionsBox();

        // 6. Monitore o evento de mudança
        $selectElement.on('changed.bs.select', atualizarVisibilidadeActionsBox);
    });
});


<style type="text/css">
    /* 1. Alinha o container de texto à direita */
    .bootstrap-select .bs-actionsbox {
        text-align: right; 
        padding: 5px 5px 5px 5px; /* Ajusta o padding para melhor visualização */
    }

    /* 2. Garante que o link de deselect seja um bloco de texto que se mova */
    .bootstrap-select .bs-actionsbox .bs-deselect-all {
        display: inline-block; /* Garante que o link se comporte como um bloco inline */
        float: none; /* Garante que não haja float de versões antigas do Bootstrap */
        padding: 0; /* Remove padding interno que possa atrapalhar */
        border: none;
        color: #007aff;
        background: transparent;
        font-size: 13px;
        font-weight: 500;        
    }
</style>

                                        <div class="row filtro_reproducao" hidden>
                                            <div class="form-group col-xs-6 col-md-3">
                                                <label for="num_natimorto_de_filtro" class="control-label">Nº Natimortos (de)</label>
                                                <input name="num_natimorto_de_filtro" type="text" class="form-control" id="num_natimorto_de_filtro" 
                                                onkeypress = "return numeros(this, event)" maxlength="3"
                                                >
                                            </div>

                                            <div class="form-group col-xs-6 col-md-3">
                                                <label for="num_natimorto_ate_filtro" class="control-label">Nº Natimortos (até)</label>
                                                <input name="num_natimorto_ate_filtro" type="text" class="form-control" id="num_natimorto_ate_filtro" 
                                                onkeypress = "return numeros(this, event)" maxlength="3"
                                               >
                                            </div>
                                        </div>

        $("#num_natimorto_de_filtro").click(function(){
            $("#num_natimorto_de_filtro").val('');
            document.getElementById("num_natimorto_de_filtro").style.borderColor = "";
            return;
        });

        $("#num_natimorto_ate_filtro").click(function(){
            $("#num_natimorto_ate_filtro").val('');
            document.getElementById("num_natimorto_ate_filtro").style.borderColor = "";
            return;
        });


        if (num_natimorto_de!='' || num_natimorto_ate!='') {
            if (num_natimorto_de=='') {
                $("#mensagem_erro_filtro").modal();
                $("#mensagem_erro_filtro .modal-body").html('Nº Natimortos (de) não pode ser vazio!');
                document.getElementById("num_natimorto_de_filtro").focus();
                document.getElementById("num_natimorto_de_filtro").style.borderColor = "red";
                return;
            }

            if (num_natimorto_ate=='') {
                $("#mensagem_erro_filtro").modal();
                $("#mensagem_erro_filtro .modal-body").html('Nº Natimortos (até) não pode ser vazio!');
                document.getElementById("num_natimorto_ate_filtro").focus();
                document.getElementById("num_natimorto_ate_filtro").style.borderColor = "red";
                return;
            }

            var num_natimorto_de = parseInt($("#num_natimorto_de_filtro").val());
            var num_natimorto_ate = parseInt($("#num_natimorto_ate_filtro").val());

            if (num_natimorto_de > num_natimorto_ate) {
                $("#mensagem_erro_filtro").modal();
                $("#mensagem_erro_filtro .modal-body").html('Nº Natimortos (até) não pode ser menor que o Nº Natimortos (de)!');
                document.getElementById("num_natimorto_ate_filtro").focus();
                document.getElementById("num_natimorto_ate_filtro").style.borderColor = "red";
                return;
            }
        }

    $("#num_natimorto_de_filtro").val('');
    $("#num_natimorto_ate_filtro").val('');


        var num_natimorto_de = $("#num_natimorto_de_filtro").val();
        var num_natimorto_ate = $("#num_natimorto_ate_filtro").val();

        if (num_natimorto_de!='' || num_natimorto_ate!='') {
            natimorto_filtro = '->Natimortos: de ' + num_natimorto_de + ' ate ' + num_natimorto_ate;
        }
        else {
            natimorto_filtro = '';
        }


    var num_natimorto_de = $("#num_natimorto_de_filtro").val();
    var num_natimorto_ate =$("#num_natimorto_ate_filtro").val();

    var filtro_num_natimorto= $("#filtro_num_natimorto").val();

    if (num_natimorto_de!='' && num_natimorto_ate!='') {
        var filtro_num_natimorto = 'S';
        filtro_reproducao = 'S'
    }
    else {
        var filtro_num_natimorto = 'N';
    }


            '&num_natimorto_de=' + num_natimorto_de + 
            '&num_natimorto_ate=' + num_natimorto_ate +

            '&filtro_num_natimorto=' + filtro_num_natimorto +

    $num_natimorto_de = $_REQUEST['num_natimorto_de'];
    $num_natimorto_ate = $_REQUEST['num_natimorto_ate'];

    $filtro_num_natimorto = $_REQUEST['filtro_num_natimorto'];

    if ($num_natimorto_de=='') {
        $num_natimorto_de = 0;
        $num_natimorto_ate = 999;
    }

                                                <input type="hidden" id="num_natimorto_de_filtro"  <?php echo "value='".$_REQUEST['num_natimorto_de']."'";?>>

                                                <input type="hidden" id="num_natimorto_ate_filtro"
                                                    <?php echo "value='".$_REQUEST['num_natimorto_ate']."'";?>>

                                                <input type="hidden" id="filtro_num_natimorto"
                                                    <?php echo "value='".$_REQUEST['filtro_num_natimorto']."'";?>>

        if ($filtro_num_natimorto=='N') {
            $filtroNumNatimortos = 'S';
            $num_natimortos = 0;

            if (!empty($dados_natimortos) && $reg_animal->tbl_animal_sexo=='F') {
                $num_natimortos = $dados_natimortos[$reg_animal->tbl_animal_codigo_id];
            }
        }
        else {
            $filtroNumNatimortos = 'N';
            $num_natimortos = 0;

            if (!empty($dados_natimortos) && $reg_animal->tbl_animal_sexo=='F') {
                $num_natimortos = $dados_natimortos[$reg_animal->tbl_animal_codigo_id];
            
                if ($filtro_reproducao=='S') {
                    if ($num_natimortos>=$num_natimorto_de && $num_natimortos<=$num_natimorto_ate) {
                        $filtroNumNatimortos = 'S';
                    }
                }
            }
        }


            'filtroNumNatimortos' => $filtroNumNatimortos,

                        $animal['filtroNumNatimortos']=='S' &&



FILTRO DESCARTE


                                        <div class="row filtro_reproducao" hidden>
                                            <div class="form-group col-xs-12">
                                                <div style="display: flex; align-items: center; gap: 15px; flex-wrap: wrap;">
                                                    
                                                    <label style="margin-bottom: 0; margin-top: 5px;">Vacas Descarte:</label>

                                                    <label class="checkbox-inline" style="margin-top: 0;">
                                                        <input type="checkbox" id="descarte" name="descarte" value="S" 
                                                        <?php if ($descarte=='S'){echo 'checked="checked"';}?>> Sim
                                                    </label>

                                                    <label class="checkbox-inline" style="margin-top: 0;">
                                                        <input type="checkbox" value="N" name="descarte_nao" id="descarte_nao"
                                                        <?php if ($descarte=='N'){echo 'checked="checked"';}?>> Não
                                                    </label>
                                                    
                                                </div>
                                            </div>
                                        </div>


$("#descarte_nao").prop("checked", false);

    if ($("#descarte").is(":checked") == true && 
        $("#descarte_nao").is(":checked") == true) {
        filtro_descarte = '->Descarte:S;N';
    }
    else if ($("#descarte").is(":checked") == true){
        filtro_descarte = '->Descarte:S';
    }
    else if ($("#descarte_nao").is(":checked") == true){
        filtro_descarte = '->Descarte:N';
    }
    else {
        filtro_descarte = '';
    }


    if ($("#descarte").is(":checked") == true && 
        $("#descarte_nao").is(":checked") == true) {
        var filtro_descarte = '';
    }
    else if ($("#descarte").is(":checked") == true){
        var filtro_descarte = 'S';
        filtro_reproducao = 'S';
    }
    else if ($("#descarte_nao").is(":checked") == true){
        var filtro_descarte = 'N';
        filtro_reproducao = 'S';
    }
    else {
        var filtro_descarte = '';
    }


        if ($filtro_descarte=='S' && $reg_animal->tbl_animal_descarte_reproducao) {
            $dados_descarte[$reg_animal->tbl_animal_codigo_id] = 'S';
        }
        else if ($filtro_descarte=='N' && $reg_animal->tbl_animal_descarte_reproducao!="S") {
            $dados_descarte[$reg_animal->tbl_animal_codigo_id] = 'S';
        }


        if ($filtro_descarte=='') {
            $filtroDescarte = 'S';
        }
        else {
            $filtroDescarte = 'N';

            if (!empty($dados_descarte)) {
                $id_animal = $reg_animal->tbl_animal_codigo_id;

                if (array_key_exists($id_animal, $dados_descarte)) {
                    if ($dados_descarte[$id_animal]=='S') {
                        $filtroDescarte = 'S';
                    }
                }
            }
        }
