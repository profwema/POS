<?php
require_once("top.php");
require_once("redirection.php");

$language 	= $_SESSION['lang'];
$storeid	= $_SESSION['storeid'];
$enableNegative	= $_SESSION['enableNegative']

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
                                <a href="saleInvoice.php"><?=SALES?></a> >> <?=ADD_NEW?>
                            </h2>
                        </div>
			<?php
                        require_once("controller.php");
						    
			?>
                        <div class="box-content" id="printed">
                            <form class="form-horizontal" id="sales-form" method='POST'action="#">
                                <input type="hidden" name="f" value="addSale" >
                                <fieldset>
                                    <div class="control-group">
                                        <div class="salesOptions">
                                            <label class="control-label" for="typeahead"><?=INVOICE_NUMBER?> : </label>
                                            <div class="controls">
                                           <?php
                                           $query   = "SELECT invoicenumber
                                                    FROM sales
 
                                                    ORDER BY SUBSTRING(invoicenumber, -4) DESC
                                                    LIMIT 1";
                                            $res	= mysqli_query($adController->MySQL,$query) or die(mysqli_error($adController->MySQL));
                                            $num	= mysqli_num_rows($res);
                                            $data	= mysqli_fetch_assoc($res);
                                            if($num)
                                            {
                                                $rest = substr($data['invoicenumber'], -4);  
                                                $invoice = intval($rest)+1;
                                            }
                                            else 
                                                $invoice = 1; 
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
                                                       value='<?php echo date("Y-m-d")?>'>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <div class="salesOptions">
                                            <label class="control-label"><?=BRANCH?> :</label>
                                            <div class="controls">
                                                <select name="branch" id= 'branch' data-rel="chosen">
                                                        <?php
                                                        $query = "SELECT * FROM branches  ORDER BY name_en ASC";
                                                        $res   = mysqli_query($adController->MySQL,$query) or die(mysqli_error($adController->MySQL));
                                                        while($data= mysqli_fetch_assoc($res))
                                                        {
                                                            $id   = $data['id'];
                                                            $name = $data['name_'.$language];
                                                            if ($data['status']=='1') $sel= "selected";
                                                            echo "<option value='$id' $sel>$name</option>";
                                                        }
                                                        ?>
                                                </select>
                                                <script type="text/javascript">
                                                    $(document).ready(function()
                                                    {
                                                        var cid = $("#branch").val(); 
                                                        getStore(cid);
                                                    });                                                                      
                                                </script>
                                            </div>
                                        </div>                                        
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
                                                <input type="text" class=" typeahead"  name='document' id='document'>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="control-group" >
                                        <label class="control-label"><?=INVOICE_DETAILS?></label>
                                        <div class="controls" style="margin-top:20px; margin-left:0px">	
                                            <table class="span12 table table-striped table-bordered " style="width:100%">        
                                                <tr>
                                                    <TH><?=STORES?> </th>
                                                    <TH ><?=ITEM_NAME?></TH>    
                                                    <TH><?=BALANCE?></TH>
                                                    <TH ><?=QUANTITY?></TH>
                                                    <TH ><?=PRICE?></TH>
                                                    <TH ><?=DISCOUNT?> %</TH>
                                                    <TH ><?=TOTAL_A_DIS?> </TH>
                                                    <TH ><?=VAT?> %</TH>
                                                    <TH ><?=VAT_VALUE?></TH>
                                                    <TH ><?=TOTAL?></TH>
                                                    <TH>&nbsp;</TH>
                                                    <TH>&nbsp;</TH>
                                                </tr>
                                            <?php
                                            for($i = 0 ;$i < 10 ; $i++)
                                            {
//										
                                            ?>                                                    
                                                <tr <?=$rowStyle?> id='row-v-<?=$i?>'>
                                                    <td style="width:10%">
                                                        <select name="store[]" id='store-<?=$i?>' class="store" data-rel="chosen" style="width:100%">
                                                            <option value=''><?=SELECT_STORE?></option>
                                                        </select>
                                                    </td>  
                                                    <td style="width:20%">
                                                        
                                                        <select id='barcode-<?=$i?>' name="barcode[]" data-rel="chosen" class="barcode-select-sale" style="width:100%" >
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
                                                                echo "<option value = '$data[id]' "
                                                                           . "index = '$i' "
                                                                           . "enableNegative = '$enableNegative' "
                                                                           . "type  = '$data[item_type]' "
                                                                           . "vat   = '$data[vat]' "
                                                                           . "price = '$data[price]' "
                                                                           . "disc  = '$data[discount]' "
                                                                           . "> $barcode | $itemName | $unitName</option>";
                                                            }
                                                            ?>
                                                        </select>
                                                    </td>
  
                                                <script type="text/javascript">
//                                                    $(document).ready(function()
//                                                                   {
//                                                    var s = <?=$itemsListArray[$i]['store']?>;
//                                                    var index = <?=$i?>;
//                                                    getBalance(s,index);
//                                                      });  
                                                                    
                                                        </script>
                                                    
                                                    <td >
                                                            <input type="text" id='balance-<?=$i?>' class="item-qty span12 typeahead" maxlength="100"  name='balance[]' readonly>
                                                    </td>                                                    
                                                    <td >
                                                            <input type="text" id='item-qty-<?=$i?>' class="item-qty span12 typeahead" maxlength="100"  name='item_qty[]' >
                                                    </td>
                                                    <td >
                                                            <input type="text" id='item-price-<?=$i?>' class="item-price span12 typeahead" maxlength="100"  name='item_price[]'>
                                                    </td>
                                                    <td >
                                                            <input type="text" id='item-discount-<?=$i?>' class="item-discount span12 typeahead" maxlength="100"  name='item_disc[]'style="width :75%; "> %
                                                    </td>
                                                    <td >
                                                            <input type="text" id='item-total-after-dis-<?=$i?>' class="item-total-after-dis span12 typeahead" maxlength="100"  name='item_total_after_dis[]' readonly>
                                                    </td>
                                                    <td >
                                                        <input type="text" id='item-vat-<?=$i?>' class="item-vat span12 typeahead" maxlength="100"  name='item_vat[]' style="width :75%; " readonly> %
                                                    </td>
                                                    <td >
                                                        <input type="text" id='item-vat-value-<?=$i?>' class="item-vat-value span12 typeahead" maxlength="100"  name='item_vat_value[]' readonly>
                                                    </td>
                                                    <td >
                                                        <input type="text" id='item-total-<?=$i?>' class="item-total span12 typeahead" maxlength="100"  name='item-total[]'readonly>
                                                        <script type="text/javascript">
                                                                $(document).ready(function()
                                                                   {
                                                                       totalParshase(<?=$i?>);
                                                                    });   
                                                        </script>                                                    </td>
                                                    <td class='del-parchase-item mult-padding' prop='<?=$i?>'><label>x</label></td>
                                                    <td class='more-parchase-item mult-padding' prop='<?=$i?>'><label>+</label></td>
                                                </tr>
                                            <?php } ?>
                                            </table>
                                        </div>
                                    </div>                                  				
                                    <div class="control-group">
                                        <div class="salesOptions">
                                            <label class="control-label"><?=PAYMENT_TYPE?> :</label>
                                            <div class="controls">
                                                <table>
                                                    <tr >
                                                        <td >                                   
                                                            <table class="table-striped table-bordered " style="width:100%;"id="Box">                                                
                                                                <tr>
                                                                    <TH><?=CASH?> </th>
                                                                    <TH><?=CARD_PAYMENT?></TH>    
                                                                    <TH><?=SABAKAH?></TH>                     
                                                                    <TH><?=LEFT?></TH>
                                                                                                                                        

                                                                </tr>
                                                                <tr>
                                                                    <td >
                                                                        <input type="text" id='cash' class="span12 typeahead" maxlength="100" name='cash' >
                                                                    </td>
                                                                    <td >
                                                                        <input type="text" id='visa' class="span12 typeahead" maxlength="100" name='visa' >
                                                                     </td>
                                                                    <td >
                                                                        <input type="text" id='mada' class="span12 typeahead" maxlength="100" name='mada' > 
                                                                    </td> 
                                                                    <td >
                                                                        <input type="text" id='left' class="span12 typeahead" maxlength="100" name='left' readonly> 
                                                                    </td>  
                                                                </tr>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                </table>   
                                            </div>
                                        </div>                                          
                                        <div style="float: right; margin-right: 20px">
                                            <table class="table-bordered" >                                                
                                                <tr>
                                                    <TH colspan="2"><h4><?=TOTAlS?></h4></th>
                                                </tr>
                                                <tr>                                                        
                                                    <Td style="width:40%">اجمالى بدون ضريبة </td>
                                                    <Td  >
                                                        <input type="text" class="span12 typeahead" width="80%" id="total-before-vat" name="gross_total" value="0.00" readonly>
                                                    </Td>    
                                                </tr>
                                                <tr>
                                                    <Td> أجمالى قيمة الضريبة</td>
                                                    <Td >
                                                        <input type="text" class="span12 typeahead" id="total-vat-value" name="vat_all" value="0.00" readonly>
                                                    </Td>    
                                                </tr>
                                                <tr>
                                                    <Td>الاجمالى شامل الضريبة </td>
                                                    <Td>
                                                        <input type="text" class="span12 typeahead" id="total-after-vat" value="0.00"readonly>
                                                    </Td>    
                                                </tr>
                                                <tr>
                                                    <Td>خدمة توصيل </td>
                                                    <Td>                                                        								
                                                        <input type="text" class="span12 typeahead" name='deliver' id='deliver' value="0.00">
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
                                        <button type="submit"  class="btn btn-primary submit-sale"><?=SAVE?></button>
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
        var totalAll = 0;
        var total_before_vat = 0;
        var total_vat_value = 0;
        var total_after_vat = 0;    

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
            var totalAll = total_after_vat + parseFloat($("#deliver").val()) - parseFloat($("#final_discount").val());
            $("#totalAll").val ( totalAll.toFixed(2) ) ;
            var cash = totalAll.toFixed(2) - $("#visa").val () - $("#mada").val ();
            $("#cash").val ( cash.toFixed(2) ) ;
            var left = totalAll - $("#cash").val () - $("#visa").val () - $("#mada").val ();
            $("#left").val ( left.toFixed(2) ) ;
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

            $('.datepicker-nobar').datepicker({
                dateFormat: 'yy-mm-dd'
            });

     

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
                var totalAll = parseFloat($("#total-after-vat").val()) + parseFloat($("#deliver").val()) - parseFloat($("#final_discount").val());
                $("#totalAll").val ( totalAll.toFixed(2) ) ;
                var cash = totalAll - $("#visa").val () - $("#mada").val ();
                $("#cash").val ( cash.toFixed(2) ) ;
                var left = totalAll - $("#cash").val () - $("#visa").val () - $("#mada").val ();
                $("#left").val ( left.toFixed(2) ) ;

            });

            $("#deliver").change(function()
            {
                var totalAll = parseFloat($("#total-after-vat").val()) + parseFloat($("#deliver").val()) - parseFloat($("#final_discount").val());
                $("#totalAll").val ( totalAll.toFixed(2) ) ;
                var cash = totalAll - $("#visa").val () - $("#mada").val ();
                $("#cash").val ( cash.toFixed(2) ) ;
                var left = totalAll - $("#cash").val () - $("#visa").val () - $("#mada").val ();
                $("#left").val ( left.toFixed(2) ) ;
            });
           
            $("#cash").change(function()
            {
                var left = $("#totalAll").val () - $("#cash").val () - $("#visa").val () - $("#mada").val ();
                $("#left").val ( left.toFixed(2) ) ;
            });        
            $("#visa").change(function()
            {
                var left = $("#totalAll").val () - $("#cash").val () - $("#visa").val () - $("#mada").val ();
                $("#left").val ( left.toFixed(2) ) ;
            });  
            $("#mada").change(function()
            {
                var left = $("#totalAll").val () - $("#cash").val () - $("#visa").val () - $("#mada").val ();
                $("#left").val ( left.toFixed(2) ) ;
            });               
        });                
                    
    </script>
</body>
</html>
