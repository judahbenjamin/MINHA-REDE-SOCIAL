<?php 

    require_once('conexao.php');

    $sql = "SELECT * FROM usuarios ";

    $objDb = new conexao();
    $link = $objDb->conecta_mysql();

    $resultado_id = mysqli_query($link, $sql);

    if($resultado_id){
        $dados_usuario = array();
        
        while($linha = mysqli_fetch_array($resultado_id, MYSQLI_ASSOC)){
            $dados_usuario[] = $linha;
        }

        var_dump($dados_usuario);

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
