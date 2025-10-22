<?php
require_once("top.php");
require_once("redirection.php");
require_once("controller.php");
$language 	= $_SESSION['lang'];
$storeid	= $_SESSION['storeid'];

$idval		= $adController->encrypt_decrypt(2,urldecode($_REQUEST['sd']),0);
$idval 		= $adController->encrypt_decrypt(2,$idval,0);
$idval2		= $adController->encrypt_decrypt(1,$_REQUEST['sd'],0);
        
$itemsListArray	= array();

$query		= "SELECT * FROM items WHERE storeid='$storeid' AND item_thread='$idval'";
$res		= mysqli_query($adController->MySQL,$query);
$dataCat	= mysqli_fetch_assoc($res);
$numRows        = mysqli_num_rows($res);

$servicesEmp    = explode(",",$dataCat['services']);
$hashofItem		= $idval;

$query		= "SELECT * FROM items WHERE storeid='$storeid' AND item_thread='$hashofItem'";
$res45		= mysqli_query($adController->MySQL,$query);
while($dataItemsList	= mysqli_fetch_assoc($res45))
{
        $itemsListArray[count($itemsListArray)] = $dataItemsList;
}

//$query 		= "SELECT * FROM images WHERE foreign_id='$dataCat[id]' AND `table`='items'";
//$resImage	= mysqli_query($adController->MySQL,$query);
//$dataImage	= mysqli_fetch_assoc($resImage);
//
//$img		= $adController->getDirectoryOnlyPath(DIR_ITEM_NAME);
//$thumb		= $img.$dataImage['thumb'];

//if($data['store_branch']!="")						
//	$branchArray	= explode(",",$dataCat['store_branch']);
//else
//{
//	$query		  = "SELECT GROUP_CONCAT(id) AS br FROM branches WHERE storeid='$storeid'";
//	$resBranch	  = mysqli_query($adController->MySQL,$query);
//	$dataBranch 	  = mysqli_fetch_assoc($resBranch);
//	$branchArray	  = explode(",",$dataBranch['br']);
//}

$catArray	= explode(",",$dataCat['catid']);
$enabled	= $dataCat['enabled'];
$Itype  	= $dataCat['item_type'];
$Qtype  	= $dataCat['measurement'];
$taxInPrice	= $dataCat['taxInPrice'];
$show_to_cash	= $dataCat['show_to_cashier'];
$is_service	= $dataCat['is_service'];
$expiry		= $dataCat['expiry_notification'];
$expiry_date	= date("m/d/Y",$dataCat['expiry_date']);

if($show_to_cash=="1")
	$show_to_cash="checked='true'";
else
	$show_to_cash="";

if($enabled=="1")
	$enabled="checked='true'";
else
	$enabled="";

if($taxInPrice=="1")
	$taxInPrice="checked='true'";
else
	$taxInPrice="";

if($is_service=="1")
	$is_service="checked='true'";
else
	$is_service="";

if($expiry=="1")
	$expiry="checked='true'";
else
	$expiry="";

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
                                                <a href="items.php"><?=ITEMS?></a> >> <?=EDIT?>
                                            </h2>                               
                        </div>
                        <div class="box-content">
                            <form class="form-horizontal" id="add-item">
                                <input type="hidden" value="<?=$idval2?>" name="nd">
                                <input type="hidden" value="updateMultiItem" name="f">
                                <fieldset>
                                    <div class="control-group">
                                        <div class="salesOptions">
                                            <label class="control-label" for="typeahead"><?=ITEM_NAME?> <?=LANGUAGE_1?> : </label>
                                            <div class="controls">
                                                <input type="text" class="span6 typeahead"  name='name' id='name' value="<?=$dataCat['name_en']?>" style="width:80%"> &nbsp; *
                                                <span class="help-inline">&nbsp;</span>
                                            </div>
                                        </div>                            
                                        <div class="salesOptions">    
                                            <label class="control-label" for="typeahead"><?=ITEM_NAME?> <?=LANGUAGE_2?> : </label>
                                            <div class="controls">
                                                <input type="text" class="span6 typeahead"  name='name_ar'  id='name_ar' value="<?=$dataCat['name_ar']?>"  style="width:80%">
                                                <span class="help-inline">&nbsp;</span>
                                            </div>
                                        </div>
                                    </div>    
                                    <hr>
                            <?php
                            if($store_type_ == "2")
                            {                                                        
                                ?>                                                           
                                    <div class="control-group">
                                        <label class="control-label"><?=EMPLOYEES?> :</label>
                                        <div class="controls" id="cat-list-data">
                                            <select name="service[]" id='service' data-rel="chosen"  multiple data-rel="chosen">
									
                                                <?php										
                                                $output = array();
                                                $query 	= "SELECT id,name_$language FROM employees WHERE storeid='$storeid' AND type='7'";
                                                $res   	= mysqli_query($adController->MySQL,$query) or die(mysqli_error($adController->MySQL));
                                                while($data= mysqli_fetch_assoc($res))
                                                {
                                                    $id   = $data['id'];
                                                    $name = $data['name_'.$language];                                                    
                                                    $sel  = "";
                                                    if(in_array($data['id'], $servicesEmp))
                                                            $sel  = " selected='true' ";                                                                                        
                                                    $output[count($output)] = "<option value='$id' $sel>$name</option>";								
                                                }
                                                echo implode(" ",$output);
                                                $output  = array();
                                                ?>
                                            </select>
                                        </div>
                                    </div>   
                                    <hr>                                   
                                    <?php
                                    }
                                    ?> 
                                    <div class="control-group">
                                        <div class="salesOptions">
                                            <label class="control-label"><?=CATEGORY?> :</label>
                                            <div class="controls" id="cat-list-data">
                                                <select name="cat"  id='cat' data-rel="chosen"width:80%"> 
                                                    <?php
                                                    $query 	= "SELECT * FROM categories WHERE storeid='$storeid' ORDER BY name_en ASC";
                                                    $res   	= mysqli_query($adController->MySQL,$query) or die(mysqli_error($adController->MySQL));
                                                    while($data= mysqli_fetch_assoc($res))
                                                    {
                                                        $id   = $data['id'];
                                                        $name = $data['name_'.$language];
                                                        $selected	="";
                                                        if(in_array($data['id'],$catArray))
                                                                $selected=" selected='true' ";
                                                        $output[count($output)] = "<option value='$id' $selected>$name</option>";																		
                                                    }
                                                    echo implode(" ",$output);
                                                    ?>
                                                </select> &nbsp; *
                                            </div>
                                        </div> 
                                        <div class="salesOptions">
                                            <label class="control-label"><?=ENABLED?></label>
                                            <div class="controls">
                                                <label class="checkbox inline">
                                                    <input type="checkbox" value="1" name="enabled" <?=$enabled?>>
                                                </label>
                                            </div>
                                        </div>	                                        
                                    </div>
                                   <div class="control-group">
                                        <div class="salesOptions">
                                            <label class="control-label"><?=ITEM_TYPE?> :</label>
                                            <div class="controls" id="cat-list-data">
                                                <select name="Itype"  id='Itype' data-rel="chosen"style="width:80%"> 
                                                        <option value='0' <?php if($Itype==0) echo 'selected'?> > <?=COMMODITY?>  </option>
                                                        <option value='1' <?php if($Itype==1) echo 'selected'?> > <?=SERVICE?>  </option>
                                                </select>
                                            </div>
                                        </div>   
                                        <div class="salesOptions">
                                            <label class="control-label"><?=QTY_TYPE?> :</label>
                                            <div class="controls" id="cat-list-data">
                                                <select name="Qtype"  id='Qtype' data-rel="chosen"style="width:80%"> 
                                                        <option value='0' <?php if($Qtype==0) echo 'selected'?> > <?=COUNT?>  </option>
                                                        <option value='1' <?php if($Qtype==1) echo 'selected'?> > <?=WEIGHT?>  </option>
                                                </select>
                                            </div>
                                        </div>                                          
                                    </div>                                    
                                    <hr>
                                    <div class="control-group">
                                        <div class="salesOptions"  style="width:30%">
                                            <label class="control-label" for="typeahead"><?=VAT?> : </label>
                                            <div class="controls">
                                                <input type="text" class="span6 typeahead"  name='vat' id='vat'value="<?=$dataCat['vat']?>" style="width:75%"> *
                                                
                                            </div>
                                        </div>
                                        
                                        <div class="salesOptions" style="width:30%">
                                            <label class="control-label" for="typeahead"><?=SELT?> : </label>
                                            <div class="controls">
                                                <input type="text" class="span6 typeahead"  name='selt' value="<?=$dataCat['selt']?>"  id='selt' style="width:80%">
                                                
                                            </div>
                                        </div> 
                                        
                                        <div class="salesOptions" style="width:30%">
                                            <label class="control-label"><?=PRICETAX?></label>
                                            <div class="controls">
                                                <label class="checkbox inline">
                                                    <input type="checkbox" value="1" name="taxInPrice" <?=$taxInPrice?>>
                                                </label>
                                            </div>
                                        </div> 
                                    </div>   
                                    <hr>
                                    <div class="control-group"> 
                                        <div class="salesOptions"style="width:45%">

                                        <label class="control-label" for="typeahead"><?=DISCRIPTION?> : </label>
                                        <div class="controls">
                                            <textarea class="span6 typeahead"  style="width: 90%" name="disc" id="disc" rows="10"  required='required'><?=$dataCat['disc']?></textarea>                                            
                                        </div>                                  
                                    </div>  
                                        <div class="salesOptions"style="width:45%">
                                        <label class="control-label" for="typeahead"><?=IMAGE?> : </label>
                                        <div class="controls">
                                            <input type="text" class="span6 typeahead"  name='photo'  id='photo'style="width:80%">
                                        </div>                                  
                                        <div class="controls">
                                            <input type="hidden" value="<?=$dataCat['image']?>" name="oldPhoto">
                                            <img src='<?=$dataCat['image']?>' class='image'>       
                                        </div>                                  
                                    </div>
                                    </div>
                                    <hr>
                                    
                                    <div class="control-group">
                                        <div class="salesOptions" style="width:100%">                                                    
                                            <h4><?=QUANTITY?></h4>
                                            <div class="controls" style="margin-top:20px; margin-left:0px">	
                                                <table class="span12 table-bordered" style="width:98%">                                                
                                                    <tr>
                                                        <TH class='mult-padding'align="left"width="16%"><?=UNITS?></TH>                                                                            
                                                        <TH class='mult-padding'align="left"width="10%"><?=QTY_VAL?></TH>
                                                        <TH class='mult-padding'align="left"width="14%"><?=BARCODE?></TH>
                                                        <TH class='mult-padding'align="left"width="14%"><?=PRICE?></TH>                                                                           
                                                        <TH class='mult-padding'align="left"width="14%"><?=DISCOUNT_VALUE?></TH>
                                                        <TH class='mult-padding'align="left"width="14%"><?=DISCOUNT_RASHIO?> %</TH>
                                                        <TH class='mult-padding'align="left"width="15%"><?=PRICE_WITH_DISCOUNT?></TH>
                                                        <TH>&nbsp;</TH>
                                                        <TH>&nbsp;</TH>
                                                    </tr>
                                                        <?php
                                                        for($i = 0 ;$i < 10 ; $i++)
                                                        {
//                                                            if($i == 0 )
//                                                                $rowStyle = "";
//                                                            else
//                                                            {
                                                                if($i < $numRows )
                                                                    //if($itemsListArray[$i]['qty_en'] != "")
                                                                    $rowStyle = "style='display:block";
                                                                else
                                                                    $rowStyle = "";											
//                                                            }
                                                            ?>
                                                    <tr <?=$rowStyle?> id='row-v-<?=$i?>'>
                                                    <input type="hidden" value="<?=$adController->encrypt_decrypt(1,$itemsListArray[$i]['id'],0)?>" name="itemsarray[]">
                                                    <td class='mult-padding'>                                                                                                                   
                                                        <select name="qty-unit[]"  id='qtyunit-<?=$i?>'data-rel="chosen" class="chzn-select" style="width:100%">        
                                                            <option value=''> </option>                                                                                                                          
                                                                <?php
                                                                $query = "SELECT * FROM units WHERE storeid='$storeid' ORDER BY name_$language ASC";
                                                                $resUnit = mysqli_query($adController->MySQL,$query) or die(mysqli_error($adController->MySQL));
                                                                $defult='';

                                                                while($dataUnit= mysqli_fetch_assoc($resUnit))
                                                                {
                                                                    $id   = $dataUnit['id'];
                                                                    $name = $dataUnit['name_'.$language];
                                                                    $defult='';
                                                                    if ($itemsListArray[$i]['unit']== $id )
                                                                    {
                                                                        $defult=" selected ";
                                                                    }                                                                                                                                        
                                                                    echo "<option value='$id' $defult >$name</option>";
                                                                }
                                                               
                                                                ?>
                                                        </select>                                                                                            
                                                    </td>                                                                                        
                                                    <td class='mult-padding'>
                                                        <input type="number" name='qty-fill[]' id='unit-fill-<?=$i?>' value = '<?=$itemsListArray[$i]['intimate_stock']?>' min="0" step="1" class="span12 typeahead" maxlength="100"  >
                                                    </td>                                                                                        
                                                    <td  class='mult-padding'>
                                                        <input type="text" name='qty-barcode[]'id='qty-barcode-<?=$i?>' value="<?=$itemsListArray[$i]['barcode']?>"  class="span12 typeahead" maxlength="100"  >
                                                    </td>
                                                    <td  class='mult-padding'>
                                                        <input type="text" name='qty-price[]' id='qty-prc-<?=$i?>'  class="price float-val span12 typeahead" value="<?=$itemsListArray[$i]['price']?>"  maxlength="5"   >
                                                    </td>
                                                    <td  class='mult-padding'>
                                                        <input type="text" name='qty-discount[]' id='discount-<?=$i?>' class="discount float-val span12 typeahead" maxlength="100" value="<?=flout_format($itemsListArray[$i]['discount']*$itemsListArray[$i]['price']/100)?>">
                                                    </td>
                                                    <td  class='mult-padding'>
                                                        <input type="text"name='qty-discountPer[]' id='discountPer-<?=$i?>' class="discountPer float-val span12 typeahead" maxlength="100" value="<?=$itemsListArray[$i]['discount']?>" >
                                                    </td>
                                                    <td  class='mult-padding'>
                                                        <input type="text" name='qty-priceDis[]' id='qty-priceDis-<?=$i?>'  class="float-val span12 typeahead" value="<?=flout_format($itemsListArray[$i]['price']-($itemsListArray[$i]['discount']*$itemsListArray[$i]['price']/100))?>" maxlength="5"  readonly>
                                                    </td>                                                                                          
                                                    <td class='del-qty mult-padding' prop='<?=$i?>'><label>x</label></td>
                                                    <td class='more-qty mult-padding' prop='<?=$i?>'><label>+</label></td>
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
                                        <button type="button" id='submit-item' prop='e' class="btn btn-primary"><?=SAVE?></button>
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
    
    <script type="text/javascript">
$(document).ready(function()
{
    $("#price").blur(function()
    {			 

        if ($("#price").val()==0 || $("#price").val()=='')
        {
            $("#price").val(0);
			$("#priceDis").val(0);
            $("#discount").val(0);
            $("#discountPer").val(0);
			
        }	 	
        if ($("#discount").val() > 0)
		{
            $("#discountPer").val(($("#discount").val()/$("#price").val()*100).toFixed(2));    
			$("#priceDis").val( $("#price").val() - $("#discount").val() );			  
		}
        else if ($("#discountPer").val() > 0 )
		{
            $("#discount").val(($("#discountPer").val()/100*$("#price").val()).toFixed(2));
			$("#priceDis").val( $("#price").val() - $("#discount").val() );			  

		}
            
    })

    $("#discount").blur(function()
    {
        if ($(this).val()=='' || $("#price").val()=='')
        {
            $(this).val(0);
            $("#discountPer").val(0);
        }
        else						
            $("#discountPer").val(($(this).val()/$("#price").val()*100).toFixed(2));
			
		$("#priceDis").val( $("#price").val() - $("#discount").val() );			  
    });

    $("#discountPer").blur(function()
    {
    if ($(this).val()=='' || $("#price").val()=='')
    {
        $(this).val(0);
        $("#discount").val(0);
    }
    else						
        $("#discount").val(($(this).val()*$("#price").val()/100).toFixed(2) );

	$("#priceDis").val( $("#price").val() - $("#discount").val() );			  
    });
    
    
    
    $(".price").blur(function()
    {		 
        var id = $(this).attr('id').replace('qty-prc-','');
           
        if ($(this).val()==0 || $(this).val()=='')
        {
            $(this).val(0);
			$("#qty-priceDis-"+id).val(0);
            $("#discount-"+id).val(0);
            $("#discountPer-"+id).val(0);
        }	 	
        if ($("#discount-"+id).val() > 0 )
		{
            $("#discountPer-"+id).val(($("#discount-"+id).val()/$(this).val()*100).toFixed(2));
			$("#qty-priceDis-"+id).val( $(this).val() - $("#discount-"+id).val() );			  
			
		}

        else if ($("#discountPer-"+id).val() > 0 )
		{
            $("#discount-"+id).val(($("#discountPer-"+id).val()/100*$(this).val()).toFixed(2));
			$("#qty-priceDis-"+id).val( $(this).val() - $("#discount-"+id).val() );			  
		}
    })

    $(".discount").blur(function()
    {
        var id = $(this).attr('id').replace('discount-','');
        
        if ($(this).val()=='' || $("#qty-prc-"+id).val()=='')
        {
            $(this).val(0);
            $("#discountPer-"+id).val(0);
        }
        else						
            $("#discountPer-"+id).val(($(this).val()/$("#qty-prc-"+id).val()*100).toFixed(2));
		$("#qty-priceDis-"+id).val( $("#qty-prc-"+id).val() - $("#discount-"+id).val() );			  
			
    });

    $(".discountPer").blur(function()
    {
        var id = $(this).attr('id').replace('discountPer-','');
    if ($(this).val()=='' || $("#qty-prc-"+id).val()=='')
    {
        $(this).val(0);
        $("#discount-"+id).val(0);
    }
    else						
        $("#discount-"+id).val(($(this).val()*$("#qty-prc-"+id).val()/100).toFixed(2) );
	$("#qty-priceDis-"+id).val( $("#qty-prc-"+id).val() - $("#discount-"+id).val() );			  
    });

});

</script>
</body>
</html>
