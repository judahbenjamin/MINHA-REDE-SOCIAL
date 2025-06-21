<?php
    session_start();

    if(!isset($_SESSION['usuario'])){
        header('Location: index.php?erro=1');
        exit();
    }

    require_once('conexao.php');

    $id_tweet_a_excluir = $_POST['id_tweet'] ?? '';
    $id_usuario_logado = $_SESSION['id_usuario'] ?? '';

    // Validação: verifica se os IDs são numéricos e válidos
    if(!is_numeric($id_tweet_a_excluir) || empty($id_tweet_a_excluir) || $id_tweet_a_excluir <= 0 ||
       !is_numeric($id_usuario_logado) || empty($id_usuario_logado) || $id_usuario_logado <= 0){
        echo 'Erro: ID do tweet ou ID de usuário inválidos.';
        exit();
    }

    $objDb = new conexao();
    $link = $objDb->conecta_mysql();

    // Verificação de segurança:
    // Garante que o tweet a ser excluído pertence ao usuário logado.
    // Isso é CRUCIAL para evitar que um usuário exclua tweets de outros.
    $sql_verifica_propriedade = "SELECT id_usuario FROM tweet WHERE id_tweet = $id_tweet_a_excluir";
    $resultado_verifica = mysqli_query($link, $sql_verifica_propriedade);

    if($resultado_verifica && mysqli_num_rows($resultado_verifica) > 0){
        $registro_tweet = mysqli_fetch_array($resultado_verifica, MYSQLI_ASSOC);
        
        if($registro_tweet['id_usuario'] == $id_usuario_logado){
            // Se o tweet pertence ao usuário logado, pode excluí-lo
            $sql_excluir = " DELETE FROM tweet WHERE id_tweet = $id_tweet_a_excluir ";
            if(mysqli_query($link, $sql_excluir)){
                echo 'Tweet excluído com sucesso!';
            } else {
                echo 'Erro ao excluir o tweet: ' . mysqli_error($link);
            }
        } else {
            echo 'Erro: Você não tem permissão para excluir este tweet.';
        }
    } else {
        echo 'Erro: Tweet não encontrado ou erro na verificação.';
    }

    mysqli_close($link);
?>