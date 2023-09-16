<?php
require_once("top.php");
require_once("redirection.php");
require_once("controller.php");
$language 	= $_SESSION['lang'];
$storeid	= $_SESSION['storeid'];

$idval		= $adController->encrypt_decrypt(2,urldecode($_REQUEST['sd']),0);
$idval 		= $adController->encrypt_decrypt(2,$idval,0);
$idval2		= $adController->encrypt_decrypt(1,$_REQUEST['sd'],0);

$query		= "SELECT * FROM employees WHERE storeid='$storeid' AND id='$idval'";
$res		= mysqli_query($adController->MySQL,$query);
$dataCat	= mysqli_fetch_assoc($res);

$query 		= "SELECT * FROM images WHERE foreign_id='$dataCat[id]' AND `table`='employees'";
$resImage	= mysqli_query($adController->MySQL,$query);
$dataImage	= mysqli_fetch_assoc($resImage);

$img		= $adController->getDirectoryOnlyPath(DIR_EMP_NAME);
$thumb		= $img.$dataImage['thumb'];
$branch 	= $dataCat['store_branch'];
$store 	        = $dataCat['store'];
$type           = $dataCat['type'];
//if($data['store_branch']!="")						
//	$branchArray	= explode(",",$dataCat['store_branch']);
//else
//{
//    $query		  = "SELECT GROUP_CONCAT(id) AS br FROM branches WHERE storeid='$storeid'";
//    $resBranch       = mysqli_query($adController->MySQL,$query);
//    $dataBranch 	  = mysqli_fetch_assoc($resBranch);
//    $branchArray	  = explode(",",$dataBranch['br']);
//}

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>WAM Tech Soft</title>
	<?php require_once("script_php_variables.php");?>
	<?php require_once("header.php");?>	

<script type="text/javascript">   

        var v1 ,v2 ;
        v1 = v2 = 1;

    $(document).ready(function()
    {  
        var s_usr = $("#user").val();
        $("#user").change(function()
        {       
            var usr = $("#user").val();
            if(usr == '') 
            {
                $("#user").focus();               
                $("#valid").html('');
                v2=0;
            }
            else if (usr == s_usr)
            {
                               
            $("#valid").html('');
                v2=1; 
            }
            else
            {
                var re = /[^A-Za-z0-9_\s\t\n\,\.\'\\?\!\@\#\$\%\^\&\*\{\}\[\]\<\>\(\)\/\+\-\^\?\~\|\:]/;
                var result = re.test(usr);
                if (result)
                {
                    $("#valid").html('<span style = "color:red">ادخال غير صحيح</span>');
                }
                else 
                {
                    $("#valid").html('<span style = "color:blue"> جارى التحقق </span>');
                    $.ajax(
                             {  
                            type: "POST",  
				url: "controller.php?f=userPresent", 
                            data : {'username':usr},
                            success: function(msg)
                            {  
                                if($.trim(msg) == 'OK')
                                { 
                                    v1=1;      
                                    $("#valid").html('<span style = "color:green">اسم المستخدم متاج   </span>');						
                                } 
                                else  
                                {  
                                    $("#valid").html(msg);
                                    $("#user").focus();               

                                }
                            }
                        });      
                        
                    
                        
                }                    
            }
        }); 
        $('#pass').keyup(function()
        {

            if ($('#pass').val()!='')
                $('#strength').html(checkStrength($('#pass').val()));
            else
            {
                $('#strength').html('');  
                v2=1;
        }
        })   
        

        
$("#reveal").on('click',function() {
    var $pwd = $("#pass");
    if ($pwd.attr('type') === 'password') {
        $pwd.attr('type', 'text');
    } else {
        $pwd.attr('type', 'password');
    }
});        
        
        function checkStrength(password)
        { 
            var strength = 0; 
            if (password.length < 6) 
            {
                $('#strength').removeClass();
                $('#strength').addClass('short');
                v2=0
                $("#pass").focus();   
                return 'Too short';
            }
            if (password.length > 7) strength += 1;     
            if (password.match(/([a-z].*[A-Z])|([A-Z].*[a-z])/))  strength += 1; 
            if (password.match(/([a-zA-Z])/) && password.match(/([0-9])/))  strength += 1 ; 
            if (password.match(/([!,%,&,@,#,$,^,*,?,_,~])/))  strength += 1; 
            if (password.match(/(.*[!,%,&,@,#,$,^,*,?,_,~].*[!,",%,&,@,#,$,^,*,?,_,~])/)) strength += 1;            
            if (strength < 2 ) 
            {
                $('#strength').removeClass();
                $('#strength').addClass('weak');
                v2=0
                $("#pass").focus();   
                return 'Weak';
            } 
            else if (strength == 2 ) 
            {
                $('#strength').removeClass();
                $('#strength').addClass('good');
                v2=1
                return 'Good';
            } 
            else 
            {
                $('#strength').removeClass();
                $('#strength').addClass('strong');
                v2=1
                return 'Strong';
            }
        }
//            $('.datepicker').datepicker({
//                dateFormat: 'yy-mm-dd'
//            });
    });
    



    
    
//        function validateForm()
//        {
//            if (v1 && v2)
//            {
//                
//                submitEmp();
//                           return false
//                       }
//            else
//                return false
//        }




   </script>

   <style>
    .short{
    color:red;
}
  .weak{
    color:red;
}
 .good{
    color:blue;
}
 .strong{
    color:green;
}
.label--valed {
    position: absolute;
    text-transform: capitalize;
    display: block;
    color: #999;
    font-size: 10px;

    left: 30px;
}
.icon
{
    cursor: pointer;

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
                            <h2><i class="halflings-icon edit"></i><span class="break"></span><?=UPDATE_EMPLOYEE?></h2>
                        </div>
                        <div class="box-content">
                            <form class="form-horizontal" id="add-emp"enctype="multipart/form-data" method='POST'action="#">
                                <input type="hidden" value="<?=$idval2?>" name="nd" id="nd">
                                <input type="hidden" value="updateEmp" name="f">
                                <fieldset>
                                     <div class="control-group">
                                          <label class="control-label" for="typeahead"> <?=EMPLOYEE_NAME?> : </label>
                                          <div class="controls" style="">
                                              <input type="text" class="span6 typeahead"  placeholder="<?=LANGUAGE_1?>" name='name' id='name'value="<?=$dataCat['name_en']?>" required> &nbsp; *
                                                 <span class="help-inline">&nbsp;</span>
                                          </div>
                                      </div>
                                        
                                      <div class="control-group">
                                          <label class="control-label" for="typeahead"> <?=EMPLOYEE_NAME?> : </label>
                                          <div class="controls">
                                              <input type="text" class="span6 typeahead" placeholder="<?=LANGUAGE_2?>" name='name_ar'value="<?=$dataCat['name_ar']?>"  required  >
                                                 <span class="help-inline">&nbsp;</span>
                                          </div>
                                      </div>                                     

                                    <div class="control-group">
                                        <label class="control-label" for="fileInput"><?=IMAGE?> : </label>
                                        <div class="controls">
                                            <div class="uploader" id="uniform-fileInput"><input class="input-file uniform_on" id="fileInput" name="file" type="file" accept="image/*"><span class="filename" style="-webkit-user-select: none;">No file selected</span><span class="action" style="-webkit-user-select: none;">Choose File</span></div>
                                            &nbsp;<img src='<?=$thumb?>' class="thumb">
                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <div class="salesOptions">    
                                                 <label class="control-label" for="typeahead"><?=USERNAME?> : </label>                                        
                                                 <div class="controls" style="position: relative;">
                                                <input class="span6 typeahead" type="text" name="user" id ="user"value="<?=$dataCat['user']?>" required>                                                    
                                                <span class="label--valed" id="valid"></span>
                                            </div>
                                        </div>                                                
                                        <div class="salesOptions">    
                                                <label class="control-label" for="typeahead"><?=PASSWORD?> : </label>                                        
                                                <div class="controls" style="position: relative;">              
                                                 <input class="span6 typeahead" type="password" name="pass" autocomplete="new-password" id = "pass" required>     
                                                 <i class="icon fa fa-eye fa-fw "id="reveal" title="Reveal" aria-label="Reveal"></i>                
                                                <span class="label--valed" id="strength"></span>             
                                            </div>
                                        </div>
                                    </div>    
                                    
                                    <div class="control-group">
                                        <label class="control-label" for="typeahead"><?=EMAIL?> : </label>
                                        <div class="controls">
                                            <input type="text" class="span6 typeahead"  name='email' id='email' value="<?=$dataCat['email']?>"> &nbsp; *
                                            <span class="help-inline email-un">&nbsp;</span>
                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <label class="control-label" for="typeahead"><?=TILL_CONTROL?> : </label>
                                        <div class="controls">
                                            <input type="checkbox"  value="1" name="till_control" <?php if(intval($dataCat['till_control']) == 1) echo "checked='true'";?>>
                                        </div>
                                    </div>                                                        
                                    <div class="control-group">
                                        <label class="control-label" for="typeahead"><?=SHOW_REPORT?> : </label>
                                        <div class="controls">
                                            <input type="checkbox"  value="1" name="show_report" <?php if(intval($dataCat['show_report']) == 1) echo "checked='true'";?>>
                                              
                                        </div>
                                    </div>

                                           
                                    <div class="control-group">
                                              
                                        <label class="control-label" for="typeahead"><?=ALLOW_PRICE_CHANGE?> : </label>
                                              
                                        <div class="controls">
                                                
                                            <input type="checkbox"  value="1" name="allow_price_change" <?php if(intval($dataCat['allow_price_change']) == 1) echo "checked='true'";?>>
                                             
                                        </div>
                                           
                                    </div>
                                           
                                    <div class="control-group">
                                           
                                        <label class="control-label" for="typeahead"><?=ALLOW_DIS_CHANGE?> : </label>
                                             
                                        <div class="controls">
                                                
                                            <input type="checkbox"  value="1" name="allow_dis_change" <?php if(intval($dataCat['allow_dis_change']) == 1) echo "checked='true'";?>>
                                            
                                        </div>
                                          
                                    </div>
                                            
                                    <div class="control-group">
                                             
                                        <label class="control-label" for="typeahead"><?=CARD_PAYMENT?> : </label>
                                            
                                        <div class="controls">
                                               
                                            <input type="checkbox"  value="1" name="visa" <?php if(intval($dataCat['visa']) == 1) echo "checked='true'";?>>
                                            
                                        </div>
                                          
                                    </div>
                                           
                                    <div class="control-group">
                                         
                                        <label class="control-label" for="typeahead"><?=SABAKAH?> : </label>
                                            
                                        <div class="controls">
                                             
                                            <input type="checkbox"  value="1" name="mada" <?php if(intval($dataCat['mada']) == 1) echo "checked='true'";?>>
                                          
                                        </div>
                                           
                                    </div>

                                          
                                    <div class="control-group">
                                        
                                        <label class="control-label" for="typeahead"><?=MOBILE?> : </label>
                                        
                                        <div class="controls">
                                               
                                            <input type="text" class="int-val span6 typeahead" maxlength="14"  name='mobile' id='mobile' value="<?=$dataCat['contact']?>">
                                                 
                                            <span class="help-inline">&nbsp;</span>
                                            
                                        </div>
                                        
                                    </div>
                                       
                                    <div class="control-group">
                                        
                                        <div class="salesOptions">
                                           
                                            <label class="control-label"><?=BRANCH?> :</label>
                                            
                                            <div class="controls">
                                                
                                                <select name="branch" id='branch' data-rel="chosen">
                                                     
       <?php
                                                   
       $query = "SELECT * FROM branches WHERE storeid='$storeid' ORDER BY name_en ASC";
                                                         
       $resb   = mysqli_query($adController->MySQL,$query) or die(mysqli_error($adController->MySQL));
                                                        
       while($datab= mysqli_fetch_assoc($resb))
                                                      
               {
                                      
           $id   = $datab['id'];
                                               
           $name = $datab['name_'.$language];
                                              
           $selected	="";
                                              
           if($datab['id'] == $branch)
                                                       
               $selected=" selected='true' ";
                                                      
           echo "<option value='$id' $selected>$name</option>";

                                                     
           }
                                                      
           ?>
                                                      
                                                </select>    
                                               
                                            </div>
                                              
                                        </div>

<!--                                               
                                        <div class="salesOptions">

                                             
                                            <label class="control-label"><?=STORES?> :</label>
                                            
                                            <div class="controls">
                                                 
                                                <select name="store" id='store' data-rel="chosen" >
                                                   
                                                    <option value="">&nbsp;</option>

                                                    
                                                    
                                                            <?php

                                                           
                                                            $query 	= "SELECT * FROM stores WHERE storeid='$storeid' AND branch = '$branch'";
                                                                    $resS   = mysqli_query($adController->MySQL,$query) or die(mysqli_error($adController->MySQL));
                                                                    while($dataS= mysqli_fetch_assoc($resS))
                                                                    {
                                                                            $id   = $dataS['id'];
                                                                            $name = $dataS['name_'.$language];
                                                                            $selected	="";
                                                                            if($dataS['id'] == $store)
                                                                                    $selected=" selected='true' ";
                                                                            echo "<option value='$id' $selected>$name</option>";

                                                                    }

    //                                                                         if($_REQUEST["branch"]!='')
    //                                                                        {  
    //                                                                            $query 	= "SELECT * FROM stores WHERE storeid='$storeid' AND branch = '$_REQUEST[branch]'";
    //                                                                            //echo $query;
    //                                                                        
    //                                                                           //$query 	= "SELECT * FROM stores WHERE storeid='$storeid' AND branch = '$_REQUEST[branch]'";
    //                                                                           $resS		= mysqli_query($adController->MySQL,$query);
    //                                                                           while($dataS 	= mysqli_fetch_assoc($resS))
    //                                                                           {
    //                                                                                $id   = $dataS['id'];
    //                                                                                $name = $dataS['name_'.$language];
    //                                                                                echo "<option value='$id'>$name</option>";
    //                                                                           }
    //                                                                        }
                                                               ?>
                                                         </select>
                                                      </div>                  
                                                </div>-->
                                           </div>
                                            <div class="control-group">
                                              <label class="control-label"><?=DESIGNATION?> :</label>
                                              <div class="controls" id="cat-list-data">
                                                      <select name="type" id='type' data-rel="chosen">
                                                            <?php

                                                            
                $query = "SELECT * FROM jobtitle WHERE storeid='$storeid' ORDER BY name_en ASC";
                        $resj   	= mysqli_query($adController->MySQL,$query) or die(mysqli_error($adController->MySQL));

                        while($dataj= mysqli_fetch_assoc($resj))
                        {
                                $id   = $dataj['id'];
                                $name = $dataj['name_'.$language];
                                $selected	="";
                                if($dataj['id'] == $type)
                                    $selected=" selected='true' ";       
                                echo "<option value='$id' $selected> $name</option>";

                        }
                                                            ?>
                                                      </select>
                                              </div>

                                            </div>



                                            <div class="control-group">
                                              <label class="control-label" for="typeahead"><?=PASSPORT_NUMBER?> : </label>
                                              <div class="controls">
                                                     <input type="text" class="span6 typeahead"   maxlength="14" name='passport_num' id='passport_num' value="<?=$dataCat['passport_num']?>">
                                                     <span class="help-inline">&nbsp;</span>
                                              </div>
                                            </div>
                                            <div class="control-group">
                                                    <label class="control-label"><?=PASSPORT_EXPIRY?></label>
                                                    <div class="controls">
                                                      <input type="text" class="input-xlarge datepicker" name="passport-expiry"  value="<?=$adController->getDateForUpdate($dataCat['passport_expiry'])?>">
                                                    </div>
                                            </div>
                                            <div class="anti-service control-group">
                                              <label class="control-label" for="typeahead"><?=IQAMA_NUMBER?> : </label>
                                              <div class="controls">
                                                    <input type="text" class="int-val span6 typeahead" maxlength="14"  name='iqama_number' id='iqama_number' value="<?=$dataCat['iqama_num']?>">
                                                     <span class="help-inline">&nbsp;</span>
                                              </div>
                                            </div>
                                            <div class="control-group">
                                                    <label class="control-label"><?=IQAMA_EXPIRY?></label>
                                                    <div class="controls">
                                                      <input type="text" class="input-xlarge datepicker" name="iqama-expiry"  value="<?=$adController->getDateForUpdate($dataCat['iqama_expiry'])?>">
                                                    </div>
                                            </div>
                                            <div class="anti-service control-group">
                                              <label class="control-label" for="typeahead"><?=INSURANCE_NUMBER?> : </label>
                                              <div class="controls">
                                                    <input type="text" class="span6 typeahead" maxlength="14"  name='insurance_number' id='insurance_number' value="<?=$dataCat['insurance_num']?>">
                                                     <span class="help-inline">&nbsp;</span>
                                              </div>
                                            </div>
                                            <div class="control-group">
                                                    <label class="control-label"><?=INSURANCE_EXPIRY?></label>
                                                    <div class="controls">
                                                      <input type="text" class="input-xlarge datepicker" name="insurance-expiry"  value="<?=$adController->getDateForUpdate($dataCat['insurance_expiry'])?>">
                                                    </div>
                                            </div>
                                            <div class="anti-service control-group">
                                              <label class="control-label" for="typeahead"><?=MEDICAL_NUMBER?> : </label>
                                              <div class="controls">
                                                    <input type="text" class="span6 typeahead" maxlength="14"  name='medical_number' id='medical_number'  value="<?=$dataCat['medical_num']?>">
                                                     <span class="help-inline">&nbsp;c</span>
                                              </div>
                                            </div>
                                            <div class="control-group">
                                                    <label class="control-label"><?=MEDICAL_EXPIRY?></label>
                                                    <div class="controls">
                                                      <input type="text" class="input-xlarge datepicker" name="medical-expiry"  value="<?=$adController->getDateForUpdate($dataCat['medical_expiry'])?>" >
                                                    </div>
                                            </div>
                                            <div class="anti-service control-group">
                                              <label class="control-label" for="typeahead"><?=LICENCE_NUMBER?> : </label>
                                              <div class="controls">
                                                    <input type="text" class="span6 typeahead" maxlength="14"  name='license_number' id='license_number'   value="<?=$dataCat['license_num']?>">
                                                     <span class="help-inline">&nbsp;</span>
                                              </div>
                                            </div>
                                            <div class="control-group">
                                                    <label class="control-label"><?=LICENCE_EXPIRY?></label>
                                                    <div class="controls">
                                                      <input type="text" class="input-xlarge datepicker" name="license-expiry" value="<?=$adController->getDateForUpdate($dataCat['license_expiry'])?>" >
                                                    </div>
                                            </div>

                                            <div class="form-actions">
                                                <button type="submit" id='submit-emp' class="btn btn-primary"><?=SAVE?></button>
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
