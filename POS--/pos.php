<?php
require_once("top.php");
require_once("redirection.php");
require_once("controller.php");
    
$userId         = $_SESSION['userlogged'];




$language     = $_SESSION['lang'];
$storeid    = $_SESSION['storeid'];
$enableNegative    = $_SESSION['enableNegative'];
        
$query = "SELECT store_branch AS branch FROM employees WHERE id = '$userId' ";
$res   = mysqli_query($adController->MySQL, $query) or die(mysqli_error($adController->MySQL));
$data = mysqli_fetch_assoc($res);
$branch = $data['branch'];

$query = "SELECT id FROM stores WHERE storeid= '$storeid' AND `default` = '1' ";
$res   = mysqli_query($adController->MySQL, $query) or die(mysqli_error($adController->MySQL));
$data = mysqli_fetch_assoc($res);
$store = $data['id'];

$query    = "SELECT * FROM delivery  WHERE `storeid` = '$storeid'";
$res     = mysqli_query($adController->MySQL, $query) or die(mysqli_error($adController->MySQL));
$data     = mysqli_fetch_assoc($res);
$delcost  = floatval($data['amount']);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>WAM Tech Soft</title>
    <?php require_once("script_php_variables.php"); ?>

    
    
    
    
<!-- end: Mobile Specific -->

<!-- start: CSS -->

<link href="font-awesome/css/font-awesome.min.css" rel="stylesheet">

<link rel="stylesheet" href="css/autocomplete.css">


<!-- end: CSS -->
<link rel="stylesheet" href="css/chosen1.css" />
<!--<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.4.2/chosen.css" />-->
<link rel="stylesheet" href="css/style-forms.css" />
<link href="css/custom.css" rel="stylesheet">
<script src="js/jquery-1.9.1.min.js"></script>
<!-- The HTML5 shim, for IE6-8 support of HTML5 elements -->
<!--[if lt IE 9]>
	<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
	<link id="ie-style" href="css/ie.css" rel="stylesheet">
<![endif]-->

<!--[if IE 9]>
	<link id="ie9style" href="css/ie9.css" rel="stylesheet">
<![endif]-->
	
<!-- start: Favicon -->
<link rel="shortcut icon" href="img/favicon.ico">  
<link rel="stylesheet" href="css/pos.css" />
<link rel="stylesheet" href="css/keyboard.css" />
<!-- end: Favicon -->
<script type="text/javascript">
        var numAlready = 0;
        var branch = '<?= $branch ?>';
    //alert(branch);
        var store = '<?= $store ?>';
        var currentLanguage = '<?= $language ?>';
        var $enableNegative = '<?= $enableNegative ?>';
        var delcost = '<?= $delcost ?>';
    <?php
        if ($_SESSION['result']) {
        
        ?>
            alert('<?= $_SESSION['result'] ?>');
           
           <?php
    }
        unset($_SESSION['result']);

    ?>
</script>
</head>

<body>
    
    <div class='delivery-table-content' id="new_trans"> 

        <div class="box-header" data-original-title>
            <h2><i class="halflings-icon edit"></i><span class="break"></span>
                <?= TRANSACTION ?> >> <?= OPEN ?>
            </h2>	
            <a href="javascript:void(0)" class="close-button" onclick="closeTables()"> &#10006; </a>
        </div>
	<div class="pos-popup">
            <div class="cont_group">
                <label><?= TRANSACTION_NO ?> : </label>
                <div>
                    <input type="text" id='trans_id' readonly>
                </div>
            </div>
            <div class="cont_group">
                <label><?= OPENING_PALANCE ?> : </label>
                <div>
                    <input type="text" id='open-palance' readonly>
                </div>
            </div>
            <div class="buttons">
                <button type="reset" class="reset"><?= OK ?></button>
            </div>
	</div>
    </div>  
    
    
    
    <!--        to add customer fastly-->
    <div class='delivery-table-content' id="customer_inf"> 

        <div class="box-header" data-original-title>
            <h2><i class="halflings-icon edit"></i><span class="break"></span>
                <?= CUSTOMERS ?> >> <?= ADD_NEW ?>
            </h2>	
            <a href="javascript:void(0)" class="close-button" onclick="closeTables()"> &#10006; </a>
        </div>
        <div class='error-red'></div>
	<div class="pos-popup">
            <form id="add-customer" autocomplete="off">
		
		<input type="hidden" value="addCustomer" name="f">
		
		<div class="cont_group">
                    <label><?= CUSTOMER_NAME ?> <?= LANGUAGE_1 ?> : </label>
		    <div>
			<input type="text" name='name' id='name'>
		    </div>
		</div>
		<div class="cont_group">
                    <label><?= CUSTOMER_NAME ?> <?= LANGUAGE_2 ?>: </label>
		    <div>
			<input type="text" name='name_ar' id='name_ar'>
		    </div>
		</div>
		<div class="cont_group">
                    <label><?= TAX_NO ?> :</label>
		    <div>
                        <input type="text" name='tax_no'>
		    </div>
		</div> 
		<div class="cont_group">
                    <label><?= PHONE ?> :</label>
		    <div>
                        <input type="text" name='phone' class="" id='phone' onkeypress="return isNumber(event)" maxlength="15">
		    </div>
		</div>
		<div class="cont_group">
                    <label><?= ADDRESS ?> :</label>
                    <div>
                        <input type="text" name='address'>
		    </div>
		</div>  
		<div class="cont_group">
                    <label><?= EMAIL ?> : </label>
		    <div>
                        <input type="email" class="" name='email' id='email'>
		    </div>
		</div>
                <div class="buttons">
                    <button type="button" class="submit" id='submit-customer' src='1' class="btn btn-primary"><?= SAVE ?></button>
                    <button type="reset" class="reset"><?= CANCEL ?></button>
		</div>
	    </form>
	</div>
	    
    </div>  
        <!--        to add deliver fastly-->
    <div class='delivery-table-content' id="deliver_inf">
        
                
        <div class="box-header" data-original-title>
            <h2><i class="halflings-icon edit"></i><span class="break"></span>
                <?= DELIVER ?>
            </h2>	
            <a href="javascript:void(0)" class="close-button" onclick="closeTables()"> &#10006; </a>
        </div>
        
        
        <div class="pos-popup">
            <div class="cont_group">
                <label><?= PHONE ?> :</label>
                <div>
                    <input type="text" name='delv_phone' class="" id='delv_phone' onkeypress="return isNumber(event)" maxlength="15">
                </div>
            </div>
            <div class="cont_group">
                <label><?= ADDRESS ?> :</label>
                <div>
                    <input type="text" name='delv_address' id='delv_address'>
                </div>
            </div>  

            <div class="buttons">

                <button type="button" src='1' class="submit" onclick="closeTables()"><?= SAVE ?></button>
            </div>     
        </div>  
    </div>     
<!--    -->
<?php
$query = "SELECT id FROM `transactions` WHERE `user_id` = '$userId' AND status = '1' ";
    $res   = mysqli_query($adController->MySQL, $query) or die(mysqli_error($adController->MySQL));
$data = mysqli_fetch_assoc($res);

    $num        = mysqli_num_rows($res);
    $data        = mysqli_fetch_assoc($res);
    if ($num) {
    $transaction = $data['id'];
    } else {
    $query = "SELECT * FROM `transactions` WHERE `user_id` = '$userId' ORDER BY date DESC LIMIT 1 ";
        $res   = mysqli_query($adController->MySQL, $query) or die(mysqli_error($adController->MySQL));
    $data = mysqli_fetch_assoc($res);
        if ($num) {
            $newTrans = $data['id'];
            +1;
        $openPalance = $data['close_palance'];
        } else {
        $newTrans = 1;
        $openPalance = 0;
    }
         
        $queryy     = "INSERT INTO `transactions`(`id`, `user_id`,  `open_palance`) "
                . "VALUES ('$newTrans','$userId','$openPalance')";
            
        $ress    = mysqli_query($adController->MySQL, $queryy) or die(mysqli_error($adController->MySQL));
?>
<script>
        $("#new_trans").show();
    </script>
        
<?php
}
?>



    <form class="form-horizontal" id="sales-form" method='POST' action="#" style="display: block;" autocomplete="off">
        <?php echo csrf_input(); ?>
                   
        <input type="hidden" value="addSale" name="f">	
        <input type="hidden" name="purchase_expiry" value='<?php echo date("Y-m-d") ?>'>
        <input type="hidden" name="branch" value='<?php echo $branch ?>'>
        <input type="hidden" name='document' value='SALE INVOICE'>
        <input type="hidden" name='cashier' value='1'>       

        <div class="left-side">
            <?php require_once("data-type.php"); ?>
        </div> 
        
        <div class="barcode-side">

            <input type="text" id="search-items" placeholder="Search item by name or by barcode" aria-label="Search items" required data-required-msg="<?= REQUIRED ?>">
            <input type="text" id="search-items22" disabled="true">

        </div>

        <div class="customer-side">        
            <select name="customer" data-rel="chosen" id='customer' aria-label="<?= CUSTOMERS ?>" required data-required-msg="<?= REQUIRED ?>">
            </select>                 
            <a href="javascript:void(0)" onclick="addCustomerDialog()" title="<?= ADD_NEW ?> <?= CUSTOMERS ?>"> <img src="img/addCust.png" class="addCus"> </a>
        </div>  
        <div class="pos-side">        
            <a href="index.php">
                <img src="img/cashier.jpg">
                <span> ادارة اليومية </span>
            </a>                             
        </div>        
        <div class="home-side">        
            <a href="index.php">
                <img src="img/pos1.png">
                <span> خروج</span> 
            </a>                             
        </div>
        <?php require_once("payment-div.php"); ?>
	
    </form>
    <script src="js/pos.js"></script>
    <?php include("include.php"); ?>
    <script type="text/javascript">
        $(document).ready(function() {
     getCustomers();
     
            $("#dChange").click(function() {
        var changedDis = $("#d-rate").val();
       // alert(changedDis);
                if (changedDis > 0) {
            changedDis = parseFloat(changedDis);
                    var k = $("#item-list-container tbody tr").length;
                    for (var j = 0; j < k; j++) {
                        $("#item-discount-" + j).val(changedDis.toFixed(2));
                totalParshase(j);
            }
        }
        $("#d-rate").val('');
    });

            $(document).on("change", "input.item-qty", function() {
                var id = $(this).attr('id').replace('item-qty-', '');
        totalParshase(id);
    });
    
            $(document).on("change", "input.item-price", function() {
                var id = $(this).attr('id').replace('item-price-', '');
        totalParshase(id);
    });
    
            $(document).on("click", "div.del-item", function() {
       var id = $(this).attr('id');   
       delItem(id);
    });
    
            $("#search-items").live("keypress", function(e) {
                var wight = 1;
                if (e.keyCode === 13) {
            var searchVal = $.trim($("#search-items").val());
                    var search = '';
                    if (searchVal.length === 13 && searchVal.substr(0, 2) === '99') {
                search = searchVal.substr(2, 5);
                wight = searchVal.substr(7, 5);
                        wight = wight / 1000;
                    } else {
                search = searchVal;    
            }
                    $.ajax({
                        url: "controller.php?f=search_items&term=" + encodeURIComponent(search),
                        success: function(result) {
                    var json = $.parseJSON(result);
                    
                            if (json.length > 1) {
                        $("#search-items22").val($.trim($("#search-items").val()));
                        $("#search-items22").autocomplete("search");
                            } else if (json.length == 1) {
                                var id = json[0].id;
                        
                        $("#search-items").val('');
                                getThisItem(id, wight);
                        $("#search-items").focus();
                    }
                }		
            });
            return false; // prevent the button click from happening
        }
    });    
            $("#search-items22").autocomplete({
        source: "controller.php?f=search_items",
        minLength: 0,
                auotoFocus: true,
                select: function(event, ui) {
            $("#search-items").val('');
                    getThisItem(ui.item.id, 1);
        },
        html: true, 
                open: function(event, ui) {
            $(".ui-autocomplete").css("z-index", 1000);
    },
   }); 
   
            $(document).on('keyup', function(evt) {
        if (evt.keyCode == 27) {
           $("#search-items22").autocomplete("close");
        }
    });


    
    
            $("#customer").chosen().change(function() {
                var phone = $('option:selected', this).attr('phone');
                var address = $('option:selected', this).attr('address');
        $("#delv_phone").val(phone);
        $("#delv_address").val(address);
    });
    
 

            $("#cash").change(function() {
                var left = $("#all-total").val() - $("#cash").val() - $("#visa").val() - $("#mada").val();
                $("#left").val(left.toFixed(2));
            });
            $("#visa").change(function() {
                var left = $("#all-total").val() - $("#cash").val() - $("#visa").val() - $("#mada").val();
                $("#left").val(left.toFixed(2));
            });
            $("#mada").change(function() {
                var left = $("#all-total").val() - $("#cash").val() - $("#visa").val() - $("#mada").val();
                $("#left").val(left.toFixed(2));
            });

            $("#cust-paid").change(function() {
                var amt = $("#all-total").val() - $("#cust-paid").val()
                $("#ret-amt").val(amt.toFixed(2));
            });

            $(document).on("click focus", "input", function() {
       var element = $(this);
       element.select();
       lastElementFocused = element.attr('id');
                lastElementFocused = '#' + lastElementFocused;
       isItNew = 1;
    });
    
    
    
    
    
            for (var selector in config) {
        $(selector).chosen(config[selector]);
    }   
});        
    </script>

</body>

</html>
        $("#new_trans").show();

    </script>

        

<?php

}

?>







    <form class="form-horizontal" id="sales-form" method='POST' action="#" style="display: block;" autocomplete="off">
        <?php echo csrf_input(); ?>
                   

        <input type="hidden" value="addSale" name="f">	

        <input type="hidden" name="purchase_expiry" value='<?php echo date("Y-m-d") ?>'>
        <input type="hidden" name="branch" value='<?php echo $branch ?>'>
        <input type="hidden" name='document' value='SALE INVOICE'>

        <input type="hidden" name='cashier' value='1'>       



        <div class="left-side">

            <?php require_once("data-type.php"); ?>
        </div> 

        

        <div class="barcode-side">



            <input type="text" id="search-items" placeholder="Search item by name or by barcode">
            <input type="text" id="search-items22" disabled="true">


        </div>



        <div class="customer-side">        

            <select name="customer" data-rel="chosen" id='customer'>
            </select>                 

            <a href="javascript:void(0)" onclick="addCustomerDialog()" title="<?= ADD_NEW ?> <?= CUSTOMERS ?>"> <img src="img/addCust.png" class="addCus"> </a>
        </div>  

        <div class="pos-side">        

            <a href="index.php">
                <img src="img/cashier.jpg">

                <span> ادارة اليومية </span>
            </a>                             

        </div>        

        <div class="home-side">        

            <a href="index.php">
                <img src="img/pos1.png">

                <span> خروج</span> 

            </a>                             

        </div>

        <?php require_once("payment-div.php"); ?>
	

    </form>

    <script src="js/pos.js"></script>

    <?php include("include.php"); ?>
    <script type="text/javascript">

        $(document).ready(function() {
     getCustomers();

     

            $("#dChange").click(function() {
        var changedDis = $("#d-rate").val();

       // alert(changedDis);

                if (changedDis > 0) {
            changedDis = parseFloat(changedDis);

                    var k = $("#item-list-container tbody tr").length;
                    for (var j = 0; j < k; j++) {
                        $("#item-discount-" + j).val(changedDis.toFixed(2));
                totalParshase(j);

            }

        }

        $("#d-rate").val('');

    });



            $(document).on("change", "input.item-qty", function() {
                var id = $(this).attr('id').replace('item-qty-', '');
        totalParshase(id);

    });

    

            $(document).on("change", "input.item-price", function() {
                var id = $(this).attr('id').replace('item-price-', '');
        totalParshase(id);

    });

    

            $(document).on("click", "div.del-item", function() {
       var id = $(this).attr('id');   

       delItem(id);

    });

    

            $("#search-items").live("keypress", function(e) {
                var wight = 1;
                if (e.keyCode === 13) {
            var searchVal = $.trim($("#search-items").val());

                    var search = '';
                    if (searchVal.length === 13 && searchVal.substr(0, 2) === '99') {
                search = searchVal.substr(2, 5);

                wight = searchVal.substr(7, 5);

                        wight = wight / 1000;
                    } else {
                search = searchVal;    

            }

                    $.ajax({
                        url: "controller.php?f=search_items&term=" + encodeURIComponent(search),
                        success: function(result) {
                    var json = $.parseJSON(result);

                    

                            if (json.length > 1) {
                        $("#search-items22").val($.trim($("#search-items").val()));

                        $("#search-items22").autocomplete("search");

                            } else if (json.length == 1) {
                                var id = json[0].id;
                        

                        $("#search-items").val('');

                                getThisItem(id, wight);
                        $("#search-items").focus();

                    }

                }		

            });

            return false; // prevent the button click from happening

        }

    });    

            $("#search-items22").autocomplete({
        source: "controller.php?f=search_items",

        minLength: 0,

                auotoFocus: true,
                select: function(event, ui) {
            $("#search-items").val('');

                    getThisItem(ui.item.id, 1);
        },

        html: true, 

                open: function(event, ui) {
            $(".ui-autocomplete").css("z-index", 1000);

    },

   }); 

   

            $(document).on('keyup', function(evt) {
        if (evt.keyCode == 27) {

           $("#search-items22").autocomplete("close");

        }

    });





    

    

            $("#customer").chosen().change(function() {
                var phone = $('option:selected', this).attr('phone');
                var address = $('option:selected', this).attr('address');
        $("#delv_phone").val(phone);

        $("#delv_address").val(address);

    });

    

 



            $("#cash").change(function() {
                var left = $("#all-total").val() - $("#cash").val() - $("#visa").val() - $("#mada").val();
                $("#left").val(left.toFixed(2));
            });
            $("#visa").change(function() {
                var left = $("#all-total").val() - $("#cash").val() - $("#visa").val() - $("#mada").val();
                $("#left").val(left.toFixed(2));
            });
            $("#mada").change(function() {
                var left = $("#all-total").val() - $("#cash").val() - $("#visa").val() - $("#mada").val();
                $("#left").val(left.toFixed(2));
            });

            $("#cust-paid").change(function() {
                var amt = $("#all-total").val() - $("#cust-paid").val()
                $("#ret-amt").val(amt.toFixed(2));
            });

            $(document).on("click focus", "input", function() {
       var element = $(this);

       element.select();

       lastElementFocused = element.attr('id');

                lastElementFocused = '#' + lastElementFocused;
       isItNew = 1;

    });

    

    

    

    

    

            for (var selector in config) {
        $(selector).chosen(config[selector]);

    }   

});        

    </script>



</body>


</html>


            for (var selector in config) {
        $(selector).chosen(config[selector]);

    }   

});        

    </script>



</body>


</html>

