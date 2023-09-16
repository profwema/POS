<?php
error_reporting(E_ALL);
require_once("top.php");
require_once("redirection.php");
require_once("controller.php");
$storeid	= $_SESSION['storeid'];
$language	= $_SESSION['lang'];


$orderid	= $_POST['o'];
$catid		= $_POST['catid'];
$itemId		= $_POST['iid'];
$branchid	= $_POST['br'];
$empId		= $_POST['ei'];
if($_REQUEST['from_date'] == "" && $_REQUEST['to_date'] == "")
{
     $_REQUEST['to_date']    = date("Y-m-d");
     $_REQUEST['from_date']  = date('Y-m-d', strtotime('-1 day', strtotime($_REQUEST['to_date'])));
}
////////////////////////////////////////////////////////////
if($_REQUEST['from_time'] == "")
    $_REQUEST['from_time'] = "16";
if($_REQUEST['to_time'] == "")
    $_REQUEST['to_time'] = "3";

if(intval($_REQUEST['from_time']) < 10)
    $_REQUEST['from_time']    = "0".$_REQUEST['from_time'];

if(intval($_REQUEST['to_time']) < 10)
    $_REQUEST['to_time']    = "0".$_REQUEST['to_time'];
////////////////////////////////////////////////////////////



$fDate			= $_REQUEST['from_date']." ".$_REQUEST['from_time'].":00:00";
$ftDate         = $_REQUEST['from_date']." ".$_REQUEST['to_time'].":00:00";
$tDate			= $_REQUEST['to_date']." ".$_REQUEST['to_time'].":59:59";

if(strtotime($tDate) < strtotime($fDate))
{
   print("<script>alert('".TO_DATE_LESS_THAN_FROM."');</script>");
   $tDate                   = date('Y-m-d H:i:s',date(strtotime("+1 day", strtotime($_REQUEST['from_date']))));
   $_REQUEST['to_date']     = date("Y-m-d",strtotime($tDate));
   $_REQUEST['to_time']     = date("H:i:s",strtotime($tDate));
}

if(strtotime($fDate) > strtotime($tDate))
{
   print("<script>alert('".FROM_DATE_GREATER_THAN_TO."');</script>");
   $fDate                       = date('Y-m-d H:i:s',date(strtotime("-1 day", strtotime($_REQUEST['to_date']))));
   $_REQUEST['from_date']       = date("Y-m-d",strtotime($fDate));
   $_REQUEST['from_time']       = date("H:i:s",strtotime($fDate));
}

$conditionArr	= array();

if($orderid != "")
	$conditionArr[] = " hasvalue='$orderid' ";
if(intval($catid) > 0)
	$conditionArr[] = " itemid IN (SELECT id FROM items WHERE catid='$catid' ) ";
if(intval($itemId) > 0)
	$conditionArr[] = " itemid='$itemId' ";
if(intval($branchid) > 0)
	$conditionArr[] = " branch='$branchid' ";
if(intval($empId) > 0)
	$conditionArr[] = " userid='$empId' ";

$conditionArr[]            =  " specific_date >= '$fDate' ";
$conditionArr[]            =   " specific_date <= '$tDate' ";



if(count($conditionArr) == 0)
{
	$condition	= " WHERE  DATE(`dated`) = CURDATE()";
	$conditionLog	= " AND DATE(`dated`) = CURDATE()";
}
else
{
	$conditionArr	= implode(" AND ",$conditionArr);
	$conditionArr	= trim($conditionArr);
	$conditionArr	= trim($conditionArr,"AND");
	$condition	= " WHERE  $conditionArr";
	$conditionLog	= " AND $conditionArr";
}


//////////////// NEW /////////////////////

$conditionArray		= array();

$conditionArray[]	 = "o.specific_date >= '$fDate' AND o.specific_date <='$tDate'";
if(intval($itemId) > 0)
	$conditionArray[] = " o.itemid	='$itemId' ";
if(intval($branchid) > 0)
	$conditionArray[] = " o.branch   ='$branchid' ";
if(intval($empId) > 0)
	$conditionArray[] = " o.userid	='$empId' ";
	
$conditionSel		= implode(" AND ",$conditionArray);
if(count($conditionArray) > 0)
	$conditionSel	= " AND $conditionSel ";
	


?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>WAM Tech Soft</title>
	<?php require_once("header.php");?>	

	<style>
		@media (max-width: 700px) {
			#mobile 
			{
				display:block;
			}
			#mobile .row-fluid
			{
				display:none;
			}
			#desktop
			{
				display:none;
			}
		}
		@media (min-width: 701px) {
			#mobile 
			{
				display:none;
			}
			#desktop
			{
				display:block;
			}
		}
                
                .export-data
                {
                    padding: 0;
                    text-align: right;
                    display: block;
                    margin: 10px;
                }
                
                .export-data li
                {
                    display:inline;
                    margin-left:10px;
                    list-style:none;
                }

	</style>
</head>

<body>
		<?php require_once("header_top.php");?>
		

		<?php include("all_orders_logic.php");?>

		<div class="container-fluid-full">
		<div class="row-fluid">
			<?php require_once("left_menu.php");?>
			<div id="content" class="span10">
			<div>
					<div class="box span12">
					<div class="box-header" data-original-title>
						<h2><i class="halflings-icon edit"></i><span class="break"></span><?=$costStructure?></h2>
						<div class="box-icon">
							<a href="#" class="btn-minimize"><i class="halflings-icon chevron-up"></i></a>
						</div>
					</div>
					<div class="box-content">
						<form class="form-horizontal" action="<?=$pgName?>" id='form' method="POST">
							<fieldset>
								<div class="control-group" style='display:none'> 
								<label class="control-label" for="typeahead"><?=CATEGORIES?></label>
								<div class="controls">
									<select  onchange="reloadPage()"   class='span3' name="catid">
										<option value=""></option>
										<?php
											// $query 		= "SELECT * FROM categories WHERE storeid='$storeid'";// id IN ( SELECT catid FROM items WHERE id IN (SELECT DISTINCT itemid FROM orders  $condition))";
// 											$res		= mysqli_query($adController->MySQL,$query);
// 											while($data 	= mysqli_fetch_assoc($res))
// 											{
// 												$name	= $data['name_'.$language];
// 												if($name == "")
// 													$name	= $data['name_en'];
// 												$sel	= "";
// 												if($_REQUEST['catid'] == $data['id'])
// 													$sel = " selected='true' ";
// 												echo "<option value='$data[id]' $sel>$name</option>";
// 											}

										?>
											
									</select>
								</div>
								</div>

								<div class="control-group">
								<label class="control-label" for="typeahead"><?=ITEMS?></label>
								<div class="controls">
									<select  onchange="reloadPage()"   class='span3' name="iid">
										<option value=""></option>
										<?php
											$query 		= "SELECT id,name_".$language." FROM items  ORDER BY name_".$language." ASC";
											$res		= mysqli_query($adController->MySQL,$query);
											while($data 	= mysqli_fetch_assoc($res))
											{
												$name	= $data["name_".$language]." ".$data['qty_'.$language];
												if($name == "")
													$name	= $data['name_en']." ".$data['qty_en'];
												$sel	= "";
												if($_REQUEST['iid'] == $data['id'])
													$sel = " selected='true' ";
												echo "<option value='$data[id]' $sel>$name</option>";
											}

										?>
											
									</select>
								</div>
								</div>

								<div class="control-group">
								<label class="control-label" for="typeahead"><?=BRANCH?></label>
								<div class="controls">
                                                                    <select onchange="reloadPage()"  class='span3' name="br">
										<option value=""></option>
										<?php
											$query 		= "SELECT name_en,name_ar,id FROM branches ";
											$res		= mysqli_query($adController->MySQL,$query);
											while($data 	= mysqli_fetch_assoc($res))
											{
												$name	= $data['name_'.$language];
												if($name == "")
													$name	= $data['name_en'];
												$sel	= "";
												if($_REQUEST['br'] == $data['id'])
													$sel = " selected='true' ";
												echo "<option value='$data[id]' $sel>$name</option>";
											}

										?>
											
									</select>
								</div>
								</div>



								<div class="control-group">
								<label class="control-label" for="typeahead"><?=CASHIER?></label>
								<div class="controls">
									<select  onchange="reloadPage()"  class='span3'    name="ei">
										<option value=""></option>
										<?php
											$query 		= "SELECT name_en,name_ar,id FROM employees ";
											$res		= mysqli_query($adController->MySQL,$query);
											while($data 	= mysqli_fetch_assoc($res))
											{
												$name	= $data['name_'.$language];
												if($name == "")
													$name	= $data['name_en'];
												$sel	= "";
												if($_REQUEST['ei'] == $data['id'])
													$sel = " selected='true' ";
												echo "<option value='$data[id]' $sel>$name</option>";
											}

										?>
											
									</select>
								</div>
								</div>
								
                                                            <hr>
                                                                <?php
//                                                                     echo "<pre>";
//                                                                    print_r($_REQUEST);
//                                                                    echo "</pre>";
//                                                                    echo $condition;
                                                                ?>
								<div class="control-group">
								<label class="control-label" for="date01"><?=FROM_DATE?></label>
								<div class="controls">
									<input type="date"  class="span3 input-xlarge" id="from_date" name="from_date" value="<?php echo $_REQUEST['from_date'];?>" >
                                                                        &nbsp;
                                                                        <select onchange="reloadPage()"  class='span3' name="from_time">
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
								</div>
								</div>
                                                            
                                                            
                                                           
								<div class="control-group">
								<label class="control-label" for="date01"><?=TO_DATE?></label>
								<div class="controls">
									<input type="date" class="span3 input-xlarge" id="to_date" name="to_date" value="<?php echo $_REQUEST['to_date'];?>">
                                                                        &nbsp;
                                                                        <select onchange="reloadPage()"  class='span3' name="to_time">
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
                                                            
      
                                                            
                                                            <hr>

								<div class="control-group">
								<label class="control-label" for="date01">&nbsp;</label>
								<div class="controls">
									<img src='img/vm.jpg' style="height:50px">&nbsp;&nbsp;<b>0</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
									<img src='img/images.jpg' style="height:50px">&nbsp;&nbsp;<b><?=$finalTotal?></b>
								</div>
								</div>
							</fieldset>
						</form>
					</div>
					</div>
					</div>

			

			<div>
				<div class="box span12">
                                        <ul class="export-data">
                                            <li><a href="#" class="list-group-item" onclick="$('#sales-table').tableExport({type:'excel',escape:'false'});"><img src="img/xls.png" width="24"> XLS</a></li>
                                            <li><a href="#" class="list-group-item" onclick="$('#sales-table').tableExport({type:'doc',escape:'false'});"><img src="img/word.png" width="24"> Word</a></li>
                                            <li><a href="#" class="list-group-item" onclick="$('#sales-table').tableExport({type:'powerpoint',escape:'false'});"><img src="img/ppt.png" width="24"> PPT</a></li>
                                        
                                        </ul>
					<div class="box-content" id="desktop">
                                                
						<table class="table table-striped table-bordered bootstrap-datatable datatable" id="sales-table" >
						  	<thead>
							  <tr>
                                                                  <th><?=DATED?></th>
								  <th><?=BRANCH?></th>
								  <th><?=ORDER_ID?></th>
								  <th><?=CATEGORY?></th>
								  <th><?=ITEMS?></th>
								  <th><?=QUANTITY?></th>
								  <th><?=PRICE?></th>
								  <th><?=DISCOUNT?></th>
								  <th><?=TOTAL?></th>
                                                                  <th><input type='checkbox' value='1' id='p-v1'>&nbsp;&nbsp;<a class='btn btn-danger' href='#' id='delete-selected' style='visibility:hidden'><i class='halflings-icon white trash'></i></a></th>
							  </tr>
						  	</thead>   
						  	<tbody>
								<?php echo implode("",$tBodyDesktop);?>
							</tbody>
                                                </table>
					  </div>









					<div class="box-content" id="mobile">
					<table class="table table-striped table-bordered bootstrap-datatable datatable" >
						  	<thead>
							  <tr>
                                                                  <th><?=DATED?></th>
								  <th><?=ORDER_ID?></th>
								  <th><?=ITEMS?></th>
								  <th><?=DETAIL?></th>
								  <th><?=PRICE?></th>
                                                                  <th><input type='checkbox' value='1' id='p-v1'>&nbsp;&nbsp;<a class='btn btn-danger' href='#' id='delete-selected' style='visibility:hidden'><i class='halflings-icon white trash'></i></a></th>
							  </tr>
						  	</thead>   
						  	<tbody>
								<?php echo $tableData = implode("",$tBodyMobile);?>
							</tbody>
					  </table>
					</div>
				</div>

			</div>

			

		</div>
		</div>
		</div>
		
	
	<div class="clearfix"></div>
        <?php
        $tableData = "<tr> <th>".CATEGORY."</th> <th>".ITEMS."</th> <th>".QUANTITY."</th> <th>".PRICE."</th> <th>".DISCOUNT."</th> <th>".TOTAL."</th> </tr>$tableData";
        $base64data = "<html xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:x='urn:schemas-microsoft-com:office:excel' xmlns='http://www.w3.org/TR/REC-html40'><head><meta http-equiv='content-type' content='application/xhtml+xml; charset=UTF-8' /><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table>$tableData</table></body></html>";
        $base64data = base64_encode($base64data);
        ?>
        <input type="hidden" id="basedata" value="<?=$base64data?>">
        
	<?php require_once("footer.php");?>
        
        <script type="text/javascript">
            $(document).ready(function(){
                
                $("#p-v1").change(function(){
                        var cla         = "p-v1";
                        var ch          = true;
               
                        if($(this).is(":checked"))
                                ch = true;
                        else
                                ch = false;
                        var rows = dataTableVaue.fnGetNodes();
                        $("."+cla).prop('checked',ch);
                        if(ch)
                        {
                                $('input[type="checkbox"]', rows).prop('checked', true);
                                $("."+cla).parent().addClass('checked');
                                $("."+cla).parent().parent().addClass('checked');
                        }
                        else
                        {
                                $('input[type="checkbox"]', rows).prop('checked', false);
                                $("."+cla).parent().removeClass('checked');
                                $("."+cla).parent().parent().removeClass('checked');
                        }

                        checkAndShowDelete();
                })
                
                
                $(".p-v1").change(function(){
                    checkAndShowDelete();
                })
                
                
                $("#delete-selected").click(function(){
                    var delData = Array();
                    $('input:checkbox.p-v1').each(function () {
                            if(this.checked)
                                delData[delData.length] = $(this).val();
                     });
                     
                     delData    = delData.join(",");
                     if(delData.length > 0)
                     {
                         if(confirm(are_you_sure))
                         {
                                $("#overlay").show();
                                $.ajax({
                                        url: "controller.php", 
                                        type : "POST",
                                        data : {"f":"deleteData","d":delData},
                                        success: function(data)
                                        {
                                                alert(data);
                                                $("#overlay").hide();
                                                if($.trim(data) == '<?=DATA_DELETED_SUCCESSFULLY?>')
                                                    location.reload();
                                        }
                                        
                                        
                                    });
                         }
                     }
                })
                
            })
            
            
            
            function checkAndShowDelete()
            {
                  var atleastone = false;
                  $('input:checkbox.p-v1').each(function () {
                    if(!atleastone)
                        atleastone = (this.checked ? $(this).val() : "");
                  });
                  
                  
                  if(atleastone)
                      $("#delete-selected").css('visibility','visible');
                  else
                      $("#delete-selected").css('visibility','hidden');
            }
         
            
        </script>
</body>
</html>
