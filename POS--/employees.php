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
			    <h2><i class="halflings-icon edit"></i><span class="break"></span><?=EMPLOYEES?></h2>
			</div>
			<div class="box-content">
			    <p align="right">
				<a class="btn btn-success" href="add_employee.php">
				    <i class="icon-plus"></i>
				</a>
			    </p>
			    <table class="table table-striped table-bordered bootstrap-datatable datatable">
				<thead>
				    <tr>
				      <th><?=IMAGE?></th>
				      <th><?=EMPLOYEE_NAME?></th>
				      <th><?=LOGIN_ID?></th>
				      <th><?=IN_BRANCH?></th>
				      <th><?=DESIGNATION?></th>
				      <th><?=MOBILE?></th>
				      <th><?=EDITING?></th>
				    </tr>
				</thead>   
				<tbody>
				    <?php
				    $language 	= LANG;
				    $storeid	= $_SESSION['storeid'];
				    $query	 	= "SELECT * FROM employees  WHERE storeid='$storeid'";
				    if(!($res 	 	= mysqli_query($adController->MySQL,$query)))
					    echo mysqli_error($adController->MySQL);
				    while($data 	= mysqli_fetch_assoc($res))
				    {
					$arrayBranch	  = array();
					if($data[store_branch]!="")
					    $query  = "SELECT * FROM branches WHERE id IN ($data[tore_branch]) AND storeid='$storeid'";
					else
					    $query  = "SELECT * FROM branches WHERE storeid='$storeid'";
					$resBranch	  = mysqli_query($adController->MySQL,$query);
					while($dataBranch = mysqli_fetch_assoc($resBranch))
					{
					    $idval	= urlencode($adController->encrypt_decrypt(1,$dataBranch['d'],0));
					    $secondIdval= urlencode($adController->encrypt_decrypt(1,$idval,0));
					    $arrayBranch[count($arrayBranch)] ="<a href='edit_branch.php?sd=$secondIdval' target='_blank'>". $dataBranch['name_'.$language]."</a>";
					}
					$name	 	= $data["name_$language"];
					$branchName	= implode("<br>",$arrayBranch);
					$email		= $data["email"];
					$contact	= $data['contact'];
					
					$query	 	= "SELECT * FROM user_rights  WHERE id='$data[type]'";
					$resType 	= mysqli_query($adController->MySQL,$query) ;
					$dataType	= mysqli_fetch_assoc($resType);
					$typeVal	= $dataType['designation_'.$language];

					$dirVal		= urlencode($adController->encrypt_decrypt(1,DIR_EMP_NAME,0));
					$idval		= urlencode($adController->encrypt_decrypt(1,$data['id'],0));
					$tableName	= urlencode($adController->encrypt_decrypt(1,'employees',0));
					$secondIdval	= urlencode($adController->encrypt_decrypt(1,$idval,0));

					$query 		= "SELECT * FROM images WHERE foreign_id='$data[id]' AND `table`='employees'";
					$resImage	= mysqli_query($adController->MySQL,$query);
					$dataImage	= mysqli_fetch_assoc($resImage);
					$img		= $adController->getDirectoryOnlyPath(DIR_EMP_NAME);
					$thumb		= $img.$dataImage['thumb'];
					if($dataImage['thumb'] == "")
						$thumb 	= NO_IMAGE;
					echo "<tr>";
					echo "<td><a href='javascript:void(0)' class='screenshot' rel='$img'><img src='$thumb' class='thumb'></a></td>";
					echo "<td>$name</td>";
					echo "<td>$email</td>";
					echo "<td>$branchName</td>";
					echo "<td>$typeVal</td>";
					echo "<td>$contact</td>";
					echo "<td class='center'>
					<a class='btn btn-info' href='edit_emp.php?sd=$secondIdval'>
						<i class='halflings-icon white edit'></i>  
					</a>
					<a class='btn btn-danger' href='javascript:void(0)' onclick='javascript:deleteData(\"$tableName\",\"$idval\",28);'>
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
