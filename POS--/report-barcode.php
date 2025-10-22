<?php
require_once("top.php");
require_once("redirection.php");
require_once("controller.php");
$language 	= $_SESSION['lang'];
$storeid	= $_SESSION['storeid'];

    $id         = $_POST['qty-id'];
    $price      = $_POST['qty-price'];
    $barcode    = $_POST['qty-barcode'];
    $fill       = $_POST['qty-fill'];
    $query      = "TRUNCATE TABLE report_barcode";
    $res	= mysqli_query($adController->MySQL,$query);

    for($i=0 ; $i<count($id) ; $i++)
    {
        $idValue        = $id[$i];
        $query		= "SELECT * FROM items WHERE id='$idValue' ";
        $res		= mysqli_query($adController->MySQL,$query);
        $data   	= mysqli_fetch_assoc($res);
        $name_en        = $data['name_en'];        
        $name_ar        = $data['name_ar'];       
        $priceValue     = $price[$i];
        $fillValue      = $fill[$i];
        $barValue       = $barcode[$i];
        for($j=0 ; $j<$fillValue ; $j++)
        {        
            $query		= "INSERT INTO 
            `report_barcode` (`name_en`, `name_ar`, `barcode`, `price`)
                     VALUES ('$name_en', '$name_ar', '$barValue', '$priceValue')";
           mysqli_query($adController->MySQL,$query);
        }
    }
    ?>
             
<script type="text/javascript">

    location.replace('reports/barcode.php');
    </script>

    


?>
