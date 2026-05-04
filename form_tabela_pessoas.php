<?php
  include "conecta_mysql.inc";

  if(isset($_REQUEST["editar"]) && $_REQUEST["editar"] == true) {
    $status_gravacao = $_REQUEST["status_gravacao"];
    $erro_mysql = $_REQUEST["erro_mysql"];
  }
  else {
    $status_gravacao = '';
    $erro_mysql = '';
  }

  include "cabecalho_novo.php";
  include "opcoes_menu.php"; include "limpar_secao_selecao_matrizes.php"; include "limpar_secao_compra_venda.php"; include "limpar_secao_ctp.php"; include "limpar_secao_ctr.php"; include "limpar_secao_pesagem.php"; include "limpar_secao_movimentacao.php"; include "limpar_secao_nutricao.php"; include "limpar_secao_nascimento.php";
?>
    <!--sidebar end-->

   <!-- <div class="modal-body">-->
    <!--main content start-->
    <section id="main-content">
        <section class="wrapper" style="margin-left: 5px;">

           <div class="row">
                <div class="col-lg-12">
                    <h3 class="page-header"><i class="fa fa-file-text-o"></i> Pessoas</h3>
                   <!-- <ol class="breadcrumb">
                        <li><i class="fa fa-home"></i><a href="menu.php">Home</a></li>
                        <li><i class="icon_document_alt"></i>Configurações</li>
                        <li><i class="fa fa-file-text-o"></i>Form elements</li>
                    </ol> -->
                </div>
            </div>

          <div class="row">
            <div class="col-lg-12">

                    <div  class="form-group">
                        <a href="#">
                            <input type="button" class="btn btn-primary" aria-label="Left Align" 
                            data-toggle="modal" 
                            data-target="#modal_incluir" 
                            value="Incluir Novo"/>
                        </a>

                    </div> 

               <section class="panel">
                <!--<header class="panel-heading">
                    Lista
                  </header> -->

                <table class="table table-striped table-advance table-hover table-bordered" id="tabela_racas">
                  <thead>
                    <tr>
                      <th> Razão/Nome</th>
                      <th> Classe</th>
                      <th> Telefone</th>
                      <th> Email</th>
                      <th>
                        <i class="icon_cogs"></i> Ações
                      </th>
                    </tr>
                  </thead>
                  

                  <tbody>
                    <?php 
                        $ssql = "select * from tbl_pessoa
                                      inner join tabela_classe_pessoas 
                                      on tbl_pessoa_classe=tab_codigo_classe_pessoas"; 
                        $rs = mysqli_query($conector, $ssql); 
             
                        while ($registro_tabela = mysqli_fetch_object($rs)){
                            $codigo = $registro_tabela->tbl_pessoa_id;
                            $nome = $registro_tabela->tbl_pessoa_nome; 
                            $lixeira = $registro_tabela->tbl_pessoa_lixeira; 
                            $classe = $registro_tabela->tab_descricao_classe_pessoas; 
                            $cpf_cnpj = $registro_tabela->tbl_pessoa_cpf_cnpj; 
                            $email = $registro_tabela->tbl_pessoa_email; 
                            $tipo_pessoa = $registro_tabela->tbl_pessoa_tipo_pessoa; 

                            if ($tipo_pessoa=='F'){
                                $cnpj_cpf_editado = substr($cpf_cnpj,0,3) . "." . substr($cpf_cnpj,3,3) . "." . 
                                                    substr($cpf_cnpj,6,3) . "-" . substr($cpf_cnpj,9,2);
                            }
                            else {
                                $cnpj_cpf_editado = substr($cpf_cnpj,0,2) . "." . substr($cpf_cnpj,2,3) . "." .
                                                    substr($cpf_cnpj,5,3) . "/" . substr($cpf_cnpj,8,4) . "-" . 
                                                    substr($cpf_cnpj,12,2);
                            }

                            if (strlen($registro_tabela->tbl_pessoa_telefone)==9) {
                                $telefone = '(' . $registro_tabela->tbl_pessoa_ddd . ') ' . 
                                substr($registro_tabela->tbl_pessoa_telefone, 0, 5) . '-' . 
                                substr($registro_tabela->tbl_pessoa_telefone, 5, 4);
                            }
                            else {
                                $telefone = '(' . $registro_tabela->tbl_pessoa_ddd . ') ' . 
                                substr($registro_tabela->tbl_pessoa_telefone, 0, 4) . '-' . 
                                substr($registro_tabela->tbl_pessoa_telefone, 4, 4);
                            }


                            if ($lixeira==1){
                                echo '<tr>';
                                echo '<td style="color:#ccc">'.$nome.'</td>';
                                echo '<td style="color:#ccc">'.$classe.'</td>';
                                echo '<td style="color:#ccc">'.$telefone.'</td>';
                                echo '<td style="color:#ccc">'.$email.'</td>';
                                echo '<td>
                                      <a class="btn" href="#" 
                                      data-toggle="modal" 
                                      data-target="#modal_excluir" 
                                      data-whatever="'.$codigo.'"
                                      data-whatevernome="'.$nome.'"
                                      data-whatevertipo="3">
                                      <i class="icon_refresh" title="Remover esse registro da lixeira" ></i>
                                      </a>
                                      </td>';
                                echo '</tr>'; 
                            }
                            else {
                                echo '<tr>';
                                echo '<td>'.$nome.'</td>';
                                echo '<td>'.$classe.'</td>';
                                echo '<td>'.$telefone.'</td>';
                                echo '<td>'.$email.'</td>';
                                echo '<td>
                                      <a class="btn" href="#" 
                                      data-toggle="modal" 
                                      data-target="#modal_editar" 
                                      data-whatever="'.$codigo.'"
                                      data-whatevernome="'.$nome.'">
                                      <i class="icon_pencil" title="Editar esse registro" ></i>
                                      </a>
                                
                                      <a class="btn" href="#" 
                                      data-toggle="modal" 
                                      data-target="#modal_excluir" 
                                      data-whatever="'.$codigo.'"
                                      data-whatevernome="'.$nome.'"
                                      data-whatevertipo="2">
                                      <i class="icon_trash_alt" 
                                       title="Enviar esse registro para lixeira"></i>
                                      </a></td>';
                                echo '</tr>'; 
                            }
                        } 
                        mysqli_close($conector);
                    ?>
                  </tbody>
                </table>
              </section>
            </div>
          </div>
          <!-- page end-->
            <div class="modal fade" id="modal_incluir" tabindex="-1" role="dialog" 
             aria-labelledby="modal_incluirCenterTitle" aria-hidden="true"  data-backdrop="static">

                <div class="modal-dialog modal-dialog-centered" role="document" style="width: 800px;">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="modal_incluirLabel">Pessoas - Incluir</h4>
                        </div>

                        <div class="modal-body">
                            <form method="POST" action="gravar_produtos.php" enctype="multipart/form-data">
                              <div class="row">
                                <div class="form-group col-md-4">
                                    <label for="codigo_pessoa" class="control-label"><span class="required">*</span>Código</label>
                                    <input name="codigo_pessoa" type="text" class="form-control" id="codigo_pessoa" required="">
                                </div>
                              </div>
                              
                              <ul class="nav nav-tabs m-bot15">
                                <li class="active">
                                  <a data-toggle="tab" href="#dados">Dados</a>
                                </li>
                                <li class="">
                                  <a data-toggle="tab" href="#outros_contatos">Outros Contatos</a>
                                </li>
                                <li class="">
                                  <a data-toggle="tab" href="#propriedades">Propriedades</a>
                                </li>
                              </ul>
                              
                              <div class="tab-content">
                                <div id="dados" class="tab-pane active">
                                  <div class="row">
                                    <div class="form-group col-md-8">
                                        <label for="nome_pessoa" class="control-label"><span class="required">*</span>Nome</label>
                                        <input name="nome_pessoa" type="text" class="form-control" id="nome_pessoa" required="">
                                    </div>
                                    <div class="form-group col-md-4">
                                      <label for="tipo_id" class="control-label"><span class="required">*</span>Tipo</label>
                                      <select class="form-control">
                                        <option>Tipo 1</option>
                                        <option>Tipo 2</option>
                                        <option>Tipo 3</option>
                                        <option>Tipo 4</option>
                                        <option>Tipo 5</option>
                                      </select>
                                    </div>
                                  </div>
                                  
                                  <div class="row">
                                    <div class="form-group col-md-4">
                                        <label for="tipo_pessoa" class="control-label">Pessoa</label>
                                        <div class="clearfix"></div>
                                        <label class="radio-inline">
                                          <input type="radio" name="tipo_pessoa" value="fisica" checked>Física
                                        </label>
                                        <label class="radio-inline">
                                          <input type="radio" name="tipo_pessoa" value="juridica">Jurídica
                                        </label>
                                    </div>
                                    <div class="form-group col-md-5">
                                        <label for="documento_pessoa" class="control-label">CPF/CNPJ</label>
                                        <input name="documento_pessoa" type="text" class="form-control" id="documento_pessoa">
                                    </div>
                                  </div>
                                  
                                  <div class="row">
                                    <div class="form-group col-md-8">
                                        <label for="email_pessoa" class="control-label">Email</label>
                                        <input name="email_pessoa" type="text" class="form-control" id="email_pessoa">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="telefone_pessoa" class="control-label">Telefone</label>
                                        <input name="telefone_pessoa" type="text" class="form-control" id="telefone_pessoa" placeholder="(##) #####-####">
                                    </div>
                                  </div>
                                  
                                  <hr>
                                  
                                  <div class="row">
                                    <div class="form-group col-md-5">
                                        <label for="cep_pessoa" class="control-label">CEP</label>
                                        <input name="cep_pessoa" type="text" class="form-control" id="cep_pessoa">
                                    </div>
                                  </div>
                                  
                                  <div class="row">
                                    <div class="form-group col-md-6">
                                        <label for="endereco_pessoa" class="control-label">Endereço</label>
                                        <input name="endereco_pessoa" type="text" class="form-control" id="endereco_pessoa">
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label for="numero_pessoa" class="control-label">Número</label>
                                        <input name="numero_pessoa" type="text" class="form-control" id="numero_pessoa">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="complemento_pessoa" class="control-label">Complemento</label>
                                        <input name="complemento_pessoa" type="text" class="form-control" id="complemento_pessoa">
                                    </div>
                                  </div>
                                  
                                  <div class="row">
                                    <div class="form-group col-md-6">
                                        <label for="bairro_pessoa" class="control-label">Bairro</label>
                                        <input name="bairro_pessoa" type="text" class="form-control" id="bairro_pessoa">
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label for="estado_pessoa" class="control-label">Estado</label>
                                        <input name="estado_pessoa" type="text" class="form-control" id="estado_pessoa">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="cidade_pessoa" class="control-label">Cidade</label>
                                        <input name="cidade_pessoa" type="text" class="form-control" id="cidade_pessoa">
                                    </div>
                                  </div>
                                  
                                  <hr>
                                  
                                  <div class="row">
                                    <div class="form-group col-md-2">
                                        <label for="banco1_pessoa" class="control-label">Banco</label>
                                        <input name="banco1_pessoa" type="text" class="form-control m-bot15" id="banco1_pessoa">
                                        <input name="banco2_pessoa" type="text" class="form-control" id="banco2_pessoa">
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label for="agencia1_pessoa" class="control-label">Agência</label>
                                        <input name="agencia1_pessoa" type="text" class="form-control m-bot15" id="agencia1_pessoa">
                                        <input name="agencia2_pessoa" type="text" class="form-control" id="agencia2_pessoa">
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label for="conta1_pessoa" class="control-label">Conta</label>
                                        <input name="conta1_pessoa" type="text" class="form-control m-bot15" id="conta1_pessoa">
                                        <input name="conta2_pessoa" type="text" class="form-control" id="conta2_pessoa">
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label for="tipo_conta1_pessoa" class="control-label">Tipo</label>
                                        <input name="tipo_conta1_pessoa" type="text" class="form-control m-bot15" id="tipo_conta1_pessoa">
                                        <input name="tipo_conta2_pessoa" type="text" class="form-control" id="tipo_conta2_pessoa">
                                    </div>
                                  </div>
                                  
                                  <hr>
                                  
                                  <div class="row m-bot15">
                                    <div class="col-md-10">
                                      <label for="observacao_pessoa" class="control-label">Observação</label>
                                      <textarea name="observacao_pessoa" type="text" class="form-control" id="observacao_pessoa" rows="1"></textarea>
                                    </div>
                                  </div>
                                </div>
                                
                                <div id="outros_contatos" class="tab-pane">
                                  <a href="#">
                                      <input type="button" class="btn btn-primary m-bot15" aria-label="Left Align" 
                                      data-toggle="modal" 
                                      data-target="#modal_incluir_contato" 
                                      value="Incluir Novo"/>
                                  </a>
                                  <table class="table table-striped table-advance table-hover table-bordered">
                                    <thead>
                                      <tr>
                                        <th> Nome</th>
                                        <th> Telefone</th>
                                        <th> Email</th>
                                        <th>
                                          <i class="icon_cogs"></i> Ações
                                        </th>
                                      </tr>
                                    </thead>
                                  </table>
                                </div>
                                
                                <div id="propriedades" class="tab-pane">
                                  <a href="#">
                                      <input type="button" class="btn btn-primary m-bot15" aria-label="Left Align" 
                                      data-toggle="modal" 
                                      data-target="#modal_incluir_propriedade" 
                                      value="Incluir Nova"/>
                                  </a>
                                  <table class="table table-striped table-advance table-hover table-bordered">
                                    <thead>
                                      <tr>
                                        <th> Nome</th>
                                        <th> Inscrição</th>
                                        <th> Município</th>
                                        <th> Estado</th>
                                        <th>
                                          <i class="icon_cogs"></i> Ações
                                        </th>
                                      </tr>
                                    </thead>
                                  </table>
                                </div>
                              </div>
                                
                              <input type="hidden" name="tipo_gravacao" id="tipo_gravacao" value="0">
                              <input type="hidden" name="status_gravacao" id="status_gravacao"
                              <?php echo "value='".$status_gravacao."'";?>>
                              <input type="hidden" name="status_erro"  size="100" id="status_erro"
                              <?php echo "value='".$erro_mysql."'";?>>

                              <div class="form-group col-md-12">
                                <button type="submit" class="btn btn-primary">Confirmar Inclusão</button>
                                <button type="button" class="btn btn-info pull-right" data-dismiss="modal">Voltar</button>
                              </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="modal fade" id="modal_incluir_contato" tabindex="-1" role="dialog" 
             aria-labelledby="modal_incluirCenterTitle" aria-hidden="true"  data-backdrop="static">
              <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                      <h4 class="modal-title" id="modal_incluirLabel">Contatos - Incluir</h4>
                  </div>

                  <div class="modal-body">
                    <form method="POST" action="gravar_produtos.php" enctype="multipart/form-data">
                      <div class="row">
                        <div class="form-group col-md-4">
                            <label for="codigo_contato" class="control-label"><span class="required">*</span>Código</label>
                            <input name="codigo_contato" type="text" class="form-control" id="codigo_pessoa" required="">
                        </div>
                      </div>
                      <div class="row">
                        <div class="form-group col-md-4">
                            <label for="nome_contato" class="control-label"><span class="required">*</span>Nome</label>
                            <input name="nome_contato" type="text" class="form-control" id="nome_contato" required="">
                        </div>
                      </div>
                      <div class="row">
                        <div class="form-group col-md-4">
                            <label for="telefone_contato" class="control-label"><span class="required">*</span>Telefone</label>
                            <input name="telefone_contato" type="text" class="form-control" id="telefone_contato" required="">
                        </div>
                      </div>
                      <div class="row">
                        <div class="form-group col-md-4">
                            <label for="email_contato" class="control-label">Email</label>
                            <input name="email_contato" type="text" class="form-control" id="codigo_pessoa" required="">
                        </div>
                      </div>
                      
                      <div class="form-group col-md-12">
                        <button type="submit" class="btn btn-primary">Confirmar Inclusão</button>
                        <button type="button" class="btn btn-info pull-right" data-dismiss="modal">Voltar</button>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
            </div>
            
            <div class="modal fade" id="modal_incluir_propriedade" tabindex="-1" role="dialog" 
             aria-labelledby="modal_incluirCenterTitle" aria-hidden="true"  data-backdrop="static">
              <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                      <h4 class="modal-title" id="modal_incluirLabel">Propriedades - Incluir</h4>
                  </div>

                  <div class="modal-body">
                    <form method="POST" action="gravar_produtos.php" enctype="multipart/form-data">
                      <div class="row">
                        <div class="form-group col-md-12">
                            <label for="codigo_propriedade" class="control-label"><span class="required">*</span>Código</label>
                            <input name="codigo_propriedade" type="text" class="form-control" id="codigo_propriedade" required="">
                        </div>
                      </div>
                      <div class="row">
                        <div class="form-group col-md-12">
                            <label for="nome_propriedade" class="control-label"><span class="required">*</span>Nome</label>
                            <input name="nome_propriedade" type="text" class="form-control" id="nome_propriedade" required="">
                        </div>
                      </div>
                      <div class="row">
                        <div class="form-group col-md-12">
                            <label for="inscricao_propriedade" class="control-label"><span class="required">*</span>Inscrição</label>
                            <input name="inscricao_propriedade" type="text" class="form-control" id="inscricao_propriedade" required="">
                        </div>
                      </div>
                      <div class="row">
                        <div class="form-group col-md-12">
                            <label for="municipio_propriedade" class="control-label"><span class="required">*</span>Município</label>
                            <input name="municipio_propriedade" type="text" class="form-control" id="municipio_propriedade" required="">
                        </div>
                      </div>
                      <div class="row">
                        <div class="form-group col-md-5">
                            <label for="estado_contato" class="control-label">Estado</label>
                            <input name="estado_contato" type="text" class="form-control" id="estado_contato" required="">
                        </div>
                      </div>
                      
                      <div class="form-group col-md-12">
                        <button type="submit" class="btn btn-primary">Confirmar Inclusão</button>
                        <button type="button" class="btn btn-info pull-right" data-dismiss="modal">Voltar</button>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
            </div>

            <div class="modal fade" id="mensagem_retorno" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
              <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">Pessoas</h4>
                  </div>
                  <div class="modal-body"></div>
                  <div class="modal-footer">
                    <button data-dismiss="modal" class="btn btn-default" type="button" onclick="location.reload();">Fechar</button>
                  </div>
                </div>
              </div>
            </div>

        </section>
    </section>

   
<?php 
  $javascript_file_name = 'cliente_fornecedor.js';
  require 'rodape.php';
?>
