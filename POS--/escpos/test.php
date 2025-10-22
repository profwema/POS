<?php
require 'vendor/autoload.php';
use Mike42\Escpos\CapabilityProfile;
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\PrintBuffers\ImagePrintBuffer;



/*
 * Due to its complxity, escpos-php does not support HTML input. To print HTML,
 * either convert it to calls on the Printer() object, or rasterise the page with
 * wkhtmltopdf, an external package which is designed to handle HTML efficiently.
 *
 * This example is provided to get you started: On Debian, first run-
 * 
 * sudo apt-get install wkhtmltopdf xvfb
 *
 * Note: Depending on the height of your pages, it is suggested that you chop it
 * into smaller sections, as printers simply don't have the buffer capacity for
 * very large images.
 *
 * As always, you can trade off quality for capacity by halving the width
 * (550 -> 225 below) and printing w/ Escpos::IMG_DOUBLE_WIDTH | Escpos::IMG_DOUBLE_HEIGHT
 */
$connector = new FilePrintConnector("//localhost/POS-80");
$profile = CapabilityProfile::load("TEP-200M");
//$profile = DefaultCapabilityProfile::getInstance();
// Works for Epson printers
$printer = new Printer($connector, $profile);
$cmd = Escpos::ESC . "V" . chr(1);
// Try out 90-degree rotation.
$printer->getPrintConnector()->write($cmd);
$printer->text("Beispieltext in Deutsch\n");
$printer->cut();
$printer->close();