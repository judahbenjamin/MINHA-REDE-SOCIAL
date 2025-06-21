<?php
    session_start();

    if(!isset($_SESSION['usuario'])){
        header('Location: index.php?erro=1');
        exit(); // Garante que o script pare se o usuário não estiver logado
    }

    // Inicializa as variáveis para os contadores no PHP, mas eles serão atualizados via AJAX
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

                // Associar o evento de click ao botão de tweet
                $('#btn_tweet').click( function(){
                    // Verifica se o campo de texto do tweet não está vazio
                    if($('#texto_tweet').val().length > 0){
                        
                        $.ajax({
                            url: 'inclui_tweet.php',
                            method: 'post',
                            data: $('#form_tweet').serialize(), // Serializa os dados do formulário (texto_tweet)
                            success: function(data) {
                                $('#texto_tweet').val(''); // Limpa o campo de texto após o sucesso
                                // console.log("Resposta de inclui_tweet.php:", data); // Para depuração
                                atualizarFeedETotais(); // Chama a função para atualizar o feed e os contadores
                            },
                            error: function(jqXHR, textStatus, errorThrown) {
                                console.error("Erro na requisição AJAX para inclui_tweet.php:", textStatus, errorThrown);
                                alert("Ocorreu um erro ao tentar publicar o tweet. Verifique o console para mais detalhes.");
                            }
                        });

                    }

                });


                // Função para atualizar o feed de tweets e os totais (tweets e seguidores)
                function atualizarFeedETotais(){
                    // Carregar os TWEETS na div #tweets
                    $.ajax({
                        url: 'get_tweet.php',
                        success: function(data) {
                            $('#tweets').html(data);
                            // Ativar o evento de click para os botões de exclusão
                            // Eles são criados dinamicamente, então precisam ser vinculados após o carregamento do HTML
                            $('.btn_excluir_tweet').click(function(){
                                var id_tweet_a_excluir = $(this).data('id_tweet');
                                
                                if(confirm("Tem certeza que deseja excluir este tweet?")){
                                    $.ajax({
                                        url: 'exclui_tweet.php',
                                        method: 'post',
                                        data: { id_tweet: id_tweet_a_excluir },
                                        success: function(data){
                                            // console.log("Resposta de exclui_tweet.php:", data); // Para depuração
                                            atualizarFeedETotais(); // Recarrega o feed e os contadores após a exclusão
                                        },
                                        error: function(jqXHR, textStatus, errorThrown) {
                                            console.error("Erro na requisição AJAX para exclui_tweet.php:", textStatus, errorThrown);
                                            alert("Ocorreu um erro ao tentar excluir o tweet. Verifique o console para mais detalhes.");
                                        }
                                    });
                                }
                            });

                            // NOVO: Evento para abrir o modal de edição e preencher o campo
                            // O .on('click', ...) é usado para elementos gerados dinamicamente
                            $('.btn_editar_tweet').click(function(event){
                                var button = $(this) // Botão que acionou o modal
                                var id_tweet = button.data('id_tweet') // Extrai info do data-* attributes
                                var texto_tweet = button.data('texto_tweet')

                                var modal = $('#modalEditarTweet')
                                modal.find('#campo-texto-editar').val(texto_tweet) // Preenche a textarea
                                modal.find('#id-tweet-editar').val(id_tweet) // Guarda o ID do tweet em um campo hidden
                                modal.modal('show'); // Abre o modal
                            });

                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            console.error("Erro na requisição AJAX para get_tweet.php:", textStatus, errorThrown);
                        }
                    });

                    // Atualizar os totais (tweets e seguidores) no painel lateral
                    $.ajax({
                        url: 'get_totais.php',
                        method: 'post',
                        data: { id_usuario: <?= $_SESSION['id_usuario'] ?> }, 
                        dataType: 'json', // Esperamos um JSON como resposta
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

                // Carrega o feed e os totais na primeira vez que a página é carregada
                atualizarFeedETotais(); 

                // Script para o Upload da Foto de Perfil via AJAX
                $('#form_upload_foto').submit(function(e){
                    e.preventDefault(); // Impede o envio tradicional do formulário

                    var formData = new FormData(this); // Captura os dados do formulário, incluindo o arquivo

                    $.ajax({
                        url: 'processa_upload_foto.php',
                        type: 'POST',
                        data: formData,
                        processData: false, // Não processar os dados (importante para FormData)
                        contentType: false, // Não definir o tipo de conteúdo (importante para FormData)
                        dataType: 'json', // Esperar uma resposta JSON
                        success: function(response){
                            if(response.status === 'success'){
                                $('#upload_feedback').html('<div class="alert alert-success">' + response.message + '</div>');
                                // Atualiza a imagem de perfil no DOM sem recarregar a página
                                $('#foto_perfil_main').attr('src', response.foto_perfil_url + '?' + new Date().getTime()); // Adiciona timestamp para evitar cache
                                // Fechar o modal
                                $('#modalFotoPerfil').modal('hide');
                                // Limpar o input de arquivo (opcional)
                                $('#foto_perfil_upload').val('');

                            } else {
                                $('#upload_feedback').html('<div class="alert alert-danger">' + response.message + '</div>');
                            }
                        },
                        error: function(jqXHR, textStatus, errorThrown){
                            console.error("Erro no upload: ", textStatus, errorThrown);
                            $('#upload_feedback').html('<div class="alert alert-danger">Erro de comunicação com o servidor.</div>');
                        }
                    });
                });

                // NOVO: Evento para salvar as alterações do tweet (botão dentro do modal de edição)
                $('#btn_salvar_edicao').click(function(){
                    var id_tweet = $('#id-tweet-editar').val();
                    var texto_editado = $('#campo-texto-editar').val();

                    if (texto_editado.trim() === '') {
                        alert('O tweet não pode estar vazio!');
                        return false;
                    }

                    // Requisição AJAX para o novo arquivo editar_tweet.php
                    $.ajax({
                        url: 'editar_tweet.php',
                        method: 'post',
                        data: { id_tweet: id_tweet, texto_editado: texto_editado },
                        success: function(data){
                            // console.log(data); // Para depuração
                            $('#modalEditarTweet').modal('hide'); // Fecha o modal
                            atualizarFeedETotais(); // Atualiza a timeline após a edição (recarrega os tweets)
                            alert('Tweet editado com sucesso!'); // Feedback visual
                        },
                        error: function(xhr, status, error){
                            console.error('Erro ao editar tweet:', status, error);
                            alert('Ocorreu um erro ao editar o tweet. Tente novamente.');
                        }
                    });
                });

            }); // Fim do $(document).ready

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
                <li><a href="sair.php">Sair</a></li>
              </ul>
            </div></div>
        </nav>


        <div class="container">
            <div class="col-md-3">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <img id="foto_perfil_main" src="<?= $foto_perfil_url ?>" class="profile-picture center-block img-responsive" alt="Foto de Perfil">
                        <div class="clearfix"></div> <h4 class="text-center" style="margin-top: 10px;"><?= $_SESSION['usuario'] ?></h4>

                        <hr/>
                        <div class="col-md-6">
                            TWEETS <span class="count"><?= $qtde_tweets ?></span>
                        </div>
                        <div class="col-md-6">
                            SEGUIDORES <span class="count"><?= $qtde_seguidores?></span>
                        </div>
                        <div class="clearfix"></div> <br/> <button type="button" class="btn btn-info btn-block" data-toggle="modal" data-target="#modalFotoPerfil">
                            Alterar Foto de Perfil
                        </button>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <form id="form_tweet" class="input-group">
                            <input type="text" id="texto_tweet" name="texto_tweet" class="form-control" placeholder="O que está acontecendo agora?" maxlength="140" />
                            <span class="input-group-btn">
                                <button class="btn btn-default" id="btn_tweet" type="button">Tweet</button>
                            </span>
                        </form>
                    </div>
                </div>

                <div id="tweets" class="list-group">
                    </div>

            </div>
            <div class="col-md-3">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <h4><a href="procurar_pessoas.php">Procurar por pessoas</a></h4>
                    </div>
                </div>
            </div>

        </div>


        </div>
    
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>

        <div class="modal fade" id="modalFotoPerfil" tabindex="-1" role="dialog" aria-labelledby="modalFotoPerfilLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="modalFotoPerfilLabel">Alterar Foto de Perfil</h4>
                    </div>
                    <div class="modal-body">
                        <form id="form_upload_foto" method="post" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="foto">Selecione uma imagem:</label>
                                <input type="file" class="form-control" id="foto_perfil_upload" name="foto_perfil_upload" accept="image/*">
                            </div>
                            <button type="submit" class="btn btn-primary">Fazer Upload</button>
                        </form>
                        <div id="upload_feedback" style="margin-top: 15px;"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modalEditarTweet" tabindex="-1" role="dialog" aria-labelledby="modalEditarTweetLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="modalEditarTweetLabel">Editar Tweet</h4>
                    </div>
                    <div class="modal-body">
                        <form>
                            <div class="form-group">
                                <label for="campo-texto-editar" class="control-label">Tweet:</label>
                                <textarea class="form-control" id="campo-texto-editar" rows="5"></textarea>
                            </div>
                            <input type="hidden" id="id-tweet-editar">
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
                        <button type="button" class="btn btn-primary" id="btn_salvar_edicao">Salvar Alterações</button>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>