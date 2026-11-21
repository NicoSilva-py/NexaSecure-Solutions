
<?php
header("Content-Type: application/json; charset=UTF-8");
require_once "conexao.php";

if ($_SERVER["REQUEST_METHOD"] !== "DELETE") {
    http_response_code(405);
    echo json_encode(["erro" => "Use o método DELETE para excluir um cliente."], JSON_UNESCAPED_UNICODE);
    exit;
}

$id = $_GET["id"] ?? null;

if (!$id || !ctype_digit($id) || (int)$id < 1) {
    http_response_code(400);
    echo json_encode(["erro" => "Informe um ID válido na URL."], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $sql = "DELETE FROM clientes WHERE id = :id";
    $consulta = $conexao->prepare($sql);
    $consulta->execute([":id" => (int)$id]);

    if ($consulta->rowCount() === 0) {
        http_response_code(404);
        echo json_encode(["erro" => "Cliente não encontrado."], JSON_UNESCAPED_UNICODE);
        exit;
    }

    echo json_encode(["mensagem" => "Cliente excluído com sucesso!"], JSON_UNESCAPED_UNICODE);

} catch (PDOException $erro) {
    if ($erro->getCode() === "23000") {
        http_response_code(409);
        echo json_encode(["erro" => "Não é possível excluir este cliente porque existem registros relacionados."], JSON_UNESCAPED_UNICODE);
    } else {
        http_response_code(500);
        echo json_encode(["erro" => "Erro interno ao excluir o cliente."], JSON_UNESCAPED_UNICODE);
    }
}
