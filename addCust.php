<?php
session_start();
error_reporting(E_ERROR);
require_once("db.conn.mah.php");


		

//
//            
//            $i=0;
//            $query         = "SELECT id, storeid FROM branches GROUP BY storeid  ";
//            $res           = mysqli_query($adController->MySQL,$query);
//             while($data   = mysqli_fetch_assoc($res))
//             {
//                 $i++;
//                 echo "Store id = ".$data['storeid']." - ";
//                 $storeid =  $data['storeid'];
//                 $branchid = $data['id'];
//                 echo "branch id = ".$data['id']." <br> ";
//                 
////                 $queryy =  "INSERT INTO `stores`( `name_en`, `name_ar`, `storeid`, `branch`, `default`)
////                             VALUES ('Main Store','المخزن الرئيسي',$storeid ,$branchid ,'1') ";
////                 $ress		= mysqli_query($adController->MySQL,$queryy) or die(mysqli_error($adController->MySQL));
//                 
//            $queryy 	=  "INSERT INTO `customers`( `name_en`, `name_ar`, `storeid`, `default`) 
//                                VALUES ('Paid Customer','عميل نقدى','$storeid','1') ";
//            $ress	= mysqli_query($adController->MySQL,$queryy) or die(mysqli_error($adController->MySQL));	                 
//                 
//
//                 
//        }
//        echo "total rows = ".$i;
//        //INSERT INTO `pos_db_server`.`stores` (`id`, `name_en`, `name_ar`, `storeid`, `default`) VALUES (NULL, 'Main Store', 'المخزن الرئيسي', '51', '1');
//        
//        
        
	$query  = "SELECT id, name FROM storeuser";
	$res 	= mysqli_query($adController->MySQL,$query);
        while($data   = mysqli_fetch_assoc($res))
        {  
            $id = $data[id];
            echo $id." - ";
            $queryy 	=  "INSERT INTO `customers`( `name_en`, `name_ar`, `storeid`, `default`) 
                                VALUES ('Paid Customer','عميل نقدى','$id','1') ";
            $ress	= mysqli_query($adController->MySQL,$queryy) or die(mysqli_error($adController->MySQL));	  
            
            echo $data[name]." - has added its paid customer ";
            echo "<br>  -- ";
                        
        }