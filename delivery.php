<?php
error_reporting(E_ALL);
require_once("top.php");
require_once("redirection.php");
require_once("controller.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>WAM Tech Soft</title>
	<?php require_once("header.php");?>	
</head>

<body>
		<?php require_once("header_top.php");?>
	
		<div class="container-fluid-full">
		<div class="row-fluid">
			<?php require_once("left_menu.php");?>
			<div id="content" class="span10">
			<div class="row-fluid sortable">
				<div class="box span12">
					<div class="box-header" data-original-title>
						<h2><i class="halflings-icon money"></i><span class="break"></span><?=DISCOUNT?></h2>
					</div>
					<div class="box-content">
						<p align="right">
							<a class="btn btn-success" href="delivery_cost.php">
								<i class="icon-plus"></i>
							</a>
						</p>
						<table class="table table-striped table-bordered bootstrap-datatable datatable">
						  	<thead>
							  <tr>
								  <th><?=BRANCH?></th>
								  <th><?=DELIVERY_COST?></th>
								  <th><?=EDITING?></th>
							  </tr>
						  	</thead>   
						  	<tbody>
								<?php
									$language 	= LANG;
									$storeid	= $_SESSION['storeid'];
									$query	 	= "SELECT * FROM delivery  WHERE storeid='$storeid'";
									$res 	 	= mysqli_query($adController->MySQL,$query) or die(mysqli_error($adController->MySQL));
									while($data 	= mysqli_fetch_assoc($res))
									{
										$query		= "SELECT * FROM branches WHERE id='$data[branchid]' AND storeid='$storeid'";
										$resBranch	= mysqli_query($adController->MySQL,$query);
										$dataBranch 	= mysqli_fetch_assoc($resBranch);
										$branchName	= $dataBranch['name_'.$language];			
										$idval		= urlencode($adController->encrypt_decrypt(1,$data['id'],0));
				
										$tableName	= urlencode($adController->encrypt_decrypt(1,'delivery',0));

										echo "<tr>";
											echo "<td>$branchName</td>";
											echo "<td>$data[amount]</td>";
											echo "<td class='center'>
													<a class='btn btn-danger' href='javascript:void(0)' onclick='javascript:deleteData(\"$tableName\",\"$idval\",\"\");'>
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
