<?php
require_once("top.php");
require_once("redirection.php");
require_once("controller.php");
$language 	= $_SESSION['lang'];
$storeid	= $_SESSION['storeid'];
?>
<!DOCTYPE html>
<html lang="en">
    <head>
	<meta charset="utf-8">
	<title>WAM Tech Soft</title>
	<?php require_once("script_php_variables.php");?>
	<?php require_once("header.php");?>	
        <style>
            h4
            {
                margin-left: 20px;
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
                                            <h2><i class="halflings-icon edit"></i><span class="break"></span>
                                                <a href="items.php"><?=ITEMS?></a> >> <?=ADD_NEW?>
                                            </h2>                  
                        </div>
                        <div class="box-content">
                            <form class="form-horizontal" id="add-item" enctype="multipart/form-data">
                                <input type="hidden" value="addMultiItem" name="f">
                                <fieldset>                                    
                                    <div class="control-group">        
                                        <div class="salesOptions">
                                            <label class="control-label" for="typeahead"><?=ITEM_NAME?> <?=LANGUAGE_1?> : </label>
                                            <div class="controls">
                                                <input type="text" class="span6 typeahead"  name='name' id='name' style="width:80%"> &nbsp; *
                                                <span class="help-inline">&nbsp;</span>
                                            </div>
                                        </div>
                                        <div class="salesOptions">
                                            <label class="control-label" for="typeahead"><?=ITEM_NAME?> <?=LANGUAGE_2?> : </label>
                                            <div class="controls">
                                                <input type="text" class="span6 typeahead"  name='name_ar'  id='name_ar' style="width:80%">
                                                <span class="help-inline">&nbsp;</span>
                                            </div>
                                        </div> 
                                    </div> 
                                    <hr>
                                        <?php //
                                        if($store_type_ == "2")
                                        {
                                            ?>                                                           
                                    <div class="control-group">
                                        <label class="control-label"><?=EMPLOYEES?> :</label>
                                        <div class="controls" id="cat-list-data">
                                            <select name="service[]" id='service' data-rel="chosen"  multiple data-rel="chosen">
                                                <?php										
                                                $output = array();
                                                $query 	= "SELECT id,name_$language FROM employees WHERE  type='7'";
                                                $res   	= mysqli_query($adController->MySQL,$query) or die(mysqli_error($adController->MySQL));
                                                while($data= mysqli_fetch_assoc($res))
                                                {
                                                    $id   = $data['id'];
                                                    $name = $data['name_'.$language];
                                                    $output[count($output)] = "<option value='$id'>$name</option>";
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
                                        <div class="salesOptions" >
                                            <label class="control-label"><?=CATEGORY?> :</label>
                                            <div class="controls" id="cat-list-data">
                                                <select  name="cat"  id='cat'data-rel="chosen" style="width:80%">
                                                    <option value=''>Select an Option </option> 
                                                    <?php
                                                    $query 	= "SELECT * FROM categories  ORDER BY name_en ASC";
                                                    $res   	= mysqli_query($adController->MySQL,$query) or die(mysqli_error($adController->MySQL));
                                                    while($data= mysqli_fetch_assoc($res))
                                                    {
                                                        $id   = $data['id'];
                                                        $name = $data['name_'.$language];
                                                        $output[count($output)] = "<option value='$id'>$name</option>";
                                                    }
                                                    echo implode(" ",$output);
                                                    ?>
                                                </select>
                                            </div>
                                        </div>    
                                        <div class="salesOptions">
                                            <label class="control-label"><?=ENABLED?></label>
                                            <div class="controls">
                                                <label class="checkbox inline">
                                                    <input type="checkbox" value="1" name="enabled" checked="true">
                                                </label>
                                            </div>
                                        </div>                                                                                
                                    </div>

                                    <div class="control-group">
                                        <div class="salesOptions">
                                            <label class="control-label"><?=ITEM_TYPE?> :</label>
                                            <div class="controls" id="cat-list-data">
                                                <select name="Itype"  id='Itype' data-rel="chosen"style="width:80%"> 
                                                        <option value='0'><?=COMMODITY?>  </option>
                                                        <option value='1'><?=SERVICE?>  </option>
                                                </select>
                                            </div>
                                        </div>   
                                        <div class="salesOptions">
                                            <label class="control-label"><?=QTY_TYPE?> :</label>
                                            <div class="controls" id="cat-list-data">
                                                <select name="Qtype"  id='Qtype' data-rel="chosen"style="width:80%"> 
                                                        <option value='0'><?=COUNT?>  </option>
                                                        <option value='1'><?=WEIGHT?>  </option>
                                                </select>
                                            </div>
                                        </div>                                          
                                    </div>                                    
                                    <hr>
                                    <div class="control-group">
                                        <div class="salesOptions" style="width:30%"  >
                                            <label class="control-label" for="typeahead"><?=VAT?> : </label>
                                            <div class="controls">
                                                <input type="text" class="span6 typeahead"  name='vat' id='vat' style="width:75%"> *
                                                
                                            </div>
                                        </div>
                                        
                                        <div class="salesOptions" style="width:30%" >
                                            <label class="control-label" for="typeahead"><?=SELT?> : </label>
                                            <div class="controls">
                                                <input type="text" class="span6 typeahead"  name='selt'  id='selt' style="width:75%">
                                                
                                            </div>
                                        </div> 
                                        
                                        <div class="salesOptions" style="width:30%" >
                                            <label class="control-label"><?=PRICETAX?></label>
                                            <div class="controls">
                                                <label class="checkbox inline">
                                                    <input type="checkbox" value="1" name="taxInPrice">
                                                </label>
                                            </div>
                                        </div> 
                                    </div>   
                                    <hr>
                                    
                                    <div class="control-group">    
                                        <div class="salesOptions">
                                        <label class="control-label" for="typeahead"><?=DISCRIPTION?> : </label>
                                        <div class="controls">
                                            <textarea class="span6 typeahead"  style="width: 90%" name="disc" id="disc"  rows="10"  required='required'></textarea>                                            
                                        </div>                                  
                                    </div>   
                                    <div class="salesOptions">                                   
                                        <label class="control-label" for="typeahead"><?=IMAGE?> : </label>
                                        <div class="controls">
                                            <input type="text" class="span6 typeahead"  name='photo'  id='photo' style="width:80%">
                                        </div>                                  
                                    </div>    
                                    </div>
                                    <hr>
                                    <div class="control-group" >     
                                        <div class="salesOptions" style="width:100%">                                                    
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
                                                            ?>                                                                                   
                                                    <tr <?=$rowStyle?> id='row-v-<?=$i?>'>
                                                        <td class='mult-padding'>                                                                                                                                                                                    
                                                            <select name="qty-unit[]"  id='qtyunit-<?=$i?>'data-rel="chosen" class="chzn-select" style="width:100%">        
                                                                <option value=''></option> 
                                                                 <?php
                                                                 $query = "SELECT * FROM units  ORDER BY name_$language ASC";
                                                                 $resUnit = mysqli_query($adController->MySQL,$query) or die(mysqli_error($adController->MySQL));
                                                                 $defult='';
                                                                 while($dataUnit= mysqli_fetch_assoc($resUnit))
                                                                 {
                                                                     $id   = $dataUnit['id'];
                                                                     $name = $dataUnit['name_'.$language];
                                                                     $defult='';                                                                                                               
                                                                     if( $dataUnit['default']==1 && $i== 0 )
                                                                         $defult=" selected ";
                                                                     echo "<option value='$id' $defult >$name</option>";
                                                                 }                                                                                                       
                                                                 ?>
                                                            </select>
                                                        </td>
                                                        <td class='mult-padding'>
                                                            <input type="number" name='qty-fill[]' id='unit-fill-<?=$i?>' value='<?php if($i==0) echo 1;?>' min="0" step="1" class="span12 typeahead" maxlength="100"  >
                                                        </td>
                                                        <td class='mult-padding'>
                                                            <input type="text" name='qty-barcode[]'id='qty-barcode-<?=$i?>' class="span12 typeahead" maxlength="100"  >
                                                        </td>
                                                        <td class='mult-padding'>
                                                            <input type="text" name='qty-price[]' id='qty-prc-<?=$i?>'   class="price float-val span12 typeahead"  maxlength="5"  >
                                                        </td>											                                           
                                                        <td class='mult-padding'>
                                                            <input type="text" name='qty-discount[]' id='discount-<?=$i?>'  class="discount float-val span12 typeahead" maxlength="5" >
                                                        </td>
                                                        <td class='mult-padding'>
                                                            <input type="text" name='qty-discountPer[]' id='discountPer-<?=$i?>' class="discountPer float-val span12 typeahead" maxlength="5"  >
                                                        </td>         
                                                        <td class='mult-padding'>
                                                            <input type="text" name='qty-priceDis[]' id='qty-priceDis-<?=$i?>'  class="float-val span12 typeahead"  maxlength="5"    readonly>
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
                                        <button type="button" id='submit-item' class="btn btn-primary"><?=SAVE?></button>
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
