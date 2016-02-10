<?php

include_once realpath(__DIR__) . '/../servidorWS/ClienteWS.php';
include_once realpath(__DIR__) . '/../servidorWS/ClienteWSController.php';
include_once realpath(__DIR__) . '/../servidorWS/TipoCliente.php';
include_once realpath(__DIR__) . '/../servidorWS/TipoComunicacao.php';

$clienteWSController = new ClienteWSController();
$clienteWS = new ClienteWS();
$clienteWS->setTipoCliente(TipoCliente::USUARIO);
$clienteWS->setId(10);
$clienteWS->setTipoComunicacaoAtual(TipoComunicacao::CONFIG_RASTREADOR);

//$clienteWSController->adicionarCliente($clienteWS);
//echo $clienteWSController->buscarCliente(TipoCliente::USUARIO, 10) . '<br>';
//var_dump($clienteWSController->getSockets());
//foreach ($clienteWSController->getClienteArray() as $cliente) {
//    echo $cliente . '<br>';
//}
echo '<br>Qtd Clientes: ' . count($clienteWSController->getClienteArray()) . '<br>';
