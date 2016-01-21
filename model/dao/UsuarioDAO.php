<?php

include_once realpath(__DIR__) . '/../../model/entity/Usuario.php';
include_once realpath(__DIR__) . '/../../model/dao/criteria/UsuarioCriteria.php';

class UsuarioDAO {

    public function create(PDO $conexao, Usuario $entity) {
        $resultado = false;
        if ($conexao != null && $entity != null) {
            try {
                $i = 0;
                $sql = "insert into usuario (login, senha, nome, data_hora) values (?, ?, ?, ?)";
                $ps = $conexao->prepare($sql);
                $ps->bindParam(++$i, $entity->getLogin(), PDO::PARAM_STR);
                $ps->bindParam(++$i, $entity->getSenha(), PDO::PARAM_STR);
                $ps->bindParam(++$i, $entity->getNome(), PDO::PARAM_STR);
                $ps->bindParam(++$i, $entity->getDataHora(), PDO::PARAM_STR);
                $resultado = $ps->execute();
                $entity->setId($conexao->lastInsertId());
                $ps = null;
            } catch (PDOException $e) {
                throw $e;
            }
        }
        return $resultado;
    }

    public function delete(PDO $conexao, $id) {
        $resultado = false;
        if ($conexao != null && $id > 0) {
            try {
                $sql = "delete from usuario where id = ?";
                $ps = $conexao->prepare($sql);
                $ps->bindParam(1, $id, PDO::PARAM_INT);
                $resultado = $ps->execute();
                $ps = null;
            } catch (PDOException $e) {
                throw $e;
            }
        }
        return $resultado;
    }

    public function readByCriteria(PDO $conexao, $criteria = NULL, $offset = -1, $limit = -1) {
        $entityArray = array();
        if ($conexao != null) {

            $sql = "select * from usuario where 1=1";

            if (is_array($criteria) && count($criteria) > 0) {
                if (array_key_exists(UsuarioCriteria::ID_EQ, $criteria)) {
                    $aux = $criteria[UsuarioCriteria::ID_EQ];
                    if ($aux != null && $aux > 0) {
                        $sql .= " and id = $aux";
                    }
                }
                if (array_key_exists(UsuarioCriteria::LOGIN_EQ, $criteria)) {
                    $aux = $criteria[UsuarioCriteria::LOGIN_EQ];
                    if ($aux != null && strlen($aux) > 0) {
                        $sql .= " and login = '$aux' COLLATE latin1_bin";
                    }
                }
                if (array_key_exists(UsuarioCriteria::SENHA_EQ, $criteria)) {
                    $aux = $criteria[UsuarioCriteria::SENHA_EQ];
                    if ($aux != null && strlen($aux) > 0) {
                        $sql .= " and senha = '$aux' COLLATE latin1_bin";
                    }
                }
                if (array_key_exists(UsuarioCriteria::NOME_LK, $criteria)) {
                    $aux = $criteria[UsuarioCriteria::NOME_LK];
                    if ($aux != null && strlen($aux) > 0) {
                        $sql .= " and lower(nome) like lower('%$aux%')";
                    }
                }
                if (array_key_exists(UsuarioCriteria::DATA_HORA_LK, $criteria)) {
                    $aux = $criteria[UsuarioCriteria::DATA_HORA_LK];
                    if ($aux != null && strlen($aux) > 0) {
                        $sql .= " and lower(data_hora) like lower('%$aux%')";
                    }
                }
            }

            if ($limit > 0) {
                $sql .= " limit $limit";
            }
            if ($offset > 0) {
                $sql .= " offset $offset";
            }

            try {
                $ps = $conexao->prepare($sql);
                $ps->execute();
                while ($linha = $ps->fetch(PDO::FETCH_ASSOC)) {
                    $entity = new Usuario();
                    $entity->setId($linha['id']);
                    $entity->setLogin($linha['login']);
                    $entity->setSenha($linha['senha']);
                    $entity->setNome($linha['nome']);
                    $entity->setDataHora($linha['data_hora']);
                    $entityArray[] = $entity;
                }
                $ps = null;
            } catch (PDOException $e) {
                throw $e;
            }
        }
        return $entityArray;
    }

    public function readById(PDO $conexao, $id) {
        $entity = null;
        if ($conexao != null && $id > 0) {
            try {
                $sql = "select * from usuario where id = ?";
                $ps = $conexao->prepare($sql);
                $ps->bindParam(1, $id, PDO::PARAM_INT);
                $ps->execute();
                if ($linha = $ps->fetch(PDO::FETCH_ASSOC)) {
                    $entity = new Usuario();
                    $entity->setId($linha['id']);
                    $entity->setLogin($linha['login']);
                    $entity->setSenha($linha['senha']);
                    $entity->setNome($linha['nome']);
                    $entity->setDataHora($linha['data_hora']);
                }
                $ps = null;
            } catch (PDOException $e) {
                throw $e;
            }
        }
        return $entity;
    }

    public function update(PDO $conexao, Usuario $entity) {
        $resultado = false;
        if ($conexao != null && $entity != null) {
            try {
                $i = 0;
                $sql = "update usuario set login = ?, senha = ?, nome = ?, data_hora = ? where id = ?";
                $ps = $conexao->prepare($sql);
                $ps->bindParam(++$i, $entity->getLogin(), PDO::PARAM_STR);
                $ps->bindParam(++$i, $entity->getSenha(), PDO::PARAM_STR);
                $ps->bindParam(++$i, $entity->getNome(), PDO::PARAM_STR);
                $ps->bindParam(++$i, $entity->getDataHora(), PDO::PARAM_STR);
                $ps->bindParam(++$i, $entity->getId(), PDO::PARAM_INT);
                $resultado = $ps->execute();
                $ps = null;
            } catch (PDOException $e) {
                throw $e;
            }
        }
        return $resultado;
    }

}
