<?php

include_once realpath(__DIR__) . '/../../model/service/CoordenadaService.php';
include_once realpath(__DIR__) . '/../../model/service/RastreadorService.php';

class ServiceLocator {

    public static function getCoordenadaService() {
        return new CoordenadaService();
    }

    public static function getRastreadorService() {
        return new RastreadorService();
    }

    public static function getUsuarioService() {
        return new UsuarioService();
    }

}
