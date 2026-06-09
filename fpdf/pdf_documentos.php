<?php

require('fpdf/fpdf.php');

class PDF extends FPDF {

   function Header() {
       @ session_start();  
		 $nome_relatorio = $_SESSION['nome_relatorio'];

         $this->SetXY(2, 2);
         $this->Cell(205,270,'',1,0,'C');
		 
		 $logo_empresa='logo_famap.jpg';
         $this->SetXY(2, 2);
         $this->Cell(35,12,'',1,0,'C');
         $this->Image('imagens/'.$logo_empresa,5,3,30,10);

         $this->SetXY(37, 2);
         $this->Cell(125,6,'',1,0,'C');

         $this->SetXY(37, 8);
         $this->Cell(125,6,'',1,0,'C');

         $this->SetXY(162, 2);
         $this->Cell(45,6,'',1,0,'C');

         $this->SetXY(162, 8);
         $this->Cell(45,6,'',1,0,'C');
		 
         $this->SetFont('arial','',9);
         $this->SetXY(38, 4);
         $this->Cell(110,2,utf8_decode('RELATÓRIO'),0,0,'C');

         $this->SetFont('arial','',9);
         $this->SetXY(38, 10);
         $this->Cell(130,2,$nome_relatorio,0,0,'C');

         $this->SetFont('arial','',9);
         $this->SetXY(130, 4);
         $this->Cell(110,2,utf8_decode('Controle da Qualidade'),0,0,'C');

         $this->SetFont('arial','',9);
         $this->SetXY(130, 10);
         $this->Cell(110,2,utf8_decode('Documento:'),0,0,'C');
   }

   function Footer() {
       $this->SetY(-14);
       $this->SetFont('Arial','B',7);
       $this->Cell(0,10,'Pag: '.$this->PageNo(),0,0,'C');
   }
}
?>

