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
                                                <a href="quotations.php"><?=QUOTATIONS?></a> >> <?=ADD_NEW?>
                                            </h2>
					</div>
                                    <div class="box-content" id="printed">
						<form class="form-horizontal" id="quote-form">
						  <input type="hidden" value="addQuote" name="f">
						  <fieldset>
							<div class="control-group">
                                                            <div class="salesOptions">
							  <label class="control-label" for="typeahead"><?=INVOICE_NUMBER?> : </label>
							  <div class="controls">
                                                               <?php
                                                               $query   = "SELECT invoicenumber
                                                                        FROM quote
                                                                                                                  
                                                                        ORDER BY invoicenumber DESC
                                                                        LIMIT 1";
                                                                $res	= mysqli_query($adController->MySQL,$query) or die(mysqli_error($adController->MySQL));
                                                                $num	= mysqli_num_rows($res);
                                                                $data	= mysqli_fetch_assoc($res);
                                                                if($num)
                                                                {
                                                                    $rest = substr($data['invoicenumber'], -4);  
                                                                    $trans_No = intval($rest)+1;
                                                                }
                                                                    
                                                                else 
                                                                    $trans_No = 1;
                                                                
                                                                //$rand           = rand(0,9999);
                                                                //$rand           =  str_pad($rand,4,"0",STR_PAD_LEFT);
                                                                //$ip             = str_replace(".","",$_SERVER['REMOTE_ADDR']);
                                                                $time           = time();
                                                                $invoice        = $storeid.'-'.$time.'-'.str_pad($trans_No,4,"0",STR_PAD_LEFT);; 
                                                               ?>
                                                              <input type="text" 
                                                                     class=" typeahead"  
                                                                     name='invoice' 
                                                                     id='invoice' 
                                                                     value ='<?php echo $invoice ?>'
                                                                     readonly>
								 <span class="help-inline email-un">&nbsp;</span>
							  </div>
							</div>  
                                                      
                                                            <div class="salesOptions">
								<label class="control-label"><?=INVOICE_DATE?></label>
								<div class="controls">
								  <input type="text" class="datepicker-nobar" name="purchase_expiry"  
                                                                         value='<?php echo date("d/m/Y")?>'>
								</div>
							</div>
                                                        </div>
                                                      <br>

							<div class="control-group">
                                                            <div class="salesOptions">

							  <label class="control-label" for="typeahead"><?=CUSTOMER_NAME?> : </label>
							  <div class="controls">
                                                                 <select name="customer" data-rel="chosen">
                                                                     <option value=''> </option>

									<?php
										$query      = "SELECT * FROM customers ";
										$res        = mysqli_query($adController->MySQL,$query) or die(mysqli_error($adController->MySQL));
										while($data = mysqli_fetch_assoc($res))
										{
											$id   = $data['id'];
											$name = $data['name_'.$language];
											echo "<option value='$id'>$name</option>";

										}
									?>
								  </select>
								 <span class="help-inline">&nbsp;</span>
                                                                 <a href='customers.php'class="linked" style=" padding-top: 20px;  margin-left: 40px ; "><?=ADD_EDIT_CUSTOMER?></a>
							  </div>
							</div>
                                                            <div class="salesOptions">
							  <label class="control-label" for="typeahead"><?=DOCUMENT?> : </label>
							  <div class="controls">
								 <input type="text" class=" typeahead"  name='supplier_invoice' id='supplier_invoice'>
							  </div>
							</div>
                                                        </div>
                                                      <br>
                                                        <div class="control-group">                                                                                                                       
                                                            <div class="salesOptions">

							  <label class="control-label"><?=BRANCH?> :</label>
							  <div class="controls">
								  <select name="branch" data-rel="chosen">
                                                                        <option value=''> </option>
									<?php
										$query = "SELECT * FROM branches  ORDER BY name_en ASC";
										$res   = mysqli_query($adController->MySQL,$query) or die(mysqli_error($adController->MySQL));
										while($data= mysqli_fetch_assoc($res))
										{
											$id   = $data['id'];
											$name = $data['name_'.$language];
											echo "<option value='$id'>$name</option>";

										}
									?>
								  </select>
							  </div>
                                                            </div>
							</div>      
                                                      <br>
                                    <div class="control-group" >
                                            <div class="controls" style="margin-top:20px; margin-left:0px">	
                                                <table  class=" table-bordered " style="width:100%">                                                
                                                                                    <tr>
                                                    <TH><?=STORES?> </th>
                                                    <TH ><?=BARCODE?>|<?=ITEM_NAME?>|<?=UNITS?></TH>    
                                                    <TH ><?=QUANTITY?></TH>
                                                    <TH ><?=QTY_AFTER?></TH>
                                                    <TH ><?=PRICE?></TH>
                                                    <TH ><?=DISCOUNT?> %</TH>
                                                    <TH ><?=TOTAL_A_DIS?> </TH>
                                                    <TH ><?=VAT?> %</TH>
                                                    <TH ><?=VAT_VALUE?></TH>
                                                    <TH ><?=TOTAL?></TH>
                                                    <TH class="edit">&nbsp;</TH>
                                                    <TH class="edit">&nbsp;</TH>
                                                </tr>
                                                <?php
                                                for($i = 0 ;$i < 10 ; $i++)
                                                {
    //										
                                                ?>
                                                <tr <?=$rowStyle?> id='row-v-<?=$i?>'>
                                                    
                                                    <td style="width:10%">
                                                        <select name="store[]" data-rel="chosen" style="width:100%" >
                                                      
                                                        <option value="">
                                                            
                                                        </option><?php
                                                        $query= "SELECT * FROM stores ";// id IN ( SELECT catid FROM items WHERE id IN (SELECT DISTINCT itemid FROM orders  $condition))";
                                                        $res        = mysqli_query($adController->MySQL,$query);
                                                        while($data     = mysqli_fetch_assoc($res))
                                                        {
                                                           $name    = $data["name_".$language];
                                                           if($data['default']=='1')
                                                                echo "<option value='$data[id]'selected >$name</option>";
                                                           else 
                                                               echo "<option value='$data[id]' >$name</option>";                                                         }
                                                        ?>
                                                    </select>
                                                    </td>
                                                    
                                                    
                                                    <td style="width:20%">
                                                        <select id='barcode-<?=$i?>' name="barcode[]" data-rel="chosen" class="barcode-select" style="width:100%" >
                                                            <option value=''> </option>
                                        <?php
                                        $name     = "name_".$_SESSION['lang'];
                                        $query   = "SELECT * FROM items  ";
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
                                                        echo "<option value='$i@@$data[id]@@$data[vat]@@$barcode@@$itemName@@$unitName'> $barcode | $itemName | $unitName</option>";

                                                }
                                        ?>
                                                        </select>
                                                    </td>

                                                    <td >
                                                            <input type="text" id='item-qty-<?=$i?>' class="item-qty span12 typeahead" maxlength="100"  name='item_qty[]' >
                                                    </td>
                                                    <td >
                                                            <input type="text" id='item-qty-after-<?=$i?>' class="item-qty-after span12 typeahead" maxlength="100"  name='item_qty_after[]' readonly>
                                                    </td>
                                                    <td >
                                                            <input type="text" id='item-price-<?=$i?>' class="item-price span12 typeahead" maxlength="100"  name='item_price[]'>
                                                    </td>
                                                    <td >
                                                            <input type="text" id='item-discount-<?=$i?>' class="item-discount span12 typeahead" maxlength="100"  name='item_disc[]'style="width :75%; "> 
                                                    </td>
                                                    <td >
                                                            <input type="text" id='item-total-after-dis-<?=$i?>' class="item-total-after-dis span12 typeahead" maxlength="100"  name='item_total_after_dis[]' readonly>
                                                    </td>
                                                    <td >
                                                        <input type="text" id='item-vat-<?=$i?>' class="item-vat span12 typeahead" maxlength="100"  name='item_vat[]' style="width :75%; " > 
                                                    </td>
                                                    <td >
                                                        <input type="text" id='item-vat-value-<?=$i?>' class="item-vat-value span12 typeahead" maxlength="100"  name='item_vat_value[]' readonly>
                                                    </td>
                                                    <td >
                                                            <input type="text" id='item-total-<?=$i?>' class="item-total span12 typeahead" maxlength="100"  name='item-total[]'readonly>
                                                                                                                        <script type="text/javascript">
                                                                $(document).ready(function()
                                                                   {
                                                                        //totalParshase(<?=$i?>);
                                                                    });   
                                                            </script>
                                                    </td>
                                                    <td class='del-parchase-item mult-padding edit' prop='<?=$i?>'><label>x</label></td>
                                                    <td class='more-parchase-item mult-padding edit' prop='<?=$i?>'><label>+</label></td>
                                            </tr>
                                                <?php } ?>
                                          </table>
                                        </div>
                                    </div>                                  				
                                  
                                    <div class="control-group">
                                        <div style="margin-top:20px; float: right; margin-right: 20px">
                                            
                                            	
                                                <table class=" table-bordered " >                                                
                                                     <tr>
                                                         <TH colspan="2"><h4><?=TOTAlS?></h4></th>
                                                     </tr>
                                                    <tr>
                                                        
                                                        <Td style="width:40%">اجمالى بدون ضريبة </td>
                                                    <Td  >
                                                        <input type="text" class="span12 typeahead" width="80%" id="total-before-vat" value="0.00" readonly>
                                                        </Td>    
                                                    </tr>
                                                    <tr>
                                                    <Td> أجمالى قيمة الضريبة</td>
                                                    <Td >
                                                        <input type="text" class="span12 typeahead" id="total-vat-value" value="0.00" readonly>
                                                        </Td>    
                                                    </tr>
                                                    <tr>
                                                    <Td>الاجمالى شامل الضريبة </td>
                                                    <Td>
                                                        <input type="text" class="span12 typeahead" id="total-after-vat" value="0.00"readonly>
                                                        </Td>    
                                                    </tr>
                                                    <tr>
                                                    <Td>خصم اضافي </td>
                                                    <Td>                                                        								
                                                        <input type="text" class="span12 typeahead" name='final_discount' id='final_discount' value="0.00">

                                                    </Td>    
                                                    </tr>
                                                    <tr style="font-size: 16px ">
                                                        <Td style=" padding: 10px; ">الأجمالى النهائي</td>
                                                    <Td>
                                                        <input type="text" class="span12 typeahead" name="totalAll" id="totalAll" value="0.00"readonly>
                                                       </Td>    
                                                    </tr>
                                                </table>
                                            
                                        </div>
                                    </div>
							<div class="form-actions">
                                                            <button type="button" class="btn btn-primary" onclick="PrintDiv();"><i class="fa fa-print"></i> Print</button>
							  <button type="button" id='submit-quote' class="btn btn-primary"><?=SAVE?></button>
							  <button type="reset" class="btn"><?=CANCEL?></button>
							</div>
							<p class='error-red'></p>

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
             
    function PrintDiv() {
        document.title = "Quotation";
        window.print();

    }
function totalParshase(id)
{
        // بيجسب اجمالى الكمية فى السعر
       var preTotal =  $("#item-qty-"+id).val() * $("#item-price-"+id).val();
       // بيحسب مقدار الخصم
       var discount = preTotal * $("#item-discount-"+id).val() / 100;
       // بيحسب الاجمالى بعد الخصم ويعرضه
       var disTotal = preTotal - discount;
       $("#item-total-after-dis-"+id).val( disTotal.toFixed(2) );
 
       //-----------------------
       // بيحسب قيمة الضريبة ويعرضها 
       var vaTax    = disTotal * $("#item-vat-"+id).val() / 100;
       $("#item-vat-value-"+id).val( vaTax.toFixed(2) );

       //-----------------------
       // بيحصب الاجمالى الاخير للصنف 
       var total    = disTotal + vaTax ;
       $("#item-total-"+id).val( total.toFixed(2) );   

       // يحسب الاجمالى فى الاسفل
        var total_before_vat = 0;
        var total_vat_value = 0;
        var total_after_vat = 0;
    for ( var i = 0 ; i<10; i++)
    {
        if ( $("#item-qty-"+i).val() != '')
        {
            //alert(i);
            total_before_vat = total_before_vat + parseFloat( $("#item-total-after-dis-"+i).val()); 
            total_vat_value = total_vat_value + parseFloat( $("#item-vat-value-"+i).val()); 
            total_after_vat = total_after_vat + parseFloat( $("#item-total-"+i).val()); 
            
        //alert(total_before_vat);
        }
        
    }
    $("#total-before-vat").val( total_before_vat.toFixed(2) ) ;
    $("#total-vat-value").val( total_vat_value.toFixed(2) ) ;
    $("#total-after-vat").val( total_after_vat.toFixed(2) ) ;
    // بيحسب الاجمالى النهائى للكل فى الاسفل باضافه الاجمالى شامل الضريبه على الخصم الاضافي
    var totalAll = total_after_vat - parseFloat($("#final_discount").val());
    $("#totalAll").val ( totalAll.toFixed(2) ) ;
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

            $(".item-qty").change(function()
            {
                var id = $(this).attr('id').replace('item-qty-','');
                totalParshase(id);
            });

            $(".item-price").change(function()
            {
                var id = $(this).attr('id').replace('item-price-','');
                totalParshase(id);
            });

            $(".item-discount").change(function()
            {
                var id = $(this).attr('id').replace('item-discount-','');
                totalParshase(id);
            });
            
            $(".item-vat").change(function()
            {
                var id = $(this).attr('id').replace('item-vat-','');
                totalParshase(id);
            });   
            
        $("#final_discount").change(function()
            {
                var totalAll = parseFloat($("#total-after-vat").val()) - parseFloat($("#final_discount").val());
                $("#totalAll").val ( totalAll.toFixed(2) ) ;
            });
        });
                

                    
    </script>
</body>
</html>
