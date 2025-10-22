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
			<div>  <!--class="row-fluid sortable"-->
				<div class="box span12">
					<div class="box-header" data-original-title>
						<h2><i class="halflings-icon edit"></i><span class="break"></span><?=QUOTATIONS?></h2>
					</div>
					<div class="box-content">
						<p align="right">
                                                    <a class="btn btn-primary" href="quote_settings.php"><?php echo 'اعدادات الطباعة' ?></a>

                                                    <a class="btn btn-success" href="add_qouta.php">
								<i class="icon-plus"></i>
							</a>
						</p>
                                                <table class="table table-striped table-bordered bootstrap-datatable datatable">
                                                    <thead>
                                                      <tr>
                                                              <th><?=INVOICE_NUMBER?></th>
                                                              <th><?=CUSTOMER_NAME?></th>
                                                              <th><?=DECUMENT?></th>
                                                              <th><?=BRANCH?></th>
                                                              <th><?=SALING_DATE?></th>
                                                              <th><?=CREATED_ON?></th>
                                                              <th><?=EDITING?></th>

                                                      </tr>
                                                    </thead>   
						  	<tbody>
								<?php
									$language 	= LANG;
									$storeid	= $_SESSION['storeid'];
									$query	 	= "SELECT * FROM quote WHERE storeid='$storeid' AND state = '1'";
									$res 	 	= mysqli_query($adController->MySQL,$query) or die(mysqli_error($adController->MySQL));
									while($data 	= mysqli_fetch_assoc($res))
									{
										$query		  = "SELECT * FROM customers  WHERE id='$data[supplier]' AND storeid='$storeid'";
										$resSup		  = mysqli_query($adController->MySQL,$query);
										$dataSup 	  = mysqli_fetch_assoc($resSup);
										$customer	= $dataSup['name_'.$language];

                                                                                $query		  = "SELECT * FROM branches  WHERE id='$data[branch]' AND storeid='$storeid'";
										$resBra		  = mysqli_query($adController->MySQL,$query);
										$dataBra 	  = mysqli_fetch_assoc($resBra);
                                                                                $branch	          = $dataBra['name_'.$language];

						
										$invoice	= $data["invoicenumber"];	
										
										
                                                                                $supp_invoice	= $data["supplier_invoice"];
										$dated		= $data["dated"];
										$purchase_date	= $data['purchase_date'];


                                                                                $idval		= urlencode($adController->encrypt_decrypt(1,$data['id'],0));
										$invoiceTable	= urlencode($adController->encrypt_decrypt(1,'quote',0));
                                                                                $detailTable	= urlencode($adController->encrypt_decrypt(1,'quote_items',0));
                                                                                //$otherTable	= urlencode($adController->encrypt_decrypt(1,'outgo',0));

										$secondIdval	= urlencode($adController->encrypt_decrypt(1,$idval,0));
								

										echo "<tr>";
											echo "<td>$invoice</td>";
                                                                                        echo "<td>$customer</td>";
                                                                                        echo "<td>$supp_invoice</td>";
                                                                                        echo "<td>$branch</td>";
											echo "<td>$purchase_date</td>";
											echo "<td>$dated</td>";
											echo "<td class='center'>
													<a class='btn btn-info' href='edit_qouta.php?sd=$secondIdval'>
														<i class='halflings-icon white edit'></i>  
													</a>
													<a class='btn btn-danger' href='javascript:void(0)' onclick='javascript:deleteInvoice(\"$invoiceTable\",\"$detailTable\",\"$idval\",\"$otherTable\");'>
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
