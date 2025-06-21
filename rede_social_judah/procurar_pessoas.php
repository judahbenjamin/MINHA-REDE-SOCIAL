<?php
    session_start();

    if(!isset($_SESSION['usuario'])){
        header('Location: index.php?erro=1');
        exit();
    }

    // Estas variáveis são inicializadas, mas seus valores serão populados via AJAX no carregamento da página
    // para garantir que os valores sejam sempre atualizados dinamicamente.
    $qtde_tweets = 0;
    $qtde_seguidores = 0;

    // Inclui a conexão para buscar a foto de perfil do usuário logado
    require_once('conexao.php');
    $objDb = new conexao();
    $link = $objDb->conecta_mysql();

    $id_usuario_logado = $_SESSION['id_usuario'];
    $sql_foto_perfil = "SELECT foto_perfil FROM usuarios WHERE id = $id_usuario_logado";
    $resultado_foto = mysqli_query($link, $sql_foto_perfil);
    $registro_foto = mysqli_fetch_assoc($resultado_foto);
    $foto_perfil_url = $registro_foto['foto_perfil'] ?? 'imagens/avatar_padrao.png'; // Caminho padrão se não houver foto

    mysqli_close($link);
?>

<!DOCTYPE HTML>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">

        <title>Twitter clone</title>
        
        <script src="https://code.jquery.com/jquery-2.2.4.min.js"></script>

        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
        
        <link rel="stylesheet" href="css/estilo.css">

        <script type="text/javascript">

            $(document).ready( function(){

                // Função para atualizar os contadores de tweets e seguidores
                function atualizarTotais() {
                    $.ajax({
                        url: 'get_totais.php',
                        method: 'post',
                        data: { id_usuario: <?= $_SESSION['id_usuario'] ?> },
                        dataType: 'json',
                        success: function(data) {
                            if(data.status === 'success') {
                                // Atualiza o contador de tweets
                                $('.col-md-6:eq(0) .count').html(data.qtde_tweets);
                                // Atualiza o contador de seguidores
                                $('.col-md-6:eq(1) .count').html(data.qtde_seguidores);
                            } else {
                                console.error('Erro ao buscar totais:', data.message);
                            }
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            console.error("Erro na requisição AJAX para get_totais.php:", textStatus, errorThrown);
                        }
                    });
                }

                // Função para carregar as pessoas na div #pessoas
                function carregarPessoas() {
                    // Só faz a busca se o campo de pesquisa tiver conteúdo
                    if($('#nome_pessoa').val().length > 0){
                        
                        $.ajax({
                            url: 'get_pessoas.php',
                            method: 'post',
                            data: $('#form_procurar_pessoas').serialize(),
                            success: function(data) {
                                $('#pessoas').html(data); // Carrega o HTML retornado na div #pessoas

                                // Re-bind dos eventos de click para os botões "Seguir" e "Deixar de Seguir"
                                // Pois eles são adicionados dinamicamente ao DOM.
                                $('.btn_seguir').click( function(){
                                    var id_usuario_a_seguir = $(this).data('id_usuario');

                                    // Esconde o botão "Seguir" e mostra "Deixar de Seguir" visualmente (UX)
                                    $('#btn_seguir_'+id_usuario_a_seguir).hide();
                                    $('#btn_deixar_seguir_'+id_usuario_a_seguir).show();

                                    $.ajax({
                                        url: 'seguir.php',
                                        method: 'post',
                                        data: { seguir_id_usuario: id_usuario_a_seguir },
                                        success: function(data){
                                            // console.log("Resposta de seguir.php:", data); // Para depuração
                                            atualizarTotais(); // Atualiza os contadores no painel lateral
                                        },
                                        error: function(jqXHR, textStatus, errorThrown) {
                                            console.error("Erro na requisição AJAX para seguir.php:", textStatus, errorThrown);
                                            alert("Ocorreu um erro ao tentar seguir. Verifique o console para mais detalhes."); // MANTER ESTE ALERT DE ERRO
                                            // Reverte a exibição dos botões em caso de erro na requisição
                                            $('#btn_seguir_'+id_usuario_a_seguir).show();
                                            $('#btn_deixar_seguir_'+id_usuario_a_seguir).hide();
                                        }
                                    });
                                });

                                $('.btn_deixar_seguir').click( function(){
                                    var id_usuario_a_deixar_seguir = $(this).data('id_usuario');

                                    // Esconde o botão "Deixar de Seguir" e mostra "Seguir" visualmente (UX)
                                    $('#btn_seguir_'+id_usuario_a_deixar_seguir).show();
                                    $('#btn_deixar_seguir_'+id_usuario_a_deixar_seguir).hide();

                                    $.ajax({
                                        url: 'deixar_seguir.php',
                                        method: 'post',
                                        data: { deixar_seguir_id_usuario: id_usuario_a_deixar_seguir },
                                        success: function(data){
                                            // console.log("Resposta de deixar_seguir.php:", data); // Para depuração
                                            atualizarTotais(); // Atualiza os contadores no painel lateral
                                        },
                                        error: function(jqXHR, textStatus, errorThrown) {
                                            console.error("Erro na requisição AJAX para deixar_seguir.php:", textStatus, errorThrown);
                                            alert("Ocorreu um erro ao tentar deixar de seguir. Verifique o console para mais detalhes."); // MANTER ESTE ALERT DE ERRO
                                            // Reverte a exibição dos botões em caso de erro na requisição
                                            $('#btn_seguir_'+id_usuario_a_deixar_seguir).hide();
                                            $('#btn_deixar_seguir_'+id_usuario_a_deixar_seguir).show();
                                        }
                                    });
                                });
                            },
                            error: function(jqXHR, textStatus, errorThrown) {
                                console.error("Erro na requisição AJAX para get_pessoas.php:", textStatus, errorThrown);
                                $('#pessoas').html("<p>Erro ao carregar pessoas.</p>");
                            }
                        });
                    } else {
                        $('#pessoas').html(''); // Limpa a lista se o campo de busca estiver vazio
                    }
                }

                // Chamar a função de carregar pessoas quando o botão de busca é clicado
                $('#btn_procurar_pessoa').click( function(){
                    carregarPessoas();
                });

                // Chamar a função de atualização de totais no carregamento da página
                atualizarTotais(); 
            });

        </script>
        
    </head>

    <body>

        <nav class="navbar navbar-default navbar-static-top">
          <div class="container">
            <div class="navbar-header">
              <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
              </button>
              <img src="imagens/icone_twitter.png" />
            </div>
            
            <div id="navbar" class="navbar-collapse collapse">
              <ul class="nav navbar-nav navbar-right">
                <li><a href="home.php">Home</a></li>
                <li><a href="sair.php">Sair</a></li>
              </ul>
            </div></div>
        </nav>


        <div class="container">
            <div class="col-md-3">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <img src="<?= $foto_perfil_url ?>" class="profile-picture center-block img-responsive" alt="Foto de Perfil">
                        <div class="clearfix"></div> <h4 class="text-center" style="margin-top: 10px;"><?= $_SESSION['usuario'] ?></h4>

                        <hr/>
                        <div class="col-md-6">
                            TWEETS <span class="count"><?= $qtde_tweets?></span>
                        </div>
                        <div class="col-md-6">
                            SEGUIDORES <span class="count"><?= $qtde_seguidores?></span>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <form id="form_procurar_pessoas" class="input-group">
                            <input type="text" id="nome_pessoa" name="nome_pessoa" class="form-control" placeholder="Quem você está procurando?" maxlength="140" />
                            <span class="input-group-btn">
                                <button class="btn btn-default" id="btn_procurar_pessoa" type="button">Procurar</button>
                            </span>
                        </form>
                    </div>
                </div>

                <div id="pessoas" class="list-group">
                    </div>

            </div>
            <div class="col-md-3">
                <div class="panel panel-default">
                    <div class="panel-body">
                        </div>
                </div>
            </div>

        </div>


        </div>
    
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
    
    </body>
</html>