<?php
   error_reporting(E_ALL);
   require_once("top.php");
   require_once("redirection.php");
   require_once("controller.php");
   
   $storeid	= $_SESSION['storeid'];
   $language	= $_SESSION['lang'];
$query      = "TRUNCATE TABLE report_purchinvoice";
$res    = mysqli_query($adController->MySQL,$query);
$query      = "TRUNCATE TABLE report_purchinvoice_all";
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
                             <a href="purchase.php"><?=PURCHASES?></a> >> <?=INVOICE_REPORT?></h2>
                        <div class="box-icon">
                           <a href="#" class="btn-minimize"><i class="halflings-icon chevron-up"></i></a>
                        </div>
                     </div>
                     <div class="box-content">
                        <form class="form-horizontal" autocomplete="off" action="<?=$pgName?>" id='form' method="POST">
                           <fieldset>
                               <div class="control-group">
                                   <div class="salesOptions">
                                       <label class="control-label" for="typeahead"><?=SUPPLIER_NAME?> : </label>
                                       <div class="controls">
                                          <select name="dupplier" class="span3" >
                                                 <option value=''> </option>
                                                    <?php
                                                            $query      = "SELECT * FROM suppliers WHERE storeid='$storeid'";
                                                            $res        = mysqli_query($adController->MySQL,$query) or die(mysqli_error($adController->MySQL));
                                                            while($data = mysqli_fetch_assoc($res))
                                                            {
                                                                    $id   = $data['id'];
                                                                    $name = $data['name_'.$language];                                                                    
                                                                    $cust	= "";
                                                                       if($_REQUEST['dupplier'] == $id)
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
                $paidTotal      =0;  
                $leftTotal      =0;
                $discTotal      =0;  
                $vatTotal       =0;
                $salesTotal     =0;  
                $reSalesTotal   =0;

                
                 $condition='';
                 $supplier		= $_POST['supplier'];
                 if ($supplier)
                 {
                     $condition .=" AND supplier = '$supplier'";
                     $query         = "SELECT * FROM suppliers WHERE id='$supplier' AND storeid='$storeid'";
                     $resCust      = mysqli_query($adController->MySQL,$query);
                     $dataCust     = mysqli_fetch_assoc($resCust);
                     $suppName     = $dataCust['name_ar'];       
                 }
                 else $suppName = 'الكل';                 
                 
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
                      <a href='reports/purchInvoiceReport.php' target='_blank'>
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
                                            <b><?=PAID?></b>
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
                                            <b><?=PURCHASES?></b>
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
                                            <b><?=DISCOUNT?></b>
                                        </td>
                                        <td class="value">                                    
                                            <div id='disc-total'></div>
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
                                            <b><?=RETURNED_PURCHASES?></b>
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

                                        <td class="value head">                                    
                                             <div id='net-total'></div>
                                        </td>
                                    </tr>
                                                             
                              </table>    
                        </div>
                      </div>
                      <div class="clearfix"></div>
                      <hr class="br">

                      
                      
                <?php

                
                $query	 	= "SELECT * FROM purchase WHERE storeid='$storeid'  $condition ORDER BY invoicenumber DESC";
                $res = mysqli_query($adController->MySQL,$query);  
                //echo $query;
                 ?>
                      <div class="titles">
                          <?=PURCHASES?>
                      </div>
                      <table class="table table-striped table-bordered bootstrap-datatable datatable">     
                          <thead>
                              <tr>
                                 <th></th>
                                 <th><?=INVOICE_NUMBER?> </th>  
                                 <th><?=DATE?></th>             
                                 <th><?=SUPPLIER_NAME?></th>    
                                 <th><?=TOTAL?></th>            
                                 <th><?=DISCOUNT?></th>         
                                 <th><?=TAX?> </th>             
                                 <th><?=TOTAL?></th>            
                                 <th><?=PAID?></th>          
                                 <th><?=LEFT?></th>          
                              </tr>
                           </thead>
                           <?php

                            $i=0;
                            while($data = mysqli_fetch_assoc($res))
                            {
                                $i++;   
                                $paymentType    = $data['paymentType'];   
                                $invoice_no     = $data['invoicenumber'];               //1
                                $invoice_date   = $data['invoice_date'];                //2 

                                $query          = "SELECT * FROM suppliers  WHERE id='$data[supplier]' AND storeid='$storeid'";
                                $resSup         = mysqli_query($adController->MySQL,$query);
                                $dataSup 	= mysqli_fetch_assoc($resSup);
                                $supplier	= $dataSup['name_'.$language];          //3
                                
                                $gross          = round(floatval($data['gross_total']),2);                  //5
                                $dis_added      = round(floatval($data['dis_added']),2);                     //6
                                $vat_all        = round(floatval($data['vat_all']),2);     
                                $all_total      = round(floatval($data['all_total']),2);                    //9                                    
                                
                                $paymentType    = $data['paymentType'];     
                                switch($paymentType)
                                {
 
                                     case 1: 
                                         $paid           = $all_total;
                                         $left           = 0;     
                                         break;
                                     case 4: 
                                         $paid           = 0;
                                         $left           = $all_total;                                              break;
                                         break;
                                 }                                 
                                 $paidTotal     += $paid;     
                                 $leftTotal     += $left;
                                 
                                 $grossTotal    += $gross;                                   
                                 $discTotal     += $dis_added;  
                                 $vatTotal      += $vat_all;
                                 $salesTotal    += $all_total;  
//9                                      

                            ?>
                            <tr>
                                <td><?=$i?></td>
                                <td>
                                    <a href='edit_purchase.php?sd=<?=$invoice_no?>'><?=$invoice_no?></a></td>  
                                <td><?=$invoice_date?></td>
                                <td><?=$supplier?></td>
                                <td><?=flout_format($gross)?></td>
                                <td><?=flout_format($dis_added)?></td>
                                <td><?=flout_format($vat_all)?></td>
                                <td><?=flout_format($all_total)?></td>                                
                                <td><?=flout_format($paid)?></td>
                                <td><?=flout_format($left)?></td>
                             

                            </tr>
                            <?php
                            $query   = "INSERT INTO `report_purchinvoice`"
                                    . "(`invoice_date`, `invoicenumber`, `supplier`, `gross_total`,`dis_added`, `vat_all`, `all_total`, `paid`, `left`, `cost`, `profit`) VALUES "
                                    ."('$invoice_date','$invoice_no',  '$supplier',  '$gross',   '$dis_added','$vat_all', '$all_total','$paid','$left','',   '' )";
                            mysqli_query($adController->MySQL,$query) or die(mysqli_error($adController->MySQL).'alllll');                              
                            }
             ?>
                        </table>
                      <div class="clearfix"></div>
                      <hr class="br">
 

                <?php                
              $query	 	= "SELECT * FROM purchase_ret WHERE storeid='$storeid'  $condition ORDER BY invoicenumber DESC";
                $res = mysqli_query($adController->MySQL,$query);  
                //echo $query;

                 ?>
                      
                                            
                      <div class="titles">
                          <?=RETURNED_PURCHASES?>
                      </div>
                      <table class="table table-striped table-bordered bootstrap-datatable datatable">     
                          <thead>
                              <tr>
                                 <th></th>
                                 <th><?=INVOICE_NUMBER?> </th>  
                                 <th><?=DATE?></th>             
                                 <th><?=SUPPLIER_NAME?></th>    
                                 <th><?=TOTAL?></th>            
                                 <th><?=DISCOUNT?></th>         
                                 <th><?=TAX?> </th>             
                                 <th><?=TOTAL?></th>            
                                 <th><?=PAYMENT?></th>          
                                 <th><?=BALANCE?></th>          
                               
                              </tr>
                           </thead>
                           <?php

                            $i=0;
                            while($data = mysqli_fetch_assoc($res))
                            {
                                $i++;
                                $invoice_no     = $data['invoicenumber'];               //1
                                $invoice_date   = $data['invoice_date'];                //2 

                                $query          = "SELECT * FROM suppliers  WHERE id='$data[supplier]' AND storeid='$storeid'";
                                $resSup         = mysqli_query($adController->MySQL,$query);
                                $dataSup 	= mysqli_fetch_assoc($resSup);
                                $supplier	= $dataSup['name_'.$language];         //3
                                
                                $gross     = round(floatval($data['gross_total']),2);                  //5
                                $dis_added      = round(floatval($data['dis_added']),2);                     //6
                                $vat_all        = round(floatval($data['vat_all']),2);                     //7                                
                                $all_total      = round(floatval($data['all_total']),2);                    //9 
                                
                                $reSalesTotal   += $all_total;  

                            ?>
                            <tr>
                                <td><?=$i?></td>
                                <td>
                                    <a href='edit_purchaseRet.php?sd=<?=$invoice_no?>'><?=$invoice_no?></a></td>                                  </td>  
                                <td><?=$invoice_date?></td>
                                <td><?=$supplier?></td>
                                <td><?=flout_format($gross)?></td>
                                <td><?=flout_format($dis_added)?></td>
                                <td><?=flout_format($vat_all)?></td>
                                <td><?=flout_format($all_total)?></td>                                
                                <td></td>
                                <td></td>

                            </tr>
                            <?php
                            }
             ?>
                      </table>
                  </div>
               </div>      
                <?php
            }


            
            $allGross = ($grossTotal);    
            $allDdisc = ($discTotal);            
            $allVat = ($vatTotal);     
            $allSales = ($salesTotal);
            $allReSales =($reSalesTotal); 
            $net = ($allSales-$allReSales);               

            $allPaid = ($paidTotal);
            $allLeft = ($leftTotal);           


            
            $query   = "INSERT INTO `report_purchinvoice_all`"
                    . "( `from`, `to`,    `branch`,     `supplier`, `grossTotal`, `discTotal`, `vatTotal`, `purchTotal`, `rePurchTotal`, `netTotal`, `payTotal`, `leftTotal`) VALUES "
                    . "('$fdate','$tdate','$branchName','$suppName','$allGross',  '$allDdisc', '$allVat',  '$allSales',  '$allReSales',  '$net',     '$allPaid', '$allLeft')";
                 mysqli_query($adController->MySQL,$query) or die(mysqli_error($adController->MySQL).'alllll');                      
             ?>
            </div>
         </div>

         <?php require_once("footer.php");?>
    <script type="text/javascript" src="js/bootstrap-datetimepicker.js" charset="UTF-8"></script>
  <script type="text/javascript">  
      $('.datetimepicker').datetimepicker({
     format:'yyyy-mm-dd hh:ii:00',
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
            
                $("#pay-total").html("<?=flout_format($allPaid)?>");
                $("#left-total").html("<?=flout_format($allLeft)?>");   
                
                $("#gross-total").html("<?=flout_format($allGross)?>");                
                $("#disc-total").html("<?=flout_format($allDdisc)?>");            
                $("#vat-total").html("<?=flout_format($allVat)?>");     
                $("#sales-total").html("<?=flout_format($allSales)?>"); 
                
                $("#reSales-total").html("<?=flout_format($allReSales)?>"); 
                
                $("#net-total").html("<?=flout_format($net)?>");   
                
               
            })
            
            
         </script>
      </div>
      
   </body>
</html>