<?php
require_once("top.php");
require_once("redirection.php");
require_once("controller.php");
$language 	= $_SESSION['lang'];
$storeid	= $_SESSION['storeid'];


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
						<h2><i class="halflings-icon edit"></i><span class="break"></span><?=SHIFTS?></h2>
					</div>
					<div class="box-content">
						<form class="form-horizontal" id="update-shifts">
						  <input type="hidden" value="shiftsUpdate" name="f">
						  <fieldset>
							
                                                      
                                                        <?php
                                                        
                                                            $query	 	= "SELECT * FROM shifts WHERE storeid='$storeid'";
                                                            $res		= mysqli_query($adController->MySQL,$query);
                                                            do
                                                            {
                                                                     $_REQUEST['from_time'] = $dataCat['starts'];
                                                                     $_REQUEST['to_time']   = $dataCat['ends'];
                                                                     $id                    = $dataCat['id'];
                                                                     $name                  = $dataCat['name_'.$language];
                                                                     $_REQUEST['from_time'] 
                                                        ?>
                                                                
                                                                    <div class="control-group">
<!--                                                                    <label class="control-label" for="typeahead">< ?=SHIFT?> : </label>-->
                                                                    <div class="controls">
                                                                            <input type="hidden" value='<?=$dataCat['id']?>' name='id[]'/>
                                                                            <input type="text" class="span3 typeahead"  name='name_en[]' id='name' value="<?=$dataCat['name_en']?>" placeholder='english name'>
                                                                            &nbsp;&nbsp;
                                                                            <input type="text" class="span3 typeahead"  name='name_ar[]' id='name' value="<?=$dataCat['name_ar']?>" placeholder='عربى'>
                                                                            &nbsp;&nbsp;
                                                                            <select  class='span2' name="from_time[]">
                                                                                    <option value="0"  <?php if($_REQUEST['from_time'] == "0") echo "selected='true'";?>>12:00 AM</option>
                                                                                    <option value="1"  <?php if($_REQUEST['from_time'] == "1") echo "selected='true'";?>>1:00 AM</option>
                                                                                    <option value="2"  <?php if($_REQUEST['from_time'] == "2") echo "selected='true'";?>>2:00 AM</option>
                                                                                    <option value="3"  <?php if($_REQUEST['from_time'] == "3") echo "selected='true'";?>>3:00 AM</option>
                                                                                    <option value="4"  <?php if($_REQUEST['from_time'] == "4") echo "selected='true'";?>>4:00 AM</option>
                                                                                    <option value="5"  <?php if($_REQUEST['from_time'] == "5") echo "selected='true'";?>>5:00 AM</option>
                                                                                    <option value="6"  <?php if($_REQUEST['from_time'] == "6") echo "selected='true'";?>>6:00 AM</option>
                                                                                    <option value="7"  <?php if($_REQUEST['from_time'] == "7") echo "selected='true'";?>>7:00 AM</option>
                                                                                    <option value="8"  <?php if($_REQUEST['from_time'] == "8") echo "selected='true'";?>>8:00 AM</option>
                                                                                    <option value="9"  <?php if($_REQUEST['from_time'] == "9") echo "selected='true'";?>>9:00 AM</option>
                                                                                    <option value="10"  <?php if($_REQUEST['from_time'] == "10") echo "selected='true'";?>>10:00 AM</option>
                                                                                    <option value="11"  <?php if($_REQUEST['from_time'] == "11") echo "selected='true'";?>>11:00 AM</option>
                                                                                    <option value="12"  <?php if($_REQUEST['from_time'] == "12") echo "selected='true'";?>>12:00 PM</option>
                                                                                    <option value="13"  <?php if($_REQUEST['from_time'] == "13") echo "selected='true'";?>>1:00 PM</option>
                                                                                    <option value="14"  <?php if($_REQUEST['from_time'] == "14") echo "selected='true'";?>>2:00 PM</option>
                                                                                    <option value="15"  <?php if($_REQUEST['from_time'] == "15") echo "selected='true'";?>>3:00 PM</option>
                                                                                    <option value="16"  <?php if($_REQUEST['from_time'] == "16") echo "selected='true'";?>>4:00 PM</option>
                                                                                    <option value="17"  <?php if($_REQUEST['from_time'] == "17") echo "selected='true'";?>>5:00 AM</option>
                                                                                    <option value="18"  <?php if($_REQUEST['from_time'] == "18") echo "selected='true'";?>>6:00 AM</option>
                                                                                    <option value="19"  <?php if($_REQUEST['from_time'] == "19") echo "selected='true'";?>>7:00 PM</option>
                                                                                    <option value="20"  <?php if($_REQUEST['from_time'] == "20") echo "selected='true'";?>>8:00 PM</option>
                                                                                    <option value="21"  <?php if($_REQUEST['from_time'] == "21") echo "selected='true'";?>>9:00 PM</option>
                                                                                    <option value="22"  <?php if($_REQUEST['from_time'] == "22") echo "selected='true'";?>>10:00 PM</option>
                                                                                    <option value="23"  <?php if($_REQUEST['from_time'] == "23") echo "selected='true'";?>>11:00 PM</option>
                                                                            </select>
                                                                        
                                                                            &nbsp;&nbsp;
                                                                        
                                                                            <select   class='span2' name="to_time[]">
										<option value="0"  <?php if($_REQUEST['to_time'] == "0") echo "selected='true'";?>>12:00 AM</option>
										<option value="1"  <?php if($_REQUEST['to_time'] == "1") echo "selected='true'";?>>1:00 AM</option>
                                                                                <option value="2"  <?php if($_REQUEST['to_time'] == "2") echo "selected='true'";?>>2:00 AM</option>
                                                                                <option value="3"  <?php if($_REQUEST['to_time'] == "3") echo "selected='true'";?>>3:00 AM</option>
                                                                                <option value="4"  <?php if($_REQUEST['to_time'] == "4") echo "selected='true'";?>>4:00 AM</option>
                                                                                <option value="5"  <?php if($_REQUEST['to_time'] == "5") echo "selected='true'";?>>5:00 AM</option>
                                                                                <option value="6"  <?php if($_REQUEST['to_time'] == "6") echo "selected='true'";?>>6:00 AM</option>
                                                                                <option value="7"  <?php if($_REQUEST['to_time'] == "7") echo "selected='true'";?>>7:00 AM</option>
										<option value="8"  <?php if($_REQUEST['to_time'] == "8") echo "selected='true'";?>>8:00 AM</option>
                                                                                <option value="9"  <?php if($_REQUEST['to_time'] == "9") echo "selected='true'";?>>9:00 AM</option>
                                                                                <option value="10"  <?php if($_REQUEST['to_time'] == "10") echo "selected='true'";?>>10:00 AM</option>
                                                                                <option value="11"  <?php if($_REQUEST['to_time'] == "11") echo "selected='true'";?>>11:00 AM</option>
                                                                                <option value="12"  <?php if($_REQUEST['to_time'] == "12") echo "selected='true'";?>>12:00 PM</option>
                                                                                <option value="13"  <?php if($_REQUEST['to_time'] == "13") echo "selected='true'";?>>1:00 PM</option>
                                                                                <option value="14"  <?php if($_REQUEST['to_time'] == "14") echo "selected='true'";?>>2:00 PM</option>
                                                                                <option value="15"  <?php if($_REQUEST['to_time'] == "15") echo "selected='true'";?>>3:00 PM</option>
                                                                                <option value="16"  <?php if($_REQUEST['to_time'] == "16") echo "selected='true'";?>>4:00 PM</option>
                                                                                <option value="17"  <?php if($_REQUEST['to_time'] == "17") echo "selected='true'";?>>5:00 AM</option>
                                                                                <option value="18"  <?php if($_REQUEST['to_time'] == "18") echo "selected='true'";?>>6:00 AM</option>
                                                                                <option value="19"  <?php if($_REQUEST['to_time'] == "19") echo "selected='true'";?>>7:00 PM</option>
                                                                                <option value="20"  <?php if($_REQUEST['to_time'] == "20") echo "selected='true'";?>>8:00 PM</option>
                                                                                <option value="21"  <?php if($_REQUEST['to_time'] == "21") echo "selected='true'";?>>9:00 PM</option>
                                                                                <option value="22"  <?php if($_REQUEST['to_time'] == "22") echo "selected='true'";?>>10:00 PM</option>
                                                                                <option value="23"  <?php if($_REQUEST['to_time'] == "23") echo "selected='true'";?>>11:00 PM</option>
									</select>
                                                                          
                                                                    </div>
                                                                  </div>
                                                        <?php
                                                            }while($dataCat      = mysqli_fetch_assoc($res));
                                                        ?>
                                                      
                                                      
                                                        
                                                      
                                                      
							
							<div class="form-actions">
							  <button type="button" id='submit-shifts' class="btn btn-primary"><?=SAVE?></button>
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
