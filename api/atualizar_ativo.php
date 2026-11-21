
<?php
header("Content-Type: application/json; charset=UTF-8");

require_once "conexao.php";

// Permite somente requisições PUT
if ($_SERVER["REQUEST_METHOD"] !== "PUT") {
    http_response_code(405);
    echo json_encode(["erro" => "Use o método PUT."]);
    exit;
}

// Obtém o ID do ativo enviado pela URL
$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    http_response_code(400);
    echo json_encode(["erro" => "Informe um ID válido na URL."]);
    exit;
}

// Lê os dados JSON enviados pelo Postman
$dados = json_decode(file_get_contents("php://input"), true);

if (!is_array($dados)) {
    http_response_code(400);
    echo json_encode(["erro" => "Envie um JSON válido."]);
    exit;
}

// Verifica os campos obrigatórios
$obrigatorios = ["nome_ativo", "tipo", "nivel_risco"];

foreach ($obrigatorios as $campo) {
    if (!isset($dados[$campo]) || !is_string($dados[$campo]) || trim($dados[$campo]) === "") {
        http_response_code(400);
        echo json_encode(["erro" => "O campo $campo é obrigatório."]);
        exit;
    }
}

try {
    // Confirma se o ativo existe
    $verificar = $conexao->prepare(
        "SELECT id FROM ativos WHERE id = :id"
    );
    $verificar->execute(["id" => $id]);

    if (!$verificar->fetch()) {
        http_response_code(404);
        echo json_encode(["erro" => "Ativo não encontrado."]);
        exit;
    }

    // Atualiza os dados do ativo
    $sql = "UPDATE ativos
            SET nome_ativo = :nome_ativo,
                tipo = :tipo,
                nivel_risco = :nivel_risco,
                endereco_ip = :endereco_ip,
                sistema_operacional = :sistema_operacional
            WHERE id = :id";

    $consulta = $conexao->prepare($sql);

    $consulta->execute([
        "nome_ativo" => trim($dados["nome_ativo"]),
        "tipo" => trim($dados["tipo"]),
        "nivel_risco" => trim($dados["nivel_risco"]),
        "endereco_ip" => $dados["endereco_ip"] ?? null,
        "sistema_operacional" => $dados["sistema_operacional"] ?? null,
        "id" => $id
    ]);

    echo json_encode([
        "mensagem" => "Ativo atualizado com sucesso.",
        "id" => $id
    ], JSON_UNESCAPED_UNICODE);

} catch (PDOException $erro) {
    http_response_code(500);
    echo json_encode(["erro" => "Não foi possível atualizar o ativo."]);
}
