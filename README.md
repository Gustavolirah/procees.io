AUTH02 - mercadolivre.php

if (!isset($_GET['code'])) {
    $auth_url = "https://auth.mercadolivre.com.br/authorization?response_type=code&client_id=$client_id&redirect_uri=$redirect_uri";
    echo "<a href='{$auth_url}'>Clique aqui para autorizar a aplicação</a>";
    exit;
}

O que está acontecendo: O código verifica se o parâmetro code está presente na URL (o código de autorização que será enviado pelo Mercado Livre após o usuário autorizar a aplicação).

O fluxo de autorização: Se o parâmetro code não está presente (o que significa que o usuário ainda não foi redirecionado ou não autorizou a aplicação), o script gera o link de autorização para o Mercado Livre. Esse link direciona o usuário para a página de login/consentimento da API do Mercado Livre.

Resultado esperado: O usuário é redirecionado para o Mercado Livre para autorizar o acesso e, após a autorização, será redirecionado de volta para a URL fornecida (definida em $redirect_uri), com o código de autorização na URL.

if (isset($_GET['code'])) {
    $code = $_GET['code'];

    $token_url = 'https://api.mercadolibre.com/oauth/token';
    $data = [
        'grant_type' => 'authorization_code',
        'client_id' => $client_id,
        'client_secret' => $client_secret,
        'code' => $code,
        'redirect_uri' => $redirect_uri
    ];

    $options = [
        'http' => [
            'method' => 'POST',
            'header' => 'Content-type: application/x-www-form-urlencoded',
            'content' => http_build_query($data)
        ]
    ];

    $context = stream_context_create($options);
    $response = file_get_contents($token_url, false, $context);
    $token_data = json_decode($response, true);

    if (isset($token_data['access_token'])) {
        $access_token = $token_data['access_token'];
    } else {
        echo "Erro ao obter o token de acesso: " . $token_data['error'];
        exit;
    }
}
O que está acontecendo: O código verifica se o parâmetro code está presente na URL (ou seja, se o usuário autorizou a aplicação).

Troca do código pelo token: Quando o código de autorização é recebido, a aplicação envia uma requisição POST para o endpoint de token do Mercado Livre (https://api.mercadolibre.com/oauth/token), passando os parâmetros necessários:

grant_type: o tipo de autorização, no caso authorization_code.
client_id: o ID da aplicação.
client_secret: o segredo da aplicação.
code: o código de autorização recebido na URL.
redirect_uri: a URL para a qual o Mercado Livre deve redirecionar o usuário.
Resultado esperado: O Mercado Livre responde com um JSON contendo o access_token (token de acesso) que pode ser usado para autenticar futuras requisições à API.

Erros possíveis: Se não houver sucesso na troca do código, o script exibe uma mensagem de erro informando o motivo (como "invalid_request" ou "unauthorized_client").
if (isset($access_token)) {
    $categories_url = 'https://api.mercadolibre.com/sites/MLB/categories';
    $response_categories = file_get_contents($categories_url);
    $categories = json_decode($response_categories, true);

    if (!empty($categories)) {
        $category_id = $categories[0]['id'];
    } else {
        echo "Erro ao obter as categorias.";
        exit;
    }

    $product_data = [
        'title' => 'Produto Exemplo',
        'category_id' => $category_id,
        'price' => 100.00,
        'currency_id' => 'BRL',
        'available_quantity' => 10,
        'condition' => 'new',
        'description' => ['plain_text' => 'Descrição do produto.'],
        'pictures' => [['source' => 'https://www.exemplo.com/imagem.jpg']]
    ];

    $create_product_url = 'https://api.mercadolibre.com/items?access_token=' . $access_token;

    $options = [
        'http' => [
            'method' => 'POST',
            'header' => 'Content-type: application/json',
            'content' => json_encode($product_data)
        ]
    ];

    $context = stream_context_create($options);
    $response = file_get_contents($create_product_url, false, $context);
    $product_response = json_decode($response, true);

    if (isset($product_response['id'])) {
        echo "Produto criado com sucesso! ID: " . $product_response['id'] . "<br>";
    } else {
        echo "Erro ao criar o produto: " . (isset($product_response['message']) ? $product_response['message'] : 'Erro desconhecido');
    }
}
O que está acontecendo: Após o sucesso na obtenção do token de acesso, a aplicação pode fazer requisições autenticadas à API do Mercado Livre.

Obtenção das categorias: O script faz uma requisição para obter as categorias de produtos no Mercado Livre (/sites/MLB/categories). Isso permite que o script escolha uma categoria para o produto.

Criação do produto: Um array com os dados do produto (como título, preço, quantidade disponível, descrição e imagem) é montado. A requisição POST é enviada para o endpoint /items da API, junto com o access_token no cabeçalho, para criar o produto.

Resultado esperado: Se o produto for criado com sucesso, a API do Mercado Livre retorna o id do produto e o status. O código exibe esses dados.

Erro ao criar produto: Se ocorrer algum erro ao criar o produto, o script tenta exibir a mensagem de erro (caso existam mensagens no retorno da API).



index.php - LEMBRANDO SE A DESCRIÇÃO FOR DIFERENTE O PRODUTO É CADASTRADO, TEM QUE SER TODOS OS DADOS IGUAIS E ELE NÃO CADASTRA.
Antes de inserir os dados, fazemos uma consulta no banco para ver se já existe um produto com as mesmas características. Essa consulta vai buscar um produto com o mesmo nome, descrição, preço, quantidade e categoria.

Se encontrar esse produto, a mensagem de erro vai ser mostrada. Caso contrário, o sistema vai continuar e inserir o novo produto.

Código para verificar a duplicidade:

Aqui está a parte importante:

php
Copiar
Editar
// Verifica se o produto já existe no banco de dados
$sqlVerifica = "SELECT * FROM produtos WHERE nome = '$nome' AND descricao = '$descricao' AND preco = '$preco' AND quantidade = '$quantidade' AND categoria = '$categoria'";
$resultado = $conn->query($sqlVerifica);

if ($resultado->num_rows > 0) {
    // Produto já existe, exibe mensagem de erro
    $erro = "Este produto já foi cadastrado!";
} else {
    // Se não encontrar nada, continua com o cadastro
    // Código para upload da imagem e inserção no banco
}
Explicando o que acontece:

O código faz uma consulta no banco (SELECT * FROM produtos...) para buscar qualquer produto que tenha exatamente os mesmos valores nos campos nome, descrição, preço, quantidade e categoria.
Se a consulta encontrar algum produto ($resultado->num_rows > 0), isso significa que o produto já existe no banco de dados, e a mensagem de erro será definida.
Caso contrário, o código vai continuar com o processo de cadastro (upload da imagem e inserção no banco).
Exibindo a mensagem de erro:

Em qualquer parte do código onde ocorre um erro, como no caso de duplicidade, a mensagem de erro é exibida assim:

php
Copiar
Editar
<?php if ($erro): ?>
    <div class="erro">
        <strong>Erro: </strong> <?php echo $erro; ?>
    </div>
<?php endif; ?>
Isso vai garantir que, se o produto já existir, o sistema avise o usuário.

Resumo:
Agora, antes de adicionar um novo produto, o sistema verifica se já existe um produto com os mesmos dados no banco. Se encontrar, ele exibe a mensagem de erro. Se não, o produto é inserido normalmente. Assim, evitamos duplicidades no cadastro.




1 Configuração de Conexão com o Banco de Dados:
Primeiro, criei as variáveis $servidor, $usuario, $senha e $banco_de_dados, que guardam as informações necessárias para conectar ao banco MySQL.
Em seguida, usei o mysqli para estabelecer a conexão com o banco de dados. Se houver algum erro de conexão, ele é mostrado e o script é interrompido.

2. Processamento do Formulário (via POST):
Quando o usuário envia o formulário, as informações são capturadas através de $_POST. O formulário inclui dados como nome, descrição, preço, quantidade, categoria e imagem.
O preço é formatado para garantir que ele tenha o ponto no lugar da vírgula (para que o banco de dados aceite corretamente).
Validei o preço e a quantidade para garantir que sejam valores numéricos e que o preço não comece com zero ou seja igual a zero. Se algum desses casos ocorrer, uma mensagem de erro é exibida.
Caso as validações sejam bem-sucedidas, continuo com o processo de upload da imagem.

3. Upload da Imagem:
O upload da imagem é feito para uma pasta chamada imagem/, e antes de mover o arquivo para lá, verifico se a pasta já existe. Se não, ela é criada.
O nome da imagem é gerado de forma única para evitar que arquivos com o mesmo nome se sobrescrevam.
Caso o upload falhe, o erro é retornado ao usuário.

4. Inserção no Banco de Dados:
Após o upload ser feito com sucesso, os dados do produto são inseridos no banco de dados (produtos), utilizando uma consulta SQL INSERT INTO.
O caminho da imagem é armazenado no banco de dados para que o produto tenha a imagem associada.
Caso a inserção seja bem-sucedida, o usuário é redirecionado para uma página de sucesso, onde os detalhes do produto são passados via URL.

5. Formulário HTML:
O formulário HTML permite que o usuário insira o nome, descrição, preço, quantidade, categoria e imagem do produto. O preço é validado pelo atributo pattern, permitindo valores como 9,90 ou 9.90.
As categorias são pré-definidas no formulário através de um campo <select>, onde o usuário escolhe a categoria do produto.
O campo de imagem permite que o usuário envie um arquivo de imagem, que será validado e processado no backend.

6. Exibição de Erros:
Caso algum erro aconteça durante o processo (como erro de conexão, validação ou upload), uma mensagem é exibida na tela, avisando o usuário sobre o problema.
Essencialmente, o código oferece um sistema simples para cadastrar produtos em um banco de dados, permitindo que o usuário adicione informações detalhadas e uma imagem associada ao produto.


sucesso.php

1. Captura dos Dados da URL:
Eu utilizo $_GET para capturar os dados passados pela URL. Esses dados incluem o nome do produto, a descrição, o preço, a categoria, a quantidade e a imagem.
Caso algum desses dados não seja passado na URL, defino valores padrão. Por exemplo, se o nome não for passado, será exibido "Produto"; se a quantidade não for passada, o valor será "0".

2. Verificação da Imagem:
A URL da imagem é decodificada com urldecode() para garantir que caracteres especiais sejam tratados corretamente.
Eu verifico se o arquivo da imagem realmente existe com file_exists(). Se a imagem não for encontrada, atribuo uma imagem padrão, imagem/padrao.jpg, para garantir que o layout não quebre.

3. Exibição das Informações:
A página é estruturada com HTML e um pouco de CSS para um visual simples e limpo.
Eu exibo as informações do produto (nome, descrição, preço, categoria, quantidade e imagem) no corpo da página.
Para prevenir vulnerabilidades de XSS (Cross-site Scripting), uso htmlspecialchars() para garantir que qualquer dado inserido pelo usuário seja escapado corretamente.

4. Design e Estilo:
O layout é simples e centralizado com uma caixa (container) que contém todas as informações do produto.
A imagem do produto é exibida de maneira responsiva, ou seja, ela se ajusta automaticamente ao tamanho da tela.
Eu adicionei um botão "Voltar" para que o usuário possa retornar à página inicial (index.php) após visualizar as informações do produto.

5. CSS:
O estilo é simples, com cores agradáveis e elementos como bordas arredondadas, sombras sutis e um botão verde para a ação "Voltar".
Eu garanti que a página tenha uma aparência limpa e moderna, mantendo o foco na exibição das informações do produto.
Essencialmente, essa página serve para confirmar ao usuário que o produto foi cadastrado corretamente, mostrando todos os detalhes do cadastro e a imagem do produto, além de permitir que ele retorne à página inicial com um clique.


servidor.php

Esse código faz a configuração inicial de um banco de dados para uma loja, cria as tabelas necessárias para armazenar informações de produtos e categorias, realiza algumas operações básicas (inserir, atualizar, excluir) e exibe informações de forma organizada na página.

1. Conexão com o Banco de Dados:
   - A conexão é feita com o MySQL usando a função `new mysqli()`, passando as informações do servidor, usuário, senha e banco de dados.
   - Se a conexão falhar, o script exibe uma mensagem de erro e interrompe a execução com `die()`.

2. Criação da Tabela de Categorias:
   - Eu crio a tabela `categorias` no banco de dados, com um campo `id` como chave primária e um campo `nome` para armazenar o nome da categoria.
   - A tabela só será criada se não existir, devido ao `IF NOT EXISTS`.

3. nserção de Categorias:
   - Em seguida, insiro algumas categorias iniciais na tabela `categorias` como 'Eletrônicos', 'Livros' e 'Roupas'. A operação é realizada com `INSERT IGNORE` para evitar duplicatas.

4. Criação da Tabela de Produtos:
   - A tabela `produtos` é criada com campos como `id`, `nome`, `descricao`, `preco`, `quantidade`, `categoria` e `imagem`.
   - A tabela de produtos inclui uma chave estrangeira (`FOREIGN KEY`) que vincula o campo `categoria` à tabela `categorias`. Ou seja, cada produto deve ter uma categoria associada.

5. Inserção de Produtos:
   - Inserimos alguns produtos na tabela `produtos`, com informações como nome, descrição, preço, quantidade e categoria, também usando o `INSERT IGNORE` para evitar duplicações.

6. Atualização do Preço de um Produto:
   - O preço do "Smartphone" é atualizado para um novo valor (1899,99) usando o comando `UPDATE`. Isso altera o preço de um produto específico.

7. Exclusão de um Produto:
   - O "Livro de Programação" é removido da tabela `produtos` com um comando `DELETE`.

8. Consultas Específicas:
   - Eu faço duas consultas para exibir informações:
     1. Produtos na Categoria 'Eletrônicos':
        - Uso uma consulta `SELECT` para buscar todos os produtos que estão na categoria 'Eletrônicos'. Se houver resultados, eles são exibidos em uma tabela HTML.
     2. Produtos por Categoria:
        - Utilizo um `JOIN` entre as tabelas `categorias` e `produtos` para mostrar todos os produtos junto com suas respectivas categorias.

9. Exibição de Resultados:
   - Se houver resultados nas consultas, exibo-os em tabelas HTML. Caso contrário, uma mensagem de "Nenhum produto encontrado" é exibida.
   - Para exibir os preços de forma legível, utilizo `number_format()` para formatar o valor com separação de milhar e duas casas decimais.

10. Fechamento da Conexão:
   - Após as operações e exibição dos resultados, a conexão com o banco de dados é fechada com `$conn->close()` para liberar os recursos.

Em resumo, o código é uma maneira simples de gerenciar um sistema de produtos e categorias em um banco de dados, com funcionalidades básicas de inserção, atualização, exclusão e consulta, exibindo os dados de forma organizada para o usuário.

style.css

Estilos Gerais:
Body: A fonte da página é definida como Arial ou sans-serif, com um fundo claro (#f4f4f4). Não há margens nem espaçamentos na parte externa da página.
Títulos (h2): O título é centralizado e possui um espaço superior para ficar mais afastado do topo da página.
Parágrafos (p): Os textos dos parágrafos são centralizados e têm uma fonte de tamanho 18px para facilitar a leitura.

Formulário:
Formulário (form): O formulário tem 50% de largura da página, é centralizado e tem um fundo branco. A caixa de texto tem bordas arredondadas e uma leve sombra para dar um efeito de profundidade.
Campos do Formulário (.form-group): Cada grupo de campo (como rótulo e input) tem um espaçamento de 15px entre eles.
Rótulos (label): Os rótulos dos campos são destacados em negrito para que o usuário saiba o que preencher.
Campos de entrada (input, textarea, select): Todos os campos têm uma largura total de 100% para ocupar toda a largura disponível dentro do formulário. Eles têm preenchimento (padding) e bordas arredondadas para um visual mais amigável.

Botões:
Botões (button, .btn): Os botões têm fundo verde, texto branco e são completamente preenchidos. Quando o usuário passa o mouse sobre o botão, ele fica um pouco mais escuro. O botão também é 100% da largura disponível no formulário, o que torna a interação mais fácil.
Mensagens de Erro:
Erro (.erro): Se algo der errado, uma mensagem de erro é exibida. Ela tem um fundo vermelho claro (#f8d7da) com texto escuro, para destacar o erro. A mensagem de erro tem bordas arredondadas e padding para deixar o texto mais legível. Um botão de fechar, que é transparente e flutuante, permite que o usuário feche a mensagem de erro.

Interação:
Quando o usuário passa o mouse sobre os botões ou o botão de fechar da mensagem de erro, a cor muda para mostrar que esses elementos são interativos.
Esses estilos visam criar uma página limpa, com boa legibilidade e interatividade, além de oferecer uma boa experiência ao usuário.

