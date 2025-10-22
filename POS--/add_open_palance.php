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
                            <h2><i class="halflings-icon edit"></i><span class="break"></span><?=NEW_ITEM?></h2>
                        </div>
                        <div class="box-content">
                            <form class="form-horizontal" id="openPalance-form">
                                <input type="hidden" value="addOpenPalance" name="f">
                                <fieldset>                                    
                                   
                                    <div class="control-group">
                                          <div class="salesOptions">
                                                <label class="control-label" for="typeahead3" style="width:30px"><?=STORES?>
                                                </label>
                                                <div class="controls" style="margin-left:60px">
                                                    <select class='span3' name="store" id="store">
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
                                    <hr>                                                    
                                    <div class="control-group" >
                                            <h4><?=ITEMS?></h4>
                                            <div class="controls" style="margin-top:20px; margin-left:0px">	
                                                <table class="span12" style="width:100%">                                                
                                                <tr>
                                                    <TH ><?=BARCODE?> | <?=ITEM_NAME?> | <?=UNITS?></TH>    
                                                    <TH ><?=QTY?></TH>
                                                    <TH ><?=COST_PRICE?></TH>
                                                    <TH ><?=DISCOUNT?> % </TH>
                                                    <TH ><?=VAT?> % </TH>
                                                    <TH ><?=TOTAL?></TH>
                                                    <TH>&nbsp;</TH>
                                                </tr>
                                                <?php
                                                for($i = 0 ;$i < 10 ; $i++)
                                                {
                                                ?>
                                                <tr <?=$rowStyle?> id='row-v-<?=$i?>'>
                                                    <td style="width:30%">
                                                        <select name="barcode[]" data-rel="chosen" class="barcode-select" style="width:100%" >
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
                                                                        echo "<option value='$i@@$data[id]@@$data[vat]@@$barcode@@$itemName@@$unitName'> $barcode | $itemName | $unitName</option>";

                                                                }
                                                        ?>
                                                        </select>
                                                    </td>

                                                    <td class='mult-padding'>
                                                            <input type="text" id='item-qty-<?=$i?>' class="item-qty span12 typeahead" maxlength="100"  name='item_qty[]' >
                                                    </td>
                                                    <td class='mult-padding'>
                                                            <input type="text" id='item-price-<?=$i?>' class="item-price span12 typeahead" maxlength="100"  name='item_price[]'>
                                                    </td>
                                                    <td class='mult-padding'>
                                                            <input type="text" id='item-discount-<?=$i?>' class="item-discount span12 typeahead" maxlength="100"  name='item_disc[]'>
                                                    </td>
                                                    <td class='mult-padding'>
                                                        <input type="text" id='item-vat-<?=$i?>' class="item-vat span12 typeahead" maxlength="100"  name='item_vat[]' readonly>
                                                    </td>
                                                    <td class='mult-padding'>
                                                            <input type="text" id='item-total-<?=$i?>' class="item-total span12 typeahead" maxlength="100"  name='item-total[]'readonly>
                                                    </td>
                                                    <td class='del-palance-item'   class='mult-padding' prop='<?=$i?>'><label>x</label></td>
                                                    <td class='more-palance-item'  class='mult-padding' prop='<?=$i?>'><label>+</label></td>
                                            </tr>
                                                <?php } ?>
                                          </table>
                                        </div>
                                    </div>                                  				
                                    <hr>
							
                                   						
                                    <div class="form-actions">
                                        <button type="button" id='submit-openPalance' class="btn btn-primary"><?=SAVE?></button>
                                        <button type="reset" class="btn"><?=CANCEL?></button>
                                    </div>
                                    
                                    <p class='error-red'> </p>
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
        {
            var config = {
                '.span3'     : { width: '95%' }
            };
            for (var selector in config) 
            {
                $(selector).chosen(config[selector]);
            }
            
            $(".item-qty").blur(function()
            {
                var id = $(this).attr('id').replace('item-qty-','');
                totalItemOpenPalance(id);
            });

            $(".item-price").blur(function()
            {
                var id = $(this).attr('id').replace('item-price-','');
                totalItemOpenPalance(id);
            });

            $(".item-discount").blur(function()
            {
                var id = $(this).attr('id').replace('item-discount-','');
                totalItemOpenPalance(id);
            });
            
        });
                

                    
    </script>
   
</body>
</html>
