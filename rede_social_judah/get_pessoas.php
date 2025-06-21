<?php
    session_start();

    if(!isset($_SESSION['usuario'])){
        header('Location: index.php?erro=1');
        exit();
    }

    require_once('conexao.php');

    $nome_pessoa = $_POST['nome_pessoa'] ?? '';
    $id_usuario_logado = $_SESSION['id_usuario'] ?? '';

    $objDb = new conexao();
    $link = $objDb->conecta_mysql();

    // Query para buscar pessoas, incluindo a foto_perfil e a verificação de "já segue"
    // Certifique-se que a coluna 'foto_perfil' existe na sua tabela 'usuarios'
    $sql = " SELECT u.id, u.usuario, u.email, u.foto_perfil, "; // Adicionado u.foto_perfil aqui
    $sql .= " (SELECT COUNT(*) FROM usuarios_seguidores AS us WHERE us.id_usuario = $id_usuario_logado AND us.seguindo_id_usuario = u.id) AS ja_segue ";
    $sql .= " FROM usuarios AS u ";
    $sql .= " WHERE u.usuario LIKE '%$nome_pessoa%' AND u.id <> $id_usuario_logado ";
    $sql .= " ORDER BY u.usuario ASC ";

    $resultado_id = mysqli_query($link, $sql);

    if($resultado_id){
        if(mysqli_num_rows($resultado_id) == 0){
            echo '<p class="list-group-item">Nenhuma pessoa encontrada com este nome.</p>';
        }

        while($registro = mysqli_fetch_array($resultado_id, MYSQLI_ASSOC)){
            $foto_perfil_url = $registro['foto_perfil'] ?? 'imagens/avatar_padrao.png'; // Caminho padrão se não houver foto

            echo '<a href="#" class="list-group-item">';
                echo '<div class="media">'; // Usa o componente media do Bootstrap para alinhar imagem e conteúdo
                    echo '<div class="media-left media-middle">'; // media-middle para alinhar verticalmente
                        echo '<img src="'.$foto_perfil_url.'" class="media-object profile-picture-small" alt="Foto de Perfil">';
                    echo '</div>';
                    echo '<div class="media-body media-body-search">'; // Adicionada classe 'media-body-search'
                        echo '<h4 class="media-heading"><strong>'.$registro['usuario'].'</strong> <small> - '.$registro['email'].'</small></h4>';
                    echo '</div>';
                echo '</div>'; // Fim do media

                echo '<div class="pull-right">';
                $id_usuario_sendo_buscado = $registro['id'];
                $ja_segue = $registro['ja_segue'];

                if($ja_segue == 0){
                    echo '<button type="button" id="btn_seguir_'.$id_usuario_sendo_buscado.'" class="btn btn-default btn_seguir" data-id_usuario="'.$id_usuario_sendo_buscado.'">Seguir</button>';
                    echo '<button type="button" id="btn_deixar_seguir_'.$id_usuario_sendo_buscado.'" class="btn btn-primary btn_deixar_seguir" data-id_usuario="'.$id_usuario_sendo_buscado.'" style="display: none;">Deixar de Seguir</button>';
                } else {
                    echo '<button type="button" id="btn_seguir_'.$id_usuario_sendo_buscado.'" class="btn btn-default btn_seguir" data-id_usuario="'.$id_usuario_sendo_buscado.'" style="display: none;">Seguir</button>';
                    echo '<button type="button" id="btn_deixar_seguir_'.$id_usuario_sendo_buscado.'" class="btn btn-primary btn_deixar_seguir" data-id_usuario="'.$id_usuario_sendo_buscado.'">Deixar de Seguir</button>';
                }
                echo '</div>';
                echo '<div class="clearfix"></div>'; // Garante que o layout não quebre
            echo '</a>';
        }

    } else {
        echo "Erro na consulta de pessoas no banco de dados! " . mysqli_error($link);
    }

    mysqli_close($link);
?>