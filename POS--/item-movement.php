<?php
   error_reporting(E_ALL);
   require_once("top.php");
   require_once("redirection.php");
   require_once("controller.php");

   $storeid	= $_SESSION['storeid'];
   $language	= $_SESSION['lang'];
      $query      = "TRUNCATE TABLE report_item_move_sam";
$res    = mysqli_query($adController->MySQL,$query);
$query      = "TRUNCATE TABLE report_item_move_sam";
$res    = mysqli_query($adController->MySQL,$query);
$query      = "TRUNCATE TABLE report_item_move_all";
$res    = mysqli_query($adController->MySQL,$query);   

   ?>
<!DOCTYPE html>
<html lang="en">
   <head>
      <meta charset="utf-8">
      <title>WAM Tech Soft</title>
      <?php require_once("header.php");?>	
      <style>
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
         @media (min-width: 701px) {
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
                        <h2><i class="halflings-icon edit"></i><span class="break"></span><?=ITEM_MOVEMENT?></h2>
                        <div class="box-icon">
                           <a href="#" class="btn-minimize"><i class="halflings-icon chevron-up"></i></a>
                        </div>
                     </div>
                     <div class="box-content">
                        <form class="form-horizontal"  action="<?=$pgName?>" id='form' method="POST">
                           <fieldset>


                               <div class="clearfix"></div>
                                       <div class="control-group">


                                       <?php
                                          $catSearch='';

                                          ?>
                                           <div class="salesOptions">
                                               <label class="control-label" for="typeahead"><?=ITEMS?></label>
                                               <div class="controls">                                              
                                                   <select  name="iid" data-rel="chosen" style="width:80%" >
                                                       <option value=''> </option>
                                        <?php
                                        $name     = "name_".$_SESSION['lang'];
                                        $query   = "SELECT * FROM items WHERE items.storeid='$storeid' ";
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
                                                                echo "<option value='$data[item_thread]' selected> $barcode | $itemName | $unitName</option>";
                                                            else
                                                                echo "<option value='$data[item_thread]'> $barcode | $itemName | $unitName</option>";
                                                           }
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
                                            <input type="text" class="datepicker-nobar" id="from_date" name="from_date" value="<?=$fromDateV?>">
                                            
                                        </div>
                                    </div>
                                    <div class="salesOptions">
                                        <label class="control-label" for="date01"><?=TO_DATE?></label>
                                        <div class="controls">
                                            <input type="text" class="datepicker-nobar" id="to_date" name="to_date" value="<?=$toDateV?>">
                                        </div>
                                    </div>
                                </div>
                                    

                               <button type="submit" class="btn btn-primary btn-lg" name="Submit" onclick="loadReport()"><?=LOAD_FORM?></button>
                                    <div class="clearfix"></div>
                           </fieldset>
                        </form>
                     </div>
                  </div>
               </div>
 <?php
 if(isset($_POST['Submit']))
 {
    $itemId		= $_POST['iid'];
    if($itemId == '')
    {
        ?>
                <script>
                    alert("NO_ITEM_ENTERED");
                    </script>
                <?php
    }
    else 
    {

        
        $conditionSel  = " AND item_thread = '$itemId' ";
        $conditionArray		= array();
   
        $conditionArray  = array();   
        $fromDateV       = $_POST['from_date'];
        if($fromDateV!='')
        {
            $fDate              = explode("/",$fromDateV);
            $fDate              = $fDate[2]."-".$fDate[1]."-".$fDate[0]." 00:00:00"; 
            $conditionArray[]   = "o.date >= '$fDate' ";
        }
        $toDateV       = $_POST['to_date'];   
        if($toDateV!='')
        {
            $tDate              = explode("/",$toDateV);
            $tDate              = $tDate[2]."-".$tDate[1]."-".$tDate[0]." 00:00:00"; 
            $conditionArray[]   = "o.date <='$tDate' ";    
        }
        $condition      = implode(" AND ",$conditionArray);
        if ($condition) $condition = "AND ".$condition;
  
        $query= "CREATE TEMPORARY TABLE `item_card` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
        `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
        `store` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
        `unit` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
        `qty` int(11) NOT NULL,
        `noOfQty` int(11) NOT NULL,
        PRIMARY KEY (`id`))";
        mysqli_query($adController->MySQL,$query);

        $total =0;

        $queryAll          = "SELECT i.id As id ,i.name_$language AS name, i.unit , i.barcode ,i.intimate_stock AS noOfQty , u.name_$language AS unit FROM items i, units u WHERE  i.unit = u.id $conditionSel ORDER BY i.intimate_stock DESC" ;
        $resAll            = mysqli_query($adController->MySQL,$queryAll);     
        while($dataAll = mysqli_fetch_assoc($resAll))
        {
           $itemsListArray[count($itemsListArray)] = $dataAll;

           $itemId = $dataAll['id'];
           $item = $dataAll['name'];
           //get from open palance
           $query = "SELECT o.quantity AS qty, o.date AS date, o.store, s.name_$language AS store "
                    . "FROM open_palance o , stores s "
                    . "WHERE  o.itemid = '$itemId' $condition"
                    . "AND o.store = s.id AND o.storeid='$storeid'";
           $res = mysqli_query($adController->MySQL,$query);
           $num	= mysqli_num_rows($res);
           //echo $num."<br>";
           while($data = mysqli_fetch_assoc($res))
           {
               $date    = $data['date'];
               $name    = "OPENING_PALANCE";
               $store   = $data['store'];
               $unit    = $dataAll['unit'];
               $noOfQty = $dataAll['noOfQty'];
               $qty     = $data['qty'];
               $total   += $qty*$noOfQty;
               $query   = "INSERT INTO item_card ( date  , name , store , unit ,  qty , noOfQty ) VALUES"
                                                 . "( '$date','$name','$store','$unit', '$qty', '$noOfQty')";
               //echo $query."<br>";
               mysqli_query($adController->MySQL,$query) or die(mysqli_error($adController->MySQL)); 
           }
           //get from open imcome items
           $query = "SELECT o.quantity AS qty, o.date AS date, o.store, o.invoice_Type AS type, s.name_$language AS store "
                    . "FROM income_items o , stores s "
                    . "WHERE  o.itemid = '$itemId' $condition"
                    . "AND o.store = s.id AND o.state = '1' AND o.storeid='$storeid'";
           //echo $query." ";
           $res = mysqli_query($adController->MySQL,$query);
           $num	= mysqli_num_rows($res);
           //echo $num."<br>";
           while($data = mysqli_fetch_assoc($res))
           {
               $date    = $data['date'];
               $name    = $data['type'];
               $store   = $data['store'];
               $unit    = $dataAll['unit'];
               $noOfQty = $dataAll['noOfQty'];
               $qty     = $data['qty'];
               $total   += $qty*$noOfQty;
               $query   = "INSERT INTO item_card ( date  , name , store , unit ,  qty , noOfQty ) VALUES"
                                                 . "( '$date','$name','$store','$unit', '$qty', '$noOfQty')";
               //echo $query."<br>";
               mysqli_query($adController->MySQL,$query) or die(mysqli_error($adController->MySQL)); 
           }  

    //get from open outgo items
           $query = "SELECT o.quantity AS qty, o.date AS date, o.store, o.invoice_Type AS type, s.name_$language AS store "
                    . "FROM outgo_items o , stores s "
                    . "WHERE  o.itemid = '$itemId'  $condition"
                    . "AND o.store = s.id AND o.state = '1' AND o.storeid='$storeid'";
           //echo $query." ";
           $res = mysqli_query($adController->MySQL,$query);
           $num	= mysqli_num_rows($res);
           //echo $num."<br>";
           while($data = mysqli_fetch_assoc($res))
           {
               $date    = $data['date'];
               $name    = $data['type'];
               $store   = $data['store'];
               $unit    = $dataAll['unit'];
               $noOfQty = $dataAll['noOfQty'];
               $qty     = $data['qty']*-1;
               $total   += $qty*$noOfQty;
               $query   = "INSERT INTO item_card ( date  , name , store , unit ,  qty , noOfQty ) VALUES"
                                                 . "( '$date','$name','$store','$unit', '$qty', '$noOfQty')";
               //echo $query."<br>";
               mysqli_query($adController->MySQL,$query) or die(mysqli_error($adController->MySQL)); 
           }              
        }
 ?>
                                           

               <div>
                  <div class="box span12">
                    <a href='reports/item_move.php' target='_blank'>
                          <img src='img/print.png' style='width:50px'>
                    </a>                       
                      <div class="clearfix"></div>
                      <center> 
                      <table style="width:50%; margin-top: 10px"class="table table-striped table-bordered bootstrap-datatable">
                          <tr>
                              <th><?=UNIT?></th>
                              <th><?=QTY_VAL?></th>
                              <th><?=PALANCE?> : <?=$total?> </th>
                          </tr>
                            <?php
                          
                            for($i=0; $i<count($itemsListArray); $i++)
                            {
                                $totalQty = intval($total/$itemsListArray[$i]['noOfQty']);
                                $totalAmount   =  0;  
                                $total = $total % $itemsListArray[$i]['noOfQty'];
                                $unitName= $itemsListArray[$i]['unit'];
                                $qtyVal = $itemsListArray[$i]['noOfQty']
                                  ?>
                            <tr>
                                <td>
                                    <div><?=$unitName?></div>
                                </td>
                                <td>
                                    <div><?=$qtyVal?></div>
                                </td>                                
                                <td>
                                    <div>
                                          <?=$totalQty?>
                                    </div>
                                </td>
                            </tr>
                            <?php
                                $query   = "INSERT INTO `report_item_move_sam`"
                                        . "( `unit`,    `count`,   `quantity`, `total`) VALUES "
                                        . "('$unitName','$qtyVal', '$totalQty','$totalAmount')";
                                mysqli_query($adController->MySQL,$query) or die(mysqli_error($adController->MySQL)); 
                            }
                            ?>
                            
                        </table> 
                      </center>
<!--                     <ul class="export-data">
                         <li><a href="javascript:void(0)"  onclick="printTable(0)"><img src="img/xls.png" width="40"> XLS</a></li>
                         <li><a href="javascript:void(0)"  onclick="printTable(1)"><img src="img/print.png" width="40"> PRINT</a></li>
                     </ul>-->
                      <div class="clearfix"></div>
                      <table class="table table-striped table-bordered bootstrap-datatable datatable">
                          <thead>
                              <tr>
                                    <th></th>
                                    <th><?=DATE?></th>
                                    <th><?=TYPE?></th>
                                    <th><?=STORE?></th>
                                    <th><?=UNITS?></th>
                                    <th><?=QTY_VAL?></th>                                    
                                    <th><?=QTY?></th>
                                    <th><?=TOTALCOUNT?> </th> 
                                    <th><?=COST?></th>
                                    <th><?=TOTAL?></th>

                              </tr>
                          </thead>
                          <tbody>
                            <?php
                                   $query = "SELECT date, name, store, unit, noOfQty, qty "
                                            . "FROM item_card "
                                            . "ORDER BY date ";
                                  //echo $query."<br>";
                                   $res = mysqli_query($adController->MySQL,$query);
                                   $j = 0;
                                   while($data = mysqli_fetch_assoc($res))
                                   {
                                       $j++;
                                       $total = $data['qty'] * $data['noOfQty'];
                                       $price = 0;
                                       $totalAmount =  $data['qty'] * $price ;
                                       $style='';
                                       if ($data['qty']<0)
                                           $style=" style='color: red'";
                                       
                                        ?>
                                          <tr <?=$style?> >
                                              <td><?=$j?></td>
                                              <td><?php echo $data['date'] ?></td>
                                              <td><?php echo constant($data['name']) ?></td>
                                              <td><?php echo $data['store'] ?></td>
                                              <td><?php echo $data['unit'] ?></td>
                                              <td><?php echo $data['noOfQty'] ?></td>                                  
                                              <td><?php echo $data['qty'] ?></td>
                                              <td><?php echo $total ?> </td> 
                                              <td><?php echo $price ?> </td> 
                                              <td><?php echo $totalAmount ?> </td> 

                                          </tr>
                                          <?php
                                        $query   = "INSERT INTO `report_item_move`"
                                                . "( `date`,      `type`,                        `store`,        `unit`,       `count`,          `quantity`,  `totalCount`, `cost`,  `total`) VALUES "
                                                . "('$data[date]','".constant($data['name'])."', '$data[store]', '$data[unit]','$data[noOfQty]', '$data[qty]','$total',     '$price','$totalAmount')";
                                        mysqli_query($adController->MySQL,$query) or die(mysqli_error($adController->MySQL));  
                                   }
                            ?>
                        </tbody>
                      </table>
                  </div>
               </div>
 <?php
    }
     $from = ($fromDateV=='')?'': $fDate;
     $to = ($toDateV=='')?'': $tDate;
            $query   = "INSERT INTO `report_item_move_all`"
                    . "(`item`, `from`, `to`)  VALUES "
                    . "('$item','$from','$to')";
            mysqli_query($adController->MySQL,$query) or die(mysqli_error($adController->MySQL).'alllll');  
 }
 ?>
 
            </div>
         </div>
         <div class="clearfix"></div>


         <?php require_once("footer.php");?>
         <script type="text/javascript">
             $(document).ready(function()
             {
               var config = {
              '.span3'     : { width: '80%' }
             }
                for (var selector in config) 
                {
                  $(selector).chosen(config[selector]);
                }
            })
            </script>
      </div>
   </body>
</html>