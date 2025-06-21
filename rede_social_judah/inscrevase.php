<?php
    $erro_usuario = isset($_GET['erro_usuario']) ? $_GET['erro_usuario'] : 0;
    $erro_email = isset($_GET['erro_email']) ? $_GET['erro_email'] : 0;
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
            // Script de validação de formulário (mantido como está, mas pode ser melhorado com AJAX se quiser)
            $(document).ready( function(){
                // Não há validação JS aqui, já que o PHP faz a validação e redireciona
                // A validação de campos vazios já é feita pelo 'required' no HTML5
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
                <li><a href="index.php">Voltar para Home</a></li>
              </ul>
            </div></div>
        </nav>


        <div class="container">
            
            <br /><br />

            <div class="col-md-4"></div>
            <div class="col-md-4">
                <div class="panel panel-default registration-panel"> <div class="panel-body">
                        <h3 class="text-center registration-heading">Inscreva-se já.</h3>
                        <br />
                        <form method="post" action="registra_usuario.php" id="formCadastrarse">
                            <div class="form-group">
                                <input type="text" class="form-control" id="usuario" name="usuario" placeholder="Usuário" required="required">
                                <?php 
                                    if($erro_usuario){
                                        echo '<div class="alert alert-danger registration-error">Usuário já existe.</div>';
                                    }
                                ?>
                            </div>

                            <div class="form-group">
                                <input type="email" class="form-control" id="email" name="email" placeholder="Email" required="required">
                                <?php 
                                    if($erro_email){
                                        echo '<div class="alert alert-danger registration-error">E-mail já existe.</div>';
                                    }
                                ?>
                            </div>
                            
                            <div class="form-group">
                                <input type="password" class="form-control" id="senha" name="senha" placeholder="Senha" required="required">
                            </div>
                            
                            <button type="submit" class="btn btn-primary btn-block">Inscreva-se</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-4"></div>

            <div class="clearfix"></div>
            <br />
            <div class="col-md-4"></div>
            <div class="col-md-4"></div>
            <div class="col-md-4"></div>

        </div>
    
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
    
    </body>
</html>