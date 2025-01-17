<?php
// Credenciais da aplicação
$client_id = '7129933654525144'; // Substitua pelo seu client_id
$client_secret = 'YHFHLYQGcrB0G4CV8ujIV8xIhyDHCWyb'; // Substitua pelo seu client_secret
$redirect_uri = 'https://abreai.link/6uy4a'; // Substitua pela sua URI de redirecionamento

// Passo 1: Gerar o link de autorização para o usuário
if (!isset($_GET['code'])) {
    // URL de autorização
    $auth_url = "https://auth.mercadolivre.com.br/authorization?response_type=code&client_id=$client_id&redirect_uri=$redirect_uri";

    // Exibe o link para o usuário autorizar a aplicação
    echo "<a href='https://auth.mercadolivre.com.br/authorization?response_type=code&client_id=7129933654525144&redirect_uri=https://abreai.link/6uy4a'>Clique aqui para autorizar a aplicação</a>";
    exit;
}

// Passo 2: Trocar o código de autorização pelo token de acesso
if (isset($_GET['code'])) {
    // Obtém o código de autorização
    $code = $_GET['code'];

    // Endpoint para troca do código por um token
    $token_url = 'https://api.mercadolibre.com/oauth/token';

    // Dados da requisição
    $data = [
        'grant_type' => 'authorization_code',
        'client_id' => $client_id,
        'client_secret' => $client_secret,
        'code' => $code,
        'redirect_uri' => $redirect_uri
    ];

    // Enviar requisição POST para obter o token
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

    // Verifica se o token foi obtido com sucesso
    if (isset($token_data['access_token'])) {
        echo "Token de acesso obtido com sucesso!<br>";
        $access_token = $token_data['access_token'];
    } else {
        echo "Erro ao obter o token de acesso: " . $token_data['error'];
        exit;
    }
}

// Passo 3: Criar o produto com o token de acesso
if (isset($access_token)) {
    // Obter categorias para escolher uma válida
    $categories_url = 'https://api.mercadolibre.com/sites/MLB/categories';
    $response_categories = file_get_contents($categories_url);
    $categories = json_decode($response_categories, true);
    
    // Verifica se obteve categorias com sucesso
    if (!empty($categories)) {
        // Aqui você pode escolher uma categoria válida
        $category_id = $categories[0]['id']; // Apenas exemplo, escolha uma categoria válida
    } else {
        echo "Erro ao obter as categorias.";
        exit;
    }

    // Dados do produto que você deseja criar
    $product_data = [
        'title' => 'Produto Exemplo',
        'category_id' => $category_id, // ID da categoria do Mercado Livre
        'price' => 100.00,
        'currency_id' => 'BRL',
        'available_quantity' => 10,
        'condition' => 'new',
        'description' => [
            'plain_text' => 'Descrição do produto.'
        ],
        'pictures' => [
            [
                'source' => 'https://www.exemplo.com/imagem.jpg' // URL da imagem do produto
            ]
        ]
    ];

    // Endpoint para criação de produto
    $create_product_url = 'https://api.mercadolibre.com/items?access_token=' . $access_token;

    // Enviar requisição POST para criar o produto
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

    // Exibe o resultado da criação do produto
    if (isset($product_response['id'])) {
        echo "Produto criado com sucesso! ID: " . $product_response['id'] . "<br>";
        echo "Status: " . $product_response['status'] . "<br>";
    } else {
        echo "Erro ao criar o produto: " . (isset($product_response['message']) ? $product_response['message'] : 'Erro desconhecido');
    }
}
?>
