<?php

include_once realpath(__DIR__) . '/../model/entity/Rastreador.php';

$a = new Rastreador();
$a->setId(20);
$a->setNome("Teste");
$a->setPublico(1);
$a->setSerial("ghghg8989");
$a->setToken("TKojf94");
$a->setCoordenadaArray(["ola" => 123, "test" => 222]);

$b = new Rastreador();
$b->setId(20);
$b->setNome("Teste");
$b->setPublico(1);
$b->setSerial("ghghg8989");
$b->setToken("TKojf94");

echo ($a->__toString() == $b->__toString());
