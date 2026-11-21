
<?php

$config = require __DIR__ . "/config.local.php";

try {

    $conexao = new PDO(
        "mysql:host={$config['host']};dbname={$config['banco']};charset=utf8mb4",
        $config['usuario'],
        $config['senha']
    );

    $conexao->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION
    );

    $conexao->setAttribute(
        PDO::ATTR_DEFAULT_FETCH_MODE,
        PDO::FETCH_ASSOC
    );

} catch (PDOException $erro) {

    http_response_code(500);

    die(json_encode([
        "erro" => "Erro ao conectar ao banco de dados."
    ], JSON_UNESCAPED_UNICODE));

}
