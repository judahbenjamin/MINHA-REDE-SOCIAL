<?php

    session_start();

    if(!isset($_SESSION['usuario'])){
        header('Location: index.php?erro=1');
        exit();
    }

    require_once('conexao.php');

    $id_tweet = $_POST['id_tweet'] ?? '';
    $texto_editado = $_POST['texto_editado'] ?? '';
    $id_usuario_logado = $_SESSION['id_usuario'] ?? '';

    // Validações básicas
    if($id_tweet == '' || $texto_editado == ''){
        echo 'Erro: ID do tweet ou texto vazio.';
        exit();
    }

    // Garante que o texto editado não exceda o limite de caracteres do banco (ex: 140 para tweets)
    // ATENÇÃO: Ajuste este limite (140) para corresponder ao tamanho da sua coluna 'tweet' no banco de dados!
    $texto_editado = substr($texto_editado, 0, 140);

    $objDb = new conexao();
    $link = $objDb->conecta_mysql();

    // Prepara a query SQL para atualização usando prepared statements para segurança
    // IMPORTANTE: A query garante que APENAS o proprietário do tweet possa editá-lo
    $sql = " UPDATE tweet SET tweet = ? WHERE id_tweet = ? AND id_usuario = ? ";

    if($stmt = mysqli_prepare($link, $sql)){
        mysqli_stmt_bind_param($stmt, "sii", $texto_editado, $id_tweet, $id_usuario_logado); // 's' para string, 'i' para int

        if(mysqli_stmt_execute($stmt)){
            echo 'Tweet atualizado com sucesso!';
        } else {
            echo 'Erro ao atualizar o tweet no banco de dados. Detalhes: ' . mysqli_stmt_error($stmt);
        }
        mysqli_stmt_close($stmt);
    } else {
        echo 'Erro na preparação da query: ' . mysqli_error($link);
    }

    mysqli_close($link);

?>