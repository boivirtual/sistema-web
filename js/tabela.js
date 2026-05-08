/**TABELA DE ANIMAIS*/

$(document).ready(function(){

    $("[data-toggle="tooltip"]").tooltip();  

    $('#tabela_animais').DataTable({
        "responsive": true,
        "paging":   false,
        "ordering": true,
        "info":     true,
        "order": [[ 1, "asc" ], [ 0, 'asc' ]],
        "language": {
        //"oPaginate": {
        //    "sFirst": "Primeira",
        //    "sLast": "Última",
        //    "sNext": "Próxima",
        //    "sPrevious": "Anterior"
       // },
        "sSearch": "Busca:",
       // "lengthMenu": "Mostrando _MENU_ registros por página",
        "zeroRecords": "Nada encontrado",
        "info": "Registros encontrados: _END_ ",
        "infoEmpty": "Nenhum registro disponível",
        "infoFiltered": "(filtrado de _MAX_ registros no total)",
        }
    });

    $('#tabela_pesagem').DataTable({
        "responsive": true,
        "paging":   false,
        "ordering": true,
        "info":     true,
        //"order": [[ 1, "asc" ], [ 0, 'asc' ]],
        "language": {
     // "oPaginate": {
     //   "sFirst": "Primeira",
     //   "sLast": "Última",
     //   "sNext": "Próxima",
     //   "sPrevious": "Anterior"
    // },
        "sSearch": "Busca:",
       // "lengthMenu": "Mostrando _MENU_ registros por página",
        "zeroRecords": "Nada encontrado",
        "info": "Registros encontrados: _END_ ",
        "infoEmpty": "Nenhum registro disponível",
        "infoFiltered": "(filtrado de _MAX_ registros no total)",
        }
    });

    $('#tabela_previsao_conta').DataTable({
        "responsive": true,
        "paging":   false,
        "ordering": true,
        "info":     true,
        "language": {
          "sSearch": "Buscar na lista:",
          "zeroRecords": "Nada encontrado",
          "info": "Registros encontrados: _END_ ",
          "infoEmpty": "Nenhum registro disponível",
          "infoFiltered": "(filtrado de _MAX_ registros no total)",
        }
    });

    $('#tabela_pastos').DataTable({
        "responsive": true,
        "paging":   false,
        "ordering": true,
        "info":     true,
        "language": {
     // "oPaginate": {
     //   "sFirst": "Primeira",
     //   "sLast": "Última",
     //   "sNext": "Próxima",
     //   "sPrevious": "Anterior"
    // },
        "sSearch": "Busca:",
       // "lengthMenu": "Mostrando _MENU_ registros por página",
        "zeroRecords": "Nada encontrado",
        "info": "Registros encontrados: _END_ ",
        "infoEmpty": "Nenhum registro disponível",
        "infoFiltered": "(filtrado de _MAX_ registros no total)",
        }
    });

    $('#tabela_movimentacao').DataTable({
        "responsive": true,
        "paging":   false,
        "ordering": true,
        "info":     true,
        "language": {
        //"order": [[ 0, "desc" ], [ 1, 'desc' ]],
     // "oPaginate": {
     //   "sFirst": "Primeira",
     //   "sLast": "Última",
     //   "sNext": "Próxima",
     //   "sPrevious": "Anterior"
    // },
        "sSearch": "Busca:",
       // "lengthMenu": "Mostrando _MENU_ registros por página",
        "zeroRecords": "Nada encontrado",
        "info": "Registros encontrados: _END_ ",
        "infoEmpty": "Nenhum registro disponível",
        "infoFiltered": "(filtrado de _MAX_ registros no total)",
        }
    });

    $('#tabela_compra_venda').DataTable({
        "responsive": true,
        "paging":   false,
        "ordering": true,
        "info":     true,
        "language": {
        "order": [[ 0, "desc" ]],
     // "oPaginate": {
     //   "sFirst": "Primeira",
     //   "sLast": "Última",
     //   "sNext": "Próxima",
     //   "sPrevious": "Anterior"
    // },
        "sSearch": "Busca:",
       // "lengthMenu": "Mostrando _MENU_ registros por página",
        "zeroRecords": "Nada encontrado",
        "info": "Registros encontrados: _END_ ",
        "infoEmpty": "Nenhum registro disponível",
        "infoFiltered": "(filtrado de _MAX_ registros no total)",
        }
    });

    $('#tabela_usuarios').DataTable({
        "paging":   false,
        "ordering": true,
        "info":     true,
        "language": {
     // "oPaginate": {
     //   "sFirst": "Primeira",
     //   "sLast": "Última",
     //   "sNext": "Próxima",
     //   "sPrevious": "Anterior"
    // },
        "sSearch": "Busca:",
       // "lengthMenu": "Mostrando _MENU_ registros por página",
        "zeroRecords": "Nada encontrado",
        "info": "Registros encontrados: _END_ ",
        "infoEmpty": "Nenhum registro disponível",
        "infoFiltered": "(filtrado de _MAX_ registros no total)",
        }
    });
    
});

