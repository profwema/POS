<?php 
require_once("vendor/autoload.php"); 
//require_once("../top.php");

/* Start to develop here. Best regards https://php-download.com/ */

use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\PrintBuffers\ImagePrintBuffer;

$connector = new FilePrintConnector("//localhost/POS-80");
$printer = new Printer($connector);


require_once("escpos/ar/I18N/Arabic.php");
mb_internal_encoding("UTF-8");
$Arabic = new I18N_Arabic('Glyphs');
$fontPath = "escpos/ar/I18N/Arabic/Examples/GD/no.otf";
$fontSize = 24;


$buffer = new ImagePrintBuffer();
$buffer  -> setFontSize($fontSize);
$buffer  -> setFont($fontPath);
$printer -> setPrintBuffer($buffer);


$lang    = $_SESSION['lang'];
$printer -> setJustification(Printer::JUSTIFY_CENTER);
$dataCat = $this->getSettings();

$title   = $dataCat['name_'.$lang];
$title   = getArabic($title,$Arabic);
$printer -> text($title.'\n');

$address  = $dataCat['address'];
$address   = getArabic($address,$Arabic);
$printer -> text($address);

$printer -> feed();


$printer -> setJustification(Printer::JUSTIFY_LEFT);
$date     = date('m/d/Y h:i:s a', time());
$date     = new topitem(getArabic(' : التاريخ',$Arabic), $date);
$printer -> text($date . "\n");

$custName = $this->getCustomerName($_POST['customer']);  
$custName  = getArabic($custName,$Arabic);
$custName  = new topitem(getArabic(' : عميل',$Arabic), $custName);
$printer -> text($custName . "\n");

$cashier  = getArabic($_SESSION['name'],$Arabic);
$cashier  = new topitem(getArabic(' : كاشير',$Arabic), $cashier);
$printer -> text($cashier . "\n");

$printer -> text(line());

$head   = new detalsitem(
        getArabic('الصنف',$Arabic),
        getArabic( 'الوحدة',$Arabic),
        getArabic('سعر',$Arabic),
        getArabic('كمية',$Arabic),
        getArabic('اجمالى',$Arabic),
        $chars_in_line);
$printer -> text($head);
$printer -> text(line());
//$printer -> text('----------------------------------------' . "\n");

$itemsArray         =   $_POST['barcode'];
$gross_total        =   flout_format($_POST['gross_total']);
$vat_all            =   flout_format($_POST['vat_all']);
$deliver            =   flout_format($_POST['deliver']);            
$totalAll           =   flout_format($_POST['totalAll']); 
for($i=0; $i<count($itemsArray); $i++)
{
    Printer::MODE_DOUBLE_HEIGHT;
    $itemId     = $itemsArray[$i];
    $name       = $this->getItemName($itemId);       
    $name       = getArabic($name,$Arabic);
    $unit       = getArabic('قطعة',$Arabic);                
    $qty        = $_POST['item_qty'][$i];
    $price      = flout_format($_POST['item_price'][$i]);
    $disc       = flout_format($_POST['item_disc'][$i]);
    $total      = flout_format($price*$qty);   
    $detail       = new detalsitem($name, $unit,$price,$qty,$total,$chars_in_line);
    $printer    -> text($detail);
    if ($disc>0)
    {
        $discValue = flout_format($disc*$price/100*$qty);
        $discLine = new totalData(getArabic('خصم',$Arabic),$discValue);
        $printer    -> text($discLine);
    }
}
$printer -> text(line());
$discLine = new totalData(getArabic('اجمالي',$Arabic),$gross_total);
$printer -> text($discLine);
$discLine = new totalData(getArabic('القيمة المضافة',$Arabic),$vat_all);
$printer -> text($discLine);
$discLine = new totalData(getArabic('خدمة توصيل',$Arabic),$deliver);
$printer -> text($discLine);
$printer -> text(line(20));

$discLine = new totalData(getArabic('اجمالى الفاتورة',$Arabic),$totalAll);
$printer -> text($discLine);

$printer -> feed(2);
$printer -> text(line());


$printer -> setJustification(Printer::JUSTIFY_CENTER);
$thnanks = getArabic('فاتورة مبيعات رقم',$Arabic);  
$printer -> text($thnanks."\n");
$printer -> text($invoice);
//$printer -> barcode($invoice, Printer::BARCODE_CODE39);
$printer->selectPrintMode(Printer::MODE_DOUBLE_HEIGHT | Printer::MODE_DOUBLE_WIDTH);
$printer->setBarcodeHeight(32);
$printer->barcode($invoice, Printer::BARCODE_ITF);

$printer -> feed();
$printer -> text(line());
$thnanks  = getArabic('شكرا على زيارتكم لنا',$Arabic);  
$printer -> text($thnanks."\n");

$printer -> feed();

$printer -> cut();
$printer -> pulse();
$printer -> close();
       



function getArabic($text,$Arabic)     
{
    $textLtr = $Arabic -> utf8Glyphs($text);
    return $textLtr;
}

function actualLength($text)
{
    if (strlen($text) != strlen(utf8_decode($text) ) )
    {
        $cont = strlen($text);
        $cont = $cont - $cont/3;   
        return $cont;
    } 
    else return 0;

}
    
function line($n=44)
{
    $line='';
    for($i=1;$i<=$n;$i++)
        $line.="-";
    $line = str_pad($line, 44,' ',STR_PAD_LEFT);
    return $line;
}
  
class topitem
{
    private $title;
    private $value;
    public function __construct($title = '', $value = '')
    {
        $this -> title = $title;
        $this -> value = $value;
    }
    public function __toString()
    {
        $leftCols   = 20+actualLength($this -> title);
        $rightCols  = 24+actualLength($this -> value);
        $left = str_pad($this -> title, $leftCols);
        $right = str_pad($this -> value, $rightCols);
        return "$left$right\n";
    }
}

class totalData
{
    private $title;
    private $value;
    public function __construct($title = '', $value = '')
    {
        $this -> title = $title;
        $this -> value = $value;
    }
    public function __toString()
    {
        $leftCols   = 37 + actualLength($this -> title);
        $rightCols  = 7  + actualLength($this -> value);
        $left = str_pad($this -> title, $leftCols ,' ',STR_PAD_LEFT);
        $right = str_pad($this -> value, $rightCols,' ',STR_PAD_LEFT);
        return "$left$right\n";
    }
}




class detalsitem
{
    private $item;
    private $unit;
    private $price;
    private $quantity;
    private $total;
    
    public function __construct($item,$unit,$price,$quantity,$total)
    {
        $this -> item = $item;
        $this -> unit = $unit;
        $this -> price = $price;
        $this -> quantity = $quantity;
        $this -> total = $total;
    }

    public function __toString()
    {
        $itemCols       = 16 + actualLength($this -> item);
        $unitCols       = 7  + actualLength($this -> unit);
        $priceCols      = 7  + actualLength($this -> price);
        $quantityCols   = 7  + actualLength($this -> quantity);
        $totalCols      = 7  + actualLength($this -> total);
        $text = str_pad($this -> item, $itemCols);
        $text.= str_pad($this -> unit, $unitCols);
        $text.= str_pad($this -> price, $priceCols,' ',STR_PAD_LEFT);
        $text.= str_pad($this -> quantity, $quantityCols,' ',STR_PAD_LEFT);
        $text.= str_pad($this -> total, $totalCols,' ',STR_PAD_LEFT);
        return "$text\n";
    }
}



//$printer -> selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
//$printer -> text("ExampleMart Ltd.\n");
//$printer -> selectPrintMode();
//$printer -> text("Shop No. 42.\n");
//
///* Title of receipt */
//$printer -> selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
//$printer -> setEmphasis(true);
//$printer -> text("SALES INVOICE\n");
//
//$printer -> selectPrintMode();
//
///* Footer */
//$printer -> feed(2);
//$printer -> setJustification(Printer::JUSTIFY_CENTER);
//
//$printer -> text("$chars_in_line \n");
//$printer -> text("For trading hours, please visit example.com\n");
//$printer -> feed(2);