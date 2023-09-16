<?php
require_once("top.php");
require_once("redirection.php");
require_once("controller.php");
$language   = $_SESSION['lang'];
$storeid    = $_SESSION['storeid'];
$customer   = '0';  
echo $storeid."---- <br>";
$query 	= "SELECT * FROM orders  WHERE storeid='$storeid' GROUP BY hasvalue ";
$res   	= mysqli_query($adController->MySQL,$query) or die(mysqli_error($adController->MySQL));
$num	= mysqli_num_rows($res);
echo "no of orders = $num <br>";
$i=0;

while($data = mysqli_fetch_assoc($res))
{
    $i++;

    $hash               = $data['hasvalue'];
    echo "$i - $hash - ";
    $branch             = $data['branch'];
    $userid             = $data['userid'];

    $orderType          = $data['order_type'];
    $paymentType        = $data['payment_type'];
      
    $pExpiry            = $data['specific_date'];    
    
    $invoice            = getInvoice('invoicenumber','sales',$storeid);
    $outgoInvoice       = getInvoice('outgo_No','outgo',$storeid);  
    
    echo " $invoice - $outgoInvoice <br>  ";

    $query              = "SELECT store FROM employees WHERE id='$userid'";
    $resStore                = mysqli_query($adController->MySQL,$query) or die(mysqli_error($adController->MySQL));
    
    $dataType		= mysqli_fetch_assoc($resStore);
    $store      	= $dataType['store'];
    
    $totalAll           = 0;
    $totaldel           = 0;
    $query              = "SELECT * FROM orders WHERE hasvalue ='$hash'";
    $resItem            = mysqli_query($adController->MySQL,$query) or die(mysqli_error($adController->MySQL));
    while($dataItem     = mysqli_fetch_assoc($resItem))
    {
        $itemId     = $dataItem['itemid'];
        $qty        = $dataItem['qty'];
        $price      = $dataItem['cost'];
        $disc       = $dataItem['discount'];
        $tax        = $dataItem['vat'];
        $delCost    = $dataItem['delivery_cost'];        
        $totalDisc  = $qty *( $price - $disc);
        $vat        = $totalDisc/100 * $tax;
        $totalNet   = $totalDisc + $vat;
        
        echo "---- $itemId --- $totalNet --- $delCost <br>  ";
        
        $totalAll += $totalNet;
        $totaldel += $delCost;
        
        $query      = "INSERT INTO sales_items ( itemid  , quantity , cost   , discount , tax  , store  , invoice_No , storeid) VALUES"
                                                   . "('$itemId','$qty'    ,'$price','$disc'   ,'$tax','$store','$invoice'  ,'$storeid')";
        mysqli_query($adController->MySQL,$query) or die(mysqli_error($adController->MySQL)); 
        $query      = "INSERT INTO outgo_items ( itemid   , quantity, cost   , discount, tax,    store  , outgo_No,  invoice_No   ,invoice_Type ,date , storeid) VALUES"
                                                . "( '$itemId','$qty'   ,'$price','$disc'  , '$tax','$store','$outgoInvoice','$invoice','SALES','$pExpiry','$storeid')";
        mysqli_query($adController->MySQL,$query) or die(mysqli_error($adController->MySQL));    
    }
    $totalAll = $totalAll + $totaldel;
     echo "---- totals :  $totalAll ---- $totaldel <br>  ";
     
    $query          = "INSERT INTO sales(invoicenumber, branch,    customer,   document, invoice_date, user,    orderType,    paymentType, delver,     all_total,  storeid) VALUES"
                                      . "('$invoice', '$branch', '$customer', '$hash',  '$pExpiry',   '$userid', '$orderType', '$paymentType','$totaldel',   '$totalAll','$storeid')";
    $exe            = mysqli_query($adController->MySQL,$query);

    $queryOutgo     = "INSERT INTO outgo (outgo_No     ,branch    ,invoice_type, Beneficiary, invoice_No, date , user  , all_total ,storeid) VALUES"
                                     . "('$outgoInvoice','$branch' ,'SALES'  , '$customer', '$invoice', '$pExpiry','$userid','$totalAll','$storeid')";
    $exe            = mysqli_query($adController->MySQL,$queryOutgo);            

            $query          = "DELETE FROM `orders` WHERE hasvalue='$hash'";
            $exe            = mysqli_query($adController->MySQL,$query) or die(mysqli_error($adController->MySQL));
   
}


function getInvoice($field, $table,$storeid)
        {
            //$storeid        = $_SESSION['storeid'];
            $query   = "SELECT $field AS invoice
                    FROM $table
                    WHERE storeid='$storeid'                                          
                    ORDER BY $field DESC
                    LIMIT 1";
            $resin	= mysqli_query($adController->MySQL,$query) or die(mysqli_error($adController->MySQL));
            $num	= mysqli_num_rows($resin);
            $datain	= mysqli_fetch_assoc($resin);
            if($num)
            {
                $rest = substr($datain['invoice'], -4);  
                $trans_No = intval($rest)+1;
            }

            else 
                $trans_No = 1;
            //$rand           = rand(0,9999);
            //$rand           =  str_pad($rand,4,"0",STR_PAD_LEFT);
            //$ip             = str_replace(".","",$_SERVER['REMOTE_ADDR']);
            $time           = time();
            $invoice        = $storeid.'-'.$time.'-'.str_pad($trans_No,4,"0",STR_PAD_LEFT);; 
            return $invoice;
        }
        
echo "end .......";