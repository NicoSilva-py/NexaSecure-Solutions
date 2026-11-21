
<?php
header("Content-Type: application/json; charset=UTF-8");
require_once "conexao.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode(
        ["erro" => "Use o método POST para cadastrar um ativo."],
        JSON_UNESCAPED_UNICODE
    );
    exit;
}

$dados = json_decode(file_get_contents("php://input"), true);

if (!is_array($dados)) {
    http_response_code(400);
    echo json_encode(
        ["erro" => "Envie os dados em formato JSON."],
        JSON_UNESCAPED_UNICODE
    );
    exit;
}

$camposObrigatorios = [
    "cliente_id",
    "nome_ativo",
    "tipo",
    "nivel_risco"
];

foreach ($camposObrigatorios as $campo) {
    if (!isset($dados[$campo]) || trim((string)$dados[$campo]) === "") {
        http_response_code(400);
        echo json_encode(
            ["erro" => "O campo $campo é obrigatório."],
            JSON_UNESCAPED_UNICODE
        );
        exit;
    }
}

$clienteId = filter_var(
    $dados["cliente_id"],
    FILTER_VALIDATE_INT,
    ["options" => ["min_range" => 1]]
);

if ($clienteId === false) {
    http_response_code(400);
    echo json_encode(
        ["erro" => "O cliente_id deve ser um número inteiro positivo."],
        JSON_UNESCAPED_UNICODE
    );
    exit;
}

try {
    $sql = "INSERT INTO ativos
            (cliente_id, nome_ativo, tipo, nivel_risco,
             endereco_ip, sistema_operacional)
            VALUES
            (:cliente_id, :nome_ativo, :tipo, :nivel_risco,
             :endereco_ip, :sistema_operacional)";

    $consulta = $conexao->prepare($sql);

    $consulta->execute([
        ":cliente_id" => $clienteId,
        ":nome_ativo" => trim($dados["nome_ativo"]),
        ":tipo" => trim($dados["tipo"]),
        ":nivel_risco" => trim($dados["nivel_risco"]),
        ":endereco_ip" => $dados["endereco_ip"] ?? null,
        ":sistema_operacional" => $dados["sistema_operacional"] ?? null
    ]);

    http_response_code(201);
    echo json_encode([
        "mensagem" => "Ativo cadastrado com sucesso!",
        "id" => $conexao->lastInsertId()
    ], JSON_UNESCAPED_UNICODE);

} catch (PDOException $erro) {
    if ($erro->getCode() === "23000") {
        http_response_code(400);
        echo json_encode(
            ["erro" => "Não foi possível cadastrar. Confira se o cliente existe e se os dados são válidos."],
            JSON_UNESCAPED_UNICODE
        );
    } else {
        http_response_code(500);
        echo json_encode(
            ["erro" => "Erro interno ao cadastrar o ativo."],
            JSON_UNESCAPED_UNICODE
        );
    }
}
