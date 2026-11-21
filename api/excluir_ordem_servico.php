
<?php

header("Content-Type: application/json; charset=UTF-8");

require_once "conexao.php";

// Aceita somente requisições DELETE
if ($_SERVER["REQUEST_METHOD"] !== "DELETE") {
    http_response_code(405);
    echo json_encode(["erro" => "Use o método DELETE."]);
    exit;
}

// Obtém o ID pela URL
$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    http_response_code(400);
    echo json_encode(["erro" => "Informe um ID válido."]);
    exit;
}

try {

    // Verifica se a ordem existe
    $verificar = $conexao->prepare(
        "SELECT id FROM ordens_servico WHERE id = :id"
    );

    $verificar->execute(["id" => $id]);

    if (!$verificar->fetch()) {
        http_response_code(404);
        echo json_encode([
            "erro" => "Ordem de serviço não encontrada."
        ]);
        exit;
    }

    // Exclui a ordem pelo ID
    $consulta = $conexao->prepare(
        "DELETE FROM ordens_servico WHERE id = :id"
    );

    $consulta->execute(["id" => $id]);

    echo json_encode([
        "mensagem" => "Ordem de serviço excluída com sucesso.",
        "id" => $id
    ], JSON_UNESCAPED_UNICODE);

} catch (PDOException $erro) {

    http_response_code(500);

    echo json_encode([
        "erro" => "Não foi possível excluir a ordem de serviço."
    ], JSON_UNESCAPED_UNICODE);

}
