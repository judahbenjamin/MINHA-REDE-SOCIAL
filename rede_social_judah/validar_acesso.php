<?php 

    session_start();

    require_once('conexao.php');
    //Efetuando autenticação de usuários
    $usuario = $_POST['usuario'];
    $senha = md5($_POST['senha']);

    $sql = "SELECT id, usuario, email FROM usuarios WHERE usuario = '$usuario' AND senha = '$senha'";

    $objDb = new conexao();
    $link = $objDb->conecta_mysql();

    $resultado_id = mysqli_query($link, $sql);

    if($resultado_id){
        $dados_usuario = mysqli_fetch_array($resultado_id);

        if(isset($dados_usuario['usuario'])){

            $_SESSION['id_usuario'] = $dados_usuario['id'];
            $_SESSION['usuario'] = $dados_usuario['usuario'];
            $_SESSION['email'] = $dados_usuario['email'];
            
            header('Location: home.php');

        } else {
            header('Location: index.php?erro=1');
        }
    } else {
        echo 'Erro na execução a consulta, favor entrar em contato com o admin do site';
    }
    
    //select md5('123456)

    /*
        Função: mysqli_fetch_array()

        Criar a conexão com banco de dados ($link)
                        |
               Montar a query ($sql)
                        |
                mysqli_query ($link, $sql)

                mysqli_fetch_array($result)

        $result: Contém os registros da consulta

    
    */
?>
