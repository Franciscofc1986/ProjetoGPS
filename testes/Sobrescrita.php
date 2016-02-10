<?php

class A {

    public function __construct() {
        $this->metodo1();
    }

    protected function metodo1() {
        $this->metodo2('opa');
    }

    protected function metodo2($texto) {
        echo __CLASS__ . " $texto";
    }

}

class B extends A {

    protected function metodo2($texto) {
        echo __CLASS__ . " $texto";
    }

}

$a = new A();
echo '<br>-------------<br>';
$b = new B();
