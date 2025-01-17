<?php
$erro = ""; // Variável para armazenar mensagens de erro

// Configuração de conexão com o banco de dados
$servidor = "localhost"; // Endereço do servidor MySQL
$usuario = "root"; // Usuário do MySQL (geralmente "root" no local)
$senha = ""; // Senha do MySQL (deixe em branco se não houver senha)
$banco_de_dados = "loja"; // Nome do banco de dados

// Criando a conexão
$conn = new mysqli($servidor, $usuario, $senha, $banco_de_dados);

// Verificando a conexão
if ($conn->connect_error) {
    die("Erro na conexão com o banco de dados: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Lógica para processar os dados do formulário
    $nome = $_POST['nome'];
    $descricao = $_POST['descricao'];

    // Formatar o preço substituindo a vírgula por ponto
    $preco = str_replace(',', '.', $_POST['preco']);

    // Garantir que preço e quantidade sejam numéricos
    $quantidade = $_POST['quantidade'];
    $categoria = $_POST['categoria'];
    $imagem = $_FILES['imagem'];

    // Validações adicionais
    if (!is_numeric($preco) || !is_numeric($quantidade)) {
        $erro = "Por favor, insira valores válidos para preço e quantidade.";
    }

    // Verifica se o preço começa com zero
    if (preg_match('/^0\d/', $preco)) {
        $erro = "O preço não pode começar com zero.";
    }

    // Verifica se o preço e a quantidade são zero
    if ($preco == 0 || $quantidade == 0) {
        $erro = "Não é possível cadastrar um produto com preço ou quantidade igual a zero.";
    }

    // Se não houver erro, prossegue com a verificação e cadastro
    if (empty($erro)) {
        // Verifica se o produto já existe no banco de dados
        $sqlVerifica = "SELECT * FROM produtos WHERE nome = '$nome' AND descricao = '$descricao' AND preco = '$preco' AND quantidade = '$quantidade' AND categoria = '$categoria'";
        $resultado = $conn->query($sqlVerifica);

        if ($resultado->num_rows > 0) {
            // Produto já existe, exibe mensagem de erro
            $erro = "Este produto já foi cadastrado!";
        } else {
            // Configurações de upload
            $uploadDir = "imagem/"; // Certifique-se de que a pasta 'imagem' existe
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true); // Cria a pasta se não existir
            }

            $fileName = uniqid() . "_" . basename($imagem["name"]);
            $targetFile = $uploadDir . $fileName;

            // Move o arquivo enviado para o destino
            if (move_uploaded_file($imagem["tmp_name"], $targetFile)) {
                // Inserir dados no banco de dados
                $sql = "INSERT INTO produtos (nome, descricao, preco, quantidade, categoria, imagem)
                        VALUES ('$nome', '$descricao', '$preco', '$quantidade', '$categoria', '$targetFile')";

                if ($conn->query($sql) === TRUE) {
                    // Redireciona para a página de sucesso com os dados, incluindo a quantidade
                    header("Location: sucesso.php?nome=$nome&descricao=$descricao&preco=$preco&categoria=$categoria&quantidade=$quantidade&imagem=" . urlencode($targetFile));
                    exit();
                } else {
                    $erro = "Erro ao cadastrar produto no banco de dados: " . $conn->error;
                }
            } else {
                $erro = "Erro ao salvar a imagem. Verifique as permissões da pasta.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Produto</title>
    <link rel="stylesheet" href="style.css"> <!-- Caminho correto do CSS -->
</head>
<body>
    <h2>Cadastro de Produto</h2>

    <!-- Exibe a mensagem de erro, caso haja -->
    <?php if ($erro): ?>
        <div class="erro">
            <strong>Erro: </strong> <?php echo $erro; ?>
        </div>
    <?php endif; ?>

    <form action="index.php" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="nome">Nome:</label>
            <input type="text" id="nome" name="nome" value="<?php echo isset($nome) ? $nome : ''; ?>" required>
        </div>
        <div class="form-group">
            <label for="descricao">Descrição:</label>
            <textarea id="descricao" name="descricao" required><?php echo isset($descricao) ? $descricao : ''; ?></textarea>
        </div>
        <div class="form-group">
            <label for="preco">Preço:</label>
            <input type="text" id="preco" name="preco" value="<?php echo isset($preco) ? $preco : ''; ?>" 
                   required pattern="^\d{1,5}([,.]\d{1,2})?$" 
                   title="Digite um valor válido para o preço (exemplo: 9,90 ou 9.90)."
                   oninput="this.value = this.value.replace(/[^0-9,.]/g, '')">
        </div>

        <div class="form-group">
            <label for="quantidade">Quantidade em Estoque:</label>
            <input type="number" id="quantidade" name="quantidade" value="<?php echo isset($quantidade) ? $quantidade : ''; ?>" required min="1">
        </div>
        <div class="form-group">
            <label for="categoria">Categoria:</label>
            <select id="categoria" name="categoria" required>
                <option value="eletronicos">Eletrônicos</option>
                <option value="roupas">Roupas</option>
                <option value="alimentos">Alimentos</option>
                <option value="livros">Livros</option>
                <option value="automotivo">Automotivo</option>
                <option value="beleza">Beleza</option>
                <option value="brinquedos">Brinquedos</option>
                <option value="informatica">Informática</option>
                <option value="esportes">Esportes</option>
                <option value="musica">Música</option>
                <option value="moveis">Móveis</option>
                <option value="decoracao">Decoração</option>
                <option value="jardinagem">Jardinagem</option>
                <option value="outros">Outros</option>
            </select>
        </div>
        <div class="form-group">
            <label for="imagem">Imagem:</label>
            <input type="file" id="imagem" name="imagem" accept="image/*" required>

            
        </div>
        <button type="submit">Cadastrar Produto</button>
    </form>
</body>
</html>
