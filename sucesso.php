<?php
// Captura os dados da URL, incluindo a quantidade
$nome = $_GET['nome'] ?? 'Produto';
$descricao = $_GET['descricao'] ?? '';
$preco = $_GET['preco'] ?? '0.00';
$categoria = $_GET['categoria'] ?? 'N/A';
$quantidade = $_GET['quantidade'] ?? '0'; // Captura a quantidade da URL
$imagem = urldecode($_GET['imagem'] ?? '');

// Verifica se a imagem existe
if (!file_exists($imagem)) {
    $imagem = "imagem/padrao.jpg"; // Caminho para uma imagem padrão caso a original não seja encontrada
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produto Cadastrado</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            text-align: center;
        }
        .container {
            margin: 50px auto;
            padding: 20px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            max-width: 500px;
        }
        h2 {
            color: #4CAF50;
        }
        img {
            max-width: 100%;
            height: auto;
            margin: 20px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        p {
            color: #333;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            color: #fff;
            background-color: #4CAF50;
            text-decoration: none;
            border-radius: 5px;
        }
        .btn:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Produto cadastrado com sucesso!</h2>
        <img src="<?= htmlspecialchars($imagem) ?>" alt="Imagem do Produto">
        <p><strong>Nome:</strong> <?= htmlspecialchars($nome) ?></p>
        <p><strong>Descrição:</strong> <?= htmlspecialchars($descricao) ?></p>
        <p><strong>Preço:</strong> R$ <?= htmlspecialchars($preco) ?></p>
        <p><strong>Categoria:</strong> <?= htmlspecialchars($categoria) ?></p>
        <p><strong>Quantidade em Estoque:</strong> <?= htmlspecialchars($quantidade) ?></p> <!-- Exibe a quantidade de estoque -->
        <a href="index.php" class="btn">Voltar</a>
    </div>
</body>
</html>
