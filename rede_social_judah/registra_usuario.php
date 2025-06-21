<?php
//=== RECUPERANDO OS DADOS DO FORMULÁRIO ===
//Temos duas formas de recuperar os dados enviados através de um formulário: Super global POST ou GET
//$_POST[] ---> Arrays Associativos
//$_GET[]

//Via POST - Os dados enviados não ficam visíveis na URL.
//Via GET - Os dados enviados ficam expostos na URL.

    require_once('conexao.php');

    $usuario = $_POST['usuario'];
    $email = $_POST['email'];
    $senha = md5($_POST['senha']); //Criptografar senha

    $objDb = new conexao();
    $link = $objDb->conecta_mysql();

    $usuario_existe = false;
    $email_existe = false;

    //Verificar se o usuário já existe
    $sql = "SELECT * FROM usuarios WHERE usuario = '$usuario'";
    if($resultado_id = mysqli_query($link, $sql)){
        $dados_usuario = mysqli_fetch_array($resultado_id);
        if(isset($dados_usuario['usuario'])){
            $usuario_existe = true;
        } else {
            echo 'Usuário não cadastrado, ok, pode cadastrar';
        }
    } else {
        echo 'Erro ao tentar localizar o registro de usuário';
    }
 
    //Verificar se o e-mail já existe
     $sql = "SELECT * FROM usuarios WHERE email = '$email'";
    if($resultado_id = mysqli_query($link, $sql)){
        $dados_usuario = mysqli_fetch_array($resultado_id);
        if(isset($dados_usuario['email'])){
            $email_existe = true;
        } else {
            echo 'E-mail não cadastrado, ok, pode cadastrar';
        }
    } else {
        echo 'Erro ao tentar localizar o registro de email';
    }
    
    if($usuario_existe || $email_existe){

        $retorno_get = '';

        if($usuario_existe){
            $retorno_get.= "erro_usuario=1&"; //O E comercial delimita o valor que deve ser atribuído a variável.
        }

        if($email_existe){
            $retorno_get.= "erro_email=1&";
        }

        header('Location: inscrevase.php?'.$retorno_get);
        die(); //Interrompe a execução do script
    }


    $sql = "INSERT INTO usuarios (usuario, email, senha) VALUES ('$usuario','$email','$senha')";

    //executar a query
    if(mysqli_query($link, $sql)){
        echo 'Usuário registrado com sucesso!';
    } else {
        echo 'Erro ao registrar o usuário!';
    }
?>