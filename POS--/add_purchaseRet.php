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
                            <h2><i class="halflings-icon edit"></i><span class="break"></span><a href="purchaseRet.php"><?=RETURNED_PURCHASES?></a> >> <?=ADD_NEW?></h2>
                        </div>
                        <div class="box-content" id="printed">
                            <form class="form-horizontal" >
                                <fieldset style="border-bottom:  #e7e7e7 solid medium">
                                    <div class="control-group">
                                        <div class="salesOptions">
                                          <label class="control-label" for="typeahead"style="width: 40%"><?=PURCHASE_INVOICE?> : </label>
                                            <div class="controls">
                                                <select name="purchInvoice" data-rel="chosen" onchange="this.form.submit()">
                                                    <option value=''> </option>
                                                        <?php
                                                                $query      = "SELECT * FROM purchase WHERE  state='1' ";
                                                                $res        = mysqli_query($adController->MySQL,$query) or die(mysqli_error($adController->MySQL));
                                                                while($data = mysqli_fetch_assoc($res))
                                                                {
//                                                                        $id   = $data['id'];
                                                                        $purchInvoice = $data['invoicenumber'];
                                                                                                                                               
                                                                        $sel='';
                                                                        if(isset($_REQUEST["purchInvoice"]) && $_REQUEST["purchInvoice"] == $purchInvoice)
                                                                            {
                                                                             $sel = " selected='true' ";
                                                                            }
                                                                        echo "<option value='$purchInvoice' $sel >$purchInvoice</option>";
                                                                }
                                                        ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>
                            </form>
                            <br>       
                            <?php
                           $custSearch='';
                           if($_REQUEST["purchInvoice"]!='')
                           {
                               $query           = "SELECT * FROM purchase_ret WHERE purchInvoice='$_REQUEST[purchInvoice]'";
                               $resP            = mysqli_query($adController->MySQL,$query);
                               $num             = mysqli_num_rows($resP);

                               if(!$num)
                               { 
                                    $queryPurch      = "SELECT * FROM purchase WHERE  invoicenumber= '$_REQUEST[purchInvoice]'";
                                    $resPurch        = mysqli_query($adController->MySQL,$queryPurch) or die(mysqli_error($adController->MySQL));
                                    $dataPurch       = mysqli_fetch_assoc($resPurch);
                               
                               ?>
                            <form class="form-horizontal" id="purchaseRet-form">
                                <input type="hidden" value="addPurchaseRet" name="f">
                                <input type="hidden" value="<?=$_REQUEST["purchInvoice"]?>" name="purchInvoice">
                                <fieldset>
                                    <div class="control-group">
                                        <div class="salesOptions">
                                            <label class="control-label" for="typeahead"><?=INVOICE_NUMBER?> : </label>
                                            <div class="controls">
                                               <?php
                                               $query   = "SELECT invoicenumber
                                                        FROM purchase_ret
                                                                                               
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
                                                       value='<?php echo date("d/m/Y")?>'>
                                            </div>
                                        </div>
                                    </div>
                                    <br>
                                    <div class="control-group">
                                        <div class="salesOptions">
                                            <label class="control-label" for="typeahead"><?=SUPPLIER?> : </label>
                                            <div class="controls">
                                                <input type="hidden" value="<?=$dataPurch[supplier]?>" name="supplier">
                                                <?php
                                                $query		  = "SELECT * FROM suppliers  WHERE id='$dataPurch[supplier]' ";
                                                $resSup		  = mysqli_query($adController->MySQL,$query);
                                                $dataSup 	  = mysqli_fetch_assoc($resSup);
                                                $supplier	= $dataSup['name_'.$language];
                                                ?>
                                                <input type="text" class=" typeahead" value ='<?php echo $supplier ?>'readonly>
                                          </div>
                                      </div>
                                      <div class="salesOptions">
                                          <label class="control-label" for="typeahead"><?=SUPPLIER_INVOICE?> : </label>
                                          <div class="controls">
                                              <input type="text" 
                                                     class=" typeahead"  
                                                     name='supplier_invoice' 
                                                     value="<?php echo $dataPurch['supplier_invoice']?>"
                                                     readonly>
                                          </div>
                                      </div>
                                  </div>
                                  <br>
                                  <div class="control-group">                                                                                                                       
                                      <div class="salesOptions">
                                          <label class="control-label"><?=BRANCH?> :</label>
                                            <div class="controls">
                                                <input type="hidden" value="<?=$dataPurch[branch]?>" name="branch">
                                                <?php
                                                $query		  = "SELECT * FROM branches  WHERE id='$dataPurch[branch]' ";
                                                $resSup		  = mysqli_query($adController->MySQL,$query);
                                                $dataSup 	  = mysqli_fetch_assoc($resSup);
                                                $branch           = $dataSup['name_'.$language];
                                                ?>
                                                <input type="text" class=" typeahead"  value ='<?php echo $branch ?>' readonly>
                                          </div>                                          

                                      </div>
                                        <div class="salesOptions">
                                            <label class="control-label"><?=PAYMENT_TYPE?> :</label>
                                            <div class="controls">
                                                 <label>
                                                <input type="radio" name="payType" value="1" checked="checked"><?=CASH?>
                                                 </label>
                                                 <label>
                                                <input type="radio" name="payType" value="4"><?=LATER?>
                                                 </label>
                                            </div>
                                        </div>                                        
                                    </div>      
                                  <br>
                                  <div class="control-group" >
                                      <div class="controls" style="margin-top:20px; margin-left:0px">	
                                          <table  class=" table-bordered " style="width:100%">                                                
                                              <tr>
                                                <TH><?=BARCODE?>|<?=ITEM_NAME?>|<?=UNITS?></TH>    
                                                <TH><?=STORES?> </th>
                                                <TH ><?=QUANTITY?></TH>
<!--                                                <TH ><?=QTY_AFTER?></TH>-->
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
                                                  <td style="width:20%">
                                                    <select id='barcode-<?=$i?>' name="barcode[]" data-rel="chosen" class="barcode-select-Ret" style="width:100%" >
                                                        <option value=''> </option>
                                    <?php
                                    $name     = "name_".$_SESSION['lang'];
                                    $query   = "SELECT items.*,purchase_items.store, purchase_items.cost, purchase_items.quantity, purchase_items.tax, purchase_items.discount AS sdis FROM items, purchase_items WHEREW  items.id = purchase_items.itemid  AND purchase_items.invoice_No='$_REQUEST[purchInvoice]'";
                                   
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
                                                $store        = $data['store'];
                                                $queryUnit    = "SELECT $name FROM stores WHERE id='$store' ";
                                                $resUint      = mysqli_query($adController->MySQL,$queryUnit) or die(mysqli_error($adController->MySQL));
                                                $dataUnit     = mysqli_fetch_assoc($resUint);
                                                $storeName    = $dataUnit[$name];                                                

                                                $unit         = $data['unit'];
                                                $queryUnit    = "SELECT $name FROM units WHERE id='$unit' ";
                                                $resUint      = mysqli_query($adController->MySQL,$queryUnit) or die(mysqli_error($adController->MySQL));
                                                $dataUnit     = mysqli_fetch_assoc($resUint);
                                                $unitName     = $dataUnit[$name];
                                                if ($unitName == '') $unitName = "-----";
                                                echo "<option value     = '$data[id]' "
                                                            . "index    = '$i' "                                                   
                                                            . "store    = '$store' "                                                                                                    
                                                            . "storeName    = '$storeName' "
                                                            . "qty      = '$data[quantity]' "
                                                            . "cost     = '$data[cost]' "
                                                            . "dis      = '$data[sdis]' "
                                                            . "vat      = '$data[tax]'> $barcode | $itemName | $unitName</option>";
                                            }
                                    ?>
                                                    </select>
                                                </td>
                                                <td style="width:10%">
                                                    <input type="hidden" id='store-<?=$i?>' name='store[]'>
                                                    <input type="text" id='storeName-<?=$i?>' class="store span12 typeahead" maxlength="100" readonly>
                                                </td>                                
                                                <td >
                                                    <input type="text" id='item-qty-<?=$i?>' class="item-qty span12 typeahead" maxlength="100"  name='item_qty[]' >
                                                </td>
                                                <td >
                                                    <input type="text" id='item-price-<?=$i?>' class="item-price span12 typeahead" maxlength="100"  name='item_price[]'readonly>
                                                </td>
                                                <td >
                                                    <input type="text" id='item-discount-<?=$i?>' class="item-discount span12 typeahead" maxlength="100"  name='item_disc[]'style="width :75%; "readonly> 
                                                </td>
                                                <td >
                                                    <input type="text" id='item-total-after-dis-<?=$i?>' class="item-total-after-dis span12 typeahead" maxlength="100"  name='item_total_after_dis[]' readonly>
                                                </td>
                                                <td >
                                                    <input type="text" id='item-vat-<?=$i?>' class="item-vat span12 typeahead" maxlength="100"  name='item_vat[]' style="width :75%; " readonly> 
                                                </td>
                                                <td >
                                                    <input type="text" id='item-vat-value-<?=$i?>' class="item-vat-value span12 typeahead" maxlength="100"  name='item_vat_value[]' readonly>
                                                </td>
                                                <td >
                                                        <input type="text" id='item-total-<?=$i?>' class="item-total span12 typeahead" maxlength="100"  name='item-total[]'readonly>

                                                </td>
                                                <td class='del-parchaseRet-item mult-padding edit' prop='<?=$i?>'><label>x</label></td>
                                                <td class='more-parchaseRet-item mult-padding edit' prop='<?=$i?>'><label>+</label></td>
                                              </tr>
                            <?php 
                            
                                                } ?>
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
<!--                                                            <button type="button" class="btn btn-primary" onclick="PrintDiv();"><i class="fa fa-print"></i> Print</button>-->
                                      <button type="button" id='submit-purchaseRet' class="btn btn-primary"><?=SAVE?></button>
                                      <button type="reset" class="btn"><?=CANCEL?></button>
                                    </div>
                                    <p class='error-red'></p>

                              </fieldset>
                            </form>   
                           <?php
                               }
                               else 
                               {
                                   $data       = mysqli_fetch_assoc($resP);
                                   $invoice	= $data["invoicenumber"];	
                                   ?>  
                            <form class="form-horizontal" >
                                    <div class="control-group"  >
                                        <center>
                                            <label>
                                           <?=INVOICE_RETURNED_ALREADY?>
                                            </label>
                                            <div >
                                            <a class='btn btn-info' href='edit_purchaseRet.php?sd=<?=$invoice?>'>
                                                <?=EDIT?>  
                                            </a>    
                                            </div>
                                        </center>
                                    </div>     
                            </form>
                            <?php
                               }
                           }
                           ?>
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
        document.title = "Sales Invoice";
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
//       alert(disTotal);
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
