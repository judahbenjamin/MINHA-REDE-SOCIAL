<?php
    session_start();

    if(!isset($_SESSION['usuario'])){
        header('Location: index.php?erro=1');
        exit();
    }

    require_once('conexao.php');

    $id_usuario_logado = $_SESSION['id_usuario'] ?? '';

    $objDb = new conexao();
    $link = $objDb->conecta_mysql();

    // Query para buscar tweets
    // Inclui a foto_perfil do usuário que postou o tweet
    $sql = " SELECT t.id_tweet, t.id_usuario, u.usuario, u.foto_perfil, t.tweet, DATE_FORMAT(t.data_inclusao, '%d %b %Y %T') AS data_inclusao_formatada ";
    $sql.= " FROM tweet AS t JOIN usuarios AS u ON t.id_usuario = u.id ";
    $sql.= " WHERE t.id_usuario = $id_usuario_logado OR t.id_usuario IN (SELECT seguindo_id_usuario FROM usuarios_seguidores WHERE id_usuario = $id_usuario_logado) ";
    $sql.= " ORDER BY t.data_inclusao DESC "; // Ordena para os tweets mais recentes aparecerem primeiro

    $resultado_id = mysqli_query($link, $sql);

    if($resultado_id){
        if(mysqli_num_rows($resultado_id) == 0){
            echo '<p class="list-group-item">Nenhum tweet para exibir.</p>';
        }

        while($registro = mysqli_fetch_array($resultado_id, MYSQLI_ASSOC)){
            $id_tweet = $registro['id_tweet']; // Adicionado para facilitar o uso no botão
            $id_usuario_tweet = $registro['id_usuario']; // Adicionado para facilitar o uso no botão
            $texto_tweet = $registro['tweet']; // Adicionado para passar o texto ao modal

            $foto_tweet_perfil = $registro['foto_perfil'] ?? 'imagens/avatar_padrao.png'; // Foto do perfil do autor do tweet
            
            echo '<a href="#" class="list-group-item">';
                echo '<div class="media">'; // Usa o componente media do Bootstrap para alinhar imagem e conteúdo
                    echo '<div class="media-left">';
                        echo '<img src="'.$foto_tweet_perfil.'" class="media-object profile-picture-small" alt="Foto de Perfil">';
                    echo '</div>';
                    echo '<div class="media-body">';
                        echo '<h4 class="media-heading"><strong>'.$registro['usuario'].'</strong> <small> - '.$registro['data_inclusao_formatada'].'</small></h4>';
                        echo '<p>'.$registro['tweet'].'</p>';
                    echo '</div>';
                echo '</div>'; // Fim do media
                
                // Botões de ação (editar e exclusão), apenas para tweets que pertencem ao usuário logado
                if($id_usuario_tweet == $id_usuario_logado){ // Usando as variáveis que definimos acima
                    echo '<div class="pull-right">';
                    // NOVO: Botão Editar Tweet
                    echo '<button type="button" class="btn btn-info btn-xs btn_editar_tweet" data-id_tweet="'.$id_tweet.'" data-texto_tweet="'.htmlspecialchars($texto_tweet).'">Editar</button>';
                    // EXISTENTE: Botão Excluir Tweet
                    echo ' <button type="button" class="btn btn-danger btn-xs btn_excluir_tweet" data-id_tweet="'.$id_tweet.'">Excluir</button>';
                    echo '</div>';
                }

                echo '<div class="clearfix"></div>'; // Garante que o layout não quebre
            echo '</a>';
        }

    } else {
        echo "Erro na consulta de tweets no banco de dados! " . mysqli_error($link);
    }

    mysqli_close($link);
?>