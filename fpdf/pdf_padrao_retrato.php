<?php

require('fpdf/fpdf.php');

class PDF extends FPDF {

   function Header() {
       @ session_start();  
		 $nome_setor = $_SESSION['nome_setor'];
		 $nome_relatorio = $_SESSION['nome_relatorio'];
         $filtros=$_SESSION['filtros'];

         $this->SetXY(2, 2);
         $this->Cell(206,282,'',1,0,'C');
		 
		 $logo_empresa='agrolandes.jpg';
         $this->SetXY(2, 2);
         $this->Cell(30,15,'',1,0,'C');
         $this->Image('images/'.$logo_empresa,11,3,12,12);

         $this->SetFont('arial','',11);

         $this->SetXY(32, 2);
         $this->Cell(130,15,'',1,0,'C');

         $this->SetXY(38, 7);
         $this->Cell(130,6,$nome_relatorio,0,0,'C');

         $this->SetFont('arial','',9);
		 
         $this->SetXY(162, 10);
         $this->Cell(46,7,'',1,0,'C');

         $this->SetXY(162, 4);
         $this->Cell(46,4,$nome_setor,0,0,'C');

         $this->SetFont('arial','',9);
         $this->SetXY(162, 10);
         $this->Cell(46,6,'Data: ' . date('d/m/Y') ,0,0,'C');
   }

   function Footer() {
        @ session_start();  
        $filtros=$_SESSION['filtros'];
        $this->SetFont('Arial','',6);
        $this->SetXY(1,-13);
        $this->Cell(0,6,$filtros,0,0,'L');

        $this->SetY(-10);
        $this->SetFont('Arial','B',7);
        $this->Cell(0,10,'Pag: '.$this->PageNo(),0,0,'C');
   }
}
?>

