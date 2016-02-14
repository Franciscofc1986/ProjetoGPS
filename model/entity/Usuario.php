<?php

include_once realpath(__DIR__) . '/../../model/entity/BaseEntity.php';
include_once realpath(__DIR__) . '/../../model/entity/Comparavel.php';

class Usuario extends BaseEntity implements Comparavel {

    protected $login;
    protected $senha;
    protected $nome;
    protected $rastreadorArray;

    public function __construct() {
        $this->rastreadorArray = array();
    }

    public function __toString() {
        return "$this->id# $this->login# $this->senha# $this->nome";
    }

    public function getLogin() {
        return $this->login;
    }

    public function getSenha() {
        return $this->senha;
    }

    public function getNome() {
        return $this->nome;
    }

    public function getRastreadorArray() {
        return $this->rastreadorArray;
    }

    public function setLogin($login) {
        $this->login = $login;
    }

    public function setSenha($senha) {
        $this->senha = $senha;
    }

    public function setNome($nome) {
        $this->nome = $nome;
    }

    public function setRastreadorArray($rastreadorArray) {
        $this->rastreadorArray = $rastreadorArray;
    }

    public function comparar($objeto) {
        if (!is_a($objeto, __CLASS__) ||
                $this->id !== $objeto->getId() ||
                $this->login !== $objeto->getLogin() ||
                $this->senha !== $objeto->getSenha() ||
                $this->nome !== $objeto->getNome()) {
            return false;
        }
        return true;
    }

}
