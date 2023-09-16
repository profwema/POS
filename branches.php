<?php
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
						<h2><i class="halflings-icon edit"></i><span class="break"></span><?=BRANCHES?></h2>
					</div>
					<div class="box-content">
						<p align="right">
							<a class="btn btn-success" href="add_branch.php">
								<i class="icon-plus"></i>
							</a>
						</p>
						<table class="table table-striped table-bordered bootstrap-datatable datatable">
						  	<thead>
							  <tr>
								  <th><?=BRANCH_NAME?></th>
								  <th><?=STREET?></th>
								  <th><?=CITY?></th>
								  <th><?=STATE?></th>
								  <th><?=COUNTRY?></th>
								  <th><?=PHONE?></th>
								  <th><?=FAX?></th>
								  <th><?=EDITING?></th>
							  </tr>
						  	</thead>   
						  	<tbody>
								<?php
									$language = LANG;
									$storeid	= $_SESSION['storeid'];
                                                                        $query	 	= "SELECT * FROM branches  WHERE storeid='$storeid'";
									//$query	 	= "SELECT b.*,c.city_name_$language AS city,con.cou_name_$language AS cou,st.st_name_$language AS stat FROM branches AS b , us_city_master AS c , us_country_master AS con , us_state_master AS st WHERE   b.city=c.city_id AND con.cou_id=b.country AND st.st_id=b.state ORDER BY id DESC";
									$res 	 	= mysqli_query($adController->MySQL,$query);
									while($data 	= mysqli_fetch_assoc($res))
									{
										
										$branchName 	= $data["name_$language"];
										$street		= $data["street_$language"];
										$city		= $data["city"];
										$state		= $data["stat"];
										$country	= $data["cou"];
										$phone		= $data["phone"];
										$fax		= $data['fax'];

										$idval		= urlencode($adController->encrypt_decrypt(1,$data['id'],0));
										$tableName	= urlencode($adController->encrypt_decrypt(1,'branches',0));
										$secondIdval	= urlencode($adController->encrypt_decrypt(1,$idval,0));

										echo "<tr>";
											echo "<td>$branchName</td>";
											echo "<td>$street</td>";
											echo "<td>$city</td>";
											echo "<td>$state</td>";
											echo "<td>$country</td>";
											echo "<td>$phone</td>";
											echo "<td>$fax</td>";
											echo "<td class='center'>
													<a class='btn btn-info' href='edit_branch.php?sd=$secondIdval'>
														<i class='halflings-icon white edit'></i>  
													</a>
													<a class='btn btn-danger' href='javascript:void(0)' onclick='javascript:deleteData(\"$tableName\",\"$idval\",\"$tableName\");'>
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
