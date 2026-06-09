<?php

require('fpdf/fpdf.php');

class PDF extends FPDF {

   function Header() {
       $this->SetY(5);
       $this->SetX(2);
       $this->Image('imagens/logofamap.jpg',8,8,30,10);
       $this->SetFont('Arial','B',12);
       $this->Cell(205,15,'Prescrição de Nutrição Parenteral',0,0,'C');
   }

   function Footer() {
       $this->SetY(-22);
       $this->SetFont('Arial','B',7);
       $this->SetX(65);
       $this->Cell(80,4,'Famap Nutrição Parenteral Ltda',0,0,'C');
       $this->SetY(-19);
       $this->SetX(65);
       $this->Cell(80,4,'Rua Coronel Jairo Pereira, 599 3 andar - Palmares - Cep: 31160-560 - Fone/Fax: (31) 3449-4700 - Belo Horizonte - MG',0,0,'C');
       $this->SetY(-16);
       $this->SetX(65);
       $this->Cell(80,4,'famap@famap.com.br - www.famap.com.br',0,0,'C');
	   
       $this->SetY(-14);
       $this->SetFont('Arial','B',7);
       $this->Cell(0,10,'Page '.$this->PageNo(),0,0,'C');
   }

    function Codabar($xpos, $ypos, $code, $start='A', $end='A', $basewidth=0.35, $height=6) {
        $barChar = array (
            '0' => array (6.5, 10.4, 6.5, 10.4, 6.5, 24.3, 17.9),
            '1' => array (6.5, 10.4, 6.5, 10.4, 17.9, 24.3, 6.5),
            '2' => array (6.5, 10.0, 6.5, 24.4, 6.5, 10.0, 18.6),
            '3' => array (17.9, 24.3, 6.5, 10.4, 6.5, 10.4, 6.5),
            '4' => array (6.5, 10.4, 17.9, 10.4, 6.5, 24.3, 6.5),
            '5' => array (17.9,    10.4, 6.5, 10.4, 6.5, 24.3, 6.5),
            '6' => array (6.5, 24.3, 6.5, 10.4, 6.5, 10.4, 17.9),
            '7' => array (6.5, 24.3, 6.5, 10.4, 17.9, 10.4, 6.5),
            '8' => array (6.5, 24.3, 17.9, 10.4, 6.5, 10.4, 6.5),
            '9' => array (18.6, 10.0, 6.5, 24.4, 6.5, 10.0, 6.5),
            '$' => array (6.5, 10.0, 18.6, 24.4, 6.5, 10.0, 6.5),
            '-' => array (6.5, 10.0, 6.5, 24.4, 18.6, 10.0, 6.5),
            ':' => array (16.7, 9.3, 6.5, 9.3, 16.7, 9.3, 14.7),
            '/' => array (14.7, 9.3, 16.7, 9.3, 6.5, 9.3, 16.7),
            '.' => array (13.6, 10.1, 14.9, 10.1, 17.2, 10.1, 6.5),
            '+' => array (6.5, 10.1, 17.2, 10.1, 14.9, 10.1, 13.6),
            'A' => array (6.5, 8.0, 19.6, 19.4, 6.5, 16.1, 6.5),
            'T' => array (6.5, 8.0, 19.6, 19.4, 6.5, 16.1, 6.5),
            'B' => array (6.5, 16.1, 6.5, 19.4, 6.5, 8.0, 19.6),
            'N' => array (6.5, 16.1, 6.5, 19.4, 6.5, 8.0, 19.6),
            'C' => array (6.5, 8.0, 6.5, 19.4, 6.5, 16.1, 19.6),
            '*' => array (6.5, 8.0, 6.5, 19.4, 6.5, 16.1, 19.6),
            'D' => array (6.5, 8.0, 6.5, 19.4, 19.6, 16.1, 6.5),
            'E' => array (6.5, 8.0, 6.5, 19.4, 19.6, 16.1, 6.5),
        );
		
		$codigo = str_pad($code, 9, "0", STR_PAD_LEFT); 
		$code = $codigo;  
        $this->SetFillColor(0);
        $code = strtoupper($start.$code.$end);
        for($i=0; $i<strlen($code); $i++){
            $char = $code[$i];
            if(!isset($barChar[$char])){
                $this->Error('Invalid character in barcode: '.$char);
            }
            $seq = $barChar[$char];
            for($bar=0; $bar<7; $bar++){
                $lineWidth = $basewidth*$seq[$bar]/6.5;
                if($bar % 2 == 0){
                    $this->Rect($xpos, $ypos, $lineWidth, $height, 'F');
                }
                $xpos += $lineWidth;
            }
            $xpos += $basewidth*10.4/6.5;
         }
     }
}
?>

