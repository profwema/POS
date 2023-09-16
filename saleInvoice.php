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
        <link href="css/bootstrap-datetimepicker.css" rel="stylesheet" media="screen">

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
                            <h2><i class="halflings-icon edit"></i><span class="break"></span><?=SALES?></h2>
                        </div>
                        <div class="box-content">
                            <p align="right">
                                <a class="btn btn-success" href="add_saleInvoice.php">
                                    <i class="icon-plus"></i>
                                </a>
                            </p>                                     
                            <form action="<?=$pgName?>" class="form-horizontal" id='form' method="post" name="form" autocomplete="off">
                                <fieldset>
                                    
                                <div class="control-group">
                                   <div class="salesOptions">                               
                                      <label class="control-label" for="date01"><?=FROM_DATE?></label>
                                      <div class="controls">
                                          <input type="text" class=" datepicker-nobar" id="fr_date" name="fr_date" value="<?=$_POST['fr_date']?>">
                                     </div>
                                   </div>
                                   <div class="salesOptions">                                                                 
                                      <label class="control-label" for="date02"><?=TO_DATE?></label>
                                      <div class="controls">
                                         <input type="text" class=" datepicker-nobar" id="to_date" name="to_date" value="<?=$_POST['to_date']?>">
                                      </div>
                                   </div>
                               </div>                                                                      
                                    <button type="submit" name="submit" id='dateSearch' style="margin-left: 10px"class="btn btn-primary"><?=LOAD_FORM?></button>

<!--                                    <div class="control-group">
                                        <div class="salesOptions">
                                            <label class="control-label"><?=INVOICE_DATE?></label>
                                            <div class="controls">
                                                <input type="text" class="datepicker-nobar" name="dateSearch"
                                                       <?php 
                                                       if($_REQUEST['dateSearch'])
                                                       {                                                                            
                                                           echo " value = '$_REQUEST[dateSearch]' ";
                                                       }
                                                       ?> >

                                            </div>
                                        </div>  
                                    </div>-->
                                </fieldset>
                            </form>
                        </div>                                    
                        <table class="table table-striped table-bordered bootstrap-datatable datatable">
                            <thead>
                                <tr>
                                    <th><?=INVOICE_NUMBER?></th>
                                    <th><?=CUSTOMER_NAME?></th>
                                    <th><?=DECUMENT?></th>
                                    <th><?=BRANCH?></th>
                                    <th><?=DATE?></th>
                                    <th><?=REPORT?></th>
                                    <th><?=EDITING?></th>
                                </tr>
                            </thead>   
                            <tbody>
                                        <?php
                                        // Date(added) = '$date;
                                        // WHERE datetime BETWEEN('2009-10-20 00:00:00' AND '2009-10-20 23:59:59')
//                                            $dateSearch  = $_POST['dateSearch'];
//                                            $dateSearch  = explode("/",$dateSearch);
//                                            $dateSearch  = $dateSearch[2]."-".$dateSearch[1]."-".$dateSearch[0];
//                                            $search = " AND `invoice_date` LIKE '%$dateSearch%' ";
                                        // SELECT * FROM `sales` WHERE `invoice_date` LIKE '%2020-01-26%' 
                                                $search = '';
                                                $fdate		        = $_POST['fr_date'];  
                                                if ($fdate) $search .=" AND invoice_date >= '$fdate 00:00:00'";                 
                                                $tdate		        = $_POST['to_date'];  
                                                if ($tdate) $search .=" AND invoice_date <= '$tdate 23:59:59'";                                                  
                                                     
                                                $language 	= LANG;
                                                $storeid	= $_SESSION['storeid'];
                                                $query	 	= "SELECT * FROM sales WHERE storeid='$storeid' AND state = '1' $search ORDER BY invoicenumber DESC";
                                                $res 	 	= mysqli_query($adController->MySQL,$query) or die(mysqli_error($adController->MySQL));
                                                while($data 	= mysqli_fetch_assoc($res))
                                                {
                                                        $query		  = "SELECT * FROM customers  WHERE id='$data[customer]' AND storeid='$storeid'";
                                                        $resSup		  = mysqli_query($adController->MySQL,$query);
                                                        $dataSup 	  = mysqli_fetch_assoc($resSup);
                                                        $customer	  = $dataSup['name_'.$language];

                                                        $query		  = "SELECT * FROM branches  WHERE id='$data[branch]' AND storeid='$storeid'";
                                                        $resBra		  = mysqli_query($adController->MySQL,$query);
                                                        $dataBra 	  = mysqli_fetch_assoc($resBra);
                                                        $branch	          = $dataBra['name_'.$language];
                                                        $invoice	  = $data["invoicenumber"];	
                                                        $supp_invoice	  = $data["document"];
                                                        $date	          = $data['invoice_date'];


                                                        $idval		  = urlencode($adController->encrypt_decrypt(1,$invoice,0));
                                                        $invoiceTable	  = urlencode($adController->encrypt_decrypt(1,'sales',0));
                                                        $otherTable	  = urlencode($adController->encrypt_decrypt(1,'outgo',0));


                                                        echo "<tr>";

                                                        echo "<td>$invoice</td>";
                                                                echo "<td>$customer</td>";
                                                                echo "<td>$supp_invoice</td>";
                                                                echo "<td>$branch</td>";
                                                                echo "<td>$date</td>";
                                                                echo "<td class='center'>
                                                                                        <a href='reports/sales.php?sd=$invoice' target='_blank'>
                                                                                                <img src='img/print.png' style='width:30px'>
                                                                                        </a>  </td>";                                                                      
                                                                echo "<td class='center'>
                                                                                <a class='btn btn-info' href='edit_saleInvoice.php?sd=$idval'>
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
			
    <div class="clearfix"></div>	
	<?php require_once("footer.php");?>
    <script type="text/javascript" src="js/bootstrap-datetimepicker.js" charset="UTF-8"></script>
  <script type="text/javascript">  
      $('.datepicker-nobar').datepicker({
          dateFormat: 'yy-mm-dd'
      });
//      $('.datetimepicker').datetimepicker({
//          
//     format:'yyyy-mm-dd hh:00:00',
//   minView:1,        
//         showMeridian: true,
//         todayBtn: true,
//        autoclose: true
////         ampm: true // FOR AM/PM FORMAT

      </script>    
        
 
</body>
</html>
