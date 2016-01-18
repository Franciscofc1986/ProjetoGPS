<?php

class ConnectionManager {

    private static $ip = "127.0.0.1";
    private static $porta = 3306;
    private static $banco = "test";
    private static $usuario = "root";
    private static $senha = "";

    public static function getConexao() {
        $conexao = null;
        try {
            $conexao = new mysqli(self::$ip, self::$usuario, self::$senha, self::$banco, self::$porta);
            if ($conexao->connect_error) {
                throw new Exception('Erro na Conexão (' . $conexao->connect_errno . ') '
                . $conexao->connect_error);
            }
            $conexao->autocommit(FALSE);
        } catch (Exception $ex) {
            throw $ex;
        }
        return $conexao;
    }

}
