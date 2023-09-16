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
			<div>
				<div class="box span12">
					<div class="box-header" data-original-title>
						<h2><i class="halflings-icon edit"></i><span class="break"></span><?=CUSTOMERS?></h2>
					</div>
					<div class="box-content">
						<p align="right">
                                                    <a class="btn btn-success" href="add_customer.php">
								<i class="icon-plus"></i>
							</a>
						</p>
						<table class="table table-striped table-bordered bootstrap-datatable datatable">
						  	<thead>
							  <tr>
								  <th><?=CUSTOMER_NAME?></th>
								  <th><?=TAX_NO?></th>
								  <th><?=EMAIL?></th>
								  <th><?=MOBILE?></th>
								  <th><?=EDITING?></th>
							  </tr>
						  	</thead>   
						  	<tbody>
								<?php
									$language 	= LANG;
									$storeid	= $_SESSION['storeid'];
									$query	 	= "SELECT * FROM customers  WHERE storeid='$storeid'";
									
					$res 	 	= mysqli_query($adController->MySQL,$query) or die(mysqli_error($adController->MySQL));
									while($data 	= mysqli_fetch_assoc($res))
									{
										
										$name	 	= $data["name_$language"];
										$email		= $data["email"];
										$phone	        = $data['phone'];
                                                                                $tax_no	        = $data['tax_no'];
                                                                                
										$idval		= urlencode($adController->encrypt_decrypt(1,$data['id'],0));
										$tableName	= urlencode($adController->encrypt_decrypt(1,'customers',0));
										$secondIdval	= urlencode($adController->encrypt_decrypt(1,$idval,0));

								

										echo "<tr>";
											echo "<td>$name</td>";
                                                                                        echo "<td>$tax_no</td>";
                                                                                        echo "<td>$email</td>";
											echo "<td>$phone</td>";
											echo "<td class='center'>
													<a class='btn btn-info' href='edit_customer.php?sd=$secondIdval'>
														<i class='halflings-icon white edit'></i>  
													</a>
													<a class='btn btn-danger' href='javascript:void(0)' onclick='javascript:deleteData(\"$tableName\",\"$idval\",1);'>
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
