<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>Máscara para campos monetários com jquery + maskMoney</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <script type="text/javascript" src="js/jquery.js" ></script>
    <script type="text/javascript" src="js/jquery.maskMoney.js" ></script>
    <script type="text/javascript">
        $(document).ready(function(){
              $("input.dinheiro").maskMoney({showSymbol:false, symbol:"", decimal:",", thousands:".", allowZero:true, precision: 6});
        });
    </script>
</head>
<body>
     <h1>Máscara para campos monetários com jquery + maskMoney</h1>
     <form>
            Valor: <input type="text" name="exemplo1" class="dinheiro" />
     </form>
</body>
</html>