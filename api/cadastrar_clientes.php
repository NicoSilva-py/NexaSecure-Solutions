
<?php

header("Content-Type: application/json; charset=UTF-8");

require_once "conexao.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);

    echo json_encode([
        "erro" => "Use o método POST para cadastrar um cliente."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

$dados = json_decode(file_get_contents("php://input"), true);

$camposObrigatorios = [
    "nome_empresa",
    "cnpj",
    "email"
];

foreach ($camposObrigatorios as $campo) {
    if (empty($dados[$campo])) {
        http_response_code(400);

        echo json_encode([
            "erro" => "O campo $campo é obrigatório."
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }
}

try {
    $sql = "INSERT INTO clientes
            (nome_empresa, cnpj, email, telefone, endereco)
            VALUES
            (:nome_empresa, :cnpj, :email, :telefone, :endereco)";

    $consulta = $conexao->prepare($sql);

    $consulta->execute([
        ":nome_empresa" => $dados["nome_empresa"],
        ":cnpj" => $dados["cnpj"],
        ":email" => $dados["email"],
        ":telefone" => $dados["telefone"] ?? null,
        ":endereco" => $dados["endereco"] ?? null
    ]);

    http_response_code(201);

    echo json_encode([
        "mensagem" => "Cliente cadastrado com sucesso!",
        "id" => $conexao->lastInsertId()
    ], JSON_UNESCAPED_UNICODE);

} catch (PDOException $erro) {
    if ($erro->getCode() === "23000") {
        http_response_code(409);

        echo json_encode([
            "erro" => "Não foi possível cadastrar. Verifique se o CNPJ já existe."
        ], JSON_UNESCAPED_UNICODE);
    } else {
        http_response_code(500);

        echo json_encode([
            "erro" => "Erro interno ao cadastrar o cliente."
        ], JSON_UNESCAPED_UNICODE);
    }
}
