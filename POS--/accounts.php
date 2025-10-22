<?php
error_reporting(E_ALL);
require_once("top.php");
require_once("redirection.php");
require_once("controller.php");
$language 	= LANG;
$storeid	= $_SESSION['storeid'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>WAM Tech Soft</title>
	<?php require_once("header.php");?>	
        <style>
            .accountsTree
            {
                border: #eee solid  1px;
                padding: 10px;
                margin-top: 20px;
                margin-left: 10px;
                margin-right: 10px;              
                width: 47%;
                float: left;
                height: 500px;
                overflow-y: auto;
            }
            .accountEdit
            {
                border: #eee solid  1px;
                padding: 0 ;
                margin-top: 20px;
                margin-left: 10px;
                margin-right: 10px;
                width: 45%;
                float: right;
                margin-bottom: 30px;
            }            
            .typeahead
            {
                width: 80%;
            }
            .form-buttons
            {
                                /* padding: 19px 20px 20px; */
                padding:  10px;  
                background-color: #f5f5f5;
                border: 1px solid #e5e5e5;
                text-align: center;
                margin: 15px;
            }
            .form-horizontal            {
                padding: 0 10px;
                margin-top: 10px;
                    
            }
            .pair
            {
              padding-left: 35px;
              margin-left: 0px;

            }
            #pair-0
            {
              padding-left: 0px;
              margin-left: 0px;

            }
            .account
            {
                margin: 5px;
                background-color: buttonface;
                border: 1px solid #e5e5e5;
                padding:5px 5px 5px 15px;
            }
            button 
            {
              border: 1px solid #0066cc;
              background-color: #0099cc;
              color: #ffffff;
              padding: 5px 10px;
            }

            button:hover {
              border: 1px solid #0099cc;
              background-color: #00aacc;
              color: #ffffff;
              padding: 5px 10px;
            }
            button:disabled,
            button[disabled]
            {
              border: 1px solid #999999;
              background-color: #cccccc;
              color: #666666;
            }

        
        </style>
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
						<h2><i class="halflings-icon edit"></i><span class="break"></span><?=ACCOUNTS?></h2>
					</div>
                            <div class="box-content">
                                <div class="accountsTree">
                                    <div id="pair-0" class='pair'></div>
                                    
                                </div>            
                                <div class="accountEdit">
                                                                                        
                                    <div class="form-buttons">
                                              <button type="button" id='new' ><?=ADD?></button>
                                              <button type="button" id='edit' disabled><?=EDIT?></button>
                                              <button type="button" id='del' disabled><?=DELETE?></button>
                                            </div>                                    
                                    <form class="form-horizontal" id="form_Account">
                                        
  
                                        <input type="hidden" name="f"id='f'>
                                        <input type="hidden" name="aid" id='aid'>
                                         <div class="control-group">
                                              <label class="control-label"><?=PAIRENT_ACCOUNT?> :</label>
                                              <div class="controls">
                                                  
                                                  <select name="pairent" id='pairent' style="width:90%"  disabled>

                                                      </select> 
                                              </div>
                                            </div>  
                                        <div class="control-group">
                                              <label class="control-label"><?=ACCOUNT_TYPE?> :</label>
                                              <div class="controls">
                                                      <select id='type'  class='span3'disabled>
                                                          <option value="0">Account Type</option>
                                                            <?php
                                                                    $query = "SELECT * FROM account_type ORDER BY name_en ASC";
                                                                    $res   = mysqli_query($adController->MySQL,$query) or die(mysqli_error($adController->MySQL));
                                                                    while($data= mysqli_fetch_assoc($res))
                                                                    {
                                                                            $id   = $data['id'];
                                                                            $name = $data['name_'.$language];
                                                                            echo "<option value='$id'>$name</option>";

                                                                    }
                                                            ?>
                                                      </select> 
                                                  <input type="hidden" name="type" id='ty'>

                                              </div>
                                            </div>
                                            <div class="control-group">
                                              <label class="control-label"><?=PLBS?> :</label>
                                              <div class="controls">
                                                      <select  id='flbs' class='span3' disabled>
                                                          <option value="0">choose <?=PLBS?></option>
                                                            <?php
                                                                    $query = "SELECT * FROM budget_set ORDER BY name_en ASC";
                                                                    $res   = mysqli_query($adController->MySQL,$query) or die(mysqli_error($adController->MySQL));
                                                                    while($data= mysqli_fetch_assoc($res))
                                                                    {
                                                                            $id   = $data['id'];
                                                                            $name = $data['name_'.$language];
                                                                            echo "<option value='$id'>$name</option>";

                                                                    }
                                                            ?>
                                                      </select> 
                                                  <input type="hidden" name="flbs" id='fl'>
                                              </div>
                                            </div>                                        
                                            <h4><?=LANGUAGE_1?></h4>
                                            <div class="control-group">
                                                <label class="control-label" for=" typeahead"><?=ACCOUNT_NAME?> : </label>
                                                <div class="controls">
                                                    <input type="text" class=" typeahead"  name='name_en' id='name_en' disabled> *
                                                     <span class="help-inline">&nbsp;</span>
                                                </div>
                                            </div>
                                            
                                            <h4><?=LANGUAGE_2?></h4>
                                            <div class="control-group">
                                                <label class="control-label" for=" typeahead"><?=ACCOUNT_NAME?> : </label>
                                                <div class="controls">
                                                    <input type="text" class=" typeahead"  name='name_ar' id='name_ar' disabled> *
                                                    <span class="help-inline">&nbsp;</span>
                                                </div>
                                            </div> 
                                            <div class="control-group">
                                                
                                                <label class="control-label" for="typeahead"><?=ACCOUNT_CODE?> : </label>
                                                <div class="controls">

                                                    <input type="text" class="typeahead"  name='code' id='code' disabled>
                                                    <span class="help-inline">&nbsp;</span>
                                              </div>
                                            </div>                                             

                                            
                                            <div class="control-group">
                                                <label class="control-label"><?=CHILD?></label>
                                                <div class="controls">
                                                    <label class="checkbox inline">
                                                        <input type="checkbox" value="1" name="child" id="child" disabled>
                                                    </label>
                                                </div>
                                            </div>                                             
                                    <div class="form-buttons">
                                              <button type="button" id='submit-acc'  disabled><?=SAVE?></button>
                                              <button type="button" id='reset' disabled><?=CANCEL?></button>
                                            </div>  
                                    </form>                                                     
                            
                                </div>                                       
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
            '.span3'     : { width: '90%' }
        }
        for (var selector in config) 
        {
            $(selector).chosen(config[selector]);
        }
        fill_treeview(0);     
        fill_parent_account();  

        function fill_parent_account()
        {
            var u="controller.php?f=fill_parent_account";   
            $.ajax
            ({    
                url:u,
                success:function(data)
                {   
                    $('#pairent').html(data);
                    $("#pairent").chosen("destroy").chosen();
                }
            });   
        }      
        function fill_treeview(pair)
        {      

            var u="controller.php?f=fitch&p="+pair; 
            $.ajax
            ({
                url:u,
                success:function(data)
                {
                    $('#pair-'+pair).html(data);
                }
            })
        }        
        function enableForm()
        {       
            var p = $("#pairent").val();   
            $("#pairent").removeAttr("disabled");  
            $("#pairent").val(p).trigger("chosen:updated");
            if (p =='0')
            {
                var t = $("#type").val();           
                $("#type").removeAttr("disabled");
                $("#type").val(t).trigger("chosen:updated");
                var f = $("#flbs").val();   
                $("#flbs").removeAttr("disabled");    
                $("#flbs").val(f).trigger("chosen:updated");            
            }   
            $("#name_en").removeAttr("disabled");
            $("#name_ar").removeAttr("disabled"); 
            $("#child").removeAttr("disabled");
            $("#submit-acc").removeAttr("disabled");
            $("#reset").removeAttr("disabled");
        }
        function disableForm()
        {       
            $("#pairent").attr('disabled', 'disabled');              
            $("#type").attr('disabled', 'disabled');              
            $("#flbs").attr('disabled', 'disabled'); 
            $("#pairent").val('0').trigger("chosen:updated");
            $("#type").val('').trigger("chosen:updated");
            $("#flbs").val('').trigger("chosen:updated");            
            $("#name_en").attr('disabled', 'disabled');
            $("#name_ar").attr('disabled', 'disabled');
            $("#child").attr('disabled', 'disabled');
            $("#submit-acc").attr('disabled', 'disabled');
            $("#reset").attr('disabled', 'disabled');
            $("#edit").attr('disabled', 'disabled');
            $("#del").attr('disabled', 'disabled');
            $("#new").removeAttr("disabled");
        }        
        function clearform()
        {  
            $("#pairent").val('0').trigger("chosen:updated");
            $("#type").val('').trigger("chosen:updated");
            $("#flbs").val('').trigger("chosen:updated"); 
            $("#name_en").val('');
            $("#name_ar").val(''); 
            $("#code").val(''); 
            $("#child").attr('checked', false).triggerHandler('click');
        }        
       $("#new").click(function()
          {
              clearform();
              enableForm();  
              $("#edit").attr('disabled', 'disabled');
              $("#del").attr('disabled', 'disabled');
              $("#f").val('addAccount');
           }); 
       $("#edit").click(function()
          {
              enableForm();  
              $("#new").attr('disabled', 'disabled');
              $("#del").attr('disabled', 'disabled');
              $("#f").val('editAccount');
           });            
           
       $("#submit-acc").click(function()
          {
              $("#ty").val($("#type").val());
              $("#fl").val($("#flbs").val());
              $.ajax
              ({
                  url:"controller.php",
                  method:"POST",
                  data:$("#form_Account").serialize(),
                  success:function(data)
                  {                      
                      alert(data);       
                      fill_treeview(0);
                      
              fill_parent_account();
              clearform();
              disableForm();
                  }
              })  
                            
 
              //location.reload()
          });
          
       $("#del").click(function()
          {
                var aid = $("#aid").val();
                var u="controller.php?f=delAccount&aid="+aid; 
                $.ajax
                ({
                    url:u,
                    success:function(data)
                    {
                       alert(data);
                    }
                })                                                       
                setTimeout(function(){location.reload()},1400);
          });          
          
       $("#reset").click(function()
          {
               fill_treeview(0);
             
              fill_parent_account();
              clearform();
              disableForm(); 
          });

        $("#pairent").chosen().change(function() 
        {              
            var index     = $(this).val();
            if(index > 0 )
            {
                var type = $('option:selected', this).attr('type');
                var budget = $('option:selected', this).attr('budget');    
                $("#type").attr('disabled', 'disabled');              
                $("#type").val(type).trigger("chosen:updated");
                $("#flbs").attr('disabled', 'disabled'); 
                $("#flbs").val(budget).trigger("chosen:updated");
            }
            else 
            {
                $("#type").removeAttr("disabled");
                $("#flbs").removeAttr("disabled");                  
                $("#type").val('').trigger("chosen:updated");
                $("#flbs").val('').trigger("chosen:updated");                           
            }
        });    
        
        $(document).on("click", "a.acc" , function() 
        {
            if($('#submit-acc').is('[disabled=disabled]'))
            {
                $("#edit").removeAttr("disabled");
                $("#del").removeAttr("disabled");
                $("#pairent").val($(this).attr('p')).trigger("chosen:updated");
                $("#type").val($(this).attr('t')).trigger("chosen:updated");
                $("#flbs").val($(this).attr('b')).trigger("chosen:updated");
                $("#aid").val($(this).attr('aid'));                
                $("#name_en").val($(this).attr('e'));
                $("#name_ar").val($(this).attr('a'));
                $("#code").val($(this).attr('d'));   
                if($(this).attr('c')=='1')
                    $("#child").attr('checked', true).triggerHandler('click');
                else
                    $("#child").attr('checked', false).triggerHandler('click');
            }
            return false;
        })
                
        $(document).on("click", "i.showChild" , function() 
        {
            $(this).toggleClass('icon-plus-sign icon-minus-sign');
            $(this).toggleClass('showChild hideChild');
            fill_treeview($(this).attr('p'));     
            return false;

        })   
        $(document).on("click", "i.hideChild" , function() 
        {
            $(this).toggleClass('icon-minus-sign icon-plus-sign');
            $(this).toggleClass('hideChild showChild');
            $('#pair-'+ $(this).attr('p')).html('');
            return false;
        }) 
        
    });
      </script>
</body>
</html>
