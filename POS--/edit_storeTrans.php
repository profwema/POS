<?php
require_once("top.php");
require_once("redirection.php");
require_once("controller.php");
$language 	= $_SESSION['lang'];
$storeid	= $_SESSION['storeid'];

$invoice             = $_REQUEST['sd'];
$query          = "SELECT * FROM store_trans WHERE trans_number='$invoice'";
$resP           = mysqli_query($adController->MySQL,$query);
$dataPur        = mysqli_fetch_assoc($resP);

$query		= "SELECT * FROM store_trans_items WHERE storeid='$storeid' AND trans_number='$invoice'";
$ress		= mysqli_query($adController->MySQL,$query);
//$dataCat	= mysqli_fetch_assoc($ress);
$numRowss        = mysqli_num_rows($ress);
$numAlready     = $numRowss;
$numAlready     = $numAlready - 1; 
if($numAlready < 0)
    $numAlready = 0;
while($dataItemsList	= mysqli_fetch_assoc($ress))
{
        $itemsListArray[count($itemsListArray)] = $dataItemsList;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>WAM Tech Soft</title>
	<?php require_once("script_php_variables.php");?>
	<?php require_once("header.php");?>	
        <script type="text/javascript">
//            var itemsAlready    = "<?=mysqli_escape_string(json_encode($arrayPi))?>";
//            itemsAlready        = $.parseJSON(itemsAlready);
            numAlready          = <?=$numAlready?>;
        </script>    
         
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
                            <h2><i class="halflings-icon edit"></i><span class="break"></span><?=EDIT_STORE_TRANSFAIR?></h2>
                        </div>
                        <div class="box-content">
                            <form class="form-horizontal" id="storeTrans-form">
                                <input type="hidden" value="<?=$id?>" name='sd'>
                                <input type="hidden" value="editStoreTrans" name="f">
                                <fieldset>                                                      
                                    <div class="control-group">
                                        <div class="salesOptions">
                                            <label class="control-label" for="typeahead"><?=TRANSFAIR_NUMBER?> : </label>
                                            <div class="controls">
                                                               <?php
                                                               $invoice        =  $dataPur['trans_number'];
                                                              ?>
                                                              <input type="text" 
                                                                     class=" typeahead"  
                                                                     name='invoice' 
                                                                     id='invoice' 
                                                                     value ='<?php echo $invoice ?>'
                                                                     readonly>
								 <span class="help-inline email-un">&nbsp;</span>
                                            </div>
                                        </div>  
                                        
                                        <div class="salesOptions">
                                            <label class="control-label"><?=INVOICE_DATE?></label>
                                            <div class="controls">
                                                <input type="text" class="datepicker-nobar" name="trans_date"  
                                                       value="<?=date("d/m/Y",strtotime($dataPur['trans_date']))?>">
                                            </div>
                                        </div>
                                    </div>
                                    <br>
                                    <div class="control-group">
                                          <div class="salesOptions">
                                                <label class="control-label" for="typeahead3"><?=FROM_STORE?>
                                                </label>
                                                <div class="controls">
                                                    <select class='span3' name="from" id="from" data-rel="chosen" style="width:70%">
                                                        <option value="">
                                                            &nbsp;
                                                        </option><?php
                                                        $query= "SELECT * FROM stores WHERE storeid='$storeid'";// id IN ( SELECT catid FROM items WHERE id IN (SELECT DISTINCT itemid FROM orders  $condition))";
                                                        $res        = mysqli_query($adController->MySQL,$query);
                                                        while($data     = mysqli_fetch_assoc($res))
                                                        {
                                                            $store   = $data['id'];											
                                                            $name = $data['name_'.$language];
                                                            if($store == $dataPur['storeFrom'])
                                                               echo "<option value='$store' selected>$name</option>"; 
                                                            else
                                                               echo "<option value='$store'>$name</option>";  

                                                           }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>        
                                          <div class="salesOptions">
                                                <label class="control-label" for="typeahead3"><?=TO_STORE?>
                                                </label>
                                                <div class="controls">
                                                    <select class='span3' name="to" id="to" data-rel="chosen" style="width:70%">
                                                        <option value="">
                                                            &nbsp;
                                                        </option><?php
                                                        $query= "SELECT * FROM stores WHERE storeid='$storeid'";// id IN ( SELECT catid FROM items WHERE id IN (SELECT DISTINCT itemid FROM orders  $condition))";
                                                        $res        = mysqli_query($adController->MySQL,$query);
                                                        while($data     = mysqli_fetch_assoc($res))
                                                        {
                                                            $store   = $data['id'];											
                                                            $name = $data['name_'.$language];
                                                            if($store == $dataPur['storeTo'])
                                                               echo "<option value='$store' selected>$name</option>"; 
                                                            else
                                                               echo "<option value='$store'>$name</option>"; 
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>                                           
                                    </div>
                                    <div class="control-group">
            
                                                <label class="control-label" for="typeahead3"><?=NOTES?>
                                                </label>
                                                <div class="controls">
                                                    <textarea class="span6 typeahead"  style="width: 70%" name="note" id="note"  rows="2">
                                                        <?=$dataPur['note']?>
                                                    </textarea>                                            

                                                </div>
                                    </div>
                                        	
                                    <div class="control-group" >
                                        <label class="control-label"><?=INCOMING_ITEMS?></label>

                                            <div class="controls" style="margin-top:20px; margin-left:0px">	
                                                <table class="  table-striped table-bordered ">                                                
                                                                                   
                                                    <tr>
                                                    <TH> </th>
                                                    <TH ><?=BARCODE?>|<?=ITEM_NAME?>|<?=UNITS?></TH>    
                                                    <TH ><?=QUANTITY?></TH>

                                                    <TH>&nbsp;</TH>
                                                    <TH>&nbsp;</TH>
                                                </tr>
                                                <?php
                                                for($i = 0 ;$i < 10 ; $i++)
                                                {
    //										
                                                ?>
                                                <tr id='row-v-<?=$i?>'>
                                                    
                                                    <td>

                                                    </td>
                                                    
                                                    
                                                    <td>
                                                        <select id='barcode-<?=$i?>' name="barcode[]" data-rel="chosen"  style="width:100%" >
                                                            <option value=''> </option>
                                        <?php
                                        $name     = "name_".$_SESSION['lang'];
                                        $query   = "SELECT * FROM items WHERE items.storeid='$storeid' ";
                                                $res        = mysqli_query($adController->MySQL,$query) or die(mysqli_error($adController->MySQL));
                                                while($data = mysqli_fetch_assoc($res))
                                                {

                                                        $barcode      = $data['barcode'];
                                                        if ($barcode == '') $barcode = "-----";
                                                        $itemName     = $data[$name];
                                                        if ($itemName == '' || $itemName =='`')
                                                        {
                                                            if ($_SESSION['lang']=='en')                                                           
                                                                  $itemName     = $data['name_ar'];
                                                            elseif($_SESSION['lang']=='ar')                                                           
                                                                  $itemName     = $data['name_en'];
                                                        }    
                                                            
                                                        $unit         = $data['unit'];
                                                        $queryUnit    = "SELECT $name FROM units WHERE id='$unit' ";
                                                        $resUint      = mysqli_query($adController->MySQL,$queryUnit) or die(mysqli_error($adController->MySQL));
                                                        $dataUnit     = mysqli_fetch_assoc($resUint);
                                                        $unitName     = $dataUnit[$name];
                                                        if ($unitName == '') $unitName = "-----";
                                                            if($data['id'] == $itemsListArray[$i]['itemid'])
                                                                
                                                                echo "<option value='$data[id]' selected> $barcode | $itemName | $unitName</option>";
                                                            else
                                                                echo "<option value='$data[id]'> $barcode | $itemName | $unitName</option>";
                                                }
                                                           ?>
                                              
                                                        </select>
                                                    </td>

                                                    <td >
                                                            <input type="text" id='item-qty-<?=$i?>' class="item-qty span12 typeahead" maxlength="100"  name='item_qty[]'value="<?=$itemsListArray[$i]['quantity']?>" >
                                                    </td>
                                                    
                                                    <td class='del-palance-item mult-padding' prop='<?=$i?>'><label>x</label></td>
                                                    <td class='more-palance-item mult-padding' prop='<?=$i?>'><label>+</label></td>
                                            </tr>
                                                <?php } ?>
                                          </table>
                                        </div>
                                    </div>                                  				
                                    <hr>
                                    
                                    
                                    
							
							<div class="form-actions">
							  <button type="button" id='submit-storeTrans' class="btn btn-primary"><?=SAVE?></button>
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
        <script type="text/javascript">
        $(document).ready(function()
        {            var config = {
                '.span3'     : { width: '95%' }
            };
            for (var selector in config) 
            {
                $(selector).chosen(config[selector]);
            }
        }
                

                    
    </script>
</body>
</html>
