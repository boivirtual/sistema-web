    <aside>
      <div id="sidebar" class="">
        <!-- sidebar menu start-->
        <ul class="sidebar-menu">
          <li class="active">
            <a class="" href="menu.php">
              <i class="icon_house_alt"></i>
              <span>Home</span>
            </a>
          </li>

          <li class="sub-menu">
            <a href="javascript:;" class="">
              <img class="icone_cow" src="img/boi.png" alt="">
              <span>Animais</span>
              <span class="menu-arrow arrow_carrot-right"></span>
            </a>
            <ul class="sub">

              <li id="opc101"><a class="" href="form_mapa_gados.php">Mapa de Gado</a></li>
              <li id="opc101d"><a style="color:#666666" href="#">Mapa de Gado</a></li>
              <li id="opc102"><a class="" href="form_pesagem_animais.php">Pesagem</a></li>
              <li id="opc102d"><a style="color:#666666" href="#">Pesagem</a></li>
              <li id="opc103"><a class="" href="form_movimentacao_animais.php">Movimentações</a></li>
              <li id="opc103d"><a style="color:#666666" href="#">Movimentações</a></li>
              <li id="opc104"><a class="" href="form_nutricao.php">Nutrição</a></li>
              <li id="opc104d"><a style="color:#666666" href="#">Nutrição</a></li>
              <!--    <li id="opc105"><a class="" href="#">Mortes e outras saídas</a></li>
                <li id="opc105d"><a style="color:#666666" href="#">Mortes e outras saídas</a></li> -->
            </ul>
          </li>


          <li class="sub-menu">
            <a href="javascript:;" class="">
              <img class="icone_cow" src="img/bois.png" alt="">
              <span>Reprodução</span>
              <span class="menu-arrow arrow_carrot-right"></span>
            </a>
            <ul class="sub">
              <?php
              @session_start();
              $controle_estoque = $_SESSION['controle_estoque'];
              $empresa = $_SESSION['id_cliente'];

              if ($controle_estoque == 'I' && ($empresa == '97174041604' || $empresa == '71746307668' || $empresa == '04527017000152' || $empresa == '10956925774')) :
              ?>
                <li id="opc201"><a class="" href="form_selecao_matrizes.php">Seleção de Fêmeas</a></li>
                <li id="opc201d"><a style="color:#666666" href="#">Seleção de Fêmeas</a></li>
                <li id="opc202"><a class="" href="form_cobertura_animais.php">Protocolo IATF</a></li>
                <li id="opc202d"><a style="color:#666666" href="#">Protocolo IATF</a></li>
                <li id="opc204"><a class="" href="form_cobertura_animais_diagnostico.php">Diagnóstico</a></li>
                <li id="opc204d"><a style="color:#666666" href="#">Diagnóstico</a></li>
              <?php

              endif;
              ?>

              <li id="opc203"><a class="" href="form_nascimento_animais.php">Nascimento</a></li>
              <li id="opc203d"><a style="color:#666666" href="#">Nascimento</a></li>

              <!--  <li id="opc204"><a class="" href="aborto.php">Aborto</a></li>

             
              <li id="opc204"><a class="" href="#">Desmama</a></li>
              <li id="opc204d"><a style="color:#666666" href="#">Desmama</a></li>
            -->
            </ul>
          </li>
          <!--
          <li class="sub-menu">
            <a href="javascript:;" class="">
                <i class="fas fa-capsules"></i>
                <span>Supl Alimentar</span>
                <span class="menu-arrow arrow_carrot-right"></span>
            </a>
            <ul class="sub">
              <li id="opc301"><a class="" href="#">###</a></li>
              <li id="opc301d"><a style="color:#666666" href="#">###</a></li>
              <li id="opc302"><a class="" href="#">###</a></li>
              <li id="opc302d"><a style="color:#666666" href="#">###</a></li>
            </ul>
          </li>

          <li class="sub-menu">
            <a href="javascript:;" class="">
                <i class="fas fa-syringe"></i>
                <span>Controle Sanitário</span>
                <span class="menu-arrow arrow_carrot-right"></span>
            </a>
            <ul class="sub">
              <li id="opc401"><a class="" href="#">###</a></li>
              <li id="opc401d"><a style="color:#666666" href="#">###</a></li>
              <li id="opc402"><a class="" href="#">###</a></li>
              <li id="opc402d"><a style="color:#666666" href="#">###</a></li>
            </ul>
          </li>
-->
          <li class="sub-menu">
            <a href="javascript:;" class="">
              <i class="icon_cog"></i>
              <span>Gestão Administrativa</span>
              <span class="menu-arrow arrow_carrot-right"></span>
            </a>
            <ul class="sub">
              <li id="opc502"><a class="" href="form_contas_pagar.php?reset=1">Contas a Pagar</a></li>
              <li id="opc502d"><a style="color:#666666" href="#">Contas a Pagar</a></li>
              <li id="opc504"><a class="" href="form_contas_receber.php">Contas a Receber</a></li>
              <li id="opc504d"><a style="color:#666666" href="#">Contas a Receber</a></li>
              <li id="opc501"><a class="" href="form_compra_venda_animais.php">Compra/Vendas Animais</a></li>
              <li id="opc501d"><a style="color:#666666" href="#">Compra/Venda Animais</a></li>
              <li id="opc506"><a class="" href="form_previsao_contas.php">Previsão de Contas</a></li>
              <li id="opc506d"><a style="color:#666666" href="#">Previsão de Contas</a></li>
              <li id="opc505"><a class="" href="form_agenda.php">Agenda de Atividades</a></li>
              <li id="opc505d"><a style="color:#666666" href="#">Agenda de Atividades</a></li>
              <!--  
              <li id="opc507"><a class="" href="#">###</a></li>
              <li id="opc507d"><a style="color:#666666" href="#">###</a></li>
            -->
            </ul>
          </li>

          <li class="sub-menu">
            <a href="javascript:;" class="">
              <i class="icon_documents_alt"></i>
              <span>Relatórios</span>
              <span class="menu-arrow arrow_carrot-right"></span>
            </a>
            <ul class="sub">
              <li id="opc901"><a class="" href="form_relatorios_produtivos.php">Produtivos</a></li>
              <li id="opc901d"><a style="color:#666666" href="#">Produtivos</a></li>

              <li id="opc903"><a class="" href="form_relatorios_financeiros.php">Financeiros</a></li>
              <li id="opc903d"><a style="color:#666666" href="#">Financeiros</a></li>

              <li id="opc902"><a class="" href="form_relatorios_estrategicos.php">Painel Estratégico</a></li>
              <li id="opc902d"><a style="color:#666666" href="#">Painel Estratégico</a></li>

            </ul>
          </li>
          <!--
          <li class="sub-menu">
            <a href="javascript:;" class="">
                <i class="icon_documents_alt"></i>
                <span>Rel Financeiros</span>
                <span class="menu-arrow arrow_carrot-right"></span>
            </a>
            <ul class="sub">
              <li id="opc411"><a class="" href="form_rel_fluxo_caixa.php">Fluxo de Caixa</a></li>
              <li id="opc411d"><a style="color:#666666" href="#">Fluxo de Caixa</a></li>
              <li id="opc412"><a class="" href="form_rel_analise_pagamento.php">Análise Pagamentos</a></li>
              <li id="opc412d"><a style="color:#666666" href="#">Análise Pagamentos</a></li>
              <li id="opc413"><a class="" href="form_rel_analise_recebimento.php">Análise Recebimentos</a></li>
              <li id="opc413d"><a style="color:#666666" href="#">Análise Recebimentos</a></li>
            </ul>
          </li>
-->

          <li class="sub-menu">
            <a href="javascript:;" class="">
              <i class="icon_pencil"></i>
              <span>Cadastros</span>
              <span class="menu-arrow arrow_carrot-right"></span>
            </a>
            <ul class="sub">
              <li id="opc701"><a class="" href="form_cliente_fornecedor.php">Pessoas</a></li>
              <li id="opc701d"><a style="color:#666666" href="#">Pessoas</a></li>

              <?php
              @session_start();
              $controle_estoque = $_SESSION['controle_estoque'];
              $empresa = $_SESSION['id_cliente'];

              if ($controle_estoque == 'I') :
              ?>
                <li id="opc702"><a class="" href="form_cadastro_animais.php">Animais</a></li>
                <li id="opc702d"><a style="color:#666666" href="#">Animais</a></li>
                <li id="opc704"><a class="" href="form_tabela_semens.php">Semen</a></li>
                <li id="opc704d"><a style="color:#666666" href="#">Semen</a></li>
              <?php
              endif;
              ?>

              <?php
              if ($controle_estoque == 'I' && ($empresa == '97174041604' || $empresa == '71746307668' || $empresa == '04527017000152')) :
              ?>
                <li id="opc706"><a class="" href="form_cadastro_protocolos_IATF.php">Protocolos IATF</a></li>
                <li id="opc706d"><a style="color:#666666" href="#">Protocolos IATF</a></li>

                <li id="opc707"><a class="" href="form_cadastro_embriao.php">Embrião</a></li>
                <li id="opc707d"><a style="color:#666666" href="#">Embrião</a></li>
              <?php
              endif;
              ?>

              <!--
              <li id="opc703"><a class="" href="#">Lotes de Animais</a></li>
              <li id="opc703d"><a style="color:#666666" href="#">Lotes de Animais</a></li>
-->

              <li id="opc705"><a class="" href="form_cadastro_produtos.php">Produtos</a></li>
              <li id="opc705d"><a style="color:#666666" href="#">Produtos</a></li>

            </ul>
          </li>

          <li class="sub-menu scrollbar scrollbar-primary">
            <a href="javascript:;" class="">
              <i class="icon_tools"></i>
              <span>Parâmetros</span>
              <span class="menu-arrow arrow_carrot-right"></span>
            </a>
            <ul class="sub">
              <li id="opc801"><a class="" href="form_tabela_usuarios.php">Usuários</a></li>
              <li id="opc801d"><a style="color:#666666" href="#">Usuários</a></li>
              <li id="opc806"><a class="" href="form_tabela_bancos.php">Bancos</a></li>
              <li id="opc806d"><a style="color:#666666" href="#">Bancos</a></li>
              <li id="opc805"><a class="" href="form_tabela_conta_pagamento.php">Conta Pagamento</a></li>
              <li id="opc819"><a class="" href="form_tabela_forma_pagamento.php">Forma Pagamento</a></li>
              <li id="opc819d"><a style="color:#666666" href="#">Forma Pagamento</a></li>
              <li id="opc805d"><a style="color:#666666" href="#">Conta Pagamento</a></li>
              <li id="opc810"><a class="" href="form_tabela_racas.php"><span>Raça de Animais</span></a></li>
              <li id="opc810d"><a style="color:#666666" href="#"><span>Raça de Animais</span></a></li>
              <li id="opc812"><a class="" href="form_tabela_pelagens.php">Pelagem</a></li>
              <li id="opc812d"><a style="color:#666666" href="#">Pelagem</a></li>
              <li id="opc813"><a class="" href="form_tabela_epoca_pesagem.php">Motivo da Pesagem</a></li>
              <li id="opc813d"><a style="color:#666666" href="#">Motivo da Pesagem</a></li>
              <li id="opc815"><a class="" href="form_tabela_causa_morte.php">Causa da Morte</a></li>
              <li id="opc815d"><a style="color:#666666" href="#">Causa da Morte</a></li>
              <li id="opc820"><a class="" href="form_tabela_modulo_pasto.php">Módulo Pasto</a></li>
              <li id="opc820d"><a style="color:#666666" href="#">Módulo Pasto</a></li>
              <li id="opc821"><a class="" href="form_tabela_capim.php">Tipo de Forragem</a></li>
              <li id="opc821d"><a style="color:#666666" href="#">Tipo de Forragem</a></li>
              <li id="opc803"><a class="" href="form_tabela_pastos.php">Mapa/Pastos</a></li>
              <li id="opc803d"><a style="color:#666666" href="#">Mapa/Pastos</a></li>
              <li id="opc809"><a class="" href="form_tabela_tipo_documento.php">Tipos de Documentos</a></li>
              <li id="opc809d"><a style="color:#666666" href="#">Tipos de Documentos</a></li>
              <li id="opc817"><a class="" href="form_tabela_unidade_produtos.php">Unidade de Produtos</a></li>
              <li id="opc817d"><a style="color:#666666" href="#">Unidade de Produtos</a></li>
              <li id="opc822"><a class="" href="form_tabela_atividade_padrao.php">Atividade Padrão</a></li>
              <li id="opc822d"><a style="color:#666666" href="#">Atividade Padrão</a></li>
              <li id="opc811"><a class="" href="form_tabela_categoria_idade.php">Categorias</a></li>
              <li id="opc811d"><a style="color:#666666" href="#">Categorias</a></li>
              <li id="opc814"><a class="" href="form_tabela_procedimento_sanitario.php">Procedimentos Sanitários</a></li>
              <li id="opc814d"><a style="color:#666666" href="#">Procedimentos Sanitários</a></li>
              <li id="opc800"><a class="" href="form_empresas.php">Empresa</a></li>
              <li id="opc800d"><a style="color:#666666" href="#">Empresa</a></li>
              <li id="opc802"><a class="" href="form_tabela_grupo_acessos.php"><span>Grupos de Acesso</span></a></li>
              <li id="opc802d"><a style="color:#666666" href="#"><span>Grupos de Acesso</span></a></li>
              <li id="opc804"><a class="" href="form_tabela_tipo_pessoas.php"><span>Classe de Pessoas</span></a></li>
              <li id="opc804d"><a style="color:#666666" href="#"><span>Classe de Pessoas</span></a></li>
              <li id="opc807"><a class="" href="form_tabela_plano_contas.php">Plano de Contas</a></li>
              <li id="opc807d"><a style="color:#666666" href="#">Plano de Contas</a></li>
              <li id="opc808"><a class="" href="form_tabela_centro_custos.php">Centro de Custos</a></li>
              <li id="opc808d"><a style="color:#666666" href="#">Centro de Custos</a></li>
              <li id="opc816"><a class="" href="form_tabela_grupo_produtos.php">Grupos de Produtos</a></li>
              <li id="opc816d"><a style="color:#666666" href="#">Grupos de Produtos</a></li>
              <!--<li id="opc818"><a class="" href="form_tabela_via_uso_produtos.php">Via de Uso de Produtos</a></li>-->
              <li id="opc818"><a class="" href="form_cadastro_busca.php">Ajuda</a></li>
              <li id="opc818d"><a style="color:#666666" href="#">Ajuda</a></li>

            </ul>
          </li>

        </ul>
        <!-- sidebar menu end-->
      </div>
    </aside>