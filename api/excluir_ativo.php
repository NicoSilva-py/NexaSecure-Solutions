
<?php
header("Content-Type: application/json; charset=UTF-8");

require_once "conexao.php";

// Permite somente requisições DELETE
if ($_SERVER["REQUEST_METHOD"] !== "DELETE") {
    http_response_code(405);
    echo json_encode(["erro" => "Use o método DELETE."]);
    exit;
}

// Obtém o ID do ativo pela URL
$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    http_response_code(400);
    echo json_encode(["erro" => "Informe um ID válido na URL."]);
    exit;
}

try {
    // Verifica se o ativo existe antes de excluir
    $verificar = $conexao->prepare(
        "SELECT id FROM ativos WHERE id = :id"
    );
    $verificar->execute(["id" => $id]);

    if (!$verificar->fetch()) {
        http_response_code(404);
        echo json_encode(["erro" => "Ativo não encontrado."]);
        exit;
    }

    // Exclui o ativo identificado pelo ID
    $consulta = $conexao->prepare(
        "DELETE FROM ativos WHERE id = :id"
    );
    $consulta->execute(["id" => $id]);

    echo json_encode([
        "mensagem" => "Ativo excluído com sucesso.",
        "id" => $id
    ], JSON_UNESCAPED_UNICODE);

} catch (PDOException $erro) {
    http_response_code(500);
    echo json_encode([
        "erro" => "Não foi possível excluir o ativo."
    ], JSON_UNESCAPED_UNICODE);
}
