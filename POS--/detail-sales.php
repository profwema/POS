<?php
error_reporting(E_ALL);
require_once("top.php");
require_once("redirection.php");
require_once("controller.php");
$storeid	= $_SESSION['storeid'];
$language	= $_SESSION['lang'];



$orderid	= $_POST['o'];
$catid		= $_POST['catid'];
$itemId		= $_POST['iid'];
$branchid	= $_POST['br'];
$empId		= $_POST['ei'];
$fDate		= $_POST['from_date'];
$tDate		= $_POST['to_date'];

$conditionArr	= array();

if($orderid != "")
	$conditionArr[] = " hasvalue='$orderid' ";
if(intval($catid) > 0)
	$conditionArr[] = " itemid IN (SELECT id FROM items WHERE catid='$catid' AND storeid='$storeid') ";
if(intval($itemId) > 0)
	$conditionArr[] = " itemid='$itemId' ";
if(intval($branchid) > 0)
	$conditionArr[] = " branch='$branchid' ";
if(intval($empId) > 0)
	$conditionArr[] = " userid='$empId' ";
if($fDate != "")
{
	$fDate 		= $adController->getDateValue($fDate);
	$fDate		= "$fDate 00:00:00";
	$conditionArr[] =  " dated >= '$fDate' ";
}
else
	$fDate		= date("Y-m-d H:i:s");

if($tDate != "")
{
	$tDate 		= $adController->getDateValue($tDate);
	$tDate		= "$tDate 23:59:59";
	$conditionArr[] =   " dated <= '$tDate' ";
}
else
	$tDate		= date("Y-m-d H:i:s");


if(count($conditionArr) == 0)
	$condition	= " WHERE storeid='$storeid' AND DATE(`dated`) = CURDATE()";
else
{
	$conditionArr	= implode(" AND ",$conditionArr);
	$conditionArr	= trim($conditionArr);
	$conditionArr	= trim($conditionArr,"AND");
	$condition	= " WHERE storeid='$storeid' AND $conditionArr";
}

// echo "<pre>";
// print_r($_POST);
// echo "</pre>";
//echo $condition;
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>WAM Tech Soft</title>
	<?php require_once("header.php");?>	

	<style>
		@media (max-width: 700px) {
			#mobile 
			{
				display:block;
			}
			#mobile .row-fluid
			{
				display:none;
			}
			#desktop
			{
				display:none;
			}
		}
		@media (min-width: 701px) {
			#mobile 
			{
				display:none;
			}
			#desktop
			{
				display:block;
			}
		}

	</style>
</head>

<body>
		<?php require_once("header_top.php");?>
		

		<?php include("detail_sales_logic.php");?>

		<div class="container-fluid-full">
		<div class="row-fluid">
			<?php require_once("left_menu.php");?>
			<div id="content" class="span10">
			<div>
					<div class="box span12">
					<div class="box-header" data-original-title>
						<h2><i class="halflings-icon edit"></i><span class="break"></span><?=$costStructure?></h2>
						<div class="box-icon">
							<a href="#" class="btn-minimize"><i class="halflings-icon chevron-up"></i></a>
						</div>
					</div>
					<div class="box-content">
						<form class="form-horizontal" action="<?=$pgName?>" id='form' method="POST">
							<fieldset>
								<div class="control-group">
								<label class="control-label" for="typeahead"><?=ORDER_ID?></label>
								<div class="controls">
									<select onchange="reloadPage()" class='span3' name="o">
										<option value=""></option>
										<?php
											$query 		= "SELECT DISTINCT hasvalue FROM orders  $condition";
											$res		= mysqli_query($adController->MySQL,$query);
											while($data 	= mysqli_fetch_assoc($res))
											{
												$oid	= $data['hasvalue'];
												$sel	= "";
												if($_REQUEST['o'] == $oid)
													$sel = " selected='true' ";
												echo "<option value='$oid' $sel>$oid</option>";
											}

										?>
											
									</select>
								</div>
								</div>
	

								<div class="control-group">
								<label class="control-label" for="typeahead"><?=CATEGORIES?></label>
								<div class="controls">
									<select onchange="reloadPage()"  class='span3' name="catid">
										<option value=""></option>
										<?php
											$query 		= "SELECT * FROM categories WHERE storeid='$storeid'";// IN ( SELECT catid FROM items WHERE id IN (SELECT DISTINCT itemid FROM orders  $condition))";
											$res		= mysqli_query($adController->MySQL,$query);
											while($data 	= mysqli_fetch_assoc($res))
											{
												$name	= $data['name_'.$language];
												if($name == "")
													$name	= $data['name_en'];
												$sel	= "";
												if($_REQUEST['catid'] == $data['id'])
													$sel = " selected='true' ";
												echo "<option value='$data[id]' $sel>$name</option>";
											}

										?>
											
									</select>
								</div>
								</div>

								<div class="control-group">
								<label class="control-label" for="typeahead"><?=ITEMS?></label>
								<div class="controls">
									<select onchange="reloadPage()"  class='span3' name="iid">
										<option value=""></option>
										<?php
											$query 		= "SELECT * FROM items WHERE id IN (SELECT DISTINCT itemid FROM orders  $condition)";
											$res		= mysqli_query($adController->MySQL,$query);
											while($data 	= mysqli_fetch_assoc($res))
											{
												$name	= $data['name_'.$language]." ".$data['qty_'.$language];
												if($name == "")
													$name	= $data['name_en']." ".$data['qty_en'];
												$sel	= "";
												if($_REQUEST['iid'] == $data['id'])
													$sel = " selected='true' ";
												echo "<option value='$data[id]' $sel>$name</option>";
											}

										?>
											
									</select>
								</div>
								</div>

								<div class="control-group">
								<label class="control-label" for="typeahead"><?=BRANCH?></label>
								<div class="controls">
									<select onchange="reloadPage()"  class='span3' name="br">
										<option value=""></option>
										<?php
											$query 		= "SELECT * FROM branches WHERE id IN (SELECT DISTINCT branch FROM orders  $condition)";
											$res		= mysqli_query($adController->MySQL,$query);
											while($data 	= mysqli_fetch_assoc($res))
											{
												$name	= $data['name_'.$language];
												if($name == "")
													$name	= $data['name_en'];
												$sel	= "";
												if($_REQUEST['br'] == $data['id'])
													$sel = " selected='true' ";
												echo "<option value='$data[id]' $sel>$name</option>";
											}

										?>
											
									</select>
								</div>
								</div>



								<div class="control-group">
								<label class="control-label" for="typeahead"><?=CASHIER?></label>
								<div class="controls">
									<select class='span3'  onchange="reloadPage()"  name="ei">
										<option value=""></option>
										<?php
											$query 		= "SELECT * FROM employees WHERE id IN (SELECT DISTINCT userid FROM orders  $condition)";
											$res		= mysqli_query($adController->MySQL,$query);
											while($data 	= mysqli_fetch_assoc($res))
											{
												$name	= $data['name_'.$language];
												if($name == "")
													$name	= $data['name_en'];
												$sel	= "";
												if($_REQUEST['ei'] == $data['id'])
													$sel = " selected='true' ";
												echo "<option value='$data[id]' $sel>$name</option>";
											}

										?>
											
									</select>
								</div>
								</div>
								
								<div class="control-group">
								<label class="control-label" for="date01"><?=FROM_DATE?></label>
								<div class="controls">
									<input type="date" class="span3 input-xlarge" id="from_date" name="from_date" value="<?php echo date("Y-m-d",strtotime($fDate));?>">
								</div>
								</div>

								<div class="control-group">
								<label class="control-label" for="date01"><?=TO_DATE?></label>
								<div class="controls">
									<input type="date" class="span3 input-xlarge" id="to_date" name="to_date" value="<?php echo date("Y-m-d",strtotime($tDate));?>">
								</div>
								</div>

								<div class="control-group">
								<label class="control-label" for="date01">&nbsp;</label>
								<div class="controls">
									<img src='img/vm.jpg' style="height:50px">&nbsp;&nbsp;<b>0</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
									<img src='img/images.jpg' style="height:50px">&nbsp;&nbsp;<b><?=$finalTotal?></b>
								</div>
								</div>
							</fieldset>
						</form>
					</div>
					</div>
					</div>
			<div>
				<div class="box span12">
					<div class="box-content" id="desktop">
						<table class="table table-striped table-bordered bootstrap-datatable datatable" >
						  	<thead>
							  <tr>
								  <th><?=DATED?></th>
								  <th><?=BRANCH?></th>
								  <th><?=ORDER_ID?></th>
								  <th><?=CATEGORY?></th>
								  <th><?=ITEMS?></th>
								  <th><?=QUANTITY?></th>
								  <th><?=PRICE?></th>
								  <th><?=DISCOUNT?></th>
								  <th><?=TOTAL?></th>
							  </tr>
						  	</thead>   
						  	<tbody>
								<?php echo implode("",$tBodyDesktop);?>
							</tbody>
					  </table>
					  </div>









					<div class="box-content" id="mobile">
					<table class="table table-striped table-bordered bootstrap-datatable datatable" >
						  	<thead>
							  <tr>
								  <th><?=DATED?></th>
								  <th><?=ORDER_ID?></th>
								  <th><?=ITEMS?></th>
								  <th><?=DETAIL?></th>
								  <th><?=PRICE?></th>
							  </tr>
						  	</thead>   
						  	<tbody>
								<?php echo implode("",$tBodyMobile);?>
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
