<?php
require_once("top.php");
require_once("redirection.php");
require_once("controller.php");
$language = defined('LANG') ? LANG : ($_SESSION['lang'] ?? 'ar');
$storeid  = (int)($_SESSION['storeid'] ?? 0);

$rawSd   = trim((string)($_REQUEST['sd'] ?? ''));
$invoice = mysqli_real_escape_string($adController->MySQL, $rawSd);
$query   = "SELECT * FROM purchase WHERE invoicenumber='$invoice' LIMIT 1";
$resP    = mysqli_query($adController->MySQL, $query);
$dataPur = ($resP && mysqli_num_rows($resP) > 0) ? mysqli_fetch_assoc($resP) : [];

$query = "SELECT * FROM purchase_items WHERE invoice_No='$invoice'";
$ress  = mysqli_query($adController->MySQL, $query);
$itemsListArray = [];
$numRowss = ($ress) ? mysqli_num_rows($ress) : 0;
$numAlready = max(0, $numRowss - 1);
if ($ress) {
	while($dataItemsList = mysqli_fetch_assoc($ress)) {
		$itemsListArray[] = $dataItemsList;
	}
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
	<meta charset="utf-8">
	<title>WAM Tech Soft - <?=PURCHASES?> &gt;&gt; <?=EDIT?></title>
	<?php require_once("script_php_variables.php");?>
	<?php require_once("header.php");?>	
	<script type="text/javascript">
		var numAlready = <?=(int)$numAlready?>;
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
							<h2><i class="halflings-icon edit"></i><span class="break"></span><a href="purchase.php"><?=PURCHASES?></a> &gt;&gt; <?=EDIT?></h2>
						</div>
						<div class="box-content">
							<form class="form-horizontal" id="purchase-form" method="POST" action="#" autocomplete="off">
								<input type="hidden" value="editPurchase" name="f">
								<input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
								<fieldset>
									<div class="control-group">
										<div class="salesOptions">
											<label class="control-label" for="invoice"><?=INVOICE_NUMBER?> : </label>
											<div class="controls">
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
												<?php 
												$invDate = !empty($dataPur['invoice_date']) ? date("Y-m-d", strtotime($dataPur['invoice_date'])) : date("Y-m-d");
												?>
												<input type="text" class="datepicker-nobar" name="purchase_expiry" value="<?=$invDate?>">
											</div>
										</div>
									</div>
									<br>

									<div class="control-group">
										<div class="salesOptions">
											<label class="control-label" for="supplier"><?=SUPPLIER?> : </label>
											<div class="controls">
												<select name="supplier" id="supplier" data-rel="chosen">
													<?php
													$query = "SELECT id, name_ar, name_en FROM suppliers WHERE storeid='$storeid' OR storeid='0' ORDER BY name_ar ASC";
													$res   = mysqli_query($adController->MySQL, $query) or die(mysqli_error($adController->MySQL));
													while($data = mysqli_fetch_assoc($res))
													{
														$sup_id = (int)$data['id'];
														$name   = htmlspecialchars(!empty($data['name_ar']) ? $data['name_ar'] : ($data['name_en'] ?? ''));
														$sel    = ($sup_id == ($dataPur['supplier'] ?? '')) ? "selected" : "";
														echo "<option value='$sup_id' $sel>$name</option>";
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
												<input type="text" 
													   class="typeahead"  
													   name="supplier_invoice" 
													   id="supplier_invoice"
													   value="<?=htmlspecialchars($dataPur['supplier_invoice'] ?? '')?>">
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
														$bra_id = (int)$data['id'];
														$name   = htmlspecialchars(!empty($data['name_ar']) ? $data['name_ar'] : ($data['name_en'] ?? ''));
														$sel    = ($bra_id == ($dataPur['branch'] ?? '')) ? "selected" : "";
														echo "<option value='$bra_id' $sel>$name</option>";
													}
													?>
												</select>
											</div>
										</div>
										<div class="salesOptions">
											<label class="control-label"><?=PAYMENT_TYPE?> :</label>
											<div class="controls">
												<label>
													<input type="radio" name="payType" value="1" <?=($dataPur['paymentType'] ?? '') == "1" ? "checked" : ""?>><?=CASH?>
												</label>
												<label>
													<input type="radio" name="payType" value="4" <?=($dataPur['paymentType'] ?? '') == "4" ? "checked" : ""?>><?=LEFT?>
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
												// Pre-cache stores
												$branchId = mysqli_real_escape_string($adController->MySQL, (string)($dataPur['branch'] ?? ''));
												$queryStores = "SELECT id, name_ar, name_en, `default` FROM stores WHERE (storeid='$storeid' OR storeid='0') AND branch='$branchId' ORDER BY name_ar ASC";
												$resStores = mysqli_query($adController->MySQL, $queryStores);
												$storesCache = [];
												if ($resStores) {
													while ($dataSt = mysqli_fetch_assoc($resStores)) {
														$storesCache[] = [
															'id' => $dataSt['id'],
															'name' => htmlspecialchars(!empty($dataSt['name_ar']) ? $dataSt['name_ar'] : ($dataSt['name_en'] ?? '')),
															'default' => $dataSt['default'] ?? '0'
														];
													}
												}

												// Pre-cache items with units via single query
												$queryIt = "SELECT i.id, i.barcode, i.name_ar, i.name_en, i.vat, i.price, i.discount, u.name_ar AS unit_ar, u.name_en AS unit_en 
															FROM items i 
															LEFT JOIN units u ON i.unit = u.id 
															WHERE i.storeid='$storeid' OR i.storeid='0' 
															ORDER BY i.name_ar ASC";
												$resIt = mysqli_query($adController->MySQL, $queryIt);
												$itemsCache = [];
												if ($resIt) {
													while ($dataIt = mysqli_fetch_assoc($resIt)) {
														$bcode = !empty($dataIt['barcode']) ? $dataIt['barcode'] : "-----";
														$iName = !empty($dataIt['name_ar']) ? $dataIt['name_ar'] : (!empty($dataIt['name_en']) ? $dataIt['name_en'] : '---');
														$uName = !empty($dataIt['unit_ar']) ? $dataIt['unit_ar'] : (!empty($dataIt['unit_en']) ? $dataIt['unit_en'] : "-----");
														$itemsCache[] = [
															'id' => (int)$dataIt['id'],
															'barcode' => $bcode,
															'name' => $iName,
															'unit' => $uName,
															'vat' => $dataIt['vat'] ?? '0',
															'price' => $dataIt['price'] ?? '0',
															'disc' => $dataIt['discount'] ?? '0'
														];
													}
												}

												for ($i = 0; $i < 10; $i++)
												{											
													$curItem = $itemsListArray[$i] ?? [];
													$curStoreId = $curItem['store'] ?? '';
													$curItemId  = $curItem['itemid'] ?? '';
													$curQty     = $curItem['quantity'] ?? '';
													$curPrice   = $curItem['cost'] ?? '';
													$curDisc    = $curItem['discount'] ?? '';
													$curVat     = $curItem['tax'] ?? '';
												?>
												<tr id="row-v-<?=$i?>">
													<td style="width:10%">
														<select id="store-<?=$i?>" name="store[]" class="store" data-rel="chosen" style="width:100%">
															<option value=""><?=SELECT_STORE?></option>
															<?php
															foreach ($storesCache as $st) {
																$sel = ($st['id'] == $curStoreId) ? 'selected' : (($curStoreId === '' && $st['default'] == '1') ? 'selected' : '');
																echo "<option value='{$st['id']}' $sel>{$st['name']}</option>";
															}
															?>
														</select>
													</td>
													
													<td style="width:20%">
														<select id="barcode-<?=$i?>" name="barcode[]" data-rel="chosen" class="barcode-select-sale" style="width:100%">
															<option value=""> </option>
															<?php
															foreach ($itemsCache as $itemData) {
																$sel = ($itemData['id'] == $curItemId) ? 'selected' : '';
																echo "<option value='{$itemData['id']}' "
																	 . "index='$i' "
																	 . "vat='{$itemData['vat']}' "
																	 . "price='{$itemData['price']}' "
																	 . "disc='{$itemData['disc']}' $sel>{$itemData['barcode']} | {$itemData['name']} | {$itemData['unit']}</option>";
															}
															?>
														</select>
													</td>

													<td>
														<input type="text" id="item-qty-<?=$i?>" class="item-qty span12 typeahead" maxlength="100" name="item_qty[]" value="<?=$curQty?>">
													</td>
													<td>
														<input type="text" id="item-price-<?=$i?>" class="item-price span12 typeahead" maxlength="100" name="item_price[]" value="<?=$curPrice?>">
													</td>
													<td>
														<input type="text" id="item-discount-<?=$i?>" class="item-discount span12 typeahead" maxlength="100" name="item_disc[]" style="width:75%;" value="<?=$curDisc?>"> 
													</td>
													<td>
														<input type="text" id="item-total-after-dis-<?=$i?>" class="item-total-after-dis span12 typeahead" maxlength="100" name="item_total_after_dis[]" readonly>
													</td>
													<td>
														<input type="text" id="item-vat-<?=$i?>" class="item-vat span12 typeahead" maxlength="100" name="item_vat[]" style="width:75%;" value="<?=$curVat?>"> 
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
														<input type="text" class="span12 typeahead" name="final_discount" id="final_discount" value="<?=htmlspecialchars((string)($dataPur['dis_added'] ?? '0.00'))?>">
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
			var qty = parseFloat($("#item-qty-"+id).val() || 0);
			var price = parseFloat($("#item-price-"+id).val() || 0);
			var preTotal = qty * price;
			
			var discPct = parseFloat($("#item-discount-"+id).val() || 0);
			var discount = preTotal * discPct / 100;
			var disTotal = preTotal - discount;
			$("#item-total-after-dis-"+id).val(disTotal.toFixed(2));

			var vatPct = parseFloat($("#item-vat-"+id).val() || 0);
			var vaTax = disTotal * vatPct / 100;
			$("#item-vat-value-"+id).val(vaTax.toFixed(2));

			var total = disTotal + vaTax;
			$("#item-total-"+id).val(total.toFixed(2));   

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

			// Calculate totals for existing items initially
			for (var k = 0; k < 10; k++) {
				if ($("#item-qty-" + k).val() != '') {
					totalParshase(k);
				}
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
