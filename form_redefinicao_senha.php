<?php 
    $valor[0]=0;
    $valor[1]='';

    $servidor = "localhost";
    $usuario_bd = "root";
    $senha_bd = "a2ngei9Mxh";
    $banco = "acesso_boi_virtual";
   
    $conector_acesso = mysqli_connect($servidor, $usuario_bd, $senha_bd, $banco);
  
    if (mysqli_connect_error()) {
        printf("Falha na conexão com o banco de dados: ", mysqli_connect_error());
        exit();
    }

    $bancoselecionado = mysqli_select_db($conector_acesso,$banco);

    if ($bancoselecionado === FALSE) {
        exit ("Falha na seleção do banco de dados: " . mysqli_error($conector));
    }

    $email_usuario = $_REQUEST['id']; 

    $usuario = mysqli_query($conector_acesso,"SELECT * FROM usuario WHERE email_usuario = '$email_usuario' and lixeira_usuario = 0");
                 
    $num_registros = mysqli_num_rows ($usuario); 

    if ($num_registros != 0){
        $registro_usuario = mysqli_fetch_array($usuario);
        $nome_usuario = $registro_usuario['nome_usuario'];
        $autorizar_redefinicao = $registro_usuario['autoriza_redefinir_senha_usuario'];

        @ session_start();

        $_SESSION["id_cliente"] = $registro_usuario['cnpj_cpf_empresa_usuario'];
    }

    $icone_empresa = 'img/boi_virtual_branco.ico';

?> 

<!DOCTYPE html>
<html lang="pt">

<head>

    <meta charset="utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <title>Boi Virtual</title>

    <link rel="shortcut icon" href="img/boi_virtual_preto.ico">

    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/bootstrap-theme.css" rel="stylesheet">
    <link href="css/elegant-icons-style.css" rel="stylesheet" />
    <link href="css/font-awesome.min.css" rel="stylesheet" />
    <link href="css/daterangepicker.css" rel="stylesheet" />
    <link href="css/bootstrap-datepicker.css" rel="stylesheet" />
    <link href="css/bootstrap-colorpicker.css" rel="stylesheet" />
    <link href="css/style.css" rel="stylesheet">
    <link href="css/style-responsive.css" rel="stylesheet" />

    <script src="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.0.0-alpha.6/css/bootstrap.css"></script>
    <link href="DataTables-1.10.18/css/dataTables.bootstrap4.min.css"rel="stylesheet" >
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css" integrity="sha384-oS3vJWv+0UjzBfQzYUhtDYW+Pj2yciDJxpsK1OYPAYjqT085Qq/1cq5FLXAZQ7Ay" crossorigin="anonymous">

</head>

<body>
<font face="Futura Std Light">

    <div class="container">

        <header class="header dark-bg">
            <a href="#" class="logo">
                <img src="<?php echo $icone_empresa;?>" alt="" class="logo-img">
                <span class="nome_empresa lite">BOI VIRTUAL</span>
            </a>
        </header>

<!--        <div class="row">
            <nav class="navbar navbar-default navbar-custom dark-bg">
                <div class="container">
                    <div class="navbar-header ">
                        <img src="images/agrolandes.png" width='120' height='80'> 
                        <img src="<?php echo $icone_empresa;?>" alt="" class="logo-img">
                        <span class="nome_empresa lite">BOI VIRTUAL</span>
                    </div>
                </div>
            </nav>      
        </div> -->
    </div>

 <!-- Contact Section -->

    <section id="contact">
        <div class="container">

            <div class="row">
				 <div class="col-md-12 text-center">
                    <h2>Redefina sua senha</h2>

                    <?php 
                        if ($num_registros != 0 && $autorizar_redefinicao=="S"){
                            echo '<h3 class="text-center">Olá ' . $nome_usuario . '</h3> ' ;
                        }
                    ?>

					<p class="text-center">A segurança dos seus dados é prioridade. Por isso, é importante você criar uma senha segura.
                    </p>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading" style="background-color:#0a5074; color: #fff">
                    ATENÇÃO! Sua senha deve conter até 8 caracteres que podem ser números e letras.

                </div>


                <div class="panel-body">

                <div class="alert alert-danger alert_erro" id="alert_erro" hidden="true">
                    <strong class="negrito"></strong><span></span>
                </div> 

                <div class="alert alert-success alert_mens" id="alert_mens" hidden="true">
                  <strong class="negrito"></strong> <span></span>
                </div>

                <?php 
                    if ($num_registros == 0){
                        echo '<div class="alert alert-danger">';
                        echo '<strong>Atenção! </strong>Algo deu errado, você não tem permissão para redefinir a senha para esse usuário.';
                        echo '</div>';      
                            exit;     
                        }
                    
                    if ($num_registros!=0){
                        if ($autorizar_redefinicao!="S"){
                            echo '<div class="alert alert-danger">';
                            echo '<strong>Atenção! </strong>Algo deu errado, você não tem permissão para redefinir a senha para esse usuário.';
                            echo '</div>'; 
                            exit;          
                        } 
                    }
                ?>

                    <form method="POST" action="#" enctype="multipart/form-data" id="form_gravar_senha">

                        <div class="row">
                           <div class="form-group col-md-3">
                                <input name="email_usuario" type="hidden" id="email_usuario" <?php echo "value='".$email_usuario."'";?>>

                                <label for="nova_senha" class="control-label"><span class="required">*</span> Nova Senha</label>

                                <input type="password" name="nova_senha" id="nova_senha" class="form-control" maxlength="8">
                            </div>
                        </div>

                        <div class="row">
                           <div class="form-group col-md-3">
                                <label for="confirme_nova_senha" class="control-label"><span class="required">*</span> Confirme a Nova Senha</label>

                                <input type="password" name="confirme_nova_senha" id="confirme_nova_senha" class="form-control" maxlength="8">
                            </div>
                        </div>

                        <div class="row">        
                            <div class="form-group col-md-12">
                                <button type="button" class="btn btn-info" onclick="gerar_nova_senha()">Salvar Nova Senha
                                </button>
                            </div>
                        </div>
                    </form>      
                    
                </div>

            </div>

        </div>
    </section>


    <!-- jQuery -->
    <script src="js/login.js?<?php echo Versao; ?>"></script> 
    <script src="js/jquery.js?<?php echo Versao; ?>"></script>
    <script src="js/jquery.scrollTo.min.js?<?php echo Versao; ?>"></script>
    <script src="js/jquery.nicescroll.js?<?php echo Versao; ?>" type="text/javascript"></script>
    <script src="js/jquery-ui-1.9.2.custom.min.js?<?php echo Versao; ?>"></script>
    <script src="js/scripts.js?<?php echo Versao; ?>"></script>
    <script src="https://cdn.datatables.net/v/bs4/dt-1.10.22/r-2.2.6/datatables.min.js" type="text/javascript" ></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js?<?php echo Versao; ?>"></script>
	

    <!-- Custom Theme JavaScript -->
    <script>
    // Closes the sidebar menu
    $("#menu-close").click(function(e) {
        e.preventDefault();
        $("#sidebar-wrapper").toggleClass("active");
    });
    // Opens the sidebar menu
    $("#menu-toggle").click(function(e) {
        e.preventDefault();
        $("#sidebar-wrapper").toggleClass("active");
    });
    // Scrolls to the selected menu item on the page
    $(function() {
        $('a[href*=#]:not([href=#],[data-toggle],[data-target],[data-slide])').click(function() {
            if (location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '') || location.hostname == this.hostname) {
                var target = $(this.hash);
                target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
                if (target.length) {
                    $('html,body').animate({
                        scrollTop: target.offset().top
                    }, 1000);
                    return false;
                }
            }
        });
    });
    //#to-top button appears after scrolling
    var fixed = false;
    $(document).scroll(function() {
        if ($(this).scrollTop() > 250) {
            if (!fixed) {
                fixed = true;
                // $('#to-top').css({position:'fixed', display:'block'});
                $('#to-top').show("slow", function() {
                    $('#to-top').css({
                        position: 'fixed',
                        display: 'block'
                    });
                });
            }
        } else {
            if (fixed) {
                fixed = false;
                $('#to-top').hide("slow", function() {
                    $('#to-top').css({
                        display: 'none'
                    });
                });
            }
        }
    });
    // Disable Google Maps scrolling
    // See http://stackoverflow.com/a/25904582/1607849
    // Disable scroll zooming and bind back the click event
    var onMapMouseleaveHandler = function(event) {
        var that = $(this);
        that.on('click', onMapClickHandler);
        that.off('mouseleave', onMapMouseleaveHandler);
        that.find('iframe').css("pointer-events", "none");
    }
    var onMapClickHandler = function(event) {
            var that = $(this);
            // Disable the click handler until the user leaves the map area
            that.off('click', onMapClickHandler);
            // Enable scrolling zoom
            that.find('iframe').css("pointer-events", "auto");
            // Handle the mouse leave event
            that.on('mouseleave', onMapMouseleaveHandler);
        }
        // Enable map zooming with mouse scroll when the user clicks the map
    $('.map').on('click', onMapClickHandler);

    </script>
		<script src="js_b/jquery.masonry.min.js"></script>
		<script src="js_b/jquery.history.js"></script>
		<script src="js_b/js-url.min.js"></script>
		<script src="js_b/jquerypp.custom.js"></script>
		<script src="js_b/gamma.js"></script>
		<script type="text/javascript">
			
			$(function() {

				var GammaSettings = {
						// order is important!
						viewport : [ {
							width : 1200,
							columns : 5
						}, {
							width : 900,
							columns : 4
						}, {
							width : 500,
							columns : 3
						}, { 
							width : 320,
							columns : 2
						}, { 
							width : 0,
							columns : 2
						} ]
				};

				Gamma.init( GammaSettings, fncallback );


				// Example how to add more items (just a dummy):

				var page = 0,
					items = ['']

				function fncallback() {

					$( '#loadmore' ).show().on( 'click', function() {

						++page;
						var newitems = items[page-1]
						if( page <= 1 ) {
							
							Gamma.add( $( newitems ) );

						}
						if( page === 1 ) {

							$( this ).remove();

						}

					} );

				}

			});

		</script>	

<script src="js_b/jquery-1.3.2.min.js" type="text/javascript" ></script>
<script src="js_b/sliding_effect.js" type="text/javascript" ></script>
</font>
</body>

</html>
