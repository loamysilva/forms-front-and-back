<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cadastro";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cpf = $_POST['cpf'];

    // Excluir o usuário do banco
    $sql = "DELETE FROM corretores WHERE cpf = '$cpf'";
    if ($conn->query($sql) === TRUE) {
        echo "Cadastro excluído com sucesso!";
    } else {
        echo "Erro ao excluir cadastro: " . $conn->error;
    }

    $conn->close();
}
?>
