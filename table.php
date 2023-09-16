<?php
require_once("top.php");
require_once("redirection.php");
require_once("controller.php");
$language 	= $_SESSION['lang'];
$storeid	= $_SESSION['storeid'];

$query	 	= "SELECT * FROM tables_count WHERE storeid='$storeid'";
$res		= mysqli_query($adController->MySQL,$query);
$dataCat	= mysqli_fetch_assoc($res);

if($data['store_branch']!="")						
	$branchArray	= explode(",",$dataCat['store_branch']);
else
{
	$query		  = "SELECT GROUP_CONCAT(id) AS br FROM branches WHERE storeid='$storeid'";
	$resBranch	  = mysqli_query($adController->MySQL,$query);
	$dataBranch 	  = mysqli_fetch_assoc($resBranch);
	$branchArray	  = explode(",",$dataBranch['br']);
}

$tableCount	= $dataCat['table_c'];

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>WAM Tech Soft</title>
	<?php require_once("script_php_variables.php");?>
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
						<h2><i class="halflings-icon edit"></i><span class="break"></span><?=TABLES?></h2>
					</div>
					<div class="box-content">
						<form class="form-horizontal" id="update-table">
						  <input type="hidden" value="tableUpdate" name="f">
						  <fieldset>
							<div class="control-group" style='display:none'>
							  <label class="control-label"><?=BRANCH?> :</label>
							  <div class="controls">
								  <select name="branch" id='branch-items'>
									<?php
										$query = "SELECT * FROM branches WHERE storeid='$storeid' ORDER BY name_en ASC";
										$res   = mysqli_query($adController->MySQL,$query) or die(mysqli_error($adController->MySQL));
										while($data= mysqli_fetch_assoc($res))
										{
											$id   = $data['id'];
											$name = $data['name_'.$language];
											$selected	="";
											if(in_array($data['id'],$branchArray))
												$selected=" selected='true' ";


											echo "<option value='$id' $selected>$name</option>";

										}
									?>
								  </select>
							  </div>
							</div>
							<div class="control-group">
							  <label class="control-label" for="typeahead"><?=TOTAL_TABLE_COUNT?> : </label>
							  <div class="controls">
								<input type="text" class="int-val span6 typeahead" maxlength="4"  name='table' id='table' value="<?=$dataCat['table_c']?>"> &nbsp; *
								 <span class="help-inline">&nbsp;</span>
							  </div>
							</div>
							<div class="form-actions">
							  <button type="button" id='submit-table' class="btn btn-primary"><?=SAVE?></button>
							  <button type="reset" class="btn"><?=CANCEL?></button>
							</div>
							<p>&nbsp;</p>
							<p class='error-red'>
								&nbsp;
								
							</p>

						  </fieldset>
						</form>   

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
