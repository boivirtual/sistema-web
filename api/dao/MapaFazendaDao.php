<?php
/**
 * Mapa (GeoJSON dos pastos) das fazendas para o Mapa Satélite do app.
 *
 * Usa as MESMAS funções do sistema web (funcao_mapa_fazenda.php e
 * funcao_modulo_pasto_cor.php): o mapa vem de tbl_mapa_fazenda quando a
 * conta já tem a tabela, senão do arquivo mapa/{cnpj}/{fazenda}.json — o
 * app enxerga sempre exatamente o mesmo mapa que o Editor de Mapa grava.
 */
require_once __DIR__ . "/../../funcao_mapa_fazenda.php";
require_once __DIR__ . "/../../funcao_modulo_pasto_cor.php";

class MapaFazendaDao{

    private $con;
    private $banco;

    /** $con: conexão já aberta para reaproveitar; sem ela, abre uma nova. */
    public function __construct($banco, $con = null){
        $this->banco = $banco;
        if ($con) {
            $this->con = $con;
        } else {
            require __DIR__ . "/../../conecta_mysql_credenciais.inc";
            $this->con = mysqli_connect($servidor, $usuario_bd, $senha_bd, $banco);
        }
    }

    /** GeoJSON (texto) e versão (md5) do mapa da fazenda; json '' = sem
     *  mapa. A fazenda é identificada como no web: id com 9 dígitos. */
    public function lerMapa($local){
        $local9 = str_pad((string) (int) $local, 9, '0', STR_PAD_LEFT);
        return mapa_ler($this->con, $this->banco, $local9);
    }

    /** Módulos ativos com a cor de cada um (mesma regra do web). */
    public function listarModulosComCor(){
        return ler_modulos_pasto($this->con);
    }

    /** Coordenada cadastrada da fazenda (centro do mapa quando ainda não
     *  há pastos desenhados). */
    public function coordenadasFazenda($local){
        $local = (int) $local;
        $r = mysqli_query($this->con, "SELECT tbl_pessoa_latitude_fazenda, tbl_pessoa_longitude_fazenda
            FROM tbl_pessoa WHERE tbl_pessoa_id = {$local}");
        $row = $r ? mysqli_fetch_assoc($r) : null;
        return [
            'latitude'  => $row ? (float) $row['tbl_pessoa_latitude_fazenda'] : 0,
            'longitude' => $row ? (float) $row['tbl_pessoa_longitude_fazenda'] : 0,
        ];
    }
}
