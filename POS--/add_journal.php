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
						<h2><i class="halflings-icon edit"></i><span class="break"></span><?=JOURNAL?> <?=ADD_NEW?></h2>
					</div>
                                    <div class="box-content" id="printed">
						<form class="form-horizontal" id="journal-form">
						  <input type="hidden" value="addJournal" name="f">
						  <fieldset>
							<div class="control-group">
                                                            <div class="salesOptions" >
							  <label class="control-label" for="typeahead"><?=JOURNAL_NUMBER?>:</label>
							  <div class="controls">
                                                               <?php
                                                               $query   = "SELECT journalNo
                                                                        FROM journal
                                                                        ORDER BY journalNo DESC
                                                                        LIMIT 1";
                                                                $res	= mysqli_query($adController->MySQL,$query) or die(mysqli_error($adController->MySQL));
                                                                $num	= mysqli_num_rows($res);
                                                                $data	= mysqli_fetch_assoc($res);
                                                                if($num)
                                                                {
                                                                    $rest = substr($data['journalNo'], -4);  
                                                                    $trans_No = intval($rest)+1;
                                                                }
                                                                else 
                                                                    $trans_No = 1;

                                                                $time           = time();
                                                                $journalNo        = 'JUOR-'.$time.'-'.str_pad($trans_No,4,"0",STR_PAD_LEFT);; 
                                                               ?>
                                                              <input type="text" 
                                                                     class=" typeahead"  
                                                                     name='journalNo' 
                                                                     id='invoice' 
                                                                     value ='<?php echo $journalNo ?>'
                                                                     readonly>
							  </div>
							</div>  
                                                            <div class="salesOptions" >
							  <label class="control-label" for="typeahead"><?=DOCUMENT?> : </label>
							  <div class="controls">
								 <input type="text" class=" typeahead"  name='document' id='document'>
							  </div>
							</div>
                                                        </div>
                                                        <div class="control-group">                                                                                                                       
                                                            <div class="salesOptions">
							  <label class="control-label"><?=BRANCH?> :</label>
							  <div class="controls">
								  <select name="branch" data-rel="chosen">
                                                                        <option value=''> </option>
									<?php
										$query = "SELECT * FROM branches  ORDER BY name_en ASC";
										$res   = mysqli_query($adController->MySQL,$query) or die(mysqli_error($adController->MySQL));
										while($data= mysqli_fetch_assoc($res))
										{
											$id   = $data['id'];
											$name = $data['name_'.$language];
											echo "<option value='$id'>$name</option>";

										}
									?>
								  </select>
							  </div>
                                                            </div>
                                                      <div class="salesOptions">
								<label class="control-label"><?=DATE?></label>
								<div class="controls">
								  <input type="text" class="datepicker-nobar" name="date"  
                                                                         value='<?php echo date("d/m/Y")?>'>
								</div>                                                           
							</div>                                                             
                                                            
                                                        </div>
                                                        <div class="control-group">                                                                                                                       
                                                            <div class="salesOptions" >
								<label class="control-label"><?=COST_CENTER?></label>
								<div class="controls">
								  <select name="cost" data-rel="chosen">
                                                                        <option value=''> </option>  
                                                                  </select>
								</div>
							</div>    
                                                            
                                        <div class="salesOptions">
                                        <label class="control-label" for="typeahead"><?=DISCRIPTION?> : </label>
                                        <div class="controls">
                                            <textarea class="span6 typeahead"  style="width: 90%" name="disc" id="disc"  rows="5"  required='required'></textarea>                                            
                                        </div>                                  
                                    </div>                                                              
                                                        </div>
                                    <div class="control-group" >
                                            <div class="controls" style="margin-top:20px; margin-left:0px">	
                                                <table  class=" table-bordered " style="width:100%">                                                
                                                                                    
                                                    <tr>
                                                    <TH ><?=ACCOUNT_NAME?> </TH>    
                                                    <TH ><?=DEBIT?></TH>
                                                    <TH ><?=CREDIT?></TH>
                                                    <TH ><?=DISCRIPTION?></TH>
                                                    <TH ><?=REFERENE?> </TH>
                                                    <TH ><?=COST_CENTER?> </TH>
                                                    <TH class="edit">&nbsp;</TH>
                                                    <TH class="edit">&nbsp;</TH>
                                                </tr>
                                                <?php
                                                for($i = 0 ;$i < 20 ; $i++)
                                                {
    //										
                                                ?>
                                                <tr <?=$rowStyle?> id='row-v-<?=$i?>'>
                                                    <td style="width:25%">
                                                  <select name="account[]" id='account-<?=$i?>'data-rel="chosen" style="width:90%" >
                                                      <option value="0">&nbsp;</option>
                                                    <?php
                                                        $language 	= $_SESSION['lang'];
                                                        $query = "SELECT * FROM  accounts WHERE hasChild ='0' ORDER BY parent ASC";

                                                        $res      = mysqli_query($adController->MySQL,$query);
                                                        while($data = mysqli_fetch_assoc($res))
                                                        {
                                                            $name = $data['name_'.$language];  
                                                            $code = $data['code'];  
                                                            $resP  = mysqli_query($adController->MySQL,"SELECT name_$language AS parent FROM  accounts WHERE id ='$data[parent]'");
                                                            $dataP = mysqli_fetch_assoc($resP);
                                                            $parent= $dataP['parent'];
                                                            echo "<option value='$code'>$parent -> $name  </option>";
                                                        }

                                                    ?>
                                                      </select> 
                                                    </td>

                                                    <td style="width:10%">
                                                            <input type="text" id='debit-<?=$i?>' class="debit span12 typeahead" maxlength="100"  name='debit[]' value="0.00">
                                                    </td>
                                                    <td style="width:10%">
                                                            <input type="text" id='credit-<?=$i?>' class="credit span12 typeahead" maxlength="100"  name='credit[]' value='0.00'>
                                                    </td>
                                                    <td style="width:30%">
                                                            <input type="text" id='disc-<?=$i?>' class="span12 typeahead" maxlength="100"  name='discItem[]'>
                                                    </td>
                                                    <td style="width:15%">
                                                            <input type="text" id='ref-<?=$i?>' class="span12 typeahead" maxlength="100"  name='ref[]'>
                                                    </td>
                                                    <td style="width:10%">
								  <select name="costItem[]" data-rel="chosen">
                                                                        <option value=''> </option>  
                                                                  </select>       
                                                    </td>
                                                    <td class='del-jurnal-item mult-padding edit' prop='<?=$i?>'><label>x</label></td>
                                                    <td class='more-jurnal-item mult-padding edit' prop='<?=$i?>'><label>+</label></td>
                                            </tr>
                                                <?php } ?>
                                          </table>
                                        </div>
                                    </div>                                  				
                                  
                                    <div class="control-group">
                                        <div style="margin-top:20px; float: right; margin-right: 20px">
                                            
                                            	
                                                <table class=" table-bordered " >                                                
                                                     <tr>
                                                         <TH colspan="2"><h4><?=TOTAlS?></h4></th>
                                                     </tr>
                                                    <tr>
                                                        
                                                        <Td style="width:40%">اجمالي المدين </td>
                                                    <Td  >
                                                        <input type="text" class="span12 typeahead" name='total-debit' id="total-debit" value="0.00"  width="80%" readonly>
                                                        </Td>    
                                                    </tr>
                                                    <tr>
                                                    <Td> اجمالي دائن</td>
                                                    <Td >
                                                        <input type="text" class="span12 typeahead" name="total-credit" id="total-credit" value="0.00" readonly>
                                                        </Td>    
                                                    </tr>


                                                    <tr style="font-size: 16px ">
                                                        <Td style=" padding: 10px; ">الفرق</td>
                                                    <Td>
                                                        <input type="text" class="span12 typeahead" name="difference" id="difference" value="0.00"readonly>
                                                       </Td>    
                                                    </tr>
                                                </table>
                                            
                                        </div>
                                    </div>
							<div class="form-actions">
							  <button type="button" id='submit-journal' class="btn btn-primary"><?=SAVE?></button>
							  <button type="reset" class="btn"><?=CANCEL?></button>
							</div>
							<p class='error-red'></p>

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
             

function totals()
{
       // يحسب الاجمالى فى الاسفل
        var total_debit = 0;
        var total_credit = 0;
    for ( var i = 0 ; i<10; i++)
    {


            total_debit = total_debit + parseFloat( $("#debit-"+i).val()); 
            total_credit = total_credit + parseFloat( $("#credit-"+i).val()); 
            
       
    }
    $("#total-debit").val( total_debit.toFixed(2) ) ;
    $("#total-credit").val( total_credit.toFixed(2) ) ;
    // بيحسب الاجمالى النهائى للكل فى الاسفل باضافه الاجمالى شامل الضريبه على الخصم الاضافي
    var difference = total_debit - total_credit;
    $("#difference").val ( difference.toFixed(2) ) ;
}
             
        $(document).ready(function()
        {
            $(".debit").change(function()
            {
                var id = $(this).attr('id').replace('debit-','');
                $("#credit-"+id).val ('0.00')  ;
                totals();
            });

            $(".credit").change(function()
            {
                var id = $(this).attr('id').replace('credit-','');
                $("#debit-"+id).val ( '0.00')  ;
                totals();
            });
            
            var config = {
                '.span3'     : { width: '95%' }
            };
            for (var selector in config) 
            {
                $(selector).chosen(config[selector]);
            }

            
        });
                

                    
    </script>
</body>
</html>
