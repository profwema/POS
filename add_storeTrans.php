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
                <div>
                    <div class="box span12">
                        <div class="box-header" data-original-title>
                            <h2><i class="halflings-icon edit"></i><span class="break"></span><?=ADD_STORE_TRANSFAIR?></h2>
                        </div>
                        <div class="box-content">
                            <form class="form-horizontal" id="storeTrans-form">
                                <input type="hidden" value="addStoreTrans" name="f">
                                <fieldset>                                                      
                                    <div class="control-group">
                                        <div class="salesOptions">
                                            <label class="control-label" for="typeahead"><?=TRANSFAIR_NUMBER?> : </label>
                                            <div class="controls">
                                                               <?php
                                                               $query   = "SELECT trans_number
                                                                        FROM store_trans
                                                                                                                
                                                                        ORDER BY trans_number DESC
                                                                        LIMIT 1";
                                                                $res	= mysqli_query($adController->MySQL,$query) or die(mysqli_error($adController->MySQL));
                                                                $num	= mysqli_num_rows($res);
                                                                $data	= mysqli_fetch_assoc($res);
                                                                if($num)
                                                                {
                                                                    $rest = substr($data['trans_number'], -4);  
                                                                    $trans_No = intval($rest)+1;
                                                                }
                                                                    
                                                                else 
                                                                    $trans_No = 1;
                                                                
                                                                //$rand           = rand(0,9999);
                                                                //$rand           =  str_pad($rand,4,"0",STR_PAD_LEFT);
                                                                //$ip             = str_replace(".","",$_SERVER['REMOTE_ADDR']);
                                                                $time           = time();
                                                                $invoice        = $storeid.'-'.$time.'-'.str_pad($trans_No,4,"0",STR_PAD_LEFT);
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
                                                       value='<?php echo date("d/m/Y")?>'>
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
                                                        $query= "SELECT * FROM stores ";// id IN ( SELECT catid FROM items WHERE id IN (SELECT DISTINCT itemid FROM orders  $condition))";
                                                        $res        = mysqli_query($adController->MySQL,$query);
                                                        while($data     = mysqli_fetch_assoc($res))
                                                        {
                                                           $name    = $data["name_".$language];

                                                           $sel = "";                                                                

                                                           if(isset( $_SESSION['store']) && $_SESSION['store'] == $data['id'])
                                                           {
                                                               $sel = " selected='true' ";
                                                           }
                                                           elseif($data["defualt"]==1)
                                                           {
                                                               $_SESSION['store']=$data['id'];
                                                               $sel = " selected='true' ";
                                                           }

                                                           echo "<option value='$data[id]' $sel>$name</option>";
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
                                                        $query= "SELECT * FROM stores ";// id IN ( SELECT catid FROM items WHERE id IN (SELECT DISTINCT itemid FROM orders  $condition))";
                                                        $res        = mysqli_query($adController->MySQL,$query);
                                                        while($data     = mysqli_fetch_assoc($res))
                                                        {
                                                           $name    = $data["name_".$language];

                                                           $sel = "";                                                                

                                                           if(isset( $_SESSION['store']) && $_SESSION['store'] == $data['id'])
                                                           {
                                                               $sel = " selected='true' ";
                                                           }
                                                           elseif($data["defualt"]==1)
                                                           {
                                                               $_SESSION['store']=$data['id'];
                                                               $sel = " selected='true' ";
                                                           }

                                                           echo "<option value='$data[id]' $sel>$name</option>";
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
                                                    <textarea class="span6 typeahead"  style="width: 70%" name="note" id="note"  rows="2"></textarea>                                            

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
                                        $query   = "SELECT * FROM items  ";
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
                                                         echo "<option value='$data[id]'> $barcode | $itemName | $unitName</option>";
                                                }
                                                           ?>
                                              
                                                        </select>
                                                    </td>

                                                    <td >
                                                            <input type="text" id='item-qty-<?=$i?>' class="item-qty span12 typeahead" maxlength="100"  name='item_qty[]'>
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
