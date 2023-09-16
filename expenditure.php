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
						<h2><i class="halflings-icon edit"></i><span class="break"></span><?=EXPENDITURE?></h2>
					</div>
					<div class="box-content">
						<p align="right">
							<a class="btn btn-success" href="new_expenditure.php">
								<i class="icon-plus"></i>
							</a>
						</p>
						<table class="table table-striped table-bordered bootstrap-datatable datatable">
						  	<thead>
							  <tr>
								  <th><?=BRANCH?></th>
								  <th><?=DESCRIPTION?></th>
								  <th><?=DATED?></th>
								  <th><?=EXPENDITURE?></th>
								  <th><?=EDITING?></th>
							  </tr>
						  	</thead>   
						  	<tbody>
								<?php
									$language 	= LANG;
									$storeid	= $_SESSION['storeid'];
									$query	 	= "SELECT * FROM expenditure  WHERE storeid='$storeid'";
									$res 	 	= mysqli_query($adController->MySQL,$query) or die(mysqli_error($adController->MySQL));
									while($data 	= mysqli_fetch_assoc($res))
									{
										
										
										if($data[store_branch]!="")
										{							
											$query	= "SELECT * FROM branches WHERE id IN ($data[store_branch]) AND storeid='$storeid'";
											$resBranch	  = mysqli_query($adController->MySQL,$query);
											while($dataBranch = mysqli_fetch_assoc($resBranch))
											{
												$idval	= urlencode($adController->encrypt_decrypt(1,$dataBranch['id'],0));
												$secondIdval= urlencode($adController->encrypt_decrypt(1,$idval,0));
												$arrayBranch[count($arrayBranch)] ="<a href='edit_branch.php?sd=$secondIdval' target='_blank'>". $dataBranch['name_'.$language]."</a>";
											}
										}
										$branchName	= implode("<br>",$arrayBranch);
										if(count($arrayBranch) == 0)
										{	
											$branchName = GENERAL;
										}

										$idval		= urlencode($adController->encrypt_decrypt(1,$data['id'],0));
										$tableName	= urlencode($adController->encrypt_decrypt(1,'expenditure',0));
										$secondIdval	= urlencode($adController->encrypt_decrypt(1,$idval,0));
										$desc		= $data['desc_'.$language];
										$dated		= date("m/d/Y",strtotime($data['dated']));
										$cost		= $data['cost'];
										echo "<tr>";
											echo "<td>$branchName</td>";
											echo "<td>$desc</td>";
											echo "<td>$dated</td>";
											echo "<td>$cost</td>";
											echo "<td class='center'>
													<a class='btn btn-info' href='edit_exp.php?sd=$secondIdval'>
														<i class='halflings-icon white edit'></i>  
													</a>
													<a class='btn btn-danger' href='javascript:void(0)' onclick='javascript:deleteData(\"$tableName\",\"$idval\",\"$dirVal\");'>
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
