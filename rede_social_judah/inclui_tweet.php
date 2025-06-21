<?php

    session_start();

    if(!isset($_SESSION['usuario'])){
        header('Location: index.php?erro=1');
        exit(); // Garante que o script pare se o usuário não estiver logado
    }

    require_once('conexao.php');

    $texto_tweet = $_POST['texto_tweet'] ?? ''; // Usa operador null coalescing para evitar Notice
    $id_usuario = $_SESSION['id_usuario'] ?? '';

    // Validação robusta: verifica se o texto não está vazio e se o ID do usuário é um número válido
    if(empty($texto_tweet) || !is_numeric($id_usuario) || $id_usuario <= 0){
        echo 'Erro: Tweet vazio ou ID de usuário inválido.';
        exit();
    }

    $objDb = new conexao();
    $link = $objDb->conecta_mysql();

    // Proteção contra SQL Injection para o texto do tweet
    $texto_tweet_seguro = mysqli_real_escape_string($link, $texto_tweet);

    // CORREÇÃO: Usando o nome correto da coluna de data: `data_inclusao`
    $sql = " INSERT INTO tweet (id_usuario, tweet, data_inclusao) VALUES ($id_usuario, '$texto_tweet_seguro', NOW()) ";

    if(mysqli_query($link, $sql)){
        echo 'Tweet publicado com sucesso!'; // Resposta de sucesso para o AJAX
    } else {
        echo 'Erro ao publicar o tweet: ' . mysqli_error($link); // Mensagem de erro do MySQL para depuração
    }

    // Fechar a conexão com o banco de dados
    mysqli_close($link);

?>