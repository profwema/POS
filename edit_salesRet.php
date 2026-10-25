<?php
require_once("top.php");
require_once("redirection.php");
require_once("controller.php");
$language 	= $_SESSION['lang'];
$storeid	= $_SESSION['storeid'];


$rawInvoice = $_REQUEST['sd'] ?? '';
$invoice = is_numeric($rawInvoice) ? $rawInvoice : $adController->encrypt_decrypt(2, urldecode($rawInvoice), 0);
$invoice = mysqli_real_escape_string($adController->MySQL, $invoice);

$query = "SELECT * FROM sales_ret WHERE invoicenumber='$invoice'";
$resS = mysqli_query($adController->MySQL, $query);
$dataPur = mysqli_fetch_assoc($resS);
$salesInvoice = $dataPur['salesInvoice'] ?? '';

$itemsListArray = [];
$query = "SELECT * FROM sales_ret_items WHERE storeid='$storeid' AND invoice_No='$invoice'";
$ress = mysqli_query($adController->MySQL, $query);
$numRowss = mysqli_num_rows($ress);
$numAlready = $numRowss - 1;
if ($numAlready < 0) $numAlready = 0;
while ($dataItemsList = mysqli_fetch_assoc($ress)) {
    $itemsListArray[] = $dataItemsList;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>WAM Tech Soft</title>
	<?php require_once("script_php_variables.php");?>
	<?php require_once("header.php");?>	
        <script type="text/javascript">
//            var itemsAlready    = "";
//            itemsAlready        = $.parseJSON(itemsAlready);
            numAlready          = <?=$numAlready?>;
        </script>    
         
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
                                            <h2><i class="halflings-icon edit"></i><span class="break"></span><a href="salesRet.php"><?=RETURNED_SALES?></a> >> <?=EDIT?></h2>
					</div>  
					<div class="box-content">
                                            <form class="form-horizontal" id="saleRet-form">
                                                <input type="hidden" value="editSaleRet" name="f">
                                                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                                <fieldset style="border-bottom:  #e7e7e7 solid medium">
                                                    <div class="control-group">
                                                        <div class="salesOptions">
                                                          <label class="control-label" for="typeahead"><?=SALE_INVOICE?> : </label>
                                                          <div class="controls">
                                                               <?php

                                                              ?>
                                                              <input type="text" 
                                                                     class=" typeahead"  
                                                                     name='saleInvoice' 
                                                                     id='saleInvoice' 
                                                                     value="<?=$salesInvoice?>"
                                                                     readonly>
                                                                 <span class="help-inline email-un">&nbsp;</span>
                                                          </div>
                                                        </div>
                                                    </div>
                                                </fieldset>    
						  <fieldset>   
							<div class="control-group">
                                                            <div class="salesOptions">
							  <label class="control-label" for="typeahead"><?=INVOICE_NUMBER?> : </label>
							  <div class="controls">
                                                               <?php

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
								  <input type="text"
                                                                         class="datepicker-nobar" 
                                                                         name="purchase_expiry"  
                                                                         value="<?=date("d/m/Y",strtotime($dataPur['invoice_date']))?>">
								</div>
							</div>
                                                        </div>
                                                      <br>

							<div class="control-group">
                                                            <div class="salesOptions">

							  <label class="control-label" for="typeahead"><?=CUSTOMER?> : </label>
							  <div class="controls">
                                                              <input type="hidden" value="<?=$dataPur['customer'] ?? ''?>" name="customer">
                                                                <?php
                                                                $query		  = "SELECT * FROM customers  WHERE id='" . intval($dataPur['customer'] ?? 0) . "' AND storeid='$storeid'";
                                                                $resSup		  = mysqli_query($adController->MySQL,$query);
                                                                $dataSup 	  = mysqli_fetch_assoc($resSup);
                                                                $customer	= $dataSup['name_'.$language];
                                                                ?>
                                                              <input type="text" class=" typeahead" value ='<?php echo $customer ?>'readonly>
                                                          </div>
							</div>
                                                            <div class="salesOptions">
							  <label class="control-label" for="typeahead"><?=DOCUMENT?> : </label>
							  <div class="controls">
								 <input type="text" 
                                                                        class=" typeahead"  
                                                                        name='document' 
                                                                        id='document'
                                                                       value="<?=$dataPur['document']?>"
                                                                       readonly>
							  </div>
							</div>
                                                        </div>
                                                      <br>
                                                        <div class="control-group">                                                                                                                       
                                                            <div class="salesOptions">

							  <label class="control-label"><?=BRANCH?> :</label>
                                                          <div class="controls">
                                                              <input type="hidden" value="<?=$dataPur['branch'] ?? ''?>" name="branch">
                                                            <?php
                                                            $query		  = "SELECT * FROM branches  WHERE id='" . intval($dataPur['branch'] ?? 0) . "' AND storeid='$storeid'";
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
                                                <input type="radio" name="payType" value="1" <?=$dataPur['paymentType']=="1" ? "checked" : "" ?>><?=CASH?>
                                                 </label>
                                                 <label>
                                                <input type="radio" name="payType" value="0" <?=$dataPur['paymentType']=="0" ? "checked" : "" ?>><?=LATER?>
                                                 </label>
                                            </div>
                                        </div>                                                             
							</div>                                                      
                                  <div class="control-group" >
                                      <div class="controls" style="margin-top:20px; margin-left:0px">	
                                          <table  class=" table-bordered " style="width:100%">                                                
                                              <tr>
                                                <TH><?=BARCODE?>|<?=ITEM_NAME?>|<?=UNITS?></TH>    
                                                <TH><?=STORES?> </th>
                                                <TH ><?=QUANTITY?></TH>
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
                                                // Pre-cache sale items for the return form
                                                $salesInvEsc = mysqli_real_escape_string($adController->MySQL, $salesInvoice);
                                                $saleItemsCache = [];
                                                $querySaleItems = "SELECT items.*, 
                                                                          si.store, si.cost, si.quantity, si.tax, si.discount AS sdis,
                                                                          s.name_ar AS store_name_ar, s.name_en AS store_name_en,
                                                                          u.name_ar AS unit_name_ar, u.name_en AS unit_name_en
                                                                   FROM sales_items si
                                                                   INNER JOIN items ON items.id = si.itemid
                                                                   LEFT JOIN stores s ON si.store = s.id
                                                                   LEFT JOIN units u ON items.unit = u.id
                                                                   WHERE si.invoice_No = '$salesInvEsc'";
                                                $resSaleItems = mysqli_query($adController->MySQL, $querySaleItems);
                                                if ($resSaleItems) {
                                                    while ($siRow = mysqli_fetch_assoc($resSaleItems)) {
                                                        $saleItemsCache[] = $siRow;
                                                    }
                                                }

                                                // Pre-cache stores map
                                                $storesMap = [];
                                                $resStores = mysqli_query($adController->MySQL, "SELECT id, name_ar, name_en FROM stores");
                                                while ($st = mysqli_fetch_assoc($resStores)) {
                                                    $storesMap[$st['id']] = $st["name_" . $language] ?? $st['name_ar'];
                                                }

                                                for($i = 0 ;$i < 10 ; $i++)
                                                {
                                                    $curItem = $itemsListArray[$i] ?? [];
                                                    $curStore = $curItem['store'] ?? '';
                                                    $curItemId = $curItem['itemid'] ?? '';
                                                    $curQty = htmlspecialchars($curItem['quantity'] ?? '');
                                                    $curCost = htmlspecialchars($curItem['cost'] ?? '');
                                                    $curDisc = htmlspecialchars($curItem['discount'] ?? '');
                                                    $curTax = htmlspecialchars($curItem['tax'] ?? '');
                                                    $curStoreName = htmlspecialchars($storesMap[$curStore] ?? '');
                                                ?>
                                                <tr id='row-v-<?=$i?>'>
                                                    <td style="width:20%">
                                                        <select id='barcode-<?=$i?>' name="barcode[]" data-rel="chosen" class="barcode-select-purRet" style="width:100%">
                                                            <option value=''> </option>
                                                            <?php
                                                            foreach ($saleItemsCache as $data) {
                                                                $barcode = $data['barcode'] ?: '-----';
                                                                $itemName = $data['name_' . $language] ?? '';
                                                                if ($itemName == '' || $itemName == '`') {
                                                                    $itemName = ($language == 'en') ? ($data['name_ar'] ?? '') : ($data['name_en'] ?? '');
                                                                }
                                                                $store = $data['store'];
                                                                $storeName = ($language == 'en') ? ($data['store_name_en'] ?? '-----') : ($data['store_name_ar'] ?? '-----');
                                                                $unitName = ($language == 'en') ? ($data['unit_name_en'] ?? '-----') : ($data['unit_name_ar'] ?? '-----');
                                                                if (!$unitName) $unitName = '-----';
                                                                $sel = ($data['id'] == $curItemId) ? 'selected' : '';
                                                                $optVal = htmlspecialchars($data['id'], ENT_QUOTES);
                                                                $costVal = htmlspecialchars($data['cost'], ENT_QUOTES);
                                                                $qtyVal = htmlspecialchars($data['quantity'], ENT_QUOTES);
                                                                $disVal = htmlspecialchars($data['sdis'], ENT_QUOTES);
                                                                $vatVal = htmlspecialchars($data['tax'], ENT_QUOTES);
                                                                $label = htmlspecialchars("$barcode | $itemName | $unitName");
                                                                echo "<option value='$optVal' index='$i' store='$store' storeName='$storeName' qty='$qtyVal' cost='$costVal' dis='$disVal' vat='$vatVal' $sel>$label</option>";
                                                            }
                                                            ?>
                                                        </select>
                                                    </td>
                                                    <td style="width:10%">
                                                        <input type="hidden" id='store-<?=$i?>' name='store[]' value='<?=$curStore?>'>
                                                        <input type="text" id='storeName-<?=$i?>' class="store span12 typeahead" maxlength="100" value="<?=$curStoreName?>" readonly>
                                                    </td>
                                                    <td><input type="text" id='item-qty-<?=$i?>' class="item-qty span12 typeahead" maxlength="100" name='item_qty[]' value="<?=$curQty?>"></td>
                                                    <td><input type="text" id='item-price-<?=$i?>' class="item-price span12 typeahead" maxlength="100" name='item_price[]' value="<?=$curCost?>" readonly></td>
                                                    <td><input type="text" id='item-discount-<?=$i?>' class="item-discount span12 typeahead" maxlength="100" name='item_disc[]' style="width:75%;" value="<?=$curDisc?>" readonly></td>
                                                    <td><input type="text" id='item-total-after-dis-<?=$i?>' class="item-total-after-dis span12 typeahead" maxlength="100" name='item_total_after_dis[]' readonly></td>
                                                    <td><input type="text" id='item-vat-<?=$i?>' class="item-vat span12 typeahead" maxlength="100" name='item_vat[]' style="width:75%;" value="<?=$curTax?>" readonly></td>
                                                    <td><input type="text" id='item-vat-value-<?=$i?>' class="item-vat-value span12 typeahead" maxlength="100" name='item_vat_value[]' readonly></td>
                                                    <td>
                                                        <input type="text" id='item-total-<?=$i?>' class="item-total span12 typeahead" maxlength="100" name='item-total[]' readonly>
                                                        <script type="text/javascript">
                                                            $(document).ready(function() {
                                                                totalParshase(<?=$i?>);
                                                            });
                                                        </script>
                                                    </td>
                                                    <td class='del-sale-item mult-padding' prop='<?=$i?>'><label>x</label></td>
                                                    <td class='more-sale-item mult-padding' prop='<?=$i?>'><label>+</label></td>
                                                </tr>
                                                <?php } ?>
                                                </table>
                                      </div>
                                  </div>                                                     

							
                                    
                                    <div class="control-group">
                                        <div style="margin-top:20px; float: right; margin-right: 20px">
                                            
                                            	
                                                <table class="span12 table table-striped table-bordered " >                                                
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
                                                        <input type="text" class="span12 typeahead" name='final_discount' id='final_discount' value="<?=$dataPur['dis_added']?>">

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
							  <button type="button" id='submit-saleRet' class="btn btn-primary"><?=SAVE?></button>
							  <button type="reset" class="btn"><?=CANCEL?></button>
							</div>
							<p>&nbsp;</p>
							<p class='error-red'>
								&nbsp;
								
							</p>

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
