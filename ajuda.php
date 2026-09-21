
        <div class="modal fade" id="ajuda" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true" data-backdrop="false">
            <div class="modal-dialog modal-sm" role="document" style="float: right; width: 30%">
                <div class="modal-content">
                
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h3 class="modal-title" id="myModalLabel" style="color:#0a5074; font-size: 16px">Orientações de Uso</h3>
                    </div>

                    <div class="modal-body nao-pode-selecionar">
                    	<a href="#" data-toggle="modal" data-target="#inicio"><h5><p style="color:#002060">Início</p></h5></a>

                    	<a href="#" data-toggle="modal" data-target="#mapa_gado"><h5><p style="color:#002060">Animais - Mapa de Gado</p></h5></a>

			            <?php
			                @ session_start(); 
			                $controle_estoque = $_SESSION['controle_estoque'];
			                $empresa = $_SESSION['id_cliente'];

			                if ($controle_estoque=='I') :
			            ?>
	                    	<a href="#" data-toggle="modal" data-target="#pesagem_id"><h5><p style="color:#002060">Animais - Pesagem</p></h5></a>

	                    	<a href="#" data-toggle="modal" data-target="#compra_id"><h5><p style="color:#002060">Animais - Movimentações - Compra</p></h5></a>

	                    	<a href="#" data-toggle="modal" data-target="#venda_id"><h5><p style="color:#002060">Animais - Movimentações - Venda</p></h5></a>

	                    	<a href="#" data-toggle="modal" data-target="#transferencia_id"><h5><p style="color:#002060">Animais - Movimentações - Transferência</p></h5></a>

	                    	<a href="#" data-toggle="modal" data-target="#morte_id"><h5><p style="color:#002060">Animais - Movimentações - Morte</p></h5></a>

	                    	<a href="#" data-toggle="modal" data-target="#outras_id"><h5><p style="color:#002060">Animais - Movimentações - Outras Saídas</p></h5></a>

	                    	<a href="#" data-toggle="modal" data-target="#nutricao_id"><h5><p style="color:#002060">Animais - Nutrição</p></h5></a>

	                    	<a href="#" data-toggle="modal" data-target="#nascimento_id"><h5><p style="color:#002060">Reprodução - Nascimento </p></h5></a>

			            <?php
			                endif;
			            ?>

						<?php
			                if ($controle_estoque=='L') :
			            ?>

                    		<a href="#" data-toggle="modal" data-target="#pesagem_lote"><h5><p style="color:#002060">Animais - Pesagem</p></h5></a>

	                    	<a href="#" data-toggle="modal" data-target="#compra_lote"><h5><p style="color:#002060">Animais - Movimentações - Compra</p></h5></a>

	                    	<a href="#" data-toggle="modal" data-target="#venda_lote"><h5><p style="color:#002060">Animais - Movimentações - Venda</p></h5></a>

	                    	<a href="#" data-toggle="modal" data-target="#transferencia_lote"><h5><p style="color:#002060">Animais - Movimentações - Transferência</p></h5></a>

	                    	<a href="#" data-toggle="modal" data-target="#morte_lote"><h5><p style="color:#002060">Animais - Movimentações - Morte</p></h5></a>

	                    	<a href="#" data-toggle="modal" data-target="#outras_lote"><h5><p style="color:#002060">Animais - Movimentações - Outras Saídas</p></h5></a>

	                    	<a href="#" data-toggle="modal" data-target="#nascimento_lote"><h5><p style="color:#002060">Reprodução - Nascimento </p></h5></a>
                    	
			            <?php
			                endif;
			            ?>

                    	<a href="#" data-toggle="modal" data-target="#contas_pagar"><h5><p style="color:#002060">Gestão Administrativa - Contas a Pagar</p></h5></a>

                    	<a href="#" data-toggle="modal" data-target="#contas_receber"><h5><p style="color:#002060">Gestão Administrativa - Contas a Receber</p></h5></a>

                    </div>

                    <div class="modal-footer">
                        <button data-dismiss="modal" type="button" class="btn btn-default">Fechar</button>
                    </div>
                </div>
            </div>  
        </div>  


        <div class="modal fade" id="inicio" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true" data-backdrop="false">
            <div class="modal-dialog modal-sm" role="document" style="float: right; width: 30%">
                <div class="modal-content">
                
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h3 class="modal-title" id="myModalLabel" style="color:#0a5074; font-size: 16px">Início</h3>
                    </div>

                    <div class="modal-body nao-pode-selecionar" style="color:#000; font-size: 11px;">
						<p style="text-align: justify;">O que devo fazer primeiro para melhor utilização do programa? 
                        </p>                    	

						<strong><em><p style="color:#002060">NO MENU PARÂMETROS</p></em></strong>

						<strong><p>Bancos</p></strong>

						<p style="text-align: justify; color:#808080;"><span>Vá ao menu lateral </span><span style="color:#00008B;">Parâmetros > Bancos > Incluir novo</span> e cadastre o código oficial do seu banco e o nome. Exemplo: 756 - SICOOB <span></span></p>												
						<p style="text-align: justify; color:#808080;">Para que? Para que você possa registrar seus pagamentos e recebimentos destinando a um banco e controlando seu fluxo de caixa </p>																		                    	
						<strong><p>Contas para pagamento</p></strong>

						<p style="text-align: justify; color:#808080;"><span>Vá ao menu lateral </span><span style="color:#00008B;">Parâmetros > Conta Pagamento > Incluir nova</span><span> e cadastre uma descrição para sua conta e escolha o tipo de conta. Para Conta Corrente ou poupança, informe: Banco, Agência, conta e Saldo inicial. Para Caixa informe o saldo.</span></p>	

						<p style="text-align: justify; color:#808080;">Para que? Para que você possa registrar destino e forma de seus pagamentos e recebimentos controlando seu fluxo de caixa. </p>	

						<strong><p>Pastos</p></strong>

						<p style="text-align: justify; color:#808080;">Cadastre os pastos de sua(s) fazendas e controle a movimentação dos seus animais, estoque e tempo de permanência em cada pasto. </p>	

						<p style="text-align: justify; color:#808080;"><span>Vá ao menu lateral </span><span style="color:#00008B;"> Parâmetros > Pastos > Incluir novo.</span><span> Informe: Local (sua fazenda); Módulo* (n° já cadastrado de acordo com a rotação de pastos que você faz), Descrição (nome que dá ao pasto ou piquete), Tipo de forragem (selecionar a partir de cadastro ou cadastrar mais), tamanho do pasto em hectares.</span></p>	

						<p style="text-align: justify; color:#808080;">Para que? Controle da distribuição dos animais, estoque, tempo de permanência, distribuição da alimentação</p>	

						<p style="text-align: justify; color:#808080; font-size: 9px;">*O sistema possui 10 módulos cadastrados também no menu Parâmetros > Módulos. Se você precisar basta cadastrar mais. Selecione os pastos indicando o mesmo módulo para aquele grupo de pastos em que você gira seus animais e faça o cadastro em uma ordem de localização na sua fazenda. </p>	

						<p>&nbsp;</p>

						<strong><em><p style="color:#002060">NO MENU ANIMAIS</p></em></strong>

						<strong><p>Acerto Inicial do Estoque</p></strong>

						<p style="text-align: justify; color:#808080;"><span>Vá ao menu lateral </span><span style="color:#00008B;"> Animais> Movimentações > Nova Movimentação</span><span> e selecionar a opção COMPRA</span></p>	

						<p style="text-align: justify; color:#808080;"><span>Em </span><span style="color:#0000FF;">*Local de Origem </span><span>selecione “Acerto Inicial de Estoque”</span></p>	

						<p style="text-align: justify; color:#808080;"><span>Em </span><span style="color:#0000FF;">*Local de Destino </span><span>selecione “Nome da sua fazenda”</span></p>	

						<p style="text-align: justify; color:#808080;">Digite a quantidade total de animais da Fazenda e depois vá preenchendo as quantidades por categoria:</p>	

						<p style="text-align: justify; color:#808080;"><span style="color:#0000FF;">*Categoria, *Idade em meses </span> (aproximado), <span style="color:#0000FF;">*sexo</span>, Raça (não é obrigatório, <span style="color:#0000FF;">*Quantidade</span> (total daquela categoria).</p>	

						<p style="text-align: justify; color:#808080;">Ir preenchendo até completar o total de animais.</p>	

						<strong><p style="color:#002060">Confirmar digitação e finalizar</p></strong>

						<p>&nbsp;</p>

						<p style="text-align: justify;">OPCIONAL 
                        </p>                    	

						<strong><em><p style="color:#002060">NO MENU CADASTROS</p></em></strong>

						<strong><p>Pessoas</p></strong>

						<p style="text-align: justify; color:#808080;"><span>Vá ao menu lateral </span><span style="color:#00008B;">Cadastros > Pessoas > Incluir novo</span></p>						

						<p style="text-align: justify; color:#808080;">Preencha todos os dados solicitados que têm o *(asterisco). Os outros dados não são obrigatórios.</p>	

						<p style="text-align: justify; color:#808080;"><span>Clique em </span><span style="color:#002060;">Confirmar a Inclusão</span> e Fechar</p>						
						<p style="text-align: justify; color:#808080;">Para que? Manter atualizado contatos de Produtores, Clientes e Fornecedores com os quais você se relaciona.</p>	

						<p style="text-align: justify; color:#808080;">Cadastre o <span style="color:#000;">tipo Produtor:</span> para que o sistema registre a origem ou o destino dos seus animais no momento da Compra e Venda de Animais.</p>	

						<p style="text-align: justify; color:#808080;">Cadastre o <span style="color:#000;">tipo Cliente:</span> para que o sistema registre a origem ou o destino dos seus animais no momento da Venda de Animais.</p>	

						<p style="text-align: justify; color:#808080;">Cadastre o <span style="color:#000;">tipo Fornecedor:</span> para que o sistema registre para onde foi seu pagamento de contas.</p>	
					</div>

                    <div class="modal-footer">
                        <button data-dismiss="modal" type="button" class="btn btn-default">Fechar</button>
                    </div>
                </div>
            </div>  
        </div>  

        <div class="modal fade" id="mapa_gado" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true" data-backdrop="false">
            <div class="modal-dialog modal-sm" role="document" style="float: right; width: 30%">
                <div class="modal-content">
                
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h3 class="modal-title" id="myModalLabel" style="color:#0a5074; font-size: 16px">Mapa de Gado</h3>
                    </div>

                    <div class="modal-body nao-pode-selecionar" style="color:#000; font-size: 11px;">

						<p style="text-align: justify; color:#808080;">Na tela inicial do programa clicar no quadro <span style="color:#00008B;">Mapa de Gado </span> em cima do nome de sua Fazenda ou acessar o menu <span style="color:#00008B;">Animais > Mapa de Gado</span> e selecionar o nome da sua Fazenda.</p>	

						<p style="text-align: justify; color:#808080;">O Mapa de Gado te auxilia a distribuir e visualizar a localização <span style="color:#0000FF;">de animais</span> por pasto/piquetes, acompanhar tempo de permanência <span style="color:#0000FF;">dos animais</span>, registrar <span style="color:#0000FF;">nascimento e morte</span> de animais no local onde ocorrerem, bem como registrar a <span style="color:#0000FF;">distribuição de nutrientes.</span> </p>			

						<strong><p>Como Funciona?</p></strong>

						<p><span style="color:#000;">PARA DISTRIBUIR OS ANIMAIS</span><span style="color:#808080;"> NOS PASTOS OU PIQUETES</span></p>

						<p style="text-align: justify; color:#808080;">1 - Vá ao menu <span style="color:#00008B;">Movimentação > Compra > Nova Movimentação</span> e selecione a opção Compra e Acerto Inicial de estoque, para dar entrada nos animais de sua fazenda – Veja também na ajuda <span style="color:#00008B;">Início</span> como criar todos os piquetes ou pastos da sua fazenda.</p>

						<p style="text-align: justify; color:#808080;">Após fazer o acerto inicial, todos os animais estarão representados no Mapa no primeiro quadro "Curral Entrada"</p>	

						<p style="text-align: justify; color:#808080;">2 - Clique no “Curral Entrada” e vá distribuindo a quantidade de animais por categoria no(s) novo(s) pasto(s) onde eles estão ocupando naquele momento na fazenda. Clique em Confirmar para validar cada transferência, até que todos os animais estejam em seus devidos locais.</p>

						<p style="text-align: justify; color:#808080;">3 - A partir desta primeira distribuição, sempre que um grupo inteiro mudar de um pasto para outro arraste os animais com mouse ou com o dedo. Responder <span style="color:#0000FF;">OK</span> à pergunta <em>“Deseja mover todos os animais do pasto...?”</em>, para confirmar a operação.</p>

						<p style="text-align: justify; color:#808080;">4 - Se apenas <span style="color:#000;">alguns animais</span> mudarem de pasto, clique no pasto de onde sairão os animais e digite a <span style="color:#0000FF;">quantidade</span> por categoria e selecione o <span style="color:#0000FF;">novo pasto</span> para onde vai aquela quantidade. Clique em <span style="color:#0000FF;">Confirmar</span>.</p>

						<p style="text-align: justify; color:#808080;"><span style="color:#000;">DICA:</span> Você pode dar nome a um lote de animais descrevendo o grupo no campo de observação do pasto.</p>

						<p><span style="text-align: justify; color:#002060;"><em>PARA REGISTRAR DISTRIBUIÇÃO DE NUTRIENTES</em></span></p>

						<p style="text-align: justify; color:#808080;">Veja na ajuda <span style="color:#00008B;">Nutrição</span> como fazer.</p>

						<p><span style="text-align: justify; color:#002060;"><em>PARA REGISTRAR NASCIMENTOS</em></span></p>

						<p style="text-align: justify; color:#808080;">Veja na ajuda <span style="color:#00008B;">Reprodução - Nascimentos</span> como fazer.</p>

						<p><span style="text-align: justify; color:#002060;"><em>PARA REGISTRAR MORTES</em></span></p>

						<p style="text-align: justify; color:#808080;">Veja na ajuda <span style="color:#00008B;">Movimentações - Morte</span> como fazer.</p>

						<strong><p style="text-align: justify; color:red;">IMPORTANTE:</p></strong>

						<p style="text-align: justify; color:red;">O mapa calcula os dias que um grupo de animais permaneceu em um pasto para que você tenha uma referência de quando manejar. O ideal é registrar a distribuição do gado e a distribuição de nutrientes no mesmo em que ela ocorreu.</p>	

						<p>&nbsp;</p>

						<strong><p>Para que usar esta funcionalidade?</p></strong>

						<strong><p>PARA CONSEGUIR VISUALIZAR SEUS ANIMAIS NO PASTO E TER MAIOR CONTROLE DO MOMENTO DE MANEJO: </p></strong>

						<p style="text-align: justify; color:#808080;">Você poderá consultar o menu <span style="color:#00008B;">Relatórios > Produtivos > Mapa de Gado</span> a localização dos seus animais e salvar relatório para comparar com a próxima mudança.</p>	

						<strong><p>PARA MANTER TODO CONTROLE DE ESTOQUE E MOVIMENTAÇÃO DOS SEUS ANIMAIS EM UM ÚNICO LUGAR NO CELULAR E EM TEMPO REAL</p></strong>

						<strong><p>PARA OBTER INFORMAÇÕES SOBRE CONSUMO DE PRODUTOS DE NUTRIÇÃO POR CABEÇA:</p></strong>

						<p style="text-align: justify; color:#808080;">Consulte diariamente como seu pessoal de campo está distribuindo seus suplementos minerais ou rações em <span style="color:#00008B;">Animais > Nutrição</span> e Consultar. Você poderá também emitir relatórios por período e calcular seu consumo de produtos, basta ir em <span style="color:#00008B;">Relatórios > Produtivos > Nutrição.</span></p>	

						<strong><p>Tire um ótimo proveito dessas informações! <i class='far fa-smile'></i></p></strong>
					</div>

                    <div class="modal-footer">
                        <button data-dismiss="modal" type="button" class="btn btn-default">Fechar</button>
                    </div>
                </div>
            </div>  
        </div>  

        <div class="modal fade" id="pesagem_lote" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true" data-backdrop="false">
            <div class="modal-dialog modal-sm" role="document" style="float: right; width: 30%">
                <div class="modal-content">
                
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h3 class="modal-title" id="myModalLabel" style="color:#0a5074; font-size: 16px">Animais - Pesagem</h3>
                    </div>

                    <div class="modal-body nao-pode-selecionar" style="color:#000; font-size: 11px;">

						<strong><p>Como Funciona?</p></strong>

						<p style="text-align: justify; color:#808080;"><span>Clicar no menu </span><span style="color:#00008B;">Animais > Pesagem > Nova Pesagem</span> e digitar a data;</p>											

						<p style="text-align: justify; color:#808080;">Selecionar a fazenda, o pasto onde estão os animais que irá pesar e o Motivo da pesagem. 
						Caso queira especificar mais os animais que irá pesar é só clicar em <span style="color:#000;">Filtrar.</span></p>							

						<p>&nbsp;</p>	

						<strong><p>PARA PESAGEM ONLINE</p></strong>

						<p style="text-align: justify; color:#808080;">1 - Clique em <span style="color:#000;">Pesagem online</span> para locais com sinal de internet;</p>

						<p style="text-align: justify; color:#808080;">2 - Digitar a quantidade total de animais que quer pesar, a categoria do animal de acordo com a idade, a quantidade de animais que está pesando (um ou mais de um) e o peso total. O sistema fará a média do peso individual de cada animal.</p>

						<p style="text-align: justify; color:#808080;">3 - Ao terminar a pesagem ou a qualquer momento que queira conferir o que já pesou clique em <span style="color:#000;">Pausar pesagem</span> e finalmente, <span style="color:#000;">Finalizar.</span></p>

						<p style="text-align: justify; color:#808080;">Somente assim você terá sua pesagem gravada.</p>																		     
						<p>&nbsp;</p>	
											               	
						<strong><p>PARA PESAGEM OFFLINE</p></strong>

						<p style="text-align: justify; color:#808080;">1 - Ainda quando estiver online, clique em <span style="color:#000;">Pesagem offline</span> e gere uma lista em Excel dos animais de sua fazenda e pasto.  </p>

						<p style="text-align: justify; color:#808080;">Se <span style="color:#000;">Filtrar</span>, só irão aparecer os animais que especificou no filtro que estão naquele pasto. EX: se machos de 08 a 12 meses, somente animais com esta classificação e que estão naquele pasto vão aparecer na lista.</p>

						<p style="text-align: justify; color:#808080;">2 - Salve sua lista em uma pasta conhecida e com nome que lhe ajudará a reconhecê-la depois; Ex: pesagem_individual_000000121 – machos 08 a 12.</p>

						<p style="text-align: justify; color:#808080;">3 - Abra a lista no local que foi salva e registre o peso dos animais diretamente nela, na coluna peso, ou imprima, anote o peso e transcreva para a lista depois. (Importante que cada célula peso deve conter o peso de um único animal).</p>

						<p style="text-align: justify; color:#808080;">4 - Depois de registrado o peso na lista Excel salve o arquivo.</p>

						<p style="text-align: justify; color:#808080;">5 - Em ambiente online novamente, vá até o menu de <span style="color:#000;">Pesagem</span> e clique em <span style="color:#000;">Consultar.</span></p>

						<p style="text-align: justify; color:#808080;">6 - Aparecerá em vermelho a lista que você gerou. Clique na ação <i class='far fa-file-excel'></i> e fazer o upload escolhendo o arquivo correspondente ao nome dado por você na geração da lista.</p>

						<p>&nbsp;</p>	
											               	
						<strong><p>Para que usar esta funcionalidade?</p></strong>

						<strong><p>PARA ACOMPANHAR O GANHO DE PESO DOS ANIMAIS:</p></strong>

						<p style="text-align: justify; color:#808080;">A pesagem ajudará você a acompanhar o ganho de peso dos seus animais de forma individual ou coletiva (ganho de peso por categoria).</p>													
						<p style="text-align: justify; color:#808080;">Para obter esta informação pese seus animais pelo menos 2 vezes (ao início e ao fim do período em que você quer acompanhar ao ganho) escolhendo o *Motivo: Controle Ganho de Peso, para esta pesagem. No menu <span style="color:#00008B;">Relatório > Produtivo > Ganho de peso</span>, você poderá obter o GMD dos animais no período e categorias pesadas.</p>													
						<p style="text-align: justify; color:#808080;">Todas as listas de pesagens podem ser exportadas em Excel e você pode acompanhar e calcular os indicadores que quiser.</p>	

						<strong><p>Tire um ótimo proveito dessas informações! <i class='far fa-smile'></i></p></strong>
					</div>

                    <div class="modal-footer">
                        <button data-dismiss="modal" type="button" class="btn btn-default">Fechar</button>
                    </div>
                </div>
            </div>  
        </div>  

        <div class="modal fade" id="pesagem_id" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true" data-backdrop="false">
            <div class="modal-dialog modal-sm" role="document" style="float: right; width: 30%">
                <div class="modal-content">
                
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h3 class="modal-title" id="myModalLabel" style="color:#0a5074; font-size: 16px">Animais - Pesagem</h3>
                    </div>

                    <div class="modal-body nao-pode-selecionar" style="color:#000; font-size: 11px;">
						<strong><p>Como Funciona?</p></strong>

						<p style="text-align: justify; color:#808080;"><span>Clicar no menu </span><span style="color:#00008B;">Animais > Pesagem > Nova Pesagem</span> e digitar a data;</p>											

						<p style="text-align: justify; color:#808080;">Selecionar a <span style="color:#00008B;">* fazenda</span> e o <span style="color:#00008B;">* Motivo da pesagem</span>. Caso queira especificar mais os animais que irá pesar é só clicar em <span style="color:#000;">Filtrar.</span></p>							

						<p>&nbsp;</p>
						<strong><p>PARA PESAGEM ONLINE</p></strong>

						<p style="text-align: justify; color:#808080;">1 - Clique em <span style="color:#000;">Pesagem online</span> para locais com sinal de internet;</p>

						<p style="text-align: justify; color:#808080;">2 - Digitar a <span style="color:#00008B;">* Descrição do Lote</span>, a <span style="color:#00008B;">* Quantidade</span> total de animais que quer pesar, a <span style="color:#00008B;">* Identificação</span> Individual de cada um e o seu respectivo <span style="color:#00008B;">* Peso</span>.</p>

						<p style="text-align: justify; color:#808080;">3 - Ao terminar a pesagem ou a qualquer momento que queira conferir o que já pesou clique em <span style="color:#00008B;">Pausar pesagem</span> e finalmente, <span style="color:#00008B;">Finalizar.</span></p>

						<p style="text-align: justify; color:#808080;">Somente assim você terá sua pesagem gravada.</p>																		     
						<p>&nbsp;</p>	
											               	
						<strong><p>PARA PESAGEM OFFLINE</p></strong>

						<p style="text-align: justify; color:#808080;">1 - Ainda quando estiver online, clique em <span style="color:#00008B;">Pesagem offline</span> e gere uma lista em Excel dos animais de sua fazenda.</p>

						<p style="text-align: justify; color:#808080;">Se <span style="color:#000;">Filtrar</span>, só irão aparecer os animais que especificou no filtro. EX: Machos de 08 a 12 meses, somente animais com esta classificação irão aparecer na lista.</p>

						<p style="text-align: justify; color:#808080;">2 - Salve sua lista em uma pasta conhecida e com nome que lhe ajudará a reconhecê-la depois; Ex: pesagem_individual_000000121 – machos 08 a 12.</p>

						<p style="text-align: justify; color:#808080;">3 - Abra a lista no local que foi salva e registre o peso dos animais diretamente nela, na coluna peso, ou imprima, anote o peso e transcreva para a lista depois. (Importante que cada célula peso deve conter o peso de um único animal).</p>

						<p style="text-align: justify; color:#808080;">4 - Depois de registrado o peso na lista Excel salve o arquivo.</p>

						<p style="text-align: justify; color:#808080;">5 - Em ambiente online novamente, vá até o menu de </span><span style="color:#00008B;">Animais > Pesagem</span> e clique em <span style="color:#00008B;">Consultar.</span></p>

						<p style="text-align: justify; color:#808080;">6 - Aparecerá em vermelho a lista que você gerou. Clique na ação <i class='far fa-file-excel'></i> e fazer o upload escolhendo o arquivo correspondente ao nome dado por você na geração da lista. Ex: pesagem_individual_000000121 – machos 08 a 12.</p>

						<p>&nbsp;</p>	
											               	
						<strong><p>Para que usar esta funcionalidade?</p></strong>

						<strong><p>PARA ACOMPANHAR O GANHO DE PESO DOS ANIMAIS:</p></strong>

						<p style="text-align: justify; color:#808080;">A pesagem ajudará você a acompanhar o ganho de peso dos seus animais de forma individual ou coletiva (ganho de peso por categoria).</p>													
						<p style="text-align: justify; color:#808080;">Para obter esta informação, pese seus animais pelo menos 2 vezes (ao início e ao fim do período em que você quer acompanhar ao ganho) escolhendo o <span style="color:#00008B;">* Motivo: Controle Ganho de Peso</span>, para esta pesagem. No menu <span style="color:#00008B;">Relatório > Produtivo > Ganho de peso</span>, você poderá obter o GMD dos animais no período e categorias pesadas.</p>	

						<strong><p>PARA CONEHCER O PESO MÉDIO DE DESMAMA DOS SEUS ANIMAIS:</p></strong>

						<p style="text-align: justify; color:#808080;">Pese seus bezerros na Desmama escolhendo o <span style="color:#00008B;">* Motivo Desmama</span>. No menu <span style="color:#00008B;">Relatório > Produtivo >Listagem de animais</span>, você obterá essa informação a partir de uma listagem completa de animais que você quer analisar a desmama.</p>	
						<strong><p>PARA CONHECER O SEU ESTOQUE ATUAL EM KG:</p></strong>

						<p style="text-align: justify; color:#808080;">A partir de qualquer pesagem por qualquer motivo você poderá visualizar a última pesagem do gado em <span style="color:#00008B;">Relatório > Produtivo > Listagem de animais</span>, gerando uma listagem completa de animais de uma fazenda sem acrescentar nenhum filtro.</p>	

						<p style="text-align: justify; color:#808080;">Todas as listas de pesagens podem ser exportadas em Excel e você pode acompanhar e calcular os indicadores que quiser.</p>

						<strong><p>Tire um ótimo proveito dessas informações! <i class='far fa-smile'></i></p></strong>

					</div>

                    <div class="modal-footer">
                        <button data-dismiss="modal" type="button" class="btn btn-default">Fechar</button>
                    </div>
                </div>
            </div>  
        </div>  

        <div class="modal fade" id="compra_lote" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true" data-backdrop="false">
            <div class="modal-dialog modal-sm" role="document" style="float: right; width: 30%">
                <div class="modal-content">
                
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h3 class="modal-title" id="myModalLabel" style="color:#0a5074; font-size: 16px">Animais - Movimentações - Compra</h3>
                    </div>

                    <div class="modal-body nao-pode-selecionar" style="color:#000; font-size: 11px;">

						<p style="text-align: justify; color:#808080;">A Movimentação Compra dá entrada nos animais de compra ou possibilita fazer o “Acerto Inicial do Estoque” garantindo a manutenção correta do estoque de sua(s) fazenda(s)</p>							

						<strong><p>Como Funciona?</p></strong>

						<p style="text-align: justify; color:#808080;"><span>Clicar no menu </span><span style="color:#00008B;">Animais > Movimentações > Nova Movimentação</span></p>											

						<p style="text-align: justify; color:#808080;">Digitar a <span style="color:#00008B;">Data da Compra</span>, clicar na opção <span style="color:#00008B;">Compra</span>, selecionar <span style="color:#00008B;">Local de Origem*, Local de Destino**</span></p>							

						<strong><p style="font-size: 10px;">ATENÇÃO: PARA ACERTO DE ESTOQUE SELECIONAR NA ORIGEM “ACERTO INICIAL DO ESTOQUE”.</p></strong>

						<p style="text-align: justify; color:#808080;">1 - Digitar a <span style="color:#00008B;">* Quantidade Total de Animais</span></p>

						<p style="text-align: justify; color:#808080;">2 - Selecionar <span style="color:#00008B;">* Categoria</span>; Digitar <span style="color:#00008B;">* Idade (meses) aproximada</span>; selecionar <span style="color:#00008B;">* Sexo</span></p>

						<p style="text-align: justify; color:#808080;">3 - Digitar o total da <span style="color:#00008B;">* Quantidade da Categoria</span></p>

						<p style="text-align: justify; color:#808080;">4 - Clicar <span style="color:#002060;">Confirma Digitação</span></p>

						<strong><p style="font-size: 10px;">Se HOUVER mais categorias e sexo para completar o total, repetir os passos de 2 a 4 até completar o total comprado. </p></strong>
 
 						<p style="text-align: justify; color:#808080;">5 - Clicar em <span style="color:#002060;">Finalizar compra</span>, para gravar e depois clicar <span style="color:#002060;">OK</span> e fechar</p>

 						<p style="text-align: justify; color:#808080;">Sua movimentação estará completa (Verificar em <span style="color:#00008B;">Consultar</span>), mas ainda estará aguardando informações de faturamento.</p>

 						<p style="text-align: justify; color:#808080;">Para faturar a compra seguir para <span style="color:#00008B;">Gestão Administrativa > Compra e Venda de Animais > Nova Compra</span>.</p>

						<p>&nbsp;</p>	

						<strong><p>Para que usar esta funcionalidade?</p></strong>

						<strong><p>PARA OBTER SEU ESTOQUE EM DIA E A MÉDIA ANUAL DO SEU ESTOQUE:</p></strong>

						<p style="text-align: justify; color:#808080;">Você poderá consultar o menu <span style="color:#00008B;">Relatórios > Produtivos > Estoque</span> e verificar todas a movimentações de entrada e saída dos seus animais e obter seu estoque médio por período a partir desta informação.</p>	

						<strong><p>PARA OBTER INFORMAÇÕES FINANCEIRAS SOBRE SUAS COMPRAS:</p></strong>

						<p style="text-align: justify; color:#808080;"> Após realizar o faturamento da compra (Gestão Administrativa > Compra e Venda de Animais > Nova compra), você poderá consultar o menu <span style="color:#00008B;">Relatórios > Financeiros > compra e venda</span> e obter informações sobre totais de animais comprados por categoria, preço médio de compra, etc.</p>	

						<p style="text-align: justify; color:#808080;">No menu <span style="color:#00008B;">Gestão Administrativa>Contas a Pagar</span> você também terá planejado o pagamento de sua compra.</p>	

						<p style="text-align: justify; color:#808080;">Todas as informações de compra podem ser exportadas para Excel e você pode acompanhar e calcular os indicadores que quiser.</p>	

						<strong><p>Tire um ótimo proveito dessas informações! <i class='far fa-smile'></i></p></strong>

						<p style="text-align: justify; color:#000; font-size: 10px;">* Local de origem é um “Produtor” ou “Fornecedor” cadastrado no menu Cadastro>Pessoas</p>	

						<p style="text-align: justify; color:#000; font-size: 10px;">**Local de Destino é a sua Fazenda para onde você está levando os animais.</p>

					</div>

                    <div class="modal-footer">
                        <button data-dismiss="modal" type="button" class="btn btn-default">Fechar</button>
                    </div>
                </div>
            </div>  
        </div>  

        <div class="modal fade" id="compra_id" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true" data-backdrop="false">
            <div class="modal-dialog modal-sm" role="document" style="float: right; width: 30%">
                <div class="modal-content">
                
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h3 class="modal-title" id="myModalLabel" style="color:#0a5074; font-size: 16px">Animais - Movimentações - Compra</h3>
                    </div>

                    <div class="modal-body nao-pode-selecionar" style="color:#000; font-size: 11px;">

						<p style="text-align: justify; color:#808080;">A Movimentação Compra dá entrada nos animais de compra ou possibilita fazer o “Acerto Inicial do Estoque” garantindo a manutenção correta do estoque de sua(s) fazenda(s)</p>							

						<strong><p>Como Funciona?</p></strong>

						<p style="text-align: justify; color:#808080;"><span>Clicar no menu </span><span style="color:#00008B;">Animais > Movimentações > Nova Movimentação</span></p>											

						<p style="text-align: justify; color:#808080;">Digitar a <span style="color:#00008B;">Data da Compra</span>, clicar na opção <span style="color:#00008B;">Compra</span>, selecionar <span style="color:#00008B;">Local de Origem*, Local de Destino**</span></p>							

						<strong><p style="font-size: 10px;">ATENÇÃO: PARA ACERTO DE ESTOQUE SELECIONAR NA ORIGEM “ACERTO INICIAL DO ESTOQUE”.</p></strong>

<span style="color:#00008B;"></span>

						<p style="text-align: justify; color:#808080;">1 - Clicar <span style="color:#000;">Sim</span> em resposta à pergunta: <span style="color:#00008B;">* Entrada rápida de animais ao cadastro?</span></p>

						<p style="text-align: justify; color:#808080;">2 - Digitar a <span style="color:#00008B;">* Quantidade Total de Animais</span></p>

						<p style="text-align: justify; color:#808080;">3 - Selecionar <span style="color:#00008B;">* Categoria</span>; Digitar <span style="color:#00008B;">* Idade (meses) aproximada</span>; selecionar <span style="color:#00008B;">* Sexo</span></p>

						<p style="text-align: justify; color:#808080;">4 - Digitar a <span style="color:#00008B;">* Raça</span> e o total da <span style="color:#00008B;">* Quantidade da Categoria</span></p>

						<p style="text-align: justify; color:#808080;">5 - Digitar uma <span style="color:#00008B;">* Sequência Numérica Inicial'</span> para os animais que está entrando (veja sugestão em Para que utilizar esta funcionalidade)</p>

						<p style="text-align: justify; color:#808080;">6 - Caso identifique seus animais com alguma sequência Alfa Numérica Digitar a informação no campo <span style="color:#00008B;">Marcação Alfanumérica</span>.</p>

						<p style="text-align: justify; color:#808080;">7 - Clicar <span style="color:#002060;">Confirma Digitação</span></p>

						<strong><p style="font-size: 10px;">Se HOUVER mais categorias e sexo para completar o total, repetir os passos de 3 a 7 até completar o total comprado. </p></strong>
 
 						<p style="text-align: justify; color:#808080;">8 - Clicar em <span style="color:#002060;">Finalizar compra</span>, para gravar e depois clicar <span style="color:#002060;">OK</span> e fechar</p>

 						<p style="text-align: justify; color:#808080;">Sua movimentação estará completa (Verificar em <span style="color:#00008B;">Consultar</span>), mas ainda estará aguardando informações de faturamento.</p>

 						<p style="text-align: justify; color:#808080;">Para faturar a compra seguir para <span style="color:#00008B;">Gestão Administrativa > Compra e Venda de Animais > Nova Compra</span>.</p>

						<p style="text-align: justify; color:#808080;">Caso você <span style="color:#000;">Não Queira</span> para Entrada Rápida de Animais, ao responder não o sitema de encaminhará ao cadastro de animais e você terá que cadastrar um a um manualmente (não recomendado)</p>

						<p>&nbsp;</p>	

						<strong><p>Para que usar esta funcionalidade?</p></strong>

						<strong><p>PARA OBTER SEU ESTOQUE EM DIA E A MÉDIA ANUAL DO SEU ESTOQUE:</p></strong>

						<p style="text-align: justify; color:#808080;">Você poderá consultar o menu <span style="color:#00008B;">Relatórios > Produtivos > Estoque</span> e verificar todas a movimentações de entrada e saída dos seus animais e obter seu estoque médio por período a partir desta informação.</p>	

						<strong><p>PARA OBTER INFORMAÇÕES FINANCEIRAS SOBRE SUAS COMPRAS:</p></strong>

						<p style="text-align: justify; color:#808080;"> Após realizar o faturamento da compra (Gestão Administrativa > Compra e Venda de Animais > Nova compra), você poderá consultar o menu <span style="color:#00008B;">Relatórios > Financeiros > compra e venda</span> e obter informações sobre totais de animais comprados por categoria, preço médio de compra, etc.</p>	

						<p style="text-align: justify; color:#808080;">No menu <span style="color:#00008B;">Gestão Administrativa>Contas a Pagar</span> você também terá planejado o pagamento de sua compra.</p>	

						<p style="text-align: justify; color:#808080;">Todas as informações de compra podem ser exportadas para Excel e você pode acompanhar e calcular os indicadores que quiser.</p>	

						<strong><p>Tire um ótimo proveito dessas informações! <i class='far fa-smile'></i></p></strong>

						<p style="text-align: justify; color:#000; font-size: 10px;">* Local de origem é um “Produtor” ou “Fornecedor” cadastrado no menu Cadastro>Pessoas</p>	

						<p style="text-align: justify; color:#000; font-size: 10px;">**Local de Destino é a sua Fazenda para onde você está levando os animais.</p>

					</div>

                    <div class="modal-footer">
                        <button data-dismiss="modal" type="button" class="btn btn-default">Fechar</button>
                    </div>
                </div>
            </div>  
        </div>  

        <div class="modal fade" id="venda_lote" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true" data-backdrop="false">
            <div class="modal-dialog modal-sm" role="document" style="float: right; width: 30%">
                <div class="modal-content">
                
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h3 class="modal-title" id="myModalLabel" style="color:#0a5074; font-size: 16px">Animais - Movimentações - Venda</h3>
                    </div>

                    <div class="modal-body nao-pode-selecionar" style="color:#000; font-size: 11px;">

						<p style="text-align: justify; color:#808080;">A Movimentação de venda de animais retira seus animais do pasto e do estoque garantindo a manutenção correta do estoque de sua(s) fazenda(s) </p>							
						<p style="text-align: justify; color:#808080;"><span style="color:red;">ATENÇÃO: PARA USAR ESTA FUNCIONALIDADE OS ANIMAIS QUE DESEJA VENDER PRECISAM ESTAR POSICIONADOS NO CURRAL DE SAIDA.</span> Veja no menu <span style="color:#000;">Mapa de Gado</span> da sua fazenda</p>		
						<strong><p>Como Funciona?</p></strong>

						<p style="text-align: justify; color:#808080;">Clicar no menu <span style="color:#00008B;">Animais > Movimentações > Nova Movimentação</span></p>											

						<p style="text-align: justify; color:#808080;">Digitar a <span style="color:#00008B;">Data da Venda</span>, clicar na opção <span style="color:#00008B;">Venda</span>, selecionar <span style="color:#00008B;">Local de Origem*, Local de Destino**</span></p>							
						<strong><p>PARA PESAGEM JÁ REALIZADA ANTES DA VENDA (OPÇÃO IDEAL)</p></strong>

						<p style="text-align: justify; color:#808080;">1 - Neste caso você irá selecionar a pesagem dos animais que está indo vender que foi realizada no menu <span style="color:#00008B;">Animais > Pesagem</span> com o motivo: <span style="color:#00008B;">Venda;</span></p>

						<p style="text-align: justify; color:#808080;">2 - Confirmar se a lista de pesagem que você selecionou corresponde aos animais que está vendendo;</p>

						<p style="text-align: justify; color:#808080;">3 - Clicar <span style="color:#00008B;">Confirmar</span> para gravar e clicar <span style="color:#00008B;">OK</span> e <span style="color:#00008B;">Fechar</span> para retorno da tela.</p>

 						<p style="text-align: justify; color:#808080;">Sua movimentação estará completa (Verificar em <span style="color:#00008B;">Consultar</span>), mas ainda estará aguardando informações de faturamento.</p>

 						<p style="text-align: justify; color:#808080;">Para faturar a venda seguir para <span style="color:#00008B;">Gestão Administrativa > Compra e Venda de Animais > Nova Venda</span>.</p>

						<p>&nbsp;</p>	

						<strong><p>PARA INICIAR DIGITAÇÃO DE ANIMAIS A SEREM VENDIDOS E QUE NÃO FORAM PESADOS</p></strong>

						<p style="text-align: justify; color:#808080;">1 - Clicar em <span style="color:#00008B;">Iniciar Digitação</span></p>

						<p style="text-align: justify; color:#808080;">2 - Digitar a <span style="color:#00008B;">Quantidade de Animais a ser vendida</span></p>

						<p style="text-align: justify; color:#808080;">3 - Depois selecionar a <span style="color:#00008B;">Categoria/Sexo</span> e a <span style="color:#00008B;">Quantidade</span> de venda por categoria/sexo selecionada. Digitar uma <span style="color:#00008B;">Observação</span> se houver e <span style="color:#00008B;">Confirmar</span></p>

						<p style="text-align: justify; color:#808080;">4 - Após finalizada a digitação clicar <span style="color:#00008B;">Pausar Digitação</span> e <span style="color:#00008B;">Finalizar.</span> Confirmar sua operação com sucesso e fechar para retornar a tela.</p>

 						<p style="text-align: justify; color:#808080;">Sua movimentação estará completa (Verificar em <span style="color:#00008B;">Consultar</span>), mas ainda estará aguardando informações de faturamento.</p>

 						<p style="text-align: justify; color:#808080;">Para faturar a venda seguir para <span style="color:#00008B;">Gestão Administrativa > Compra e Venda de Animais > Nova Venda</span>.</p>

						<p>&nbsp;</p>	

						<strong><p>Para que usar esta funcionalidade?</p></strong>

						<strong><p>PARA OBTER SEU ESTOQUE EM DIA E A MÉDIA ANUAL DO SEU ESTOQUE:</p></strong>

						<p style="text-align: justify; color:#808080;">Você poderá consultar o menu <span style="color:#00008B;">Relatórios > Produtivos > Estoque</span> e verificar todas a movimentações de entrada e saída dos seus animais e obter seu estoque médio por período a partir desta informação.</p>	

						<strong><p>PARA OBTER INFORMAÇÕES FINANCEIRAS SOBRE SUAS VENDAS:</p></strong>

						<p style="text-align: justify; color:#808080;"> Após realizar o faturamento da venda (Gestão Administrativa > Compra e Venda de Animais > Nova venda), você poderá consultar o menu <span style="color:#00008B;">Relatórios > Financeiros > compra e venda</span>e obter informações sobre totais de animais vendidos por categoria, preço médio de venda por período, venda por categoria, etc.</p>	

						<p style="text-align: justify; color:#808080;">No menu <span style="color:#00008B;">Gestão Administrativa>Contas a Receber</span> você também terá planejado o recebimento de sua venda.</p>	

						<p style="text-align: justify; color:#808080;">Todas as informações de venda podem ser exportadas para Excel e você pode acompanhar e calcular os indicadores que quiser.</p>	

						<strong><p>Tire um ótimo proveito dessas informações! <i class='far fa-smile'></i></p></strong>

						<p style="text-align: justify; color:#000; font-size: 10px;">* Local de origem é a Fazenda de onde você está retirando o animal.</p>	

						<p style="text-align: justify; color:#000; font-size: 10px;">**Local de Destino é outro “Produtor” ou “Cliente” cadastrado no menu Cadastro>Pessoas</p>
					</div>

                    <div class="modal-footer">
                        <button data-dismiss="modal" type="button" class="btn btn-default">Fechar</button>
                    </div>
                </div>
            </div>  
        </div>  

        <div class="modal fade" id="venda_id" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true" data-backdrop="false">
            <div class="modal-dialog modal-sm" role="document" style="float: right; width: 30%">
                <div class="modal-content">
                
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h3 class="modal-title" id="myModalLabel" style="color:#0a5074; font-size: 16px">Animais - Movimentações - Venda</h3>
                    </div>

                    <div class="modal-body nao-pode-selecionar" style="color:#000; font-size: 11px;">
						<p style="text-align: justify; color:#808080;">A Movimentação de venda de animais retira seus animais do pasto e do estoque garantindo a manutenção correta do estoque de sua(s) fazenda(s) </p>							
						<p style="text-align: justify; color:#808080;"><span style="color:red;">ATENÇÃO: PARA USAR ESTA FUNCIONALIDADE OS ANIMAIS QUE DESEJA VENDER PRECISAM ESTAR POSICIONADOS NO CURRAL DE SAIDA.</span> Veja no menu <span style="color:#000;">Mapa de Gado</span> da sua fazenda</p>		
						<strong><p>Como Funciona?</p></strong>

						<p style="text-align: justify; color:#808080;">Clicar no menu <span style="color:#00008B;">Animais > Movimentações > Nova Movimentação</span></p>											

						<p style="text-align: justify; color:#808080;">Digitar a <span style="color:#00008B;">Data da Venda</span>, clicar na opção <span style="color:#00008B;">Venda</span>, selecionar <span style="color:#00008B;">Local de Origem*, Local de Destino**</span></p>							
						<strong><p>PARA PESAGEM JÁ REALIZADA ANTES DA VENDA (OPÇÃO IDEAL)</p></strong>

						<p style="text-align: justify; color:#808080;">1 - Neste caso você irá selecionar a pesagem dos animais que está indo vender que foi realizada no menu <span style="color:#00008B;">Animais > Pesagem</span> com o motivo: <span style="color:#00008B;">Venda;</span></p>

						<p style="text-align: justify; color:#808080;">2 - Confirmar se a lista de pesagem que você selecionou corresponde aos animais que está vendendo;</p>

						<p style="text-align: justify; color:#808080;">3 - Clicar <span style="color:#00008B;">Confirmar</span> para gravar e clicar <span style="color:#00008B;">OK</span> e <span style="color:#00008B;">Fechar</span> para retorno da tela.</p>

 						<p style="text-align: justify; color:#808080;">Sua movimentação estará completa (Verificar em <span style="color:#00008B;">Consultar</span>), mas ainda estará aguardando informações de faturamento.</p>

 						<p style="text-align: justify; color:#808080;">Para faturar a venda seguir para <span style="color:#00008B;">Gestão Administrativa > Compra e Venda de Animais > Nova Venda</span>.</p>

						<p>&nbsp;</p>	

						<strong><p>PARA INICIAR DIGITAÇÃO DE ANIMAIS A SEREM VENDIDOS E QUE NÃO FORAM PESADOS</p></strong>

						<p style="text-align: justify; color:#808080;">1 - Clicar em <span style="color:#00008B;">Iniciar Digitação</span></p>

						<p style="text-align: justify; color:#808080;">2 - Digitar a <span style="color:#00008B;">Quantidade de Animais a ser vendida</span></p>

						<p style="text-align: justify; color:#808080;">3 - Digitar a <span style="color:#00008B;">Identificação</span> de cada animal. Digitar uma <span style="color:#00008B;">Observação</span> se houver e <span style="color:#00008B;">Confirmar</span></p>

						<p style="text-align: justify; color:#808080;">4 - Após finalizada a digitação clicar <span style="color:#00008B;">Pausar Digitação</span>, (conferir se todos os animais que serão vendidos foram digitados na lista) e <span style="color:#00008B;">Finalizar.</span> Confirmar sua operação com sucesso e fechar para retornar a tela.</p>

 						<p style="text-align: justify; color:#808080;">Sua movimentação estará completa (Verificar em <span style="color:#00008B;">Consultar</span>), mas ainda estará aguardando informações de faturamento.</p>

 						<p style="text-align: justify; color:#808080;">Para faturar a venda seguir para <span style="color:#00008B;">Gestão Administrativa > Compra e Venda de Animais > Nova Venda</span>.</p>

						<p>&nbsp;</p>	

						<strong><p>Para que usar esta funcionalidade?</p></strong>

						<strong><p>PARA OBTER SEU ESTOQUE EM DIA E A MÉDIA ANUAL DO SEU ESTOQUE:</p></strong>

						<p style="text-align: justify; color:#808080;">Você poderá consultar o menu <span style="color:#00008B;">Relatórios > Produtivos > Estoque</span> e verificar todas a movimentações de entrada e saída dos seus animais e obter seu estoque médio por período a partir desta informação.</p>	

						<strong><p>PARA OBTER INFORMAÇÕES FINANCEIRAS SOBRE SUAS VENDAS:</p></strong>

						<p style="text-align: justify; color:#808080;"> Após realizar o faturamento da venda (Gestão Administrativa > Compra e Venda de Animais > Nova venda), você poderá consultar o menu <span style="color:#00008B;">Relatórios > Financeiros > compra e venda</span>e obter informações sobre totais de animais vendidos por categoria, preço médio de venda por período, venda por categoria, etc.</p>	

						<p style="text-align: justify; color:#808080;">No menu <span style="color:#00008B;">Gestão Administrativa>Contas a Receber</span> você também terá planejado o recebimento de sua venda.</p>	

						<p style="text-align: justify; color:#808080;">Todas as informações de venda podem ser exportadas para Excel e você pode acompanhar e calcular os indicadores que quiser.</p>	

						<strong><p>Tire um ótimo proveito dessas informações! <i class='far fa-smile'></i></p></strong>

						<p style="text-align: justify; color:#000; font-size: 10px;">* Local de origem é a Fazenda de onde você está retirando o animal.</p>	

						<p style="text-align: justify; color:#000; font-size: 10px;">**Local de Destino é outro “Produtor” ou “Cliente” cadastrado no menu Cadastro>Pessoas</p>
					</div>

                    <div class="modal-footer">
                        <button data-dismiss="modal" type="button" class="btn btn-default">Fechar</button>
                    </div>
                </div>
            </div>  
        </div>  

        <div class="modal fade" id="transferencia_lote" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true" data-backdrop="false">
            <div class="modal-dialog modal-sm" role="document" style="float: right; width: 30%">
                <div class="modal-content">
                
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h3 class="modal-title" id="myModalLabel" style="color:#0a5074; font-size: 16px">Animais - Movimentações - Transferência</h3>
                    </div>

                    <div class="modal-body nao-pode-selecionar" style="color:#000; font-size: 11px;">

						<p style="text-align: justify; color:#808080;">A Movimentação de transferência de animais retira seus animais de uma  das suas  propriedade e leva para outra garantindo a manutenção correta estoque de sua(s) fazenda(s)</p>	

						<p style="text-align: justify; color:#808080;"><span style="color:red;">ATENÇÃO: PARA USAR ESTA FUNCIONALIDADE OS ANIMAIS QUE DESEJA TRANSFERIR PRECISAM ESTAR POSICIONADOS NO CURRAL DE SAIDA.</span> Veja no menu <span style="color:#000;">Mapa de Gado</span> da sua fazenda</p>		

						<strong><p>Como Funciona?</p></strong>

						<p style="text-align: justify; color:#808080;">Clicar no menu <span style="color:#00008B;">Animais > Movimentações > Nova Movimentação</span></p>											

						<p style="text-align: justify; color:#808080;">Digitar a <span style="color:#00008B;">Data da Transferência</span>, clicar na opção <span style="color:#00008B;">Transferência</span>, selecionar <span style="color:#00008B;">Local de Origem*, Local de Destino**</span></p>							
						<strong><p>PARA PESAGEM JÁ REALIZADA ANTES DA TRANSFERÊNCIA (OPÇÃO IDEAL)</p></strong>

						<p style="text-align: justify; color:#808080;">1 - Neste caso você irá selecionar a pesagem dos animais que está indo transferir que foi realizada no menu <span style="color:#00008B;">Animais > Pesagem</span> com o motivo: <span style="color:#00008B;">Transferência;</span></p>

						<p style="text-align: justify; color:#808080;">2 - Confirmar se a lista de pesagem que você selecionou corresponde aos animais que está transferindo;</p>

						<p style="text-align: justify; color:#808080;">3 - Clicar <span style="color:#00008B;">Confirmar</span> para gravar e clicar <span style="color:#00008B;">OK</span> e <span style="color:#00008B;">Fechar</span> para retorno da tela.</p>

 						<p style="text-align: justify; color:#808080;">Sua movimentação, para esta fazenda, estará completa (Verificar em <span style="color:#00008B;">Consultar</span>), mas ainda estará aguardando o aceite dos mesmos para outra fazenda para a qual você transferiu as cabeças.</p>

 						<p style="text-align: justify; color:#808080;">A pessoa que confirmar a chegada dos animais deverá clicar na Tela Inicial do programa no Quadro de categoria na mensagem “Existe transferência para confirmar” e executar a confirmação.</p>

						<p>&nbsp;</p>	

						<strong><p>PARA INICIAR DIGITAÇÃO DE ANIMAIS A SEREM TRASNFERIDOS E QUE NÃO FORAM PESADOS</p></strong>

						<p style="text-align: justify; color:#808080;">1 - Clicar em <span style="color:#00008B;">Iniciar Digitação</span></p>

						<p style="text-align: justify; color:#808080;">2 - Digitar a <span style="color:#00008B;">Quantidade de Animais a ser transferida</span></p>

						<p style="text-align: justify; color:#808080;">3 - Depois selecionar a <span style="color:#00008B;">Categoria/Sexo</span> e a <span style="color:#00008B;">Quantidade</span> de transferencia para cada categoria/sexo selecionada. Digitar uma <span style="color:#00008B;">Observação</span> se houver e <span style="color:#00008B;">Confirmar</span></p>

						<p style="text-align: justify; color:#808080;">4 - Após finalizada a digitação clicar <span style="color:#00008B;">Pausar Digitação</span> e <span style="color:#00008B;">Finalizar.</span> Confirmar sua operação com sucesso e fechar para retornar a tela.</p>

 						<p style="text-align: justify; color:#808080;">Sua movimentação, para esta fazenda, estará completa (Verificar em <span style="color:#00008B;">Consultar</span>), mas ainda estará aguardando o aceite dos mesmos para outra fazenda para a qual você transferiu as cabeças.</p>

 						<p style="text-align: justify; color:#808080;">A pessoa que confirmar a chegada dos animais deverá clicar na Tela Inicial do programa no Quadro de categoria na mensagem “Existe transferência para confirmar” e executar a confirmação.</p>

						<p>&nbsp;</p>	

						<strong><p>Para que usar esta funcionalidade?</p></strong>

						<strong><p>PARA OBTER SEU ESTOQUE EM DIA E A MÉDIA ANUAL DO SEU ESTOQUE:</p></strong>

						<p style="text-align: justify; color:#808080;">Você poderá consultar o menu <span style="color:#00008B;">Relatórios > Produtivos > Estoque</span> e verificar todas a movimentações de entrada e saída dos seus animais e obter seu estoque médio por período a partir desta informação.</p>	

						<strong><p>Tire um ótimo proveito dessas informações! <i class='far fa-smile'></i></p></strong>

						<p style="text-align: justify; color:#000; font-size: 10px;">* Local de origem é a Fazenda de onde você está retirando o animal.</p>	

						<p style="text-align: justify; color:#000; font-size: 10px;">**Local de Destino é uma fazenda de sua propriedade para onde está levando os animais.</p>

					</div>

                    <div class="modal-footer">
                        <button data-dismiss="modal" type="button" class="btn btn-default">Fechar</button>
                    </div>
                </div>
            </div>  
        </div>  

        <div class="modal fade" id="transferencia_id" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true" data-backdrop="false">
            <div class="modal-dialog modal-sm" role="document" style="float: right; width: 30%">
                <div class="modal-content">
                
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h3 class="modal-title" id="myModalLabel" style="color:#0a5074; font-size: 16px">Animais - Movimentações - Transferência</h3>
                    </div>

                    <div class="modal-body nao-pode-selecionar" style="color:#000; font-size: 11px;">

						<p style="text-align: justify; color:#808080;">A Movimentação de transferência de animais retira seus animais de uma  das suas  propriedade e leva para outra garantindo a manutenção correta estoque de sua(s) fazenda(s)</p>	

						<p style="text-align: justify; color:#808080;"><span style="color:red;">ATENÇÃO: PARA USAR ESTA FUNCIONALIDADE OS ANIMAIS QUE DESEJA TRANSFERIR PRECISAM ESTAR POSICIONADOS NO CURRAL DE SAIDA.</span> Veja no menu <span style="color:#000;">Mapa de Gado</span> da sua fazenda</p>		

						<strong><p>Como Funciona?</p></strong>

						<p style="text-align: justify; color:#808080;">Clicar no menu <span style="color:#00008B;">Animais > Movimentações > Nova Movimentação</span></p>											

						<p style="text-align: justify; color:#808080;">Digitar a <span style="color:#00008B;">Data da Transferência</span>, clicar na opção <span style="color:#00008B;">Transferência</span>, selecionar <span style="color:#00008B;">Local de Origem*, Local de Destino**</span></p>							
						<strong><p>PARA PESAGEM JÁ REALIZADA ANTES DA TRANSFERÊNCIA (OPÇÃO IDEAL)</p></strong>

						<p style="text-align: justify; color:#808080;">1 - Neste caso você irá selecionar a pesagem dos animais que está indo transferir que foi realizada no menu <span style="color:#00008B;">Animais > Pesagem</span> com o motivo: <span style="color:#00008B;">Transferência;</span></p>

						<p style="text-align: justify; color:#808080;">2 - Confirmar se a lista de pesagem que você selecionou corresponde aos animais que está transferindo;</p>

						<p style="text-align: justify; color:#808080;">3 - Clicar <span style="color:#00008B;">Confirmar</span> para gravar e clicar <span style="color:#00008B;">OK</span> e <span style="color:#00008B;">Fechar</span> para retorno da tela.</p>

 						<p style="text-align: justify; color:#808080;">Sua movimentação, para esta fazenda, estará completa (Verificar em <span style="color:#00008B;">Consultar</span>), mas ainda estará aguardando o aceite dos mesmos para outra fazenda para a qual você transferiu as cabeças.</p>

 						<p style="text-align: justify; color:#808080;">A pessoa que confirmar a chegada dos animais deverá clicar na Tela Inicial do programa no Quadro de categoria na mensagem “Existe transferência para confirmar” e executar a confirmação.</p>

						<p>&nbsp;</p>	

						<strong><p>PARA INICIAR DIGITAÇÃO DE ANIMAIS A SEREM TRASNFERIDOS E QUE NÃO FORAM PESADOS</p></strong>

						<p style="text-align: justify; color:#808080;">1 - Clicar em <span style="color:#00008B;">Iniciar Digitação</span></p>

						<p style="text-align: justify; color:#808080;">2 - Digitar a <span style="color:#00008B;">Quantidade de Animais a ser transferida</span></p>

						<p style="text-align: justify; color:#808080;">3 - Digitar a <span style="color:#00008B;">Identificação</span> de cada animal. Digitar uma <span style="color:#00008B;">Observação</span> se houver e <span style="color:#00008B;">Confirmar</span></p>

						<p style="text-align: justify; color:#808080;">4 - Após finalizada a digitação clicar <span style="color:#00008B;">Pausar Digitação</span> e <span style="color:#00008B;">Finalizar.</span> Confirmar sua operação com sucesso e fechar para retornar a tela.</p>

 						<p style="text-align: justify; color:#808080;">Sua movimentação, para esta fazenda, estará completa (Verificar em <span style="color:#00008B;">Consultar</span>), mas ainda estará aguardando o aceite dos mesmos para outra fazenda para a qual você transferiu as cabeças.</p>

 						<p style="text-align: justify; color:#808080;">A pessoa que confirmar a chegada dos animais deverá clicar na Tela Inicial do programa no Quadro de categoria na mensagem “Existe transferência para confirmar” e executar a confirmação.</p>

						<p>&nbsp;</p>	

						<strong><p>Para que usar esta funcionalidade?</p></strong>

						<strong><p>PARA OBTER SEU ESTOQUE EM DIA E A MÉDIA ANUAL DO SEU ESTOQUE:</p></strong>

						<p style="text-align: justify; color:#808080;">Você poderá consultar o menu <span style="color:#00008B;">Relatórios > Produtivos > Estoque</span> e verificar todas a movimentações de entrada e saída dos seus animais e obter seu estoque médio por período a partir desta informação.</p>	

						<strong><p>Tire um ótimo proveito dessas informações! <i class='far fa-smile'></i></p></strong>

						<p style="text-align: justify; color:#000; font-size: 10px;">* Local de origem é a Fazenda de onde você está retirando o animal.</p>	

						<p style="text-align: justify; color:#000; font-size: 10px;">**Local de Destino é uma fazenda de sua propriedade para onde está levando os animais.</p>

					</div>

                    <div class="modal-footer">
                        <button data-dismiss="modal" type="button" class="btn btn-default">Fechar</button>
                    </div>
                </div>
            </div>  
        </div>  

        <div class="modal fade" id="morte_lote" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true" data-backdrop="false">
            <div class="modal-dialog modal-sm" role="document" style="float: right; width: 30%">
                <div class="modal-content">
                
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h3 class="modal-title" id="myModalLabel" style="color:#0a5074; font-size: 16px">Animais - Movimentações - Morte</h3>
                    </div>

                    <div class="modal-body nao-pode-selecionar" style="color:#000; font-size: 11px;">

						<p style="text-align: justify; color:#808080;">A Movimentação Morte retira um animal de um pasto de sua propriedade, garantindo a manutenção correta estoque de sua(s) fazenda(s)</p>	

						<strong><p>Como Funciona?</p></strong>

						<strong><p>PARA DIGITAÇÃO DIRETA A PARTIR DO MAPA DE GADO (OPÇÃO IDEAL)</p></strong>

						<p style="text-align: justify; color:#808080;">1 - Clicar no nome da fazenda no quadro <span style="color:#00008B;">MAPA DE GADO</span> (tela inicial do programa "Home")</p>

						<p style="text-align: justify; color:#808080;">2 - Clicar no pasto em que o animal morreu está;</p>

						<p style="text-align: justify; color:#808080;">3 - Clicar no botão <span style="color:#00008B;">Morte</span>, abaixo da tela;</p>

						<p style="text-align: justify; color:#808080;">4 - Selecionar o <span style="color:#00008B;">* Motivo da Morte</span> (certifique-se de que está no pasto certo no texto acima);</p>

						<p style="text-align: justify; color:#808080;">5 - Selecionar <span style="color:#00008B;">* Data da morte</span> e <span style="color:#00008B;">* categoria e sexo</span> do animal que morreu;</p>

						<p style="text-align: justify; color:#808080;">6 - Clicar <span style="color:#00008B;">Confirmar e Fechar</span> confirmando a baixo do animal.</p>

						<p>&nbsp;</p>	

						<strong><p>PARA DIGITAÇÃO A PARTIR DO MENU ANIMAIS>MOVIMENTAÇÕES>NOVA MOVIMENTAÇÃO>MORTE</p></strong>

						<p style="text-align: justify; color:#808080;">1 -Clicar no menu <span style="color:#00008B;">Animais > Movimentações > Nova Movimentação</span></p>

						<p style="text-align: justify; color:#808080;">2 - Digitar a <span style="color:#00008B;">Data da Morte</span>, clicar na opção <span style="color:#00008B;">Morte</span>, selecionar <span style="color:#00008B;">Local de Origem*</span>;</p>

						<p style="text-align: justify; color:#808080;">3 - Selecionar <span style="color:#00008B;">* Motivo da Morte</span> e o <span style="color:#00008B;">* Pasto</span> onde o animal que morreu se encontrava e <span style="color:#00008B;">* Categoria/Sexo</span>;</p>

						<p style="text-align: justify; color:#808080;">4 - Clicar <span style="color:#00008B;">Confirmar</span>;</p>

						<p style="text-align: justify; color:#808080;">5 - Clicar em <span style="color:#00008B;">Ok</span> para a Saída do animal que digitou e <span style="color:#00008B;">Fechar</span>.</p>

 						<!--<p style="text-align: justify; color:#808080;font-size: 10px;">ATENÇÃO: O ANIMAL QUE VOCÊ QUER BAIXAR PRECISA ESTAR NO PASTO SELECIONADO NA MESMA CATEGORIA E SEXO DO ANIMAL QUE VAI BAIXAR.</p>-->

						<p>&nbsp;</p>	

						<strong><p>Para que usar esta funcionalidade?</p></strong>

						<strong><p>PARA OBTER SEU ESTOQUE EM DIA E A MÉDIA ANUAL DO SEU ESTOQUE:</p></strong>

						<p style="text-align: justify; color:#808080;">Você poderá consultar o menu <span style="color:#00008B;">Relatórios > Produtivos > Estoque</span> e verificar todas a movimentações de entrada e saída dos seus animais e obter seu estoque médio por período a partir desta informação.</p>	

						<p style="text-align: justify; color:#808080;">As informações de estoque podem ser exportadas para Excel e você pode acompanhar e calcular os indicadores que quiser.</p>	

						<strong><p>Tire um ótimo proveito dessas informações! <i class='far fa-smile'></i></p></strong>

						<p style="text-align: justify; color:#000; font-size: 10px;">* Local de origem é a Fazenda de onde você está baixando o animal.</p>	
					</div>

                    <div class="modal-footer">
                        <button data-dismiss="modal" type="button" class="btn btn-default">Fechar</button>
                    </div>
                </div>
            </div>  
        </div>  

        <div class="modal fade" id="morte_id" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true" data-backdrop="false">
            <div class="modal-dialog modal-sm" role="document" style="float: right; width: 30%">
                <div class="modal-content">
                
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h3 class="modal-title" id="myModalLabel" style="color:#0a5074; font-size: 16px">Animais - Movimentações - Morte</h3>
                    </div>

                    <div class="modal-body nao-pode-selecionar" style="color:#000; font-size: 11px;">
						<p style="text-align: justify; color:#808080;">A Movimentação Morte retira um animal de um pasto de sua propriedade, garantindo a manutenção correta estoque de sua(s) fazenda(s)</p>	

						<strong><p>Como Funciona?</p></strong>

						<strong><p>PARA DIGITAÇÃO DIRETA A PARTIR DO MAPA DE GADO (OPÇÃO IDEAL)</p></strong>

						<p style="text-align: justify; color:#808080;">1 - Clicar no nome da fazenda no quadro <span style="color:#00008B;">MAPA DE GADO</span> (tela inicial do programa "Home")</p>

						<p style="text-align: justify; color:#808080;">2 - Clicar no pasto em que o animal morreu está;</p>

						<p style="text-align: justify; color:#808080;">3 - Clicar no botão <span style="color:#00008B;">Morte</span>, abaixo da tela;</p>

						<p style="text-align: justify; color:#808080;">4 - Selecionar o <span style="color:#00008B;">* Nº do Animal</span>, o <span style="color:#00008B;">* Motivo da Morte</span> e a <span style="color:#00008B;">* Data da morte</span>. (certifique-se de que está no pasto certo no texto acima);</p>

						<p style="text-align: justify; color:#808080;">5 - Clicar <span style="color:#00008B;">Confirmar e Fechar</span> confirmando a baixo do animal.</p>

						<p>&nbsp;</p>	

						<strong><p>PARA DIGITAÇÃO A PARTIR DO MENU ANIMAIS>MOVIMENTAÇÕES>NOVA MOVIMENTAÇÃO>MORTE</p></strong>

						<p style="text-align: justify; color:#808080;">1 -Clicar no menu <span style="color:#00008B;">Animais > Movimentações > Nova Movimentação</span></p>

						<p style="text-align: justify; color:#808080;">2 - Digitar a <span style="color:#00008B;">Data da Morte</span>, clicar na opção <span style="color:#00008B;">Morte</span>, selecionar <span style="color:#00008B;">Local de Origem*</span>;</p>

						<p style="text-align: justify; color:#808080;">3 - Selecionar o <span style="color:#00008B;">* Nº do Animal</span>, o <span style="color:#00008B;">* Motivo da Morte</span> e o <span style="color:#00008B;">* Pasto</span> onde o animal que morreu se encontrava;</p>

						<p style="text-align: justify; color:#808080;">4 - Clicar <span style="color:#00008B;">Confirmar</span>;</p>

						<p style="text-align: justify; color:#808080;">5 - Clicar em <span style="color:#00008B;">Ok</span> para a Saída do animal que digitou e <span style="color:#00008B;">Fechar</span>.</p>

 						<p style="text-align: justify; color:#808080;font-size: 10px;">ATENÇÃO: O ANIMAL QUE VOCÊ QUER BAIXAR PRECISA ESTAR NO PASTO SELECIONADO NA MESMA CATEGORIA E SEXO DO Nº DO ANIMAL QUE VAI BAIXAR.</p>

						<p>&nbsp;</p>	

						<strong><p>Para que usar esta funcionalidade?</p></strong>

						<strong><p>PARA OBTER SEU ESTOQUE EM DIA E A MÉDIA ANUAL DO SEU ESTOQUE:</p></strong>

						<p style="text-align: justify; color:#808080;">Você poderá consultar o menu <span style="color:#00008B;">Relatórios > Produtivos > Estoque</span> e verificar todas a movimentações de entrada e saída dos seus animais e obter seu estoque médio por período a partir desta informação.</p>	

						<p style="text-align: justify; color:#808080;">As informações de estoque podem ser exportadas para Excel e você pode acompanhar e calcular os indicadores que quiser.</p>	

						<strong><p>Tire um ótimo proveito dessas informações! <i class='far fa-smile'></i></p></strong>

						<p style="text-align: justify; color:#000; font-size: 10px;">* Local de origem é a Fazenda de onde você está baixando o animal.</p>	
					</div>

                    <div class="modal-footer">
                        <button data-dismiss="modal" type="button" class="btn btn-default">Fechar</button>
                    </div>
                </div>
            </div>  
        </div>  

        <div class="modal fade" id="outras_lote" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true" data-backdrop="false">
            <div class="modal-dialog modal-sm" role="document" style="float: right; width: 30%">
                <div class="modal-content">
                
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h3 class="modal-title" id="myModalLabel" style="color:#0a5074; font-size: 16px">Animais - Movimentações - Outras Saídas</h3>
                    </div>

                    <div class="modal-body nao-pode-selecionar" style="color:#000; font-size: 11px;">

						<p style="text-align: justify; color:#808080;">A Movimentação Outras Saídas retira um animal um pasto de sua propriedade, garantindo a manutenção correta estoque de sua(s) fazenda(s)</p>	

						<strong><p>Como Funciona?</p></strong>

						<p style="text-align: justify; color:#808080;">1 -Clicar no menu <span style="color:#00008B;">Animais > Movimentações > Nova Movimentação</span></p>

						<p style="text-align: justify; color:#808080;">2 - Digitar a <span style="color:#00008B;">Data</span>, clicar na opção <span style="color:#00008B;">Outras Saídas</span>, selecionar <span style="color:#00008B;">Local de Origem*</span>;</p>

						<p style="text-align: justify; color:#808080;">3 - Selecionar o <span style="color:#00008B;">* Pasto</span> onde o animal esta <span style="color:#00008B;">* Categoria/Sexo</span> e <span style="color:#00008B;">* Observação</span>;</p>

						<p style="text-align: justify; color:#808080;">4 - Clicar <span style="color:#00008B;">Confirmar</span>;</p>

						<p style="text-align: justify; color:#808080;">5 - Clicar em <span style="color:#00008B;">Ok</span> para a Saída do animal que digitou e <span style="color:#00008B;">Fechar</span>.</p>

 						<!--<p style="text-align: justify; color:#808080;font-size: 10px;">ATENÇÃO: O ANIMAL QUE VOCÊ QUER BAIXAR PRECISA ESTAR NO PASTO SELECIONADO NA MESMA CATEGORIA E SEXO DO ANIMAL QUE VAI BAIXAR.</p>-->

						<p>&nbsp;</p>	

						<strong><p>Para que usar esta funcionalidade?</p></strong>

						<strong><p>SUGESTÃO:</p></strong>

						<p style="text-align: justify; color:#808080;">ESSA FUNCIONALIDADE FOI PENSADA PARA BAIXAR ANIMAIS QUE NÃO ESTÃO MAIS NO ESTOQUE, MAS NÃO OUVE NENHUM GANHO FINANCEIRO COM ELES. (perdas doações e outros acertos).</p>	

						<p style="text-align: justify; color:#808080;">Você poderá consultar o menu <span style="color:#00008B;">Relatórios > Produtivos > Estoque</span> e verificar todas a movimentações de entrada e saída dos seus animais e obter seu estoque médio por período a partir desta informação.</p>	

						<p style="text-align: justify; color:#808080;">As informações de estoque podem ser exportadas para Excel e você pode acompanhar e calcular os indicadores que quiser.</p>	

						<strong><p>Tire um ótimo proveito dessas informações! <i class='far fa-smile'></i></p></strong>

						<p style="text-align: justify; color:#000; font-size: 10px;">* Local de origem é a Fazenda de onde você está baixando o animal.</p>	

					</div>

                    <div class="modal-footer">
                        <button data-dismiss="modal" type="button" class="btn btn-default">Fechar</button>
                    </div>
                </div>
            </div>  
        </div>  

        <div class="modal fade" id="outras_id" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true" data-backdrop="false">
            <div class="modal-dialog modal-sm" role="document" style="float: right; width: 30%">
                <div class="modal-content">
                
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h3 class="modal-title" id="myModalLabel" style="color:#0a5074; font-size: 16px">Animais - Movimentações - Outras Saídas</h3>
                    </div>

                    <div class="modal-body nao-pode-selecionar" style="color:#000; font-size: 11px;">
						<p style="text-align: justify; color:#808080;">A Movimentação Outras Saídas retira um animal um pasto de sua propriedade, garantindo a manutenção correta estoque de sua(s) fazenda(s)</p>	

						<strong><p>Como Funciona?</p></strong>

						<p style="text-align: justify; color:#808080;">1 -Clicar no menu <span style="color:#00008B;">Animais > Movimentações > Nova Movimentação</span></p>

						<p style="text-align: justify; color:#808080;">2 - Digitar a <span style="color:#00008B;">Data</span>, clicar na opção <span style="color:#00008B;">Outras Saídas</span>, selecionar <span style="color:#00008B;">Local de Origem*</span>;</p>

						<p style="text-align: justify; color:#808080;">3 - Selecionar o <span style="color:#00008B;">* ID do Animal</span>, o <span style="color:#00008B;">* Pasto</span> onde o animal esta e a <span style="color:#00008B;">* Observação</span>;</p>

						<p style="text-align: justify; color:#808080;">4 - Clicar <span style="color:#00008B;">Confirmar</span>;</p>

						<p style="text-align: justify; color:#808080;">5 - Clicar em <span style="color:#00008B;">Ok</span> para a Saída do animal que digitou e <span style="color:#00008B;">Fechar</span>.</p>

 						<p style="text-align: justify; color:#808080;font-size: 10px;">ATENÇÃO: O ANIMAL QUE VOCÊ QUER BAIXAR PRECISA ESTAR NO PASTO SELECIONADO NA MESMA CATEGORIA E SEXO DO ID DO ANIMAL QUE VAI BAIXAR.</p>

						<p>&nbsp;</p>	

						<strong><p>Para que usar esta funcionalidade?</p></strong>

						<strong><p>SUGESTÃO:</p></strong>

						<p style="text-align: justify; color:#808080;">ESSA FUNCIONALIDADE FOI PENSADA PARA BAIXAR ANIMAIS QUE NÃO ESTÃO MAIS NO ESTOQUE, MAS NÃO OUVE NENHUM GANHO FINANCEIRO COM ELES. (perdas doações e outros acertos).</p>	

						<p style="text-align: justify; color:#808080;">Você poderá consultar o menu <span style="color:#00008B;">Relatórios > Produtivos > Estoque</span> e verificar todas a movimentações de entrada e saída dos seus animais e obter seu estoque médio por período a partir desta informação.</p>	

						<p style="text-align: justify; color:#808080;">As informações de estoque podem ser exportadas para Excel e você pode acompanhar e calcular os indicadores que quiser.</p>	

						<strong><p>Tire um ótimo proveito dessas informações! <i class='far fa-smile'></i></p></strong>

						<p style="text-align: justify; color:#000; font-size: 10px;">* Local de origem é a Fazenda de onde você está baixando o animal.</p>	
					</div>

                    <div class="modal-footer">
                        <button data-dismiss="modal" type="button" class="btn btn-default">Fechar</button>
                    </div>
                </div>
            </div>  
        </div>  

        <div class="modal fade" id="nutricao_id" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true" data-backdrop="false">
            <div class="modal-dialog modal-sm" role="document" style="float: right; width: 30%">
                <div class="modal-content">
                
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h3 class="modal-title" id="myModalLabel" style="color:#0a5074; font-size: 16px">Animais - Nutrição</h3>
                    </div>

                    <div class="modal-body nao-pode-selecionar" style="color:#000; font-size: 11px;">
					<!--	<p style="text-align: justify; color:#808080;">A Movimentação Outras Saídas retira um animal um pasto de sua propriedade, garantindo a manutenção correta estoque de sua(s) fazenda(s)</p>-->	

						<strong><p>Como Funciona?</p></strong>

						<p style="text-align: justify; color:#808080;">Clicar <span style="color:#000;">MAPA DE GADO</span>  e nome da sua fazenda na tela inicial do sistema;</p>

						<p style="text-align: justify; color:#808080;">Selecionar o pasto onde quer distribuir o(s) produto(s).</p>

						<p style="text-align: justify; color:#808080;">1 - Clique em <span style="color:#00008B;">Distribuir Nutrição;</span>;</p>

						<p style="text-align: justify; color:#808080;">2 - Selecione a <span style="color:#00008B;">Situação do cocho</span> com relação ao restante de produto que colocou no dia anterior;</p>

						<p style="text-align: justify; color:#808080;">Para início da distribuição de produto selecione: “Iniciando nutrição para este grupo”  ou selecione a opção que mais se aproxima da quantidade de produto restante nos cochos;</p>

						<p style="text-align: justify; color:#808080;">3 - Confirme a data (de preferência registrar a distribuição no mesmo dia em que foi feita e m ordem crescente dos dias);</p>

						<p style="text-align: justify; color:#808080;">4 - Selecione o produto previamente cadastrado<span style="color:#00008B;">*</span></p>

						<p style="text-align: justify; color:#808080;">5 - Selecione quantidade em KG;</p>

						<p style="text-align: justify; color:#808080;">6 - E <span style="color:#00008B;">Confirme</span>.</p>

						<p style="text-align: justify; color:#808080;">7 - Para adicionar mais produtos clicar novamente em <span style="color:#00008B;">Distribuir Nutrição</span>.</p>

 						<p style="text-align: justify; color:#808080;">A quantidade de produto será gravada. Se você descrever o nome do Lote em cada pasto este nome acompanhará o grupo de animais e você poderá conhecer o consumo de produto deste grupo (Veja no uso das funcionalidades).</p>

						<p>&nbsp;</p>	

						<strong><p>Para que usar esta funcionalidade?</p></strong>

						<p style="text-align: justify; color:#000;">PARA ACOMPANHAR A DISTRIBUIÇÃO DIÁRIA DE NUTRIENTES NO PASTO:</p>	

						<p style="text-align: justify; color:#808080;">Você poderá acompanhar a distribuição dos produtos de nutrição no menu <span style="color:#00008B;">Animais > Nutrição</span> selecionando período, fazenda e produto, se assim definir.</p>	

						<p style="text-align: justify; color:#000;">PARA CONHECER O CONSUMO POR PERÍODO E POR CABEÇA DE CADA PRODUTO UTILIZADO:</p>	

						<p style="text-align: justify; color:#808080;">No menu <span style="color:#00008B;">Relatório > Produtivo > Nutrição</span>, faça os filtros das informações que precisa no período e conheça o gasto total do produto e o consumo/cabeça de animal neste período.</p>	

						<p style="text-align: justify; color:#808080;">Os relatórios gerados podem ser exportados em Excel e você pode acompanhar e calcular os indicadores que quiser.</p>	

						<strong><p>Tire um ótimo proveito dessas informações! <i class='far fa-smile'></i></p></strong>

						<p style="text-align: justify; color:#808080;"><span style="color:#00008B;">* ATIVIDADE PRÉVIA:</span>  Antes de iniciar a Distribuição da nutrição nos pastos você precisará ir ao menu <span style="color:#00008B;">Cadastro > Produtos</span> e cadastrar os produtos que pretende adquirir para distribuir. Para tanto: </p>	

						<p style="text-align: justify; color:#808080;">1 - clique em <span style="color:#00008B;">Incluir Novo</span></p>	

						<p style="text-align: justify; color:#808080;">2 - selecione uma descrição padrão que o sistema te oferece e adicione um nome comercial ou uma descrição que fará sua equipe reconhecer o produto. </p>	

						<p style="text-align: justify; color:#808080;">3 - Selecione a apresentação do produto por saco e em kg.</p>	

						<p style="text-align: justify; color:#000;">NÃO SERÁ NECESSÁRIO PREENCHER O CONTROLE DE ESTOQUE MAS CASO QUEIRA, PODE SER UMA INFORMAÇÃO A MAIS QUE O BOI VIRTUAL PERMITE QUE VOCÊ TENHA.</p>	

					</div>

                    <div class="modal-footer">
                        <button data-dismiss="modal" type="button" class="btn btn-default">Fechar</button>
                    </div>
                </div>
            </div>  
        </div>  


        <div class="modal fade" id="nascimento_lote" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true" data-backdrop="false">
            <div class="modal-dialog modal-sm" role="document" style="float: right; width: 30%">
                <div class="modal-content">
                
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h3 class="modal-title" id="myModalLabel" style="color:#0a5074; font-size: 16px">Reprodução - Nascimento</h3>
                    </div>

                    <div class="modal-body nao-pode-selecionar" style="color:#000; font-size: 11px;">

						<p style="text-align: justify; color:#808080;">A Movimentação de Nascimento gera novo animal no Mapa e no Estoque de sua fazenda com idade de 0 a 7 meses</p>	

						<strong><p>Como Funciona?</p></strong>

						<strong><p>PARA DIGITAÇÃO DIRETA A PARTIR DO MAPA DE GADO (OPÇÃO IDEAL)</p></strong>

						<p style="text-align: justify; color:#808080;">1 - Clicar no nome da fazenda no quadro <span style="color:#00008B;">MAPA DE GADO</span> (tela inicial do programa "Home")</p>

						<p style="text-align: justify; color:#808080;">2 - Clicar no pasto em que o animal que nasceu está;</p>

						<p style="text-align: justify; color:#808080;">3 - Clicar no botão <span style="color:#00008B;">Nascimento</span>, abaixo da tela;</p>

						<p style="text-align: justify; color:#808080;">4 - Selecionar a <span style="color:#00008B;">* Data de Nascimento</span> (certifique-se de que está no pasto certo no texto acima);</p>

						<p style="text-align: justify; color:#808080;">5 - Digitar a <span style="color:#00008B;">* Qtd Animais</span> (total de animais do mesmo sexo que nasceram) </p>

						<p style="text-align: justify; color:#808080;">6 - Digitar o <span style="color:#00008B;">* Sexo</span> e se quiser, pode colocar a Raça e peso médio ao nascer;</p>

						<p style="text-align: justify; color:#808080;">7 - Clicar <span style="color:#00008B;">Confirmar Inclusão e Fechar</span>.</p>

						<p style="text-align: justify; color:#808080;">A tela continuará aberta para entrada de mais nascimentos caso haja. Se não houver clicar em voltar para cessar o Mapa de Gado.</p>	

						<p>&nbsp;</p>	

						<strong><p>PARA DIGITAÇÃO A PARTIR DO MENU REPRODUÇÃO>NASCIMENTO </p></strong>

						<p style="text-align: justify; color:#808080;">1 -Clicar no menu <span style="color:#00008B;">Reprodução > Nascimento > Incluir Novo</span></p>

						<p style="text-align: justify; color:#808080;">2 - Selecionar a <span style="color:#00008B;">* Fazenda</span> onde o(s) animal(is) nasceram e o <span style="color:#00008B;">* Pasto</span>;</p>

						<p style="text-align: justify; color:#808080;">3 - Selecionar a <span style="color:#00008B;">* Data de Nascimento</span>;</p>

						<p style="text-align: justify; color:#808080;">4 - Digitar a <span style="color:#00008B;">* Qtd Animais</span> (total de animais do mesmo sexo que nasceram);</p>

						<p style="text-align: justify; color:#808080;">5 - Digitar o <span style="color:#00008B;">* Sexo</span> e se quiser, pode colocar a Raça e peso médio ao nascer;</p>

						<p style="text-align: justify; color:#808080;">6 - Clicar <span style="color:#00008B;">Confirmar Inclusão e Fechar</span>.</p>

						<p style="text-align: justify; color:#808080;">A tela continuará aberta para entrada de mais nascimentos caso haja. Se não houver clicar em voltar para cessar o Mapa de Gado.</p>	

 						<!--<p style="text-align: justify; color:#808080;font-size: 10px;">ATENÇÃO: O ANIMAL QUE VOCÊ QUER BAIXAR PRECISA ESTAR NO PASTO SELECIONADO NA MESMA CATEGORIA E SEXO DO ANIMAL QUE VAI BAIXAR.</p>-->

						<p>&nbsp;</p>	

						<strong><p>Para que usar esta funcionalidade?</p></strong>

						<strong><p>PARA OBTER SEU ESTOQUE EM DIA E A MÉDIA ANUAL DO SEU ESTOQUE:</p></strong>

						<p style="text-align: justify; color:#808080;">Você poderá consultar o menu <span style="color:#00008B;">Relatórios > Produtivos > Estoque</span> e verificar todas a movimentações de entrada e saída dos seus animais e obter seu estoque médio por período a partir desta informação.</p>	

						<p style="text-align: justify; color:#808080;">As informações de estoque podem ser exportadas para Excel e você pode acompanhar e calcular os indicadores que quiser.</p>	

						<strong><p>Tire um ótimo proveito dessas informações! <i class='far fa-smile'></i></p></strong>
					</div>

                    <div class="modal-footer">
                        <button data-dismiss="modal" type="button" class="btn btn-default">Fechar</button>
                    </div>
                </div>
            </div>  
        </div>  

        <div class="modal fade" id="nascimento_id" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true" data-backdrop="false">
            <div class="modal-dialog modal-sm" role="document" style="float: right; width: 30%">
                <div class="modal-content">
                
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h3 class="modal-title" id="myModalLabel" style="color:#0a5074; font-size: 16px">Reprodução - Nascimento</h3>
                    </div>

                    <div class="modal-body nao-pode-selecionar" style="color:#000; font-size: 11px;">

						<p style="text-align: justify; color:#808080;">A Movimentação de Nascimento gera novo animal no Mapa e no Estoque de sua fazenda com idade de 0 a 7 meses</p>	

						<strong><p>Como Funciona?</p></strong>

						<strong><p>PARA DIGITAÇÃO DIRETA A PARTIR DO MAPA DE GADO (OPÇÃO IDEAL)</p></strong>

						<p style="text-align: justify; color:#808080;">1 - Clicar no nome da fazenda no quadro <span style="color:#00008B;">MAPA DE GADO</span> (tela inicial do programa "Home")</p>

						<p style="text-align: justify; color:#808080;">2 - Clicar no pasto em que o animal que nasceu está;</p>

						<p style="text-align: justify; color:#808080;">3 - Clicar no botão <span style="color:#00008B;">Nascimento</span>, abaixo da tela;</p>

						<p style="text-align: justify; color:#808080;">4 - Selecionar a <span style="color:#00008B;">* Data de Nascimento</span> (certifique-se de que está no pasto certo no texto acima);</p>

						<p style="text-align: justify; color:#808080;">5 - Digitar a <span style="color:#00008B;">* Mãe Nº</span>; (Clicar no número que vier aproximado a partir de sua digitação) </p>

						<p style="text-align: justify; color:#808080;">6 - <span style="color:#00008B;">* N° do animal</span>; (Já vem sugerido de acordo com parâmetro criado previamente em <span style="color:#00008B;">Reprodução > Seleção de Matrizes > Parâmetros da estação</span>);</p>

						<p style="text-align: justify; color:#808080;">7 - Selecionar <span style="color:#00008B;">* Sexo</span>; <span style="color:#00008B;">* Raça</span>, <span style="color:#00008B;">Peso</span> (o peso não é obrigatório, pode preencher valor aproximado);</p>

						<p style="text-align: justify; color:#808080;">8 - Clicar <span style="color:#00008B;">Confirmar Inclusão e Fechar</span>.</p>

						<p style="text-align: justify; color:#808080;">A tela continuará aberta para entrada de mais nascimentos caso haja. Se não houver clicar em voltar para cessar o Mapa de Gado.</p>	

						<p>&nbsp;</p>	

						<strong><p>PARA DIGITAÇÃO A PARTIR DO MENU REPRODUÇÃO>NASCIMENTO </p></strong>

						<p style="text-align: justify; color:#808080;">1 -Clicar no menu <span style="color:#00008B;">Reprodução > Nascimento > Incluir Novo</span></p>

						<p style="text-align: justify; color:#808080;">2 - Selecionar a <span style="color:#00008B;">* Fazenda</span> onde o(s) animal(is) nasceram e o <span style="color:#00008B;">* Pasto</span>;</p>

						<p style="text-align: justify; color:#808080;">3 - Selecionar a <span style="color:#00008B;">* Data de Nascimento</span>;</p>

						<p style="text-align: justify; color:#808080;">4 - Digitar a <span style="color:#00008B;">* Mãe Nº</span>; (Clicar no número que vier aproximado a partir de sua digitação) </p>

						<p style="text-align: justify; color:#808080;">5 - <span style="color:#00008B;">* N° do animal</span>; (Já vem sugerido de acordo com parâmetro criado previamente em <span style="color:#00008B;">Reprodução > Seleção de Matrizes > Parâmetros da estação</span>);</p>

						<p style="text-align: justify; color:#808080;">6 - Selecionar <span style="color:#00008B;">* Sexo</span>; <span style="color:#00008B;">* Raça</span>, <span style="color:#00008B;">Peso</span> (o peso não é obrigatório, pode preencher valor aproximado);</p>

						<p style="text-align: justify; color:#808080;">7 - Clicar <span style="color:#00008B;">Confirmar Inclusão e Fechar</span>.</p>

						<p style="text-align: justify; color:#808080;">A tela continuará aberta para entrada de mais nascimento caso haja. Se não houver clicar em voltar consulta dos nascimentos.</p>	

						<p>&nbsp;</p>	

						<strong><p>Para que usar esta funcionalidade?</p></strong>

						<strong><p>PARA OBTER SEU ESTOQUE EM DIA E A MÉDIA ANUAL DO SEU ESTOQUE:</p></strong>

						<p style="text-align: justify; color:#808080;">Você poderá consultar o menu <span style="color:#00008B;">Relatórios > Produtivos > Estoque</span> e verificar todas a movimentações de entrada e saída dos seus animais e obter seu estoque médio por período a partir desta informação.</p>	

						<p style="text-align: justify; color:#808080;">No <span style="color:#00008B;">Relatórios > Produtivo > Listagem de animais > Completa</span> você poderá visualizar também todos os pesos de nascimento de animais registrados, bem como pesos de desmama e pesos atuais.</p>	
						<p style="text-align: justify; color:#808080;">As informações de estoque e lista de animais podem ser exportadas para Excel e você pode acompanhar e calcular os indicadores que quiser.</p>	

						<strong><p>Tire um ótimo proveito dessas informações! <i class='far fa-smile'></i></p></strong>

					</div>

                    <div class="modal-footer">
                        <button data-dismiss="modal" type="button" class="btn btn-default">Fechar</button>
                    </div>
                </div>
            </div>  
        </div>  

        <div class="modal fade" id="contas_pagar" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true" data-backdrop="false">
            <div class="modal-dialog modal-sm" role="document" style="float: right; width: 30%">
                <div class="modal-content">
                
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h3 class="modal-title" id="myModalLabel" style="color:#0a5074; font-size: 16px">Gestão Administrativa - Contas a Pagar</h3>
                    </div>

                    <div class="modal-body nao-pode-selecionar" style="color:#000; font-size: 11px;">

						<p style="text-align: justify; color:#808080;">O Registro de Contas a Pagar permite você ter Controle de todos os gastos de sua fazenda.</p>	

						<p style="text-align: justify; color:red;">ATENÇÃO: JÁ ENTREGAMOS PARA VOCÊ UM PLANO DE CONTAS PRONTINHO QUE SEPARA SEUS GASTOS POR ÁREA DA FAZENDA, MAS VOCÊ PODE FAZER ALGUMA CUSTOMIZAÇÃO NO PLANO. BASTA SOLICITAR AO NOSSO SUPORTE. <i class='far fa-smile'></i></p>			
						<strong><p>Como Funciona?</p></strong>

						<p style="text-align: justify; color:#808080;">Clicar no menu <span style="color:#00008B;">Getão Administrativa > Contas a Pagar > Incluir Nova</span></p>

						<p style="text-align: justify; color:#808080;">1 - Incluir a <span style="color:#0000FF;">*Descrição da Compra</span> (campo livre para descrição do que comprou)</p>

						<p style="text-align: justify; color:#808080;">2 - Selecionar um fornecedor cadastrado no campo <span style="color:#0000FF;">*Razão/Nome</span> ou cadastrar o fornecedor novo clicando em <span style="color:#0000FF;">+</span>.</p>

						<p style="text-align: justify; color:#808080;">Dica: É importante você cadastrar fornecedores habituais, caso queira obter relatórios de quanto gasta naquele fornecedor em específico por período. Para fornecedores esporádicos você pode usar o campo  de Fornecedor não Cadastrado.</p>

						<p style="text-align: justify; color:#808080;">3 - Vá seguindo o preenchimento de todos os campos selecionando ou preenchendo.</p>

						<p style="text-align: justify; color:#808080;">4 - Para parcela única, após preencher o campo <span style="color:#0000FF;">*Conta Pagamento</span> e <span style="color:#0000FF;">Confirmar Inclusão</span>.</p>

						<p style="text-align: justify; color:#808080;">5 - Para mais de uma parcela, após preencher <span style="color:#0000FF;">*Conta Pagamento</span>, ir para o campo <span style="color:#0000FF;">*Número de Ocorrências das Parcelas Restantes</span>, no quadro ao lado de parcelas restantes. Lembrar que você deve digitar a qtd de parcelas restantes. Ex: se o total é de 3 parcelas digitar 2.</p>

						<p style="text-align: justify; color:#808080;">6 - Selecionar a <span style="color:#0000FF;">*Conta Pagamento</span> das demais parcelas e </p>

						<p style="text-align: justify; color:#808080;">7 - Se a frequência das parcelas for fixa (semanal, quinzenal, mensal, etc.) selecionar <span style="color:#0000FF;">Repetir Pagamento por Frequência</span></p>

						<p style="text-align: justify; color:#808080;">8 - Se for em dias variados selecionar <span style="color:#0000FF;">Parcelar por prazos em dias</span></p>

						<p style="text-align: justify; color:#808080;">9 - Em seguida escolher os dias em números de dias para o vencimento das parcelas restantes separando por virgulas. Ex. 28, 35. e <span style="color:#0000FF;">Confirmar Inclusão</span>.</p>

						<p style="text-align: justify; color:#808080;">Você pode registrar uma conta paga já paga ou com uma à pagar, porém ambas, para serem visualizadas no fluxo de caixa e relatórios precisa de <span style="color:#0000FF;">Aceite de Contas</span>. Este é um mecanismo de segurança do sistema para que você confira as contas a pagar que lançou ou autorize o pagamento caso seja outra pessoa quem lança a conta.</p>

						<p style="text-align: justify; color:#808080;">O <span style="color:#0000FF;">Aceite de Contas</span> é um botão na sua tela <span style="color:#00008B;">Gestão Administrativa > Contas a Pagar</span>.</p>

						<p style="text-align: justify; color:#808080;">Uma ótima notícia é que o pagamento de compras de animais vai direto para o <span style="color:#0000FF;">Aceite de Contas</span> assim que você faz o registro da compra em <span style="color:#00008B;">Gestão Administrativa > Compra e Venda de Animais</span>.</p>

						<p>&nbsp;</p>

						<strong><p>Para que usar esta funcionalidade?</p></strong>

						<strong><p>PARA TER O CONTROLE DE TODOS OS SEUS GASTOS SEPARADOS POR CONTA E TOMAR DECISÕES MAIS ASSERTIVAS:</p></strong>

						<p style="text-align: justify; color:#808080;">Você poderá consultar o menu <span style="color:#00008B;">Relatórios > Financeiros</span> e verificar os relatórios de <span style="color:#0000FF;">Fluxo de Caixa, Análise de Contas a pagar, Análise de previsto/Realizado</span> e fazer seu acompanhamento de gastos sempre que quiser dentro do período que estabelecer para isto. </p>	

						<strong><p>PARA ANALISAR SE ESTA SEGUINDO SEU PLANEJAMENTO DE GASTOS E REDIRECIONAR SUAS DECISÕES O QUANTO ANTES:</p></strong>

						<p style="text-align: justify; color:#808080;">Para esta análise você precisará ter preenchido a previsão de contas para o período de análise. Veja em <span style="color:#00008B;">Gestão Administrativa > Previsão de Contas</span>.</p>	

						<strong><p>PARA TER UMA AGENDA DIÁRIA DE PAGAMENTOS DE CONTAS:</p></strong>

						<p style="text-align: justify; color:#808080;">Se você acessar diariamente a <span style="color:#00008B;">Gestão administrativa > Contas a Pagar</span> e consultar você visualizará diariamente as contas que precisa pagar e fazer a baixa do pagamento, mantendo seu fluxo de caixa em dia.</p>	

						<p style="text-align: justify; color:#808080;">Não esqueça, todos os relatórios são exportáveis para Excel.</p>	

						<strong><p>Tire um ótimo proveito dessas informações! <i class='far fa-smile'></i></p></strong>
					</div>

                    <div class="modal-footer">
                        <button data-dismiss="modal" type="button" class="btn btn-default">Fechar</button>
                    </div>
                </div>
            </div>  
        </div>  

        <div class="modal fade" id="contas_receber" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true" data-backdrop="false">
            <div class="modal-dialog modal-sm" role="document" style="float: right; width: 30%">
                <div class="modal-content">
                
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h3 class="modal-title" id="myModalLabel" style="color:#0a5074; font-size: 16px">Gestão Administrativa - Contas a Receber</h3>
                    </div>

                    <div class="modal-body nao-pode-selecionar" style="color:#000; font-size: 11px;">

						<p style="text-align: justify; color:#808080;">O Registro de Contas a Receber permite você ter Controle de todos os recebimentos de sua fazenda.</p>	

						<p style="text-align: justify; color:red;">ATENÇÃO: JÁ ENTREGAMOS PARA VOCÊ UM PLANO DE CONTAS PRONTINHO QUE SEPARA SEUS GASTOS POR ÁREA DA FAZENDA, MAS VOCÊ PODE FAZER ALGUMA CUSTOMIZAÇÃO NO PLANO. BASTA SOLICITAR AO NOSSO SUPORTE. <i class='far fa-smile'></i></p>			
						<strong><p>Como Funciona?</p></strong>

						<strong><p>SE SEUS RECEBIMENTOS TODOS VEM DA VENDA DE ANIMAIS</p></strong>

						<p style="text-align: justify; color:#808080;">Basta ler as orientações descritas para <span style="color:#00008B;">Gestão Administrativa > Compra e Venda de Animais > Venda</span></p>

						<strong><p>SE VOCÊ TEM OUTRA FONTE DE REDA QUE <span style="color:#0000FF;">NÃO SEJA</span> VENDA DE ANIMAIS</p></strong>

						<p style="text-align: justify; color:#808080;">1 - Clicar no menu <span style="color:#00008B;">Gestão Administratica > Contas a Receber > Incluir Nova</span></p>

						<p style="text-align: justify; color:#808080;">2 - Incluir a <span style="color:#0000FF;">*Descrição da Receita</span> (campo livre para descrição do que está vendendo)</p>

						<p style="text-align: justify; color:#808080;">3 - Selecionar um Cliente cadastrado no campo <span style="color:#0000FF;">*Cliente/Parceiro</span> ou cadastrar o cliente novo clicando em <span style="color:#0000FF;">+</span>. ( No tipo de pessoa no cadastro selecionar a opção Cliente ou Produtor.</p>

						<p style="text-align: justify; color:#808080;">Dica: É importante você cadastrar clientes habituais, caso queira obter relatórios de quanto vende para ele por período. Para clientes esporádicos você pode usar o campo  de nome não cadastrado.</p>

						<p style="text-align: justify; color:#808080;">4 - Vá seguindo o preenchimento de todos os campos selecionando ou preenchendo.</p>

						<p style="text-align: justify; color:#808080;">5 - Para parcela única, após preencher o campo <span style="color:#0000FF;">*Conta Pagamento</span> e <span style="color:#0000FF;">Confirmar Inclusão</span>.</p>

						<p style="text-align: justify; color:#808080;">6 - Para mais de uma parcela, após preencher <span style="color:#0000FF;">*Conta Pagamento</span>, clicar em <span style="color:#0000FF;">Repetir</span> e <span style="color:#0000FF;">*Selecionar Frequência</span>. No campo <span style="color:#0000FF;">*Número de Ocorrências</span> digitar a qtd de parcelas restantes. Ex: se o total de parcelas a receber é de 3 digitar 2, que serão as restantes.</p>

						<p style="text-align: justify; color:#808080;">7 - Digitar o <span style="color:#0000FF;">*Valor das Parcelas</span> e <span style="color:#0000FF;">*Data Inicial p/ Próximos Recebimentos</span> que corresponde a data de vencimento da 2ª parcela. O sistema calculará o vencimento das próximas de acordo com a frequência e nº de parcelas informada. </p>

						<p style="text-align: justify; color:#808080;">8 - Continuar preenchendo os campos até o final e <span style="color:#0000FF;">Confirmar Inclusão</span>.</p>

						<p>&nbsp;</p>

						<strong><p>Para que usar esta funcionalidade?</p></strong>

						<strong><p>PARA TER UMA AGENDA DIÁRIA DE RECEBIMENTOS:</p></strong>

						<p style="text-align: justify; color:#808080;">Se você acessar diariamente a <span style="color:#00008B;">Gestão administrativa > Contas a Receber</span> e consultar você visualizará diariamente as contas que irá receber naquela data e acompanhar seu recebimento sem atrasos. Em seguida fazer a baixa do recebimento, mantendo seu fluxo de caixa em dia. </p>	

						<strong><p>PARA TER O CONTROLE DE TODOS OS SEUS GANHOS E TOMAR DECISÕES MAIS ASSERTIVAS:</p></strong>

						<p style="text-align: justify; color:#808080;">Você poderá consultar o menu <span style="color:#00008B;">Relatórios > Financeiros</span> e verificar os relatórios de <span style="color:#0000FF;">Fluxo de Caixa, Análise de Contas a Receber, Análise de previsto/Realizado</span> e fazer seu acompanhamento de ganhos sempre que quiser dentro do período que estabelecer para isto.</p>	

						<strong><p>PARA ANALISAR SE ESTA SEGUINDO SEU PLANEJAMENTO E REDIRECIONAR DECISÕES MAIS ASSERTIVAS:</p></strong>

						<p style="text-align: justify; color:#808080;">Para esta análise você precisará ter preenchido a previsão de contas para o período de análise. Veja em <span style="color:#00008B;">Gestão Administrativa > Previsão de Contas</span>.</p>	

						<p style="text-align: justify; color:#808080;">Não esqueça, todos os relatórios são exportáveis para Excel.</p>	

						<strong><p>Tire um ótimo proveito dessas informações! <i class='far fa-smile'></i></p></strong>
					</div>

                    <div class="modal-footer">
                        <button data-dismiss="modal" type="button" class="btn btn-default">Fechar</button>
                    </div>
                </div>
            </div>  
        </div>  
