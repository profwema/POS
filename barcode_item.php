<?php
require_once("top.php");
require_once("redirection.php");
require_once("controller.php");
$language 	= $_SESSION['lang'];
$storeid	= $_SESSION['storeid'];




$idval		= $adController->encrypt_decrypt(2,urldecode($_REQUEST['sd']),0);
$idval 		= $adController->encrypt_decrypt(2,$idval,0);
$idval2		= $adController->encrypt_decrypt(1,$_REQUEST['sd'],0);

$query		= "SELECT name_$language AS name FROM items WHERE item_thread='$idval'";
$res		= mysqli_query($adController->MySQL,$query);
$dataCat	= mysqli_fetch_assoc($res);
$numRows        = mysqli_num_rows($res);
$itemName       = $dataCat['name'];

$itemsListArray	= array();
$query		= "SELECT * FROM items WHERE  item_thread='$idval'";
$res45		= mysqli_query($adController->MySQL,$query);
while($dataItemsList	= mysqli_fetch_assoc($res45))
{
        $itemsListArray[count($itemsListArray)] = $dataItemsList;
}

$query		= "SELECT name_$language AS name FROM main_settings  ";
$resSet		= mysqli_query($adController->MySQL,$query);
$dataSet	= mysqli_fetch_assoc($resSet);
$storeName      = $dataSet['name'];




?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>WAM Tech Soft</title>
	<?php require_once("script_php_variables.php");?>
	<?php require_once("header.php");?>


</head>
<body>
    <?php require_once("header_top.php");?>	
    <div class="container-fluid-full">
        <div class="row-fluid">			
            <?php require_once("left_menu.php");?>
            <div id="content" class="span10">
                <div class="row-fluid sortable">
                    <div class="box span12">
                        <div class="box-header" data-original-title>
                            <h2><i class="halflings-icon edit"></i><span class="break"></span>
                                <a href="items.php"><?=ITEMS?></a> >> <?=BARCODE?> <?=$itemName;?>
                            </h2>  
                        </div>
                        <div class="box-content">
                            <form class="form-horizontal" method="POST" action="report-barcode.php" target="_blank">
                            
                                <input type="hidden" value="<?=$itemName;?>" name="name">
                                <!--<input type="hidden" value="print-barcode" name="f">-->
                                    <fieldset>
                                    <div class="control-group" id="multi-type"  <?=$multidisplayDiv?>>
                                        <div class="salesOptions" style="width:100%">                                                    
                                            <div class="controls" style="margin-top:20px; margin-left:0px">	
                                                <table class="span12 table-bordered" style="width:60%">                                                
                                                    <tr>
                                                        <TH class='mult-padding'align="left"width="30%"><?=UNITS?></TH>                                                                            
                                                        <TH class='mult-padding'align="left"width="30%"><?=BARCODE?></TH>
                                                        <TH class='mult-padding'align="left"width="20%"><?=PRICE?></TH> 
                                                        <TH class='mult-padding'align="left"width="20%"><?=QUANTITY?></TH>
                                                    </tr>
                                                        <?php
                                                        for($i = 0 ;$i < $numRows ; $i++)
                                                        {                                                              
                                                            $rowStyle = "style='display:block";
                                                            ?>
                                                    <tr <?=$rowStyle?> id='row-v-<?=$i?>'>
                                                    <input type="hidden" value="<?=$adController->encrypt_decrypt(1,$itemsListArray[$i]['id'],0)?>" name="itemsarray[]">
                                                    <td class='mult-padding'>
                                                        
                                                      <?php
                                                                $unit =  $itemsListArray[$i]['unit'];
                                                                $query = "SELECT name_$language AS name FROM units WHERE  id = '$unit'";
                                                                $resUnit		  = mysqli_query($adController->MySQL,$query);
                                                                $dataUnit                 = mysqli_fetch_assoc($resUnit);  
                                                                ?>
                                                        <input type="hidden" name="qty-id[]"  id='qtyid-<?=$i?>' value="<?=$itemsListArray[$i]['id']?>">                          
                                                        <input type="text" name="qty-unit[]"  id='qtyunit-<?=$i?>' value="<?=$dataUnit['name']?>"  class="barcode span12 typeahead" maxlength="100"  readonly>                          
                                                    </td>                                                                                        
                                                                                     
                                                    <td  class='mult-padding'>
                                                        <input type="text" name='qty-barcode[]'id='qty-barcode-<?=$i?>' value="<?=$itemsListArray[$i]['barcode']?>"  class="barcode span12 typeahead" maxlength="100"  readonly>
                                                    </td>
                                                    <td  class='mult-padding'>
                                                        <input type="text" name='qty-price[]' id='qty-prc-<?=$i?>' value="<?=$itemsListArray[$i]['price']?>" class="price span12 typeahead"   maxlength="5"  readonly >
                                                    </td>                                                    
                                                    <td  class='mult-padding'>
                                                        <input type="text" name='qty-fill[]' id='qty-fill-<?=$i?>' class="float-val span12 "   maxlength="5"  typeahead >
                                                    </td>
                                                    
                                                </tr>
                                                    <?php 
                                                        }                                                   
                                                        ?>
                                                </table>								  
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="form-actions">
                                        <button type="submit" id='print_barcode'  class="btn btn-primary"><?=PRINT_BARCODE?></button>
                                        <button type="reset" class="btn"><?=CANCEL?></button>
                                    </div>
                                    <p class='error-red'> </p>
                                </fieldset>
                            </form>   
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="clearfix"></div>
    
	<?php require_once("footer.php");?>
    

</body>
</html>
