document.getElementById("cadastroForm").addEventListener("submit", function (event) {
    event.preventDefault(); // Evita o envio padrão do formulário

    let id = document.getElementById("id").value.trim() || null; // ID do usuário (para edição)
    let cpf = document.getElementById("cpf").value.trim().replace(/\D/g, ''); // Remove caracteres não numéricos

    if (cpf.length !== 11) {
        alert("O CPF deve ter exatamente 11 dígitos.");
        return;
    }    
    let nome = document.getElementById("nome").value.trim();
    let creci = document.getElementById("creci").value.trim();
    let cpfOriginal = document.getElementById("cpf_original").value.trim(); // CPF original

    // Validação do Nome
    if (nome.length < 2 || /\d/.test(nome)) {
        alert("O nome deve ter pelo menos 2 caracteres e não pode conter números.");
        return;
    }

    // Validação do CRECI
    if (creci.length < 2) {
        alert("O CRECI deve ter pelo menos 2 caracteres.");
        return;
    }

    // Envia os dados via AJAX para o PHP (para cadastro ou edição)
    fetch("php/processar.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: new URLSearchParams({
            id: id || "", // Envia ID apenas se for uma edição
            cpf: cpf, // Envia o CPF digitado
            cpf_original: cpfOriginal, // Passando o CPF original para o PHP
            nome: nome,
            creci: creci
        })
    })
    .then(response => response.text())
    .then(data => {
        alert(data); // Exibe a resposta do PHP

        // Se for sucesso, atualiza a tabela
        if (data.includes("sucesso")) {
            if (!id) {
                // Se for um novo cadastro, pega um novo ID gerado
                fetch("php/obter_ultimo_id.php")
                .then(response => response.text())
                .then(novoId => {
                    atualizarTabela(novoId, cpf, nome, creci);
                });
            } else {
                atualizarTabela(id, cpf, nome, creci);
            }
        }
    })
    .catch(error => {
        console.error("Erro ao enviar os dados:", error);
    });

    // Limpa o formulário após o envio
    resetarFormulario();
});

// Função para limpar o formulário
function resetarFormulario() {
    document.getElementById("cadastroForm").reset();
    document.getElementById("formTitulo").innerText = "Cadastrar Cadastro";
    document.getElementById("id").value = ""; // Limpa o ID oculto
    document.getElementById("cpf_original").value = "";
    document.getElementById("submitButton").innerText = "Cadastrar";
}

// Função para adicionar ou atualizar a tabela de usuários
function atualizarTabela(id, cpf, nome, creci) {
    let tabela = document.getElementById("tabelaUsuarios").getElementsByTagName('tbody')[0];
    let linhaExistente = document.getElementById("usuario_" + id); // Verifica se a linha já existe

    // Formatação do CPF para o padrão xxx.xxx.xxx-xx
    cpf = formatarCPF(cpf);

    if (linhaExistente) {
        // Atualiza os dados da linha existente
        linhaExistente.innerHTML = `
            <td>${cpf}</td>
            <td>${nome}</td>
            <td>${creci}</td>
            <td>
                <button class="editar" onclick="editarUsuario(this, ${id})">Editar</button>
                <button class="excluir" onclick="excluirUsuario(this, ${id})">Excluir</button>
            </td>
        `;
    } else {
        // Adiciona uma nova linha à tabela
        let novaLinha = tabela.insertRow();
        novaLinha.id = "usuario_" + id; // Atribui o ID real da linha
        novaLinha.innerHTML = `
            <td>${cpf}</td>
            <td>${nome}</td>
            <td>${creci}</td>
            <td>
                <button class="editar" onclick="editarUsuario(this, ${id})">Editar</button>
                <button class="excluir" onclick="excluirUsuario(this, ${id})">Excluir</button>
            </td>
        `;
    }
}

// Função para formatar o CPF para o padrão xxx.xxx.xxx-xx
function formatarCPF(cpf) {
    return cpf.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, "$1.$2.$3-$4");
}

// Função para excluir o usuário da tabela e do banco
function excluirUsuario(botao, id) {
    let linha = botao.parentNode.parentNode;
    let cpf = linha.cells[0].innerText.replace(/\D/g, ''); // Obtém o CPF sem formatação

    // Envia os dados via AJAX para excluir no banco
    fetch("php/excluir.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: new URLSearchParams({ cpf: cpf })
    })
    .then(response => response.text())
    .then(data => {
        alert(data);
        if (data.includes("sucesso")) {
            linha.remove(); // Remove a linha da tabela
        }
    })
    .catch(error => {
        console.error("Erro ao excluir o usuário:", error);
    });
}

// Função para editar um usuário e preencher o formulário
function editarUsuario(botao, id) {
    let linha = botao.parentNode.parentNode;
    let cpf = linha.cells[0].innerText.replace(/\D/g, ''); // Obtém o CPF sem formatação
    let nome = linha.cells[1].innerText;
    let creci = linha.cells[2].innerText;

    // Preenche os campos do formulário com os dados do usuário
    document.getElementById("formTitulo").innerText = "Editar Cadastro";
    document.getElementById("cpf").value = cpf;
    document.getElementById("nome").value = nome;
    document.getElementById("creci").value = creci;
    document.getElementById("id").value = id; // Atribui o ID no campo oculto
    document.getElementById("cpf_original").value = cpf; // Atribui o CPF original
    document.getElementById("submitButton").innerText = "Atualizar Cadastro";
}
