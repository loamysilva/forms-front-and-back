<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cadastro";

// Conexão com o banco de dados
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Receber dados via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = isset($_POST['id']) && $_POST['id'] !== "" ? intval($_POST['id']) : null;
    $cpf = preg_replace('/\D/', '', $_POST['cpf']); // Remove caracteres não numéricos

    if (strlen($cpf) !== 11) {
        echo "O CPF deve ter exatamente 11 dígitos.";
        exit;
    }
    
    $nome = trim($_POST['nome']);
    $creci = trim($_POST['creci']);
    $cpf_original = isset($_POST['cpf_original']) ? trim($_POST['cpf_original']) : $cpf;

    if (empty($cpf) || empty($nome) || empty($creci)) {
        echo "Todos os campos são obrigatórios!";
        exit;
    }

    if ($id) {
        // Atualização
        if ($cpf !== $cpf_original) {
            // Verifica se o CPF já existe no banco
            $stmt = $conn->prepare("SELECT COUNT(*) FROM corretores WHERE cpf = ?");
            $stmt->bind_param("s", $cpf);
            $stmt->execute();
            $stmt->bind_result($count);
            $stmt->fetch();
            $stmt->close();

            if ($count > 0) {
                echo "CPF já cadastrado.";
                exit;
            }
        }

        // Atualiza o registro sem alterar o CPF se não foi modificado
        $stmt = $conn->prepare("UPDATE corretores SET cpf = ?, nome = ?, creci = ? WHERE id = ?");
        $stmt->bind_param("sssi", $cpf, $nome, $creci, $id);

        if ($stmt->execute()) {
            echo "Cadastro atualizado com sucesso!";
        } else {
            echo "Erro ao atualizar cadastro: " . $stmt->error;
        }

        $stmt->close();
    } else {
        // Cadastro de novo usuário
        $stmt = $conn->prepare("SELECT COUNT(*) FROM corretores WHERE cpf = ?");
        $stmt->bind_param("s", $cpf);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();

        if ($count > 0) {
            echo "CPF já cadastrado.";
            exit;
        }

        // Insere novo cadastro
        $stmt = $conn->prepare("INSERT INTO corretores (cpf, nome, creci) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $cpf, $nome, $creci);

        if ($stmt->execute()) {
            echo "Cadastro realizado com sucesso!";
        } else {
            echo "Erro ao cadastrar: " . $stmt->error;
        }

        $stmt->close();
    }

    $conn->close();
}
?>
