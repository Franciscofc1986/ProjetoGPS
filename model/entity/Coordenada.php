<?php

include_once realpath(__DIR__) . '/../../model/entity/BaseEntity.php';
include_once realpath(__DIR__) . '/../../model/entity/Rastreador.php';

class Coordenada extends BaseEntity {

    protected $latitude;
    protected $longitude;
    protected $hdop;
    protected $dataHora;
    protected $rastreador;

    public function __toString() {
        $rastreadorFk = ($this->rastreador != null) ? $this->rastreador->getId() : NULL;
        return "$this->id# $this->latitude# $this->longitude# $this->dataHora# $rastreadorFk";
    }

    public function getLatitude() {
        return $this->latitude;
    }

    public function getLongitude() {
        return $this->longitude;
    }

    public function getHdop() {
        return $this->hdop;
    }

    public function getDataHora() {
        return $this->dataHora;
    }

    public function getRastreador() {
        return $this->rastreador;
    }

    public function setLatitude($latitude) {
        $this->latitude = $latitude;
    }

    public function setLongitude($longitude) {
        $this->longitude = $longitude;
    }

    public function setHdop($hdop) {
        $this->hdop = $hdop;
    }

    public function setDataHora($dataHora) {
        $this->dataHora = $dataHora;
    }

    public function setRastreador(Rastreador $rastreador) {
        $this->rastreador = $rastreador;
    }

}
