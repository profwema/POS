<?php
    include_once('class/tcpdf/tcpdf.php');
    include_once("class/PHPJasperXML.inc.php");
    include_once ('setting.php');


    $xml =  simplexml_load_file("barcode.jrxml");

    $PHPJasperXML = new PHPJasperXML();
    //$PHPJasperXML->debugsql=true;
    $PHPJasperXML->arrayParameter=array(""=>'');
    $PHPJasperXML->xml_dismantle($xml);
    $PHPJasperXML->transferDBtoArray($server,$user,$pass,$db);
    $PHPJasperXML->outpage("I");    //page output method I:standard output  D:Download file   

?>


