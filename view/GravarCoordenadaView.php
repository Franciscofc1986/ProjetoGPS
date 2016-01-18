<?php

@include_once("controller/CoordenadaController.php");

class GravarCoordenadaView{

	public function salvarCoordenada($serialDispositivo, $latitude, $longitude)
	{
       
		$coordenada = new CoordenadaController();
		
		$result = $coordenada->salvarCoordenador($serialDispositivo, $latitude, $longitude);
		
	}

}

?>