<?php
   define("Versao", "7022"); 

   //ob_start();
   //header('Content-7ype: text/html; charset=utf-8');
/*   @ session_start();    
       //$usuario = $_SESSION['id_usuario']; 
       //$senha_usuario = $_SESSION['senha_usuario'];
    
   if (isset($_SESSION['id_usuario'])){  
       $usuario = $_SESSION['id_usuario']; 
   }	    
	   
   if (isset($_SESSION['senha_usuario'])){
          $senha_usuario = $_SESSION['senha_usuario'];
   }	   
	   
   if (!(empty($usuario) OR empty($senha_usuario))) {
       include "conecta_mysql.inc";
	   
       $rs = mysqli_query($conector, "SELECT * FROM usuarios WHERE id_usuario = '$usuario'" );
	   if (mysqli_num_rows($rs) == 1) {

    		$registro_usuario = mysqli_fetch_array($rs);
    		$id_cadastro = $registro_usuario['id_usuario'];
        	$senha_cadastro = $registro_usuario['senha_usuario'];

            if ($senha_usuario != $senha_cadastro)				   	   
			{
			 	unset ($_SESSION['id_usuario']);
				unset ($_SESSION['senha_usuario']);
				echo "Voce não efetuou o Login! Senha " . $senha_usuario . ' - ' . $senha_cadastro;
				exit;
			}
		}
		else
		{
		 	unset ($_SESSION['id_usuario']);
		    unset ($_SESSION['senha_usuario']);
			echo "Voce não efetuou o Login! sem registro " . $usuario . mysqli_num_rows($rs);
			exit;
    	}
	}
	else
	{
		echo "Voce não efetuou o Login! Session vazia";
		exit;
	}
	mysqli_close($conector);			
*/
?>
