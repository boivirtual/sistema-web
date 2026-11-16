/**SCRIPT AUXILIAR*/

$(document).ready(function(){
    $('#seleciona_clientes_comprar').click(function(event) {
        if(this.checked) {
            $('.checkbox2').each(function() {
                this.checked = true; 
            });

        }else{
            $('.checkbox2').each(function() {
                this.checked = false;  
            });         
        }
    });
});

