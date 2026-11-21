
<?php
header("Content-Type: application/json; charset=UTF-8");
require_once "conexao.php";

if ($_SERVER["REQUEST_METHOD"] !== "PUT") {
    http_response_code(405);
    echo json_encode(["erro" => "Use o método PUT para atualizar um cliente."], JSON_UNESCAPED_UNICODE);
    exit;
}

$id = $_GET["id"] ?? null;

if (!$id || !ctype_digit($id) || (int)$id < 1) {
    http_response_code(400);
    echo json_encode(["erro" => "Informe um ID válido na URL."], JSON_UNESCAPED_UNICODE);
    exit;
}

$dados = json_decode(file_get_contents("php://input"), true);

if (!is_array($dados)) {
    http_response_code(400);
    echo json_encode(["erro" => "Envie os dados em formato JSON."], JSON_UNESCAPED_UNICODE);
    exit;
}

$camposObrigatorios = ["nome_empresa", "cnpj", "email"];

foreach ($camposObrigatorios as $campo) {
    if (empty($dados[$campo])) {
        http_response_code(400);
        echo json_encode(["erro" => "O campo $campo é obrigatório."], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

try {
    $sql = "UPDATE clientes
            SET nome_empresa = :nome_empresa,
                cnpj = :cnpj,
                email = :email,
                telefone = :telefone,
                endereco = :endereco
            WHERE id = :id";

    $consulta = $conexao->prepare($sql);
    $consulta->execute([
        ":nome_empresa" => $dados["nome_empresa"],
        ":cnpj" => $dados["cnpj"],
        ":email" => $dados["email"],
        ":telefone" => $dados["telefone"] ?? null,
        ":endereco" => $dados["endereco"] ?? null,
        ":id" => (int)$id
    ]);

    if ($consulta->rowCount() === 0) {
        $verifica = $conexao->prepare("SELECT id FROM clientes WHERE id = :id");
        $verifica->execute([":id" => (int)$id]);

        if (!$verifica->fetch()) {
            http_response_code(404);
            echo json_encode(["erro" => "Cliente não encontrado."], JSON_UNESCAPED_UNICODE);
            exit;
        }
    }

    echo json_encode(["mensagem" => "Cliente atualizado com sucesso!"], JSON_UNESCAPED_UNICODE);

} catch (PDOException $erro) {
    if ($erro->getCode() === "23000") {
        http_response_code(409);
        echo json_encode(["erro" => "Esse CNPJ já está cadastrado."], JSON_UNESCAPED_UNICODE);
    } else {
        http_response_code(500);
        echo json_encode(["erro" => "Erro interno ao atualizar o cliente."], JSON_UNESCAPED_UNICODE);
    }
}
