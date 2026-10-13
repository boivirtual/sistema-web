<?php
/**
 * Mapa de Gado (tela do pasto) — botão NASCIMENTO, chamado pelo aplicativo
 * (só online). Mesmas regras de form_mapa_gados_movimentacao.php do
 * sistema web.
 */
class MapaGadoNascimentoService{

    /**
     * Opções dos selects da tela: "Pai Nº" (grupos SEMEM e ANIMAIS),
     * "Raça" e "Pelagem".
     */
    public function listas($dados){
        $bd = trim((string) ($dados['bd'] ?? ''));
        if ($bd === '') {
            return ["success" => false, "message" => "Informe o bd."];
        }
        $dao = new NascimentoDao($bd);
        return [
            "success"  => true,
            "semens"   => $dao->listarSemens(),
            "machos"   => $dao->listarMachos(),
            "racas"    => $dao->listarRacas(),
            "pelagens" => $dao->listarPelagens(),
        ];
    }
}
