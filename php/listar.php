<?php
include 'php/conexao.php'; // Incluir a conexão

// Buscar os registros na tabela corretores
$sql = "SELECT id, cpf, nome, creci FROM corretores";
$result = $conn->query($sql);

// Exibir os dados na tabela
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Evitar injeção de código JavaScript e caracteres especiais
        $cpf = htmlspecialchars($row["cpf"]);
        $nome = htmlspecialchars($row["nome"]);
        $creci = htmlspecialchars($row["creci"]);
        $id = $row["id"];

        echo "<tr>
                <td>$cpf</td>
                <td>$nome</td>
                <td>$creci</td>
                <td>
                    <button class='editar' onclick='editarUsuario($id)'>Editar</button>
                    <button class='excluir' onclick='excluirUsuario($id)'>Excluir</button>
                </td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='4'>Nenhum usuário cadastrado.</td></tr>";
}

// Fechar a conexão
$conn->close();
?>
