<?php
/* Demonstration of upside-down printing */
require_once("vendor/autoload.php"); 

use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;

$connector = new FilePrintConnector("//localhost/POS-80");
$printer = new Printer($connector);

// Most simple example
$printer -> text("Hello\n");
$printer -> setUpsideDown(true);
$printer -> text("World\n");
$printer -> cut();
$printer -> close();

