<?php

include_once realpath(__DIR__) . '/../../model/entity/Coordenada.php';
include_once realpath(__DIR__) . '/../../model/dao/criteria/CoordenadaCriteria.php';

class CoordenadaDAO {

    public function create(PDO $conexao, Coordenada $entity) {
        $resultado = false;
        if ($conexao != null && $entity != null) {
            try {
                $i = 0;
                $sql = "insert into coordenada (latitude, longitude, data_hora, rastreador_fk) values (?, ?, ?, ?)";
                $ps = $conexao->prepare($sql);
                $ps->bindParam(++$i, $entity->getLatitude(), PDO::PARAM_STR);
                $ps->bindParam(++$i, $entity->getLongitude(), PDO::PARAM_STR);
                $ps->bindParam(++$i, $entity->getDataHora(), PDO::PARAM_STR);
                $rastreadorFk = ($entity->getRastreador() != null) ? $entity->getRastreador()->getId() : NULL;
                $ps->bindParam(++$i, $rastreadorFk, PDO::PARAM_INT);
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
                $sql = "delete from coordenada where id = ?";
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

            $sql = "select * from coordenada where 1=1";

            if (is_array($criteria) && count($criteria) > 0) {
                if (array_key_exists(CoordenadaCriteria::ID_EQ, $criteria)) {
                    $aux = $criteria[CoordenadaCriteria::ID_EQ];
                    if ($aux != null && $aux > 0) {
                        $sql .= " and id = $aux";
                    }
                }
                if (array_key_exists(CoordenadaCriteria::DATA_HORA_LK, $criteria)) {
                    $aux = $criteria[CoordenadaCriteria::DATA_HORA_LK];
                    if ($aux != null && strlen($aux) > 0) {
                        $sql .= " and lower(data_hora) like lower('%$aux%')";
                    }
                }
                if (array_key_exists(CoordenadaCriteria::RASTREADOR_FK_EQ, $criteria)) {
                    $aux = $criteria[CoordenadaCriteria::RASTREADOR_FK_EQ];
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
                    $entity = new Coordenada();
                    $entity->setId($linha['id']);
                    $entity->setLatitude($linha['latitude']);
                    $entity->setLongitude($linha['longitude']);
                    $entity->setDataHora($linha['data_hora']);
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
                $sql = "select * from coordenada where id = ?";
                $ps = $conexao->prepare($sql);
                $ps->bindParam(1, $id, PDO::PARAM_INT);
                $ps->execute();
                if ($linha = $ps->fetch(PDO::FETCH_ASSOC)) {
                    $entity = new Coordenada();
                    $entity->setId($linha['id']);
                    $entity->setLatitude($linha['latitude']);
                    $entity->setLongitude($linha['longitude']);
                    $entity->setDataHora($linha['data_hora']);
                    $rastreador = new Rastreador();
                    $rastreador->setId($linha['rastreador_fk']);
                    $entity->setRastreador($rastreador);
                }
                $ps = null;
            } catch (PDOException $e) {
                throw $e;
            }
        }
        return $entity;
    }

    public function update(PDO $conexao, Coordenada $entity) {
        $resultado = false;
        if ($conexao != null && $entity != null) {
            try {
                $i = 0;
                $sql = "update coordenada set latitude = ?, longitude = ?, data_hora = ?, rastreador_fk = ? where id = ?";
                $ps = $conexao->prepare($sql);
                $ps->bindParam(++$i, $entity->getLatitude(), PDO::PARAM_STR);
                $ps->bindParam(++$i, $entity->getLongitude(), PDO::PARAM_STR);
                $ps->bindParam(++$i, $entity->getDataHora(), PDO::PARAM_STR);
                $rastreadorFk = ($entity->getRastreador() != null) ? $entity->getRastreador()->getId() : NULL;
                $ps->bindParam(++$i, $rastreadorFk, PDO::PARAM_INT);
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
