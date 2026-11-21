
<?php

header("Content-Type: application/json; charset=UTF-8");

require_once "conexao.php";

try {

    $sql = "SELECT * FROM ordens_servico";

    $consulta = $conexao->query($sql);

    $ordens = $consulta->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(
        $ordens,
        JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
    );

} catch (PDOException $erro) {

    http_response_code(500);

    echo json_encode([
        "erro" => "Não foi possível consultar as ordens de serviço."
    ], JSON_UNESCAPED_UNICODE);

}

