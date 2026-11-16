<script type='text/javascript'>
<?php
$php_array = array();

$php_array[0]= 'geeks';
$php_array[1]= 'for';
$php_array[2]= 'geeks';

$js_array = json_encode($php_array);
echo "var javascript_array = ". $js_array . ";\n";
?>
document.write(javascript_array[0]);
</script>
  
