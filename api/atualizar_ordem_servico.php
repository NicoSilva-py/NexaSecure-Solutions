
<?php

header("Content-Type: application/json; charset=UTF-8");

require_once "conexao.php";

// Aceita somente requisições PUT
if ($_SERVER["REQUEST_METHOD"] !== "PUT") {
    http_response_code(405);
    echo json_encode(["erro" => "Use o método PUT."]);
    exit;
}

// Identifica a ordem pelo ID da URL
$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    http_response_code(400);
    echo json_encode(["erro" => "Informe um ID válido."]);
    exit;
}

// Recebe os dados enviados pelo Postman
$dados = json_decode(file_get_contents("php://input"), true);

if (!is_array($dados)) {
    http_response_code(400);
    echo json_encode(["erro" => "Envie um JSON válido."]);
    exit;
}

// Verifica os campos obrigatórios
$obrigatorios = ["titulo", "status", "prioridade"];

foreach ($obrigatorios as $campo) {
    if (!isset($dados[$campo]) ||
        !is_string($dados[$campo]) ||
        trim($dados[$campo]) === "") {

        http_response_code(400);
        echo json_encode([
            "erro" => "O campo $campo é obrigatório."
        ]);
        exit;
    }
}

try {

    // Verifica se a ordem existe
    $verificar = $conexao->prepare(
        "SELECT id FROM ordens_servico WHERE id = :id"
    );

    $verificar->execute(["id" => $id]);

    if (!$verificar->fetch()) {
        http_response_code(404);
        echo json_encode(["erro" => "Ordem de serviço não encontrada."]);
        exit;
    }

    // Define a data de conclusão automaticamente
    $dataConclusao = null;

    if ($dados["status"] === "Concluída") {
        $dataConclusao = date("Y-m-d H:i:s");
    }

    // Atualiza os dados da ordem
    $sql = "UPDATE ordens_servico
            SET titulo = :titulo,
                descricao = :descricao,
                status = :status,
                prioridade = :prioridade,
                data_conclusao = :data_conclusao
            WHERE id = :id";

    $consulta = $conexao->prepare($sql);

    $consulta->execute([
        "titulo" => trim($dados["titulo"]),
        "descricao" => $dados["descricao"] ?? null,
        "status" => trim($dados["status"]),
        "prioridade" => trim($dados["prioridade"]),
        "data_conclusao" => $dataConclusao,
        "id" => $id
    ]);

    echo json_encode([
        "mensagem" => "Ordem de serviço atualizada com sucesso.",
        "id" => $id
    ], JSON_UNESCAPED_UNICODE);

} catch (PDOException $erro) {

    http_response_code(500);

    echo json_encode([
        "erro" => "Não foi possível atualizar a ordem de serviço."
    ], JSON_UNESCAPED_UNICODE);

}
