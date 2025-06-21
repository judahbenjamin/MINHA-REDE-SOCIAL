<?php

    session_start();

    if(!isset($_SESSION['usuario'])){
        header('Location: index.php?erro=1');
        exit();
    }

    require_once('conexao.php');

    $id_usuario = $_SESSION['id_usuario'] ?? '';
    $seguir_id_usuario = $_POST['seguir_id_usuario'] ?? '';

    // Validação robusta: verifica se os IDs são numéricos válidos e não estão vazios
    if(!is_numeric($id_usuario) || !is_numeric($seguir_id_usuario) || empty($id_usuario) || empty($seguir_id_usuario) || $id_usuario <= 0 || $seguir_id_usuario <= 0){
        echo 'Erro: IDs de usuário inválidos ou incompletos.';
        exit();
    }

    $objDb = new conexao();
    $link = $objDb->conecta_mysql();

    // Impedir que o usuário siga a si mesmo
    if ($id_usuario == $seguir_id_usuario) {
        echo 'Erro: Você não pode seguir a si mesmo.';
        mysqli_close($link);
        exit();
    }

    // Verificar se o usuário já segue para evitar duplicidade
    $sql_verifica = "SELECT COUNT(*) AS total FROM usuarios_seguidores WHERE id_usuario = $id_usuario AND seguindo_id_usuario = $seguir_id_usuario";
    $resultado_verifica = mysqli_query($link, $sql_verifica);
    $registro_verifica = mysqli_fetch_array($resultado_verifica, MYSQLI_ASSOC);

    if ($registro_verifica['total'] > 0) {
        echo 'Você já segue este usuário.';
        mysqli_close($link);
        exit();
    }

    // Inserir o relacionamento de seguir
    // A coluna 'id_usuario_seguidor' é auto_increment, não precisa ser inserida
    // A coluna 'data_registro' tem DEFAULT CURRENT_TIMESTAMP
    $sql = " INSERT INTO usuarios_seguidores (id_usuario, seguindo_id_usuario) VALUES ($id_usuario, $seguir_id_usuario) ";

    if(mysqli_query($link, $sql)){
        echo 'Sucesso ao seguir!'; // Resposta para o JavaScript (visível no console.log)
    } else {
        echo 'Erro ao seguir: ' . mysqli_error($link); // Exibe o erro do MySQL para depuração
    }

    // Fechar a conexão
    mysqli_close($link);

?>