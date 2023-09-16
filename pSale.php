<?php 

////function printSales($invoice,$time)
////{
////    echo ($invoice);
////    
//    
////

require_once("vendor/autoload.php"); 

/* Start to develop here. Best regards https://php-download.com/ */

use Mike42\Escpos\Printer;

use Mike42\Escpos\PrintConnectors\FilePrintConnector;

/* Open the printer; this will change depending on how it is connected */
$connector = new FilePrintConnector("//localhost/POS-80");
$printer = new Printer($connector);
 echo "<script>alert('kbkjb')</script>";


/* Initialize */
$printer -> initialize();

/* Text */
$printer -> text('$invoice');
//$printer -> cut();
//
///* Line feeds */
//$printer -> text("ABC");
//$printer -> feed(7);
//$printer -> text("DEF");
//$printer -> feedReverse(3);
//$printer -> text("GHI");
//$printer -> feed();
//$printer -> cut();

$printer -> pulse();

/* Always close the printer! On some PrintConnectors, no actual
 * data is sent until the printer is closed. */
$printer -> close();


