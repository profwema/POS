<?php
error_reporting(E_ALL);
require_once("top.php");
require_once("redirection.php");
require_once("controller.php");

$pgName = basename(__FILE__);
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
	<meta charset="utf-8">
	<title>WAM Tech Soft - <?=PURCHASES?></title>
	<?php require_once("header.php");?>	
	<link href="css/bootstrap-datetimepicker.css" rel="stylesheet" media="screen">
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
							<h2><i class="halflings-icon edit"></i><span class="break"></span><?=PURCHASES?></h2>
						</div>
						<div class="box-content">
							<p align="right">
								<a class="btn btn-success" href="add_purchase.php" title="<?=ADD_NEW?>">
									<i class="icon-plus"></i> <?=ADD_NEW?>
								</a>
							</p>

							<form action="<?=$pgName?>" class="form-horizontal" id="form" method="post" name="form" autocomplete="off">
								<fieldset>
									<div class="control-group">
										<div class="salesOptions">                               
											<label class="control-label" for="fr_date"><?=FROM_DATE?></label>
											<div class="controls">
												<input type="text" class="datepicker-nobar" id="fr_date" name="fr_date" value="<?=htmlspecialchars($_POST['fr_date'] ?? '')?>">
											</div>
										</div>
										<div class="salesOptions">                                                                 
											<label class="control-label" for="to_date"><?=TO_DATE?></label>
											<div class="controls">
												<input type="text" class="datepicker-nobar" id="to_date" name="to_date" value="<?=htmlspecialchars($_POST['to_date'] ?? '')?>">
											</div>
										</div>
									</div>                                                                      
									<button type="submit" name="submit" id="dateSearch" style="margin-left: 10px" class="btn btn-primary"><?=LOAD_FORM?></button>
								</fieldset>
							</form>

							<table class="table table-striped table-bordered bootstrap-datatable datatable">
								<thead>
									<tr>
										<th><?=INVOICE_NUMBER?></th>
										<th><?=SUPPLIER_NAME?></th>
										<th><?=SUPPLIER_INVOICE?></th>
										<th><?=BRANCH?></th>
										<th><?=DATE?></th>
										<th><?=REPORT?></th>
										<th><?=EDITING?></th>
									</tr>
								</thead>   
								<tbody>
									<?php
										$search = '';
										$fdate  = !empty($_POST['fr_date']) ? mysqli_real_escape_string($adController->MySQL, trim($_POST['fr_date'])) : '';
										if ($fdate) $search .= " AND p.invoice_date >= '$fdate 00:00:00'";
										$tdate  = !empty($_POST['to_date']) ? mysqli_real_escape_string($adController->MySQL, trim($_POST['to_date'])) : '';
										if ($tdate) $search .= " AND p.invoice_date <= '$tdate 23:59:59'";

										$language = defined('LANG') ? LANG : 'ar';
										$storeid  = (int)($_SESSION['storeid'] ?? 0);

										$query = "SELECT p.*, 
														 s.name_{$language} AS supplier_name, 
														 b.name_{$language} AS branch_name 
												  FROM purchase p 
												  LEFT JOIN suppliers s ON p.supplier = s.id AND s.storeid = '$storeid' 
												  LEFT JOIN branches b ON p.branch = b.id AND b.storeid = '$storeid' 
												  WHERE p.storeid = '$storeid' AND p.state = '1' $search 
												  ORDER BY p.invoicenumber DESC";
										$res = mysqli_query($adController->MySQL, $query) or die(mysqli_error($adController->MySQL));
										while($data = mysqli_fetch_assoc($res))
										{
											$supplier      = htmlspecialchars($data['supplier_name'] ?? '');
											$branch        = htmlspecialchars($data['branch_name'] ?? '');
											$invoice       = htmlspecialchars($data["invoicenumber"] ?? '');
											$supp_invoice  = htmlspecialchars($data["supplier_invoice"] ?? '');
											$date          = htmlspecialchars($data['invoice_date'] ?? '');

											$idval         = urlencode($adController->encrypt_decrypt(1, $data["invoicenumber"] ?? '', 0));
											$invoiceTable  = urlencode($adController->encrypt_decrypt(1, 'purchase', 0));
											$otherTable    = urlencode($adController->encrypt_decrypt(1, 'income', 0));

											echo "<tr>";
											echo "<td>$invoice</td>";
											echo "<td>$supplier</td>";
											echo "<td>$supp_invoice</td>";
											echo "<td>$branch</td>";
											echo "<td>$date</td>";
											echo "<td class='center'>
													<a href='reports/purchase.php?sd=" . urlencode($data["invoicenumber"] ?? '') . "' target='_blank' title='" . (defined('PRINT_INVOICE') ? PRINT_INVOICE : 'طباعة') . "'>
														<img src='img/print.png' style='width:30px' alt='طباعة'>
													</a>
												  </td>";                                                                                           
											echo "<td class='center'>
													<a class='btn btn-info' href='edit_purchase.php?sd=$idval' title='" . (defined('EDIT') ? EDIT : 'تعديل') . "'>
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
