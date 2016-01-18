<?php

include_once realpath(__DIR__) . '/../../model/entity/BaseEntity.php';

class Usuario extends BaseEntity {

    protected $login;
    protected $senha;
    protected $nome;
    protected $dataHora;
    protected $rastreadorArray;

    public function getLogin() {
        return $this->login;
    }

    public function getSenha() {
        return $this->senha;
    }

    public function getNome() {
        return $this->nome;
    }

    public function getDataHora() {
        return $this->dataHora;
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

    public function setDataHora($dataHora) {
        $this->dataHora = $dataHora;
    }

    public function setRastreadorArray($registradorArray) {
        $this->rastreadorArray = $registradorArray;
    }

}
