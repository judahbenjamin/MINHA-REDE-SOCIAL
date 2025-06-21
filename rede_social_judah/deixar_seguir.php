<?php

    session_start();

    if(!isset($_SESSION['usuario'])){
        header('Location: index.php?erro=1');
        exit();
    }

    require_once('conexao.php');

    $id_usuario = $_SESSION['id_usuario'] ?? '';
    $deixar_seguir_id_usuario = $_POST['deixar_seguir_id_usuario'] ?? '';

    // Validação robusta: verifica se os IDs são numéricos válidos e não estão vazios
    if(!is_numeric($id_usuario) || !is_numeric($deixar_seguir_id_usuario) || empty($id_usuario) || empty($deixar_seguir_id_usuario) || $id_usuario <= 0 || $deixar_seguir_id_usuario <= 0){
        echo 'Erro: IDs de usuário inválidos ou incompletos.';
        exit();
    }

    $objDb = new conexao();
    $link = $objDb->conecta_mysql();

    // Deletar o relacionamento de seguir
    $sql = " DELETE FROM usuarios_seguidores WHERE id_usuario = $id_usuario AND seguindo_id_usuario = $deixar_seguir_id_usuario ";

    if(mysqli_query($link, $sql)){
        echo 'Sucesso ao deixar de seguir!'; // Resposta para o JavaScript (visível no console.log)
    } else {
        echo 'Erro ao deixar de seguir: ' . mysqli_error($link); // Exibe o erro do MySQL para depuração
    }

    // Fechar a conexão
    mysqli_close($link);

?>