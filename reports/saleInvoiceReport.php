<?php

//$idval 		= $adController->encrypt_decrypt(2,$idval,0);
//$idval2		= $_REQUEST['sd'];

include_once('class/tcpdf/tcpdf.php');
include_once("class/PHPJasperXML.inc.php");
include_once ('setting.php');


//display errors should be off in the php.ini file

$xml =  simplexml_load_file("saleInvoiceReport.jrxml");

$PHPJasperXML = new PHPJasperXML();
//$PHPJasperXML->debugsql=true;
//$PHPJasperXML->arrayParameter=array("store"=>$idval2);
$PHPJasperXML->xml_dismantle($xml);
$PHPJasperXML->transferDBtoArray($server,$user,$pass,$db);
$PHPJasperXML->outpage("I");    //page output method I:standard output  D:Download file



?>
      <script type="text/javascript">
          history.go(-1);
      </script>

