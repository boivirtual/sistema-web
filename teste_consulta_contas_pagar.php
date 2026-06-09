<?php
$registros = [
    [
        'pago' => true,
        'documento' => '2601071402',
        'parcela' => '001',
        'local' => 'FAZENDA CASA BRANCA',
        'conta' => 'Refeições e despesas de viagens',
        'razao' => 'EXAGRO',
        'emissao' => '02/01/2026',
        'vencimento' => '02/01/2026',
        'valor_parcela' => '76,93',
        'pagamento' => '02/01/2026',
        'valor_pago' => '76,93'
    ],
    [
        'pago' => true,
        'documento' => '2601071656',
        'parcela' => '001',
        'local' => 'FAZENDA PEDRA BONITA',
        'conta' => 'Refeições e despesas de viagens',
        'razao' => 'EXAGRO',
        'emissao' => '02/01/2026',
        'vencimento' => '02/01/2026',
        'valor_parcela' => '153,86',
        'pagamento' => '02/01/2026',
        'valor_pago' => '153,86'
    ],
    [
        'pago' => true,
        'documento' => '000037895',
        'parcela' => '001',
        'local' => 'FAZENDA PEDRA BONITA',
        'conta' => 'Medicamentos',
        'razao' => 'CASA DO AGRICULTOR - JAIME GOMES FERREIRA EPP',
        'emissao' => '03/12/2025',
        'vencimento' => '02/01/2026',
        'valor_parcela' => '73,98',
        'pagamento' => '05/01/2026',
        'valor_pago' => '73,98'
    ],
    [
        'pago' => false,
        'documento' => '000088888',
        'parcela' => '001',
        'local' => 'FAZENDA MODELO',
        'conta' => 'Energia Elétrica',
        'razao' => 'CEMIG',
        'emissao' => '10/05/2026',
        'vencimento' => '20/05/2026',
        'valor_parcela' => '890,00',
        'pagamento' => '',
        'valor_pago' => ''
    ],
    [
        'pago' => false,
        'documento' => '000099999',
        'parcela' => '001',
        'local' => 'FAZENDA CASA BRANCA',
        'conta' => 'Internet',
        'razao' => 'VIVO',
        'emissao' => '15/05/2026',
        'vencimento' => '25/05/2026',
        'valor_parcela' => '149,90',
        'pagamento' => '',
        'valor_pago' => ''
    ]
];

function valorBrParaFloat($valor)
{
    $valor = str_replace('.', '', $valor);
    $valor = str_replace(',', '.', $valor);
    return floatval($valor);
}

function dataBrParaDateTime($data)
{
    return DateTime::createFromFormat('d/m/Y', $data);
}

function formatarMoedaBr($valor)
{
    return number_format($valor, 2, ',', '.');
}

/*
    Para teste, deixei a data de hoje como 20/05/2026,
    assim você consegue ver valores em "Vencem Hoje" e "A Vencer".

    Depois, no sistema real, você pode trocar por:
    $dataHoje = new DateTime();
*/
$dataHoje = dataBrParaDateTime('20/05/2026');
$dataHoje->setTime(0, 0, 0);

$totalVencidos = 0;
$totalVencemHoje = 0;
$totalAVencer = 0;
$totalPagos = 0;
$totalPeriodo = 0;

foreach ($registros as $item) {

    $valorParcela = valorBrParaFloat($item['valor_parcela']);
    $valorPago = valorBrParaFloat($item['valor_pago']);

    $totalPeriodo += $valorParcela;

    if ($item['pago']) {
        $totalPagos += $valorPago;
    } else {

        $dataVencimento = dataBrParaDateTime($item['vencimento']);
        $dataVencimento->setTime(0, 0, 0);

        if ($dataVencimento < $dataHoje) {
            $totalVencidos += $valorParcela;
        } elseif ($dataVencimento == $dataHoje) {
            $totalVencemHoje += $valorParcela;
        } else {
            $totalAVencer += $valorParcela;
        }
    }
}

$totalRegistros = count($registros);
?>

<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <title>Consulta Contas a Pagar</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 4 -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <style>
        body {
            background: #ffffff;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 13px;
        }

        .box-consulta {
            border: 1px solid #333;
            margin: 30px 15px;
            padding: 35px 25px 25px 25px;
            min-height: 690px;
            position: relative;
        }

        .box-titulo {
            position: absolute;
            top: -18px;
            left: 45px;
            background: #fff;
            padding: 0 15px;
            font-size: 24px;
            color: #000;
        }

        .btn-mes,
        .btn-filtro {
            height: 56px;
            background: #d9d9d9;
            color: #777;
            border: 1px solid #c5c5c5;
            font-size: 17px;
        }

        .card-total {
            border: 1px solid #c8c8c8;
            border-top: 1px solid #c8c8c8;
            height: 62px;
            padding-top: 6px;
            text-align: center;
            color: #777;
            font-size: 16px;
            cursor: pointer;
            transition: all .2s ease;
            background: #fff;
        }

        .card-total:hover {
            background: #f8f9fa;
        }

        .card-total .valor {
            font-size: 18px;
            margin-top: -2px;
            font-weight: 400;
        }

        .card-total.ativo .valor {
            font-weight: 600;
            font-size: 19px;
        }

        .card-total.ativo.vermelho {
            border-top: 1px solid #d9534f;
        }

        .card-total.ativo.azul {
            border-top: 1px solid #4a90e2;
        }

        .card-total.ativo.verde {
            border-top: 1px solid #5cb85c;
        }

        .texto-vermelho {
            color: red;
        }

        .texto-azul {
            color: #005ecb;
        }

        .texto-verde {
            color: #00b050;
        }

        .area-info {
            margin-top: 25px;
            margin-bottom: 15px;
        }

        .campo-busca {
            height: 44px;
            border-radius: 0;
        }

        .table {
            font-size: 12px;
            color: #4d5b66;
        }

        .table thead th {
            border-top: 1px solid #ddd;
            border-bottom: 2px solid #ddd;
            font-weight: 600;
            vertical-align: middle;
            white-space: nowrap;
        }

        .table td {
            vertical-align: middle;
            border-top: 1px solid #ddd;
        }

        .check-pago {
            color: green;
            font-size: 17px;
            font-weight: bold;
        }

        .icone-editar,
        .icone-pagar,
        .icone-excluir {
            color: #128cb8;
            font-size: 14px;
            transition: all .2s ease;
        }

        .icone-editar:hover,
        .icone-pagar:hover,
        .icone-excluir:hover {
            color: #0d6efd;
            transform: scale(1.15);
            text-decoration: none;
        }

        .acoes {
            white-space: nowrap;
            min-width: 80px;
        }

        .acoes a {
            display: inline-block;
            margin-right: 5px;
        }

        .acoes a:last-child {
            margin-right: 0;
        }

        #tabela_contas_pagar th:last-child,
        #tabela_contas_pagar td:last-child {
            width: 80px;
            min-width: 80px;
            white-space: nowrap;
        }

        .ordenacao {
            color: #c0c0c0;
            font-size: 10px;
            margin-left: 5px;
        }
    </style>
</head>

<body>

<div class="container-fluid">

    <div class="box-consulta">

        <div class="box-titulo">Consulta Contas a Pagar</div>

        <div class="row mb-4">
            <div class="col-md-4 col-sm-8">
                <div class="btn-group w-100">
                    <button type="button" class="btn btn-mes" style="max-width: 55px;">&lt;</button>
                    <button type="button" class="btn btn-mes flex-fill">Maio 2026</button>
                    <button type="button" class="btn btn-mes" style="max-width: 55px;">&gt;</button>
                </div>
            </div>

            <div class="col-md-2 col-sm-4">
                <button type="button" class="btn btn-filtro btn-block" data-toggle="modal" data-target="#modalMaisFiltros">
                    Mais Filtros
                </button>
            </div>
        </div>

        <div class="row no-gutters mt-4">

            <div class="col">
                <div class="card-total vermelho" data-filtro="vencidos">
                    <div>Vencidos R$</div>
                    <div class="valor texto-vermelho">
                        <?php echo formatarMoedaBr($totalVencidos); ?>
                    </div>
                </div>
            </div>

            <div class="col">
                <div class="card-total vermelho" data-filtro="vencem_hoje">
                    <div>Vencem Hoje R$</div>
                    <div class="valor texto-vermelho">
                        <?php echo formatarMoedaBr($totalVencemHoje); ?>
                    </div>
                </div>
            </div>

            <div class="col">
                <div class="card-total azul" data-filtro="a_vencer">
                    <div>A Vencer R$</div>
                    <div class="valor texto-azul">
                        <?php echo formatarMoedaBr($totalAVencer); ?>
                    </div>
                </div>
            </div>

            <div class="col">
                <div class="card-total verde" data-filtro="pagos">
                    <div>Pagos R$</div>
                    <div class="valor texto-verde">
                        <?php echo formatarMoedaBr($totalPagos); ?>
                    </div>
                </div>
            </div>

            <div class="col">
                <div class="card-total azul" data-filtro="total_periodo">
                    <div>Total do Período R$</div>
                    <div class="valor texto-azul">
                        <?php echo formatarMoedaBr($totalPeriodo); ?>
                    </div>
                </div>
            </div>

        </div>

        <div class="row area-info align-items-center">
            <div class="col-md-6 text-muted">
                Registros encontrados: <?php echo $totalRegistros; ?>
            </div>

            <div class="col-md-6">
                <div class="form-inline justify-content-end">
                    <label class="mr-2 text-muted">Buscar na Lista:</label>
                    <input type="text" class="form-control campo-busca" id="buscar_lista">
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-hover" id="tabela_contas_pagar">
                <thead>
                    <tr>
                        <th class="text-center"><input type="checkbox"></th>
                        <th></th>
                        <th>Documento <span class="ordenacao">↕</span></th>
                        <th>Parcela <span class="ordenacao">↕</span></th>
                        <th>Local <span class="ordenacao">↕</span></th>
                        <th>Conta <span class="ordenacao">↕</span></th>
                        <th>Razão<br>Social/Nome <span class="ordenacao">↕</span></th>
                        <th>Emissão <span class="ordenacao">↕</span></th>
                        <th>Vencimento <span class="ordenacao">↕</span></th>
                        <th class="text-right">Valor<br>Parcela <span class="ordenacao">↕</span></th>
                        <th>Pagamento <span class="ordenacao">↕</span></th>
                        <th class="text-right">Valor<br>Pago <span class="ordenacao">↕</span></th>
                        <th class="text-center">Ações</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($registros as $item) { ?>
                        <tr>
                            <td></td>

                            <td class="text-center">
                                <?php if ($item['pago']) { ?>
                                    <span class="check-pago">✓</span>
                                <?php } ?>
                            </td>

                            <td><?php echo $item['documento']; ?></td>
                            <td><?php echo $item['parcela']; ?></td>
                            <td><?php echo $item['local']; ?></td>
                            <td><?php echo $item['conta']; ?></td>
                            <td><?php echo $item['razao']; ?></td>
                            <td><?php echo $item['emissao']; ?></td>
                            <td><?php echo $item['vencimento']; ?></td>
                            <td class="text-right"><?php echo $item['valor_parcela']; ?></td>
                            <td><?php echo $item['pagamento']; ?></td>
                            <td class="text-right"><?php echo $item['valor_pago']; ?></td>

                            <td class="text-center acoes">

                                <a href="#" class="icone-editar" title="Editar">
                                    <i class="fas fa-pen"></i>
                                </a>

                                <?php if (!$item['pago']) { ?>

                                    <a href="#" class="icone-pagar" title="Pagar Conta">
                                        <i class="far fa-check-circle"></i>
                                    </a>

                                    <a href="#" class="icone-excluir" title="Excluir">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>

                                <?php } ?>

                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

    </div>

</div>

<!-- Modal Mais Filtros -->
<div class="modal fade" id="modalMaisFiltros" tabindex="-1" role="dialog" aria-labelledby="modalMaisFiltrosLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="modalMaisFiltrosLabel">
                    Consultar Contas a Pagar
                </h5>

                <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">

                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label>Data Inicial</label>
                        <input type="date" class="form-control" id="data_inicial" value="2026-01-01">
                    </div>

                    <div class="form-group col-md-4">
                        <label>Data Final</label>
                        <input type="date" class="form-control" id="data_final" value="2026-06-04">
                    </div>

                    <div class="form-group col-md-4">
                        <label>Tipo de Data</label>

                        <div class="mt-2">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="tipo_data" id="tipo_vencimento" value="vencimento" checked>
                                <label class="form-check-label" for="tipo_vencimento">Vencimento</label>
                            </div>

                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="tipo_data" id="tipo_emissao" value="emissao">
                                <label class="form-check-label" for="tipo_emissao">Emissão</label>
                            </div>

                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="tipo_data" id="tipo_pagamento" value="pagamento">
                                <label class="form-check-label" for="tipo_pagamento">Pagamento</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Local</label>
                        <select class="form-control" id="local">
                            <option value="">Todos</option>
                            <option value="FAZENDA CASA BRANCA">FAZENDA CASA BRANCA</option>
                            <option value="FAZENDA PEDRA BONITA">FAZENDA PEDRA BONITA</option>
                            <option value="FAZENDA MODELO">FAZENDA MODELO</option>
                        </select>
                    </div>

                    <div class="form-group col-md-6">
                        <label>Centro de Custo</label>
                        <select class="form-control" id="centro_custo">
                            <option value="">Todos</option>
                            <option value="ADMINISTRATIVO">ADMINISTRATIVO</option>
                            <option value="PECUÁRIA">PECUÁRIA</option>
                            <option value="REPRODUÇÃO">REPRODUÇÃO</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Fornecedor</label>
                        <select class="form-control" id="fornecedor">
                            <option value="">Todos</option>
                            <option value="EXAGRO">EXAGRO</option>
                            <option value="CEMIG">CEMIG</option>
                            <option value="VIVO">VIVO</option>
                            <option value="CASA DO AGRICULTOR">CASA DO AGRICULTOR</option>
                        </select>
                    </div>

                    <div class="form-group col-md-6">
                        <label>Conta Contábil</label>
                        <select class="form-control" id="conta_contabil">
                            <option value="">Todas ou clique p/ selecionar contas</option>
                            <option value="REFEIÇÕES E DESPESAS">Refeições e despesas de viagens</option>
                            <option value="MEDICAMENTOS">Medicamentos</option>
                            <option value="ENERGIA ELÉTRICA">Energia Elétrica</option>
                            <option value="INTERNET">Internet</option>
                        </select>
                    </div>
                </div>

            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    Fechar
                </button>

                <button type="button" class="btn btn-primary" id="btn_consultar_filtros">
                    Consultar
                </button>
            </div>

        </div>
    </div>
</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

<!-- Popper -->
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>

<!-- Bootstrap JS -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script>
    $(document).ready(function () {

        $('.card-total').on('click', function () {

            $('.card-total').removeClass('ativo');

            $(this).addClass('ativo');

            var filtroSelecionado = $(this).data('filtro');

            console.log('Filtro selecionado:', filtroSelecionado);

            /*
                Aqui depois você pode chamar sua função:

                if (filtroSelecionado === 'vencidos') {
                    listarContasVencidas();
                }

                if (filtroSelecionado === 'vencem_hoje') {
                    listarContasVencemHoje();
                }

                if (filtroSelecionado === 'a_vencer') {
                    listarContasAVencer();
                }

                if (filtroSelecionado === 'pagos') {
                    listarContasPagas();
                }

                if (filtroSelecionado === 'total_periodo') {
                    listarTotalPeriodo();
                }
            */
        });

    });
</script>

<script>
    $('#btn_consultar_filtros').on('click', function () {
        var filtros = {
            data_inicial: $('#data_inicial').val(),
            data_final: $('#data_final').val(),
            tipo_data: $('input[name="tipo_data"]:checked').val(),
            local: $('#local').val(),
            centro_custo: $('#centro_custo').val(),
            fornecedor: $('#fornecedor').val(),
            conta_contabil: $('#conta_contabil').val()
        };

        console.log(filtros);

        $('#modalMaisFiltros').modal('hide');

        /*
            Depois você pode chamar sua função aqui:

            consultarContasPagar(filtros);
        */
    });
</script>
</body>
</html>