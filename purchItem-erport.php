<?php
   error_reporting(E_ALL);
   require_once("top.php");
   require_once("redirection.php");
   require_once("controller.php");
   
   $storeid	= $_SESSION['storeid'];
   $language	= $_SESSION['lang'];
$query      = "TRUNCATE TABLE report_saleitem";
$res    = mysqli_query($adController->MySQL,$query);
$query      = "TRUNCATE TABLE report_saleitem_all";
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
                         <h2>
                             <i class="halflings-icon edit"></i>
                             <span class="break"></span>
                             <a href="purchase.php"><?=PURCHASES?></a> >> <?=ITEMS_REPORT?></h2>
                        <div class="box-icon">
                           <a href="#" class="btn-minimize"><i class="halflings-icon chevron-up"></i></a>
                        </div>
                     </div>
                     <div class="box-content">
                        <form class="form-horizontal" autocomplete="off" action="<?=$pgName?>" id='form' method="POST">
                           <fieldset>
                               <div class="control-group">
                                   <div class="salesOptions">
                                       <label class="control-label" for="typeahead"><?=CATEGORY?></label>
                                       <div class="controls">
                                           <select onchange="this.form.submit()"  class='span3' name="catid">
                                               <option value="">&nbsp;</option>
                                                <?php
                                                   $query= "SELECT * FROM categories WHERE storeid='$storeid'";// id IN ( SELECT catid FROM items WHERE id IN (SELECT DISTINCT itemid FROM orders  $condition))";
                                                   $res		= mysqli_query($adController->MySQL,$query);
                                                   while($data 	= mysqli_fetch_assoc($res))
                                                   {
                                                       $name	= $data["name_".$language];
                                                   
                                                       $sel	= "";                                                                
                                                   
                                                       if(isset($_REQUEST["catid"]) && $_REQUEST["catid"] == $data['id'])
                                                               {
                                                                       $sel = " selected='true' ";
                                                               }
                                                               echo "<option value='$data[id]' $sel>$name</option>";
                                                               }
                                                   ?>											
                                           </select>
                                       </div>
                                   </div>

                                   <div class="salesOptions">
                                       <label class="control-label" for="typeahead"><?=ITEMS?></label>
                                       <div class="controls">                                              
                                           <select  name="iid" data-rel="chosen" style="width:80%" >
                                               <option value="">&nbsp;</option>
                                               
                                        <?php
                                        $catSearch='';
                                        if(isset($_REQUEST["catid"]))
                                        {
                                            if ($_REQUEST["catid"]!='')
                                            {
                                                $catSearch=" AND catid = ".$_REQUEST["catid"]." ";
                                            }                                                                                      
                                            $name     = "name_".$_SESSION['lang'];
                                            $query   = "SELECT * FROM items WHERE items.storeid='$storeid' $catSearch GROUP BY item_thread";
                                            $res        = mysqli_query($adController->MySQL,$query) or die(mysqli_error($adController->MySQL));
                                            while($data = mysqli_fetch_assoc($res))
                                            {
                                                $barcode      = $data['barcode'];
                                                if ($barcode == '') $barcode = "-----";
                                                $itemName     = $data[$name];
                                                if ($itemName == '' || $itemName =='`')
                                                {
                                                    if ($_SESSION['lang']=='en')                                                           
                                                        $itemName     = $data['name_ar'];
                                                    elseif($_SESSION['lang']=='ar')                                                           
                                                        $itemName     = $data['name_en'];
                                                }    
                                                $unit         = $data['unit'];
                                                $queryUnit    = "SELECT $name FROM units WHERE id='$unit' ";
                                                $resUint      = mysqli_query($adController->MySQL,$queryUnit) or die(mysqli_error($adController->MySQL));
                                                $dataUnit     = mysqli_fetch_assoc($resUint);
                                                $unitName     = $dataUnit[$name];
                                                if ($unitName == '') $unitName = "-----";
                                                if( isset ($_POST['iid']) && $_POST['iid'] == $data['item_thread'])
                                                {
                                                    echo "<option value='$data[item_thread]' selected> $barcode | $itemName | $unitName</option>";
                                                }
                                                else
                                                    echo "<option value='$data[item_thread]'> $barcode | $itemName | $unitName</option>";
                                            }
                                        }
                                          ?>
                                           </select>                                                   
                                       </div>
                                   </div>       
                               </div>       
                               <div class="clearfix"></div>                              
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
                $sabakahTotal   =0;  
                $cashTotal      =0;
                $cardTotal      =0;
                $grossTotal     =0;
                $discTotal      =0;  
                $vatTotal       =0;
                $delvTotal      =0;
                $salesTotal     =0;  
                $reSalesTotal   =0;
                 $conditionItem='';
                 $catid                 = $_POST['catid'];  
                 if ($catid) 
                 {
                     $conditionItem .=" AND i.catid = '$catid'";        
                     $query         = "SELECT * FROM categories WHERE id='$catid' AND storeid='$storeid'";
                     $resCust      = mysqli_query($adController->MySQL,$query);
                     $dataCust     = mysqli_fetch_assoc($resCust);
                     $catName     = $dataCust['name_ar'];       
                 }
                 else $catName = 'الكل';                 
                 $iid                   = $_POST['iid'];                  
                 if ($iid) 
                 {
                     $conditionItem .=" AND i.id = '$iid'";    
                     $query         = "SELECT * FROM items WHERE id='$iid' AND storeid='$storeid'";
                     $resCust      = mysqli_query($adController->MySQL,$query);
                     $dataCust     = mysqli_fetch_assoc($resCust);
                     $itemName     = $dataCust['name_ar'];       
                 }
                 else $itemName = 'الكل';                     
                 
                 
                 $conditionRep='';
                 $supplier		= $_POST['supplier'];
                 if ($supplier)
                 {
                     $conditionRep .=" AND supplier = '$supplier'";
                     $query         = "SELECT * FROM suppliers WHERE id='$supplier' AND storeid='$storeid'";
                     $resCust      = mysqli_query($adController->MySQL,$query);
                     $dataCust     = mysqli_fetch_assoc($resCust);
                     $suppName     = $dataCust['name_ar'];       
                 }
                 else $suppName = 'الكل';         
                 
                 $branch		= $_POST['branch'];  
                 if ($branch)
                 {
                     $conditionRep .=" AND branch = '$branch'";
                     $query         = "SELECT * FROM branches WHERE id='$branchid' AND storeid='$storeid'";
                     $resStore      = mysqli_query($adController->MySQL,$query);
                     $dataStore     = mysqli_fetch_assoc($resStore);
                     $branchName     = $dataStore['name_ar'];       
                 }
                 else $branchName = 'الكل';
                 
                 $fdate		        = $_POST['fr_date'];  
                 if ($fdate) $conditionRep .=" AND s.invoice_date >= '$fdate'";                 
                 $tdate		        = $_POST['to_date'];  
                 if ($tdate) $conditionRep .=" AND s.invoice_date <= '$tdate'";               
                 ?>
                <div>
                    <div class="box span12">  
                        <a href='reports/purchItemReport.php' target='_blank'>
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
                        </div>
                          
                          <DIV class="counts"> 
                              <table>
                                  <tr>
                                      <td class="title" >
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

                //echo $query;
                 ?>
                      <div class="titles">
                          <?=PURCHASES?>
                      </div>

                      <table class="table table-striped table-bordered bootstrap-datatable datatable">     
                          <thead>
                              <tr>
                                 <th></th>
                                 <th><?=ITEM_NAME?> </th> 
                                    <th><?=UNITS?> </th>   
                                    <th><?=BARCODE?> </th> 
                                 <th><?=QUANTITY?> </th>
                                 <th><?=PRICE?></th>
                                 <th><?=TOTAL?></th>            
                                 <th><?=DISCOUNT?></th>         
                                 <th><?=TAX?> </th>   
                                 <th><?=TOTAL?></th>                  
                               
                              </tr>
                           </thead>
                           <?php
                           $queryAll          = "SELECT i.id As id ,i.name_$language AS name, i.unit , i.barcode , u.name_$language AS unit FROM items i, units u WHERE  i.unit = u.id $conditionItem " ;
//                           echo $queryAll.'<br>';
                           $resAll            = mysqli_query($adController->MySQL,$queryAll); 
                           $i=0;                           
                           while($dataAll = mysqli_fetch_assoc($resAll))
                           {   
//                               echo $dataAll[id].'---- <br>';
                               $i++;   
                               $itsQty          =   0;  
                               $priceItem       =   0;
                               $grossItem       =   0;
                               $discItem        =   0;
                               $vatItem         =   0;
                               $netItem         =   0;
                               $priceArray     = array();
                               $discountArray  = array();
                               
                               $itemid = $dataAll['id'];                               
                               $name = $dataAll['name'];
                               $unit = $dataAll['unit'];
                               $barcode = $dataAll['barcode'];
                               
                              
                               $queryItem            = "SELECT si.*, s.* FROM purchase_items si, purchase s WHERE si.itemid = '$dataAll[id]' AND si.invoice_No = s.invoicenumber $conditionRep" ;
//                               echo '--- '.$queryItem.'<br>';

                               $resItem              = mysqli_query($adController->MySQL,$queryItem);
                               $num                  = mysqli_num_rows($resItem);
                               if($num)
                               {
                                    while($dataItem       = mysqli_fetch_assoc($resItem))
                                    {
                                        $qty                = floatval($dataItem['quantity']);
                                        $price		= round(floatval($dataItem['cost']),2);  
                                        $gross              = $price * $qty ;

                                        $discount		= round(floatval($dataItem['discount']),2);
                                        $discount           = $discount /100 * $price ;
                                        $discount           = $discount * $qty;

                                        $vat                 = round(floatval($dataItem['tax']),2);   
                                        $vat                 = ($gross - $discount) * $vat /100;

                                        $net                 = $gross - $discount + $vat;

                                        $itsQty                 += $qty;
                                        $grossItem              += $gross;
                                        $vatItem                += $vat;
                                        $netItem                += $net;

                                        $priceArray[]           = $price;
                                        $discountArray[]        = $discount;                                   

//                                        $paymentType    = $dataItem['paymentType'];     
//                                        switch($paymentType)
//                                        {
//                                             case 1: 
//                                                 $cashTotal += $net;
//                                                 break;
//                                             case 2: 
//                                                 $cardTotal += $net;
//                                                 break;
//                                             case 3: 
//                                                 $sabakahTotal += $net;
//                                                 break;
//                                        }
                                    }
                                    $priceItem   = array_sum($priceArray)/count($priceArray);
                                    $discItem    = array_sum($discountArray)/count($discountArray);
                                ?>
                                <tr>
                                    <td><?=$i?></td>
                                    <td><?=$name?></td>
                                    <td><?=$unit?></td>
                                    <td><?=$barcode?></td>                                    
                                    <td><?=flout_format($itsQty)?></td>
                                    <td><?=flout_format($priceItem)?></td>
                                    <td><?=flout_format($grossItem)?></td>                                
                                    <td><?=flout_format($discItem)?></td>
                                    <td><?=flout_format($vatItem)?></td>
                                    <td><?=flout_format($netItem)?></td>    

                                </tr>
                                <?php
                                     $grossTotal    += $grossItem;                                   
                                     $discTotal     += $discItem;  
                                     $vatTotal      += $vatItem;
                                     $salesTotal    += $netItem;  
                                     $query   = "INSERT INTO `report_saleitem`"
                                    ."( `itemId`, `barcode`, `item`, `unit`, `quantity`, `price`,    `discount`, `vat`,     `total`,   `cost`, `profit`)  VALUES "
                                    ."('$itemid','$barcode','$name', '$unit','$itsQty', '$priceItem','$discItem','$vatItem','$netItem', '',    '' )";
                                     mysqli_query($adController->MySQL,$query) or die(mysqli_error($adController->MySQL).'alllll');                                         
                               }
                           }
             ?>
                        </table>
                      <div class="clearfix"></div>
                      <hr class="br">
                <?php

                //echo $query;
                 ?>
                      <div class="titles">
                          <?=RETURNED_PURCHASES?>
                      </div>

                      <table class="table table-striped table-bordered bootstrap-datatable datatable">     
                          <thead>
                              <tr>
                                 <th></th>
                                 <th><?=ITEM_NAME?> </th> 
                                 <th><?=QUANTITY?> </th>
                                 <th><?=PRICE?></th>
                                 <th><?=TOTAL?></th>            
                                 <th><?=DISCOUNT?></th>         
                                 <th><?=TAX?> </th>             
                                 <th><?=TOTAL?></th>            
                               
                              </tr>
                           </thead>
                           <?php
                           $queryAll          = "SELECT i.id As id ,i.name_$language AS name, i.unit , i.barcode , u.name_$language AS unit FROM items i, units u WHERE  i.unit = u.id $conditionItem " ;
//                           echo $queryAll.'<br>';
                           $resAll            = mysqli_query($adController->MySQL,$queryAll); 
                           $i=0;                           
                           while($dataAll = mysqli_fetch_assoc($resAll))
                           {   
//                               echo $dataAll[id].'---- <br>';
                               $i++;   
                               $itsQty      = 0;  
                               $priceItem   = 0;
                               $grossItem   = 0;
                               $discItem    = 0;
                               $vatItem     = 0;
                               $netItem     = 0;
                               $priceArray     = array();
                               $discountArray  = array();                               
                               
                               $name = $dataAll['id'].' - '.$dataAll['name'].' - '.$dataAll['unit'];
//                               echo $name.'---- <br>';  
                               
                               $queryItem            = "SELECT si.*, s.* FROM purchase_ret_items si, purchase_ret s WHERE si.itemid = '$dataAll[id]' AND si.invoice_No = s.invoicenumber $conditionRep" ;
//                               echo '--- '.$queryItem.'<br>';

                               $resItem              = mysqli_query($adController->MySQL,$queryItem);
                               $num                  = mysqli_num_rows($resItem);
                               if($num)
                               {
                                    while($dataItem       = mysqli_fetch_assoc($resItem))
                                    {
                                        $qty                = floatval($dataItem['quantity']);
                                        $price		= round(floatval($dataItem['cost']),2);  
                                        $gross              = $price * $qty ;

                                        $discount		= round(floatval($dataItem['discount']),2);
                                        $discount           = $discount /100 * $price ;
                                        $discount           = $discount * $qty;

                                        $vat                 = round(floatval($dataItem['tax']),2);   
                                        $vat                 = ($gross - $discount) * $vat /100;

                                        $net                 = $gross - $discount + $vat;

                                        $itsQty                 += $qty;
                                        $grossItem              += $gross;
                                        $vatItem                += $vat;
                                        $netItem                += $net;

                                        $priceArray[]           = $price;
                                        $discountArray[]        = $discount;                                   

//                                        $paymentType    = $dataItem['paymentType'];     
//                                        switch($paymentType)
//                                        {
//                                             case 1: 
//                                                 $cashTotal += $net;
//                                                 break;
//                                             case 2: 
//                                                 $cardTotal += $net;
//                                                 break;
//                                             case 3: 
//                                                 $sabakahTotal += $net;
//                                                 break;
//                                        }
                                    }
                                    $priceItem   = array_sum($priceArray)/count($priceArray);
                                    $discItem    = array_sum($discountArray)/count($discountArray);
                                ?>
                                <tr>
                                    <td><?=$i?></td>
                                    <td><?=$name?></td>
                                    <td><?=flout_format($itsQty)?></td>
                                    <td><?=flout_format($priceItem)?></td>
                                    <td><?=flout_format($grossItem)?></td>                                
                                    <td><?=flout_format($discItem)?></td>
                                    <td><?=flout_format($vatItem)?></td>
                                    <td><?=flout_format($netItem)?></td>                                
                                </tr>
                                <?php
//                                     $grossTotal    += $grossItem;                                   
//                                     $discTotal     += $discItem;  
//                                     $vatTotal      += $vatItem;
                                     $reSalesTotal    += $netItem;  
                                     $query   = "UPDATE `report_saleitem` SET `retQuantity`='$itsQty',`retTotal`= '$netItem' WHERE `itemId` = '$dataAll[id]' ";
                                     mysqli_query($adController->MySQL,$query) or die(mysqli_error($adController->MySQL).'alllll');                                         
                               }
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
            
            $allReSales = ($reSalesTotal); 
            $net = ($allSales-$allReSales);       
            
            $query   = "INSERT INTO `report_saleitem_all`"
                    . "(`from`, `to`,     `branch`,      `customer`, `category`,`item`,    `grossTotal`, `discTotal`, `vatTotal`, `salesTotal`, `reSalesTotal`, `netTotal`) VALUES "
                    . "('$fdate','$tdate','$branchName', '$suppName','$catName','$itemName','$allGross',  '$allDdisc', '$allVat',  '$allSales',  '$allReSales',  '$net' )";
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
                $("#gross-total").html("<?=flout_format($grossTotal)?>");                
                $("#disc-total").html("<?=flout_format($discTotal)?>");            
                 $("#vat-total").html("<?=flout_format($vatTotal)?>");     
                 $("#sales-total").html("<?=flout_format($salesTotal)?>"); 
                $("#reSales-total").html("<?=flout_format($reSalesTotal)?>"); 
                $("#net-total").html("<?=flout_format(($salesTotal)-($reSalesTotal))?>");               
    
            })
            
            
         </script>
      </div>
      
   </body>
</html>