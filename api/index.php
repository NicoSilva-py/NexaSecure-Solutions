
<?php

header("Content-Type: application/json; charset=UTF-8");

echo json_encode([
    "projeto" => "NexaSecure Solutions",
    "mensagem" => "API funcionando!"
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
