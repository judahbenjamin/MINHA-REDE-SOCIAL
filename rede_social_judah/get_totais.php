<?php
session_start();

// É crucial que não haja nenhum caractere (espaço, quebra de linha, etc.) antes da tag <?php
// ou antes do header(). Isso garante que o JSON seja a única coisa na resposta.
header('Content-Type: application/json');

if (!isset($_SESSION['usuario']) || !isset($_SESSION['id_usuario'])) {
    // Retorna um erro JSON se o usuário não estiver autenticado ou a sessão inválida
    echo json_encode(['status' => 'error', 'message' => 'Usuário não autenticado ou sessão inválida.']);
    exit();
}

require_once('conexao.php');

$id_usuario = $_SESSION['id_usuario'];

$objDb = new conexao();
$link = $objDb->conecta_mysql();

$qtde_tweets = 0;
$qtde_seguidores = 0;

// Query para obter a quantidade de tweets do usuário logado
// Nome da tabela 'tweet' (singular) está correto de acordo com seu SQL
$sql_tweets = " SELECT COUNT(*) AS qtde_tweets FROM tweet WHERE id_usuario = $id_usuario ";
$resultado_tweets = mysqli_query($link, $sql_tweets);

if ($resultado_tweets) {
    $registro_tweets = mysqli_fetch_array($resultado_tweets, MYSQLI_ASSOC);
    $qtde_tweets = $registro_tweets['qtde_tweets'];
} else {
    // Logar o erro para depuração no servidor, mas não exibir para o usuário final no JSON
    error_log('Erro ao executar query de tweets em get_totais.php: ' . mysqli_error($link));
}

// Query para obter a quantidade de seguidores do usuário logado
$sql_seguidores = " SELECT COUNT(*) AS qtde_seguidores FROM usuarios_seguidores WHERE seguindo_id_usuario = $id_usuario ";
$resultado_seguidores = mysqli_query($link, $sql_seguidores);

if ($resultado_seguidores) {
    $registro_seguidores = mysqli_fetch_array($resultado_seguidores, MYSQLI_ASSOC);
    $qtde_seguidores = $registro_seguidores['qtde_seguidores'];
} else {
    // Logar o erro para depuração no servidor, mas não exibir para o usuário final no JSON
    error_log('Erro ao executar query de seguidores em get_totais.php: ' . mysqli_error($link));
}

mysqli_close($link);

// Retorna os dados como um objeto JSON
echo json_encode([
    'status' => 'success',
    'qtde_tweets' => $qtde_tweets,
    'qtde_seguidores' => $qtde_seguidores
]);

exit();
?>