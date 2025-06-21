<?php
session_start();

if (!isset($_SESSION['usuario']) || !isset($_SESSION['id_usuario'])) {
    echo json_encode(['status' => 'error', 'message' => 'Usuário não autenticado.']);
    exit();
}

require_once('conexao.php');

$id_usuario = $_SESSION['id_usuario'];
$dir_uploads = 'uploads/perfis/'; // Pasta onde as fotos serão salvas

// Cria a pasta de destino se ela não existir
if (!is_dir($dir_uploads)) {
    mkdir($dir_uploads, 0755, true); // 0755 é uma permissão comum, true para criar recursivamente
}

if (isset($_FILES['foto_perfil_upload']) && $_FILES['foto_perfil_upload']['error'] == UPLOAD_ERR_OK) {
    $arquivo_tmp = $_FILES['foto_perfil_upload']['tmp_name'];
    $nome_original = $_FILES['foto_perfil_upload']['name'];
    $tipo_arquivo = $_FILES['foto_perfil_upload']['type'];
    $tamanho_arquivo = $_FILES['foto_perfil_upload']['size'];

    // Validações básicas
    $extensoes_permitidas = ['jpg', 'jpeg', 'png', 'gif'];
    $max_tamanho = 5 * 1024 * 1024; // 5MB

    $extensao = strtolower(pathinfo($nome_original, PATHINFO_EXTENSION));

    if (!in_array($extensao, $extensoes_permitidas)) {
        echo json_encode(['status' => 'error', 'message' => 'Tipo de arquivo não permitido. Apenas JPG, JPEG, PNG, GIF.']);
        exit();
    }

    if ($tamanho_arquivo > $max_tamanho) {
        echo json_encode(['status' => 'error', 'message' => 'O arquivo é muito grande. Tamanho máximo: 5MB.']);
        exit();
    }

    // Gerar um nome de arquivo único para evitar conflitos (ex: id_usuario.extensao)
    // Isso também permite que o usuário atualize a foto, sobrescrevendo a antiga
    $nome_novo_arquivo = $id_usuario . '.' . $extensao;
    $caminho_final_arquivo = $dir_uploads . $nome_novo_arquivo;

    // Tentar mover o arquivo para o destino final
    if (move_uploaded_file($arquivo_tmp, $caminho_final_arquivo)) {
        $objDb = new conexao();
        $link = $objDb->conecta_mysql();

        // Atualizar o caminho da foto de perfil no banco de dados
        $caminho_foto_db = mysqli_real_escape_string($link, $caminho_final_arquivo);
        $sql = "UPDATE usuarios SET foto_perfil = '$caminho_foto_db' WHERE id = $id_usuario";

        if (mysqli_query($link, $sql)) {
            echo json_encode(['status' => 'success', 'message' => 'Foto de perfil atualizada com sucesso!', 'foto_perfil_url' => $caminho_final_arquivo]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Erro ao atualizar o banco de dados: ' . mysqli_error($link)]);
        }

        mysqli_close($link);

    } else {
        echo json_encode(['status' => 'error', 'message' => 'Erro ao mover o arquivo para o servidor.']);
    }

} else {
    // Melhorar a mensagem de erro para debug
    $error_code = $_FILES['foto_perfil_upload']['error'] ?? UPLOAD_ERR_NO_FILE;
    $error_message = 'Nenhum arquivo enviado ou erro no upload. Código: ' . $error_code;
    switch ($error_code) {
        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE:
            $error_message = 'O arquivo excede o tamanho máximo permitido no servidor (php.ini).';
            break;
        case UPLOAD_ERR_PARTIAL:
            $error_message = 'O upload do arquivo foi feito parcialmente.';
            break;
        case UPLOAD_ERR_NO_FILE:
            $error_message = 'Nenhum arquivo foi enviado.';
            break;
        case UPLOAD_ERR_NO_TMP_DIR:
            $error_message = 'Pasta temporária ausente.';
            break;
        case UPLOAD_ERR_CANT_WRITE:
            $error_message = 'Falha ao escrever o arquivo em disco.';
            break;
        case UPLOAD_ERR_EXTENSION:
            $error_message = 'Uma extensão do PHP interrompeu o upload do arquivo.';
            break;
    }
    echo json_encode(['status' => 'error', 'message' => $error_message]);
}
?>