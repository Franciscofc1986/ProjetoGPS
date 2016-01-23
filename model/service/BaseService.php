<?php

interface BaseService {

    public function create($entity);

    public function readById($id);

    public function readByCriteria($criteria = null, $offset = -1, $limit = -1);

    public function update($entity);

    public function delete($id);
}
