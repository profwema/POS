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
						<h2><i class="halflings-icon edit"></i><span class="break"></span><?=JOURNAL?></h2>
					</div>
					<div class="box-content">
						<p align="right">

                                                    <a class="btn btn-success" href="add_journal.php">
								<i class="icon-plus"></i>
							</a>
						</p>
                                                <table class="table table-striped table-bordered bootstrap-datatable datatable">
                                                    <thead>
                                                      <tr>
                                                              <th><?=JOURNAL_NUMBER?></th>
                                                              <th><?=DECUMENT?></th>                                                              
                                                              <th><?=DISCRIPTION?></th>
                                                              <th><?=BRANCH?></th>
                                                              <th><?=DEBIT?></th>
                                                              <th><?=CREDIT?></th>
                                                              <th><?=DATE?></th>
                                                              <th><?=EDITING?></th>

                                                      </tr>
                                                    </thead>   
						  	<tbody>
								<?php
									$language 	= LANG;
                                                                        $storeid	= $_SESSION['storeid'];

									$query	 	= "SELECT * FROM journal WHERE state = '1'";
                                                                        
									$res 	 	= mysqli_query($adController->MySQL,$query) or die(mysqli_error($adController->MySQL));
									while($data 	= mysqli_fetch_assoc($res))
									{

                                                                                $pranch           = $data['brarnch'];
                                                                                $query		  = "SELECT * FROM branches  WHERE id ='$pranch' AND storeid='$storeid'";
										$resBra		  = mysqli_query($adController->MySQL,$query);
										$dataBra 	  = mysqli_fetch_assoc($resBra);
                                                                                $branch	          = $dataBra['name_'.$language];
										$journalNo      = $data["journalNo"];										
                                                                                $docNo          = $data["docNo"];
                                                                                $disc          = $data["disc"];
										$totalDebit    = $data["totalDebit"];
										$totalCredit    = $data["totalCredit"];                                                                                
										$date           = $data['date'];
//                                                                                $idval		= urlencode($adController->encrypt_decrypt(1,$journalNo,0));
//										$invoiceTable	= urlencode($adController->encrypt_decrypt(1,'journal',0));
//                                                                                $detailTable	= urlencode($adController->encrypt_decrypt(1,'journal_items',0));
//										$secondIdval	= urlencode($adController->encrypt_decrypt(1,$idval,0));
								

										echo "<tr>";
											echo "<td>$journalNo</td>";
                                                                                        echo "<td>$docNo</td>";
                                                                                        echo "<td>$branch</td>";
                                                                                        echo "<td>$disc</td>";
                                                                                        echo "<td>$totalDebit</td>";
                                                                                        echo "<td>$totalCredit</td>";                                                                                        
											echo "<td>$date</td>";
											echo "<td class='center'>
													<a class='btn btn-info' href='edit_journal.php?no=$journalNo'>
														<i class='halflings-icon white edit'></i>  
													</a>
													<a class='btn btn-danger' href='javascript:void(0)' onclick='javascript:deleteJournal(\"$journalNo\");'>
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
