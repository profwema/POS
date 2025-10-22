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
$services	= $_POST['sei'];
$shiftid        = $_POST['shift'];

if($shiftid != "")
{
    $shiftid        = intval($shiftid);
    
    $query          = "SELECT * FROM shifts  WHERE id='$shiftid'";
    $res            = mysqli_query($adController->MySQL,$query);
    $data           = mysqli_fetch_assoc($res);
    
    $starts         = str_pad($data['starts'],2,'0',STR_PAD_LEFT);
    $ends           = str_pad($data['ends'],2,'0',STR_PAD_LEFT);
    $starts         = date("Y-m-d")." ".$starts.":00:00";
    $ends           = date("Y-m-d")." ".$ends.":00:00";
    
    $strStarts      = strtotime($starts);
    $strEnds        = strtotime($ends);
    $query          = "SELECT NOW() AS cur FROM shifts";
    $resT           = mysqli_query($adController->MySQL,$query);
    $dataT          = mysqli_fetch_assoc($resT);
    $currentStamp   = strtotime($dataT[cur]);
    
    if($currentStamp <= $strStarts)
    {
        $strStarts  = strtotime('-1 day', $strStarts);
    }
   
    if($strStarts >= $strEnds)
    {
        $strEnds    = strtotime('+1 day', $strEnds);
    }
    
    
    
    
    $fDate                      = date("Y-m-d H:i:s",$strStarts);;
    $tDate                      = date("Y-m-d H:i:s",$strEnds);
    
    $_REQUEST['from_time']      = $data['starts'];
    $_REQUEST['to_time']        = $data['ends'];
    
}
else
{
    if($_REQUEST['from_date'] == "" && $_REQUEST['to_date'] == "")
    {
         $_REQUEST['to_date']    = date("d/m/Y");
         $_REQUEST['from_date']  = date('d/m/Y', strtotime('-1 day', time()));
    }



    $dValue                 = explode("/",$_REQUEST['from_date']);
    $_REQUEST['from_date']  = $dValue[2]."-".$dValue[1]."-".$dValue[0];

    $dValue                 = explode("/",$_REQUEST['to_date']);
    $_REQUEST['to_date']    = $dValue[2]."-".$dValue[1]."-".$dValue[0];
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
    $tDate			= $_REQUEST['to_date']." ".$_REQUEST['to_time'].":00:00";

    if(strtotime($tDate) < strtotime($fDate))
    {
       $tDate                   = date('Y-m-d H:i:s',date(strtotime("+1 day", strtotime($_REQUEST['from_date']))));
       $_REQUEST['to_date']     = date("Y-m-d",strtotime($tDate));
       $_REQUEST['to_time']     = date("H:i:s",strtotime($tDate));
    }

    if(strtotime($fDate) > strtotime($tDate))
    {
       $fDate                       = date('Y-m-d H:i:s',date(strtotime("-1 day", strtotime($_REQUEST['to_date']))));
       $_REQUEST['from_date']       = date("Y-m-d",strtotime($fDate));
       $_REQUEST['from_time']       = date("H:i:s",strtotime($fDate));
    }
    
}

$conditionArr	= array();

if($orderid != "")
	$conditionArr[] = " hasvalue='$orderid' ";
if(intval($catid) > 0)
	$conditionArr[] = " itemid IN (SELECT id FROM items WHERE catid='$catid' AND storeid='$storeid') ";
if(intval($itemId) > 0)
	$conditionArr[] = " itemid='$itemId' ";
if(intval($branchid) > 0)
	$conditionArr[] = " branch='$branchid' ";
if(intval($empId) > 0)
	$conditionArr[] = " userid='$empId' ";

$conditionArr[]            =  " specific_date >= '$fDate' ";
$conditionArr[]            =   " specific_date < '$tDate' ";



if(count($conditionArr) == 0)
{
	$condition	= " WHERE storeid='$storeid' AND DATE(`dated`) = CURDATE()";
	$conditionLog	= " AND storeid='$storeid' AND DATE(`dated`) = CURDATE()";
}
else
{
	$conditionArr	= implode(" AND ",$conditionArr);
	$conditionArr	= trim($conditionArr);
	$conditionArr	= trim($conditionArr,"AND");
	$condition	= " WHERE storeid='$storeid' AND $conditionArr";
	$conditionLog	= " AND  storeid='$storeid' AND $conditionArr";
}


//////////////// NEW /////////////////////

$conditionArray		= array();
$conditionArray[]	= " o.storeid='$storeid' ";
$orderidIncoming        = $_REQUEST['order_id'];


$conditionArray[]	 = "o.specific_date >= '$fDate' AND o.specific_date <='$tDate'";
if(intval($itemId) > 0)
	$conditionArray[] = " o.itemid	='$itemId' ";
if(intval($branchid) > 0)
	$conditionArray[] = " o.branch   ='$branchid' ";
if(intval($empId) > 0)
	$conditionArray[] = " o.userid	='$empId' ";
if($orderidIncoming != "")
        $conditionArray[] = " o.hasvalue ='$orderidIncoming' ";
if(intval($services) > 0)
        $conditionArray[] = " o.service_by ='$services' ";
        
$conditionArray[]       = " o.transaction_closed ='1' ";
	
$conditionSel		= implode(" AND ",$conditionArray);

$searchCondition        = array();

$colspan        = 3;
$blankStr  = "<tr>";
$blankStr .= "<td colspan='$colspan'>&nbsp;</td>";
$blankStr .= "</tr>";
        

$str2  = "<tr>";
$str2 .= "<td align='left'><strong  style='float:left;text-align:left'>".FROM_DATE."</strong></td>";
$str2 .= "<td colspan='$colspan'>$fDate</td>";
$str2 .= "</tr>";
$searchCondition[] =$str2;// FROM_DATE."&nbsp;:&nbsp;".$fDate;

$str2  = "<tr>";
$str2 .= "<td  align='left'><strong style='float:left;text-align:left'>".TO_DATE."</strong></td>";
$str2 .= "<td  colspan='$colspan'>$tDate</td>";
$str2 .= "</tr>";
$searchCondition[] =$str2;// TO_DATE."&nbsp;:&nbsp;".$tDate;



$timing         = time();

$query          = "SELECT SUM(((o.qty * o.cost)- o.discount)) AS cst,SUM(((o.cost / 100) * o.vat) * o.qty) AS de FROM orders AS o WHERE $conditionSel";
$res            = mysqli_query($adController->MySQL,$query);
$data           = mysqli_fetch_assoc($res);
$allTotal       = $data['cst'];
$tax            = $data['de'];
$afterTax       = $allTotal + $tax;

$diff           = time() - $timing;
$diff2          = $diff/1000;
$str00          = "<script>console.log('first :: $diff  --  $diff2')</script>";

$query          = "SELECT SUM(((o.qty * o.cost)- o.discount)) AS cash ,SUM(((o.cost / 100) * o.vat) * o.qty) AS de FROM orders AS o WHERE $conditionSel AND payment_type='1'";
$res            = mysqli_query($adController->MySQL,$query);
$data           = mysqli_fetch_assoc($res);
$cashTotal      = $data['cash'];
$cashTotal      = round(floatval($cashTotal + $data['de']),2); 

$diff           = time() - $timing;
$diff2          = $diff/1000;
$str11          = "<script>console.log('second :: $diff  --  $diff2')</script>";

$query          = "SELECT SUM(((o.qty * o.cost)- o.discount)) AS card,SUM(((o.cost / 100) * o.vat) * o.qty) AS de FROM orders AS o WHERE $conditionSel AND payment_type='2'";
$res            = mysqli_query($adController->MySQL,$query);
$data           = mysqli_fetch_assoc($res);
$cardTotal      = $data['card'];
$cardTotal      = round(floatval($cardTotal + $data['de']),2); 

$diff           = time() - $timing;
$diff2          = $diff/1000;
$str22          = "<script>console.log('third :: $diff  --  $diff2')</script>";

$query          = "SELECT SUM(((o.qty * o.cost)- o.discount)) AS sabakah,SUM(((o.cost / 100) * o.vat) * o.qty) AS de FROM orders AS o WHERE $conditionSel AND payment_type='3'";
$res            = mysqli_query($adController->MySQL,$query);
$data           = mysqli_fetch_assoc($res);
$sabakahTotal   = $data['sabakah'];
$sabakahTotal   = round(floatval($sabakahTotal + $data['de']),2); 

$diff           = time() - $timing;
$diff2          = $diff/1000;


$fromDateV      = date("d/m/Y",strtotime($fDate));
$toDateV        = date("d/m/Y",strtotime($tDate));


$str2  = "<tr>";
$str2 .= "<td style='text-align:left'>Cash</td>";
$str2 .= "<td colspan='2'><strong>$cashTotal</strong></td>";
$str2 .= "<td align='right'>كاش</td>";
$str2 .= "</tr>";
$searchCondition[] =$str2;

$str2  = "<tr>";
$str2 .= "<td style='text-align:left'>Span</td>";
$str2 .= "<td colspan='2'><strong>$sabakahTotal</strong></td>";
$str2 .= "<td align='right'>شبكة</td>";
$str2 .= "</tr>";
$searchCondition[] =$str2;

$str2  = "<tr>";
$str2 .= "<td style='text-align:left'>Visa</td>";
$str2 .= "<td colspan='2'><strong>$cardTotal</strong></td>";
$str2 .= "<td align='right'>فيزا</td>";
$str2 .= "</tr>";
$searchCondition[] =$str2;


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
								<label class="control-label" for="typeahead"><?=ORDER_ID?></label>
								<div class="controls">
									<select  onchange="reloadPage()"  data-rel="chosen"  class='span3' name="order_id">
										<option value="">&nbsp;</option>
										<?php
											$query 		= "SELECT DISTINCT hasvalue FROM orders WHERE storeid='$storeid' AND specific_date >='$fDate' AND specific_date <='$tDate' ORDER BY specific_date DESC";
											$res		= mysqli_query($adController->MySQL,$query);
											while($data 	= mysqli_fetch_assoc($res))
											{
                                                                                            
												$name	= $data["hasvalue"];
												$sel	= "";
												if($_REQUEST['order_id'] == $data['hasvalue'])
                                                                                                {
													$sel = " selected='true' ";
                                                                                                        
                                                                                                        $str2  = "<tr>";
                                                                                                        $str2 .= "<td><strong>".ORDER_ID."</strong></td>";
                                                                                                        $str2 .= "<td  colspan='$colspan'>$name</td>";
                                                                                                        $str2 .= "</tr>";
                                                                                                        
                                                                                                        $searchCondition[] = $str2;
                                                                                                }
												echo "<option value='$name' $sel>$name</option>";
											}

										?>
											
									</select>
								</div>
								</div>
                                                            

								<div class="control-group">
								<label class="control-label" for="typeahead"><?=ITEMS?></label>
								<div class="controls">
									<select  onchange="reloadPage()" data-rel="chosen"   class='span3' name="iid">
										<option value="">&nbsp;</option>
										<?php
											$query 		= "SELECT id,name_".$language." ,qty_".$language." FROM items WHERE storeid='$storeid' ORDER BY name_".$language." ASC";
											$res		= mysqli_query($adController->MySQL,$query);
											while($data 	= mysqli_fetch_assoc($res))
											{
                                                                                            
                                                                                                $qt     = "";
                                                                                                if($data['qty_'.$language] != "")
                                                                                                    $qt     = " (".$data['qty_'.$language].")";
                                                                                                else if($data['qty_en'] != "")
                                                                                                    $qt     = " (".$data['qty_en'].")";
                                                                                            
												$name	= $data["name_".$language].$qt;
												if($data["name_".$language] == "")
													$name	= $data['name_en'].$qt;
												$sel	= "";
												if($_REQUEST['iid'] == $data['id'])
                                                                                                {
													$sel = " selected='true' ";
                                                                                                        
                                                                                                        $str2  = "<tr>";
                                                                                                        $str2 .= "<td><strong>".ITEMS."</strong></td>";
                                                                                                        $str2 .= "<td  colspan='$colspan'>$name</td>";
                                                                                                        $str2 .= "</tr>";
                                                                                                        
                                                                                                        $searchCondition[] = $str2;
                                                                                                }
												echo "<option value='$data[id]' $sel>$name</option>";
											}

										?>
											
									</select>
								</div>
								</div>

								<div class="control-group">
								<label class="control-label" for="typeahead"><?=BRANCH?></label>
								<div class="controls">
                                                                    <select onchange="reloadPage()" data-rel="chosen"  class='span3' name="br">
										<option value="">&nbsp;</option>
										<?php
											$query 		= "SELECT name_en,name_ar,id FROM branches WHERE storeid='$storeid'";
											$res		= mysqli_query($adController->MySQL,$query);
											while($data 	= mysqli_fetch_assoc($res))
											{
												$name	= $data['name_'.$language];
												if($name == "")
													$name	= $data['name_en'];
												$sel	= "";
												if($_REQUEST['br'] == $data['id'])
                                                                                                {
                                                                                                    
                                                                                                        $sel = " selected='true' ";
                                                                                                        $str2  = "<tr>";
                                                                                                        $str2 .= "<td><strong>".BRANCH."</strong></td>";
                                                                                                        $str2 .= "<td  colspan='$colspan'>$name</td>";
                                                                                                        $str2 .= "</tr>";
                                                                                                        $searchCondition[] = $str2;
                                                                                                        
                                                                                                }
													
												echo "<option value='$data[id]' $sel>$name</option>";
											}

										?>
											
									</select>
								</div>
								</div>
                                                                <?php 
                                                                if($store_type_  == SALOON) 
                                                                {                                                   
                                                                ?>
                                                                <div class="control-group">
								<label class="control-label" for="typeahead"><?=WHO?></label>
								<div class="controls">
									<select  onchange="reloadPage()" data-rel="chosen"  class='span3'    name="sei">
										<option value="">&nbsp;</option>
										<?php
											$query 		= "SELECT name_en,name_ar,id FROM employees WHERE storeid='$storeid' AND FIND_IN_SET(id,(SELECT DISTINCT services FROM items WHERE services != ''))";
											$res		= mysqli_query($adController->MySQL,$query);
											while($data 	= mysqli_fetch_assoc($res))
											{
												$name	= $data['name_'.$language];
												if($name == "")
													$name	= $data['name_en'];
												$sel	= "";
												if($_REQUEST['sei'] == $data['id'])
                                                                                                {
                                                                                                    
                                                                                                        $sel = " selected='true' ";
                                                                                                        $str2  = "<tr>";
                                                                                                        $str2 .= "<td><strong>".CASHIER."</strong></td>";
                                                                                                        $str2 .= "<td  colspan='$colspan'>$name</td>";
                                                                                                        $str2 .= "</tr>";
                                                                                                        $searchCondition[] = $str2;
                                                                                                        
                                                                                                }
												echo "<option value='$data[id]' $sel>$name</option>";
											}

										?>
											
									</select>
								</div>
								</div>

                                                                <?php 
                                                                }                                                   
                                                                ?>
								<div class="control-group">
								<label class="control-label" for="typeahead"><?=CASHIER?></label>
								<div class="controls">
									<select  onchange="reloadPage()" data-rel="chosen"  class='span3'    name="ei">
										<option value="">&nbsp;</option>
										<?php
											$query 		= "SELECT name_en,name_ar,id FROM employees WHERE storeid='$storeid'";
											$res		= mysqli_query($adController->MySQL,$query);
											while($data 	= mysqli_fetch_assoc($res))
											{
												$name	= $data['name_'.$language];
												if($name == "")
													$name	= $data['name_en'];
												$sel	= "";
												if($_REQUEST['ei'] == $data['id'])
                                                                                                {
                                                                                                    
                                                                                                        $sel = " selected='true' ";
                                                                                                        $str2  = "<tr>";
                                                                                                        $str2 .= "<td><strong>".CASHIER."</strong></td>";
                                                                                                        $str2 .= "<td colspan='$colspan'>$name</td>";
                                                                                                        $str2 .= "</tr>";
                                                                                                        $searchCondition[] = $str2;
                                                                                                        
                                                                                                }
												echo "<option value='$data[id]' $sel>$name</option>";
											}

										?>
											
									</select>
								</div>
								</div>
                                                            
                                                                <hr>
                                                                <div class="control-group" >
								<label class="control-label" for="date01"><?=SHIFT?></label>
								<div class="controls">
                                                                        <select onchange="shiftUpdate(this)"  class='span3' name="shift">
                                                                        <option value=''></option>
                                                                        <?php
                                                                        $dispNone       = "";
                                                                        $query	 	= "SELECT * FROM shifts WHERE storeid='$storeid'";
                                                                        $res		= mysqli_query($adController->MySQL,$query);
                                                                        while($dataCat  = mysqli_fetch_assoc($res))
                                                                        {
                                                                            $name       = $dataCat['name_'.$language];
                                                                            
                                                                            $sel	= "";
                                                                            if($_REQUEST['shift'] == $dataCat['id'])
                                                                            {
                                                                                $sel            = " selected='true' ";
                                                                                $dispNone       = "style='display:none'";
                                                                            }
                                                                                
                                                                            echo "<option value='$dataCat[id]' $sel>$name</option>";
                                                                        }

                                                                        ?>
                                                                        </select>
                                                                    
                                                                </div>
                                                                </div>
                                                                <hr>
                                                                <p>&nbsp;</p>
								<div class="control-group" <?=$dispNone?> id='from_div'>
								<label class="control-label" for="date01"><?=FROM_DATE?></label>
								<div class="controls">
                                                                        <input type="text" class="datepicker-nobar" id="from_date" name="from_date" value="<?=$fromDateV?>">
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
                                                            
                                                                
                                                           
								<div class="control-group" <?=$dispNone?> id='to_div'>
								<label class="control-label" for="date01"><?=TO_DATE?></label>
								<div class="controls">
                                                                        <input type="text" class="datepicker-nobar" id="to_date" name="to_date" value="<?=$toDateV?>">
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
								
								<div class="control-group">
								<label class="control-label">&nbsp;</label>
								<div class="controls">
                                                                    <input type="button" onclick="loadReport()" class="fixed-width-button btn btn-primary" value="<?=LOAD_FORM?>">
								</div>
								</div>
                                                            
      
                                                            
                                                            <hr>

								<div class="control-group">
								<label class="control-label" for="date01">&nbsp;</label>
								<div class="controls">
									<img src='img/vm.jpg' style="height:50px">&nbsp;&nbsp;<b id="card-total"></b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                                        <span style='font-style: italic;color:#f55;font-weight:bold;padding:5px;border:1px solid #f55;'>Sabakah</span>&nbsp;&nbsp;<b id="sabakah-total"></b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
									<img src='img/images.jpg' style="height:50px">&nbsp;&nbsp;<b id='cash-total'></b>
								</div>
								</div>
								
								<div class="control-group">
								<label class="control-label" for="date01"><?=VAT?> </label>
								<div class="controls">
									<?=round(floatval($tax),2)?>
								</div>
								</div>
                                                            
                                                                <div class="control-group">
								<label class="control-label" for="date01"><?=TOTAL?> </label>
								<div class="controls"><?=round(floatval($afterTax),2)?></div>
								</div>
							</fieldset>
						</form>
					</div>
					</div>
					</div>

			

			<div>
                                
                                  <div class="box span12">
                                    <ul class="export-data">
                                            <li><a href="javascript:void(0)" class="list-group-item" onclick="printTable(0)"><img src="img/xls.png" width="24"> XLS</a></li>
                                            <li><a href="javascript:void(0)" class="list-group-item" onclick="printTable(1)"><img src="img/print.png" width="24"> PRINT</a></li>

                                        </ul>
					<div class="box-content">
                                                
						<table class="table table-striped table-bordered bootstrap-datatable " id='lazy-load' page='controller.php?f=getsales'>
						  	<thead>
							  <tr>
								  <th><?=ITEMS?></th>
								  <th><?=QUANTITY?> <span id='tot-qty'></span></th>
								  <th><?=PRICE?></th>
								  <th><?=DISCOUNT?></th>
								  <th><?=GROSS?></th>
                                  <th><?=VAT?></th>
								  <th><?=TOTAL?></th>
                                                                  <th><input type='checkbox' value='1' id='p-v1'>&nbsp;&nbsp;<a class='btn btn-danger' href='#' id='delete-selected' style='visibility:hidden'><i class='halflings-icon white trash'></i></a></th>

							  </tr>
						  	</thead>   
						  	
                                                </table>
					  </div>

			</div>

			

		</div>
		</div>
		</div>
		
	
	<div class="clearfix"></div>
          <div id='tmp' style='display:none'></div>
        <input type="hidden" id="basedata" value="<?=$base64data?>">
        
        
        <?php
//        echo "<pre>";
//        print_r($searchCondition);
//        echo "</pre>";
        ?>
	<?php require_once("footer.php");?>
        
        <script type="text/javascript">
            $(document).ready(function(){
 
 				<?php
                if(count($_POST) > 0)
                {
                ?>
                $('#lazy-load').DataTable( {
				"sPaginationType": "bootstrap",
                                "aoColumnDefs" : [   
				{
					'bSortable' : false,  
					'aTargets' : [1,2,3,4,5]
				}],
                                "iDisplayLength": 5,
                                "aLengthMenu": [[1,2,3,4,5,10,30,50], [1,2,3,4,5,10,30,50]],
				"processing": true,
				"serverSide": true,
				"ajax":{
					url  : "controller.php",
					type : "post", 
                                        data : {"f":"getsalesLazy","d":"<?=base64_encode($conditionSel)?>"},
					error: function(){ 
						$("#lazy-load_processing").css("display","none");	
					}
				}
			});
  
  				<?php
                }
                ?>

                $(".dataTables_length").css('float','left');
		$(".dataTables_length").css('display','inline');
                $("#lazy-load_filter").css('float','right');
		$("#lazy-load_filter").css('display','none');
                            
                $("#sabakah-total").html("<?=$sabakahTotal?>");            
                $("#cash-total").html("<?=$cashTotal?>");     
                $("#card-total").html("<?=$cardTotal?>");     
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
                                $(".overlay").show();
                                $.ajax({
                                        url: "controller.php", 
                                        type : "POST",
                                        data : {"f":"deleteData","d":delData,"c":"<?=base64_encode($conditionSel)?>"},
                                        success: function(data)
                                        {
                                                alert(data);
                                                $(".overlay").hide();
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
            
            
            
            
              /*
                jQuery Document ready
              */

              $(function()
              {
                    var startDateTextBox    = $('#from_date1');
                    var endDateTextBox      = $('#to_date1');

                    startDateTextBox.datetimepicker({ 
                            timeFormat: 'HH:mm',
                            dateFormat: 'y/m/d',
                            onClose: function(dateText, inst) {
                                    if (endDateTextBox.val() != '') {
                                            var testStartDate = startDateTextBox.datetimepicker('getDate');
                                            var testEndDate = endDateTextBox.datetimepicker('getDate');
                                            if (testStartDate > testEndDate)
                                                    endDateTextBox.datetimepicker('setDate', testStartDate);
                                    }
                                    else {
                                            endDateTextBox.val(dateText);
                                    }
                                    
                                    
                            },
                            onSelect: function (selectedDateTime){
                                  //  endDateTextBox.datetimepicker('option', 'minDate', startDateTextBox.datetimepicker('getDate') );
                            }
                    });
                    endDateTextBox.datetimepicker({ 
                            timeFormat: 'HH:mm',
                            dateFormat: 'y/m/d',
                            onClose: function(dateText, inst) {
                                    if (startDateTextBox.val() != '') {
                                            var testStartDate = startDateTextBox.datetimepicker('getDate');
                                            var testEndDate = endDateTextBox.datetimepicker('getDate');
                                            if (testStartDate > testEndDate)
                                                    startDateTextBox.datetimepicker('setDate', testEndDate);
                                    }
                                    else {
                                            startDateTextBox.val(dateText);
                                    }
                                    
                            },
                            onSelect: function (selectedDateTime){
                                    startDateTextBox.datetimepicker('option', 'maxDate', endDateTextBox.datetimepicker('getDate') );
                            }
                    });
                    
                    
              });
            
             var dataHolder;
             var lastValue;
             var newData = false;
             function printTable(val)
              {
              			
                    <?php $imp = base64_encode(implode("",$searchCondition));?>
                    $(".overlay").show();
                    $.ajax({
                            url: "controller.php", 
                            type : "POST",
                            data : {"f":"getsalesallLazy","d":"<?=base64_encode($conditionSel)?>","top":"<?=$imp?>","t":val},
                            success: function(data)
                            {
                                
                                    
                                    dataHolder	= data;
                                    lastValue	= val;
                                    newData		= true;
                                    $("#print-now").show(100);
                                    $("#loader-img").hide(100);
                                    if(lastValue == "1")
                                    	$("#print-now").val('Print Now');
                                    else
                                    	$("#print-now").val('Download Now');
                            }
                        });
              }
              
              
              function finalPrint()
              {
              		$("#print-now").hide();
                    $("#loader-img").show();
              		$(".overlay").hide();
              		
              		if(newData)
              		{
              			newData = false;
              			if(lastValue == "1")
                                    {
                                            var mywindow = window.open('', 'PRINT', 'height=400,width=600');
                                            console.log(dataHolder);
                                            mywindow.document.write("<style>table{width:260px;margin:0;padding:0;table-layout: fixed;} table tr th{text-align:center;font-weight:bold;font-size:10px;} table tr td{text-align:center;font-weight:bold;font-size:10px;}</style>"+dataHolder);
                                            mywindow.document.close();
                                            mywindow.focus(); 
                                            mywindow.print();
                                            mywindow.close();
                                    }
                                    else if(lastValue == "0")
                                    {
                                            var v = $.parseJSON(dataHolder);
                                            $("#tmp").html(v['table']);
                                            $("#basedata").val(v['base']);
                                            $('#sales-table').tableExport({type:'excel',escape:'false'});
                                    }
              		}
              		
					
					
              
              }
              
              
        </script>
</body>
</html>
