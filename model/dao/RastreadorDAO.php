<?php

include_once realpath(__DIR__) . '/../../model/entity/Rastreador.php';
include_once realpath(__DIR__) . '/../../model/dao/criteria/RastreadorCriteria.php';

class RastreadorDAO {

    public function create(PDO $conexao, $entity) {
        $resultado = false;
        if ($conexao != null && is_a($entity, 'Rastreador')) {
            try {
                $i = 0;
                $sql = "insert into rastreador (serial, nome, data_hora, ultima_coordenada_fk) values (?, ?, ?, ?)";
                $ps = $conexao->prepare($sql);
                $ps->bindParam( ++$i, $entity->getSerial(), PDO::PARAM_STR);
                $ps->bindParam( ++$i, $entity->getNome(), PDO::PARAM_STR);
                $ps->bindParam( ++$i, $entity->getDataHora(), PDO::PARAM_STR);
                $ultimaCoordenadaFk = ($entity->getUltimaCoordenada() != null) ? $entity->getUltimaCoordenada()->getId() : NULL;
                $ps->bindParam( ++$i, $ultimaCoordenadaFk, PDO::PARAM_INT);
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
                $sql = "delete from rastreador where id = ?";
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
        $rastreadorArray = array();
        if ($conexao != null) {

            $sql = "select r.id as 'id_r', r.serial, r.nome, r.data_hora as 'data_hora_r', "
                    . "c.id as 'id_c', c.latitude, c.longitude, c.data_hora as 'data_hora_c' "
                    . "from (select * from rastreador where 1=1";

            if (is_array($criteria) && count($criteria) > 0) {
                if (array_key_exists(RastreadorCriteria::ID_EQ, $criteria)) {
                    $aux = $criteria[RastreadorCriteria::ID_EQ];
                    if ($aux != null && $aux > 0) {
                        $sql .= " and id = $aux";
                    }
                }
                if (array_key_exists(RastreadorCriteria::SERIAL_EQ, $criteria)) {
                    $aux = $criteria[RastreadorCriteria::SERIAL_EQ];
                    if ($aux != null && strlen($aux) > 0) {
                        $sql .= " and serial = '$aux' COLLATE latin1_bin";
                    }
                }
                if (array_key_exists(RastreadorCriteria::NOME_EQ, $criteria)) {
                    $aux = $criteria[RastreadorCriteria::NOME_EQ];
                    if ($aux != null && strlen($aux) > 0) {
                        $sql .= " and nome = '$aux' COLLATE latin1_bin";
                    }
                }
                if (array_key_exists(RastreadorCriteria::NOME_LK, $criteria)) {
                    $aux = $criteria[RastreadorCriteria::NOME_LK];
                    if ($aux != null && strlen($aux) > 0) {
                        $sql .= " and lower(nome) like lower('%$aux%')";
                    }
                }
                if (array_key_exists(RastreadorCriteria::DATA_HORA_LK, $criteria)) {
                    $aux = $criteria[RastreadorCriteria::DATA_HORA_LK];
                    if ($aux != null && strlen($aux) > 0) {
                        $sql .= " and lower(data_hora) like lower('%$aux%')";
                    }
                }
            }
            $sql .= ") as r left join (coordenada c) on (r.ultima_coordenada_fk = c.id)";

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
                    $rastreador = new Rastreador();
                    $rastreador->setId($linha['id_r']);
                    $rastreador->setSerial($linha['serial']);
                    $rastreador->setNome($linha['nome']);
                    $rastreador->setDataHora($linha['data_hora_r']);
                    $ultimaCoordenada = new Coordenada();
                    $ultimaCoordenada->setId($linha['id_c']);
                    $ultimaCoordenada->setLatitude($linha['latitude']);
                    $ultimaCoordenada->setLongitude($linha['longitude']);
                    $ultimaCoordenada->setDataHora($linha['data_hora_c']);
                    $ultimaCoordenada->setRastreador($rastreador);
                    $rastreador->setUltimaCoordenada($ultimaCoordenada);
                    $rastreadorArray[] = $rastreador;
                }
                $ps = null;
            } catch (PDOException $e) {
                throw $e;
            }
        }
        return $rastreadorArray;
    }

    public function readById(PDO $conexao, $id) {
        $rastreador = null;
        if ($conexao != null && $id > 0) {
            try {
                $sql = "select r.id as 'id_r', r.serial, r.nome, r.data_hora as 'data_hora_r', "
                        . "c.id as 'id_c', c.latitude, c.longitude, c.data_hora as 'data_hora_c' "
                        . "from (select * from rastreador where id = ?) as r "
                        . "left join (coordenada c) on (r.ultima_coordenada_fk = c.id)";
                $ps = $conexao->prepare($sql);
                $ps->bindParam(1, $id, PDO::PARAM_INT);
                $ps->execute();
                if ($linha = $ps->fetch(PDO::FETCH_ASSOC)) {
                    $rastreador = new Rastreador();
                    $rastreador->setId($linha['id_r']);
                    $rastreador->setSerial($linha['serial']);
                    $rastreador->setNome($linha['nome']);
                    $rastreador->setDataHora($linha['data_hora_r']);
                    $ultimaCoordenada = new Coordenada();
                    $ultimaCoordenada->setId($linha['id_c']);
                    $ultimaCoordenada->setLatitude($linha['latitude']);
                    $ultimaCoordenada->setLongitude($linha['longitude']);
                    $ultimaCoordenada->setDataHora($linha['data_hora_c']);
                    $ultimaCoordenada->setRastreador($rastreador);
                    $rastreador->setUltimaCoordenada($ultimaCoordenada);
                }
                $ps = null;
            } catch (PDOException $e) {
                throw $e;
            }
        }
        return $rastreador;
    }

    public function update(PDO $conexao, $entity) {
        $resultado = false;
        if ($conexao != null && is_a($entity, 'Rastreador')) {
            try {
                $i = 0;
                $sql = "update rastreador set serial = ?, nome = ?, data_hora = ?, ultima_coordenada_fk = ? where id = ?";
                $ps = $conexao->prepare($sql);
                $ps->bindParam( ++$i, $entity->getSerial(), PDO::PARAM_STR);
                $ps->bindParam( ++$i, $entity->getNome(), PDO::PARAM_STR);
                $ps->bindParam( ++$i, $entity->getDataHora(), PDO::PARAM_STR);
                $ultimaCoordenadaFk = ($entity->getUltimaCoordenada() != null) ? $entity->getUltimaCoordenada()->getId() : NULL;
                $ps->bindParam( ++$i, $ultimaCoordenadaFk, PDO::PARAM_INT);
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
