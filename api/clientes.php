
<?php

header("Content-Type: application/json; charset=UTF-8");

require_once "conexao.php";

try {
    $sql = "SELECT id, nome_empresa, cnpj, email, telefone, endereco
            FROM clientes";

    $consulta = $conexao->query($sql);
    $clientes = $consulta->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(
        $clientes,
        JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
    );

} catch (PDOException $erro) {
    http_response_code(500);

    echo json_encode([
        "erro" => "Não foi possível consultar os clientes."
    ], JSON_UNESCAPED_UNICODE);
}
