function initializeJS() {

    //tool tips
    jQuery('.tooltips').tooltip();

    //popovers
    jQuery('.popovers').popover();

    //custom scrollbar
        //for html
    // jQuery("html").niceScroll({styler:"fb",cursorcolor:"#007AFF", cursorwidth: '6', cursorborderradius: '10px', background: '#F7F7F7', cursorborder: '', zindex: '1000'});
        //for sidebar
    // jQuery("#sidebar").niceScroll({styler:"fb",cursorcolor:"#007AFF", cursorwidth: '3', cursorborderradius: '10px', background: '#F7F7F7', cursorborder: ''});
        // for scroll panel
    jQuery(".scroll-panel").niceScroll({styler:"fb",cursorcolor:"#007AFF", cursorwidth: '3', cursorborderradius: '10px', background: '#F7F7F7', cursorborder: ''});
    
    //sidebar dropdown menu
    jQuery('#sidebar .sub-menu > a').click(function () {
        var last = jQuery('.sub-menu.open', jQuery('#sidebar'));        
        jQuery('.menu-arrow').removeClass('arrow_carrot-right');
        jQuery('.sub', last).slideUp(200);
        var sub = jQuery(this).next();
        if (sub.is(":visible")) {
            jQuery('.menu-arrow').addClass('arrow_carrot-right');            
            sub.slideUp(200);
        } else {
            jQuery('.menu-arrow').addClass('arrow_carrot-down');            
            sub.slideDown(200);
        }
        var o = (jQuery(this).offset());
        diff = 200 - o.top;
        if(diff>0)
            jQuery("#sidebar").scrollTo("-="+Math.abs(diff),500);
        else
            jQuery("#sidebar").scrollTo("+="+Math.abs(diff),500);
    });

    // sidebar menu toggle
    jQuery(function() {
        function responsiveView() {
            var wSize = jQuery(window).width();
            if (wSize <= 768) {
                jQuery('#container').addClass('sidebar-close');
                jQuery('#sidebar > ul').hide();
            }

            if (wSize > 768) {
                jQuery('#container').removeClass('sidebar-close');
                jQuery('#sidebar > ul').show();
            }
        }
        jQuery(window).on('load', responsiveView);
        jQuery(window).on('resize', responsiveView);
    });

    jQuery('.toggle-nav').click(function () {
        if (jQuery('#sidebar > ul').is(":visible") === true) {
            jQuery('#main-content').css({
                'margin-left': '0px'
            });
            jQuery('#sidebar').css({
                'margin-left': '-180px'
            });
            jQuery('#sidebar > ul').hide();
            jQuery("#container").addClass("sidebar-closed");
        } else {
            jQuery('#main-content').css({
                'margin-left': '180px'
            });
            jQuery('#sidebar > ul').show();
            jQuery('#sidebar').css({
                'margin-left': '0'
            });
            jQuery("#container").removeClass("sidebar-closed");
        }
    });

    //bar chart
    if (jQuery(".custom-custom-bar-chart")) {
        jQuery(".bar").each(function () {
            var i = jQuery(this).find(".value").html();
            jQuery(this).find(".value").html("");
            jQuery(this).find(".value").animate({
                height: i
            }, 2000)
        })
    }

}

jQuery(document).ready(function(){
    initializeJS();
});

/** permite digitar somente letras minusculas */
function minuscula(lstr){ 
    var str=lstr.value; 
    lstr.value=str.toLowerCase(); 
}

/** permite digitar somente letras minusculas */
function maiuscula(lstr){ 
    var str=lstr.value; 
    lstr.value=str.toUpperCase(); 
}

// a função principal de validação CPF e CNPJ
function validar(obj) { // recebe um objeto
    
    var tipo_pessoa = $("input[name='tipo_pessoa']:checked").val();

    var s = (obj.value).replace(/\D/g,'');

    if (s==""){
        return false;
    }
    
    var tam=(s).length; // removendo os caracteres não numéricos
    if (!(tam==11 || tam==14)){ // validando o tamanho
        alert("'"+s+"' Não é um CPF ou um CNPJ válido!" ); // tamanho inválido
        document.getElementById("documento_pessoa").value="";
        document.getElementById("documento_pessoa").focus();
    }
    
// se for CPF

    if (tam==11){
        if (tipo_pessoa=='F'){
            if (!validaCPF(s)){ // chama a função que valida o CPF
                alert("'"+s+"' Não é um código válido!" ); // se quiser mostrar o erro
                document.getElementById("documento_pessoa").value="";
                document.getElementById("documento_pessoa").focus();
                return false;
            }
            else {
            obj.value=maskCPF(s);   // se validou o CPF mascaramos corretamente
            
                if (flag_gravar=="I"){ 
                
                    var idcnpj_cpf=s;

                    if (for_cli=="C") {
                        var codigo_cliente=0;
                        var tipo="";
     
                        $.post("ler_cliente.php",{idcnpj_cpf: idcnpj_cpf, codigo_cliente: codigo_cliente, tipo: tipo}, function(valor){
                            var txt = valor; 
                            var php=txt.split("<|>");
                
                            var cnpj_cpf_lido= (php[1]).replace(/\D/g,''); 
                            var cnpj_cpf_dig= "000" + s; 

                            if(cnpj_cpf_lido==cnpj_cpf_dig){
                                alert("Cliente ja existe para esse CPF");
                                document.getElementById("documento_pessoa").value="";
                                document.getElementById("documento_pessoa").focus();
                                return false
                            }
                            else {
                                return true;
                            }                   
                        });
                    }
                    else if (for_cli=="P") {

                        var codigo_fonte_pagadora=0;
                        var tipo="";
     
                        $.post("ler_fonte_pagadora.php",{idcnpj_cpf: idcnpj_cpf, 
                                                         idfontepagadora: codigo_fonte_pagadora, 
                                                         tipo: tipo}, function(valor){
                            var txt = valor; 
                            var php=txt.split("<|>");
                
                            var cnpj_cpf_lido= (php[2]).replace(/\D/g,''); 
                            var cnpj_cpf_dig= "000" + s; 

                            if(cnpj_cpf_lido==cnpj_cpf_dig){
                                alert("Fonte Pagadora ja existe para esse CPF");
                                document.getElementById("documento_pessoa").value="";
                                document.getElementById("documento_pessoa").focus();
                                return false
                            }
                            else {
                                return true;
                            }                   
                        });
                    }
                    else if (for_cli=="F") {

                        var codigo_fornecedor=0;
                        var tipo="";
                        var numero_nota=0;
                        $.post("ler_fornecedor.php",{idcnpj_cpf: idcnpj_cpf, codigo_fornecedor: codigo_fornecedor, tipo: tipo, numero_nota:numero_nota}, function(valor){
                            var txt = valor; 
                            var php=txt.split("<|>");
                
                            var cnpj_cpf_lido= (php[1]).replace(/\D/g,''); 
                            var cnpj_cpf_dig= "000" + s; 

                            if(cnpj_cpf_lido==cnpj_cpf_dig){
                                alert("Fornecedor ja existe para esse CPF");
                                document.getElementById("documento_pessoa").value="";
                                document.getElementById("documento_pessoa").focus();
                                return false
                            }
                            else {
                                return true;
                            }                   
                        });
                    }
                    else if (for_cli=="T") {
                        var codigo_tra=0;
                        var tipo="";
                        $.post("ler_transportadora.php",{idcnpj_cpf: idcnpj_cpf, codigo_tra: codigo_tra, tipo: tipo}, function(valor){
                            var txt = valor; 
                            var php=txt.split("<|>");
                
                            var cnpj_cpf_lido= (php[1]).replace(/\D/g,''); 
                            var cnpj_cpf_dig= "000" + s; 

                            if(cnpj_cpf_lido==cnpj_cpf_dig){
                                alert("Transportadora ja existe para esse CPF");
                                document.getElementById("documento_pessoa").value="";
                                document.getElementById("documento_pessoa").focus();
                                return false
                            }
                            else {
                                return true;
                            }                   
                        });
                    }
                    else {
                        $.post("ler_vendedor.php",{idcnpj_cpf: idcnpj_cpf}, function(valor){
                            var txt = valor; 
                            var php=txt.split("<|>");
                
                            var cnpj_cpf_lido= (php[1]).replace(/\D/g,''); 
                            var cnpj_cpf_dig= "000" + s; 

                            if(cnpj_cpf_lido==cnpj_cpf_dig){
                                alert("Representente ja existe para esse CPF");
                                document.getElementById("documento_pessoa").value="";
                                document.getElementById("documento_pessoa").focus();
                                return false
                            }
                            else {
                                return true;
                            }                   
                        });
                    }
                }
            }
        }
        else {
            alert("Código inválido para esse Tipo de pessoa");
            document.getElementById("documento_pessoa").value="";
            document.getElementById("documento_pessoa").focus();
            return false;
        }
    }
    
// se for CNPJ          
    if (tam==14){
        
        if (tipo_pessoa=='J'){
            if(!validaCNPJ(s)){ // chama a função que valida o CNPJ
                alert("'"+s+"' Não é um código válido!" ); // se quiser mostrar o erro
                document.getElementById("documento_pessoa").value="";
                document.getElementById("documento_pessoa").focus();
                return false;           
            }
            else {
                obj.value=maskCNPJ(s);  // se validou o CNPJ mascaramos corretamente
                if (flag_gravar=="I"){ 
                
                    var idcnpj_cpf=s;
                    
                    if (for_cli=="C") {
                        var codigo_cliente=0;
                        var tipo="";
                        $.post("ler_cliente.php",{idcnpj_cpf: idcnpj_cpf, codigo_cliente: codigo_cliente, tipo: tipo}, function(valor){
                            var txt = valor; 
                            var php=txt.split("<|>");
                
                            var cnpj_cpf_lido= (php[1]).replace(/\D/g,''); 
                            var cnpj_cpf_dig=s; 

                            if(cnpj_cpf_lido==cnpj_cpf_dig){
                                alert("Cliente ja existe para esse CNPJ");
                                document.getElementById("documento_pessoa").value="";
                                document.getElementById("documento_pessoa").focus();
                                return false
                            }
                            else {
                                return true;
                            }                   
                        });
                    }

                    else if (for_cli=="P") {

                        var codigo_fonte_pagadora=0;
                        var tipo="";
     
                        $.post("ler_fonte_pagadora.php",{idcnpj_cpf: idcnpj_cpf, 
                                                         idfontepagadora: codigo_fonte_pagadora, 
                                                         tipo: tipo}, function(valor){
                            var txt = valor; 
                            var php=txt.split("<|>");
                
                            var cnpj_cpf_lido= (php[2]).replace(/\D/g,''); 
                            var cnpj_cpf_dig=s; 

                            if(cnpj_cpf_lido==cnpj_cpf_dig){
                                alert("Fonte Pagadora ja existe para esse CNPJ");
                                document.getElementById("documento_pessoa").value="";
                                document.getElementById("documento_pessoa").focus();
                                return false
                            }
                            else {
                                return true;
                            }                   
                        });
                    }
                    else if (for_cli=="F") {
                        var codigo_fornecedor=0;
                        var tipo="";
                        var numero_nota=0;
                        $.post("ler_fornecedor.php",{idcnpj_cpf: idcnpj_cpf, codigo_fornecedor: codigo_fornecedor, tipo: tipo, numero_nota:numero_nota}, function(valor){
                            var txt = valor; 
                            var php=txt.split("<|>");
                
                            var cnpj_cpf_lido= (php[1]).replace(/\D/g,''); 
                            var cnpj_cpf_dig=s; 

                            if(cnpj_cpf_lido==cnpj_cpf_dig){
                                alert("Fornecedor ja existe para esse CNPJ");
                                document.getElementById("documento_pessoa").value="";
                                document.getElementById("documento_pessoa").focus();
                                return false
                            }
                            else {
                                return true;
                            }                   
                        });
                    }
                    else if (for_cli=="T") {
                        var codigo_tra=0;
                        var tipo="";
                        $.post("ler_transportadora.php",{idcnpj_cpf: idcnpj_cpf, codigo_tra: codigo_tra, tipo: tipo}, function(valor){
                            var txt = valor; 
                            var php=txt.split("<|>");
                
                            var cnpj_cpf_lido= (php[1]).replace(/\D/g,''); 
                            var cnpj_cpf_dig=s; 

                            if(cnpj_cpf_lido==cnpj_cpf_dig){
                                alert("Trasnportadora ja existe para esse CNPJ");
                                document.getElementById("documento_pessoa").value="";
                                document.getElementById("documento_pessoa").focus();
                                return false
                            }
                            else {
                                return true;
                            }                   
                        });
                    }
                    else {
                        $.post("ler_vendedor.php",{idcnpj_cpf: idcnpj_cpf}, function(valor){
                            var txt = valor; 
                            var php=txt.split("<|>");
                
                            var cnpj_cpf_lido= (php[1]).replace(/\D/g,''); 
                            var cnpj_cpf_dig=s; 

                            if(cnpj_cpf_lido==cnpj_cpf_dig){
                                alert("Representante ja existe para esse CNPJ");
                                document.getElementById("documento_pessoa").value="";
                                document.getElementById("documento_pessoa").focus();
                                return false
                            }
                            else {
                                return true;
                            }                   
                        });
                        
                    }
                }
            }
        }
        else {
             alert("Código inválido para esse Tipo de pessoa");
             document.getElementById("documento_pessoa").value="";
             document.getElementById("documento_pessoa").focus();
             return false;
        }
    }
}
// fim da funcao validar()

// função que valida CPF
// O algorítimo de validação de CPF é baseado em cálculos
// para o dígito verificador (os dois últimos)
// Não entrarei em detalhes de como funciona
function validaCPF(s) {
    var c = s.substr(0,9);
    var dv = s.substr(9,2);
    var d1 = 0;
    for (var i=0; i<9; i++) {
        d1 += c.charAt(i)*(10-i);
    }
    if (d1 == 0) return false;
    d1 = 11 - (d1 % 11);
    if (d1 > 9) d1 = 0;
    if (dv.charAt(0) != d1){
        return false;
    }
    d1 *= 2;
    for (var i = 0; i < 9; i++) {
        d1 += c.charAt(i)*(11-i);
    }
    d1 = 11 - (d1 % 11);
    if (d1 > 9) d1 = 0;
    if (dv.charAt(1) != d1){
        return false;
    }
    return true;
}

// Função que valida CNPJ
// O algorítimo de validação de CNPJ é baseado em cálculos
// para o dígito verificador (os dois últimos)
// Não entrarei em detalhes de como funciona
function validaCNPJ(CNPJ) {
    var a = new Array();
    var b = new Number;
    var c = [6,5,4,3,2,9,8,7,6,5,4,3,2];
    for (i=0; i<12; i++){
        a[i] = CNPJ.charAt(i);
        b += a[i] * c[i+1];
    }
    if ((x = b % 11) < 2) { a[12] = 0 } else { a[12] = 11-x }
    b = 0;
    for (y=0; y<13; y++) {
        b += (a[y] * c[y]);
    }
    if ((x = b % 11) < 2) { a[13] = 0; } else { a[13] = 11-x; }
    if ((CNPJ.charAt(12) != a[12]) || (CNPJ.charAt(13) != a[13])){
        return false;
    }
    return true;
}

//  função que mascara o CPF
function maskCPF(CPF){
    var cpf_cnpj = CPF;
    cpf_cnpj_editado = cpf_cnpj.substring(0,3) +"."+ 
                       cpf_cnpj.substring(3,6) +"."+ 
                       cpf_cnpj.substring(6,9) +"-"+ 
                       cpf_cnpj.substring(9,11);

    return cpf_cnpj_editado;
}

//  função que mascara o CPF de registros lidos do banco de dados
function maskCPFA(CPF){
    return CPF.substring(3,6)+"."+CPF.substring(6,9)+"."+CPF.substring(9,12)+"-"+CPF.substring(12,14);
}


//  função que mascara o CNPJ
function maskCNPJ(CNPJ){
    return CNPJ.substring(0,2)+"."+CNPJ.substring(2,5)+"."+CNPJ.substring(5,8)+"/"+CNPJ.substring(8,12)+"-"+CNPJ.substring(12,14);  
}
