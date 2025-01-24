<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cadastro";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Verificar se os dados foram enviados via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Receber dados do formulário
    $id = $_POST['id'];
    $cpf = $_POST['cpf']; // O CPF não será alterado
    $nome = $_POST['nome'];
    $creci = $_POST['creci'];

    // Validar os dados recebidos (opcional, mas recomendado)
    if (empty($id) || empty($cpf) || empty($nome) || empty($creci)) {
        echo "Todos os campos são obrigatórios!";
        exit;
    }

    // Atualizar no banco de dados, sem alterar o CPF
    $stmt = $conn->prepare("UPDATE corretores SET nome=?, creci=? WHERE id=?");
    $stmt->bind_param("ssi", $nome, $creci, $id); // Associar os parâmetros

    if ($stmt->execute()) {
        echo "Cadastro atualizado com sucesso!";
    } else {
        echo "Erro ao atualizar cadastro: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
