<?php

class conexao {

    //host
    private $host = 'localhost';
    //usuario
    private $usuario = 'root';
    //senha
    private $senha = '';
    //banco de dados
    private $database = 'rede_social_judah';

    //Função que vai executar a conexão entre o PHP e MySQL
    public function conecta_mysql(){
        //criar a conexao
        //mysqli_connect(localizacao do bd, usuario de acesso, senha, banco de dados);
        //OBS: O this faz referência a propriedade existente dentro da própria classe
        $con = mysqli_connect($this->host, $this->usuario, $this->senha, $this->database);

        //ajustar o charset de comunicação entre a aplicação e o banco de dados
        mysqli_set_charset($con, 'utf8');

        //verificar se houve erro de conexão
        if(mysqli_connect_errno()){
            // Melhoria: Não exibir detalhes sensíveis em produção, mas útil para desenvolvimento
            echo 'Erro ao tentar se conectar com o BD MySQL: '.mysqli_connect_error();
            exit(); // Para o script se a conexão falhar
        }

        return $con; //retorna a variável de conexão
    }
}

?>