<?php

@include_once("model/entity/Rastreador");
@include_once("model/entity/Coordenada");
@include_once("model/service/CoordenadaService");
@include_once("model/service/RastreadorService");

class CoordenadaController
{

	public function salvarCoordenador($serialDispositivo, $latitude, $longitude)
	{
		$rastreadorService = new RastreadorService();
		$coordService = new CoordenadaService();
		$rastreador  = new Rastreador();
		$coordenada = new Coordenada();
		
		$rastreador = $rastreadorService->getBySerial($serialDispositivo);
	  
	   $coordenada->rastreador = $rastreador;
	  
	   $coordenada->latitude = $latitude;
	   
	   $coordenada->longitude = $longitude;
	   
	   $coordenada->data_hora = time();
	   
	   $result = $coordService->save($coordenada);
	 	
	}

}

?>