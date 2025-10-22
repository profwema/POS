<?php
error_reporting(E_ALL);
require_once("top.php");
require_once("redirection.php");
require_once("controller.php");
$language   = $_SESSION['lang'];
$storeid    = $_SESSION['storeid'];
$query      = "TRUNCATE TABLE report_open_palance";
$res    = mysqli_query($adController->MySQL,$query);
$query      = "TRUNCATE TABLE report_open_palance_all";
$res    = mysqli_query($adController->MySQL,$query);






if(isset($_REQUEST["store"]))
 $_SESSION['store']= $_REQUEST["store"]


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>WAM Tech Soft
    </title><?php require_once("header.php");?>
    <style>
        .totalAll
        {
            width: 100px;
            border: #D73D3D solid 1px;
            padding: 10px 20px;
            margin-left: 100px;
            margin-top: -5px;
            font-size: 18px;
            color: #D73D3D;
            
        }
    </style>
</head>
<body>
    <?php require_once("header_top.php");?>
    <div class="container-fluid-full">
        <div class="row-fluid">
            <?php require_once("left_menu.php");?>
            <div class="span10" id="content">
                <div>
                    <!--class="row-fluid sortable"-->
                    <div class="box span12">
                        <div class="box-header" data-original-title="">
                            <h2><i class="halflings-icon edit"></i><span class="break"></span><?=OPENING_PALANCE ?>
                            </h2>
                        </div>
                        <div class="box-content">
                            <p align="right"><a class="btn btn-success" href="add_open_palance.php"><i class="icon-plus"></i></a></p>
                            <div class="box-content">
                                <form action="open-palance.php" class="form-horizontal" id='form' method="post" name="form">
                                    <fieldset>
                                        <div class="control-group">
                                            <div class="salesOptions" style="width:25%">
                                                <label class="control-label" for="typeahead3" style="width:30px"><?=STORES?>
                                                </label>
                                                <div class="controls" style="margin-left:60px">
                                                    <select class='span3' name="store" id="store" onchange="this.form.submit()">
                                                        <option value="">
                                                            &nbsp;
                                                        </option><?php
                                                        $query= "SELECT * FROM stores WHERE storeid='$storeid'";// id IN ( SELECT catid FROM items WHERE id IN (SELECT DISTINCT itemid FROM orders  $condition))";
                                                        $res        = mysqli_query($adController->MySQL,$query);
                                                        while($data     = mysqli_fetch_assoc($res))
                                                        {
                                                           $name    = $data["name_".$language];
                                                           $sel = "";                                                                
                                                           if(isset( $_SESSION['store']) && $_SESSION['store'] == $data['id'])
                                                           {
                                                               $sel = " selected='true' ";
                                                           }
                                                           elseif($data["defualt"]==1)
                                                           {
                                                               $_SESSION['store']=$data['id'];
                                                               $sel = " selected='true' ";
                                                           }
                                                           echo "<option value='$data[id]' $sel>$name</option>";
                                                           }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="salesOptions" style="width:25%" >
                                                <?php 
                                                    $queryDate	= "SELECT * FROM open_palance_date WHERE storeid='$storeid'";
                                                    $resDate	= mysqli_query($adController->MySQL,$queryDate);
                                                    $dataDate	= mysqli_fetch_assoc($resDate);
                                                    $open_palance_date = date("d/m/Y",strtotime($dataDate['date']));
                                                    $_SESSION['open_palance_date']=$open_palance_date;
                                                ?>
                                                <label class="control-label"style="width:50px"><?=DATE?></label>						
                                                <div class="controls" style="width:100%;margin-left: 70px">
                                                    <input type="text" class="span4 input-xlarge datepicker-nobar" name="palance-date" id="palance-date"  style="width:70%"value='<?=$_SESSION['open_palance_date']?>' >
                                                    <button type="button" id='open-palance-date' class="btn btn-primary"><?=EDIT?></button>
                                                </div>
                                            </div>  
                                            <div class="salesOptions" style="width:25%">
                                                <label class="control-label"style="width:80px"><?=TOTAL?></label>
                                                <div class="totalAll" id="totalAll"> 0.00</div>
                                                
                                            </div>  
                                            <div class="salesOptions" style="width:10%">
                                                <button type="submit" id='report' name='report' class="btn btn-dark">
                                                    <i class='halflings-icon white list-alt'></i></button>
                                           
                                                  
                                            </a>                                            
                                        </div>
                                        </div>
                                    </fieldset>
                                </form>
                            </div>
                            <table class="table table-striped table-bordered bootstrap-datatable datatable">
                                <thead>
                                    <tr>
                                        <th>
                                        </th>
                                        <th><?=STORE?>
                                        </th>                                        
                                        <th><?=ITEM_NAME?>
                                        </th>
                                        <th><?=UNIT?>
                                        </th>
                                        <th><?=QTY?>
                                        </th>
                                        <th><?=PRICE?>
                                        </th>
                                        <th><?=DISCOUNT?> %
                                        </th>

                                        <th><?=TOTAL?>
                                         </th> 
                                         <th>
                                         </th>  
                                        
                                        
                                        
                                        
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $storeSearch='';
                                        if(isset( $_SESSION['store']))
                                        {
                                             if ( $_SESSION['store']!='')
                                             {
                                                   $storeSearch=" AND store = ". $_SESSION['store']." ";
                                             }
                                        }
                                        ?>
                                            <?php
                                                $allTotal = 0;
                                                $i = 1;
                                                $query      = "SELECT * FROM open_palance  WHERE storeid='$storeid' $storeSearch";
                                                $res        = mysqli_query($adController->MySQL,$query) or die(mysqli_error($adController->MySQL));
                                                while($data     = mysqli_fetch_assoc($res))
                                                {
                                                    $idval	   = urlencode($adController->encrypt_decrypt(1,$data['id'],0));
                                                    $tableName     = urlencode($adController->encrypt_decrypt(1,'open_palance',0));
                                                    
                                                    $query         = "SELECT * FROM stores WHERE id='$data[store]' AND storeid='$storeid'";
                                                    $resStore      = mysqli_query($adController->MySQL,$query);
                                                    $dataStore     = mysqli_fetch_assoc($resStore);
                                                    
                                                    $query         = "SELECT * FROM items WHERE id='$data[itemid]' AND storeid='$storeid'";
                                                    $resItem       = mysqli_query($adController->MySQL,$query);
                                                    $dataItem      = mysqli_fetch_assoc($resItem);
                                                    
                                                    $query         = "SELECT * FROM units WHERE id='$dataItem[unit]' AND storeid='$storeid'";
                                                    $resUnit       = mysqli_query($adController->MySQL,$query);
                                                    $dataUnit      = mysqli_fetch_assoc($resUnit);

                                                    $barcode       = $dataItem["barcode"];
                                                    $count         = $dataItem["intimate_stock"];
                                                    
                                                    $itemName      = $dataItem["name_$language"];
                                                if($itemName == "" || $itemName == "`")
                                                    {
                                                        if($language == "ar")
                                                                $itemName   = $dataItem['name_en'];
                                                        else
                                                                $itemName   = $dataItem['name_ar'];
                                                    }
                                                    
                                                    $unitName       = $dataUnit["name_$language"];
                                                    if($unitName == "")
                                                    {
                                                        if($language == "ar")
                                                                $unitName   = $dataUnit['name_en'];
                                                        else
                                                                $unitName   = $dataUnit['name_ar'];
                                                    } 
                                                    
                                                    $storeName       = $dataStore["name_$language"];
                                                    if($storeName == "")
                                                    {
                                                        if($language == "ar")
                                                                $storeName   = $dataStore['name_en'];
                                                        else
                                                                $storeName   = $dataStore['name_ar'];
                                                    }    
                                                    $quantity  =  $data['quantity'];
                                                    $cost      =  $data['cost'];     
                                                    $discount  =  $data['discount'];
//                                                    $vat       =  $dataItem['vat'];
                                                    $preTotal  = $quantity * $cost;
                                                    $discValue = $preTotal * $discount / 100;
                                                    $discTotal = $preTotal - $discValue;
                                                    $allTotal += $discTotal;
//                                                    $vatValue  = $discTotal * $vat / 100;
//                                                    $total     = $discTotal - $vatValue ; 
                                                    $query   = "INSERT INTO `report_open_palance`"
                                                            . "(`id`, `store`, `barcode`, `item`,    `unit`,  `count`,   `quantity`, `cost`, `discount`, `total`) VALUES "
                                                            . "('$i','$storeName','$barcode',  '$itemName','$unitName','$count', '$quantity','$cost','$discount','$discTotal')";
                                                    mysqli_query($adController->MySQL,$query) or die(mysqli_error($adController->MySQL));                                           ?>

                                                   <tr>
                                                       <td>
                                                           <?php
                                                           echo $i;
                                                        
                                                           ?>
                                                       </td>
                                                     <td><?php echo $storeName ?></td>
                                                      
                                                    <td>
                                                        <input type="hidden" id="item-id-<?=$i?>" value='<?=$data[id]?>' name='id[]'/>

                                                        <?php echo $itemName ?>
                                                    </td>
                                                    <td><?php echo $unitName ?></td>
                                                    <td>
                                                        <input type="text" id='item-qty-<?=$i?>' class="item-qty" maxlength="20"  name='item_qty[]' style="width: 70%; margiqtyn:auto -30px"  value="<?php echo $quantity ?>">
                                                        
                                                    </td>
                                                    <td>
                                                        <input type="text" id='item-price-<?=$i?>' class="item-price" maxlength="20"  name='item_price[]'style="width: 70%; margin:auto -30px" value="<?php echo $cost ?>">
                                                            
                                                    </td>
                                                    <td>
                                                        <input type="text" id='item-discount-<?=$i?>' class="item-discount" maxlength="20"  name='item_disc[]'style="width: 70%; margin:auto -30px" value="<?php echo $discount ?>">
                                                        
                                                    </td>
<!--                                                    <td id='item-vat-<?=$i?>'><?php echo $vat ?></td>-->
                                                    <td id='item-total-<?=$i?>'>
                                                            <script type="text/javascript">
                                                                $(document).ready(function()
                                                                   {
                                                                        totalOpenPalance(<?=$i?>);
                                                                    });   
                                                            </script>
                                                    </td>                                                    
                                                    <td class='center'>                                                        
                                                        <a class='item-edit btn btn-info' id='item-edit-<?=$i?>' href='javascript:void(0)'>
                                                            <i class='halflings-icon white edit'></i>  
                                                        </a>
                                                              <?php 
                                                              echo "<a class='btn btn-danger' href='javascript:void(0)' onclick='javascript:deleteData(\"$tableName\",\"$idval\");'>
                                                                        <i class='halflings-icon white trash'></i> 
                                                               </a>";
                                                        ?>
                                                    </td>
                                                   </tr>
                                                    <?php

                                                    
                                                       $i++;
                                                }
                                        ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
     if ( $_SESSION['store']=='') 
         $store= 'كل المخازن';
     else 
    { 
         $query         = "SELECT * FROM stores WHERE id='$_SESSION[store]' AND storeid='$storeid'";
         $resStore      = mysqli_query($adController->MySQL,$query);
         $dataStore     = mysqli_fetch_assoc($resStore);
         $store         = $dataStore['name_ar'];
     }
       $query   = "INSERT INTO `report_open_palance_all`"
               . "( `store`, `date`, `total`) VALUES "
               . "('$store','$dataDate[date]','$allTotal')";
       mysqli_query($adController->MySQL,$query);
    
     if(isset($_POST["report"]))
     {
           unset($_POST["report"]);
      ?>
        <script type="text/javascript">
                                                 
    window.open('reports/openPalance.php', '_blank')  ;
        </script>    
      <?php
     }
    ?>
     
    <div class="clearfix"></div><?php require_once("footer.php");?>
    <script type="text/javascript">
     
     function totalOpenPalance(id)
{
       var preTotal =  $("#item-qty-"+id).val() * $("#item-price-"+id).val();
      // alert($("#item-qty-"+id).val());
       var discount = preTotal * $("#item-discount-"+id).val() / 100;
       var disTotal = preTotal - discount;
       var vaTax   = 0;
            //   = disTotal * $("#item-vat-"+id).html() / 100;
       var total    = disTotal + vaTax ;
       $("#item-total-"+id).html( total.toFixed(2) );
       
       var totalAll = parseInt( $("#totalAll").html() )
       totalAll = totalAll + total
       $("#totalAll").html( totalAll.toFixed(2) ) ;

}

    $(document).ready(function()
    {
            
           var config = {
              '.span3'     : { width: '95%' }
            };
            for (var selector in config) 
            {
              $(selector).chosen(config[selector]);
            }
            var totalAll = 0;

                
                
            $(".item-qty").blur(function()
            {
                var id = $(this).attr('id').replace('item-qty-','');
                totalOpenPalance(id);
            });

            $(".item-price").blur(function()
            {
                var id = $(this).attr('id').replace('item-price-','');
                totalOpenPalance(id);
            });

            $(".item-discount").blur(function()
            {
                var id = $(this).attr('id').replace('item-discount-','');
                totalOpenPalance(id);
            });
            
            $(".item-edit").click(function()
            {  
                //alert("qty");
                var id = $(this).attr('id').replace('item-edit-','');
                var q = $("#item-qty-"+id).val();
              
                var p = $("#item-price-"+id).val();
                var d = $("#item-discount-"+id).val();
                var v = $("#item-id-"+id).val();

                    var u = "controller.php";
                    $("#overlay").show(300);
                    $.ajax({
                                 url:u,
                                 data : {'f':'update_openPalance','q':q,'p':p,'d':d,'id':v},
                                type : "POST",
                                success:function(result)
                                {
                                        $("#overlay").hide(300);
                                        if(parseInt($.trim(result)) > 0)
                                        {
                                                alert(data_updated);
                                                location.reload();
                                        }
                                        else
                                                alert(result);
                                }		

                    });

            });   
//                    $("#report").click(function()
//        {
//            var store = $("#store").val();
//            window.open('reports/openPalance.php?sd='+store, '_blank') ;
//        });
//            
            
            
            
            
            
});
               



        
    </script>
</body>
</html>