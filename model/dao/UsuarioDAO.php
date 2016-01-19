<?php

include_once realpath(__DIR__) . '/../../model/entity/Usuario.php';
include_once realpath(__DIR__) . '/../../model/dao/criteria/UsuarioCriteria.php';

class UsuarioDAO {

    public function create(mysqli $conexao, Usuario $entity) {
        $resultado = false;
        if ($conexao != null && $entity != null) {
            $sql = "insert into usuario (login, senha, nome, data_hora) values (?, ?, ?, ?)";
            $stmt = $conexao->stmt_init();
            if ($stmt->prepare($sql)) {
                $stmt->bind_param("ssss", $entity->getLogin(), $entity->getSenha(), $entity->getNome(), $entity->getDataHora());
                $resultado = $stmt->execute();
                $entity->setId($stmt->insert_id);
            }
            $stmt->close();
        }
        return $resultado;
    }

    public function delete(mysqli $conexao, $id) {
        $resultado = false;
        if ($conexao != null && $id > 0) {
            $sql = "delete from usuario where id = ?";
            $stmt = $conexao->stmt_init();
            if ($stmt->prepare($sql)) {
                $stmt->bind_param("i", $id);
                $resultado = $stmt->execute();
            }
            $stmt->close();
        }
        return $resultado;
    }

    public function readByCriteria(mysqli $conexao, $criteria = NULL, $offset = -1, $limit = -1) {
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

            $stmt = $conexao->stmt_init();
            if ($stmt->prepare($sql)) {
                $stmt->execute();
                $result = $stmt->get_result();
                while ($linha = $result->fetch_array(MYSQLI_ASSOC)) {
                    $entity = new Usuario();
                    $entity->setId($linha['id']);
                    $entity->setLogin($linha['login']);
                    $entity->setSenha($linha['senha']);
                    $entity->setNome($linha['nome']);
                    $entity->setDataHora($linha['data_hora']);
                    $entityArray[] = $entity;
                }
            }
            $stmt->close();
        }
        return $entityArray;
    }

    public function readById(mysqli $conexao, $id) {
        $entity = null;
        if ($conexao != null && $id > 0) {
            $sql = "select * from usuario where id = ?";
            $stmt = $conexao->stmt_init();
            if ($stmt->prepare($sql)) {
                $stmt->bind_param("i", $id);
                $stmt->execute();

                $result = $stmt->get_result();
                if ($linha = $result->fetch_array(MYSQLI_ASSOC)) {

                    $entity = new Usuario();
                    $entity->setId($linha['id']);
                    $entity->setLogin($linha['login']);
                    $entity->setSenha($linha['senha']);
                    $entity->setNome($linha['nome']);
                    $entity->setDataHora($linha['data_hora']);
                }
            }
            $stmt->close();
        }
        return $entity;
    }

    public function update(mysqli $conexao, Usuario $entity) {
        $resultado = false;
        if ($conexao != null && $entity != null) {
            $sql = "update usuario set login = ?, senha = ?, nome = ?, data_hora = ? where id = ?";
            $stmt = $conexao->stmt_init();
            if ($stmt->prepare($sql)) {
                $stmt->bind_param("ssssi", $entity->getLogin(), $entity->getSenha(), $entity->getNome(), $entity->getDataHora(), $entity->getId());
                $resultado = $stmt->execute();
            }
            $stmt->close();
        }
        return $resultado;
    }

}
