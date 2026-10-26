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
	<title>WAM Tech Soft - <?=RETURNED_PURCHASES?> &gt;&gt; <?=ADD_NEW?></title>
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
							<h2><i class="halflings-icon edit"></i><span class="break"></span><a href="purchaseRet.php"><?=RETURNED_PURCHASES?></a> &gt;&gt; <?=ADD_NEW?></h2>
						</div>
						<div class="box-content" id="printed">
							<form class="form-horizontal" method="GET" action="add_purchaseRet.php">
								<fieldset style="border-bottom: #e7e7e7 solid medium">
									<div class="control-group">
										<div class="salesOptions">
											<label class="control-label" for="purchInvoice" style="width: 40%"><?=PURCHASE_INVOICE?> : </label>
											<div class="controls">
												<select name="purchInvoice" id="purchInvoice" data-rel="chosen" onchange="this.form.submit()">
													<option value=""> </option>
													<?php
													$selectedPurch = trim((string)($_REQUEST["purchInvoice"] ?? ''));
													$query = "SELECT invoicenumber FROM purchase WHERE state='1' AND (storeid='$storeid' OR storeid='0') ORDER BY invoicenumber DESC";
													$res   = mysqli_query($adController->MySQL, $query) or die(mysqli_error($adController->MySQL));
													while($data = mysqli_fetch_assoc($res))
													{
														$invNum = $data['invoicenumber'];
														$sel = ($selectedPurch !== '' && $selectedPurch == $invNum) ? "selected" : "";
														echo "<option value='" . htmlspecialchars($invNum) . "' $sel>" . htmlspecialchars($invNum) . "</option>";
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
							if (!empty($selectedPurch))
							{
								$purchInvEsc = mysqli_real_escape_string($adController->MySQL, $selectedPurch);
								$queryCheck  = "SELECT * FROM purchase_ret WHERE purchInvoice='$purchInvEsc' AND state='1' LIMIT 1";
								$resCheck    = mysqli_query($adController->MySQL, $queryCheck);
								$alreadyReturned = ($resCheck && mysqli_num_rows($resCheck) > 0);

								if (!$alreadyReturned)
								{ 
									$queryPurch = "SELECT * FROM purchase WHERE invoicenumber='$purchInvEsc' LIMIT 1";
									$resPurch   = mysqli_query($adController->MySQL, $queryPurch) or die(mysqli_error($adController->MySQL));
									$dataPurch  = mysqli_fetch_assoc($resPurch) ?: [];

									$defBranch = !empty($dataPurch['branch']) ? (int)$dataPurch['branch'] : 1;
									$invoice = $adController->getNewInvoiceNo("purchase_ret", "invoicenumber", '22', $defBranch);

									$supId = (int)($dataPurch['supplier'] ?? 0);
									$querySup = "SELECT name_ar, name_en FROM suppliers WHERE id='$supId' LIMIT 1";
									$resSup = mysqli_query($adController->MySQL, $querySup);
									$dataSup = $resSup ? mysqli_fetch_assoc($resSup) : [];
									$supplierName = htmlspecialchars(!empty($dataSup['name_ar']) ? $dataSup['name_ar'] : ($dataSup['name_en'] ?? ''));

									$brId = (int)($dataPurch['branch'] ?? 0);
									$queryBr = "SELECT name_ar, name_en FROM branches WHERE id='$brId' LIMIT 1";
									$resBr = mysqli_query($adController->MySQL, $queryBr);
									$dataBr = $resBr ? mysqli_fetch_assoc($resBr) : [];
									$branchName = htmlspecialchars(!empty($dataBr['name_ar']) ? $dataBr['name_ar'] : ($dataBr['name_en'] ?? ''));
								?>
								<form class="form-horizontal" id="purchaseRet-form" method="POST" action="#" autocomplete="off">
									<input type="hidden" value="addPurchaseRet" name="f">
									<input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
									<input type="hidden" value="<?=htmlspecialchars($selectedPurch)?>" name="purchInvoice">
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
													<input type="text" class="datepicker-nobar" name="purchase_expiry" value="<?=date("Y-m-d")?>">
												</div>
											</div>
										</div>
										<br>
										<div class="control-group">
											<div class="salesOptions">
												<label class="control-label"><?=SUPPLIER?> : </label>
												<div class="controls">
													<input type="hidden" value="<?=$supId?>" name="supplier">
													<input type="text" class="typeahead" value="<?=$supplierName?>" readonly>
												</div>
											</div>
											<div class="salesOptions">
												<label class="control-label"><?=SUPPLIER_INVOICE?> : </label>
												<div class="controls">
													<input type="text" 
														   class="typeahead"  
														   name="supplier_invoice" 
														   value="<?=htmlspecialchars($dataPurch['supplier_invoice'] ?? '')?>"
														   readonly>
												</div>
											</div>
										</div>
										<br>
										<div class="control-group">                                                                                                                       
											<div class="salesOptions">
												<label class="control-label"><?=BRANCH?> :</label>
												<div class="controls">
													<input type="hidden" value="<?=$brId?>" name="branch">
													<input type="text" class="typeahead" value="<?=$branchName?>" readonly>
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
										<div class="control-group">
											<div class="controls" style="margin-top:20px; margin-left:0px">	
												<table class="table-bordered" style="width:100%">                                                
													<tr>
														<th><?=BARCODE?> | <?=ITEM_NAME?> | <?=UNITS?></th>    
														<th><?=STORES?></th>
														<th><?=QUANTITY?></th>
														<th><?=PRICE?></th>
														<th><?=DISCOUNT?> %</th>
														<th><?=TOTAL_A_DIS?></th>
														<th><?=VAT?> %</th>
														<th><?=VAT_VALUE?></th>
														<th><?=TOTAL?></th>
														<th class="edit">&nbsp;</th>
														<th class="edit">&nbsp;</th>
													</tr>
													<?php
													// Pre-cache purchase items with stores and units via single query (fixed WHEREW bug)
													$purchItemsCache = [];
													$queryPurchItems = "SELECT items.id AS item_id, items.barcode, items.name_ar, items.name_en,
																			   pi.store, pi.cost, pi.quantity, pi.tax, pi.discount AS sdis,
																			   s.name_ar AS store_name_ar, s.name_en AS store_name_en,
																			   u.name_ar AS unit_name_ar, u.name_en AS unit_name_en
																		FROM purchase_items pi
																		INNER JOIN items ON items.id = pi.itemid
																		LEFT JOIN stores s ON pi.store = s.id
																		LEFT JOIN units u ON items.unit = u.id
																		WHERE pi.invoice_No = '$purchInvEsc'";
													$resPurchItems = mysqli_query($adController->MySQL, $queryPurchItems);
													if ($resPurchItems) {
														while ($piRow = mysqli_fetch_assoc($resPurchItems)) {
															$purchItemsCache[] = $piRow;
														}
													}

													for ($i = 0; $i < 10; $i++)
													{
													?>
													<tr id="row-v-<?=$i?>">
														<td style="width:20%">
															<select id="barcode-<?=$i?>" name="barcode[]" data-rel="chosen" class="barcode-select-Ret" style="width:100%">
																<option value=""> </option>
																<?php
																foreach ($purchItemsCache as $data) {
																	$barcode   = !empty($data['barcode']) ? $data['barcode'] : "-----";
																	$itemName  = !empty($data['name_ar']) ? $data['name_ar'] : (!empty($data['name_en']) ? $data['name_en'] : '---');
																	$storeName = !empty($data['store_name_ar']) ? $data['store_name_ar'] : (!empty($data['store_name_en']) ? $data['store_name_en'] : '---');
																	$unitName  = !empty($data['unit_name_ar']) ? $data['unit_name_ar'] : (!empty($data['unit_name_en']) ? $data['unit_name_en'] : "-----");
																	echo "<option value='{$data['item_id']}' "
																		 . "index='$i' "
																		 . "store='{$data['store']}' "
																		 . "storeName='{$storeName}' "
																		 . "qty='{$data['quantity']}' "
																		 . "cost='{$data['cost']}' "
																		 . "dis='{$data['sdis']}' "
																		 . "vat='{$data['tax']}'>{$barcode} | {$itemName} | {$unitName}</option>";
																}
																?>
															</select>
														</td>
														<td style="width:10%">
															<input type="hidden" id="store-<?=$i?>" name="store[]">
															<input type="text" id="storeName-<?=$i?>" class="store span12 typeahead" maxlength="100" readonly>
														</td>                                
														<td>
															<input type="text" id="item-qty-<?=$i?>" class="item-qty span12 typeahead" maxlength="100" name="item_qty[]">
														</td>
														<td>
															<input type="text" id="item-price-<?=$i?>" class="item-price span12 typeahead" maxlength="100" name="item_price[]" readonly>
														</td>
														<td>
															<input type="text" id="item-discount-<?=$i?>" class="item-discount span12 typeahead" maxlength="100" name="item_disc[]" style="width:75%;" readonly> 
														</td>
														<td>
															<input type="text" id="item-total-after-dis-<?=$i?>" class="item-total-after-dis span12 typeahead" maxlength="100" name="item_total_after_dis[]" readonly>
														</td>
														<td>
															<input type="text" id="item-vat-<?=$i?>" class="item-vat span12 typeahead" maxlength="100" name="item_vat[]" style="width:75%;" readonly> 
														</td>
														<td>
															<input type="text" id="item-vat-value-<?=$i?>" class="item-vat-value span12 typeahead" maxlength="100" name="item_vat_value[]" readonly>
														</td>
														<td>
															<input type="text" id="item-total-<?=$i?>" class="item-total span12 typeahead" maxlength="100" name="item-total[]" readonly>
														</td>
														<td class="del-parchaseRet-item mult-padding edit" prop="<?=$i?>"><label>x</label></td>
														<td class="more-parchaseRet-item mult-padding edit" prop="<?=$i?>"><label>+</label></td>
													</tr>
													<?php } ?>
												</table>
											</div>
										</div>                                  				

										<div class="control-group">
											<div style="margin-top:20px; float: right; margin-right: 20px">
												<table class="table-bordered">                                                
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
											<button type="button" id="submit-purchaseRet" class="btn btn-primary"><?=SAVE?></button>
											<button type="reset" class="btn"><?=CANCEL?></button>
										</div>
										<p class="error-red"></p>
									</fieldset>
								</form>   
								<?php
								}
								else 
								{
									$dataRet = mysqli_fetch_assoc($resCheck);
									$retInvoice = $dataRet["invoicenumber"] ?? '';
								?>  
								<form class="form-horizontal">
									<div class="control-group">
										<center>
											<label>
												<?=INVOICE_RETURNED_ALREADY?>
											</label>
											<div>
												<a class="btn btn-info" href="edit_purchaseRet.php?sd=<?=urlencode($retInvoice)?>">
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

			$(".item-qty").on('input change', function()
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
