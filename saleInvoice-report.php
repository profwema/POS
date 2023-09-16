<?php
error_reporting(E_ALL);
require_once("top.php");
require_once("redirection.php");
require_once("controller.php");

$storeid	= $_SESSION['storeid'];
$language	= $_SESSION['lang'];
$query      = "TRUNCATE TABLE report_saleInvoice";
$res    = mysqli_query($adController->MySQL,$query);
$query      = "TRUNCATE TABLE report_saleInvoice_all";
$res    = mysqli_query($adController->MySQL,$query);
?>





<!DOCTYPE html>
<html lang="en">
   <head>
      <meta charset="utf-8">
      <title>WAM Tech Soft</title>
      <?php require_once("header.php");?>	
      <style>
          .datetime
              {
               width: 75% ;
               border:  green solid 1px !important;
          }
          
          
         @media (max-width: 700px) {
         #mobile 
         {
         display:block;
         }
         #mobile .row-fluid
         {
         display:none;
         }
         #desktop
         {
         display:none;
         }
         }
         @media (min-width: 701px)
         {
         #mobile 
         {
         display:none;
         }
         #desktop
         {
         display:block;
         }
         }

      </style>
      <link href="css/bootstrap-datetimepicker.css" rel="stylesheet" media="screen">
   </head>
<body>
      <?php require_once("header_top.php");?>
<div class="container-fluid-full">
         <div class="row-fluid">
            <?php require_once("left_menu.php");?>
            <div id="content" class="span10">
               <div>
                  <div class="box span12">
                     <div class="box-header" data-original-title>
                         <h2><i class="halflings-icon edit"></i>
                             <span class="break"></span>
                             <a href="saleInvoice.php"><?=SALES?></a> >> <?=INVOICE_REPORT?></h2>
                        <div class="box-icon">
                           <a href="#" class="btn-minimize"><i class="halflings-icon chevron-up"></i></a>
                        </div>
                     </div>
                     <div class="box-content">
                        <form class="form-horizontal" autocomplete="off" action="<?=$pgName?>" id='form' method="POST">
                           <fieldset>
                               <div class="control-group">
                                   <div class="salesOptions">
                                       <label class="control-label" for="typeahead"><?=CUSTOMER_NAME?> : </label>
                                       <div class="controls">
                                          <select name="customer" class="span3" >
                                                 <option value=''> </option>
                                                    <?php
                                                            $query      = "SELECT * FROM customers WHERE storeid='$storeid'";
                                                            $res        = mysqli_query($adController->MySQL,$query) or die(mysqli_error($adController->MySQL));
                                                            while($data = mysqli_fetch_assoc($res))
                                                            {
                                                                    $id   = $data['id'];
                                                                    $name = $data['name_'.$language];                                                                    
                                                                    $cust	= "";
                                                                       if($_REQUEST['customer'] == $id)
                                                                       {                                                                            
                                                                           $cust = " selected='true' ";
                                                                       }
                                                                       echo "<option value='$id' $cust>$name</option>";

                                                            }
                                                    ?>
                                              </select>
                                       </div>
                                   </div>
                                   <div class="salesOptions">
                                       <label class="control-label"><?=BRANCH?> :</label>
                                       <div class="controls">
                                           <select name="branch"  class="span3">
                                                      <option value="">&nbsp;</option>
                                                        <?php
                                                                $query = "SELECT * FROM branches WHERE storeid='$storeid' ORDER BY name_en ASC";
                                                                $res   = mysqli_query($adController->MySQL,$query) or die(mysqli_error($adController->MySQL));
                                                                while($data= mysqli_fetch_assoc($res))
                                                                {
                                                                        $id   = $data['id'];
                                                                        $name = $data['name_'.$language];                                                                    
                                                                        $brn	= "";
                                                                           if($_REQUEST['branch'] == $id)
                                                                           {                                                                            
                                                                               $brn = " selected='true' ";
                                                                           }
                                                                           echo "<option value='$id' $brn>$name</option>";

                                                                }
                                                        ?>
                                           </select>
                                       </div>
                                   </div>
                               </div>
                               <div class="clearfix"></div>
                               <div class="control-group">
                                   <div class="salesOptions">
                                       <label class="control-label" for="typeahead"><?=DATA_ENTRY?></label>
                                       <div class="controls">
                                           <select class='span3'  name="data-erntry">
                                               <option value="">&nbsp;</option>
                                                <?php
//                                                   $query = "SELECT name_en,name_ar,id FROM employees WHERE storeid='$storeid'";
//                                                   $res		= mysqli_query($adController->MySQL,$query);
//                                                   while($data 	= mysqli_fetch_assoc($res))
//                                                   {
//                                                        $id   = $data['id'];
//                                                        $name = $data['name_'.$language];
//                                                        $emp	= "";
//                                                           if($_REQUEST['emp'] == $id)
//                                                           {                                                                            
//                                                               $emp = " selected='true' ";
//                                                           }
//                                                           echo "<option value='$id' $emp>$name</option>";                                                   }
                                                   ?>
                                           </select>
                                       </div>
                                   </div>
                                   <div class="salesOptions">
                                       <label class="control-label" for="date01"><?=SALES_MAN?></label>
                                       <div class="controls">
                                           <select  class='span3' name="sales-man">
                                               <option value="">&nbsp;</option>
                                        <?php
                                          

                                           ?>
                                           </select>
                                       </div>
                                   </div>
                               </div>
                               <div class="clearfix"></div>
                               
                               <div class="control-group">
                                   <div class="salesOptions">                               
                                      <label class="control-label" for="date01"><?=FROM_DATE?></label>
                                      <div class="controls">
                                          <input type="text" class="datetime datetimepicker" id="fr_date" name="fr_date" value="<?=$_POST['fr_date']?>">
                                     </div>
                                   </div>
                                   <div class="salesOptions">                                                                 
                                      <label class="control-label" for="date02"><?=TO_DATE?></label>
                                      <div class="controls">
                                         <input type="text" class="datetime datetimepicker" id="to_date" name="to_date" value="<?=$_POST['to_date']?>">
                                      </div>
                                   </div>
                               </div>
                               <div class="clearfix"></div>
                               <button type="submit" class="btn btn-primary btn-lg" name="Submit" onclick="loadReport()"><?=LOAD_FORM?></button>
                           </fieldset>
                        </form>
                     </div>
                  </div>
               </div>

          <?php
            if(isset($_POST['Submit']))
            {
                $sabakahTotal   =0;  
                $cashTotal      =0;
                $cardTotal      =0;
                $grossTotal     =0;
                $discTotal      =0;  
                $vatTotal       =0;
                $delvTotal      =0;
                $salesTotal     =0;  
                $reSalesTotal   =0;
                
                 $condition='';
                 $cust                  = $_POST['customer'];
                 if ($cust)
                 {
                     $condition .=" AND customer = '$cust'";
                     $query         = "SELECT * FROM customers WHERE id='$cust' AND storeid='$storeid'";
                     $resCust      = mysqli_query($adController->MySQL,$query);
                     $dataCust     = mysqli_fetch_assoc($resCust);
                     $custName     = $dataCust['name_ar'];       
                 }
                 else $custName = 'الكل';
                 
                 $branch		= $_POST['branch'];  
                 if ($branch)
                 {
                     $condition .=" AND branch = '$branch'";
                     $query         = "SELECT * FROM branches WHERE id='$branchid' AND storeid='$storeid'";
                     $resStore      = mysqli_query($adController->MySQL,$query);
                     $dataStore     = mysqli_fetch_assoc($resStore);
                     $branchName     = $dataStore['name_ar'];       
                 }
                 else $branchName = 'الكل';
                 
                 $fdate		        = $_POST['fr_date'];  
                 if ($fdate) $condition .=" AND invoice_date >= '$fdate'";                 
                 $tdate		        = $_POST['to_date'];  
                 if ($tdate) $condition .=" AND invoice_date <= '$tdate'";   
                 ?>
                <div>
                    
                    
                  <div class="box span12">   
                      <a href='reports/saleInvoiceReport.php' target='_blank'>
                          <img src='img/print.png' style='width:50px'>
                      </a> 
                      <div class="clearfix"></div>
                      <div class="titles">
                          <?=TOTAlS?>
                      </div>
                      <div class="totalAll">
                          <DIV class="counts"> 
                              <table>
                                  <tr>
                                      <td class="title" colspan="2">
                                            <b><?=PAYMENT_TYPE?></b>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="head">
                                            <img src="img/cash.png"/><b><?=CASH?></b>
                                        </td>
                                        <td class="value">                                    
                                            <div id='cash-total'></div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="head">
                                            <img src="img/mada.png"/><b><?=SABAKAH?></b>
                                        </td>
                                        <td class="value">                                    
                                             <div id='sabakah-total'></div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="head">
                                            <img src="img/visa.png"/><b><?=CARD_PAYMENT?></b>
                                        </td>
                                        <td class="value">                                    
                                            <div id='card-total'></div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="head">
                                            <b><?=TOTAL?> <?=PAID?></b>
                                        </td>
                                        <td class="value">                                    
                                            <div id='pay-total'></div>
                                        </td>
                                    </tr>   
                                    <tr>
                                        <td class="head">
                                            <b><?=LEFT?></b>
                                        </td>
                                        <td class="value">                                    
                                            <div id='left-total'></div>
                                        </td>
                                    </tr>                                       
                              </table> 
                          </div>
                        <DIV class="counts"> 
                              <table>
                                  <tr>
                                      <td class="title" colspan="2">
                                            <b><?=SALES?></b>
                                        </td>
                                    </tr>       
                                    <tr>
                                        <td class="head">
                                            <b><?=TOTAL?></b>
                                        </td>
                                        <td class="value">                                    
                                            <div id='gross-total'></div>
                                        </td>
                                    </tr>   
                                    <tr>
                                        <td class="head">
                                            <b><?=VAT?></b>
                                        </td>
                                        <td class="value">                                    
                                             <div id='vat-total'></div>
                                        </td>
                                    </tr>                                        
                                    <tr>
                                        <td class="head">
                                            <b><?=DELIVER?></b>
                                        </td>
                                        <td class="value">                                    
                                            <div id='delv-total'></div>
                                        </td>
                                    </tr>                                 
                                    <tr>
                                        <td class="head">
                                            <b><?=DISCOUNT_ADDED?></b>
                                        </td>
                                        <td class="value">                                    
                                            <div id='disc-total'></div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="head">
                                            <b><?=TOTAL?></b>
                                        </td>
                                        <td class="value">                                    
                                            <div id='sales-total'></div>
                                        </td>
                                    </tr>                                    
                              </table>                               
                        </div>
                        <DIV class="counts"> 
                              <table>
                                  <tr>
                                      <td class="title" colspan="2">
                                            <b><?=RETURNED_SALES?></b>
                                        </td>
                                    </tr>                                   
                                    <tr>
                                        <td class="head">
                                            <b><?=TOTAL?></b>
                                        </td>
                                        <td class="value">                                    
                                            <div id='reSales-total'></div>
                                        </td>
                                    </tr>
                              </table>
                            <table>
 
                                  <tr>
                                      <td class="title">
                                            <b><?=NET_BALANCE?></b>
                                        </td>
                                    </tr>                                   
                                    <tr>

                                        <td class="head value">                                    
                                             <div id='net-total'></div>
                                        </td>
                                    </tr>
                                                             
                              </table>    
                        </div>
                      </div>
                      <div class="clearfix"></div>
                      <hr class="br">

                      
                      
                <?php

                
                $query	 	= "SELECT * FROM sales WHERE storeid='$storeid' AND state='1'  $condition ORDER BY invoicenumber DESC";
                $res = mysqli_query($adController->MySQL,$query);  
                //echo $query;
                 ?>
                      <div class="titles">
                          <?=SALES?>
                      </div>
                      <table class="table table-striped table-bordered bootstrap-datatable datatable">     
                          <thead>
                              <tr>
                                 <th></th>
                                 <th><?=INVOICE_NUMBER?> </th>  
                                 <th><?=DATE?></th>             
                                 <th><?=CUSTOMER_NAME?></th>    
                                 <th><?=SALES_MAN?></th>        
                                 <th><?=TOTAL?></th>   
                                 <th><?=TAX?> </th>             
                                 <th><?=DELIVER?> </th>                                    
                                 <th><?=DISCOUNT_ADDED?></th>         
                                 <th><?=TOTAL?></th>            
                                 <th><?=PAID?></th>          
                                 <th><?=LEFT?></th>          
                                 <th><?=TOTAL_COST?></th>       
                                 <th><?=TOTAL_COM?></th>                                 
                              </tr>
                           </thead>
                           <?php

                            $i=0;
                            while($data = mysqli_fetch_assoc($res))
                            {
                                $i++;   
                                $invoice_no     = $data['invoicenumber'];               //1
                                $invoice_date   = $data['invoice_date'];                //2 

                                $query          = "SELECT * FROM customers  WHERE id='$data[customer]' AND storeid='$storeid'";
                                $resSup         = mysqli_query($adController->MySQL,$query);
                                $dataSup 	= mysqli_fetch_assoc($resSup);
                                $customer	= $dataSup['name_'.$language];          //3
                                
                                $gross          = round(floatval($data['gross_total']),2);                  //5
                                $dis_added      = round(floatval($data['dis_added']),2);                     //6
                                $vat_all        = round(floatval($data['vat_all']),2);     
                                $delver         = round(floatval($data['delver']),2);    //7                                
                                $all_total      = round(floatval($data['all_total']),2);                    //9                                    
                                //-----------  for calculate totals
                                //$paymentType    = $data['paymentType'];     
                                $cash           = round(floatval($data['cash']),2);  
                                $visa           = round(floatval($data['visa']),2);  
                                $mada           = round(floatval($data['mada']),2);     
                                $paid           = $cash + $visa + $mada;
                                $left           = round(floatval($data['left']),2);     
                                         
                                $cashTotal      += $cash;
                                $cardTotal      += $visa;
                                $sabakahTotal   += $mada;
                                $leftTotal      += $left;                                
                                
                                $grossTotal     += $gross;                                   
                                $discTotal      += $dis_added;  
                                $vatTotal       += $vat_all;                                
                                $delvTotal      += $delver;
                                $salesTotal     += $all_total;  
                                
                            ?>
                            <tr>
                                <td><?=$i?></td>
                                <td>
                                    <a href='edit_saleInvoice.php?sd=<?=$invoice_no?>'><?=$invoice_no?></a></td>  
                                <td><?=$invoice_date?></td>
                                <td><?=$customer?></td>
                                <td></td>
                                
                                <td><?=flout_format($gross)?></td>
                                <td><?=flout_format($vat_all)?></td>
                                <td><?=flout_format($delver)?></td>                                
                                <td><?=flout_format($dis_added)?></td>
                                <td><?=flout_format($all_total)?></td>                                
                                <td><?=flout_format($paid)?></td>
                                <td><?=flout_format($left)?></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <?php
                            $query   = "INSERT INTO `report_saleinvoice`"
                               ." (`invoice_date`,`invoicenumber`,`customer`,`user`,`gross_total`,`dis_added`, `vat_all`, `delver`, `all_total`, `paid`, `left`,`cost`,`profit`) VALUES "
                               ."('$invoice_date','$invoice_no',  '$customer', '',  '$gross',     '$dis_added','$allVat', '$delver','$all_total','$paid','$left','',   '' )";
                            mysqli_query($adController->MySQL,$query) or die(mysqli_error($adController->MySQL).'alllll');              
                            }
             ?>
                        </table>
                      <div class="clearfix"></div>
                      <hr class="br">
 

                <?php                
              $query	 	= "SELECT * FROM sales_ret WHERE storeid='$storeid' AND state='1' $condition ORDER BY invoicenumber DESC";
                $res = mysqli_query($adController->MySQL,$query);  
                //echo $query;

                 ?>
                      
                                            
                      <div class="titles">
                          <?=RETURNED_SALES?>
                      </div>
                      <table class="table table-striped table-bordered bootstrap-datatable datatable">     
                          <thead>
                              <tr>
                                 <th></th>
                                 <th><?=INVOICE_NUMBER?> </th>  
                                 <th><?=DATE?></th>             
                                 <th><?=CUSTOMER_NAME?></th>    
                                 <th><?=SALES_MAN?></th>   
                                 <th><?=SALE_INVOICE?> </th>                                  
                                 <th><?=TOTAL?></th>  
                                 <th><?=TAX?> </th>             
                                 <th><?=DISCOUNT_ADDED?></th>         
                                 <th><?=TOTAL?></th>            
                                 <th><?=PAYMENT?></th>          
                                 <th><?=LEFT?></th>          
                               
                              </tr>
                           </thead>
                           <?php

                            $i=0;
                            while($data = mysqli_fetch_assoc($res))
                            {
                                $i++;
                                $invoice_no     = $data['invoicenumber'];               //1
                                $salesInvoice     = $data['salesInvoice'];               //1                                
                                $invoice_date   = $data['invoice_date'];                //2 

                                $query          = "SELECT * FROM customers  WHERE id='$data[customer]' AND storeid='$storeid'";
                                $resSup         = mysqli_query($adController->MySQL,$query);
                                $dataSup 	= mysqli_fetch_assoc($resSup);
                                $customer	= $dataSup['name_'.$language];          //3
                                
                                $gross          = round(floatval($data['gross_total']),2);                  //5
                                $dis_added      = round(floatval($data['dis_added']),2);                     //6
                                $vat_all        = round(floatval($data['vat_all']),2);                     //7                                
                                $all_total      = round(floatval($data['all_total']),2);                    //9 
                                $paid = ($data['paymentType']==1)? $all_total : 0 ;
                                $left = $all_total - $paid ;
                                $reSalesTotal   += $all_total;  

                            ?>
                            <tr>
                                <td><?=$i?></td>
                                <td>
                                    <a href='edit_recallsales.php?sd=<?=$invoice_no?>'><?=$invoice_no?></a></td>                                  </td>  
                                <td><?=$invoice_date?></td>
                                <td><?=$customer?></td>
                                <td></td>
                               <td><?=$salesInvoice?></td>                                  
                                <td><?=flout_format($gross)?></td>
                                <td><?=flout_format($vat_all)?></td>
                                <td><?=flout_format($dis_added)?></td>
                                <td><?=flout_format($all_total)?></td>                                
                                <td><?=flout_format($paid)?></td>
                                <td><?=flout_format($left)?></td>

                            </tr>
                            <?php
                            }
             ?>
                      </table>
                  </div>
               </div>      
                <?php
            }


            
            $allGross = flout_format($grossTotal);                
            $allDdisc = flout_format($discTotal);            
            $allVat = flout_format($vatTotal);     
            $allDelv = flout_format($delvTotal);
            $allSales = flout_format($salesTotal);
            
            $allReSales =flout_format($reSalesTotal); 
            $net = flout_format($allSales-$allReSales);               

            $allMada = flout_format($sabakahTotal);            
            $allCash = flout_format($cashTotal);     
            $allCard = flout_format($cardTotal); 
            $allPaid = flout_format($cardTotal+$cashTotal+$sabakahTotal);
            $allLeft = flout_format($leftTotal);           


            
            $query   = "INSERT INTO `report_saleinvoice_all`"
                    . "( `from`, `to`,    `branch`,     `user`, `customer`, `grossTotal`, `discTotal`, `vatTotal`, `delvTotal`, `salesTotal`, `reSalesTotal`, `netTotal`, `payTotal`, `cashTotal`, `cardTotal`, `sabakahTotal`, `leftTotal`) VALUES "
                    . "('$fdate','$tdate','$branchName', '',    '$custName','$allGross',  '$allDdisc', '$allVat',  '$allDelv',  '$allSales',  '$allReSales',  '$net',     '$allCash', '$allCard',  '$allMada',  '$allPaid',     '$allLeft' )";
                 mysqli_query($adController->MySQL,$query) or die(mysqli_error($adController->MySQL).'alllll');              
             ?>
            </div>
         </div>

         <?php require_once("footer.php");?>
    <script type="text/javascript" src="js/bootstrap-datetimepicker.js" charset="UTF-8"></script>
  <script type="text/javascript">  
      $('.datetimepicker').datetimepicker({
     format:'yyyy-mm-dd hh:00:00',
   minView:1,        
         showMeridian: true,
         todayBtn: true,
        autoclose: true

//         ampm: true // FOR AM/PM FORMAT


        });
      </script>
        <script type="text/javascript">

     
            $(document).ready(function(){
            
               var config = {
              '.span3'     : { width: '80%' }
            }
            for (var selector in config) 
            {
              $(selector).chosen(config[selector]);
            }
            
                $("#sabakah-total").html("<?=$allMada?>");            
                $("#cash-total").html("<?=$allCash?>");     
                $("#card-total").html("<?=$allCard?>"); 
                $("#pay-total").html("<?=$allPaid?>");
                $("#left-total").html("<?=$allLeft?>");    
                
                $("#gross-total").html("<?=$allGross?>");                
                $("#disc-total").html("<?=$allDdisc?>");            
                $("#vat-total").html("<?=$allVat?>");     
                $("#delv-total").html("<?=$allDelv?>");
                $("#sales-total").html("<?=$allSales?>"); 
                
                $("#reSales-total").html("<?=$allReSales?>"); 
                
                $("#net-total").html("<?=$net?>");               

            })
            
            
         </script>
      </div>
      
   </body>
</html>