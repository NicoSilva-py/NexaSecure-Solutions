
<?php

header("Content-Type: application/json; charset=UTF-8");

require_once "conexao.php";

// Aceita somente requisições POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode(["erro" => "Use o método POST."]);
    exit;
}

// Recebe os dados enviados em JSON
$dados = json_decode(file_get_contents("php://input"), true);

if (!is_array($dados)) {
    http_response_code(400);
    echo json_encode(["erro" => "Envie um JSON válido."]);
    exit;
}

// Verifica os campos obrigatórios
$obrigatorios = [
    "cliente_id",
    "servico_id",
    "tecnico_id",
    "titulo",
    "status",
    "prioridade"
];

foreach ($obrigatorios as $campo) {
    if (!isset($dados[$campo]) || $dados[$campo] === "") {
        http_response_code(400);
        echo json_encode([
            "erro" => "O campo $campo é obrigatório."
        ]);
        exit;
    }
}

try {

    // Insere a nova ordem no banco de dados
    $sql = "INSERT INTO ordens_servico
            (cliente_id, servico_id, tecnico_id, titulo,
             descricao, status, prioridade, data_abertura,
             data_conclusao)
            VALUES
            (:cliente_id, :servico_id, :tecnico_id, :titulo,
             :descricao, :status, :prioridade, NOW(),
             :data_conclusao)";

    $consulta = $conexao->prepare($sql);

    $consulta->execute([
        "cliente_id" => $dados["cliente_id"],
        "servico_id" => $dados["servico_id"],
        "tecnico_id" => $dados["tecnico_id"],
        "titulo" => trim($dados["titulo"]),
        "descricao" => $dados["descricao"] ?? null,
        "status" => $dados["status"],
        "prioridade" => $dados["prioridade"],
        "data_conclusao" => $dados["data_conclusao"] ?? null
    ]);

    // Recupera o ID gerado automaticamente
    $id = $conexao->lastInsertId();

    http_response_code(201);

    echo json_encode([
        "mensagem" => "Ordem de serviço cadastrada com sucesso.",
        "id" => $id
    ], JSON_UNESCAPED_UNICODE);

} catch (PDOException $erro) {

    http_response_code(500);

    echo json_encode([
        "erro" => "Não foi possível cadastrar a ordem de serviço."
    ], JSON_UNESCAPED_UNICODE);

}
