<?php

class ConnectionManager {

    private static $drive = "mysql";
    private static $ip = "127.0.0.1";
    private static $porta = 3306;
    private static $banco = "test";
    private static $usuario = "root";
    private static $senha = "";

    public static function getConexao() {
        $conexao = null;
        try {
            $dsn = self::$drive . ':host=' . self::$ip .
                    ';port=' . self::$porta . ';dbname=' . self::$banco;
            $conexao = new PDO($dsn, self::$usuario, self::$senha);
            $conexao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $conexao->setAttribute(PDO::ATTR_AUTOCOMMIT, false);
            $conexao->setAttribute(PDO::ATTR_TIMEOUT, 20);
        } catch (PDOException $e) {
            echo 'Falha na Conexão: ' . $e->getMessage();
        }
        return $conexao;
    }

}
