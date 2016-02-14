<?php

include_once realpath(__DIR__) . '/../model/entity/Comparavel.php';

class ClienteWSBase implements Comparavel {

    protected $id;
    protected $socket;

    public function __toString() {
        return "$this->id# $this->socket";
    }

    public function getId() {
        return $this->id;
    }

    public function getSocket() {
        return $this->socket;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function setSocket($socket) {
        $this->socket = $socket;
    }

    public function comparar($objeto) {
        if (!is_a($objeto, __CLASS__) ||
                $this->id !== $objeto->getId() ||
                $this->socket != $objeto->getSocket()) {
            return false;
        }
        return true;
    }

}
