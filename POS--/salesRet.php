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
						<h2><i class="halflings-icon edit"></i><span class="break"></span><?=RETURNED_SALES?></h2>
					</div>
					<div class="box-content">
						<p align="right">
                                                    <a class="btn btn-success" href="add_SalesRet.php">
								<i class="icon-plus"></i>
							</a>
						</p>
                                                <table class="table table-striped table-bordered bootstrap-datatable datatable">
                                                    <thead>
                                                      <tr>
                                                              <th><?=INVOICE_NUMBER?></th>
                                                              <th><?=SALE_INVOICE?></th>   
                                                              <th><?=BRANCH?></th>
                                                              <th><?=CUSTOMER_NAME?></th>
                                                              <th><?=DOCUMENT?></th>
                                                              <th><?=DATE?></th>
                                                              <th><?=REPORT?></th>
                                                              <th><?=EDITING?></th>

                                                      </tr>
                                                    </thead>   
						  	<tbody>
								<?php
									$language 	= LANG;
									$storeid	= $_SESSION['storeid'];
									$query	 	= "SELECT * FROM sales_ret WHERE storeid='$storeid' AND state = '1' ORDER BY invoicenumber DESC ";
									$res 	 	= mysqli_query($adController->MySQL,$query) or die(mysqli_error($adController->MySQL));
									while($data 	= mysqli_fetch_assoc($res))
									{
										$query		  = "SELECT * FROM customers  WHERE id='$data[customer]' AND storeid='$storeid'";
										$resSup		  = mysqli_query($adController->MySQL,$query);
										$dataSup 	  = mysqli_fetch_assoc($resSup);
										$customer	= $dataSup['name_'.$language];

                                                                                $query		  = "SELECT * FROM branches  WHERE id='$data[branch]' AND storeid='$storeid'";
										$resBra		  = mysqli_query($adController->MySQL,$query);
										$dataBra 	  = mysqli_fetch_assoc($resBra);
                                                                                $branch	          = $dataBra['name_'.$language];
										$invoice	= $data["invoicenumber"];
                                                                                $salesInvoice   = $data['salesInvoice'];

                                                                                $supp_invoice	= $data["document"];
//										$dated		= $data["dated"];
										$date	        = $data['invoice_date'];



                                                                                $idval		= urlencode($adController->encrypt_decrypt(1,$invoice,0));
										$invoiceTable	= urlencode($adController->encrypt_decrypt(1,'sales_ret',0));
                                                                               $otherTable	= urlencode($adController->encrypt_decrypt(1,'income',0));
                                                                                
 								

										echo "<tr>";
											echo "<td>$invoice</td>";
											echo "<td>$salesInvoice</td>";
                                                                                         echo "<td>$branch</td>";
                                                                                        echo "<td>$customer</td>";
                                                                                        echo "<td>$supp_invoice</td>";
											echo "<td>$date</td>";
                                                                                        echo "<td class='center'>
                                                                                                                <a href='reports/reSales.php?sd=$invoice' target='_blank'>
                                                                                                                        <img src='img/print.png' style='width:30px'>
                                                                                                                </a>  </td>";                                                                                             
											echo "<td class='center'>
													<a class='btn btn-info' href='edit_salesRet.php?sd=$idval'>
														<i class='halflings-icon white edit'></i>  
													</a>
													<a class='btn btn-danger' href='javascript:void(0)' onclick='javascript:deleteInvoice(\"$invoiceTable\",\"$idval\",\"$otherTable\");'>
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
