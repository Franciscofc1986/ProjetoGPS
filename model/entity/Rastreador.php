<?php

include_once realpath(__DIR__) . '/../../model/entity/BaseEntity.php';
include_once realpath(__DIR__) . '/../../model/entity/Coordenada.php';
include_once realpath(__DIR__) . '/../../model/entity/Comparavel.php';

class Rastreador extends BaseEntity implements Comparavel {

    protected $serial;
    protected $token;
    protected $nome;
    protected $publico;
    protected $usuarioArray;
    protected $ultimaCoordenada;
    protected $coordenadaArray;

    public function __construct() {
        $this->usuarioArray = array();
        $this->coordenadaArray = array();
    }

    public function __toString() {
        return "$this->id# $this->serial# $this->token# $this->nome# $this->publico# $this->ultimaCoordenada";
    }

    public function getSerial() {
        return $this->serial;
    }

    public function getToken() {
        return $this->token;
    }

    public function getNome() {
        return $this->nome;
    }

    public function isPublico() {
        return $this->publico;
    }

    public function getUsuarioArray() {
        return $this->usuarioArray;
    }

    public function getUltimaCoordenada() {
        return $this->ultimaCoordenada;
    }

    public function getCoordenadaArray() {
        return $this->coordenadaArray;
    }

    public function setSerial($serial) {
        $this->serial = $serial;
    }

    public function setToken($token) {
        $this->token = $token;
    }

    public function setNome($nome) {
        $this->nome = $nome;
    }

    public function setPublico($publico) {
        $this->publico = $publico;
    }

    public function setUsuarioArray($usuarioArray) {
        $this->usuarioArray = $usuarioArray;
    }

    public function setUltimaCoordenada(Coordenada $ultimaCoordenada) {
        $this->ultimaCoordenada = $ultimaCoordenada;
    }

    public function setCoordenadaArray($coordenadaArray) {
        $this->coordenadaArray = $coordenadaArray;
    }

    public function comparar($objeto) {
        if (!is_a($objeto, __CLASS__) ||
                $this->id !== $objeto->getId() ||
                $this->serial !== $objeto->getSerial() ||
                $this->token !== $objeto->getToken() ||
                $this->nome !== $objeto->getNome() ||
                $this->publico !== $objeto->isPublico()) {
            return false;
        }
        return true;
    }

}
