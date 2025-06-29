<?php
    $erro = isset($_GET['erro']) ? $_GET['erro'] : 0;
?>

<!DOCTYPE HTML>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">

        <title>Twitter clone</title>

        <script src="https://code.jquery.com/jquery-2.2.4.min.js"></script>

        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
        
        <link rel="stylesheet" href="css/estilo.css"> 

        <script>
            $(document).ready( function(){

                //verificar se os campos de usuário e senha foram devidamente preenchidos
                $('#btn_login').click(function(){

                    var campo_vazio = false;

                    if($('#campo_usuario').val() == ''){
                        $('#campo_usuario').css({'border-color' : 'var(--twitter-dark-blue)'}); // Usar cor da paleta
                        campo_vazio = true;
                    } else {
                        $('#campo_usuario').css({'border-color': 'var(--border-color)'}); // Restaurar cor da paleta
                    }

                    if($('#campo_senha').val() == ''){
                        $('#campo_senha').css({'border-color' : 'var(--twitter-dark-blue)'}); // Usar cor da paleta
                        campo_vazio = true;
                    } else {
                        $('#campo_senha').css({'border-color': 'var(--border-color)'}); // Restaurar cor da paleta
                    }

                    if(campo_vazio) return false;
                });
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
              <img src="imagens/icone_twitter.png" alt="Logo Twitter" />
            </div>
            
            <div id="navbar" class="navbar-collapse collapse">
              <ul class="nav navbar-nav navbar-right">
                <li><a href="inscrevase.php">Inscrever-se</a></li>
                <li class="<?= $erro == 1 ? 'open' : '' ?>">
                    <a id="entrar" data-target="#" href="#" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Entrar</a>
                    <ul class="dropdown-menu login-dropdown-menu" aria-labelledby="entrar"> <div class="col-md-12">
                            <p class="login-heading">Você possui uma conta?</p> <br />
                            <form method="post" action="validar_acesso.php" id="formLogin">
                                <div class="form-group">
                                    <input type="text" class="form-control" id="campo_usuario" name="usuario" placeholder="Usuário" />
                                </div>
                                
                                <div class="form-group">
                                    <input type="password" class="form-control" id="campo_senha" name="senha" placeholder="Senha" />
                                </div>
                                
                                <button type="submit" class="btn btn-primary btn-block" id="btn_login">Entrar</button> <br />
                                <?php
                                    if($erro == 1){
                                        echo '<div class="alert alert-danger" style="margin-top: 10px;">Usuário e ou senha inválido(s)</div>';
                                    }
                                ?>
                            </form>
                        </div>
                    </ul>
                </li>
              </ul>
            </div></div>
        </nav>


        <div class="container">

          <div class="jumbotron index-jumbotron"> <h1>Bem vindo ao X (Twitter clone)</h1> <p>Veja o que está acontecendo agora...</p>
          </div>

          <div class="clearfix"></div>
        </div>
    
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
    
    </body>
</html>