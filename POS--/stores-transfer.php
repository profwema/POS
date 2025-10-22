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
                            <!--class="row-fluid sortable"-->
				<div class="box span12">
					<div class="box-header" data-original-title>
						<h2><i class="halflings-icon edit"></i><span class="break"></span><?=STORE_TRANSFAIR?></h2>
					</div>
					<div class="box-content">
						<p align="right">
                                                    <a class="btn btn-success" href="add_storeTrans.php">
								<i class="icon-plus"></i>
							</a>
						</p>
                                                <table class="table table-striped table-bordered bootstrap-datatable datatable">
                                                    <thead>
                                                      <tr>
                                                              <th><?=TRANS_NUMBER?></th>
                                                              <th><?=STORE_FROM?></th>
                                                              <th><?=STORE_TO?></th>
                                                              <th><?=ENTRY_DATE?></th>
                                                              <th><?=CREATED_ON?></th>
                                                              <th><?=EDITING?></th>

                                                      </tr>
                                                    </thead>   
						  	<tbody>
								<?php
									$language 	= LANG;
									$storeid	= $_SESSION['storeid'];
									$query	 	= "SELECT * FROM store_trans WHERE storeid='$storeid' AND state = '1' ";
									$res 	 	= mysqli_query($adController->MySQL,$query) or die(mysqli_error($adController->MySQL));
									while($data 	= mysqli_fetch_assoc($res))
									{
						
										$invoice	  = $data["trans_number"];	

                                                                                $query		  = "SELECT * FROM stores  WHERE id='$data[storeFrom]' AND storeid='$storeid'";
										$resBra		  = mysqli_query($adController->MySQL,$query);
										$dataBra 	  = mysqli_fetch_assoc($resBra);
                                                                                $from	          = $dataBra['name_'.$language];                                                                                

                                                                                $query		  = "SELECT * FROM stores  WHERE id='$data[storeTo]' AND storeid='$storeid'";
										$resBra		  = mysqli_query($adController->MySQL,$query);
										$dataBra 	  = mysqli_fetch_assoc($resBra);
                                                                                $to	          = $dataBra['name_'.$language];                                                                                
										
                                                                                $trans_date	= $data['trans_date'];								
										$dated		= $data["dated"];
								
                                                                                $idval		= urlencode($adController->encrypt_decrypt(1,$invoice,0));
								
										$secondIdval	= urlencode($adController->encrypt_decrypt(1,$idval,0));

								

										echo "<tr>";
											echo "<td>$invoice</td>";
                                                                                        echo "<td>$from</td>";
                                                                                        echo "<td>$to</td>";
											echo "<td>$trans_date</td>";
											echo "<td>$dated</td>";
         										echo "<td class='center'>
													<a class='btn btn-info' href='edit_storeTrans.php?sd=$secondIdval'>
														<i class='halflings-icon white edit'></i>  
													</a>
													<a class='btn btn-danger' href='javascript:void(0)' onclick='javascript:deleteTrans(\"$idval\");'>
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
