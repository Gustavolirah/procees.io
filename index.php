<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Lógica para processar os dados do formulário
    $nome = $_POST['nome'];
    $descricao = $_POST['descricao'];
    $preco = $_POST['preco'];
    $quantidade = $_POST['quantidade'];
    $categoria = $_POST['categoria'];
    $imagem = $_FILES['imagem'];

    // Validações adicionais no backend (preço e quantidade devem ser numéricos)
    if (!is_numeric($preco) || !is_numeric($quantidade)) {
        echo "Por favor, insira valores válidos para preço e quantidade.";
        exit();
    }

    // Redireciona para a página de sucesso
    header("Location: sucesso.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Produto</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>Cadastro de Produto</h2>
    <form action="index.php" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="nome">Nome:</label>
            <input type="text" id="nome" name="nome" required>
        </div>

        <div class="form-group">
            <label for="descricao">Descrição:</label>
            <textarea id="descricao" name="descricao" required></textarea>
        </div>

        <div class="form-group">
            <label for="preco">Preço:</label>
            <input 
                type="number" 
                id="preco" 
                name="preco" 
                step="0.01" 
                required 
                oninput="this.value = this.value.replace(/[^0-9.]/g, '')" 
                pattern="^\d+(\.\d{1,2})?$" 
                title="Digite um valor válido para o preço (somente números).">
        </div>

        <div class="form-group">
            <label for="quantidade">Quantidade em Estoque:</label>
            <input 
                type="number" 
                id="quantidade" 
                name="quantidade" 
                required 
                oninput="this.value = this.value.replace(/[^0-9]/g, '')" 
                pattern="^[0-9]+$" 
                title="Digite um valor válido para a quantidade (somente números inteiros).">
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
