
<?php
header("Content-Type: application/json; charset=UTF-8");
require_once "conexao.php";

try {
    $sql = "SELECT
                ativos.id,
                ativos.nome_ativo,
                ativos.tipo,
                ativos.nivel_risco,
                ativos.endereco_ip,
                ativos.sistema_operacional,
                clientes.nome_empresa AS cliente
            FROM ativos
            INNER JOIN clientes
                ON ativos.cliente_id = clientes.id";

    $consulta = $conexao->query($sql);
    $ativos = $consulta->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(
        $ativos,
        JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
    );

} catch (PDOException $erro) {
    http_response_code(500);
    echo json_encode(
        ["erro" => "Não foi possível consultar os ativos."],
        JSON_UNESCAPED_UNICODE
    );
}
