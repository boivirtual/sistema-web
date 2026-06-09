<?php 

$tipo_gravacao = $_POST['tipo_gravacao'];
$codigo = $_POST['codigo_grupo'];
$descricao = $_POST['descricao_grupo'];

$manejo_animais = array();
if(isset($_POST['opc101'])) { $manejo_animais[0] = 1; } else { $manejo_animais[0] = 0; }
if(isset($_POST['opc102'])) { $manejo_animais[1] = 1; } else { $manejo_animais[1] = 0; }
if(isset($_POST['opc103'])) { $manejo_animais[2] = 1; } else { $manejo_animais[2] = 0; }
if(isset($_POST['opc104'])) { $manejo_animais[3] = 1; } else { $manejo_animais[3] = 0; }
//if(isset($_POST['opc105'])) { $manejo_animais[4] = 1; } else { $manejo_animais[4] = 0; }
//if(isset($_POST['opc106'])) { $manejo_animais[5] = 1; } else { $manejo_animais[5] = 0; }
$grupo_array_manejo_animais = implode("!",$manejo_animais);

$array_manejo_reprodutivo = array();
if(isset($_POST['opc201'])) { $array_manejo_reprodutivo[0] = 1; } else { $array_manejo_reprodutivo[0] = 0; }
if(isset($_POST['opc202'])) { $array_manejo_reprodutivo[1] = 1; } else { $array_manejo_reprodutivo[1] = 0; }
if(isset($_POST['opc203'])) { $array_manejo_reprodutivo[2] = 1; } else { $array_manejo_reprodutivo[2] = 0; }
if(isset($_POST['opc204'])) { $array_manejo_reprodutivo[3] = 1; } else { $array_manejo_reprodutivo[3] = 0; }
$grupo_array_manejo_reprodutivo = implode("!",$array_manejo_reprodutivo);

$array_suplemento_alimentar = array();
if(isset($_POST['opc301'])) { $array_suplemento_alimentar[0] = 1; } else { $array_suplemento_alimentar[0] = 0; }
if(isset($_POST['opc302'])) { $array_suplemento_alimentar[1] = 1; } else { $array_suplemento_alimentar[1] = 0; }
$grupo_array_suplemento_alimentar = implode("!",$array_suplemento_alimentar);

$array_controle_sanitario = array();
if(isset($_POST['opc401'])) { $array_controle_sanitario[0] = 1; } else { $array_controle_sanitario[0] = 0; }
if(isset($_POST['opc402'])) { $array_controle_sanitario[1] = 1; } else { $array_controle_sanitario[1] = 0; }
$grupo_array_controle_sanitario = implode("!",$array_controle_sanitario);

$array_gestao_adm = array();
if(isset($_POST['opc501'])) { $array_gestao_adm[0] = 1; } else { $array_gestao_adm[0] = 0; }
if(isset($_POST['opc502'])) { $array_gestao_adm[1] = 1; } else { $array_gestao_adm[1] = 0; }
if(isset($_POST['opc503'])) { $array_gestao_adm[2] = 1; } else { $array_gestao_adm[2] = 0; }
if(isset($_POST['opc504'])) { $array_gestao_adm[3] = 1; } else { $array_gestao_adm[3] = 0; }
if(isset($_POST['opc505'])) { $array_gestao_adm[4] = 1; } else { $array_gestao_adm[4] = 0; }
if(isset($_POST['opc506'])) { $array_gestao_adm[5] = 1; } else { $array_gestao_adm[5] = 0; }
if(isset($_POST['opc507'])) { $array_gestao_adm[6] = 1; } else { $array_gestao_adm[6] = 0; }
$grupo_array_gestao_adm = implode("!",$array_gestao_adm);

$array_cadastros = array();
if(isset($_POST['opc701'])) { $array_cadastros[0] = 1; } else { $array_cadastros[0] = 0; }
if(isset($_POST['opc702'])) { $array_cadastros[1] = 1; } else { $array_cadastros[1] = 0; }
if(isset($_POST['opc703'])) { $array_cadastros[2] = 1; } else { $array_cadastros[2] = 0; }
if(isset($_POST['opc704'])) { $array_cadastros[3] = 1; } else { $array_cadastros[3] = 0; }
if(isset($_POST['opc705'])) { $array_cadastros[4] = 1; } else { $array_cadastros[4] = 0; }
if(isset($_POST['opc706'])) { $array_cadastros[5] = 1; } else { $array_cadastros[5] = 0; }
if(isset($_POST['opc707'])) { $array_cadastros[6] = 1; } else { $array_cadastros[6] = 0; }
$grupo_array_cadastros = implode("!",$array_cadastros);

$array_parametros = array();
if(isset($_POST['opc800'])) { $array_parametros[0] = 1; } else { $array_parametros[0] = 0; }
if(isset($_POST['opc801'])) { $array_parametros[1] = 1; } else { $array_parametros[1] = 0; }
if(isset($_POST['opc802'])) { $array_parametros[2] = 1; } else { $array_parametros[2] = 0; }
if(isset($_POST['opc803'])) { $array_parametros[3] = 1; } else { $array_parametros[3] = 0; }
if(isset($_POST['opc804'])) { $array_parametros[4] = 1; } else { $array_parametros[4] = 0; }
if(isset($_POST['opc805'])) { $array_parametros[5] = 1; } else { $array_parametros[5] = 0; }
if(isset($_POST['opc806'])) { $array_parametros[6] = 1; } else { $array_parametros[6] = 0; }
if(isset($_POST['opc807'])) { $array_parametros[7] = 1; } else { $array_parametros[7] = 0; }
if(isset($_POST['opc808'])) { $array_parametros[8] = 1; } else { $array_parametros[8] = 0; }
if(isset($_POST['opc809'])) { $array_parametros[9] = 1; } else { $array_parametros[9] = 0; }
if(isset($_POST['opc810'])) { $array_parametros[10] = 1; } else { $array_parametros[10] = 0; }
if(isset($_POST['opc811'])) { $array_parametros[11] = 1; } else { $array_parametros[11] = 0; }
if(isset($_POST['opc812'])) { $array_parametros[12] = 1; } else { $array_parametros[12] = 0; }
if(isset($_POST['opc813'])) { $array_parametros[13] = 1; } else { $array_parametros[13] = 0; }
if(isset($_POST['opc814'])) { $array_parametros[14] = 1; } else { $array_parametros[14] = 0; }
if(isset($_POST['opc815'])) { $array_parametros[15] = 1; } else { $array_parametros[15] = 0; }
if(isset($_POST['opc816'])) { $array_parametros[16] = 1; } else { $array_parametros[16] = 0; }
if(isset($_POST['opc817'])) { $array_parametros[17] = 1; } else { $array_parametros[17] = 0; }
if(isset($_POST['opc818'])) { $array_parametros[18] = 1; } else { $array_parametros[18] = 0; }
if(isset($_POST['opc819'])) { $array_parametros[19] = 1; } else { $array_parametros[19] = 0; }
if(isset($_POST['opc820'])) { $array_parametros[20] = 1; } else { $array_parametros[20] = 0; }
if(isset($_POST['opc821'])) { $array_parametros[21] = 1; } else { $array_parametros[21] = 0; }
if(isset($_POST['opc822'])) { $array_parametros[22] = 1; } else { $array_parametros[22] = 0; }
$grupo_array_parametros = implode("!",$array_parametros);

$array_relatorios = array();
if(isset($_POST['opc901'])) { $array_relatorios[1] = 1; } else { $array_relatorios[1] = 0; }
if(isset($_POST['opc902'])) { $array_relatorios[2] = 1; } else { $array_relatorios[2] = 0; }
if(isset($_POST['opc903'])) { $array_relatorios[3] = 1; } else { $array_relatorios[3] = 0; }
$grupo_array_relatorios = implode("!",$array_relatorios);

$data_sistema = date("Y-m-d H:i:s");

@ session_start(); 
$nomeusuario = $_SESSION['nome_usuario'];

include "conecta_mysql.inc";

if ($tipo_gravacao==1){
	$sql = ("UPDATE grupos_acessos SET descricao_grupo_acesso='$descricao',
		                               array_menu_manejo_animais_grupo_acesso='$grupo_array_manejo_animais', 
		                               array_menu_manejo_reprodutivo_grupo_acesso='$grupo_array_manejo_reprodutivo', 
		                               array_menu_suplemento_alimentar_grupo_acesso='$grupo_array_suplemento_alimentar', 
		                               array_menu_controle_sanitario_grupo_acesso='$grupo_array_controle_sanitario', 
		                               array_menu_gestao_adm_grupo_acesso='$grupo_array_gestao_adm',
		                               array_menu_cadastro_grupo_acesso='$grupo_array_cadastros', 
		                               array_menu_parametro_grupo_acesso='$grupo_array_parametros', 
		                               array_menu_relatorios_grupo_acesso='$grupo_array_relatorios', 
       	                               grupo_acesso_alterado_em='$data_sistema',
		                               grupo_acesso_alterado_por='$nomeusuario'
 		                       WHERE codigo_grupo_acesso='$codigo'");
	$resultado = mysqli_query($conector,$sql);
	$erro_mysql = mysqli_error($conector);

 	if (!$resultado){
	    echo '
			<script type="text/javascript">
				location.href="form_tabela_grupo_acessos.php?editar=true&status_gravacao=E&erro_mysql='.$erro_mysql.'";
			</script>"
		    ';
	}
    else {
        $_SESSION['menu_manejo_animais'] = $grupo_array_manejo_animais;
        $_SESSION['menu_manejo_reprodutivo'] = $grupo_array_manejo_reprodutivo;
        $_SESSION['menu_suplemento_alimentar'] = $grupo_array_suplemento_alimentar;
        $_SESSION['menu_controle_sanitario'] = $grupo_array_controle_sanitario;
        $_SESSION['menu_gestao_adm'] = $grupo_array_gestao_adm;
        $_SESSION['menu_cadastros'] = $grupo_array_cadastros;
        $_SESSION['menu_parametros'] = $grupo_array_parametros;
        $_SESSION['menu_relatorios'] = $grupo_array_relatorios;
	    echo '
		<script type="text/javascript">
			location.href="form_tabela_grupo_acessos.php?editar=true&status_gravacao=A&erro_mysql='.$erro_mysql.'";
		</script>"
	    ';
    }
}
else {
	$sql = "INSERT INTO grupos_acessos (descricao_grupo_acesso,
		                               array_menu_manejo_animais_grupo_acesso, 
		                               array_menu_manejo_reprodutivo_grupo_acesso, 
		                               array_menu_suplemento_alimentar_grupo_acesso, 
		                               array_menu_controle_sanitario_grupo_acesso, 
		                               array_menu_gestao_adm_grupo_acesso,
		                               array_menu_cadastro_grupo_acesso, 
		                               array_menu_parametro_grupo_acesso, 
									   array_menu_relatorios_grupo_acesso,
									   registro_lixeira_grupo_acesso,
	                                   grupo_acesso_incluido_em,
	                                   grupo_acesso_incluido_por
                                     ) 
                              VALUES ('$descricao',
                                      '$grupo_array_manejo_animais',
                                      '$grupo_array_manejo_reprodutivo',
                                      '$grupo_array_suplemento_alimentar',
                                      '$grupo_array_controle_sanitario',
                                      '$grupo_array_gestao_adm',
                                      '$grupo_array_cadastros',
                                      '$grupo_array_parametros',
                                      '$grupo_array_relatorios',
                                      0,
                                      '$data_sistema',
                                      '$nomeusuario'
                                     )";
	$resultado = mysqli_query($conector,$sql);
	$erro_mysql = mysqli_error($conector);

 	if (!$resultado){
	    echo '
			<script type="text/javascript">
				location.href="form_tabela_grupo_acessos.php?editar=true&status_gravacao=E&erro_mysql='.$erro_mysql.'";
			</script>"
		    ';
	}
	else {
	    echo '
			<script type="text/javascript">
				location.href="form_tabela_grupo_acessos.php?editar=true&status_gravacao=I&erro_mysql='.$erro_mysql.'";
			</script>"
		    ';
	}    
}

mysqli_close($conector);


?>