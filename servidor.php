<?php
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

// Criar tabela categorias
$sql_categorias = "
CREATE TABLE IF NOT EXISTS categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(50) NOT NULL UNIQUE
);";
$conn->query($sql_categorias);

// Inserir categorias
$sql_inserir_categorias = "
INSERT IGNORE INTO categorias (nome)
VALUES ('Eletrônicos'), ('Livros'), ('Roupas');
";
$conn->query($sql_inserir_categorias);

// Criar tabela produtos
$sql_produtos = "
CREATE TABLE IF NOT EXISTS produtos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    descricao TEXT NOT NULL,
    preco DECIMAL(10,2) NOT NULL,
    quantidade INT NOT NULL,
    categoria VARCHAR(50) NOT NULL,
    imagem VARCHAR(255) NOT NULL,
    CONSTRAINT fk_categoria FOREIGN KEY (categoria) REFERENCES categorias(nome)
);";
$conn->query($sql_produtos);

// Inserir produtos
$sql_inserir_produtos = "
INSERT IGNORE INTO produtos (nome, descricao, preco, quantidade, categoria, imagem)
VALUES
    ('Smartphone', 'Smartphone de última geração', 1999.99, 50, 'Eletrônicos', 'smartphone.jpg'),
    ('Livro de Programação', 'Aprenda programação do zero', 59.90, 100, 'Livros', 'livro.jpg'),
    ('Camisa Polo', 'Camisa polo masculina', 79.90, 200, 'Roupas', 'camisa.jpg');
";
$conn->query($sql_inserir_produtos);

// Atualizar o preço do Smartphone
$sql_update = "
UPDATE produtos
SET preco = 1899.99
WHERE nome = 'Smartphone';
";
$conn->query($sql_update);

// Excluir o livro de programação
$sql_delete = "
DELETE FROM produtos
WHERE nome = 'Livro de Programação';
";
$conn->query($sql_delete);

// Consultas específicas
echo "<h1>Produtos na Categoria 'Eletrônicos'</h1>";
$sql_categoria = "
SELECT nome, preco, quantidade, categoria
FROM produtos
WHERE categoria = 'Eletrônicos';
";
$resultado_categoria = $conn->query($sql_categoria);

if ($resultado_categoria->num_rows > 0) {
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>Nome</th><th>Preço</th><th>Quantidade</th><th>Categoria</th></tr>";
    while ($linha = $resultado_categoria->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $linha['nome'] . "</td>";
        echo "<td>R$ " . number_format($linha['preco'], 2, ',', '.') . "</td>";
        echo "<td>" . $linha['quantidade'] . "</td>";
        echo "<td>" . $linha['categoria'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>Nenhum produto encontrado na categoria 'Eletrônicos'.</p>";
}

// Exibir produtos com categorias
echo "<h1>Produtos por Categoria</h1>";
$sql_join = "
SELECT c.nome AS categoria, p.nome AS produto
FROM categorias c
JOIN produtos p ON c.nome = p.categoria;
";
$resultado_join = $conn->query($sql_join);

if ($resultado_join->num_rows > 0) {
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>Categoria</th><th>Produto</th></tr>";
    while ($linha = $resultado_join->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $linha['categoria'] . "</td>";
        echo "<td>" . $linha['produto'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>Nenhum produto encontrado com categoria associada.</p>";
}

// Fechando a conexão
$conn->close();
?>
