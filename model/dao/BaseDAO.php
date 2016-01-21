<?php

interface BaseDAO {

    public function create(PDO $conexao, $entity);

    public function readById(PDO $conexao, $id);

    public function readByCriteria(PDO $conexao, $criteria = NULL, $offset = -1, $limit = -1);

    public function update(PDO $conexao, $entity);

    public function delete(PDO $conexao, $id);
}
