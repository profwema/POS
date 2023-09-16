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

<script type="text/javascript">   

        var v1 ,v2 ;
        v1 = v2 =  0;

    $(document).ready(function()
    {  

        $("#user").change(function()
        {       
            var usr = $("#user").val();
            if(usr == '') 
            {
                $("#valid").html('');
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
                $('#strength').html('');      
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


<body>
    <?php require_once("header_top.php");?>
    <div class="container-fluid-full">
        <div class="row-fluid">
            <?php require_once("left_menu.php");?>
                <div id="content" class="span10">
                    <div>
                        <div class="box span12">
                            <div class="box-header" data-original-title>
                                <h2>
                                    <i class="halflings-icon edit"></i><span class="break"></span>
            <?=ADD_EMPLOYEE?>
                                </h2>
                            </div>
                            <div class="box-content">
                                <form class="form-horizontal" id="add-emp" enctype="multipart/form-data" method='POST'action="#">
                                    <input type="hidden" value="addEmp" name="f">
                                    <fieldset>     
                                      <div class="control-group">
                                          <label class="control-label" for="typeahead"> <?=EMPLOYEE_NAME?> : </label>
                                          <div class="controls" style="">
                                              <input type="text" class="span6 typeahead"  placeholder="<?=LANGUAGE_1?>" name='name' id='name' required> &nbsp; *
                                                 <span class="help-inline">&nbsp;</span>
                                          </div>
                                      </div>
                                        
                                      <div class="control-group">
                                          <label class="control-label" for="typeahead"> <?=EMPLOYEE_NAME?> : </label>
                                          <div class="controls">
                                              <input type="text" class="span6 typeahead" placeholder="<?=LANGUAGE_2?>" name='name_ar'  required  >
                                                 <span class="help-inline">&nbsp;</span>
                                          </div>
                                      </div>                                                        
                                      <div class="control-group">
                                          <label class="control-label" for="fileInput"><?=IMAGE?> : </label>
                                          <div class="controls">
                                                <div class="uploader" id="uniform-fileInput">
                                                    <input class="input-file uniform_on" id="fileInput" name="file" type="file" accept="image/*">
                                                    <span class="filename" style="-webkit-user-select: none;">No file selected</span>
                                                    <span class="action" style="-webkit-user-select: none;">Choose File</span></div>
                                          </div>
                                      </div>
                                        <div class="control-group">
                                            <div class="salesOptions">    
                                                 <label class="control-label" for="typeahead"><?=USERNAME?> : </label>
                                                 <div class="controls" style="position: relative;">
                                                    <input class="span6 typeahead" type="text" name="user" id ="user" required>                                                    
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
                                                 <input type="text" class="span6 typeahead"  name='email' id='email'> &nbsp; *
                                                 <span class="help-inline email-un">&nbsp;</span>
                                          </div>
                                      </div>
                                      <div class="control-group">
                                          <label class="control-label" for="typeahead"><?=TILL_CONTROL?> : </label>
                                          <div class="controls">
                                                <input type="checkbox"  value="1" name="till_control" >
                                          </div>
                                      </div>
                                      <div class="control-group">
                                          <label class="control-label" for="typeahead"><?=SHOW_REPORT?> : </label>
                                          <div class="controls">
                                                <input type="checkbox"  value="1" name="show_report">
                                          </div>
                                      </div>                                                        
                                      <div class="control-group">
                                          <label class="control-label" for="typeahead"><?=ALLOW_PRICE_CHANGE?> : </label>
                                          <div class="controls">
                                                <input type="checkbox"  value="1" name="allow_price_change">
                                          </div>
                                      </div>
                                      <div class="control-group">
                                          <label class="control-label" for="typeahead"><?=ALLOW_DIS_CHANGE?> : </label>
                                          <div class="controls">
                                                <input type="checkbox"  value="1" name="allow_dis_change">
                                          </div>
                                      </div>
                                      <div class="control-group">
                                          <label class="control-label" for="typeahead"><?=CARD_PAYMENT?> : </label>
                                          <div class="controls">
                                              <input type="checkbox"  value="1" name="visa" checked>
                                          </div>
                                      </div>
                                      <div class="control-group">
                                          <label class="control-label" for="typeahead"><?=SABAKAH?> : </label>
                                          <div class="controls">
                                                <input type="checkbox"  value="1" name="mada" checked>
                                          </div>
                                      </div>
                                      <div class="control-group">
                                          <label class="control-label" for="typeahead"><?=MOBILE?> : </label>
                                          <div class="controls">
                                                 <input type="text" class="int-val span6 typeahead" maxlength="14"  name='mobile' id='mobile'>
                                                 <span class="help-inline">&nbsp;</span>
                                          </div>
                                      </div>
                                      <div class="control-group">

                                              <label class="control-label"><?=BRANCH?> :</label>
                                              <div class="controls">
                                                  <select name="branch" id='branch' data-rel="chosen">
                                                     
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

<!--                                                            <div class="salesOptions">

                                            <label class="control-label"><?=STORES?> :</label>
                                              <div class="controls">
                                                      <select name="store" id='store' data-rel="chosen" >
                                                          <option value="">&nbsp;</option>

                                                        <?php
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
                                              <select name="type" id='type'data-rel="chosen">
                                                  <option value="">&nbsp;</option>
                                                    <?php
                                                            $query = "SELECT * FROM jobtitle  ORDER BY name_en ASC";
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
                                      <div class="control-group">
                                          <label class="control-label" for="typeahead"><?=PASSPORT_NUMBER?> : </label>
                                          <div class="controls">
                                             <input type="text" class="span6 typeahead"   maxlength="14" name='passport_num' id='passport_num'>
                                             <span class="help-inline">&nbsp;</span>
                                          </div>
                                      </div>
                                      <div class="control-group">
                                          <label class="control-label"><?=PASSPORT_EXPIRY?></label>
                                          <div class="controls">
                                              <input type="text" class="input-xlarge datepicker" name="passport-expiry"  >
                                          </div>
                                      </div>
                                      <div class="anti-service control-group">
                                          <label class="control-label" for="typeahead"><?=IQAMA_NUMBER?> : </label>
                                          <div class="controls">
                                              <input type="text" class="int-val span6 typeahead" maxlength="14"  name='iqama_number' id='iqama_number'>
                                              <span class="help-inline">&nbsp;</span>
                                          </div>
                                      </div>
                                      <div class="control-group">
                                          <label class="control-label"><?=IQAMA_EXPIRY?></label>
                                          <div class="controls">
                                              <input type="text" class="input-xlarge datepicker" name="iqama-expiry"  >
                                          </div>
                                      </div>
                                      <div class="anti-service control-group">
                                          <label class="control-label" for="typeahead"><?=INSURANCE_NUMBER?> : </label>
                                          <div class="controls">
                                              <input type="text" class="span6 typeahead" maxlength="14"  name='insurance_number' id='iqama_number'>
                                              <span class="help-inline">&nbsp;</span>
                                          </div>
                                      </div>     
                                      <div class="control-group">
                                                <label class="control-label"><?=INSURANCE_EXPIRY?></label>
                                                <div class="controls">
                                                  <input type="text" class="input-xlarge datepicker" name="insurance-expiry"  >
                                                </div>
                                      </div>
                                      <div class="anti-service control-group">
                                          <label class="control-label" for="typeahead"><?=MEDICAL_NUMBER?> : </label>
                                          <div class="controls">
                                                <input type="text" class="span6 typeahead" maxlength="14"  name='medical_number' id='iqama_number'>
                                                 <span class="help-inline">&nbsp;</span>
                                          </div>
                                      </div>
                                      <div class="control-group">
                                                <label class="control-label"><?=MEDICAL_EXPIRY?></label>
                                                <div class="controls">
                                                  <input type="text" class="input-xlarge datepicker" name="medical-expiry"  >
                                                </div>
                                      </div>
                                      <div class="anti-service control-group">
                                          <label class="control-label" for="typeahead"><?=LICENCE_NUMBER?> : </label>
                                          <div class="controls">
                                                <input type="text" class="span6 typeahead" maxlength="14"  name='license_number' id='iqama_number'>
                                                 <span class="help-inline">&nbsp;</span>
                                          </div>
                                      </div>
                                      <div class="control-group">
                                                <label class="control-label"><?=LICENCE_EXPIRY?></label>
                                                <div class="controls">
                                                  <input type="text" class="input-xlarge datepicker" name="license-expiry"  >
                                                </div>
                                      </div>
                                      <div class="form-actions">
                                          <button type="submit" id='submit-emp' class="btn btn-primary">
                                                            <?=SAVE?>
                                          </button>
                                          <button type="reset" class="btn"><?=CANCEL?></button>
                                      </div>
                                      <p>&nbsp;</p>
                                      <p class='error-red'>&nbsp;</p>                                        
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
