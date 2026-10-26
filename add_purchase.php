<?php
require_once("top.php");
require_once("redirection.php");
require_once("controller.php");
$language = defined('LANG') ? LANG : ($_SESSION['lang'] ?? 'ar');
$storeid  = (int)($_SESSION['storeid'] ?? 0);
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
	<meta charset="utf-8">
	<title>WAM Tech Soft - <?=PURCHASES?> >> <?=ADD_NEW?></title>
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
							<h2><i class="halflings-icon edit"></i><span class="break"></span><a href="purchase.php"><?=PURCHASES?></a> &gt;&gt; <?=ADD_NEW?></h2>
						</div>
						<div class="box-content">
							<form class="form-horizontal" id="purchase-form" method="POST" action="#" autocomplete="off">
								<input type="hidden" value="addPurchase" name="f">
								<input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
								<fieldset>                                                      
									<div class="control-group">
										<div class="salesOptions">
											<label class="control-label" for="invoice"><?=INVOICE_NUMBER?> : </label>
											<div class="controls">
												<?php
												$defBranch = 1;
												$resBr = mysqli_query($adController->MySQL, "SELECT id FROM branches WHERE status='1' LIMIT 1");
												if ($resBr && $brRow = mysqli_fetch_assoc($resBr)) {
													$defBranch = (int)$brRow['id'];
												}
												$invoice = $adController->getNewInvoiceNo("purchase", "invoicenumber", '21', $defBranch);
												?>
												<input type="text" 
													   class="typeahead"  
													   name="invoice" 
													   id="invoice" 
													   value="<?=htmlspecialchars($invoice)?>"
													   readonly>
												<span class="help-inline email-un">&nbsp;</span>
											</div>
										</div>  
										
										<div class="salesOptions">
											<label class="control-label"><?=INVOICE_DATE?> : </label>
											<div class="controls">
												<input type="text" class="datepicker-nobar" name="purchase_expiry" value="<?=date("Y-m-d")?>">
											</div>
										</div>
									</div>
									<br>							
									<div class="control-group">                
										<div class="salesOptions">
											<label class="control-label" for="supplier"><?=SUPPLIER?> : </label>
											<div class="controls">
												<select name="supplier" id="supplier" data-rel="chosen">
													<option value=""> </option>
													<?php
													$query = "SELECT id, name_ar, name_en FROM suppliers WHERE storeid='$storeid' OR storeid='0' ORDER BY name_ar ASC";
													$res   = mysqli_query($adController->MySQL, $query) or die(mysqli_error($adController->MySQL));
													while($data = mysqli_fetch_assoc($res))
													{
														$id   = (int)$data['id'];
														$name = htmlspecialchars(!empty($data['name_ar']) ? $data['name_ar'] : ($data['name_en'] ?? ''));
														echo "<option value='$id'>$name</option>";
													}
													?>
												</select>
												<span class="help-inline">&nbsp;</span>
												<a href="suppliers.php" style="padding-top: 20px; margin-left: 40px;"><?=ADD_EDIT_SUPPLIER?></a>
											</div>
										</div>
										<div class="salesOptions">
											<label class="control-label" for="supplier_invoice"><?=SUPPLIER_INVOICE?> : </label>
											<div class="controls">
												<input type="text" class="typeahead" name="supplier_invoice" id="supplier_invoice">
											</div>
										</div>
									</div>
									<br>
									
									<div class="control-group">                                                                                                                       
										<div class="salesOptions">
											<label class="control-label"><?=BRANCH?> :</label>
											<div class="controls">
												<select name="branch" id="branch" data-rel="chosen">
													<option value=""> </option>
													<?php
													$query = "SELECT id, name_ar, name_en FROM branches WHERE storeid='$storeid' OR storeid='0' ORDER BY name_ar ASC";
													$res   = mysqli_query($adController->MySQL, $query) or die(mysqli_error($adController->MySQL));
													while($data = mysqli_fetch_assoc($res))
													{
														$id   = (int)$data['id'];
														$name = htmlspecialchars(!empty($data['name_ar']) ? $data['name_ar'] : ($data['name_en'] ?? ''));
														$selected = ($id === $defBranch) ? 'selected' : '';
														echo "<option value='$id' $selected>$name</option>";
													}
													?>
												</select>
											</div>
										</div>
										<div class="salesOptions">
											<label class="control-label"><?=PAYMENT_TYPE?> :</label>
											<div class="controls">
												<label>
													<input type="radio" name="payType" value="1" checked="checked"><?=CASH?>
												</label>
												<label>
													<input type="radio" name="payType" value="4"><?=LEFT?>
												</label>
											</div>
										</div>                                        
									</div>                                                      

									<div class="control-group">
										<label class="control-label"><?=INVOICE_DETAILS?></label>
										<div class="controls" style="margin-top:20px; margin-left:0px">	
											<table class="span12 table-striped table-bordered" style="width:100%">                                                
												<tr>
													<th><?=STORES?></th>
													<th><?=BARCODE?> | <?=ITEM_NAME?> | <?=UNITS?></th>    
													<th><?=QUANTITY?></th>
													<th><?=PRICE?></th>
													<th><?=DISCOUNT?> %</th>
													<th><?=TOTAL_A_DIS?></th>
													<th><?=VAT?> %</th>
													<th><?=VAT_VALUE?></th>
													<th><?=TOTAL?></th>
													<th>&nbsp;</th>
													<th>&nbsp;</th>
												</tr>
												<?php
												// Pre-fetch items and units once to eliminate N+1 query loop
												$queryItemsFast = "SELECT i.id, i.barcode, i.name_ar, i.name_en, i.vat, i.price, i.discount, u.name_ar AS unit_ar, u.name_en AS unit_en 
																   FROM items i 
																   LEFT JOIN units u ON i.unit = u.id 
																   WHERE i.storeid = '$storeid' OR i.storeid = '0' 
																   ORDER BY i.name_ar ASC";
												$resItemsFast = mysqli_query($adController->MySQL, $queryItemsFast) or die(mysqli_error($adController->MySQL));
												$itemsCache = [];
												while ($row = mysqli_fetch_assoc($resItemsFast)) {
													$bcode = !empty($row['barcode']) ? $row['barcode'] : "-----";
													$iName = !empty($row['name_ar']) ? $row['name_ar'] : (!empty($row['name_en']) ? $row['name_en'] : '---');
													$uName = !empty($row['unit_ar']) ? $row['unit_ar'] : (!empty($row['unit_en']) ? $row['unit_en'] : "-----");
													$itemsCache[] = [
														'id' => (int)$row['id'],
														'barcode' => $bcode,
														'name' => $iName,
														'unit' => $uName,
														'vat' => $row['vat'] ?? '0',
														'price' => $row['price'] ?? '0',
														'disc' => $row['discount'] ?? '0'
													];
												}

												for($i = 0; $i < 10; $i++)
												{
												?>                                                    
													<tr id="row-v-<?=$i?>">
														<td style="width:10%">
															<select name="store[]" id="store-<?=$i?>" class="store" data-rel="chosen" style="width:100%">
																<option value=""><?=SELECT_STORE?></option>
															</select>
														</td>
														<td style="width:20%">
															<select id="barcode-<?=$i?>" name="barcode[]" data-rel="chosen" class="barcode-select-sale" style="width:100%">
																<option value=""> </option>
																<?php
																foreach ($itemsCache as $itemData) {
																	echo "<option value='{$itemData['id']}' "
																		 . "index='$i' "
																		 . "vat='{$itemData['vat']}' "
																		 . "price='{$itemData['price']}' "
																		 . "disc='{$itemData['disc']}' "
																		 . ">{$itemData['barcode']} | {$itemData['name']} | {$itemData['unit']}</option>";
																}
																?>
															</select>
														</td>
														<td>
															<input type="text" id="item-qty-<?=$i?>" class="item-qty span12 typeahead" maxlength="100" name="item_qty[]">
														</td>
														<td>
															<input type="text" id="item-price-<?=$i?>" class="item-price span12 typeahead" maxlength="100" name="item_price[]">
														</td>
														<td>
															<input type="text" id="item-discount-<?=$i?>" class="item-discount span12 typeahead" maxlength="100" name="item_disc[]" style="width:75%;"> 
														</td>
														<td>
															<input type="text" id="item-total-after-dis-<?=$i?>" class="item-total-after-dis span12 typeahead" maxlength="100" name="item_total_after_dis[]" readonly>
														</td>
														<td>
															<input type="text" id="item-vat-<?=$i?>" class="item-vat span12 typeahead" maxlength="100" name="item_vat[]" style="width:75%;"> 
														</td>
														<td>
															<input type="text" id="item-vat-value-<?=$i?>" class="item-vat-value span12 typeahead" maxlength="100" name="item_vat_value[]" readonly>
														</td>
														<td>
															<input type="text" id="item-total-<?=$i?>" class="item-total span12 typeahead" maxlength="100" name="item-total[]" readonly> 
														</td>
														<td class="del-parchase-item mult-padding" prop="<?=$i?>"><label>x</label></td>
														<td class="more-parchase-item mult-padding" prop="<?=$i?>"><label>+</label></td>
													</tr>
												<?php } ?>
											</table>
										</div>
									</div>                                  				
									<hr>
									<div class="control-group">
										<div style="margin-top:20px; float: right; margin-right: 20px">
											<table class="span12 table table-striped table-bordered">                                                
												<tr>
													<th colspan="2"><h4><?=TOTAlS?></h4></th>
												</tr>
												<tr>
													<td style="width:40%">اجمالى بدون ضريبة</td>
													<td>
														<input type="text" class="span12 typeahead" width="80%" id="total-before-vat" name="gross_total" value="0.00" readonly>
													</td>    
												</tr>
												<tr>
													<td>أجمالى قيمة الضريبة</td>
													<td>
														<input type="text" class="span12 typeahead" id="total-vat-value" name="vat_all" value="0.00" readonly>
													</td>    
												</tr>
												<tr>
													<td>الاجمالى شامل الضريبة</td>
													<td>
														<input type="text" class="span12 typeahead" id="total-after-vat" value="0.00" readonly>
													</td>    
												</tr>
												<tr>
													<td>خصم اضافي</td>
													<td>                                                        								
														<input type="text" class="span12 typeahead" name="final_discount" id="final_discount" value="0.00">
													</td>    
												</tr>
												<tr style="font-size: 16px">
													<td style="padding: 10px;">الأجمالى النهائي</td>
													<td>
														<input type="text" class="span12 typeahead" name="totalAll" id="totalAll" value="0.00" readonly>
													</td>    
												</tr>
											</table>
										</div>
									</div>
									<div class="form-actions">
										<button type="button" id="submit-purchase" class="btn btn-primary"><?=SAVE?></button>
										<button type="reset" class="btn"><?=CANCEL?></button>
									</div>
									<p>&nbsp;</p>
									<p class="error-red">&nbsp;</p>
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
			// يحسب اجمالي الكمية في السعر
			var preTotal = ($("#item-qty-"+id).val() || 0) * ($("#item-price-"+id).val() || 0);
			// يحسب مقدار الخصم
			var discount = preTotal * ($("#item-discount-"+id).val() || 0) / 100;
			// يحسب الاجمالي بعد الخصم
			var disTotal = preTotal - discount;
			$("#item-total-after-dis-"+id).val(disTotal.toFixed(2));

			// يحسب قيمة الضريبة
			var vaTax = disTotal * ($("#item-vat-"+id).val() || 0) / 100;
			$("#item-vat-value-"+id).val(vaTax.toFixed(2));

			// يحسب الاجمالي النهائي للبند
			var total = disTotal + vaTax;
			$("#item-total-"+id).val(total.toFixed(2));   

			// يحسب الاجماليات في الاسفل
			var total_before_vat = 0;
			var total_vat_value = 0;
			var total_after_vat = 0;
			for (var i = 0; i < 10; i++)
			{
				if ($("#item-qty-"+i).val() != '' && $("#item-qty-"+i).val() != undefined)
				{
					total_before_vat += parseFloat($("#item-total-after-dis-"+i).val() || 0); 
					total_vat_value += parseFloat($("#item-vat-value-"+i).val() || 0); 
					total_after_vat += parseFloat($("#item-total-"+i).val() || 0); 
				}
			}
			$("#total-before-vat").val(total_before_vat.toFixed(2));
			$("#total-vat-value").val(total_vat_value.toFixed(2));
			$("#total-after-vat").val(total_after_vat.toFixed(2));
			
			var finalDisc = parseFloat($("#final_discount").val() || 0);
			var totalAll = total_after_vat - finalDisc;
			$("#totalAll").val(totalAll.toFixed(2));
		}

		$(document).ready(function()
		{
			var config = {
				'.span3': { width: '95%' }
			};
			for (var selector in config) 
			{
				$(selector).chosen(config[selector]);
			}

			// Initial store population for default branch
			var initialBranch = $("#branch").val();
			if (initialBranch) {
				getStore(initialBranch);
			}

			$(".item-qty, .item-price, .item-discount, .item-vat").on('input change', function()
			{
				var id = $(this).attr('id').split('-').pop();
				totalParshase(id);
			});

			$("#final_discount").on('input change', function()
			{
				var totalAfterVat = parseFloat($("#total-after-vat").val() || 0);
				var finalDisc = parseFloat($(this).val() || 0);
				var totalAll = totalAfterVat - finalDisc;
				$("#totalAll").val(totalAll.toFixed(2));
			});
		});
	</script>
</body>
</html>
