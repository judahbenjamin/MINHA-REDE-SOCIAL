<?php

session_start();

// Destrói todas as variáveis de sessão
session_unset();

// Destrói a sessão
session_destroy();

// Define a mensagem de saída
$mensagem_saida = 'Esperamos você de volta em breve!';

?>

<!DOCTYPE HTML>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">

        <title>Twitter clone</title>
        
        <script src="https://code.jquery.com/jquery-2.2.4.min.js"></script>

        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
        
        <link rel="stylesheet" href="css/estilo.css"> 

        <style>
            /* Estilos específicos para a página de saída */
            .logout-container {
                margin-top: 100px;
                text-align: center;
            }
            .logout-panel {
                background-color: var(--white);
                border: 1px solid var(--border-color);
                border-radius: 8px;
                box-shadow: 0 2px 8px rgba(0,0,0,0.1);
                padding: 40px;
                max-width: 500px;
                margin: 0 auto; /* Centraliza o painel */
            }
            .logout-panel h2 {
                color: var(--twitter-blue);
                font-size: 2.5em;
                font-weight: bold;
                margin-bottom: 20px;
            }
            .logout-panel p {
                color: var(--text-primary);
                font-size: 1.2em;
                margin-bottom: 30px;
            }
            .btn-return-home {
                background-color: var(--twitter-blue);
                border-color: var(--twitter-blue);
                color: var(--white);
                font-weight: bold;
                padding: 12px 30px;
                border-radius: 30px;
                font-size: 1.1em;
                transition: background-color 0.2s ease-in-out, border-color 0.2s ease-in-out;
            }
            .btn-return-home:hover {
                background-color: var(--twitter-dark-blue);
                border-color: var(--twitter-dark-blue);
                color: var(--white); /* Garante que a cor do texto não mude no hover */
            }
        </style>
        
    </head>

    <body>

        <nav class="navbar navbar-default navbar-static-top">
          <div class="container">
            <div class="navbar-header">
              <img src="imagens/icone_twitter.png" alt="Logo Twitter" />
            </div>
            
            <div id="navbar" class="navbar-collapse collapse">
              <ul class="nav navbar-nav navbar-right">
                <li><a href="index.php">Entrar</a></li> </ul>
            </div></div>
        </nav>

        <div class="container logout-container">
            <div class="logout-panel">
                <h2>Logout Efetuado</h2>
                <p><?= $mensagem_saida ?></p>
                <a href="index.php" class="btn btn-return-home">Voltar para a página inicial</a>
            </div>
        </div>
    
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
    
    </body>
</html>