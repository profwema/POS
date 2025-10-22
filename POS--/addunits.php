<?php
session_start();
error_reporting(E_ERROR);
require_once("db.conn.php");


		

	$query  = "SELECT id, name FROM storeuser";
	$res 	= mysqli_query($adController->MySQL,$query);
        while($data   = mysqli_fetch_assoc($res))
        {  
            $id = $data[id];
            echo $id." - ";
            $queryy 		=  "INSERT INTO `units`( `name_en`, `name_ar`, `storeid`, `default`) "
                    . "VALUES ('pesea','قطعة','$id','1') ";
                                                       
            $ress		= mysqli_query($adController->MySQL,$queryy) or die(mysqli_error($adController->MySQL));
            echo $data[name]." - has added its unit ";
            echo "<br>  -- ";
                        
        }
        