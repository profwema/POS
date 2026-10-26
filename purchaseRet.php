<?php
error_reporting(E_ALL);
require_once("top.php");
require_once("redirection.php");
require_once("controller.php");
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
	<meta charset="utf-8">
	<title>WAM Tech Soft - <?=RETURNED_PURCHASES?></title>
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
							<h2><i class="halflings-icon edit"></i><span class="break"></span><?=RETURNED_PURCHASES?></h2>
						</div>
						<div class="box-content">
							<p align="right">
								<a class="btn btn-success" href="add_purchaseRet.php" title="<?=ADD_NEW?>">
									<i class="icon-plus"></i> <?=ADD_NEW?>
								</a>
							</p>
							<table class="table table-striped table-bordered bootstrap-datatable datatable">
								<thead>
									<tr>
										<th><?=INVOICE_NUMBER?></th>
										<th><?=PURCHASE_INVOICE?></th>    
										<th><?=BRANCH?></th>
										<th><?=SUPPLIER_NAME?></th>
										<th><?=SUPPLIER_INVOICE?></th>
										<th><?=DATE?></th>
										<th><?=REPORT?></th>
										<th><?=EDITING?></th>
									</tr>
								</thead>   
								<tbody>
									<?php
										$language = defined('LANG') ? LANG : 'ar';
										$storeid  = (int)($_SESSION['storeid'] ?? 0);
										$query    = "SELECT pr.*, 
														   s.name_{$language} AS supplier_name, 
														   b.name_{$language} AS branch_name 
													FROM purchase_ret pr 
													LEFT JOIN suppliers s ON pr.supplier = s.id AND s.storeid = '$storeid' 
													LEFT JOIN branches b ON pr.branch = b.id AND b.storeid = '$storeid' 
													WHERE pr.storeid = '$storeid' AND pr.state = '1' 
													ORDER BY pr.invoicenumber DESC";
										$res = mysqli_query($adController->MySQL, $query) or die(mysqli_error($adController->MySQL));
										while($data = mysqli_fetch_assoc($res))
										{
											$supplier      = htmlspecialchars($data['supplier_name'] ?? '');
											$branch        = htmlspecialchars($data['branch_name'] ?? '');
											$invoice       = htmlspecialchars($data["invoicenumber"] ?? '');
											$purchInvoice   = htmlspecialchars($data['purchInvoice'] ?? '');
											$supp_invoice  = htmlspecialchars($data["supplier_invoice"] ?? '');
											$date          = htmlspecialchars($data['invoice_date'] ?? '');

											$idval         = urlencode($adController->encrypt_decrypt(1, $data["invoicenumber"] ?? '', 0));
											$invoiceTable  = urlencode($adController->encrypt_decrypt(1, 'purchase_ret', 0));
											$otherTable    = urlencode($adController->encrypt_decrypt(1, 'outgo', 0));

											echo "<tr>";
											echo "<td>$invoice</td>";
											echo "<td>$purchInvoice</td>";
											echo "<td>$branch</td>";
											echo "<td>$supplier</td>";
											echo "<td>$supp_invoice</td>";
											echo "<td>$date</td>";
											echo "<td class='center'>
													<a href='reports/purchaseRet.php?sd=" . urlencode($data["invoicenumber"] ?? '') . "' target='_blank' title='" . (defined('PRINT_INVOICE') ? PRINT_INVOICE : 'طباعة') . "'>
														<img src='img/print.png' style='width:30px' alt='طباعة'>
													</a>
												  </td>";      
											echo "<td class='center'>
													<a class='btn btn-info' href='edit_purchaseRet.php?sd=$idval' title='" . (defined('EDIT') ? EDIT : 'تعديل') . "'>
														<i class='halflings-icon white edit'></i>  
													</a>
													<a class='btn btn-danger' href='javascript:void(0)' onclick='deleteInvoice(\"$invoiceTable\",\"$idval\",\"$otherTable\");' title='" . (defined('DELETE') ? DELETE : 'حذف') . "'>
														<i class='halflings-icon white trash'></i> 
													</a>
												  </td>";
											echo "</tr>";
										}
									?>
								</tbody>
							</table>            
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
