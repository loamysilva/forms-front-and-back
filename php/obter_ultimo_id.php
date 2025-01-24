<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cadastro";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Obtém o último ID inserido
$sql = "SELECT MAX(id) FROM corretores";
$result = $conn->query($sql);
$row = $result->fetch_row();
echo $row[0]; // Retorna o último ID

$conn->close();
?>
