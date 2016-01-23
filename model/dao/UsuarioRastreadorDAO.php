<?php

include_once realpath(__DIR__) . '/../../model/entity/UsuarioRastreador.php';
include_once realpath(__DIR__) . '/../../model/dao/criteria/UsuarioRastreadorCriteria.php';

class UsuarioRastreadorDAO {

    public function create(PDO $conexao, $entity) {
        $resultado = false;
        if ($conexao != null && is_a($entity, 'UsuarioRastreador')) {
            try {
                $i = 0;
                $sql = "insert into usuario_rastreador (usuario_fk, rastreador_fk) values (?, ?)";
                $ps = $conexao->prepare($sql);
                $usuarioFK = $entity->getUsuario() != null ? $entity->getUsuario()->getId() : null;
                $ps->bindParam( ++$i, $usuarioFK, PDO::PARAM_INT);
                $rastreadorFK = $entity->getRastreador() != null ? $entity->getRastreador()->getId() : null;
                $ps->bindParam( ++$i, $rastreadorFK, PDO::PARAM_INT);
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
                $sql = "delete from usuario_rastreador where id = ?";
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

    public function readByCriteria(PDO $conexao, $criteria = null, $offset = -1, $limit = -1) {
        $entityArray = array();
        if ($conexao != null) {

            $sql = "select * from usuario_rastreador where 1=1";

            if (is_array($criteria) && count($criteria) > 0) {
                if (array_key_exists(UsuarioRastreadorCriteria::ID_EQ, $criteria)) {
                    $aux = $criteria[UsuarioRastreadorCriteria::ID_EQ];
                    if ($aux != null && $aux > 0) {
                        $sql .= " and id = $aux";
                    }
                }
                if (array_key_exists(UsuarioRastreadorCriteria::USUARIO_FK_EQ, $criteria)) {
                    $aux = $criteria[UsuarioRastreadorCriteria::USUARIO_FK_EQ];
                    if ($aux != null && $aux > 0) {
                        $sql .= " and usuario_fk = $aux";
                    }
                }
                if (array_key_exists(UsuarioRastreadorCriteria::RASTREADOR_FK_EQ, $criteria)) {
                    $aux = $criteria[UsuarioRastreadorCriteria::RASTREADOR_FK_EQ];
                    if ($aux != null && $aux > 0) {
                        $sql .= " and rastreador_fk = $aux";
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
                    $entity = new UsuarioRastreador();
                    $entity->setId($linha['id']);
                    $usuario = new Usuario();
                    $usuario->setId($linha['usuario_fk']);
                    $entity->setUsuario($usuario);
                    $rastreador = new Rastreador();
                    $rastreador->setId($linha['rastreador_fk']);
                    $entity->setRastreador($rastreador);
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
                $sql = "select * from usuario_rastreador where id = ?";
                $ps = $conexao->prepare($sql);
                $ps->bindParam(1, $id, PDO::PARAM_INT);
                $ps->execute();
                if ($linha = $ps->fetch(PDO::FETCH_ASSOC)) {
                    $entity = new UsuarioRastreador();
                    $entity->setId($linha['id']);
                    $entity->setUsuario($linha['usuario_fk']);
                    $entity->setRastreador($linha['rastreador_fk']);
                }
                $ps = null;
            } catch (PDOException $e) {
                throw $e;
            }
        }
        return $entity;
    }

    public function update(PDO $conexao, $entity) {
        $resultado = false;
        if ($conexao != null && is_a($entity, 'UsuarioRastreador')) {
            try {
                $i = 0;
                $sql = "update usuario_rastreador set usuario_fk = ?, rastreador_fk = ? where id = ?";
                $ps = $conexao->prepare($sql);
                $usuarioFK = $entity->getUsuario() != null ? $entity->getUsuario()->getId() : null;
                $ps->bindParam( ++$i, $usuarioFK, PDO::PARAM_INT);
                $rastreadorFK = $entity->getRastreador() != null ? $entity->getRastreador()->getId() : null;
                $ps->bindParam( ++$i, $rastreadorFK, PDO::PARAM_INT);
                $ps->bindParam( ++$i, $entity->getId(), PDO::PARAM_INT);
                $resultado = $ps->execute();
                $ps = null;
            } catch (PDOException $e) {
                throw $e;
            }
        }
        return $resultado;
    }

}
