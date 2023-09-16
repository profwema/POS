<?php
   error_reporting(E_ALL);
   require_once("top.php");
   require_once("redirection.php");
   require_once("controller.php");

   $storeid	= $_SESSION['storeid'];
   $language	= $_SESSION['lang'];
   $query      = "TRUNCATE TABLE report_inventory_sam";
$res    = mysqli_query($adController->MySQL,$query);
$query      = "TRUNCATE TABLE report_inventory";
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
                        <h2><i class="halflings-icon edit"></i><span class="break"></span><?=STORES_INVENTORY?></h2>
                        <div class="box-icon">
                           <a href="#" class="btn-minimize"><i class="halflings-icon chevron-up"></i></a>
                        </div>
                     </div>
                     <div class="box-content">
                        <form class="form-horizontal"  action="<?=$pgName?>" id='form' method="POST">
                           <fieldset>

                                       <div class="control-group">
                                           <div class="salesOptions">
                                               <label class="control-label" for="typeahead3">
                                          <?=BRANCH?>
                                               </label>
                                               <div class="controls">
                                                   <select onchange="this.form.submit()"  class='span3' name="br">
                                                       <option value="">&nbsp;</option>
                                                <?php
                                                   $query 		= "SELECT name_en,name_ar,id FROM branches WHERE storeid='$storeid'";
                                                   $res		= mysqli_query($adController->MySQL,$query);
                                                   while($data 	= mysqli_fetch_assoc($res))
                                                   {
                                                       $name	= $data['name_'.$language];
                                                       if($name == "")
                                                           $name	= $data['name_en'];
                                                       $sel	= "";
                                                       if($_REQUEST['br'] == $data['id'])
                                                       {                                                                            
                                                           $sel = " selected='true' ";
//                                                           $str2  = "<tr>";
//                                                           $str2 .= "<td><strong>".BRANCH."</strong></td>";
//                                                           $str2 .= "<td  colspan='$colspan'>$name</td>";                                                   
//                                                           $str2 .= "</tr>";                                                                            
//                                                           $searchCondition[] = $str2;
                                                       }
                                                       echo "<option value='$data[id]' $sel>$name</option>";
                                                   }
                                                   ?>
-->                                                   </select>
                                               </div>
                                           </div>
                                           <?php

                                          ?>
                                          <div class="salesOptions">
                                               <label class="control-label" for="typeahead2">
                                          <?=STORES?>
                                       </label>
                                                  <div class="controls">
                                                      <select class='span3' name="store">
                                                          <option value="">&nbsp;</option>
                                                          
                                                        <?php
                                                  if(isset($_REQUEST["br"]))
                                                  {
                                                      if ($_REQUEST["br"]!='')
                                                      {
                                                           $query 	= "SELECT name_en,name_ar,id FROM stores WHERE storeid='$storeid'AND branch = ".$_REQUEST["br"]."";
                                                           $res		= mysqli_query($adController->MySQL,$query);
                                                           while($data 	= mysqli_fetch_assoc($res))
                                                           {
                                                               $name	= $data['name_'.$language];
                                                               if($name == "")
                                                                   $name	= $data['name_en'];
                                                               $sel	= "";
                                                               if($_REQUEST['store'] == $data['id'])
                                                               {                                                                            
                                                                   $sel = " selected='true' ";
                                                               }
                                                               echo "<option value='$data[id]' $sel>$name</option>";
                                                           }
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
     $conditionItemArray    = array();
//     $itemId                = $_POST['iid'];
     $catid                 = $_POST['catid'];
//     if($itemId != '')
      $conditionItemArray[]  = " storeid='$storeid'";
    if(intval($catid) > 0)   
    {
       $conditionItemArray[]   = " catid = '$catid' ";  

         $query         = "SELECT * FROM categories WHERE id='$catid' AND storeid='$storeid'";
         $resStore      = mysqli_query($adController->MySQL,$query);
         $dataStore     = mysqli_fetch_assoc($resStore);
         $catName         = $dataStore['name_ar'];       
    }
    else $catName = 'كل الفئات';
    $conditionItem = implode(" AND ",$conditionItemArray);
    
    $conditionSearchArray   = "";    
    $branchid               = $_POST['br'];
    $store                  = $_POST['store'];
//    $fromDateV              = $_POST['from_date'];
//    $toDateV                = $_POST['to_date'];   
//   $conditionArray[]        = " storeid ='$storeid' ";
   if(intval($store) > 0)
   {
   	$conditionSearchArray.= " AND o.store ='$store' ";
        
         $query         = "SELECT * FROM stores WHERE id='$store' AND storeid='$storeid'";
         $resStore      = mysqli_query($adController->MySQL,$query);
         $dataStore     = mysqli_fetch_assoc($resStore);
         $storeName     = $dataStore['name_ar'];              
   }
       else $storeName = 'كل المخازن';

   if(intval($branchid) > 0)
   {
   	$conditionSearchArray .= " AND b.id = '$branchid' ";  
        
         $query         = "SELECT * FROM branches WHERE id='$branchid' AND storeid='$storeid'";
         $resStore      = mysqli_query($adController->MySQL,$query);
         $dataStore     = mysqli_fetch_assoc($resStore);
         $branchName     = $dataStore['name_ar'];       
   }
          else $branchName = 'كل الفروع';

   

//   if($fromDateV!='')
//   {
//        $fDate              = explode("/",$fromDateV);
//        $fDate              = $fDate[2]."-".$fDate[1]."-".$fDate[0]." 00:00:00"; 
//        $conditionSearchArray[]   = "o.date >= '$fDate' ";
//    }
//
//    if($toDateV!='')
//    {
//        $tDate              = explode("/",$toDateV);
//        $tDate              = $tDate[2]."-".$tDate[1]."-".$tDate[0]." 00:00:00"; 
//        $conditionSearchArray[]   = "o.date <='$tDate' ";    
//    }
    $conditionSearch =     $conditionSearchArray;
//    if ($condition) $condition = "AND ".$condition;
    ?>
                <div>
                    <div class="box span12">  
                        <a  href='reports/storeInvestory.php' target='_blank'>
                          <img src='img/print.png' style='width:50px'>
                         </a> 
                        <div class="clearfix"></div>

                        <table class="table table-striped table-bordered bootstrap-datatable ">
                          <thead>
                              <tr>    
                                  <th><?=BARCODE?></th>
                                  <th><?=ITEM_NAME?></th>
                                  <th><?=UNITS?></th>
                                  <th><?=FILLING?></th>
                                  <th><?=PALANCE?>  </th>
                                  <th><?=COST?>  </th>
                                  <th><?=TOTALS?>  </th>


                              </tr>
                          </thead>
                          <tbody>
                <?php

    $queryThread          = "SELECT item_thread, name_".$language." AS name "
                            . "FROM items WHERE $conditionItem  "
                            . "GROUP BY item_thread " ;
    $resThread            = mysqli_query($adController->MySQL,$queryThread);    
    
    while($dataThread = mysqli_fetch_assoc($resThread))
    {
        $total =0;
        $itemThread      = $dataThread['item_thread'];   
        $itemname      = $dataThread['name'];  
         

         
        $queryItems   = "SELECT i.id AS id ,i.name_$language AS name, i.barcode AS barcode ,i.intimate_stock AS noOfQty ,"
                . " u.name_$language AS unit "
                . "FROM items i, units u "
                . "WHERE i.item_thread = '$itemThread' "
                . "AND i.unit = u.id ORDER BY i.intimate_stock DESC" ;
        $resItem      = mysqli_query($adController->MySQL,$queryItems);        //echo $queryAll."<br>";hhh
        while($dataItem = mysqli_fetch_assoc($resItem))
        {
            $itemsListArray[count($itemsListArray)] = $dataItem;
            $itemId = $dataItem['id'];                      //get from open palance
            $query  = "SELECT o.quantity AS qty, o.date AS date, o.store, s.name_$language AS store "
                . "FROM open_palance o , stores s, branches b "
                . "WHERE  o.itemid = '$itemId' $conditionSearch"
                . "AND o.store = s.id AND s.branch = b.id AND o.storeid='$storeid'";
//       echo $query."<br> ";
           
            $res    = mysqli_query($adController->MySQL,$query);
            while($data = mysqli_fetch_assoc($res))
            {
               $noOfQty = $dataItem['noOfQty'];
               $qty     = $data['qty'];
               $total   += $qty*$noOfQty;
            }       //get from open imcome items
      
            $query = "SELECT o.quantity AS qty, o.date AS date, o.store, o.invoice_Type AS type, s.name_$language AS store "
                . "FROM income_items o, stores s, branches b "
                . "WHERE  o.itemid = '$itemId' $conditionSearch"
                . "AND o.store = s.id AND s.branch = b.id AND o.state = '1' AND o.storeid='$storeid'";
//       echo $query."<br> ";
       $res = mysqli_query($adController->MySQL,$query);
           //echo $num."<br>";
           while($data = mysqli_fetch_assoc($res))
           {
               $noOfQty = $dataItem['noOfQty'];
               $qty     = $data['qty'];
               $total   += $qty*$noOfQty;
           }  
    //get from open outgo items
           $query = "SELECT o.quantity AS qty, o.date AS date, o.store, o.invoice_Type AS type, s.name_$language AS store "
                    . "FROM outgo_items o, stores s, branches b "
                    . "WHERE  o.itemid = '$itemId'  $conditionSearch"
                    . "AND o.store = s.id AND s.branch = b.id AND o.state = '1' AND o.storeid='$storeid'";
//       echo $query."--------------------------------------------------------<br> ";
           $res = mysqli_query($adController->MySQL,$query);
           //echo $num."<br>";
           while($data = mysqli_fetch_assoc($res))
           {
               $noOfQty = $dataItem['noOfQty'];
               $qty     = $data['qty']*-1;
               $total   += $qty*$noOfQty;
           }                                     
        }
        
        for($i=0; $i<count($itemsListArray); $i++)
        {          
            $palance = intval($total/$itemsListArray[$i]['noOfQty']);
            $total = $total % $itemsListArray[$i]['noOfQty'];
            if ($palance!=0)
            {
                $barcode = $itemsListArray[$i]['barcode'];
                $itemName = $itemsListArray[$i]['name'];
                $unitName = $itemsListArray[$i]['unit'];
                $count = $itemsListArray[$i]['noOfQty'];
                $cost = 0;
                $totals = $palance * $cost; 
                $allTotal += $totals;
                $query   = "INSERT INTO `report_inventory`"
                        . "( `barcode`, `item`,    `unit`,  `count`,   `quantity`, `cost`, `total`) VALUES "
                        . "('$barcode',  '$itemName','$unitName','$count', '$palance','$cost','$totals')";
                mysqli_query($adController->MySQL,$query) or die(mysqli_error($adController->MySQL));  
                ?>
                          <tr>
                              <td> 
                                  <div><?=$barcode?></div>
                              </td>
                              <td>
                                  <div><?=$itemName?></div>
                              </td>  
                              <td>
                                  <div><?=$unitName?></div>
                              </td> 
                              <td>
                                  <div><?=$count?></div>
                              </td>      
                              <td>
                                  <div><?=$palance;?></div>
                              </td>                              
                              <td>
                                  <div><?=$cost;?></div>
                              </td>
                              <td>
                                  <div><?=$totals;?></div>
                              </td>

                          </tr>
                          
                            <?php
            }
        }
                                   
        unset($itemsListArray);
   }
             ?>
                          </tbody>
                        </table>
                    </div>
                </div>
                 <?php
                 
              $query   = "INSERT INTO `report_inventory_sam`"
               . "( `pranch`, `store`, `category`, `totals`) VALUES "
               . "('$branchName','$storeName','$catName','$allTotal')";
       mysqli_query($adController->MySQL,$query);          
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